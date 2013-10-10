<?php
/**
* @package		RooCMS
* @subpackage	Engine RooCMS classes
* @subpackage	Debug Class
* @author		alex Roosso
* @copyright	2010-2014 (c) RooCMS
* @link			http://www.roocms.com
* @version		1.3.2
* @since		$date$
* @license		http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*	RooCMS - Russian free content managment system
*   Copyright (C) 2010-2014 alex Roosso aka alexandr Belov info@roocms.com
*
*   This program is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   This program is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with this program.  If not, see <http://www.gnu.org/licenses/
*
*
*   RooCMS - Русская бесплатная система управления сайтом
*   Copyright (C) 2010-2014 alex Roosso (александр Белов) info@roocms.com
*
*   Это программа является свободным программным обеспечением. Вы можете
*   распространять и/или модифицировать её согласно условиям Стандартной
*   Общественной Лицензии GNU, опубликованной Фондом Свободного Программного
*   Обеспечения, версии 3 или, по Вашему желанию, любой более поздней версии.
*
*   Эта программа распространяется в надежде, что она будет полезной, но БЕЗ
*   ВСЯКИХ ГАРАНТИЙ, в том числе подразумеваемых гарантий ТОВАРНОГО СОСТОЯНИЯ ПРИ
*   ПРОДАЖЕ и ГОДНОСТИ ДЛЯ ОПРЕДЕЛЁННОГО ПРИМЕНЕНИЯ. Смотрите Стандартную
*   Общественную Лицензию GNU для получения дополнительной информации.
*
*   Вы должны были получить копию Стандартной Общественной Лицензии GNU вместе
*   с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/
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
	public	$show_debug 			= false;			# [bool] 	show full debug text
	public	$debug_info 			= "";				# [text] 	buffer for debug info text
	public	$phpextensions			= array();			# [array]	Список установленных PHP расширений
	public	$nophpextensions		= array();			# [array]	Список отсуствующих PHP приложений, требуемых для RooCMS

	private	$starttime				= 0;
	public	$productivity_time		= 0;

	private	$memory_usage			= 0;
	public	$productivity_memory	= 0;
	public	$memory_peak_usage		= 0;

	# requirement
	private $reqphpext				= array("Core",		# [array]	Обязательные php расширения для работы RooCMS
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


        if(!defined('DEBUGMODE')) 	define('DEBUGMODE', true);
        if(!defined('DEVMODE')) 	define('DEVMODE', true);


        # Для админа всегода показываем ошибки и замеряем время выполнения RooCMS
		if(DEBUGMODE || defined('ACP') || defined('INSTALL')) {
			# start Debug timer
			$this->start_productivity();

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
    private function start_productivity() {

    	# timer
        $mtime = STARTTIME;
        $mtime = explode(' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $this->starttime = $mtime;

        # memory
        $this->memory_usage = MEMORYUSAGE;
    }


	/**
	* Останавливаем таймер подсчета времени выполнения скрипта
	*
	*/
    public function end_productivity() {

    	# timer
        $mtime = microtime();
        $mtime = explode(' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $endtime = $mtime;
        $totaltime = round(($endtime - $this->starttime), 4);

		$this->productivity_time = $totaltime;

		# memory
		$this->productivity_memory 	= memory_get_usage() - $this->memory_usage;
		$this->memory_peak_usage 	= memory_get_peak_usage();
    }


	/**
	* Проверяем наличие требуемых PHP расширений
	*
	*/
	private function check_phpextensions() {

		$this->phpextensions = get_loaded_extensions();

		foreach($this->reqphpext AS $k=>$v) {
			if(!in_array($v, $this->phpextensions)) $this->nophpextensions[] = $v;
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
        $file_error = _LOGS."/errors.log";
		$subj = "";
        if(file_exists($file_error)) {
			$f = file($file_error);
	        foreach($f AS $v) {
        		$subj .= $v;
	        }
        }

		$subj .= date("d.m.Y H:i:s")."\t|\tPHPError\t|\t(".$errno.") ".$msg." (Строка: ".$line." в файле ".$file.")\r\n";

		$f = fopen($file_error, "w+");
		if(is_writable($file_error)) {
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