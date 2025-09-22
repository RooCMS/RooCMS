import { register } from '../app/auth.js';

document.addEventListener('alpine:init', () => {
    window.Alpine.data('registerForm', () => ({
        login: '',
        email: '',
        password: '',
        password_confirmation: '',
        form_error: '',
        form_success: '',
        loading: false,

        async submitForm() {
            if (this.loading) return;

            // Client-side validation
            if (!this.validateForm()) {
                return;
            }

            this.loading = true;
            this.form_error = '';
            this.form_success = '';

            try {
                const result = await register(this.login, this.email, this.password, this.password_confirmation);
                this.form_success = 'Account created successfully! You can now log in.';
                this.clearForm();

                // Redirect to registration complete page
                setTimeout(() => {
                    window.location.href = '/login';
                }, 1500);

            } catch (error) {
                this.handleRegistrationError(error);
            } finally {
                this.loading = false;
            }
        },

        validateForm() {
            // Clear previous errors
            this.clearErrors();

            let isValid = true;

            // Login validation
            if (!this.login.trim()) {
                this.showError('login-error', 'Login is required');
                isValid = false;
            } else if (this.login.length < 3) {
                this.showError('login-error', 'Login must be at least 3 characters');
                isValid = false;
            }

            // Email validation
            if (!this.email.trim()) {
                this.showError('email-error', 'Email is required');
                isValid = false;
            } else if (!this.isValidEmail(this.email)) {
                this.showError('email-error', 'Please enter a valid email address');
                isValid = false;
            }

            // Password validation
            if (!this.password) {
                this.showError('password-error', 'Password is required');
                isValid = false;
            } else if (this.password.length < 8) {
                this.showError('password-error', 'Password must be at least 8 characters');
                isValid = false;
            }

            // Password confirmation validation
            if (!this.password_confirmation) {
                this.showError('password-confirmation-error', 'Password confirmation is required');
                isValid = false;
            } else if (this.password !== this.password_confirmation) {
                this.showError('password-confirmation-error', 'Passwords do not match');
                isValid = false;
            }

            return isValid;
        },

        isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        },

        showError(elementId, message) {
            const element = document.getElementById(elementId);
            if (element) {
                element.textContent = message;
                element.classList.remove('hidden');
            }
        },

        clearErrors() {
            const errorElements = ['login-error', 'email-error', 'password-error', 'password-confirmation-error'];
            errorElements.forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = '';
                    element.classList.add('hidden');
                }
            });
        },

        clearForm() {
            this.login = '';
            this.email = '';
            this.password = '';
            this.password_confirmation = '';
        },

        handleRegistrationError(error) {
            // Clear previous field errors
            this.clearErrors();

            // Clear form-level error first
            this.form_error = '';

            // Handle different error types based on HTTP status
            switch (error.status) {
                case 409: // Conflict - user already exists
                    if (error.message.includes('Login already exists')) {
                        this.showError('login-error', 'This login is already taken. Please choose another one.');
                    } else if (error.message.includes('Email already exists')) {
                        this.showError('email-error', 'This email is already registered. Please use another email or try to login.');
                    } else {
                        this.form_error = 'Account with these credentials already exists.';
                    }
                    break;

                case 400: // Bad Request
                case 422: // Unprocessable Entity - validation errors
                    if (error.details) {
                        // Show field-specific validation errors
                        Object.keys(error.details).forEach(field => {
                            const elementId = `${field}-error`;
                            this.showError(elementId, error.details[field]);
                        });
                    } else {
                        this.form_error = error.message || 'Please check your input data and try again.';
                    }
                    break;

                case 500: // Internal Server Error
                    this.form_error = 'Server error occurred. Please try again later.';
                    break;

                default:
                    this.form_error = error.message || 'Registration failed. Please try again.';
                    break;
            }
        }
    }));
});