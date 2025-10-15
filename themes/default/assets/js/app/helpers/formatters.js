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

/**
 * Formats timestamp to relative time (e.g., "2 hours ago")
 * @param {number} timestamp - Unix timestamp in seconds
 * @returns {string} - Relative time string
 */
export function formatRelativeTime(timestamp) {
    if (!timestamp) return 'Never';

    const date = new Date(timestamp * 1000);
    const now = new Date();
    const diffMs = now - date;
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
    const diffMinutes = Math.floor(diffMs / (1000 * 60));

    if (diffDays > 0) {
        return diffDays === 1 ? '1 day ago' : `${diffDays} days ago`;
    } else if (diffHours > 0) {
        return diffHours === 1 ? '1 hour ago' : `${diffHours} hours ago`;
    } else if (diffMinutes > 0) {
        return diffMinutes === 1 ? '1 minute ago' : `${diffMinutes} minutes ago`;
    } else {
        return 'Just now';
    }
}

/**
 * Gets initials from user data
 * @param {object} user - User object with first_name, last_name, login
 * @returns {string} - User initials (2 chars)
 */
export function getUserInitials(user) {
    const firstName = user.first_name || '';
    const lastName = user.last_name || '';
    const login = user.login || '';

    if (firstName && lastName) {
        return (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();
    } else if (firstName) {
        return firstName.substring(0, 2).toUpperCase();
    } else if (lastName) {
        return lastName.substring(0, 2).toUpperCase();
    } else {
        return login.substring(0, 2).toUpperCase();
    }
}