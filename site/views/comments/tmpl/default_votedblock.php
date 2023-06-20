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
$inputs = Factory::getApplication()->input;

$ischild = 0;
//require param - config
require_once JPATH_SITE . DS . 'components' . DS . 'com_jacomment' . DS . 'helpers' . DS . 'config.php';

//check site offline			
if ($inputs->get("islogin", 0) == 0) {
	if (! JACommentHelpers::check_permissions()) {
		return;
	}
}
?>
<!-- BEGIN - load blog items -->
<div id="jac-container-comment" class="clearfix">
<?php
include_once $helper->jaLoadBlock("comments/voteditems.php");
?>
</div>
<!-- END - load blog items -->
<br />