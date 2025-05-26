<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Person extends Model
{
    protected $fillable = [
        'age',
    ];

    /**
     * Get the Table that owns the Person.
     *
     * @return BelongsTo<Table>
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }
}
