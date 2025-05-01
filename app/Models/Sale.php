<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    protected $fillable = [
        'itemId',
        'amount',
        'saleDate'
    ];

    protected $casts = [
        'saleDate' => 'datetime'
    ];

    /**
     * Get the menu item that was sold
     */
    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'itemId');
    }
}
