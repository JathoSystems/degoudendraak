<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_code',
        'table_id',
        'name',
        'email',
        'food_rating',
        'service_rating',
        'ambiance_rating',
        'overall_rating',
        'feedback',
        'favorite_dish_selected',
        'favorite_dish',
        'improvement_area',
        'would_return',
        'heard_about_us',
        'discount_code',
        'discount_used',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

}
