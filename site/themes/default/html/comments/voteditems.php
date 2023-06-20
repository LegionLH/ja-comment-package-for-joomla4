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

$items = $this->items;
global $jacconfig;
$helper = new JACommentHelpers();
?>
<!-- COMMENT CONTENT -->
<div class="comment-content wrap">
	<!-- START COMMENT LIST -->
	<div class="comment-listwrap">
<?php if($items) { $k = 0; ?>	
<ol class="comment-list comment-list-lv<?php echo $items[0]->level; ?>">	
	<?php foreach($items as $item): ?>
		<?php if($k % 2){$jacRow = "row0";}else{$jacRow = "row1";}?>		
		<!-- A ROW COMMENT -->
		<li id="jac-row-comment-<?php echo $item->id?>" class="jac-row-comment <?php echo $jacRow; ?> list-item <?php if($ischild){ if($isEnableThreads){ echo "comment-hasreply";}else{ echo "comment-notree";}}?> rank-high <?php if($k == 0) echo "jac-first ";if($ischild) echo " comment-replycontent";?>">
		<?php //set archo for comment?>
	    <a name="jacommentid:<?php echo $item->id;?>" href="#jacommentid:<?php echo $item->id;?>" id="jacommentid:<?php echo $item->id;?>" style="margin-top: -34px;" title=""></a>
		<div id="jac-content-of-comment-<?php echo $item->id?>" class="comment-contentmain comment-contentholder clearfix ja-imagesize<?php echo $avatarSize;?> <?php if($item->isSpecialUser){ echo " comment-byadmin";}else{if($item->isCurrentUser) echo " comment-byyou";}if($item->type == 0) echo " comment-ispending"; ?>">						
				<?php if($enableAvatar):?>					
	        <div class="avatar clearfix">                	
	            <?php if($item->strWebsite){ ?>
	            	<a href="<?php echo $item->strWebsite;?>">
						<?php if($item->avatar[0]){?>
						<img src="<?php echo $item->avatar[0];?>" alt="<?php echo $item->strUser;?>" style="<?php echo $item->avatar[1];?>"/>
						<?php }?>
						<?php if($item->icon != ''){ echo $item->icon; }?>
					</a>
	            <?php }else{ ?>
	            	<?php if($item->avatar[0]){?>
	            	<?php if(isset($item->userLink)):?><a href="<?php echo $item->userLink;?>" target="_blank"><?php endif;?>
						<img src="<?php echo $item->avatar[0];?>" alt="<?php echo $item->strUser;?>" style="<?php echo $item->avatar[1];?>"/>
					<?php if(isset($item->userLink)):?></a><?php endif;?>
					<?php }?>
					<?php if($item->icon != ''){ echo $item->icon;}?>
	            <?php } ?>
	        </div>
				<?php endif;?>
				<div class="comment-data clearfix">
					<div class="comment-heading clearfix">
						<?php if($item->strWebsite){ ?>
							<a href="<?php echo $item->strWebsite;?>" class="comment-user">
								<span class="comment-user"><?php echo $item->strUser; ?></span>
							</a>
						<?php
						    }else{
						?>
							<span class="comment-user"><?php echo $item->strUser; ?></span>
		              <?php } ?>
						<?php
	            if($enableTimestamp){
					echo $helper->generatTimeStamp(strtotime($item->date));
				}else{
					echo "<span class='comment-date'>". $item->date ."</span>";
				}
	          ?>
			          <div class="comment-ranking" id="jac-vote-comment-<?php echo $item->id; ?>">
			          	<span class="vote-comment-<?php echo $avatarSize;?> comment-rankingresult" id="voted-of-<?php echo $item->id;?>">(<?php echo $item->totalVote;?>) <?php echo JText::_("VOTE");?></span>
			          </div>
					</div>
					<div id="jac-text-<?php echo $item->id;?>" class="comment-text">
						<?php
							$item->comment = $helper->replaceBBCodeToHTML($item->comment);
		         			echo html_entity_decode($helper -> showComment($item->comment));
		        		?>
					</div>
					<?php
						$target_path =  JPATH_ROOT.DS."images".DS."stories".DS."ja_comment".DS.$item->id;

						$listFiles = "";
						if(is_dir($target_path))
							$listFiles  = JFolder::files($target_path);
					?>
					<?php if($listFiles){?>
					<fieldset class="fieldset legend" id="jac-attach-file-<?php echo $item->id;?>">
							<legend><?php echo JText::_("ATTACHED_FILE");?></legend>
							<div id="jac-list-attach-file-<?php echo $item->id;?>" class='jac-list-upload-title'>
								<?php
									foreach ($listFiles as $listFile) {
										$type = substr(strtolower(trim($listFile)), -3, 3);
										if($type=='ocx'){
						 					$type = "doc";
										}
										$linkOfFile = "index.php?tmpl=component&option=com_jacomment&view=comments&task=downloadfile&id=".$item->id."&filename=".$listFile;
						
										$_path = JPATH_BASE.DS."/components/com_jacomment/themes/" . $theme . "/images/". $type .".gif";
										if(file_exists($_path)) {
											$_link = JURI::root()."/components/com_jacomment/themes/" . $theme . "/images/". $type .".gif";
										}
										else {
											$_link = JURI::root().'templates/'.$app->getTemplate()."/html/com_jacomment/themes/" . $theme . "/images/". $type .".gif";
										}
										
										echo "<img src='". $_link . "' alt='". $listFile ."' /> <a href='". JRoute::_($linkOfFile) ."' title='". JText::_("DOWNLOAD_FILE") ."'>". $listFile ."</a><br />";
									}
								?>
							</div>
					</fieldset>
				<?php }?>
				</div>
			<span id="jac-badge-pending-<?php echo $item->id; ?>" class="badge-pending" <?php if($item->type != 0): ?>style="display: none;"<?php endif ?>></span>
		</div>
	</li>
	<!-- //A ROW COMMENT -->
	<?php $k++; ?>
	<?php endforeach; ?>
</ol>
<?php }//if Items is not null?>
	</div>
</div>
<!-- COMMENT CONTENT -->