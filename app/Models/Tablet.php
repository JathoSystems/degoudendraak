<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tablet extends Model
{
    protected $fillable = [
        'name',
        'token',
    ];

    protected static function booted()
    {
        static::creating(function ($tablet) {
            if (empty($tablet->token)) {
                do {
                    $token = Str::random(64);
                } while (static::where('token', $token)->exists());

                $tablet->token = $token;
            }
        });
    }

    /**
     * Get the table associated with the tablet.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Table>
     */
    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    /**
     * Get the ordering URL for this tablet.
     */
    public function getOrderingUrlAttribute()
    {
        return route('tablet.order', ['token' => $this->token]);
    }
}
