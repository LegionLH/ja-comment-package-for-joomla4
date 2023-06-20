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
 * This model is used for JAReports feature of the component
 * 
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */
class JACommentModelReports extends JACModel
{
	var $_table = null;
	
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
	 * Get comment table instant
	 * 
	 * @return Table object
	 */
	function &_getTable()
	{
		if ($this->_table == null) {
			$this->_table = JTable::getInstance('comments', 'Table');
		}
		return $this->_table;
	}
	
	/**
	 * Get configuration item
	 * 
	 * @param integer $id Item id
	 * 
	 * @return Table object
	 */
	function getItem($id = 0)
	{
		static $item = null;
		if (isset($item)) {
			return $item;
		}
		$inputs = Factory::getApplication()->input;
		if (! $id) {
			$cid = $inputs->get('cid', array(0), 'array');
			ArrayHelper::toInteger($cid, array(0));
			
			if (isset($cid[0]) && $cid[0] > 0) {
				$id = $cid[0];
			}
		}
		$this->_getTable();
		
		if ($id) {
			$this->_table->load($id);
		}
		
		return $this->_table;
	}
	
	/**
	 * Get parameters of comment list
	 * 
	 * @return array Array of comments
	 */
	function _getVars()
	{
		$app = Factory::getApplication();
		$option = 'comments';
		$list = array();
		$app = Factory::getApplication('administrator');
		$list['filter_order'] = $app->getUserStateFromRequest($option . '.filter_order', 'filter_order', 'c.ordering', 'cmd');
		$list['filter_order_Dir'] = $app->getUserStateFromRequest($option . '.filter_order_Dir', 'filter_order_Dir', '', 'word');
		$list['limit'] = $app->getUserStateFromRequest($option . 'list_limit', 'limit', $app->getCfg('list_limit'), 'int');
		$list['limitstart'] = $app->getUserStateFromRequest($option . '.limitstart', 'limitstart', 0, 'int');
		$list['search'] = $app->getUserStateFromRequest($option . '.search', 'search', '', 'string');
		return $list;
	}
	
	/**
	 * Generate a where clause for getting comment item
	 * 
	 * @param array $lists Array of parameters
	 * 
	 * @return string Where clause
	 */
	function getWhereClause($lists)
	{
		//where clause 
		$where = array();
		if ($lists['search']) {
			if (is_numeric($lists['search'])) {
				$where[] = " c.id ='" . $lists['search'] . "' ";
			} else {
				$where[] = " c.title LIKE '%" . $lists['search'] . "%' ";
			}
		}
		$where = count($where) ? " AND " . implode(' AND ', $where) : '';
		return $where;
	}
	
	/**
	 * Get comment items
	 * 
	 * @param string  $where 	  Where clause
	 * @param string  $groupby 	  Group by clause
	 * @param string  $orderby	  Order by clause
	 * @param integer $limitstart Start offset position
	 * @param integer $limit	  Limit records
	 * @param string  $fields	  Field list
	 * @param string  $joins	  Table join list
	 * 
	 * @return array Array of item objects
	 */
	function getItems($where = '', $groupby = '', $orderby = '', $limitstart = 0, $limit = 0, $fields = '', $joins = '')
	{
		$db = Factory::getDBO();
		
		$query = " SELECT c.* ";
		if ($fields) {
			$query .= " , $fields ";
		}
		
		$query .= "\n FROM #__jacomment as c ";
		if ($joins) {
			$query .= " $joins ";
		}
		
		$query .= "\n WHERE 1 $where ";
		
		if ($groupby) {
			$query .= " GROUP BY $groupby ";
		}
		
		if ($orderby) {
			$query .= " ORDER BY $orderby ";
		}
		
		if ($limit > 0) {
			$query .= " LIMIT $limitstart,$limit ";
		}
		
		$db->setQuery($query);
		
		return $db->loadObjectList();
	}
	
	/**
	 * Get comment items
	 * 
	 * @param string  $where 	  Where clause
	 * @param string  $orderby	  Order by clause
	 * @param integer $limitstart Start offset position
	 * @param integer $limit	  Limit records
	 * @param string  $fields	  Field list
	 * @param string  $joins	  Table join list
	 * 
	 * @return array Array of items
	 */
	function getDyamicItems($where = '', $orderby = '', $limitstart = 0, $limit = 0, $fields = '', $joins = '')
	{
		$db = Factory::getDBO();
		$query = '';
		if ($fields) {
			$query .= "SELECT $fields ";
		} else {
			$query .= " SELECT c.* ";
		}
		
		$query .= " FROM #__jacomment as c ";
		if ($joins) {
			$query .= " $joins ";
		}
		
		$query .= " WHERE 1 $where ";
		if ($orderby) {
			$query .= " ORDER BY $orderby ";
		}
		
		if ($limit > 0) {
			$query .= " LIMIT $limitstart, $limit ";
		}
		
		$db->setQuery($query);
		
		return $db->loadColumn();
	}
	
	/**
	 * Get total of comment items
	 * 
	 * @param string $where Where clause
	 * @param string $joins Table join list
	 * 
	 * @return integer Total of comment items
	 */
	function getTotal($where = '', $joins = '')
	{
		$db = Factory::getDBO();
		$query = " SELECT COUNT(c.id) " . " FROM #__jacomment as c " . "\n  $joins" . " WHERE 1 $where ";
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	/**
	 * Get total of report items
	 * 
	 * @param integer $commentid Comment id
	 * 
	 * @return integer Total of report items
	 */
	function getTotalReports($commentid)
	{
		$db = Factory::getDBO();
		$query = " SELECT COUNT(r.id) as total_reports " . " FROM #__jacomment_reports as r " . " WHERE commentid= '" . $commentid . "' ";
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	/**
	 * Get ordering of comments
	 * 
	 * @param object $item Item object
	 * 
	 * @return mixed Ordering list or False on error
	 */
	function getOrdering($item)
	{
		$query = 'SELECT ordering AS value, title AS text' . ' FROM #__jacomment' . ' ORDER BY ordering';
		return JHTML::_('list.specificordering', $item, $item->id, $query);
	}
	
	/**
	 * Publish items
	 * 
	 * @param integer $publish Publish status
	 * 
	 * @return boolean True if have no error and vice versa
	 */
	function published($publish)
	{
		$db = Factory::getDBO();
		
		$inputs = Factory::getApplication()->input;
		$ids = $inputs->get('cid', array());
		ArrayHelper::toInteger($ids, array());
		$ids = implode(',', $ids);
		
		$query = "UPDATE #__jacomment" . " SET published = " . intval($publish) . " WHERE id IN ( $ids )";
		$db->setQuery($query);
		if (! $db->query()) {
			return false;
		}
		return true;
	}
	
	/**
	 * Dismiss an item
	 * 
	 * @param integer $id Item id
	 * 
	 * @return boolean True if have no error and vice versa
	 */
	function dismiss($id)
	{
		$db = Factory::getDBO();
		
		$query = "DELETE FROM #__jacomment_reports WHERE commentid = '" . intval($id) . "'";
		$db->setQuery($query);
		if (! $db->query()) {
			return false;
		}
		
		$query = "INSERT INTO #__jacomment_reported(commentid) VALUES( '" . intval($id) . "')";
		$db->setQuery($query);
		if (! $db->query()) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Dismiss all items
	 * 
	 * @return boolean True if have no error and vice versa
	 */
	function dismiss_all()
	{
		$db = Factory::getDBO();
		
		$inputs = Factory::getApplication()->input;
		$ids = $inputs->getVar('cid', array(), 'array');
		ArrayHelper::toInteger($ids, array());
		
		for ($i = 0; $i < sizeof($ids); $i++) {
			$query = "DELETE FROM #__jacomment_reports WHERE commentid = '" . intval($ids[$i]) . "'";
			$db->setQuery($query);
			if (! $db->query()) {
				return false;
			}
			
			$query2 = "INSERT INTO #__jacomment_reported(commentid) VALUES( '" . intval($ids[$i]) . "')";
			$db->setQuery($query2);
			if (! $db->query()) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Delete items
	 * 
	 * @return array Error list
	 */
	function remove()
	{
		$db = Factory::getDBO();
		
		$inputs = Factory::getApplication()->input;
		$cids = $inputs->get('cid', null, 'array');
		ArrayHelper::toInteger($cids, null);
		
		$count = count($cids);
		$errors = array();
		$is_fail = array();
		if ($count > 0) {
			foreach ($cids as $cid) {
				$query = "DELETE FROM #__jacomment WHERE id=$cid";
				$db->setQuery($query);
				if (! $db->query()) {
					$is_fail[] = $cid;
				}
			}
			if (count($is_fail) > 0) {
				$errors[] = "[ID: " . implode(',', $is_fail) . "]" . JText::_('FAILURE_TO_DELETE_COMMENT');
			}
		}
		return $errors;
	}
}
?>
