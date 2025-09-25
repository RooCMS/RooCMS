/**
 * Universal form helper utilities
 */

/**
 * Clears field errors
 * @param {Array} errorElementIds - Array of IDs of elements with errors
 */
export function clearFieldErrors(errorElementIds) {
    errorElementIds.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = '';
            element.classList.add('hidden');
        }
    });
}

/**
 * Shows an error for a field
 * @param {string} elementId - ID of the element with an error
 * @param {string} message - Error message
 */
export function showFieldError(elementId, message) {
    const element = document.getElementById(elementId);
    if (element) {
        element.textContent = message;
        element.classList.remove('hidden');
    }
}

/**
 * Clears the form
 * @param {Array} fieldNames - Array of field names to clear
 */
export function clearFormFields(fieldNames) {
    fieldNames.forEach(fieldName => {
        const element = document.querySelector(`[x-model="${fieldName}"]`);
        if (element && element._x_model) {
            element._x_model.set('', {}); // Alpine.js way
        }
    });
}

/**
 * Sets the loading state of the form
 * @param {boolean} loading - Loading state
 * @param {string} submitButtonSelector - Selector of the submit button
 */
export function setFormLoading(loading, submitButtonSelector = 'button[type="submit"]') {
    const submitButton = document.querySelector(submitButtonSelector);
    if (submitButton) {
        submitButton.disabled = loading;
        submitButton.textContent = loading ? 'Loading...' : 'Submit';
    }
}

/**
 * Shows a success message
 * @param {string} message - Success message
 * @param {string} selector - Selector of the element for the message
 */
export function showSuccessMessage(message, selector = '.form-success') {
    const element = document.querySelector(selector);
    if (element) {
        element.textContent = message;
        element.classList.remove('hidden');
        element.classList.add('text-green-600');

        // Hide after 5 seconds
        setTimeout(() => {
            element.classList.add('hidden');
        }, 5000);
    }
}

/**
 * Shows an error message
 * @param {string} message - Error message
 * @param {string} selector - Selector of the element for the message
 */
export function showErrorMessage(message, selector = '.form-error') {
    const element = document.querySelector(selector);
    if (element) {
        element.textContent = message;
        element.classList.remove('hidden');
        element.classList.add('text-red-600');

        // Hide after 5 seconds
        setTimeout(() => {
            element.classList.add('hidden');
        }, 5000);
    }
}

/**
 * Clears all form messages
 * @param {string} successSelector - Selector of the element of success
 * @param {string} errorSelector - Selector of the element of error
 */
export function clearFormMessages(successSelector = '.form-success', errorSelector = '.form-error') {
    const successEl = document.querySelector(successSelector);
    const errorEl = document.querySelector(errorSelector);

    if (successEl) {
        successEl.classList.add('hidden');
    }
    if (errorEl) {
        errorEl.classList.add('hidden');
    }
}

/**
 * Automatically hides messages after a specified time
 * @param {number} timeout - Time in milliseconds (default 5000)
 */
export function autoHideMessages(timeout = 5000) {
    setTimeout(() => {
        clearFormMessages();
    }, timeout);
}

/**
 * Redirects the user after a successful operation
 * @param {string} url - URL for redirection
 * @param {number} delay - Delay in milliseconds
 */
export function redirectAfterSuccess(url, delay = 1500) {
    setTimeout(() => {
        window.location.href = url;
    }, delay);
}
