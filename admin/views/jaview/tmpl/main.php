<?php
/**
 * ------------------------------------------------------------------------
 * JA Comment Component for Joomla 2.5
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */
defined('_JEXEC') or die('Retricted Access');
?>
<div id="jacom-mainwrap">
	<div id="jacom-mainnav">
		<div class="inner">
			<div class="ja-showhide">
				<a id="menu_open" href="javascript:;" onclick="JATreeMenu.openall();" title="<?php echo JText::_('OPEN_ALL'); ?>" class=""><?php echo JText::_('OPEN_ALL'); ?></a>
				<a id="menu_close" href="javascript:;" onclick="JATreeMenu.closeall();" title="<?php echo JText::_('CLOSE_ALL'); ?>" class=""><?php echo JText::_('CLOSE_ALL'); ?></a>
			</div>
			<?php JAMenu::_menu();?>
			<script type="text/javascript">
				JATreeMenu.initmenu();
			</script>	
		</div>
	</div>
	<div id="jacom-maincontent">
		<?php JView::display($tpl); ?>
	</div>
	
	<div id="jacom-footer">
		<?php require dirname(__file__).DS.'footer.php'; ?>
	</div>
</div>