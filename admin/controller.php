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
	JLoader::register('JACController', JPATH_ADMINISTRATOR.'/components/com_jacomment/controllers/controller.php');
}

use Joomla\CMS\Factory;

/**
 * Component Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */
class JACommentController extends JACController
{
	/**
	 * Method to display a view
	 * 
	 * @return void
	 */
	function display($cachable = false, $urlparams = false)
	{
		$inputs = Factory::getApplication()->input;
		$view = $inputs->getCmd('view');
		if (! $inputs->getCmd('tmpl') && $view != 'emailtemplates') {
			echo '<div id="jac-msg-succesfull" style="display:none"></div>';
			?>
			<script type="text/javascript">
			var siteurl = '<?php
			echo JURI::base() . "index.php?tmpl=component&option=com_jacomment&view=" . $view;
			?>';
			</script>
			<?php
		}
		parent::display($cachable, $urlparams);
		
		return $this;
	}
}
?>