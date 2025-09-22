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
            if (!this.validateForm()) {
                return;
            }

            this.loading = true;
            this.form_error = '';
            this.form_success = '';

            try {
                const result = await login(this.login, this.password);
                this.form_success = 'Login successful! Redirecting...';

                // Update auth status globally
                if (window.Alpine && window.Alpine.store) {
                    window.Alpine.store('auth').updateStatus();
                }

                // Update all authButtons components
                if (window.Alpine && window.Alpine.all) {
                    window.Alpine.all().forEach(component => {
                        if (component.isAuth !== undefined) {
                            component.checkAuth();
                        }
                    });
                }

                // Redirect after successful login
                setTimeout(() => {
                    window.location.href = '/profile';
                }, 1500);

            } catch (error) {
                this.handleLoginError(error);
            } finally {
                this.loading = false;
            }
        },

        validateForm() {
            // Clear previous error
            this.form_error = '';

            let isValid = true;

            // Login validation
            if (!this.login.trim()) {
                this.form_error = 'Login or email is required';
                isValid = false;
            }

            // Password validation
            if (!this.password) {
                this.form_error = 'Password is required';
                isValid = false;
            }

            return isValid;
        },

        handleLoginError(error) {
            // Clear form-level error first
            this.form_error = '';

            // Handle different error types based on HTTP status
            switch (error.status) {
                case 401: // Unauthorized - invalid credentials
                    this.form_error = 'Invalid login credentials. Please check your login/email and password.';
                    break;

                case 403: // Forbidden - account issues
                    if (error.message.includes('not verified')) {
                        this.form_error = 'Your account is not verified. Please check your email for verification link.';
                    } else if (error.message.includes('banned')) {
                        this.form_error = 'Your account has been banned. Please contact support.';
                    } else {
                        this.form_error = 'Access denied. Please contact support.';
                    }
                    break;

                case 400: // Bad Request - validation errors
                case 422: // Unprocessable Entity - validation errors
                    if (error.details) {
                        // Show specific validation errors from server
                        if (typeof error.details === 'object') {
                            const messages = Object.values(error.details).flat();
                            this.form_error = messages.join('. ') + '.';
                        } else {
                            this.form_error = error.details;
                        }
                    } else {
                        this.form_error = error.message || 'Please check your input data and try again.';
                    }
                    break;

                case 429: // Too Many Requests - rate limiting
                    this.form_error = 'Too many login attempts. Please wait a few minutes before trying again.';
                    break;

                case 500: // Internal Server Error
                    this.form_error = 'Server error occurred. Please try again later.';
                    break;

                default:
                    this.form_error = error.message || 'Login failed. Please try again.';
                    break;
            }
        }
    }));
});
