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
header("Content-type: application/x-javascript; charset=utf-8");
header('Content-transfer-encoding: binary\n');
header('Accept-Ranges: bytes');
ob_start("ob_gzhandler", 9);
?>

document.write('<link rel="stylesheet" href="/plugin/codemirror/lib/codemirror.min.css">');
document.write('<link rel="stylesheet" href="/plugin/codemirror/addon/dialog/dialog.min.css">');
document.write('<link rel="stylesheet" href="/plugin/codemirror/addon/display/fullscreen.min.css">');

document.write('<script src="/plugin/codemirror/lib/codemirror.min.js"></script>');			// Engine
document.write('<script src="/plugin/codemirror/addon/dialog/dialog.min.js"></script>');		// ALL
document.write('<script src="/plugin/codemirror/addon/search/search.min.js"></script>');
document.write('<script src="/plugin/codemirror/addon/search/searchcursor.min.js"></script>');
document.write('<script src="/plugin/codemirror/addon/search/match-highlighter.min.js"></script>');
document.write('<script src="/plugin/codemirror/addon/edit/matchbrackets.min.js"></script>');
document.write('<script src="/plugin/codemirror/addon/edit/closebrackets.min.js"></script>');

document.write('<script src="/plugin/codemirror/addon/mode/overlay.min.js"></script>');
document.write('<script src="/plugin/codemirror/addon/mode/multiplex.min.js"></script>');
document.write('<script src="/plugin/codemirror/addon/display/fullscreen.min.js"></script>');		// Util

/* mode */
document.write('<script src="/plugin/codemirror/mode/htmlmixed/htmlmixed.min.js"></script>');
document.write('<script src="/plugin/codemirror/mode/xml/xml.min.js"></script>');
document.write('<script src="/plugin/codemirror/mode/javascript/javascript.min.js"></script>');
document.write('<script src="/plugin/codemirror/mode/css/css.min.js"></script>');
document.write('<script src="/plugin/codemirror/mode/clike/clike.min.js"></script>');
document.write('<script src="/plugin/codemirror/mode/php/php.min.js"></script>');
document.write('<script src="/plugin/codemirror/mode/htmlembedded/htmlembedded.min.js"></script>');

<?php if($mode == "php") { ?>

<?php } ?>
