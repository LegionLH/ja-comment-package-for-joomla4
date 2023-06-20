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

global $mainframe;
//JHTML::_('behavior.tooltip');
jimport('joomla.html.pane');
$helper = new JACommentHelpers();
$maxSizeUpload = (int)$helper->checkUploadSize();
?>
<script type="text/javascript">
maxSizeUpload = <?php echo $maxSizeUpload; ?>;
function submitbutton(pressbutton){
	var form = document.adminForm;
	var checkInteger  = /(^\d\d*$)/;
	if(!checkInteger.test($("total_attach_file").value)){	
		$("error_total_attach_file").innerHTML = $("hdInvalidTotalAttach").value;
		alert($("hdInvalidTotalAttach").value);
		$("total_attach_file").focus();
		return;
	}
	
	if(!checkInteger.test($("maximum_comment_in_item").value)){
		$("error_maximum_comment_in_item").innerHTML = $("hdInvalidMaximumNumber").value;
		alert($("hdInvalidMaximumNumber").value);
		$("maximum_comment_in_item").focus();
		return;
	}

	if(!checkInteger.test($("max_size_attach_file").value)){
		$("error_max_size_attach_file").innerHTML = $("hdInvalidSizeAttach").value;
		alert($("hdInvalidSizeAttach").value);
		$("max_size_attach_file").focus();
		return;
	}
				
	if(!checkInteger.test($("duration_post_comment").value)){
		$("error_max_size_attach_file").innerHTML = $("hdInvalidDurationPost").value;
		alert($("hdInvalidDurationPost").value);
		$("duration_post_comment").focus();
		return;
	}
				
    form.task.value = pressbutton;
    form.submit();		
}

// jQuery(document).ready(function(){
    // jQuery("input").click(function() {
    	// var checkInteger  = /(^\d\d*$)/;
    	// if(checkInteger.test($("total_attach_file").value) && checkInteger.test($("maximum_comment_in_item").value)){
    		// show_bar_preview('<?php echo JText::_('Preview')?>', '<?php echo JText::_('Cancel')?>');
    	// }        			        			
    // });
// });
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="col100">
	<fieldset class="adminform">
		<?php echo $this->getTabs();?>
	</fieldset>
	<br/>
	<div class="box">
		<h2><?php echo JText::_('COMMENTS_SETTINGS');?></h2>	
		<div class="box_content">
			<ul class="ja-list-checkboxs">
				<li class="row-1">
					<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">
						<col width="10%" /><col width="90%" />
						<tr>
							<td><img class="screenshot" alt="Screenshot" src="components/com_jacomment/asset/images/settings/comments/layout-screenshot-child-comment.png"/></td>
							<td>
								<?php $is_show_child_comment = $this->params->get('is_show_child_comment', 0);?>
								<label for="is_show_child_comment">
									<input type="checkbox" <?php if($is_show_child_comment) echo 'checked="checked"';?>value="1" name="comments[is_show_child_comment]" id="is_show_child_comment" /> <?php echo JText::_('SHOW_CHILD_COMMENT')?>					
								</label>
								<p class="info"><?php echo JText::_('ALWAYS_SHOW_CHILD_COMMENT')?></p>
							</td>											
						</tr>
					</table>										  							
				</li>
				<li class="row-0">
					<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">
						<col width="10%" /><col width="90%" />
						<tr>
							<td><img class="screenshot" alt="Screenshot" src="components/com_jacomment/asset/images/settings/comments/layout-screenshot-threads.png"/></td>
							<td>
								<?php $is_enable_threads = $this->params->get('is_enable_threads', 0);?>
								<label for="is_enable_threads">
									<input type="checkbox" <?php if($is_enable_threads) echo 'checked="checked"';?>value="1" name="comments[is_enable_threads]" id="is_enable_threads" /> <?php echo JText::_('ENABLE_THREADS')?>
								</label>
								<p class="info"><?php echo JText::_('THREADS_MAKE_IT_EASIER_TO_FOLLOW_A_CONVERSATION')?></p>
							</td>											
						</tr>
					</table>										  							
				</li>
				<?php //BEGIN - ADD by NghiaTD - change config of vote?>
				<li class="row-1">                				
					<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">
						<col width="10%" /><col width="90%" />
						<tr>
							<td><img class="screenshot" alt="Screenshot" src="components/com_jacomment/asset/images/settings/comments/layout-screenshot-voting.png"/></td>
							<td>
								<?php $isAllowVoting = $this->params->get('is_allow_voting', 0);?>
								<label for="is_allow_voting">					
									<input type="checkbox" <?php if($isAllowVoting) echo 'checked="checked"';?>value="1" name="comments[is_allow_voting]" id="is_allow_voting" /> <?php echo JText::_('ENABLE_VOTING')?>
								</label>
								<p style="margin-bottom: 0pt;" class="info"><?php echo JText::_('ALLOW_USERS_TO_VOTE_UP_OR_DOWN_A_COMMENT')?></p>
							</td>											
						</tr>
					</table>
				 </li>		
				<?php //END - ADD by NghiaTD - change config of vote?>	
				<li class="row-0">
					<?php $is_attach_image = $this->params->get('is_attach_image', 0);?>				
					<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">
						<col width="10%" /><col width="90%" />
						<tr>
							<td><img class="screenshot" alt="Screenshot" src="components/com_jacomment/asset/images/settings/comments/layout-screenshot-images.png?=2"/></td>
							<td>
								<label for="is_attach_image">
									<input type="checkbox" <?php if($is_attach_image) echo 'checked="checked"';?>value="1" name="comments[is_attach_image]" id="is_attach_image" onclick="changeStatusAttachImg(this)" /> <?php echo JText::_('ENABLE_ATTACH_FILES_IN_POSTED_COMMENTS')?>
								</label>
								<p class="info"><?php echo JText::_('ENABLE_USERS_TO_POST_FILES_HOSTED_ELSEWHERE_INTO_COMMENTS')?></p>
								<div id="div_is_attach_image" <?php if(!$is_attach_image) echo "style='display:none;'";?>>
									<?php  $total_attach_file = $this->params->get('total_attach_file', '5');?>
									<label>
										<?php echo JText::_("MAX_NUMBER_OF_ALLOWED_ATTACHMENTS");?>
									</label>
									<br />
									<input type="text" onkeyup="checkValidKey(this.value,'total_attach_file')" onkeypress="return isNumberKey(event)" size="3" maxlength="4" value="<?php echo $total_attach_file;?>" name="comments[total_attach_file]" id="total_attach_file" onblur="checkTotalAttach(this)" /> <?php echo JText::_('TOTAL_OF_ATTACHED_FILE')?>
									<p class="info" style="color: red;" id="error_total_attach_file"></p>
									<input type="hidden" value="<?php echo $total_attach_file;?>" id="hidden_total_attach_file" />
									<?php $max_size_attach_file = $this->params->get('max_size_attach_file', $maxSizeUpload);?>
									<label>
										<?php echo JText::_("MAX_SIZE_FOR_AN_ATTACHED_FILE");?>
									</label>
									<br />
									<input type="text" onkeypress="return isNumberKey(event)" onkeyup="checkValidKey(this.value,'max_size_attach_file')" onblur="checkSizeUpload(this, this.value)" size="3" maxlength="4" value="<?php echo $max_size_attach_file;?>" name="comments[max_size_attach_file]" id="max_size_attach_file" /> (<?php echo JText::_("M");?><?php echo "<=" . $helper->checkUploadSize(); ?>)  <?php echo JText::_('SIZE_OF_ATTACH_FILE'); ?>
									<p class="info" style="color: red;" id="error_max_size_attach_file"></p>
									<input type="hidden" value="<?php echo $max_size_attach_file;?>" id="hidden_max_size_attach_file" />		
									<?php 
										$attach_file_type = $this->params->get('attach_file_type', 'doc,docx,pdf,txt,zip,rar,jpg,bmp,gif,png');
									?>	
									<label>
										<?php echo JText::_("ALLOWED_FILE_TYPES");?>
									</label>																		
									<br />
									<?php 
										$listAllowUploads = array('doc', 'docx', 'pdf', 'txt','zip','rar','jpg','bmp','gif','png');
										$listUploads 	  = explode(",", $attach_file_type);
										foreach ($listAllowUploads as $listAllowUpload) {
									?>
											<label for="fileType<?php echo $listAllowUpload; ?>" style="float: left;">	
												<input type="checkbox" name="comments[attach_file_type][]" <?php if(in_array($listAllowUpload, $listUploads)) echo("checked='checked'");?>  id="fileType<?php echo $listAllowUpload; ?>" value="<?php echo $listAllowUpload; ?>">&nbsp;<?php echo $listAllowUpload;?>&nbsp;
											</label>
									<?php
										}
									?>
									<br class="clear" />
									<p class="info"><?php echo JText::_('SELECT_FILE_TYPE_WHICH_CAN_BE_UPLOADED_AS_ATTACHMENTS_SUPPORT_DOC_DOCX_PDF_TXT_ZIP_RAR_JPG_BMP_GIF_PNG_ONLY')?></p>
								</div>
							</td>
						</tr>
					</table>
				</li>
				<li class="row-1">
					<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">
						<col width="10%" /><col width="90%" />
						<tr>
							<td><img class="screenshot" alt="Screenshot" src="components/com_jacomment/asset/images/settings/comments/layout-screenshot-url.png?=2"/></td>
							<td>
								<?php $is_enable_website_field = $this->params->get('is_enable_website_field', 0);?>
								<label for="is_enable_website_field">
									<input type="checkbox" <?php if($is_enable_website_field) echo 'checked="checked"';?>value="1" name="comments[is_enable_website_field]" id="is_enable_website_field"/> <?php echo JText::_('ENABLE_WEBSITE_FIELD')?>
								</label>
								<p class="info"><?php echo JText::_('HIDE_OR_SHOW_THE_OPTIONAL_WEBSITE_FIELD_FOR_GUEST_COMMENTS')?></p>
							</td>
						</tr>
					</table>
				</li>
				<li class="row-0">
					<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">
						<col width="10%" /><col width="90%" />
						<tr>
							<td><img class="screenshot" alt="Screenshot" src="components/com_jacomment/asset/images/settings/comments/layout-screenshot-expanding.gif?=2"/></td>
							<td>
								<?php $is_enable_autoexpanding = $this->params->get('is_enable_autoexpanding', 0);?>
								<label for="is_enable_autoexpanding">
									<input type="checkbox" <?php if($is_enable_autoexpanding) echo 'checked="checked"';?>value="1" name="comments[is_enable_autoexpanding]" id="is_enable_autoexpanding"/> <?php echo JText::_('ENABLE_AUTOEXPANDING_TEXT_AREA')?>
								</label>
								<p class="info"><?php echo JText::_('THE_COMMENT_BOX_GROWS_BIGGER_AS_YOU_TYPE')?></p>
							</td>
						</tr>
					</table>
				</li>
				<li class="row-1">
					<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">
						<col width="10%" /><col width="90%" />
						<tr>
							<td><img class="screenshot" alt="Screenshot" src="components/com_jacomment/asset/images/settings/comments/layout-screenshot-subscription.png"/></td>
							<td>
								<?php $is_enable_email_subscription = $this->params->get('is_enable_email_subscription', 0);?>
								<label for="is_enable_email_subscription">
									<input type="checkbox" <?php if($is_enable_email_subscription) echo 'checked="checked"';?>value="1" name="comments[is_enable_email_subscription]" id="is_enable_email_subscription"/> <?php echo JText::_('ENABLE_EMAIL_SUBSCRIPTION')?>
								</label>
								<p style="margin-bottom: 0pt;" class="info"><?php echo JText::_('ALLOW_USERS_TO_CHOOSE_TO_RECEIVE_EMAILS_FOR_REPLIES_TO_THEIR_COMMENTS_OR_ALL_COMMENTS_MADE_ON_THE_POST')?></p>
							</td>
						</tr>
					</table>
				</li>
				
				<?php //BEGIN - ADD by NghiaTD - change config of report?>
				<li class="row-0">    
					<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">
						<col width="10%" /><col width="90%" />
						<tr>
							<td><img class="screenshot" alt="Screenshot" src="components/com_jacomment/asset/images/settings/comments/layout-screenshot-report.png"/></td>
							<td>
								<?php $isAllowReport = $this->params->get('is_allow_report', 0);?>
								<label for="is_allow_report">					
									<input type="checkbox" <?php if($isAllowReport) echo 'checked="checked"';?>value="1" name="comments[is_allow_report]" id="is_allow_report" /> <?php echo JText::_('ENABLE_REPORT')?>
								</label>
								<p style="margin-bottom: 0pt;" class="info"><?php echo JText::_('ENABLE_COMMENT_REPORTING_HELPFUL_FOR_EASY_IDENTIFICATION_OF_SPAM')?></p>					
							</td>
						</tr>
					</table>				
				</li>		
				<?php //END - ADD by NghiaTD - change config of report?>
				<li class="row-1">
					<?php $is_allow_approve_new_comment = $this->params->get('is_allow_approve_new_comment', 0);?>
					<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">
						<col width="10%" /><col width="90%" />
						<tr>
							<td>&nbsp;</td>
							<td>
								<label for="is_allow_approve_new_comment">
									<input type="checkbox" <?php if($is_allow_approve_new_comment) echo 'checked="checked"';?>value="1" name="comments[is_allow_approve_new_comment]" id="is_allow_approve_new_comment" onclick="changeStatusApproveNewComment(this);" /> <?php echo JText::_("ADMIN_NEED_TO_APPROVE_NEW_COMMENT");?>
								</label>
								<p class="info"><?php echo JText::_('HIDE_OR_SHOW_ADMIN_NEED_TO_APPROVE_NEW_COMMENT')?></p>
								<div id="div_is_allow_approve_new_comment" class="child clearfix" <?php if(!$is_allow_approve_new_comment) echo "style='display:none;'";?>>
									<?php
									$is_allow_approve_guest_comment = $this->params->get('is_allow_approve_guest_comment', '0');
									$is_allow_approve_member_comment = $this->params->get('is_allow_approve_member_comment', '0');
									?>
									<label>
										<input type="checkbox" <?php if($is_allow_approve_new_comment && $is_allow_approve_guest_comment) echo 'checked="checked" ';?>value="1" name="comments[is_allow_approve_guest_comment]" id="is_allow_approve_guest_comment" onclick="checkApproveStatus(this);" /> <?php echo JText::_("ONLY_APPROVE_FOR_GUEST");?>
									</label>
									<p class="info"><?php echo JText::_('ONLY_APPROVE_FOR_GUEST_DESC')?></p>
									<br />
									<label>
										<input type="checkbox" <?php if($is_allow_approve_new_comment && $is_allow_approve_member_comment) echo 'checked="checked" '; ?>value="1" name="comments[is_allow_approve_member_comment]" id="is_allow_approve_member_comment" onclick="checkApproveStatus(this);" /> <?php echo JText::_("ONLY_APPROVE_FOR_MEMBER"); ?>
									</label>
									<p class="info"><?php echo JText::_('ONLY_APPROVE_FOR_MEMBER_DESC'); ?></p>
									<br />
								</div>
							</td>
						</tr>
					</table>
				</li>
				<li class="row-0">
					<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">
						<col width="10%" /><col width="90%" />
						<tr>
							<td></td>
							<td>
								<label>
									<?php echo JText::_("USER_INFO_IS_DISPLAYED_IN_COMMENTS");?>
								</label>
								<br />
								<select name="comments[display_user_info]" id="display_user_info" class="inputbox">
									<option id="display_user_info_fullname" <?php if($this->params->get('display_user_info', 'fullname') == "fullname") echo 'selected="selected"';?>  value="fullname"><?php echo JText::_("FULL_NAME");?></option>
									<option id="display_user_info_username" <?php if($this->params->get('display_user_info', 'fullname') == "username") echo 'selected="selected"';?>  value="username"><?php echo JText::_("USER_NAME");?></option>
								</select> <?php echo JText::_('DISPLAY_FULL_NAME_OR_USER_NAME_IN_EACH_COMMENT')?>
							</td>
						</tr>
					</table>				
				</li>
				<li class="row-1">
					<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">
						<col width="10%" /><col width="90%" />
						<tr>
							<td>&nbsp;</td>
							<td>
								<label for="maximum_comment_in_item">                                                            
									<?php echo JText::_('MAXIMUM_NUMBER_OF_COMMENT')?>
								</label>
								<br />
								<input type="text" class="inputbox" size="3" onkeyup="checkValidKey(this.value,'maximum_comment_in_item')" onkeypress="return isNumberKey(event)" value="<?php echo $this->params->get('maximum_comment_in_item', '100');?>" id="maximum_comment_in_item" name="comments[maximum_comment_in_item]" onblur="checkMaximumNumber(this)" /> <?php echo JText::_('THE_MAXIMUM_NUMBER_OF_COMMENT_IN_A_ITEM_USERS_CAN_POST')?>
								<input type="hidden" id="hidden_maximum_comment_in_item" value="<?php echo $this->params->get('maximum_comment_in_item', '100');?>" />
								<p style="margin-bottom: 0pt;color: red;" id="error_maximum_comment_in_item" class="info"></p>
							</td>
						</tr>
					</table>
				</li>
				<li class="row-0">
					<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">
						<col width="10%" /><col width="90%" />
						<tr>
							<td>&nbsp;</td>
							<td>
								<label for="number_comment_in_page">                                                            
									<?php echo JText::_('NUMBER_OF_COMMENT_IN_A_PAGE')?>
								</label>
								<br />
								<?php $arraySort = array(5,10,15,20,50,100);?>
								<select id="number_comment_in_page" name="comments[number_comment_in_page]" class="inputbox">
									<?php for($i=0;$i<count($arraySort);$i++){ ?>
										<option value="<?php echo $arraySort[$i];?>" <?php if($arraySort[$i]==$this->params->get('number_comment_in_page', 10)) echo 'selected="selected"';?>><?php echo $arraySort[$i];?></option>	
									<?php }?>
								</select> <?php echo JText::_('THE_NUMBER_OF_COMMENT_DISPLAY_IN_A_PAGE')?>
							</td>
						</tr>
					</table>
				</li>
				<li class="row-1">
					<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">
						<col width="10%" /><col width="90%" />
						<tr>
							<td>&nbsp;</td>
							<td>
								<?php $isEnableRss = $this->params->get('is_enable_rss', 1);?>
								<label for="is_enable_rss">
									<input type="checkbox" <?php if($isEnableRss) echo 'checked="checked"';?>value="1" name="comments[is_enable_rss]" id="is_enable_rss" /> <?php echo JText::_('ENABLE_RSS')?>
								</label>
								<p style="margin-bottom: 0pt;" class="info"><?php echo JText::_('ENABLE_RSS_')?></p>
							</td>
						</tr>
					</table>                    
				</li>
				<li class="row-0">
					<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">
						<col width="10%" /><col width="90%" />
						<tr>
							<td>&nbsp;</td>
							<td>
								<?php $duration_post_comment = $this->params->get('duration_post_comment', 1);?>
								<label for="duration_post_comment">					
									<?php echo JText::_('DURATION_POST_A_COMMENT')?>
								</label>
								<br/>
								<input type="text" class="inputbox" size="3" value="<?php echo $duration_post_comment;?>" onkeyup="checkValidKey(this.value,'duration_post_comment')" onkeypress="return isNumberKey(event)" name="comments[duration_post_comment]" id="duration_post_comment" /> <?php echo JText::_('SECONDS')?>
							</td>
						</tr>
					</table>
				</li>
				<li class="row-1 last_row">
					<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">
						<col width="10%" /><col width="90%" />
						<tr>
							<td>&nbsp;</td>
							<td>
								<?php $isShowCommentCount = $this->params->get('is_show_comment_count', 1); ?>
								<label for="is_show_comment_count">
									<input type="checkbox" <?php if ($isShowCommentCount) echo 'checked="checked" '; ?>value="1" name="comments[is_show_comment_count]" id="is_show_comment_count" /> <?php echo JText::_('SHOW_COMMENT_COUNT'); ?>
								</label>
								<p style="margin-bottom: 0pt;" class="info"><?php echo JText::_('SHOW_COMMENT_COUNT_'); ?></p>
							</td>
						</tr>
					</table>
				</li>
			</ul>				
		</div>
	</div>		
</div>
<div class="clr"></div>
<input type="hidden" id="hdInvalidTotalAttach" value="<?php echo JText::_("TOTAL_OF_ATTACHED_FILE_MUST_BE_INTEGER_NUMBERS_NOT_NULL_AND_GREATER_THAN_0");?>" />
<input type="hidden" id="hdInvalidSizeAttach" value="<?php echo JText::_("MAX_SIZE_FOR_AN_ATTACHED_FILE_MUST_BE_INTEGER_NUMBERS_NOT_NULL_GREATER_THAN_0_AND_LESS_THAN") . ' ' . $maxSizeUpload;?>" />
<input type="hidden" id="hdInvalidMaximumNumber" value="<?php echo JText::_("THE_MAXIMUM_NUMBER_OF_COMMENT_MUST_BE_INTEGER_NUMBERS_NOT_NULL_AND_GREATER_THAN_0");?>" />
<input type="hidden" id="hdInvalidDurationPost" value="<?php echo JText::_("DURATION_POST_A_COMMENT_MUST_BE_INTEGER_NUMBERS_NOT_NULL_AND_GREATER_THAN_0");?>" />
<input type="hidden" name="option" value="com_jacomment" />
<input type="hidden" name="view" value="configs" />
<input type="hidden" name="task" value="" />
<input type="hidden" id="hidden_error_lag_time" value="<?php echo JText::_("YOU_MUST_INPUT_LAG"); ?>" />
<input type="hidden" name="group" value="<?php echo $this->group; ?>" />
<input type="hidden" name="cid" value="<?php echo $this->cid; ?>" />
<?php echo JHTML::_( 'form.token' ); ?>	
</form>
<script type="text/javascript">
//function isenablereport(obj){
//    var display='';
//    if(!obj.checked) display='none';
//    $('div_report_extras').setStyle('display', display);
//}
//isenablereport($('is_enable_report'));

function checkSizeUpload(obj, value){	
	if(value == ""){
		$("error_max_size_attach_file").innerHTML = $("hdInvalidSizeAttach").value;		
		obj.value = maxSizeUpload;
		return;
	}		
	if(value > maxSizeUpload || value <=0){		
		if($('hidden_max_size_attach_file').value	<= maxSizeUpload)
			obj.value = $('hidden_max_size_attach_file').value;
		else
			obj.value = maxSizeUpload;
		$("error_max_size_attach_file").innerHTML = $("hdInvalidSizeAttach").value;		
		return;
	}
	$("error_max_size_attach_file").innerHTML = "";		
}

function checkValidKey(value,obj){		
	if(value == 0){
		$(obj).value = "";
	}
}

function isNumberKey(evt){
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode == 35 || charCode == 36 || charCode == 37 || charCode == 38 || charCode == 39 || charCode == 40 || charCode == 46) {
			return true;
		}
		if (charCode > 31 && (charCode < 48 || charCode > 57)) {
	      	return false;
		}

	   	return true;
}

function checkTotalAttach(obj){
	var checkInteger  = /(^\d\d*$)/;
	if(!checkInteger.test($("total_attach_file").value)){
		$("error_total_attach_file").innerHTML = $("hdInvalidTotalAttach").value;	
		
		obj.value = $('hidden_total_attach_file').value;
				
		jQuery('#ja-box-action').animate( {
			bottom :"-45px"
		}, 300);
		return false;
	}else{
		$("error_total_attach_file").innerHTML = "";
		// if($("maximum_comment_in_item").value >= 0 || checkInteger.test($("maximum_comment_in_item").value)){
			// show_bar_preview('<?php echo JText::_('PREVIEW');?>', '<?php echo JText::_('CANCEL');?>');
		// }
		return true;
	}
}

function checkMaximumNumber(obj){
	var checkInteger  = /(^\d\d*$)/;
	if(!checkInteger.test($("maximum_comment_in_item").value)){
		$("error_maximum_comment_in_item").innerHTML = $("hdInvalidMaximumNumber").value;
		$("maximum_comment_in_item").value = $("hidden_maximum_comment_in_item").value;
		jQuery('#ja-box-action').animate( {
			bottom :"-45px"
		}, 300);
		return false;
	}else{
		$("error_maximum_comment_in_item").innerHTML = "";
		// if($("total_attach_file").value >= 0 || checkInteger.test($("total_attach_file").value)){
			// show_bar_preview('<?php echo JText::_('PREVIEW');?>', '<?php echo JText::_('CANCEL');?>');
		// }
		return true;
	}
}

function isEnableUserVoting(obj){	
	if(!obj.checked){		
		$('type_user_voting_1').disabled	= true;
		$('type_user_voting_1').checked		= false;
		
		$('type_user_voting_2').disabled	= true;
		$('type_user_voting_2').checked		= false;
		
		$('type_user_voting_3').disabled	= true;
		$('type_user_voting_3').checked		= false;
		
		$('lag_user_voting').disabled		= true;
		$('lag_user_voting').value			= '';
	}else{				
		$('type_user_voting_1').disabled	= false;						
		$('type_user_voting_2').disabled	= false;
		$('type_user_voting_3').disabled	= false;
		type = $('type_user_voting').value;
		if(type == 0)
			$('type_user_voting_1').checked	= false;			
		else{
			$('type_user_voting_' + type).checked		= true;		
			if(type == 3)
				$('lag_user_voting').value = $('hd_lag_user_voting').value;
		}
	}		
}

function disableOrEnableLagUser(obj, value){		
	if(value == "1"){ 
		$('lag_user_voting').disabled = false;
		$('lag_user_voting').value = $('hd_lag_user_voting').value;
		$('lag_user_voting').focus(); 
	}else{
		$('lag_user_voting').value = ''; 
		$('lag_user_voting').disabled = true;
	}	
	$('type_user_voting').value = obj.value;
}


function saveLag(obj, value){	
	if(value == ""){
		alert($('hidden_error_lag_time').value);	
		obj.focus();	 
	}else{	
		$("hd"+obj.id).value = value;
	} 		
}

function changeStatusAttachImg(obj){
	if(obj.checked == true){
		$('div_is_attach_image').style.display = "block";
	}else{
		$('div_is_attach_image').style.display = "none";
	}			
}

function changeStatusApproveNewComment(obj){
	if(obj.checked == true){
		$('div_is_allow_approve_new_comment').style.display = "block";
		$('is_allow_approve_guest_comment').checked = true;
		$('is_allow_approve_member_comment').checked = true;
	}else{
		$('div_is_allow_approve_new_comment').style.display = "none";
		$('is_allow_approve_guest_comment').checked = false;
		$('is_allow_approve_member_comment').checked = false;
	}
}

function checkApproveStatus(obj) {
	var guestComment = $('is_allow_approve_guest_comment');
	var memberComment = $('is_allow_approve_member_comment');
	
	if (obj == guestComment) {
		// guest comment check box is clicked
		if (! obj.checked) {
			memberComment.checked = false;
		}
	}
	else {
		// member comment check box is clicked
		if (obj.checked) {
			guestComment.checked = true;
		}
	}
}
</script>