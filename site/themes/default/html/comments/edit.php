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

$display=FALSE;
//JHTML::_('behavior.tooltip'); 
$enableSmileys				= $this->enableSmileys;
$theme						= $this->theme;
$enableAfterTheDeadline		= $this->enableAfterTheDeadline;
$enableYoutube				= $this->enableYoutube; 
$isAttachImage 				= $this->isAttachImage;
$enableBbcode				= $this->enableBbcode;
$isEnableAutoexpanding		= $this->isEnableAutoexpanding;
$isEnableEmailSubscription	= $this->isEnableEmailSubscription;
$totalAttachFile			= $this->totalAttachFile;
$minLength					= $this->minLength;
$maxLength					= $this->maxLength;
$enableCharacterCounter 	= $this->enableCharacterCounter;
$enableLocationDetection 	= $this->enableLocationDetection;
$app = JFactory::getApplication();
?>
       
<div id="jac-edit-comment">
	<ul class="form-comment">
		<li class="form-comment" id="jac-editor-edit">
			<?php require_once $helper->jaLoadBlock("comments/editor.php");?>			
			<script type="text/javascript">										
				jac_init_expand("edit");					
			</script>						
		</li>
		<!-- BEGIN -  UPLOAD AND LIST FILE -->
			<?php if($this->isAttachImage){?>
				<li class="clearfix form-upload" <?php if(!$listFiles) echo "style='display:none;'";?> id="jac-form-uploadedit">																				
				<form id="form1edit" name="form1edit" enctype="multipart/form-data" method="post" action="index.php">
						<?php if($allowUploadSubscription){unset($_SESSION['jaccountedit']);unset($_SESSION['jactempedit']);unset($_SESSION['jacnameFolderedit']);?>
							<div class="small_edit">
							<input name="myfileedit" id="myfileedit" type="file" size="20" <?php if($totalAttachFile <= 0 || count($listFiles) >= $totalAttachFile) echo 'disabled="disabled"';?>  onblur="changeBackgroundNone(this)" onchange="startEditUpload('<?php echo $this->item->id; ?>')" class="field file" tabindex="7" onfocus="changeBackground(this)" />
							<input name="mypostedit" id="mypostedit" type="hidden" value="" />
							<span id="jac_upload_processedit" class="jac-upload-loading" style="display: none;"></span>
							<?php echo "<br />".JText::_("ATTACHED_FILE");?> (&nbsp;<?php echo JText::_("TOTAL");?> <?php echo $totalAttachFile; ?> <?php if($totalAttachFile>1){ echo JText::_("FILES_AND_MAX_SIZE_TEXT").'&nbsp;<b>'.$helper->getSizeUploadFile().'</b>';}else{ echo JText::_("FILE_AND_MAX_SIZE").'&nbsp;<b>'.$helper->getSizeUploadFile().'</b>';}?>&nbsp;)</div>								
							<p class="err" style="color:red;" id="err_myfileedit"></p>
						<?php }?>																																																							
						<div id="result_uploadedit">
							<?php 																		
								if($listFiles){
									$_SESSION['jaccountedit'] = 0;
									foreach ($listFiles as $listFile){
										$_SESSION['jaccountedit'] ++;
										$type = substr($listFile, -3);
										if ($type == 'ocx') {
											$type = 'doc';
										}
						
										$_path = JPATH_BASE . DS . '/components/com_jacomment/themes/' . $theme . '/images/' . $type . '.gif';
										if (file_exists($_path)) {
											$_link = JURI::root() . 'components/com_jacomment/themes/' . $theme . '/images/' . $type . '.gif';
										} else {
											$_link = JURI::root() . 'templates/' . $app->getTemplate() . '/html/com_jacomment/themes/' . $theme . '/images/' . $type . '.gif';
										}
										?>
										<div style="float: left; clear: both;">
											<input tabindex='4' type="checkbox" name='listfile[]' value='<?php echo $listFile;?>' onclick="checkTotalFileEdit()" checked>&nbsp;&nbsp;											
											<img src="<?php echo $_link; ?>" alt="<?php echo $listFile?>" /> <?php echo $listFile;?>
										</div>											
										<?php
									}
								}		
							?>
						</div>											
				</form>
				</li>	
			<?php }?>								
			<!-- END -  UPLOAD AND LIST FILE -->				
	</ul>	
	<div class="jac-act-form clearfix" style="display: none;"></div>
	<div id="err_newcommentedit" style="color: red;"></div>
	<!-- Expan form-->
		<?php if($isEnableEmailSubscription && $allowUploadSubscription){?>
	<div class="jac-expand-form">
			<!-- BEGIN -  EMAIL SUBSCRIPTION -->
			<div class="jac-subscribe clearfix">
				<span class="jac-text-blow-guest"> <?php echo JText::_("SUBSCRIBE_TO"); ?></span>&nbsp;
				<?php
					$listSubscribe = array();$listSubscribe[0] = JHTML::_('select.option','0',JText::_("JANONE"));$listSubscribe[1] = JHTML::_('select.option','1',JText::_('REPLIES'));$listSubscribe[2] = JHTML::_('select.option','2',JText::_('NEW_COMMENTS'));
					echo JHTML::_('select.genericlist', $listSubscribe, 'subscription_type',null,'value','text', $this->item->subscription_type);
				?>
			</div>
			<br />
			<!-- END -  EMAIL SUBSCRIPTION -->
	</div>
		<?php }?>
	<!--BEGIN - action buttion	-->
	<div class="jac-addnew clearfix" style="clear:both;">
		<input type="button" class="btTxt" onclick="cancelEditComment('<?php echo $this->item->id;?>')" title="<?php echo JText::_("CANCEL");?>" value="<?php echo JText::_("CANCEL");?>" name="btlCancelEdit" id="btlCancelEdit"/>
		<input type="button" class="btTxt" name="btlSubmit" title="<?php echo JText::_("EDIT_COMMENT");?>" value="<?php echo JText::_("SUBMIT_COMMENT");?>" onclick="saveComment('<?php echo $this->item->id;?>')" id="btlEditComment"/>				
		<!--<a href="javascript:cancelEditComment('<?php echo $this->item->id;?>')" title="<?php echo JText::_("CANCEL");?>" id="jac_cancel_edit_link"><?php echo JText::_("CANCEL");?></a>				
		<a href="javascript:saveComment('<?php echo $this->item->id;?>');" title="<?php echo JText::_("EDIT_COMMENT");?>" id="jac_edit_comment"><?php echo JText::_("SUBMIT_COMMENT");?></a>
		<span id="jac_span_edit_comment" style="display: none;"><?php echo JText::_("SUBMIT_COMMENT");?></span>													
	--></div>
	<!--END - action buttion	-->	
</div>	