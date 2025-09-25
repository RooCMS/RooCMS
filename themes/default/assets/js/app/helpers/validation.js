/**
 * Universal validation utilities for forms
 */

/**
 * Checks if the email address is correct
 * @param {string} email - Email to check
 * @returns {boolean} - true if email is correct
 */
export function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Checks if the string has the minimum length
 * @param {string} value - Value to check
 * @param {number} minLength - Minimum length
 * @returns {boolean} - true if the length is sufficient
 */
export function hasMinLength(value, minLength) {
    return value && value.trim().length >= minLength;
}

/**
 * Checks if the strings match
 * @param {string} value1 - First value
 * @param {string} value2 - Second value
 * @returns {boolean} - true if the strings match
 */
export function valuesMatch(value1, value2) {
    return value1 && value2 && value1.trim() === value2.trim();
}

/**
 * Checks if the value is not empty
 * @param {string} value - Value to check
 * @returns {boolean} - true if the value is not empty
 */
export function isNotEmpty(value) {
    return value && value.trim().length > 0;
}
