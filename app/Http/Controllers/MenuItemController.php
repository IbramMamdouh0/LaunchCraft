<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Website;
use Illuminate\Http\Request;

class MenuItemController extends Controller
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

        $menuItems = MenuItem::where('website_id', $websiteId)->get();

        return response()->json(['status' => 'success', 'data' => $menuItems]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'website_id' => 'required|string',
            'category_id' => 'nullable|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string|max:255',
            'is_available' => 'nullable|boolean',
        ]);

        $website = Website::where('_id', $validated['website_id'])
            ->where('user_id', $request->user()->id)
            ->exists();

        if (! $website) {
            return response()->json(['status' => 'error', 'message' => 'Website not found.'], 404);
        }

        $validated['is_available'] = $validated['is_available'] ?? true;

        $menuItem = MenuItem::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Menu item created successfully.',
            'data' => $menuItem,
        ], 201);
    }

    public function show(Request $request, string $id)
    {
        $menuItem = MenuItem::find($id);

        if (! $menuItem) {
            return response()->json(['status' => 'error', 'message' => 'Menu item not found.'], 404);
        }

        $website = Website::where('_id', $menuItem->website_id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if (! $website) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden.'], 403);
        }

        return response()->json(['status' => 'success', 'data' => $menuItem]);
    }

    public function update(Request $request, string $id)
    {
        $menuItem = MenuItem::find($id);

        if (! $menuItem) {
            return response()->json(['status' => 'error', 'message' => 'Menu item not found.'], 404);
        }

        $website = Website::where('_id', $menuItem->website_id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if (! $website) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden.'], 403);
        }

        $validated = $request->validate([
            'category_id' => 'nullable|string',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|string|max:255',
            'is_available' => 'nullable|boolean',
        ]);

        $menuItem->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Menu item updated successfully.',
            'data' => $menuItem,
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        $menuItem = MenuItem::find($id);

        if (! $menuItem) {
            return response()->json(['status' => 'error', 'message' => 'Menu item not found.'], 404);
        }

        $website = Website::where('_id', $menuItem->website_id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if (! $website) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden.'], 403);
        }

        $menuItem->delete();

        return response()->json(['status' => 'success', 'message' => 'Menu item deleted successfully.']);
    }
}
