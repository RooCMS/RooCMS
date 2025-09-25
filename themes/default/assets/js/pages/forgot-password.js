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
                this.form_error = window.ErrorHandlerUtils.handleForgotPasswordError(error);
            } finally {
                this.loading = false;
            }
        }
    }));
});


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
