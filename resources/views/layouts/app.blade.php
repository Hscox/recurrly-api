<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Recurrly — Smart Subscription Dashboard')</title>
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

  <!-- Ambient Mesh Orbs -->
  <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
    <div class="absolute -top-28 -left-24 w-[420px] h-[420px] rounded-full bg-[#ea7a53]/25 blur-[110px]"></div>
    <div class="absolute top-1/3 -right-24 w-[460px] h-[460px] rounded-full bg-[#93c5fd]/30 blur-[120px]"></div>
    <div class="absolute -bottom-32 left-1/3 w-[440px] h-[440px] rounded-full bg-[#c4b5fd]/30 blur-[120px]"></div>
  </div>

  <div class="max-w-5xl mx-auto px-5 py-8 md:py-12">

    <!-- Top Floating Glass Navbar -->
    <header class="glass rounded-full px-4 sm:px-5 py-2.5 mb-4 md:mb-8 flex items-center justify-between gap-4">
      <a href="/" class="flex items-center gap-3 shrink-0">
        <div class="w-9 h-9 rounded-full bg-ink text-white flex items-center justify-center font-extrabold text-sm shadow-sm">
          R
        </div>
        <div class="flex items-baseline gap-2">
          <span class="font-extrabold text-lg tracking-tight">Recurrly</span>
          <span class="hidden sm:inline text-[11px] font-semibold uppercase tracking-[0.14em] text-ink/45">
            Workspace
          </span>
        </div>
      </a>

      <!-- Desktop Multi-Page Navigation -->
      <nav aria-label="Primary navigation" class="hidden md:flex items-center gap-1 rounded-full bg-white/55 border border-white/80 p-1">
        <a
          href="/"
          class="rounded-full px-4 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('dashboard') ? 'bg-ink text-white shadow-sm' : 'text-ink/65 hover:text-ink hover:bg-white/70' }}"
        >
          Overview
        </a>
        <a
          href="/subscriptions"
          class="rounded-full px-4 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('subscriptions.index', 'subscriptions.show') ? 'bg-ink text-white shadow-sm' : 'text-ink/65 hover:text-ink hover:bg-white/70' }}"
        >
          All Subs
        </a>
        <a
          href="/subscriptions/create"
          class="rounded-full px-4 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('subscriptions.create') ? 'bg-ink text-white shadow-sm' : 'text-ink/65 hover:text-ink hover:bg-white/70' }}"
        >
          New Sub
        </a>
        <a
          href="/analytics"
          class="rounded-full px-4 py-1.5 text-xs font-bold transition-all {{ request()->routeIs('subscriptions.analytics') ? 'bg-ink text-white shadow-sm' : 'text-ink/65 hover:text-ink hover:bg-white/70' }}"
        >
          Analytics
        </a>
      </nav>
    </header>

    <!-- Mobile Multi-Page Navigation -->
    <nav aria-label="Mobile navigation" class="md:hidden glass rounded-2xl p-1.5 mb-6 grid grid-cols-4 gap-1">
      <a
        href="/"
        class="rounded-xl py-2 text-center text-xs font-bold transition-all {{ request()->routeIs('dashboard') ? 'bg-ink text-white shadow-sm' : 'text-ink/65 hover:text-ink' }}"
      >
        Overview
      </a>
      <a
        href="/subscriptions"
        class="rounded-xl py-2 text-center text-xs font-bold transition-all {{ request()->routeIs('subscriptions.index', 'subscriptions.show') ? 'bg-ink text-white shadow-sm' : 'text-ink/65 hover:text-ink' }}"
      >
        All Subs
      </a>
      <a
        href="/subscriptions/create"
        class="rounded-xl py-2 text-center text-xs font-bold transition-all {{ request()->routeIs('subscriptions.create') ? 'bg-ink text-white shadow-sm' : 'text-ink/65 hover:text-ink' }}"
      >
        New Sub
      </a>
      <a
        href="/analytics"
        class="rounded-xl py-2 text-center text-xs font-bold transition-all {{ request()->routeIs('subscriptions.analytics') ? 'bg-ink text-white shadow-sm' : 'text-ink/65 hover:text-ink' }}"
      >
        Analytics
      </a>
    </nav>

    <!-- Notifications -->
    @if(session('success'))
      <div class="glass mb-6 rounded-2xl px-4 py-3 flex items-center justify-between border-emerald-300/70 bg-emerald-50/70 text-emerald-950 text-sm font-semibold">
        <div class="flex items-center gap-2.5">
          <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
          <span>{{ session('success') }}</span>
        </div>
      </div>
    @endif

    @if($errors->any())
      <div class="glass mb-6 rounded-2xl px-4 py-3 border-red-300/80 bg-red-50/70 text-red-900 text-sm font-medium">
        {{ $errors->first() }}
      </div>
    @endif

    @yield('content')
  </div>

  @stack('scripts')
</body>
</html>
