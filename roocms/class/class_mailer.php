<?php declare(strict_types=1);
/**
 * RooCMS - Open Source Free Content Managment System
 * © 2010-2025 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      https://www.roocms.com
 * @license   https://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program. If not, see https://www.gnu.org/licenses/
 */

//#########################################################
//	Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit('403:Access denied');
}
//#########################################################


/**
 * Class for sending email messages
 * Supports SMTP, sendmail and the built-in mail() function
 */
class Mailer {

    private SiteSettings $siteSettings;
    private string $last_error = '';

    // Configuration of the mail driver
    private readonly string $driver;
    private readonly string $host;
    private readonly int $port;
    private readonly string $username;
    private readonly string $password;
    private readonly string $encryption;
    private readonly string $from;
    private readonly string $from_name;
    private readonly string $reply_to;
    private readonly string $reply_to_name;
    
    private readonly string $site_domain;
    
    // Limits for attachments
    private readonly int $max_attachment_size;
    private readonly int $max_attachments_count;



    /**
     * Constructor with initialization of properties
     * 
     * @param SiteSettings $siteSettings Settings
     */
    public function __construct(SiteSettings $siteSettings) {

        $this->siteSettings = $siteSettings;

        // Initialization of readonly properties through the constructor
        $this->driver = $this->siteSettings->get_by_key('mailer_driver') ?? 'mail';
        $this->host = $this->siteSettings->get_by_key('mailer_host') ?? 'localhost';
        $this->port = (int)($this->siteSettings->get_by_key('mailer_port') ?? 25);
        $this->username = $this->siteSettings->get_by_key('mailer_username') ?? '';
        $this->password = $this->siteSettings->get_by_key('mailer_password') ?? '';
        $this->encryption = $this->siteSettings->get_by_key('mailer_encryption') ?? 'tls';
        $this->from = $this->siteSettings->get_by_key('mailer_from') ?? '';
        $this->from_name = $this->siteSettings->get_by_key('mailer_from_name') ?? '';
        $this->reply_to = $this->siteSettings->get_by_key('mailer_reply_to') ?? '';
        $this->reply_to_name = $this->siteSettings->get_by_key('mailer_reply_to_name') ?? '';
        
        $this->site_domain = $this->siteSettings->get_by_key('site_domain') ?? 'localhost';
        
        // Initialization of limits for attachments
        $this->max_attachment_size = (int)($this->siteSettings->get_by_key('mailer_max_attachment_size') ?? 10485760); // 10MB by default
        $this->max_attachments_count = (int)($this->siteSettings->get_by_key('mailer_max_attachments_count') ?? 10); // 10 files by default
    }


    /**
     * Sending a notification to the user
     * 
     * @param object $user User
     * @param string $message Message
     * @return bool
     */
    public function send_notification(object $user, string $message): bool {
        if (!isset($user->email) || !is_string($user->email)) {
            $this->last_error = 'Invalid user email';
            return false;
        }

        $subject = 'Notification from ' . ($this->siteSettings->get_by_key('site_name') ?? 'RooCMS');
        
        return $this->send([
            'to' => $user->email,
            'subject' => $subject,
            'body' => $message,
            'is_html' => true
        ]);
    }


    /**
     * Main method of sending email
     * 
     * @param array $params Parameters
     * @return bool
     */
    public function send(array $params): bool {
        // Validate required parameters
        $required_params = ['to' => 'Missing required parameter: to', 'subject' => 'Missing required parameter: subject', 'body' => 'Missing required parameter: body'];
        foreach ($required_params as $param => $error_msg) {
            if (!isset($params[$param]) || ($param !== 'body' && empty($params[$param]))) {
                $this->last_error = $error_msg;
                return false;
            }
        }

        // Extract and validate parameters
        $to = sanitize_email($params['to']);
        $subject = $this->sanitize_subject($params['subject']);
        
        if (!$to) {
            $this->last_error = 'Invalid recipient email';
            return false;
        }
        
        if (empty($subject)) {
            $this->last_error = 'Empty message subject';
            return false;
        }

        // Prepare parameters for driver methods
        $send_params = [
            $to, 
            $subject, 
            $params['body'],
            $params['is_html'] ?? true,
            $params['attachments'] ?? null,
            $params['from'] ?? null,
            $params['from_name'] ?? null
        ];

        // Driver method mapping
        $drivers = [
            'smtp' => 'send_via_smtp',
            'sendmail' => 'send_via_sendmail', 
            'mail' => 'send_via_mail'
        ];

        if (!isset($drivers[$this->driver])) {
            $this->last_error = 'Unsupported driver: ' . $this->driver;
            return false;
        }

        return $this->{$drivers[$this->driver]}(...$send_params);
    }


    /**
     * Sending via SMTP
     * 
     * @param string $to To
     * @param string $subject Subject
     * @param string $body Body
     * @param bool $is_html Is HTML
     * @param array|null $attachments Attachments
     * @param string|null $custom_from Custom from
     * @param string|null $custom_from_name Custom from name
     * @return bool
     */
    private function send_via_smtp(string $to, string $subject, string $body, bool $is_html, 
                        ?array $attachments, ?string $custom_from, ?string $custom_from_name): bool {
        try {
            $socket = $this->create_smtp_connection();
            if (!$socket) {
                return false;
            }

            // SMTP protocol steps - consolidated error handling
            $commands = [
                ['EHLO ' . $this->site_domain, null],
            ];

            // Add TLS commands if needed
            if ($this->encryption === 'tls') {
                $commands[] = ['STARTTLS', '220'];
            }

            // Execute initial commands
            foreach ($commands as [$command, $expected_code]) {
                if (!$this->smtp_command($socket, $command, $expected_code)) {
                    fclose($socket);
                    return false;
                }
            }

            // Handle TLS encryption
            if ($this->encryption === 'tls') {
                if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    $this->last_error = 'Failed to enable TLS encryption';
                    fclose($socket);
                    return false;
                }
                // Repeat EHLO after TLS (RFC 3207)
                if (!$this->smtp_command($socket, 'EHLO ' . $this->site_domain)) {
                    fclose($socket);
                    return false;
                }
            }

            // Authentication if credentials provided
            if (!empty($this->username) && !empty($this->password) && !$this->smtp_auth($socket)) {
                fclose($socket);
                return false;
            }

            // Send message and cleanup
            $success = $this->smtp_send_message($socket, $to, $subject, $body, $is_html, $attachments, $custom_from, $custom_from_name);
            
            $this->smtp_command($socket, 'QUIT', '221');
            fclose($socket);

            return $success;

        } catch (Exception $e) {
            $this->last_error = 'SMTP error: ' . $e->getMessage();
            return false;
        }
    }


    /**
     * Sending via sendmail
     * 
     * @param string $to To
     * @param string $subject Subject
     * @param string $body Body
     * @param bool $is_html Is HTML
     * @param array|null $attachments Attachments
     * @param string|null $custom_from Custom from
     * @param string|null $custom_from_name Custom from name
     * @return bool
     */
    private function send_via_sendmail(string $to, string $subject, string $body, bool $is_html,
                                ?array $attachments, ?string $custom_from, ?string $custom_from_name): bool {
        $headers = $this->build_headers($is_html, $custom_from, $custom_from_name);
        
        // Processing attachments
        $processed = $this->process_attachments($body, $is_html, $attachments, $headers);
        if (!$processed['valid']) {
            return false;
        }
        $headers = $processed['headers'];
        $body = $processed['body'];

        $sendmailPath = '/usr/sbin/sendmail -t -i';
        $pipe = popen($sendmailPath, 'w');
        
        if (!$pipe) {
            $this->last_error = 'Failed to open sendmail';
            return false;
        }

        fwrite($pipe, 'To: '.$to."\r\n");
        fwrite($pipe, 'Subject: '.$subject."\r\n");
        fwrite($pipe, $headers);
        fwrite($pipe, "\r\n");
        fwrite($pipe, $body);

        $result = pclose($pipe);
        
        if ($result !== 0) {
            $this->last_error = 'Sendmail ended with an error';
            return false;
        }

        return true;
    }


    /**
     * Sending via the built-in mail() function
     * 
     * @param string $to To
     * @param string $subject Subject
     * @param string $body Body
     * @param bool $is_html Is HTML
     * @param array|null $attachments Attachments
     * @param string|null $custom_from Custom from
     * @param string|null $custom_from_name Custom from name
     * @return bool
     */
    private function send_via_mail(string $to, string $subject, string $body, bool $is_html, 
                        ?array $attachments, ?string $custom_from, ?string $custom_from_name): bool {
        $headers = $this->build_headers($is_html, $custom_from, $custom_from_name);
        
        // Processing attachments
        $processed = $this->process_attachments($body, $is_html, $attachments, $headers);
        if (!$processed['valid']) {
            return false;
        }
        $headers = $processed['headers'];
        $body = $processed['body'];

        $from = $custom_from ?? $this->from;
        if ($from === '') {
            $from = 'no-reply@' . $this->site_domain;
        }

        $result = mail($to, $subject, $body, $headers, '-f '.$from);
        
        if (!$result) {
            $this->last_error = 'The mail() function returned false';
            return false;
        }

        return true;
    }


    /**
     * Creating an SMTP connection
     * 
     * @return resource|false
     */
    private function create_smtp_connection() {
        $verify_peer = (bool)($this->siteSettings->get_by_key('mailer_verify_peer') ?? false);
        $verify_peer_name = (bool)($this->siteSettings->get_by_key('mailer_verify_peer_name') ?? false);
        $allow_self_signed = (bool)($this->siteSettings->get_by_key('mailer_allow_self_signed') ?? true);

        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => $verify_peer,
                'verify_peer_name' => $verify_peer_name,
                'allow_self_signed' => $allow_self_signed
            ]
        ]);

        $protocol = ($this->encryption === 'ssl') ? 'ssl://' : '';
        $socket = stream_socket_client(
            $protocol.$this->host.':'.$this->port,
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$socket) {
            $this->last_error = 'Failed to connect to the SMTP server: '.$errstr.' ('.$errno.')';
            return false;
        }

        // Reading the server greeting
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) !== '220') {
            $this->last_error = 'SMTP server returned an error: '.$response;
            fclose($socket);
            return false;
        }

        return $socket;
    }


    /**
     * Executing an SMTP command
     * 
     * @param resource $socket Socket
     * @param string $command Command
     * @param string $expectedCode Expected code
     * @return bool
     */
    private function smtp_command($socket, string $command, string $expectedCode = '250'): bool {
        if (!is_resource($socket)) {
            $this->last_error = 'Invalid socket';
            return false;
        }

        fwrite($socket, $command . "\r\n");
        $response = fgets($socket, 515);
        
        if (substr($response, 0, 3) !== $expectedCode) {
            $this->last_error = 'SMTP command <'.$command.'> returned an error: '.$response;
            return false;
        }
        
        return true;
    }


    /**
     * SMTP authentication
     * 
     * @param resource $socket Socket
     * @return bool
     */
    private function smtp_auth($socket): bool {
        if (!$this->smtp_command($socket, 'AUTH LOGIN', '334')) {
            return false;
        }
        
        if (!$this->smtp_command($socket, base64_encode($this->username), '334')) {
            return false;
        }
        
        if (!$this->smtp_command($socket, base64_encode($this->password), '235')) {
            return false;
        }
        
        return true;
    }


    /**
     * Sending a message via SMTP
     * 
     * @param resource $socket Socket
     * @param string $to To
     * @param string $subject Subject
     * @param string $body Body
     * @param bool $is_html Is HTML
     * @param array|null $attachments Attachments
     * @param string|null $custom_from Custom from
     * @param string|null $custom_from_name Custom from name
     * @return bool
     */
    private function smtp_send_message($socket, string $to, string $subject, string $body, bool $is_html,
                                ?array $attachments, ?string $custom_from, ?string $custom_from_name): bool {   
        $from = $custom_from ?? $this->from;
        if ($from === '') {
            $from = 'no-reply@' . $this->site_domain;
        }
        
        if (!$this->smtp_command($socket, 'MAIL FROM: <'.$from.'>')) {
            return false;
        }
        
        if (!$this->smtp_command($socket, 'RCPT TO: <'.$to.'>')) {
            return false;
        }
        
        if (!$this->smtp_command($socket, 'DATA', '354')) {
            return false;
        }

        // Forming the headers and the body of the message
        $headers = $this->build_headers($is_html, $custom_from, $custom_from_name);
        
        // Processing the attachments
        $processed = $this->process_attachments($body, $is_html, $attachments, $headers);
        $processed_headers = $processed['headers'];
        $processed_body = $processed['body'];
        
        $message = 'To: '.$to."\r\n";
        $message .= 'Subject: '.$subject."\r\n";
        $message .= $processed_headers;
        $message .= "\r\n";
        $message .= $processed_body;

        $message .= "\r\n.\r\n";
        
        fwrite($socket, $message);
        $response = fgets($socket, 515);
        
        if (substr($response, 0, 3) !== '250') {
            $this->last_error = 'Error sending the message: '.$response;
            return false;
        }
        
        return true;
    }


    /**
     * Building the headers of the email
     * 
     * @param bool $is_html Is HTML
     * @param string|null $custom_from Custom from
     * @param string|null $custom_from_name Custom from name
     * @return string
     */
    private function build_headers(bool $is_html, ?string $custom_from = null, ?string $custom_from_name = null): string {
        $from = $custom_from ?? $this->from;
        if ($from === '') {
            $from = 'no-reply@' . $this->site_domain;
        }

        $from_name = $custom_from_name ?? $this->from_name;
        if ($from_name === '') {
            $from_name = $this->siteSettings->get_by_key('site_name') ?? 'RooCMS';
        }
        $encoded_from_name = mb_encode_mimeheader($from_name, 'UTF-8', 'B');

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Date: " . date('r') . "\r\n";
        $headers .= 'From: ' . $encoded_from_name . ' <' . $from . ">\r\n";
        
        if (!empty($this->reply_to)) {
            $reply_to_name = $this->reply_to_name !== '' ? $this->reply_to_name : $from_name;
            $encoded_reply_to_name = mb_encode_mimeheader($reply_to_name, 'UTF-8', 'B');
            $headers .= 'Reply-To: ' . $encoded_reply_to_name . ' <' . $this->reply_to . ">\r\n";
        }
        
        $headers .= 'X-Sender: <no-reply@'.$this->site_domain.">\r\n";
        $headers .= 'X-Mailer: RooCMS from '.$this->site_domain."\r\n";
        $headers .= 'Return-Path: <no-reply@'.$this->site_domain.">\r\n";
        
        return $headers;
    }


    /**
     * Generating a boundary for multipart messages
     * 
     * @return string
     */
    private function generate_boundary(): string {
        return '----=_NextPart_' . md5(uniqid(time()));
    }


    /**
     * Validation of attachments
     * 
     * @param array|null $attachments Attachments
     * @return array
     */
    private function validate_attachments(?array $attachments): array {
        if (!$attachments || count($attachments) === 0) {
            return ['valid' => true, 'attachments' => []];
        }

        // Helper function for error response
        $error_response = fn($message) => ($this->last_error = $message) && ['valid' => false, 'attachments' => []];

        // Check attachments count
        if (count($attachments) > $this->max_attachments_count) {
            return $error_response('Exceeded the maximum number of attachments ('.$this->max_attachments_count.')');
        }

        $validated_attachments = [];
        $total_size = 0;
        $max_size_mb = round($this->max_attachment_size / 1048576, 1);
        $max_total_mb = round(($this->max_attachment_size * $this->max_attachments_count) / 1048576, 1);

        foreach ($attachments as $attachment) {
            // Validate attachment structure and file
            $file_path = $attachment['path'] ?? null;
            
            $file_checks = [
                [!isset($attachment['path']) || !is_string($file_path), 'Invalid attachment structure: the path to the file is missing'],
                [!file_exists($file_path), 'File not found: '.$file_path],
                [!is_readable($file_path), 'File is not accessible for reading: '.$file_path],
            ];

            foreach ($file_checks as [$condition, $message]) {
                if ($condition) {
                    return $error_response($message);
                }
            }

            // Check file size
            $file_size = filesize($file_path);
            if ($file_size === false) {
                return $error_response('Failed to determine the size of the file: '.$file_path);
            }

            if ($file_size > $this->max_attachment_size) {
                return $error_response('File is too large: '.$file_path.' (maximum '.$max_size_mb.'MB)');
            }

            $total_size += $file_size;
            if ($total_size > ($this->max_attachment_size * $this->max_attachments_count)) {
                return $error_response('The total size of the attachments is too large (maximum '.$max_total_mb.'MB)');
            }

            // Add validated attachment
            $validated_attachments[] = [
                'path' => $file_path,
                'name' => $attachment['name'] ?? basename($file_path),
                'mime' => $attachment['mime'] ?? $this->get_mime_type($file_path),
                'size' => $file_size
            ];
        }

        return ['valid' => true, 'attachments' => $validated_attachments];
    }


    /**
     * Determining the MIME type of the file
     * 
     * @param string $file_path File path
     * @return string
     */
    private function get_mime_type(string $file_path): string {
        // Using finfo if available
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mime_type = finfo_file($finfo, $file_path);
                finfo_close($finfo);
                if ($mime_type !== false) {
                    return $mime_type;
                }
            }
        }

        // Fallback by the file extension
        $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        
        return match($extension) {
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'txt' => 'text/plain',
            'html' => 'text/html',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            default => 'application/octet-stream'
        };
    }


    /**
     * Processing attachments and preparing headers/body
     * 
     * @param string $body Body
     * @param bool $is_html Is HTML
     * @param array|null $attachments Attachments
     * @param string $headers Headers
     * @return array
     */
    private function process_attachments(string $body, bool $is_html, ?array $attachments, string $headers): array {
        // Validating attachments
        $validation = $this->validate_attachments($attachments);
        if (!$validation['valid']) {
            return ['valid' => false, 'headers' => $headers, 'body' => $body];
        }

        $validated_attachments = $validation['attachments'];

        if (count($validated_attachments) === 0) {
            // No attachments - adding only Content-Type
            $headers .= $is_html ? 'Content-Type: text/html; charset=UTF-8\r\n' : 'Content-Type: text/plain; charset=UTF-8\r\n';
            return ['valid' => true, 'headers' => $headers, 'body' => $body];
        }

        // There are attachments - creating a multipart message
        $boundary = $this->generate_boundary();
        $headers .= 'Content-Type: multipart/mixed; boundary="'.$boundary."\"\r\n";
        $body = $this->build_multipart_body($body, $validated_attachments, $boundary, $is_html);
        
        return ['valid' => true, 'headers' => $headers, 'body' => $body];
    }


    /**
     * Building the multipart body of the message with attachments
     * 
     * @param string $body Body
     * @param array $attachments Attachments
     * @param string $boundary Boundary
     * @param bool $is_html Is HTML
     * @return string
     */
    private function build_multipart_body(string $body, array $attachments, string $boundary, bool $is_html): string {
        $message = '--'.$boundary."\r\n";
        $message .= $is_html ? 'Content-Type: text/html; charset=UTF-8\r\n' : 'Content-Type: text/plain; charset=UTF-8\r\n';
        $message .= 'Content-Transfer-Encoding: 8bit\r\n\r\n';
        $message .= $body . "\r\n\r\n";

        foreach ($attachments as $attachment) {
            if (!isset($attachment['path']) || !file_exists($attachment['path'])) {
                continue;
            }

            $filename = $attachment['name'] ?? basename($attachment['path']);
            $mime_type = $attachment['mime'] ?? 'application/octet-stream';
            $file_raw = file_read($attachment['path']);
            if ($file_raw === false) {
                $this->last_error = 'Failed to read attachment file: '.$attachment['path'];
                continue;
            }
            $file_content = base64_encode($file_raw);

            $message .= '--'.$boundary."\r\n";
            $message .= 'Content-Type: '.$mime_type.'; name="'.$filename."\"\r\n";
            $message .= 'Content-Transfer-Encoding: base64\r\n';
            $message .= 'Content-Disposition: attachment; filename="'.$filename."\"\r\n";
            $message .= chunk_split($file_content) . "\r\n";
        }

        $message .= '--'.$boundary.'--'."\r\n";
        return $message;
    }


    /**
     * Sanitization of the message subject
     * 
     * @param string $subject Subject
     * @return string
     */
    private function sanitize_subject(string $subject): string {
        // Removing potentially dangerous characters for headers
        $subject = str_ireplace(["\r", "\n", "\t"], '', trim($subject));
        return mb_encode_mimeheader($subject, 'UTF-8', 'B');
    }


    /**
     * Rendering the email template
     * 
     * @param string $template Template
     * @param array $data Data
     * @return string
     */
    public function render_template(string $template, array $data = []): string {
        $template_path = _ASSETS . '/mail/'.$template.'.php';
        
        if (!file_exists($template_path)) {
            $this->last_error = 'Email template not found: '.$template;
            return '';
        }

        // Extracting variables into the local scope
        extract($data, EXTR_SKIP);
        
        ob_start();
        include $template_path;
        return ob_get_clean();
    }


    /**
     * Sending email using a template
     * 
     * @param array $params Parameters
     * @return bool
     */
    public function send_with_template(array $params): bool {
        // Validate required parameters
        $required_params = ['to' => 'Missing required parameter: to',
                            'subject' => 'Missing required parameter: subject', 
                            'template' => 'Missing required parameter: template'];
        foreach ($required_params as $param => $error_msg) {
            if (!isset($params[$param]) || empty($params[$param])) {
                $this->last_error = $error_msg;
                return false;
            }
        }
        
        try {
            $body = $this->render_template($params['template'], $params['data'] ?? []);
            
            // Prepare parameters for send method - merge with defaults
            $send_params = array_merge($params, [
                'body' => $body,
                'is_html' => true,
                'attachments' => $params['attachments'] ?? null,
                'from' => $params['from'] ?? null,
                'from_name' => $params['from_name'] ?? null
            ]);
            
            return $this->send($send_params);
        } catch (Exception $e) {
            $this->last_error = 'Error rendering the template: ' . $e->getMessage();
            return false;
        }
    }


    /**
     * Getting the last error
     * 
     * @return string
     */
    public function get_last_error(): string {
        return $this->last_error;
    }


    /**
     * Checking the configuration
     * 
     * @return bool
     */
    public function is_configured(): bool {
        return !empty($this->from) && !empty($this->from_name);
    }


    /**
     * Testing the connection
     * 
     * @return bool
     */
    public function test_connection(): bool {
        if ($this->driver === 'smtp') {
            $socket = $this->create_smtp_connection();
            if ($socket) {
                $this->smtp_command($socket, 'QUIT', '221');
                fclose($socket);
                return true;
            }
            return false;
        }
        
        // For other drivers simply check the configuration
        return $this->is_configured();
    }


    /**
     * Getting information about the limitations of attachments
     * 
     * @return array
     */
    public function get_attachment_limits(): array {
        return [
            'max_size_bytes' => $this->max_attachment_size,
            'max_size_mb' => round($this->max_attachment_size / 1048576, 1),
            'max_count' => $this->max_attachments_count,
            'max_total_size_mb' => round(($this->max_attachment_size * $this->max_attachments_count) / 1048576, 1)
        ];
    }
}