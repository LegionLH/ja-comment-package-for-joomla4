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
$helper = new JACommentHelpers();

$isEnableConversationBar = 1;//$jacconfig["general"]->get("enable_conversation_bar", 0);
$avatarType = $jacconfig["layout"]->get("type_avatar", 0);

$avatarSize = $jacconfig["layout"]->get("avatar_size", 1);
if($avatarSize == 1){
	$size = 'height:18px; width:18px;';
}else if($avatarSize == 2){
    $size = 'height:26px; width:26px;';
}else if($avatarSize == 3){
    $size = 'height:42px; width:42px;';
}

if ($isEnableConversationBar) {
	$authors = $this->authors;
?>
<div class="conversation-bar">
	<h3><?php echo JText::_( 'CONVERSATION_BAR_TITLE' ); ?></h3>
	<ul>
		<?php
		if ($authors) {
			if ($enableAvatar) {
				// Render registered user
				foreach ($authors->registered as $author) {
					$authorAvatar = $helper->getAvatar($author, 0, $avatarSize, $avatarType, $helper->getAuthorEmail($author)); 
				?>
				<li>
					<div class="conversation-avatar" title="<?php echo $helper->getAuthorName($author); ?>"><img src="<?php echo $authorAvatar[0]; ?>" style="<?php echo $size; ?>" /></div>
				</li>
				<?php
				}
				// Render guests
				foreach ($authors->guest as $author) {
					$authorAvatar = $helper->getAvatar(0, 0, $avatarSize, $avatarType, $helper->getAuthorEmail(0, $author));
				?>
				<li>
					<div class="conversation-avatar" title="<?php echo $author; ?>"><img src="<?php echo $authorAvatar[0]; ?>" style="<?php echo $size; ?>" /></div>
				</li>
			<?php
				}
			} else {
				// Render registered user
				foreach ($authors->registered as $author) {
			?>
				<li><?php echo $helper->getAuthorName($author); ?></li>
				<?php
				}
				// Render guests
				foreach ($authors->guest as $author) {
				?>
				<li><?php echo $author; ?></li>
				<?php
				}
			}
		}
		?>
	</ul>
</div>
<br class="clear" />
<?php } ?>