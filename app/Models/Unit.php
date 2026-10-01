<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'is_weight_unit',
        'is_order_unit',
        'status',
    ];

    protected $casts = [
        'is_weight_unit' => 'boolean',
        'is_order_unit' => 'boolean',
        'status' => 'boolean',
    ];

    public function fruits(): HasMany
    {
        return $this->hasMany(Fruit::class, 'default_unit_id');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(FruitPrice::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(InventoryStock::class);
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}