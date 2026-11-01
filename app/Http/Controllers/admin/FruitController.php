<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Fruit;
use App\Models\FruitBoxConfiguration;
use App\Models\FruitPrice;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FruitController extends Controller
{
    public function index(Request $request)
    {
        $query = Fruit::query()->with(['category', 'defaultUnit']);

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%")
                    ->orWhereHas('category', fn ($category) => $category->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->boolean('status'));
        }

        $fruits = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('backend.pages.fruits.partials.table', compact('fruits'))->render(),
            ]);
        }

        $categories = Category::query()->orderBy('name')->get();
        $units = Unit::query()->where('is_order_unit', true)->orderBy('name')->get();

        return view('backend.pages.fruits.index', compact('fruits', 'categories', 'units'));
    }

    public function store(Request $request)
    {
        $fruit = DB::transaction(function () use ($request): Fruit {
            $fruit = Fruit::create([
                ...$this->validatedData($request),
                'code' => 'TEMP-'.Str::uuid(),
            ]);

            $code = 1_000_000_000_000 + $fruit->id;
            while (Fruit::query()->where('code', (string) $code)->exists()) {
                $code += 1_000_000_000_000;
            }

            $fruit->update(['code' => (string) $code]);

            if ($fruit->allow_box) {
                $boxUnit = Unit::query()
                    ->where('code', 'BOX')
                    ->where('is_order_unit', true)
                    ->firstOrFail();

                $box = FruitBoxConfiguration::create([
                    'fruit_id' => $fruit->id,
                    'name' => 'Caja estándar',
                    'code' => 'STD',
                    'weight_kg' => 10,
                    'tare_weight_kg' => 0,
                    'is_default' => true,
                    'status' => true,
                ]);

                FruitPrice::create([
                    'fruit_id' => $fruit->id,
                    'unit_id' => $boxUnit->id,
                    'box_configuration_id' => $box->id,
                    'price' => 0,
                    'currency' => 'EUR',
                    'effective_from' => today()->toDateString(),
                    'is_active' => false,
                ]);
            }

            return $fruit;
        });

        return response()->json([
            'status' => 'success',
            'message' => $fruit->allow_box
                ? 'Fruit created with a standard 10 kg box and an inactive price placeholder. Set the actual price before enabling orders.'
                : 'Fruit created successfully.',
            'data' => $fruit,
        ]);
    }

    public function edit(int $id)
    {
        return response()->json([
            'status' => 'success',
            'data' => Fruit::findOrFail($id),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $fruit = Fruit::findOrFail($id);
        $fruit->update($this->validatedData($request));

        return response()->json([
            'status' => 'success',
            'message' => 'Fruit updated successfully.',
        ]);
    }

    public function destroy(int $id)
    {
        $fruit = Fruit::findOrFail($id);

        if (
            $fruit->orderItems()->exists()
            || $fruit->stocks()->exists()
            || $fruit->inventoryTransactions()->exists()
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'This fruit is used by order or warehouse history and cannot be deleted. Deactivate it instead.',
            ], 422);
        }

        $fruit->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Fruit deleted successfully.',
        ]);
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'default_unit_id' => [
                'required',
                'integer',
                Rule::exists('units', 'id')->where('is_order_unit', true),
            ],
            'allow_kg' => ['required', 'boolean'],
            'allow_box' => ['required', 'boolean'],
            'status' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        Validator::make($data, [])->after(function ($validator) use ($data): void {
            if (! $data['allow_kg'] && ! $data['allow_box']) {
                $validator->errors()->add('allow_kg', 'At least one order unit must be enabled.');
            }

            $unit = Unit::find($data['default_unit_id']);
            if ($unit?->is_weight_unit && ! $data['allow_kg']) {
                $validator->errors()->add('default_unit_id', 'Enable kilogram orders or choose a different default unit.');
            } elseif ($unit?->code === 'BOX' && ! $data['allow_box']) {
                $validator->errors()->add('default_unit_id', 'Enable box orders or choose a different default unit.');
            }
        })->validate();

        return $data;
    }
}
