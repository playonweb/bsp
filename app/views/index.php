<?php
// Initial users are already pre-formatted from the model
$initialUsers = $users ?? [];
?>

<!-- Initialize app with data -->
<script>
	// @ts-check
	document.addEventListener('DOMContentLoaded', function () {
		if (window.initApp) {
			window.initApp({
				users: <?= json_encode($initialUsers) ?>
			});
		}
	});
</script>

<div @click="closeDropdowns" v-cloak class="font-sans antialiased text-slate-200 selection:bg-blue-500/30">

	<!-- Global Error Toast -->
	<div v-if="globalError" class="fixed top-6 right-6 z-[100] max-w-sm w-full animate-fade-in">
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
		<div class="relative glass-dark rounded-[2.5rem] p-8 sm:p-10 max-w-sm w-full shadow-2xl overflow-hidden animate-scale-in border border-white/10"
			@click.stop> <!-- Prevents bubbling to backdrop and main div -->

			<!-- Card Header Decoration -->
			<div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500">
			</div>

			<!-- Branding Watermark -->
			<div class="absolute -right-8 -top-8 w-32 h-32 bg-blue-500/5 rounded-full blur-3xl pointer-events-none">
			</div>

			<!-- Close Button -->
			<button @click="closeContact()"
				class="absolute top-6 right-6 w-8 h-8 rounded-full bg-white/5 hover:bg-white/10 flex items-center justify-center text-slate-500 hover:text-white transition-all">
				<i class="pi pi-times"></i>
			</button>

			<div class="flex flex-col items-center text-center mt-4">
				<!-- Avatar / Initials -->
				<div
					class="w-24 h-24 rounded-[2rem] bg-gradient-to-br from-slate-800 to-slate-900 border border-white/10 flex items-center justify-center text-3xl font-black text-white shadow-2xl mb-6 transform hover:rotate-3 transition-transform">
					{{ getInitials(selectedContact?.name ?? '') }}
				</div>

				<h3 class="text-2xl font-black text-white tracking-tight leading-tight">{{ selectedContact?.name ?? ''
					}}</h3>
				<div
					class="flex items-center gap-2 text-blue-400 font-bold text-xs uppercase tracking-widest mt-2 bg-blue-400/10 px-3 py-1 rounded-full">
					<i class="pi pi-map-marker text-[10px]"></i>
					{{ selectedContact?.city ?? '' }}
				</div>
			</div>

			<!-- QR Code Section -->
			<div class="mt-10 flex justify-center">
				<div id="qrcode" class="p-4 bg-white/5 rounded-3xl border border-white/10 shadow-inner group relative">
					<div
						class="absolute inset-0 bg-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity rounded-3xl blur-xl">
					</div>
				</div>
			</div>

			<!-- Action Buttons -->
			<div class="mt-10 space-y-3">
				<div class="grid grid-cols-2 gap-3">
					<a :href="'mailto:' + selectedContact?.email ?? ''"
						class="flex flex-col items-center justify-center p-4 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 hover:border-blue-500/30 transition-all group">
						<i class="pi pi-envelope text-lg text-blue-400 group-hover:scale-110 transition-transform"></i>
						<span class="text-[10px] font-bold text-slate-400 mt-2 uppercase">Email</span>
					</a>
					<a v-if="selectedContact?.phone ?? ''" :href="'tel:' + selectedContact?.phone ?? ''"
						class="flex flex-col items-center justify-center p-4 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 hover:border-emerald-500/30 transition-all group">
						<i class="pi pi-phone text-lg text-emerald-400 group-hover:scale-110 transition-transform"></i>
						<span class="text-[10px] font-bold text-slate-400 mt-2 uppercase">Call</span>
					</a>
					<div v-else
						class="flex flex-col items-center justify-center p-4 rounded-2xl bg-white/5 border border-white/5 opacity-50">
						<i class="pi pi-phone text-lg text-slate-600"></i>
						<span class="text-[10px] font-bold text-slate-600 mt-2 uppercase">No Phone</span>
					</div>
				</div>

				<button @click="shareContact(selectedContact)"
					class="w-full py-4 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-xl shadow-blue-600/20 flex items-center justify-center gap-3 transition-all active:scale-[0.98]">
					<i class="pi pi-share-alt"></i>
					Share Digital Card
				</button>
			</div>

			<!-- Branding Footer -->
			<div class="mt-10 pt-6 border-t border-white/5 flex flex-col items-center gap-2">
				<div class="flex items-center gap-2">
					<div class="w-5 h-5 bg-blue-600 rounded-md flex items-center justify-center">
						<img src="/favicon.ico" class="w-3 h-3 invert" />
					</div>
					<span class="text-[10px] font-black text-white uppercase tracking-tighter">Alink Digital</span>
				</div>
				<p class="text-[9px] text-slate-600 font-bold uppercase tracking-widest">Minimalist vCard • Fast &
					Secure</p>
			</div>
		</div>
	</div>



	<!-- FAB - Mobile Only -->
	<div class="fixed bottom-6 right-6 z-30" v-if="!formVisible">
		<button @click.stop="toggleForm()"
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
						<button @click="closeForm()"
							class="absolute top-8 right-8 w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/10 transition-all border border-white/5">
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
										placeholder="e.g. Jonathan Ive"
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
										placeholder="john@example.com"
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
										placeholder="e.g. Cupertino"
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
										placeholder="+1 (555) 000-0000"
										class="w-full bg-black/40 border border-white/10 rounded-2xl pl-12 pr-4 py-4 text-sm text-white focus:outline-none focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10 transition-all placeholder-slate-600"
										:class="{'border-red-500/50 focus:ring-red-500/10': errors.phone}">
								</div>
								<p v-if="errors.phone" class="text-red-400 text-[11px] font-medium ml-1">{{ errors.phone
									}}</p>
							</div>
							<!-- Terms & Conditions -->
							<div>
								<input type="checkbox" v-model="terms" required>
								I agree to the <a href="/docs/index.php#terms" class="text-blue-400">Terms &
									Conditions</a>
							</div>

							<button type="submit" :disabled="submitting"
								class="w-full py-5 mt-6 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-2xl shadow-blue-500/20 flex items-center justify-center gap-3 transition-all transform active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed">
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
					<div v-for="user in users" :key="user.id"
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

						<button @click.stop="viewContact(user)"
							class="mt-6 w-full py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-xs font-bold text-slate-300 hover:text-white transition-all flex items-center justify-center gap-2 border border-white/5 group-hover:border-white/10">
							<i class="pi pi-id-card"></i>
							View Contact
						</button>
					</div>
				</div>

				<!-- Empty State -->
				<div v-if="users.length === 0 && !loading"
					class="bg-slate-800/30 border border-white/10 rounded-3xl p-12 text-center backdrop-blur-xl">
					<div class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center mx-auto mb-4">
						<i class="pi pi-search text-3xl text-slate-600"></i>
					</div>
					<p class="text-slate-400 font-medium">No members found matching your search.</p>
					<button @click="searchQuery = ''; handleSearch()"
						class="mt-4 text-blue-400 hover:text-blue-300 text-sm font-bold transition-colors">
						Clear Filters
					</button>
				</div>

				<!-- Pagination / Load More -->
				<div class="mt-8 flex justify-center" v-if="hasMore || loadingMore">
					<div id="scroll-sentinel"></div>
					<button @click="loadMore()" :disabled="loadingMore"
						class="px-8 py-3 rounded-full border border-white/10 bg-white/5 text-xs font-bold text-slate-300 uppercase tracking-wider hover:bg-white/10 hover:scale-105 active:scale-95 transition-all duration-300 disabled:opacity-50 disabled:scale-100 flex items-center gap-2 shadow-lg group">
						<i v-if="loadingMore" class="pi pi-spin pi-spinner"></i>
						<span v-else>Load More Results</span>
					</button>
				</div>

			</div>
		</div>
	</div>
</div>