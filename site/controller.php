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
	JLoader::register('JACController', JPATH_BASE.'/components/com_jacomment/controllers/controller.php');
}

/**
 * JA Comment Component Controller
 *
 * @package		Joomla.Site
 * @subpackage	JAComment
 */
class JACommentController extends JACController
{
	/**
	 * Method to display a view
	 *
	 * @return JACController This object to support chaining
	 */
	function display($cachable = false, $urlparams = false)
	{
		parent::display($cachable, $urlparams);

		return $this;
	}
}
?>