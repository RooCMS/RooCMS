<?php
/**
* @package      RooCMS
* @subpackage	Engine RooCMS classes
* @subpackage	Parser Class [extends: Text]
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.1
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*   RooCMS - Russian free content managment system
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


class ParserText {

	/* ####################################################
	 * Парсим ББКод (BBCode)
	 * Функция в разработке
	*/
	public function bbcode($text) {

		# [blackquote]
		$text = str_ireplace("[blockquote]", "<blockquote>", $text, $tag_bq_o);
		$text = str_ireplace("[/blockquote]", "</blockquote>", $text, $tag_bq_c);

		# [b]
		$text = str_ireplace("[b]", "<b>", $text, $tag_b_o);
		$text = str_ireplace("[/b]", "</b>", $text, $tag_b_c);
		# [i]
		$text = str_ireplace("[i]", "<i>", $text, $tag_i_o);
		$text = str_ireplace("[/i]", "</i>", $text, $tag_i_c);
		# [u]
		$text = str_ireplace("[u]", "<u>", $text, $tag_u_o);
		$text = str_ireplace("[/u]", "</u>", $text, $tag_u_c);

		#font

		#link

		$text = $this->br($text);

		return $text;
	}


	/* ####################################################
	 * Парсим хтмл (HTML)
	 * Функция принимает данные обработанный функцией htmlspecialchars() и возвращает им обратное значение.
	 * 	$text	-	[text]	Текстовый буфер, который надлежит отпарсить
	 */
	public function html($text) {

		$text = htmlspecialchars_decode($text);
 		$text = strtr($text, array(
			'&#123;'	=> '{', 	//	{
			'&#125;'	=> '}', 	//	}
			'&#39;'		=> '\'', 	//	" [quot]
			'&#36;'		=> '$'
		));

		return $text;
	}


	/* ####################################################
	 * Парсим данные
	 * Функция принимает данные обработанный функцией htmlspecialchars() и вычищает все специфические символы.
	 * 	$text	-	[text]	Текстовый буфер, который надлежит отпарсить
	 */
	public function clearhtml($text) {

 		$text = strtr($text, array(
			'&lt;'		=> '', 		//	< [lt]
			'&gt;'		=> '', 		//	> [rt]
			'&#123;'	=> '', 		//	{
			'&#125;'	=> '', 		//	}
			'&#39;'		=> '', 		//	" [quot]
			'&quot;'	=> '', 		//	" [quot]
			'&amp;'		=> '',		//	& [amp]
			'&#36;'		=> ''
		));

		return $text;
	}


	/* ####################################################
	 * Парсим данные
	 * Функция заменяет перевод каретки \n на хтмл тег <br />
	 * 	$text	-	[text]	Текстовый буфер, который надлежит отпарсить
	 */
	public function br($text) {
		$text = nl2br($text);
		return $text;
	}


	/**
	* Функция транслитерации русских символов в английские
	*
	* @param mixed $txt - строк для траслитирования (?) // не уверен я в этом слове...
	* @param mixed $case - указываем регистр [default: false|lower|upper]
	* @return вернет транслитированную (?) строку
	*/
	public function transliterate($txt, $case=false) {

		$rus = Array('А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К',
		'Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ',
		'Ь','Ы','Ъ','Э','Ю','Я','а','б','в','г','д','е','ё','ж','з',
		'и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц',
		'ч','ш','щ','ь','ы','ъ','э','ю','я');

		$eng = Array('A','B','V','G','D','E','Yo','J','Z','I','Y','K',
		'L','M','N','O','P','R','S','T','U','F','H','C','Ch','Sh','Csh',
		'','i','','E','Yu','Ya','a','b','v','g','d','e','yo','j','z',
		'i','y','k','l','m','n','o','p','r','s','t','u','f','h','c',
		'ch','sh','csh','','i','','e','yu','ya');

		$txt = str_replace($rus,$eng,trim($txt));

		# case
		if($case && $case == "lower")		$txt = mb_strtolower($txt);
		elseif($case && $case == "upper")	$txt = mb_strtoupper($txt);

		return $txt;
	}


	/**
	* Парсим текст на предмет ссылок и делаем их активными
	* Временная функция из старой версии.
	* Будет заменена.
	*
	* @param mixed $text
	*/
	public function anchors($text) {

        	$pattern = "#(^|\s|)((http(s?)://)|(www\.))(\w+[^\s\)\<]+)#i";

		if (preg_match_all($pattern, $text, $matches)) {

			for($i=0;$i<sizeof($matches['0']);$i++) {

				$period = '';

				if (preg_match("|\.$|", $matches['6'][$i])) {

					$period = '.';
					$matches['6'][$i] = mb_substr($matches['6'][$i], 0, -1);
				}

				$text = str_ireplace($matches['0'][$i],
						     $matches['1'][$i].'<a href="http'.
						     $matches['4'][$i].'://'.
						     $matches['5'][$i].
						     $matches['6'][$i].'" target="_blank" rel="nofollow">http'.
						     $matches['4'][$i].'://'.
						     $matches['5'][$i].
						     $matches['6'][$i].'</a>'.
						     $period, $text);
			}
		}

		return $text;
	}


	/* ####################################################
	 * Парсим данные
	 * Функция обрабатывает строку, оставляя в ней только цифры
	 * 	$n	-	[string]	Текстовая строка, которую требуется отпарсить
	 */
	public function only_numbers($n) {
		return preg_replace("/[^0-9]+/","",$n);
	}


	/**
	 * Преобразовываем текст из ISO8859-5 в Unicode
	 * Использовать перед запуском imagettftext
	 * [Морально устаревшая функция после перехода на utf8]
	 */
	protected function tounicode($text, $from="w") {
		$text = convert_cyr_string($text, $from, "i");
		$uni  = "";
		for($i = 0, $len = mb_strlen($text, 'utf8'); $i < $len; $i++) {
			$char = $text{$i};
			$code = ord($char);
			$uni .= ($code > 175)? "&#" . (1040 + ($code - 176)) . ";" : $char;
		}

		return $uni;
	}
}

?>