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
use Joomla\Utilities\ArrayHelper;

/**
 * This controller is used for JAEmailtemplates feature of the component
 *
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 *
 */
class JACommentControllerEmailtemplates extends JACommentController
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
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
	}
	
	/**
	 * Display current jaemail of the component to administrator
	 * 
	 * @return void
	 * 
	 */
	function display($cachable = false, $urlparams = false)
	{
		$inputs = Factory::getApplication()->input;
		switch ($this->getTask()) {
			case 'add':
				$inputs->set('hidemainmenu', 1);
				$inputs->set('edit', false);
				$inputs->set('layout', 'form');
				break;
			case 'edit':
				$inputs->set('hidemainmenu', 1);
				$inputs->set('edit', true);
				$inputs->set('layout', 'form');
				break;
			case 'show_duplicate':
				$inputs->set('layout', 'duplicate');
				break;
			case 'show_import':
				$inputs->set('layout', 'import');
				break;
			default:
				break;
		}
		
		parent::display($cachable, $urlparams);
		
		return $this;
	}
	
	/**
	 * Cancel current operation
	 * 
	 * @return void
	 */
	function cancel()
	{
		$inputs = Factory::getApplication()->input;
		$option = $inputs->getCmd('option');
		$this->setRedirect("index.php?option=$option&view=emailtemplates");
	}
	
	/**
	 * Remove a jaemail row
	 * 
	 * @return boolean True if have no error and vice versa
	 */
	function remove()
	{
		$inputs = Factory::getApplication()->input;
		$option = $inputs->getCmd('option');
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		$model = $this->getModel('emailtemplates');
		$item = $model->getItem();
		if ($item->system == 1) {
			JError::raiseWarning(1001, JText::_("THIS_EMAIL_DOES_NOT_ALLOW_DELETION"));
			$this->setRedirect("index.php?option=$option&view=emailtemplates");
			return false;
		}
		if (($n = $model->remove()) < 0) {
			JError::raiseWarning(500, $item->getError());
		}
		
		$msg = JText::_("DELETE_EMAIL_TEMPLATE_SUCCESSFULLY");
		$this->setRedirect("index.php?option=$option&view=emailtemplates", $msg);
	}
	
	/**
	 * Save categories record
	 * 
	 * @return void
	 */
	function save()
	{
		$inputs = Factory::getApplication()->input;
		$option = $inputs->getCmd('option');
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		$cid = $inputs->get('cid', array(0), 'array');
		ArrayHelper::toInteger($cid, array(0));
		
		$cache = Factory::getCache($option);
		$cache->clean();
		
		$model = $this->getModel('emailtemplates');
		$post = $inputs->get('post','');
		
		// allow name only to contain html
		$paramsField = $inputs->get('params', null, 'array');
		if ($paramsField) {
			$params = new JRegistry;
			$params->loadString('{}');
			$data = array();
			foreach ($paramsField as $k => $v) {
				$params->set($k, $v);
			}
			$post['params'] = $params->toString();
		}
		
		$post['content'] = $inputs->getString('content', JREQUEST_ALLOWRAW);
		$post['subject'] = $inputs->getString('subject', JREQUEST_ALLOWRAW);
		$model->setState('request', $post);
		
		if ($id = $model->store()) {
			if ((isset($cid[0])) && ($cid[0] != 0)) {
				$msg = JText::_('UPDATED_EMAIL_TEMPLATE_SUCCESSFULLY');
			} else {
				$msg = JText::_('CREATED_EMAIL_TEMPLATE_SUCCESSFULLY');
			}
		} else {
			$msg = JText::_('ERROR_OCCURRED_SAVE_THE_EMAIL_TEMPLATE_IS_NOT_SUCCESSFUL');
		}
		
		switch ($this->_task) {
			case 'apply':
				$this->setRedirect("index.php?option=$option&view=emailtemplates&task=edit&cid[]=$id", $msg);
				break;
			
			case 'save':
			default:
				$this->setRedirect("index.php?option=$option&view=emailtemplates", $msg);
				break;
		}
	}
	
	/**
	 * change Is_Published status
	 */
	/**
	 * Unpublish item list
	 *  
	 * @return void
	 **/
	function unpublish()
	{
		$this->publish(0);
	}
	
	/**
	 * Publish item list
	 * 
	 * @param boolean $publish Publish status, 1: publish, 0: unpublish
	 * 
	 * @return unknown_type
	 */
	function publish($publish = 1)
	{
		$inputs = Factory::getApplication()->input;
		$option = $inputs->getCmd('option');
		$model = $this->getModel('emailtemplates');
		
		if (! $model->dopublish($publish)) {
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
			exit();
		}
		$cache = Factory::getCache($option);
		$cache->clean();
		$this->setRedirect("index.php?option=$option&view=emailtemplates", JText::_("UPDATED_EMAIL_TEMPLATE_SUCCESSFULLY"));
	}
	
	/**
	 * Duplicate item
	 * 
	 * @return void
	 */
	function duplicate()
	{
		$app = Factory::getApplication();
		$inputs = Factory::getApplication()->input;
		$option = $inputs->getCmd('option');
		$model = $this->getModel('emailtemplates');
		if (! $model->do_duplicate()) {
			JError::raiseWarning(1, JText::_('THE_PROCESS_OF_COPYING_ERRORS_OCCUR'));
			return $this->setRedirect("index.php?option=$option&view=emailtemplates");
		}
		$app = Factory::getApplication('administrator');
		$filter_lang = $app->getUserStateFromRequest($option . '.emailtemplates.filter_lang', 'filter_lang', 'en-GB', 'string');
		return $this->setRedirect("index.php?option=$option&view=emailtemplates&filter_lang=$filter_lang", JText::_('COPY_EMAIL_TEMPLATE_SUCCESSFULLY'));
	
	}
	
	/**
	 * Import an e-mail template
	 * 
	 * @return void
	 */
	function import()
	{
		$app = Factory::getApplication();
		$inputs = Factory::getApplication()->input;
		$option = $inputs->getCmd('option');
		
		$cache = Factory::getCache($option);
		$cache->clean();
		
		jimport('joomla.filesystem.file');
		$model = $this->getModel('emailtemplates');
		$app = Factory::getApplication('administrator');
		if (isset($_FILES['userfile']) && $_FILES['userfile']['name'] != '') {
			$desk = JPATH_COMPONENT_ADMINISTRATOR . DS . 'temp' . DS . substr($_FILES['userfile']['name'], 0, strlen($_FILES['userfile']['name']) - 4) . time() . rand() . substr($_FILES['userfile']['name'], - 4, 4);
			
			if (JFile::upload($_FILES['userfile']['tmp_name'], $desk)) {
				$filecontent = JFile::read($desk);
				if (! $model->import($filecontent)) {
					return $this->setRedirect("index.php?option=$option&view=emailtemplates");
				}
				
				$filter_lang = $app->getUserStateFromRequest($option . '.emailtemplates.filter_lang', 'filter_lang', 'en-GB', 'string');
				return $this->setRedirect("index.php?option=$option&view=emailtemplates&filter_lang=$filter_lang", JText::_('IMPORT_EMAIL_TEMPLATE_SUCCESSFULLY'));
			}
			unset($_FILES['userfile']);
			JError::raiseWarning(1, JText::_('ERROR_OCCURRED_UPLOAD_FAILED'));
			return $this->setRedirect("index.php?option=$option&view=emailtemplates&task=show_import");
		}
		
		JError::raiseWarning(1, JText::_('PLEASE_CHOOSE_FILE_TO_UPLOAD'));
		return $this->setRedirect("index.php?option=$option&view=emailtemplates&task=show_import");
	}
	
	/**
	 * Export an e-mail template
	 * 
	 * @return void
	 */
	function export()
	{
		$inputs = Factory::getApplication()->input;
		$option = $inputs->getCmd('option');
		
		$cid = $inputs->get('cid', array(), 'array');
		ArrayHelper::toInteger($cid);
		if (! $cid) {
			JError::raiseWarning(1, JText::_('PLEASE_SELECT_EMAIL_TEMPLATE'));
			return $this->setRedirect("index.php?option=$option&view=emailtemplates");
		}
		
		if ($cid) {
			$cid = implode(',', $cid);
		}
		
		$model = $this->getModel('emailtemplates');
		$items = $model->getItemsbyWhere($cid);
		
		if (! $items) {
			JError::raiseWarning(1, JText::_('PLEASE_SELECT_EMAIL_TEMPLATE'));
			return $this->setRedirect("index.php?option=$option&view=emailtemplates");
		}
		
		$content = '';
		foreach ($items as $item) {
			$content .= JACommentHelpers::temp_export($item);
		}
		
		$filename = 'jacomment_email_templates.ini';
		$ctype = "text/plain";
		
		if ($content) {
			header("Pragma: public"); // required
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private", false); // required for certain browsers
			header("Content-Type: $ctype; name=\"" . basename($filename) . "\";");
			header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\";");
			header("Content-Transfer-Encoding: utf-8");
			header("Content-Length: " . strlen($content));
			echo $content;
			exit();
		} else {
			JError::raiseWarning(1, JText::_('CONTENT_IS_EMPTY'));
			return $this->setRedirect("index.php?option=$option&view=emailtemplates");
		}
	}
}
?>