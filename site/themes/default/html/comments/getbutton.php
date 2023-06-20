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
    
use Joomla\CMS\Factory;

require_once (JPATH_SITE.DS.'components'.DS.'com_jacomment'.DS.'models'.DS.'comments.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_jacomment'.DS.'helpers'.DS.'jahelper.php');
global $jacconfig;
JACommentHelpers::get_config_system();
$isCommentJavoice = $jacconfig["general"]->get("is_comment_javoice", 0);
$inputs = Factory::getApplication()->input;
$contentoption	= $inputs->getCmd('option');

$model = new JACommentModelComments(); 
$helper = new JACommentHelpers ( );
$app = Factory::getApplication();
//add style of template for component					
JHTML::stylesheet('components/com_jacomment/themes/'.$theme.'/css/style.css');
if(file_exists(JPATH_BASE.DS.'templates'.DS.$app->getTemplate().DS.'html'.DS."com_jacomment".DS."themes".DS. $theme .DS."css".DS."style.css")){		
	JHTML::stylesheet('templates/'.$app->getTemplate().'/html/com_jacomment/themes/'.$theme.'/css/style.css');	 
}
$lang = Factory::getLanguage();											
if ( $lang->isRTL() ) {
	if(file_exists(JPATH_BASE.DS.'components/com_jacomment/themes/'.$theme.'/css/style_rtl.css')){
		JHTML::stylesheet('components/com_jacomment/themes/'.$theme.'/css/style_rtl.css');
	}
	if(file_exists(JPATH_BASE.DS.'templates'.DS.$app->getTemplate().DS.'html'.DS."com_jacomment".DS."themes".DS. $theme .DS."css".DS."style_rtl.css")){		
		JHTML::stylesheet('templates/'.$app->getTemplate().'/html/com_jacomment/themes/'.$theme.'/css/style_rtl.css');	 
	}
}

$display_comment_link 	= $plgParams->get('display_comment_link',1);
$display_comment_count 	= $plgParams->get('display_comment_count',1);

//display normal addbutton and count in com_content
if(!isset($typeDisplay)){
	if($display_comment_count){
		$search  = '';
		$search .= ' AND c.option="'.$option.'"';
		$search .= ' AND c.contentid='.$id.'';
		//get all Item is approved
		if(!$helper->isSpecialUser()){	
			$search .= ' AND type=1';
		}
		if($isCommentJavoice && trim($contentoption)=='com_javoice'){
			$items = $model->getItems($search);
			$items = $model->getItems($search);
			$totalAll = $helper->getTotalAnswer($items);
		}else{
			$totalType = $model->getTotalByType($search);
			
			if($totalType){
				$totalAll = (int)array_sum($totalType);
			}
			else{
				$totalAll = 0;
			}
		}
	}
?>
	<?php if($display_comment_link){?>	
		<div class="jac-add-button contentpaneopen"><a class="jac-links" style="background: url('components/com_jacomment/asset/images/comment.png') no-repeat left center; padding-left: 16px;" href="<?php echo $links."#ja-contentwrap"; ?>" title="<?php if($isCommentJavoice && trim($contentoption)=='com_javoice'){echo JText::_('ADD_ANSWER');}else{echo JText::_('ADD_COMMENT');} ?>"><?php if($isCommentJavoice && trim($contentoption)=='com_javoice'){echo JText::_('ADD_ANSWER');}else{echo JText::_('ADD_COMMENT');} ?></a>
		<?php if(!$display_comment_count){?>		
			</div>
		<?php }?>					
	<?php }?>
	
	<?php if($display_comment_count){?>
		<?php if(!$display_comment_link){?>
		<div class="jac-add-button contentpaneopen">
		<?php }?>		
		<span class="jac-count-comment"><?php echo '('.$totalAll.')';?></span></div>
	<?php }?>	
<?php		
}
//only display addbutton or count for system template
else{
?>
<?php //display add comment button ?>	
<?php if($typeDisplay == "onlyButton" && $display_comment_link):?>	
	<a class="jac_links jac-links<?php echo $id;?>" style="background: url('components/com_jacomment/asset/images/comment.png') no-repeat left center; padding-left: 16px;" href=<?php echo $links."#ja-contentwrap"; ?> title="<?php if($isCommentJavoice && trim($contentoption)=='com_javoice'){echo JText::_('ADD_ANSWER');}else{echo JText::_('ADD_COMMENT');} ?>"><?php if($isCommentJavoice && trim($contentoption)=='com_javoice'){echo JText::_('ADD_ANSWER');}else{echo JText::_('ADD_COMMENT');} ?></a>	
<?php endif; ?>
<?php //display count button?>
<?php if($typeDisplay == "onlyCount" && $display_comment_count):
		$search  = '';
		$search .= ' AND c.option="'.$option.'"';
		$search .= ' AND c.contentid='.$id.'';				
		//get all Item is approved
		if(!$helper->isSpecialUser()){$search .= ' AND type=1';}
		
		if($isCommentJavoice && trim($contentoption)=='com_javoice'){
			$items = $model->getItems($search);
			$totalAll = $helper->getTotalAnswer($items);
		}else{
			$totalType = $model->getTotalByType($search);	 
			if($totalType){
				$totalAll = (int)array_sum($totalType);
			}else{ 
				$totalAll = 0;
			}
		}		
?>	
	<span class="jac-links<?php echo $id;?>">(<?php echo $totalAll ?>)</span>
<?php endif;?>
<?php }?>