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

global $jacconfig;
$theme = $jacconfig['layout']->get('theme');
$textAreaID = $this->textAreaID;
?>
	<a href="#" title="<?php echo JText::_("BOLD_TEXT"); ?>" onclick="return DCODE.doClick ('<?php echo $textAreaID;?>', 'B');">
		<img alt="" src="../components/com_jacomment/themes/default/images/gfx/b.gif" height="25" width="25"/>
	</a>
	<a href="#" title="<?php echo JText::_("ITALIC_TEXT"); ?>" onclick="return DCODE.doClick ('<?php echo $textAreaID;?>', 'I');">
		<img alt="" src="../components/com_jacomment/themes/default/images/gfx/i.gif" height="25" width="25"/>
	</a>
	<a href="#" title="<?php echo JText::_("UNDERLINE_TEXT"); ?>" onclick="return DCODE.doClick ('<?php echo $textAreaID;?>', 'U');">
		<img alt="" src="../components/com_jacomment/themes/default/images/gfx/u.gif" height="25" width="25"/>
	</a>
	<a href="#" title="<?php echo JText::_("LINETHROUGH_TEXT"); ?>" onclick="return DCODE.doClick ('<?php echo $textAreaID;?>', 'S');">
		<img alt="" src="../components/com_jacomment/themes/default/images/gfx/s.gif" height="25" width="25"/>
	</a>
	<a href="#" title="<?php echo JText::_("UNORDERED_BULLET_LIST"); ?>" onclick="return DCODE.doClick ('<?php echo $textAreaID;?>', 'UL');">
		<img alt="" src="../components/com_jacomment/themes/default/images/gfx/ul.gif" height="25" width="25"/>
	</a>	
	<a href="#" title="<?php echo JText::_("QUOTATION"); ?>" onclick="return DCODE.doClick ('<?php echo $textAreaID;?>', 'QUOTE');">
		<img alt="" src="../components/com_jacomment/themes/default/images/gfx/quote.gif" height="25" width="25"/>
	</a>
	<a href="#" title="<?php echo JText::_("LINK_PER_EMAIL"); ?>" onclick="return DCODE.doClick ('<?php echo $textAreaID;?>', 'LINK');">
		<img alt="" src="../components/com_jacomment/themes/default/images/gfx/link.gif" height="25" width="25"/>
	</a>
	<a href="#" title="<?php echo JText::_("IMAGE"); ?>" onclick="return DCODE.doClick ('<?php echo $textAreaID;?>', 'IMG');">
		<img alt="" src="../components/com_jacomment/themes/default/images/gfx/img.gif" height="25" width="25"/>
	</a>