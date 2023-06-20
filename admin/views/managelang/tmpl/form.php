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
defined('_JEXEC') or die('Restricted access'); 
use Joomla\CMS\Factory;
$inputs = Factory::getApplication()->input;
$option	= $inputs->getCmd('option');
$inputs->set('hidemainmenu', 1);
				
if (! is_writable($this->root)) {
	echo '<span style="color:red; font-size:14px"><b>'. JText::_('FILE_IS_UNWRITABLE').'</b></span><br/><br/>';
}

?>
<div style="position:relative; width:100%; float:left ;">
<form action="index.php" method="post" name="adminForm" id="adminForm" style=" width:100%;">
	<div><b><?php echo JText::_('EDIT_LANGUAGE_FILE'), ' "', $this->filename;?>"</b></div>
	<textarea wrap="off" spellcheck="false" onscroll="scrollEditor(this);" class="inputbox jav-editor-code" id="datalang" name="datalang" rows="25" cols="110">
		<?php echo $this->data; ?>
	</textarea>			
	
	<input type="hidden" name="path_lang"  value="<?php echo $this->path_lang;?>" />
	<input type="hidden" name="task"  value="" />
	<input type="hidden" name="filename"  value="<?php echo $this->lang;?>" />
	<input type="hidden" name="client"  value="<?php echo $this->client->id;?>" />
	<input type="hidden" name="option" value="<?php echo $option;?>" />
	<input type="hidden" name="view" value="managelang" />
</form>				
</div>		