<?php
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
 * Trait for extending the DbBackuper class
 */
trait DbBackuperExtends {
     

	/**
	 * Ensure backup directory exists and is properly secured
	 * @return void
	 */
	private function ensure_backup_directory(): void {
		if(!is_dir($this->backup_path)) {
			if(!mkdir($this->backup_path, 0755, true)) {
				throw new Exception('Cannot create backup directory: ' . $this->backup_path);
			}
		}
		
		// Create index.php for security
		$index_file = $this->backup_path . DIRECTORY_SEPARATOR . 'index.php';
		if(!file_exists($index_file)) {
			file_put_contents($index_file, '<?php http_response_code(403); exit(\'403:Access denied\'); ?>');
		}
		
		// Create .htaccess for additional security
		$htaccess_file = $this->backup_path . DIRECTORY_SEPARATOR . '.htaccess';
		if(!file_exists($htaccess_file)) {
			$htaccess_content = $this->generate_htaccess_content();
			file_put_contents($htaccess_file, $htaccess_content);
		}
	}


	/**
	 * Generate .htaccess content for backup directory security
	 * @return string .htaccess content
	 */
	private function generate_htaccess_content(): string {
		$domain = env('HTTP_HOST') ?? 'localhost';
		
		return <<<HTACCESS
# RooCMS Database Backups Security
# Deny all direct access to backup files

# Deny access to all files in this directory
<Files "*">
    Order Allow,Deny
    Deny from all
</Files>

# Specifically deny backup file extensions
<FilesMatch "\.(sql|sql\.gz|sql\.bz2|dump|backup)$">
    Order Allow,Deny 
    Deny from all
</FilesMatch>

# Deny directory browsing
Options -Indexes

# Prevent access to hidden files (like .htaccess itself)
<Files ".*">
    Order Allow,Deny
    Deny from all
</Files>

# Custom error page for unauthorized access
ErrorDocument 403 /err.php

# Additional security headers
<IfModule mod_headers.c>
    Header always set X-Robots-Tag "noindex, nofollow, nosnippet, noarchive"
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
</IfModule>

# Disable execution of any scripts
<IfModule mod_php8.c>
    php_flag engine off
</IfModule>

# Prevent hotlinking
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTP_REFERER} !^$
    RewriteCond %{HTTP_REFERER} !^https?://(www\.)?{$domain} [NC]
    RewriteRule \.(sql|sql\.gz|sql\.bz2|dump|backup)$ - [F,L]
</IfModule>
HTACCESS;
	}
}