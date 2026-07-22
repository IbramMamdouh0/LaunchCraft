<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Website;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $websiteId = $request->query('website_id');
        $status = $request->query('status');

        if (! $websiteId) {
            return response()->json(['status' => 'error', 'message' => 'website_id is required.'], 400);
        }

        $website = Website::where('_id', $websiteId)
            ->where('user_id', $request->user()->id)
            ->exists();

        if (! $website) {
            return response()->json(['status' => 'error', 'message' => 'Website not found.'], 404);
        }

        $query = Order::where('website_id', $websiteId);

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        return response()->json(['status' => 'success', 'data' => $orders]);
    }

    public function show(Request $request, string $id)
    {
        $order = Order::find($id);

        if (! $order) {
            return response()->json(['status' => 'error', 'message' => 'Order not found.'], 404);
        }

        $website = Website::where('_id', $order->website_id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if (! $website) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden.'], 403);
        }

        return response()->json(['status' => 'success', 'data' => $order]);
    }

    public function updateStatus(Request $request, string $id)
    {
        $order = Order::find($id);

        if (! $order) {
            return response()->json(['status' => 'error', 'message' => 'Order not found.'], 404);
        }

        $website = Website::where('_id', $order->website_id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if (! $website) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden.'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:pending,preparing,completed,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        return response()->json([
            'status' => 'success',
            'message' => 'Order status updated successfully.',
            'data' => $order,
        ]);
    }
}
