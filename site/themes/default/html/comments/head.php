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

$app = Factory::getApplication();
if (! isset($theme)) $theme = "default";
$session = Factory::getSession();
$inputs = Factory::getApplication()->input;
if ($inputs->get("jacomment_theme", '')) {
	jimport('joomla.filesystem.folder');
	$themeURL = $inputs->get("jacomment_theme",'');
	if (JFolder::exists('components/com_jacomment/themes/' . $themeURL) || (JFolder::exists('templates/' . $app->getTemplate() . '/html/com_jacomment/themes/' . $themeURL))) {
		$theme = $themeURL;
	}
	$session->set('jacomment_theme', $theme);
} else {
	if ($session->get('jacomment_theme', null)) {
		$theme = $session->get('jacomment_theme', $theme);
	}
}
global $jacconfig;
$theme = $jacconfig["layout"]->get("theme", "default");
$theme = $inputs->get("jacomment_theme", $theme);
$lang = Factory::getLanguage();
$extension = 'com_jacomment';
$base_dir = JPATH_SITE;
$lang->load($extension, $base_dir, '', true);
//get css and JS befor perform ajax
if (! defined('JACOMMENT_GLOBAL_CSS')) {
	$mainframe = Factory::getApplication();
	
	//add style for japopup			      
	JHTML::stylesheet('components/com_jacomment/asset/css/ja.popup.css');
	//override template for japopup in template
	if (file_exists(JPATH_BASE . DS . 'templates/' . $mainframe->getTemplate() . '/css/ja.popup.css')) {
		JHTML::stylesheet('templates/' . $mainframe->getTemplate() . '/css/ja.popup.css');
	}
	
	//add style for all componennt
	JHTML::stylesheet('components/com_jacomment/asset/css/ja.comment.css');
	//override for all component
	if (file_exists(JPATH_BASE . DS . 'templates/' . $mainframe->getTemplate() . '/css/ja.comment.css')) {
		JHTML::stylesheet('templates/' . $mainframe->getTemplate() . '/css/ja.comment.css');
	}
	
	//add style only IE for all component
	JHTML::stylesheet('components/com_jacomment/asset/css/ja.ie.php');
	if (file_exists(JPATH_BASE . DS . 'templates/' . $mainframe->getTemplate() . '/css/ja.ie.php')) {
		JHTML::stylesheet('templates/' . $mainframe->getTemplate() . '/css/ja.ie.php');
	}
	
	//add style of template for component		
	if (file_exists('components/com_jacomment/themes/' . $theme . '/css/style.css')) {
		JHTML::stylesheet('components/com_jacomment/themes/' . $theme . '/css/style.css');
	}
	if (file_exists(JPATH_BASE . DS . 'templates' . DS . $mainframe->getTemplate() . DS . 'html' . DS . "com_jacomment" . DS . "themes" . DS . $theme . DS . "css" . DS . "style.css")) {
		JHTML::stylesheet('templates/' . $mainframe->getTemplate() . '/html/com_jacomment/themes/' . $theme . '/css/style.css');
	}
	
	if (file_exists(JPATH_BASE . DS . 'components/com_jacomment/themes/' . $theme . '/css/style.ie.css')) {
		JHTML::stylesheet('components/com_jacomment/themes/' . $theme . '/css/style_ie.css');
	}
	if (file_exists(JPATH_BASE . DS . 'templates' . DS . $mainframe->getTemplate() . DS . 'html' . DS . "com_jacomment" . DS . "themes" . DS . $theme . DS . "css" . DS . "style.ie.css")) {
		JHTML::stylesheet('templates/' . $mainframe->getTemplate() . '/html/com_jacomment/themes/' . $theme . '/css/style.ie.css');
	}
	//override for all component
	if (file_exists(JPATH_BASE . DS . 'templates/' . $mainframe->getTemplate() . '/css/ja.comment.css')) {
		JHTML::stylesheet('templates/' . $mainframe->getTemplate() . '/css/ja.comment.css');
	}
	
	if ($lang->isRTL()) {
		if (file_exists(JPATH_BASE . DS . 'components/com_jacomment/asset/css/ja.popup_rtl.css')) {
			JHTML::stylesheet('components/com_jacomment/asset/css/ja.popup_rtl.css');
		}
		if (file_exists(JPATH_BASE . DS . 'templates/' . $mainframe->getTemplate() . '/css/ja.popup_rtl.css')) {
			JHTML::stylesheet('templates/' . $mainframe->getTemplate() . '/css/ja.popup_rtl.css');
		}
		
		JHTML::stylesheet('components/com_jacomment/asset/css/ja.comment_rtl.css');
		if (file_exists(JPATH_BASE . DS . 'templates/' . $mainframe->getTemplate() . '/css/ja.comment_rtl.css')) {
			JHTML::stylesheet('templates/' . $mainframe->getTemplate() . '/css/ja.comment_rtl.css');
		}
		
		//add style only IE for all component
		if (file_exists(JPATH_BASE . DS . 'components/com_jacomment/asset/css/ja.ie_rtl.php')) {
			JHTML::stylesheet('components/com_jacomment/asset/css/ja.ie.php');
		}
		if (file_exists(JPATH_BASE . DS . 'templates/' . $mainframe->getTemplate() . '/css/ja.ie_rtl.php')) {
			JHTML::stylesheet('templates/' . $mainframe->getTemplate() . '/css/ja.ie_rtl.php');
		}
		
		if (file_exists(JPATH_BASE . DS . 'components/com_jacomment/themes/' . $theme . '/css/style_rtl.css')) {
			JHTML::stylesheet('components/com_jacomment/themes/' . $theme . '/css/style_rtl.css');
		}
		if (file_exists(JPATH_BASE . DS . 'templates' . DS . $mainframe->getTemplate() . DS . 'html' . DS . "com_jacomment" . DS . "themes" . DS . $theme . DS . "css" . DS . "style_rtl.css")) {
			JHTML::stylesheet('templates/' . $mainframe->getTemplate() . '/html/com_jacomment/themes/' . $theme . '/css/style_rtl.css');
		}
		
		if (file_exists(JPATH_BASE . DS . 'components/com_jacomment/themes/' . $theme . '/css/style.ie_rtl.css')) {
			JHTML::stylesheet('components/com_jacomment/themes/' . $theme . '/css/style_ie_rtl.css');
		}
		if (file_exists(JPATH_BASE . DS . 'templates' . DS . $mainframe->getTemplate() . DS . 'html' . DS . "com_jacomment" . DS . "themes" . DS . $theme . DS . "css" . DS . "style.ie_rtl.css")) {
			JHTML::stylesheet('templates/' . $mainframe->getTemplate() . '/html/com_jacomment/themes/' . $theme . '/css/style.ie_rtl.css');
		}
	}
	
	if (file_exists(JPATH_BASE . DS . 'templates/' . $mainframe->getTemplate() . '/css/ja.comment.css')) {
		JHTML::stylesheet('templates/' . $mainframe->getTemplate() . '/css/ja.comment.css');
	}
	
	// add Bootstrap style
	JHTML::stylesheet('components/com_jacomment/libs/bootstrap/css/bootstrap.css');
	
	define('JACOMMENT_GLOBAL_CSS', true);
}
if ($enableSmileys && ! defined("JACOMMENT_GLOBAL_CSS_SMILEY")) {
	$style = '
		/* This is dynamic style for smiley */
        #jac-wrapper .plugin_embed .smileys li,.jac-mod_content .smileys li{
            display: inline;
            float: left;
            height:20px;
            width:20px;
            margin:0 1px 1px 0 !important;
            border:none;
            padding:0
        }
        #jac-wrapper .plugin_embed .smileys .smiley,.jac-mod_content .smileys .smiley{
            background: url(' . JURI::base() . 'components/com_jacomment/asset/images/smileys/' . $smiley . '/smileys_bg.png) no-repeat;
            display:block;
            height:20px;
            width:20px;
        }
        #jac-wrapper .plugin_embed .smileys .smiley:hover,.jac-mod_content .smileys .smiley:hover{
            background:#fff;
        }
        #jac-wrapper .plugin_embed .smileys .smiley span, .jac-mod_content .smileys .smiley span{
            background: url(' . JURI::base() . 'components/com_jacomment/asset/images/smileys/' . $smiley . '/smileys.png) no-repeat;
            display: inline;
            float: left;
            height:12px;
            width:12px;
            margin:0px;
        }
        #jac-wrapper .plugin_embed .smileys .smiley span span, .jac-mod_content .smileys .smiley span span{
            display: none;
        } 
        #jac-wrapper .comment-text .smiley {
            font-family:inherit;
			font-size:100%;
			font-style:inherit;
			font-weight:inherit;
			text-align:justify;
        }
        #jac-wrapper .comment-text .smiley span, .jac-mod_content .smiley span{
            background: url(' . JURI::base() . 'components/com_jacomment/asset/images/smileys/' . $smiley . '/smileys.png) no-repeat scroll 0 0 transparent;
			display:inline;
			float:left;
			height:12px;
			margin:0px;
			width:12px;
        }
        .comment-text .smiley span span,.jac-mod_content .smiley span span{
            display:none;
        }
	';
	$doc = Factory::getDocument();
	$doc->addStyleDeclaration($style);
}
?>
<?php
if (! defined('JACOMMENT_PLUGIN_ATD')) {
	JHTML::stylesheet('components/com_jacomment/asset/css/atd.css');
	JHTML::script('components/com_jacomment/libs/js/atd/jquery.atd.js');
	JHTML::script('components/com_jacomment/libs/js/atd/csshttprequest.js');
	JHTML::script('components/com_jacomment/libs/js/atd/atd.js');
	define('JACOMMENT_PLUGIN_ATD', true);
}
$isCommentJavoice = $jacconfig["general"]->get("is_comment_javoice", 0);
$contentoption = $inputs->getCmd('option');
$reply_comment = JText::_("REPLY_COMMENT");
if($isCommentJavoice && trim($contentoption)=='com_javoice'){
	$reply_comment = JText::_("JAVOICE_COMMENT");
}
?>
<script type="text/javascript">
//<![CDATA[	
	jQuery(document).ready(function($){	
		jac_init();		
	});
	
	var JACommentConfig = {
		jac_base_url 			: '<?php echo JRoute::_("index.php?option=com_jacomment"); ?>',
		siteurl 				: '<?php echo JRoute::_("index.php?tmpl=component&option=com_jacomment&view=comments"); ?>',
		minLengthComment 		: '<?php echo $minLength; ?>',
		errorMinLength 			: '<?php echo JText::_("YOUR_COMMENT_IS_TOO_SHORT", true); ?>',
		maxLengthComment 		: '<?php echo $maxLength; ?>',
		errorMaxLength 			: '<?php echo JText::_("YOUR_COMMENT_IS_TOO_LONG", true); ?>',
		isEnableAutoexpanding  	: '<?php echo $isEnableAutoexpanding; ?>',
		dateASC					: '<?php echo JText::_("LATEST_COMMENT_ON_TOP", true); ?>',
		dateDESC				: '<?php echo JText::_("LATEST_COMMENT_IN_BOTTOM", true); ?>',
		votedASC				: '<?php echo addslashes(JText::_("MOST_VOTED_ON_TOP", true)); ?>',
		votedDESC				: '<?php echo addslashes(JText::_("MOST_VOTED_IN_BOTTOM", true)); ?>',
		strLogin				: '<?php echo JText::_("LOGIN_NOW", true); ?>',
		isEnableBBCode			: '<?php echo $enableBbcode; ?>',
		isEnableCharacterCounter : '<?php echo $enableCharacterCounter; ?>',
		isEnableLocationDetection : '<?php echo $enableLocationDetection; ?>',
		commentFormPosition 	: '<?php echo $commentFormPosition; ?>',
		hdCurrentComment		: 0,
<?php if (isset($lists['contentoption'])): ?>
		contentoption			: '<?php echo $lists['contentoption']; ?>',
		contentid				: '<?php echo $lists['contentid']; ?>',
		commenttype				: '<?php echo $lists['commenttype']; ?>',
		jacomentUrl				: '<?php echo $lists['jacomentUrl']; ?>',
		contenttitle			: '<?php echo $lists['contenttitle']; ?>',
<?php elseif (isset($this->lists['contentoption'])): ?>
		contentoption			: '<?php echo $this->lists['contentoption']; ?>',
		contentid				: '<?php echo $this->lists['contentid']; ?>',
		commenttype				: '<?php echo $this->lists['commenttype']; ?>',
<?php endif; ?>
		hidInputComment			: '<?php echo JText::_("YOU_MUST_INPUT_COMMENT", true); ?>',
		hidInputWordInComment	: '<?php echo JText::_("THE_WORDS_ARE_TOO_LONG_YOU_SHOULD_ADD_MORE_SPACES_BETWEEN_THEM", true); ?>',
		hidEndEditText			: '<?php echo JText::_("PLEASE_EXIT_SPELL_CHECK_BEFORE_SUBMITTING_COMMENT", true); ?>',
		hidInputName			: '<?php echo JText::_("YOU_MUST_INPUT_NAME", true); ?>',
		hidInputEmail			: '<?php echo JText::_("YOU_MUST_INPUT_EMAIL", true); ?>',
		hidValidEmail			: '<?php echo JText::_("YOUR_EMAIL_IS_INVALID", true); ?>',
		hidAgreeToAbide			: '<?php echo JText::_("YOU_MUST_AGREE_TO_ABIDE_BY_THE_WEBSITE_RULES", true); ?>',
		hidInputCaptcha			: '<?php echo JText::_("YOU_MUST_INPUT_TEXT_OF_CAPTCHA", true); ?>',
		textQuoting			    : '<?php echo JText::_("QUOTING", true); ?>',
		textQuote			    : '<?php echo JText::_("QUOTE", true); ?>',
		textPosting			    : '<?php echo JText::_("POSTING", true); ?>',
		textReply			    : '<?php echo $reply_comment; ?>',
		textCheckSpelling		: '<?php echo JText::_("NO_WRITING_ERRORS", true); ?>',
		mesExpandForm			: '<?php echo "(+) " . JText::_("CLICK_TO_EXPAND", true); ?>',
		mesCollapseForm			: '<?php echo "(-) " . JText::_("CLICK_TO_COLLAPSE", true); ?>',
		theme					: '<?php echo $theme; ?>',
		txtCopiedDecode			: '<?php echo JText::_("COPIED_DCODE", true); ?>'
	};																	
//]]>
</script>
<?php if ($isAttachImage):
	$strTypeFile = JText::_("SUPPORT_FILE_TYPE", true) . ": " . $attachFileType . " " . JText::_("ONLY", true);
	$arrTypeFile = explode(",", $attachFileType);
	$strListFile = "";
	if ($arrTypeFile) {
		foreach ($arrTypeFile as $type) {
			$strListFile .= "'$type',";
		}
		$strListFile .= '0000000';
	}
	?>
<script type="text/javascript">			
		JACommentConfig.v_array_type 	  = [ <?php echo $strListFile; ?> ];
		JACommentConfig.error_type_file   = "<?php echo $strTypeFile; ?>";
		JACommentConfig.total_attach_file =	"<?php echo $totalAttachFile; ?>";
		JACommentConfig.error_name_file   = "<?php echo JText::_("FILE_NAME_IS_TOO_LONG"); ?>";
	</script>
<script type="text/javascript"
	src="/components/com_jacomment/asset/js/ja.upload.js"></script>
<!-- src="#" -->
<iframe id="upload_target" name="upload_target" 
	style="width: 0; height: 0; border: 0px solid #fff;"></iframe>
<?php endif; ?>
<?php

if ($isEnableAutoexpanding) {
	?><script type="text/javascript"
	src="/components/com_jacomment/libs/js/jquery/jquery.autoresize.js"></script><?php
}
?>
<?php

if ($enableBbcode) {
	?>
<script type="text/javascript"
	src="/components/com_jacomment/libs/js/dcode/dcodr.js"></script>
<script type="text/javascript"
	src="/components/com_jacomment/libs/js/dcode/dcode.js"></script>
<?php
}
?>	  
<?php
if ($enableYoutube) {
	?>
<script language="javascript" type="text/javascript">
	function open_youtube(id){
		jacCreatForm('open_youtube',id,400,200,0,0,'<?php echo JText::_("EMBED_A_YOUTUBE_VIDEO", true); ?>',0,'<?php echo JText::_("EMBED_VIDEO", true); ?>');
	}
</script>
<?php
}
?>