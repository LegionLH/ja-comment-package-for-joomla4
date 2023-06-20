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
?>
<script type="text/javascript">
function show_hide_file(value){
	if(value=='default'){
		$('userfile').disabled = true;
	}
	else $('userfile').disabled = false;
}
</script>
<form name="adminForm" id="adminForm" action="index.php" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend>
			<?php
			echo JText::_('IMPORT_EMAIL_TEMPLATE');
			?>
		</legend>
		<table class="admintable ja-form-wrrapper" align="left">
			<tr>
				<td class="key" align="left" style="width: 240px">
					<?php
					echo JText::_('FILE');
					?>:
					<br />
					<small>
						<?php
						echo JText::_('ONLY_SUPPORT_FILE_TYPES_INI')?>
					</small>
				</td>
				<td align="left"><input type="file" name="userfile" id="userfile" /></td>
			</tr>
			<tr>
				<td class="key" align="left" style="width: 240px">
					<?php
					echo JText::_('IMPORT_LANGUAGE');
					?>:
				</td>
				<td align="left">
					<?php
					echo $this->languages;
					?>
				</td>
			</tr>
			<tr>
				<td class="key" align="left">
					<?php
					echo JText::_('OVERWRITTEN_IF_THE_TEMPLATE_ALREADY_EXISTS')?>
				</td>
				<td align="left">
					<?php
					echo JHTML::_('select.booleanlist', 'overwrite', '', 0);
					?>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td align="left">
					<input type="submit" class="btn_add import" value="<?php echo JText::_('IMPORT')?>" />
					<input type="button" class="btn_add cancel" onclick="window.history.go(-1)" value="<?php echo JText::_('CANCEL')?>" />
				</td>
			</tr>
		</table>
	</fieldset>
	
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="view" value="emailtemplates" />
	<input type="hidden" name="task" value="import" />
	<?php
	echo JHTML::_('form.token');
	?>	
 </form>