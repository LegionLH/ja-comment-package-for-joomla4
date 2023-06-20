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

//JHTML::_('behavior.tooltip');
?> 
<script type="text/javascript">
    function updateCounter()
    {
	url = "index.php?option=com_jacomment&view=configs&group=maintenance&task=updatecounter";

	jQuery(document).ready( function($) {
	    $('#btnCounter').attr('disabled', 'disabled');
	    $("#counter_success").css('display','none');
	    $('#ajax_loading').show();

	    jQuery.ajax({
		type: "GET",
		url:url,
		success: function(){
		    $('#btnCounter').attr('disabled', '');
		    $('#ajax_loading').hide();
		    $("#counter_success").css('display','inline');
		}
	    });
	});
    }
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
    <div class="col100">
	<fieldset class="adminform">
	    <?php echo $this->getTabs(); ?>
	</fieldset>
	<br/>
	<div id="MaintenanceSettings">
	    <div class="box">
		<h2><?php echo JText::_('MAINTENANCE_SETTINGS'); ?></h2>
		<div class="box_content">
		    <ul class="ja-list-checkboxs">
			<li class="row-0">
			    <span class="editlinktip hasTip" title="<?php echo JText::_("UPDATE_COUNTER") ?>::<?php echo JText::_("CLICK_THIS_BUTTON_TO_UPDATE_COUNTER_OF_JACOMMENT_SYSTEM") ?>">
				<input type="button" value="<?php echo JText::_("UPDATE_COUNTER") ?>" name="btnCounter" id="btnCounter" onclick="updateCounter()" />
			    </span>
			    <img src="components/com_jacomment/asset/images/loading.gif" id="ajax_loading" border="0" alt="" style="display:none; vertical-align:middle;" />
			    <span id="counter_success" style="display:none; color:#7CC369;"><?php echo JText::_('UPDATE_COUNTER_SUCCESSFULLY'); ?></span>
			</li>
		    </ul>
		</div>
	    </div>
	</div>
    </div>
    <div class="clr"></div>
    <input type="hidden" name="option" value="com_jacomment" />
    <input type="hidden" name="view" value="configs" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="group" value="maintenance" />
    <?php echo JHTML::_('form.token'); ?>
</form>