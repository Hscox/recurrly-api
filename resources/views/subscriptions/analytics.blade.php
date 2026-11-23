@extends('layouts.app')

@section('title', 'Spending Analytics — Recurrly')

@section('content')
  <!-- Page Header -->
  <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
    <div>
      <p class="text-xs font-bold uppercase tracking-[0.14em] text-ink/45">Insights</p>
      <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-ink mt-1">Spending Analytics</h1>
      <p class="text-xs sm:text-sm text-ink/55 mt-1">
        Category shares, billing cadence split, and your highest-impact recurring plans.
      </p>
    </div>

    <a
      href="/subscriptions/create"
      class="glass-dark inline-flex items-center justify-center rounded-full px-5 py-2.5 text-xs font-bold text-white hover:scale-[1.02] active:scale-[0.98] transition-all shrink-0"
    >
      + New Subscription
    </a>
  </div>

  <!-- 4-Card KPI Grid -->
  <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="glass-coral text-white rounded-3xl p-6 flex flex-col justify-between min-h-[136px]">
      <span class="text-xs font-semibold uppercase tracking-[0.12em] text-white/80">Total Balance</span>
      <div class="mt-4">
        <p class="text-3xl font-extrabold tracking-tight tabular-nums">
          RM {{ number_format($summary['totalBalance'], 2) }}
        </p>
        <p class="text-xs text-white/80 mt-1">{{ $summary['totalCount'] }} active plans</p>
      </div>
    </div>

    <div class="glass rounded-3xl p-6 flex flex-col justify-between min-h-[136px]">
      <span class="text-xs font-semibold uppercase tracking-[0.12em] text-ink/50">Monthly Run-Rate</span>
      <div class="mt-4">
        <p class="text-3xl font-extrabold tracking-tight tabular-nums text-ink">
          RM {{ number_format($summary['monthlyEquivalent'], 2) }}
        </p>
        <p class="text-xs text-ink/55 mt-1">Normalized per month</p>
      </div>
    </div>

    <div class="glass rounded-3xl p-6 flex flex-col justify-between min-h-[136px]">
      <span class="text-xs font-semibold uppercase tracking-[0.12em] text-ink/50">Annualized Spend</span>
      <div class="mt-4">
        <p class="text-3xl font-extrabold tracking-tight tabular-nums text-ink">
          RM {{ number_format($summary['annualizedSpend'], 2) }}
        </p>
        <p class="text-xs text-ink/55 mt-1">Projected 12-month cost</p>
      </div>
    </div>

    <div class="glass rounded-3xl p-6 flex flex-col justify-between min-h-[136px]">
      <span class="text-xs font-semibold uppercase tracking-[0.12em] text-ink/50">Avg. Monthly / Plan</span>
      <div class="mt-4">
        <p class="text-3xl font-extrabold tracking-tight tabular-nums text-ink">
          RM {{ number_format($summary['averageMonthlyCost'], 2) }}
        </p>
        <p class="text-xs text-ink/55 mt-1">Top: {{ $summary['topCategory'] }}</p>
      </div>
    </div>
  </section>

  @if($subscriptions->isEmpty())
    <section class="glass rounded-3xl p-8">
      <div class="rounded-2xl border border-dashed border-ink/15 bg-white/40 py-14 px-6 text-center">
        <p class="text-sm font-bold text-ink/75">No analytics data yet</p>
        <p class="text-xs text-ink/50 mt-1 mb-5">
          Add at least one subscription to visualize category breakdowns and billing trends.
        </p>
        <a
          href="/subscriptions/create"
          class="glass-dark inline-flex items-center rounded-full px-5 py-2.5 text-xs font-bold text-white hover:scale-[1.02] transition-all"
        >
          + New Subscription
        </a>
      </div>
    </section>
  @else
    <main class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

      <!-- Left: Spending by Category -->
      <section class="lg:col-span-7 glass rounded-3xl p-6">
        <div class="mb-6">
          <h2 class="text-lg font-bold tracking-tight">Spending by Category</h2>
          <p class="text-xs text-ink/55 mt-0.5">Ranked by normalized monthly cost.</p>
        </div>

        <div class="space-y-4">
          @foreach($categoryBreakdown as $item)
            <div class="rounded-2xl bg-white/60 border border-white/90 p-4">
              <div class="flex items-center justify-between gap-4 mb-2">
                <div class="flex items-center gap-2.5 min-w-0">
                  <span
                    class="w-3.5 h-3.5 rounded-full shrink-0 shadow-inner"
                    style="background-color: {{ $item['color'] }};"
                  ></span>
                  <span class="font-bold text-sm text-ink truncate">{{ $item['category'] }}</span>
                  <span class="rounded-full bg-ink/5 px-2 py-0.5 text-[11px] font-semibold text-ink/60">
                    {{ $item['count'] }} {{ $item['count'] === 1 ? 'plan' : 'plans' }}
                  </span>
                </div>

                <div class="text-right shrink-0">
                  <span class="font-extrabold text-sm tabular-nums text-ink">
                    RM {{ number_format($item['monthlyTotal'], 2) }}/mo
                  </span>
                  <span class="text-[11px] font-semibold text-ink/45 ml-1.5 tabular-nums">
                    ({{ number_format($item['percentage'], 1) }}%)
                  </span>
                </div>
              </div>

              <div class="h-2.5 rounded-full bg-ink/5 overflow-hidden">
                <div
                  class="h-full rounded-full transition-all duration-300"
                  style="width: {{ min(100, round($item['percentage'], 1)) }}%; background-color: {{ $item['color'] }};"
                ></div>
              </div>
            </div>
          @endforeach
        </div>
      </section>

      <!-- Right Stack: Billing Cycle Split + Top Subscriptions -->
      <aside class="lg:col-span-5 space-y-6">
        <section class="glass rounded-3xl p-6">
          <div class="mb-5">
            <h2 class="text-lg font-bold tracking-tight">Billing Cycle Split</h2>
            <p class="text-xs text-ink/55 mt-0.5">Monthly vs. Yearly normalized share.</p>
          </div>

          <div class="h-3 rounded-full bg-ink/10 overflow-hidden flex mb-5">
            <div
              class="h-full bg-coral transition-all duration-300"
              style="width: {{ round($billingBreakdown['monthlyShare'], 1) }}%;"
            ></div>
            <div
              class="h-full bg-ink transition-all duration-300"
              style="width: {{ round($billingBreakdown['yearlyShare'], 1) }}%;"
            ></div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div class="rounded-2xl bg-white/65 border border-white/90 p-4">
              <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-coral"></span>
                <span class="text-xs font-bold text-ink">Monthly</span>
              </div>
              <p class="text-xl font-extrabold tabular-nums text-ink mt-2">
                RM {{ number_format($billingBreakdown['monthlyTotal'], 2) }}
              </p>
              <p class="text-[11px] text-ink/50 mt-0.5">
                {{ $billingBreakdown['monthlyCount'] }} {{ $billingBreakdown['monthlyCount'] === 1 ? 'plan' : 'plans' }} · {{ number_format($billingBreakdown['monthlyShare'], 1) }}%
              </p>
            </div>

            <div class="rounded-2xl bg-white/65 border border-white/90 p-4">
              <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-ink"></span>
                <span class="text-xs font-bold text-ink">Yearly</span>
              </div>
              <p class="text-xl font-extrabold tabular-nums text-ink mt-2">
                RM {{ number_format($billingBreakdown['yearlyTotal'], 2) }}/mo
              </p>
              <p class="text-[11px] text-ink/50 mt-0.5">
                {{ $billingBreakdown['yearlyCount'] }} {{ $billingBreakdown['yearlyCount'] === 1 ? 'plan' : 'plans' }} · {{ number_format($billingBreakdown['yearlyShare'], 1) }}%
              </p>
            </div>
          </div>
        </section>

        <section class="glass rounded-3xl p-6">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h2 class="text-lg font-bold tracking-tight">Top Subscriptions</h2>
              <p class="text-xs text-ink/55 mt-0.5">Highest monthly-equivalent cost.</p>
            </div>
            <a
              href="/subscriptions"
              class="text-xs font-bold text-coral hover:text-ink transition-colors"
            >
              All →
            </a>
          </div>

          <div class="space-y-2.5">
            @foreach($topSubscriptions as $sub)
              <a
                href="/subscriptions/{{ $sub->id }}"
                class="group rounded-2xl bg-white/65 hover:bg-white/90 border border-white/90 px-3.5 py-3 flex items-center justify-between gap-3 transition-all"
              >
                <div class="flex items-center gap-3 min-w-0">
                  <div
                    class="w-9 h-9 rounded-xl flex items-center justify-center font-extrabold text-[11px] text-ink shrink-0"
                    style="background-color: {{ $sub->color ?? '#fed7aa' }};"
                  >
                    {{ $sub->monogram() }}
                  </div>
                  <div class="min-w-0">
                    <p class="font-bold text-xs text-ink truncate group-hover:text-coral transition-colors">
                      {{ $sub->name }}
                    </p>
                    <p class="text-[11px] text-ink/45">{{ $sub->category }}</p>
                  </div>
                </div>

                <div class="text-right shrink-0">
                  <p class="font-extrabold text-xs tabular-nums text-ink">
                    RM {{ number_format($sub->monthlyEquivalent(), 2) }}/mo
                  </p>
                  <p class="text-[10px] text-ink/45">{{ $sub->formattedPrice() }} {{ $sub->billingCycle() }}</p>
                </div>
              </a>
            @endforeach
          </div>
        </section>
      </aside>
    </main>
  @endif
@endsection
