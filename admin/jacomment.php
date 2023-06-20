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

if (! defined('DS')) {
	define('DS',DIRECTORY_SEPARATOR);
}
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

jimport('joomla.filesystem.file');
if(JFile::exists(JPATH_COMPONENT."/installer/update/update.php")){
	require_once JPATH_COMPONENT."/installer/update/update.php";
}

if (! defined('JAC_REGISTERED')) {
	JLoader::register('JACController', JPATH_COMPONENT_ADMINISTRATOR . '/controllers/controller.php');
	JLoader::register('JACView', JPATH_COMPONENT_ADMINISTRATOR . '/views/view.php');
	JLoader::register('JACModel', JPATH_COMPONENT_ADMINISTRATOR . '/models/model.php');
	
	define('JAC_REGISTERED', 1);
}

// Require Helper
require_once JPATH_SITE . DS . 'components' . DS . 'com_jacomment' . DS . 'helpers' . DS . 'jahelper.php';
$GLOBALS['jacconfig'] = array();
JACommentHelpers::get_config_system();

require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'asset' . DS . 'jaconstants.php';
// Require the base controller
require_once JPATH_COMPONENT . DS . 'controller.php';

if (version_compare(JVERSION, '3.0', 'ge')) {
	require_once JPATH_COMPONENT_SITE . DS . 'libs' . DS . 'simplexml.php';
}

//Require the submenu for component
require_once JPATH_COMPONENT . DS . 'views' . DS . 'jaview' . DS . 'view.html.php';

$javersion = new JVersion();

//JHtml::_('behavior.framework', true);
if (version_compare(JVERSION, '3.0', 'ge')) {
	JHtml::_('jquery.framework');
}
if(!defined('JACOMMENT_GLOBAL_FRONT_END_CSS')){
		global $jacconfig,$mainframe;
		$mainframe = Factory::getApplication();
		$theme 	= $jacconfig['layout']->get('theme', 'default' );
		
		if(file_exists(JPATH_ROOT.DS.'components/com_jacomment/themes/'.$theme.'/css/style.css')){		
			JHTML::stylesheet(JURI::root().'components/com_jacomment/themes/'.$theme.'/css/style.css');
		}
		if(file_exists(JPATH_ROOT.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS."com_jacomment".DS."themes".DS. $theme .DS."css".DS."style.css")){		
			JHTML::stylesheet(JURI::root().'templates/'.$mainframe->getTemplate().'/html/com_jacomment/themes/'.$theme.'/css/style.css');	 
		}
			
		if(file_exists(JPATH_ROOT.DS.'components/com_jacomment/themes/'.$theme.'/css/style.ie.css')){
		    JHTML::stylesheet(JURI::root().'components/com_jacomment/themes/'.$theme.'/css/style_ie.css');
		}	
		if(file_exists(JPATH_ROOT.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS."com_jacomment".DS."themes".DS. $theme .DS."css".DS."style.ie.css")){		
			JHTML::stylesheet(JURI::root().'templates/'.$mainframe->getTemplate().'/html/com_jacomment/themes/'.$theme.'/css/style.ie.css');	 
		}
		 define('JACOMMENT_GLOBAL_FRONT_END_CSS', true); 
}
if (! defined('JACOMMENT_GLOBAL_SKIN')) {
	JHTML::stylesheet(JURI::root() . 'administrator/components/com_jacomment/asset/css/ja.comment.css');
	JHTML::stylesheet(JURI::root() . 'components/com_jacomment/asset/css/ja.popup.css');
	
	JHTML::script(JURI::root() . 'administrator/components/com_jacomment/asset/js/ja.comment.jbk.js');
	JHTML::script(JURI::root() . 'components/com_jacomment/libs/bootstrap/js/jquery.js');
	JHTML::script(JURI::root() . 'administrator/components/com_jacomment/asset/js/ja.comment.noconflict.js');
	JHTML::script(JURI::root() . 'administrator/components/com_jacomment/asset/js/ja.comment.js');
	JHTML::script(JURI::root() . 'administrator/components/com_jacomment/asset/js/jquery.savecomment.js');
	JHTML::script(JURI::root() . 'administrator/components/com_jacomment/asset/js/ja.popup.js');
	
	define('JACOMMENT_GLOBAL_SKIN', true);
}

if (! defined('JACOMMENT_PLUGIN_ATD')) {
	JHTML::stylesheet(JURI::root() . 'components/com_jacomment/asset/css/atd.css');
	JHTML::script(JURI::root() . 'components/com_jacomment/libs/js/atd/jquery.atd.js');
	JHTML::script(JURI::root() . 'components/com_jacomment/libs/js/atd/csshttprequest.js');
	JHTML::script(JURI::root() . 'components/com_jacomment/libs/js/atd/atd.js');
	
	define('JACOMMENT_PLUGIN_ATD', true);
}

jimport('joomla.application.component.model');
JACModel::addIncludePath(JPATH_ROOT . DS . 'components' . DS . 'com_jacomment' . DS . 'models');

$input = Factory::getApplication()->input;

if (! $input->getCmd('view')) {
	$input->set('view', 'comments');
}

if ($controller = $input->getCmd('view')) {
	$path = JPATH_COMPONENT . DS . 'controllers' . DS . $controller . '.php';
	if (file_exists($path)) {
		include_once $path;
	} else {
		$controller = '';
	}
}

// Create the controller
$classname = 'JACommentController' . ucfirst($controller);
$controller = new $classname();

$task = $input->getCmd('task', null, 'default');

// Perform the Request task
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();
?>