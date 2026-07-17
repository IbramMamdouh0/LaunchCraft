<?php

namespace App\Http\Controllers;

use App\Models\Website;
use Illuminate\Http\Request;

class MobileWebsiteController extends Controller
{
    public function show(Request $request)
    {
        $website = Website::where('user_id', $request->user()->id)
            ->where('is_published', true)
            ->first();

        if (! $website) {
            return response()->json(['message' => 'No published website found.'], 404);
        }

        return response()->json([
            'website' => [
                'id' => $website->id,
                'name' => $website->name,
                'domain' => $website->domain,
                'is_published' => true,
            ],
            'theme' => $website->theme,
            'sections' => $website->sections,
        ]);
    }
}
