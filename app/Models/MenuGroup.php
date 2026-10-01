<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class MenuGroup extends Model
{
    protected $fillable = ['name', 'order', 'status'];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'group_id')->orderBy('order');
    }
}