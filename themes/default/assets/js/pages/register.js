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
            const validation = validateRegisterForm({
                login: this.login,
                email: this.email,
                password: this.password,
                password_confirmation: this.password_confirmation
            });

            if (!validation.isValid) {
                // Clear previous errors
                window.FormHelperUtils.clearFieldErrors(['login-error', 'email-error', 'password-error', 'password-confirmation-error']);

                // Show new errors
                Object.keys(validation.errors).forEach(field => {
                    window.FormHelperUtils.showFieldError(`${field}-error`, validation.errors[field]);
                });
                return;
            }

            this.loading = true;
            this.form_error = '';
            this.form_success = '';

            try {
                const result = await register(this.login, this.email, this.password, this.password_confirmation);
                this.form_success = 'Account created successfully! You can now log in.';

                // Clear form after successful submission
                window.FormHelperUtils.clearFormFields(['login', 'email', 'password', 'password_confirmation']);

                // Redirect to login page
                window.FormHelperUtils.redirectAfterSuccess('/login');

            } catch (error) {
                const { formError, fieldErrors } = handleRegisterError(error);

                if (formError) {
                    this.form_error = formError;
                }

                if (fieldErrors) {
                    Object.keys(fieldErrors).forEach(field => {
                        window.FormHelperUtils.showFieldError(`${field}-error`, fieldErrors[field]);
                    });
                }
            } finally {
                this.loading = false;
            }
        }
    }));
});


/**
 * Validate register form
 * @param {Object} formData - Data of the form {login, email, password, password_confirmation}
 * @returns {Object} - {isValid: boolean, errors: Object}
 */
function validateRegisterForm(formData) {
    const errors = {};

    if (!window.ValidationUtils.isNotEmpty(formData.login)) {
        errors.login = 'Login is required';
    } else if (!window.ValidationUtils.hasMinLength(formData.login, 3)) {
        errors.login = 'Login must be at least 3 characters';
    }

    if (!window.ValidationUtils.isNotEmpty(formData.email)) {
        errors.email = 'Email is required';
    } else if (!window.ValidationUtils.isValidEmail(formData.email)) {
        errors.email = 'Please enter a valid email address';
    }

    if (!window.ValidationUtils.isNotEmpty(formData.password)) {
        errors.password = 'Password is required';
    } else if (!window.ValidationUtils.hasMinLength(formData.password, 8)) {
        errors.password = 'Password must be at least 8 characters';
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
 * Handles errors for the registration form
 * @param {Error} error - Error object
 * @returns {Object} - {formError: string, fieldErrors: Object}
 */
function handleRegisterError(error) {
    const fieldErrors = {};
    let formError = '';

    // Handle different error types based on HTTP status
    switch (error.status) {
        case 409: // Conflict - user already exists
            if (error.message?.includes('Login already exists')) {
                fieldErrors.login = 'This login is already taken. Please choose another one.';
            } else if (error.message?.includes('Email already exists')) {
                fieldErrors.email = 'This email is already registered. Please use another email or try to login.';
            } else {
                formError = 'Account with these credentials already exists.';
            }
            break;

        case 400: // Bad Request
        case 422: // Unprocessable Entity - validation errors
            if (error.details) {
                // Show field-specific validation errors
                Object.keys(error.details).forEach(field => {
                    fieldErrors[field] = error.details[field];
                });
            } else {
                formError = error.message || 'Please check your input data and try again.';
            }
            break;

        case 500: // Internal Server Error
            formError = 'Server error occurred. Please try again later.';
            break;

        default:
            formError = error.message || 'Registration failed. Please try again.';
            break;
    }

    return { formError, fieldErrors };
}