<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Fruit;
use App\Models\FruitBoxConfiguration;
use App\Models\FruitPrice;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FruitPriceController extends Controller
{
    public function index(Request $request)
    {
        $query = FruitPrice::query()->with(['fruit', 'unit', 'boxConfiguration']);

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('currency', 'like', "%{$search}%")
                    ->orWhereHas('fruit', function ($fruit) use ($search) {
                        $fruit->where('name', 'like', "%{$search}%")
                            ->orWhere('display_name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('unit', fn ($unit) => $unit->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('fruit_id')) {
            $query->where('fruit_id', $request->integer('fruit_id'));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->boolean('status'));
        }

        $prices = $query
            ->orderByDesc('effective_from')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('backend.pages.fruit_prices.partials.table', compact('prices'))->render(),
            ]);
        }

        $fruits = Fruit::query()->with('boxConfigurations')->orderBy('name')->get();
        $units = Unit::query()->where('is_order_unit', true)->orderBy('name')->get();

        return view('backend.pages.fruit_prices.index', compact('prices', 'fruits', 'units'));
    }

    public function store(Request $request)
    {
        $price = FruitPrice::create($this->validatedData($request));

        return response()->json([
            'status' => 'success',
            'message' => 'Fruit price created successfully.',
            'data' => $price,
        ]);
    }

    public function edit(int $id)
    {
        return response()->json([
            'status' => 'success',
            'data' => FruitPrice::findOrFail($id),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $price = FruitPrice::findOrFail($id);
        $price->update($this->validatedData($request, $price));

        return response()->json([
            'status' => 'success',
            'message' => 'Fruit price updated successfully.',
        ]);
    }

    public function destroy(int $id)
    {
        FruitPrice::findOrFail($id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Fruit price deleted successfully.',
        ]);
    }

    private function validatedData(Request $request, ?FruitPrice $price = null): array
    {
        $request->merge([
            'currency' => strtoupper((string) $request->input('currency')),
            'box_configuration_id' => $request->filled('box_configuration_id')
                ? $request->input('box_configuration_id')
                : null,
        ]);

        $data = $request->validate([
            'fruit_id' => ['required', 'integer', 'exists:fruits,id'],
            'unit_id' => [
                'required',
                'integer',
                Rule::exists('units', 'id')->where('is_order_unit', true),
            ],
            'box_configuration_id' => ['nullable', 'integer', 'exists:fruit_box_configurations,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],
            'is_active' => ['required', 'boolean'],
        ]);

        $validator = Validator::make($data, []);
        $fruit = Fruit::findOrFail($data['fruit_id']);
        $unit = Unit::findOrFail($data['unit_id']);

        $validator->after(function ($validator) use ($data, $fruit, $unit, $price): void {
            if ($unit->code === 'BOX' && !$data['box_configuration_id']) {
                $validator->errors()->add('box_configuration_id', 'Select a box configuration for caja pricing.');
            }

            if ($unit->code !== 'BOX' && $data['box_configuration_id']) {
                $validator->errors()->add('box_configuration_id', 'A box configuration can only be used with the BOX unit.');
            }

            if ($unit->is_weight_unit && !$fruit->allow_kg) {
                $validator->errors()->add('unit_id', 'This fruit is not configured for kilogram orders.');
            }

            if ($unit->code === 'BOX' && !$fruit->allow_box) {
                $validator->errors()->add('unit_id', 'This fruit is not configured for box orders.');
            }

            if ($data['box_configuration_id']) {
                $belongsToFruit = FruitBoxConfiguration::query()
                    ->whereKey($data['box_configuration_id'])
                    ->where('fruit_id', $fruit->id)
                    ->exists();

                if (!$belongsToFruit) {
                    $validator->errors()->add('box_configuration_id', 'The selected box configuration does not belong to this fruit.');
                }
            }

            $duplicatePrice = FruitPrice::query()
                ->where('fruit_id', $data['fruit_id'])
                ->where('unit_id', $data['unit_id'])
                ->whereDate('effective_from', $data['effective_from'])
                ->when(
                    $data['box_configuration_id'],
                    fn ($query, $boxId) => $query->where('box_configuration_id', $boxId),
                    fn ($query) => $query->whereNull('box_configuration_id')
                )
                ->when($price, fn ($query) => $query->where('id', '!=', $price->id))
                ->exists();

            if ($duplicatePrice) {
                $validator->errors()->add('effective_from', 'A price already exists for this fruit, unit and effective date.');
            }
        });

        $validator->validate();

        return $data;
    }
}
