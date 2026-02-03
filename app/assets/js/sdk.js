// @ts-check
const HEADERS = {
    'Content-Type': 'application/vnd.api+json',
    'Accept': 'application/vnd.api+json'
};

export const UserSDK = {
    async fetchUsers(params = {}) {
        // Handle backward compatibility if params is just a string (city)
        if (typeof params === 'string') {
            params = { city: params };
        }

        const query = new URLSearchParams();
        if (params.city) query.append('city', params.city);
        if (params.page) query.append('page', params.page);
        if (params.limit) query.append('limit', params.limit);

        const url = `/api/v1/users?${query.toString()}`;
        const res = await fetch(url, { headers: HEADERS });
        if (!res.ok) throw await res.json();

        const json = await res.json();
        const users = json.data.map(u => ({
            id: u.id,
            name: u.attributes.name,
            email: u.attributes.email,
            city: u.attributes.city,
            phone: u.attributes.phone
        }));

        return { users, meta: json.meta };
    },

    async createUser(attributes) {
        const res = await fetch('/api/v1/users', {
            method: 'POST',
            headers: HEADERS,
            body: JSON.stringify({
                data: { type: 'users', attributes }
            })
        });

        const json = await res.json();
        if (!res.ok) throw json;

        return {
            id: json.data.id,
            ...json.data.attributes
        };
    },

    async fetchUser(id) {
        const res = await fetch(`/api/v1/users/${id}`, { headers: HEADERS });
        if (!res.ok) throw await res.json();
        const json = await res.json();
        return {
            id: json.data.id,
            ...json.data.attributes
        };
    }
};