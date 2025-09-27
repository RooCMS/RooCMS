import { request } from '../app/api.js';

/**
 * Verify email
 * @param {string} verification_code - Verification code
 * @returns {Promise<object>} - Verification result
 */
async function verifyEmail(verification_code) {
    const res = await request(`/v1/users/verify-email/${encodeURIComponent(verification_code)}`, {
        method: 'GET'
    });
    const j = await res.json();

    if (!res.ok) {
        const error = new Error(j?.message || 'Email verification failed');
        error.status = res.status;
        error.details = j?.details?.validation_errors || j?.errors || null;
        throw error;
    }

    return j.data;
}

/**
 * Validate verify email form
 * @param {Object} formData - Data of the form {verification_code}
 * @returns {Object} - {isValid: boolean, errors: Object}
 */
function validateVerifyEmailForm(formData) {
    const errors = {};

    if (!window.ValidationUtils.isNotEmpty(formData.verification_code)) {
        errors.verification_code = 'Verification code is required';
    }

    return {
        isValid: Object.keys(errors).length === 0,
        errors
    };
}

/**
 * Handles errors for the verify email form
 * @param {Error} error - Error object
 * @returns {string} - Error message for the user
 */
function handleVerifyEmailError(error) {
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
                return error.message || 'Please check your verification code and try again.';
            }

        case 401: // Unauthorized - invalid code
            return 'This verification code is invalid or has expired. Please request a new verification email.';

        case 403: // Forbidden - code expired or used
            return 'This verification code has expired. Please request a new verification email.';

        case 404: // Not Found - code not found
            return 'This verification code is invalid. Please request a new verification email.';

        case 429: // Too Many Requests - rate limiting
            return 'Too many verification attempts. Please wait a few minutes before trying again.';

        case 500: // Internal Server Error
            return 'Server error occurred. Please try again later.';

        default:
            return error.message || 'Failed to verify email. Please try again.';
    }
}

/**
 * Handles the verify email form
 */
document.addEventListener('alpine:init', () => {
    window.Alpine.data('verifyEmailForm', () => ({
        verification_code: '',
        form_error: '',
        form_success: '',
        loading: false,
        auto_verified: false,
        isAuthenticated: false,

        init() {
            // Check authentication status
            this.isAuthenticated = !!localStorage.getItem('access_token');

            // Check if we have an auto-verification code from URL
            if (window.autoVerificationCode) {
                this.verification_code = window.autoVerificationCode;
                // Delay auto-submission to ensure Alpine is fully initialized
                this.$nextTick(() => {
                    this.submitForm();
                });
            }
        },

        async submitForm() {
            if (this.loading) return;

            // Client-side validation
            const validation = validateVerifyEmailForm({ verification_code: this.verification_code });
            if (!validation.isValid) {
                this.form_error = Object.values(validation.errors)[0];
                return;
            }

            this.loading = true;
            this.form_error = '';
            this.form_success = '';

            try {
                const result = await verifyEmail(this.verification_code.trim());
                this.form_success = 'Email verified successfully! Welcome to RooCMS.';

                // Clear form after successful submission
                window.FormHelperUtils.clearFormFields(['verification_code']);

                // Determine redirect destination based on authentication status
                const accessToken = localStorage.getItem('access_token');
                const redirectUrl = accessToken ? '/profile' : '/login';

                // Redirect after showing success message
                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 2000);

            } catch (error) {
                this.form_error = handleVerifyEmailError(error);
            } finally {
                this.loading = false;
            }
        }
    }));
});
