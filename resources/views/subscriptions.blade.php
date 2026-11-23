<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Recurrly — Smart Subscription Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
          },
          colors: {
            ink: '#081126',
            coral: '#ea7a53',
            cream: '#fff9e3',
          },
        },
      },
    };
  </script>
  <style>
    body {
      background-color: #f8f5ec;
      font-feature-settings: "cv02", "cv03", "cv04", "cv11";
    }
    .glass {
      background: rgba(255, 255, 255, 0.58);
      backdrop-filter: blur(22px);
      -webkit-backdrop-filter: blur(22px);
      border: 1px solid rgba(255, 255, 255, 0.85);
      box-shadow: 0 10px 35px -10px rgba(8, 17, 38, 0.08);
    }
    .glass-dark {
      background: rgba(8, 17, 38, 0.86);
      backdrop-filter: blur(22px);
      -webkit-backdrop-filter: blur(22px);
      border: 1px solid rgba(255, 255, 255, 0.14);
      box-shadow: 0 18px 40px -12px rgba(8, 17, 38, 0.32);
    }
    .glass-coral {
      background: linear-gradient(135deg, rgba(234, 122, 83, 0.92), rgba(224, 96, 68, 0.86));
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.35);
      box-shadow: 0 18px 40px -12px rgba(234, 122, 83, 0.42);
    }
    .glass-input {
      background: rgba(255, 255, 255, 0.65);
      border: 1px solid rgba(8, 17, 38, 0.08);
      transition: all 0.2s ease;
    }
    .glass-input:focus {
      background: rgba(255, 255, 255, 0.95);
      border-color: rgba(234, 122, 83, 0.65);
      box-shadow: 0 0 0 4px rgba(234, 122, 83, 0.12);
      outline: none;
    }
  </style>
</head>
<body class="min-h-screen text-ink antialiased relative overflow-x-hidden selection:bg-coral/20">

  @php
    // Smart monthly equivalent: Monthly counts full, Yearly divided by 12
    $monthlyEquivalent = $subscriptions->sum(function ($sub) {
      $amount = (float) preg_replace('/[^0-9.]/', '', $sub->price);
      return strtolower($sub->frequency ?? $sub->billing ?? 'monthly') === 'yearly'
        ? $amount / 12
        : $amount;
    });

    $topCategory = $subscriptions->isNotEmpty()
      ? $subscriptions->groupBy('category')->sortByDesc->count()->keys()->first()
      : 'None yet';
  @endphp

  <!-- Ambient Mesh Orbs (makes glassmorphism glow softly) -->
  <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
    <div class="absolute -top-28 -left-24 w-[420px] h-[420px] rounded-full bg-[#ea7a53]/25 blur-[110px]"></div>
    <div class="absolute top-1/3 -right-24 w-[460px] h-[460px] rounded-full bg-[#93c5fd]/30 blur-[120px]"></div>
    <div class="absolute -bottom-32 left-1/3 w-[440px] h-[440px] rounded-full bg-[#c4b5fd]/30 blur-[120px]"></div>
  </div>

  <div class="max-w-5xl mx-auto px-5 py-8 md:py-12">

    <!-- Top Floating Glass Navbar -->
    <header class="glass rounded-full px-5 py-3 mb-8 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-full bg-ink text-white flex items-center justify-center font-extrabold text-sm shadow-sm">
          R
        </div>
        <div class="flex items-baseline gap-2">
          <span class="font-extrabold text-lg tracking-tight">Recurrly</span>
          <span class="hidden sm:inline text-[11px] font-semibold uppercase tracking-[0.14em] text-ink/45">
            Workspace
          </span>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <div class="hidden sm:flex items-center gap-2 rounded-full bg-white/70 border border-white px-3.5 py-1.5 text-xs font-semibold text-ink/70">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          MySQL + Mobile Sync
        </div>
        <a
          href="/api/subscriptions"
          target="_blank"
          class="rounded-full bg-ink/5 hover:bg-ink hover:text-white border border-ink/10 px-4 py-1.5 text-xs font-semibold transition-all duration-200"
        >
          JSON API ↗
        </a>
      </div>
    </header>

    <!-- Notifications -->
    @if(session('success'))
      <div class="glass mb-6 rounded-2xl px-4 py-3 flex items-center justify-between border-emerald-300/70 bg-emerald-50/70 text-emerald-950 text-sm font-semibold">
        <div class="flex items-center gap-2.5">
          <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
          <span>{{ session('success') }}</span>
        </div>
        <span class="text-xs text-emerald-800/70 font-medium">Synced to API</span>
      </div>
    @endif

    @if($errors->any())
      <div class="glass mb-6 rounded-2xl px-4 py-3 border-red-300/80 bg-red-50/70 text-red-900 text-sm font-medium">
        {{ $errors->first() }}
      </div>
    @endif

    <!-- Smart KPI Bento Row -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
      <!-- Total Balance Card -->
      <div class="glass-coral text-white rounded-3xl p-6 flex flex-col justify-between min-h-[142px]">
        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold uppercase tracking-[0.12em] text-white/80">Total Balance</span>
          <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-[11px] font-semibold">Live DB</span>
        </div>
        <div class="mt-4">
          <p class="text-3xl font-extrabold tracking-tight tabular-nums">
            ${{ number_format($totalBalance, 2) }}
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
            ${{ number_format($monthlyEquivalent, 2) }}
          </p>
          <p class="text-xs text-ink/55 mt-1">Yearly plans normalized ÷ 12</p>
        </div>
      </div>

      <!-- Active Plans & Top Category -->
      <div class="glass rounded-3xl p-6 flex flex-col justify-between min-h-[142px]">
        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold uppercase tracking-[0.12em] text-ink/50">Active Plans</span>
          <span class="rounded-full bg-ink/5 px-2.5 py-0.5 text-[11px] font-semibold text-ink/70">
            {{ $topCategory }}
          </span>
        </div>
        <div class="mt-4">
          <p class="text-3xl font-extrabold tracking-tight tabular-nums text-ink">
            {{ $subscriptions->count() }}
          </p>
          <p class="text-xs text-ink/55 mt-1">Top category: {{ $topCategory }}</p>
        </div>
      </div>
    </section>

    <!-- Main 2-Column Smart Workspace -->
    <main class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

      <!-- Left: Add Subscription Glass Panel -->
      <section class="lg:col-span-5 glass rounded-3xl p-6">
        <div class="mb-5">
          <h2 class="text-lg font-bold tracking-tight">New Subscription</h2>
          <p class="text-xs text-ink/55 mt-0.5">Saved instantly to MySQL &amp; synced with Expo Go.</p>
        </div>

        <form action="/subscriptions" method="POST" class="space-y-4">
          @csrf

          <div>
            <label class="block text-xs font-semibold text-ink/70 mb-1.5">Service Name</label>
            <input
              type="text"
              name="name"
              placeholder="Gemini Pro, Spotify, GitHub..."
              required
              class="glass-input w-full rounded-2xl px-4 py-3 text-sm font-medium placeholder:text-ink/35"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-ink/70 mb-1.5">Price (USD)</label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-ink/40">$</span>
              <input
                type="number"
                step="0.01"
                min="0.01"
                name="price"
                placeholder="20.00"
                required
                class="glass-input w-full rounded-2xl pl-8 pr-4 py-3 text-sm font-medium tabular-nums placeholder:text-ink/35"
              />
            </div>
          </div>

          <!-- Segmented Frequency Toggle -->
          <div>
            <label class="block text-xs font-semibold text-ink/70 mb-1.5">Billing Frequency</label>
            <div class="grid grid-cols-2 gap-2.5">
              <label class="cursor-pointer">
                <input type="radio" name="frequency" value="Monthly" checked class="peer sr-only" />
                <div class="rounded-2xl py-2.5 text-center text-xs font-semibold border border-ink/10 bg-white/50 text-ink/65 peer-checked:bg-coral/15 peer-checked:border-coral peer-checked:text-coral transition-all">
                  Monthly
                </div>
              </label>
              <label class="cursor-pointer">
                <input type="radio" name="frequency" value="Yearly" class="peer sr-only" />
                <div class="rounded-2xl py-2.5 text-center text-xs font-semibold border border-ink/10 bg-white/50 text-ink/65 peer-checked:bg-coral/15 peer-checked:border-coral peer-checked:text-coral transition-all">
                  Yearly
                </div>
              </label>
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-ink/70 mb-1.5">Category</label>
            <select
              name="category"
              class="glass-input w-full rounded-2xl px-4 py-3 text-sm font-medium cursor-pointer"
            >
              <option value="AI Tools">AI Tools</option>
              <option value="Entertainment">Entertainment</option>
              <option value="Developer Tools">Developer Tools</option>
              <option value="Design">Design</option>
              <option value="Productivity">Productivity</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <button
            type="submit"
            class="glass-dark w-full rounded-2xl py-3.5 text-sm font-bold text-white hover:scale-[1.01] active:scale-[0.99] transition-all cursor-pointer mt-2"
          >
            + Add Subscription
          </button>
        </form>
      </section>

      <!-- Right: Live Subscriptions Glass List -->
      <section class="lg:col-span-7 glass rounded-3xl p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
          <div>
            <h2 class="text-lg font-bold tracking-tight">All Subscriptions</h2>
            <p class="text-xs text-ink/55 mt-0.5">Click cancel to remove from web &amp; mobile.</p>
          </div>

          <!-- Instant Search Filter -->
          <div class="relative w-full sm:w-52">
            <input
              id="sub-search"
              type="text"
              placeholder="Search plans..."
              class="glass-input w-full rounded-full px-4 py-2 text-xs font-medium placeholder:text-ink/40"
            />
          </div>
        </div>

        @if($subscriptions->isEmpty())
          <div class="rounded-2xl border border-dashed border-ink/15 bg-white/40 py-14 px-6 text-center">
            <p class="text-sm font-bold text-ink/75">No subscriptions yet</p>
            <p class="text-xs text-ink/50 mt-1">
              Add your first plan on the left or from your Recurrly mobile app.
            </p>
          </div>
        @else
          <div id="sub-list" class="space-y-3">
            @foreach($subscriptions as $sub)
              <div
                data-search="{{ strtolower($sub->name . ' ' . $sub->category) }}"
                class="sub-item group rounded-2xl bg-white/65 hover:bg-white/90 border border-white/90 px-4 py-3.5 flex items-center justify-between gap-4 transition-all duration-200 shadow-[0_4px_16px_rgba(8,17,38,0.03)]"
              >
                <div class="flex items-center gap-3.5 min-w-0">
                  <!-- Soft Pastel Brand Monogram -->
                  <div
                    class="w-11 h-11 rounded-2xl flex items-center justify-center font-extrabold text-xs text-ink shrink-0 shadow-inner"
                    style="background-color: {{ $sub->color ?? '#fed7aa' }};"
                  >
                    {{ strtoupper(substr($sub->name, 0, 2)) }}
                  </div>
                  <div class="min-w-0">
                    <h3 class="font-bold text-sm text-ink truncate">{{ $sub->name }}</h3>
                    <div class="flex items-center gap-2 mt-0.5">
                      <span class="text-[11px] font-semibold text-ink/55">{{ $sub->category }}</span>
                      <span class="text-ink/25">•</span>
                      <span class="text-[11px] text-ink/45">{{ $sub->firstBill ?? 'Active' }}</span>
                    </div>
                  </div>
                </div>

                <div class="flex items-center gap-4 shrink-0">
                  <div class="text-right">
                    <p class="font-extrabold text-sm tabular-nums text-ink">{{ $sub->price }}</p>
                    <p class="text-[11px] font-medium text-ink/45">{{ $sub->billing ?? $sub->frequency }}</p>
                  </div>

                  <form
                    action="/subscriptions/{{ $sub->id }}"
                    method="POST"
                    onsubmit="return confirm('Cancel {{ $sub->name }}?')"
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
            @endforeach
          </div>
        @endif
      </section>
    </main>
  </div>

  <!-- Lightweight Client-Side Search Script -->
  <script>
    const searchInput = document.getElementById('sub-search');
    if (searchInput) {
      searchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        document.querySelectorAll('.sub-item').forEach((card) => {
          const text = card.getAttribute('data-search') || '';
          card.style.display = text.includes(query) ? 'flex' : 'none';
        });
      });
    }
  </script>
</body>
</html>