import { request } from '../app/api.js';

/**
 * Password recovery
 * @param {string} email - Email address
 * @returns {Promise<object>} - Password recovery result
 */
async function forgotPassword(email) {
    const res = await request('/v1/auth/password/recovery', {
        method: 'POST',
        body: JSON.stringify({ email })
    });
    const j = await res.json();

    if (!res.ok) {
        const error = new Error(j?.message || 'Password recovery failed');
        error.status = res.status;
        error.details = j?.details?.validation_errors || j?.errors || null;
        throw error;
    }

    return j.data;
}

/**
 * Validate forgot password form
 * @param {Object} formData - Data of the form {email}
 * @returns {Object} - {isValid: boolean, errors: Object}
 */
function validateForgotPasswordForm(formData) {
    const errors = {};

    if (!window.ValidationUtils.isNotEmpty(formData.email)) {
        errors.email = 'Email address is required';
    } else if (!window.ValidationUtils.isValidEmail(formData.email)) {
        errors.email = 'Please enter a valid email address';
    }

    return {
        isValid: Object.keys(errors).length === 0,
        errors
    };
}

/**
 * Handles errors for the forgot password form
 * @param {Error} error - Error object
 * @returns {string} - Error message for the user
 */
function handleForgotPasswordError(error) {
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
                return error.message || 'Please check your email address and try again.';
            }

        case 404: // Not Found - email not found
            return 'No account found with this email address.';

        case 429: // Too Many Requests - rate limiting
            return 'Too many password reset requests. Please wait a few minutes before trying again.';

        case 500: // Internal Server Error
            return 'Server error occurred. Please try again later.';

        default:
            return error.message || 'Failed to send password reset email. Please try again.';
    }
}


/**
 * Handles the forgot password form
 */
document.addEventListener('alpine:init', () => {
    window.Alpine.data('forgotPasswordForm', () => ({
        email: '',
        form_error: '',
        form_success: '',
        loading: false,

        async submitForm() {
            if (this.loading) return;

            // Client-side validation
            const validation = validateForgotPasswordForm({ email: this.email });
            if (!validation.isValid) {
                this.form_error = Object.values(validation.errors)[0];
                return;
            }

            this.loading = true;
            this.form_error = '';
            this.form_success = '';

            try {
                const result = await forgotPassword(this.email);
                this.form_success = 'Password reset code has been sent to your email address. Please check your inbox and enter the code on the password reset page.';

                // Clear form after successful submission
                window.FormHelperUtils.clearFormFields(['email']);

                // Redirect to password reset page after showing success message
                setTimeout(() => {
                    window.location.href = '/password-reset';
                }, 3000);

            } catch (error) {
                this.form_error = handleForgotPasswordError(error);
            } finally {
                this.loading = false;
            }
        }
    }));
});