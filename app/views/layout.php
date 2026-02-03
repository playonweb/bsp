<!DOCTYPE html>
<html lang="en" class="dark">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Registry of contacts</title>
  <link href="/favicon.ico" rel="icon" type="image/x-icon" />

  <!-- Tailwind CSS -->
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

  <!-- PrimeIcons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/primeicons@7.0.0/primeicons.min.css">

  <!-- Application CSS -->
  <link rel="stylesheet" href="/assets/css/application.css">

  <!-- Import Map for ES Modules -->
  <script type="importmap">
  {
    "imports": {
      "petite-vue": "https://unpkg.com/petite-vue@0.4.1/dist/petite-vue.es.js",
      "sdk": "/assets/js/sdk-legacy.js"
    }
  }
  </script>

  <!-- Application JavaScript -->
  <script type="module" src="/assets/js/application.js"></script>
</head>

<body class="antialiased min-h-screen">
  <!-- Background aura -->
  <div class="fixed inset-0 pointer-events-none opacity-20 overflow-hidden">
    <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] bg-blue-600 rounded-full blur-[120px]"></div>
    <div class="absolute -bottom-[20%] -right-[10%] w-[40%] h-[40%] bg-indigo-600 rounded-full blur-[100px]"></div>
  </div>

  <!-- Header -->
  <nav class="sticky top-0 z-50 bg-black/60 backdrop-blur-2xl border-b border-white/10 px-4 sm:px-8 py-4">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      <div class="flex items-center gap-4 group cursor-pointer">
        <div
          class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-transform duration-300">
          <img src="/favicon.ico" alt="Logo" class="w-6 h-6 object-contain invert">
        </div>
        <div class="flex flex-col">
          <span class="font-extrabold text-lg sm:text-xl tracking-tight text-white leading-none">Better Stack</span>
          <span class="text-[10px] text-neutral-500 font-bold uppercase tracking-widest mt-1">Registry Platform</span>
        </div>
      </div>

      <div class="hidden md:flex items-center gap-1 transition-all">
        <a href="/index.php"
          class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition-all flex items-center gap-2">
          <i class="pi pi-home text-xs"></i> Directory
        </a>
        <a href="/openapi/viewer.html"
          class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition-all flex items-center gap-2">
          <i class="pi pi-box text-xs"></i> API Reference
        </a>
        <a href="/docs/index.html#solutions"
          class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition-all flex items-center gap-2">
          <i class="pi pi-check-circle text-xs"></i> Solutions
        </a>
        <div class="h-4 w-px bg-white/10 mx-2"></div>
        <a href="/docs/index.html#challenges"
          class="ml-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-bold shadow-lg shadow-blue-600/20 hover:bg-blue-500 hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
          <i class="pi pi-code"></i> Challenges
        </a>
      </div>

      <!-- Mobile Menu Button -->
      <button class="md:hidden w-10 h-10 flex items-center justify-center text-slate-400 hover:text-white">
        <i class="pi pi-bars text-xl"></i>
      </button>
    </div>
  </nav>

  <main class="relative z-10 max-w-6xl mx-auto px-4 py-6 sm:py-12">
    <?= $content ?>
  </main>

  <footer
    class="mt-auto py-10 text-center text-neutral-600 text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.2em]">
    &copy; <?= date('Y') ?> Registry of contacts
  </footer>
</body>

</html>