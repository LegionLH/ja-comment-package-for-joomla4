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
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

/**
 * jacommentControllercomment controller
 *
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */
class jacommentControllercomment extends JACommentController
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
		$inputs->set('view', 'comment');
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
	}
	
	/**
	 * Display function
	 * 
	 * @return void
	 */
	function display($cachable = false, $urlparams = false)
	{
		$user = Factory::getUser();
		$task = $this->getTask();
		$inputs = Factory::getApplication()->input;
		switch ($task) {
			case 'verify':
				$post = $inputs->get('post', JREQUEST_ALLOWHTML);
				if (count($post) > 0 && $post['email'] != '' && $post['payment_id'] != '') {
					$objVerify = new JACommentLicense($post['email'], $post['payment_id']);
					$objVerify->verify_license($post['email'], $post['payment_id']);
				}
				$inputs->set('layout', 'verify');
				break;
			default:
				// other tasks
				break;
		}
		if ($user->id == 0) {
			JError::raiseWarning(1001, JText::_("YOU_MUST_BE_SIGNED_IN"));
			$this->setRedirect(JRoute::_("index.php?option=com_user&view=login"));
			return;
		}
		parent::display($cachable, $urlparams);
		
		return $this;
	}
}
?>