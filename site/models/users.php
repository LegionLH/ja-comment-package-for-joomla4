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

/**
 * JACommentModelUsers model
 *
 * @package		Joomla.Site
 * @subpackage	JAComment
 */
class JACommentModelUsers extends JACModel
{
	/**
	 * Get user parameters
	 * 
	 * @param integer $userid User id
	 * 
	 * @return object Parameter data or null
	 */
	function getParam($userid)
	{
		global $option;
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		
		$query = "SELECT params FROM #__users WHERE id=" . $userid;
		$db->setQuery($query);
		$params = $db->loadObjectList();
		
		$data = '';
		if ($params) {
			foreach ($params as $param) {
				$data .= $param->params;
			}
			$params = new JRegistry;
			$params->loadString($data);
			return $params->_registry['_default']['data'];
		}
		
		return null;
	}
}
?>
