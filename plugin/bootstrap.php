<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2020 alexandr Belov aka alex Roosso. All rights reserved.
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
//document.write('<link href="/plugin/bootstrap/css/bs3extend.min.css" rel="stylesheet">');
//document.write('<link href="/plugin/bootstrap/css/bootstrap-select.min.css" rel="stylesheet">');
//document.write('<link href="/plugin/bootstrap/css/bootstrap-datepicker.min.css" rel="stylesheet">');

//document.write('<script src="/plugin/bootstrap/js/fa.min.js"></script>');
//document.write('<script src="/plugin/bootstrap/js/bootstrap.bundle.min.js"></script>');
<?php if(!isset($_GET['short'])) { ?>



<?php } else { ?>



<?php } ?>