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
use Joomla\Utilities\ArrayHelper;

/**
 * JACommentModelLogs model
 *
 * @package		Joomla.Site
 * @subpackage	JAComment
 */
class JACommentModelLogs extends JACModel
{
	var $_table = null;
	
	/**
	 * Contructor
	 * 
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get table instance
	 * 
	 * @return object Table instance
	 */
	function _getTable()
	{
		if ($this->_table == null) {
			$this->_table = JTable::getInstance('Logs', 'Table');
		}
		return $this->_table;
	}
	
	/**
	 * Get item by id
	 * 
	 * @param integer $id Item id
	 * 
	 * @return object Table instance
	 */
	function getItem($id = 0)
	{
		static $item = null;
		if (isset($item)) {
			return $item;
		}
		if (! $id) {
			$inputs = Factory::getApplication()->input;
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
	 * Store an item
	 * 
	 * @param array $post Post data
	 * 
	 * @return integer Item id if store successfully, otherwise false
	 */
	function store($post = null)
	{
		$row = $this->getItem();
		
		if (! $row->bind($post)) {
			JError::raiseWarning(1, $row->getError(true));
			return false;
		}
		
		if (! $row->store()) {
			JError::raiseWarning(1, $row->getError(true));
			return false;
		}
		
		return $row->id;
	}
	
	/**
	 * Get item by user
	 * 
	 * @param integer $userID User id
	 * @param integer $itemID Item id
	 * 
	 * @return object Item object
	 */
	function getItemByUser($userID, $itemID)
	{
		$db = Factory::getDBO();
		
		$where_more = "AND l.userid = $userID AND l.itemid = $itemID";
		
		$sql = "SELECT l.* " . "\n FROM #__jacomment_logs as l " . "\n WHERE 1=1 $where_more";
		
		$db->setQuery($sql);
		return $db->loadObject();
	}
	
	/**
	 * Update number of reports to log
	 * 
	 * @param integer $id 	  Log id
	 * @param integer $report Number of report
	 * 
	 * @return void
	 */
	function updateReport($id, $report)
	{
		$id = intval($id);
		$report = intval($report);
		
		$db = Factory::getDBO();
		$sql = "UPDATE #__jacomment_logs SET reports=$report WHERE id=$id";
		
		$db->setQuery($sql);
		
		$db->execute();
	}
	
	/**
	 * Get items
	 * 
	 * @param string  $where_more Criteria string
	 * @param integer $limit 	  Limit records
	 * @param integer $limitstart Offset position
	 * @param string  $order 	  Order string
	 * @param string  $fields 	  Fields list
	 * @param string  $joins 	  Join string
	 * 
	 * @return Array List of objects
	 */
	function getItems($where_more = '', $limit = 10, $limitstart = 0, $order = '', $fields = '', $joins = '')
	{
		$db = Factory::getDBO();
		
		if (! $order) {
			$order = ' c.id';
		}
		
		if ($fields) {
			$fields = "l.id";
		} else {
			$fields = 'c.*';
		}
		
		$sql = "SELECT $fields " . "\n FROM #__jacomment_logs as l " . "\n $joins" . "\n WHERE 1=1 $where_more" . "\n ORDER BY $order " . "\n LIMIT $limitstart, $limit";
		
		$db->setQuery($sql);
		
		return $db->loadObjectList();
	}
}
?>