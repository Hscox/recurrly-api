@extends('layouts.app')

@section('title', 'Overview — Recurrly')

@section('content')
  <!-- Page Header -->
  <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
    <div>
      <p class="text-xs font-bold uppercase tracking-[0.14em] text-ink/45">Dashboard</p>
      <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-ink mt-1">Subscription Overview</h1>
      <p class="text-xs sm:text-sm text-ink/55 mt-1">Track and manage your recurring subscriptions in one place.</p>
    </div>

    <div class="flex items-center gap-2.5 shrink-0">
      <a
        href="/subscriptions"
        class="glass rounded-full px-4 py-2.5 text-xs font-bold text-ink hover:bg-white/85 transition-all"
      >
        All Subscriptions
      </a>
      <a
        href="/subscriptions/create"
        class="glass-dark rounded-full px-5 py-2.5 text-xs font-bold text-white hover:scale-[1.02] active:scale-[0.98] transition-all"
      >
        + New Subscription
      </a>
    </div>
  </div>

  <!-- Summary Cards -->
  <section class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <!-- Total Balance Card -->
    <div class="glass-coral text-white rounded-3xl p-6 flex flex-col justify-between min-h-[142px]">
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold uppercase tracking-[0.12em] text-white/80">Total Balance</span>
      </div>
      <div class="mt-4">
        <p class="text-3xl font-extrabold tracking-tight tabular-nums">
          RM {{ number_format($summary['totalBalance'], 2) }}
        </p>
        <p class="text-xs text-white/80 mt-1">Sum of all active plans</p>
      </div>
    </div>

    <!-- Smart Monthly Run-Rate Card -->
    <div class="glass rounded-3xl p-6 flex flex-col justify-between min-h-[142px]">
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold uppercase tracking-[0.12em] text-ink/50">Monthly Run-Rate</span>
        <span class="text-xs font-bold text-coral">Smart Est.</span>
      </div>
      <div class="mt-4">
        <p class="text-3xl font-extrabold tracking-tight tabular-nums text-ink">
          RM {{ number_format($summary['monthlyEquivalent'], 2) }}
        </p>
        <p class="text-xs text-ink/55 mt-1">Yearly plans normalized ÷ 12</p>
      </div>
    </div>

    <!-- Active Plans & Top Category -->
    <div class="glass rounded-3xl p-6 flex flex-col justify-between min-h-[142px]">
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold uppercase tracking-[0.12em] text-ink/50">Active Plans</span>
        <span class="rounded-full bg-ink/5 px-2.5 py-0.5 text-[11px] font-semibold text-ink/70">
          {{ $summary['topCategory'] }}
        </span>
      </div>
      <div class="mt-4">
        <p class="text-3xl font-extrabold tracking-tight tabular-nums text-ink">
          {{ $summary['totalCount'] }}
        </p>
        <p class="text-xs text-ink/55 mt-1">Top category: {{ $summary['topCategory'] }}</p>
      </div>
    </div>
  </section>

  <!-- 2-Column Overview Grid -->
  <main class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

    <!-- Left: Recent Subscriptions Preview -->
    <section class="lg:col-span-7 glass rounded-3xl p-6">
      <div class="flex items-center justify-between gap-3 mb-5">
        <div>
          <h2 class="text-lg font-bold tracking-tight">Recent Subscriptions</h2>
          <p class="text-xs text-ink/55 mt-0.5">Your most recently added subscriptions.</p>
        </div>
        <a
          href="/subscriptions"
          class="text-xs font-bold text-coral hover:text-ink transition-colors shrink-0"
        >
          View all →
        </a>
      </div>

      @if($recentSubscriptions->isEmpty())
        <div class="rounded-2xl border border-dashed border-ink/15 bg-white/40 py-14 px-6 text-center">
          <p class="text-sm font-bold text-ink/75">No subscriptions yet</p>
          <p class="text-xs text-ink/50 mt-1 mb-5">
            Create your first recurring plan on the New Subscription page.
          </p>
          <a
            href="/subscriptions/create"
            class="glass-dark inline-flex items-center rounded-full px-5 py-2.5 text-xs font-bold text-white hover:scale-[1.02] transition-all"
          >
            + Add First Subscription
          </a>
        </div>
      @else
        <div class="space-y-3">
          @foreach($recentSubscriptions as $sub)
            <a
              href="/subscriptions/{{ $sub->id }}"
              class="group rounded-2xl bg-white/65 hover:bg-white/90 border border-white/90 px-4 py-3.5 flex items-center justify-between gap-4 transition-all duration-200 shadow-[0_4px_16px_rgba(8,17,38,0.03)]"
            >
              <div class="flex items-center gap-3.5 min-w-0">
                <div
                  class="w-11 h-11 rounded-2xl flex items-center justify-center font-extrabold text-xs text-ink shrink-0 shadow-inner"
                  style="background-color: {{ $sub->color ?? '#fed7aa' }};"
                >
                  {{ $sub->monogram() }}
                </div>
                <div class="min-w-0">
                  <h3 class="font-bold text-sm text-ink truncate group-hover:text-coral transition-colors">
                    {{ $sub->name }}
                  </h3>
                  <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-[11px] font-semibold text-ink/55">{{ $sub->category }}</span>
                    <span class="text-ink/25">•</span>
                    <span class="text-[11px] text-ink/45">{{ $sub->firstBill ?? 'Active' }}</span>
                  </div>
                </div>
              </div>

              <div class="text-right shrink-0">
                <p class="font-extrabold text-sm tabular-nums text-ink">{{ $sub->formattedPrice() }}</p>
                <p class="text-[11px] font-medium text-ink/45">{{ $sub->billingCycle() }}</p>
              </div>
            </a>
          @endforeach
        </div>
      @endif
    </section>

    <!-- Right Stack: Category Snapshot + Quick Add CTA -->
    <aside class="lg:col-span-5 space-y-6">
      <section class="glass rounded-3xl p-6">
        <div class="flex items-center justify-between gap-3 mb-5">
          <div>
            <h2 class="text-lg font-bold tracking-tight">Category Snapshot</h2>
            <p class="text-xs text-ink/55 mt-0.5">Share of normalized monthly spend.</p>
          </div>
          <a
            href="/analytics"
            class="text-xs font-bold text-coral hover:text-ink transition-colors shrink-0"
          >
            Full analytics →
          </a>
        </div>

        @if(empty($topCategories))
          <div class="rounded-2xl border border-dashed border-ink/15 bg-white/40 py-8 px-4 text-center">
            <p class="text-xs font-semibold text-ink/60">Category breakdown appears once you add a plan.</p>
          </div>
        @else
          <div class="space-y-4">
            @foreach($topCategories as $item)
              <div>
                <div class="flex items-center justify-between text-xs mb-1.5">
                  <div class="flex items-center gap-2 font-bold text-ink">
                    <span
                      class="w-2.5 h-2.5 rounded-full shrink-0"
                      style="background-color: {{ $item['color'] }};"
                    ></span>
                    <span>{{ $item['category'] }}</span>
                    <span class="text-[11px] font-medium text-ink/45">({{ $item['count'] }})</span>
                  </div>
                  <span class="font-bold tabular-nums text-ink">
                    RM {{ number_format($item['monthlyTotal'], 2) }}/mo
                  </span>
                </div>
                <div class="h-2 rounded-full bg-ink/5 overflow-hidden">
                  <div
                    class="h-full rounded-full transition-all duration-300"
                    style="width: {{ min(100, round($item['percentage'], 1)) }}%; background-color: {{ $item['color'] }};"
                  ></div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </section>

      <section class="glass rounded-3xl p-6">
        <p class="text-xs font-bold uppercase tracking-[0.12em] text-coral">Quick Action</p>
        <h2 class="text-lg font-bold tracking-tight mt-1">Track a new recurring plan</h2>
        <p class="text-xs text-ink/55 mt-1 leading-relaxed">
          Add a new subscription with instant monthly and annual cost calculations.
        </p>
        <a
          href="/subscriptions/create"
          class="glass-dark mt-4 w-full inline-flex items-center justify-center rounded-2xl py-3 text-xs font-bold text-white hover:scale-[1.01] active:scale-[0.99] transition-all"
        >
          + Open New Subscription Form
        </a>
      </section>
    </aside>
  </main>
@endsection
