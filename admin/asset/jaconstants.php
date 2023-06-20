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
defined('_JEXEC') or die('Retricted Access');

/**
 * JA Comment constants
 *
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */
class jacommentConstant
{
	/**
	 * get_Variable_Email function
	 * 
	 * @return array Variable value
	 */
	function get_Variable_Email()
	{
		$variable = array();
		$variable[0]->value = '[USER_NAME]';
		$variable[0]->text = 'USER_NAME - User\'s name';
		$variable[1]->value = '[USER_EMAIL]';
		$variable[1]->text = 'USER_EMAIL - User\' email';
		$variable[2]->value = '[ADMIN_NAME]';
		$variable[2]->text = 'ADMIN_NAME - Administrator\'s name';
		$variable[3]->value = '[ADMIN_EMAIL]';
		$variable[3]->text = 'ADMIN_EMAIL - Administrator\'s email';
		$variable[4]->value = '[CONTACT_EMAIL]';
		$variable[4]->text = 'CONTACT_EMAIL - Email for user contacting';
		$variable[5]->value = '[SITE_URL]';
		$variable[5]->text = 'SITE_URL - Website\'s URL';
		$variable[6]->value = '[SITE_NAME]';
		$variable[6]->text = 'SITE_NAME - Site name';
		return $variable;
	}
	
	
	/**
	 * getEmailConfig function
	 * 
	 * @return array Email configuration
	 */
	function getEmailConfig()
	{
		global $jacconfig;
		$app = JFactory::getApplication();
		$emailConfig = array();
		
		$app = JFactory::getApplication('administrator');
		
		$emailConfig['site_contact_email'] = 'jooms@joomsolutions.com';
		$emailConfig['site_title'] = $jacconfig['emails']->get('sitname');
		$emailConfig['root_url'] = $app->getCfg('live_site');
		$emailConfig['fromemail'] = $jacconfig['emails']->get('fromemail');
		$emailConfig['fromname'] = $jacconfig['emails']->get('fromname');
		$emailConfig['admin_email'] = $app->getCfg('mailfrom');
		$emailConfig['admin_name'] = $app->getCfg('fromname');
		return $emailConfig;
	}
	
	
	/**
	 * get_Email_Group function
	 * 
	 * @return array Email group text
	 */
	static function get_Email_Group()
	{
		$result = array(JText::_('JA_COMMENT') . ' - ' . JText::_("COMMENT"), JText::_('JA_COMMENT') . ' - ' . JText::_("HEADER_FOOTER"));
		return $result;
	}
}
?>
