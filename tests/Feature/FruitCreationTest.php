<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Fruit;
use App\Models\FruitBoxConfiguration;
use App\Models\FruitPrice;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FruitCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_fruit_codes_are_generated_as_unique_numbers(): void
    {
        $category = Category::create([
            'name' => 'Fruit',
            'code' => 'FRUIT',
            'status' => true,
            'sort_order' => 1,
        ]);
        $unit = Unit::create([
            'name' => 'Kilogram',
            'code' => 'KG',
            'symbol' => 'kg',
            'is_weight_unit' => true,
            'is_order_unit' => true,
            'status' => true,
        ]);
        Unit::create([
            'name' => 'Box',
            'code' => 'BOX',
            'symbol' => 'box',
            'is_weight_unit' => false,
            'is_order_unit' => true,
            'status' => true,
        ]);
        $payload = [
            'category_id' => $category->id,
            'code' => 'client-entered-code',
            'name' => 'Dragon fruit',
            'default_unit_id' => $unit->id,
            'allow_kg' => true,
            'allow_box' => true,
            'status' => true,
            'sort_order' => 0,
        ];

        $this->withoutMiddleware()
            ->postJson(route('admin.fruits.store'), $payload)
            ->assertOk()
            ->assertJsonPath('status', 'success');

        $this->withoutMiddleware()
            ->postJson(route('admin.fruits.store'), [...$payload, 'name' => 'Passion fruit'])
            ->assertOk()
            ->assertJsonPath('status', 'success');

        $codes = Fruit::query()->orderBy('id')->pluck('code');

        $this->assertCount(2, $codes);
        $this->assertMatchesRegularExpression('/^\d+$/', $codes[0]);
        $this->assertMatchesRegularExpression('/^\d+$/', $codes[1]);
        $this->assertNotSame($codes[0], $codes[1]);
        $this->assertSame(2, FruitBoxConfiguration::query()->count());
        $this->assertSame(2, FruitPrice::query()->count());
        $this->assertDatabaseHas('fruit_box_configurations', [
            'fruit_id' => 1,
            'code' => 'STD',
            'weight_kg' => 10,
            'is_default' => true,
            'status' => true,
        ]);
        $this->assertDatabaseHas('fruit_prices', [
            'fruit_id' => 1,
            'price' => 0,
            'currency' => 'EUR',
            'is_active' => false,
        ]);
    }
}
