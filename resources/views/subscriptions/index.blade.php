@extends('layouts.app')

@section('title', 'All Subscriptions — Recurrly')

@section('content')
  <!-- Page Header -->
  <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
    <div>
      <div class="flex items-center gap-2.5">
        <p class="text-xs font-bold uppercase tracking-[0.14em] text-ink/45">Directory</p>
        <span class="rounded-full bg-white/75 border border-white px-2.5 py-0.5 text-[11px] font-bold text-ink/70 tabular-nums">
          {{ $summary['totalCount'] }} {{ $summary['totalCount'] === 1 ? 'plan' : 'plans' }}
        </span>
      </div>
      <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-ink mt-1">All Subscriptions</h1>
      <p class="text-xs sm:text-sm text-ink/55 mt-1">
        Estimated run-rate:
        <span class="font-bold text-ink tabular-nums">RM {{ number_format($summary['monthlyEquivalent'], 2) }}/mo</span>
        · Browse, filter, or manage all your recurring plans.
      </p>
    </div>

    <a
      href="/subscriptions/create"
      class="glass-dark inline-flex items-center justify-center rounded-full px-5 py-2.5 text-xs font-bold text-white hover:scale-[1.02] active:scale-[0.98] transition-all shrink-0"
    >
      + New Subscription
    </a>
  </div>

  <!-- Filter & Search Glass Bar -->
  <section class="glass rounded-3xl p-5 mb-6 space-y-4">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
      <div class="relative w-full md:w-80">
        <input
          id="sub-search"
          type="text"
          placeholder="Search by service, category, or payment..."
          class="glass-input w-full rounded-full px-4 py-2.5 text-xs font-medium placeholder:text-ink/40"
        />
      </div>

      <!-- Frequency Pills -->
      <div class="flex items-center gap-1.5 flex-wrap">
        <span class="text-[11px] font-bold uppercase tracking-[0.1em] text-ink/40 mr-1">Billing:</span>
        @foreach(['all' => 'All', 'monthly' => 'Monthly', 'yearly' => 'Yearly'] as $freqValue => $freqLabel)
          <button
            type="button"
            data-frequency="{{ $freqValue }}"
            class="freq-pill rounded-full px-3.5 py-1.5 text-xs font-bold transition-all cursor-pointer {{ $freqValue === 'all' ? 'bg-ink text-white shadow-sm' : 'bg-white/60 text-ink/65 hover:bg-white hover:text-ink border border-white/80' }}"
          >
            {{ $freqLabel }}
          </button>
        @endforeach
      </div>
    </div>

    @if(!empty($activeCategories))
      <div class="flex items-center gap-1.5 flex-wrap pt-1 border-t border-ink/5">
        <span class="text-[11px] font-bold uppercase tracking-[0.1em] text-ink/40 mr-1">Category:</span>
        <button
          type="button"
          data-category="all"
          class="cat-pill rounded-full px-3.5 py-1.5 text-xs font-bold transition-all cursor-pointer bg-ink text-white shadow-sm"
        >
          All Categories
        </button>
        @foreach($activeCategories as $category)
          <button
            type="button"
            data-category="{{ strtolower($category) }}"
            class="cat-pill rounded-full px-3.5 py-1.5 text-xs font-bold transition-all cursor-pointer bg-white/60 text-ink/65 hover:bg-white hover:text-ink border border-white/80"
          >
            {{ $category }}
          </button>
        @endforeach
      </div>
    @endif
  </section>

  <!-- Subscriptions List -->
  <section class="glass rounded-3xl p-6">
    @if($subscriptions->isEmpty())
      <div class="rounded-2xl border border-dashed border-ink/15 bg-white/40 py-14 px-6 text-center">
        <p class="text-sm font-bold text-ink/75">No subscriptions yet</p>
        <p class="text-xs text-ink/50 mt-1 mb-5">
          Add your first subscription on the New Subscription page to get started.
        </p>
        <a
          href="/subscriptions/create"
          class="glass-dark inline-flex items-center rounded-full px-5 py-2.5 text-xs font-bold text-white hover:scale-[1.02] transition-all"
        >
          + New Subscription
        </a>
      </div>
    @else
      <div id="sub-list" class="space-y-3">
        @foreach($subscriptions as $sub)
          <div
            data-search="{{ strtolower($sub->name . ' ' . $sub->category . ' ' . ($sub->paymentMethod ?? '')) }}"
            data-category="{{ strtolower($sub->category) }}"
            data-frequency="{{ strtolower($sub->billingCycle()) }}"
            class="sub-item group rounded-2xl bg-white/65 hover:bg-white/90 border border-white/90 px-4 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-all duration-200 shadow-[0_4px_16px_rgba(8,17,38,0.03)]"
          >
            <a href="/subscriptions/{{ $sub->id }}" class="flex items-center gap-3.5 min-w-0 flex-1">
              <div
                class="w-11 h-11 rounded-2xl flex items-center justify-center font-extrabold text-xs text-ink shrink-0 shadow-inner"
                style="background-color: {{ $sub->color ?? '#fed7aa' }};"
              >
                {{ $sub->monogram() }}
              </div>
              <div class="min-w-0">
                <h2 class="font-bold text-sm text-ink truncate group-hover:text-coral transition-colors">
                  {{ $sub->name }}
                </h2>
                <div class="flex flex-wrap items-center gap-2 mt-0.5">
                  <span class="rounded-full bg-ink/5 px-2 py-0.5 text-[10px] font-bold text-ink/70">
                    {{ $sub->category }}
                  </span>
                  <span class="text-ink/25">•</span>
                  <span class="text-[11px] text-ink/50">{{ $sub->firstBill ?? 'Active' }}</span>
                  @if(filled($sub->paymentMethod))
                    <span class="text-ink/25">•</span>
                    <span class="text-[11px] text-ink/45">{{ $sub->paymentMethod }}</span>
                  @endif
                </div>
              </div>
            </a>

            <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0">
              <div class="text-left sm:text-right mr-1">
                <p class="font-extrabold text-sm tabular-nums text-ink">{{ $sub->formattedPrice() }}</p>
                <p class="text-[11px] font-medium text-ink/45">{{ $sub->billingCycle() }}</p>
              </div>

              <div class="flex items-center gap-2">
                <a
                  href="/subscriptions/{{ $sub->id }}"
                  class="rounded-full bg-white/80 hover:bg-ink hover:text-white border border-ink/10 text-ink/75 text-[11px] font-bold px-3.5 py-1.5 transition-all"
                >
                  Details
                </a>

                <form
                  action="/subscriptions/{{ $sub->id }}"
                  method="POST"
                  data-confirm="Cancel {{ $sub->name }}?"
                  onsubmit="return confirm(this.dataset.confirm)"
                >
                  @csrf
                  @method('DELETE')
                  <button
                    type="submit"
                    class="rounded-full bg-ink/5 hover:bg-red-500 hover:text-white text-ink/60 text-[11px] font-bold px-3.5 py-1.5 transition-all cursor-pointer"
                  >
                    Cancel
                  </button>
                </form>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div id="no-filter-matches" class="hidden rounded-2xl border border-dashed border-ink/15 bg-white/40 py-12 px-6 text-center">
        <p class="text-sm font-bold text-ink/75">No matching subscriptions</p>
        <p class="text-xs text-ink/50 mt-1 mb-4">Try adjusting your search query or selected filters.</p>
        <button
          id="reset-filters"
          type="button"
          class="rounded-full bg-ink/5 hover:bg-ink hover:text-white px-4 py-2 text-xs font-bold text-ink/70 transition-all cursor-pointer"
        >
          Reset filters
        </button>
      </div>
    @endif
  </section>
@endsection

@push('scripts')
  <script>
    (() => {
      const searchInput = document.getElementById('sub-search');
      const items = Array.from(document.querySelectorAll('.sub-item'));
      const freqPills = Array.from(document.querySelectorAll('.freq-pill'));
      const catPills = Array.from(document.querySelectorAll('.cat-pill'));
      const emptyMatches = document.getElementById('no-filter-matches');
      const resetButton = document.getElementById('reset-filters');

      if (items.length === 0) {
        return;
      }

      let activeFreq = 'all';
      let activeCat = 'all';

      const activeClasses = ['bg-ink', 'text-white', 'shadow-sm'];
      const inactiveClasses = ['bg-white/60', 'text-ink/65', 'hover:bg-white', 'hover:text-ink', 'border', 'border-white/80'];

      const setPillState = (pills, value, attr) => {
        pills.forEach((pill) => {
          const isSelected = pill.getAttribute(attr) === value;
          activeClasses.forEach((cls) => pill.classList.toggle(cls, isSelected));
          inactiveClasses.forEach((cls) => pill.classList.toggle(cls, !isSelected));
        });
      };

      const applyFilters = () => {
        const query = (searchInput?.value || '').toLowerCase().trim();
        let visibleCount = 0;

        items.forEach((card) => {
          const searchText = card.getAttribute('data-search') || '';
          const category = card.getAttribute('data-category') || '';
          const frequency = card.getAttribute('data-frequency') || '';

          const matchesSearch = searchText.includes(query);
          const matchesFreq = activeFreq === 'all' || frequency === activeFreq;
          const matchesCat = activeCat === 'all' || category === activeCat;

          const visible = matchesSearch && matchesFreq && matchesCat;
          card.style.display = visible ? 'flex' : 'none';
          if (visible) {
            visibleCount += 1;
          }
        });

        if (emptyMatches) {
          emptyMatches.classList.toggle('hidden', visibleCount > 0);
        }
      };

      searchInput?.addEventListener('input', applyFilters);

      freqPills.forEach((pill) => {
        pill.addEventListener('click', () => {
          activeFreq = pill.getAttribute('data-frequency') || 'all';
          setPillState(freqPills, activeFreq, 'data-frequency');
          applyFilters();
        });
      });

      catPills.forEach((pill) => {
        pill.addEventListener('click', () => {
          activeCat = pill.getAttribute('data-category') || 'all';
          setPillState(catPills, activeCat, 'data-category');
          applyFilters();
        });
      });

      resetButton?.addEventListener('click', () => {
        if (searchInput) {
          searchInput.value = '';
        }
        activeFreq = 'all';
        activeCat = 'all';
        setPillState(freqPills, 'all', 'data-frequency');
        setPillState(catPills, 'all', 'data-category');
        applyFilters();
      });
    })();
  </script>
@endpush
