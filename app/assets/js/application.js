// @ts-check

// @ts-ignore
import { createApp } from 'petite-vue';
// @ts-ignore
import { getUsers, createUser, getUser } from 'sdk';

/**
 * @typedef {Object} UserAttributes
 * @property {string} name
 * @property {string} email
 * @property {string} city
 * @property {string} [phone]
 * @property {string} [created_at]
 */

/**
 * @typedef {Object} UserResource
 * @property {string} id
 * @property {UserAttributes} attributes
 */

/**
 * @typedef {Object} AppUser - Flat user object used in the UI
 * @property {string} id
 * @property {string} name
 * @property {string} email
 * @property {string} city
 * @property {string} [phone]
 * @property {string} [created_at]
 * @property {boolean} [email_revealing]
 * @property {boolean} [phone_revealing]
 * @property {boolean} [email_revealed]
 * @property {boolean} [phone_revealed]
 */


/**
 * @typedef {Object} SDKResponse
 * @property {any} data
 * @property {number} status
 */

/**
 * @param {Object} initialData
 * @param {AppUser[]} [initialData.users]
 * @param {AppUser} [initialData.user]
 */
// @ts-ignore
window.initApp = function (initialData) {
    const appState = {
        /** @type {AppUser[]} */
        users: initialData.users || [],
        newUser: { name: '', email: '', city: '', phone: '' },
        terms: false,
        searchQuery: '',
        loading: false,
        submitting: false,
        /** @type {Record<string, string>} */
        errors: {},
        formVisible: false,
        isMobile: window.innerWidth < 1024,

        // Pagination
        page: 1,
        hasMore: true,
        loadingMore: false,
        /** @type {string|null} */
        globalError: null,

        // Contact View State
        /** @type {AppUser|null} */
        selectedContact: initialData.user || (initialData.users ? initialData.users[0] : null),
        contactModalOpen: false,

        /**
         * @param {string} name 
         */
        getInitials(name) {
            if (!name) return '?';
            return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
        },

        toggleForm() {
            this.formVisible = !this.formVisible;
        },

        closeDropdowns() {
            // Only close if not clicking inside a modal or button that should open one
            // Handled via @click.stop in the template mostly
        },

        closeForm() {
            this.formVisible = false;
            this.newUser = { name: '', email: '', city: '', phone: '' };
            this.errors = {};
        },

        /**
         * @param {string} url
         */
        generateQRCode(url) {
            const container = document.getElementById('qrcode');
            if (!container) return;
            container.innerHTML = '';
            // @ts-ignore
            new QRCode(container, {
                text: url,
                width: 180,
                height: 180,
                colorDark: "#ffffff",
                colorLight: "transparent",
                // @ts-ignore
                correctLevel: QRCode.CorrectLevel.H
            });
        },

        /**
         * View contact details in modal
         * @param {AppUser} user
         * @returns {Promise<void>}
         */
        async viewContact(user) {
            this.selectedContact = { ...user }; // Clone to avoid mutating list item directly if we want isolation
            this.contactModalOpen = true;

            // Generate share link
            const shareUrl = `${window.location.protocol}//${window.location.host}/${user.id}`;
            setTimeout(() => this.generateQRCode(shareUrl), 100);
        },

        /**
         * Reveal obfuscated field (email or phone)
         * @param {AppUser} user
         * @param {'email'|'phone'} field
         * @returns {Promise<void>}
         */
        async revealField(user, field) {
            if (!user) return;

            // Mark as loading/revealing
            if (field === 'email') user.email_revealing = true;
            if (field === 'phone') user.phone_revealing = true;

            try {
                /** @type {SDKResponse} */
                // @ts-ignore
                const res = await getUser(user.id, { reveal: true });
                if (res.status === 200 && res.data && res.data.data) {
                    const u = res.data.data.attributes;
                    // Update the specific field
                    user[field] = u[field];

                    if (field === 'email') user.email_revealed = true;
                    if (field === 'phone') user.phone_revealed = true;
                }
            } catch (err) {
                console.error(`Failed to reveal ${field}`, err);
                this.globalError = 'Failed to reveal data. Please try again.';
            } finally {
                if (field === 'email') user.email_revealing = false;
                if (field === 'phone') user.phone_revealing = false;
            }
        },

        /**
         * Copy value to clipboard
         * @param {string} value
         * @returns {Promise<void>}
         */
        async copyValue(value) {
            if (!value) return;
            try {
                await navigator.clipboard.writeText(value);
                // alert('Copied!'); // Or use a toast
            } catch (err) {
                console.error('Failed to copy', err);
            }
        },

        /**
         * Close contact modal
         */
        closeContact() {
            this.contactModalOpen = false;
            this.selectedContact = null;
        },

        /**
         * Share contact information
         * @param {AppUser|null} user
         * @returns {Promise<void>}
         */
        async shareContact(user) {
            if (!user) return;
            const shareUrl = `${window.location.protocol}//${window.location.host}/${user.id}`;
            const shareData = {
                title: user.name + ' - Card Identity',
                url: shareUrl
            };

            try {
                if (navigator.share) {
                    await navigator.share(shareData);
                } else {
                    await this.copyToClipboard(shareUrl);
                    alert('Share link copied to clipboard!');
                }
            } catch (err) {
                console.error('Error sharing', err);
            }
        },

        /**
         * Copy text to clipboard
         * @param {string} text 
         * @returns {Promise<void>}
         */
        async copyToClipboard(text) {
            if (!text) return;
            try {
                await navigator.clipboard.writeText(text);
            } catch (err) {
                console.error('Failed to copy', err);
            }
        },

        /**
         * @param {string} field 
         */
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
                        if (!/^[\d\s\+\-\(\)]{7,20}$/.test(this.newUser.phone)) {
                            this.errors.phone = 'Invalid phone format';
                        } else {
                            delete this.errors.phone;
                        }
                    } else {
                        delete this.errors.phone;
                    }
                    break;
                case 'terms':
                    if (!this.terms) {
                        this.errors.terms = 'You must agree to the terms and conditions';
                    } else {
                        delete this.errors.terms;
                    }
                    break;
            }
        },

        // API Methods
        async handleSearch() {
            this.loading = true;
            this.page = 1;
            try {
                /** @type {SDKResponse} */
                // @ts-ignore
                const res = await getUsers({ city: this.searchQuery || undefined, page: 1, limit: 20 });
                if (res.status === 200 && res.data) {
                    const data = /** @type {UserResource[]} */ (res.data.data || []);
                    this.users = data.map(u => ({ id: u.id, ...u.attributes }));
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
                /** @type {SDKResponse} */
                // @ts-ignore
                const res = await getUsers({
                    city: this.searchQuery || undefined,
                    page: this.page + 1,
                    limit: 20
                });
                if (res.status === 200 && res.data) {
                    const data = /** @type {UserResource[]} */ (res.data.data || []);
                    const newUsers = data.map(u => ({ id: u.id, ...u.attributes }));
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

            ['name', 'email', 'city', 'phone', 'terms'].forEach(f => this.validateField(f));
            if (Object.keys(this.errors).length > 0) return;

            this.submitting = true;
            try {
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

                /** @type {SDKResponse} */
                // @ts-ignore
                const res = await createUser(payload);

                if (res.status === 201 && res.data && res.data.data) {
                    const u = /** @type {UserResource} */ (res.data.data);
                    const newUserRecord = { id: u.id, ...u.attributes };

                    this.users.unshift(newUserRecord);
                    this.selectedContact = newUserRecord;
                    this.contactModalOpen = true; // Open newly created contact view

                    // Reset
                    this.newUser = { name: '', email: '', city: '', phone: '' };
                    this.terms = false;
                    this.errors = {};
                    this.closeForm();

                    // Generate share QR for new contact
                    const shareUrl = `${window.location.protocol}//${window.location.host}/api/v1/users/${u.id}`;
                    setTimeout(() => this.generateQRCode(shareUrl), 100);

                } else if (res.status === 422) {
                    if (res.data && res.data.errors) {
                        this.errors = {};
                        /** @type {any[]} */ (res.data.errors).forEach(e => {
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

    // Generate initial QR code if contact is pre-selected (standalone card view)
    if (appState.selectedContact) {
        const shareUrl = `${window.location.protocol}//${window.location.host}/${appState.selectedContact.id}`;
        setTimeout(() => appState.generateQRCode(shareUrl), 100);
    }

    window.addEventListener('resize', () => {
        appState.isMobile = window.innerWidth < 1024;
    });

    setTimeout(() => appState.initInfiniteScroll(), 100);
};

// Auto-initialize if data is pre-defined on window
// @ts-ignore
if (window.APP_INITIAL_DATA) {
    // @ts-ignore
    window.initApp(window.APP_INITIAL_DATA);
}