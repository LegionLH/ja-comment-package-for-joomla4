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
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
?>
<?php global $jacconfig, $option;//print_r($this->pagination);exit;?>
<div class="jav-page-links" id="jav-page-links-0">
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>
<div class="jav-display-limit">
	<?php if($this->pagination->total>0){?>
		<label for="limit"><?php echo JText::_("DISPLAY")?> # </label>
		<?php echo $this->getListLimit($this->lists['limitstart'], $this->lists['limit'], $this->lists['order']); ?>
	<?php }?>
</div>
<div class="clear"></div>