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
defined('_JEXEC') or die('Restricted access');

if (! defined('JAC_REGISTERED')) {
	JLoader::register('JACModel', JPATH_BASE.'/components/com_jacomment/models/model.php');
}

use Joomla\CMS\Factory;

/**
 * JACommentModelAddons model
 *
 * @package		Joomla.Site
 * @subpackage	JAComment
 */
class JACommentModelAddons extends JACModel
{
	
	var $_script = null;
	
	/**
	 * Get parameter of add-ons
	 * 
	 * @param string $name Add-on name: addthis, addtoany_comment, ...
	 * 
	 * @return mixed Parameter value or array of parameters
	 */
	function getScript($name = "")
	{
		$inputs = Factory::getApplication()->input;
		$group = $inputs->getCmd('group', 'layout');
		$db = JFactory::getDBO();
		
		$query = "SELECT * FROM #__jacomment_configs as s WHERE s.group='" . $group . "'";
		$db->setQuery($query);
		$items = $db->loadObjectList();
		if (! $items) {
			$items[0]->data = '';
		}
		
		$data = $items[0]->data;
		$params = new JRegistry;
		$params->loadString($data);
		
		if ($name) {
			// return only value
			return $params->get($name);
		} else {
			// return array
			return $params;
		}
	}
	
	/**
	 * Get configuration parameters
	 * 
	 * @return object Parameter object
	 */
	function getConfig()
	{
		$db = JFactory::getDBO();
		
		$query = "SELECT * FROM #__jacomment_configs as s";
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		$data = '';
		if ($items) {
			foreach ($items as $item) {
				$data .= $item->data;
			}
			$params = new JRegistry;
			$params->loadString($data);
			return $params;
		}
		
		return null;
	}
}
?>