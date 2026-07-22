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

        return response()->json([
            'status' => 'success',
            'data' => $websites,
        ]);
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

        return response()->json([
            'status' => 'success',
            'message' => 'Website created successfully.',
            'data' => $website,
        ], 201);
    }

    public function show(Request $request, Website $website)
    {
        if ($website->user_id !== $request->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden.',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $website,
        ]);
    }

    public function update(Request $request, Website $website)
    {
        if ($website->user_id !== $request->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden.',
            ], 403);
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

        return response()->json([
            'status' => 'success',
            'message' => 'Website updated successfully.',
            'data' => $website,
        ]);
    }

    public function destroy(Request $request, Website $website)
    {
        if ($website->user_id !== $request->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden.',
            ], 403);
        }

        $website->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Website deleted successfully.',
        ]);
    }

    public function publish(Request $request, Website $website)
    {
        if ($website->user_id !== $request->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden.',
            ], 403);
        }

        $validated = $request->validate([
            'theme.primary_color' => 'nullable|string|max:20',
            'theme.secondary_color' => 'nullable|string|max:20',
            'theme.font_family' => 'nullable|string|max:100',
            'sections' => 'nullable|array',
            'sections.*.type' => 'required_with:sections|string|max:50',
            'sections.*.sort_order' => 'required_with:sections|integer|min:0',
            'sections.*.data' => 'nullable|array',
            'sections.*.style' => 'nullable|array',
        ]);

        $website->update([
            'theme' => $validated['theme'] ?? $website->theme,
            'sections' => $validated['sections'] ?? $website->sections,
            'is_published' => true,
            'status' => 'published',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Website published successfully.',
            'data' => [
                'website' => [
                    'id' => $website->id,
                    'name' => $website->name,
                    'domain' => $website->domain,
                    'is_published' => true,
                ],
                'theme' => $website->theme,
                'sections' => $website->sections,
            ],
        ]);
    }
}
