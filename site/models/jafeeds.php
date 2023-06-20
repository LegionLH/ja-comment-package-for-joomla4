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
 * JACommentModelJAFeeds model
 *
 * @package		Joomla.Site
 * @subpackage	JAComment
 */
class JACommentModelJAFeeds extends JACModel
{
	var $_table = null;
	var $_data = null;
	var $_id = null;
	var $_content = null;
	
	/**
	 * Constructor
	 * 
	 * @return void
	 */
	function __construct()
	{
		$inputs = Factory::getApplication()->input;
		parent::__construct();
		$id = $inputs->getInt('feed_id', 0);
		$this->setId((int) $id);
	}
	
	/**
	 * Setter function for id property
	 * 
	 * @param integer $id Id value
	 * 
	 * @return void
	 */
	function setId($id)
	{
		// Set id and wipe data
		$this->_id = $id;
		$this->_data = null;
	}
	
	/**
	 * Get email table instance
	 * 
	 * @return object Email table object
	 */
	function &_getTable()
	{
		if ($this->_table == null) {
			$this->_table = JTable::getInstance('JA_Feeds', 'Table');
		}
		return $this->_table;
	}
	
	/**
	 * Get email item by ID
	 * 
	 * @return object Email Table object
	 */
	function getItem()
	{
		static $item = null;
		if (isset($item)) {
			return $item;
		}
		
		$table = $this->_getTable();
		
		// Load the current item if it has been defined
		$inputs = Factory::getApplication()->input;
		$edit = $inputs->getBool('edit', true);
		$cid = $inputs->get('cid', array(0), 'array');
		ArrayHelper::toInteger($cid, array(0));
		
		if ($edit) {
			$table->load($cid[0]);
		}
		
		if ((($table->id == null) || ($table->id == 0)) && $inputs->getCmd('layout') != 'form') {
			$table = $this->getDefault($table);
		}
		
		return $table;
	}
	
	/**
	 * Get default values of an item
	 * 
	 * @param object $item Item object
	 * 
	 * @return object Item
	 */
	function getDefault($item)
	{
		global $jbconfig;
		$item->feed_type = $jbconfig['feeds']->get('defaultType');
		$item->feed_description = $jbconfig['feeds']->get('description');
		$item->msg_count = $jbconfig['feeds']->get('count');
		$item->msg_orderby = $jbconfig['feeds']->get('orderby');
		$item->msg_numWords = $jbconfig['feeds']->get('numWords');
		$item->feed_renderAuthorFormat = $jbconfig['feeds']->get('renderAuthorFormat');
		$item->feed_renderHTML = $jbconfig['feeds']->get('renderHTML');
		$item->feed_cache = $jbconfig['feeds']->get('cache');
		return $item;
	}
	
	/**
	 * Get URL parameters
	 * 
	 * @return array List of parameters of URL
	 */
	function getURLParams()
	{
		$inputs = Factory::getApplication()->input;
		$urlparams = array();
		$urlparams['type'] = $inputs->getString('type','');
		$urlparams['name'] = $inputs->getString('name','');
		$urlparams['description'] = $inputs->getString('description','');
		$urlparams['cache'] = $inputs->getString('cache','');
		$urlparams['category'] = $inputs->getString('category','');
		$urlparams['location'] = $inputs->getString('location','');
		$urlparams['effected_date'] = $inputs->getString('effected_date','');
		$urlparams['premium'] = $inputs->getString('premium','');
		$urlparams['order_by'] = $inputs->getString('order_by','');
		$urlparams['job_number'] = $inputs->getString('job_number','');
		$urlparams['exitems'] = $inputs->getString('exitems','');
		$urlparams['inemployers'] = $inputs->getString('inemployers','');
		$urlparams['exemployers'] = $inputs->getString('exemployers','');
		return $urlparams;
	
	}
	
	/**
	 * Get menu item array
	 * 
	 * @return array List of menu items
	 */
	function getMenuItemArray()
	{
		$type = 'content_blog_section';
		$database = Factory::getDBO();
		
		$itemids = null;
		
		$database->setQuery("SELECT id, componentid " . "\n FROM #__menu " . "\n WHERE type = '$type'" . "\n AND published = 1");
		$rows = $database->loadObjectList();
		foreach ($rows as $row) {
			$itemids[$row->componentid] = $row->id;
		}
		return $itemids;
	}
	
	/**
	 * Get items 
	 * 
	 * @return array List of items
	 */
	function getItems()
	{
		global $option;
		$app = Factory::getApplication();
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest($option . '.salary.limitstart', 'limitstart', 0, 'int');
		
		$db = Factory::getDBO();
		$query = $this->_buildQuery(" AND f.is_user=0");
		$db->setQuery($query);
		
		$items = $db->loadObjectList();
		
		$total = count($items);
		
		jimport('joomla.html.pagination');
		$this->_pagination = new JPagination($total, $limitstart, $limit);
		
		// slice out elements based on limits
		$list = array_slice($items, $this->_pagination->limitstart, $this->_pagination->limit);
		
		return $list;
	}
	
	/**
	 * Get pagination
	 * 
	 * @return object JPagination
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		
		}
		
		return $this->_pagination;
	}
	
	/**
	 * Build get feed query
	 * 
	 * @param string  $where_more Criteria string
	 * @param integer $limit 	  Limit records
	 * @param integer $limitstart Offset position
	 * 
	 * @return string Query string
	 */
	function _buildQuery($where_more = '', $limit = 0, $limitstart = 0)
	{
		global $option;
		$db = Factory::getDBO();
		$app = Factory::getApplication();
		$option_1 = $option . 'jafeeds';
		$search = $app->getUserStateFromRequest($option_1 . '.feeds.search', 'search', '', 'string');
		$filter_order = $app->getUserStateFromRequest($option_1 . '.feeds.filter_order', 'filter_order', 'f.feed_name', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest($option_1 . '.feeds.filter_order_Dir', 'filter_order_Dir', 'ASC', 'word');
		$filter_state = $app->getUserStateFromRequest($option_1 . '.filter_state', 'filter_state', '', 'word');
		
		$search = JString::strtolower($search);
		$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir;
		$where = ' WHERE 1=1 ';
		if ($search) {
			$where = " AND LOWER(value) like " . $db->Quote('%' . $db->getEscaped($search, true) . '%', false);
		}
		if ($where_more != '') {
			$where .= $where_more;
		}
		if ($filter_state) {
			if ($filter_state == 'P') {
				$where .= ' AND f.published = 1';
			} else if ($filter_state == 'U') {
				$where .= ' AND f.published = 0';
			}
		}
		$query = "SELECT * " . "FROM `#__ja_feeds` as f " . $where . $orderby;
		
		return $query;
	}
	
	/**
	 * Store function
	 * 
	 * @return integer Item id if store successfully, otherwise false
	 */
	function store()
	{
		$db = Factory::getDBO();
		$row = $this->getItem();
		$inputs = Factory::getApplication()->input;
		$post = $inputs->get('request', JREQUEST_ALLOWHTML);
		if (is_array($post['filter_cat_id'])) {
			$post['filter_cat_id'] = implode(',', $post['filter_cat_id']);
		}
		if (is_array($post['filter_location_id'])) {
			$post['filter_location_id'] = implode(',', $post['filter_location_id']);
		}
		if (! $row->bind($post)) {
			echo "<script> alert('" . $row->getError(true) . "'); window.history.go(-1); </script>\n";
			return JText::_("NOT_BIND_DATA");
		}
		
		if ($row->check() != 'SUCCESS') {
			return false;
		}
		
		if (! $row->store()) {
			echo "<script> alert('" . $row->getError(true) . "'); window.history.go(-1); </script>\n";
			return JText::_('STORE_FAIL');
		}
		return $row->id;
	}
}
?>