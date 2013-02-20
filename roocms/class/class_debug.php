<?php
/**
* @package		RooCMS
* @subpackage	Engine RooCMS classes
* @subpackage	Debug Class
* @author		alex Roosso
* @copyright	2010-2014 (c) RooCMS
* @link			http://www.roocms.com
* @version		1.2.1
* @since		$date$
* @license		http://www.gnu.org/licenses/gpl-2.0.html
*
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.
*
*   Данное программное обеспечение является свободным и распространяется
*   по лицензии Фонда Свободного ПО - GNU General Public License версия 2.
*   При любом использовании данного ПО вы должны соблюдать все условия
*   лицензии.
*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


/**
* Инициализируем класс отладки
*
* @var Debug
*/
$debug = new Debug;

/**
* Класс отладки
*/
class Debug {

	# vars
	public $debug			= false;		# [bool] 	on/off debug mode
	public $show_debug 		= false;		# [bool] 	show full debug text
	public $debug_info 		= "";			# [text] 	buffer for debug info text
	public $dev_mode		= false;		# [bool]	developer mode on/off [пока что не реализовано]
	public $phpextensions	= array();			# [array]	Список установленных PHP расширений
	public $nophpextensions = array();			# [array]	Список отсуствующих PHP приложений, требуемых для RooCMS

	# requirement
	private $reqphpext		= array("Core",		# [array]	Обязательные php расширения для работы RooCMS
									"calendar",
									"date",
									"pcre",
									"session",
									"xml",
									"gd",
									"mbstring",
									"standard",
									"SimpleXML",
									"apache2handler",
									"mysql");


	/**
	* Запускаем класс
	*
	*/
	function __construct() {

		# устанавливаем перехватчик ошибок
		@set_error_handler(array('Debug','debug_error'), E_ALL);

		# Если включен режим разработчика, автоматически включаем режим отладки
		if($this->dev_mode) $this->debug =& $this->dev_mode;

        # Для админа всегода показываем ошибки и замеряем время выполнения RooCMS
		if($this->debug || defined('ACP') || defined('INSTALL')) {
			# start Debug timer
			$this->startTimer();

			# Проверяем наличие требуемых PHP расширений
			$this->check_phpextensions();

			# try show error
			$this->error_report(true);
		}
		else $this->error_report(false);
	}


	/**
	* Запускаем таймер подсчета времени выполнения скрипта
	*
	*/
    public function startTimer() {

		global $starttime;

        $mtime = microtime ();
        $mtime = explode (' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $starttime = $mtime;
    }


	/**
	* Останавливаем таймер подсчета времени выполнения скрипта
	*
	*/
    public function endTimer() {

		global $starttime;

        $mtime = microtime ();
        $mtime = explode (' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $endtime = $mtime;
        $totaltime = round (($endtime - $starttime), 4);

		return $totaltime;
    }


	/**
	* Проверяем наличие требуемых PHP расширений
	*
	*/
	private function check_phpextensions() {

		$this->phpextensions = get_loaded_extensions();

		foreach($this->reqphpext AS $k=>$v) {
			if(!in_array($v,$this->phpextensions)) $this->nophpextensions[] = $v;
		}
	}


	/**
	* Перехватчик системных ошибок
	*
	* @param mixed $errno - Номер ошибки
	* @param mixed $msg   - Сообщение об ошибке
	* @param mixed $file  - Имя файла с ошбкой
	* @param mixed $line  - Номер строки с ошибкой
	*/
	public static function debug_error($errno, $msg, $file, $line) {

        static $use = 0;

		if(error_reporting() == 0) return;

		if($use == 0) {
			echo "
			<style>
			.system_error {
				padding: 5px;
				top: 0; left: 0; z-index: 100;
				font-family: Tahoma; font-size: 10px; font-weight: bold; color: #dd0000; text-align: left;
				width: 99%;
				background-color: #ffeeee;
				border: 1px dashed red;
				position: static;
			}
			</style>";
		}

		echo "<div class=\"system_error\">
		<font color=#990000>ВНИМАНИЕ ОШИБКА: </font> <b>{$errno}</b>
		<br /><font color=#770000>Cтрока:</font> <b>{$line}</b> <font color=#770000>В файле:</font> <b>{$file}</b>
		<br /><font color=#770000>Сообщение:</font> <b>{$msg}</b>
		</div>\n";


        // Записываем ошибку в файл
		$subj = "";
        $f = file(_LOGS."/errors.log");
        foreach($f AS $v) {
        	$subj .= $v;
        }

		$subj .= date("d.m.Y H:i:s")."\t|\tPHPError\t|\t(".$errno.") ".$msg." (Строка: ".$line." в файле ".$file.")\r\n";
        $ferror = _LOGS."/errors.log";

		$f = fopen($ferror, "w+");
		if(is_writable($ferror)) {
			fwrite($f, $subj);
		}
		fclose($f);

		$use++;
	}


    /**
    * Функция включения/отключения протоколирования ошибок
    *
    * @param boolean $show - флаг включает/выключает ошибки
    */
	private static function error_report($show = false) {
		if($show) {
			error_reporting(E_ALL);					#8191
			ini_set("display_startup_errors",	1);
			ini_set("display_errors",			1);
			ini_set("html_errors",				1);
			ini_set("report_memleaks",			1);
			ini_set("track_errors",				1);
			ini_set("log_errors",				1);
			ini_set("log_errors_max_len",		2048);
			ini_set("ignore_repeated_errors",	1);
			ini_set("ignore_repeated_source",	1);
			ini_set("error_log",				_LOGS."/php_error.log");
		}
		else {
			error_reporting(0);
		}
	}

}



/**
* DEBUG
*
* @param mixed $var     - Переменная для отладки
* @param mixed $expand  - Флаг развернутого вида
* @return mixed - Функция выводит на экран дамп переменной $var
*/
function debug($var, $expand=false) {

	static $use = 0;

	$b = $use*100;
	echo <<<HTML
			<style>
				#debug{$use} {position: absolute;z-index: 100;bottom: {$use}px;left: 9%;padding: 6px;margin: 0px;background-color: #ffffee;border: 1px solid #ccc;overflow: auto;height: 100px;max-width: 90%;}
				#debug{$use}:hover {z-index: 101;height: 99%;}
			</style>
			<div id="debug{$use}" class="shadow">
			<legend style="font-weight: bold;">Debug #{$use}</legend>
			<pre>\n
HTML;
	print_r($var);
	echo "</pre></div>\n";

	if($expand == true) {
		echo "<fieldset><legend>Var Dump</legend><pre>\n";
		var_dump($var);
		echo "</pre></fieldset>\n";

		echo "<fieldset><legend>Backtrace function</legend><pre>\n";
		print_r(debug_backtrace());
		echo "</pre></fieldset>\n";

		echo "<fieldset><legend>Backtrace</legend><pre>\n";
		debug_print_backtrace();
		echo "</pre></fieldset>\n";
	}

	$use++;
}


?>
