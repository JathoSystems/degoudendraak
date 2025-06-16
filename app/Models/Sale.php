<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    protected $fillable = [
        'itemId',
        'table_id',
        'amount',
        'saleDate',
        'remarks'
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

    /**
     * Get the table this sale belongs to (optional)
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }
}
