<?php
// Prepare initial data safely
$initialUsers = [];
$cities = [];
if (!empty($users)) {
	foreach ($users as $user) {
		$initialUsers[] = [
			'id' => $user->getId(),
			'name' => htmlspecialchars($user->getName(), ENT_QUOTES, 'UTF-8'),
			'email' => htmlspecialchars($user->getEmail(), ENT_QUOTES, 'UTF-8'),
			'city' => htmlspecialchars($user->getCity(), ENT_QUOTES, 'UTF-8'),
			'phone' => $user->getPhone() ? htmlspecialchars($user->getPhone(), ENT_QUOTES, 'UTF-8') : ''
		];
		if ($user->getCity()) {
			$cities[] = htmlspecialchars($user->getCity(), ENT_QUOTES, 'UTF-8');
		}
	}
}
$uniqueCities = array_unique($cities);
sort($uniqueCities);

// Country codes for phone validation
$countryCodes = [
	'+1' => 'US/Canada',
	'+44' => 'UK',
	'+49' => 'Germany',
	'+33' => 'France',
	'+420' => 'Czech Republic',
	'+421' => 'Slovakia',
	'+91' => 'India',
	'+86' => 'China',
	'+81' => 'Japan'
];
?>

<!-- Initialize app with data -->
<!-- Initialize app with data -->
<script>
	document.addEventListener('DOMContentLoaded', function () {
		if (window.initApp) {
			window.initApp({
				users: <?= json_encode($initialUsers) ?>,
				cities: <?= json_encode($uniqueCities) ?>,
				countryCodes: <?= json_encode($countryCodes) ?>
			});
		}
	});
</script>

<div v-scope="appState" @click="closeDropdowns" v-cloak
	class="font-sans antialiased text-slate-200 selection:bg-blue-500/30">

	<!-- Global Error Toast -->
	<Transition enter-active-class="transform ease-out duration-300 transition"
		enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
		enter-to-class="translate-y-0 opacity-100 sm:translate-x-0" leave-active-class="transition ease-in duration-100"
		leave-from-class="opacity-100" leave-to-class="opacity-0">
		<div v-if="globalError" class="fixed top-6 right-6 z-[100] max-w-sm w-full">
			<div
				class="bg-red-500/10 backdrop-blur-xl border border-red-500/20 text-red-200 px-4 py-3 rounded-2xl shadow-2xl flex items-start gap-3">
				<i class="pi pi-exclamation-circle text-xl mt-0.5 text-red-400"></i>
				<div class="flex-1 text-sm font-medium leading-tight pt-0.5">{{ globalError }}</div>
				<button @click="globalError = null" class="text-red-400/50 hover:text-red-300 transition-colors">
					<i class="pi pi-times"></i>
				</button>
			</div>
		</div>
	</Transition>

	<!-- Mobile Overlay -->
	<Transition enter-active-class="transition-opacity duration-300 ease-linear" enter-from-class="opacity-0"
		enter-to-class="opacity-100" leave-active-class="transition-opacity duration-300 ease-linear"
		leave-from-class="opacity-100" leave-to-class="opacity-0">
		<div v-if="isMobile && formVisible" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40"
			@click="closeForm()">
		</div>
	</Transition>

	<!-- FAB - Mobile Only -->
	<div class="fixed bottom-6 right-6 z-30 lg:hidden" v-if="!formVisible">
		<button @click="toggleForm()"
			class="w-14 h-14 bg-blue-600 hover:bg-blue-500 active:scale-95 rounded-full flex items-center justify-center shadow-lg shadow-blue-600/40 text-white transition-all duration-200">
			<i class="pi pi-plus text-xl"></i>
		</button>
	</div>

	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-8">

		<!-- Header -->
		<div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6">
			<div>
				<h1
					class="text-4xl font-extrabold tracking-tight text-white flex items-center gap-3 bg-clip-text text-transparent bg-gradient-to-r from-white to-white/70">
					Registry
				</h1>
				<p class="text-slate-400 font-medium mt-2">Global contacts directory.</p>
			</div>

			<div class="relative w-full sm:w-80 group">
				<div
					class="absolutetop-0 left-0 w-full h-full rounded-2xl bg-gradient-to-r from-blue-500/20 to-purple-500/20 blur-xl opacity-0 group-focus-within:opacity-100 transition-opacity duration-500 pointer-events-none">
				</div>
				<i
					class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-blue-400 transition-colors text-sm z-10"></i>
				<input type="text" v-model="searchQuery" @input="handleSearch" placeholder="Search by city..."
					class="relative w-full bg-slate-800/50 border border-white/10 rounded-2xl pl-11 pr-4 py-3.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:bg-slate-800/80 transition-all shadow-lg shadow-black/20">
				<div v-if="loading" class="absolute right-4 top-1/2 -translate-y-1/2 z-10">
					<i class="pi pi-spin pi-spinner text-blue-500 text-xs"></i>
				</div>
			</div>
		</div>

		<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start relative">

			<!-- Form Section -->
			<div class="lg:col-span-4 transition-all duration-300 ease-in-out" :class="{
					'fixed inset-y-0 right-0 w-full sm:w-[420px] z-50 bg-[#1c1c1e] shadow-2xl transform translate-x-0': isMobile && formVisible, 
					'translate-x-full': isMobile && !formVisible, 
					'sticky top-8': !isMobile
				}">

				<div class="h-full overflow-y-auto"
					:class="{'p-6 sm:p-8': isMobile, 'bg-slate-800/30 border border-white/10 rounded-3xl p-6 sm:p-8 backdrop-blur-xl shadow-2xl': !isMobile}">

					<!-- Mobile Form Header -->
					<div v-if="isMobile" class="flex items-center justify-between mb-8 pb-4 border-b border-white/10">
						<h2 class="text-2xl font-bold text-white">Add Member</h2>
						<button @click="closeForm()"
							class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/20 transition-all">
							<i class="pi pi-times"></i>
						</button>
					</div>

					<div v-else class="mb-8 flex items-center gap-3">
						<div
							class="w-10 h-10 rounded-2xl bg-blue-500/20 flex items-center justify-center text-blue-400">
							<i class="pi pi-user-plus text-lg"></i>
						</div>
						<h2 class="text-xl font-bold text-white">New Entry</h2>
					</div>

					<form @submit.prevent="handleSubmit" class="space-y-6">
						<!-- Custom Input Component Style -->
						<div class="space-y-2">
							<label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider ml-1">Full
								Name</label>
							<input v-model="newUser.name" @blur="validateField('name')" type="text"
								placeholder="John Appleseed"
								class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3.5 text-sm text-white focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 transition-all placeholder-slate-600"
								:class="{'border-red-500/50 focus:border-red-500/50 focus:ring-red-500/20': errors.name}">
							<p v-if="errors.name" class="text-red-400 text-xs ml-1">{{ errors.name }}</p>
						</div>

						<div class="space-y-2">
							<label
								class="text-[11px] font-bold text-slate-500 uppercase tracking-wider ml-1">Email</label>
							<input v-model="newUser.email" @blur="validateField('email')" type="email"
								placeholder="john@example.com"
								class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3.5 text-sm text-white focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 transition-all placeholder-slate-600"
								:class="{'border-red-500/50 focus:border-red-500/50 focus:ring-red-500/20': errors.email}">
							<p v-if="errors.email" class="text-red-400 text-xs ml-1">{{ errors.email }}</p>
						</div>

						<div class="space-y-2 relative">
							<label
								class="text-[11px] font-bold text-slate-500 uppercase tracking-wider ml-1">City</label>
							<div class="relative" @click.stop="showCityDropdown = !showCityDropdown">
								<input v-model="newUser.city" @blur="validateField('city')" type="text"
									placeholder="Select or type..."
									class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3.5 text-sm text-white focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 transition-all placeholder-slate-600 cursor-pointer"
									:class="{'border-red-500/50 focus:border-red-500/50 focus:ring-red-500/20': errors.city}">
								<i class="pi pi-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-600 text-[10px] transition-transform duration-300"
									:class="{'rotate-180': showCityDropdown}"></i>
							</div>

							<!-- Dropdown -->
							<Transition enter-active-class="transition ease-out duration-200"
								enter-from-class="opacity-0 translate-y-1" enter-to-class="opacity-100 translate-y-0"
								leave-active-class="transition ease-in duration-150"
								leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-1">
								<div v-if="showCityDropdown"
									class="absolute z-50 w-full mt-2 bg-[#252528] border border-white/10 rounded-xl shadow-2xl overflow-hidden flex flex-col max-h-60 ring-1 ring-black/50">
									<div class="p-2 border-b border-white/5 sticky top-0 bg-[#252528]">
										<input v-model="cityFilter" @click.stop type="text" placeholder="Filter..."
											class="w-full bg-white/5 border-none rounded-lg text-white text-xs px-3 py-2 outline-none placeholder-slate-500 focus:bg-white/10 transition-colors">
									</div>
									<div class="overflow-y-auto py-1 custom-scrollbar">
										<template v-if="allCities.length">
											<div v-for="city in filteredCities" @click="selectCity(city)"
												class="px-4 py-2.5 text-xs font-medium text-slate-300 hover:bg-blue-600 hover:text-white cursor-pointer transition-colors">
												{{ city }}
											</div>
										</template>
										<div v-else class="px-4 py-4 text-xs text-slate-500 text-center">
											No matches found.
										</div>
									</div>
								</div>
							</Transition>
							<p v-if="errors.city" class="text-red-400 text-xs ml-1">{{ errors.city }}</p>
						</div>

						<div class="space-y-2">
							<label
								class="text-[11px] font-bold text-slate-500 uppercase tracking-wider ml-1">Phone</label>
							<div class="flex gap-3">
								<div class="relative w-36">
									<select v-model="newUser.countryCode"
										class="w-full appearance-none bg-black/20 border border-white/10 rounded-xl pl-4 pr-8 py-3.5 text-sm text-white focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 transition-all cursor-pointer">
										<option v-for="(name, code) in countryCodes" :value="code">{{ code }}</option>
									</select>
									<i
										class="pi pi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-600 text-[10px] pointer-events-none"></i>
								</div>
								<div class="flex-1 relative">
									<input v-model="newUser.phoneNumber" @blur="validateField('phone')" type="tel"
										placeholder="1234567890"
										class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3.5 text-sm text-white focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 transition-all placeholder-slate-600"
										:class="{'border-red-500/50 focus:border-red-500/50 focus:ring-red-500/20': errors.phone}">
								</div>
							</div>
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

			<!-- Main Table -->
			<div class="lg:col-span-8 w-full">
				<div
					class="bg-slate-800/30 border border-white/10 rounded-3xl overflow-hidden backdrop-blur-xl shadow-2xl">
					<div class="overflow-x-auto">
						<table class="w-full text-left border-collapse">
							<thead>
								<tr
									class="text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-white/5 bg-white/[0.02]">
									<th class="px-8 py-5">Member</th>
									<th class="px-8 py-5">Location</th>
									<th class="px-8 py-5 hidden sm:table-cell">Contact</th>
								</tr>
							</thead>
							<tbody class="divide-y divide-white/5">
								<tr v-for="user in users" :key="user.id"
									class="group hover:bg-white/[0.02] transition-colors duration-200">
									<!-- Member Column -->
									<td class="px-8 py-5">
										<div class="flex items-center gap-5">
											<div
												class="w-11 h-11 rounded-full bg-slate-700/50 border border-white/10 flex items-center justify-center text-sm font-bold text-slate-300 shadow-inner group-hover:scale-110 group-hover:border-blue-500/30 group-hover:bg-blue-500/20 group-hover:text-blue-400 transition-all duration-300">
												{{ getInitials(user.name) }}
											</div>
											<div class="min-w-0">
												<div
													class="text-sm font-semibold text-white tracking-tight group-hover:text-blue-200 transition-colors">
													{{ user.name }}</div>

												<!-- Anti-Scraping Email -->
												<div class="mt-1 relative" @mouseenter="hoverReveal(user.id, 'email')"
													@mouseleave="unveil(user.id, 'email', false)">
													<div v-if="isRevealed(user.id, 'email')"
														class="flex items-center gap-3 animate-fade-in bg-white/5 px-2 py-1 rounded-md -ml-2 w-max border border-white/10">
														<a :href="'mailto:' + user.email"
															class="text-xs text-blue-300 hover:text-white transition-colors truncate max-w-[150px]">{{
															user.email }}</a>
														<div class="h-3 w-px bg-white/10"></div>
														<button @click="copyToClipboard(user.email)" title="Copy Email"
															class="text-slate-400 hover:text-white transition-colors">
															<i class="pi pi-copy text-[10px]"></i>
														</button>
													</div>
													<div v-else @click="reveal(user.id, 'email')"
														class="flex items-center gap-2 cursor-pointer group/reveal w-max">
														<span
															class="text-[10px] text-slate-600 blur-[3px] group-hover/reveal:blur-[2px] transition-all select-none">**************</span>
														<div
															class="bg-white/5 rounded-full p-1 opacity-0 group-hover/reveal:opacity-100 transition-opacity">
															<i class="pi pi-lock text-[8px] text-slate-500"></i>
														</div>
													</div>
												</div>

											</div>
										</div>
									</td>

									<!-- Location Column -->
									<td class="px-8 py-5">
										<span
											class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium bg-white/5 text-slate-300 border border-white/5 shadow-sm group-hover:border-blue-500/20 group-hover:bg-blue-500/10 group-hover:text-blue-300 transition-all">
											<i class="pi pi-map-marker text-[10px] opacity-70"></i>
											{{ user.city }}
										</span>
									</td>

									<!-- Contact Column (Phone) -->
									<td class="px-8 py-5 hidden sm:table-cell">
										<div v-if="user.phone">
											<!-- Anti-Scraping Phone -->
											<div class="relative" @mouseenter="hoverReveal(user.id, 'phone')"
												@mouseleave="unveil(user.id, 'phone', false)">
												<div v-if="isRevealed(user.id, 'phone')"
													class="flex items-center gap-3 animate-fade-in bg-white/5 px-2 py-1 rounded-md -ml-2 w-max border border-white/10">
													<a :href="'tel:' + user.phone"
														class="text-xs font-mono text-emerald-400 hover:text-emerald-300 transition-colors">{{
														user.phone }}</a>
													<div class="h-3 w-px bg-white/10"></div>
													<button @click="copyToClipboard(user.phone)" title="Copy Phone"
														class="text-slate-400 hover:text-white transition-colors">
														<i class="pi pi-copy text-[10px]"></i>
													</button>
												</div>
												<div v-else @click="reveal(user.id, 'phone')"
													class="flex items-center gap-2 cursor-pointer group/reveal w-max">
													<span
														class="text-[10px] text-slate-600 blur-[3px] group-hover/reveal:blur-[2px] transition-all select-none">•••••••••••</span>
													<div
														class="bg-white/5 rounded-full p-1 opacity-0 group-hover/reveal:opacity-100 transition-opacity">
														<i class="pi pi-lock text-[8px] text-slate-500"></i>
													</div>
												</div>
											</div>
										</div>
										<span v-else class="text-slate-700 text-xs pl-2">—</span>
									</td>
								</tr>

								<!-- Empty State -->
								<tr v-if="users.length === 0 && !loading">
									<td colspan="3" class="px-6 py-24 text-center">
										<div class="flex flex-col items-center gap-4">
											<div
												class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center">
												<i class="pi pi-search text-3xl text-slate-600"></i>
											</div>
											<p class="text-sm font-bold text-slate-500 uppercase tracking-widest">No
												members found</p>
											<button @click="searchQuery = ''; handleSearch()"
												class="text-blue-500 hover:text-blue-400 text-xs font-medium transition-colors">
												Clear search
											</button>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<!-- Pagination / Load More -->
					<div class="p-6 border-t border-white/5 bg-white/[0.01] flex flex-col items-center gap-4"
						v-if="hasMore || loadingMore">
						<button @click="loadMore()" :disabled="loadingMore"
							class="px-8 py-3 rounded-full border border-white/10 bg-white/5 text-xs font-bold text-slate-300 uppercase tracking-wider hover:bg-white/10 hover:scale-105 active:scale-95 transition-all duration-300 disabled:opacity-50 disabled:scale-100 flex items-center gap-2 shadow-lg group">
							<i v-if="loadingMore" class="pi pi-spin pi-spinner"></i>
							<span v-else>Load More Results</span>
							<i v-if="!loadingMore"
								class="pi pi-arrow-down text-[10px] group-hover:translate-y-0.5 transition-transform"></i>
						</button>
						<p class="text-[10px] text-slate-600">Showing top results from global registry</p>
					</div>
					<div class="p-8 text-center" v-else-if="users.length > 0">
						<div
							class="inline-flex items-center gap-2 text-[10px] font-bold text-slate-600 uppercase tracking-widest px-4 py-2 rounded-full bg-white/[0.02] border border-white/5">
							<span>All members loaded</span>
							<i class="pi pi-check text-emerald-500/50"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>