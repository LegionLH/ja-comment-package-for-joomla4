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
 * It is used for comment items table
 * 
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */
class TableComments extends JTable
{
	/** @var int */
	var $id = 0;
	/** @var int */
	var $parentid = null;
	/** @var int */
	var $contentid = 0;
	/** @var varchar */
	var $contenttitle = '';
	/** @var varchar */
	var $ip = null;
	/** @var varchar */
	var $name = null;
	/** @var varchar */
	var $comment = '';
	/** @var datetime */
	var $date = null;
	/** @var int */
	var $ordering = 0;
	/** @var tinyint */
	var $published = 1;
	/** @var tinyint */
	var $locked = 0;
	/** @var varchar */
	var $email = null;
	/** @var varchar */
	var $website = null;
	/** @var int */
	var $star = 0;
	/** @var int */
	var $userid = null;
	/** @var mediumtext */
	var $usertype = '';
	/** @var varchar */
	var $option = null;
	/** @var smallint */
	var $voted = null;
	/** @var smallint */
	var $report = null;
	/** @var tinyint */
	var $subscription_type = null;
	/** @var varchar */
	var $referer = '';
	/** @var varchar */
	var $source = '';
	/** @var varchar */
	var $date_active = null;
	/** @var int */
	var $children = null;
	/** @var int */
	var $active_children = null;
	/** @var int */
	var $p0 = null;
	/*
    0: Unapproved
    1: Approved
    2: Spam
    */
	/** @var tinyint */
	var $type = null;
	
	/**
	 * Contructor
	 * 
	 * @param object &$db JDatabase connector object
	 * 
	 * @return void
	 */
	function __construct(&$db)
	{
		parent::__construct('#__jacomment_items', 'id', $db);
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
		if (! isset($this->id)) {
			$error[] = JText::_("ID_MUST_NOT_BE_NULL");
		} elseif (! is_numeric($this->id)) {
			$error[] = JText::_("ID_MUST_BE_NUMBER");
		}
		if (! isset($this->comment)) {
			$error[] = JText::_("COMMENT_MUST_NOT_BE_NULL");
		}
		
		return $error;
	}
}
?>