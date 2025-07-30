<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('profile.edit') => __('breadcrumbs.profile.edit')
        ];

        return view('profile.edit', [
            'user' => $request->user(),
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|max:5120',
            'shift_start' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'shift_end' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'work_location' => 'nullable|string|max:255',
        ]);

        try {
            // Handle profile image upload
            if ($request->hasFile('profile_picture')) {
                try {
                    // Delete old profile picture if exists
                    if ($user->profile_picture) {
                        Storage::disk('public')->delete($user->profile_picture);
                    }
                    
                    $file = $request->file('profile_picture');
                    $path = $file->store('profile_pictures', 'public');
                    $data['profile_picture'] = $path;
                } catch (\Exception $e) {
                    return Redirect::route('profile.edit')->withErrors(['profile_picture' => 'Error uploading image: ' . $e->getMessage()]);
                }
            }

            // Only update allowed fields
            $user->fill(array_intersect_key($data, array_flip([
                'name', 'email', 'phone', 'profile_picture', 'shift_start', 'shift_end', 'work_location'
            ])));

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();
            flash()->success(__('users.edit.success'));
        } catch (\Exception $e) {
            flash()->error(__('users.edit.error'));
        }

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function show()
    {
        $user = auth()->user();
        return view('users.show', compact('user'));
    }
}
