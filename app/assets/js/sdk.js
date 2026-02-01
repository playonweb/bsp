const HEADERS = {
    'Content-Type': 'application/vnd.api+json',
    'Accept': 'application/vnd.api+json'
};

export const UserSDK = {
    async fetchUsers(city = '') {
        const url = city ? `/api/v1/users?city=${encodeURIComponent(city)}` : '/api/v1/users';
        const res = await fetch(url, { headers: HEADERS });
        if (!res.ok) throw await res.json();
        
        const json = await res.json();
        return json.data.map(u => ({
            id: u.id,
            name: u.attributes.name,
            email: u.attributes.email,
            city: u.attributes.city,
            phone: u.attributes.phone
        }));
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
    }
};