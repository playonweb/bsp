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
      "sdk": "/assets/js/sdk.js"
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
  <nav class="sticky top-0 z-50 bg-black/50 backdrop-blur-xl border-b border-white/5 px-4 sm:px-6 py-4">
    <div class="max-w-6xl mx-auto flex items-center">
      <div class="flex items-center gap-3">
        <div class="w-7 h-7 bg-white rounded-lg flex items-center justify-center">
          <img src="/favicon.ico" alt="Logo" class="w-full h-full object-contain">
        </div>
        <span class="font-bold text-base sm:text-lg tracking-tight">Better Stack</span>
        <span class="text-xs text-neutral-500">PHP Test Application</span>
      </div>
      <div class="links">
        <a href="/docs/challenges.php" class="highlight"><i class="pi pi-file"></i> Challenges</a>
        <a href="/docs/solutions.php"><i class="pi pi-file"></i> Solutions</a>
        <a href="/docs/openapi.php"><i class="pi pi-file"></i> API</a>
      </div>
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