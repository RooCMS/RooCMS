<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2023 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      http://www.roocms.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
 */


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class ParserText
 */
class ParserText {

	/**
	 * Parse BBCode
	 *  ... in progress ...
	 *
	 * @param $text
	 *
	 * @return mixed
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
		return $this->br($text);
	}


	/**
	 * Parse HTML
	 * Функция принимает данные обработанный функцией htmlspecialchars() и возвращает им обратное значение.
	 *
	 * @param string|array $text  - text for parsing
	 *
	 * @return string|array
	 */
	public function html($text) {

		if(is_array($text)) {
			foreach($text AS $k=>$v) {
				$text[$k] = $this->html($v);
			}
		}
		else {
			$text = htmlspecialchars_decode($text);
			$text = str_ireplace(
				array('&#123;','&#125;','&#39;','&#36;','&#036;','&#33;','&#124;','...'),
				array('{','}','\'','$','$','!','|','&hellip;'),
				$text
			);
		}

		return $text;
	}


	/**
	 * Парсим данные
	 * Функция принимает данные обработанный функцией htmlspecialchars() и вычищает все специфические символы.
	 *
	 * @param string $text - Текстовый буфер, который надлежит отпарсить
	 *
	 * @return string
	 */
	public function clearhtml(string $text) {

		$text = strip_tags($text);
 		$text = str_ireplace(array('&lt;','&gt;','&#123;','&#125;','&#39;','&quot;','&amp;','&#36;'), '', $text);

		return $text;
	}


	/**
	 * Функция заменяет перевод каретки \n на хтмл тег <br />
	 *
	 * @param string $text - Текстовый буфер, который надлежит отпарсить
	 *
	 * @return string
	 */
	public function br(string $text) {
		return nl2br($text);
	}


	// TODO: Данная функция не идеальна. Ее нужно переписывать так, что бы
	// учитывались языковые контрукции. Сейчас она русского на англ справляется. А вот обратно...
	/**
	 * Функция транслитерации русских символов в английские
	 *
	 * @param mixed  $txt  - строк для траслитирования (?) // не уверен я в этом слове...
	 * @param mixed  $case - указываем регистр [default: false|lower|upper|title]
	 *
	 * @param string $from - Язык который транслитируем
	 * @param string $to   - Язык в который транслитируем
	 *
	 * @return mixed|string вернет транслитированную (?) строку
	 */
	public function transliterate($txt, $case=false, string $from="rus", string $to="eng") {

		# подгружаем алфавиты
		require _LIB."/abc.php";

		$txt = str_ireplace($abc[$from],$abc[$to],trim($txt));

		# case
		if($case) {
			if($case == "lower") {
				$txt = mb_strtolower($txt);
			}
			elseif($case == "upper") {
				$txt = mb_strtoupper($txt);
			}
			elseif($case == "title") {
				$txt = mb_convert_case($txt, MB_CASE_TITLE);
			}
		}

		return $txt;
	}


	/**
	 * Парсим текст на предмет ссылок и делаем их активными
	 * Временная функция из старой версии.
	 * Будет заменена.
	 *
	 * @param mixed $text
	 *
	 * @return string ссылка в виде nofollow
	 */
	public function anchors($text) {

        	$pattern = "#(^|\s|)((http(s?)://)|(www\.))(\w+[^\s\)\<]+)#i";

		if (preg_match_all($pattern, $text, $matches)) {

			$cmatch = sizeof($matches['0']);
			for($i=0; $i < $cmatch ;$i++) {

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


	/**
	 * Функция обрабатывает строку, оставляя в ней только цифры
	 *
	 * @param string $n - Текстовая строка, которую требуется отпарсить
	 *
	 * @return mixed
	 */
	public function only_numbers(string $n) {
		return preg_replace("/[^0-9]+/","",$n);
	}


	/**
	 * Преобразовываем текст из ISO8859-5 в Unicode
	 * Использовать перед запуском imagettftext
	 * [Морально устаревшая функция после перехода на utf8]
	 *
	 * @param string $text
	 * @param string $from
	 *
	 * @return string
	 */
	protected function tounicode(string $text, string $from="w") {
		$text = convert_cyr_string($text, $from, "i");
		$uni  = "";
		for($i = 0, $len = mb_strlen($text, 'utf8'); $i < $len; $i++) {
			$char = $text{$i};
			$code = ord($char);
			$uni .= ($code > 175)? "&#" . (1040 + ($code - 176)) . ";" : $char;
		}

		return $uni;
	}


	/**
	 * Корректируем имена алиасов, что бы избегать ошибок
	 *
	 * @param string|int $var - Значение переменной
	 *
	 * @return string
	 */
	public function correct_aliases($var) {

		$var = $this->transliterate($var,"lower");

		$var = preg_replace(array('(\s\s+)','(\-\-+)','(__+)','([^a-zA-Z0-9\-_])'), array('-','-','_',''), $var);

		if(is_numeric($var) || $var == "") {
			$var .= randcode(3, "abcdefghijklmnopqrstuvwxyz");
		}

		return $var;
	}
}
