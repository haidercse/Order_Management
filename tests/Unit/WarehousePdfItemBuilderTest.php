<?php

namespace Tests\Unit;

use App\Models\Fruit;
use App\Models\FruitBoxConfiguration;
use App\Models\FruitPrice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Unit;
use App\Support\WarehousePdfItemBuilder;
use PHPUnit\Framework\TestCase;

class WarehousePdfItemBuilderTest extends TestCase
{
    public function test_fixed_weight_box_uses_quantity_times_kilo_instead_of_scale_weight(): void
    {
        $fruit = new Fruit;
        $fruit->id = 5;
        $fruit->name = 'ACELGAS';
        $fruit->display_name = null;

        $box = new FruitBoxConfiguration;
        $box->id = 9;
        $box->name = 'Caja estándar';
        $box->weight_kg = 12;
        $box->tare_weight_kg = 0.5;
        $box->is_default = true;

        $unit = new Unit;
        $unit->code = 'BOX';

        $price = new FruitPrice;
        $price->price = 9.6;
        $price->box_configuration_id = 9;
        $price->setRelation('unit', $unit);

        $fruit->setRelation('boxConfigurations', collect([$box]));
        $fruit->setRelation('prices', collect([$price]));

        $firstItem = new OrderItem([
            'fruit_id' => 5,
            'box_configuration_id' => 9,
            'quantity' => 1,
            'unit_weight_kg' => 12,
            'unit_tare_kg' => 0.5,
            'warehouse_weight_kg' => 12.2,
        ]);
        $firstItem->setRelation('fruit', $fruit);
        $firstItem->setRelation('boxConfiguration', $box);

        $secondItem = new OrderItem([
            'fruit_id' => 5,
            'box_configuration_id' => 9,
            'quantity' => 1,
            'unit_weight_kg' => 12,
            'unit_tare_kg' => 0.5,
            'warehouse_weight_kg' => 12.2,
        ]);
        $secondItem->setRelation('fruit', $fruit);
        $secondItem->setRelation('boxConfiguration', $box);

        $firstOrder = new Order;
        $firstOrder->setRelation('items', collect([$firstItem]));
        $secondOrder = new Order;
        $secondOrder->setRelation('items', collect([$secondItem]));

        $result = (new WarehousePdfItemBuilder)->build(collect([$firstOrder, $secondOrder]))->sole();

        $this->assertSame(2.0, $result['quantity']);
        $this->assertSame(0.5, $result['tare']);
        $this->assertSame(1.0, $result['total_tare']);
        $this->assertSame(24.0, $result['total_weight']);
        $this->assertSame(23.0, $result['net_weight']);
        $this->assertSame(0.8, $result['price']);
        $this->assertSame(18.4, $result['total']);
    }

    public function test_variable_weight_box_uses_saved_scale_weight_and_rounds_line_total_half_up(): void
    {
        $fruit = new Fruit;
        $fruit->id = 6;
        $fruit->name = 'AGUACATE OFF';
        $fruit->display_name = 'AGUACATE OFF';

        $kgUnit = new Unit;
        $kgUnit->code = 'KG';
        $price = new FruitPrice;
        $price->price = 2.5;
        $price->box_configuration_id = null;
        $price->setRelation('unit', $kgUnit);
        $fruit->setRelation('boxConfigurations', collect());
        $fruit->setRelation('prices', collect([$price]));

        $item = new OrderItem([
            'fruit_id' => 6,
            'quantity' => 4,
            'unit_weight_kg' => null,
            'unit_tare_kg' => 0.3,
            'warehouse_weight_kg' => 5.5,
        ]);
        $item->setRelation('fruit', $fruit);
        $item->setRelation('boxConfiguration', null);

        $order = new Order;
        $order->setRelation('items', collect([$item]));

        $result = (new WarehousePdfItemBuilder)->build(collect([$order]))->sole();

        $this->assertSame(4.0, $result['quantity']);
        $this->assertSame(5.5, $result['total_weight']);
        $this->assertSame(0.3, $result['tare']);
        $this->assertSame(1.2, $result['total_tare']);
        $this->assertSame(4.3, $result['net_weight']);
        $this->assertSame(2.5, $result['price']);
        $this->assertSame(10.75, $result['total']);
    }

    public function test_line_total_rounds_half_up_to_two_decimal_places(): void
    {
        $fruit = new Fruit;
        $fruit->id = 7;
        $fruit->name = 'BANANAS';
        $fruit->setRelation('boxConfigurations', collect());
        $fruit->setRelation('prices', collect());

        $item = new OrderItem([
            'fruit_id' => 7,
            'quantity' => 1,
            'unit_weight_kg' => 1.25,
            'unit_tare_kg' => 0,
            'unit_price' => 1.38,
        ]);
        $item->setRelation('fruit', $fruit);
        $item->setRelation('boxConfiguration', null);

        $order = new Order;
        $order->setRelation('items', collect([$item]));

        $result = (new WarehousePdfItemBuilder)->build(collect([$order]))->sole();

        $this->assertSame(1.375, $result['net_weight']);
        $this->assertSame(1.1, $result['price']);
        $this->assertSame(1.51, $result['total']);
    }
}
