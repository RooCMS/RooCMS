<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

// Read debug logs
$debug_logs = [];
$debug_log_file = _LOGS . '/debug.log';

if (is_file($debug_log_file) && is_readable($debug_log_file)) {
    // Use read_file without locking to avoid blocking log writes
    // The parsing algorithm below is robust enough to handle partial reads
    $log_content = read_file($debug_log_file);
    
    if ($log_content !== false && !empty($log_content)) {
        // Convert the sequence of JSON objects into a JSON array
        // Normalize line endings for cross-platform compatibility (Windows/Unix)
        $normalized_content = str_replace(["\r\n", "\r"], "\n", $log_content);
        $normalized_content = trim($normalized_content);
        
        // Remove trailing comma and newline if present
        $normalized_content = rtrim($normalized_content, ",\n");
        
        // Split content by lines to process each JSON object separately
        // This is more robust than trying to parse the entire file as one JSON array
        $lines = explode("\n", $normalized_content);
        $current_json = '';
        $brace_count = 0;
        $valid_entries = [];
        $max_entries = 100; // Limit to last 100 entries for performance
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            $current_json .= $line;
            
            // Count braces to determine when we have a complete JSON object
            $brace_count += substr_count($line, '{') - substr_count($line, '}');
            
            // When brace count reaches 0, we should have a complete JSON object
            if ($brace_count === 0 && !empty($current_json)) {
                // Remove trailing comma if present
                $current_json = rtrim($current_json, ',');
                
                // Try to decode this JSON object
                $entry = json_decode($current_json, true);
                if ($entry !== null && is_array($entry)) {
                    $valid_entries[] = $entry;
                    
                    // Keep only the last N entries for performance
                    if (count($valid_entries) > $max_entries) {
                        array_shift($valid_entries);
                    }
                }
                
                // Reset for next object
                $current_json = '';
                $brace_count = 0;
            }
        }
        
            // Filter entries that have debug data
        foreach ($valid_entries as $entry) {
            if (isset($entry['debug']) && is_array($entry['debug'])) {
                    $debug_logs[] = $entry;
            }
        }

        // Sort by timestamp (newest first)
        if (!empty($debug_logs)) {
        usort($debug_logs, function($a, $b) {
            $time_a = $a['timestamp'] ?? '';
            $time_b = $b['timestamp'] ?? '';

            // Convert timestamps to comparable values
            $time_a_val = is_numeric($time_a) ? (float)$time_a : strtotime($time_a);
            $time_b_val = is_numeric($time_b) ? (float)$time_b : strtotime($time_b);

            return $time_b_val <=> $time_a_val;
        });
        }
    }
}

$page_title = 'Debug — RooCMS';
$page_description = 'System Debug logs for RooCMS';

$theme_name = basename(dirname(dirname(__DIR__)));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [
	$theme_base.'/assets/js/app/acp.js',
    $theme_base.'/assets/js/app/acp-access.js',
    $theme_base.'/assets/js/pages/acp-debug.js'
];

ob_start();
?>

<div class="py-10">
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-[260px_1fr] px-4 sm:px-6 lg:px-8 space-y-8 max-w-full">
        <?php require __DIR__ . '/../../layouts/acp-nav.php'; ?>

        <section class="min-w-0 overflow-hidden">
            <header class="mb-8">
                <nav class="mb-3 text-sm text-zinc-500" aria-label="Breadcrumbs">
                    <ol class="flex items-center gap-2">
                        <li><a href="/" class="hover:text-zinc-700">Home</a></li>
                        <li aria-hidden="true" class="text-zinc-400">/</li>
                        <li><a href="/acp" class="hover:text-zinc-700">ACP</a></li>
                        <li aria-hidden="true" class="text-zinc-400">/</li>
                        <li><span class="text-zinc-700">Debug Logs</span></li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Debug Logs</h1>
                <p class="mt-2 text-sm text-zinc-600">System debug information and performance metrics</p>
            </header>

            <div class="space-y-6" x-data="debugLogsManager(<?= htmlspecialchars(json_encode($debug_logs), ENT_QUOTES, 'UTF-8') ?>)">

                <!-- Controls -->
                <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
                    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-zinc-700">Search:</span>
                                <input type="text"
                                       x-model="searchQuery"
                                       placeholder="Search logs..."
                                       class="rounded-lg border border-zinc-300 bg-white px-3 py-1.5 text-sm focus:border-sky-500 focus:outline-none min-w-48">
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-sm text-zinc-500" x-text="'Showing ' + filteredLogs.length + ' of ' + logs.length + ' logs'"></span>
                            <button type="button"
                                    @click="clearLogs()"
                                    :disabled="logs.length === 0"
                                    class="inline-flex items-center justify-center rounded-lg border border-red-300 bg-red-50 px-3 py-1.5 text-sm font-medium text-red-700 hover:bg-red-100 disabled:opacity-50 transition-colors cursor-pointer">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Clear logs
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Logs List -->
                <div class="space-y-4">
                    <template x-for="(log, index) in filteredLogs" :key="index">
                        <div class="rounded-xl border border-zinc-200/80 bg-white/80 shadow-sm backdrop-blur overflow-hidden max-w-full"
                             :class="{ 'ring-2 ring-sky-500 ring-opacity-50': expandedIndex === index }">

                            <!-- Log Header -->
                            <div class="p-5 cursor-pointer" @click="toggleExpanded(index)">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded-full"
                                                 :class="getStatusColor(log)">
                                            </div>
                                            <span class="text-sm font-medium text-zinc-900" x-text="formatTimestamp(log.timestamp)"></span>
                                        </div>

                                        <div class="flex items-center gap-4 text-sm text-zinc-600">
                                            <div class="flex items-center gap-1" x-show="hasPerformanceValue(log, 'execution_time')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span x-text="(getPerformanceValue(log, 'execution_time') || 0) + 's'"></span>
                                            </div>

                                            <div class="flex items-center gap-1" x-show="hasPerformanceValue(log, 'memory_usage')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"></path>
                                                </svg>
                                                <span x-text="(getPerformanceValue(log, 'memory_usage') || 0) + 'MB'"></span>
                                            </div>

                                            <div class="flex items-center gap-1" x-show="hasPerformanceValue(log, 'db_queries')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-5.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                                </svg>
                                                <span x-text="(getPerformanceValue(log, 'db_queries') || 0) + ' queries'"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-zinc-400 transition-transform" :class="{ 'rotate-180': expandedIndex === index }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Expanded Content -->
                            <div x-show="expandedIndex === index"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 max-h-0"
                                 x-transition:enter-end="opacity-100 max-h-screen"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 max-h-screen"
                                 x-transition:leave-end="opacity-0 max-h-0"
                                 class="border-t border-zinc-200/60 bg-zinc-50/50 overflow-hidden">

                                <div class="p-5 space-y-6 overflow-x-auto max-w-full min-w-0">

                                    <!-- Request Initiator -->
                                    <div x-show="hasInitiator(log)">
                                        <h4 class="text-sm font-semibold text-zinc-900 mb-3">Request Initiator</h4>
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                                            <!-- Connection Info -->
                                            <div class="bg-white rounded-lg border border-zinc-200 p-4">
                                                <h5 class="text-sm font-medium text-zinc-900 mb-3 flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 0 1 7.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 0 1 1.06 0Z" />
                                                    </svg>
                                                    Connection
                                                </h5>
                                                <div class="space-y-2 text-sm">
                                                    <div class="flex justify-between">
                                                        <span class="text-zinc-600">Protocol:</span>
                                                        <span class="font-medium text-zinc-900" x-text="getInitiatorValue(log, 'protocol') || 'N/A'"></span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-zinc-600">Host:</span>
                                                        <span class="font-medium text-zinc-900 break-all" x-text="getInitiatorValue(log, 'host') || 'N/A'"></span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-zinc-600">Port:</span>
                                                        <span class="font-medium text-zinc-900" x-text="getInitiatorValue(log, 'port') || 'N/A'"></span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-zinc-600">Server IP:</span>
                                                        <span class="font-medium text-zinc-900" x-text="getInitiatorValue(log, 'ip') || 'N/A'"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Request Info -->
                                            <div class="bg-white rounded-lg border border-zinc-200 p-4">
                                                <h5 class="text-sm font-medium text-zinc-900 mb-3 flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                    </svg>
                                                    Request
                                                </h5>
                                                <div class="space-y-2 text-sm">
                                                    <div class="flex justify-between">
                                                        <span class="text-zinc-600">Method:</span>
                                                        <span class="font-medium text-zinc-900" x-text="getInitiatorValue(log, 'request_method') || 'N/A'"></span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-zinc-600">URI:</span>
                                                        <span class="font-medium text-zinc-900 break-all text-xs" x-text="getInitiatorValue(log, 'request_uri') || 'N/A'"></span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-zinc-600">Time:</span>
                                                        <span class="font-medium text-zinc-900 text-xs" x-text="getInitiatorValue(log, 'request_time') || 'N/A'"></span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-zinc-600">Float Time:</span>
                                                        <span class="font-medium text-zinc-900" x-text="getInitiatorValue(log, 'request_time_float') || 'N/A'"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- User Agent & Referer -->
                                        <div class="space-y-3">
                                            <div x-show="hasInitiatorValue(log, 'user_agent')" class="bg-white rounded-lg border border-zinc-200 p-4">
                                                <h5 class="text-sm font-medium text-zinc-900 mb-2">User Agent</h5>
                                                <div class="text-xs text-zinc-600 bg-zinc-50 rounded p-2 font-mono break-all overflow-x-auto" x-text="getInitiatorValue(log, 'user_agent')"></div>
                                            </div>
                                            <div x-show="hasInitiatorValue(log, 'referer')" class="bg-white rounded-lg border border-zinc-200 p-4">
                                                <h5 class="text-sm font-medium text-zinc-900 mb-2">Referer</h5>
                                                <div class="text-xs text-zinc-600 bg-zinc-50 rounded p-2 font-mono break-all overflow-x-auto" x-text="getInitiatorValue(log, 'referer')"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Performance Details -->
                                    <div>
                                        <h4 class="text-sm font-semibold text-zinc-900 mb-3">Performance</h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 max-w-full">
                                            <div class="bg-white rounded-lg p-3 border border-zinc-200 min-w-0 overflow-hidden" x-show="hasPerformanceValue(log, 'execution_time')">
                                                <div class="text-xs text-zinc-500 uppercase tracking-wide">Execution Time</div>
                                                <div class="text-lg font-semibold text-zinc-900" x-text="(getPerformanceValue(log, 'execution_time') || 0) + 's'"></div>
                                                <div class="text-xs text-zinc-500" x-text="getPerformanceStatus(getPerformanceValue(log, 'execution_time') || 0, 'time')"></div>
                                            </div>
                                            <div class="bg-white rounded-lg p-3 border border-zinc-200 min-w-0 overflow-hidden" x-show="hasPerformanceValue(log, 'memory_usage')">
                                                <div class="text-xs text-zinc-500 uppercase tracking-wide">Memory Usage</div>
                                                <div class="text-lg font-semibold text-zinc-900" x-text="(getPerformanceValue(log, 'memory_usage') || 0) + 'MB'"></div>
                                                <div class="text-xs text-zinc-500" x-text="getPerformanceStatus(getPerformanceValue(log, 'memory_usage') || 0, 'memory')"></div>
                                            </div>
                                            <div class="bg-white rounded-lg p-3 border border-zinc-200 min-w-0 overflow-hidden" x-show="hasPerformanceValue(log, 'memory_peak')">
                                                <div class="text-xs text-zinc-500 uppercase tracking-wide">Peak Memory</div>
                                                <div class="text-lg font-semibold text-zinc-900" x-text="(getPerformanceValue(log, 'memory_peak') || 0) + 'MB'"></div>
                                            </div>
                                            <div class="bg-white rounded-lg p-3 border border-zinc-200 min-w-0 overflow-hidden" x-show="hasPerformanceValue(log, 'db_queries')">
                                                <div class="text-xs text-zinc-500 uppercase tracking-wide">DB Queries</div>
                                                <div class="text-lg font-semibold text-zinc-900" x-text="getPerformanceValue(log, 'db_queries') || 0"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Query Statistics -->
                                    <div x-show="hasQueryStats(log)">
                                        <h4 class="text-sm font-semibold text-zinc-900 mb-3">Query Statistics</h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                                            <div class="bg-white rounded-lg p-3 border border-zinc-200 min-w-0 overflow-hidden">
                                                <div class="text-xs text-zinc-500 uppercase tracking-wide">Total Queries</div>
                                                <div class="text-lg font-semibold text-zinc-900" x-text="getQueryStatsValue(log, 'count_queries') || 0"></div>
                                                <div class="text-xs text-zinc-500">Database queries executed</div>
                                            </div>

                                            <div class="bg-white rounded-lg p-3 border border-zinc-200 min-w-0 overflow-hidden">
                                                <div class="text-xs text-zinc-500 uppercase tracking-wide">Total Time</div>
                                                <div class="text-lg font-semibold text-zinc-900" x-text="getQueryStatsValue(log, 'total_time') ? (Math.round(getQueryStatsValue(log, 'total_time') * 1000 * 100) / 100 + 'ms') : '0ms'"></div>
                                                <div class="text-xs text-zinc-500">All queries combined</div>
                                            </div>

                                            <div class="bg-white rounded-lg p-3 border border-zinc-200 min-w-0 overflow-hidden">
                                                <div class="text-xs text-zinc-500 uppercase tracking-wide">Average Time</div>
                                                <div class="text-lg font-semibold text-zinc-900" x-text="getQueryStatsValue(log, 'average_time') ? (Math.round(getQueryStatsValue(log, 'average_time') * 1000 * 100) / 100 + 'ms') : '0ms'"></div>
                                                <div class="text-xs text-zinc-500">Per query average</div>
                                            </div>

                                            <div class="bg-white rounded-lg p-3 border border-zinc-200 min-w-0 overflow-hidden">
                                                <div class="text-xs text-zinc-500 uppercase tracking-wide">Query Memory</div>
                                                <div class="text-lg font-semibold text-zinc-900" x-text="getQueryStatsValue(log, 'memory_usage') ? (Math.round(getQueryStatsValue(log, 'memory_usage') / 1024 / 1024 * 100) / 100 + 'MB') : '0MB'"></div>
                                                <div class="text-xs text-zinc-500">Memory used for queries</div>
                                            </div>
                                        </div>

                                        <!-- Individual Queries -->
                                        <div x-show="hasQueryStatsQueries(log)" class="bg-white rounded-lg border border-zinc-200 p-4">
                                            <h5 class="text-sm font-medium text-zinc-900 mb-3">Individual Queries</h5>
                                            <div class="space-y-3 max-h-64 overflow-y-auto">
                                                <template x-for="(query, queryIndex) in (getQueryStatsValue(log, 'queries') || [])" :key="queryIndex">
                                                    <div class="border-l-4 border-blue-200 pl-4 py-2">
                                                        <div class="flex items-center justify-between mb-2">
                                                            <span class="text-xs font-medium text-blue-600" x-text="'Query #' + (query.number || queryIndex + 1)"></span>
                                                            <span class="text-xs text-zinc-500" x-text="query.time ? (Math.round(query.time * 1000 * 100) / 100 + 'ms') : '0ms'"></span>
                                                        </div>
                                                        <div class="bg-zinc-50 rounded p-2 font-mono text-xs overflow-x-auto">
                                                            <pre x-text="query.sql || 'No SQL'"></pre>
                                                        </div>
                                                        <div x-show="query.params && query.params.length > 0" class="mt-2 text-xs text-zinc-600">
                                                            <span class="font-medium">Parameters:</span>
                                                            <span x-text="JSON.stringify(query.params)"></span>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Debug Dumps -->
                                    <div x-show="hasDumps(log)">
                                        <h4 class="text-sm font-semibold text-zinc-900 mb-3">Debug Dumps</h4>
                                        <div class="space-y-4">
                                            <template x-for="(dump, dumpIndex) in log.debug.info.dumps" :key="dumpIndex">
                                                <div class="bg-white rounded-lg border border-zinc-200 overflow-hidden">
                                                    <!-- Dump Header -->
                                                    <div class="p-4 border-b border-zinc-100">
                                                    <div class="flex items-start justify-between mb-2">
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-sm font-medium text-zinc-900" x-text="dump.label"></span>
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" :class="getDumpTypeColor(dump)" x-text="getDumpType(dump)"></span>
                                                            </div>
                                                            <div class="text-xs text-zinc-500" x-text="dump.caller.file + ':' + dump.caller.line + ' in ' + dump.caller.function + '()'"></div>
                                                        </div>
                                                        <div x-show="getDumpInfo(dump)" class="text-xs text-zinc-600" x-text="getDumpInfo(dump)"></div>
                                                    </div>

                                                    <!-- Object Analysis (for objects with detailed analysis) -->
                                                    <div x-show="hasDetailedAnalysis(dump)" class="p-4 bg-zinc-50/50">
                                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                            <!-- Methods -->
                                                            <div x-show="hasDumpAnalysis(dump, 'methods')" class="bg-white rounded p-3 border border-zinc-200">
                                                                <h6 class="text-xs font-semibold text-zinc-700 mb-2 uppercase tracking-wide">Methods</h6>
                                                                <div class="text-xs text-zinc-600 space-y-1 max-h-32 overflow-y-auto">
                                                                    <template x-for="method in getDumpMethods(dump)" :key="method">
                                                                        <div class="font-mono" x-text="method + '()'"></div>
                                                                    </template>
                                                                </div>
                                                                <div class="text-xs text-zinc-500 mt-2" x-text="getDumpMethods(dump).length + ' methods'"></div>
                                                            </div>

                                                            <!-- Properties -->
                                                            <div x-show="hasDumpAnalysis(dump, 'properties')" class="bg-white rounded p-3 border border-zinc-200">
                                                                <h6 class="text-xs font-semibold text-zinc-700 mb-2 uppercase tracking-wide">Properties</h6>
                                                                <div class="text-xs text-zinc-600 space-y-1 max-h-32 overflow-y-auto">
                                                                    <template x-for="(value, key) in getDumpProperties(dump)" :key="key">
                                                                        <div class="font-mono">
                                                                            <span class="text-blue-600" x-text="key"></span>: 
                                                                            <span class="text-zinc-500" x-text="formatPropertyValue(value)"></span>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </div>

                                                            <!-- Traits & Interfaces -->
                                                            <div class="space-y-3">
                                                                <!-- Traits -->
                                                                <div x-show="hasDumpAnalysis(dump, 'traits')" class="bg-white rounded p-3 border border-zinc-200">
                                                                    <h6 class="text-xs font-semibold text-zinc-700 mb-2 uppercase tracking-wide">Traits</h6>
                                                                    <div class="text-xs text-zinc-600 space-y-1">
                                                                        <template x-for="trait in getDumpTraitValues(dump)" :key="trait">
                                                                            <div class="font-mono text-purple-600" x-text="trait"></div>
                                                                        </template>
                                                                    </div>
                                                                </div>

                                                                <!-- Interfaces -->
                                                                <div x-show="hasDumpAnalysis(dump, 'interfaces')" class="bg-white rounded p-3 border border-zinc-200">
                                                                    <h6 class="text-xs font-semibold text-zinc-700 mb-2 uppercase tracking-wide">Interfaces</h6>
                                                                    <div class="text-xs text-zinc-600 space-y-1">
                                                                        <template x-for="interface in getDumpInterfaces(dump)" :key="interface">
                                                                            <div class="font-mono text-green-600" x-text="interface"></div>
                                                                        </template>
                                                        </div>
                                                    </div>

                                                                <!-- Parent Class -->
                                                                <div x-show="getDumpParent(dump)" class="bg-white rounded p-3 border border-zinc-200">
                                                                    <h6 class="text-xs font-semibold text-zinc-700 mb-2 uppercase tracking-wide">Parent Class</h6>
                                                                    <div class="text-xs font-mono text-orange-600" x-text="getDumpParent(dump)"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Raw Value (always show for all dumps) -->
                                                    <div class="p-4">
                                                        <h6 class="text-xs font-semibold text-zinc-700 mb-2 uppercase tracking-wide">Value</h6>
                                                        <div class="bg-zinc-50 rounded p-3 font-mono text-sm overflow-x-auto max-w-full min-w-0">
                                                            <pre class="whitespace-pre-wrap break-words max-w-full overflow-hidden min-w-0" x-text="formatDumpValue(dump)"></pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Environment Info -->
                                    <div>
                                        <h4 class="text-sm font-semibold text-zinc-900 mb-3">Environment</h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                            <div class="bg-white rounded-lg p-3 border border-zinc-200 min-w-0 overflow-hidden" x-show="hasEnvironmentValue(log, 'php.checks.version')">
                                                <div class="text-xs text-zinc-500 uppercase tracking-wide mb-1">PHP Version</div>
                                                <div class="text-sm font-medium text-zinc-900" x-text="getEnvironmentValue(log, 'php.checks.version.value') || 'N/A'"></div>
                                                <div class="text-xs" :class="getEnvironmentValue(log, 'php.checks.version.status') === 'ok' ? 'text-green-600' : 'text-red-600'" x-text="getEnvironmentValue(log, 'php.checks.version.message') || ''"></div>
                                            </div>

                                            <div class="bg-white rounded-lg p-3 border border-zinc-200 min-w-0 overflow-hidden" x-show="hasEnvironmentValue(log, 'php.checks.memory_limit')">
                                                <div class="text-xs text-zinc-500 uppercase tracking-wide mb-1">Memory Limit</div>
                                                <div class="text-sm font-medium text-zinc-900" x-text="getEnvironmentValue(log, 'php.checks.memory_limit.value') || 'N/A'"></div>
                                                <div class="text-xs" :class="getEnvironmentValue(log, 'php.checks.memory_limit.status') === 'ok' ? 'text-green-600' : 'text-red-600'" x-text="getEnvironmentValue(log, 'php.checks.memory_limit.message') || ''"></div>
                                            </div>

                                            <div class="bg-white rounded-lg p-3 border border-zinc-200 min-w-0 overflow-hidden" x-show="hasEnvironmentValue(log, 'php.checks.max_execution_time')">
                                                <div class="text-xs text-zinc-500 uppercase tracking-wide mb-1">Max Execution Time</div>
                                                <div class="text-sm font-medium text-zinc-900" x-text="getEnvironmentValue(log, 'php.checks.max_execution_time.value') || 'N/A'"></div>
                                                <div class="text-xs" :class="getEnvironmentValue(log, 'php.checks.max_execution_time.status') === 'ok' ? 'text-green-600' : 'text-red-600'" x-text="getEnvironmentValue(log, 'php.checks.max_execution_time.message') || ''"></div>
                                            </div>

                                            <div class="bg-white rounded-lg p-3 border border-zinc-200 min-w-0 overflow-hidden" x-show="hasEnvironmentValue(log, 'php.checks.extensions')">
                                                <div class="text-xs text-zinc-500 uppercase tracking-wide mb-1">Extensions</div>
                                                <div class="text-sm font-medium text-zinc-900" x-text="getEnvironmentValue(log, 'php.checks.extensions.status') === 'ok' ? 'All loaded' : 'Missing some'"></div>
                                                <div x-show="getEnvironmentValue(log, 'php.checks.extensions.missing') && getEnvironmentValue(log, 'php.checks.extensions.missing').length > 0" class="text-xs text-red-600" x-text="'Missing: ' + (getEnvironmentValue(log, 'php.checks.extensions.missing') || []).join(', ')"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Filesystem Info -->
                                    <div x-show="hasFilesystemValue(log, 'disk_space') || hasFilesystemValue(log, 'uploads') || hasFilesystemValue(log, 'logs')">
                                        <h4 class="text-sm font-semibold text-zinc-900 mb-3">Filesystem</h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                            <div class="bg-white rounded-lg p-3 border border-zinc-200 min-w-0 overflow-hidden" x-show="hasFilesystemValue(log, 'disk_space')">
                                                <div class="text-xs text-zinc-500 uppercase tracking-wide mb-1">Disk Space</div>
                                                <div class="text-sm font-medium text-zinc-900" x-text="getFilesystemValue(log, 'disk_space.message') || 'N/A'"></div>
                                                <div class="text-xs text-zinc-500">
                                                    <span x-text="getFilesystemValue(log, 'disk_space.free_bytes') ? (Math.round(getFilesystemValue(log, 'disk_space.free_bytes') / 1024 / 1024 / 1024 * 100) / 100 + ' GB free') : ''"></span>
                                                </div>
                                            </div>

                                            <div class="bg-white rounded-lg p-3 border border-zinc-200 min-w-0 overflow-hidden" x-show="hasFilesystemValue(log, 'uploads')">
                                                <div class="text-xs text-zinc-500 uppercase tracking-wide mb-1">Uploads Directory</div>
                                                <div class="text-sm font-medium text-zinc-900" x-text="getFilesystemValue(log, 'uploads.message') || 'N/A'"></div>
                                                <div class="text-xs text-zinc-500 break-all overflow-hidden" x-text="getFilesystemValue(log, 'uploads.path') || ''"></div>
                                            </div>

                                            <div class="bg-white rounded-lg p-3 border border-zinc-200 min-w-0 overflow-hidden" x-show="hasFilesystemValue(log, 'logs')">
                                                <div class="text-xs text-zinc-500 uppercase tracking-wide mb-1">Logs Directory</div>
                                                <div class="text-sm font-medium text-zinc-900" x-text="getFilesystemValue(log, 'logs.message') || 'N/A'"></div>
                                                <div class="text-xs text-zinc-500 break-all overflow-hidden" x-text="getFilesystemValue(log, 'logs.path') || ''"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- User Environment -->
                                    <div x-show="hasUserEnvValue(log, 'user_functions') || hasUserEnvValue(log, 'user_classes') || hasUserEnvValue(log, 'user_constants')">
                                        <h4 class="text-sm font-semibold text-zinc-900 mb-3">User Environment</h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                            <div class="bg-white rounded-lg p-3 border border-zinc-200 min-w-0 overflow-hidden">
                                                <div class="text-xs text-zinc-500 uppercase tracking-wide mb-1">User Functions</div>
                                                <div class="text-lg font-semibold text-zinc-900" x-text="(getUserEnvValue(log, 'user_functions') || []).length"></div>
                                                <div class="text-xs text-zinc-500">Custom functions loaded</div>
                                            </div>

                                            <div class="bg-white rounded-lg p-3 border border-zinc-200 min-w-0 overflow-hidden">
                                                <div class="text-xs text-zinc-500 uppercase tracking-wide mb-1">User Classes</div>
                                                <div class="text-lg font-semibold text-zinc-900" x-text="(getUserEnvValue(log, 'user_classes') || []).length"></div>
                                                <div class="text-xs text-zinc-500">Custom classes loaded</div>
                                            </div>

                                            <div class="bg-white rounded-lg p-3 border border-zinc-200 min-w-0 overflow-hidden">
                                                <div class="text-xs text-zinc-500 uppercase tracking-wide mb-1">User Constants</div>
                                                <div class="text-lg font-semibold text-zinc-900" x-text="(getUserEnvValue(log, 'user_constants') || []).length"></div>
                                                <div class="text-xs text-zinc-500">Custom constants defined</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Server Load -->
                                    <div x-show="hasServerLoad(log)">
                                        <h4 class="text-sm font-semibold text-zinc-900 mb-3">Server Load</h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                            <div class="bg-white rounded-lg p-3 border border-zinc-200 min-w-0 overflow-hidden">
                                                <div class="text-xs text-zinc-500 uppercase tracking-wide mb-1">1 Minute</div>
                                                <div class="text-lg font-semibold text-zinc-900" x-text="getServerLoadValue(log, '1min') || 'N/A'"></div>
                                                <div class="text-xs text-zinc-500">Average load</div>
                                            </div>

                                            <div class="bg-white rounded-lg p-3 border border-zinc-200 min-w-0 overflow-hidden">
                                                <div class="text-xs text-zinc-500 uppercase tracking-wide mb-1">5 Minutes</div>
                                                <div class="text-lg font-semibold text-zinc-900" x-text="getServerLoadValue(log, '5min') || 'N/A'"></div>
                                                <div class="text-xs text-zinc-500">Average load</div>
                                            </div>

                                            <div class="bg-white rounded-lg p-3 border border-zinc-200 min-w-0 overflow-hidden">
                                                <div class="text-xs text-zinc-500 uppercase tracking-wide mb-1">15 Minutes</div>
                                                <div class="text-lg font-semibold text-zinc-900" x-text="getServerLoadValue(log, '15min') || 'N/A'"></div>
                                                <div class="text-xs text-zinc-500">Average load</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Process Info -->
                                    <div x-show="hasProcessId(log)">
                                        <h4 class="text-sm font-semibold text-zinc-900 mb-3">Process Info</h4>
                                        <div class="bg-white rounded-lg p-3 border border-zinc-200">
                                            <div class="text-xs text-zinc-500 uppercase tracking-wide mb-1">Process ID</div>
                                            <div class="text-lg font-semibold text-zinc-900" x-text="getProcessId(log) || 'N/A'"></div>
                                            <div class="text-xs text-zinc-500">Current PHP process identifier</div>
                                        </div>
                                    </div>

                                    <!-- DEBUG: Show log structure (temporary) -->
                                    <div class="bg-red-50 border border-red-200 rounded p-3 mb-4">
                                        <h5 class="text-sm font-medium text-red-900 mb-2">DEBUG: Log Structure</h5>
                                        <div class="text-xs font-mono text-red-700 max-h-32 overflow-y-auto">
                                            <pre x-text="JSON.stringify(log, null, 2)"></pre>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <div x-show="logs.length === 0" class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-zinc-900">No debug logs</h3>
                        <p class="mt-1 text-sm text-zinc-500">Debug logs will appear here when DEBUGMODE is enabled.</p>
                    </div>

                    <!-- No Results -->
                    <div x-show="logs.length > 0 && filteredLogs.length === 0" class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-zinc-900">No matching logs</h3>
                        <p class="mt-1 text-sm text-zinc-500">Try adjusting your search or filter criteria.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../../layouts/base.php';
