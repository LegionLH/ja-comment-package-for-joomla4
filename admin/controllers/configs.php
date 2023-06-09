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
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;

/**
 * This controller is used for JAConfiguration feature of the component
 *
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */
class JACommentControllerConfigs extends JACommentController
{
	/**
	 * Constructor
	 * 
	 * @param array $jbconfig Array of configuration settings
	 * 
	 * @return void
	 */
	function __construct($jbconfig = array())
	{
		parent::__construct($jbconfig);
		$this->registerTask('loadlanguage', 'loadLanguage');
		$this->registerTask('savelanguage', 'saveLanguage');
		$this->registerTask('saveblockblack', 'saveBlockBlack');
		$this->registerTask('removeblockblack', 'removeBlockBlack');
	}
	
	/**
	 * Display current configs of the component to administrator
	 * 
	 * @return void
	 */
	function display($cachable = false, $urlparams = false)
	{
		parent::display($cachable, $urlparams);
		
		return $this;
	}
	
	/**
	 * Edit CSS layout
	 * 
	 * @return void
	 */
	function editcss()
	{
		$inputs = Factory::getApplication()->input;
		$inputs->set('edit', true);
		$inputs->set('layout', 'editcss');
		parent::display();
	}
	
	/**
	 * Save configuration record
	 * 
	 * @return void
	 */
	function save()
	{
		$inputs = Factory::getApplication()->input;
		$option = $inputs->getCmd('option');
		$model = $this->getModel('configs');
		$item = $model->getItems();
		
		$data = $item->data;
		$params = new JRegistry;
		$params->loadString($data);
		
		JSession::checkToken() or jexit('Invalid Token');
		$task = $this->getTask();
		$cache = Factory::getCache($option);
		$cache->clean();
		
		$group = $inputs->getCmd('group', 'general');
		if ($group == '') {
			$group = 'general';
		}
		$paramsField = $inputs->get($group, null, 'array');
		
		// Build parameter INI string
		$access = $inputs->get('access', null);
		if (isset($access)) {
			$paramsField['access'] = $access;
		}
		
		// ++ add by congtq
		$category = $inputs->get('category', null);
		if (is_array($category) && ! empty($category)) {
			$categories = implode(", ", $category);
			$paramsField['category'] = $categories;
		}
		// --
		
		// ++ added by congtq 19/10/2009
		// general tab
		if ($group == 'general') {
			if (! $paramsField['is_comment_offline']) {
				$paramsField['is_comment_offline'] = 0;
			}
			if (! $paramsField['is_close_thread']) {
				$paramsField['is_close_thread'] = 0;
			}
			if (! $paramsField['is_comment_javoice']) {
				$paramsField['is_comment_javoice'] = 0;
			}
			if (! $paramsField['comment_javoice_level']) {
				$paramsField['comment_javoice_level'] = 1;
			}
			if (! $paramsField['is_notify_admin']) {
				$paramsField['is_notify_admin'] = 0;
			}
			if (! $paramsField['is_notify_author']) {
				$paramsField['is_notify_author'] = 0;
			}
			if (! $paramsField['is_notify_content_author']) {
				$paramsField['is_notify_content_author'] = 0;
			}
			if (! $paramsField['is_enabled_email']) {
				$paramsField['is_enabled_email'] = 0;
			}
			if (! $paramsField['mail_view_only']) {
				$paramsField['mail_view_only'] = 0;
			}
			if (! $paramsField['is_use_ja_login_form']) {
				$paramsField['is_use_ja_login_form'] = 0;
			}
		}
		// comment tab
		if ($group == 'comments') {
			if (! $paramsField['is_enable_threads']) {
				$paramsField['is_enable_threads'] = 0;
			}
			if (! $paramsField['is_show_child_comment']) {
				$paramsField['is_show_child_comment'] = 0;
			}
			
			//BEGIN - ADD by NghiaTD - change config of vote
			if (@! $paramsField['is_allow_voting']) {
				$paramsField['is_allow_voting'] = 0;
			}
			//END - ADD by NghiaTD	- change config of vote			
			$paramsField['attach_file_type'] = implode(",", $paramsField['attach_file_type']);
			
			if (! $paramsField['is_attach_image']) {
				$paramsField['is_attach_image'] = 0;
			}
			if (! $paramsField['is_enable_website_field']) {
				$paramsField['is_enable_website_field'] = 0;
			}
			if (! $paramsField['is_enable_autoexpanding']) {
				$paramsField['is_enable_autoexpanding'] = 0;
			}
			if (! $paramsField['is_enable_email_subscription']) {
				$paramsField['is_enable_email_subscription'] = 0;
			}
			//if(!$paramsField['is_enable_report']) $paramsField['is_enable_report']=0;
			//BEGIN - ADD by NghiaTD - change config of vote
			if (! $paramsField['is_allow_report']) {
				$paramsField['is_allow_report'] = 0;
			}
			//END - ADD by NghiaTD	- change config of vote
			if (! $paramsField['is_allow_approve_new_comment'] 
				|| (! $paramsField['is_allow_approve_guest_comment'] && ! $paramsField['is_allow_approve_member_comment'])
				) {
				$paramsField['is_allow_approve_new_comment'] = 0;
			}
			if (! $paramsField['is_allow_approve_guest_comment']) {
				$paramsField['is_allow_approve_guest_comment'] = 0;
			}
			if (! $paramsField['is_allow_approve_member_comment']) {
				$paramsField['is_allow_approve_member_comment'] = 0;
			}
			if (! $paramsField['display_user_info']) {
				$paramsField['display_user_info'] = 'fullname';
			}
			if (! $paramsField['is_enable_rss']) {
				$paramsField['is_enable_rss'] = 0;
			}
			if (! $paramsField['is_show_comment_count']) {
				$paramsField['is_show_comment_count'] = 0;
			}
		}
		// spamfilters tab
		if ($group == 'spamfilters') {
			if (! $paramsField['is_enable_captcha']) {
				$paramsField['is_enable_captcha'] = 0;
			}
			if (! $paramsField['is_enable_captcha_user']) {
				$paramsField['is_enable_captcha_user'] = 0;
			}
			if (! $paramsField['is_use_akismet']) {
				$paramsField['is_use_akismet'] = 0;
			}
			if (! $paramsField['is_enable_terms']) {
				$paramsField['is_enable_terms'] = 0;
			}
			if (! $paramsField['is_nofollow']) {
				$paramsField['is_nofollow'] = 0;
			}
		}
		//print_r($paramsField);echo "<br/>";
		// layout tab
		if ($group == 'layout') {
			if (! $paramsField['enable_avatar']) {
				$paramsField['enable_avatar'] = 0;
			}
			if (! $paramsField['use_default_avatar']) {
				$paramsField['use_default_avatar'] = 0;
			}
			if (! $paramsField['enable_login_button']) {
				$paramsField['enable_login_button'] = 0;
			}
			if (! $paramsField['enable_subscribe_menu']) {
				$paramsField['enable_subscribe_menu'] = 0;
			}
			if (! $paramsField['enable_sorting_options']) {
				$paramsField['enable_sorting_options'] = 0;
			}
			if (! $paramsField['enable_timestamp']) {
				$paramsField['enable_timestamp'] = 0;
			}
			if (! $paramsField['enable_conversationbar']) {
				$paramsField['enable_conversationbar'] = 0;
			}
			if (! $paramsField['enable_votedlist']) {
				$paramsField['enable_votedlist'] = 0;
			}
			if (! $paramsField['enable_user_rep_indicator']) {
				$paramsField['enable_user_rep_indicator'] = 0;
			}
			if (! $paramsField['enable_comment_form']) {
				$paramsField['enable_comment_form'] = 0;
			}
			
			if (! $paramsField['enable_login_rpx']) {
				$paramsField['enable_login_rpx'] = 0;
			}
			if (! $paramsField['enable_addthis']) {
				$paramsField['enable_addthis'] = 0;
			}
			if (! $paramsField['enable_addtoany']) {
				$paramsField['enable_addtoany'] = 0;
			}
			if (! $paramsField['enable_after_the_deadline']) {
				$paramsField['enable_after_the_deadline'] = 0;
			}
			if (! $paramsField['enable_polldaddy']) {
				$paramsField['enable_polldaddy'] = 0;
			}
			if (! $paramsField['enable_seesmic']) {
				$paramsField['enable_seesmic'] = 0;
			}
			if (! $paramsField['enable_smileys']) {
				$paramsField['enable_smileys'] = 0;
			}
			if (! $paramsField['enable_tweetmeme']) {
				$paramsField['enable_tweetmeme'] = 0;
			}
			if (! $paramsField['enable_youtube']) {
				$paramsField['enable_youtube'] = 0;
			}
			if (! $paramsField['enable_bbcode']) {
				$paramsField['enable_bbcode'] = 0;
			}
			if (! $paramsField['enable_activity_stream']) {
				$paramsField['enable_activity_stream'] = 0;
			}
			if (! $paramsField['enable_character_counter']) {
				$paramsField['enable_character_counter'] = 0;
			}
			if (! $paramsField['enable_location_detection']) {
				$paramsField['enable_location_detection'] = 0;
			}
		}
		// -- added by congtq 19/10/2009
		

		if ($paramsField) {
			foreach ($paramsField as $k => $v) {
				$params->set($k, $v);
			}
			
			$post['data'] = $params->toString();
			$model->setState('request', $post);
			if ($id = $model->store()) {
				if ($group == 'layout') {
					if (isset($paramsField['custom_css'])) {
						$msg = $this->saveCustomCSS($paramsField['custom_css']) . "\n";
					}
				}
				
				$msg = JText::_('UPDATED_CONFIGURATION_HAS_BEEN_SAVED_SUCCESSFULLY');
			} else {
				$msg = JText::_('ERROR_CONFIGURATION_UPDATE_FAILED');
			}
		}
		if ($task != 'saveIFrame') {
			$this->setRedirect("index.php?option=$option&view=configs&group=$group", $msg);
		} else {
			return true;
		}
		return true;
	}
	
	/**
	 * Load language file
	 * 
	 * @return void
	 */
	function loadLanguage()
	{
		$helper = new JACommentHelpers();
		$inputs = Factory::getApplication()->input;
		$type = $inputs->getString('type', '');
		$name = $inputs->get('name', 'en-GB.com_jacomment');
		
		if ($type == 'frontend') {
			$dir = JPATH_SITE . DS . 'components' . DS . 'com_jacomment' . DS . 'languages' . DS;
		} else {
			$dir = JPATH_COMPONENT . DS . 'languages' . DS;
		}
		$content = $helper->loadLangFile($dir . $name . '.php');
		echo $content;
	}
	
	/**
	 * Save language file
	 * 
	 * @return void
	 */
	function saveLanguage()
	{
		$inputs = Factory::getApplication()->input;
		$type = $inputs->getString('type', '');
		$name = $inputs->getString('name', 'en-GB.com_jacomment');
		$content = $inputs->getString('content', '');
		
		if ($type == 'frontend') {
			$dir = JPATH_SITE . DS . 'components' . DS . 'com_jacomment' . DS . 'languages' . DS;
		} else {
			$dir = JPATH_COMPONENT . DS . 'languages' . DS;
		}
		
		while (@ ob_end_clean()) {
			;
		}
		$content = "<?php\n" . $content . "?" . ">";
		$content = stripslashes($content);
		
		$filename = $dir . $name . '.php';
		
		$handle = fopen($filename, "w");
		fwrite($handle, ($content));
		fclose($handle);
		
		echo $content;
	}
	
	/**
	 * Save blocked/blacklist e-mail list
	 * 
	 * @return void
	 */
	function saveBlockBlack()
	{
		$model = $this->getModel('configs');
		$inputs = Factory::getApplication()->input;
		$tab = $inputs->getCmd('tab');
		
		$data = $inputs->get('data','');
		$data = preg_replace("/[^a-zA-Z0-9-,\\.\\@\s]/", "", $data);
		$data = preg_replace('/\s*\s/m', ' ', $data);
		$data = preg_replace('/\n*\n/m', '\n', $data);
		$data = str_replace(' ', '\n', $data);
		//auto replace special character.
		

		if ($tab != "blacklist_email_list" && $tab != "blocked_email_list") {
			$data = strtoupper($data);
			$arr_str = explode("\n", strtoupper($model->getBlockBlack()));
		} else {
			$arr_str = explode("\n", $model->getBlockBlack());
		}
		
		$arr_data = explode('\n', $data);
		
		//remove data duplicate
		$arr_data = array_unique($arr_data);
		// ++ check existed word

		$arrExit = array();
		
		$arr_temp = $arr_data;
		for ($i = 0; $i < count($arr_temp); $i++) {
			if (@in_array($arr_temp[$i], $arr_str)) {
				$arrExit[] = @$arr_temp[$i];
				unset($arr_data[$i]);
			}
		}
		
		$msg = '';
		
		if (isset($arrExit) && (count($arrExit) > 0)) {
			if (count($arrExit) > 1) {
				if ($tab == "blocked_word_list" || $tab == "blacklist_word_list") {
					$msg = "<span id='jac-word-error'>" . JText::_('THESE_WORDS_ALREADY_EXIST') . "</span>";
				} else if ($tab == "blocked_ip_list" || $tab == "blacklist_ip_list") {
					$msg = "<span id='jac-ip-error'>" . JText::_('THESE_IP_ADDRESSES_ALREADY_EXIST') . "</span>";
				} else {
					$msg = "<span id='jac-email-error'>" . JText::_('THESE_EMAIL_ADDRESES_ALREADY_EXIST') . "</span>";
				}
			} else {
				if ($tab == "blocked_word_list" || $tab == "blacklist_word_list") {
					if ($data == "" || $data == "\n") {
						$msg = "<span id='jac-word-error'>" . JText::_('YOU_MUST_INPUT_WORD') . "</span>";
					} else {
						$msg = "<span id='jac-word-error'>" . JText::_('THIS_WORD_ALREADY_EXISTS') . "</span>";
					}
				} else if ($tab == "blocked_ip_list" || $tab == "blacklist_ip_list") {
					if ($data == "" || $data == "\n") {
						$msg = "<span id='jac-ip-error'>" . JText::_('YOU_MUST_INPUT_IP') . "</span>";
					} else {
						$msg = "<span id='jac-ip-error'>" . JText::_('THIS_IP_ADDRESS_ALREADY_EXISTS') . "</span>";
					}
				} else {
					if ($data == "" || $data == "\n") {
						$msg = "<span id='jac-email-error'>" . JText::_('YOU_MUST_INPUT_EMAIL_ADDRESS') . "</span>";
					} else {
						$msg = "<span id='jac-email-error'>" . JText::_('THIS_EMAIL_ADDRESS_ALREADY_EXISTS') . "</span>";
					}
				}
			}
		}
		
		if (count($arr_data) > 0) {
			$strData = implode("\n", $arr_data);
			if (! $model->saveBlockBlack($strData)) {
				$msg = "<span id='jac-blocked-error'>" . JText::_('ERROR_DATA_NOT_SAVED') . "</span>";
			}
		}
		
		$arr_str = explode("\n", $model->getBlockBlack());
		asort($arr_str);
		foreach ($arr_str as $k => $v) {
			if ($v) {
				$msg .= "<li id='" . $tab . "_" . $k . "' onclick='javascript: remove_blockblack(\"" . $tab . "\", \"" . $k . "\");'>" . $v . "</li>";
			}
		}
		
		echo $msg;
		exit();
	
	}
	
	/**
	 * Delete blocked/blacklist e-mail list
	 * 
	 * @return void
	 */
	function removeBlockBlack()
	{
		$model = $this->getModel('configs');
		
		$inputs = Factory::getApplication()->input;
		$tab = $inputs->getCmd('tab');
		
		if (! $model->removeBlockBlack()) {
			$msg = JText::_('ERROR_DATA_NOT_SAVED');
		
		} else {
			$arr_str = explode("\n", $model->getBlockBlack());
			if (sizeof($arr_str) > 1) {
				asort($arr_str);
				$msg = '';
				foreach ($arr_str as $k => $v) {
					if ($v) {
						$msg .= "<li id='" . $tab . "_" . $k . "' onclick='javascript: remove_blockblack(\"" . $tab . "\", \"" . $k . "\");'>" . $v . "</li>";
					}
				}
			} else {
				$msg = JText::_('NO_KEYWORD_ARE_CURRENTLY_BLACKLISTED');
				if ($tab == "blocked_word_list") {
					$msg = JText::_('NO_KEYWORD_IS_CURRENTLY_BLOCKED');
				} else if ($tab == "blocked_ip_list") {
					$msg = JText::_('NO_IP_ADDRESS_IS_CURRENTLY_BLOCKED');
				} else if ($tab == "blocked_email_list") {
					$msg = JText::_('NO_EMAIL_ADDRESS_IS_CURRENTLY_BLOCKED');
				} else if ($tab == "blacklist_word_list") {
					$msg = JText::_('NO_KEYWORD_IS_CURRENTLY_BLACKLISTED');
				} else if ($tab == "blacklist_ip_list") {
					$msg = JText::_('NO_IP_ADDRESS_IS_CURRENTLY_BLACKLISTED');
				} else {
					$msg = JText::_('NO_EMAIL_ADDRESS_IS_CURRENTLY_BLACKLISTED');
				}
			}
		}
		
		echo $msg;
		exit();
	}
	
	/**
	 * Save new moderator
	 * 
	 * @return void
	 */
	function saveAddUser()
	{
		$result = true;
		$helper = new JACommentHelpers();
		$objects = array();
		
		$model = $this->getModel('configs');
		$data = $model->getItems();
		$item = JTable::getInstance('configs', 'Table');
		$item->bind($data);
		
		$data = $item->data;
		$params = new JRegistry;
		$params->loadString($data);
		
		$inputs = Factory::getApplication()->input;
		$group = $inputs->getCmd('group', null);
		if (! $group) {
			$result = false;
		}
		
		if ($result) {
			$cid = $inputs->get('cid', array(), 'array');
			ArrayHelper::toInteger($cid, array());
			
			$count = count($cid);
			
			$user_id = '';
			$cids = array();
			$listUser = array();
			
			//check is exist user
			$listUser = $params->get('moderator', '');
			if ($cid) {
				$cids[] = implode(',', $cid);
			}
			
			if ($params->get('moderator', '') != '') {
				$cids[] = $params->get('moderator', '');
				$listUser = $params->get('moderator', '');
				$listUser = explode(',', $listUser);
			
			}
			if ($cids) {
				$user_id = implode(',', $cids);
			}
			
			if ($user_id) {
				$user_id = explode(",", $user_id);
				$user_id = array_unique($user_id);
				$user_id = implode(",", $user_id);
			}
			
			$params->set('moderator', $user_id);
			$item->group = $group;
			$item->data = $params->toString();
			
			if ($item->store() && $count > 0) {
				$modelMod = JACModel::getInstance('moderator', 'JACommentModel');
				
				$uid = implode(",", $cid);
				$where_more = ' AND u.id IN (' . $uid . ')';
				$items = $modelMod->getItems($where_more);
				
				$no = substr_count($item->data = $params->toString(), ',');
				
				$content = '';
				for ($i = 0; $c = sizeof($items), $i < $c; $i++) {
					if ($items[$i]->usertype) {
						$items[$i]->usertype = $items[$i]->usertype;
					} else {
						$items[$i]->usertype = JText::_("REGISTERED") . ' ';
					}
					
					$content .= '<tr class="row0">
	                                    <td align="center">' . ($i + 1 + $no) . '</td>
	                                    <td><input id="cb' . ($i + $no) . '" name="cid[]" value="' . $items[$i]->id . '" onclick="isChecked(this.checked);" type="checkbox"></td>
	                                    <td>' . $items[$i]->username . '</td>
	                                    <td>' . $items[$i]->usertype . '</td>
	                                    <td align="left">' . $items[$i]->email . '</td>
	                                    <td align="center">' . $items[$i]->id . '</td>
	                                </tr>';
				}
				
				$k = 0;
				$objects[$k] = new stdClass();
				$objects[$k]->id = '#user_added';
				$objects[$k]->type = 'append_id';
				$objects[$k]->status = 'ok';
				$objects[$k]->content = $content;
				$k++;
				// -- add by congtq 18/12/2009

				$helper->displayInform(JText::_("SAVE_DATA_SUCCESSFULLY"), $k, $objects);
			
			} else {
				$helper->displayInform(JText::_("ERROR_OCCURRED"), $k, $objects);
			}
		} else {
			$message[] = JText::_("ERROR_DATA_NOT_SAVED");
			$objects[] = $helper->parseProperty("html", "#system-message", $helper->message(1, $message));
		}
		
		echo $helper->parse_JSON_new($objects);
		exit();
	}
	
	/**
	 * Delete a moderator
	 * 
	 * @return void
	 */
	function remove()
	{
		$inputs = Factory::getApplication()->input;
		$group = $inputs->get('group', null);
		$cid = $inputs->get('cid', array(0), 'array');
		
		if (! isset($group) || count($cid) == 0) {
			$message = JText::_("ERROR_DATA_NOT_SAVED");
			$this->setRedirect("index.php?option=com_jacomment&view=configs&group=moderator", $message);
		} else {
			$model = $this->getModel('configs');
			$data = $model->getItems();
			$config = JTable::getInstance('configs', 'Table');
			$config->bind($data);
			
			$params = new JRegistry;
			$params->loadString($config->data);
			
			$user = $params->get('moderator', null);
			
			$user_new = array();
			
			$error = $this->checkDeletePermission($cid);
			
			if ($user) {
				$user = explode(",", $user);
				$user_new = array_diff($user, $cid);
			} else {
				$model_moderator = JACModel::getInstance('moderator', 'JACommentModel');
				$items = $model_moderator->getItems();
				if (count($items) > 0) {
					foreach ($items as $item) {
						if (! in_array($item->id, $cid)) {
							$user_new[] = $item->id;
						}
					}
				}
			}
			if ($user_new) {
				$user_new = implode(',', $user_new);
			} else {
				$user_new = '';
			}
			$user_new = 'moderator=' . $user_new;
			$config->data = $params->set('moderator', $user_new);
			$config->group = $group;
			if ($config->store()) {
				if ($error) {
					foreach ($error as $err) {
						JError::raiseWarning(1001, $err);
						$this->setRedirect("index.php?option=com_jacomment&view=configs&group=moderator");
					}
				} else {
					$message = JText::_("DELETE_DATA_SUCCESSFULLY");
					$this->setRedirect("index.php?option=com_jacomment&view=configs&group=moderator", $message);
				}
			} else {
				$message = JText::_("ERROR_DATA_NOT_SAVED");
				$this->setRedirect("index.php?option=com_jacomment&view=configs&group=moderator", $message);
			}
		}
		return true;
	}
	
	/**
	 * Check for error before deleting permission
	 * 
	 * @param integer &$cid User id
	 * 
	 * @return array Error message
	 */
	function checkDeletePermission(&$cid)
	{
		$cid_not = array();
		$error = array();
		$currentUser = Factory::getUser();
		// Access checks.
		$allow = $currentUser->authorise('core.delete', 'com_users');
		if ($allow) {
			foreach ($cid as $id) {
				$user = Factory::getUser($id);
				if ($currentUser->id == $user->id) {
					$error[] = JText::_("YOU_CAN_NOT_REMOVE_YOURSELF");
					$cid_not[] = $id;
				} else {
					$result = new JObject();
					$action = array('core.admin');
					$result->set($action, $user->authorise($action, 'com_users'));
					if ($result->get("core.admin") == 1) {
						$error[] = JText::_("YOU_CANT_REMOVE_ADMIN");
						$cid_not[] = $id;
					} else {
					
					}
				}
			}
		} else {
			$error[] = JText::_("YOU_NOT_HAVE_PERMISSION_REMOVE_MODERATOR");
		}
		if ($cid_not) {
			$cid = array_diff($cid, $cid_not);
		}
		return $error;
	}
	
	/**
	 * Save CSS file
	 * 
	 * @return void
	 */
	function saveEditCSS()
	{
		jimport('joomla.filesystem.file');
		
		$inputs = Factory::getApplication()->input;
		$post = $inputs->get('request','');
		
		$theme = $post['theme'];
		$content = $post['content'];
		
		$themeFolders = JPATH_SITE . '/components/com_jacomment/themes/';
		$file = $themeFolders . $theme . '/css/style.css';
		
		$helper = new JACommentHelpers();
		$objects = array();
		$k = 0;
		if (JFile::write($file, $content)) {
			$k = 0;
			$objects[$k] = new stdClass();
			$objects[$k]->id = '#system-message';
			$objects[$k]->type = 'html';
			$objects[$k]->status = 'ok';
			$objects[$k]->content = '';
			$k++;
			
			$helper->displayInform(JText::_("SAVE_DATA_SUCCESSFULLY"), $k, $objects);
		
		} else {
			$helper->displayInform(JText::_("ERROR_DATA_NOT_SAVED"), $k, $objects);
		}
		
		echo $helper->parse_JSON_new($objects);
		exit();
	}
	
	/**
	 * Save custom CSS file
	 * 
	 * @param string $content CSS content
	 * 
	 * @return string Result message
	 */
	function saveCustomCSS($content)
	{
		jimport('joomla.filesystem.file');
		
		$helper = new JACommentHelpers();
		$template = $helper->getTemplate();
		
		$file = JPATH_SITE . '\templates\\' . $template . '\css\ja.comment.custom.css';
		if (JFile::exists($file)) {
			if (JFile::write($file, $content)) {
				$message = JText::_("SAVE_CSS_SUCCESSFULLY");
			} else {
				$message = JText::_("ERROR_OCCURRED_CSS_NOT_SAVED") . '. ' . JText::_("PLEASE_CHECK_WRITE_PERMISSIONS");
			}
		} else {
			if (JFile::write($file, $content)) {
				$message = JText::_("SAVE_CSS_SUCCESSFULLY");
			} else {
				$message = JText::_("ERROR_OCCURRED_CSS_NOT_SAVED") . '. ' . JText::_("PLEASE_CHECK_WRITE_PERMISSIONS");
			}
		}
		return $message;
	}
}
?>