<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(503); header('Content-Type: text/plain; charset=utf-8'); exit('503:Service Unavailable'); }

$page_title = 'Offline - RooCMS';
$page_description = 'You are currently offline';

ob_start();
?>

<div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <!-- Offline Icon -->
        <div class="flex justify-center">
            <div class="flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-r from-gray-400 to-gray-500">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-12.728 12.728m0-12.728l12.728 12.728m-1.414-4.95A7 7 0 105.05 8.636"></path>
                </svg>
            </div>
        </div>

        <!-- Title and Message -->
        <div class="space-y-4">
            <h1 class="text-3xl font-extrabold text-gray-900">
                You are offline
            </h1>
            <p class="text-gray-600">
                It looks like you've lost your internet connection. Don't worry, some content may still be available from cache.
            </p>
        </div>

        <!-- Actions -->
        <div class="space-y-4">
            <button 
                onclick="window.location.reload()" 
                class="btn-primary-full"
            >
                Try Again
            </button>
            
            <a 
                href="javascript:history.back()" 
                class="btn-secondary w-full text-center"
            >
                Go Back
            </a>
        </div>

        <!-- Network Status -->
        <div class="pt-8">
            <div id="network-status" class="text-sm text-gray-500">
                Checking connection...
            </div>
        </div>
    </div>
</div>

<script nonce="<?php render_html($csp_nonce); ?>">
// Check network status
function updateNetworkStatus() {
    const statusEl = document.getElementById('network-status');
    if (navigator.onLine) {
        statusEl.textContent = 'Connection restored! You can try refreshing the page.';
        statusEl.className = 'text-sm text-green-600';
    } else {
        statusEl.textContent = 'Still offline. Please check your connection.';
        statusEl.className = 'text-sm text-red-600';
    }
}

// Initial check
updateNetworkStatus();

// Listen for connection changes
window.addEventListener('online', updateNetworkStatus);
window.addEventListener('offline', updateNetworkStatus);

// Auto-retry when online
window.addEventListener('online', () => {
    setTimeout(() => {
        window.location.reload();
    }, 1000);
});
</script>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
