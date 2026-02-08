<!-- Form Modal (Apple Design Standards) -->
<div v-if="formVisible" class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 sm:px-0">

    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/60 backdrop-blur-md animate-fade-in" @click="closeForm()"></div>

    <!-- Modal Container - Responsive: wider on desktop, proper sizing on mobile -->
    <div class="relative glass-dark rounded-[2.5rem] w-full max-w-[90vw] sm:max-w-2xl max-h-[85vh] shadow-2xl animate-scale-in flex flex-col"
        @click.stop>

        <!-- Header Decoration -->
        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500">
        </div>

        <!-- Scrollable Content -->
        <div style="scrollbar-width: none; -ms-overflow-style: none;" class="overflow-y-auto flex-1 p-6 sm:p-12">
            <!-- Close Button -->
            <button @click="closeForm()" data-testid="form-modal-close"
                class="absolute top-6 right-6 sm:top-8 sm:right-8 w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/10 transition-all border border-white/5 z-10">
                <i class="pi pi-times"></i>
            </button>

            <!-- Form Header -->
            <div class="mb-8 sm:mb-10">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight mb-3">Join the Future</h2>
                <p class="text-slate-400 text-sm font-medium leading-relaxed">
                    Alink makes networking seamless, memorable, and eco-friendly. Create your one-tap
                    digital card today.
                </p>
            </div>

            <form @submit.prevent="handleSubmit" class="space-y-6">
                <!-- Name -->
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">Full
                        Name</label>
                    <div class="relative group">
                        <i
                            class="pi pi-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-blue-400 transition-colors"></i>
                        <input v-model="newUser.name" @blur="validateField('name')" type="text"
                            data-testid="form-input-name" placeholder="e.g. Jonathan Ive"
                            class="w-full bg-black/40 border border-white/10 rounded-2xl pl-12 pr-4 py-4 text-sm text-white focus:outline-none focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10 transition-all placeholder-slate-600"
                            :class="{'border-red-500/50 focus:ring-red-500/10': errors.name}">
                    </div>
                    <p v-if="errors.name" class="text-red-400 text-[11px] font-medium ml-1">{{ errors.name
                        }}</p>
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">Email</label>
                    <div class="relative group">
                        <i
                            class="pi pi-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-blue-400 transition-colors"></i>
                        <input v-model="newUser.email" @blur="validateField('email')" type="email"
                            data-testid="form-input-email" placeholder="john@example.com"
                            class="w-full bg-black/40 border border-white/10 rounded-2xl pl-12 pr-4 py-4 text-sm text-white focus:outline-none focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10 transition-all placeholder-slate-600"
                            :class="{'border-red-500/50 focus:ring-red-500/10': errors.email}">
                    </div>
                    <p v-if="errors.email" class="text-red-400 text-[11px] font-medium ml-1">{{ errors.email
                        }}</p>
                </div>

                <!-- City -->
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">City</label>
                    <div class="relative group">
                        <i
                            class="pi pi-map-marker absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-blue-400 transition-colors"></i>
                        <input v-model="newUser.city" @blur="validateField('city')" type="text"
                            data-testid="form-input-city" placeholder="e.g. Cupertino"
                            class="w-full bg-black/40 border border-white/10 rounded-2xl pl-12 pr-4 py-4 text-sm text-white focus:outline-none focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10 transition-all placeholder-slate-600"
                            :class="{'border-red-500/50 focus:ring-red-500/10': errors.city}">
                    </div>
                    <p v-if="errors.city" class="text-red-400 text-[11px] font-medium ml-1">{{ errors.city
                        }}</p>
                </div>

                <!-- Phone -->
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">Phone</label>
                    <div class="relative group">
                        <i
                            class="pi pi-phone absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-blue-400 transition-colors"></i>
                        <input v-model="newUser.phone" @blur="validateField('phone')" type="tel"
                            data-testid="form-input-phone" placeholder="+1 (555) 000-0000"
                            class="w-full bg-black/40 border border-white/10 rounded-2xl pl-12 pr-4 py-4 text-sm text-white focus:outline-none focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10 transition-all placeholder-slate-600"
                            :class="{'border-red-500/50 focus:ring-red-500/10': errors.phone}">
                    </div>
                    <p v-if="errors.phone" class="text-red-400 text-[11px] font-medium ml-1">{{ errors.phone
                        }}</p>
                </div>
                <!-- Terms & Conditions -->
                <div>
                    <input type="checkbox" v-model="terms" required data-testid="form-terms-checkbox">
                    I agree to the <a href="/docs/index.php?page=terms#terms" class="text-blue-400">Terms &
                        Conditions</a>
                </div>

                <button type="submit" :disabled="submitting" data-testid="form-submit-btn"
                    class="relative w-full py-5 mt-6 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-2xl shadow-blue-500/20 flex items-center justify-center gap-3 transition-all transform active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed z-10">
                    <i v-if="submitting" class="pi pi-spin pi-spinner"></i>
                    <span v-else>Get Your Digital Card</span>
                </button>

                <p class="text-center text-[10px] text-slate-600 font-bold uppercase tracking-[0.2em] mt-8">
                    Your Digital Contact, One Tap Away
                </p>
            </form>
        </div>
    </div>
</div>