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
defined('_JEXEC') or die('Restricted access');

/**
 * It is used for configs table
 * 
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */
class Tableconfigs extends JTable
{
	/** @var int */
	var $id = null;
	/** @var varchar */
	var $group = '';
	/** @var text */
	var $data = '';
	
	/**
	 * Contructor
	 * 
	 * @param object &$db JDatabase connector object
	 * 
	 * @return void
	 */
	function __construct(&$db)
	{
		parent::__construct('#__jacomment_configs', 'id', $db);
	}
	
	/**
	 * Override bind method from JTable
	 * 
	 * @param array $array  Array to bind to the JTable instance
	 * @param mixed $ignore An optional array or space separated list of properties to ignore while binding
	 * 
	 * @return boolean  True on success
	 */
	function bind($array, $ignore = '')
	{
		if (key_exists('params', $array) && is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}
		return parent::bind($array, $ignore);
	}
	
	/**
	 * Override bind method from JTable
	 * 
	 * @return array Error messages
	 */
	function check()
	{
		return true;
	}
}
?>