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

use Joomla\CMS\Factory;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
/**
 * This controller is used for JACustomcss feature of the component
 *
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */
class JACommentControllerCustomcss extends JACommentController
{
	/**
	 * Constructor
	 * 
	 * @param array $location Array of configuration settings
	 * 
	 * @return void
	 */
	function __construct($location = array())
	{
		parent::__construct($location);
		// Register Extra tasks
		$this->registerTask('apply', 'save');
	}
	
	/**
	 * Display current customcss of the component to administrator
	 * 
	 * @return void
	 * 
	 */
	function display($cachable = false, $urlparams = false)
	{
		$inputs = Factory::getApplication()->input;
		switch ($this->getTask()) {
			case 'edit':
			default:
				$inputs->set('hidemainmenu', 1);
				$inputs->set('edit', true);
				$inputs->set('layout', 'form');
				break;
		}
		
		parent::display($cachable, $urlparams);
		
		return $this;
	}
	
	/**
	 * Cancel current operation
	 * 
	 * @return boolean True if have no error and vice versa
	 * 
	 */
	function cancel()
	{
		$inputs = Factory::getApplication()->input;
		$option = $inputs->getCmd('option');
		$this->setRedirect("index.php?option=$option&view=customcss");
		return true;
	}
	
	/**
	 * Save record
	 * 
	 * @return boolean True if have no error and vice versa
	 */
	function save()
	{
		$inputs = Factory::getApplication()->input;
		$option = $inputs->getCmd('option');
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		$content = $inputs->getString('content', '');
		
		$file = $inputs->getString('file', '');
		$path = '';
		$template = JACommentHelpers::checkFileTemplate($file);
		if ($template) {
			$path = $template;
		} else {
			$path = JPATH_COMPONENT_SITE . DS . 'asset' . DS . 'css' . DS . $file;
		}
		$msg = '';
		if (JFile::exists($path)) {
			$res = JFile::write($path, $content);
			if ($res) {
				$msg = JText::_('SAVE_DATA_SUCCESSFULLY') . ': ' . $file;
			} else {
				JError::raiseWarning(1001, JText::_("ERROR_DATA_NOT_SAVED") . " " . $file);
			}
		} else {
			JError::raiseWarning(1001, JText::_("FILE_NOT_FOUND_TO_EDIT"));
		}
		
		switch ($this->_task) {
			case 'apply':
				$this->setRedirect("index.php?option=$option&view=customcss&task=edit&file=$file", $msg);
				break;
			
			case 'save':
			default:
				$this->setRedirect("index.php?option=$option&view=customcss", $msg);
				break;
		}
		return true;
	}
}
?>