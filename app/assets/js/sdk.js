;
export const getGetApiV1UsersUrl = (params) => {
    const normalizedParams = new URLSearchParams();
    Object.entries(params || {}).forEach(([key, value]) => {
        if (value !== undefined) {
            normalizedParams.append(key, value === null ? 'null' : value.toString());
        }
    });
    const stringifiedParams = normalizedParams.toString();
    return stringifiedParams.length > 0 ? `/api/v1/users?${stringifiedParams}` : `/api/v1/users`;
};
export const getApiV1Users = async (params, options) => {
    const res = await fetch(getGetApiV1UsersUrl(params), {
        ...options,
        method: 'GET'
    });
    const body = [204, 205, 304].includes(res.status) ? null : await res.text();
    const data = body ? JSON.parse(body) : {};
    return { data, status: res.status, headers: res.headers };
};
export const getPostApiV1UsersUrl = () => {
    return `/api/v1/users`;
};
export const postApiV1Users = async (userCreateRequest, options) => {
    const res = await fetch(getPostApiV1UsersUrl(), {
        ...options,
        method: 'POST',
        headers: { 'Content-Type': 'application/vnd.api+json', ...options?.headers },
        body: JSON.stringify(userCreateRequest)
    });
    const body = [204, 205, 304].includes(res.status) ? null : await res.text();
    const data = body ? JSON.parse(body) : {};
    return { data, status: res.status, headers: res.headers };
};
export const getGetApiV1UsersIdUrl = (id) => {
    return `/api/v1/users/${id}`;
};
export const getApiV1UsersId = async (id, options) => {
    const res = await fetch(getGetApiV1UsersIdUrl(id), {
        ...options,
        method: 'GET'
    });
    const body = [204, 205, 304].includes(res.status) ? null : await res.text();
    const data = body ? JSON.parse(body) : {};
    return { data, status: res.status, headers: res.headers };
};
;
export const getGetApiV1UsersCitiesUrl = () => {
    return `/api/v1/users/cities`;
};
export const getApiV1UsersCities = async (options) => {
    const res = await fetch(getGetApiV1UsersCitiesUrl(), {
        ...options,
        method: 'GET'
    });
    const body = [204, 205, 304].includes(res.status) ? null : await res.text();
    const data = body ? JSON.parse(body) : {};
    return { data, status: res.status, headers: res.headers };
};
//# sourceMappingURL=sdk.js.map