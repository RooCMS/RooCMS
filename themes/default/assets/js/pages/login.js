import { login } from '../app/auth.js';

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
                this.form_error = window.ErrorHandlerUtils.handleLoginError(error);
            } finally {
                this.loading = false;
            }
        }
    }));
});


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