<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\SettingsHelper;
use App\Models\User;

class TestEmailSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email settings and send a test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Email Settings...');
        $this->newLine();

        // Test email settings
        $fromAddress = SettingsHelper::get('mail_from_address');
        $fromName = SettingsHelper::get('mail_from_name');
        $emailEnabled = SettingsHelper::get('email_notifications');

        $this->info("From Address: " . ($fromAddress ?: 'Not set'));
        $this->info("From Name: " . ($fromName ?: 'Not set'));
        $this->info("Email Notifications Enabled: " . ($emailEnabled ? 'Yes' : 'No'));

        // Test with a user
        $userId = $this->argument('user_id') ?: 1;
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return 1;
        }

        $this->info("Testing with user: {$user->name} ({$user->email})");

        // Test NotificationHelper
        $this->info("\nTesting NotificationHelper...");
        $results = \App\Helpers\NotificationHelper::sendNotification(
            $user,
            'Test email from settings',
            'dashboard',
            null,
            'system_alert'
        );

        $this->info("Database notification: " . ($results['database'] ? 'Sent' : 'Failed'));
        $this->info("Email notification: " . ($results['email'] ? 'Sent' : 'Failed'));
        $this->info("Push notification: " . ($results['push'] ? 'Sent' : 'Failed'));

        $this->newLine();
        $this->info('Email settings test completed!');
        
        if (!$emailEnabled) {
            $this->warn('⚠️  Email notifications are disabled in settings!');
        }
    }
} 