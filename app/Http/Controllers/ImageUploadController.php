<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $file = $request->file('image');
            
            // Ensure the storage directory exists
            if (!Storage::disk('public')->exists('editor_images')) {
                Storage::disk('public')->makeDirectory('editor_images');
            }
            
            // Store the file using the public disk (like profile pictures)
            $path = $file->store('editor_images', 'public');
            
            // Return the public URL
            $url = Storage::url($path);
            
            return response()->json([
                'success' => true,
                'url' => $url
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
} 