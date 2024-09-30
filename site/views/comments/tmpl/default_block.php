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

$ischild = 0;
//require param - config
require_once JPATH_SITE . DS . 'components' . DS . 'com_jacomment' . DS . 'helpers' . DS . 'config.php';

//check site offline			
$inputs = Factory::getApplication()->input;
if ($inputs->get("islogin", 0) == 0) {
	if (! JACommentHelpers::check_permissions()) {
		return;
	}
}
global $jacconfig;
$isCloseThread = $jacconfig["general"]->get("is_close_thread", 0);
$isShowCommentCount = $jacconfig["comments"]->get("is_show_comment_count", 0);
$isEnableConversationBar = $jacconfig["layout"]->get("enable_conversationbar", 0);
$isEnableVotedTab = $jacconfig["layout"]->get("enable_votedlist", 0);

?>
<!-- BEGIN - load block head -->
<?php /*require_once $helper->jaLoadBlock("comments/head.php");*/	?>
<!-- END   - load block head -->

<!-- BEGIN - load block header -->
<?php require_once $helper->jaLoadBlock("comments/header.php");	?>
<!-- END   - load block header -->
<div id="jac-headerbar"><?php echo JText::_("COMMENT_AND_JOIN_THE_DISCUSSION"); ?></div>
<!-- BEGIN - load conversation bar -->
<?php
if ($isEnableConversationBar) {
	include_once $helper->jaLoadBlock("comments/conversation.php");
}
?>
<!-- END - load conversation bar -->
<?php
if ($isEnableVotedTab) {
?>
<ul class="nav nav-tabs" id="jacTab">
  <li class="active"><a href="#jacAll" data-toggle="tab"><?php echo JText::_("COMMENTS_TAB_HEADER"); ?></a></li>
  <li><a href="#jacVoted" data-toggle="tab"><?php echo JText::_("VOTED_TAB_HEADER"); ?></a></li>
</ul>
<div class="tab-content jac-tab-content">
  <div class="tab-pane fade in active" id="jacAll">
<?php
}

if ($formPosition == 1) {
	//position: form add new above items
	?>
	<!-- BEGIN - load blog add new -->
	<?php
		if (! $isCloseThread) {
			?>
			<div id="jac-wrapper-form-add-new" class="clearfix">
			<?php
			include_once $helper->jaLoadBlock("comments/addnew.php");
			?>
			</div>
		<?php
		}
	
	?>
	<!-- END - load blog total -->

	<!-- BEGIN - load blog sort -->
	<?php
	include_once $helper->jaLoadBlock("comments/sort.php");
	?>
	<!-- END - load blog sort -->

	<!-- BEGIN - load blog total -->
	<?php
	if ($isShowCommentCount) {
		include_once $helper->jaLoadBlock("comments/total.php");
	}
	?>
	<!-- END - load blog total -->
	<div id="jac-container-new-comment" class="clearfix lh0"></div>
	<!-- END - load blog items -->
	<div id="jac-container-comment" class="clearfix lh0">
	<?php
	include_once $helper->jaLoadBlock("comments/items.php");
	?>    
	</div>
	<!-- END - load blog items -->
	<?php
} else {
	//items above form add new
	?>
	<!-- BEGIN - load blog sort -->
	<?php
	include_once $helper->jaLoadBlock("comments/sort.php");
	?>
	<!-- END - load blog sort -->

	<!-- BEGIN - load blog total -->
	<?php
	if ($isShowCommentCount) {
		include_once $helper->jaLoadBlock("comments/total.php");
	}
	?>
	<!-- END - load blog total -->

	<!-- END - load blog items -->
	<div id="jac-container-comment" class="clearfix">
	<?php
	include_once $helper->jaLoadBlock("comments/items.php");
	?>    
	</div>
	<!-- END - load blog items -->

	<div id="jac-container-new-comment" class="clearfix"></div>

	<!-- BEGIN - load blog add new -->
	<?php
	if (! $isCloseThread) {
		?>
		<div id="jac-wrapper-form-add-new" class="wrap clearfix">
		<?php
		include_once $helper->jaLoadBlock("comments/addnew.php");
		?>
		</div>
		<?php
	}
	?>
	<!-- END - load blog total -->
	<?php
}

if ($this->pagination->total > 0) {
	?>
	<div id="jac-pagination" class="pagination wrap clearfix">
	<?php
	include_once $helper->jaLoadBlock("comments/paging.php");
	?>
	</div>
	<?php
}
?>

<input type="hidden" id="limitstart" value="0" />
<?php
if ($isEnableVotedTab) {
?>
	</div>
	<div class="tab-pane fade" id="jacVoted">
		<div id="jac-container-voted-comment" class="clearfix"><?php echo JText::_('LOADING_VOTED_COMMENTS_LIST'); ?></div>
	</div>
</div>
<?php
}
?>
<!-- BEGIN - load template footer -->
<?php
if ($footerText) {
	include_once $helper->jaLoadBlock("comments/footer.php");
}
?>
<!-- END   - load template footer -->