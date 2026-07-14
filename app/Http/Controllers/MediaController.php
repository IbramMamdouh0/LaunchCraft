<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index(Request $request, Website $website)
    {
        if ($website->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $media = $website->media;

        return response()->json($media);
    }

    public function store(Request $request, Website $website)
    {
        if ($website->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validated = $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $file = $validated['file'];
        $filename = $file->hashName();
        $path = $file->store('uploads', 'public');

        $media = Media::create([
            'website_id' => $website->id,
            'filename' => $filename,
            'path' => $path,
            'size' => $file->getSize(),
        ]);

        $media->url = asset('storage/' . $path);

        return response()->json($media, 201);
    }

    public function destroy(Request $request, Website $website, Media $medium)
    {
        if ($website->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        if ($medium->website_id !== $website->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        Storage::disk('public')->delete($medium->path);

        $medium->delete();

        return response()->json(['message' => 'Media deleted successfully.']);
    }
}
