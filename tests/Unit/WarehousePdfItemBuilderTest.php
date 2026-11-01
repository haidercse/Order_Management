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
    public function test_it_calculates_total_tare_net_weight_and_total_from_box_and_price_data(): void
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
        $this->assertSame(1.0, $result['tare']);
        $this->assertSame(23.4, $result['net_weight']);
        $this->assertSame(0.8, $result['price']);
        $this->assertSame(18.72, $result['total']);
    }
}
