// @ts-check

/**
 * @typedef {Object} GetUsersParams
 * @property {string} [city]
 * @property {number} [page]
 * @property {number} [limit]
 * @property {boolean} [reveal]
 */

/**
 * @typedef {Object} CreateUserRequest
 * @property {Object} data
 * @property {string} data.type
 * @property {Object} data.attributes
 * @property {string} data.attributes.name
 * @property {string} data.attributes.email
 * @property {string} data.attributes.city
 * @property {string} [data.attributes.phone]
 */

/**
 * @typedef {Object} SDKResponse
 * @property {any} data
 * @property {number} status
 * @property {Headers} headers
 */

// eslint-disable-next-line @typescript-eslint/no-redeclare
export const UserResourceType = {
    users: 'users',
};
// eslint-disable-next-line @typescript-eslint/no-redeclare
export const UserCreateRequestDataType = {
    users: 'users',
};

/**
 * @param {GetUsersParams} [params]
 * @returns {string}
 */
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

/**
 * @param {GetUsersParams} [params]
 * @param {RequestInit} [options]
 * @returns {Promise<SDKResponse>}
 */
export const getUsers = async (params, options) => {
    const res = await fetch(getGetUsersUrl(params), {
        ...options,
        method: 'GET'
    });
    const body = [204, 205, 304].includes(res.status) ? null : await res.text();
    const data = body ? JSON.parse(body) : {};
    return { data, status: res.status, headers: res.headers };
};

/**
 * @returns {string}
 */
export const getCreateUserUrl = () => {
    return `/api/v1/users`;
};

/**
 * @param {CreateUserRequest} userCreateRequest
 * @param {RequestInit} [options]
 * @returns {Promise<SDKResponse>}
 */
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

/**
 * @param {string|number} id
 * @param {Object} [params]
 * @property {boolean} [params.reveal]
 * @returns {string}
 */
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

/**
 * @param {string|number} id
 * @param {Object} [params]
 * @property {boolean} [params.reveal]
 * @param {RequestInit} [options]
 * @returns {Promise<SDKResponse>}
 */
export const getUser = async (id, params, options) => {
    const res = await fetch(getGetUserUrl(id, params), {
        ...options,
        method: 'GET'
    });
    const body = [204, 205, 304].includes(res.status) ? null : await res.text();
    const data = body ? JSON.parse(body) : {};
    return { data, status: res.status, headers: res.headers };
};