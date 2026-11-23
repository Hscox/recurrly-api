<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    /**
     * Return all subscriptions as JSON for mobile and API clients.
     */
    public function index(): JsonResponse
    {
        return response()->json(Subscription::latest()->get());
    }

    /**
     * Store a newly created subscription via JSON API.
     */
    public function store(Request $request): JsonResponse
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

    /**
     * Delete a subscription via JSON API.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->delete();

        return response()->json(['message' => 'Subscription deleted successfully']);
    }

    /**
     * Display the overview dashboard page.
     */
    public function webDashboard(): View
    {
        $subscriptions = Subscription::latest()->get();
        $summary = $this->buildSummary($subscriptions);
        $categoryBreakdown = $this->buildCategoryBreakdown($subscriptions, $summary['monthlyEquivalent']);

        return view('subscriptions.dashboard', [
            'subscriptions' => $subscriptions,
            'recentSubscriptions' => $subscriptions->take(5),
            'summary' => $summary,
            'topCategories' => array_slice($categoryBreakdown, 0, 4),
        ]);
    }

    /**
     * Display the searchable and filterable all-subscriptions page.
     */
    public function webIndex(): View
    {
        $subscriptions = Subscription::latest()->get();
        $summary = $this->buildSummary($subscriptions);
        $activeCategories = $subscriptions
            ->pluck('category')
            ->filter()
            ->unique()
            ->values()
            ->all();

        return view('subscriptions.index', compact('subscriptions', 'summary', 'activeCategories'));
    }

    /**
     * Display the new subscription form page.
     */
    public function webCreate(): View
    {
        $categories = Subscription::categories();
        $categoryColors = Subscription::CATEGORY_COLORS;

        return view('subscriptions.create', compact('categories', 'categoryColors'));
    }

    /**
     * Persist a new subscription submitted from the web UI.
     */
    public function webStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
            'frequency' => 'required|in:Monthly,Yearly',
            'category' => 'required|string|max:100',
            'paymentMethod' => 'nullable|string|max:100',
            'firstBill' => 'nullable|string|max:100',
        ]);

        $firstBill = filled($validated['firstBill'] ?? null)
            ? trim((string) $validated['firstBill'])
            : now()->format('M d, Y');

        $paymentMethod = filled($validated['paymentMethod'] ?? null)
            ? trim((string) $validated['paymentMethod'])
            : 'Card';

        Subscription::create([
            'name' => $validated['name'],
            'price' => 'RM '.number_format((float) $validated['price'], 2),
            'frequency' => $validated['frequency'],
            'billing' => $validated['frequency'],
            'category' => $validated['category'],
            'firstBill' => $firstBill,
            'paymentMethod' => $paymentMethod,
            'color' => Subscription::colorForCategory($validated['category']),
        ]);

        return redirect('/subscriptions')
            ->with('success', 'Subscription added!');
    }

    /**
     * Display the detail page for a single subscription.
     */
    public function webShow(Subscription $subscription): View
    {
        return view('subscriptions.show', compact('subscription'));
    }

    /**
     * Delete a subscription from the web UI and return to all subscriptions.
     */
    public function webDestroy(Subscription $subscription): RedirectResponse
    {
        $subscription->delete();

        return redirect('/subscriptions')
            ->with('success', 'Subscription cancelled!');
    }

    /**
     * Display the spending analytics page.
     */
    public function webAnalytics(): View
    {
        $subscriptions = Subscription::latest()->get();
        $summary = $this->buildSummary($subscriptions);
        $categoryBreakdown = $this->buildCategoryBreakdown($subscriptions, $summary['monthlyEquivalent']);
        $billingBreakdown = $this->buildBillingBreakdown($subscriptions, $summary['monthlyEquivalent']);
        $topSubscriptions = $subscriptions
            ->sortByDesc(fn (Subscription $sub): float => $sub->monthlyEquivalent())
            ->take(5)
            ->values();

        return view('subscriptions.analytics', compact(
            'subscriptions',
            'summary',
            'categoryBreakdown',
            'billingBreakdown',
            'topSubscriptions',
        ));
    }

    /**
     * Build aggregate KPI metrics for a collection of subscriptions.
     *
     * @param  Collection<int, Subscription>  $subscriptions
     * @return array{
     *     totalBalance: float,
     *     monthlyEquivalent: float,
     *     annualizedSpend: float,
     *     averageMonthlyCost: float,
     *     totalCount: int,
     *     topCategory: string
     * }
     */
    private function buildSummary(Collection $subscriptions): array
    {
        $totalCount = $subscriptions->count();
        $totalBalance = $subscriptions->sum(fn (Subscription $sub): float => $sub->numericPrice());
        $monthlyEquivalent = $subscriptions->sum(fn (Subscription $sub): float => $sub->monthlyEquivalent());
        $annualizedSpend = $subscriptions->sum(fn (Subscription $sub): float => $sub->yearlyEquivalent());
        $averageMonthlyCost = $totalCount > 0 ? $monthlyEquivalent / $totalCount : 0.0;

        $topCategory = $subscriptions->isNotEmpty()
            ? (string) $subscriptions->groupBy('category')->sortByDesc->count()->keys()->first()
            : 'None yet';

        return [
            'totalBalance' => $totalBalance,
            'monthlyEquivalent' => $monthlyEquivalent,
            'annualizedSpend' => $annualizedSpend,
            'averageMonthlyCost' => $averageMonthlyCost,
            'totalCount' => $totalCount,
            'topCategory' => $topCategory,
        ];
    }

    /**
     * Group subscriptions by category and compute normalized monthly shares.
     *
     * @param  Collection<int, Subscription>  $subscriptions
     * @return list<array{
     *     category: string,
     *     color: string,
     *     count: int,
     *     monthlyTotal: float,
     *     percentage: float
     * }>
     */
    private function buildCategoryBreakdown(Collection $subscriptions, float $monthlyEquivalent): array
    {
        return $subscriptions
            ->groupBy('category')
            ->map(function (Collection $group, string $category) use ($monthlyEquivalent): array {
                $monthlyTotal = $group->sum(fn (Subscription $sub): float => $sub->monthlyEquivalent());
                $percentage = $monthlyEquivalent > 0 ? ($monthlyTotal / $monthlyEquivalent) * 100 : 0.0;

                return [
                    'category' => $category !== '' ? $category : 'Other',
                    'color' => Subscription::colorForCategory($category),
                    'count' => $group->count(),
                    'monthlyTotal' => $monthlyTotal,
                    'percentage' => $percentage,
                ];
            })
            ->sortByDesc('monthlyTotal')
            ->values()
            ->all();
    }

    /**
     * Compute Monthly vs. Yearly billing cycle distribution and normalized spend share.
     *
     * @param  Collection<int, Subscription>  $subscriptions
     * @return array{
     *     monthlyCount: int,
     *     yearlyCount: int,
     *     monthlyTotal: float,
     *     yearlyTotal: float,
     *     yearlyRawTotal: float,
     *     monthlyShare: float,
     *     yearlyShare: float
     * }
     */
    private function buildBillingBreakdown(Collection $subscriptions, float $monthlyEquivalent): array
    {
        $yearlySubs = $subscriptions->filter(
            fn (Subscription $sub): bool => strtolower($sub->billingCycle()) === 'yearly',
        );
        $monthlySubs = $subscriptions->reject(
            fn (Subscription $sub): bool => strtolower($sub->billingCycle()) === 'yearly',
        );

        $monthlyTotal = $monthlySubs->sum(fn (Subscription $sub): float => $sub->monthlyEquivalent());
        $yearlyTotal = $yearlySubs->sum(fn (Subscription $sub): float => $sub->monthlyEquivalent());
        $yearlyRawTotal = $yearlySubs->sum(fn (Subscription $sub): float => $sub->numericPrice());

        return [
            'monthlyCount' => $monthlySubs->count(),
            'yearlyCount' => $yearlySubs->count(),
            'monthlyTotal' => $monthlyTotal,
            'yearlyTotal' => $yearlyTotal,
            'yearlyRawTotal' => $yearlyRawTotal,
            'monthlyShare' => $monthlyEquivalent > 0 ? ($monthlyTotal / $monthlyEquivalent) * 100 : 0.0,
            'yearlyShare' => $monthlyEquivalent > 0 ? ($yearlyTotal / $monthlyEquivalent) * 100 : 0.0,
        ];
    }
}
