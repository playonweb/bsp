<?php
if (!$user) {
    header("Location: /", true, 302);
    exit;
}

// Prepare Initials
$name = $user->getName();
$nameParts = explode(' ', trim($name));
$initials = strtoupper(substr($nameParts[0], 0, 1));
if (count($nameParts) > 1) {
    $initials .= strtoupper(substr(end($nameParts), 0, 1));
} else {
    $initials = strtoupper(substr($name, 0, 2));
}
?>

<div class="flex items-center justify-center min-h-[70vh] py-12">
    <div
        class="w-full max-w-[360px] p-[2px] rounded-[32px] bg-gradient-to-br from-white/10 via-white/5 to-transparent shadow-2xl animate-in fade-in zoom-in duration-700">
        <div
            class="bg-neutral-900/90 backdrop-blur-3xl rounded-[30px] p-8 px-6 relative overflow-hidden border border-white/5 shadow-inner">

            <!-- Dynamic Background Glow -->
            <div
                class="absolute -top-[50px] -right-[50px] w-[180px] h-[180px] bg-blue-600/20 blur-[80px] pointer-events-none rounded-full">
            </div>
            <div
                class="absolute -bottom-[50px] -left-[50px] w-[150px] h-[150px] bg-indigo-600/10 blur-[70px] pointer-events-none rounded-full">
            </div>

            <!-- Header Section -->
            <div class="text-center mb-10 relative z-10">
                <div
                    class="w-24 h-24 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-3xl mx-auto mb-6 flex items-center justify-center text-4xl font-black text-white shadow-xl shadow-blue-500/20 rotate-3 hover:rotate-0 transition-transform duration-500 cursor-default">
                    <?= $initials ?>
                </div>
                <h1 class="m-0 text-2xl font-black tracking-tight text-white mb-2">
                    <?= htmlspecialchars($user->getName()) ?>
                </h1>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/5 rounded-full border border-white/10">
                    <i class="pi pi-map-marker text-blue-400 text-[10px]"></i>
                    <span
                        class="text-slate-400 text-[11px] font-bold uppercase tracking-widest"><?= htmlspecialchars($user->getCity()) ?></span>
                </div>
            </div>

            <!-- Contact List -->
            <div class="flex flex-col gap-4 relative z-10">

                <!-- Email Row -->
                <div
                    class="group bg-white/5 border border-white/5 rounded-2xl p-4 transition-all duration-300 hover:bg-white/10 hover:border-white/10 hover:translate-y-[-2px]">
                    <div class="flex items-center justify-between mb-3">
                        <span
                            class="text-[10px] font-bold text-slate-500 uppercase tracking-widest flex items-center gap-1.5">
                            <i class="pi pi-envelope text-[12px] text-blue-400"></i>
                            Email Address
                        </span>
                        <div class="flex gap-2">
                            <button onclick="copyValue('email')"
                                class="p-1.5 text-slate-500 hover:text-white transition-colors cursor-pointer rounded-lg hover:bg-white/5"
                                title="Copy">
                                <i class="pi pi-copy text-sm"></i>
                            </button>
                            <button onclick="openMail()"
                                class="cursor-pointer bg-blue-600 hover:bg-blue-500 text-white px-4 py-1.5 rounded-xl text-[11px] font-black uppercase tracking-wider transition-all shadow-lg shadow-blue-500/20 active:scale-95">Mail</button>
                        </div>
                    </div>
                    <div id="email" onclick="reveal('email')"
                        class="text-[14px] text-slate-400 cursor-pointer font-mono bg-black/40 p-3 rounded-xl border border-dashed border-white/10 text-center transition-all hover:border-blue-500/30 hover:bg-black/60 group-hover:text-slate-300">
                        click to reveal email
                    </div>
                </div>

                <!-- Phone Row -->
                <div
                    class="group bg-white/5 border border-white/5 rounded-2xl p-4 transition-all duration-300 hover:bg-white/10 hover:border-white/10 hover:translate-y-[-2px]">
                    <div class="flex items-center justify-between mb-3">
                        <span
                            class="text-[10px] font-bold text-slate-500 uppercase tracking-widest flex items-center gap-1.5">
                            <i class="pi pi-phone text-[12px] text-emerald-400"></i>
                            Phone Number
                        </span>
                        <div class="flex gap-2">
                            <button onclick="copyValue('phone')"
                                class="p-1.5 text-slate-500 hover:text-white transition-colors cursor-pointer rounded-lg hover:bg-white/5"
                                title="Copy">
                                <i class="pi pi-copy text-sm"></i>
                            </button>
                            <button onclick="openTel()"
                                class="cursor-pointer bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-1.5 rounded-xl text-[11px] font-black uppercase tracking-wider transition-all shadow-lg shadow-emerald-500/20 active:scale-95">Call</button>
                        </div>
                    </div>
                    <div id="phone" onclick="reveal('phone')"
                        class="text-[14px] text-slate-400 cursor-pointer font-mono bg-black/40 p-3 rounded-xl border border-dashed border-white/10 text-center transition-all hover:border-emerald-500/30 hover:bg-black/60 group-hover:text-slate-300">
                        click to reveal phone
                    </div>
                </div>

            </div>

            <div class="mt-8 text-center">
                <a href="/docs/index.php#privacy"
                    class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">
                    Privacy Policy
                </a>
            </div>
        </div>
    </div>
</div>

<script type="module">
    import { getUser } from 'sdk';

    const userId = <?= json_encode((string) $user->getId()) ?>;

    // We no longer inject sensitive data here.
    // const userData = { ... }; 

    function showStatus(msg, isSuccess = true) {
        const status = document.getElementById('status');
        // Check if status element exists, if not create a temp toast or ignore
        if (!status) return;

        const originalText = 'Verified Connection';

        status.textContent = msg;
        status.classList.remove('text-slate-600');
        status.classList.add(isSuccess ? 'text-blue-400' : 'text-red-400');

        setTimeout(() => {
            status.textContent = originalText;
            status.classList.remove('text-blue-400', 'text-red-400');
            status.classList.add('text-slate-600');
        }, 2500);
    }

    // Expose functions to window so onclick handlers work
    window.reveal = async function (id) {
        const el = document.getElementById(id);
        if (el.dataset.revealed) return el.textContent;

        el.textContent = 'Loading...';

        try {
            // @ts-ignore
            const res = await getUser(userId, { reveal: true });
            if (res.status === 200 && res.data && res.data.data) {
                const attrs = res.data.data.attributes;
                const value = attrs[id]; // 'email' or 'phone'

                if (value) {
                    el.textContent = value;
                    el.dataset.revealed = '1';
                    el.classList.remove('border-dashed', 'text-slate-400');
                    el.classList.add('border-solid', 'border-white/20', 'text-white', 'bg-black/60');

                    // Add a subtle pop animation
                    el.animate([
                        { transform: 'scale(1)' },
                        { transform: 'scale(1.02)' },
                        { transform: 'scale(1)' }
                    ], { duration: 200 });

                    return value;
                }
            }
        } catch (e) {
            console.error(e);
            el.textContent = 'Error loading data';
        }
        return '';
    };

    window.copyValue = async function (id) {
        // If not revealed, reveal first
        const el = document.getElementById(id);
        if (!el.dataset.revealed) {
            await window.reveal(id);
        }

        const val = el.textContent;
        // Check if it's the actual value and not "Loading..." or error
        if (!val || val === 'Loading...' || val === 'click to reveal ' + id) return;

        navigator.clipboard.writeText(val).then(() => {
            // showStatus('Copied to clipboard'); 
            // Simple toast fallback since status element might be missing in this view
            alert('Copied to clipboard');
        }).catch(() => {
            alert('Failed to copy');
        });
    };

    window.openMail = async function () {
        const el = document.getElementById('email');
        if (!el.dataset.revealed) await window.reveal('email');
        const val = el.textContent;
        if (val && val.includes('@')) window.location.href = 'mailto:' + val;
    };

    window.openTel = async function () {
        const el = document.getElementById('phone');
        if (!el.dataset.revealed) await window.reveal('phone');
        const val = el.textContent;
        if (val && /[\d\+]/.test(val)) window.location.href = 'tel:' + val.replace(/[\s\-\(\)]/g, '');
    };
</script>