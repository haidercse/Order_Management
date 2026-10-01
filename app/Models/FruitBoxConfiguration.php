<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FruitBoxConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'fruit_id',
        'name',
        'code',
        'weight_kg',
        'is_default',
        'status',
    ];

    protected $casts = [
        'weight_kg' => 'decimal:3',
        'is_default' => 'boolean',
        'status' => 'boolean',
    ];

    public function fruit(): BelongsTo
    {
        return $this->belongsTo(Fruit::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(FruitPrice::class, 'box_configuration_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'box_configuration_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(InventoryStock::class, 'box_configuration_id');
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'box_configuration_id');
    }
}