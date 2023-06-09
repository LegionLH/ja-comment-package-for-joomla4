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
<form name="adminForm" id="adminForm" action="index.php" method="post">
	<fieldset>
		<legend>
			<?php
			echo JText::_('DUPLICATE_EMAIL_TEMPLATE');
			?>
		</legend>
		<table class="admintable" align="center" width="100%">
			<tr>
				<td class="key" align="right" width="28%">
					<?php
					echo JText::_('PLEASE_CHOOSE_LANGUAGE_TO_DUPLICATE');
					?>:
				</td>
				<td align="left">
					<?php
					echo $this->languages;
					?>
				</td>
			</tr>
			<tr>
				<td class="key" align="right">
					<?php
					echo JText::_('OVERRIDE')?>?
					<br />
					<small><?php echo JText::_('AUTOMATICALLY_OVERWRITTEN_IF_THE_TEMPLATE_ALREADY_EXISTS')?></small>
				</td>
				<td align="left" valign="top">
					<?php
					echo JHTML::_('select.booleanlist', 'overwrite', '', 0);
					?>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td align="left">
					<input type="submit" value="<?php echo JText::_('OK')?>" />
					<input type="button" onclick="window.history.go(-1)" value="<?php echo JText::_('CANCEL')?>" />
				</td>
			</tr>
		</table>
	</fieldset>
	
	<input type="hidden" name="cid" value="<?php echo $this->cid; ?>" />
	<input type="hidden" name="option" value="<?php	echo $this->option; ?>" />
	<input type="hidden" name="view" value="emailtemplates" />
	<input type="hidden" name="task" value="duplicate" />
	<?php
	echo JHTML::_('form.token');
	?>	
 </form>