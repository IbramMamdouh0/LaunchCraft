<?php

namespace App\Http\Controllers;

use App\Models\Website;

class MobileController extends Controller
{
    public function website()
    {
        $website = Website::where('status', 'published')->first();

        if (! $website) {
            return response()->json(['message' => 'No published website found.'], 404);
        }

        return response()->json([
            'website' => $website,
            'theme' => $website->theme,
            'sections' => $website->pages,
        ]);
    }
}
