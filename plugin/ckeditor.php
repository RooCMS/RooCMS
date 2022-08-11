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


# OUTPUT
header('HTTP/1.1 200 OK');
header('Content-type: application/x-javascript');
header('Content-transfer-encoding: binary\n');
header('Accept-Ranges: bytes');
ob_start("ob_gzhandler", 9);

?>
document.write('<script type="text/javascript" src="plugin/ckeditor/ckeditor.js"></script>');
document.write('<script type="text/javascript" src="plugin/ckeditor/adapters/jquery.js"></script>');

(function($) {
	"use strict";
	$(window).on('load', function() {
		/* CKEditor */
		$(".ckeditor").ckeditor();
		$(".ckeditor-mail").ckeditor({toolbar: 'Mail'});
		$(".ckeditor-html").ckeditor({height: '150px', toolbar: 'HTML'});
	});
})(jQuery);