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
defined('_JEXEC') or die();

if (! defined('JAC_REGISTERED')) {
	JLoader::register('JACModel', JPATH_ADMINISTRATOR.'/components/com_jacomment/models/model.php');
}

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;

/**
 * This model is used for JAModerator feature of the component
 * 
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */
class JACommentModelModerator extends JACModel
{
	
	/**
	 * Constructor
	 * 
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get total of users with some criteria
	 * 
	 * @param string $where_more Criteria
	 * @param string $joins 	 Join table string
	 * 
	 * @return integer Total of users
	 */
	function getTotal($where_more = '', $joins = '')
	{
		$db = Factory::getDBO();
		
		$query = "SELECT count(u.id) FROM #__users as u" . "\n  $joins" . "\n WHERE 1=1 $where_more";
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	/**
	 * Get user items
	 * 
	 * @param string  $where 	  Criteria
	 * @param integer $limit 	  Limit records
	 * @param integer $limitStart Start offset position
	 * 
	 * @return array Array of item objects
	 */
	function getItems($where = "", $limit = 100, $limitStart = 0)
	{
		$db = Factory::getDBO();
		
		$sql = "SELECT u.*, COUNT(map.group_id) AS group_count, GROUP_CONCAT(g2.title SEPARATOR " . $db->Quote("\n") . ") AS group_names" . "\n FROM `#__users` AS u" . " LEFT JOIN #__user_usergroup_map AS map" . " ON map.user_id = u.id" . " LEFT JOIN #__usergroups AS g2" . " ON g2.id = map.group_id" . " LEFT JOIN #__user_usergroup_map AS map2" . " ON map2.user_id = u.id" . "\n WHERE 1=1" . $where . "\n GROUP BY u.id" . "\n ORDER BY u.name asc" . "\n LIMIT " . $limitStart . ", " . $limit;
		
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
	
	/**
	 * Parse JSON parameter to component parameters
	 * 
	 * @param array &$items Array of parameter objects
	 * 
	 * @return void
	 */
	function parse(&$items)
	{
		$count = count($items);
		if ($count > 0) {
			for ($i = 0; $i < $count; $i++) {
				$item = & $items[$i];
				$params = new JRegistry;
				$params->loadString($item->params);
				$item->params = $params;
			}
		}
	}
	
	/**
	 * Get an item from table by id
	 * 
	 * @param array $cid Array of item ids
	 * 
	 * @return object Item object
	 */
	function getItem($cid = array(0))
	{
		$edit = JRequest::getBool('edit', true);
		$cid = null;
		
		if (! $cid || @! $cid[0]) {
			$cid = JRequest::getVar('cid', array(0), '', 'array');
		}
		$this->_getTable();
		ArrayHelper::toInteger($cid, array(0));
		if ($edit) {
			$this->_table->load($cid[0]);
		}
		
		return $this->_table;
	}
	
	/**
	 * Get parameters of moderator list
	 * 
	 * @return array Array of parameters
	 */
	function _getVars()
	{
		$app = Factory::getApplication();
		$option = 'moderator';
		
		$app = Factory::getApplication('administrator');
		
		$list = array();
		$list['filter_order'] = $app->getUserStateFromRequest($option . '.filter_order', 'filter_order', 'u.username', 'cmd');
		$list['filter_order_Dir'] = $app->getUserStateFromRequest($option . '.filter_order_Dir', 'filter_order_Dir', '', 'word');
		$list['limit'] = $app->getUserStateFromRequest($option . 'list_limit', 'limit', $app->getCfg('list_limit'), 'int');
		$list['limitstart'] = $app->getUserStateFromRequest($option . '.limitstart', 'limitstart', 0, 'int');
		$list['group'] = $app->getUserStateFromRequest($option . '.group', 'group', 'moderator', 'string');
		
		return $list;
	}
}
?>