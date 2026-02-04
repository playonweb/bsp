// @ts-check

// @ts-ignore
import { createApp } from 'petite-vue';
// @ts-ignore
import { getUsers, createUser, getUser } from 'sdk';

// @ts-ignore
window.initApp = function (initialData) {
    const appState = {
        users: initialData.users || [],
        newUser: { name: '', email: '', city: '', phone: '' },
        searchQuery: '',
        loading: false,
        submitting: false,
        errors: {},
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

        getInitials(name) {
            return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
        },

        toggleForm() {
            this.formVisible = !this.formVisible;
        },

        closeForm() {
            this.formVisible = false;
        },

        // Contact Modal Logic
        async viewContact(user) {
            // Optimistic UI: show what we have (obfuscated) first
            this.selectedContact = user;
            this.contactModalOpen = true;

            // Fetch clear text details
            try {
                // @ts-ignore
                const res = await getUser(user.id, { reveal: true });
                if (res.status === 200 && res.data && res.data.data) {
                    // Update the selected contact with revealed info
                    this.selectedContact = {
                        id: res.data.data.id,
                        ...res.data.data.attributes
                    };
                }
            } catch (err) {
                console.error("Failed to reveal contact", err);
                // Graceful degradation: keep showing obfuscated
            }
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
                // @ts-ignore
                const res = await getUsers({ city: this.searchQuery || undefined, page: 1, limit: 20 });
                if (res.status === 200 && res.data) {
                    // map attributes to flat object for UI
                    this.users = (res.data.data || []).map(u => ({ id: u.id, ...u.attributes }));
                    this.hasMore = !!(res.data.meta && res.data.meta.next_page);
                } else {
                    this.users = [];
                }
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
                // @ts-ignore
                const res = await getUsers({
                    city: this.searchQuery || undefined,
                    page: this.page + 1,
                    limit: 20
                });
                if (res.status === 200 && res.data) {
                    const newUsers = (res.data.data || []).map(u => ({ id: u.id, ...u.attributes }));
                    this.users = [...this.users, ...newUsers];
                    this.page = res.data.meta.page;
                    this.hasMore = !!(res.data.meta && res.data.meta.next_page);
                }
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
                // Construct JSON:API payload
                const payload = {
                    data: {
                        type: 'users',
                        attributes: {
                            name: this.newUser.name,
                            email: this.newUser.email,
                            city: this.newUser.city,
                            phone: this.newUser.phone || null
                        }
                    }
                };

                // @ts-ignore
                const res = await createUser(payload);

                if (res.status === 201 && res.data && res.data.data) {
                    const newUserRecord = { id: res.data.data.id, ...res.data.data.attributes };

                    // UI Updates
                    this.users.unshift(newUserRecord);

                    // Reset
                    this.newUser = { name: '', email: '', city: '', phone: '' };
                    this.errors = {};
                    this.closeForm();
                } else if (res.status === 422) {
                    // Handle validation errors from server
                    if (res.data && res.data.errors) {
                        this.errors = {};
                        res.data.errors.forEach(e => {
                            // simplistic mapping, might need adjustment based on exact error structure
                            const field = e.source?.pointer?.split('/').pop() || 'general';
                            this.errors[field] = e.detail || e.title;
                        });
                    }
                } else {
                    throw new Error('Unexpected status: ' + res.status);
                }

            } catch (err) {
                console.error("Submit Error", err);
                this.globalError = 'Connection Error: Unable to reach server.';
                setTimeout(() => this.globalError = null, 5000);
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