<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/csp.php';

// DIRTY TRICK: to hydrate required page, todo in ssr
$render_page = $_GET["page"] ?? "";

$sidebar_menu_items = [
                [
                    "title" => "Challenges",
                    "id" => "challenges",
                    "link" => "challenges.md"
                ],
                [
                    "title" => "Solutions",
                    "id" => "solutions",
                    "link" => "solutions.md"
                ],
                [
                    "title" => "System Design",
                    "id" => "system-design",
                    "link" => "system-design.md"
                ],
                [
                    "title" => "Branding",
                    "id" => "branding",
                    "link" => "branding.md"
                ],
                [
                    "title" => "Security Audit",
                    "id" => "security-audit",
                    "link" => "security-audit.md"
                ],
                [
                    "title" => "Terms & Conditions",
                    "id" => "terms",
                    "link" => "terms.md"
                ],
                [
                    "title" => "Privacy Policy",
                    "id" => "privacy",
                    "link" => "privacy.md"
                ]
            ]
?>

<!DOCTYPE html>
<!--
DocsViewer
Source: https://gist.github.com/rahuldhole/78c425c1b44302cdfad40865a85b74b4
This file a readymade template to quick & dirty host markdown files.

Usage:
- add your markdown files in some public folder.
- add links of your markdown files in the `sidebarMenu` array.
- add links of your markdown files in the `topBar` array.
-->
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DocsViewer</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/primeicons@7.0.0/primeicons.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/github-markdown-css/5.5.1/github-markdown-dark.min.css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/tokyo-night-dark.min.css" />

    <script>
        window.MathJax = {
            tex: { inlineMath: [['$', '$'], ['\\(', '\\)']], displayMath: [['$$', '$$']] },
            options: { ignoreHtmlClass: 'tex2jax_ignore', processHtmlClass: 'tex2jax_process' }
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked@12.0.1/lib/marked.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/mermaid@10.9.0/dist/mermaid.min.js"></script>

    <style>
        body {
            background-color: #0d1117;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
            /* Hides the page until Petite Vue & Tailwind are ready to prevent flashing */
            opacity: 0;
            transition: opacity 0.2s;
        }

        .markdown-body {
            background: transparent !important;
            font-family: inherit;
            line-height: 1.6;
        }

        .mermaid {
            background: #161b22;
            padding: 1.5rem;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            margin: 1.5rem 0;
            border: 1px solid #30363d;
            overflow-x: auto;
        }

        .markdown-body pre {
            background-color: #1a1b26 !important;
            border: 1px solid #30363d;
            border-radius: 12px;
            position: relative;
        }

        .mdiv {
            background: rgba(56, 189, 248, 0.1);
            border-left: 4px solid #38bdf8;
            padding: 1rem 1.5rem;
            margin: 1.5rem 0;
            border-radius: 0.5rem;
            color: #e2e8f0;
            font-size: 0.95em;
        }

        .mdiv::before {
            content: attr(data-label);
            display: block;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #38bdf8;
            margin-bottom: 0.5rem;
            opacity: 0.8;
        }

        .submenu-grid {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows 0.2s ease-out;
        }

        .submenu-grid.open {
            grid-template-rows: 1fr;
        }

        .submenu-content {
            overflow: hidden;
        }

        .chevron {
            transition: transform 0.2s ease;
        }

        .chevron.rotate {
            transform: rotate(90deg);
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #30363d;
            border-radius: 10px;
        }

        .loader {
            border: 2px solid #30363d;
            border-top: 2px solid #58a6ff;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <script type="importmap">
    { "imports": { "petite-vue": "https://unpkg.com/petite-vue@0.4.1/dist/petite-vue.es.js" } }
  </script>
</head>

<body @vue:mounted="init" class="flex flex-col h-screen text-gray-300 overflow-hidden bg-[#0d1117]">

    <header
        class="h-14 bg-[#161b22]/95 backdrop-blur-md border-b border-[#30363d] flex items-center justify-between px-4 sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <!-- Mobile Menu Toggle -->
            <button class="md:hidden text-gray-400 hover:text-white p-1" @click="sidebarOpen = !sidebarOpen"
                v-if="!isSingleDoc">
                <i class="pi pi-bars text-lg"></i>
            </button>

            <!-- Desktop Sidebar Toggle -->
            <button @click="desktopSidebarVisible = !desktopSidebarVisible" v-if="!isSingleDoc"
                class="hidden md:flex items-center justify-center w-8 h-8 text-gray-400 hover:text-white rounded-md hover:bg-[#1f242c] transition-colors mr-1">
                <i class="pi" :class="desktopSidebarVisible ? 'pi-align-left' : 'pi-align-justify'"></i>
            </button>

            <div class="flex items-center gap-2">
                <div class="w-7 h-7 bg-blue-500/10 rounded-lg flex items-center justify-center text-blue-400">
                    <img src="/favicon.ico" alt="Logo" class="w-5 h-5">
                </div>
                <span class="font-bold text-white tracking-tight text-sm sm:text-base">link</span>
                <span class="text-[10px] text-neutral-500 font-bold uppercase tracking-widest mt-1">Your Digital
                    Contact</span>
            </div>
        </div>

        <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
            <a v-for="link in topBar" :href="link.link"
                class="hover:text-blue-400 transition-colors flex items-center gap-2 text-gray-400">
                <i :class="link.icon" v-if="link.icon.includes('pi-')"></i>
                <span v-else class="font-bold opacity-70">{{link.icon}}</span>
                <span>{{link.text}}</span>
            </a>
        </nav>
    </header>

    <div class="flex flex-1 overflow-hidden relative">
        <!-- Mobile Backdrop -->
        <div v-if="sidebarOpen" @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/60 z-30 md:hidden transition-opacity"></div>

        <!-- Sidebar -->
        <aside v-if="!isSingleDoc"
            class="bg-[#0d1117] border-r border-[#30363d] flex flex-col absolute md:relative h-full z-40 shadow-2xl md:shadow-none overflow-hidden transition-all duration-300 ease-in-out"
            :class="[
        sidebarOpen ? 'translate-x-0 w-72' : '-translate-x-full w-72', /* Mobile State */
        desktopSidebarVisible ? 'md:translate-x-0 md:w-72' : 'md:w-0 md:border-r-0' /* Desktop State */
      ]">
            <div class="p-4 flex flex-col h-full w-72">
                <!-- Fixed width inner container to prevent content squishing during transition -->

                <!-- Search Bar -->
                <div class="mb-4">
                    <div class="relative">
                        <i class="pi pi-search absolute left-3 top-2.5 text-gray-500 text-xs"></i>
                        <input v-model="searchQuery" placeholder="Filter docs..."
                            class="w-full bg-[#161b22] border border-[#30363d] rounded-md py-1.5 pl-8 pr-3 text-xs text-gray-300 focus:border-blue-500 focus:outline-none placeholder-gray-600 transition-colors">
                    </div>
                </div>

                <div class="overflow-y-auto flex-1 custom-scrollbar pb-6">
                    <div class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-3 px-2">Explorer</div>

                    <div class="space-y-1">
                        <div v-for="item in filteredSidebarMenu" :key="item.id || item.title">

                            <!-- Regular Link -->
                            <button v-if="!item.submenu" @click="loadPage(item.link, item.id)"
                                :class="currentUrl === item.link ? 'bg-blue-500/10 text-blue-400' : 'text-gray-400 hover:bg-[#1f242c] hover:text-gray-100'"
                                class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-all flex items-center gap-3">
                                <i class="pi pi-file text-xs opacity-50"></i>
                                <span class="truncate" v-html="highlightText(item.title)"></span>
                            </button>

                            <!-- Submenu -->
                            <div v-else>
                                <button @click="toggleSubmenu(item.title)"
                                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium text-gray-400 hover:bg-[#1f242c] hover:text-gray-100 transition-all flex items-center justify-between group">
                                    <div class="flex items-center gap-3">
                                        <i class="pi pi-folder text-xs opacity-50"></i>
                                        <span v-html="highlightText(item.title)"></span>
                                    </div>
                                    <i class="pi pi-chevron-right text-[10px] opacity-40 group-hover:opacity-80 transition-transform chevron"
                                        :class="{ 'rotate': searchQuery || openSubmenus.includes(item.title) }"></i>
                                </button>

                                <!-- Auto-expand if searching -->
                                <div class="submenu-grid"
                                    :class="{ 'open': searchQuery || openSubmenus.includes(item.title) }">
                                    <div class="submenu-content pl-6 pt-1 space-y-1">
                                        <button v-for="sub in item.submenu" :key="sub.id || sub.title"
                                            @click="loadPage(sub.link, sub.id)"
                                            :class="currentUrl === sub.link ? 'text-blue-400' : 'text-gray-500 hover:text-gray-200'"
                                            class="w-full text-left px-3 py-1.5 text-[13px] transition-colors truncate">
                                            <span v-html="highlightText(sub.title)"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Empty Search State -->
                        <div v-if="filteredSidebarMenu.length === 0"
                            class="text-center py-4 text-xs text-gray-600 italic">
                            No matching documents
                        </div>
                    </div>

                    <!-- Mobile Only Top Bar Links -->
                    <div class="md:hidden mt-6 pt-6 border-t border-[#30363d] space-y-2">
                        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-2 px-2">Links
                        </div>
                        <a v-for="link in topBar" :href="link.link"
                            class="block px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg hover:bg-[#1f242c] transition-colors flex items-center gap-3">
                            <i :class="link.icon" v-if="link.icon.includes('pi-')"></i>
                            <span v-else class="font-bold opacity-70">{{link.icon}}</span>
                            <span>{{link.text}}</span>
                        </a>
                    </div>

                </div>
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto bg-[#0d1117] relative">
            <div class="max-w-4xl mx-auto px-6 py-10 md:px-12 md:py-16">

                <div v-if="loading" class="flex flex-col items-center justify-center py-32 opacity-60">
                    <div class="loader mb-4"></div>
                    <p class="text-xs font-mono tracking-widest uppercase">Fetching</p>
                </div>

                <div v-else-if="error" class="p-8 rounded-2xl border border-red-500/20 bg-red-500/5 text-center mt-10">
                    <i class="pi pi-exclamation-circle text-3xl text-red-400/50 mb-4"></i>
                    <h3 class="font-bold text-red-400">Unable to load document</h3>
                    <p class="text-sm text-gray-500 mt-2">{{error}}</p>
                    <button @click="loadPage(currentUrl)"
                        class="mt-6 text-xs bg-red-500/10 hover:bg-red-500/20 text-red-400 px-4 py-2 rounded-full transition-colors">Retry
                        Connection</button>
                </div>

                <div v-show="!loading && !error" id="content" class="markdown-body"></div>
            </div>
        </main>
    </div>

    <script type="module">
        import { createApp } from 'petite-vue';

        createApp({
            sidebarOpen: false,
            desktopSidebarVisible: true,
            loading: false,
            error: null,
            currentUrl: '',
            openSubmenus: [],
            searchQuery: '',

            topBar: [
                { text: "Home", icon: "pi pi-home", link: "/" },
                { text: "Repository", icon: "pi pi-github", link: "#" },
                { text: "OpenAPI", icon: "pi pi-file", link: "/docs/openapi.php" },
                { text: "v1.0", icon: "📦", link: "#" }
            ],

            sidebarMenu: <?php echo json_encode($sidebar_menu_items); ?>,

            get isSingleDoc() {
                if (!this.sidebarMenu || this.sidebarMenu.length === 0) return false;
                if (this.sidebarMenu.length > 1) return false;
                // Check if the single item has a submenu
                return !this.sidebarMenu[0].submenu;
            },

            get filteredSidebarMenu() {
                if (!this.searchQuery) return this.sidebarMenu;
                const q = this.searchQuery.toLowerCase();

                return this.sidebarMenu.map(item => {
                    // Check children
                    let filteredSub = [];
                    if (item.submenu) {
                        filteredSub = item.submenu.filter(s => s.title.toLowerCase().includes(q));
                    }

                    const matchesParent = item.title.toLowerCase().includes(q);

                    if (matchesParent) return item; // Parent matches, keep all (or logic to keep filtered)
                    if (filteredSub.length > 0) return { ...item, submenu: filteredSub }; // Only children match

                    return null;
                }).filter(Boolean);
            },

            init() {
                requestAnimationFrame(() => {
                    mermaid.initialize({
                        startOnLoad: false,
                        theme: 'dark',
                        securityLevel: 'loose'
                    });
                    marked.setOptions({ gfm: true, breaks: true });

                    this.checkHashAndLoad();

                    document.body.style.opacity = '1';
                });
            },

            findPathToId(items, id, path = []) {
                for (const item of items) {
                    if (item.id === id) return [...path, item];
                    if (item.submenu) {
                        const found = this.findPathToId(item.submenu, id, [...path, item.title]);
                        if (found) return found;
                    }
                }
                return null;
            },

            findFirstItem(items) {
                for (const item of items) {
                    if (item.link) return item;
                    if (item.submenu) {
                        const found = this.findFirstItem(item.submenu);
                        if (found) return found;
                    }
                }
                return null;
            },

            checkHashAndLoad() {
                let hash = window.location.hash.slice(1);
                let targetItem = null;

                // check $render_page parameter
                <?php if ($render_page) { ?>
                    hash = '<?php echo $render_page; ?>';
                <?php } ?>

                if (hash) {
                    const path = this.findPathToId(this.sidebarMenu, hash);
                    if (path) {
                        // Open parent submenus
                        path.slice(0, -1).forEach(p => {
                            if (typeof p === 'string' && !this.openSubmenus.includes(p)) {
                                this.openSubmenus.push(p);
                            }
                        });
                        targetItem = path[path.length - 1];
                    }
                }

                // Fallback to first item if no hash or hash not found
                if (!targetItem) {
                    targetItem = this.findFirstItem(this.sidebarMenu);
                }

                // cleanup url remove parameters and hash
                window.history.replaceState(null, null, window.location.pathname);

                if (targetItem) {
                    this.loadPage(targetItem.link, targetItem.id);
                }
            },

            highlightText(text) {
                if (!this.searchQuery) return text;
                const regex = new RegExp(`(${this.searchQuery})`, 'gi');
                return text.replace(regex, '<span class="text-blue-400 font-bold">$1</span>');
            },

            toggleSubmenu(title) {
                // No-op while searching to prevent confusion
                if (this.searchQuery) return;

                const index = this.openSubmenus.indexOf(title);
                if (index > -1) this.openSubmenus.splice(index, 1);
                else this.openSubmenus.push(title);
            },

            async loadPage(url, id) {
                if (!url || url === '#') return;
                this.currentUrl = url;

                // Update URL hash without scrolling
                if (id) {
                    window.history.pushState(null, null, `#${id}`);
                }

                this.loading = true;
                this.error = null;
                this.sidebarOpen = false;

                try {
                    const res = await fetch(url);
                    if (!res.ok) throw new Error(`Status: ${res.status}`);
                    const rawMd = await res.text();
                    this.renderContent(rawMd);
                } catch (e) {
                    this.error = e.message;
                } finally {
                    this.loading = false;
                }
            },
            renderContent(rawMd) {
                // 1. Pre-process custom <mdiv> tags
                const processed = rawMd.replace(
                    //gi, 
                    (match, capture) => capture.replace(/^<mdiv/, `<div`).replace(/<\/mdiv>$/, '</div>')
                );

                const el = document.getElementById("content");
                if (!el) return;

                // 2. Parse Markdown
                el.innerHTML = marked.parse(processed);

                // 3. Process all <pre> blocks
                el.querySelectorAll('pre').forEach(pre => {
                    const code = pre.querySelector('code');
                    if (!code) return;

                    // Check if it's a Mermaid diagram
                    const isMermaid = code.classList.contains('language-mermaid');

                    if (isMermaid) {
                        // MERMAID LOGIC: 
                        // Move text to a clean div and hide the original <code> to prevent conflicts
                        const diagramReq = code.textContent;
                        pre.innerHTML = ''; // Clear the <pre>
                        const mermaidDiv = document.createElement('div');
                        mermaidDiv.className = 'mermaid';
                        mermaidDiv.textContent = diagramReq;
                        pre.appendChild(mermaidDiv);

                        // Remove <pre> background/border to let the .mermaid class handle styling
                        pre.style.background = 'transparent';
                        pre.style.border = 'none';
                    } else {
                        // REGULAR CODE LOGIC:
                        hljs.highlightElement(code);

                        // Add Copy Button
                        const btn = document.createElement('button');
                        btn.className = 'absolute top-3 right-3 text-[10px] font-bold uppercase tracking-wider text-gray-500 hover:text-white bg-[#0d1117] border border-[#30363d] px-2 py-1 rounded opacity-0 transition-opacity z-10 cursor-pointer';
                        btn.innerText = 'Copy';
                        btn.onclick = (e) => {
                            e.stopPropagation();
                            navigator.clipboard.writeText(code.innerText);
                            btn.innerText = 'Done';
                            setTimeout(() => btn.innerText = 'Copy', 2000);
                        };
                        pre.addEventListener('mouseenter', () => btn.classList.remove('opacity-0'));
                        pre.addEventListener('mouseleave', () => btn.classList.add('opacity-0'));
                        pre.appendChild(btn);
                    }
                });

                const diagrams = el.querySelectorAll('.mermaid');
                if (diagrams.length) {
                    mermaid.run({ nodes: diagrams });
                }

                if (window.MathJax) {
                    MathJax.typesetPromise([el]);
                }
            }
        }).mount();
    </script>
</body>

</html>