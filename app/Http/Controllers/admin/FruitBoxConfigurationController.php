<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Fruit;
use App\Models\FruitBoxConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FruitBoxConfigurationController extends Controller
{
    public function index(Request $request)
    {
        $query = FruitBoxConfiguration::query()->with('fruit');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('fruit', function ($fruit) use ($search) {
                        $fruit->where('name', 'like', "%{$search}%")
                            ->orWhere('display_name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('fruit_id')) {
            $query->where('fruit_id', $request->integer('fruit_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->boolean('status'));
        }

        $boxes = $query
            ->orderBy('fruit_id')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('backend.pages.boxes.partials.table', compact('boxes'))->render(),
            ]);
        }

        $fruits = Fruit::query()->orderBy('name')->get();

        return view('backend.pages.boxes.index', compact('boxes', 'fruits'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($data): void {
            if ($data['is_default']) {
                FruitBoxConfiguration::query()
                    ->where('fruit_id', $data['fruit_id'])
                    ->update(['is_default' => false]);
            }

            FruitBoxConfiguration::create($data);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Box configuration created successfully.',
        ]);
    }

    public function edit(int $id)
    {
        return response()->json([
            'status' => 'success',
            'data' => FruitBoxConfiguration::findOrFail($id),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $box = FruitBoxConfiguration::findOrFail($id);
        $data = $this->validatedData($request, $box);

        DB::transaction(function () use ($box, $data): void {
            if ($data['is_default']) {
                FruitBoxConfiguration::query()
                    ->where('fruit_id', $data['fruit_id'])
                    ->where('id', '!=', $box->id)
                    ->update(['is_default' => false]);
            }

            $box->update($data);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Box configuration updated successfully.',
        ]);
    }

    public function destroy(int $id)
    {
        $box = FruitBoxConfiguration::findOrFail($id);

        if (
            $box->prices()->exists()
            || $box->stocks()->exists()
            || $box->orderItems()->exists()
            || $box->inventoryTransactions()->exists()
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'This box configuration is used by pricing, orders, or warehouse history and cannot be deleted.',
            ], 422);
        }

        $box->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Box configuration deleted successfully.',
        ]);
    }

    private function validatedData(Request $request, ?FruitBoxConfiguration $box = null): array
    {
        $data = $request->validate([
            'fruit_id' => ['required', 'integer', 'exists:fruits,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('fruit_box_configurations', 'code')
                    ->where('fruit_id', $request->input('fruit_id'))
                    ->ignore($box?->id),
            ],
            'weight_kg' => ['required', 'numeric', 'decimal:0,3', 'gt:0'],
            'is_default' => ['required', 'boolean'],
            'status' => ['required', 'boolean'],
        ]);

        Validator::make($data, [])->after(function ($validator) use ($data, $box): void {
            if ($data['is_default'] && !$data['status']) {
                $validator->errors()->add('is_default', 'A default box configuration must be active.');
            }

            if (!$box) {
                return;
            }

            $hasHistory = $box->prices()->exists()
                || $box->stocks()->exists()
                || $box->orderItems()->exists()
                || $box->inventoryTransactions()->exists();

            if ($hasHistory && (int) $data['fruit_id'] !== $box->fruit_id) {
                $validator->errors()->add('fruit_id', 'A box configuration with pricing, order, or warehouse history cannot be moved to another fruit.');
            }

            $newWeight = number_format((float) $data['weight_kg'], 3, '.', '');
            $storedWeight = number_format((float) $box->weight_kg, 3, '.', '');
            if ($hasHistory && $newWeight !== $storedWeight) {
                $validator->errors()->add('weight_kg', 'The weight cannot be changed after this configuration has been used. Create a new configuration instead.');
            }
        })->validate();

        return $data;
    }
}
