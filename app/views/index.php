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

<div v-scope="appState" @click="showCityDropdown = false" v-cloak>
	<!-- Mobile Overlay -->
	<div class="mobile-overlay mobile-only" :class="{'active': formVisible}" @click="closeForm()"></div>

	<!-- FAB - Mobile Only -->
	<div class="fab mobile-only" @click="toggleForm()" v-if="!formVisible">
		<i class="pi pi-plus text-white text-xl"></i>
	</div>

	<div class="space-y-6 sm:space-y-8">
		<!-- Dashboard Header -->
		<div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 sm:gap-6">
			<div>
				<h1 class="text-2xl sm:text-3xl md:text-4xl font-bold tracking-tight text-white">Personnel</h1>
				<p class="text-neutral-500 font-medium mt-1 text-sm sm:text-base">Registry of all team members.</p>
			</div>

			<div class="relative w-full sm:w-72 group">
				<i
					class="pi pi-filter absolute left-4 top-1/2 -translate-y-1/2 text-neutral-600 group-focus-within:text-blue-500 transition-colors text-xs"></i>
				<input type="text" v-model="searchQuery" @input="handleSearch" placeholder="Search by city..."
					class="input-apple w-full pl-11 pr-4 py-2.5 text-sm">
				<div v-if="loading" class="absolute right-4 top-1/2 -translate-y-1/2">
					<i class="pi pi-spin pi-spinner text-blue-500 text-xs"></i>
				</div>
			</div>
		</div>

		<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start">
			<!-- Desktop Sidebar Form -->
			<div class="lg:col-span-4 desktop-only">
				<div class="apple-card p-6 sm:p-8 sticky top-24">
					<h2 class="text-xl font-bold mb-6 flex items-center gap-2">
						<i class="pi pi-user-plus text-blue-500"></i>
						Add Member
					</h2>

					<form @submit.prevent="handleSubmit" class="space-y-5">
						<!-- Name Input -->
						<div class="space-y-1.5">
							<label class="text-[10px] font-bold text-neutral-500 uppercase tracking-widest ml-1">Full
								Name</label>
							<div class="relative">
								<i
									class="pi pi-user absolute left-4 top-1/2 -translate-y-1/2 text-neutral-600 text-xs"></i>
								<input v-model="newUser.name" @blur="validateField('name')" type="text"
									placeholder="John Appleseed" class="input-apple w-full pl-11 pr-4 py-3 text-sm"
									:class="{'error': errors.name}" required>
							</div>
							<p v-if="errors.name" class="text-red-400 text-[10px] font-medium ml-1 mt-1">{{ errors.name
								}}</p>
						</div>

						<!-- Email Input -->
						<div class="space-y-1.5">
							<label
								class="text-[10px] font-bold text-neutral-500 uppercase tracking-widest ml-1">Email</label>
							<div class="relative">
								<i
									class="pi pi-envelope absolute left-4 top-1/2 -translate-y-1/2 text-neutral-600 text-xs"></i>
								<input v-model="newUser.email" @blur="validateField('email')" type="email"
									placeholder="john@example.com" class="input-apple w-full pl-11 pr-4 py-3 text-sm"
									:class="{'error': errors.email}" required>
							</div>
							<p v-if="errors.email" class="text-red-400 text-[10px] font-medium ml-1 mt-1">{{
								errors.email }}</p>
						</div>

						<!-- City Select -->
						<div class="space-y-1.5 relative">
							<label
								class="text-[10px] font-bold text-neutral-500 uppercase tracking-widest ml-1">City</label>
							<div class="relative" @click.stop="showCityDropdown = !showCityDropdown">
								<i
									class="pi pi-map-marker absolute left-4 top-1/2 -translate-y-1/2 text-neutral-600 text-xs"></i>
								<input v-model="newUser.city" @blur="validateField('city')" type="text"
									placeholder="Select or type..."
									class="input-apple w-full pl-11 pr-10 py-3 text-sm cursor-pointer"
									:class="{'error': errors.city}" required>
								<i class="pi pi-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-neutral-600 text-[10px] transition-transform"
									:class="{'rotate-180': showCityDropdown}"></i>
							</div>

							<!-- Dropdown -->
							<div v-if="showCityDropdown"
								class="absolute z-20 w-full mt-2 apple-card border border-white/10 shadow-2xl overflow-hidden flex flex-col max-h-60">
								<div class="p-2 border-b border-white/5 bg-white/5">
									<div class="relative">
										<i
											class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-neutral-600 text-[10px]"></i>
										<input v-model="cityFilter" @click.stop type="text"
											placeholder="Filter cities..."
											class="input-apple w-full pl-8 pr-3 py-1.5 text-xs">
									</div>
								</div>
								<div class="overflow-y-auto py-1">
									<template v-if="filteredCitiesList.length">
										<div v-for="city in filteredCitiesList" @click="selectCity(city)"
											class="px-4 py-2 text-xs font-medium hover:bg-blue-600 text-neutral-300 hover:text-white cursor-pointer transition-colors">
											{{ city }}
										</div>
									</template>
									<div v-else class="px-4 py-3 text-[10px] text-neutral-600 text-center italic">
										No matches.
									</div>
								</div>
							</div>
							<p v-if="errors.city" class="text-red-400 text-[10px] font-medium ml-1 mt-1">{{ errors.city
								}}</p>
						</div>

						<!-- Phone Input with Country Code -->
						<div class="space-y-1.5">
							<label class="text-[10px] font-bold text-neutral-500 uppercase tracking-widest ml-1">Phone
								(Optional)</label>
							<div class="flex gap-2">
								<div class="relative w-32">
									<select v-model="newUser.countryCode"
										class="input-apple w-full pl-3 pr-8 py-3 text-sm appearance-none cursor-pointer">
										<option v-for="(name, code) in countryCodes" :value="code">{{ code }}</option>
									</select>
									<i
										class="pi pi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-neutral-600 text-[10px] pointer-events-none"></i>
								</div>
								<div class="relative flex-1">
									<i
										class="pi pi-phone absolute left-4 top-1/2 -translate-y-1/2 text-neutral-600 text-xs"></i>
									<input v-model="newUser.phoneNumber" @blur="validateField('phone')" type="tel"
										placeholder="1234567890" class="input-apple w-full pl-11 pr-4 py-3 text-sm"
										:class="{'error': errors.phone}" pattern="[0-9]{7,12}">
								</div>
							</div>
							<p v-if="errors.phone" class="text-red-400 text-[10px] font-medium ml-1 mt-1">{{
								errors.phone }}</p>
							<p class="text-neutral-600 text-[9px] ml-1 mt-1">Format: Country code + 7-12 digits</p>
						</div>

						<button type="submit" :disabled="submitting"
							class="btn-apple w-full py-4 mt-4 flex items-center justify-center gap-2 shadow-lg shadow-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed">
							<i v-if="submitting" class="pi pi-spin pi-spinner text-sm"></i>
							<i v-else class="pi pi-plus text-sm"></i>
							<span>Register</span>
						</button>
					</form>
				</div>
			</div>

			<!-- Mobile Drawer Form -->
			<div class="mobile-drawer mobile-only" :class="{'active': formVisible}">
				<div class="p-6">
					<div class="flex items-center justify-between mb-6">
						<h2 class="text-xl font-bold flex items-center gap-2">
							<i class="pi pi-user-plus text-blue-500"></i>
							Add Member
						</h2>
						<button @click="closeForm()" type="button"
							class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center hover:bg-white/10 transition-colors">
							<i class="pi pi-times text-neutral-400"></i>
						</button>
					</div>

					<form @submit.prevent="handleSubmit" class="space-y-4">
						<!-- Name Input -->
						<div class="space-y-1.5">
							<label class="text-[10px] font-bold text-neutral-500 uppercase tracking-widest ml-1">Full
								Name</label>
							<div class="relative">
								<i
									class="pi pi-user absolute left-4 top-1/2 -translate-y-1/2 text-neutral-600 text-xs"></i>
								<input v-model="newUser.name" @blur="validateField('name')" type="text"
									placeholder="John Appleseed" class="input-apple w-full pl-11 pr-4 py-3 text-sm"
									:class="{'error': errors.name}" required>
							</div>
							<p v-if="errors.name" class="text-red-400 text-[10px] font-medium ml-1">{{ errors.name }}
							</p>
						</div>

						<!-- Email Input -->
						<div class="space-y-1.5">
							<label
								class="text-[10px] font-bold text-neutral-500 uppercase tracking-widest ml-1">Email</label>
							<div class="relative">
								<i
									class="pi pi-envelope absolute left-4 top-1/2 -translate-y-1/2 text-neutral-600 text-xs"></i>
								<input v-model="newUser.email" @blur="validateField('email')" type="email"
									placeholder="john@example.com" class="input-apple w-full pl-11 pr-4 py-3 text-sm"
									:class="{'error': errors.email}" required>
							</div>
							<p v-if="errors.email" class="text-red-400 text-[10px] font-medium ml-1">{{ errors.email }}
							</p>
						</div>

						<!-- City Input -->
						<div class="space-y-1.5">
							<label
								class="text-[10px] font-bold text-neutral-500 uppercase tracking-widest ml-1">City</label>
							<div class="relative">
								<i
									class="pi pi-map-marker absolute left-4 top-1/2 -translate-y-1/2 text-neutral-600 text-xs"></i>
								<input v-model="newUser.city" @blur="validateField('city')" type="text"
									placeholder="Enter city..." class="input-apple w-full pl-11 pr-4 py-3 text-sm"
									:class="{'error': errors.city}" required>
							</div>
							<p v-if="errors.city" class="text-red-400 text-[10px] font-medium ml-1">{{ errors.city }}
							</p>
						</div>

						<!-- Phone Input -->
						<div class="space-y-1.5">
							<label class="text-[10px] font-bold text-neutral-500 uppercase tracking-widest ml-1">Phone
								(Optional)</label>
							<div class="flex gap-2">
								<select v-model="newUser.countryCode"
									class="input-apple w-24 px-2 py-3 text-sm appearance-none">
									<option v-for="(name, code) in countryCodes" :value="code">{{ code }}</option>
								</select>
								<input v-model="newUser.phoneNumber" @blur="validateField('phone')" type="tel"
									placeholder="1234567890" class="input-apple flex-1 px-4 py-3 text-sm"
									:class="{'error': errors.phone}">
							</div>
							<p v-if="errors.phone" class="text-red-400 text-[10px] font-medium ml-1">{{ errors.phone }}
							</p>
						</div>

						<button type="submit" :disabled="submitting"
							class="btn-apple w-full py-4 mt-4 flex items-center justify-center gap-2 shadow-lg shadow-blue-500/20 text-base font-bold disabled:opacity-50"
							style="min-height: 44px;">
							<i v-if="submitting" class="pi pi-spin pi-spinner"></i>
							<i v-else class="pi pi-check"></i>
							<span>Add Member</span>
						</button>
					</form>
				</div>
			</div>

			<!-- Table -->
			<div class="lg:col-span-8 w-full">
				<div class="apple-card overflow-hidden">
					<div class="overflow-x-auto">
						<table class="w-full mobile-compact-table">
							<thead>
								<tr
									class="text-[10px] font-bold text-neutral-500 uppercase tracking-widest border-b border-white/5 bg-white/[0.01]">
									<th class="px-6 py-4 text-left">Member</th>
									<th class="px-6 py-4 text-left">Location</th>
									<th class="px-6 py-4 text-left hidden sm:table-cell">Contact</th>
								</tr>
							</thead>
							<tbody class="divide-y divide-white/5">
								<tr v-for="user in users" :key="user.id"
									class="group hover:bg-white/[0.02] transition-colors">
									<td class="px-6 py-5">
										<div class="flex items-center gap-3 sm:gap-4">
											<div
												class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-neutral-800 border border-white/10 flex items-center justify-center text-[10px] sm:text-xs font-bold text-neutral-400 group-hover:text-blue-400 group-hover:border-blue-500/50 transition-all flex-shrink-0">
												{{ getInitials(user.name) }}
											</div>
											<div class="min-w-0">
												<div
													class="text-xs sm:text-sm font-bold text-white tracking-tight truncate">
													{{ user.name }}</div>
												<div
													class="text-[10px] sm:text-[11px] font-medium text-neutral-500 truncate">
													{{ user.email }}</div>
											</div>
										</div>
									</td>
									<td class="px-6 py-5">
										<span
											class="inline-flex items-center gap-1.5 px-2 sm:px-2.5 py-1 rounded-full text-[9px] sm:text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20 whitespace-nowrap">
											<i class="pi pi-map-marker text-[8px]"></i>
											{{ user.city }}
										</span>
									</td>
									<td
										class="px-6 py-5 text-xs sm:text-sm text-neutral-400 font-medium tabular-nums hidden sm:table-cell">
										{{ user.phone || '—' }}
									</td>
								</tr>
								<tr v-if="users.length === 0">
									<td colspan="3" class="px-6 py-16 sm:py-20 text-center">
										<div class="flex flex-col items-center gap-3 sm:gap-4 opacity-30">
											<i class="pi pi-users text-2xl sm:text-4xl"></i>
											<p class="text-xs sm:text-sm font-bold uppercase tracking-widest">No members
												found</p>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<!-- Footer -->
					<div
						class="px-4 sm:px-6 py-3 sm:py-4 bg-white/[0.01] border-t border-white/5 flex justify-between items-center text-[9px] sm:text-[10px] font-bold text-neutral-600 uppercase tracking-widest">
						<span>{{ users.length }} Member{{ users.length !== 1 ? 's' : '' }}</span>
						<span class="hidden sm:inline">Live Status</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>