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
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if (! defined('JAC_REGISTERED')) {
	JLoader::register('JACView', JPATH_ADMINISTRATOR.'/components/com_jacomment/views/view.php');
}

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;

/**
 * This view is used for JAComments feature of the component
 * 
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */
class jacommentViewcomments extends JACView
{
	/**
	 * Display the view
	 * 
	 * @param string $tpl The name of the template file
	 * 
	 * @return void
	 */
	function display($tpl = null)
	{
		$inputs = Factory::getApplication()->input;
		// Display menu header
		//if (! JRequest::getVar("ajax") && JRequest::getVar('tmpl') != 'component' && JRequest::getVar('viewmenu', 1) != 0) {
		if (! $inputs->get("ajax",'') && $inputs->get('tmpl','') != 'component' && $inputs->get('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT_ADMINISTRATOR . DS . "views" . DS . "jaview" . DS . "tmpl" . DS . "main_header.php";
			if (file_exists($file)) {
				include_once($file);
			}
		}
		
		switch ($this->getLayout()) {
			case 'comment':
				$this->show_detail();
				break;
			case 'comments':
				$this->setLayout('default');
				$this->changeTab();
				break;
			case 'changetype':
				$this->setLayout('default');
				$this->changeTypeOfComments();
				break;
			case 'paging':
				$this->setLayout('default');
				$this->pagingData();
				break;
			case 'checksubofcomment':
				$this->setLayout('default');
				$this->checkSubOfComment();
				break;
			case 'editcomment':
				$this->setLayout('default');
				$this->showFormEdit();
				break;
			case 'replycomment':
				$this->setLayout('default');
				$this->showFormReply();
				break;
			case 'youtube':
				$this->showYouTube();
				break;
			case 'sortcomment':
				$this->setLayout('default');
				$this->sortComment();
				break;
			default:
				$this->displayItems();
				break;
		}
		
		$this->addToolbar();
		parent::display($tpl);
		
		// Display menu footer
		//if (!JRequest::getVar("ajax") && JRequest::getVar('tmpl') != 'component' && JRequest::getVar('viewmenu', 1) != 0) {
		if (!$inputs->get("ajax",'') && $inputs->get('tmpl','') != 'component' && $inputs->get('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT_ADMINISTRATOR . DS . "views" . DS . "jaview" . DS . "tmpl" . DS . "main_footer.php";
			if (file_exists($file)) {
				include_once($file);
			}
		}
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		// Set the titlebar text
		JToolBarHelper::title(JText::_('JA_COMMENT'));
		
		$inputs = Factory::getApplication()->input;
		$task = $inputs->getCmd('task', '');
		switch ($task) {
			case 'add':
			case 'save':
			case 'apply':
			case 'edit':
				JToolBarHelper::apply();
				JToolBarHelper::save();
				JToolBarHelper::cancel();
				break;
			default:
				JToolBarHelper::publish("approve", JText::_("APPROVE"));
				JToolBarHelper::unpublish("unapprove", JText::_("UNAPPROVE"));
				JToolBarHelper::custom('spam', 'trash.png', 'trash.png', JText::_('MARK_SPAM'), false, false);
				JToolBarHelper::custom('delete', 'delete.png', 'delete.png', JText::_('DELETE'), false, false);
				break;
		}
	}
	
	/**
	 * Edit form view
	 * 
	 * @return void
	 */
	function showFormEdit()
	{
		$model = $this->getModel();
		$helper = new JACommentHelpers();
		
		$inputs = Factory::getApplication()->input;
		//$id = JRequest::getInt('id', 0);
		$id = $inputs->getInt('id', 0);
		//$curentTypeID = JRequest::getInt('currenttypeid', 0);
		$curentTypeID = $inputs->getInt('currenttypeid', 0);
		
		$item = $model->getItem($id);
		
		$this->item = $item;
		$this->id = $id;
		$this->curentTypeID = $curentTypeID;
		
		$k = 0;
		$object = array();
		$object[$k] = new stdClass();
		$object[$k]->id = '#jac-edit-comment-' . $curentTypeID . "-" . $id;
		$object[$k]->type = 'html';
		$object[$k]->content = $this->loadTemplate('edit');
		$k++;
		
		echo $helper->parse_JSON_new($object);
		exit();
	}
	
	/**
	 * Reply form view
	 * 
	 * @return void
	 */
	function showFormReply()
	{
		$model = $this->getModel();
		$helper = new JACommentHelpers();
		
		$inputs = Factory::getApplication()->input;
		//$id = JRequest::getInt('id', 0);
		$id = $inputs->getInt('id', 0);
		$curentTypeID = $inputs->getInt('currenttypeid', 0);
		$replyTo = $inputs->get('replyto', '');
		$item = $model->getItem($id);
		
		$this->item = $item;
		$this->id = $id;
		$this->replyTo = $replyTo;
		$this->curentTypeID = $curentTypeID;
		
		$k = 0;
		$object = array();
		$object[$k] = new stdClass();
		$object[$k]->id = '#jac-result-reply-comment-' . $curentTypeID . "-" . $id;
		$object[$k]->type = 'html';
		$object[$k]->content = $this->loadTemplate('reply');
		$k++;
		
		echo $helper->parse_JSON_new($object);
		exit();
	}
	
	/**
	 * Check a comment has sub-comments or not
	 * 
	 * @return void
	 */
	function checkSubOfComment()
	{
		$inputs = Factory::getApplication()->input;
		//$id = JRequest::getVar('id', '');
		$id = $inputs->get('id', '');
		$model = $this->getModel();
		if ($id != '') {
			$result = $model->checkSubOfComment($id);
			if (count($result) > 0) {
				echo "HASSUB";
			} else {
				echo "OK";
			}
		} else {
			$cid = $inputs->get('cid', array(0), '', 'array');
			foreach ($cid as $id) {
				$results = $model->checkSubOfComment($id);
				if (count($results) > 0) {
					foreach ($results as $result) {
						$numberOfComment = count($cid);
						for ($i = 0; $i < $numberOfComment; $i++) {
							if ($result->id == $cid[$i]) {
								break;
							}
						}
						if ($i >= $numberOfComment) {
							echo "HASSUB";
							exit();
						}
					}
				
				}
			}
		}
		exit();
	}
	
	/**
	 * Change type or delete comment
	 * 
	 * @return void
	 */
	function changeTypeOfComments()
	{
		global $jacconfig;
		$isSendDeleteMail = $jacconfig["general"]->get("is_enabled_email", 0);
		$isAttachImage = $jacconfig['comments']->get("is_attach_image", 0);
		$inputs = Factory::getApplication()->input;
		//$id = JRequest::getVar('id', '');
		//$type = JRequest::getVar('type', '');
		$id = $inputs->get('id', '');
		$type = $inputs->get('type', '');
		$model = $this->getModel();
		$currentTypeID = $inputs->getInt('curenttypeid', 99);
		//$limitstart = JRequest::getInt('limitstart', 0);
		//$limit = JRequest::getInt('limit', 10);
		$limitstart = $inputs->getInt('limitstart', 0);
		$limit = $inputs->getInt('limit', 10);
		$helper = new JACommentHelpers();
		$currentUserInfo = Factory::getUser();
		
		$search = '';
		//$searchComponent = JRequest::getString('optionsearch', '');
		$searchComponent = $inputs->getString('optionsearch', '');
		if ($searchComponent) {
			$search .= " AND `option` = '" . $searchComponent . "'";
		}
		
		//$searchSource = JRequest::getString('sourcesearch', '');
		$searchSource = $inputs->getString('sourcesearch', '');
		if ($searchSource) {
			$search .= " AND `source` = '" . $searchSource . "'";
		}
		
		//$reported = JRequest::getInt('reported', 0);
		$reported = $inputs->getInt('reported', 0);
		if ($reported == 1) {
			$search .= " AND report > 0";
		}
		
		//$keyword = JRequest::getString('keyword');
		$keyword = $inputs->getString('keyword','');
		if ($keyword) {
			$search .= " AND (c.email LIKE '%" . $keyword . "%' OR c.id LIKE '%" . $keyword . "%' OR c.contenttitle LIKE '%" . $keyword . "%' OR c.name LIKE '%" . $keyword . "%' OR c.comment LIKE '%" . $keyword . "%')";
			$model->builQueryWhenSearch($search);
		}
		
		//if action is delete comments
		//if (JRequest::getVar('type', '') == "delete") {
		if ($inputs->get('type', '') == "delete") {
			if ($id != '') {
				$db = Factory::getDBO();
				
				//delete comment
				$comment = $model->deleteComment($id);
				
				// Added by NhatNX
				// After deleting comment, update number of children
				$model->updateChildren($comment->parentid);
				// End Added by NhatNX
				

				//send mail for author of comment
				if ($isSendDeleteMail) {
					$userID = $comment->userid;
					if ($userID == 0) {
						$userEmail = $comment->email;
						$userName = $comment->name;
					} else {
						$userInfo = Factory::getUser($userID);
						$userEmail = $userInfo->email;
						$userName = $userInfo->name;
					}
					$content = $helper->replaceBBCodeToHTML($comment->comment);
					
					$helper->sendMailWhenDelete($userName, $userEmail, $content, $comment->referer, $currentUserInfo->name);
				}
				
				if ($isAttachImage) {
					$file_path = JPATH_ROOT . DS . "images" . DS . "stories" . DS . "ja_comment" . DS . $id;
					if (is_dir($file_path)) {
						jimport('joomla.filesystem.folder');
						JFolder::delete($file_path);
					
					}
				}
			} else {
				//$cid = JRequest::getVar('cid', array(0), '', 'array');
				$cid = $inputs->get('cid', array(0), '', 'array');
				ArrayHelper::toInteger($cid, array(0));
				foreach ($cid as $id) {
					$comment = $model->deleteComment($id);
					
					if ($isSendDeleteMail) {
						$userID = $comment->userid;
						if ($userID == 0) {
							$userEmail = $comment->email;
							$userName = $comment->name;
						} else {
							$userInfo = Factory::getUser($userID);
							$userEmail = $userInfo->email;
							$userName = $userInfo->name;
						}
						$content = $helper->replaceBBCodeToHTML($comment->comment);
						$helper->sendMailWhenDelete($userName, $userEmail, $content, $comment->referer, $currentUserInfo->name);
					}
					
					if ($isAttachImage) {
						$file_path = JPATH_ROOT . DS . "images" . DS . "stories" . DS . "ja_comment" . DS . $id;
						if (is_dir($file_path)) {
							jimport('joomla.filesystem.folder');
							JFolder::delete($file_path);
						}
					}
				}
			}
		} else { //if action is changetype of comment
			if ($id != '') {
				$model->changeTypeOfComment($id, $type);
			} else {
				//$cid = JRequest::getVar('cid', array(0), '', 'array');
				$cid = $inputs->get('cid', array(0), 'array');
				ArrayHelper::toInteger($cid, array(0));
				foreach ($cid as $id) {
					$model->changeTypeOfComment($id, $type);
				}
			}
		}
		$totalType = $model->getTotalByType($search);
		
		if ($totalType) {
			$totalAll = (int) array_sum($totalType);
		} else {
			$totalAll = 0;
		}
		
		$totalUnApproved = (int) $totalType[0];
		$totalApproved = (int) $totalType[1];
		$totalSpam = (int) $totalType[2];
		
		$k = 0;
		$object = array();
		$object[$k] = new stdClass();
		$object[$k]->id = '#number-of-tab-99';
		$object[$k]->type = 'html';
		$object[$k]->content = $totalAll;
		$k++;
		
		$object[$k] = new stdClass();
		$object[$k]->id = '#number-of-tab-0';
		$object[$k]->type = 'html';
		$object[$k]->content = $totalUnApproved;
		$k++;
		
		$object[$k] = new stdClass();
		$object[$k]->id = '#number-of-tab-1';
		$object[$k]->type = 'html';
		$object[$k]->content = $totalApproved;
		$k++;
		
		$object[$k] = new stdClass();
		$object[$k]->id = '#number-of-tab-2';
		$object[$k]->type = 'html';
		$object[$k]->content = $totalSpam;
		$k++;
		
		$object[$k] = new stdClass();
		$object[$k]->id = "#jav-mainbox-" . $currentTypeID;
		$object[$k]->type = 'html';
		//get Item have type
		if ($currentTypeID != 99) {
			$search .= " AND type = $currentTypeID";
		}
		$object[$k]->content = $this->loadContentChangeData($search, $currentTypeID, $limit, $limitstart, 'changetype');
		
		$k++;
		
		$object[$k] = new stdClass();
		$object[$k]->id = "#expandOrCollapse" . $currentTypeID;
		$object[$k]->type = 'html';
		//$statusAll = JRequest::getInt('hidAllStatus' . $currentTypeID, 0);
		$statusAll = $inputs->getInt('hidAllStatus' . $currentTypeID, 0);
		if ($statusAll == 1) {
			$object[$k]->content = '[-] ' . JText::_("COLLAPSE_ALL_COMMENTS");
		} else {
			$object[$k]->content = '[+] ' . JText::_("EXPAND_ALL_COMMENTS");
		}
		$k++;
		
		$object[$k] = new stdClass();
		$object[$k]->id = "#hidAllStatus" . $currentTypeID;
		$object[$k]->type = 'value';
		//$statusAll = JRequest::getInt('hidAllStatus' . $currentTypeID, 0);
		$statusAll = $inputs->getInt('hidAllStatus' . $currentTypeID, 0);
		if ($statusAll == 1) {
			$object[$k]->content = 1;
		} else if ($statusAll == 0) {
			$object[$k]->content = 0;
		} else {
			$object[$k]->content = 2;
		}
		$k++;
		
		//jav-page-links-0
		$object[$k] = new stdClass();
		$object[$k]->id = '#jav-pagination-' . $currentTypeID;
		$object[$k]->attr = 'html';
		$pagination = $model->getPagination($limitstart, $limit, 'jav-pagination-' . $currentTypeID);
		$lists['limitstart'] = $limitstart;
		$lists['limit'] = $limit;
		$lists['order'] = "";
		$this->lists = $lists;
		$this->pagination = $pagination;
		$object[$k]->content = $this->loadTemplate('paging');
		$k++;
		
		if ($inputs->getString('displaymessage','') == "show") {
			$message = '<script type="text/javascript">displaymessageadmin();</script>';
			$object[$k] = new stdClass();
			$object[$k]->id = '#jac-msg-succesfull';
			$object[$k]->attr = 'html';
			$object[$k]->content = JText::_("SAVE_DATA_SUCCESSFULLY") . $message;
		}
		
		echo $helper->parse_JSON_new($object);
		exit();
	}
	
	/**
	 * Action is change tab
	 * 
	 * @return void
	 */
	function changeTab()
	{
		global $jacconfig;
		$inputs = Factory::getApplication()->input;
		//$limit = JRequest::getVar('limit', $jacconfig["comments"]->get("number_comment_in_page", 10));
		//$currentTypeID = JRequest::getVar('curenttypeid', '99');
		$limit = $inputs->get('limit', $jacconfig["comments"]->get("number_comment_in_page", 10));
		//$currentTypeID = JRequest::getVar('curenttypeid', '99');
		$currentTypeID = $inputs->get('curenttypeid', '99');
		$search = '';
		//$searchComponent = JRequest::getString('optionsearch', '');
		$searchComponent = $inputs->getString('optionsearch', '');
		if ($searchComponent) {
			$search .= " AND `option` = '" . $searchComponent . "'";
		}
		
		// ++
		//$reported = JRequest::getInt('reported', 0);
		$reported = $inputs->getInt('reported', 0);
		if ($reported == 1) {
			$search .= " AND report > 0";
		}
		// --
		

		//$searchSource = JRequest::getString('sourcesearch', '');
		$searchSource = $inputs->getString('sourcesearch', '');
		if ($searchSource) {
			$search .= " AND `source` = '" . $searchSource . "'";
		}
		
		if ($currentTypeID != 99) {
			$search .= " AND type = $currentTypeID";
		}
		//$keyword = JRequest::getString('keyword');
		$keyword = $inputs->getString('keyword');
		if ($keyword || $reported) {
			$search .= " AND (c.email LIKE '%" . $keyword . "%' OR c.id LIKE '%" . $keyword . "%' OR c.contenttitle LIKE '%" . $keyword . "%' OR c.name LIKE '%" . $keyword . "%' OR c.comment LIKE '%" . $keyword . "%')";
			$model = $this->getModel();
			$model->builQueryWhenSearch($search);
		}
		
		$object = array();
		$k = 0;
		
		$object[$k] = new stdClass();
		$object[$k]->id = '#currentTypeID';
		$object[$k]->attr = 'value';
		$object[$k]->content = $currentTypeID;
		$k++;
		
		$object[$k] = new stdClass();
		$object[$k]->id = '#jav-mainbox-' . $currentTypeID;
		$object[$k]->attr = 'html';
		$object[$k]->content = $this->loadContentChangeData($search, $currentTypeID,$limit);
		$k++;
		
		$helper = new JACommentHelpers();
		
		echo $helper->parse_JSON_new($object);
		exit();
	}
	
	/**
	 * When action is complete - load data again
	 * 
	 * @param string  $where 		 Where clause
	 * @param integer $currentTypeID Current comment type id
	 * @param integer $limit 		 Limit records
	 * @param integer $limitstart 	 Start offset position
	 * @param string  $action		 Change type action
	 * 
	 * @return mixed Comment template
	 */
	function loadContentChangeData($where, $currentTypeID, $limit = 10, $limitstart = 0, $action = '')
	{
		
		$model = $this->getModel();
		$inputs = Factory::getApplication()->input;
		//$sortType = JRequest::getCmd('sorttype', 'DESC');
		$sortType = $inputs->getCmd('sorttype', 'DESC');
		$orderBy = "";
		if ($sortType == "DESC") {
			$orderBy = ' ORDER BY c.id DESC';
			$this->sortType = "DESC";
		} else {
			$orderBy = ' ORDER BY c.id ASC';
			$this->sortType = "ASC";
		}
		
		//buil struct of comment.
		$items = $this->builtTreeComment($where, $currentTypeID, $orderBy, $limit, $limitstart);
		
		$countItems = $model->getCountItems($where);
		
		$lists['search'] = '';
		$lists['limitstart'] = $limitstart;
		$lists['limit'] = $limit;
		$lists['order'] = "";
		$this->lists = &$lists;
		//display item by paging
		$count = ($lists['limit'] < $countItems) ? $lists['limit'] : $countItems;
		$this->count_items = &$count;
		
		$this->items = $items;
		$this->currentTypeID = $currentTypeID;
		
		$this->codeApproved = '1';
		$this->codeUnApproved = '0';
		$this->codeSpam = '2';
		return $this->loadTemplate('comments');
	}
	
	/**
	 * Comment summary
	 * 
	 * @param string $comment Full-text comment
	 * 
	 * @return string Trimmed text Comment
	 */
	function sumaryComment($comment)
	{
		$helper = new JACSmartTrim();
		return $helper->mb_trim($comment, 0, 300);
	}
	
	/**
	 * Sort comment
	 * 
	 * @return void
	 */
	function sortComment()
	{
		$model = $this->getModel();
		$inputs = Factory::getApplication()->input;
		//$currentTypeID = JRequest::getInt('curenttypeid', 99);
		//$limitstart = JRequest::getInt('limitstart', 0);
		//$limit = JRequest::getInt('limit', 10);
		$currentTypeID = $inputs->getInt('curenttypeid', 99);
		$limitstart = $inputs->getInt('limitstart', 0);
		$limit = $inputs->getInt('limit', 10);
		$helper = new JACommentHelpers();
		$currentUserInfo = Factory::getUser();
		
		$search = '';
		//$searchComponent = JRequest::getString('optionsearch', '');
		$searchComponent = $inputs->getString('optionsearch', '');
		if ($searchComponent) {
			$search .= " AND `option` = '" . $searchComponent . "'";
		}
		
		//$searchSource = JRequest::getString('sourcesearch', '');
		$searchSource = $inputs->getString('sourcesearch', '');
		if ($searchSource) {
			$search .= " AND `source` = '" . $searchSource . "'";
		}
		
		//$reported = JRequest::getInt('reported', 0);
		$reported = $inputs->getInt('reported', 0);
		if ($reported == 1) {
			$search .= " AND report > 0";
		}
		
		//$keyword = JRequest::getString('keyword');
		$keyword = $inputs->getString('keyword','');
		if ($keyword || $reported) {
			$search .= " AND (c.email LIKE '%" . $keyword . "%' OR c.id LIKE '%" . $keyword . "%' OR c.contenttitle LIKE '%" . $keyword . "%' OR c.name LIKE '%" . $keyword . "%' OR c.comment LIKE '%" . $keyword . "%')";
			$model = $this->getModel();
			$model->builQueryWhenSearch($search);
		}
		
		$k = 0;
		$object = array();
		$object[$k] = new stdClass();
		$object[$k]->id = "#jav-mainbox-" . $currentTypeID;
		$object[$k]->type = 'html';
		//get Item have type
		if ($currentTypeID != 99) {
			$search = " AND type = $currentTypeID";
		}
		$object[$k]->content = $this->loadContentChangeData($search, $currentTypeID, $limit, $limitstart, 'changetype');
		
		$k++;
		
		$object[$k] = new stdClass();
		$object[$k]->id = "#expandOrCollapse" . $currentTypeID;
		$object[$k]->type = 'html';
		//$statusAll = JRequest::getInt('hidAllStatus' . $currentTypeID, 0);
		$statusAll = $inputs->getInt('hidAllStatus' . $currentTypeID, 0);
		if ($statusAll == 1) {
			$object[$k]->content = '[-] ' . JText::_("COLLAPSE_ALL_COMMENTS");
		} else {
			$object[$k]->content = '[+] ' . JText::_("EXPAND_ALL_COMMENTS");
		}
		$k++;
		
		$object[$k] = new stdClass();
		$object[$k]->id = "#hidAllStatus" . $currentTypeID;
		$object[$k]->type = 'value';
		//$statusAll = JRequest::getInt('hidAllStatus' . $currentTypeID, 0);
		$statusAll = $inputs->getInt('hidAllStatus' . $currentTypeID, 0);
		if ($statusAll == 1) {
			$object[$k]->content = 1;
		} else if ($statusAll == 0) {
			$object[$k]->content = 0;
		} else {
			$object[$k]->content = 2;
		}
		$k++;
		
		//jav-page-links-0
		$object[$k] = new stdClass();
		$object[$k]->id = '#jav-pagination-' . $currentTypeID;
		$object[$k]->attr = 'html';
		$pagination = $model->getPagination($limitstart, $limit, 'jav-pagination-' . $currentTypeID);
		$lists['limitstart'] = $limitstart;
		$lists['limit'] = $limit;
		$lists['order'] = "";
		$this->lists = &$lists;
		$this->pagination = &$pagination;
		$object[$k]->content = $this->loadTemplate('paging');
		$k++;
		
		echo $helper->parse_JSON_new($object);
		exit();
	}
	
	/**
	 * Perform when user start page
	 * 
	 * @return void
	 */
	function displayItems()
	{
		global $jacconfig;
		$app = Factory::getApplication();
		$inputs = Factory::getApplication()->input;
		//$option = JRequest::getCmd('option');
		//$task = JRequest::getCmd('task');
		$option = $inputs->getCmd('option');
		$task = $inputs->getCmd('task');
		$model = $this->getModel();
		
		if ($task == 'edit') {
			//$cid = JRequest::getVar('cid', array(0), '', '');
			$cid = $inputs->get('cid', array(0), '', '');
			$item = $model->getItem($cid[0]);
			$this->item = $item;
		} else {
			//$limit = JRequest::getVar('limit', $jacconfig["comments"]->get("number_comment_in_page", 10));
			//$limitstart = JRequest::getVar('limitstart', 0);
			$limit = $inputs->get('limit', $jacconfig["comments"]->get("number_comment_in_page", 10));
			$limitstart = $inputs->get('limitstart', 0);
			$filtercurrentTypeID = '99';
			$search = '';
			
			//$searchComponent = JRequest::getVar('optionsearch', '');
			$searchComponent = $inputs->get('optionsearch', '');
			if ($searchComponent) {
				$search .= " AND `option` = '" . $searchComponent . "'";
			}
			$listSearchSources = $model->getCommentSource();
			
			//$searchSource = JRequest::getVar('sourcesearch', '');
			$searchSource = $inputs->get('sourcesearch', '');
			if ($searchSource != "") {
				$search .= " AND `source` = '" . $searchSource . "'";
			}
			$listSearchOptions = $model->getCommentOption();
			
			//$reported = JRequest::getInt('reported', 0);
			$reported = $inputs->getInt('reported', 0);
			if ($reported == 1) {
				$search .= " AND report > 0";
			}
			
			//$keyword = JRequest::getVar('keyword');
			$keyword = $inputs->get('keyword');
			if ($keyword || $reported) {
				//$filtercurrentTypeID = JRequest::getInt('filetercurrentTypeID','99');
				$filtercurrentTypeID = $inputs->getInt('filetercurrentTypeID','99');
				$search .= " AND (c.email LIKE '%" . $keyword . "%' OR c.id LIKE '%" . $keyword . "%' OR c.contenttitle LIKE '%" . $keyword . "%' OR c.name LIKE '%" . $keyword . "%' OR c.comment LIKE '%" . $keyword . "%')";
				$model->builQueryWhenSearch($search);
			}
			
			$totalType = $model->getTotalByType($search);
			if ($totalType) {
				$totalAll = (int) array_sum($totalType);
			} else {
				$totalAll = 0;
			}
			
			$totalUnApproved = (int) $totalType[0];
			$totalApproved = (int) $totalType[1];
			$totalSpam = (int) $totalType[2];
			if($filtercurrentTypeID != 99)            
        	$search .= " AND type = $filtercurrentTypeID";
			$items = $this->builtTreeComment($search, $filtercurrentTypeID, '', $limit, $limitstart);
			
			$session = Factory::getSession();
			$jaActiveComments = $session->get("jaActiveComments");
			if ($jaActiveComments) {
				$session->clear("jaActiveComments");
			}
			
			$lists['search'] = '';
			$lists['limitstart'] = $limitstart;
			$lists['limit'] = $limit;
			$lists['order'] = "";
			$this->lists = &$lists;
			
			$this->keyword = $keyword;
			$this->reported = $reported;
			$this->searchComponent = $searchComponent;
			$this->searchSource = $searchSource;
			$this->totalAll = $totalAll;
			$this->totalApproved = $totalApproved;
			$this->totalSpam = $totalSpam;
			$this->totalUnApproved = $totalUnApproved;
			$this->items = $items;
			$this->filtercurrentTypeID = $filtercurrentTypeID;
			$this->listSearchOptions = $listSearchOptions;
			$this->listSearchSources = $listSearchSources;
			
			$this->codeApproved = '1';
			$this->codeUnApproved = '0';
			$this->codeSpam = '2';
			
			//display item by paging
			$count = ($lists['limit'] < count($items)) ? $lists['limit'] : count($items);
			$this->count_items = &$count;
			$this->currentTypeID = $filtercurrentTypeID;
		}
	}
	
	/**
	 * Get text type of comment
	 * 
	 * @param integer $commentType Comment type
	 * 
	 * @return string Text of type of comment
	 */
	function getTextTypeOfComment($commentType)
	{
		if ($commentType == 1) {
			return JText::_("APPROVED");
		} else if ($commentType == 0) {
			return JText::_("UNAPPROVED");
		} else {
			return JText::_("SPAM");
		}
	}
	
	/**
	 * Paging page
	 * 
	 * @param integer $type_id Comment type id
	 * 
	 * @return mixed Paging template
	 */
	function getPaging($type_id)
	{
		$model = $this->getModel();
		
		$inputs = Factory::getApplication()->input;
		//$limitstart = JRequest::getString('limitstart', '');
		//$limit = JRequest::getString('limit', '');
		$limitstart = $inputs->getString('limitstart', '');
		$limit = $inputs->getString('limit', '');
		
		//$keyword = JRequest::getString('keyword');
		$keyword = $inputs->get('keyword');
		
		if (! $limitstart) {
			//JRequest::setVar('limitstart', '0');
			$inputs->set('limitstart', '0');
		}
		if (! $limit) {
			//JRequest::setVar('limit', '0');
			$inputs->set('limit', '0');
		}
		if (! $keyword) {
			//JRequest::setVar('keyword', '');
			$inputs->set('keyword', '');
		}
		
		$link = '';
		if ($keyword) {
			$link = "index.php?keyword=" . $keyword;
		}
		
		if ($limit == '') {
			$lists = $model->_getVars();
			$pagination = $model->getPagination($lists['limitstart'], $lists['limit'], 'jav-pagination-' . $type_id, $link);
			$this->lists = &$lists;
			$this->pagination = &$pagination;
		} else {
			$lists['limitstart'] = $limitstart;
			$lists['limit'] = $limit;
			$lists['order'] = '';
			
			$pagination = $model->getPagination($limitstart, $limit, 'jav-pagination-' . $type_id, $link);
			
			$this->lists = &$lists;
			$this->pagination = &$pagination;
		}
		
		return $this->loadTemplate('paging');
	}
	
	/**
	 * Generate limit list
	 * 
	 * @param integer $limitstart Start offset position
	 * @param integer $limit 	  Limit records
	 * @param string  $order 	  Ordering string
	 * 
	 * @return string HTML select box code for limit list
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
		//$keyword = JRequest::getString('keyword');
		$keyword = $inputs->getString('keyword','');
		$listID = "list";
		//$currentTypeID = JRequest::getString('curenttypeid', '99');
		$currentTypeID = $inputs->getString('curenttypeid', '99');
		$listID .= $currentTypeID;
		$list_html = JHTML::_('select.genericlist', $list_html, $listID, ' onchange="jac_doPaging(' . $limitstart . ', this.value, \'' . $order . '\', \'' . $keyword . '\')"', 'value', 'text', $limit);
		
		return $list_html;
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
		//$currentTypeID = JRequest::getInt('curenttypeid', 99);
		//$limitstart = JRequest::getInt('limitstart', 0);
		//$limit = JRequest::getInt('limit', 10);
		$currentTypeID = $inputs->getInt('curenttypeid', 99);
		$limitstart = $inputs->getInt('limitstart', 0);
		$limit = $inputs->getInt('limit', 10);
		
		$search = '';
		//$searchComponent = JRequest::getString('optionsearch', '');
		$searchComponent = $inputs->getString('optionsearch', '');
		if ($searchComponent) {
			$search .= " AND `option` = '" . $searchComponent . "'";
		}
		
		//$searchSource = JRequest::getString('sourcesearch', '');
		$searchSource = $inputs->getString('sourcesearch', '');
		if ($searchSource) {
			$search .= " AND `source` = '" . $searchSource . "'";
		}
		
		//$reported = JRequest::getInt('reported', 0);
		$reported = $inputs->getInt('reported', 0);
		if ($reported == 1) {
			$search .= " AND report > 0";
		}
		
		//$keyword = JRequest::getString('keyword');
		$keyword = $inputs->getString('keyword','');
		if ($currentTypeID != 99) {
			$search .= " AND type = $currentTypeID";
		}
		if ($keyword || $reported) {
			$search .= " AND (c.email LIKE '%" . $keyword . "%' OR c.id LIKE '%" . $keyword . "%' OR c.contenttitle LIKE '%" . $keyword . "%' OR c.name LIKE '%" . $keyword . "%' OR c.comment LIKE '%" . $keyword . "%')";
			$model->builQueryWhenSearch($search);
		}
		
		$object = array();
		$k = 0;
		
		$object[$k] = new stdClass();
		$object[$k]->id = '#jav-mainbox-' . $currentTypeID;
		$object[$k]->attr = 'html';
		$object[$k]->content = $this->loadContentChangeData($search, $currentTypeID, $limit, $limitstart, 'paging');
		$k++;
		
		$object[$k] = new stdClass();
		$object[$k]->id = '#limitstart' . $currentTypeID;
		$object[$k]->attr = 'value';
		$object[$k]->content = $limitstart;
		
		$helper = new JACommentHelpers();
		
		echo $helper->parse_JSON_new($object);
		exit();
	}
	
	/**
	 * Built tree
	 * 
	 * @param string  $search 		 Criteria to get comment
	 * @param integer $currentTypeID Current type id 
	 * @param string  $orderBy 		 Order by string
	 * @param integer $limit 		 Limit records
	 * @param integer $limitstart 	 Start offset position
	 * 
	 * @return array List of items in tree form
	 */
	function builtTreeComment($search, $currentTypeID = 99, $orderBy = '', $limit = 10, $limitstart = 0)
	{
		// get data items
		$model = $this->getModel();
		
		$searchAll = '';
		$inputs = Factory::getApplication()->input;
		
		//get all items of tab
		//$searchComponent = JRequest::getString("optionsearch", '');
		$searchComponent = $inputs->getString("optionsearch", '');
		if ($searchComponent) {
			$searchAll .= " AND `option` = '" . $searchComponent . "'";
		}
		
		//$searchSource = JRequest::getString('sourcesearch', '');
		$searchSource = $inputs->getString('sourcesearch', '');
		if ($searchSource) {
			$searchAll .= " AND `source` = '" . $searchSource . "'";
		}
		
		$items = array();
		if ($currentTypeID == 99) {
			$items = $model->getItems($searchAll, $orderBy, 1);
		}
			
		//get array item search
		if ($currentTypeID != 99) {
			$search_rows = $model->getItems($search, $orderBy, 1);
		} else {
			$search_rows = $model->getItems($search, $orderBy);
		}
		
		$children = array();
		// first pass - collect children
		$list = array();
		$listSearch = array();
		if ($items) {
			foreach ($items as $v) {	
				if (isset($children[$v->parentid])) {
					$children[$v->parentid][] = $v;
				} else {
					$children[$v->parentid] = array($v);
				}
			}
			
			$list = $this->treerecurse(0, '', array(), $children);
			
			if ($list) {
				foreach ($list as $i => $item) {
					$treename = $item->treename;
					$treename = JFilterOutput::ampReplace($treename);
					$treename = str_replace('"', '&quot;', $treename);
					
					$list[$i]->treename = $treename;
				}
			}
		}
		
		if ($currentTypeID == 99) {
			$list1 = array();
			if ($search_rows) {
				//group search rows
				$childrenSearch = array();
				foreach ($search_rows as $v) {
					if (isset($childrenSearch[$v->parentid])) {
						$childrenSearch[$v->parentid][] = $v;
					} else {
						$childrenSearch[$v->parentid] = array($v);
					}
				}
				
				$listSearch = $this->treerecurse(0, '', array(), $childrenSearch);

				$tmp_list = array();
				foreach ($list as $item) {
					$tmp_list[$item->id] = $item;
				}
				
				foreach ($listSearch as $srow) {
					if (isset($tmp_list[$srow->id])) {
						$list1[] = $tmp_list[$srow->id];
					}
				}
			}
			// replace full list with found items
			$list = $list1;
		} else {
			$list = $search_rows;
		}

		if ($limitstart + $limit > count($list)) {
			$maxLimit = count($list);
		} else {
			$maxLimit = $limitstart + $limit;
		}
		
		$tmpIds = array();
		for ($i = $limitstart; $i < $maxLimit; $i++) {
			$tmpIds[] = $list[$i]->id;
		}
		
		if ($currentTypeID != 99) {
			$realList = $model->getFullItems($search, $orderBy, 1, $tmpIds);
		} else {
			$realList = $model->getFullItems($search, $orderBy, '', $tmpIds);
		}
		
		if ($realList) {
			foreach ($realList as $realItem) {
				if ($realItem->parentid) {
					$parentValueAlready = false;
					for ($i = 0; $i < $maxLimit; $i++) {
						if ($list[$i]->id == $realItem->parentid) {
							if (isset($list[$i]->contentid)) {
								$parentValueAlready = true;
								break;
							} else {
								break;
							}
						}
					}
					
					if (! $parentValueAlready) {
						if ($currentTypeID != 99) {
							$parentItem = $model->getFullItems($search, $orderBy, 1, $realItem->parentid);
						} else {
							$parentItem = $model->getFullItems($search, $orderBy, '', $realItem->parentid);
						}
						
						if (empty($parentItem)) {
							$parentItem = new stdClass();
							$parentItem->id = 0;
						} else {
							$parentItem = $parentItem[0];
						}
						if ($i < $maxLimit) {
							if ($list[$i]->id == $parentItem->id) {
								$list[$i]->contentid = $parentItem->contentid;
								$list[$i]->ip = $parentItem->ip;
								$list[$i]->name = $parentItem->name;
								$list[$i]->contenttitle = $parentItem->contenttitle;
								$list[$i]->comment = $parentItem->comment;
								$list[$i]->date = $parentItem->date;
								$list[$i]->published = $parentItem->published;
								$list[$i]->locked = $parentItem->locked;
								$list[$i]->ordering = $parentItem->ordering;
								$list[$i]->email = $parentItem->email;
								$list[$i]->website = $parentItem->website;
								$list[$i]->star = $parentItem->star;
								$list[$i]->userid = $parentItem->userid;
								$list[$i]->usertype = $parentItem->usertype;
								$list[$i]->option = $parentItem->option;
								$list[$i]->voted = $parentItem->voted;
								$list[$i]->report = $parentItem->report;
								$list[$i]->subscription_type = $parentItem->subscription_type;
								$list[$i]->referer = $parentItem->referer;
								$list[$i]->source = $parentItem->source;
								$list[$i]->type = $parentItem->type;
								$list[$i]->date_active = $parentItem->date_active;
								// $list[$i]->children = $parentItem->children;
								$list[$i]->active_children = $parentItem->active_children;
								$list[$i]->p0 = $parentItem->p0;
							}
						}
					}
				}
				
				for ($i = $limitstart; $i < $maxLimit; $i++) {
					if ($list[$i]->id == $realItem->id) {
						$list[$i]->contentid = $realItem->contentid;
						$list[$i]->ip = $realItem->ip;
						$list[$i]->name = $realItem->name;
						$list[$i]->contenttitle = $realItem->contenttitle;
						$list[$i]->comment = $realItem->comment;
						$list[$i]->date = $realItem->date;
						$list[$i]->published = $realItem->published;
						$list[$i]->locked = $realItem->locked;
						$list[$i]->ordering = $realItem->ordering;
						$list[$i]->email = $realItem->email;
						$list[$i]->website = $realItem->website;
						$list[$i]->star = $realItem->star;
						$list[$i]->userid = $realItem->userid;
						$list[$i]->usertype = $realItem->usertype;
						$list[$i]->option = $realItem->option;
						$list[$i]->voted = $realItem->voted;
						$list[$i]->report = $realItem->report;
						$list[$i]->subscription_type = $realItem->subscription_type;
						$list[$i]->referer = $realItem->referer;
						$list[$i]->source = $realItem->source;
						$list[$i]->type = $realItem->type;
						$list[$i]->date_active = $realItem->date_active;
						// $list[$i]->children = $realItem->children;
						$list[$i]->active_children = $realItem->active_children;
						$list[$i]->p0 = $realItem->p0;
						
						break;
					}
				}
			}
		}

		return $list;
	}
	
	/**
	 * Recursive build tree function
	 * 
	 * @param integer $id 		 Current item id
	 * @param string  $indent 	 Indent string
	 * @param array   $list 	 List of items
	 * @param array   &$children List of children items
	 * @param integer $maxlevel  Maximum level of an item
	 * @param integer $level 	 Item level
	 * @param integer $type 	 Item type
	 * 
	 * @return array List of items in tree form
	 */
	function treerecurse($id, $indent, $list, &$children, $maxlevel = 9999, $level = 0, $type = 1)
	{
		if (@$children[$id] && $level <= $maxlevel) {
			foreach ($children[$id] as $v) {
				$id = $v->id;
				$txt = "";
				if ($type) {
					$pre = '|_&nbsp;';
					$spacer = '';
					if ($level > 0) {
						$spacer = '.';
					}
				} else {
					$pre = '- ';
					$spacer = '&nbsp;&nbsp;';
				}
				
				if ($v->parentid != 0) {
					$txt = $pre;
				}
				
				$pt = $v->parentid;
				$list[$id] = $v;
				
				$list[$id]->treename = "$indent$txt";
				$list[$id]->children = (!empty($children[$id])) ? count(@$children[$id]):0;
				$list[$id]->level = $level + 1;
				$list = $this->treeRecurse($id, $indent . $spacer, $list, $children, $maxlevel, $level + 1, $type);
			}
		}
		return $list;
	}
	
	/**
	 * Get view lists
	 * 
	 * @return array List of view state
	 */
	function &_getViewLists()
	{
		$app = Factory::getApplication();
		$inputs = Factory::getApplication()->input;
		//$option = JRequest::getCmd('option');
		$option = $inputs->getCmd('option');
		//$db = Factory::getDBO();
		$db = Factory::getDBO();
		
		$option_1 = $option . '.jacategories';
		$app = Factory::getApplication('administrator');
		
		$lists['limitstart'] = $app->getUserStateFromRequest("$option_1.limitstart", 'limitstart', 0);
		$lists['limit'] = $app->getUserStateFromRequest("$option_1.limit", 'limit', 20);
		$filter_order = $app->getUserStateFromRequest("$option_1.filter_order", 'filter_order', 's.ordering', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest("$option_1.filter_order_Dir", 'filter_order_Dir', 'ASC', 'word');
		$filter_state = $app->getUserStateFromRequest("$option_1.filter_state", 'filter_state', '', 'word');
		$search = $app->getUserStateFromRequest("$option_1.search", 'search', '', 'string');
		$search = JString::strtolower($search);
		
		// state filter
		$lists['state'] = JHTML::_('grid.state', $filter_state);
		
		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;
		
		$lists['option'] = $option;
		
		// search filter
		$lists['search'] = $search;
		
		return $lists;
	}
	
	// ++ add by congtq 26/11/2009
	/**
	 * Show YouTube video
	 * 
	 * @return void
	 */
	function showYouTube()
	{
		$inputs = Factory::getApplication()->input;
		$cid = $inputs->get('cid', array(), '', 'array');
		ArrayHelper::toInteger($cid);
		$id = $cid[0] ? $cid[0] : '';
		$this->id = $id;
	}
	
	/**
	 * Show YouTube link
	 * 
	 * @param string $id Div id
	 * 
	 * @return void
	 */
	function showYouTubeLink($id)
	{
		$document = Factory::getDocument();
		
		$document->addScriptDeclaration("jQuery(document).ready( function() { jQuery('#" . $id . "').append('&nbsp;<a href=\"javascript:open_embed();\" class=\"plugin\"><img title=\"Add a YouTube Video\" alt=\"YouTube\" src=\"http://www.youtube.com/favicon.ico\"> <span>" . JText::_("EMBED_VIDEO") . "<\/span><\/a>'); });");
		
		$document->addScriptDeclaration("function open_embed(){ jaCreatForm('open_youtube',0,340,300,0,0,'" . JText::_("EMBED_A_YOUTUBE_VIDEO") . "',0,'" . JText::_("EMBED_VIDEO") . "'); }");
	}
	
	/**
	 * Show AfterDeadLine link
	 * 
	 * @param string $id Div id
	 * 
	 * @return void
	 */
	function showAfterDeadLineLink($id)
	{
		$document = Factory::getDocument();
		if (! defined('JACOMMENT_PLUGIN_ATD')) {
			JHTML::stylesheet(JURI::root() . 'components/com_jacomment/asset/css/atd.css');
			JHTML::script(JURI::root() . 'components/com_jacomment/libs/js/atd/jquery.atd.js');
			JHTML::script(JURI::root() . 'components/com_jacomment/libs/js/atd/csshttprequest.js');
			JHTML::script(JURI::root() . 'components/com_jacomment/libs/js/atd/atd.js');
			define('JACOMMENT_PLUGIN_ATD', true);
		}
		
		$document->addScriptDeclaration("jQuery(document).ready( function() { jQuery('#" . $id . "').append('&nbsp;<a href=\"javascript:jac_check_atd(\'\')\"><img title=\"Proofread Comment w/ After the Deadline\" alt=\"AtD\" src=\"http://www.polishmywriting.com/atd_jquery/images/atdbuttontr.gif\"> <span id=\"checkLink\">" . JText::_("CHECK_SPELLING") . "<\/span><\/a>'); });");	
	}
	// -- add by congtq 26/11/2009

	/**
	 * Show smiley box
	 * 
	 * @param string $id  Div id
	 * @param array  $cid Item id
	 * 
	 * @return mixed Smiley template
	 */
	function showSmileys($id, $cid)
	{
		$cid = $cid ? $cid : '';
		
		$func = 'jacInsertSmiley';

		$this->func = $func;
		$this->cid = $cid;
		
		return $this->loadTemplate('smiley');
	}
	
	/**
	 * Get user parameters
	 * 
	 * @param integer $userid User id
	 * 
	 * @return object User parameters
	 */
	function showParamsUser($userid)
	{
		include_once JPATH_SITE . DS . 'components' . DS . 'com_jacomment' . DS . 'models' . DS . 'users.php';
		$modelusers = new JACommentModelUsers();
		$paramsUsers = $modelusers->getParam($userid);
		
		return $paramsUsers;
	}
	
	/**
	 * Show BBCode
	 * 
	 * @return mixed BBCode template
	 */
	function showBBCode()
	{
		$this->textAreaID = "newcomment";
		return $this->loadTemplate('bbcode');
	}
	
	/**
	 * Check user id in comment
	 * 
	 * @param integer $userID 	 User id
	 * @param integer $commentID Comment id
	 * 
	 * @return boolean True if have no error and vice versa
	 */
	function checkUserId($userID, $commentID)
	{
		$model = $this->getModel();
		return $model->checkUserId($userID, $commentID);
	}
}
?>