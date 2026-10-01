<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FruitPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'fruit_id',
        'unit_id',
        'box_configuration_id',
        'price',
        'currency',
        'effective_from',
        'effective_to',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_active' => 'boolean',
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