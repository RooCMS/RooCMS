/**
 * Universal error handler utilities for forms
 */

/**
 * Handles errors for the login form
 * @param {Error} error - Error object
 * @returns {string} - Error message for the user
 */
export function handleLoginError(error) {
    // Handle different error types based on HTTP status
    switch (error.status) {
        case 401: // Unauthorized - invalid credentials
            return 'Invalid login credentials. Please check your login/email and password.';

        case 403: // Forbidden - account issues
            if (error.message?.includes('not verified')) {
                return 'Your account is not verified. Please check your email for verification link.';
            } else if (error.message?.includes('banned')) {
                return 'Your account has been banned. Please contact support.';
            } else {
                return 'Access denied. Please contact support.';
            }

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

        case 429: // Too Many Requests - rate limiting
            return 'Too many login attempts. Please wait a few minutes before trying again.';

        case 500: // Internal Server Error
            return 'Server error occurred. Please try again later.';

        default:
            return error.message || 'Login failed. Please try again.';
    }
}

/**
 * Handles errors for the registration form
 * @param {Error} error - Error object
 * @returns {Object} - {formError: string, fieldErrors: Object}
 */
export function handleRegisterError(error) {
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

/**
 * Handles errors for the forgot password form
 * @param {Error} error - Error object
 * @returns {string} - Error message for the user
 */
export function handleForgotPasswordError(error) {
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
 * Handles errors for the reset password form
 * @param {Error} error - Error object
 * @returns {string} - Error message for the user
 */
export function handleResetPasswordError(error) {
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
