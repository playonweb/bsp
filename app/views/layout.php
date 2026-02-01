<!DOCTYPE html>
<html lang="en" class="dark">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Better Stack | Personnel Registry</title>
  <link href="/favicon.ico" rel="icon" type="image/x-icon" />

  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/primeicons@7.0.0/primeicons.min.css">
  <script src="https://unpkg.com/petite-vue" defer init></script>

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    :root {
      --apple-blue: #0A84FF;
      --apple-bg: #000000;
    }

    * {
      -webkit-tap-highlight-color: transparent;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      background-color: var(--apple-bg);
      color: #FFFFFF;
      overflow-x: hidden;
    }

    .apple-card {
      background: rgba(28, 28, 30, 0.8);
      backdrop-filter: blur(40px);
      -webkit-backdrop-filter: blur(40px);
      border: 0.5px solid rgba(255, 255, 255, 0.1);
      border-radius: 16px;
    }

    .input-apple {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid transparent;
      border-radius: 10px;
      color: #fff;
      transition: all 0.2s ease;
    }

    .input-apple:focus {
      background: rgba(255, 255, 255, 0.1);
      border-color: var(--apple-blue);
      outline: none;
      box-shadow: 0 0 0 4px rgba(10, 132, 255, 0.15);
    }

    .btn-apple {
      background: var(--apple-blue);
      color: #fff;
      border-radius: 10px;
      font-weight: 600;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-apple:hover {
      background: #0070E0;
      transform: scale(1.01);
    }

    .btn-apple:active {
      transform: scale(0.98);
    }

    /* FAB - Floating Action Button */
    .fab {
      position: fixed;
      bottom: 24px;
      right: 24px;
      width: 56px;
      height: 56px;
      border-radius: 50%;
      background: linear-gradient(135deg, #0A84FF 0%, #0066CC 100%);
      box-shadow: 0 8px 24px rgba(10, 132, 255, 0.4);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      z-index: 100;
    }

    .fab:hover {
      transform: scale(1.1);
      box-shadow: 0 12px 32px rgba(10, 132, 255, 0.6);
    }

    .fab:active {
      transform: scale(0.95);
    }

    /* Mobile Form Drawer */
    .mobile-drawer {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: rgba(28, 28, 30, 0.98);
      backdrop-filter: blur(40px);
      border-radius: 20px 20px 0 0;
      max-height: 85vh;
      overflow-y: auto;
      transform: translateY(100%);
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      z-index: 200;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .mobile-drawer.active {
      transform: translateY(0);
    }

    .mobile-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.5);
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
      z-index: 150;
    }

    .mobile-overlay.active {
      opacity: 1;
      visibility: visible;
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
      width: 6px;
    }

    ::-webkit-scrollbar-track {
      background: transparent;
    }

    ::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 3px;
    }

    /* Mobile optimizations */
    @media (max-width: 767px) {
      .desktop-only {
        display: none !important;
      }

      .mobile-compact-table td {
        padding: 12px 8px !important;
        font-size: 13px;
      }

      .mobile-compact-table th {
        padding: 10px 8px !important;
        font-size: 10px;
      }
    }

    @media (min-width: 768px) {
      .mobile-only {
        display: none !important;
      }
    }

    [v-cloak] {
      display: none;
    }
  </style>
</head>

<body class="antialiased min-h-screen">
  <div class="fixed inset-0 pointer-events-none opacity-20 overflow-hidden">
    <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] bg-blue-600 rounded-full blur-[120px]"></div>
    <div class="absolute -bottom-[20%] -right-[10%] w-[40%] h-[40%] bg-indigo-600 rounded-full blur-[100px]"></div>
  </div>

  <nav class="sticky top-0 z-50 bg-black/50 backdrop-blur-xl border-b border-white/5 px-4 sm:px-6 py-4">
    <div class="max-w-6xl mx-auto flex items-center">
      <div class="flex items-center gap-3">
        <div class="w-7 h-7 bg-white rounded-lg flex items-center justify-center">
          <i class="pi pi-bolt text-black text-[10px]"></i>
        </div>
        <span class="font-bold text-base sm:text-lg tracking-tight">Better Stack</span>
      </div>
    </div>
  </nav>

  <main class="relative z-10 max-w-6xl mx-auto px-4 py-6 sm:py-12">
    <?= $content ?>
  </main>

  <footer
    class="mt-auto py-10 text-center text-neutral-600 text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.2em]">
    &copy; <?= date('Y') ?> Better Stack Personnel Registry
  </footer>
</body>

</html>