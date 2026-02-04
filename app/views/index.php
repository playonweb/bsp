<?php
// Prepare initial data safely
$initialUsers = [];
if (!empty($users)) {
	foreach ($users as $user) {
		// Use toJsonApi() to ensure we get the standard (obfuscated) representation + ID
		$json = $user->toJsonApi(false);
		// Flatten structure for the frontend: { id: ..., name: ..., ... }
		$initialUsers[] = array_merge(['id' => $json['id']], $json['attributes']);
	}
}
?>

<!-- Initialize app with data -->
<script>
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
		class="fixed inset-0 z-[60] flex items-center justify-center px-4 animate-fade-in">
		<div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="closeContact()"></div>
		<div
			class="relative bg-slate-900 border border-white/10 rounded-3xl p-8 max-w-md w-full shadow-2xl overflow-hidden">

			<!-- Modal Header -->
			<div class="flex items-start justify-between mb-8">
				<div class="flex items-center gap-4">
					<div
						class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 border border-white/10 flex items-center justify-center text-2xl font-bold text-white shadow-inner">
						{{ selectedContact ? getInitials(selectedContact.name) : '' }}
					</div>
					<div v-if="selectedContact">
						<h3 class="text-2xl font-bold text-white">{{ selectedContact ? selectedContact.name : '' }}</h3>
						<div class="flex items-center gap-2 text-slate-400 text-sm mt-1">
							<i class="pi pi-map-marker text-xs"></i>
							{{ selectedContact ? selectedContact.city : '' }}
						</div>
					</div>
				</div>
				<button @click="closeContact()"
					class="w-8 h-8 rounded-full bg-white/5 hover:bg-white/10 flex items-center justify-center text-slate-400 hover:text-white transition-colors">
					<i class="pi pi-times"></i>
				</button>
			</div>

			<!-- Contact Info -->
			<div class="space-y-4" v-if="selectedContact">
				<div
					class="bg-white/5 rounded-2xl p-4 border border-white/5 hover:border-blue-500/30 transition-colors group">
					<p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Email Address</p>
					<div class="flex items-center justify-between gap-3">
						<a :href="selectedContact ? 'mailto:' + selectedContact.email : '#'"
							class="text-blue-300 hover:text-blue-200 truncate font-mono text-sm">
							{{ selectedContact ? selectedContact.email : '' }}
						</a>
						<button @click="copyToClipboard(selectedContact ? selectedContact.email : '')"
							class="opacity-0 group-hover:opacity-100 p-2 text-slate-400 hover:text-white transition-all bg-white/5 rounded-lg">
							<i class="pi pi-copy"></i>
						</button>
					</div>
				</div>

				<div
					class="bg-white/5 rounded-2xl p-4 border border-white/5 hover:border-emerald-500/30 transition-colors group">
					<p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Phone Number</p>
					<div class="flex items-center justify-between gap-3">
						<div v-if="selectedContact && selectedContact.phone">
							<a :href="selectedContact ? 'tel:' + selectedContact.phone : '#'"
								class="text-emerald-300 hover:text-emerald-200 truncate font-mono text-sm">
								{{ selectedContact.phone }}
							</a>
						</div>
						<span v-else class="text-slate-500 text-sm italic">Not provided</span>

						<button v-if="selectedContact && selectedContact.phone"
							@click="copyToClipboard(selectedContact.phone)"
							class="opacity-0 group-hover:opacity-100 p-2 text-slate-400 hover:text-white transition-all bg-white/5 rounded-lg">
							<i class="pi pi-copy"></i>
						</button>
					</div>
				</div>
			</div>

			<div class="mt-8 pt-6 border-t border-white/5 text-center">
				<p class="text-xs text-slate-500">Contact details are private and for internal use only.</p>
			</div>
		</div>
	</div>


	<!-- Mobile Overlay -->
	<div v-if="formVisible" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40" @click="closeForm()">
	</div>

	<!-- FAB - Mobile Only -->
	<div class="fixed bottom-6 right-6 z-30" v-if="!formVisible">
		<button @click="toggleForm()"
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

			<!-- Form Section (Always Drawer) -->
			<div class="transition-all duration-300 ease-in-out fixed inset-y-0 right-0 w-full sm:w-[420px] z-50 bg-[#1c1c1e] shadow-2xl transform"
				:class="{
					'translate-x-0': formVisible, 
					'translate-x-full': !formVisible
				}">

				<div class="h-full overflow-y-auto p-6 sm:p-8">

					<!-- Form Header -->
					<div class="flex items-center justify-between mb-8 pb-4 border-b border-white/10">
						<h2 class="text-2xl font-bold text-white">Add Member</h2>
						<button @click="closeForm()"
							class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/20 transition-all">
							<i class="pi pi-times"></i>
						</button>
					</div>

					<form @submit.prevent="handleSubmit" class="space-y-6">
						<!-- Name -->
						<div class="space-y-2">
							<label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider ml-1">Full
								Name</label>
							<input v-model="newUser.name" @blur="validateField('name')" type="text"
								placeholder="John Appleseed"
								class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3.5 text-sm text-white focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 transition-all placeholder-slate-600"
								:class="{'border-red-500/50 focus:border-red-500/50 focus:ring-red-500/20': errors.name}">
							<p v-if="errors.name" class="text-red-400 text-xs ml-1">{{ errors.name }}</p>
						</div>

						<!-- Email -->
						<div class="space-y-2">
							<label
								class="text-[11px] font-bold text-slate-500 uppercase tracking-wider ml-1">Email</label>
							<input v-model="newUser.email" @blur="validateField('email')" type="email"
								placeholder="john@example.com"
								class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3.5 text-sm text-white focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 transition-all placeholder-slate-600"
								:class="{'border-red-500/50 focus:border-red-500/50 focus:ring-red-500/20': errors.email}">
							<p v-if="errors.email" class="text-red-400 text-xs ml-1">{{ errors.email }}</p>
						</div>

						<!-- City -->
						<div class="space-y-2 relative">
							<label
								class="text-[11px] font-bold text-slate-500 uppercase tracking-wider ml-1">City</label>
							<p v-if="errors.city" class="text-red-400 text-xs ml-1">{{ errors.city }}</p>
						</div>

						<!-- Phone (Simple) -->
						<div class="space-y-2">
							<label
								class="text-[11px] font-bold text-slate-500 uppercase tracking-wider ml-1">Phone</label>
							<input v-model="newUser.phone" @blur="validateField('phone')" type="tel"
								placeholder="+1 (555) 000-0000"
								class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3.5 text-sm text-white focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 transition-all placeholder-slate-600"
								:class="{'border-red-500/50 focus:border-red-500/50 focus:ring-red-500/20': errors.phone}">
							<p v-if="errors.phone" class="text-red-400 text-xs ml-1">{{ errors.phone }}</p>
						</div>

						<button type="submit" :disabled="submitting"
							class="w-full py-4 mt-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-lg shadow-blue-900/20 flex items-center justify-center gap-2 transition-all transform active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
							<i v-if="submitting" class="pi pi-spin pi-spinner"></i>
							<span v-else>Register Member</span>
						</button>
					</form>
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

						<button @click="viewContact(user)"
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