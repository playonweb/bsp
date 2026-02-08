<?php
// Initial users are already pre-formatted from the model
$initialUsers = $users ?? [];
?>

<!-- Initialize app with data -->
<script>
	// @ts-check
	window.APP_INITIAL_DATA = {
		users: <?= json_encode($initialUsers) ?>
	};
</script>

<div v-scope @click="closeDropdowns" v-cloak class="font-sans antialiased text-slate-200 selection:bg-blue-500/30">

	<!-- Global Error Toast -->
	<div v-if="globalError" data-testid="global-error-toast"
		class="fixed top-6 right-6 z-[100] max-w-sm w-full animate-fade-in">
		<div
			class="bg-red-500/10 backdrop-blur-xl border border-red-500/20 text-red-200 px-4 py-3 rounded-2xl shadow-2xl flex items-start gap-3">
			<i class="pi pi-exclamation-circle text-xl mt-0.5 text-red-400"></i>
			<div class="flex-1 text-sm font-medium leading-tight pt-0.5">{{ globalError }}</div>
			<button @click="globalError = null" class="text-red-400/50 hover:text-red-300 transition-colors">
				<i class="pi pi-times"></i>
			</button>
		</div>
	</div>

	<!-- Contact Details Modal (Visiting Card) -->
	<div v-if="contactModalOpen && selectedContact"
		class="fixed inset-0 z-[60] flex items-center justify-center px-4 animate-fade-in" @click="closeContact()">

		<!-- Backdrop (Blur) -->
		<div class="absolute inset-0 bg-black/80 backdrop-blur-xl"></div>


		<!-- Card Container -->
		<?php include '_card.php'; ?>
	</div>



	<!-- FAB - Mobile Only -->
	<div class="fixed bottom-6 right-6 z-30" v-if="!formVisible">
		<button @click.stop="toggleForm()" data-testid="fab-add-contact"
			class="w-14 h-14 bg-blue-600 hover:bg-blue-500 active:scale-95 rounded-full flex items-center justify-center shadow-lg shadow-blue-600/40 text-white transition-all duration-200">
			<i class="pi pi-plus text-xl"></i>
		</button>
	</div>

	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-8">

		<!-- Custom Toolbar / Navigation -->
		<div
			class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-slate-800/20 border border-white/5 p-4 rounded-3xl backdrop-blur-md">
			<div class="flex items-center gap-4">
				<div
					class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-600/20">
					<i class="pi pi-users text-xl text-white"></i>
				</div>
				<div>
					<h2 class="text-lg font-bold text-white leading-tight">Alink</h2>
					<p class="text-xs text-slate-400 font-medium">Your Digital Contact, One Tap Away</p>
				</div>
			</div>

			<div class="relative w-full md:w-96 group">
				<i
					class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-blue-400 transition-colors text-sm z-10"></i>
				<input type="text" v-model="searchQuery" @input="handleSearch" placeholder="Search members by city..."
					data-testid="search-input"
					class="w-full bg-black/20 border border-white/10 rounded-xl pl-11 pr-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:bg-black/40 transition-all">
				<div v-if="loading" class="absolute right-4 top-1/2 -translate-y-1/2 z-10">
					<i class="pi pi-spin pi-spinner text-blue-500 text-xs"></i>
				</div>
			</div>
		</div>

		<div class="relative">

			<!-- Form Modal (Apple Design Standards) -->
			<div v-if="formVisible" class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 sm:px-0">

				<!-- Backdrop -->
				<div class="absolute inset-0 bg-black/60 backdrop-blur-md animate-fade-in" @click="closeForm()"></div>

				<!-- Modal Container -->
				<div class="relative glass-dark rounded-[2.5rem] w-full max-w-lg shadow-2xl overflow-hidden animate-scale-in"
					@click.stop>

					<!-- Header Decoration -->
					<div
						class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500">
					</div>

					<div class="p-8 sm:p-12">
						<!-- Close Button -->
						<button @click="closeForm()" data-testid="form-modal-close"
							class="absolute top-8 right-8 w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/10 transition-all border border-white/5 z-10">
							<i class="pi pi-times"></i>
						</button>

						<!-- Form Header -->
						<div class="mb-10">
							<h2 class="text-3xl font-extrabold text-white tracking-tight mb-3">Join the Future</h2>
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
								<label
									class="text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">Email</label>
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
								<label
									class="text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">City</label>
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
								<label
									class="text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">Phone</label>
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

			<!-- Main Content: Grid -->
			<div class="w-full">

				<!-- Grid Layout -->
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
					<div v-for="user in users" :key="user.id" data-testid="contact-card"
						class="bg-slate-800/30 border border-white/10 rounded-3xl p-6 backdrop-blur-xl hover:bg-slate-800/50 transition-colors group">

						<div class="flex items-start justify-between">
							<div class="flex items-center gap-4">
								<div
									class="w-12 h-12 rounded-full bg-slate-700/50 border border-white/10 flex items-center justify-center text-sm font-bold text-slate-300 shadow-inner group-hover:scale-105 group-hover:border-blue-500/30 group-hover:bg-blue-500/20 group-hover:text-blue-400 transition-all duration-300">
									{{ getInitials(user.name) }}
								</div>
								<div>
									<h3 class="font-bold text-white text-base leading-tight">{{ user.name }}</h3>
									<div class="flex items-center gap-2 mt-1">
										<span
											class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium bg-white/5 text-slate-400 border border-white/5">
											<i class="pi pi-map-marker text-[9px]"></i>
											{{ user.city }}
										</span>
									</div>
								</div>
							</div>
						</div>

						<button @click.stop="viewContact(user)" data-testid="view-contact-btn"
							class="mt-6 w-full py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-xs font-bold text-slate-300 hover:text-white transition-all flex items-center justify-center gap-2 border border-white/5 group-hover:border-white/10">
							<i class="pi pi-id-card"></i>
							View Contact
						</button>
					</div>
				</div>

				<!-- Empty State -->
				<div v-if="users.length === 0 && !loading" data-testid="empty-state"
					class="bg-slate-800/30 border border-white/10 rounded-3xl p-12 text-center backdrop-blur-xl">
					<div class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center mx-auto mb-4">
						<i class="pi pi-search text-3xl text-slate-600"></i>
					</div>
					<p class="text-slate-400 font-medium">No members found matching your search.</p>
					<button @click="searchQuery = ''; handleSearch()" data-testid="clear-search-btn"
						class="mt-4 text-blue-400 hover:text-blue-300 text-sm font-bold transition-colors">
						Clear Filters
					</button>
				</div>

				<!-- Pagination / Load More -->
				<div class="mt-8 flex justify-center" v-if="hasMore || loadingMore">
					<div id="scroll-sentinel"></div>
					<button @click="loadMore()" :disabled="loadingMore" data-testid="load-more-btn"
						class="px-8 py-3 rounded-full border border-white/10 bg-white/5 text-xs font-bold text-slate-300 uppercase tracking-wider hover:bg-white/10 hover:scale-105 active:scale-95 transition-all duration-300 disabled:opacity-50 disabled:scale-100 flex items-center gap-2 shadow-lg group">
						<i v-if="loadingMore" class="pi pi-spin pi-spinner"></i>
						<span v-else>Load More Results</span>
					</button>
				</div>

			</div>
		</div>
	</div>
</div>