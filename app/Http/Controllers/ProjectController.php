<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Website;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    private function getWebsite(Request $request)
    {
        $websiteId = $request->input('website_id') ?? $request->query('website_id');

        if (! $websiteId) {
            return null;
        }

        return Website::where('_id', $websiteId)
            ->where('user_id', $request->user()->id)
            ->first();
    }

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

        $projects = Project::where('website_id', $websiteId)->get();

        return response()->json(['status' => 'success', 'data' => $projects]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'website_id' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'string',
            'project_url' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
        ]);

        $website = Website::where('_id', $validated['website_id'])
            ->where('user_id', $request->user()->id)
            ->exists();

        if (! $website) {
            return response()->json(['status' => 'error', 'message' => 'Website not found.'], 404);
        }

        $project = Project::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Project created successfully.',
            'data' => $project,
        ], 201);
    }

    public function show(Request $request, string $id)
    {
        $project = Project::find($id);

        if (! $project) {
            return response()->json(['status' => 'error', 'message' => 'Project not found.'], 404);
        }

        $website = Website::where('_id', $project->website_id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if (! $website) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden.'], 403);
        }

        return response()->json(['status' => 'success', 'data' => $project]);
    }

    public function update(Request $request, string $id)
    {
        $project = Project::find($id);

        if (! $project) {
            return response()->json(['status' => 'error', 'message' => 'Project not found.'], 404);
        }

        $website = Website::where('_id', $project->website_id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if (! $website) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden.'], 403);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'string',
            'project_url' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
        ]);

        $project->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Project updated successfully.',
            'data' => $project,
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        $project = Project::find($id);

        if (! $project) {
            return response()->json(['status' => 'error', 'message' => 'Project not found.'], 404);
        }

        $website = Website::where('_id', $project->website_id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if (! $website) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden.'], 403);
        }

        $project->delete();

        return response()->json(['status' => 'success', 'message' => 'Project deleted successfully.']);
    }
}
