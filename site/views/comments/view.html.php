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
	JLoader::register('JACView', JPATH_BASE.'/components/com_jacomment/views/view.php');
}

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;

/**
 * JACommentViewComments View
 *
 * @package		Joomla.Site
 * @subpackage	JAComment
 */
class JACommentViewComments extends JACView
{
	var $lists = array();
	/**	
	 * Display the view
	 * 
	 * @param string $tmpl The template file to include
	 * 
	 * @return void
	 */
	function display($tmpl = null)
	{
		
		$app = Factory::getApplication();
		switch ($this->getLayout()) {
			case 'preview':
				$this->showScript();
				break;
			case 'showchild':
				$this->setLayout('default');
				$this->showChilds();
				break;
			case 'paging':
				$this->setLayout('default');
				$this->pagingData();
				break;
			case 'sort':
				$this->setLayout('default');
				$this->sortComment();
				break;
			case 'button':
				$this->showLinks();
				break;
			case 'youtube':
				$this->showYouTube();
				break;
			case 'attach_file':
				$this->showAttachFile();
				break;
			case 'showformedit':
				$this->setLayout('default');
				$this->showFormEdit();
				break;
			case 'showreply':
				$this->setLayout('default');
				$this->showFormReply();
				break;
			case 'changetype':
				$this->setLayout('default');
				$this->changeType();
				break;
			case 'showvotedlist':
				$this->setLayout('default');
				$this->showVotedList();
				break;
			default:
				$this->displayItems();
				break;
		}
		
		parent::display($tmpl);
	}
	
	/**
	 * Show reply form
	 * 
	 * @return void
	 */
	function showFormReply()
	{
		global $jacconfig;
		$helper = new JACommentHelpers();
		$currentUserInfo = Factory::getUser();
		$object = array();
		$k = 0;
		$message = '<script type="text/javascript">jacdisplaymessage();</script>';
		if ($jacconfig['permissions']->get('post', 'all') == "member" && $currentUserInfo->guest) {
			$object[$k] = new stdClass();
			$object[$k]->id = '#jac-msg-succesfull';
			$object[$k]->type = 'html';
			$object[$k]->content = JText::_("YOU_MUST_LOGIN_TO_POST_COMMENT") . $message;
			$k++;
		} else {
			$inputs = Factory::getApplication()->input;
			$currentTotal = $inputs->getInt("currenttotal", 0);
			if ($currentTotal >= $jacconfig["comments"]->get("maximum_comment_in_item", 1)) {
				$object[$k] = new stdClass();
				$object[$k]->id = '#jac-msg-succesfull';
				$object[$k]->type = 'html';
				$object[$k]->content = JText::_("COMMENT_IN_THIS_ARTICLE_IS_FULL_TEXT") . $message;
				$k++;
			} else {
				$this->id = $inputs->getInt("id", 0);
				$this->replyto = $inputs->getCmd("replyto", '');
				
				$message = '<script type="text/javascript">actionBeforEditReply("' . $inputs->getInt("id", 0) . '", "' . JText::_("REPLY") . '", "reply", "' . JText::_("POSTING") . '");jacChangeDisplay("jac-result-reply-comment-' . $inputs->get("id", 0) . '", "block")</script>';
				
				$object[$k] = new stdClass();
				$object[$k]->id = '#jac-result-reply-comment-' . $inputs->getInt("id", 0);
				$object[$k]->type = 'html';
				$object[$k]->content = $this->loadTemplate('reply') . $message;
				$k++;
			}
		}
		echo $helper->parse_JSON_new($object);
		exit();
	}
	
	/**
	 * Show a comment
	 * 
	 * @param object $item Item object
	 * 
	 * @return void
	 */
	function showComment($item)
	{
		global $jacconfig;
		$inputs = Factory::getApplication()->input;
		$isReply = $inputs->getInt("isreply", 0);
		
		$item = $this->assignItems($item);
		$this->isreply = $isReply;
		$this->items = $item;
		if ($isReply) {
			$this->ischild = '1';
		}
		
		return $this->loadTemplate('comments');
	}
	
	/**
	 * Show edit form
	 * 
	 * @return void
	 */
	function showFormEdit()
	{
		global $jacconfig, $isEnableAutoexpanding;
		$model = $this->getModel();
		$helper = new JACommentHelpers();
		
		$inputs = Factory::getApplication()->input;
		$id = $inputs->getInt("id", 0);
		$item = $model->getItem($id);
		
		$app = Factory::getApplication();
		
		$this->item = $item;
		$this->id = $id;
		$this->fucSmiley = "jacInsertSmileyEdit";
		$theme = $jacconfig["layout"]->get("theme", "default");
		$session = Factory::getSession();
		if ($inputs->getCmd("jacomment_theme", '')) {
			jimport('joomla.filesystem.folder');
			$themeURL = $inputs->getCmd("jacomment_theme");
			if (JFolder::exists('components/com_jacomment/themes/' . $themeURL) || (JFolder::exists('templates/' . $app->getTemplate() . '/html/com_jacomment/themes/' . $themeURL))) {
				$theme = $themeURL;
			}
			$session->set('jacomment_theme', $theme);
		} else {
			if ($session->get('jacomment_theme', null)) {
				$theme = $session->get('jacomment_theme', $theme);
			}
		}
		$this->theme = $theme;
		
		$this->isAttachImage = $jacconfig["comments"]->get("is_attach_image", 0);
		$this->enableAfterTheDeadline = $jacconfig['layout']->get('enable_after_the_deadline', 0);
		$this->enableBbcode = $jacconfig['layout']->get('enable_bbcode', 0);
		$this->isEnableEmailSubscription = $jacconfig['comments']->get('is_enable_email_subscription', 1);
		$this->enableSmileys = $jacconfig['layout']->get('enable_smileys', 0);
		$this->totalAttachFile = $jacconfig['comments']->get('total_attach_file', 5);
		$this->enableYoutube = $jacconfig['layout']->get('enable_youtube', 1);
		$this->isEnableAutoexpanding = $isEnableAutoexpanding;
		$this->minLength = $jacconfig['spamfilters']->get('min_length', 0);
		$this->maxLength = $jacconfig['spamfilters']->get('max_length', 0);
		$this->enableCharacterCounter = $jacconfig['layout']->get('enable_character_counter', 0);
		$this->enableLocationDetection = $jacconfig['layout']->get('enable_location_detection', 0);
		
		$object = array();
		$k = 0;
		$object[$k] = new stdClass();
		$object[$k]->id = '#jac-edit-comment-' . $id;
		$object[$k]->type = 'html';
		ob_start();
		include $helper->jaLoadBlock("comments/edit.php");
		$content = ob_get_contents();
		ob_end_clean();
		$object[$k]->content = $content . '<script type="text/javascript">$("newcommentedit").focus();jacChangeDisplay("jac-edit-comment-' . $id . '","block")</script>';
		$k++;
		
		echo $helper->parse_JSON_new($object);
		exit();
	}
	
	/**
	 * Sort comment list
	 * 
	 * @return void
	 */
	function sortComment()
	{
		global $jacconfig;
		$inputs = Factory::getApplication()->input;
		$limit = $inputs->getInt('limit', 10);
		$limitstart = $inputs->getInt('limitstart', 0);
		
		$wherejatotalcomment = "";
		$wherejacomment = "";
		
		$this->buildWhereComment($wherejatotalcomment, $wherejacomment);
		
		$object = array();
		$k = 0;
		
		$object[$k] = new stdClass();
		$object[$k]->id = '#jac-container-comment';
		$object[$k]->type = 'html';
		$object[$k]->content = $this->loadContentChangeData($wherejatotalcomment, $wherejacomment, $limit, $limitstart, 'sort');
		$k++;
		
		$helper = new JACommentHelpers();
		echo $helper->parse_JSON_new($object);
		exit();
	}
	
	/**
	 * Show links
	 * 
	 * @return string Links
	 */
	function showLinks()
	{
		include_once JPATH_SITE . DS . 'components' . DS . 'com_jacomment' . DS . 'models' . DS . 'addons.php';
		$model = new JACommentModelAddons();
		$paramsArrays = $model->getScript();
		$this->paramsArray = $paramsArrays;
		
		$inputs = Factory::getApplication()->input;
		$links = $inputs->getString('links','');
		
		$this->links = $links;
		return $links;
	}
	
	/**
	 * Perform when user click page number, list limit or search comment
	 * 
	 * @return void
	 */
	function pagingData()
	{
		$model = $this->getModel();
		
		$inputs = Factory::getApplication()->input;
		$limitstart = $inputs->getCmd('limitstart', '0');
		$limit = $inputs->getCmd('limit', '10');
		$wherejatotalcomment = "";
		$wherejacomment = "";
		
		$this->buildWhereComment($wherejatotalcomment, $wherejacomment);
		
		$object = array();
		$k = 0;
		
		$object[$k] = new stdClass();
		$object[$k]->id = '#jac-container-comment';
		$object[$k]->type = 'html';
		$object[$k]->content = $this->loadContentChangeData($wherejatotalcomment, $wherejacomment, $limit, $limitstart, 'paging');
		$k++;
		
		$this->getObjectPaging($object, $k);
		
		$helper = new JACommentHelpers();
		echo $helper->parse_JSON_new($object);
		exit();
	}
	
	/**
	 * Get paging object
	 * 
	 * @param object  &$object Paging object
	 * @param integer &$k	   Object index
	 * 
	 * @return void
	 */
	function getObjectPaging(&$object, &$k)
	{
		$lists = array();
		$this->getPaging($lists);
		$helper = new JACommentHelpers();
		
		$object[$k] = new stdClass();
		$object[$k]->id = '#jac-pagination';
		$object[$k]->type = 'html';
		ob_start();
		include $helper->jaLoadBlock("comments/paging.php");
		$content = ob_get_contents();
		ob_end_clean();
		$object[$k]->content = $content;
	}
	
	/**
	 * Show children comment
	 * 
	 * @return void
	 */
	function showChilds()
	{
		$inputs = Factory::getApplication()->input;
		$parentID = $inputs->getInt('parentid', 0);
		$wherejatotalcomment = "";
		$wherejacomment = "";
		
		$this->buildWhereComment($wherejatotalcomment, $wherejacomment);
		
		$k = 0;
		$object = array();
		$object[$k] = new stdClass();
		$object[$k]->id = '#childen-comment-of-' . $parentID;
		$object[$k]->type = 'html';
		$object[$k]->content = $this->loadContentChangeData($wherejatotalcomment, $wherejacomment, '', '', 'getChilds');
		$k++;
		
		$helper = new JACommentHelpers();
		echo $helper->parse_JSON_new($object);
		exit();
	}
	
	/**
	 * When action is completed, load data again
	 * 
	 * @param string  $searchTotal Criteria to search total of comments
	 * @param string  $search	   Criteria to search comments
	 * @param integer $limit	   Limit records
	 * @param integer $limitstart  Offset start position
	 * @param string  $action	   Action when load data
	 * @param integer $commentID   Comment id
	 * 
	 * @return string Comment template
	 */
	function loadContentChangeData($searchTotal = '', $search = '', $limit = 10, $limitstart = 0, $action = '', $commentID = 0)
	{
		global $jacconfig;
		
		$inputs = Factory::getApplication()->input;
		$orderBy = $inputs->get('orderby', '');
		if ($inputs->getString('typeorderby', '')) {
			$orderBy .= " " . $inputs->getCmd('typeorderby', '');
		}
		$model = $this->getModel();
		$itemAll = array();
		if ($action == "getChilds") {
			$indexOf = strpos($search, "AND c.parentid =");
			$searchAll = substr($search, 0, $indexOf);
			$itemAll = $model->getItemsFrontEnd($searchAll, "all", $limitstart, $orderBy);
			$items = $model->getChildItems($search, $limit, $limitstart, '', '', '', $commentID);
			$this->ischild = 1;
		} else {
			$searchAll = str_replace("AND c.parentid = 0", "", $search);
			$itemAll = $model->getItemsFrontEnd($searchAll, "all", $limitstart, $orderBy);
			$items = $model->getItemsFrontEnd($search, $limit, $limitstart, $orderBy);
		}
		
		if ($jacconfig["comments"]->get("is_show_child_comment")) {
			$structItemAll = $this->buildStructParent($itemAll);
			$results = array();
			$this->getArrayChildren($items, $structItemAll, $itemAll, $results, 0);
			
			$this->searchItems = $results;
			
			$items = $this->assignItems($items, $itemAll);
		} else {
			$items = $this->assignItems($items, $itemAll);
		}
		
		$lists['order'] = $orderBy;
		$this->lists = &$lists;
		
		$this->items = $items;
		
		return $this->loadTemplate('comments');
	}
	
	/*
	 * 
	 */
	/**
	 * Get parameters from URL
	 * 
	 * @param string $url URL string
	 * 
	 * @return array List of parameters from URL
	 */
	function getVarFromUrl($url)
	{
		$str = explode("&", $url);
		foreach ($str as $k => $v) {
			$str_arr[] = explode("=", $v);
		}
		foreach ($str_arr as $key => $val) {
			$arr[$val[0]] = $val[1];
		}
		return $arr;
	}
	
	/**
	 * Build WHERE criteria
	 * 
	 * @param string &$wherejatotalcomment Criteria to get total of comments
	 * @param string &$wherejacomment	   Criteria to get comments
	 * 
	 * @return void
	 */
	function buildWhereComment(&$wherejatotalcomment, &$wherejacomment)
	{
		$helper = new JACommentHelpers();
		$inputs = Factory::getApplication()->input;
		$contentOption = $inputs->getCmd('contentoption', '');
		$contentID = $inputs->getInt('contentid', 0);
		$commentType = $inputs->getInt('commenttype', 1);
		$parentID = $inputs->getInt('parentid', 0);
		
		$wherejatotalcomment = " AND c.option= '" . $contentOption . "'";
		
		//check user is specialUser
		$isSpecialUser = $helper->isSpecialUser();
		//get aproved comment if user isn't special User
		if (! $isSpecialUser) {
			$wherejatotalcomment .= " AND c.type = " . $commentType;
		}
		$wherejatotalcomment .= " AND c.contentid= '" . $contentID . "'";
		
		$wherejacomment = $wherejatotalcomment;
		$wherejacomment .= " AND c.parentid = " . $parentID . "";
	}
	
	/**
	 * Display items
	 * 
	 * @return void
	 */
	function displayItems()
	{
		global $option, $jacconfig;
		$app = Factory::getApplication();
		$helper = new JACommentHelpers();
		$inputs = Factory::getApplication()->input;
		$task = $inputs->getCmd('task');
		$model = $this->getModel();
		$isCommentJavoice = $jacconfig["general"]->get("is_comment_javoice", 0);
		$comment_javoice_level = $jacconfig["general"]->get("comment_javoice_level", 1);
		$contentoption = $inputs->getCmd('contentoption');
		if ($task == 'edit') {
			$cid = $inputs->get('cid', array(0), '', '');
			ArrayHelper::toInteger($cid, array(0));
			$item = $model->getItem($cid[0]);
			$this->item = $item;
		} else {
			//check user is specialUser
			$isSpecialUser = $helper->isSpecialUser();
			if ($task == 'preview') {
				$limit = $inputs->getInt('limit', $jacconfig["comments"]->get("number_comment_in_page", 10));
				$limitstart = $inputs->getInt('limitstart', 0);
				$search = '';
				
				$opt = 'com_content';
				$contentid = 45;
				
				$lists['contentoption'] = $opt;
				$search .= ' AND c.option="' . $opt . '"';
				
				$lists['contentid'] = $contentid;
				$search .= ' AND c.contentid=' . $contentid . '';
				
				$lists['parentid'] = 0;
				$lists['commenttype'] = 1;
				
				$orderBy = $jacconfig['layout']->get('default_sort', 'date');
				$orderBy .= " " . $jacconfig['layout']->get('default_sort_type', 'ASC');
				
				$lists['searchtotal'] = $search;
				$totalType = $model->getTotalByType($search);
				if ($totalType) {
					$totalAll = (int) array_sum($totalType);
				} else {
					$totalAll = 0;
				}
				
				$search .= ' AND parentid = 0';
				$lists['search'] = $search;
				
				$lists['order'] = "";
				$this->lists = &$lists;
				
				$this->totalAll = $totalAll;
				
				$custom_addthis = '';
				if ($inputs->getInt('enable_addthis') == 1) {
					$custom_addthis = $jacconfig['layout']->get('custom_addthis');
				}
				$this->custom_addthis = $custom_addthis;
				
				$custom_addtoany = '';
				if ($inputs->getInt('enable_addtoany') == 1) {
					$custom_addtoany = $jacconfig['layout']->get('custom_addtoany');
				}
				$this->custom_addtoany = $custom_addtoany;
				
				$custom_tweetmeme = '';
				/*
				if ($inputs->getInt('enable_tweetmeme') == 1) {
					$custom_tweetmeme = $jacconfig['layout']->get('custom_tweetmeme');
				}
				*/
				$this->custom_tweetmeme = $custom_tweetmeme;
				
				$this->preview_enable_youtube = $inputs->getInt('enable_youtube', $jacconfig["layout"]->get("enable_youtube", 1));
				
				$this->preview_enable_bbcode = $inputs->getInt('enable_bbcode', $jacconfig["layout"]->get("enable_bbcode", 1));
				
				$this->preview_enable_after_the_deadline = $inputs->getInt('enable_after_the_deadline', $jacconfig["layout"]->get("enable_after_the_deadline", 0));
				
				$this->preview_enable_smileys = $inputs->getInt('enable_smileys', $jacconfig["layout"]->get("enable_smileys", 0));
			} else {
				$search = '';
				
				$lists['contentoption'] = $inputs->getCmd('contentoption', '');
				if ($lists['contentoption']) {
					$search .= " AND c.option='{$lists['contentoption']}'";
				}
				
				$lists['contentid'] = $inputs->getInt('contentid', 0);
				if ($lists['contentid']) {
					$search .= ' AND c.contentid=' . (int) $lists['contentid'] . '';
				}
				
				$lists['parentid'] = 0;
				$lists['commenttype'] = 1;
				
				$orderBy = $jacconfig['layout']->get('default_sort', 'date');
				$orderBy .= " " . $jacconfig['layout']->get('default_sort_type', 'ASC');
				
				//get aproved comment if user isn't special User
				if (! $isSpecialUser) {
					$search .= ' AND type=1';
				}
				
				$lists['searchtotal'] = $search;
				$totalType = $model->getTotalByType($search);
				if ($totalType) {
					$totalAll = (int) array_sum($totalType);
				} else {
					$totalAll = 0;
				}
				
				$search .= ' AND parentid = 0';
				
				$lists['search'] = $search;
				
				$limit = $inputs->getInt('limit', $jacconfig["comments"]->get("number_comment_in_page", 10));
				$limitstart = $inputs->getInt('limitstart', 0);
				$lists['order'] = "";
				$this->lists = &$lists;
				$this->totalAll = $totalAll;
			}
			//get Rss Link
			$linkRss = 'index.php?option=com_jacomment&amp;view=jafeeds&amp;layout=rss&amp;contentid=' . $lists['contentid'] . '&amp;contentoption=' . $lists['contentoption'] . '&amp;tmpl=component';
			$this->linkRss = $linkRss;
			
			//get smiley
			$this->fucSmiley = "jacInsertSmiley";
			$this->id = "";
			
			//--assign item in to html
			$searchAll = str_replace(" AND parentid = 0", "", $search);
			$currentCommentID = $inputs->getInt('currentCommentID', 0);
			if ($currentCommentID != 0) {
				$itemAll = $model->getItemsFrontEnd($searchAll, "all", $limitstart, $orderBy);
				$itemAllNoStruct = $itemAll;
				
				$currentItem = array();
				$currentItem = $this->getCurrentItem($itemAll, $currentCommentID);
				
				//if found this item
				if (isset($currentItem) && isset($currentItem->id)) {
					if ($currentItem->parentid == 0) {
						//if it is hasn't parent
						$this->buildParentAndLimitStart($itemAll, $limitstart, $currentCommentID);
						$inputs->set("limitstart", $limitstart);
						$inputs->set("limit", 10);
						$items = $model->getItemsFrontEnd($search, $limit, $limitstart, $orderBy);
						if ($jacconfig["comments"]->get("is_show_child_comment")) {
							$results = array();
							$this->getArrayChildren($items, $itemAll, $itemAllNoStruct, $results, 0);
							$this->searchItems = $results;
							$items = $this->assignItems($items, $itemAll, $itemAllNoStruct, "highLight");
						} else {
							$items = $this->assignItems($items, $itemAll, $itemAllNoStruct, "highLight");
						}
					} else {
						if ($jacconfig["comments"]->get("is_show_child_comment")) {
							//if it has parent
							$searchItems = array();
							$limitstart = 0;
							$rootParentID = 0;
							
							$structItemAll = $this->buildStructParent($itemAll);
							//get array items
							$this->getArrayParent($structItemAll, $itemAll, $currentItem, $searchItems, $rootParentID);
							
							$limitstart = $this->getLimitStart($itemAll, $rootParentID);
							
							$inputs->set("limitstart", $limitstart);
							$inputs->set("limit", 10);
							
							$items = $model->getItemsFrontEnd($search, $limit, $limitstart, $orderBy);
							
							$results = array();
							$this->getArrayChildren($items, $structItemAll, $itemAll, $results, 0);
							$this->searchItems = $results;
							
							$items = $this->assignItems($items, $structItemAll, $itemAllNoStruct, "highLight");
						} else {
							$searchItems = array();
							$limitstart = 0;
							$rootParentID = 0;
							
							$structItemAll = $this->buildStructParent($itemAll);
							//get array items
							$this->getArrayParent($structItemAll, $itemAll, $currentItem, $searchItems, $rootParentID);
							
							//get and get limitstart base on root parent
							//$rootParentID = $searchItems[count($searchItems)-1][0]->parentid;
							$limitstart = $this->getLimitStart($itemAll, $rootParentID);
							
							$inputs->set("limitstart", $limitstart);
							$inputs->set("limit", 10);
							
							$items = $model->getItemsFrontEnd($search, $limit, $limitstart, $orderBy);
							$items = $this->assignItems($items, $structItemAll, $itemAllNoStruct, "highLight");
							
							$this->searchItems = $searchItems;
							$this->rootParentID = $rootParentID;
						}
					}
				} else {
					//don't find it in database - display nomal
					$items = $model->getItemsFrontEnd($search, $limit, $limitstart, $orderBy);
					if ($jacconfig["comments"]->get("is_show_child_comment")) {
						$structItemAll = $this->buildStructParent($itemAll);
						$results = array();
						$this->getArrayChildren($items, $structItemAll, $itemAll, $results, 0);
						$this->searchItems = $results;
						
						$items = $this->assignItems($items, $itemAll);
					} else {
						$items = $this->assignItems($items, $itemAll);
					}
				}
			} else {
				$items = $model->getItemsFrontEnd($search, $limit, $limitstart, $orderBy);
				
				// ++ Edited by NhatNX
				// Get parentid of displayed items
				if ($items) {
					$arrParentId = array_map(array("JACommentViewComments", "getIdsOfParent"), $items);
				} else {
					$arrParentId = array(0);
				}
				$searchAllChildren = ' AND p0 IN (' . implode(',', $arrParentId) . ')';
				
				$itemAll = $model->getItemsFrontEnd($searchAll, 'all', $limitstart, $orderBy, '', '', $searchAllChildren);
				// -- Edited by NhatNX

				if ($jacconfig["comments"]->get("is_show_child_comment")) {
					$structItemAll = $this->buildStructParent($itemAll);
					$results = array();
					$this->getArrayChildren($items, $structItemAll, $itemAll, $results, 0);
					
					$this->searchItems = $results;
					
					$items = $this->assignItems($items, $itemAll);
				} else {
					$items = $this->assignItems($items, $itemAll);
				}
			}
			if($isCommentJavoice && trim($contentoption)=='com_javoice'){
				$total = 0;
				
				if(isset($itemAll)){
					foreach ($itemAll AS $i){
						if(is_array($i)){
							$i_id = $i['0']->id;
						}else{
							$i_id = $i->id;
						}
						if($helper->checkItem($i_id,$comment_javoice_level)){
							$total++;
						}
					}
					$this->totalAll = $total;
				}
			}
			$this->items = $items;
			$this->currentCommentID = $currentCommentID;
			
			// get authors in conversation
			$authors = NULL;
			if ($items) {
				$authors = $helper->getConversationAuthors($lists['contentoption'], $lists['contentid']);
			}
			$this->authors = $authors;
			//get paging
			$this->getPaging($lists);
			if ($task != 'preview') {
				$_document = new JDocument();
				$charset = $_document->getCharset();
				$app->setHeader('Content-Type', "text/html; charset=$charset", true);
				
				$body = $this->loadTemplate("block");
				
				$app->setBody($body);
				echo $app->toString();
				exit();
			}
			//prview comment
			echo "<div id='jac-wrapper'>" . $this->loadTemplate("block") . "</div>";
		}
	}
	
	/**
	 * Get array of children
	 * 
	 * @param array   $items 	  Items
	 * @param array   $itemStruct Structured items in array
	 * @param array   $itemAll	  All items
	 * @param array   &$results	  Result array
	 * @param integer $level	  Level of children
	 * 
	 * @return void
	 */
	function getArrayChildren($items, $itemStruct, $itemAll, &$results, $level)
	{
		if (! $items) {
			return null;
		}
		
		foreach ($items as $item) {
			if (isset($itemStruct[$item->id])) {
				$results[$item->id] = $this->assignItems($itemStruct[$item->id], $itemStruct, $itemAll, "highLight");
				$this->getArrayChildren($itemStruct[$item->id], $itemStruct, $itemAll, $results, $level + 1);
			}
		}
	}
	
	/**
	 * Show array items when pass items
	 * 
	 * @param array   $items 			Items
	 * @param array	  $searchItems 		Search items
	 * @param integer $currentCommentID Current comment id
	 * @param integer $rootParentID 	Root parent id
	 * 
	 * @return string Comments template
	 */
	function showItems($items, $searchItems, $currentCommentID, $rootParentID)
	{
		$this->items = $items;
		$this->currentCommentID = $currentCommentID;
		$this->ischild = 1;
		$this->searchItems = $searchItems;
		$this->subParentID = $rootParentID;
		return $this->loadTemplate('comments');
	}
	
	/**
	 * Get array of parent
	 * 
	 * @param array   $structItemAll  Structured items array
	 * @param array   $itemAll		  All items array
	 * @param object  $item			  Item object
	 * @param array   &$searchItems	  Result items array
	 * @param integer &$rootParentID  Root parent id
	 * 
	 * @return void
	 */
	function getArrayParent($structItemAll, $itemAll, $item, &$searchItems, &$rootParentID)
	{
		//assign value for search item
		$tmpArray = $structItemAll[$item->parentid];
		$tmpArray = $this->assignItems($tmpArray, $structItemAll, $itemAll, "highLight");
		
		$searchItems[$item->parentid] = $tmpArray;
		$currentItem = $this->getCurrentItem($itemAll, $item->parentid);
		if ($currentItem->parentid != 0) {
			$this->getArrayParent($structItemAll, $itemAll, $currentItem, $searchItems, $rootParentID);
		} else {
			$rootParentID = $item->parentid;
		}
	}
	
	/**
	 * Get current item when pass itemId
	 * 
	 * @param array   $itemAll			All items array
	 * @param integer $currentCommentID Current comment id
	 * 
	 * @return mixed Item object if it exist, otherwise false
	 */
	function getCurrentItem($itemAll, $currentCommentID)
	{
		foreach ($itemAll as $item) {
			if ($item->id == $currentCommentID) {
				return $item;
			}
		}
		return false;
	}
	
	/**
	 * Get limit start number
	 * 
	 * @param array   $itemAll			Array of all items
	 * @param integer $currentCommentID Current comment id
	 * 
	 * @return string Limit start number
	 */
	function getLimitStart($itemAll, $currentCommentID)
	{
		$limitStart = 0;
		foreach ($itemAll as $item) {
			if ($item->parentid == 0 && $currentCommentID != 0) {
				$limitStart++;
				if ($item->id == $currentCommentID) {
					break;
				}
			}
		}
		$limitStart = intval(($limitStart / 10)) * 10;
		return $limitStart . "";
	}
	
	/**
	 * Build struct of all items and get number page of this item
	 * 
	 * @param array   &$itemAll			Array of all items
	 * @param array	  &$limitStart		Offset start position
	 * @param integer $currentCommentID Current comment id
	 * 
	 * @return void
	 */
	function buildParentAndLimitStart(&$itemAll, &$limitStart, $currentCommentID)
	{
		$children = array();
		$list = array();
		$position = 0;
		$inputs = Factory::getApplication()->input;
		$limit = $inputs->getInt('limit', 10);
		
		foreach ($itemAll as $item) {
			$pt = $item->parentid;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push($list, $item);
			$children[$pt] = $list;
			if ($item->parentid == 0 && $currentCommentID != 0) {
				if ($item->id == $currentCommentID) {
					$currentCommentID = 0;
				}
				$position++;
			}
		}
		
		//0 - 0, 10 - 0, 20 -10, 30 - 20
		if (($position % $limit) == 0) {
			if ($position > $limit) {
				$limitStart = $position - $limit;
			}
		} else {
			$limitStart = intval(($position / $limit)) * $limit;
		}
		
		$itemAll = $children;
	}
	
	/**
	 * Build struct of parent
	 * 
	 * @param array $itemAll Array of all items
	 * 
	 * @return array Array of children items
	 */
	function buildStructParent($itemAll)
	{
		if (! $itemAll) {
			return null;
		}
		
		$children = array();
		$list = array();
		
		foreach ($itemAll as $item) {
			$pt = $item->parentid;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push($list, $item);
			$children[$pt] = $list;
		
		}
		return $children;
	}
	
	/**
	 * Get number of children items
	 * 
	 * @param array   $itemAll		  Array of all items
	 * @param integer $itemID		  Item id
	 * @param integer &$countChildren Count of children
	 * 
	 * @return void
	 */
	function getNumberOfChildrent($itemAll, $itemID, &$countChildren)
	{
		if (isset($itemAll[$itemID])) {
			$countChildren += count($itemAll[$itemID]);
			foreach ($itemAll[$itemID] as $arr) {
				$this->getNumberOfChildrent($itemAll, $arr->id, $countChildren);
			}
		}
	}
	
	/**
	 * Assign some attributes to items
	 * 
	 * @param array  $items			  Array of items need to be assigned attributes
	 * @param array  $itemAll		  Array of all items
	 * @param array  $itemAllNoStruct Array of all unstructured items
	 * @param string $actionCall	  Action when assign items
	 * 
	 * @return array Array of assigned items
	 */
	function assignItems($items, $itemAll = 0, $itemAllNoStruct = 0, $actionCall = '')
	{
		global $jacconfig;
		$helper = new JACommentHelpers();
		$model = $this->getModel();
		$inputs = Factory::getApplication()->input;
		$parentID = $inputs->getInt('parentid', 0);
		$parentArray = array();
		
		if (! isset($jacconfig['permissions'])) {
			$jacconfig['permissions'] = new JRegistry;
			$jacconfig['permissions']->loadString('{}');
		}
		if (! isset($jacconfig['comments'])) {
			$jacconfig['comments'] = new JRegistry;
			$jacconfig['comments']->loadString('{}');
		}
		if (! isset($jacconfig['layout'])) {
			$jacconfig['layout'] = new JRegistry;
			$jacconfig['layout']->loadString('{}');
		}
		$avatarSize = $jacconfig["layout"]->get("avatar_size", 1);
		$avatarType = $jacconfig["layout"]->get("type_avatar", 0);
		$reportComment = $jacconfig["comments"]->get("is_allow_report", 1);
		$typeAllowReport = $jacconfig['permissions']->get('report', "all");
		$currentUserInfo = Factory::getUser();
		$isAllowVoting = $jacconfig['comments']->get('is_allow_voting', 1);
		$voteComment = $jacconfig['permissions']->get('vote', "all");
		$typeVote = $jacconfig['permissions']->get('type_voting', 1);
		$enableTimestamp = $jacconfig['layout']->get('enable_timestamp', 1);
		$typeEditing = $jacconfig['permissions']->get('type_editing', 1);
		$displayUserInfo = $jacconfig['comments']->get('display_user_info', 'fullname');
		$sessionAddnew = array();
		if ($typeEditing == 2) {
			// Returns a reference to the global JSession object, only creating it if it doesn't already exist
			$session = Factory::getSession();
			// Get a value from a session var
			$sessionAddnew = $session->get('jacaddNew', null);
		}
		$lagEditing = $jacconfig['permissions']->get('lag_editing', 172800);
		
		//check user is specialUser
		$isSpecialUser = $helper->isSpecialUser();
		
		if ($itemAll) {
			if ($actionCall == "") {
				$itemAllNoStruct = $itemAll;
				$itemAll = $this->buildStructParent($itemAll);
			}
		}
		
		for ($i = 0; $i < count($items); $i++) {
			$item = $items[$i];
			
			//get level for item
			if ($i == 0) {
				$item->level = 0;
				//use itemAll with NoStruct for search level of comment
				$this->getLevelOfComment($item->id, $item->level, $itemAllNoStruct);
			}
			
			//if current user is special user pass parent type
			$item->parentType = 1;
			if ($isSpecialUser) {
				//$item->parentid
				if ($item->parentid > 0) {
					$item->parentType = $this->getTypeOfComment($item->parentid, $itemAllNoStruct);
				}
			}
			
			//BEGIN - get info of user
			$userInfo = Factory::getUser($item->userid);
			if ($userInfo->id == 0) {
				$item->strUser = $item->name;
				$item->strEmail = $item->email;
				
				if ($item->website && stristr($item->website, 'http://') === false) {
					$item->strWebsite = 'http://' . $item->website;
				} else {
					$item->strWebsite = $item->website;
				}
			} else {
				if ($displayUserInfo == "fullname") {
					$item->strUser = $userInfo->name;
				} else {
					$item->strUser = $userInfo->username;
				}
				$item->strEmail = $userInfo->email;
				$item->strWebsite = '';
			}
			
			$item->isCurrentUser = 0;
			if ($currentUserInfo->id == $userInfo->id && $userInfo->id != 0) {
				$item->isCurrentUser = 1;
			}
			
			$item->isSpecialUser = 0;
			if ($helper->isSpecialUser($userInfo->id, 'check')) {
				$item->isSpecialUser = 1;
			}
			
			$item->rpx_avatar = '';
			$item->icon = '';
			if (isset($item->usertype) && $item->usertype) {
				$itemUserInfo = Factory::getUser($item->userid);
				$item->paramsUser = new JRegistry;
				$item->paramsUser->loadString($itemUserInfo->params);
				if (is_object($item->paramsUser)) {
					if ($item->paramsUser->get("providerName")) {
						if ($item->paramsUser->get("providerName") == 'Twitter' || $item->paramsUser->get("providerName") == 'Facebook') {
							if ($item->paramsUser->get("photo")) {
								$item->rpx_avatar = $item->paramsUser->get("photo");
								$item->icon = '<img height="16" width="16" class="jac-provider-icon-' . $avatarSize . '" alt="' . $item->paramsUser->get("providerName") . '" src="' . JURI::base() . 'components/com_jacomment/asset/images/' . strtolower($item->paramsUser->get("providerName")) . '.ico" />';
							}
							$item->strUser = $item->paramsUser->get("displayName");
							$item->strWebsite = $item->paramsUser->get("url");
						
						} else if ($item->paramsUser->get("providerName") == 'Yahoo!') {
							$item->icon = '<img height="16" width="16" class="jac-provider-icon-' . $avatarSize . '" alt="' . $item->paramsUser->get("providerName") . '" src="' . JURI::base() . 'components/com_jacomment/asset/images/' . strtolower($item->paramsUser->get("providerName")) . '.gif" />';
						}
					}
				}
			}
			$tmpAvatar = $helper->getAvatar($userInfo->id, 0, 0, $avatarType, $item->strEmail);
			//only pass avatar link in 0
			if (empty($tmpAvatar)) {
				$item->avatar = '';
			} else {
				if (! is_array($tmpAvatar[0])) {
					$item->avatar = $tmpAvatar;
				} else {
					$item->avatar[] = $tmpAvatar[0][0];
					$item->avatar[] = $tmpAvatar[1];
					$item->userLink = $tmpAvatar[0][1];
				}
			}
			
			//BEGIN - vote
			if ($item->voted == 0) {
				$item->totalVote = $item->voted;
				$item->jacVoteClass = "jac-vote0";
			} else if ($item->voted > 0) {
				$item->totalVote = "+" . $item->voted;
				$item->jacVoteClass = "jac-vote1";
			} else {
				$item->totalVote = $item->voted;
				$item->jacVoteClass = "jac-vote-1";
			}
			$item->isAllowVote = 0;
			//if user has been loged
			if (! $currentUserInfo->guest) {
				if ($isAllowVoting && ($currentUserInfo->id != $item->userid) && $this->isEnableCommentUser($currentUserInfo->id, $item->id, $typeVote)) {
					$item->isAllowVote = 1;
				}
			} else {
				//check email don't allow vote when new post
				$email = $inputs->getCmd('email', 0);
				if ($isAllowVoting && $this->isEnableCommentGuest($item->id, $typeVote) && $voteComment == "all" && ! $email) {
					$item->isAllowVote = 1;
				}
			
			}
			//END - vote

			$item->isDisableReportButton = 1;
			if (! $currentUserInfo->guest) {
				if ($this->isEnableReportCommentUser($currentUserInfo->id, $item->id)) {
					$item->isDisableReportButton = 0;
				}
			} else {
				if ($this->isEnableReportCommentGuest($item->id) && $typeAllowReport == "all") {
					$item->isDisableReportButton = 0;
				}
			}
			
			//END - get info of user			
			$item->isAllowEditComment = 0;
			if (! $isSpecialUser) {
				if ($item->userid == $currentUserInfo->id && $currentUserInfo->id != 0) {
					if ($typeEditing == 1) {
						$item->isAllowEditComment = 1;
					} else if ($typeEditing == 2) {
						if (isset($sessionAddnew) && count($sessionAddnew) > 1) {
							if (in_array($item->id, $sessionAddnew)) {
								$item->isAllowEditComment = 1;
							}
						}
					} else {
						if ((time() - strtotime($item->date)) <= $lagEditing) {
							$item->isAllowEditComment = 1;
						}
					}
				}
			} else {
				$item->isAllowEditComment = 1;
			}
		}
		
		return $items;
	}
	
	/**
	 * Get type of comment
	 * 
	 * @param integer $commentID 	   Comment id
	 * @param array   $itemAllNoStruct Array of all unstructured items
	 * 
	 * @return integer Comment type
	 */
	function getTypeOfComment($commentID, $itemAllNoStruct)
	{
		if (! $itemAllNoStruct) {
			return;
		}
		
		foreach ($itemAllNoStruct as $item) {
			if ($item->id == $commentID) {
				return $item->type;
			}
		}
	}
	
	/**
	 * Get level of comment
	 * 
	 * @param integer $id			   Id of item
	 * @param integer &$level		   Level of item
	 * @param array	  $itemAllNoStruct Array of all unstructured items
	 * 
	 * @return void
	 */
	function getLevelOfComment($id, &$level, $itemAllNoStruct)
	{
		$parentID = 0;
		if (! $itemAllNoStruct) {
			return;
		}
		foreach ($itemAllNoStruct as $comment) {
			if ($comment->id == $id) {
				$parentID = $comment->parentid;
			}
		}
		
		if ($parentID != 0) {
			$level++;
			$this->getLevelOfComment($parentID, $level, $itemAllNoStruct);
		}
	}
	
	/**
	 * Show editor
	 * 
	 * @param object $item Item object
	 * 
	 * @return void
	 */
	function showEditor($item = 0)
	{
		$this->item = $item;
		echo $this->loadTemplate("editor");
	}
	
	/**
	 * Show script
	 * 
	 * @param string $name Script name
	 * 
	 * @return string Script string
	 */
	function showScript($name = '')
	{
		$model = JACModel::getInstance('addons', 'JACommentModel');
		$addons = $model->getScript($name);
		
		return $addons;
	}
	
	/**
	 * Get limitation list
	 * 
	 * @param integer $limitstart Offset start position
	 * @param integer $limit	  Limit records
	 * @param string  $order	  Order by string
	 * 
	 * @return string HTML select list
	 */
	function getListLimit($limitstart, $limit, $order = '')
	{
		$array = array(5, 10, 15, 20, 50, 100);
		$list_html = array();
		foreach ($array as $value) {
			$list_html[] = JHTML::_('select.option', $value, $value);
		}
		//limitstart, limit, order
		$onchange = "$limitstart, $limit, '$order'";
		$inputs = Factory::getApplication()->input;
		$keyword = $inputs->getString('keyword','');
		$listID = "list";
		$limitstart = (int) $limitstart;
		$list_html = JHTML::_('select.genericlist', $list_html, $listID, ' onchange="jac_doPaging(' . $limitstart . ', this.value, \'' . $order . '\', \'' . $keyword . '\')"', 'value', 'text', $limit);
		return $list_html;
	}
	
	/**
	 * Paging page
	 * 
	 * @param array &$lists List of parameters for paging
	 * 
	 * @return void
	 */
	function getPaging(&$lists)
	{
		$model = $this->getModel();
		$inputs = Factory::getApplication()->input;
		$limitstart = $inputs->getInt('limitstart', 0);
		$limit = $inputs->getCmd('limit', '');
		
		$keyword = $inputs->getString('keyword','');
		
		if (! $limitstart) {
			$inputs->set('limitstart', 0);
		}
		if (! $limit) {
			$inputs->set('limit', 0);
		}
		if (! $keyword) {
			$inputs->set('keyword', '');
		}
		
		$link = '';
		if ($keyword) {
			$link = "index.php?keyword=" . $keyword;
		}
		
		if ($limit == '') {
			$getLists = $model->_getVars();
			$lists = array_merge($lists, $getLists);
			$pagination = $model->getPagination($lists['limitstart'], $lists['limit'], 'jac-pagination', $link);
			$this->lists = &$lists;
			$this->pagination = &$pagination;
		} else {
			$getLists['limitstart'] = $limitstart;
			$getLists['limit'] = $limit;
			$getLists['order'] = '';
			$lists = array_merge($lists, $getLists);
			
			$pagination = $model->getPagination($limitstart, $limit, 'jac-pagination', $link);
			
			$this->lists = &$lists;
			$this->pagination = &$pagination;
		}
	}
	
	/**
	 * Check enable or disable button report of comment when user is a guest
	 * 
	 * @param integer $id Comment id
	 * 
	 * @return integer 1 if option is enabled, otherwise 0
	 */
	function isEnableReportCommentGuest($id)
	{
		$app = Factory::getApplication();
		$cookieName = JApplicationHelper::getHash($app->getName() . 'reportcomments' . $id);
		
		// ToDo - may be adding those information to the session?
		$inputs = Factory::getApplication()->input;
		$voted = $inputs->getInt($cookieName, 0);
		//this guest already vote for comment
		if ($voted) {
			return 0;
		} else {
			return 1;
		}
	}
	
	/**
	 * Check enable or disable report of comment when user has logged
	 * 
	 * @param integer $userid User id
	 * @param integer $id	  Comment id
	 * 
	 * @return integer 1 if option is enabled, otherwise 0
	 */
	function isEnableReportCommentUser($userid, $id)
	{
		$app = Factory::getApplication();
		//check display voting for comment
		include_once JPATH_SITE . DS . 'components' . DS . 'com_jacomment' . DS . 'models' . DS . 'logs.php';
		$modelLogs = new JACommentModelLogs();
		
		$logs = $modelLogs->getItemByUser($userid, $id);
		
		//----------Only one for each comment item----------
		if (! $logs || $logs->reports == 0) {
			return 1;
		} else {
			return 0;
		}
	}
	
	/**
	 * Check enable or disable vote of comment when user is a guest
	 * 
	 * @param integer $id		Comment id
	 * @param integer $typeVote Type of report
	 * 
	 * @return integer 1 if option is enabled, otherwise 0
	 */
	function isEnableCommentGuest($id, $typeVote)
	{
		$app = Factory::getApplication();
		switch ($typeVote) {
			case 2:
				//----------Only one for each comment item in a session-------- 
				$session = Factory::getSession();
				
				// Get a value from a session var
				$sessionVote = $session->get('vote', null);
				//if comment don't exit in session vote												
				if (isset($sessionVote[$id])) {
					return 0;
				} else {
					return 1;
				}
				break;
			default:
				//----------Only one for each comment item----------
				$cookieName = JApplicationHelper::getHash($app->getName() . 'comments' . $id);
				
				// ToDo - may be adding those information to the session?
				$inputs = Factory::getApplication()->input;
				$voted = $inputs->getInt($cookieName, 0);
				//this guest already vote for comment
				if ($voted) {
					return 0;
				} else {
					return 1;
				}
				break;
		}
	}
	
	/**
	 * Check enable or disable vote of comment when user has logged
	 * 
	 * @param integer $userid	User id
	 * @param integer $id		Comment id
	 * @param integer $typeVote Type of report
	 * 
	 * @return integer 1 if option is enabled, otherwise 0
	 */
	function isEnableCommentUser($userid, $id, $typeVote)
	{
		$app = Factory::getApplication();
		//check display voting for comment
		include_once JPATH_SITE . DS . 'components' . DS . 'com_jacomment' . DS . 'models' . DS . 'logs.php';
		$modelLogs = new JACommentModelLogs();
		
		$logs = $modelLogs->getItemByUser($userid, $id);
		switch ($typeVote) {
			case 2:
				//----------Only one for earch comment item in a session-------- 
				$session = Factory::getSession();
				
				// Get a value from a session var
				$sessionVote = $session->get('vote', null);
				
				//if comment don't exit in session vote												
				if (isset($sessionVote[$id])) {
					return 0;
				} else {
					return 1;
				}
				break;
			case 3:
				//----------use lag to voting----------------------
				if (! $logs || $logs->votes == 0) {
					return 1;
				} else {
					$timeExpired = $logs->time_expired;
					if (time() < $timeExpired) {
						return 0;
					} else {
						return 1;
					}
				}
				break;
			default:
				//----------Only one for earch comment item----------
				if (! $logs || $logs->votes == 0) {
					return 1;
				} else {
					return 0;
				}
				break;
		}
	}
	
	// ++ add by congtq 26/11/2009     
	/**
	 * Show YouTube
	 * 
	 * @return void
	 */
	function showYouTube()
	{
		$inputs = Factory::getApplication()->input;
		$cid = $inputs->get('cid', array(), 'array');
		ArrayHelper::toInteger($cid, array());
		$id = $cid[0] ? $cid[0] : '';
		$this->id = $id;
	}
	
	/**
	 * Show attached files
	 * 
	 * @return void
	 */
	function showAttachFile()
	{
		global $jacconfig;
		$app = Factory::getApplication();
		$inputs = Factory::getApplication()->input;
		$cid = $inputs->get('cid', array(0), 'array');
		ArrayHelper::toInteger($cid, array(0));
		$id = $cid[0] ? $cid[0] : '';
		$this->id = $id;
		
		$totalAttachFile = $jacconfig["comments"]->get("total_attach_file", 5);
		$this->totalAttachFile = $totalAttachFile;
		
		$theme = $jacconfig["layout"]->get("theme", "default");
		$session = Factory::getSession();
		if ($inputs->getCmd("jacomment_theme", '')) {
			jimport('joomla.filesystem.folder');
			$themeURL = $inputs->getCmd("jacomment_theme");
			if (JFolder::exists('components/com_jacomment/themes/' . $themeURL) || (JFolder::exists('templates/' . $app->getTemplate() . '/html/com_jacomment/themes/' . $themeURL))) {
				$theme = $themeURL;
			}
			$session->set('jacomment_theme', $theme);
		} else {
			if ($session->get('jacomment_theme', null)) {
				$theme = $session->get('jacomment_theme', $theme);
			}
		}
		$this->theme = $theme;
		
		$attachFileType = $jacconfig["comments"]->get("attach_file_type", "doc,docx,pdf,txt,zip,rar,jpg,bmp,gif,png");
		$this->attachFileType = $attachFileType;
		
		$listFiles = $inputs->get("listfile",'');
		$this->listFiles = $listFiles;
	}
	
	/**
	 * Show YouTube link
	 * 
	 * @param string $id Div id to contain link
	 * 
	 * @return void
	 */
	function showYouTubeLink($id)
	{
		$document = Factory::getDocument();
		
		$document->addScriptDeclaration("jQuery(document).ready( function() { jQuery('#" . $id . "').append('&nbsp;<a href=\"javascript:open_embed();\" class=\"plugin\"><img title=\"Add a YouTube Video\" alt=\"YouTube\" src=\"" . JURI::base() . "components/com_jacomment/asset/images/youtube.ico\"> <span>" . JText::_("EMBED_VIDEO") . "<\/span><\/a>'); });");
		
		$document->addScriptDeclaration("function open_embed(){ jacCreatForm('open_youtube',0,340,200,0,0,'" . JText::_("EMBED_A_YOUTUBE_VIDEO") . "',0,'" . JText::_("EMBED_VIDEO") . "'); }");
	}
	
	/**
	 * Show AfterDeadLine plug-in
	 * 
	 * @param string $id Div id to contain plug-in
	 * 
	 * @return void
	 */
	function showAfterDeadLineLink($id)
	{
		$document = Factory::getDocument();
		
		if (! defined('JACOMMENT_PLUGIN_ATD')) {
			JHTML::stylesheet('components/com_jacomment/libs/js/atd/atd.css');
			JHTML::script('components/com_jacomment/libs/js/atd/jquery.atd.js');
			JHTML::script('components/com_jacomment/libs/js/atd/csshttprequest.js');
			JHTML::script('components/com_jacomment/libs/js/atd/atd.js');
			
			define('JACOMMENT_PLUGIN_ATD', true);
		}
		
		$document->addScriptDeclaration("jQuery(document).ready( function() {jQuery('#" . $id . "').append('&nbsp;<a href=\"javascript:jac_check_atd(\'\')\"><img title=\"Proofread Comment w/ After the Deadline\" alt=\"AtD\" src=\"" . JURI::base() . "components/com_jacomment/asset/images/atd.gif\"> <span id=\"checkLink\">" . JText::_("CHECK_SPELLING") . "<\/span><\/a>');});");
	}
	// -- add by congtq 26/11/2009

	/**
	 * Show smileys
	 * 
	 * @param string  $id  Div id to contain plug-in
	 * @param integer $cid Comment id
	 * 
	 * @return string Smiley template
	 */
	function showSmileys($id = 0, $cid = 0)
	{
		//$cid = '';
		$cid = $cid ? $cid : '';
		if ($cid) {
			$func = 'jacInsertSmileyEdit';
		} else {
			$func = 'jacInsertSmiley';
		}
		
		$this->func = $func;
		$this->cid = $cid;
		
		return $this->loadTemplate('smiley');
	}
	
	/**
	 * Show BBCode plug-in
	 * 
	 * @param string  $id	 	  Div id to contain plug-in
	 * @param integer $cid	 	  Comment id
	 * @param string  $textAreaID Text area id
	 * 
	 * @return string BBCode template
	 */
	function showBBCode($id, $cid, $textAreaID)
	{
		if ($cid) {
			$func = 'insertBBcodeEdit';
		} else {
			$func = 'insertBBcode';
		}
		
		$this->func = $func;
		$this->cid = $cid;
		$this->textAreaID = $textAreaID;
		
		return $this->loadTemplate('bbcode');
	}
	
	// ++ add by congtq 02/12/2009  
	/**
	 * Show RPXNow plug-in
	 * 
	 * @param string $realm   Domain URL
	 * @param string $api_key API key
	 * @param string $id	  Div id to contain plug-in
	 * 
	 * @return void
	 */
	function showRPX($realm, $api_key, $id)
	{
		$document = Factory::getDocument();
		
		$tokenurl = urlencode(JURI::base() . 'index.php?' . $_SERVER['QUERY_STRING']);
		
		JHTML::script('https://rpxnow.com/openid/v2/widget');
		$document->addScriptDeclaration("RPXNOW.overlay = true; RPXNOW.language_preference = 'en';");
		$document->addScriptDeclaration("jQuery(document).ready( function() { jQuery('#" . $id . "').append('&nbsp;<a id=\"rpxlogin\" class=\"rpxnow\" onclick=\"return false;\" href=\"https://" . $realm . "/openid/v2/signin?token_url=" . $tokenurl . "\">" . JTEXT::_("Sign In") . " <\/a>'); });");
	}
	// -- add by congtq 02/12/2009

	/**
	 * Show login status
	 * 
	 * @param array $auth List of authentication
	 * @param array $id	  List of div id
	 * 
	 * @return string Login status template
	 */
	function showLoginStatus($auth, $id = '')
	{
		if ($id) {
			$jquery = '';
			$arrid = explode(',', $id);
			for ($i = 0; $count = sizeof($arrid), $i < $count; $i++) {
				$jquery .= "jQuery('#" . $arrid[$i] . "').remove();";
			}
			
			$document = Factory::getDocument();
			$document->addScriptDeclaration("jQuery(document).ready( function() { " . $jquery . " });");
		}
		
		if (array_key_exists('photo', $auth)) {
			$this->photo = $auth['photo'];
		}
		if (array_key_exists('url', $auth)) {
			$this->url = $auth['url'];
		}
		
		$this->displayName = $auth['displayName'];
		$this->providerName = $auth['providerName'];
		
		return $this->loadTemplate('login_status');
	}
	
	/**
	 * Show parameters of user
	 * 
	 * @param integer $userid User id
	 * 
	 * @return array List of parameters
	 */
	function showParamsUser($userid)
	{
		include_once JPATH_SITE . DS . 'components' . DS . 'com_jacomment' . DS . 'models' . DS . 'users.php';
		$modelusers = new JACommentModelUsers();
		$paramsUsers = $modelusers->getParam($userid);
		return $paramsUsers;
	}
	
	/**
	 * Callback function to get id of a parent comment item
	 * 
	 * @param object $item A comment item
	 * 
	 * @return int Parent id of an item
	 */
	function getIdsOfParent($item)
	{
		return $item->id;
	}
	
	function showVotedList()
	{
		global $option, $jacconfig;
		$app = Factory::getApplication();
		$helper = new JACommentHelpers();
		$model = $this->getModel();
		
		//check user is specialUser
		$isSpecialUser = $helper->isSpecialUser();
		
		$search = '';
		
		$inputs = Factory::getApplication()->input;
		$contentoption = $inputs->getCmd('contentoption', '');
		if ($contentoption) {
			$search .= " AND c.option='{$contentoption}'";
		}
		
		$contentid = $inputs->getInt('contentid', 0);
		if ($contentid) {
			$search .= ' AND c.contentid=' . (int) $contentid . '';
		}
		
		$orderBy = $jacconfig['layout']->get('default_sort', 'date');
		$orderBy .= " " . $jacconfig['layout']->get('default_sort_type', 'ASC');
		
		//get aproved comment if user isn't special User
		if (! $isSpecialUser) {
			$search .= ' AND type=1';
		}
		
		// get voted comment list
		$search .= ' AND voted > 0';
		
		$totalType = $model->getTotalByType($search);
		if ($totalType) {
			$totalAll = (int) array_sum($totalType);
		} else {
			$totalAll = 0;
		}
		
		//get smiley
		$this->fucSmiley = "jacInsertSmiley";
		$this->id = "";
		
		//--assign item in to html
		$items = $model->getItemsFrontEnd($search, "all", 0, $orderBy);
		$items = $this->assignItems($items);

		$this->items = $items;
		
		$_document = new JDocument();
		$charset = $_document->getCharset();
		$app->setHeader('Content-Type', "text/html; charset=$charset", true);

		$body = $this->loadTemplate("votedblock");
		
		$app->setBody($body);
		echo JApplicationCms::toString();
		exit();
	}
}
?>