<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sale;
use App\Models\DailySalesReport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\DailySalesReportMail;
use App\Models\User;
use App\Jobs\SendDailySalesReportEmail;

class GenerateDailySalesReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:daily-report {date?} {--no-email : Skip sending email notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily sales report in Excel format and optionally send email notification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get date from argument or use yesterday
        $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : Carbon::yesterday();
        
        $this->info("Generating daily sales report for {$date->format('Y-m-d')}...");

        // Check if report already exists
        $existingReport = DailySalesReport::where('report_date', $date->format('Y-m-d'))->first();
        if ($existingReport) {
            if (!$this->confirm("Report for {$date->format('Y-m-d')} already exists. Regenerate?")) {
                $this->info('Report generation cancelled.');
                return;
            }
        }

        // Get sales data for the date
        $sales = Sale::with('menuItem')
            ->whereDate('saleDate', $date)
            ->get();

        if ($sales->isEmpty()) {
            $this->warn("No sales found for {$date->format('Y-m-d')}");
            return;
        }

        // Calculate totals and summary
        $totalSales = $sales->sum(function ($sale) {
            return $sale->amount * $sale->menuItem->price;
        });

        $totalOrders = $sales->count();

        // Group sales by menu item
        $salesSummary = $sales->groupBy('itemId')->map(function ($itemSales) {
            $menuItem = $itemSales->first()->menuItem;
            $totalAmount = $itemSales->sum('amount');
            $totalRevenue = $totalAmount * $menuItem->price;

            return [
                'menu_item_id' => $menuItem->id,
                'menu_item_name' => $menuItem->naam,
                'menu_number' => $menuItem->menunummer,
                'menu_addition' => $menuItem->menu_toevoeging,
                'category' => $menuItem->soortgerecht,
                'price' => $menuItem->price,
                'total_amount' => $totalAmount,
                'total_revenue' => $totalRevenue,
            ];
        })->values()->toArray();

        // Generate Excel file
        $fileName = $this->generateExcelReport($date, $sales, $salesSummary, $totalSales, $totalOrders);

        // Save report to database
        if ($existingReport) {
            // Delete old file if exists
            if ($existingReport->getFileExists()) {
                Storage::disk('public')->delete($existingReport->file_path);
            }
            $report = $existingReport;
            $existingReport->update([
                'file_path' => $fileName,
                'total_sales' => $totalSales,
                'total_orders' => $totalOrders,
                'sales_summary' => $salesSummary,
            ]);
        } else {
            $report = DailySalesReport::create([
                'report_date' => $date->format('Y-m-d'),
                'file_path' => $fileName,
                'total_sales' => $totalSales,
                'total_orders' => $totalOrders,
                'sales_summary' => $salesSummary,
            ]);
        }

        // Send email notifications to admin users (unless --no-email flag is used)
        if (!$this->option('no-email')) {
            $this->sendEmailNotifications($report);
        }

        $this->info("Daily sales report generated successfully!");
        $this->info("File: storage/app/public/{$fileName}");
        $this->info("Total Sales: €" . number_format($totalSales, 2));
        $this->info("Total Orders: {$totalOrders}");
    }

    /**
     * Send email notifications to admin users
     */
    private function sendEmailNotifications(DailySalesReport $report): void
    {
        $adminUsers = User::where('is_admin', true)->get();
        
        if ($adminUsers->isEmpty()) {
            $this->warn("No admin users found to send email notifications.");
            return;
        }

        $this->info("Queuing email notifications for " . $adminUsers->count() . " admin user(s)...");

        // Dispatch job to send emails in the background
        SendDailySalesReportEmail::dispatch($report);
        
        $this->info("Email notifications have been queued successfully.");
    }

    private function generateExcelReport(Carbon $date, $sales, $salesSummary, $totalSales, $totalOrders): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setTitle('Dagelijks Verkoopoverzicht');

        // Header
        $sheet->setCellValue('A1', 'De Gouden Draak - Dagelijks Verkoopoverzicht');
        $sheet->setCellValue('A2', 'Datum: ' . $date->format('d-m-Y'));
        $sheet->setCellValue('A3', 'Gegenereerd op: ' . now()->format('d-m-Y H:i'));

        // Summary section
        $sheet->setCellValue('A5', 'SAMENVATTING');
        $sheet->setCellValue('A6', 'Totale Omzet:');
        $sheet->setCellValue('B6', '€' . number_format($totalSales, 2));
        $sheet->setCellValue('A7', 'Aantal Bestellingen:');
        $sheet->setCellValue('B7', $totalOrders);

        // VAT calculation (similar to existing implementation)
        $priceExVat = ($totalSales / 106) * 100;
        $vatAmount = $totalSales - $priceExVat;
        
        $sheet->setCellValue('A8', 'Subtotaal (excl. BTW):');
        $sheet->setCellValue('B8', '€' . number_format($priceExVat, 2));
        $sheet->setCellValue('A9', 'BTW (6%):');
        $sheet->setCellValue('B9', '€' . number_format($vatAmount, 2));

        // Products table header
        $row = 11;
        $sheet->setCellValue('A' . $row, 'PRODUCTEN OVERZICHT');
        $row += 2;

        $headers = ['Menunummer', 'Gerecht', 'Categorie', 'Prijs per stuk', 'Aantal Verkocht', 'Totale Omzet'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F'];

        foreach ($headers as $index => $header) {
            $sheet->setCellValue($columns[$index] . $row, $header);
        }

        // Style headers
        $headerRange = 'A' . $row . ':F' . $row;
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle($headerRange)->getFill()->getStartColor()->setRGB('4F46E5');
        $sheet->getStyle($headerRange)->getFont()->getColor()->setRGB('FFFFFF');

        // Add data rows
        $row++;
        foreach ($salesSummary as $item) {
            $menuNumber = '';
            if ($item['menu_number']) {
                $menuNumber = $item['menu_number'] . ($item['menu_addition'] ?? '');
            }

            $sheet->setCellValue('A' . $row, $menuNumber);
            $sheet->setCellValue('B' . $row, $item['menu_item_name']);
            $sheet->setCellValue('C' . $row, $item['category']);
            $sheet->setCellValue('D' . $row, '€' . number_format($item['price'], 2));
            $sheet->setCellValue('E' . $row, $item['total_amount']);
            $sheet->setCellValue('F' . $row, '€' . number_format($item['total_revenue'], 2));
            $row++;
        }

        // Style the data range
        if (count($salesSummary) > 0) {
            $dataRange = 'A13:F' . ($row - 1);
            $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        // Auto-size columns
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Style title and summary
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A11')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A6:A9')->getFont()->setBold(true);

        // Save file
        $fileName = 'daily-reports/daily-sales-report-' . $date->format('Y-m-d') . '.xlsx';
        $filePath = storage_path('app/public/' . $fileName);
        
        // Create directory if it doesn't exist
        $directory = dirname($filePath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return $fileName;
    }
}
