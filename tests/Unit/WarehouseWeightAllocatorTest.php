<?php

namespace Tests\Unit;

use App\Models\OrderItem;
use App\Support\WarehouseWeightAllocator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class WarehouseWeightAllocatorTest extends TestCase
{
    public function test_total_weight_is_distributed_by_caja_quantity_without_losing_grams(): void
    {
        $items = collect([
            $this->item(1, 1),
            $this->item(2, 2),
            $this->item(3, 3),
        ]);

        $allocation = (new WarehouseWeightAllocator())->distribute($items, 100);

        $this->assertSame([1 => 17, 2 => 33, 3 => 50], $allocation);
        $this->assertSame(100, array_sum($allocation));
    }

    public function test_equal_remainders_are_assigned_deterministically_by_item_id(): void
    {
        $items = collect([
            $this->item(20, 1),
            $this->item(10, 1),
        ]);

        $allocation = (new WarehouseWeightAllocator())->distribute($items, 3);

        $this->assertSame([20 => 1, 10 => 2], $allocation);
    }

    public function test_weight_too_small_to_assign_a_positive_amount_to_each_shop_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new WarehouseWeightAllocator())->distribute(collect([
            $this->item(1, 1),
            $this->item(2, 1),
        ]), 1);
    }

    private function item(int $id, int $quantity): OrderItem
    {
        $item = new OrderItem();
        $item->id = $id;
        $item->quantity = $quantity;

        return $item;
    }
}
