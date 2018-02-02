<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsFunction
 * @author Jambik <> idea and creations
 *         alex Roosso <http://www.roocms.com> - mod: added flag noentitys & modifications
 */

/**
 * Smarty {get_params} function plugin
 *
 * Type:     function
 * Name:     get_params
 * Purpose:  generate get parameters
 * Input:
 *         - prefix  = before string
 *         - suffix  = after string
 *         - exclude = GET params wich should be excluded
 *
 * @author Jambik <> idea and creations
 *         alex Roosso <http://www.roocms.com> - mod: added flag noentitys & modifications
 *
 * @param array                    $params   parameters
 * @param Smarty_Internal_Template $template template object
 * @return string
 */
function smarty_function_get_params($params, $template)
{
	$prefix = isset($params['prefix']) ? trim($params['prefix']) : "";
	$suffix = isset($params['suffix']) ? trim($params['suffix']) : "";
	$excludeParams = isset($params['exclude']) ? trim($params['exclude']) : "";
	$noentity = isset($params['noentity']) ? true : false;

	$output = "";
	
	$excludeParams = explode(",", $excludeParams);
	foreach($excludeParams as $key => $value) {
		$excludeParams[$key] = trim($value);
	}

	if($_GET) {
		foreach($_GET as $key=>$value) {
			if(!in_array($key, $excludeParams)) {
				$entity = $noentity ? "&" : "&amp;" ;
				$output .= $entity.$key."=".$value;
			}
		}
	}

	$output = $prefix.$output.$suffix;

	return $output;
}
