<?php
/**
 * ------------------------------------------------------------------------
 * JA Comment Package for Joomla 2.5 & 3.x
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

if (! defined('JAC_REGISTERED')) {
	JLoader::register('JACModel', JPATH_ADMINISTRATOR.'/components/com_jacomment/models/model.php');
}

/**
 * This model is used for JAComment feature of the component
 * 
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */
class JACommentModelComment extends JACModel
{
	/**
	 * getKey function
	 * 
	 * @return string Key in JSON structure
	 */
	function getKey()
	{
		global $jacconfig;
		$sql = "select data from #__jacomment_configs where `group`='key'";
		$db = JFactory::getDBO();
		$db->setQuery($sql);
		$key = $db->loadResult();
		return $key;
	}
	
	/**
	 * getLatestVersion function
	 * 
	 * @return string Latest version
	 */
	function getLatestVersion()
	{
		global $JACVERSION;
		if (isset($_SESSION['latest_version'])) {
			$latest_version = $_SESSION['latest_version'];
		} else {
			global $JACPRODUCTKEY;
			
			$req = 'type=product_name';
			$req .= '&key=com_jacomment';
			$req .= '&jversion=1.6';
			//$req .= '&current_version=' . $JACPRODUCTKEY;
			$host = 'www.joomlart.com';
			$path = '/forums/getlatestversion.php';
			$URL = "http://$host$path";
			$latest_version = '';
			if (! function_exists('curl_version')) {
				if (stristr(ini_get('disable_functions'), "fsockopen")) {
					return;
				} else {
					$latest_version = JACommentHelpers::socket_getdata($host, $path, $req);
				}
			} else {
				$latest_version = JACommentHelpers::curl_getdata($URL, $req);
			}
			$_SESSION['latest_version'] = $latest_version;
		}
		
		return $latest_version;
	}
}
?>