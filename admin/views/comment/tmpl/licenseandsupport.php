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
<div style="text-align: right;"><a id='jac_help' href="index.php"
	onclick="hiddenNote('moderator','<?php
	echo JText::_('TEXT_HELP')?>','<?php
	echo JText::_('CLOSE_TEXT')?>');return false;"><?php
	echo JText::_('TEXT_HELP')?></a>
</div>
<?php
$note = JText::_('IN_ORDER_TO_DOWNLOAD_UPDATES_AND_RECEIVE_SUPPORT_YOU_NEED_TO_BE_ACTIVE_JAEC_MEMBER_OR_JA_DEVELOPER_MEMBER');
JACommentHelpers::displayNote($note, 'moderator');
?>
<div style="width: 100%;">
	<fieldset>
		<legend>
			<?php
			echo JText::_('DOCUMENTATION_AMP_USERGUIDES');
			?>
		</legend>
		<table width="100%">
			<tr>
				<td width="80%" valign="top">Detailed documentation and userguides are
				available on our wiki site.
					<ul>
						<li>Wiki &amp; Documentation (<a target="_blank"
							href="http://wiki.joomlart.com/wiki/JA_Comment/Overview"
							title="Click here to go to Wiki &amp; Documentation">JA Comment Wiki
						Section</a>)</li>
					</ul>
				</td>
			</tr>
		</table>
	</fieldset>
</div>
<div style="width: 100%;">
	<fieldset>
		<legend>
			<?php
			echo JText::_('SUPPORT');
			?>
		</legend>
		<table width="100%">
			<tr>
				<td width="80%" valign="top">Customer support is our top priority,
				with a valid active subscription, you can always get help via one of
				follow options:
				<ul>
					<li>JA Comment <a target="_blank"
						href="http://www.joomlart.com/forums/forumdisplay.php?194-JA-Comment-Component"
						title="Click here to go to JA Comment Forum">Forum</a></li>
					<li>Premium <a target="_blank"
						href="http://support.joomlart.com/index.php"
						title="Click here to go to Ticket Support">ticket Support</a></li>
				</ul>
				We will try our best to get back to you within 24 hours (9:00AM -
				5:00PM, Monday - Friday GMT +8)</td>
			</tr>
		</table>
	</fieldset>
</div>
<!--<fieldset>
  <legend><?php
		echo JText::_('YOUR_LICENSE_INFORMATION');
		?></legend>
  <table align="center" class="admintable" width="50%">
  <tr>
    <td class="key hasTip" align="left" title="<?php
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
        <td class="key hasTip" align="left" title="<?php
								echo JText::_("EMAIL_OR_USERNAME");
								?>::<?php
								echo JText::_("EMAIL_OR_USERNAME_DESC");
								?>">
		<?php
		echo JText::_("EMAIL");
		?>:</td>
        <td align="left"><?php
								echo $row['email'];
								?></td>
    </tr>
    <tr>
        <td class="key hasTip" align="left" title="<?php
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
        <td align="left" colspan="2">
        <input type="button" value="<?php
								echo JText::_('CHANGE')?>" onclick="window.location.href='index.php?option=com_jacomment&amp;view=comment&amp;task=verify'; return false;" title="" />
        </td>
    </tr>
    </table>
  </fieldset>-->