import { createApp } from 'petite-vue';
import { UserSDK } from 'sdk';

window.initApp = function(initialData) {
    const appState = {
        users: initialData.users || [],
        allCities: initialData.cities || [],
        countryCodes: initialData.countryCodes || {},
        newUser: { name: '', email: '', city: '', countryCode: '', phoneNumber: '' },
        searchQuery: '',
        loading: false,
        submitting: false,
        errors: {},
        showCityDropdown: false,
        cityFilter: '',
        formVisible: window.innerWidth >= 1024,
        isMobile: window.innerWidth < 1024,
        
        // Pagination & UI State
        viewMode: 'grid', // 'list' or 'grid'
        page: 1,
        hasMore: true,
        loadingMore: false,
        globalError: null,
        
        // Visiting Card State
        selectedUser: null,
        cardLoading: false,
        cardVisible: false,

        // Getters
        get filteredCities() {
            if (!this.cityFilter) return this.allCities;
            const lower = this.cityFilter.toLowerCase();
            return this.allCities.filter(c => c.toLowerCase().includes(lower));
        },

        getInitials: (name) => name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2),
        
        selectCity(city) {
            this.newUser.city = city;
            this.showCityDropdown = false;
            this.cityFilter = '';
        },
        
        toggleForm() { 
            this.formVisible = !this.formVisible; 
        },
        
        closeForm() { 
            if (this.isMobile) this.formVisible = false; 
        },

        closeDropdowns() {
            this.showCityDropdown = false;
        },

        // Visiting Card Logic
        async openCard(user) {
            this.selectedUser = { ...user }; // Show what we have initially (Name, City)
            this.cardVisible = true;
            this.cardLoading = true;
            try {
                // Fetch full details
                const fullUser = await UserSDK.fetchUser(user.id);
                this.selectedUser = fullUser;
            } catch (e) {
                console.error("Failed to fetch user details", e);
                this.globalError = "Could not fetch contact details.";
            } finally {
                this.cardLoading = false;
            }
        },

        closeCard() {
            this.cardVisible = false;
            setTimeout(() => this.selectedUser = null, 300); // Wait for animation
        },

        copyToClipboard(text) {
            if (!text) return;
            navigator.clipboard.writeText(text);
            // Optional: toast
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

        // API Methods
        async handleSearch() {
            this.loading = true;
            this.page = 1;
            try {
                const res = await UserSDK.fetchUsers({ city: this.searchQuery, page: 1, limit: 20 });
                this.users = res.users;
                this.hasMore = !!(res.meta && res.meta.next_page);
            } catch (e) {
                console.error('Search error:', e);
                this.globalError = 'Failed to search. Please check your connection.';
            } finally {
                this.loading = false;
            }
        },

        async loadMore() {
            if (this.loadingMore || !this.hasMore) return;
            this.loadingMore = true;
            try {
                const res = await UserSDK.fetchUsers({ 
                    city: this.searchQuery, 
                    page: this.page + 1,
                    limit: 20
                });
                this.users = [...this.users, ...res.users];
                this.page = res.meta.page;
                this.hasMore = !!res.meta.next_page;
            } catch (e) {
                console.error('Load more error:', e);
                this.globalError = 'Failed to load more. Please try again.';
            } finally {
                this.loadingMore = false;
            }
        },

        async handleSubmit() {
            if (this.submitting) return;

            ['name', 'email', 'city', 'phone'].forEach(f => this.validateField(f));
            if (Object.keys(this.errors).length > 0) return;

            this.submitting = true;
            try {
                const countryCode = this.newUser.countryCode.startsWith('+') 
                    ? this.newUser.countryCode 
                    : `+${this.newUser.countryCode}`;

                const phone = this.newUser.phoneNumber 
                    ? `${countryCode}${this.newUser.phoneNumber}` 
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
                this.newUser = { name: '', email: '', city: '', countryCode: '', phoneNumber: '' };
                this.errors = {};
                this.closeForm();

            } catch (err) {
                console.error("Submit Error", err);
                if (err && err.errors) {
                    this.errors = {};
                    err.errors.forEach(e => {
                        const field = e.source?.pointer?.split('/').pop();
                        if (field) this.errors[field] = e.detail;
                    });
                } else if (err && (err.detail || err.title)) {
                    const msg = err.detail || err.title;
                    this.globalError = msg;
                    setTimeout(() => this.globalError = null, 5000);
                } else {
                    this.globalError = 'Connection Error: Unable to reach server.';
                    setTimeout(() => this.globalError = null, 5000);
                }
            } finally {
                this.submitting = false;
            }
        },

        initInfiniteScroll() {
            setTimeout(() => {
                const sentinel = document.getElementById('scroll-sentinel');
                if (!sentinel) {
                    console.warn('Scroll sentinel not found, retrying...');
                    this.initInfiniteScroll();
                    return;
                }

                const observer = new IntersectionObserver((entries) => {
                    if (entries[0].isIntersecting && this.hasMore && !this.loadingMore && !this.loading) {
                        this.loadMore();
                    }
                }, { 
                    root: null,
                    rootMargin: '400px',
                    threshold: 0.1
                });

                observer.observe(sentinel);
            }, 500);
        }
    };

    // Responsive listener
    window.addEventListener('resize', () => {
        const wasMobile = appState.isMobile;
        appState.isMobile = window.innerWidth < 1024;
        
        if (appState.isMobile && !wasMobile) {
            appState.formVisible = false; 
        } else if (!appState.isMobile && wasMobile) {
            appState.formVisible = true;
        }
    });

    createApp({ 
        appState,
        mounted() {
            this.appState.initInfiniteScroll();
        }
    }).mount();
};