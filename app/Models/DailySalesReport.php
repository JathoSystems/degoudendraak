<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class DailySalesReport extends Model
{
    protected $fillable = [
        'report_date',
        'file_path',
        'total_sales',
        'total_orders',
        'sales_summary',
    ];

    protected $casts = [
        'report_date' => 'date',
        'sales_summary' => 'array',
        'total_sales' => 'decimal:2',
    ];

    protected function salesSummary(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => json_decode($value, true),
            set: fn (array $value) => json_encode($value),
        );
    }

    public function getFileUrl(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFileExists(): bool
    {
        return file_exists(storage_path('app/public/' . $this->file_path));
    }
}
