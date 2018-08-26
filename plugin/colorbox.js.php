<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      http://www.roocms.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
 */


$skin = 1;
if(isset($_GET['s']) && $_GET['s'] >= 1 && $_GET['s'] <=5) {
	$skin = $_GET['s'];
}
$zoom = false;
if(isset($_GET['zoom'])) {
	$zoom = true;
}

# OUTPUT
header('HTTP/1.1 200 OK');
header('Content-type: application/x-javascript');
header('Content-transfer-encoding: binary\n');
header('Accept-Ranges: bytes');
ob_start("ob_gzhandler", 9);

?>

document.write('<link href="/plugin/colorbox/<?=$skin;?>/colorbox.min.css" rel="stylesheet">');
document.write('<script src="/plugin/colorbox/jquery.colorbox.min.js"></script>');
<?php
	if($zoom) :
?>
document.write('<script src="/plugin/colorbox/jquery.zoom.min.js"></script>');
<?php
	endif;
?>