/**
 * Alpine.js Debug Logs Manager Component
 * Handles displaying and managing debug logs
 */

import { request } from '../app/api.js';
import { DEBUG } from '../app/config.js';

// Alpine.js Debug Logs Manager Component
document.addEventListener('alpine:init', () => {
    Alpine.data('debugLogsManager', (initialLogs) => ({
    // Reactive data
    logs: initialLogs || [],
    expandedIndex: null,
    _searchQuery: '',
    loading: false,

    // Initialization
    init() {
        if (DEBUG) console.log('Initializing Alpine debug logs manager with', this.logs.length, 'logs');
        
        // Validate logs data
        if (!Array.isArray(this.logs)) {
            console.warn('Debug logs data is not an array, initializing empty array');
            this.logs = [];
        }
    },

    // Computed property for filtered logs
    get filteredLogs() {
        let filtered = [...this.logs];

        // Apply search filter
        if (this._searchQuery && this._searchQuery.trim()) {
            const query = this._searchQuery.toLowerCase().trim();
            filtered = filtered.filter(log => {
                // Search in timestamp, performance data, dumps, environment
                return this.formatTimestamp(log.timestamp).toLowerCase().includes(query) ||
                       String(this.safeGet(log, 'debug.info.performance.execution_time')).includes(query) ||
                       String(this.safeGet(log, 'debug.info.performance.memory_usage')).includes(query) ||
                       String(this.safeGet(log, 'debug.info.performance.db_queries')).includes(query) ||
                       this.searchInDumps(log, query) ||
                       this.searchInEnvironment(log, query);
            });
        }

        return filtered;
    },

    // Getters/Setters for reactive properties
    get searchQuery() { return this._searchQuery; },
    set searchQuery(value) { this._searchQuery = value || ''; },

    // Toggle expanded log
    toggleExpanded(index) {
        this.expandedIndex = this.expandedIndex === index ? null : index;
    },

    // Universal safe property access
    safeGet(obj, path) {
        try {
            return path.split('.').reduce((current, key) => current && current[key], obj) || null;
        } catch (e) {
            return null;
        }
    },

    // Check if property exists and has value
    safeHas(obj, path) {
        const value = this.safeGet(obj, path);
        return value !== null && value !== undefined;
    },

    // Search helpers
    searchInDumps(log, query) {
        const dumps = this.safeGet(log, 'debug.info.dumps') || [];
        return dumps.some(dump => 
            (dump.label && dump.label.toLowerCase().includes(query)) ||
            (dump.type && dump.type.toLowerCase().includes(query)) ||
            JSON.stringify(dump.value || dump).toLowerCase().includes(query)
        );
    },

    searchInEnvironment(log, query) {
        const version = this.safeGet(log, 'debug.info.environment.php.checks.version.value');
        const missing = this.safeGet(log, 'debug.info.environment.php.checks.extensions.missing') || [];
        return (version && version.toLowerCase().includes(query)) ||
               missing.some(ext => ext.toLowerCase().includes(query));
    },

    // Format timestamp
    formatTimestamp(timestamp) {
        if (!timestamp) return 'Unknown';
        try {
            // Check if timestamp is string without using typeof
            const date = (timestamp && timestamp.constructor === String) ? new Date(timestamp) : new Date(timestamp * 1000);
            return date.toLocaleString('ru-RU', {
                year: 'numeric', month: '2-digit', day: '2-digit',
                hour: '2-digit', minute: '2-digit', second: '2-digit'
            });
        } catch (e) {
            return 'Invalid date';
        }
    },

    // Get status color based on performance
    getStatusColor(log) {
        const perf = this.safeGet(log, 'debug.info.performance') || {};
        const execTime = perf.execution_time || 0;
        const memory = perf.memory_usage || 0;
        
        if (execTime > 3 || memory > 50) return 'bg-red-500';
        if (execTime > 1.5 || memory > 20) return 'bg-yellow-500';
        return 'bg-green-500';
    },

    // Get performance status text
    getPerformanceStatus(value, type) {
        if (type === 'time') {
            if (value < 0.5) return 'Excellent';
            if (value < 1) return 'Good';
            if (value < 2) return 'Slow';
            return 'Very slow';
        }
        if (type === 'memory') {
            if (value < 5) return 'Low';
            if (value < 10) return 'Moderate';
            if (value < 20) return 'High';
            return 'Very high';
        }
        return '';
    },

    // Universal value formatter
    formatValue(value) {
        if (value === null) return 'null';
        if (value === undefined) return 'undefined';
        
        if (value && value.constructor === String) return `"${value}"`;
        if (value && (value.constructor === Number || value.constructor === Boolean)) return String(value);
        if (Array.isArray(value)) return `Array(${value.length})`;
        if (value && value.constructor === Function) return `function ${value.name || 'anonymous'}()`;
        if (value && value.constructor === Object) return JSON.stringify(value, null, 2);
        
        return String(value);
    },

    // Format dump value for display
    formatDumpValue(dump) {
        const value = dump.simple_dump ? dump.value : 
                     (dump.analysis && dump.analysis.value !== undefined) ? dump.analysis.value :
                     dump.value !== undefined ? dump.value : 'No value available';
        return this.formatValue(value);
    },

    // Get dump type for display
    getDumpType(dump) {
        if (dump.analysis && dump.analysis.class) return dump.analysis.class;
        if (dump.type) return dump.type;
        if (dump.value === null) return 'null';
        if (dump.value !== undefined) {
            if (Array.isArray(dump.value)) return `Array[${dump.value.length}]`;
            if (dump.value && dump.value.constructor === Object) return 'Object';
            if (dump.value && dump.value.constructor === String) return 'string';
            if (dump.value && dump.value.constructor === Number) return 'number';
            if (dump.value && dump.value.constructor === Boolean) return 'boolean';
            if (dump.value && dump.value.constructor === Function) return 'function';
        }
        return 'Unknown';
    },

    // Get type badge color
    getDumpTypeColor(dump) {
        const type = this.getDumpType(dump).toLowerCase();
        const colors = {
            'string': 'bg-green-100 text-green-800',
            'number': 'bg-blue-100 text-blue-800',
            'boolean': 'bg-yellow-100 text-yellow-800',
            'null': 'bg-gray-100 text-gray-800',
            'object': 'bg-orange-100 text-orange-800',
            'function': 'bg-pink-100 text-pink-800'
        };
        return type.includes('array') ? 'bg-purple-100 text-purple-800' : 
               colors[type] || 'bg-indigo-100 text-indigo-800';
    },

    // Dump analysis helpers (consolidated)
    getDumpAnalysis(dump, property) { return this.safeGet(dump, `analysis.${property}`); },
    hasDumpAnalysis(dump, property) { 
        const value = this.getDumpAnalysis(dump, property);
        return Array.isArray(value) ? value.length > 0 : value !== null && value !== undefined;
    },
    hasDetailedAnalysis(dump) { return dump.analysis && (dump.analysis.class || dump.analysis.methods || dump.analysis.properties); },
    getDumpMethods(dump) { return this.getDumpAnalysis(dump, 'methods') || []; },
    getDumpProperties(dump) { return this.getDumpAnalysis(dump, 'properties') || {}; },
    getDumpTraits(dump) { return this.getDumpAnalysis(dump, 'traits') || {}; },
    getDumpInterfaces(dump) { return this.getDumpAnalysis(dump, 'interfaces') || []; },
    getDumpParent(dump) { return this.getDumpAnalysis(dump, 'parent'); },
    getDumpInfo(dump) { return this.getDumpAnalysis(dump, 'info'); },

    // Object helpers for CSP compatibility
    getObjectKeys(obj) {
        if (!obj || obj.constructor !== Object) return [];
        const keys = [];
        for (const key in obj) if (obj.hasOwnProperty(key)) keys.push(key);
        return keys;
    },
    getObjectValues(obj) {
        if (!obj || obj.constructor !== Object) return [];
        const values = [];
        for (const key in obj) if (obj.hasOwnProperty(key)) values.push(obj[key]);
        return values;
    },
    getDumpTraitValues(dump) { return this.getObjectValues(this.getDumpTraits(dump)); },
    formatPropertyValue(value) {
        if (value === null) return 'null';
        if (value === undefined) return 'undefined';
        return (value && (value.constructor === Object || Array.isArray(value))) ? JSON.stringify(value) : String(value);
    },

    // Unified data access helpers
    getPerformanceValue(log, property) { return this.safeGet(log, `debug.info.performance.${property}`) || 0; },
    hasPerformanceValue(log, property) { return this.safeHas(log, `debug.info.performance.${property}`); },
    getEnvironmentValue(log, path) { return this.safeGet(log, `debug.info.environment.${path}`); },
    hasEnvironmentValue(log, path) { return this.safeHas(log, `debug.info.environment.${path}`); },
    getFilesystemValue(log, property) { return this.safeGet(log, `debug.info.filesystem.${property}`); },
    hasFilesystemValue(log, property) { return this.safeHas(log, `debug.info.filesystem.${property}`); },
    getUserEnvValue(log, property) { return this.safeGet(log, `debug.info.environment.user_environment.${property}`); },
    hasUserEnvValue(log, property) { return this.safeHas(log, `debug.info.environment.user_environment.${property}`); },
    getServerLoadValue(log, period) { return this.safeGet(log, `debug.info.server_load.${period}`); },
    hasServerLoad(log) { return this.safeHas(log, 'debug.info.server_load'); },
    getProcessId(log) { return this.safeGet(log, 'debug.info.process_id'); },
    hasProcessId(log) { return this.safeHas(log, 'debug.info.process_id'); },
    getQueryStatsValue(log, property) { return this.safeGet(log, `debug.info.performance.query_stats.${property}`); },
    hasQueryStats(log) { return this.safeHas(log, 'debug.info.performance.query_stats'); },
    hasQueryStatsQueries(log) { 
        const queries = this.safeGet(log, 'debug.info.performance.query_stats.queries');
        return Array.isArray(queries) && queries.length > 0;
    },
    hasDumps(log) {
        const dumps = this.safeGet(log, 'debug.info.dumps');
        return Array.isArray(dumps) && dumps.length > 0;
    },
    hasInitiator(log) {
        return this.safeHas(log, 'debug.info.initiator') || this.safeHas(log, 'debug.initiator') || this.safeHas(log, 'initiator');
    },
    getInitiatorValue(log, property) {
        return this.safeGet(log, `debug.info.initiator.${property}`) ||
               this.safeGet(log, `debug.initiator.${property}`) ||
               this.safeGet(log, `initiator.${property}`);
    },
    hasInitiatorValue(log, property) {
        return this.safeHas(log, `debug.info.initiator.${property}`) ||
               this.safeHas(log, `debug.initiator.${property}`) ||
               this.safeHas(log, `initiator.${property}`);
    },

    // Clear all logs
    async clearLogs() {
        const confirmed = await window.modal(
            'Очистить логи?',
            'Вы действительно хотите очистить все debug логи? Это действие нельзя отменить.',
            'Очистить',
            'Отмена',
            'warning'
        );
        
        if (!confirmed) return;
        if (this.loading) return;

        this.loading = true;
        try {
            const response = await request('/v1/admin/debug/clear', { method: 'POST' });
            if (!response.ok) throw new Error(`Failed to clear logs: ${response.status}`);
            
            this.logs = [];
            this.expandedIndex = null;
            
            await window.modal(
                'Успешно!',
                'Debug логи успешно очищены.',
                'OK',
                '',
                'success'
            );
        } catch (error) {
            if (DEBUG) console.error('Error clearing logs:', error);
            await window.modal(
                'Ошибка',
                'Произошла ошибка при очистке логов: ' + error.message,
                'OK',
                '',
                'alert'
            );
        } finally {
            this.loading = false;
        }
    },

    }));
});