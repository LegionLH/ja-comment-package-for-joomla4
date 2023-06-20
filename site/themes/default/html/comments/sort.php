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
?>
<?php if($enableSortingOptions && $this->totalAll >0) { ?>
<div id="jac-sort">
	<?php echo JText::_("SORT_BY");?>:&nbsp;
	<a href="javascript:sortComment('date',this)"  <?php if($defaultSort == "date"){ if($defaultSortType == "ASC"){ echo 'class="jac-sort-by-active-asc"';echo ' title="' . JText::_("LATEST_COMMENT_ON_TOP").'"';}else{echo 'class="jac-sort-by-active-desc"';echo ' title="'.JText::_("LATEST_COMMENT_IN_BOTTOM").'"';}}else{echo 'class="jac-sort-by"';echo ' title="'. JText::_("LATEST_COMMENT_ON_TOP").'"';}?> id="jac-sort-by-date"><?php echo JText::_("DATE");?></a>&nbsp;
	<a href="javascript:sortComment('voted',this)" <?php if($defaultSort == "voted"){ if($defaultSortType == "ASC"){ echo 'class="jac-sort-by-active-asc"';echo ' title="' . JText::_("MOST_VOTED_ON_TOP").'"';}else{echo 'class="jac-sort-by-active-desc"';echo ' title="' . JText::_("MOST_VOTED_IN_BOTTOM").'"';}}else{echo 'class="jac-sort-by"';echo ' title="'. JText::_("MOST_VOTED_ON_TOP").'"';}?> id="jac-sort-by-voted"><?php echo JText::_("VOTES");?></a>&nbsp;						
</div>
<?php }?>
<?php if($defaultSort){?>
	<input type="hidden" value="<?php echo $defaultSort;?>" id="orderby" name="orderby" />			
<?php }?>