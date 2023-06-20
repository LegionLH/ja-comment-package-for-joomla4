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

$isCommentJavoice = $jacconfig["general"]->get("is_comment_javoice", 0);
$inputs = Factory::getApplication()->input;
$contentoption = $inputs->getCmd('contentoption');

?>
<div id="jac-total-comment">
	<h2 class="componentheading"><span id="jac-number-total-comment"><?php echo $this->totalAll; ?> <?php if($isCommentJavoice && $contentoption){echo JText::_("TOTAL_ANSWER");}else if($this->totalAll > 1){echo JText::_("COMMENTS");}else{echo JText::_("COMMENT");}?></span>
		<?php if($isEnableRss){?>
			<a id="jac-rss" href="<?php echo JRoute::_($this->linkRss);?>"></a>
		<?php }?>
	</h2>	
</div>