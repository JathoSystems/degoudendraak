<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Takeaway extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'pickup_time',
        'total_price',
        'status',
    ];

    public function menuItems(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'takeaway_items', 'takeaway_order_id', 'menu_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
