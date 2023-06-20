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
 * It is used for logs table
 * 
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */
class TableLogs extends JTable
{
	/** @var int */
	var $id = 0;
	/** @var int */
	var $userid = 0;
	/** @var int */
	var $itemid = 0;
	/** @var int */
	var $votes = null;
	/** @var int */
	var $reports = null;
	/** @var int */
	var $time_expired = null;
	/** @var varchar */
	var $remote_addr = null;
	
	/**
	 * Contructor
	 * 
	 * @param object &$db JDatabase connector object
	 * 
	 * @return void
	 */
	function __construct(&$db)
	{
		parent::__construct('#__jacomment_logs', 'id', $db);
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
		$error = array();
		if (! isset($this->id)) {
			$error[] = JText::_("ID_MUST_NOT_BE_NULL");
		}
		
		return $error;
	}
	
	/**
	 * Override load method from JTable
	 * 
	 * @param mixed $key A primary key value to load the row by, or an array of fields to match
	 * 
	 * @return JTable This table object
	 */
	function load($keys = null, $reset = true)
	{
		parent::load($key, $reset);
		return $this;
	}
}
?>