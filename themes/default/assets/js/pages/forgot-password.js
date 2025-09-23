import { forgotPassword } from '../app/auth.js';

document.addEventListener('alpine:init', () => {
    window.Alpine.data('forgotPasswordForm', () => ({
        email: '',
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
                const result = await forgotPassword(this.email);
                this.form_success = 'Password reset code has been sent to your email address. Please check your inbox and enter the code on the password reset page.';

                // Clear form after successful submission
                this.email = '';

                // Redirect to password reset page after showing success message
                setTimeout(() => {
                    window.location.href = '/password-reset';
                }, 3000);

            } catch (error) {
                this.handleForgotPasswordError(error);
            } finally {
                this.loading = false;
            }
        },

        validateForm() {
            // Clear previous error
            this.form_error = '';

            let isValid = true;

            // Email validation
            if (!this.email.trim()) {
                this.form_error = 'Email address is required';
                isValid = false;
            } else if (!this.isValidEmail(this.email)) {
                this.form_error = 'Please enter a valid email address';
                isValid = false;
            }

            return isValid;
        },

        isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        },

        handleForgotPasswordError(error) {
            // Clear form-level error first
            this.form_error = '';

            // Handle different error types based on HTTP status
            switch (error.status) {
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
                        this.form_error = error.message || 'Please check your email address and try again.';
                    }
                    break;

                case 404: // Not Found - email not found
                    this.form_error = 'No account found with this email address.';
                    break;

                case 429: // Too Many Requests - rate limiting
                    this.form_error = 'Too many password reset requests. Please wait a few minutes before trying again.';
                    break;

                case 500: // Internal Server Error
                    this.form_error = 'Server error occurred. Please try again later.';
                    break;

                default:
                    this.form_error = error.message || 'Failed to send password reset email. Please try again.';
                    break;
            }
        }
    }));
});
