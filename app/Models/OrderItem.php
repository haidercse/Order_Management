<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'fruit_id',
        'unit_id',
        'box_configuration_id',
        'quantity',
        'unit_weight_kg',
        'converted_kg',
        'unit_price',
        'line_total',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_weight_kg' => 'decimal:3',
        'converted_kg' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

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
}