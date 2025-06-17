<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailySalesReport;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use App\Jobs\SendDailySalesReportEmail;

class DailySalesReportController extends Controller
{
    public function index()
    {
        $reports = DailySalesReport::orderBy('report_date', 'desc')->paginate(20);
        
        return view('reports.daily-sales.index', compact('reports'));
    }

    public function show(DailySalesReport $report)
    {
        return view('reports.daily-sales.show', compact('report'));
    }

    public function download(DailySalesReport $report)
    {
        if (!$report->getFileExists()) {
            return back()->with('error', 'Het rapport bestand kon niet worden gevonden.');
        }

        $filePath = storage_path('app/public/' . $report->file_path);
        $fileName = 'daily-sales-report-' . $report->report_date->format('Y-m-d') . '.xlsx';

        return response()->download($filePath, $fileName);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        // Run the command to generate the report
        \Artisan::call('sales:daily-report', [
            'date' => $request->date,
            '--no-email' => true // Don't send email from manual generation
        ]);

        $output = \Artisan::output();

        if (str_contains($output, 'successfully')) {
            return back()->with('success', 'Rapport succesvol gegenereerd!');
        } else {
            return back()->with('error', 'Er is een fout opgetreden bij het genereren van het rapport.');
        }
    }

    public function sendEmail(DailySalesReport $report)
    {
        if (!$report->getFileExists()) {
            return back()->with('error', 'Het rapport bestand kon niet worden gevonden.');
        }

        // Dispatch email job
        SendDailySalesReportEmail::dispatch($report);

        return back()->with('success', 'Email notificaties zijn in de wachtrij geplaatst en worden binnenkort verzonden naar alle admins.');
    }
}
