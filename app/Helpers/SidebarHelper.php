<?php

namespace App\Helpers;

use App\Models\SidebarLink;
use App\Models\Task;
use App\Models\ExecutivePlanCell;
use App\Models\User;
use App\Models\Election;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SidebarHelper
{
    private static $calculatedLinks = null;
    
    public static function getSidebarLinksWithBadges()
    {
        // Return cached result if already calculated
        if (self::$calculatedLinks !== null) {
            return self::$calculatedLinks;
        }
        
        $sidebarLinks = SidebarLink::getSidebarLinks();
        
        foreach ($sidebarLinks as $link) {
            $link->badge_count = self::calculateBadgeForLink($link);
            $link->is_active = self::isLinkActive($link);
            
            if ($link->children) {
                foreach ($link->children as $child) {
                    $child->badge_count = self::calculateBadgeForLink($child);
                    $child->is_active = self::isLinkActive($child);
                }
            }
        }
        
        // Cache the result for this request
        self::$calculatedLinks = $sidebarLinks;
        
        return $sidebarLinks;
    }
    
    private static function calculateBadgeForLink($link)
    {
        if (!$link->url) return 0;
        
        $badgeCount = 0;
        
        // Tasks badges
        if (str_contains($link->url, 'tasks.')) {
            $badgeCount += self::calculateTasksBadges($link);
        }
        
        // Evaluate badges
        if (str_contains($link->url, 'evaluate')) {
            $badgeCount += self::calculateEvaluateBadges();
        }
        
        // Executive plan badges
        if (str_contains($link->url, 'executive-plan')) {
            $badgeCount += self::calculateExecutivePlanBadges();
        }
        
        // Approval badges
        if (str_contains($link->url, 'need-approval') || str_contains($link->url, 'approve')) {
            $badgeCount += self::calculateApprovalBadges($link);
        }
        
        return $badgeCount;
    }
    
    private static function calculateTasksBadges($link)
    {
        $badgeCount = 0;
        
        if (Auth::user()->can('task.executive-plan') && str_contains($link->url, 'executive-plan')) {
            $badge = ExecutivePlanCell::whereDate('date', '>=', now()->startOfDay())
                ->whereDate('date', '<=', now()->endOfDay())
                ->count();
            $badgeCount += $badge;
        }
        
        if ((Auth::user()->can('task.approve-department') || Auth::user()->can('task.approve-subordinates')) && 
            str_contains($link->url, 'need-approval')) {
            $subordinateUsers = Auth::user()->subordinateUsers();
            $subordinateUserIds = $subordinateUsers->pluck('id')->toArray();
            
            $badge = Task::where('status', 'submitted')
                ->where(function ($query) use ($subordinateUserIds) {
                    $query->where('assigned_by', Auth::id())
                        ->orWhereIn('user_id', $subordinateUserIds);
                })
                ->whereNot('user_id', Auth::id())
                ->count();
            $badgeCount += $badge;
        }
        
        return $badgeCount;
    }
    
    private static function calculateEvaluateBadges()
    {
        if (!Auth::user()->can('evaluate')) return 0;
        
        $evaluations = User::with([
            'evaluations',
            'timesheets' => function ($query) {
                $query->whereDate('start_at', now());
            },
        ])
            ->whereDoesntHave('evaluations')
            ->whereHas('timesheets', function ($query) {
                $query->whereDate('start_at', now());
            })
            ->count();
            
        return $evaluations;
    }
    
    private static function calculateExecutivePlanBadges()
    {
        if (!Auth::user()->can('executive-plan.view-any')) return 0;
        
        return ExecutivePlanCell::whereDate('date', '>=', now()->startOfDay())
            ->whereDate('date', '<=', now()->endOfDay())
            ->count();
    }
    
    private static function calculateApprovalBadges($link)
    {
        $badgeCount = 0;
        
        // Task approvals
        if (str_contains($link->url, 'tasks') && 
            (Auth::user()->can('task.approve-department') || Auth::user()->can('task.approve-subordinates'))) {
            $subordinateUsers = Auth::user()->subordinateUsers();
            $subordinateUserIds = $subordinateUsers->pluck('id')->toArray();
            
            $badge = Task::where('status', 'submitted')
                ->where(function ($query) use ($subordinateUserIds) {
                    $query->where('assigned_by', Auth::id())
                        ->orWhereIn('user_id', $subordinateUserIds);
                })
                ->whereNot('user_id', Auth::id())
                ->count();
            $badgeCount += $badge;
        }
        
        // Request approvals
        if (str_contains($link->url, 'requests') && Auth::user()->can('request.approve')) {
            $badge = \App\Models\Request::where('status', 'pending')
                ->where('assigned_to', Auth::id())
                ->count();
            $badgeCount += $badge;
        }
        
        return $badgeCount;
    }
    
    private static function isLinkActive($link)
    {
        if (!$link->url) return false;
        
        // Handle external URLs
        if ($link->is_external) {
            return request()->url() === $link->url;
        }
        
        // Handle route-based URLs
        try {
            return request()->routeIs($link->url . '*');
        } catch (\Exception $e) {
            return false;
        }
    }
    
    public static function getActiveElection()
    {
        return Election::where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where('is_public', true)
            ->first();
    }
    
    // Method to clear cache when needed
    public static function clearCache()
    {
        self::$calculatedLinks = null;
    }
} 