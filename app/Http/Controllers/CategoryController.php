<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Website;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $websiteId = $request->query('website_id');

        if (! $websiteId) {
            return response()->json(['status' => 'error', 'message' => 'website_id is required.'], 400);
        }

        $website = Website::where('_id', $websiteId)
            ->where('user_id', $request->user()->id)
            ->exists();

        if (! $website) {
            return response()->json(['status' => 'error', 'message' => 'Website not found.'], 404);
        }

        $categories = Category::where('website_id', $websiteId)
            ->orderBy('sort_order')
            ->get();

        return response()->json(['status' => 'success', 'data' => $categories]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'website_id' => 'required|string',
            'name' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $website = Website::where('_id', $validated['website_id'])
            ->where('user_id', $request->user()->id)
            ->exists();

        if (! $website) {
            return response()->json(['status' => 'error', 'message' => 'Website not found.'], 404);
        }

        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $category = Category::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Category created successfully.',
            'data' => $category,
        ], 201);
    }

    public function show(Request $request, string $id)
    {
        $category = Category::find($id);

        if (! $category) {
            return response()->json(['status' => 'error', 'message' => 'Category not found.'], 404);
        }

        $website = Website::where('_id', $category->website_id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if (! $website) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden.'], 403);
        }

        return response()->json(['status' => 'success', 'data' => $category]);
    }

    public function update(Request $request, string $id)
    {
        $category = Category::find($id);

        if (! $category) {
            return response()->json(['status' => 'error', 'message' => 'Category not found.'], 404);
        }

        $website = Website::where('_id', $category->website_id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if (! $website) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden.'], 403);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $category->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully.',
            'data' => $category,
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        $category = Category::find($id);

        if (! $category) {
            return response()->json(['status' => 'error', 'message' => 'Category not found.'], 404);
        }

        $website = Website::where('_id', $category->website_id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if (! $website) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden.'], 403);
        }

        $category->delete();

        return response()->json(['status' => 'success', 'message' => 'Category deleted successfully.']);
    }
}
