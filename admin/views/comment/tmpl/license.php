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

$row = $this->row;
?>
<fieldset class="adminform">
	<?php
	echo $this->menu();
	?>
</fieldset>
<br />
<fieldset>
	<legend>
		<?php
		echo JText::_('YOUR_LICENSE_INFORMATION');
		?>
	</legend>
	<table align="center" class="admintable" width="50%">
		<tr>
			<td class="key hasTip" align="left"
				title="<?php
				echo JText::_("LICENSE_FOR_DOMAINS");
				?>::<?php
				echo JText::_("LICENSE_FOR_DOMAINS_DESC");
				?>">
			<?php
			echo JText::_("LICENSE_FOR_DOMAINS");
			?>:</td>
			<td align="left"><?php
			echo $_SERVER['HTTP_HOST'];
			?></td>
		</tr>
		<tr>
			<td class="key hasTip" align="left"
				title="<?php
				echo JText::_("EMAIL_OR_USERNAME");
				?>::<?php
					echo JText::_("EMAIL_OR_USERNAME_DESC");
					?>">
				<?php
				echo JText::_("EMAIL_OR_USERNAME");
				?>:</td>
			<td align="left"><?php
			echo $row['email'];
			?></td>
		</tr>
		<tr>
			<td class="key hasTip" align="left"
				title="<?php
				echo JText::_("PAYMENT_ID");
				?>::<?php
					echo JText::_("PAYMENT_ID_DESC");
					?>">
				<?php
				echo JText::_("PAYMENT_ID");
				?>:</td>
			<td align="left"><?php
			echo $row['payment_id'];
			?></td>
		</tr>
		<tr>
			<td align="left" colspan="2"><input type="button"
				value="<?php
				echo JText::_('CHANGE')?>"
				onclick="window.location.href='index.php?option=com_jacomment&amp;view=comment&amp;task=verify'; return false;"
				title=""></td>
		</tr>
	</table>
</fieldset>