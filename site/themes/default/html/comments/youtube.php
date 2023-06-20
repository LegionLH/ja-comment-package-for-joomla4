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
?>
<form name="JAFrom" id="JAFrom" action="<?php echo JRoute::_('index.php'); ?>" method="post">
<label for="txtYouTubeUrl">
	<?php echo JText::_("TYPE_YOUR_YOU_TUBE_URL_HERE");?>:<br />
	<input type="text" id="txtYouTubeUrl" style="width:295px" name="txtYouTubeUrl" value="" onKeyPress="javascript:if (event.keyCode==13) return false;"/>
</label>
<input type="hidden" name="option" value="com_jacomment"/>
<input type="hidden" name="view" value="comments"/>
<input type="hidden" name="layout" value="youtube"/>
<input type="hidden" name="task" value="embed_youtube"/>
<input type="hidden" name="tmpl" value="component"/>
<input type="hidden" name="id" value="<?php echo $id;?>"/>
</form>