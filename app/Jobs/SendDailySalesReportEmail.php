<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
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
        $adminUsers = User::where('is_admin', true)->get();
        
        foreach ($adminUsers as $admin) {
            Mail::to($admin->email)->send(new DailySalesReportMail($this->report));
        }
    }
}
