<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $table = 'menu';

    protected $fillable = [
        'menunummer',
        'menu_toevoeging',
        'naam',
        'price',
        'soortgerecht',
        'beschrijving'
    ];

    /**
     * Get all sales for this menu item
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'itemId');
    }
}
