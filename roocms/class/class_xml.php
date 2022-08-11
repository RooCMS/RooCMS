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
 * Class ParserXML
 */
class XML {

	# object output data
	public $data;


	# params
	protected $file 	= "";		# target xml file
	private $xml_parser	= false;	# xml parser
	private $xml_string	= "";		# xml data buffer for parsing



	/**
	 * Parse XML data file
	 *
	 * @param bool|string $file
	 * @param bool        $callback
	 *
	 * @return SimpleXMLElement|null
	 */
	public function parse($file = false, bool $callback = false) {

		if(!$file) {
			$file = $this->file;
		}

		$this->xml_string = "";

		$this->xml_parser = xml_parser_create();

		# set options
		xml_parser_set_option($this->xml_parser,XML_OPTION_SKIP_WHITE,1);
		xml_parser_set_option($this->xml_parser,XML_OPTION_CASE_FOLDING,1);

		# set handlers
		xml_set_element_handler($this->xml_parser, array('ParserXML','start_el'), array('ParserXML','end_el'));
		xml_set_character_data_handler($this->xml_parser, array('ParserXML','data_el'));

		xml_set_default_handler($this->xml_parser, array('ParserXML','data_el'));

		//xml_set_processing_instruction_handler($this->xml_parser, array('ParserXML','xmlpi'));
		//xml_set_external_entity_ref_handler($this->xml_parser, array('ParserXML','eer'));

		# read
		$xml_file = file($file);
		foreach($xml_file AS $value) {
			xml_parse($this->xml_parser,$value);
		}
		xml_parser_free($this->xml_parser);

		# i-i-h-h-h-a-a-a
		if($callback) {
			return simplexml_load_string($this->xml_string);
		}
		else {
			$this->data = simplexml_load_string($this->xml_string);
		}
	}


	/**
	 * Function start element
	 *
	 * @param $parser
	 * @param $name
	 * @param $attr
	 */
	protected function start_el($parser, $name, $attr) {

		$this->xml_string .= "<".mb_strtolower($name);
		foreach($attr AS $key=>$value) {
			$this->xml_string .= ' '.mb_strtolower($key).'="'.mb_strtolower($value).'"';
		}
		$this->xml_string .= ">";
	}


	/**
	 * Function data element
	 *
	 * @param $parser
	 * @param $data
	 */
	protected function data_el($parser, $data) {
		$this->xml_string .= htmlspecialchars($data);
	}


	/**
	 * Function end element
	 *
	 * @param $parser
	 * @param $name
	 */
	protected function end_el($parser, $name) {
		$this->xml_string .= "</".mb_strtolower($name).">";
	}


	/**
	 * Function entity element
	 *
	 * @param $parser
	 * @param $enname
	 * @param $base
	 * @param $sid
	 * @param $pid
	 */
	protected function xmlpi($parser, $enname, $base, $sid, $pid) {
		$this->xml_string .= "enname:".$enname;
		$this->xml_string .= "base:".$base;
		$this->xml_string .= "sid:".$sid;
		$this->xml_string .= "pid:".$pid;
	}


	/**
	 * Function end element
	 *
	 * @param $parser
	 * @param $target
	 * @param $data
	 */
	protected function xmlphp($parser, $target, $data) {
		$this->xml_string .= "<!".$target."!>";
		$this->xml_string .= "<!".$data."!>";
	}
}
