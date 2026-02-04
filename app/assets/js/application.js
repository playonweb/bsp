// @ts-check

// @ts-ignore
import { createApp } from 'petite-vue';
// @ts-ignore
import { UserSDK } from 'sdk';

// @ts-ignore
window.initApp = function (initialData) {
    const appState = {
        users: initialData.users || [],
        allCities: initialData.cities || [],
        newUser: { name: '', email: '', city: '', phone: '' },
        searchQuery: '',
        loading: false,
        submitting: false,
        errors: {},
        showCityDropdown: false,
        cityFilter: '',
        formVisible: false,
        isMobile: window.innerWidth < 1024,

        // Pagination
        page: 1,
        hasMore: true,
        loadingMore: false,
        globalError: null,

        // Contact View State
        selectedContact: null,
        contactModalOpen: false,

        // Getters
        get filteredCities() {
            if (!this.cityFilter) return this.allCities;
            const lower = this.cityFilter.toLowerCase();
            return this.allCities.filter(c => c.toLowerCase().includes(lower));
        },

        getInitials(name) {
            return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
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
            this.formVisible = false;
        },

        closeDropdowns() {
            this.showCityDropdown = false;
        },

        // Contact Modal Logic
        viewContact(user) {
            this.selectedContact = user;
            this.contactModalOpen = true;
        },

        closeContact() {
            this.contactModalOpen = false;
            this.selectedContact = null;
        },

        async copyToClipboard(text) {
            if (!text) return;
            try {
                await navigator.clipboard.writeText(text);
                // Could add a toast here
            } catch (err) {
                console.error('Failed to copy match', err);
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
                    if (this.newUser.phone) {
                        // Allow digits, spaces, plus, dashes, parens
                        if (!/^[\d\s\+\-\(\)]{7,20}$/.test(this.newUser.phone)) {
                            this.errors.phone = 'Invalid phone format';
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
                    page: parseInt(this.page) + 1,
                    limit: 20
                });
                this.users = [...this.users, ...res.users];
                this.page = parseInt(res.meta.page);
                this.hasMore = !!(res.meta && res.meta.next_page);
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
                const newUserRecord = await UserSDK.createUser({
                    name: this.newUser.name,
                    email: this.newUser.email,
                    city: this.newUser.city,
                    phone: this.newUser.phone || null
                });

                // UI Updates
                this.users.unshift(newUserRecord);
                if (newUserRecord.city && !this.allCities.includes(newUserRecord.city)) {
                    this.allCities.push(newUserRecord.city);
                    this.allCities.sort();
                }

                // Reset
                this.newUser = { name: '', email: '', city: '', phone: '' };
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
            const sentinel = document.getElementById('scroll-sentinel');
            if (!sentinel) return;

            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting && this.hasMore && !this.loadingMore) {
                    this.loadMore();
                }
            }, { rootMargin: '200px' });

            observer.observe(sentinel);
        }
    };

    const app = createApp(appState);

    app.mount();

    // Responsive listener
    window.addEventListener('resize', () => {
        appState.isMobile = window.innerWidth < 1024;
    });

    // Handle infinite scroll after mount
    setTimeout(() => appState.initInfiniteScroll(), 100);
};