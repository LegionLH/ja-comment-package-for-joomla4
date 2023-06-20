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
 * It is used for reports table
 * 
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */
class TableReports extends JTable
{
	/** @var int */
	var $id = 0;
	/** @var varchar */
	var $ip = null;
	/** @var int */
	var $userid = 0;
	/** @var int */
	var $commentid = 0;
	/** @var varchar */
	var $option = null;
	/** @var tinytext */
	var $reason = null;
	/** @var tinyint */
	var $status = 0;
	
	/**
	 * Contructor
	 * 
	 * @param object &$db JDatabase connector object
	 * 
	 * @return void
	 */
	function __construct(&$db)
	{
		parent::__construct('#__jacomment_reports', 'id', $db);
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
		/** check error data */
		if (! $this->title) {
			$error[] = JText::_("TITLE_MUST_NOT_BE_NULL");
		}
		if (! isset($this->id)) {
			$error[] = JText::_("ID_MUST_NOT_BE_NULL");
		} elseif (! is_numeric($this->id)) {
			$error[] = JText::_("ID_MUST_BE_NUMBER");
		}
		
		return $error;
	}
}
?>