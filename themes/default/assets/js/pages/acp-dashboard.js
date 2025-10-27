/**
 * ACP Dashboard functionality
 *
 * Handles system status display and data loading for the admin control panel dashboard.
 */
import { request } from '../app/api.js';

// Auto-refresh interval in seconds
const REFRESH_INTERVAL = 15;

/**
 * Get system health details from API
 * @returns {Promise<Object>} Health details data
 */
async function getHealthDetails() {
    const res = await request('/v1/health/details', { method: 'GET' });
    const json = await res.json();

    if (!res.ok) {
        throw new Error(json.message || 'Failed to fetch health details');
    }

    return json.data || json;
}

// Create Alpine component for system status
document.addEventListener('alpine:init', () => {
    window.Alpine.data('systemStatus', () => ({
        loading: true,
        lastUpdated: null,
        healthData: null,
        error: null,
        countdown: REFRESH_INTERVAL,
        countdownTimer: null,
        refreshTimer: null,

        // Computed properties for status
        get apiStatus() {
            const status = this.healthData?.['api check']?.status;
            return status === 'ok' ? 'ok' : 'error';
        },

        get databaseStatus() {
            const status = this.healthData?.['database check']?.status;
            return (status === 'ok' || status === 'healthy') ? 'ok' : 'error';
        },

        get apiResponseTime() {
            const responseTime = this.healthData?.['api check']?.response_time;
            return responseTime ? `${(responseTime * 1000).toFixed(0)}ms` : '0ms';
        },

        get memoryUsage() {
            const current = this.healthData?.system_info?.memory_usage?.current;
            return current ? `${(current / 1024 / 1024).toFixed(1)}MB` : '0MB';
        },

        get memoryLimit() {
            const limit = this.healthData?.system_info?.memory_usage?.limit || '0M';
            return limit.replace('M', 'MB');
        },

        get phpVersion() {
            const version = this.healthData?.php_info?.version;
            return version ? `PHP ${version}` : 'Unknown';
        },

        get maxExecutionTime() {
            const time = this.healthData?.php_info?.configuration?.max_execution_time;
            return time ? `${time}s` : '30s';
        },

        get timezone() {
            return this.healthData?.system_info?.timezone || 'UTC';
        },

        get roocmsVersion() {
            return this.healthData?.roocms_info?.version || 'Unknown';
        },

        get usersCount() {
            return this.healthData?.['users in system'] || 0;
        },

        async init() {
            await this.loadHealthData();
            this.startAutoRefresh();
        },

        async loadHealthData() {
            this.loading = true;
            this.error = null;

            try {
                this.healthData = await getHealthDetails();
                this.lastUpdated = new Date();
                this.resetCountdown();
            } catch (error) {
                window.log('error', 'Error loading system health data:', error);
                this.error = error.message || 'Error loading system health data';
            } finally {
                this.loading = false;
            }
        },

        // Format time only
        formatTimeOnly(timestamp) {
            return timestamp ? window.FormatterUtils.formatTimeOnly(timestamp) : '';
        },

        // Reset countdown to initial value
        resetCountdown() {
            this.countdown = REFRESH_INTERVAL;
        },

        // Start auto-refresh with countdown
        startAutoRefresh() {
            // Clear existing timers if any
            this.stopAutoRefresh();

            // Countdown timer (updates every second)
            this.countdownTimer = setInterval(() => {
                if (this.countdown > 0) {
                    this.countdown--;
                } else {
                    // When countdown reaches 0, reload data
                    this.loadHealthData();
                }
            }, 1000);
        },

        // Stop auto-refresh timers
        stopAutoRefresh() {
            if (this.countdownTimer) {
                clearInterval(this.countdownTimer);
                this.countdownTimer = null;
            }
            if (this.refreshTimer) {
                clearInterval(this.refreshTimer);
                this.refreshTimer = null;
            }
        },

        // Cleanup on component destroy
        destroy() {
            this.stopAutoRefresh();
        }
    }));
});
