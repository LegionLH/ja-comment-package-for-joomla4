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

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
/**
 * This model is used for JAConfigs feature of the component
 * 
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */
class JACommentModelConfigs extends JACModel
{
	var $_data;
	var $_table;
	
	/**
	 * Get configuration table instance
	 * 
	 * @return JTable Configuration table object
	 */
	function &_getTable()
	{
		if ($this->_table == null) {
			$this->_table = JTable::getInstance('configs', 'Table');
		}
		return $this->_table;
	}
	
	/**
	 * Check a component does exist on system or not
	 * 
	 * @param string $component Component name
	 * 
	 * @return integer Number of components existing on system
	 */
	function checkComponent($component)
	{
		$query = " SELECT Count(*) FROM #__extensions as c WHERE c.element ='$component' ";
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
	
	/**
	 * Publish or unpublish a component
	 * 
	 * @param string 	$component 	Component name
	 * @param integer 	$publish 	Publish status, 0: unpublish, 1: publish
	 * 
	 * @return boolean True if have no error and vice versa
	 */
	function publishComponent($component, $publish = 0)
	{
		$query = " UPDATE #__extensions SET enabled = $publish WHERE element ='$component' ";
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	/**
	 * getItems function
	 * 
	 * @return object Item object
	 */
	function getItems()
	{
		$app = Factory::getApplication();
		$inputs = Factory::getApplication()->input;
		$option = $inputs->getCmd('option');
		$group = $inputs->getCmd('group', 'systems');
		$db = Factory::getDBO();
		
		$query = "SELECT * " . "FROM #__jacomment_configs as s WHERE s.group='" . $group . "'";
		$db->setQuery($query);
		$items = $db->loadObjectList();
		if (! $items) {
			$items = array();
			$items[0] = new stdClass();
			
			$items[0]->id = 0;
			$items[0]->data = '';
		}
		return $items[0];
	}
	
	/**
	 * Get configuration item
	 * 
	 * @param integer $cid Item id
	 * 
	 * @return Config Table object
	 */
	function getItem($cid = 0)
	{
		static $item = null;
		if (isset($item)) {
			return $item;
		}
		$inputs = Factory::getApplication()->input;
		$group = $inputs->getCmd('group', 'systems');
		$table = $this->_getTable();
		
		// Load the current item if it has been defined
		if (! $cid) {
			$cid = $inputs->get('cid', array(0), 'array');
			ArrayHelper::toInteger($cid, array(0));
		}
		if ($cid) {
			$table->load($cid[0]);
		}
		$table->group = $group;
		$item = $table;
		return $item;
	}
	
	/** 
	 * Store configuration item
	 * 
	 * @return boolean True if have no error and vice versa
	 */
	function store()
	{
		// Initialize variables
		$db = Factory::getDBO();
		$row = $this->getItem();
		$post = $this->getState('request');
		
		if (! $row->bind($post)) {
			echo "<script> alert('" . $row->getError(true) . "'); window.history.go(-1); </script>\n";
			return false;
		}
		if (! $row->check()) {
			echo "<script> alert('" . $row->getError(true) . "'); window.history.go(-1); </script>\n";
			return false;
		}
		if (! $row->store()) {
			echo "<script> alert('" . $row->getError(true) . "'); window.history.go(-1); </script>\n";
			return false;
		}
		$row->checkin();
		return $row->id;
	}
	
	/**
	 * Parse comment list
	 * 
	 * @param object 	&$params 	Parameter object
	 * @param array 	$comments 	Array of comments
	 * 
	 * @return void
	 */
	function parse(&$params, $comments)
	{
		$count = count($comments);
		
		if ($count > 0) {
			for ($i = 0; $i < $count; $i++) {
				$title = '';
				$comment = $comments[$i];
				
				if ($title == '') {
					$title = "<span style='font-weight:bold;' id='jac_parent_title_$comment->id'>----</span>: " . "<span id='jac_title_$comment->id'>---</span>";
				}
				$params->set("status_spam_title_{$comment->id}", $title);
			}
		}
	}
	
	/**
	 * Fetch category list
	 * 
	 * @param string $name 			Parameter name
	 * @param string $value 		Control value
	 * @param object &$node			Node attributes
	 * @param string $control_name 	Control name
	 * 
	 * @return string HTML select box string
	 */
	function fetchElement($name, $value, &$node, $control_name)
	{
		$db = Factory::getDBO();
		
		$section = $node->attributes('section');
		$class = $node->attributes('class');
		if (! $class) {
			$class = "inputbox";
		}
		
		if (! isset($section)) {
			// alias for section
			$section = $node->attributes('scope');
			if (! isset($section)) {
				$section = 'content';
			}
		}
		
		if ($section == 'content') {
			// This might get a conflict with the dynamic translation
			// - TODO: search for better solution
			$query = 'SELECT c.id AS value, CONCAT_WS( "/",s.title, c.title ) AS text' . ' FROM #__categories AS c' . ' LEFT JOIN #__sections AS s ON s.id=c.section' . ' WHERE c.published = 1' . ' AND s.scope = ' . $db->Quote($section) . ' ORDER BY s.title, c.title';
		} else {
			$query = 'SELECT c.id AS value, c.title AS text' . ' FROM #__categories AS c' . ' WHERE c.published = 1' . ' AND c.section = ' . $db->Quote($section) . ' ORDER BY c.title';
		}
		$db->setQuery($query);
		$options = $db->loadObjectList();
		
		return JHTML::_('select.genericlist', $options, '' . $control_name . '[' . $name . '][]', 'class="inputbox" size="15" multiple="multiple"', 'value', 'text', $value, $control_name . $name);
	
	}
	
	/**
	 * Get category list
	 * 
	 * @return object Category list
	 */
	function getCategories()
	{
		$db = Factory::getDBO();
		$query = "SELECT c.id AS `value`, c.section AS `id`, CONCAT_WS( ' / ', s.title, c.title) AS `text` 
					FROM #__sections AS s INNER JOIN #__categories AS c ON c.section = s.id 
					WHERE s.scope = 'content' ORDER BY s.name,c.name";
		$db->setQuery($query);
		$categories = $db->loadObjectList(); // load the results into an array
		

		return $categories;
	}
	
	/**
	 * Get Blocked/Blacklist configuration data
	 * 
	 * @param string $group Group key
	 * 
	 * @return array Configuration data
	 */
	function getBlockBlackByTab($group)
	{
		$db = Factory::getDBO();
		
		if ($group == 'spamfilters') {
			$query = "SELECT `group`, data FROM #__jacomment_configs 
	            WHERE `group`='blocked_word_list' OR `group`='blocked_ip_list' OR `group`='blocked_email_list'";
		} else {
			$query = "SELECT `group`, data FROM #__jacomment_configs 
	            WHERE `group`='blacklist_word_list' OR `group`='blacklist_ip_list' OR `group`='blacklist_email_list'";
		}
		
		$db->setQuery($query);
		$blockblackbytab = $db->loadObjectList();
		
		$arr = array();
		for ($i = 0; $count = sizeof($blockblackbytab), $i < $count; $i++) {
			$arr[$blockblackbytab[$i]->group] = $blockblackbytab[$i]->data;
		}
		return $arr;
	}
	
	/**
	 * getGroupByName function
	 * 
	 * @param string $groupName Group name
	 * 
	 * @return object Configuration data of group
	 */
	function getGroupByName($groupName)
	{
		$db = Factory::getDBO();
		
		$query = "SELECT data FROM #__jacomment_configs WHERE `group`='" . $groupName . "'";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 * Get data of a group according to tab name
	 * 
	 * @return string Data in JSON structure
	 */
	function getBlockBlack()
	{
		$db = Factory::getDBO();
		$inputs = Factory::getApplication()->input;
		$tab = $inputs->getCmd('tab');
		
		$query = "SELECT data FROM #__jacomment_configs WHERE `group`='" . $tab . "'";
		$db->setQuery($query);
		$blockblack = $db->loadObjectList();
		if ($blockblack) {
			return $blockblack[0]->data;
		}
	}
	
	/**
	 * Save data of group
	 * 
	 * @param string $strData Data in JSON structure
	 * 
	 * @return boolean True if have no error and vice versa
	 */
	function saveBlockBlack($strData = "")
	{
		$data = '';
		$msg = '';
		
		$db = Factory::getDBO();
		
		$inputs = Factory::getApplication()->input;
		$tab = $inputs->getCmd('tab');
		
		// ++ check        
		$query_check = "SELECT data FROM #__jacomment_configs WHERE `group`='" . $tab . "'";
		$db->setQuery($query_check);
		$items = $db->loadObjectList();
		
		if (sizeof($items) == 0) {
			$exist = false;
		} else {
			$exist = true;
			$data .= $items[0]->data;
		}
		// -- check
		$strData = str_replace('\N', "\n", $strData);
		
		$data = $data . "\n" . $strData;
		if ($strData) {
			if ($exist == true) {
				$query = "UPDATE #__jacomment_configs SET data='" . $data . "' WHERE `group`='" . $tab . "'";
			
			} else {
				$query = "INSERT INTO #__jacomment_configs(`group`, data) VALUES( '" . $tab . "', '" . $data . "')";
			
			}
			
			$db->setQuery($query);
			if (! $db->query()) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Delete parameter data of group
	 * 
	 * @return boolean True if have no error and vice versa
	 */
	function removeBlockBlack()
	{
		$db = Factory::getDBO();
		
		$inputs = Factory::getApplication()->input;
		$tab = $inputs->getCmd('tab');
		$id = $inputs->getInt('id');
		
		$arr = explode("\n", $this->getBlockBlack());
		unset($arr[$id]);
		
		$data = implode("\n", $arr);
		
		$query = "UPDATE #__jacomment_configs SET data='" . $data . "' WHERE `group`='" . $tab . "'";
		$db->setQuery($query);
		if (! $db->query()) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Update p0, children, active_children fields of items
	 * 
	 * @return void
	 */
	function maintainSystem()
	{
		$db = Factory::getDBO();
		
		$query = "SELECT id, parentid, children, active_children, p0, type FROM #__jacomment_items ORDER BY parentid";
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		if (count($items)) {
			$childrenItems = array();
			$activeChildrenItems = array();
			$p0Items = array();
			
			// update values of arrays
			for ($i = 0, $j = count($items) - 1; $i < count($items); $i++, $j--) {
				$item = $items[$i];
				$lastItem = $items[$j];
				
				// update p0
				if ($item->parentid) {
					$p0Items[$item->id] = $p0Items[$item->parentid];
				} else {
					$p0Items[$item->id] = $item->id;
				}
				
				// calculate sum of children and sum of active children
				$sumChildren = 0;
				$sumActiveChildren = 0;
				if ($j < count($items) - 1) {
					$numDirectChildren = 0;
					$numDirectActiveChildren = 0;
					
					for ($k = count($items); $k > $j; $k--) {
						if ($lastItem->id == $items[$k - 1]->parentid) {
							$sumChildren += $childrenItems[$items[$k - 1]->id];
							$sumActiveChildren += $activeChildrenItems[$items[$k - 1]->id];
						}
						if ($items[$k - 1]->parentid == $lastItem->id) {
							$numDirectChildren++;
						}
						if ($items[$k - 1]->parentid == $lastItem->id && $items[$k - 1]->type == 1) {
							$numDirectActiveChildren++;
						}
					}
					
					$sumChildren += $numDirectChildren;
					$sumActiveChildren += $numDirectActiveChildren;
				}
				
				// update children
				$childrenItems[$lastItem->id] = $sumChildren;
				
				// update active children
				$activeChildrenItems[$lastItem->id] = $sumActiveChildren;
			}
		}
		
		if (count($p0Items)) {
			$arrItemIds = array_keys($p0Items);
			
			for ($i = 0; $i < count($p0Items); $i++) {
				$itemId = $arrItemIds[$i];
				
				$query = "UPDATE #__jacomment_items SET 
					children = '{$childrenItems[$itemId]}',
					active_children = '{$activeChildrenItems[$itemId]}',
					p0 = '{$p0Items[$itemId]}'
				  WHERE id = '{$itemId}'";
				$db->setQuery($query);
				$db->query();
			}
		}
		return;
	}
}
?>