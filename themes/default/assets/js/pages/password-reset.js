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
            const validation = validateResetPasswordForm({
                token: this.token,
                password: this.password,
                password_confirmation: this.password_confirmation
            });

            if (!validation.isValid) {
                this.form_error = Object.values(validation.errors)[0];
                return;
            }

            this.loading = true;
            this.form_error = '';
            this.form_success = '';

            try {
                const result = await resetPassword(this.token.trim(), this.password.trim(), this.password_confirmation.trim());
                this.form_success = 'Password has been reset successfully! You can now log in with your new password.';

                // Clear form after successful submission
                window.FormHelperUtils.clearFormFields(['token', 'password', 'password_confirmation']);

                // Redirect to login page after success
                window.FormHelperUtils.redirectAfterSuccess('/login', 2000);

            } catch (error) {
                this.form_error = handleResetPasswordError(error);
            } finally {
                this.loading = false;
            }
        }
    }));
});


/**
 * Validate reset password form
 * @param {Object} formData - Data of the form {token, password, password_confirmation}
 * @returns {Object} - {isValid: boolean, errors: Object}
 */
function validateResetPasswordForm(formData) {
    const errors = {};

    if (!window.ValidationUtils.isNotEmpty(formData.token)) {
        errors.token = 'Reset code is required';
    }

    if (!window.ValidationUtils.isNotEmpty(formData.password)) {
        errors.password = 'New password is required';
    } else if (!window.ValidationUtils.hasMinLength(formData.password, 8)) {
        errors.password = 'New password must be at least 8 characters';
    }

    if (!window.ValidationUtils.isNotEmpty(formData.password_confirmation)) {
        errors.password_confirmation = 'Password confirmation is required';
    } else if (!window.ValidationUtils.valuesMatch(formData.password, formData.password_confirmation)) {
        errors.password_confirmation = 'Passwords do not match';
    }

    return {
        isValid: Object.keys(errors).length === 0,
        errors
    };
}

/**
 * Handles errors for the reset password form
 * @param {Error} error - Error object
 * @returns {string} - Error message for the user
 */
function handleResetPasswordError(error) {
    // Handle different error types based on HTTP status
    switch (error.status) {
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

        case 401: // Unauthorized - invalid token
            return 'This reset code is invalid or has expired. Please request a new password reset.';

        case 403: // Forbidden - token expired or used
            return 'This reset code has expired. Please request a new password reset.';

        case 404: // Not Found - token not found
            return 'This reset code is invalid. Please request a new password reset.';

        case 429: // Too Many Requests - rate limiting
            return 'Too many password reset attempts. Please wait a few minutes before trying again.';

        case 500: // Internal Server Error
            return 'Server error occurred. Please try again later.';

        default:
            return error.message || 'Failed to reset password. Please try again.';
    }
}
