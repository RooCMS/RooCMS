/**
 * Universal formatting utilities
 */

/**
 * Formats timestamp to date
 * @param {number|string} timestamp - Unix timestamp or date string
 * @returns {string} - Formatted date
 */
export function formatDate(timestamp) {
    if (!timestamp) return 'Not set';

    const date = new Date(typeof timestamp === 'string' ? timestamp : timestamp * 1000);
    if (isNaN(date.getTime())) return 'Invalid date';

    return date.toLocaleDateString();
}

/**
 * Formats timestamp to date and time
 * @param {number|string|Date} timestamp - Unix timestamp, date string, or Date object
 * @returns {string} - Formatted date and time
 */
export function formatDateTime(timestamp) {
    if (!timestamp) return 'Not set';

    let date;
    if (timestamp instanceof Date) {
        date = timestamp;
    } else {
        date = new Date(typeof timestamp === 'string' ? timestamp : timestamp * 1000);
    }

    if (isNaN(date.getTime())) return 'Invalid date';

    return date.toLocaleString('ru-RU');
}

/**
 * Formats timestamp to time only (HH:MM:SS)
 * @param {number|string|Date} timestamp - Unix timestamp, date string, or Date object
 * @returns {string} - Formatted time only
 */
export function formatTimeOnly(timestamp) {
    if (!timestamp) return '';

    let date;
    if (timestamp instanceof Date) {
        date = timestamp;
    } else {
        date = new Date(typeof timestamp === 'string' ? timestamp : timestamp * 1000);
    }

    if (isNaN(date.getTime())) return '';

    return date.toLocaleTimeString('ru-RU');
}


/**
 * Truncates text to the specified length
 * @param {string} text - Original text
 * @param {number} maxLength - Maximum length
 * @param {string} suffix - Suffix for truncated text (default '...')
 * @returns {string} - Truncated text
 */
export function truncateText(text, maxLength, suffix = '...') {
    if (!text || text.length <= maxLength) return text;
    return text.substring(0, maxLength - suffix.length) + suffix;
}

/**
 * Converts the first letter to uppercase
 * @param {string} str - String to convert
 * @returns {string} - String with the first letter uppercase
 */
export function capitalizeFirst(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
}
