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
/*
 * DEVNOTE: This is the 'main' file. 
 * It's the one that will be called when we go to the JAComment component. 
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

if (! defined('DS')) {
	define('DS',DIRECTORY_SEPARATOR);
}

if (! defined('JAC_REGISTERED')) {
	JLoader::register('JACController', JPATH_COMPONENT . '/controllers/controller.php');
	JLoader::register('JACView', JPATH_COMPONENT . '/views/view.php');
	JLoader::register('JACModel', JPATH_COMPONENT . '/models/model.php');
	
	define('JAC_REGISTERED', 1);
}

jimport('joomla.utilities.date');
jimport('joomla.filesystem.folder');

JTable::addIncludePath(JPATH_SITE . DS . 'administrator' . DS . 'components' . DS . 'com_jacomment' . DS . 'tables');

//------------------------------check Component Offline-------------------------
/* Require Helper */
require_once JPATH_SITE . DS . 'components' . DS . 'com_jacomment' . DS . 'helpers' . DS . 'jahelper.php';
require_once JPATH_SITE . DS . 'components' . DS . 'com_jacomment' . DS . 'helpers' . DS . 'jacaptcha' . DS . 'jacapcha.php';

$GLOBALS['jacconfig'] = array();
JACommentHelpers::get_config_system();
global $jacconfig;

if (isset($jacconfig['general']) && $jacconfig['general']->get('is_comment_offline', 0)) {
	if (! JACommentHelpers::check_access()) {
		return;
	}
}
if (! isset($_SESSION['JAC_LAST_VISITED'])) {
	if (isset($_COOKIE['JAC_LAST_VISITED'])) {
		$_SESSION['JAC_LAST_VISITED'] = $_COOKIE['JAC_LAST_VISITED'];
	} else {
		$_SESSION['JAC_LAST_VISITED'] = strtotime(date("Y-m-d") . " -3 days");
	}
	setcookie('JAC_LAST_VISITED', time());
}

$app = Factory::getApplication();
$checkTheme = $jacconfig['layout']->get('theme', 'default');
if (!JFolder::exists('templates/' . $app->getTemplate() . '/html/com_jacomment/themes/' . $checkTheme)) {
	$jacconfig['layout']->set('theme', 'default');
}

$inputs = Factory::getApplication()->input;

if (! $inputs->getCmd('view')) {
	$inputs->set('view', 'comments');
}
$controller = $inputs->getCmd('view');
//die(var_dump($inputs));
require_once JPATH_SITE . DS . 'components' . DS . 'com_jacomment' . DS . 'controller.php';
$view = $controller;
if ($controller) {
	$path = JPATH_SITE . DS . 'components' . DS . 'com_jacomment' . DS . 'controllers' . DS . $controller . '.php';
	if (file_exists($path)) {
		include_once $path;
	} else {
		$controller = '';
	}
}

if (! defined('JACOMMENT_GLOBAL_JS')) {
	if(!version_compare(JVERSION, '3.0', 'ge')){
		JHTML::script('components/com_jacomment/libs/bootstrap/js/jquery.js');
	}
	JHTML::script('components/com_jacomment/asset/js/ja.comment.js');
	JHTML::script('components/com_jacomment/asset/js/ja.popup.js');
	
	if ($jacconfig['layout']->get('enable_character_counter', 0) == 1) {
		JHTML::script('components/com_jacomment/libs/js/jquery/jquery.counter-2.2.min.js');
	}
	
	if ($jacconfig['layout']->get('enable_location_detection', 0) == 1) {
		JHTML::script('components/com_jacomment/asset/js/ja.location.js');
	}
	JHTML::script('components/com_jacomment/libs/bootstrap/js/bootstrap.min.js');
	define('JACOMMENT_GLOBAL_JS', true);
}

// Create the controller
$classname = 'JACommentController' . ucfirst($controller);
$controller = new $classname();

$controller->_basePath = JPATH_SITE . DS . 'components' . DS . 'com_jacomment';
$controller->_path['view'][0] = JPATH_SITE . DS . 'components' . DS . 'com_jacomment' . DS . 'views' . DS;

$task = $inputs->getCmd('task', null, 'default');
//var_dump($task);
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();
?>