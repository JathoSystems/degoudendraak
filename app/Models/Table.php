<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Table extends Model
{
    protected $fillable = [
        'name',
        'capacity',
        'round',
        'last_ordered_at',
        'extra_deluxe_menu',
    ];

    protected $casts = [
        'last_ordered_at' => 'datetime',
    ];

    /**
     * Get the people associated with the table.
     *
     * @return HasMany<Person>
     */
    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    /**
     * Get the tablet associated with the table.
     *
     * @return HasOne<Tablet>
     */
    public function tablet(): HasOne
    {
        return $this->hasOne(Tablet::class);
    }

    /**
     * Get the sales associated with the table.
     *
     * @return HasMany<Sale>
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get the sales from the last round for re-ordering
     */
    public function lastRoundSales()
    {
        if (!$this->last_ordered_at) {
            return collect();
        }

        // Get sales from the last ordering session (within a 10-minute window around last_ordered_at)
        $startTime = $this->last_ordered_at->copy()->subMinutes(5);
        $endTime = $this->last_ordered_at->copy()->addMinutes(5);

        return $this->sales()
            ->with('menuItem')
            ->whereBetween('saleDate', [$startTime, $endTime])
            ->get();
    }
}
