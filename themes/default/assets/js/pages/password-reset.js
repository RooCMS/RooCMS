import { resetPassword } from '../app/auth.js';

document.addEventListener('alpine:init', () => {
    window.Alpine.data('resetPasswordForm', () => ({
        password: '',
        password_confirmation: '',
        form_error: '',
        form_success: '',
        loading: false,
        token: '',

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
                const result = await resetPassword(this.token.trim(), this.password.trim(), this.password_confirmation.trim());
                this.form_success = 'Password has been reset successfully! You can now log in with your new password.';

                // Clear form after successful submission
                this.clearForm();

                // Redirect to login page after success
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);

            } catch (error) {
                this.handleResetPasswordError(error);
            } finally {
                this.loading = false;
            }
        },

        validateForm() {
            // Clear previous errors
            this.form_error = '';

            let isValid = true;

            // Token validation
            if (!this.token.trim()) {
                this.form_error = 'Reset code is required';
                isValid = false;
            }

            // Password validation
            if (!this.password.trim()) {
                this.form_error = 'New password is required';
                isValid = false;
            } else if (this.password.trim().length < 8) {
                this.form_error = 'New password must be at least 8 characters';
                isValid = false;
            }

            // Password confirmation validation
            if (!this.password_confirmation.trim()) {
                this.form_error = 'Password confirmation is required';
                isValid = false;
            } else if (this.password.trim() !== this.password_confirmation.trim()) {
                this.form_error = 'Passwords do not match';
                isValid = false;
            }

            return isValid;
        },

        clearForm() {
            this.token = '';
            this.password = '';
            this.password_confirmation = '';
        },

        handleResetPasswordError(error) {
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
                        this.form_error = error.message || 'Please check your input data and try again.';
                    }
                    break;

                case 401: // Unauthorized - invalid token
                    this.form_error = 'This reset code is invalid or has expired. Please request a new password reset.';
                    break;

                case 403: // Forbidden - token expired or used
                    this.form_error = 'This reset code has expired. Please request a new password reset.';
                    break;

                case 404: // Not Found - token not found
                    this.form_error = 'This reset code is invalid. Please request a new password reset.';
                    break;

                case 429: // Too Many Requests - rate limiting
                    this.form_error = 'Too many password reset attempts. Please wait a few minutes before trying again.';
                    break;

                case 500: // Internal Server Error
                    this.form_error = 'Server error occurred. Please try again later.';
                    break;

                default:
                    this.form_error = error.message || 'Failed to reset password. Please try again.';
                    break;
            }
        }
    }));
});
