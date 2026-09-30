<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuGroup extends Model
{
    protected $fillable = ['name', 'order'];

    public function menus()
    {
        return $this->hasMany(Menu::class, 'group_id')->orderBy('order');
    }
}