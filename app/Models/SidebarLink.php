<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class SidebarLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'icon',
        'permission',
        'visibility',
        'is_external',
        'is_active',
        'parent_id',
        'order',
    ];

    protected $casts = [
        'is_external' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(SidebarLink::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(SidebarLink::class, 'parent_id')->orderBy('order');
    }

    public function getDisplayTitleAttribute(): string
    {
        return $this->title;
    }

    public function hasAccess(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Check visibility settings
        $isAuthenticated = Auth::check();
        
        switch ($this->visibility) {
            case 'guest':
                // Only show to non-authenticated users
                return !$isAuthenticated;
            case 'authenticated':
                // Only show to authenticated users
                if (!$isAuthenticated) {
                    return false;
                }
                break;
            case 'all':
                // Show to everyone (both authenticated and guest)
                break;
            default:
                // Default to authenticated only
                if (!$isAuthenticated) {
                    return false;
                }
        }

        // Check permissions if user is authenticated
        if ($this->permission && $isAuthenticated) {
            $permissions = explode(',', $this->permission);
            $hasPermission = false;
            
            foreach ($permissions as $permission) {
                if (Auth::user()->can(trim($permission))) {
                    $hasPermission = true;
                    break;
                }
            }
            
            if (!$hasPermission) {
                return false;
            }
        }

        return true;
    }

    public static function getSidebarLinks()
    {
        return static::with(['children' => function ($query) {
            $query->where('is_active', true);
        }])
        ->whereNull('parent_id')
        ->where('is_active', true)
        ->orderBy('order')
        ->get()
        ->filter(function ($link) {
            // Filter parent links
            if (!$link->hasAccess()) {
                return false;
            }
            
            // Filter children links
            $link->children = $link->children->filter(function ($child) {
                return $child->hasAccess();
            });
            
            return true;
        });
    }

    public static function reorder(array $orderData): void
    {
        foreach ($orderData as $item) {
            static::where('id', $item['id'])->update(['order' => $item['order']]);
        }
    }
} 