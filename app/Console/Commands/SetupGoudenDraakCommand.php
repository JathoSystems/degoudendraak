<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupGoudenDraakCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'goudendraak:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up the migrated De Gouden Draak system with required database tables and initial data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting setup of De Gouden Draak system...');

        // Run the migrations
        $this->info('Running database migrations...');
        Artisan::call('migrate:fresh', [
            '--force' => true,
        ]);
        $this->info('Migrations completed successfully.');

        // Seed the database with initial data
        $this->info('Seeding the database with initial data...');
        Artisan::call('db:seed', [
            '--force' => true,
        ]);
        $this->info('Database seeding completed successfully.');

        $this->newLine();
        $this->info('✅ De Gouden Draak system has been set up successfully!');
        $this->info('You can now access the system with the following credentials:');
        $this->newLine();
        $this->line('Admin User:');
        $this->line('Email: admin@goudendraak.nl');
        $this->line('Password: test');
        $this->newLine();
        $this->line('Regular User:');
        $this->line('Email: user@goudendraak.nl');
        $this->line('Password: password');
        $this->newLine();
        $this->info('Remember to change these passwords in a production environment!');

        return Command::SUCCESS;
    }
}
