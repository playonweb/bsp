<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/csp.php';
?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Alink</title>
  <link href="/favicon.ico" rel="icon" type="image/x-icon" />

  <!-- Preload critical CSS resources to prevent FOUC -->
  <link rel="preload" href="https://cdn.jsdelivr.net/npm/primeicons@7.0.0/primeicons.min.css" as="style">
  <link rel="preload" href="/assets/css/application.css" as="style">

  <!-- Critical inline CSS to prevent layout shift -->
  <style>
    [v-cloak] {
      display: none;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      background-color: #000000;
      color: #FFFFFF;
      overflow-x: hidden;
      margin: 0;
      padding: 0;
    }
  </style>

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
      "sdk": "/assets/js/sdk.js"
    }
  }
  </script>

  <!-- Application JavaScript -->
  <script type="module" src="/assets/js/application.js"></script>

  <!-- QRCode Library -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>

<body class="antialiased min-h-screen">
  <!-- Background aura -->
  <div class="fixed inset-0 pointer-events-none opacity-20 overflow-hidden">
    <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] bg-blue-600 rounded-full blur-[120px]"></div>
    <div class="absolute -bottom-[20%] -right-[10%] w-[40%] h-[40%] bg-indigo-600 rounded-full blur-[100px]"></div>
  </div>

  <!-- Header -->
  <nav data-testid="header-nav"
    class="top-0 z-30 bg-black/60 backdrop-blur-2xl border-b border-white/10 px-4 sm:px-8 py-4">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      <div class="flex items-center gap-4 group cursor-pointer">
        <a href="/" data-testid="logo-link">
          <div
            class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-transform duration-300">
            <img src="/favicon.ico" alt="Logo" class="w-6 h-6 object-contain invert">
          </div>
        </a>
        <div class="flex flex-col">
          <span class="font-extrabold text-lg sm:text-xl tracking-tight text-white leading-none">link</span>
          <span class="text-[10px] text-neutral-500 font-bold uppercase tracking-widest mt-1">Digital Contact</span>
        </div>
      </div>

      <div class="flex items-center gap-1 transition-all">
        <a href="https://github.com/rahuldhole" data-testid="github-link"
          class="px-3 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition-all flex items-center gap-2">
          <i class="pi pi-github text-[10px] sm:text-xs"></i> Github
        </a>
        <a href="/docs/index.php" data-testid="docs-link"
          class="px-3 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition-all flex items-center gap-2">
          <i class="pi pi-file text-[10px] sm:text-xs"></i> Docs
        </a>
      </div>
    </div>
  </nav>

  <main class="relative max-w-6xl mx-auto px-4 py-6 sm:py-12">
    <?= $content ?>
  </main>

  <footer data-testid="footer"
    class="mt-auto py-10 text-center text-neutral-600 text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.2em]">
    &copy; <?= date('Y') ?> Alink
  </footer>
</body>

</html>