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
<script type="text/javascript">
function submitbutton(pressbutton){
    var form = document.adminForm;
    form.task.value = pressbutton;
    form.submit();
}
</script>
<form name="JAFrom" id="JAFrom" action="index.php" method="post">
	<p><?php echo JText::_("ENTER_THE_VIDEO_URL_TO_EMBED_BELOW");?></p>
	<input type="text" id="txtYouTubeUrl" name="txtYouTubeUrl" value=""/>
	<input type="hidden" name="option" value="com_jacomment" />
	<input type="hidden" name="view" value="comments" />
	<input type="hidden" name="layout" value="youtube" />
	<input type="hidden" name="task" value="embed_youtube" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="id" value="<?php echo $this->id;?>" />
</form>