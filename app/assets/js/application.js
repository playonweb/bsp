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
            // ... (Your existing validation logic remains here) ...
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