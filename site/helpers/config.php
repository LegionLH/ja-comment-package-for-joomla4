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

//get config general
global $jacconfig;
$app = Factory::getApplication();
$inputs = Factory::getApplication()->input;
$generalView = "all";
if (! isset($jacconfig['general'])) {
	$jacconfig['general'] = new JRegistry;
	$jacconfig['general']->loadString('{}');
}
if (isset($jacconfig['general'])) {
	$generalView = $jacconfig['general']->get('view', "all");
}

//get config of layout
if (! isset($jacconfig['layout'])) {
	$jacconfig['layout'] = new JRegistry;
	$jacconfig['layout']->loadString('{}');
}
$enableAvatar = $jacconfig['layout']->get('enable_avatar', 0);
$useDefaultAvatar = $jacconfig['layout']->get('use_default_avatar', 0);
$avatarSize = $jacconfig['layout']->get('avatar_size', 1);
$buttonType = $jacconfig['layout']->get('button_type', 1);
$formPosition = $jacconfig['layout']->get('form_position');
$enableSubscribeMenu = $jacconfig['layout']->get('enable_subscribe_menu', 1);
$enableSortingOptions = $jacconfig['layout']->get('enable_sorting_options', 1);
$defaultSort = $jacconfig['layout']->get('default_sort', 1);
$defaultSortType = $jacconfig['layout']->get('default_sort_type', "ASC");
$enableTimestamp = $jacconfig['layout']->get('enable_timestamp', 1);
$enableUserRepIndicator = $jacconfig['layout']->get('enable_user_rep_indicator', 1);
$footerText = $jacconfig['layout']->get('footer_text', "");
$theme = $jacconfig['layout']->get('theme', 'default');
$session = JFactory::getSession();
if ($inputs->getCmd("jacomment_theme", '')) {
	jimport('joomla.filesystem.folder');
	$themeURL = $inputs->getCmd("jacomment_theme",'');
	if (JFolder::exists('components/com_jacomment/themes/' . $themeURL) || (JFolder::exists('templates/' . $app->getTemplate() . '/html/com_jacomment/themes/' . $themeURL))) {
		$theme = $themeURL;
	}
	$session->set('jacomment_theme', $theme);
} else {
	if ($session->get('jacomment_theme', null)) {
		$theme = $session->get('jacomment_theme', $theme);
	}
}

$enableBbcode = $jacconfig['layout']->get('enable_bbcode', 1);
$enableYoutube = $jacconfig['layout']->get('enable_youtube', 1);
$enableAfterTheDeadline = $jacconfig['layout']->get('enable_after_the_deadline', 1);
if (! $enableAfterTheDeadline && !defined('JACOMMENT_PLUGIN_ATD')) {
	define('JACOMMENT_PLUGIN_ATD', true);
}
$enableSmileys = $jacconfig['layout']->get('enable_smileys', 1);
$smiley = $jacconfig['layout']->get('smiley', 'default');
$enableCharacterCounter = $jacconfig['layout']->get('enable_character_counter', 0);
$enableLocationDetection = $jacconfig['layout']->get('enable_location_detection', 0);
$commentFormPosition = $jacconfig['layout']->get('form_position', 1);

//get config of comments
if (! isset($jacconfig['comments'])) {
	$jacconfig['comments'] = new JRegistry;
	$jacconfig['comments']->loadString('{}');
}
$isEnableWebsiteField = $jacconfig['comments']->get('is_enable_website_field', 0);
$isEnableEmailSubscription = $jacconfig['comments']->get('is_enable_email_subscription', 1);
$isAllowVoting = $jacconfig['comments']->get('is_allow_voting', 1);
$isAttachImage = $jacconfig['comments']->get('is_attach_image', 0);
$attachFileType = $jacconfig['comments']->get('attach_file_type', "doc,docx,pdf,txt,zip,rar,jpg,bmp,gif,png");
$totalAttachFile = $jacconfig['comments']->get('total_attach_file', 5);
$isAllowReport = $jacconfig['comments']->get('is_allow_report', 1);
$maximumCommentInItem = $jacconfig['comments']->get('maximum_comment_in_item', 20);
$isEnableThreads = $jacconfig['comments']->get('is_enable_threads', 1);
$isEnableAutoexpanding = $jacconfig['comments']->get('is_enable_autoexpanding', 1);
$isEnableRss = $jacconfig['comments']->get('is_enable_rss', 1);

if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== false) {
	$isEnableAutoexpanding = 0;
}

//Spam Fiters
if (! isset($jacconfig['spamfilters'])) {
	$jacconfig['spamfilters'] = new JRegistry;
	$jacconfig['spamfilters']->loadString('{}');
}
$isEnableCaptcha = $jacconfig['spamfilters']->get('is_enable_captcha', 1);
$isEnableCaptchaUser = $jacconfig['spamfilters']->get('is_enable_captcha_user', 0);
$isEnableTerms = $jacconfig['spamfilters']->get('is_enable_terms', 0);
$termsOfUsage = $jacconfig['spamfilters']->get('terms_of_usage', 0);
$minLength = $jacconfig['spamfilters']->get('min_length', 0);
$maxLength = $jacconfig['spamfilters']->get('max_length', 1000);
$numberOfLinks = $jacconfig['spamfilters']->get('number_of_links', 5);

//Permissions
if (! isset($jacconfig['permissions'])) {
	$jacconfig['permissions'] = new JRegistry;
	$jacconfig['permissions']->loadString('{}');
}
$postComment = $jacconfig['permissions']->get('post', "all");

$voteComment = $jacconfig['permissions']->get('vote', "all");
$typeVote = $jacconfig['permissions']->get('type_voting', 1);

$reportComment = $jacconfig['permissions']->get('report', "all");
$totalToReportSpam = $jacconfig['permissions']->get('total_to_report_spam', 10);

$typeEditing = $jacconfig['permissions']->get('type_editing', 1);
$lagEditing = $jacconfig['permissions']->get('lag_editing', 172800);

//info of current user
$currentUserInfo = JFactory::getUser();

$isShowCaptcha = 0;

if ($currentUserInfo->guest) {
	if ($isEnableCaptcha) {
		//check to show captcha
		$isShowCaptcha = 1;
	}
} else {
	//check to show button report	
	if ($isEnableCaptcha && $isEnableCaptchaUser) {
		//check to show captcha
		$isShowCaptcha = 1;
	}
}

//check user is allow edit or delete comment
$helper = new JACommentHelpers();
$isSpecialUser = $helper->isSpecialUser();
if ($isSpecialUser) {
	$isShowCaptcha = 0;
	$isEnableTerms = 0;
}
jimport('joomla.filesystem.folder');

$inputs = Factory::getApplication()->input;

$task = $inputs->getCmd('task', null, 'default');
if ($task == 'preview') {
	// layout & plugin
	$theme = $inputs->getCmd('theme');
	$enableAvatar = $inputs->getInt('enable_avatar');
	$useDefaultAvatar = $inputs->getInt('use_default_avatar');
	$avatarSize = $inputs->getInt('avatar_size');
	$buttonType = $inputs->getInt('button_type');
	$enableCommentForm = $inputs->getInt('enable_comment_form');
	$formPosition = $inputs->getInt('form_position');
	$enableSortingOptions = $inputs->getInt('enable_sorting_options');
	$defaultSort = $inputs->getInt('default_sort');
	$defaultSortType = $inputs->getInt('default_sort_type');
	$enableTimestamp = $inputs->getInt('enable_timestamp');
	$footerText = $inputs->getString('footer_text');
	
	// comment
	$isEnableThreads = $inputs->getInt('is_enable_threads');
	$isAllowVoting = $inputs->getInt('is_allow_voting');
	$isAttachImage = $inputs->getInt('is_attach_image');
	$isEnableWebsiteField = $inputs->getInt('is_enable_website_field');
	
	$isEnableAutoexpanding = $inputs->getInt('is_enable_autoexpanding');
	$isEnableEmailSubscription = $inputs->getInt('is_enable_email_subscription');
	$isAllowReport = $inputs->getInt('is_allow_report');
	
	// spamfilter
	$isShowCaptcha = $inputs->getInt('is_enable_captcha');
	$isEnableTerms = $inputs->getInt('is_enable_terms');
}
?>