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
 * This controller is used for JAManagelang feature of the component
 * 
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */

class JACommentControllerManagelang extends JACommentController
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
		$this->registerTask('apply', 'save');
	}
	
	/**
	 * Display current JAManagelang of the component to administrator
	 * 
	 * @return void
	 */
	function display($cachable = false, $urlparams = false)
	{
		parent::display($cachable, $urlparams);
		
		return $this;
	}
	
	/**
	 * Cancel save file
	 * 
	 * @return void
	 */
	function cancel()
	{
		$inputs = Factory::getApplication()->input;
		$option = $inputs->getCmd('option');
		$client = $inputs->get('client', 0);
		$this->setRedirect("index.php?option=$option&view=managelang&client=$client");
	}
	
	/**
	 * Save language file
	 * 
	 * @return void
	 */
	function save()
	{
		$inputs = Factory::getApplication()->input;
		$option = $inputs->getCmd('option');
		jimport('joomla.filesystem.file');
		$post = $inputs->get('post', 2);
		$file = $post['path_lang'] . DS . $post['filename'] . DS . $post['filename'] . '.' . $option . '.ini';
		JFile::write($file, $post['datalang']);
		if ($this->getTask() == 'apply') {
			$this->setRedirect('index.php?option=' . $option . '&view=managelang&task=edit&layout=form&client=' . $post['client'] . '&lang=' . $post['filename'], JText::_('UPDATED_LANGUAGE_FILE_SUCCESSFULLY'));
		} else {
			$this->setRedirect('index.php?option=' . $option . '&view=managelang&client=' . $post['client'], JText::_('UPDATED_LANGUAGE_FILE_SUCCESSFULLY'));
		}
	}
}
?>