<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'fruit_id',
        'unit_id',
        'box_configuration_id',
        'quantity',
        'reserved_quantity',
        'quantity_kg',
        'reserved_kg',
        'status',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'reserved_quantity' => 'decimal:3',
        'quantity_kg' => 'decimal:3',
        'reserved_kg' => 'decimal:3',
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
}