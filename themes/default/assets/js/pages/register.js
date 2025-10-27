import { register } from '../app/auth.js';

/**
 * Handles the register form with Alpine.js
 */
document.addEventListener('alpine:init', () => {
    window.Alpine.data('registerForm', () => ({
        login: '',
        email: '',
        password: '',
        password_confirmation: '',
        form_error: '',
        form_success: '',
        loading: false,

        /**
         * Basic client-side validation for better UX
         * @returns {boolean} - Is form valid
         */
        validateBasic() {
            // Clear previous errors
            window.FormHelperUtils.clearFieldErrors(['login-error', 'email-error', 'password-error', 'password-confirmation-error']);
            
            if (!this.login?.trim()) {
                window.FormHelperUtils.showFieldError('login-error', 'Login is required');
                return false;
            }
            if (this.login.length < 3) {
                window.FormHelperUtils.showFieldError('login-error', 'Login must be at least 3 characters');
                return false;
            }
            if (!this.email?.trim()) {
                window.FormHelperUtils.showFieldError('email-error', 'Email is required');
                return false;
            }
            if (!this.password) {
                window.FormHelperUtils.showFieldError('password-error', 'Password is required');
                return false;
            }
            if (this.password.length < 6) {
                window.FormHelperUtils.showFieldError('password-error', 'Password must be at least 6 characters');
                return false;
            }
            if (this.password !== this.password_confirmation) {
                window.FormHelperUtils.showFieldError('password-confirmation-error', 'Passwords do not match');
                return false;
            }
            
            return true;
        },

        /**
         * Handle API errors
         * @param {Error} error - Error object from API
         */
        handleError(error) {
            // Field-specific errors from server
            if (error.details) {
                Object.keys(error.details).forEach(field => {
                    window.FormHelperUtils.showFieldError(`${field}-error`, error.details[field]);
                });
            } 
            // Conflict errors (409) - user already exists
            else if (error.status === 409) {
                if (error.message?.includes('Login')) {
                    window.FormHelperUtils.showFieldError('login-error', 'This login is already taken');
                } else if (error.message?.includes('Email')) {
                    window.FormHelperUtils.showFieldError('email-error', 'This email is already registered');
                } else {
                    this.form_error = 'Account already exists';
                }
            }
            // Generic error
            else {
                this.form_error = error.message || 'Registration failed. Please try again.';
            }
        },

        /**
         * Submit registration form
         */
        async submitForm() {
            if (this.loading) return;

            // Quick validation for better UX
            if (!this.validateBasic()) return;

            this.loading = true;
            this.form_error = '';
            this.form_success = '';

            try {
                await register(this.login, this.email, this.password, this.password_confirmation);
                this.form_success = 'Account created successfully!';
                
                // Clear form fields
                window.FormHelperUtils.clearFormFields(['login', 'email', 'password', 'password_confirmation']);
                
                // Redirect to login page
                window.FormHelperUtils.redirectAfterSuccess('/!/login');

            } catch (error) {
                this.handleError(error);
            } finally {
                this.loading = false;
            }
        }
    }));
});