<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * © 2010-2025 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      http://www.roocms.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program. If not, see http://www.gnu.org/licenses/
 */

/**
 * error reporting
 */
error_reporting(0);

/**
 * Get error message based on HTTP status code
 */
$status_code = http_response_code();

switch($status_code) {
    case 400:
        $error = '400: Bad request.<br />Please check the request and try again.';
        break;
    case 401:
        $error = '401: Unauthorized.<br />Please check your credentials and try again.';
        break;
    case 403:
        $error = '403: Access denied.<br />Please check your credentials and try again.';
        break;
    case 404:
        $error = '404: Page not found.<br />Please check the URL and try again.';
        break;
    case 405:
        $error = '405: Method not allowed.<br />Please check the request method and try again.';
        break;
    case 408:
        $error = '408: Request timeout.<br />Please try again later.';
        break;
    case 409:
        $error = '409: Conflict.<br />Please try again later.';
        break;
    case 414:
        $error = '414: URI too long.<br />Please check the URL and try again.';
        break;
    case 500:
        $error = '500: Internal server error.<br />Please try again later.';
        break;
    default:
        $error = 'Unknown error.<br />Please try again later.';
        break;
}

$safe_error = htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

header('Content-Type: text/html; charset=UTF-8');

echo <<<HTML
<!doctype html>
<html lang="ru" class="no-js">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="color-scheme" content="light" />
    <title>RooCMS — Technical Message</title>
    <style>
        :root {
            --bg: #fbfbfd;
            --text: #0b2a55;
            --muted: #405b86;
            --shadow: rgba(11, 42, 85, 0.10);
        }
        * { box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            font-family: "Montserrat", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", Ubuntu, "Cantarell", "Liberation Sans", sans-serif;
        }
        .shell {
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 16px;
            position: relative;
            overflow: hidden;
            background-image: url('logo2.0.png');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: contain;
            margin: 0 auto;
        }
        
        /* Background image adaptation */
        @media (max-width: 1536px) {
            .shell {
                background-size: contain;
            }
        }
        @media (max-width: 1024px) {
            .shell {
                background-size: 90% auto;
                background-position: center 40%;
            }
        }
        @media (max-width: 768px) {
            .shell {
                background-size: 95% auto;
                background-position: center 35%;
            }
        }
        @media (max-width: 480px) {
            .shell {
                background-size: 98% auto;
                background-position: center 30%;
            }
        }

        /* Subtle ambient glow for a modern Web3 look without breaking brand colors */
        .shell::before {
            content: "";
            position: absolute;
            width: 90vw;
            max-width: 1100px;
            height: 90vw;
            max-height: 1100px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            background: radial-gradient(circle, rgba(11,42,85,0.14) 0%, rgba(11,42,85,0.08) 35%, rgba(11,42,85,0.02) 60%, rgba(11,42,85,0) 75%);
            filter: blur(20px);
        }
        .stack {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            max-width: 100%;
            margin-top: 74vh;
            transform: translateY(-50%);
        }
        .message {
            margin: 0;
            max-width: 900px;
            font-weight: 500;
            line-height: 1.55;
            font-size: 36px;
            letter-spacing: .1px;
            color: var(--text);
            opacity: .98;
            animation: fadeUp .9s ease-out .1s both;
        }
        @media (max-width: 600px) {
            .message { 
                font-size: 32px;
                max-width: 280px;
            }
        }
        @media (max-width: 400px) {
            .message { 
                font-size: 28px;
            }
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(6px) scale(.995); }
            to   { opacity: 1; transform: translateY(0)    scale(1); }
        }
        /* Respect reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .logo, .message { animation: none; }
        }
    </style>
</head>
<body>
    <main class="shell">
        <div class="stack" role="group" aria-label="Status message">
            <p class="message">{$safe_error}</p>
        </div>
    </main>
    <script>
        // Remove no-js class to enable potential progressive enhancements
        document.documentElement.classList.remove('no-js');
    </script>
</body>
</html>
HTML;
