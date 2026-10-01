<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fruit extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'code',
        'name',
        'display_name',
        'default_unit_id',
        'allow_kg',
        'allow_box',
        'status',
        'sort_order',
        'notes',
    ];

    protected $casts = [
        'allow_kg' => 'boolean',
        'allow_box' => 'boolean',
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function defaultUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'default_unit_id');
    }

    public function boxConfigurations(): HasMany
    {
        return $this->hasMany(FruitBoxConfiguration::class);
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