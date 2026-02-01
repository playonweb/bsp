// Application JavaScript
// Initialize Petite Vue application

// Import from CDN via importmap
import { createApp } from 'petite-vue';

// Initialize app when DOM is ready
window.initApp = function(initialData) {
	const appState = {
		users: initialData.users || [],
		allCities: initialData.cities || [],
		countryCodes: initialData.countryCodes || {},
		newUser: {
			name: '',
			email: '',
			city: '',
			countryCode: '+1',
			phoneNumber: ''
		},
		searchQuery: '',
		loading: false,
		submitting: false,
		errors: {},
		showCityDropdown: false,
		cityFilter: '',
		formVisible: window.innerWidth >= 768,
		isMobile: window.innerWidth < 768,

		get filteredCitiesList() {
			if (!this.cityFilter) return this.allCities;
			return this.allCities.filter(c => c.toLowerCase().includes(this.cityFilter.toLowerCase()));
		},

		getInitials(name) {
			return name.split(' ').map(n => n[0]).join('').toUpperCase();
		},

		selectCity(city) {
			this.newUser.city = city;
			this.showCityDropdown = false;
			this.cityFilter = '';
		},

		toggleForm() {
			this.formVisible = !this.formVisible;
		},

		closeForm() {
			if (this.isMobile) {
				this.formVisible = false;
			}
		},

		validateField(field) {
			switch (field) {
				case 'name':
					if (!this.newUser.name || this.newUser.name.length < 2) {
						this.errors.name = 'Name must be at least 2 characters';
					} else if (!/^[a-zA-Z\s\-'\.]+$/.test(this.newUser.name)) {
						this.errors.name = 'Name contains invalid characters';
					} else {
						delete this.errors.name;
					}
					break;

				case 'email':
					const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
					if (!this.newUser.email) {
						this.errors.email = 'Email is required';
					} else if (!emailRegex.test(this.newUser.email)) {
						this.errors.email = 'Invalid email format';
					} else {
						delete this.errors.email;
					}
					break;

				case 'city':
					if (!this.newUser.city || this.newUser.city.length < 2) {
						this.errors.city = 'City must be at least 2 characters';
					} else if (!/^[a-zA-Z\s\-]+$/.test(this.newUser.city)) {
						this.errors.city = 'City contains invalid characters';
					} else {
						delete this.errors.city;
					}
					break;

				case 'phone':
					if (this.newUser.phoneNumber) {
						if (!/^\d{7,12}$/.test(this.newUser.phoneNumber)) {
							this.errors.phone = 'Phone must be 7-12 digits';
						} else {
							delete this.errors.phone;
						}
					} else {
						delete this.errors.phone;
					}
					break;
			}
		},

		async handleSearch() {
			this.loading = true;
			try {
				const url = this.searchQuery
					? `/api/v1/users?city=${encodeURIComponent(this.searchQuery)}`
					: '/api/v1/users';
				const res = await fetch(url, {
					headers: { 'Accept': 'application/vnd.api+json' }
				});

				if (res.ok) {
					const data = await res.json();
					this.users = data.data.map(u => ({
						id: u.id,
						name: u.attributes.name,
						email: u.attributes.email,
						city: u.attributes.city,
						phone: u.attributes.phone
					}));
				}
			} catch (e) {
				console.error('Search error:', e);
			} finally {
				this.loading = false;
			}
		},

		async handleSubmit() {
			if (this.submitting) return;

			// Validate all fields
			this.validateField('name');
			this.validateField('email');
			this.validateField('city');
			this.validateField('phone');

			if (Object.keys(this.errors).length > 0) {
				return;
			}

			this.submitting = true;

			try {
				const phone = this.newUser.phoneNumber
					? `${this.newUser.countryCode}${this.newUser.phoneNumber}`
					: null;

				const res = await fetch('/api/v1/users', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/vnd.api+json',
						'Accept': 'application/vnd.api+json'
					},
					body: JSON.stringify({
						data: {
							type: 'users',
							attributes: {
								name: this.newUser.name,
								email: this.newUser.email,
								city: this.newUser.city,
								phone: phone
							}
						}
					})
				});

				const data = await res.json();

				if (res.status === 201) {
					this.users.unshift({
						id: data.data.id,
						name: data.data.attributes.name,
						email: data.data.attributes.email,
						city: data.data.attributes.city,
						phone: data.data.attributes.phone
					});

					if (this.newUser.city && !this.allCities.includes(this.newUser.city)) {
						this.allCities.push(this.newUser.city);
						this.allCities.sort();
					}

					this.newUser = { name: '', email: '', city: '', countryCode: '+1', phoneNumber: '' };
					this.errors = {};
					this.closeForm();
				} else if (res.status === 422) {
					// Handle validation errors from server
					this.errors = {};
					data.errors.forEach(err => {
						const field = err.source?.pointer?.split('/').pop();
						if (field) {
							this.errors[field] = err.detail;
						}
					});
				} else {
					alert(data.errors?.[0]?.detail || 'Error occurred during registration.');
				}
			} catch (e) {
				console.error('Submission error:', e);
				alert('Connection failure. Is the database running?');
			} finally {
				this.submitting = false;
			}
		}
	};

	createApp({ appState }).mount();
};
