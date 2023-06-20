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

global $jacconfig;
$isCloseThread = $jacconfig["general"]->get("is_close_thread", 0);
?>
	<?php if($isSpecialUser){ ?>
		<span class="status-btn"><?php if($type==1) echo JText::_("APPROVED");else if($type==2) echo JText::_("SPAM");else echo JText::_("UNAPPROVED");?></span>
	<?php }?>
	<?php if((!$isCloseThread && $isAllowEditComment) || ($isCloseThread && $isSpecialUser && $isAllowEditComment)){?>	
	<div class="comment-menu" id="edit-delete-<?php echo $itemID?>">
		<a href="javascript:void(0)" onclick="return false;" class="admin-btn menu-btn"><?php echo JText::_("ADMIN");?></a>
		<div class="admin-actions menu-content">
			<ul>
				<?php
					if($isSpecialUser){
						$treeTypes = $helper->getListTreeStatus($type, $parentType);										 
						foreach ( $treeTypes as $key => $value ) {?>
							<li>
								<a onclick="changeTypeOfComment('<?php echo $key ;?>','<?php echo $itemID ;?>','<?php echo $type;?>');return false;" href="#"><?php echo $value;?></a>																																         
							</li>
				<?php  }//end for	
					}//end if																					
					
					if (!$isCloseThread || ($isCloseThread && $isSpecialUser)) {
				?>
				<li><a href="javascript:editComment(<?php echo $itemID?>,'<?php echo JText::_("REPLY")?>')" title="<?php echo JText::_("EDIT_COMMENT"); ?>"><?php echo JText::_("EDIT");?></a></li>
				<li><a href="javascript:deleteComment(<?php echo $itemID?>)" title="<?php echo JText::_("DELETE_COMMENT");?>"><?php echo JText::_("DELETE");?></a></li>
				<?php
					}
				?>
			</ul>
		</div>
	</div>	
	<?php } ?>