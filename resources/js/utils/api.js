/**
 * Get JWT token from localStorage
 * The token is stored when user logs in via the auth API
 */
export function getAuthToken() {
    return localStorage.getItem('auth_token');
}

/**
 * Set JWT token to localStorage
 * Called after successful login
 */
export function setAuthToken(token) {
    localStorage.setItem('auth_token', token);
}

/**
 * Remove JWT token from localStorage
 * Called on logout
 */
export function removeAuthToken() {
    localStorage.removeItem('auth_token');
}

/**
 * Get request headers with authentication
 * Includes Content-Type and Authorization header if token exists
 */
export function getApiHeaders() {
    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
    };

    const token = getAuthToken();
    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    return headers;
}

/**
 * Make an API request with proper headers and error handling
 * @param {string} url - The API endpoint
 * @param {object} options - Fetch options (method, body, etc.)
 * @returns {Promise<Response>}
 */
export async function apiCall(url, options = {}) {
    const method = options.method || 'GET';
    
    const response = await fetch(url, {
        ...options,
        method,
        headers: getApiHeaders(),
    });

    if (!response.ok) {
        const contentType = response.headers.get('content-type');
        let message = `API error: ${response.status} ${response.statusText}`;

        if (contentType && contentType.includes('application/json')) {
            const data = await response.json();
            message = data.message || data.error || message;
        }

        // Check if it's an authentication error
        if (response.status === 401 || response.status === 403) {
            removeAuthToken();
            // Optionally redirect to login
            // window.location.href = '/login';
            throw new Error(message || `Authentication error: ${response.status}`);
        }
        throw new Error(message);
    }

    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
        throw new Error('Invalid response type: expected JSON');
    }

    return response;
}

/**
 * Make a GET request to API
 */
export async function apiGet(url) {
    const response = await apiCall(url, { method: 'GET' });
    return response.json();
}

/**
 * Make a POST request to API
 */
export async function apiPost(url, data) {
    const response = await apiCall(url, {
        method: 'POST',
        body: JSON.stringify(data),
    });
    return response.json();
}

/**
 * Make a PUT request to API
 */
export async function apiPut(url, data) {
    const response = await apiCall(url, {
        method: 'PUT',
        body: JSON.stringify(data),
    });
    return response.json();
}

/**
 * Make a DELETE request to API
 */
export async function apiDelete(url) {
    const response = await apiCall(url, { method: 'DELETE' });
    return response.json();
}
