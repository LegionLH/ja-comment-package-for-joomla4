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

use Joomla\CMS\Factory;

$inputs = Factory::getApplication()->input;

$viewmenu = $inputs->get('viewmenu', 1);
// if (! $viewmenu) {
	// parent::display($tpl);
// } else {
if ($viewmenu) {
	$path = str_replace(JPATH_BASE, '', dirname(__FILE__));
	$path = 'administrator' . str_replace('\\', '/', $path) . '/assets/';

	JHTML::stylesheet(JURI::root() . $path . 'style.css');
	JHTML::script(JURI::root() . $path . 'menu.js');
	
	if ($inputs->get('menuId', 0)) {
		$_SESSION['menuId'] = $inputs->get('menuId', 0);
	}
	include_once dirname(__FILE__) . DS . 'menu.class.php';
}