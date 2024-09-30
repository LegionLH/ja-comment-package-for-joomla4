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

/**
 * JACommentControllerUsers Controller
 *
 * @package		Joomla.Site
 * @subpackage	JAComment
 */
class JACommentControllerUsers extends JACommentController
{
	/**
	 * Display function
	 * 
	 * @return void
	 */
	function display($cachable = false, $urlparams = false)
	{
		parent::display($cachable, $urlparams);
		
		return $this;
	}
	
	/**
	 * Save e-mail notification
	 * 
	 * @return void
	 */
	function setEmailNotificationReferences()
	{
		$db = Factory::getDBO();
		$user = Factory::getUser();
		
		$inputs = Factory::getApplication()->input;
		if ($user->id) {
			$user->setParam('votedCommentUpdateNotification', $inputs->getInt("votedCommentUpdateNotification", 0));
			$user->setParam('receive', $inputs->getInt("receive", 0));
			$user->setParam('often', $inputs->getInt('often', 0));
			$user->save();
		}
		
		$object = array();
		$k = 0;
		$object[$k] = new stdClass();
		$object[$k]->id = '#jav-email-preference .jav-msg-successful';
		$object[$k]->attr = 'html';
		$object[$k]->content = JText::_('SAVE_SUCCESSFUL');
		
		$helper = new JACommentHelpers();
		echo $helper->parse_JSON_new($object);
		exit();
	}
	
	/**
	 * Login function
	 * 
	 * @return void
	 */
	function login_old()
	{
		$helper = new JACommentHelpers();
		$app = Factory::getApplication();
		// Check for request forgeries
		JSession::checkToken('request') or jexit('Invalid Token');
		
		$options = array();
		
		$inputs = Factory::getApplication()->input;
		$credentials = array();
		$credentials['username'] = $inputs->get('username', '');
		$credentials['password'] = $inputs->getString('passwd', '', 'post', JREQUEST_ALLOWRAW);
		
		$object = array();
		$k = 0;
		
		//preform the login action
		$error = $app->login($credentials, $options);
		
		if ($error) {
			$helper->displayInform(JText::_("USERNAME_OR_PASSWORD_IS_INCORRECT"), $k, $object);
		} else {
			$k = 0;
			$object[$k] = new stdClass();
			
			$object[$k]->id = '#jac-text-guest';
			$object[$k]->type = 'html';
			$object[$k]->status = 'ok';
			$object[$k]->content = 'Posting as ' . $credentials['username'] . '(<a href="' . JURI::base() . 'index.php?option=com_jacomment&view=users&task=logout_rpx">Logout<\/a>)';
			$k++;
			
			$helper->displayInform(JText::_("LOGIN_SUCCESSFULLY"), $k, $object);
			
			//
			$jquery = '';
			$arrid = explode(',', 'comment_as,other_field');
			for ($i = 0; $count = sizeof($arrid), $i < $count; $i++) {
				$jquery .= "jQuery('#" . $arrid[$i] . "').remove();";
			}
			
			$jq = "<script language='javascript' type='text/javascript'>
                        jQuery(document).ready( function() { 
                        " . $jquery . " });
                    </script>";
			$helper->showOtherField($jq, $k, $object);
			
			// ++ add by congtq 08/12/2009
			$currentUserInfo = Factory::getUser();
			$model = $this->getModel('comments');
			$items = $model->getItems(' AND userid=' . $currentUserInfo->id);
			//print_r($items);
			for ($k = 0; $count = sizeof($items), $k < $count; $k++) {
				$object[$k + 3]->id = '#edit-delete-' . $items[$k]->id;
				$object[$k + 3]->type = 'html';
				$object[$k + 3]->status = 'ok';
				$object[$k + 3]->content = '<a href="javascript:editComment(' . $items[$k]->id . ', \'' . JText::_("REPLY") . '\')" title="' . JText::_("EDIT_COMMENT") . '">' . JText::_("EDIT") . '</a>&nbsp;<a href="javascript:deleteComment(' . $items[$k]->id . '" title="' . JText::_("DELETE_COMMENT") . '" title="' . JText::_("EDIT_COMMENT") . '">' . JText::_("DELETE") . '</a>';
			
			}
			$k++;
			// -- add by congtq 08/12/2009
		}
		
		echo $helper->parse_JSON_new($object);
		exit();
	}

	/**
	 * Sign in function
	 *
	 * @return void
	 */
	function signin_old()
	{
		$app = Factory::getApplication();
		
		// Check for request forgeries
		JSession::checkToken('request') or jexit('Invalid Token');
		
		$options = array();
		
		$inputs = Factory::getApplication()->input;
		$credentials = array();
		$credentials['username'] = $inputs->get('username', '');
		$credentials['password'] = $inputs->getString('passwd', '', 'post', JREQUEST_ALLOWRAW);
		
		//preform the login action
		$error = $app->login($credentials, $options);
		
		if ($error) {
			$inputs->set('view', 'users');
			$inputs->set('layout', 'login');
			parent::display();
		
		} else {
			$document = Factory::getDocument();
			$helper = new JACommentHelpers();
			
			$jquery = '';
			
			// hide #comment_as, #other_field
			$arrid = explode(',', 'comment_as,other_field');
			for ($i = 0; $count = sizeof($arrid), $i < $count; $i++) {
				$jquery .= "jQuery('#" . $arrid[$i] . "', window.parent.document).remove();";
			}
			
			// get comment
			$currentUserInfo = Factory::getUser();
			$model = $this->getModel('comments');
			
			$isSpecialUser = $helper->isSpecialUser();
			
			$cond = '';
			// if is NOT SpecialUser then show links by UserID, else show all link Edit/Delete
			if (! $isSpecialUser) {
				$cond = ' AND userid=' . $currentUserInfo->id;
			}
			$items = $model->getItems($cond);
			
			for ($k = 0; $count = sizeof($items), $k < $count; $k++) {
				$jquery .= "jQuery('#edit-delete-" . $items[$k]->id . "', window.parent.document).html('<a href=\"javascript:editComment(\'" . $items[$k]->id . "\', \'" . JText::_("EDIT") . "\')\" title=\'" . JText::_("EDIT") . "\'>" . JText::_("EDIT") . "</a>&nbsp;<a href=\"javascript:deleteComment(\'" . $items[$k]->id . "\', \'" . JText::_("DELETE") . "\')\" title=\'" . JText::_("DELETE") . "\'>" . JText::_("DELETE") . "</a>');";
			}
			
			// show link logout and close popup
			$logout = JTEXT::_('POSTING_AS') . ' ' . $currentUserInfo->username . '(<a href="' . JURI::base() . 'index.php?option=com_jacomment&view=users&task=logout_rpx">' . JTEXT::_('LOGOUT') . '</a>)';
			$jquery .= "
                        jQuery('#jac-text-guest', window.parent.document).html('" . $logout . "');
                        jQuery('#ja-popup', window.parent.document).fadeOut('slow', function() {
                            jQuery('#ja-popup', window.parent.document).remove();
                        });            
            ";
			
			// show and hide some #id   
			$document->addScriptDeclaration("jQuery(document).ready(function(){" . $jquery . "});");
		}
	}
	
	/**
	 * Sign in function
	 * 
	 * @return void
	 */
	function signin()
	{
		$app = Factory::getApplication();
		
		// Check for request forgeries
		JSession::checkToken('request') or jexit('Invalid Token');
		
		$options = array();
		
		$credentials = array();
		$inputs = Factory::getApplication()->input;
		$credentials['username'] = $inputs->get('username', '');
		$credentials['password'] = $inputs->get('passwd', '', 'raw');
		
		//preform the login action
		$container = \Joomla\CMS\Factory::getContainer();
		$container->alias(\Joomla\Session\SessionInterface::class, 'session.web.site');
		$auth = $container->get(\Joomla\CMS\Application\SiteApplication::class);
		$error = $auth->login($credentials, $options);
		
		if (!$error) {
			$inputs->set('view', 'users');
			$inputs->set('layout', 'login');
			parent::display();
		
		} else {
			$session = Factory::getSession();
			$return = $session->get('returnLink', null);
			if (! $return) {
				$return = $inputs->getCmd('return');
			}
			
			$document = Factory::getDocument();
			$document->addScriptDeclaration("
				jQuery(document).ready(function() {
					window.parent.document.location.href = '" . $return . "';
				});
			");
		}
	}
	
	/**
	 * Log in form (for rpx)
	 * 
	 * @return void
	 */
	function login()
	{
		$currentUserInfo = Factory::getUser();
		$ses_url = "";
		if ($currentUserInfo->id) {
			if (isset($_SESSION['ses_url'])) {
				$ses_url = $_SESSION['ses_url'];
				$this->setRedirect($ses_url);
			}
		}
		$inputs = Factory::getApplication()->input;
		$inputs->set('view', 'users');
		$inputs->set('layout', 'login');
		parent::display();
	}
	
	/**
	 * Log out (for rpx)
	 * 
	 * @return void
	 */
	function logout_rpx()
	{
		// logout joomla account
		$app = Factory::getApplication();
		$app->logout();
		
		// return
		$return = $_SERVER['HTTP_REFERER'];
		$this->setRedirect($return);
	}
}
?>