import { login } from '../app/auth.js';

/**
 * Validate login form
 * @param {Object} formData - Data of the form {login, password}
 * @returns {Object} - {isValid: boolean, errors: Object}
 */
function validateLoginForm(formData) {
    const errors = {};

    if (!window.ValidationUtils.isNotEmpty(formData.login)) {
        errors.login = 'Login or email is required';
    }

    if (!window.ValidationUtils.isNotEmpty(formData.password)) {
        errors.password = 'Password is required';
    }

    return {
        isValid: Object.keys(errors).length === 0,
        errors
    };
}

/**
 * Updates the authentication status in the global storage
 */
function updateAuthStatus() {
    if (window.Alpine && window.Alpine.store && window.Alpine.store('auth')) {
        window.Alpine.store('auth').updateStatus();
    }
}

/**
 * Updates the state of all authentication components
 */
function updateAuthComponents() {
    if (window.Alpine && window.Alpine.all) {
        window.Alpine.all().forEach(component => {
            if (component.isAuth !== undefined) {
                component.checkAuth();
            }
        });
    }
}

/**
 * Handles errors for the login form
 * @param {Error} error - Error object
 * @returns {string} - Error message for the user
 */
function handleLoginError(error) {
    // Handle different error types based on HTTP status
    switch (error.status) {
        case 401: // Unauthorized - invalid credentials
            return 'Invalid login credentials. Please check your login/email and password.';

        case 403: // Forbidden - account issues
            if (error.message?.includes('not verified')) {
                return 'Your account is not verified. Please check your email for verification link.';
            } else if (error.message?.includes('banned')) {
                return 'Your account has been banned. Please contact support.';
            } else {
                return 'Access denied. Please contact support.';
            }

        case 400: // Bad Request - validation errors
        case 422: // Unprocessable Entity - validation errors
            if (error.details) {
                // Show specific validation errors from server
                if (typeof error.details === 'object') {
                    const messages = Object.values(error.details).flat();
                    return messages.join('. ') + '.';
                } else {
                    return error.details;
                }
            } else {
                return error.message || 'Please check your input data and try again.';
            }

        case 429: // Too Many Requests - rate limiting
            return 'Too many login attempts. Please wait a few minutes before trying again.';

        case 500: // Internal Server Error
            return 'Server error occurred. Please try again later.';

        default:
            return error.message || 'Login failed. Please try again.';
    }
}


/**
 * Handles the login form
 */
document.addEventListener('alpine:init', () => {
    window.Alpine.data('loginForm', () => ({
        login: '',
        password: '',
        form_error: '',
        form_success: '',
        loading: false,

        async submitForm() {
            if (this.loading) return;

            // Client-side validation
            const validation = validateLoginForm({ login: this.login, password: this.password });
            if (!validation.isValid) {
                this.form_error = Object.values(validation.errors)[0];
                return;
            }

            this.loading = true;
            this.form_error = '';
            this.form_success = '';

            try {
                const result = await login(this.login, this.password);
                this.form_success = 'Login successful! Redirecting...';

                // Update auth status globally
                updateAuthStatus();
                updateAuthComponents();

                // Redirect after successful login
                window.FormHelperUtils.redirectAfterSuccess('/profile');

            } catch (error) {
                this.form_error = handleLoginError(error);
            } finally {
                this.loading = false;
            }
        }
    }));
});