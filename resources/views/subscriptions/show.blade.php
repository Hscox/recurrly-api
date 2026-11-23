@extends('layouts.app')

@section('title', $subscription->name.' — Recurrly')

@section('content')
  <!-- Back Link -->
  <div class="mb-6">
    <a
      href="/subscriptions"
      class="inline-flex items-center gap-1.5 text-xs font-bold text-ink/55 hover:text-ink transition-colors"
    >
      ← Back to All Subscriptions
    </a>
  </div>

  <!-- Main Glass Detail Card -->
  <main class="glass rounded-3xl p-6 sm:p-8 space-y-8">
    <!-- Top Hero Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-ink/8">
      <div class="flex items-center gap-4 min-w-0">
        <div
          class="w-16 h-16 rounded-3xl flex items-center justify-center font-extrabold text-xl text-ink shrink-0 shadow-inner"
          style="background-color: {{ $subscription->color ?? '#fed7aa' }};"
        >
          {{ $subscription->monogram() }}
        </div>
        <div class="min-w-0">
          <div class="flex flex-wrap items-center gap-2 mb-1.5">
            <span class="rounded-full bg-ink/5 px-3 py-0.5 text-[11px] font-bold text-ink/70">
              {{ $subscription->category }}
            </span>
            <span class="rounded-full bg-coral/15 text-coral px-3 py-0.5 text-[11px] font-bold">
              {{ $subscription->billingCycle() }}
            </span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-ink truncate">
            {{ $subscription->name }}
          </h1>
        </div>
      </div>

      <div class="sm:text-right shrink-0">
        <p class="text-xs font-semibold uppercase tracking-[0.12em] text-ink/45">Plan Price</p>
        <p class="text-3xl font-extrabold tracking-tight tabular-nums text-ink mt-1">
          {{ $subscription->formattedPrice() }}
        </p>
        <p class="text-xs text-ink/55 mt-0.5">Billed {{ strtolower($subscription->billingCycle()) }}</p>
      </div>
    </div>

    <!-- 3-Card Cost Metrics Row -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="rounded-2xl bg-white/65 border border-white/90 p-5">
        <p class="text-xs font-semibold uppercase tracking-[0.1em] text-ink/45">Listed Price</p>
        <p class="text-2xl font-extrabold tabular-nums text-ink mt-2">{{ $subscription->formattedPrice() }}</p>
        <p class="text-xs text-ink/55 mt-1">Per {{ strtolower($subscription->billingCycle()) }} cycle</p>
      </div>

      <div class="rounded-2xl bg-white/65 border border-white/90 p-5">
        <p class="text-xs font-semibold uppercase tracking-[0.1em] text-ink/45">Monthly Equivalent</p>
        <p class="text-2xl font-extrabold tabular-nums text-ink mt-2">
          RM {{ number_format($subscription->monthlyEquivalent(), 2) }}
        </p>
        <p class="text-xs text-ink/55 mt-1">Normalized monthly run-rate</p>
      </div>

      <div class="glass-coral text-white rounded-2xl p-5">
        <p class="text-xs font-semibold uppercase tracking-[0.1em] text-white/80">Annualized Cost</p>
        <p class="text-2xl font-extrabold tabular-nums mt-2">
          RM {{ number_format($subscription->yearlyEquivalent(), 2) }}
        </p>
        <p class="text-xs text-white/80 mt-1">Projected 12-month spend</p>
      </div>
    </section>

    <!-- Metadata Details Grid -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div class="rounded-2xl bg-white/55 border border-white/80 p-4">
        <p class="text-[11px] font-semibold uppercase tracking-[0.1em] text-ink/45">Category</p>
        <p class="text-sm font-bold text-ink mt-1">{{ $subscription->category }}</p>
      </div>

      <div class="rounded-2xl bg-white/55 border border-white/80 p-4">
        <p class="text-[11px] font-semibold uppercase tracking-[0.1em] text-ink/45">Billing Cycle</p>
        <p class="text-sm font-bold text-ink mt-1">{{ $subscription->billingCycle() }}</p>
      </div>

      <div class="rounded-2xl bg-white/55 border border-white/80 p-4">
        <p class="text-[11px] font-semibold uppercase tracking-[0.1em] text-ink/45">First Bill Date</p>
        <p class="text-sm font-bold text-ink mt-1">{{ $subscription->firstBill ?? 'Active' }}</p>
      </div>

      <div class="rounded-2xl bg-white/55 border border-white/80 p-4">
        <p class="text-[11px] font-semibold uppercase tracking-[0.1em] text-ink/45">Payment Method</p>
        <p class="text-sm font-bold text-ink mt-1">{{ $subscription->paymentMethod ?? 'Card' }}</p>
      </div>

      <div class="rounded-2xl bg-white/55 border border-white/80 p-4">
        <p class="text-[11px] font-semibold uppercase tracking-[0.1em] text-ink/45">Created</p>
        <p class="text-sm font-bold text-ink mt-1">{{ $subscription->created_at?->format('M d, Y') ?? '—' }}</p>
      </div>

      <div class="rounded-2xl bg-white/55 border border-white/80 p-4">
        <p class="text-[11px] font-semibold uppercase tracking-[0.1em] text-ink/45">Plan ID</p>
        <p class="text-sm font-bold tabular-nums text-ink mt-1">#{{ $subscription->id }}</p>
      </div>
    </section>

    <!-- Footer Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-4 border-t border-ink/8">
      <a
        href="/subscriptions"
        class="rounded-full bg-white/70 hover:bg-white border border-ink/10 px-5 py-2.5 text-center text-xs font-bold text-ink transition-all"
      >
        ← Back to All Subs
      </a>

      <form
        action="/subscriptions/{{ $subscription->id }}"
        method="POST"
        data-confirm="Cancel {{ $subscription->name }}?"
        onsubmit="return confirm(this.dataset.confirm)"
      >
        @csrf
        @method('DELETE')
        <button
          type="submit"
          class="w-full sm:w-auto rounded-full bg-red-500/10 hover:bg-red-500 text-red-600 hover:text-white border border-red-500/20 px-5 py-2.5 text-xs font-bold transition-all cursor-pointer"
        >
          Cancel Subscription
        </button>
      </form>
    </div>
  </main>
@endsection
