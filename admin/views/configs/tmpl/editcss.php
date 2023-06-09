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

$content = $this->content;
$theme = $this->theme;
?>
<form name="adminForm" id="adminForm" action="index.php" method="post">
<table class="admintable">		        			        	
	<tr>		        		
		<td align="left">
			<textarea wrap="off" spellcheck="false" onscroll="scrollEditor(this);" class="inputbox jac-editor-code" id="content" name="content" rows="19" cols="90"><?php echo $content;?></textarea>
		</td>
	</tr>	
</table>
<input type="hidden" name="option" value="com_jacomment" />
<input type="hidden" name="view" value="configs" />
<input type="hidden" name="group" value="layout" />
<input type="hidden" name="theme" value="<?php echo $theme;?>" />
<input type="hidden" name="task" value="saveEditCSS" />
<input type="hidden" name="tmpl" value="component" /> 
<?php echo JHTML::_( 'form.token' ); ?>	
</form>