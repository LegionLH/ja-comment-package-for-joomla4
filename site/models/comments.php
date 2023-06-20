<?php
/**
 * ------------------------------------------------------------------------
 * JA Comment Package for Joomla 2.5 & 3.0
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
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
 * JACommentModelComments model
 *
 * @package		Joomla.Site
 * @subpackage	JAComment
 */
class JACommentModelComments extends JACModel
{
	var $_total = 0;
	var $_childTotal = 0;
	var $_limit = 0;
	var $_limitStart = 0;
	var $_table = null;
	var $id = null;
	var $user_id = null;
	var $title = null;
	var $comment = null;
	var $type = null;
	var $arrayComment = null;
	var $mysqlVersion = 0;
	
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
	 * Get data list of configuration
	 * 
	 * @return array Array of objects contain configuration data
	 */
	function &getData()
	{
		// load data if it doesnnot already exists
		if (empty($this->_data)) {
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query);
		}
		
		return $this->_data;
	}
	
	/**
	 * Get total of items
	 * 
	 * @param string $search Criteria for searching
	 * 
	 * @return integer Total of records
	 */
	function getTotal($search, $backend = 0)
	{
		$db = Factory::getDBO();
		$query = $this->_buildQueryTotal($search, $backend);
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	/**
	 * Get children items
	 * 
	 * @param integer $id Parent id
	 * 
	 * @return array List of items
	 */
	function checkSubOfComment($id)
	{
		$db = Factory::getDBO();
		$query = "SELECT c.id FROM #__jacomment_items as c WHERE parentid = $id";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 * Build total query
	 * 
	 * @param string $search Criteria for searching
	 * 
	 * @return string Query string
	 */
	function _buildQueryTotal($search, $backend = 0)
	{
		global $jacconfig;
		
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildContentWhere($search);
		
		//if (! $backend && ! (int) $jacconfig["comments"]->get("is_show_child_comment", 0)) {
		if ( $backend !=0) {
			$where .= ' and c.parentid=0';
		}
		
		$query = 'SELECT count(*) ' . ' FROM #__jacomment_items as c ' . $where;
		
		return $query;
	}
	
	/**
	 * Get customer information
	 * 
	 * @param integer $uid User id
	 * 
	 * @return object List of objects
	 */
	function getCustomerInfo($uid)
	{
		$db = Factory::getDBO();
		$query = "SELECT * FROM #__users WHERE id = $uid";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	// ++ add by congtq 29/10/2009 
	/**
	 * Merge array key
	 * 
	 * @param array $array1 Array 1
	 * @param array $array2 Array 2
	 * 
	 * @return array Array after merging
	 */
	function array_merge_keys($array1, $array2)
	{
		foreach ($array2 as $k => $v) {
			if (! array_key_exists($k, $array1)) {
				$array1[$k] = $v;
			} else {
				if (is_array($v)) {
					$array1[$k] = $this->array_merge_keys($array1[$k], $array2[$k]);
				}
			}
		}
		return $array1;
	}
	
	/**
	 * Get total items by comment type
	 * 
	 * @param string $search Criteria string
	 * 
	 * @return array List of items
	 */
	function getTotalByType($search)
	{
		$db = Factory::getDBO();
		//$search .= ' and c.parentid=0';
		$query = $this->_buildQueryTotalByType($search);
		
		$db->setQuery($query);
		$arr = array();
		$arr2 = $db->loadAssocList();
		if (sizeof($arr2) > 0) {
			for ($i = 0; $i < sizeof($arr2); $i++) {
				$arr[$arr2[$i]['type']] = $arr2[$i]['total'];
			}
			
		}
		for ($j = 0; $j <= 2; $j++) {
			if (! array_key_exists($j, $arr)) {
				$arr[$j] = 0;
			}
		}
		return $arr;
	}
	
	/**
	 * Build query Get total by type
	 * 
	 * @param string $search Criteria string
	 * 
	 * @return string Query string
	 */
	function _buildQueryTotalByType($search)
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildContentWhere($search);
		$query = "SELECT count(*) as total, type FROM #__jacomment_items as c
                    $where
                    GROUP BY c.type";
		
		return $query;
	}
	
	/**
	 * Build query Get items by type
	 * 
	 * @param integer $limitstart Offset position
	 * @param integer $limit	  Limit records
	 * @param boolean $dosearch	  Query for searching or listing items
	 * 
	 * @return string Query string
	 */
	function _buildQueryItemsByType($limitstart, $limit, $dosearch = false)
	{
		$db = Factory::getDBO();
		
		$where = '';
		$where = $this->_buildContentWhere($dosearch);
		$orderby = $this->_buildContentOrderBy();

		$fields = ($dosearch) ? 'c.id' : '*';
		
		$query = "SELECT $fields " . "FROM #__jacomment_items as c" . $where . $orderby;
		
		return $query;
	}
	
	/**
	 * Parse items parameter
	 * 
	 * @param object &$items Items
	 * 
	 * @return void
	 */
	function parse(&$items)
	{
		$count = count($items);
		if ($count > 0) {
			for ($i = 0; $i < $count; $i++) {
				$item = $items[$i];
				$item->params = new JRegistry;
				$item->params->loadString($item->params);
			}
		}
	}
	
	/**
	 * Get table instance
	 * 
	 * @return object Table object
	 */
	function &_getTable()
	{
		if ($this->_table == null) { 
			$this->_table = JTable::getInstance('Comments', 'Table');
		} 
		return $this->_table;
	}
	
	/**
	 * Build query when searching
	 * 
	 * @param string &$search Criteria string
	 * 
	 * @return void
	 */
	function builQueryWhenSearch(&$search)
	{
		$db = Factory::getDBO();
		$query = $this->_buildQuery($search); //echo $query;exit;
		$fields = ($search) ? 'c.id, c.parentid' : '*';
		$orderby = " ORDER BY c.id ";
		
		$query = "SELECT $fields " . "FROM #__jacomment_items as c WHERE 1=1 " . $search . $orderby;
		
		$db->setQuery($query);
		$items = $db->loadObjectList();
		$parentArray = array();
		
		if ($items) {
			foreach ($items as $item) {
				if ($item->parentid != 0) {
					if (! $this->isExistItemInSearch($items, $item->parentid)) {
						$parentArray[] = $item->parentid;
					}
					$this->getArrayParent($item->parentid, $parentArray, $items);
					$this->getQuerySearchWithID($search, $parentArray);
				}
			}
		}
	}
	
	/**
	 * Check if item is existed or not
	 * 
	 * @param object  $items Items objects
	 * @param integer $id 	 Id to check
	 * 
	 * @return boolean True if have no error and vice versa
	 */
	function isExistItemInSearch($items, $id)
	{
		foreach ($items as $item) {
			if ($item->id == $id) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Get parent type
	 * 
	 * @param integer $id Item id
	 * 
	 * @return string Type of parent
	 */
	function getParentType($id)
	{
		$db = Factory::getDBO();
		$query = "SELECT c.parentid FROM #__jacomment_items as c WHERE c.id = " . $id;
		$db->setQuery($query);
		
		$parentID = $db->loadResult();
		
		if ($parentID == 0) {
			return 1;
		} else {
			$query = "SELECT c.type FROM #__jacomment_items as c WHERE c.id = " . $parentID;
			$db->setQuery($query);
			return $db->loadResult();
		}
	}
	
	/**
	 * Get array of parents
	 * 
	 * @param integer $id 		    Item id
	 * @param array   &$parentArray Parent array
	 * @param object  $itemsAll     Items objects
	 * 
	 * @return void
	 */
	function getArrayParent($id, &$parentArray, $itemsAll)
	{
		$db = Factory::getDBO();
		$query = "SELECT c.id, c.parentid FROM #__jacomment_items as c WHERE c.id = " . $id;
		
		$db->setQuery($query);
		
		$items = $db->loadObject();
		
		if ($items->parentid != 0) {
			if (! $this->isExistItemInSearch($itemsAll, $items->parentid)) {
				$parentArray[] = $items->parentid;
			}
			
			$this->getArrayParent($items->parentid, $parentArray, $itemsAll);
		}
	}
	
	/**
	 * Build search query with parent id
	 * 
	 * @param string &$search 	   Criteria string
	 * @param array  $parentArrays Parent array
	 * 
	 * @return void
	 */
	function getQuerySearchWithID(&$search, $parentArrays)
	{
		$strPost = strpos($search, "c.email LIKE");
		$searchParent = "";
		if ($strPost !== false) {
			if ($parentArrays) {
				foreach ($parentArrays as $parentArray) {
					$searchParent .= " c.id=" . $parentArray . " OR ";
				}
			}
		}
		
		$search1 = substr($search, 0, $strPost) . $searchParent . substr($search, $strPost);
		$search = $search1;
	}
	
	/**
	 * Get items
	 * 
	 * @param string $search  Criteria string
	 * @param string $orderBy Order by string
	 * @param string $getAll  Get all items or not
	 * 
	 * @return object Item object
	 */
	function getItems($search = '', $orderBy = '', $getAll = '')
	{
		$session = Factory::getSession();
		$session->set('totalComment', $this->getTotal($search, 1));
		$session->set('totalMostParentComment', $this->getTotal($search, 1));
		
		$db = Factory::getDBO();
		$query = $this->_buildQuery($search, $orderBy, $getAll);
		
		$db->setQuery($query);
		
		$items = $db->loadObjectList();
		return $items;
	}
	
	/**
	 * Build query to get items
	 * 
	 * @param string $search  Criteria string
	 * @param string $orderBy Order by string
	 * @param string $getAll  Get all items or not
	 * 
	 * @return string Query string
	 */
	function _buildQuery($search, $orderBy = '', $getAll = '')
	{
		$where = '';
		
		$where = $this->_buildContentWhere($search);
		$orderby = $this->_buildContentOrderBy($orderBy);

		// if($getAll)
		// $feilds = '*';
		// else
		// $feilds = ($search) ? 'c.id, c.parentid' : '*';
		// $query = "SELECT $feilds " . "FROM #__jacomment_items as c" . $where . $orderby;
		$query = 'SELECT c.id, c.parentid, c.contentid, c.ip, c.name, 
					c.contenttitle, c.comment, c.date, c.published, c.locked, 
					c.ordering, c.email, c.website, c.star, c.userid, c.usertype, 
					c.option, c.voted, c.report, c.subscription_type, c.referer, 
					c.source, c.type, c.date_active, c.active_children, c.p0 
				  FROM #__jacomment_items as c' . $where . $orderby;
		
		return $query;
	}
	
	/**
	 * Get full items
	 * 
	 * @param string $search  Criteria string
	 * @param string $orderBy Order string
	 * @param string $getAll  Get all items or not
	 * @param mixed  $idList  List of item ids
	 * 
	 * @return array List of item objects
	 */
	function getFullItems($search = '', $orderBy = '', $getAll = '', $idList = null)
	{
		$db = Factory::getDBO();
		$query = $this->_buildFullQuery($search, $orderBy, $getAll, $idList);
		
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		return $items;
	}
	
	/**
	 * Build full query
	 * 
	 * @param string $search  Criteria string
	 * @param string $orderBy Order string
	 * @param string $getAll  Get all items or not
	 * @param mixed  $idList  List of item ids
	 * 
	 * @return string Query string
	 */
	function _buildFullQuery($search, $orderBy = '', $getAll = '', $idList = null)
	{
		$where = '';
		
		$where = $this->_buildContentWhere($search);
		$orderby = $this->_buildContentOrderBy($orderBy);
		
		if ($getAll) {
			$fields = '*';
		} else {
			$fields = ($search) ? 'c.id, c.parentid, c.contentid, c.ip, c.name, c.contenttitle, c.comment, 
									c.date, c.published, c.locked, c.ordering, c.email, c.website, c.star, 
									c.userid, c.usertype, c.option, c.voted, c.report, c.subscription_type, 
									c.referer, c.source, c.type, c.date_active, c.active_children, c.p0' 
								: '*';
		}
		
		if ($idList) {
			if (is_array($idList)) {
				$idList = implode(',', $idList);
			}
			$query = "SELECT $fields FROM #__jacomment_items as c $where AND c.id IN ($idList) $orderby";
		} else {
			$query = "SELECT $fields FROM #__jacomment_items as c $where AND c.id IN (0) $orderby";
		}
		
		return $query;
	}
	
	/**
	 * Get items for RSS
	 * 
	 * @param string $search  Criteria string
	 * @param string $orderBy Order string
	 * @param string $getAll  Get all items or not
	 * 
	 * @return object Item objects
	 */
	function getItemsRSS($search = '', $orderBy = '', $getAll = '')
	{
		$session = Factory::getSession();
		$session->set('totalComment', $this->getTotal($search));
		
		$db = Factory::getDBO();
		$query = $this->_buildQueryRSS($search, $orderBy, $getAll);
		
		$db->setQuery($query);
		$items = $db->loadObjectList();
		return $items;
	}
	
	/**
	 * Build RSS query
	 * 
	 * @param string $search  Criteria string
	 * @param string $orderBy Order string
	 * @param string $getAll  Get all items or not
	 * 
	 * @return string Query string
	 */
	function _buildQueryRSS($search, $orderBy = '', $getAll = '')
	{
		$where = '';
		
		$where = $this->_buildContentWhere($search);
		$orderby = $this->_buildContentOrderBy($orderBy);
		
		$query = 'SELECT c.id, c.parentid, c.name, c.voted, c.referer, c.comment, c.date FROM #__jacomment_items as c' . $where . $orderby;
		
		return $query;
	}
	
	/**
	 * Get total comment items
	 * 
	 * @param string $search Criteria string
	 * 
	 * @return integer Total of items
	 */
	function getCountItems($search = '')
	{
		$db = Factory::getDBO();
		
		$where = '';
		$where = $this->_buildContentWhere($search);
		
		$query = "SELECT COUNT(*) FROM #__jacomment_items $where";
		
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	/**
	 * Build where criteria for query
	 * 
	 * @param string $search Criteria string
	 * 
	 * @return string Criteria string
	 */
	function _buildContentWhere($search)
	{
		$where = ' WHERE 1=1 ' . $search;
		
		return $where;
	}
	
	/**
	 * Build order by string for query
	 * 
	 * @param string $orderBy Order by string
	 * 
	 * @return string Order by string
	 */
	function _buildContentOrderBy($orderBy = '')
	{
		if (! $orderBy) {
			$orderBy = ' ORDER BY c.id DESC';
		}
		
		return $orderBy;
	}
	
	/**
	 * Get an item
	 * 
	 * @param integer $id Item id
	 * 
	 * @return object Item object
	 */
	function getItem($id = 0)
	{
		static $item = null;
		
		if (isset($item)) {
			return $item;
		}
		if (! $id) {
			$inputs = Factory::getApplication()->input;
			$cid = $inputs->get('cid', array(0));
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
	 * Get JA Comment variables
	 * 
	 * @return array List of variables
	 */
	function _getVars()
	{
		global $jacconfig;
		
		$app = Factory::getApplication();
		
		$option = 'moderator';
		
		$list = array();
		$list['filter_order'] = $app->getUserStateFromRequest($option . '.filter_order', 'filter_order', 'u.username', 'cmd');
		
		$list['filter_order_Dir'] = $app->getUserStateFromRequest($option . '.filter_order_Dir', 'filter_order_Dir', '', 'word');
		
		$list['limit'] = $jacconfig["comments"]->get("number_comment_in_page", 10);
		$list['limitstart'] = 0;
		$list['order'] = '';
		
		$list['group'] = $app->getUserStateFromRequest($option . '.group', 'group', 'moderator', 'string');
		
		return $list;
	}
	
	/**
	 * Get comment options
	 * 
	 * @return array Object list
	 */
	function getCommentOption()
	{
		$db = Factory::getDBO();
		
		$query = "SELECT `option` FROM #__jacomment_items GROUP BY `option`";
		
		$db->setQuery($query);
		
		return $db->loadObjectList();
	}
	
	/**
	 * Get comment source
	 * 
	 * @return array Object list
	 */
	function getCommentSource()
	{
		$db = Factory::getDBO();
		
		$query = "SELECT `source` FROM #__jacomment_items GROUP BY `source` HAVING `source` != ''";
		
		$db->setQuery($query);
		       
		return $db->loadObjectList();
	}
	
	/**
	 * Change type of comment
	 * 
	 * @param integer $id 			Item id
	 * @param integer $type			Item type
	 * @param boolean $updateReport Update report or not
	 * @param string  $action		Action when change type
	 * 
	 * @return void
	 */
	function changeTypeOfComment($id, $type, $updateReport = false, $action = '')
	{
		global $jacconfig;
		$db = Factory::getDBO();
		$dateActive = $db->Quote(date("Y-m-d H:i:s"));
		
		//reset report of comment if this comment is approved
		$items = $this->getItem($id);
		
		if ($updateReport) {
			$sql = "UPDATE #__jacomment_items SET type=$type, `date_active` =$dateActive, `report` = 0 WHERE id = $id";
		} else {
			$sql = "UPDATE #__jacomment_items SET type=$type, `date_active` =$dateActive WHERE id = $id";
		}
		
		$session = Factory::getSession();
		
		$jaActiveComments = $session->get("jaActiveComments");
		if (isset($jaActiveComments) && ! in_array($id, $jaActiveComments)) {
			$jaActiveComments[] = $id;
		} else {
			$jaActiveComments[] = $id;
		}
		// Put a value in a session var
		$session->set('jaActiveComments', $jaActiveComments);
		
		$db->setQuery($sql);
		$db->execute();
		
		//is_enabled_email
		if ($jacconfig["general"]->get("is_enabled_email", 0)) {
			$helper = new JACommentHelpers();
			$items->comment = $helper->replaceBBCodeToHTML($items->comment);
			if ($action) {
				if ($action == "reportspam") {
					//send mail to admin if a comment is report spam
					$helper->sendMailWhenChangeType($items->name, $items->email, $items->comment, $items->referer, $type, $action);
					//send mail to author
					$helper->sendMailWhenChangeType($items->name, $items->email, $items->comment, $items->referer, $type);
				}
			} else {
				//remove spam by admin
				if ($items->type == 2 && $type == 1) {
					$helper->sendMailWhenChangeType($items->name, $items->email, $items->comment, $items->referer, $type, "removeSpam");
				} else {
					//send mail when admin approved new comment
					if ($jacconfig["comments"]->get("is_allow_approve_new_comment", 1)) {
						if ($type == 1 && $items->type == 0 && $items->date_active == "0000-00-00 00:00:00") {
							//send mail when adnew
							if ($items->parentid == 0) {
								$type = "addNew";
							}
							
							$post["id"] = $items->id;
							$post["parentid"] = $items->parentid;
							$post["contentid"] = $items->contentid;
							$post["name"] = $items->name;
							$post["comment"] = $items->comment;
							$post["date"] = $items->date;
							$post["email"] = $items->email;
							$post["userid"] = $items->userid;
							$post["option"] = $items->option;
							$post["subscription_type"] = $items->subscription_type;
							$post["referer"] = $items->referer;
							$post["type"] = $type;
							$post["children"] = $items->children;
							$post["active_children"] = $items->active_children;
							$post["p0"] = $items->p0;
							
							$wherejatotalcomment = " AND c.type=1 AND c.contentid=" . $post['contentid'] . " AND c.option='" . $post['option'] . "'";
							$post["comment"] = $helper->replaceBBCodeToHTML($post["comment"]);
							$helper->sendMailWhenNewCommentApproved($items->id, $wherejatotalcomment, $type, $post);
							
							$type = 1;
						}
					}
					
					$helper->sendMailWhenChangeType($items->name, $items->email, $items->comment, $items->referer, $type);
				}
			}
		}
		
		$childArrays = null;
		$this->getChildArray($id, $childArrays);
		if (count($childArrays) > 0) {
			foreach ($childArrays as $childArray) {
				$this->changeTypeOfComment($childArray->id, $type, $updateReport, $action);
			}
		}
		
		// ++ Added by NhatNX
		// After deleting comment, update number of children
		$this->updateChildren($items->parentid);
		// -- Added by NhatNX
	}
	
	/**
	 * Get array of children comment
	 * 
	 * @param integer $parentID	   Parent id
	 * @param array   &$childArray Array of children
	 * 
	 * @return void
	 */
	function getChildArray($parentID, &$childArray)
	{
		$db = Factory::getDBO();
		$sql = "SELECT id, type FROM #__jacomment_items WHERE parentid = $parentID";
		$db->setQuery($sql);
		$results = $db->loadObjectList();
		foreach ($results as $result) {
			$childArray[] = $result;
			$this->getChildArray($result->id, $childArray);
		}
	}
	
	/**
	 * Delete a item
	 * 
	 * @param integer $id Item id
	 * 
	 * @return mixed Deleted item object or false if has error
	 */
	function deleteComment($id)
	{
		$db = Factory::getDBO();
		$items = $this->getItem($id);
		$sql = "DELETE FROM #__jacomment_items WHERE id = $id";
		$db->setQuery($sql);
		
		if ($db->execute()) {
			/* Add JomSocial:: activity Stream*/
			if ($items->userid) {
				$title = sprintf(JText::_('JOMSOCIAL_ACTIVITI_STREAM_TITLE_REMOVE_COMMENT'), $items->referer, $items->contenttitle);
				JACommentHelpers::JomSocial_addActivityStream($items->userid, $title, $items->id, 'remove');
			}
			/* End*/
			
			// ++ Added by NhatNX
			// After deleting comment, update number of children
			$this->updateChildren($items->parentid);
			// -- Added by NhatNX

			return $items;
		}
		
		return false;
	}
	
	/**
	 * Get type of comment
	 * 
	 * @param integer $id Item id
	 * 
	 * @return integer Type of comment
	 */
	function getParentTypeOfComment($id)
	{
		$db = Factory::getDBO();
		$query = "SELECT type FROM #__jacomment_items WHERE id = $id";
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	/**
	 * Get page navigator object
	 * 
	 * @param integer $limitstart Offset position
	 * @param integer $limit	  Limit records
	 * @param string  $divId	  Div id will be updated by ajax
	 * @param string  $link		  Paging link
	 * 
	 * @return object Paging object
	 */
	function &getPagination($limitstart = 0, $limit = 0, $divId = '', $link = '')
	{
		$app = Factory::getApplication();
		jimport('joomla.html.pagination');
		
		//include_once JPATH_SITE . DS . 'components' . DS . 'com_jacomment' . DS . 'helpers' . DS . 'japagination.php';
		$path_japagination = JPATH_SITE . DS . 'components' . DS . 'com_jacomment' . DS . 'helpers' . DS . 'japagination.php';
	
		if (file_exists('templates/' . $app->getTemplate() . '/html/com_jacomment/japagination.php')) {
			$path_japagination = 'templates/' . $app->getTemplate() . '/html/com_jacomment/japagination.php';
		}
		include_once $path_japagination;
		
		$session = Factory::getSession();
		// $totalComment = $session->get('totalComment', 0);
		$totalComment = $session->get('totalMostParentComment', 0);
		
		if ($this->_limit != 0 && $this->_limitStart != 0) {
			$this->_pagination = new JACPagination($totalComment, $this->_limitStart, $this->_limit, $divId, $link);
			$this->_limitStart = 0;
			$this->_limit = 0;
		} else {
			$this->_pagination = new JACPagination($totalComment, $limitstart, $limit, $divId, $link);
		}
		
		$this->_pagination->_link = JURI::base(true) . "/index.php?";
		return $this->_pagination;
	}
	
	/**
	 * Save an item
	 * 
	 * @param array   $post   Post request data
	 * @param integer $insert Is insert or not
	 * 
	 * @return mixed Added item id or false if has error
	 */
	function store($post = 0, $insert = 0)
	{
		global $jacconfig;
		$helper = new JACommentHelpers();
		$row = $this->getItem();
		if (! $post) {
			$post = $this->getState('request');
		}
		if (! $row->bind($post)) { 
			//JError::raiseWarning(1, $row->getError(1));
			Factory::getApplication()->enqueueMessage(
				$row->getError(1),
				'warning'
			);
			return false;
		}
		
		//insert with comment id when import jacomment		
		if ($insert) {
			$dbo = $row->getDbo();
			$ret = $dbo->insertObject($row->getTableName(), $row, $row->getKeyName());
			if (! $ret) {
				$row->setError(get_class($row) . '::store failed - ' . $row->_db->getErrorMsg());
				//JError::raiseWarning(1, $row->getError(1));
				Factory::getApplication()->enqueueMessage(
					$row->getError(1),
					'warning'
				);
				return false;
			}
		} else {
			if (! $row->store()) { var_dump($row->getError(0));
				//JError::raiseWarning(1, $row->getError(1));
				Factory::getApplication()->enqueueMessage(
					$row->getError(1),
					'warning'
				);
				return false;
			}
		}
		
		/* Add JomSocial:: activity Stream*/
		if (! $insert) {
			//add new or reply a new comment
			$user = Factory::getUser();
			if (! isset($post["id"])) {
				$action = 'add';
				$row->referer = $row->referer . "#jacommentid:" . $row->id;
				$title = sprintf(JText::_('JOMSOCIAL_ACTIVITI_STREAM_TITLE_COMMENT_NEW_ITEM'), $row->referer, $row->contenttitle);
				JACommentHelpers::JomSocial_addActivityStream($user->id, $title, $row->id, $action);
			} else {
				if (JACommentHelpers::isSpecialUser()) {
					$action = 'update';
					$row = $this->getItem($post["id"]);
					$title = sprintf(JText::_('JOMSOCIAL_ACTIVITI_STREAM_TITLE_UPDATED_COMMENT'), $row->referer, $row->contenttitle);
					JACommentHelpers::JomSocial_addActivityStream($user->id, $title, $row->id, $action);
				}
			}
		}
		/* End*/
		return $row->id;
	}
	
	/**
	 * Get item information
	 * 
	 * @param integer $id Item id
	 * 
	 * @return object Item object
	 */
	function getComment($id)
	{
		$db = Factory::getDBO();
		$query = "SELECT * FROM #__jacomment_items WHERE id = $id";
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	/**
	 * Get number of children of a parent item
	 * 
	 * @param integer $id Parent item id
	 * 
	 * @return integer Number of children of a parent item
	 */
	function getNumberChildOfItems($id)
	{
		$db = Factory::getDBO();
		$query = "SELECT count(*) FROM #__jacomment_items WHERE parentid = $id AND type=1";
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	/**
	 * Get children items
	 * 
	 * @param string  $where_more Criteria string
	 * @param integer $limit	  Limit records
	 * @param integer $limitstart Offset position
	 * @param string  $order	  Order string
	 * @param string  $fields	  Selected fields list
	 * @param string  $joins	  Join string
	 * @param integer $commentID  Comment id
	 * 
	 * @return array Object list
	 */
	function getChildItems($where_more = '', $limit = 10, $limitstart = 0, $order = '', $fields = '', $joins = '', $commentID = 0)
	{
		$db = Factory::getDBO();
		if (! $order) {
			$order = ' c.id';
		}
		$strFind = "type=";
		
		// ++ Edited by NhatNX
		// Only use select query on fields instead of nested query
		if (strpos($where_more, $strFind) === false && strpos($where_more, "type =") === false) {
			$fields = "children ";
		} else {
			$fields = "active_children as children ";
		}
		$fields = "c.*,id as sid, $fields ";
		// -- Edited by NhatNX

		$this->_childTotal = $this->getTotal($where_more);
		
		if ($limitstart == '' && $limit == '') {
			if ($commentID == 0) {
				$sql = "SELECT $fields " . "\n FROM #__jacomment_items as c " . "\n $joins" . "\n WHERE 1=1 $where_more" . "\n ORDER BY $order ";
			} else {
				$sql = "SELECT $fields " . "\n FROM #__jacomment_items as c " . "\n $joins" . "\n WHERE (1=1 $where_more" . "\n) OR c.id = " . $commentID . " ORDER BY $order ";
			}
		} else {
			if ($commentID == 0) {
				$sql = "SELECT $fields " . "\n FROM #__jacomment_items as c " . "\n $joins" . "\n WHERE 1=1 $where_more" . "\n ORDER BY $order ";
			} else {
				$sql = "SELECT $fields " . "\n FROM #__jacomment_items as c " . "\n $joins" . "\n WHERE (1=1 $where_more" . "\n) OR c.id=" . $commentID . " ORDER BY $order ";
			}
		}
		
		$db->setQuery($sql);
		
		return $db->loadObjectList();
	}
	
	/**
	 * Vote for a comment
	 * 
	 * @param integer $itemID Item id
	 * @param integer $value  Vote value
	 * 
	 * @return integer Vote value
	 */
	function voteComment($itemID, $value)
	{
		$db = Factory::getDBO();
		$query = "SELECT voted FROM #__jacomment_items WHERE `id`=$itemID";
		$db->setQuery($query);
		$currentVote = $db->loadResult();
		$currentVote = $currentVote + $value;
		
		$query = "UPDATE #__jacomment_items SET `voted` = $currentVote WHERE `id`=$itemID";
		$db->setQuery($query);
		$db->execute();
		return $currentVote;
	}
	
	/**
	 * Report a comment
	 * 
	 * @param integer $itemID Item id
	 * 
	 * @return integer Number of reports
	 */
	function reportComment($itemID)
	{
		$db = Factory::getDBO();
		$query = "SELECT report FROM #__jacomment_items WHERE `id`=$itemID";
		$db->setQuery($query);
		$currentReport = $db->loadResult();
		$currentReport = $currentReport + 1;
		
		$query = "UPDATE #__jacomment_items SET `report` = $currentReport WHERE `id`=$itemID";
		$db->setQuery($query);
		$db->execute();
		return $currentReport;
	}
	
	/**
	 * Undo a report
	 * 
	 * @param integer $itemID Item id
	 * 
	 * @return integer Number of reports
	 */
	function undoReportComment($itemID)
	{
		$db = Factory::getDBO();
		$query = "SELECT report FROM #__jacomment_items WHERE `id`=$itemID";
		$db->setQuery($query);
		$currentReport = $db->loadResult();
		if ($currentReport > 0) {
			$currentReport = $currentReport - 1;
		}
		
		$query = "UPDATE #__jacomment_items SET `report` = $currentReport WHERE `id`=$itemID";
		$db->setQuery($query);
		$db->execute();
		return $currentReport;
	}
	
	/**
	 * Check if an item or a parent item is existed
	 * 
	 * @param integer $id 		Item id
	 * @param integer $parentID Parent item id
	 * 
	 * @return string Exist status
	 */
	function isExistItemIDParentID($id, $parentID)
	{
		$db = Factory::getDBO();
		$sql = "SELECT count(*) FROM #__jacomment_items WHERE id=$id";
		$db->setQuery($sql);
		
		if ($db->LoadResult()) {
			return "existID";
		}
		
		if ($parentID == 0) {
			return "OK";
		}
		
		$sql = "SELECT count(*) FROM #__jacomment_items WHERE id=$parentID";
		$db->setQuery($sql);
		if ($db->LoadResult()) {
			return "OK";
		} else {
			return "notExistParent";
		}
	}
	
	/**
	 * Get an item from front-end
	 * 
	 * @param integer $id Item id
	 * 
	 * @return array Object list
	 */
	function getItemFrontEnd($id)
	{
		$db = Factory::getDBO();
		
		$sql = "SELECT *,0 as children FROM #__jacomment_items as c  WHERE id=$id";
		
		$db->setQuery($sql);
		
		return $db->loadObjectList();
	}
	
	/**
	 * Get MySQL version
	 * 
	 * @return string MySQL version
	 */
	function find_SQL_Version()
	{
		$db = Factory::getDBO();
		$db->setQuery("SELECT Version() AS version ");
		$row = $db->LoadResult();
		$row = explode("-", $row);
		$row = explode(".", $row[0]);
		return $row[0];
	}
	
	/**
	 * Get items from front-end
	 * 
	 * @param string  $where_more	  Criteria to get items
	 * @param integer $limit		  Limit records
	 * @param integer $limitstart	  Offset position
	 * @param string  $order		  Order string
	 * @param string  $fields		  Fields list string
	 * @param string  $joins		  Join string
	 * @param string  $where_children Criteria to get children
	 * 
	 * @return array Object list
	 */
	function getItemsFrontEnd($where_more = '', $limit = 10, $limitstart = 0, $order = '', $fields = '', $joins = '', $where_children = '')
	{
		$db = Factory::getDBO();
		
		if (! $order) {
			$order = ' c.id';
		}
		$strFind = "type=";
		
		// ++ Edited by NhatNX
		// Only use select query on fields instead of nested query
		if (strpos($where_more, $strFind) === false) {
			$fields = "children ";
		} else {
			$fields = "active_children as children ";
		}
		$fields = "c.*,id as sid, $fields ";
		// -- Edited by NhatNX
		//die($where_more);
		$session = Factory::getSession();
		$session->set('totalComment', $this->getTotal($where_more));
		$session->set('totalMostParentComment', $this->getTotal($where_more, 1));
		
		$where_more .= $where_children;
		
		if ($limit == "all") {
			$sql = "SELECT $fields " . "\n FROM #__jacomment_items as c " . "\n $joins" . "\n WHERE 1=1 $where_more" . "\n ORDER BY $order ";
		} else {
			$sql = "SELECT $fields " . "\n FROM #__jacomment_items as c " . "\n $joins" . "\n WHERE 1=1 $where_more" . "\n ORDER BY $order " . "\n LIMIT $limitstart, $limit";
		}
		
		$db->setQuery($sql);
		
		return $db->loadObjectList();
	}
	
	/**
	 * Get parent item information
	 * 
	 * @param integer $id Parent item id
	 * 
	 * @return array Object list
	 */
	function getParent($id)
	{
		$db = Factory::getDBO();
		$sql = "SELECT * FROM #__jacomment_items as c WHERE id = $id";
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
	
	/**
	 * Get items to send e-mail
	 * 
	 * @param string $where_more  Criteria string
	 * @param string $order 	  Order string
	 * 
	 * @return array Object list
	 */
	function getItemsSendMail($where_more, $order = '')
	{
		$db = Factory::getDBO();
		if (! $order) {
			$order = ' c.id';
		}
		$fields = "c.id, c.name, c.email, c.subscription_type, c.date, c.parentid";
		
		$sql = "SELECT $fields " . "\n FROM #__jacomment_items as c INNER JOIN (SELECT MAX(id) AS id FROM #__jacomment_items GROUP BY email) ids ON c.id = ids.id WHERE 1=1 $where_more" . "\n ORDER BY $order";
		
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
	
	/**
	 * Get values of parameters
	 * 
	 * @param string $group		   Group name
	 * @param string $name		   Parameter name
	 * @param mixed  $defaultValue Parameter default value
	 * 
	 * @return mixed Array of parameters or only 1 parameter
	 */
	static function getParamValue($group, $name = '', $defaultValue = 1)
	{
		$db = Factory::getDBO();
		
		$query = "SELECT * FROM #__jacomment_configs as s WHERE s.group='" . $group . "'";
		$db->setQuery($query);
		$items = $db->loadObjectList();
		if (! $items) {
			$items[0]->data = '';
			return $defaultValue;
		}
		
		$data = $items[0]->data;
		$params = new JRegistry;
		$params->loadString($data);
		if ($name) {
			// return only value
			return $params->get($name, $defaultValue);
		} else {
			// return array
			return $params;
		}
	}
	
	// ++ add by congtq 19/11/2009
	/**
	 * Check blocked and blacklist word
	 * 
	 * @param string $ip    IP address to block
	 * @param string $email E-mail address to block
	 * @param string $word  Word to block
	 * 
	 * @return string Blocking status
	 */
	function checkBlockedWord($ip, $email, $word)
	{
		$ins = array('blacklist_email_list', 'blacklist_ip_list', 'blacklist_word_list', 'blocked_email_list', 'blocked_ip_list', 'blocked_word_list');
		
		$db = Factory::getDBO();
		
		foreach ($ins as $in) {
			$query = "SELECT data FROM #__jacomment_configs WHERE `group` = '$in'";
			$db->setQuery($query);
			$arr[] = $db->loadRowList();
		}
		
		if (sizeof($arr) > 0) {
			$arr_blacklist_email = array();
			$arr_blacklist_ip = array();
			$arr_blacklist_word = array();
			
			$arr_blocked_email = array();
			$arr_blocked_ip = array();
			$arr_blocked_word = array();
			
			if (isset($arr[0][0][0])) {
				$arr_blacklist_email = explode("\n", $arr[0][0][0]);
			}
			
			if (isset($arr[1][0][0])) {
				$arr_blacklist_ip = explode("\n", $arr[1][0][0]);
			}
			
			if (isset($arr[2][0][0])) {
				$arr_blacklist_word = explode("\n", $arr[2][0][0]);
			}
			
			if (isset($arr[3][0][0])) {
				$arr_blocked_email = explode("\n", $arr[3][0][0]);
			}
			if (isset($arr[4][0][0])) {
				$arr_blocked_ip = explode("\n", $arr[4][0][0]);
			}
			if (isset($arr[5][0][0])) {
				$arr_blocked_word = explode("\n", $arr[5][0][0]);
			}
			
			// check for blocked first
			array_shift($arr_blocked_ip);
			if (in_array($ip, $arr_blocked_ip)) {
				return 'IP Blocked';
				exit();
			}
			
			array_shift($arr_blocked_email);
			if (in_array($email, $arr_blocked_email)) {
				return 'Email Blocked';
				exit();
			}
			
			$found_blocked = false;
			array_shift($arr_blocked_word);
			
			$arr_word = explode(" ", $word);
			for ($i = 0; $i < sizeof($arr_word); $i++) {
				$arr_word[$i] = strtoupper($arr_word[$i]);
			}
			//$arr_blocked_word
			for ($i = 0; $i < sizeof($arr_blocked_word); $i++) {
				$arr_blocked_word[$i] = strtoupper($arr_blocked_word[$i]);
			}
			for ($i = 0; $count = sizeof($arr_word), $i < $count; $i++) {
				//damn in [damn]
				if (in_array($arr_word[$i], $arr_blocked_word)) {
					return 'Word Blocked';
					exit();
				}
				//damndamn in [damn]	            
				foreach ($arr_blocked_word as $blocked_word) {
					if ($blocked_word) {
						if (strpos($arr_word[$i], $blocked_word) !== false) {
							return 'Word Blocked';
							exit();
						}
					}
				}
			}
			// check for blacklist second
			array_shift($arr_blacklist_ip);
			if (in_array($ip, $arr_blacklist_ip)) {
				return 'IP Blacklist';
				exit();
			}
			array_shift($arr_blacklist_email);
			if (in_array($email, $arr_blacklist_email)) {
				return 'Email Blacklist';
				exit();
			}
			
			$found_blacklist = false;
			array_shift($arr_blacklist_word);
			
			for ($i = 0; $i < sizeof($arr_blacklist_word); $i++) {
				$arr_blacklist_word[$i] = strtoupper($arr_blacklist_word[$i]);
			}
			
			for ($i = 0; $count = sizeof($arr_word), $i < $count; $i++) {
				if (in_array($arr_word[$i], $arr_blacklist_word)) {
					return 'Word Blacklist';
					exit();
				}
				foreach ($arr_blacklist_word as $blacklist_word) {
					if ($blacklist_word) {
						if (strpos($arr_word[$i], $blacklist_word) !== false) {
							return 'Word Blacklist';
							exit();
						}
					}
				}
			}
		}
	}
	
	/**
	 * Check censored word
	 * 
	 * @param string $word 			  		 Word to check
	 * @param string $censored_words	     Censored words list
	 * @param string $censored_words_replace Words list to replace censored word
	 * 
	 * @return string String after replacing censored words
	 */
	function checkCensoredWord($word, $censored_words, $censored_words_replace)
	{
		$tmp_word = str_replace("\n", ' ', $word);
		$arr_word = explode(" ", $tmp_word);
		
		$arr_censored_words = explode(",", str_replace(' ', '', $censored_words));
		
		$str = '';
		$arr_word_replace = array();
		for ($i = 0; $count = sizeof($arr_word), $i < $count; $i++) {
			if (in_array($arr_word[$i], $arr_censored_words)) {
				$arr_word_replace[] = $arr_word[$i];
			}
		}
		$str = str_replace($arr_word_replace, $censored_words_replace, $word);
		
		return $str;
	}
	
	/**
	 * Update reference link after storing
	 * 
	 * @param integer $commentID Item id
	 * @param string  $url		 Reference link
	 * 
	 * @return void
	 */
	function updateUrl($commentID, $url)
	{
		$db = Factory::getDBO();
		$url = $db->Quote($url);
		$query = "UPDATE #__jacomment_items SET `referer` = $url WHERE `id`=$commentID";
		$db->setQuery($query);
		$db->execute();
	}
	
	/**
	 * Check for send mail
	 * 
	 * @param boolean $is_notify_admin  Send notify to admin or not
	 * @param boolean $is_notify_author Send notify to author or not
	 * @param boolean $is_enabled_email Send e-mail function is enabled or not
	 * 
	 * @return void
	 */
	function checkSendMail($is_notify_admin, $is_notify_author, $is_enabled_email)
	{
	}
	// -- add by congtq 19/11/2009
	

	// ++ add by congtq 25/11/2009
	/**
	 * Check maximum links in comment content
	 * 
	 * @param string  $comment		   Comment content
	 * @param integer $number_of_links Allowed maximum number links
	 * 
	 * @return integer 1 if number of links in comment content reached to maximum, otherwise is 0
	 */
	function checkMaxLink($comment, $number_of_links)
	{
		$count = 0;
		
		$count = substr_count($comment, "[LINK=");
		if (intval($count) > $number_of_links) {
			return 1;
		}
		return 0;
	}
	// -- add by congtq 25/11/2009
	

	/**
	 * Check an user is existed or not
	 * 
	 * @param integer $userID    User id
	 * @param integer $commentID Comment id
	 * 
	 * @return boolean True if user is existed and vice versa
	 */
	function checkUserId($userID, $commentID)
	{
		$db = Factory::getDBO();
		$query = "SELECT name FROM #__users WHERE id='" . $userID . "'";
		$db->setQuery($query);
		if ($db->loadResult()) {
			return true;
		} else {
			$query = "UPDATE #__jacomment_items SET `userid` = '0' WHERE `id` ='" . $commentID . "'";
			$db->setQuery($query);
			$db->execute();
		}
		return false;
	}
	
	/**
	 * Update number of children of parent item
	 *
	 * @param integer $pId Parent item id
	 * 
	 * @return void
	 */
	function updateChildren($pId)
	{
		$db = Factory::getDBO();
		
		$pId = intval($pId);
		
		if (! $pId) {
			return;
		}
		
		$query = "SELECT parentid FROM #__jacomment_items WHERE id = '$pId'";
		$db->setQuery($query);
		$parentId = $db->loadResult();
		
		// count direct active children and get children value of its direct active children
		$query = "SELECT 
	    (SELECT COUNT(*) FROM #__jacomment_items WHERE parentid = '$pId' AND type = 1) 
	    + 
	    (SELECT SUM(active_children) FROM #__jacomment_items GROUP BY parentid HAVING parentid = '$pId')";
		$db->setQuery($query);
		$noActiveItems = $db->loadResult();
		
		// count direct children and get children value of its direct children
		$query = "SELECT 
	    (SELECT COUNT(*) FROM #__jacomment_items WHERE parentid = '$pId') 
	    + 
	    (SELECT SUM(children) FROM #__jacomment_items GROUP BY parentid HAVING parentid = '$pId')";
		$db->setQuery($query);
		$noAllItems = $db->loadResult();
		
		$query = "UPDATE #__jacomment_items 
                  SET `active_children` = '$noActiveItems', `children` = '$noAllItems'
                  WHERE `id` = '$pId'";
		$db->setQuery($query);
		if (! $db->execute()) {
			return;
		}
		
		if ($parentId) {
			self::updateChildren($parentId);
		} else {
			return;
		}
	}
	
	/**
	 * Get p0 value of parent item
	 * 
	 * @param integer $parentId Id of parent item
	 * @param integer $id 		Id of item
	 * 
	 * @return integer 0 if getting error, otherwise return p0 value
	 */
	function updateP0FromParent($parentId, $id)
	{
		$db = Factory::getDBO();
		
		$parentId = intval($parentId);
		$id = intval($id);
		
		if ($parentId) {
			$query = "SELECT p0 FROM #__jacomment_items WHERE id = '$parentId'";
			$db->setQuery($query);
			$p0 = intval($db->loadResult());
			
			$query = "UPDATE #__jacomment_items SET p0 = '$p0' WHERE id = '$id'";
		} else {
			// it is a level 0 item
			$query = "UPDATE #__jacomment_items SET p0 = '$id' WHERE id = '$id'";
		}
		
		$db->setQuery($query);
		
		$db->execute();
	}
}
?>