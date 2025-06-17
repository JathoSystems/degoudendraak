<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DailySalesReport;

class ListDailySalesReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:list-reports {--limit=10 : Number of reports to show}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List recent daily sales reports';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        
        $reports = DailySalesReport::orderBy('report_date', 'desc')
            ->limit($limit)
            ->get();

        if ($reports->isEmpty()) {
            $this->info('No reports found.');
            return;
        }

        $this->info("Showing last {$reports->count()} daily sales reports:");
        $this->line('');

        $headers = ['Date', 'Total Sales', 'Orders', 'File Status', 'Generated'];
        $rows = [];

        foreach ($reports as $report) {
            $rows[] = [
                $report->report_date->format('Y-m-d'),
                '€' . number_format($report->total_sales, 2),
                $report->total_orders,
                $report->getFileExists() ? '✅ Available' : '❌ Missing',
                $report->created_at->format('Y-m-d H:i')
            ];
        }

        $this->table($headers, $rows);
        
        $this->line('');
        $this->info('Use "php artisan sales:daily-report [date]" to generate a new report.');
        $this->info('Reports are automatically generated daily at 06:00.');
    }
}
