<?php

namespace App\Helpers;

use App\Models\User;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\NotificationMail;

class NotificationHelper
{
    /**
     * Send a notification to a user
     *
     * @param  User  $user 
     * @param  string  $message
     * @param  string|null  $action_route
     * @param  string|null  $action_params
     * @param  string  $type
     * @return array
     * 
     **/
    public static function sendNotification(User $user, $message, $action_route = null, $action_params = null, $type = 'general'): array
    {
        $results = [
            'database' => false,
            'email' => false,
            'push' => false,
        ];

        // Check if notifications are enabled globally
        if (!self::areNotificationsEnabled()) {
            return $results;
        }

        // Check specific notification type settings
        if (!self::isNotificationTypeEnabled($type)) {
            return $results;
        }

        // Create the database notification
        $user->notifications()->create([
            'content' => $message,
            'action_route' => $action_route,
            'action_params' => $action_params,
            'type' => $type,
        ]);
        $results['database'] = true;

        // Automatically send email notification if enabled
        if (self::isEmailNotificationsEnabled()) {
            $emailSubject = self::getEmailSubjectByType($type, $message);
            $results['email'] = self::sendEmailNotification($user, $emailSubject, $message, $action_route, $action_params);
        }

        // Automatically send push notification if enabled
        if (self::isPushNotificationsEnabled()) {
            $pushTitle = self::getPushTitleByType($type, $message);
            $results['push'] = self::sendPushNotification($user, $pushTitle, $message, $action_route, $action_params);
        }

        return $results;
    }

    /**
     * Send email notification if enabled
     *
     * @param  User  $user 
     * @param  string  $subject
     * @param  string  $message
     * @param  string|null  $action_route
     * @param  string|null  $action_params
     * @return bool
     * 
     **/
    public static function sendEmailNotification(User $user, $subject, $message, $action_route = null, $action_params = null): bool
    {
        // Check if email notifications are enabled
        if (!self::isEmailNotificationsEnabled()) {
            return false;
        }

        try {
            // Send the email notification
            Mail::to($user->email)->send(new NotificationMail($subject, $message, $action_route, $action_params));
            
            return true;
        } catch (\Exception $e) {
            // Log the error but don't throw it to prevent breaking the notification flow
            Log::error('Failed to send email notification', [
                'user_id' => $user->id,
                'email' => $user->email,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Send push notification if enabled
     *
     * @param  User  $user 
     * @param  string  $title
     * @param  string  $message
     * @param  string|null  $action_route
     * @param  string|null  $action_params
     * @return bool
     * 
     **/
    public static function sendPushNotification(User $user, $title, $message, $action_route = null, $action_params = null): bool
    {
        // Check if push notifications are enabled
        if (!self::isPushNotificationsEnabled()) {
            return false;
        }

        // TODO: Implement push notification logic
        // This could integrate with services like Firebase, OneSignal, etc.
        
        return true;
    }

    /**
     * Send attendance reminder notification
     *
     * @param  User  $user 
     * @param  string  $message
     * @return array
     * 
     **/
    public static function sendAttendanceReminder(User $user, $message): array
    {
        // Check if attendance reminders are enabled
        if (!self::isAttendanceRemindersEnabled()) {
            return ['database' => false, 'email' => false, 'push' => false];
        }

        return self::sendNotification($user, $message, null, null, 'attendance_reminder');
    }

    /**
     * Check if notifications are globally enabled
     *
     * @return bool
     * 
     **/
    public static function areNotificationsEnabled(): bool
    {
        // For now, return true. You can add a global notification setting if needed
        return true;
    }

    /**
     * Check if email notifications are enabled
     *
     * @return bool
     * 
     **/
    public static function isEmailNotificationsEnabled(): bool
    {
        return SettingsHelper::get('email_notifications', true);
    }

    /**
     * Check if push notifications are enabled
     *
     * @return bool
     * 
     **/
    public static function isPushNotificationsEnabled(): bool
    {
        return SettingsHelper::get('push_notifications', true);
    }

    /**
     * Check if attendance reminders are enabled
     *
     * @return bool
     * 
     **/
    public static function isAttendanceRemindersEnabled(): bool
    {
        return SettingsHelper::get('attendance_reminders', true);
    }

    /**
     * Check if a specific notification type is enabled
     *
     * @param  string  $type
     * @return bool
     * 
     **/
    public static function isNotificationTypeEnabled($type): bool
    {
        switch ($type) {
            case 'email':
                return self::isEmailNotificationsEnabled();
            case 'push':
                return self::isPushNotificationsEnabled();
            case 'attendance_reminder':
                return self::isAttendanceRemindersEnabled();
            default:
                return true; // Default to enabled for unknown types
        }
    }

    /**
     * Send comprehensive notification (all types if enabled)
     *
     * @param  User  $user 
     * @param  string  $title
     * @param  string  $message
     * @param  string|null  $action_route
     * @param  string|null  $action_params
     * @return array
     * 
     **/
    public static function sendComprehensiveNotification(User $user, $title, $message, $action_route = null, $action_params = null): array
    {
        $results = [
            'database' => false,
            'email' => false,
            'push' => false,
        ];

        // Send database notification
        $results['database'] = self::sendNotification($user, $message, $action_route, $action_params);

        // Send email notification if enabled
        if (self::isEmailNotificationsEnabled()) {
            $results['email'] = self::sendEmailNotification($user, $title, $message, $action_route, $action_params);
        }

        // Send push notification if enabled
        if (self::isPushNotificationsEnabled()) {
            $results['push'] = self::sendPushNotification($user, $title, $message, $action_route, $action_params);
        }

        return $results;
    }

    /**
     * Get email subject based on notification type
     *
     * @param  string  $type
     * @param  string  $message
     * @return string
     * 
     **/
    public static function getEmailSubjectByType($type, $message): string
    {
        switch ($type) {
            case 'task_assigned':
                return 'New Task Assigned';
            case 'task_completed':
                return 'Task Completed';
            case 'attendance_reminder':
                return 'Attendance Reminder';
            case 'approval_required':
                return 'Approval Required';
            case 'system_alert':
                return 'System Alert';
            default:
                return 'New Notification';
        }
    }

    /**
     * Get push notification title based on notification type
     *
     * @param  string  $type
     * @param  string  $message
     * @return string
     * 
     **/
    public static function getPushTitleByType($type, $message): string
    {
        switch ($type) {
            case 'task_assigned':
                return 'New Task';
            case 'task_completed':
                return 'Task Done';
            case 'attendance_reminder':
                return 'Attendance';
            case 'approval_required':
                return 'Approval';
            case 'system_alert':
                return 'Alert';
            default:
                return 'Notification';
        }
    }

    /**
     * Get notification settings summary
     *
     * @return array
     * 
     **/
    public static function getNotificationSettings(): array
    {
        return [
            'email_notifications' => self::isEmailNotificationsEnabled(),
            'push_notifications' => self::isPushNotificationsEnabled(),
            'attendance_reminders' => self::isAttendanceRemindersEnabled(),
        ];
    }
}
