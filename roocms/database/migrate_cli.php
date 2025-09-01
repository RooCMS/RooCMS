<?php
/**
 * RooCMS - Database Migration CLI Tool
 * © 2010-2025 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      https://www.roocms.com
 * @license   https://www.gnu.org/licenses/gpl-3.0.html
 * 
 * You should have received a copy of the GNU General Public License v3
 * along with this program. If not, see https://www.gnu.org/licenses/
 */

/**
 * ATTENTION!
 * If something... I haven't tested this yet, so there may be errors
 */

// Set a constant to protect against direct access
define('RooCMS', true);

// Set the base paths for the CLI
define('_SITEROOT', dirname(__DIR__, 2));

// Connect the necessary classes
require_once _SITEROOT . '/roocms/config/config.php';
require_once _SITEROOT . '/roocms/config/defines.php'; 
require_once _CLASS . '/roocms/class/class_db.php';
require_once _CLASS . '/roocms/class/class_dbQueryBuilder.php';
require_once _CLASS . '/roocms/class/trait_dbExtends.php';
require_once _CLASS . '/roocms/class/trait_debugLog.php';
require_once _CLASS . '/roocms/class/class_dbMigrator.php';

/**
 * CLI interface for managing database migrations
 */
class MigrationCLI {

	private DbMigrator $migrator;
	private array $commands = [
		'migrate' => 'Execute all pending migrations',
		'rollback' => 'Rollback the last migrations (default: 1)',
		'status' => 'Show the status of migrations',
		'help' => 'Show the help for the commands',
		'version' => 'Show the version of the migrator',
	];

	public function __construct() {
		try {
			// Check the presence of necessary constants
			$this->checkRequiredConstants();
			
			// Create a connection to the database
			$db = new Db();
			$this->migrator = new DbMigrator($db);
			
			echo "🚀 RooCMS Database Migration Tool v1.0.0-alpha\n";
			echo "Connection to the database: ✅\n\n";
			
		} catch (Exception $e) {
			echo "❌ Error connecting to the database: " . $e->getMessage() . "\n";
			exit(1);
		}
	}


	/**
	 * Check the presence of necessary constants
	 */
	private function checkRequiredConstants(): void {
		$required_constants = [
			'TABLE_MIGRATIONS',
			'TABLE_CONFIG_PARTS', 
			'TABLE_CONFIG_SETTINGS'
		];

		$missing = [];
		foreach ($required_constants as $constant) {
			if (!defined($constant)) {
				$missing[] = $constant;
			}
		}

		if (!empty($missing)) {
			echo "❌ The necessary constants are missing in defines.php:\n";
			foreach ($missing as $constant) {
				echo "   • {$constant}\n";
			}
			echo "\n💡 Check the file roocms/config/defines.php\n";
			exit(1);
		}
	}


	/**
	 * Main method to run the CLI
	 */
	public function run(): void {
		global $argv;
		
		$command = $argv[1] ?? 'help';
		$arguments = array_slice($argv, 2);

		switch ($command) {
			case 'migrate':
				$this->runMigrate();
				break;

			case 'rollback':
				$steps = (int)($arguments[0] ?? 1);
				$this->runRollback($steps);
				break;

			case 'status':
				$this->showStatus();
				break;

			case 'version':
				$this->showVersion();
				break;

			case 'help':
			default:
				$this->showHelp();
				break;
		}
	}


	/**
	 * Executing migrations
	 */
	private function runMigrate(): void {
		echo "📊 Checking for pending migrations...\n";
		
		$status = $this->migrator->status();
		
		if ($status['pending'] === 0) {
			echo "✅ All migrations have already been executed!\n";
			return;
		}

		echo "📋 Found pending migrations: {$status['pending']}\n";
		echo "📝 List of migrations to execute:\n";
		
		foreach ($status['pending_list'] as $migration) {
			echo "   • {$migration}\n";
		}

		echo "\n🔄 Executing migrations...\n";
		
		$executed = $this->migrator->migrate();
		
		echo "\n✅ Executed migrations: " . count($executed) . "\n";
		
		if (count($executed) > 0) {
			echo "📊 Updated status:\n";
			$this->showStatus(false);
		}
	}


	/**
	 * Rollback migrations
	 */
	private function runRollback(int $steps = 1): void {
		echo "🔄 Rollback migrations (number of steps: {$steps})...\n";
		
		$status = $this->migrator->status();
		
		if ($status['executed'] === 0) {
			echo "⚠️  No executed migrations for rollback!\n";
			return;
		}

		if ($steps > $status['executed']) {
			echo "⚠️  Requested {$steps} steps, but only {$status['executed']} migrations were executed.\n";
			$steps = $status['executed'];
			echo "📝 Will rollback {$steps} migrations.\n";
		}

		$rolled_back = $this->migrator->rollback($steps);
		
		echo "\n✅ Rollback completed for " . count($rolled_back) . " migrations\n";
		
		if (count($rolled_back) > 0) {
			echo "📊 Updated status:\n";
			$this->showStatus(false);
		}
	}


	/**
	 * Show the status of migrations
	 */
	private function showStatus(bool $show_header = true): void {
		if ($show_header) {
			echo "📊 Status of database migrations\n";
			echo str_repeat("=", 50) . "\n";
		}

		$status = $this->migrator->status();
		
		echo "📈 Total migrations: {$status['total']}\n";
		echo "✅ Executed: {$status['executed']}\n";
		echo "⏳ Pending: {$status['pending']}\n\n";

		if (count($status['executed_list']) > 0) {
			echo "✅ Executed migrations:\n";
			foreach ($status['executed_list'] as $migration) {
				echo "   • {$migration}\n";
			}
			echo "\n";
		}

		if (count($status['pending_list']) > 0) {
			echo "⏳ Pending:\n";
			foreach ($status['pending_list'] as $migration) {
				echo "   • {$migration}\n";
			}
			echo "\n";
		}
	}


	/**
	 * Show the help
	 */
	private function showHelp(): void {
		echo "📚 Help for the commands Migration Tool\n";
		echo str_repeat("=", 50) . "\n\n";

		echo "Usage: php migrate_cli.php [command] [arguments]\n\n";
		
		echo "Available commands:\n";
		foreach ($this->commands as $command => $description) {
			echo sprintf("  %-12s %s\n", $command, $description);
		}
		
		echo "\nExamples:\n";
		echo "  php migrate_cli.php migrate           # Execute all pending migrations\n";
		echo "  php migrate_cli.php rollback          # Rollback the last migration\n";
		echo "  php migrate_cli.php rollback 3        # Rollback the last 3 migrations\n";
		echo "  php migrate_cli.php status            # Show the status of migrations\n\n";
		
		echo "💡 Tip: Always create a backup of the database before executing migrations!\n";
	}


	/**
	 * Show the version
	 */
	private function showVersion(): void {
		echo "🎯 RooCMS Database Migration Tool\n";
		echo "Version: 1.0.0 alpha\n";
		echo "Author: alex Roosso <info@roocms.com>\n";
		echo "Repository: https://github.com/roocms/roocms\n";
		echo "License: GNU GPL v3\n\n";
		
		echo "💻 Supported databases:\n";
		echo "   • MySQL / MariaDB\n";
		echo "   • PostgreSQL\n";
		echo "   • Firebird\n\n";
		
		echo "🌐 More: https://www.roocms.com\n";
	}
}


// Check if the script is being run from the command line
if (php_sapi_name() !== 'cli') {
	echo "This script is intended to be run from the command line\n";
	exit(1);
}

// Run the CLI interface
try {
	$cli = new MigrationCLI();
	$cli->run();
} catch (Exception $e) {
	echo "❌ Critical error: " . $e->getMessage() . "\n";
	exit(1);
}
