/**
 * ACP Dashboard functionality
 *
 * Handles system status display and data loading for the admin control panel dashboard.
 */
import { request } from './api.js';

/**
 * Get system health details from API
 * @returns {Promise<Object|null>} Health details data or null on error
 */
async function getHealthDetails() {
    try {
        const res = await request('/v1/health/details', {
            method: 'GET'
        });

        if (!res.ok) {
            console.error('Failed to fetch health details:', res.status, res.statusText);
            return null;
        }

        const data = await res.json();
        return data?.data || data;
    } catch (error) {
        console.error('Error fetching health details:', error);
        return null;
    }
}

// Create Alpine component for system status
document.addEventListener('alpine:init', () => {
    window.Alpine.data('systemStatus', () => ({
        loading: true,
        lastUpdated: null,
        healthData: null,
        error: null,
        countdown: 15,

        // Helper methods for safe data access
        getApiCheck() {
            return this.healthData?.['api check'];
        },

        getDatabaseCheck() {
            return this.healthData?.['database check'];
        },

        getSystemInfo() {
            return this.healthData?.system_info;
        },

        getPhpInfo() {
            return this.healthData?.php_info;
        },

        getRoocmsInfo() {
            return this.healthData?.roocms_info;
        },

        // Computed properties for status
        get apiStatus() {
            const check = this.getApiCheck();
            return check?.status === 'ok' ? 'ok' : 'error';
        },

        get databaseStatus() {
            const check = this.getDatabaseCheck();
            const status = check?.status;
            return (status === 'ok' || status === 'healthy') ? 'ok' : 'error';
        },

        get apiResponseTime() {
            const check = this.getApiCheck();
            if (!check?.response_time) return '0ms';
            return (check.response_time * 1000).toFixed(0) + 'ms';
        },

        get memoryUsage() {
            const memory = this.getSystemInfo()?.memory_usage;
            if (!memory?.current) return '0MB';
            return (memory.current / 1024 / 1024).toFixed(1) + 'MB';
        },

        get memoryLimit() {
            const memory = this.getSystemInfo()?.memory_usage;
            const limit = memory?.limit || '0M';
            return limit.replace('M', 'MB');
        },

        get phpVersion() {
            const version = this.getPhpInfo()?.version;
            return version ? 'PHP ' + version : 'Unknown';
        },

        get maxExecutionTime() {
            const config = this.getPhpInfo()?.configuration;
            return config?.max_execution_time ? config.max_execution_time + 's' : '30s';
        },

        get timezone() {
            return this.getSystemInfo()?.timezone || 'UTC';
        },

        get roocmsVersion() {
            return this.getRoocmsInfo()?.version || 'Unknown';
        },

        async init() {
            await this.loadHealthData();
            this.startCountdown();
            this.startAutoRefresh();
        },

        async loadHealthData() {
            this.loading = true;
            this.error = null;

            try {
                const data = await getHealthDetails();
                if (data) {
                    this.healthData = data;
                    this.lastUpdated = new Date();
                    this.countdown = 15; // Reset countdown
                } else {
                    this.error = 'Not able to load system health data';
                }
            } catch (error) {
                console.error('Error loading system health data:', error);
                this.error = 'Error loading system health data';
            } finally {
                this.loading = false;
            }
        },

        // Format time only
        formatTimeOnly(timestamp) {
            if (!timestamp) return '';
            return window.FormatterUtils.formatTimeOnly(timestamp);
        },

        // Start countdown timer
        startCountdown() {
            setInterval(() => {
                if (this.countdown > 0) {
                    this.countdown--;
                }
            }, 1000);
        },

        // Auto-refresh every countdown seconds
        startAutoRefresh() {
            setInterval(() => {
                this.loadHealthData();
            }, this.countdown * 1000);
        }
    }));
});
