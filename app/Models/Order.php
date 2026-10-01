<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'order_date',
        'status',
        'created_by',
        'submitted_at',
        'prepared_at',
        'ready_at',
        'sent_at',
        'notes',
    ];

    protected $casts = [
        'order_date' => 'date',
        'submitted_at' => 'datetime',
        'prepared_at' => 'datetime',
        'ready_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'reference_order_id');
    }
}