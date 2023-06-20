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
<?php global $option;//print_r($this->pagination);exit;?>
<div class="jac-display-limit">
	<?php if($this->pagination->total>0){?>
		<label for="list"><?php echo JText::_("DISPLAY")?> # </label>
		<?php echo $this->getListLimit($this->lists['limitstart'], $this->lists['limit'], $this->lists['order']); ?>
	<?php }?>
</div>
<div class="jac-page-links" id="jav-page-links-comment">
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>