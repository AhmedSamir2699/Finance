<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StaticPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_description',
        'meta_keywords',
        'visibility',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
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
                return $isAuthenticated;
            case 'all':
                // Show to everyone (both authenticated and guest)
                return true;
            default:
                // Default to authenticated only
                return $isAuthenticated;
        }
    }

    public static function getPublicPages()
    {
        return static::where('is_active', true)
            ->get()
            ->filter(function ($page) {
                return $page->hasAccess();
            });
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    public function getPublicUrlAttribute()
    {
        return route('static-pages.show', $this->slug);
    }

    public function getCopyLinkAttribute()
    {
        return $this->public_url;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            if (Auth::check()) {
                $page->created_by = Auth::id();
            }
        });

        static::updating(function ($page) {
            if (Auth::check()) {
                $page->updated_by = Auth::id();
            }
        });
    }
} 