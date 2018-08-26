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
 * @package     RooCMS
 * @subpackage	Plugin Utilites
 * @subpackage	Bootstrap UI
 * @author      alex Roosso
 * @copyright   2010-2019 (c) RooCMS
 * @link        http://www.roocms.com
 * @version     1.2.2
 * @since       $date$
 * @license     http://www.gnu.org/licenses/gpl-3.0.html
 */


# OUTPUT
header('HTTP/1.1 200 OK');
header('Content-type: application/x-javascript');
header('Content-transfer-encoding: binary\n');
header('Accept-Ranges: bytes');
ob_start("ob_gzhandler", 9);

if(!isset($_GET['short'])) {
?>

document.write('<link href="/plugin/bootstrap/css/font-awesome.min.css" rel="stylesheet">');
document.write('<link href="/plugin/bootstrap/css/bootstrap.min.css" rel="stylesheet">');
document.write('<link href="/plugin/bootstrap/css/bootstrap-select.min.css" rel="stylesheet">');
document.write('<link href="/plugin/bootstrap/css/bootstrap-datepicker.min.css" rel="stylesheet">');
document.write('<link href="/plugin/bootstrap/css/bootstrap-colorpicker.min.css" rel="stylesheet">');
document.write('<link href="/plugin/bootstrap/css/bootstrap-tagsinput.min.css" rel="stylesheet">');
document.write('<script src="/plugin/bootstrap/js/bootstrap.min.js"></script>');
document.write('<script src="/plugin/bootstrap/js/bootstrap-select.min.js"></script>');
document.write('<script src="/plugin/bootstrap/js/i18n/defaults-ru_RU.min.js"></script>');
document.write('<script src="/plugin/bootstrap/js/bootstrap-datepicker.min.js"></script>');
document.write('<script src="/plugin/bootstrap/js/locales/bootstrap-datepicker.ru.js"></script>');
document.write('<script src="/plugin/bootstrap/js/bootstrap-colorpicker.min.js"></script>');
document.write('<script src="/plugin/bootstrap/js/bootstrap-tagsinput.min.js"></script>');

<?php } else { ?>

document.write('<link href="/plugin/bootstrap/css/font-awesome.min.css" rel="stylesheet">');
document.write('<link href="/plugin/bootstrap/css/bootstrap.min.css" rel="stylesheet">');
document.write('<link href="/plugin/bootstrap/css/bootstrap-select.min.css" rel="stylesheet">');
document.write('<link href="/plugin/bootstrap/css/bootstrap-datepicker.min.css" rel="stylesheet">');
document.write('<script src="/plugin/bootstrap/js/bootstrap.min.js"></script>');
document.write('<script src="/plugin/bootstrap/js/bootstrap-select.min.js"></script>');
document.write('<script src="/plugin/bootstrap/js/i18n/defaults-ru_RU.min.js"></script>');
document.write('<script src="/plugin/bootstrap/js/bootstrap-datepicker.min.js"></script>');
document.write('<script src="/plugin/bootstrap/js/locales/bootstrap-datepicker.ru.js"></script>');

<?php } ?>