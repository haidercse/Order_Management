<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'fruit_id',
        'unit_id',
        'box_configuration_id',
        'type',
        'quantity',
        'quantity_kg',
        'unit_price',
        'total_price',
        'reference_order_id',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'quantity_kg' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function fruit(): BelongsTo
    {
        return $this->belongsTo(Fruit::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function boxConfiguration(): BelongsTo
    {
        return $this->belongsTo(FruitBoxConfiguration::class, 'box_configuration_id');
    }

    public function referenceOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'reference_order_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}