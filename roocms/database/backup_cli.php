<?php
/**
 * RooCMS - Database Backup CLI Tool
 * Command line interface for database backup and restore operations
 * 
 * © 2010-2025 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      https://www.roocms.com
 * @license   https://www.gnu.org/licenses/gpl-3.0.html
 * 
 * You should have received a copy of the GNU General Public License v3
 * along with this program. If not, see https://www.gnu.org/licenses/
 * 
 * Usage:
 * php backup_cli.php create [options]
 * php backup_cli.php restore <filename> [options]
 * php backup_cli.php list
 * php backup_cli.php delete <filename>
 */

// Define RooCMS constant for security
define('RooCMS', true);

// Set working directory
$siteroot = dirname(__DIR__, 2);
define('_SITEROOT', $siteroot);

// Include configuration first
$config_files = [
    $siteroot . '/roocms/config/config.php',
    $siteroot . '/roocms/config/defines.php'
];

foreach($config_files as $config) {
    if(file_exists($config)) {
        require_once $config;
    }
}

// Include helpers
require_once _ROOCMS . '/helpers/functions.php';

// Include classes manually
require_once _MODULES . '/db/trait_debugLog.php';
require_once _MODULES . '/db/trait_dbExtends.php';
require_once _MODULES . '/db/class_dbConnect.php';
require_once _MODULES . '/db/class_dbQueryBuilder.php';
require_once _MODULES . '/db/class_db.php';
require_once _MODULES . '/db/class_dbBackuper.php';

/**
 * Database Backup CLI Handler
 */
class BackupCli {

	private DbBackuper $backup;
	private array $commands = ['create', 'restore', 'list', 'delete', 'help'];


	/**
	 * Constructor
	 */
	public function __construct() {
		// Initialize database connection
		$db = new Db();
		$this->backup = new DbBackuper($db);
	}


	/**
	 * Run CLI command
	 *
	 * @param array $argv Command line arguments
	 * @return void
	 */
	public function run(array $argv): void {
		if(count($argv) < 2) {
			$this->show_help();
			return;
		}

		$command = $argv[1];

		if(!in_array($command, $this->commands)) {
			$this->output_error("Unknown command: {$command}");
			$this->show_help();
			return;
		}

		try {
			match($command) {
				'create' => $this->handle_create_command($argv),
				'restore' => $this->handle_restore_command($argv),
				'list' => $this->handle_list_command(),
				'delete' => $this->handle_delete_command($argv),
				'help' => $this->show_help(),
				default => $this->show_help()
			};
		} catch(Exception $e) {
			$this->output_error("Error: " . $e->getMessage());
			exit(1);
		}
	}


	/**
	 * Handle create backup command
	 *
	 * @param array $argv Command arguments
	 * @return void
	 */
	private function handle_create_command(array $argv): void {
		$options = $this->parse_create_options($argv);

		$this->output_info("Creating database backup...");
		$this->output_info("Options: " . json_encode($options, JSON_UNESCAPED_UNICODE));

		$result = $this->backup->create_backup($options);

		if($result['success']) {
			$this->output_success("Backup created successfully!");
			$this->output_info("Filename: {$result['filename']}");
			$this->output_info("Size: {$result['size_human']}");
			$this->output_info("Execution time: {$result['execution_time']}s");
			$this->output_info("Compressed: " . ($result['compressed'] ? 'Yes' : 'No'));
		} else {
			$this->output_error("Backup creation failed!");
		}
	}


	/**
	 * Handle restore backup command
	 *
	 * @param array $argv Command arguments
	 * @return void
	 */
	private function handle_restore_command(array $argv): void {
		if(count($argv) < 3) {
			$this->output_error("Usage: php backup_cli.php restore <filename> [options]");
			return;
		}

		$filename = $argv[2];
		$options = $this->parse_restore_options($argv);

		$this->output_warning("WARNING: This will modify your database!");
		$this->output_info("Backup file: {$filename}");
		$this->output_info("Options: " . json_encode($options, JSON_UNESCAPED_UNICODE));

		// Ask for confirmation in interactive mode
		if($this->is_interactive()) {
			$this->output_question("Are you sure you want to continue? (y/N): ");
			$confirmation = trim(fgets(STDIN));
			if(strtolower($confirmation) !== 'y') {
				$this->output_info("Restore cancelled.");
				return;
			}
		}

		$this->output_info("Restoring database from backup...");
		$result = $this->backup->restore_backup($filename, $options);

		if($result['success']) {
			$this->output_success("Database restored successfully!");
			$this->output_info("Statements executed: {$result['statements_executed']}");
			$this->output_info("Execution time: {$result['execution_time']}s");
			
			if(!empty($result['errors'])) {
				$this->output_warning("Warnings during restore:");
				foreach($result['errors'] as $error) {
					$this->output_warning("  - {$error}");
				}
			}
		} else {
			$this->output_error("Database restore failed!");
		}
	}


	/**
	 * Handle list backups command
	 *
	 * @return void
	 */
	private function handle_list_command(): void {
		$backups = $this->backup->list_backups();

		if(empty($backups)) {
			$this->output_info("No backups found.");
			return;
		}

		$this->output_info("Available backups:");
		$this->output_line(str_repeat("-", 80));
		$this->output_line(sprintf("%-40s %-12s %-20s %s", "Filename", "Size", "Created", "Compressed"));
		$this->output_line(str_repeat("-", 80));

		foreach($backups as $backup) {
			$compressed = $backup['compressed'] ? 'Yes' : 'No';
			$this->output_line(sprintf(
				"%-40s %-12s %-20s %s", 
				$backup['filename'], 
				$backup['size_human'], 
				$backup['created'],
				$compressed
			));
		}

		$this->output_line(str_repeat("-", 80));
		$this->output_info("Total backups: " . count($backups));
	}


	/**
	 * Handle delete backup command
	 *
	 * @param array $argv Command arguments
	 * @return void
	 */
	private function handle_delete_command(array $argv): void {
		if(count($argv) < 3) {
			$this->output_error("Usage: php backup_cli.php delete <filename>");
			return;
		}

		$filename = $argv[2];

		// Ask for confirmation in interactive mode
		if($this->is_interactive()) {
			$this->output_warning("WARNING: This will permanently delete the backup file!");
			$this->output_question("Delete backup '{$filename}'? (y/N): ");
			$confirmation = trim(fgets(STDIN));
			if(strtolower($confirmation) !== 'y') {
				$this->output_info("Deletion cancelled.");
				return;
			}
		}

		$deleted = $this->backup->delete_backup($filename);

		if($deleted) {
			$this->output_success("Backup '{$filename}' deleted successfully!");
		} else {
			$this->output_error("Failed to delete backup '{$filename}' (file not found or permission denied).");
		}
	}


	/**
	 * Parse create command options
	 *
	 * @param array $argv Command arguments
	 * @return array Parsed options
	 */
	private function parse_create_options(array $argv): array {
		$options = [
			'compress' => true,
			'include_data' => true,
			'include_structure' => true,
			'exclude_tables' => [],
			'filename' => null,
			'universal_format' => true
		];

		for($i = 2; $i < count($argv); $i++) {
			$arg = $argv[$i];

			switch($arg) {
				case '--no-compress':
					$options['compress'] = false;
					break;
				case '--no-data':
					$options['include_data'] = false;
					break;
				case '--structure-only':
					$options['include_data'] = false;
					break;
				case '--data-only':
					$options['include_structure'] = false;
					break;
				case '--filename':
					if(isset($argv[$i + 1])) {
						$options['filename'] = $argv[$i + 1];
						$i++; // Skip next argument
					}
					break;
				case '--exclude-tables':
					if(isset($argv[$i + 1])) {
						$options['exclude_tables'] = explode(',', $argv[$i + 1]);
						$i++; // Skip next argument
					}
					break;
			}
		}

		return $options;
	}


	/**
	 * Parse restore command options
	 *
	 * @param array $argv Command arguments
	 * @return array Parsed options
	 */
	private function parse_restore_options(array $argv): array {
		$options = [
			'drop_existing' => false,
			'ignore_errors' => false,
			'batch_size' => 1000
		];

		for($i = 3; $i < count($argv); $i++) {
			$arg = $argv[$i];

			switch($arg) {
				case '--drop-existing':
					$options['drop_existing'] = true;
					break;
				case '--ignore-errors':
					$options['ignore_errors'] = true;
					break;
				case '--batch-size':
					if(isset($argv[$i + 1]) && is_numeric($argv[$i + 1])) {
						$options['batch_size'] = (int)$argv[$i + 1];
						$i++; // Skip next argument
					}
					break;
			}
		}

		return $options;
	}


	/**
	 * Check if running in interactive mode
	 *
	 * @return bool True if interactive
	 */
	private function is_interactive(): bool {
		return php_sapi_name() === 'cli' && function_exists('posix_isatty') && posix_isatty(STDIN);
	}


	/**
	 * Show help information
	 *
	 * @return void
	 */
	private function show_help(): void {
		$this->output_line("RooCMS Database Backup CLI Tool");
		$this->output_line("==============================");
		$this->output_line("");
		$this->output_line("Usage:");
		$this->output_line("  php backup_cli.php <command> [options]");
		$this->output_line("");
		$this->output_line("Commands:");
		$this->output_line("  create              Create a new database backup");
		$this->output_line("  restore <filename>  Restore database from backup");
		$this->output_line("  list                List available backups");
		$this->output_line("  delete <filename>   Delete a backup file");
		$this->output_line("  help                Show this help message");
		$this->output_line("");
		$this->output_line("Create Options:");
		$this->output_line("  --no-compress       Don't compress the backup file");
		$this->output_line("  --no-data           Export structure only");
		$this->output_line("  --structure-only    Export structure only (same as --no-data)");
		$this->output_line("  --data-only         Export data only");
		$this->output_line("  --filename <name>   Custom backup filename");
		$this->output_line("  --exclude-tables <list>  Comma-separated list of tables to exclude");
		$this->output_line("");
		$this->output_line("Restore Options:");
		$this->output_line("  --drop-existing     Drop existing tables before restore");
		$this->output_line("  --ignore-errors     Continue on errors");
		$this->output_line("  --batch-size <size> Batch size for processing (default: 1000)");
		$this->output_line("");
		$this->output_line("Examples:");
		$this->output_line("  php backup_cli.php create");
		$this->output_line("  php backup_cli.php create --no-compress --filename mybackup");
		$this->output_line("  php backup_cli.php create --structure-only");
		$this->output_line("  php backup_cli.php restore backup_database_2025-01-15_14-30-00.sql.gz");
		$this->output_line("  php backup_cli.php restore mybackup.sql --ignore-errors");
		$this->output_line("  php backup_cli.php list");
		$this->output_line("  php backup_cli.php delete old_backup.sql");
	}


	/**
	 * Output colored text to console
	 *
	 * @param string $message Message to output
	 * @param string $color Color code
	 * @return void
	 */
	private function output_colored(string $message, string $color = ''): void {
		if($this->supports_colors()) {
			echo $color . $message . "\033[0m" . PHP_EOL;
		} else {
			echo $message . PHP_EOL;
		}
	}


	/**
	 * Output regular message
	 */
	private function output_line(string $message): void {
		echo $message . PHP_EOL;
	}


	/**
	 * Output info message
	 */
	private function output_info(string $message): void {
		$this->output_colored("ℹ " . $message, "\033[36m"); // Cyan
	}


	/**
	 * Output success message
	 */
	private function output_success(string $message): void {
		$this->output_colored("✓ " . $message, "\033[32m"); // Green
	}


	/**
	 * Output warning message
	 */
	private function output_warning(string $message): void {
		$this->output_colored("⚠ " . $message, "\033[33m"); // Yellow
	}


	/**
	 * Output error message
	 */
	private function output_error(string $message): void {
		$this->output_colored("✗ " . $message, "\033[31m"); // Red
	}


	/**
	 * Output question message
	 */
	private function output_question(string $message): void {
		echo "\033[35m" . "? " . $message . "\033[0m"; // Magenta, no newline
	}


	/**
	 * Check if terminal supports colors
	 *
	 * @return bool True if colors are supported
	 */
	private function supports_colors(): bool {
		return php_sapi_name() === 'cli' && 
			   (env('TERM') !== false || env('ANSICON') !== false);
	}
}


// Run CLI if called directly
if(basename(__FILE__) === basename(env('SCRIPT_NAME'))) {
	$cli = new BackupCli();
	$cli->run($argv);
}