<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'group_id',
        'parent_id',
        'title',
        'route',
        'icon',
        'permission',
        'order',
        'is_active'
    ];

    public function group()
    {
        return $this->belongsTo(MenuGroup::class, 'group_id');
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }
  
}