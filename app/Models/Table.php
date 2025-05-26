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
}
