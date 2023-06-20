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
defined('_JEXEC') or die('Retricted Access');
use Joomla\CMS\Factory;
$inputs = Factory::getApplication()->input;
$option	= $inputs->get('option');
//JHTML::_('behavior.tooltip');
?>
<script type="text/javascript">
function export2() {
	from = document.getElementById("from").value;
	num = document.getElementById("num").value;
	
	if ((from != "" && (isNaN(from) || from <= 0)) || (num != "" && (isNaN(num) || num <= 0))) {
		document.getElementById("error").style.display = "inline";
	} else {
		window.location.href = "index.php?tmpl=component&option=<?php echo $option;?>&view=imexport&group=export&task=export&from="+from+"&num="+num+"&no_html=1";
	}
}
</script>
<form action="index.php" method="post" name="adminForm">
	<div class="col100">
		<fieldset class="adminform">
			<?php echo $this->getTabs(); ?>
		</fieldset>
		<br />
		<div id="OtherComment">
			<div class="box">
				<h2><?php echo JText::_('EXPORT'); ?></h2>	
				<div class="box_content">
					<ul class="ja-list-checkboxs">
						<li class="row-1 ja-section-title">
							<h4><?php echo JText::_("EXPORT_FOR_BACKUP"); ?></h4>
						</li>
						<li class="row-0">
							<div>
								<span class="editlinktip hasTip" title="<?php echo JText::_('FROM_RECORD');?>::<?php echo JText::_('EMPTY_FOR_BEGINNING'); ?>">
									<?php echo JText::_("FROM_RECORD");?>
								</span>
								<input type="text" size="6" id="from" value="" />
								<span class="editlinktip hasTip" title="<?php echo JText::_('MAX_RECORDS');?>::<?php echo JText::_('EMPTY_FOR_ALL'); ?>">
									<?php echo JText::_("MAX_RECORDS");?>
								</span>
								<input type="text" size="6" id="num" value="" />
								<input type="button" class="btn_add export" name="export" value="<?php echo JText::_('EXPORT');?>" onclick="javascript: export2();" />
								<span id="error" style="display:none; color:#FF0000;"><?php echo JText::_('INVALID_VALUE_PLEASE_INPUT_A_POSITIVE_NUMBER');?></span>
							</div>
						</li>                                                                                                               
					</ul>
				</div>
			</div>
		</div>	
	</div>
</form>