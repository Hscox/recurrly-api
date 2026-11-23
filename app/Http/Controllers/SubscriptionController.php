<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    //GET/api/subscriptions
    public function index()
    {
        return response()->json(Subscription::latest()->get());
    }

    //POST/api/subscriptions
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'frequency' => 'required|string',
            'price' => 'required|string',
            'category' => 'required|string',
            'billing' => 'required|string',
            'firstBill' => 'nullable|string',
            'paymentMethod' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $subscription = Subscription::create($validated);
        return response()->json($subscription, 201);
    }

    //DELETE/api/subscriptions/{id}
    public function destroy($id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->delete();

        return response()->json(['message' => 'Subscription deleted successfully']);
    }

    //web routes for the subscription management
    //GET /subscriptions
    public function webIndex()
    {
        $subscriptions = Subscription::latest()->get();

        $totalBalance = $subscriptions->sum(function ($sub) {
            return (float) preg_replace('/[^0-9.]/', '', $sub->price);
        });

        return view('subscriptions', compact('subscriptions', 'totalBalance'));
    }

    //POST/subscriptions
    public function webStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
            'frequency' => 'required|in:Monthly,Yearly',
            'category' => 'required|string',
        ]);

        $categoryColors = [
            'Entertainment' => '#ff7666',
            'AI Tools' => '#cfe2fe',
            'Developer Tools' => '#ded2f9',
            'Design' => '#eed766',
            'Productivity' => '#eadece',
            'Other' => '#fed7aa',
        ];

        Subscription::create([
            'name' => $validated['name'],
            'price' => '$' . number_format((float) $validated['price'], 2),
            'frequency' => $validated['frequency'],
            'billing' => $validated['frequency'],
            'category' => $validated['category'],
            'firstBill' => now()->format('M d, Y'),
            'paymentMethod' => 'Card',
            'color' => $categoryColors[$validated['category']] ?? '#fed7aa',
        ]);

        return redirect('/')->with('success', 'Subscription added!');
    }

    //DELETE/subscriptions/{id}
    public function webDestroy($id)
    {
        Subscription::findOrFail($id)->delete();

        return redirect('/')->with('success', 'Subscription cancelled!');
    }
}