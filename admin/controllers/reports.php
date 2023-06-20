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

use Joomla\CMS\Factory;

/**
 * This controller is used for JAReports feature of the component
 * 
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */
class JACommentControllerReports extends JACommentController
{
	/**
	 * Constructor
	 * 
	 * @param array $default Array of configuration settings
	 * 
	 * @return void
	 */
	function __construct($default = array())
	{
		parent::__construct($default);
		// Register Extra tasks
		$inputs = Factory::getApplication()->input;
		$inputs->set('view', 'reports');
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('dismiss', 'dismiss');
		$this->registerTask('dismiss_all', 'dismiss_all');
	}
	
	/**
	 * Display current JAReports of the component to administrator
	 * 
	 * @return void
	 */
	function display($cachable = false, $urlparams = false)
	{
		$user = JFactory::getUser();
		if ($user->id == 0) {
			JError::raiseWarning(1001, JText::_("YOU_MUST_BE_SIGNED_IN"));
			$this->setRedirect(JRoute::_("index.php?option=com_user&view=login"));
			return;
		}
		parent::display($cachable, $urlparams);
		
		return $this;
	}
	
	/**
	 * Display edit form
	 * 
	 * @return void
	 */
	function edit()
	{
		
		$inputs = Factory::getApplication()->input;
		$inputs->set('edit', true);
		$inputs->set('layout', 'form');
		parent::display();
	}
	
	/**
	 * Cancel current operation
	 * 
	 * @return boolean True if have no error and vice versa
	 */
	function cancel()
	{
		$this->setRedirect('index.php?option=com_jacomment&view=reports');
		return true;
	}
	
	/**
	 * Save record
	 * 
	 * @param array &$errors Error messages
	 * 
	 * @return boolean True if have no error and vice versa
	 */
	function save(&$errors = '')
	{
		
		$task = $this->getTask();
		$model = $this->getModel('reports');
		$item = $model->getItem();
		$inputs = Factory::getApplication()->input;
		$post = $inputs->get('request','');
		
		if (! $item->bind($post)) {
			$errors[] = JText::_("DO_NOT_BIND_DATA");
		
		}
		if ($errors) {
			return false;
		}
		$item->title = trim($item->title);
		$errors = $item->check();
		
		if (count($errors) > 0) {
			return false;
		}
		$where = " AND c.title = '$item->title' AND c.id!=$item->id";
		$count = $model->getTotal($where);
		if ($count > 0) {
			$errors[] = JText::_("ERROR_DUPLICATE_FOR_COMMENT_TITLE");
			return false;
		}
		
		if (! $item->store()) {
			$errors[] = JText::_("ERROR_DATA_NOT_SAVED");
			return false;
		} else {
			$item->reorder(1);
		}
		if ($task != 'saveIFrame') {
			$link = 'index.php?option=com_jacomment&view=reports';
			if ($this->getTask() == 'apply') {
				$link .= "&task=edit&cid[]=" . $item->id;
			}
			$msg = JText::_('SAVE_DATA_SUCCESSFULLY');
			$this->setRedirect($link, $msg);
		
		}
		return $item->id;
	}
	
	/**
	 * Save content from iframe
	 * 
	 * @return void
	 */
	function saveIFrame()
	{
		$inputs = Factory::getApplication()->input;
		$post = $inputs->get('request','');
		
		$errors = array();
		$id = $this->save($errors);
		$helper = new JACommentHelpers();
		$objects = array();
		
		if (count($errors) == 0) {
			
			$model = $this->getModel('reports');
			
			$item = $model->getItem($id);
			
			if ($post['id'] == '0') {
				$objects[] = $helper->parseProperty("reload", "#reload" . $item->id, 1);
			} else {
				$objects[] = $helper->parseProperty("html", "#system-message", $helper->message(0, JText::_("SAVE_DATA_SUCCESSFULLY")));
			}
			$objects[] = $helper->parseProperty("html", "#title" . $item->id, $item->title);
			
			$objects[] = $helper->parsePropertyPublish("html", "#publish" . $item->id, $item->published, $number);
			
			$objects[] = $helper->parseProperty("value", "#order" . $item->id, $item->ordering);
		
		} else {
			$objects[] = $helper->parseProperty("html", "#system-message", $helper->message(1, $errors));
		
		}
		
		echo $helper->parse_JSON_new($objects);
		exit();
	}
	
	/**
	 * Publish a report
	 * 
	 * @return void
	 */
	function publish()
	{
		$model = $this->getModel('reports');
		if (! $model->published(1)) {
			JError::raiseWarning(1001, JText::_('ERROR_DATA_NOT_SAVED'));
		} else {
			$msg = JText::_('SAVE_DATA_SUCCESSFULLY');
		}
		$this->setRedirect('index.php?option=com_jacomment&view=reports', $msg);
	}
	
	/**
	 * Unpublish a report
	 * 
	 * @return void
	 */
	function unpublish()
	{
		$model = $this->getModel('reports');
		if (! $model->published(0)) {
			JError::raiseWarning(1001, JText::_('ERROR_DATA_NOT_SAVED'));
		} else {
			$msg = JText::_('SAVE_DATA_SUCCESSFULLY');
		}
		$this->setRedirect('index.php?option=com_jacomment&view=reports', $msg);
	}
	
	/**
	 * Dismiss a report
	 * 
	 * @return void
	 */
	function dismiss()
	{
		$inputs = Factory::getApplication()->input;
		$id = $inputs->get('id', 0);
		
		$model = $this->getModel('reports');
		if (! $model->dismiss($id)) {
			JError::raiseWarning(1001, JText::_('ERROR_DATA_NOT_SAVED'));
		} else {
			$msg = JText::_('DISMISS_DATA_SUCCESSFULLY');
		}
		exit();
	}
	
	/**
	 * Dismiss all reports
	 * 
	 * @return void
	 */
	function dismiss_all()
	{
		$model = $this->getModel('reports');
		if (! $model->dismiss_all()) {
			JError::raiseWarning(1001, JText::_('ERROR_DATA_NOT_SAVED'));
		} else {
			$msg = JText::_('DISMISS_DATA_SUCCESSFULLY');
		}
		$this->setRedirect('index.php?option=com_jacomment&view=reports', $msg);
	}
	
	/**
	 * Delete a report
	 * 
	 * @return void
	 */
	function remove()
	{
		$model = $this->getModel('reports');
		$errors = $model->remove();
		if ($errors) {
			foreach ($errors as $error) {
				JError::raiseWarning(1001, $error);
			}
		} else {
			$msg = JText::_("DELETE_DATA_SUCCESSFULLY");
		}
		$this->setRedirect('index.php?option=com_jacomment&view=reports', $msg);
	}
}
?>