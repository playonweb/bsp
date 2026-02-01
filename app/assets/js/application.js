import { createApp } from 'petite-vue';
import { UserSDK } from 'sdk';

window.initApp = function(initialData) {
    const appState = {
        users: initialData.users || [],
        allCities: initialData.cities || [],
        countryCodes: initialData.countryCodes || {},
        newUser: { name: '', email: '', city: '', countryCode: '+1', phoneNumber: '' },
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

        // Logic Helpers
        getInitials: (name) => name.split(' ').map(n => n[0]).join('').toUpperCase(),
        selectCity(city) {
            this.newUser.city = city;
            this.showCityDropdown = false;
            this.cityFilter = '';
        },
        toggleForm() { this.formVisible = !this.formVisible; },
        closeForm() { if (this.isMobile) this.formVisible = false; },

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

        // API Methods
        async handleSearch() {
            this.loading = true;
            try {
                this.users = await UserSDK.fetchUsers(this.searchQuery);
            } catch (e) {
                console.error('Search error:', e);
            } finally {
                this.loading = false;
            }
        },

        async handleSubmit() {
            if (this.submitting) return;

            ['name', 'email', 'city', 'phone'].forEach(f => this.validateField(f));
            if (Object.keys(this.errors).length > 0) return;

            this.submitting = true;
            try {
                const phone = this.newUser.phoneNumber 
                    ? `${this.newUser.countryCode}${this.newUser.phoneNumber}` 
                    : null;

                const newUserRecord = await UserSDK.createUser({
                    name: this.newUser.name,
                    email: this.newUser.email,
                    city: this.newUser.city,
                    phone: phone
                });

                // UI Updates
                this.users.unshift(newUserRecord);
                if (newUserRecord.city && !this.allCities.includes(newUserRecord.city)) {
                    this.allCities.push(newUserRecord.city);
                    this.allCities.sort();
                }

                // Reset
                this.newUser = { name: '', email: '', city: '', countryCode: '+1', phoneNumber: '' };
                this.errors = {};
                this.closeForm();

            } catch (err) {
                if (err.errors) {
                    this.errors = {};
                    err.errors.forEach(e => {
                        const field = e.source?.pointer?.split('/').pop();
                        if (field) this.errors[field] = e.detail;
                    });
                } else {
                    alert('An unexpected error occurred.');
                }
            } finally {
                this.submitting = false;
            }
        }
    };

    createApp({ appState }).mount();
};