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
?>
<fieldset class="adminform">
	<?php
	echo $this->menu();
	?>
</fieldset>
<br />
<form name="adminForm" action="index.php" method="post" enctype="multipart/form-data">
	<div id="Statistic">
		<div class="box">
			<h2>
				<?php
				echo JText::_('STATISTIC');
				?>
			</h2>
			<div class="box_content">
				<table class='adminlist statistic' width="100%">
					<tr>
						<td class="key" width="200"><?php
						echo JText::_('TOTAL_NEW_COMMENTS');
						?></td>
						<td><?php
						echo $this->total_new;
						?></td>
					</tr>
					<tr>
						<td class="key"><?php
						echo JText::_('COMMENTS_MADE_TODAY');
						?></td>
						<td><?php
						echo $this->total_today;
						?></td>
					</tr>
					<tr>
						<td class="key"><?php
						echo JText::_('TOTAL_COMMENTS_LAST_30_DAYS');
						?></td>
						<td><?php
						echo $this->total_30day;
						?></td>
					</tr>
					<tr>
						<td class="key"><?php
						echo JText::_('TOTAL_COMMENTS');
						?></td>
						<td><?php
						echo $this->total;
						?></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</form>