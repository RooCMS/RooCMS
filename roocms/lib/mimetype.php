<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 * Contacts: <info@roocms.com>
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
 */

/**
* @package      RooCMS
* @subpackage	Library
* @author       alex Roosso
* @copyright    2010-2019 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.5
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


$mimetype = [];


/**
* File
*
* @var array
*/
$filetype	= [];
$filetype[]	= array('ext'	=> '7z',	'mime_type'	=> 'application/octet-stream',		'ico'	=> '7z.png');
$filetype[]	= array('ext'	=> 'm3u',	'mime_type'	=> 'audio/mpegurl',			'ico'	=> 'mp3.png');
$filetype[]	= array('ext'	=> 'ttf',	'mime_type'	=> 'application/octet-stream',		'ico'	=> 'ttf.png');
//$filetype[]	= array('ext'	=> 'zip',	'mime_type'	=> 'application/x-zip-compressed',	'ico'	=> 'zip.png');
//$filetype[]	= array('ext'	=> 'zip',	'mime_type'	=> 'application/zip',			'ico'	=> 'zip.png');
$filetype[]	= array('ext'	=> 'zip',	'mime_type'	=> 'application/octet-stream',		'ico'	=> 'zip.png');
//$filetype[]	= array('ext'	=> 'tar.gz',	'mime_type'	=> 'application/octetstream',		'ico'	=> 'tgz.png');
$filetype[]	= array('ext'	=> 'tar.gz',	'mime_type'	=> 'application/gzip',			'ico'	=> 'tgz.png');
$filetype[]	= array('ext'	=> 'rar',	'mime_type'	=> 'application/octet-stream',		'ico'	=> 'rar.png');
$filetype[]	= array('ext'	=> 'js',	'mime_type'	=> 'application/x-javascript',		'ico'	=> 'js.png');
$filetype[]	= array('ext'	=> 'html',	'mime_type'	=> 'text/html',				'ico'	=> 'html.png');
$filetype[]	= array('ext'	=> 'htm',	'mime_type'	=> 'text/html',				'ico'	=> 'htm.png');
$filetype[]	= array('ext'	=> 'css',	'mime_type'	=> 'text/css',				'ico'	=> 'css.png');
$filetype[]	= array('ext'	=> 'xml',	'mime_type'	=> 'text/xml',				'ico'	=> 'xml.png');
$filetype[]	= array('ext'	=> 'ini',	'mime_type'	=> 'application/octet-stream',		'ico'	=> 'ini.png');
$filetype[]	= array('ext'	=> 'swf',	'mime_type'	=> 'application/x-shockwave-flash',	'ico'	=> 'swf.png');
$filetype[]	= array('ext'	=> 'fla',	'mime_type'	=> 'application/octet-stream',		'ico'	=> 'fla.png');
$filetype[]	= array('ext'	=> 'psd',	'mime_type'	=> 'application/octet-stream',		'ico'	=> 'psd.png');
$filetype[]	= array('ext'	=> 'pdf',	'mime_type'	=> 'application/pdf',			'ico'	=> 'pdf.png');
$filetype[]	= array('ext'	=> 'cdr',	'mime_type'	=> 'application/octet-stream',		'ico'	=> 'cdr.png');
$filetype[]	= array('ext'	=> 'csv',	'mime_type'	=> 'application/vnd.ms-excel',		'ico'	=> 'csv.png');
$filetype[]	= array('ext'	=> 'doc',	'mime_type'	=> 'application/msword',		'ico'	=> 'doc.png');
$filetype[]	= array('ext'	=> 'xls',	'mime_type'	=> 'application/vnd.ms-excel',		'ico'	=> 'xls.png');
$filetype[]	= array('ext'	=> 'docx',	'mime_type'	=> 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',	'ico'	=> 'docx.png');
$filetype[]	= array('ext'	=> 'xlsx',	'mime_type'	=> 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',		'ico'	=> 'xlsx.png');
$filetype[]	= array('ext'	=> 'torrent',	'mime_type'	=> 'application/x-bittorrent',		'ico'	=> 'torrent.png');


/**
* Image
*
* @var array
*/
$imagetype	= [];
$imagetype[]	= array('ext'	=> 'png',	'mime_type'	=> 'image/png',		'ico'	=> 'png.png');
$imagetype[]	= array('ext'	=> 'gif',	'mime_type'	=> 'image/gif',		'ico'	=> 'gif.png');
$imagetype[]	= array('ext'	=> 'jpg',	'mime_type'	=> 'image/jpg',		'ico'	=> 'jpg.png');
$imagetype[]	= array('ext'	=> 'jpg',	'mime_type'	=> 'image/jpeg',	'ico'	=> 'jpeg.png');
$imagetype[]	= array('ext'	=> 'jpg',	'mime_type'	=> 'image/pjpeg',	'ico'	=> 'jpg.png');
$imagetype[]	= array('ext'	=> 'webp',	'mime_type'	=> 'image/webp',	'ico'	=> 'jpeg.png'); # TODO: Find icon
//$imagetype[]	= array('ext'	=> 'ico',	'mime_type'	=> 'image/x-icon',	'ico'	=> 'ico.png');
//$imagetype[]	= array('ext'	=> 'bmp',	'mime_type'	=> 'image/bmp',		'ico'	=> 'bmp.png');

$mimetype = array_merge($filetype, $imagetype);