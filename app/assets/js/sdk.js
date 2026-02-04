// eslint-disable-next-line @typescript-eslint/no-redeclare
export const UserResourceType = {
    users: 'users',
};
// eslint-disable-next-line @typescript-eslint/no-redeclare
export const UserCreateRequestDataType = {
    users: 'users',
};
;
export const getGetUsersUrl = (params) => {
    const normalizedParams = new URLSearchParams();
    Object.entries(params || {}).forEach(([key, value]) => {
        if (value !== undefined) {
            normalizedParams.append(key, value === null ? 'null' : value.toString());
        }
    });
    const stringifiedParams = normalizedParams.toString();
    return stringifiedParams.length > 0 ? `/api/v1/users?${stringifiedParams}` : `/api/v1/users`;
};
export const getUsers = async (params, options) => {
    const res = await fetch(getGetUsersUrl(params), {
        ...options,
        method: 'GET'
    });
    const body = [204, 205, 304].includes(res.status) ? null : await res.text();
    const data = body ? JSON.parse(body) : {};
    return { data, status: res.status, headers: res.headers };
};
export const getCreateUserUrl = () => {
    return `/api/v1/users`;
};
export const createUser = async (userCreateRequest, options) => {
    const res = await fetch(getCreateUserUrl(), {
        ...options,
        method: 'POST',
        headers: { 'Content-Type': 'application/vnd.api+json', ...options?.headers },
        body: JSON.stringify(userCreateRequest)
    });
    const body = [204, 205, 304].includes(res.status) ? null : await res.text();
    const data = body ? JSON.parse(body) : {};
    return { data, status: res.status, headers: res.headers };
};
;
export const getGetUserUrl = (id, params) => {
    const normalizedParams = new URLSearchParams();
    Object.entries(params || {}).forEach(([key, value]) => {
        if (value !== undefined) {
            normalizedParams.append(key, value === null ? 'null' : value.toString());
        }
    });
    const stringifiedParams = normalizedParams.toString();
    return stringifiedParams.length > 0 ? `/api/v1/users/${id}?${stringifiedParams}` : `/api/v1/users/${id}`;
};
export const getUser = async (id, params, options) => {
    const res = await fetch(getGetUserUrl(id, params), {
        ...options,
        method: 'GET'
    });
    const body = [204, 205, 304].includes(res.status) ? null : await res.text();
    const data = body ? JSON.parse(body) : {};
    return { data, status: res.status, headers: res.headers };
};
//# sourceMappingURL=sdk.js.map