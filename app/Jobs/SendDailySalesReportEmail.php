<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\DailySalesReportMail;
use App\Models\DailySalesReport;
use App\Models\User;

class SendDailySalesReportEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public DailySalesReport $report
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Job started: Sending daily sales report email for {$this->report->report_date->format('Y-m-d')}...");

        $adminUsers = User::where('isAdmin', true)->get();
        Log::info("Found {$adminUsers->count()} admin users");

        if ($adminUsers->isEmpty()) {
            Log::warning("No admin users found with isAdmin = true");
            return;
        }

        foreach ($adminUsers as $admin) {
            Log::info("Sending email to admin: {$admin->email}");
            Mail::to($admin->email)->send(new DailySalesReportMail($this->report));
            Log::info("Email sent successfully to: {$admin->email}");
        }

        Log::info("Job completed: All emails sent successfully");
    }
}
