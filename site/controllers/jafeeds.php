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
 * JACommentControllerJaFeeds Controller
 *
 * @package		Joomla.Site
 * @subpackage	JAComment
 */
class JACommentControllerJaFeeds extends JACommentController
{
	/**
	 * Constructor
	 * 
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Display function
	 * 
	 * @return void
	 */
	function display($cachable = false, $urlparams = false)
	{
		parent::display($cachable, $urlparams);
		
		return $this;
	}
	
	/**
	 * Save feed
	 * 
	 * @return void
	 */
	function save()
	{
		$model = $this->getModel('jafeeds');
		
		$inputs = Factory::getApplication()->input;
		$cid = $inputs->get('cid', array(0), 'array');
		JArrayHelper::toInteger($cid, array(0));
		
		if ($id = $model->store()) {
			$msg = JText::_('SUCCESSFULLY_CREATED_FEED');
			$this->setRedirect(JRoute::_("index.php?option=" . JBCOMNAME . "&view=jafeeds&layout=feed_link&cid[]=$id&Itemid=1000"), $msg);
		} else {
			Factory::getApplication()->enqueueMessage(
				JText::_('FAIL_TO_SAVE_FEED_TEXT'),
				'warning'
			);
			//JError::raiseWarning(1, JText::_('FAIL_TO_SAVE_FEED_TEXT'));
			$inputs->set('Itemid', '1000');
			$inputs->set('layout', 'form');
			$inputs->set('postback', true);
			
			parent::display();
		}
	}
	
	/**
	 * Cancel a adding feed form
	 * 
	 * @return unknown_type
	 */
	function cancel()
	{
		$inputs = Factory::getApplication()->input;
		$Itemid = $inputs->getCmd('Itemid');
		$this->setRedirect(JRoute::_("index.php?option=" . JBCOMNAME . "&view=jafeeds&layout=guide&Itemid=" . $Itemid));
	}
}