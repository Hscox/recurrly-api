@extends('layouts.app')

@section('title', 'New Subscription — Recurrly')

@section('content')
  <!-- Back Link & Heading -->
  <div class="mb-6">
    <a
      href="/subscriptions"
      class="inline-flex items-center gap-1.5 text-xs font-bold text-ink/55 hover:text-ink transition-colors mb-2"
    >
      ← Back to All Subscriptions
    </a>
    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-ink">New Subscription</h1>
    <p class="text-xs sm:text-sm text-ink/55 mt-1">
      Enter your subscription details below to add it to your tracker.
    </p>
  </div>

  <!-- 2-Column Create + Preview Layout -->
  <main class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

    <!-- Left: Add Subscription Glass Form -->
    <section class="lg:col-span-7 glass rounded-3xl p-6 sm:p-8">
      <form action="/subscriptions" method="POST" class="space-y-5">
        @csrf

        <div>
          <label for="input-name" class="block text-xs font-semibold text-ink/70 mb-1.5">Service Name</label>
          <input
            id="input-name"
            type="text"
            name="name"
            value="{{ old('name') }}"
            placeholder="Gemini Pro, Spotify, GitHub..."
            required
            class="glass-input w-full rounded-2xl px-4 py-3 text-sm font-medium placeholder:text-ink/35"
          />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label for="input-price" class="block text-xs font-semibold text-ink/70 mb-1.5">Price (MYR)</label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-ink/40">RM</span>
              <input
                id="input-price"
                type="number"
                step="0.01"
                min="0.01"
                name="price"
                value="{{ old('price') }}"
                placeholder="20.00"
                required
                class="glass-input w-full rounded-2xl pl-12 pr-4 py-3 text-sm font-medium tabular-nums placeholder:text-ink/35"
              />
            </div>
          </div>

          <div>
            <label for="input-category" class="block text-xs font-semibold text-ink/70 mb-1.5">Category</label>
            <select
              id="input-category"
              name="category"
              class="glass-input w-full rounded-2xl px-4 py-3 text-sm font-medium cursor-pointer"
            >
              @foreach($categories as $category)
                <option value="{{ $category }}" @selected(old('category', 'AI Tools') === $category)>
                  {{ $category }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <!-- Segmented Frequency Toggle -->
        <div>
          <span class="block text-xs font-semibold text-ink/70 mb-1.5">Billing Frequency</span>
          <div class="grid grid-cols-2 gap-2.5">
            <label class="cursor-pointer">
              <input
                type="radio"
                name="frequency"
                value="Monthly"
                @checked(old('frequency', 'Monthly') === 'Monthly')
                class="peer sr-only frequency-radio"
              />
              <div class="rounded-2xl py-2.5 text-center text-xs font-semibold border border-ink/10 bg-white/50 text-ink/65 peer-checked:bg-coral/15 peer-checked:border-coral peer-checked:text-coral transition-all">
                Monthly
              </div>
            </label>
            <label class="cursor-pointer">
              <input
                type="radio"
                name="frequency"
                value="Yearly"
                @checked(old('frequency') === 'Yearly')
                class="peer sr-only frequency-radio"
              />
              <div class="rounded-2xl py-2.5 text-center text-xs font-semibold border border-ink/10 bg-white/50 text-ink/65 peer-checked:bg-coral/15 peer-checked:border-coral peer-checked:text-coral transition-all">
                Yearly
              </div>
            </label>
          </div>
        </div>

        <!-- Optional Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
          <div>
            <label for="input-payment" class="block text-xs font-semibold text-ink/70 mb-1.5">
              Payment Method <span class="text-ink/40 font-normal">(optional)</span>
            </label>
            <input
              id="input-payment"
              type="text"
              name="paymentMethod"
              value="{{ old('paymentMethod') }}"
              placeholder="Card, PayPal, Apple Pay..."
              class="glass-input w-full rounded-2xl px-4 py-3 text-sm font-medium placeholder:text-ink/35"
            />
          </div>

          <div>
            <label for="input-first-bill" class="block text-xs font-semibold text-ink/70 mb-1.5">
              First Bill Date <span class="text-ink/40 font-normal">(optional)</span>
            </label>
            <input
              id="input-first-bill"
              type="text"
              name="firstBill"
              value="{{ old('firstBill') }}"
              placeholder="e.g. {{ now()->format('M d, Y') }}"
              class="glass-input w-full rounded-2xl px-4 py-3 text-sm font-medium placeholder:text-ink/35"
            />
          </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 pt-2">
          <button
            type="submit"
            class="glass-dark w-full sm:flex-1 rounded-2xl py-3.5 px-6 text-sm font-bold text-white hover:scale-[1.01] active:scale-[0.99] transition-all cursor-pointer"
          >
            + Add Subscription
          </button>
          <a
            href="/subscriptions"
            class="w-full sm:w-auto text-center rounded-2xl bg-white/60 hover:bg-white/90 border border-ink/10 px-6 py-3.5 text-sm font-bold text-ink/70 transition-all"
          >
            Cancel
          </a>
        </div>
      </form>
    </section>

    <!-- Right: Plan Preview -->
    <aside class="lg:col-span-5 lg:sticky lg:top-8 space-y-4">
      <section class="glass rounded-3xl p-6">
        <div class="flex items-center justify-between mb-5">
          <span class="text-xs font-bold uppercase tracking-[0.12em] text-ink/45">Plan Preview</span>
        </div>

        <div class="rounded-2xl bg-white/75 border border-white p-4 flex items-center justify-between gap-4 shadow-[0_4px_16px_rgba(8,17,38,0.04)]">
          <div class="flex items-center gap-3.5 min-w-0">
            <div
              id="preview-monogram"
              class="w-12 h-12 rounded-2xl flex items-center justify-center font-extrabold text-sm text-ink shrink-0 shadow-inner"
              style="background-color: {{ $categoryColors[old('category', 'AI Tools')] ?? '#cfe2fe' }};"
            >
              GP
            </div>
            <div class="min-w-0">
              <h2 id="preview-name" class="font-bold text-sm text-ink truncate">Gemini Pro</h2>
              <div class="flex items-center gap-2 mt-0.5">
                <span id="preview-category" class="text-[11px] font-semibold text-ink/55">
                  {{ old('category', 'AI Tools') }}
                </span>
                <span class="text-ink/25">•</span>
                <span id="preview-payment" class="text-[11px] text-ink/45">Card</span>
              </div>
            </div>
          </div>

          <div class="text-right shrink-0">
            <p id="preview-price" class="font-extrabold text-sm tabular-nums text-ink">RM 20.00</p>
            <p id="preview-frequency" class="text-[11px] font-medium text-ink/45">
              {{ old('frequency', 'Monthly') }}
            </p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3 mt-4">
          <div class="rounded-2xl bg-white/55 border border-white/80 p-3.5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-ink/45">Monthly Est.</p>
            <p id="preview-monthly" class="text-lg font-extrabold tabular-nums text-ink mt-1">RM 20.00</p>
          </div>
          <div class="rounded-2xl bg-white/55 border border-white/80 p-3.5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-ink/45">Annualized</p>
            <p id="preview-yearly" class="text-lg font-extrabold tabular-nums text-ink mt-1">RM 240.00</p>
          </div>
        </div>
      </section>
    </aside>
  </main>
@endsection

@push('scripts')
  <script>
    (() => {
      const categoryColors = @json($categoryColors);

      const nameInput = document.getElementById('input-name');
      const priceInput = document.getElementById('input-price');
      const categorySelect = document.getElementById('input-category');
      const paymentInput = document.getElementById('input-payment');
      const freqRadios = Array.from(document.querySelectorAll('.frequency-radio'));

      const previewMonogram = document.getElementById('preview-monogram');
      const previewName = document.getElementById('preview-name');
      const previewCategory = document.getElementById('preview-category');
      const previewPayment = document.getElementById('preview-payment');
      const previewPrice = document.getElementById('preview-price');
      const previewFrequency = document.getElementById('preview-frequency');
      const previewMonthly = document.getElementById('preview-monthly');
      const previewYearly = document.getElementById('preview-yearly');

      const updatePreview = () => {
        const rawName = (nameInput?.value || '').trim();
        const displayName = rawName || 'Gemini Pro';
        const monogram = displayName.slice(0, 2).toUpperCase();

        const rawPrice = parseFloat(priceInput?.value || '0');
        const amount = Number.isFinite(rawPrice) && rawPrice > 0 ? rawPrice : 20;

        const category = categorySelect?.value || 'AI Tools';
        const payment = (paymentInput?.value || '').trim() || 'Card';
        const selectedFreq = freqRadios.find((r) => r.checked)?.value || 'Monthly';

        const monthlyEq = selectedFreq === 'Yearly' ? amount / 12 : amount;
        const yearlyEq = selectedFreq === 'Yearly' ? amount : amount * 12;

        if (previewName) previewName.textContent = displayName;
        if (previewMonogram) {
          previewMonogram.textContent = monogram;
          previewMonogram.style.backgroundColor = categoryColors[category] || '#fed7aa';
        }
        if (previewCategory) previewCategory.textContent = category;
        if (previewPayment) previewPayment.textContent = payment;
        if (previewPrice) previewPrice.textContent = 'RM ' + amount.toFixed(2);
        if (previewFrequency) previewFrequency.textContent = selectedFreq;
        if (previewMonthly) previewMonthly.textContent = 'RM ' + monthlyEq.toFixed(2);
        if (previewYearly) previewYearly.textContent = 'RM ' + yearlyEq.toFixed(2);
      };

      nameInput?.addEventListener('input', updatePreview);
      priceInput?.addEventListener('input', updatePreview);
      categorySelect?.addEventListener('change', updatePreview);
      paymentInput?.addEventListener('input', updatePreview);
      freqRadios.forEach((radio) => radio.addEventListener('change', updatePreview));

      updatePreview();
    })();
  </script>
@endpush
