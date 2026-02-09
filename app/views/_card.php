<div style="scrollbar-width: none; -ms-overflow-style: none;" class="relative glass-dark rounded-[2rem] p-5 sm:p-10 max-w-sm sm:max-w-2xl w-full shadow-2xl overflow-y-auto sm:overflow-visible max-h-[85vh] sm:max-h-none animate-scale-in border border-white/10"
    @click.stop> <!-- Prevents bubbling to backdrop and main div -->

    <!-- Branding Watermark -->
    <div class="absolute -right-8 -top-8 w-24 h-24 bg-blue-500/5 rounded-full blur-2xl pointer-events-none">
    </div>

    <!-- Close Button -->
    <button v-if="contactModalOpen" @click="closeContact()" data-testid="contact-modal-close"
        class="absolute top-4 right-4 w-8 h-8 rounded-full bg-white/5 hover:bg-white/10 flex items-center justify-center text-slate-500 hover:text-white transition-all z-10">
        <i class="pi pi-times"></i>
    </button>

    <div class="flex flex-col sm:flex-row gap-8 sm:gap-12 mt-2">
        <!-- Left Column: Identity & QR -->
        <div class="flex flex-col items-center sm:w-[200px] flex-shrink-0">
            <!-- Avatar / Initials -->
            <div
                class="w-20 h-20 sm:w-28 sm:h-28 rounded-[2rem] bg-gradient-to-br from-slate-800 to-slate-900 border border-white/10 flex items-center justify-center text-3xl sm:text-4xl font-black text-white shadow-xl mb-4 transform hover:rotate-3 transition-transform">
                {{ getInitials(selectedContact?.name ?? '') }}
            </div>

            <div class="text-center">
                <h3 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-tight">{{
                    selectedContact?.name ?? '' }}</h3>
                <div
                    class="inline-flex items-center gap-1.5 text-blue-400 font-bold text-[10px] uppercase tracking-widest mt-2 bg-blue-400/10 px-3 py-1 rounded-full">
                    <i class="pi pi-map-marker text-[9px]"></i>
                    {{ selectedContact?.city ?? '' }}
                </div>
            </div>

            <!-- QR Code Section -->
            <div class="mt-6">
                <div id="qrcode" data-testid="qr-code-container"
                    class="p-3 bg-white/5 rounded-2xl border border-white/10 shadow-inner group relative">
                    <div
                        class="absolute inset-0 bg-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl blur-xl">
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Contact Details & Actions -->
        <div class="flex-1 flex flex-col justify-center space-y-3">
            <h4 class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-1 ml-1 hidden sm:block">
                Digital Contact Details</h4>

            <!-- Email -->
            <div
                class="group bg-white/5 border border-white/5 rounded-xl p-4 transition-all duration-300 hover:bg-white/10 hover:border-white/10 hover:translate-y-[-1px]">
                <div class="flex items-center justify-between mb-2.5">
                    <span
                        class="text-[10px] font-bold text-slate-500 uppercase tracking-widest flex items-center gap-1.5">
                        <i class="pi pi-envelope text-[12px] text-blue-400"></i>
                        Email
                    </span>
                    <div class="flex gap-2">
                        <button @click="copyValue(selectedContact?.email)" data-testid="card-copy-email"
                            class="p-1.5 text-slate-500 hover:text-white transition-colors cursor-pointer rounded-lg hover:bg-white/5"
                            title="Copy">
                            <i class="pi pi-copy text-sm"></i>
                        </button>
                        <a :href="'mailto:' + selectedContact?.email" data-testid="card-email-action"
                            class="cursor-pointer bg-blue-600 hover:bg-blue-500 text-white px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all shadow-lg shadow-blue-500/20 active:scale-95 block pt-2 text-center decoration-0">Mail</a>
                    </div>
                </div>
                <!-- Reveal Logic -->
                <div v-if="!selectedContact?.email_revealed" @click="revealField(selectedContact, 'email')"
                    data-testid="card-email-reveal"
                    class="text-sm text-slate-400 cursor-pointer font-mono bg-black/40 p-3 rounded-xl border border-dashed border-white/10 text-center transition-all hover:border-blue-500/30 hover:bg-black/60 group-hover:text-slate-300">
                    click to reveal email
                </div>
                <div v-else
                    class="text-sm font-mono bg-black/60 p-3 rounded-xl border border-solid border-white/20 text-white text-center animate-pulse-once break-all">
                    {{ selectedContact.email }}
                </div>
            </div>

            <!-- Phone -->
            <div v-if="selectedContact?.phone || selectedContact?.has_phone"
                class="group bg-white/5 border border-white/5 rounded-xl p-4 transition-all duration-300 hover:bg-white/10 hover:border-white/10 hover:translate-y-[-1px]">
                <div class="flex items-center justify-between mb-2.5">
                    <span
                        class="text-[10px] font-bold text-slate-500 uppercase tracking-widest flex items-center gap-1.5">
                        <i class="pi pi-phone text-[12px] text-emerald-400"></i>
                        Phone
                    </span>
                    <div class="flex gap-2">
                        <button @click="copyValue(selectedContact?.phone)" data-testid="card-copy-phone"
                            class="p-1.5 text-slate-500 hover:text-white transition-colors cursor-pointer rounded-lg hover:bg-white/5"
                            title="Copy">
                            <i class="pi pi-copy text-sm"></i>
                        </button>
                        <a :href="'tel:' + selectedContact?.phone" data-testid="card-phone-action"
                            class="cursor-pointer bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all shadow-lg shadow-emerald-500/20 active:scale-95 block pt-2 text-center decoration-0">Call</a>
                    </div>
                </div>
                <!-- Reveal Logic -->
                <div v-if="!selectedContact?.phone_revealed" @click="revealField(selectedContact, 'phone')"
                    data-testid="card-phone-reveal"
                    class="text-sm text-slate-400 cursor-pointer font-mono bg-black/40 p-3 rounded-xl border border-dashed border-white/10 text-center transition-all hover:border-emerald-500/30 hover:bg-black/60 group-hover:text-slate-300">
                    click to reveal phone
                </div>
                <div v-else
                    class="text-sm font-mono bg-black/60 p-3 rounded-xl border border-solid border-white/20 text-white text-center animate-pulse-once">
                    {{ selectedContact.phone }}
                </div>
            </div>

            <!-- Share Button -->
            <button @click="shareContact(selectedContact)" data-testid="share-contact-btn"
                class="w-full mt-4 py-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-xl shadow-blue-600/20 flex items-center justify-center gap-3 transition-all active:scale-[0.98]">
                <i class="pi pi-share-alt"></i>
                Share Digital Card
            </button>

            <!-- Branding Footer (Desktop only) -->
            <div class="hidden sm:flex mt-4 pt-4 border-t border-white/5 flex-row items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 bg-white rounded-md flex items-center justify-center">
                        <img src="/assets/images/logo.svg" class="w-4 h-4" />
                    </div>
                    <span class="text-[10px] font-black text-white uppercase tracking-tighter">Card Identity</span>
                </div>
                <span class="text-[9px] text-slate-600 font-bold uppercase tracking-widest">Minimalist vCard</span>
            </div>
        </div>
    </div>

    <!-- Branding Footer (Mobile only) -->
    <div class="sm:hidden mt-6 pt-4 border-t border-white/5 flex flex-col items-center gap-2">
        <div class="flex items-center gap-2">
            <div class="w-5 h-5 bg-white rounded-md flex items-center justify-center">
                <img src="/assets/images/logo.svg" class="w-4 h-4" />
            </div>
            <span class="text-[10px] font-black text-white uppercase tracking-tighter">Card Identity</span>
        </div>
    </div>
</div>