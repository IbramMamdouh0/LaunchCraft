<?php

namespace App\Http\Controllers;

use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebsiteController extends Controller
{
    public function index(Request $request)
    {
        $websites = $request->user()->websites;

        return response()->json($websites);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'business_type' => 'required|string|max:255',
            'template' => 'nullable|string|max:255',
            'theme' => 'nullable|array',
            'pages' => 'nullable|array',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(6);
        $validated['status'] = 'draft';

        $website = Website::create($validated);

        return response()->json($website, 201);
    }

    public function show(Request $request, Website $website)
    {
        if ($website->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return response()->json($website);
    }

    public function update(Request $request, Website $website)
    {
        if ($website->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'business_type' => 'nullable|string|max:255',
            'template' => 'nullable|string|max:255',
            'theme' => 'nullable|array',
            'status' => 'nullable|string|in:draft,published',
            'pages' => 'nullable|array',
        ]);

        $website->update($validated);

        return response()->json($website);
    }

    public function destroy(Request $request, Website $website)
    {
        if ($website->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $website->delete();

        return response()->json(['message' => 'Website deleted successfully.']);
    }

    public function publish(Request $request, Website $website)
    {
        if ($website->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $website->update(['status' => 'published']);

        return response()->json(['message' => 'Website published successfully.', 'website' => $website]);
    }
}
