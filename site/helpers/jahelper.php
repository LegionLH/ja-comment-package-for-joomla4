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

$GLOBALS['jacconfig'] = array();
// Component Helper
jimport('joomla.application.component.helper');

$GLOBALS['JACVERSION'] = '1.0.7';
$GLOBALS['JACPRODUCTKEY'] = 'JACOMMENT';

if (! defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

use Joomla\CMS\Factory;
use Joomla\CMS\Application\ApplicationHelper;
/**
 * JACommentHelpers class
 *
 * @package		Joomla.Site
 * @subpackage	JAComment
 */
class JACommentHelpers
{
	/**
	 * Check if data is posted back to server
	 * 
	 * @return integer Number of post
	 */
	function isPostBack()
	{
		$inputs = Factory::getApplication()->input;
		if ($inputs->getCmd('task') == 'add') {
			return false;
		}
		return count($_POST);
	}
	
	/**
	 * Generate date
	 * 
	 * @param integer $timestamp The time stamp
	 * @param integer $mid 		 Use "today", "yesterday", ... or not
	 * @param string  $format	 Time format
	 * 
	 * @return string Date time string
	 */
	function generatDate($timestamp, $mid = 0, $format = "d/M/Y H:i:s")
	{
		if (intval($timestamp) == 0) {
			return "<span class=\"small\"> " . JText::_('NOT_AVAILABLE') . "</span>";
		}
		$cal = explode(" ", date($format, $timestamp));
		if ($mid != 0) {
			if ($cal[0] == date("d/M/Y")) {
				return JText::_("TODAY");
			} else {
				return $cal[0];
			}
		} else {
			return $cal[0] . " " . JText::_('AT') . " " . $cal[1];
		}
	}
	
	/**
	 * Check file template
	 * 
	 * @param string $file   Template file name
	 * @param string $type   Template type
	 * @param string $folder Template folder
	 * 
	 * @return string Current path template
	 */
	function checkFileTemplate($file, $type = 'css', $folder = '')
	{
		$inputs = Factory::getApplication()->input;
		$client = ApplicationHelper::getClientInfo($inputs->get('client', 0));
		$tBaseDir = $client->path . DS . 'templates';
		$template = JACommentHelpers::templateDefaulte();
		$fileName = '';
		if ($template) {
			$tBaseDir .= DS . $template->name;
			$fileName = $tBaseDir . DS . $type . DS;
			if ($folder) {
				$fileName .= $folder . DS . 'tmpl';
			}
			$fileName .= DS . $file;
			if (! JFile::exists($fileName)) {
				return false;
			}
		}
		return $fileName;
	}
	
	/**
	 * Get default template
	 * 
	 * @return string Template name
	 */
	static function templateDefaulte()
	{
		$inputs = Factory::getApplication()->input;
		$client = ApplicationHelper::getClientInfo($inputs->get('client', 0));
		$tBaseDir = $client->path . DS . 'templates';
		//get template xml file info
		$rows = array();
		$rows = JACommentHelpers::parseXMLTemplateFiles($tBaseDir);
		$template = '';
		for ($i = 0; $i < count($rows); $i++) {
			if (JACommentHelpers::isTemplateDefault($rows[$i]->directory, $client->id)) {
				$template = $rows[$i];
			}
		}
		return $template;
	}
	
	/**
	 * Get font of captcha
	 * 
	 * @return string Path of font
	 */
	function getFontOfCaptcha()
	{
		global $jacconfig;
		$app = Factory::getApplication();
		$fileName = "captcha/font.ttf";
		if (isset($jacconfig) && isset($jacconfig["layout"])) {
			$templateJaName = $jacconfig["layout"]->get("theme", "default");
		} else {
			$templateJaName = "default";
		}
		
		$session = Factory::getSession();
		$inputs = Factory::getApplication()->input;
		if ($inputs->getCmd("jacomment_theme", '')) {
			jimport('joomla.filesystem.folder');
			$themeURL = $inputs->getCmd("jacomment_theme");
			if (JFolder::exists('components/com_jacomment/themes/' . $themeURL) || (JFolder::exists('templates/' . $app->getTemplate() . '/html/com_jacomment/themes/' . $themeURL))) {
				$templateJaName = $themeURL;
			}
			$session->set('jacomment_theme', $templateJaName);
		} else {
			if ($session->get('jacomment_theme', null)) {
				$templateJaName = $session->get('jacomment_theme', $templateJaName);
			}
		}
		
		$templateDirectory = JPATH_BASE . DS . 'templates' . DS . $app->getTemplate() . DS . 'html' . DS . "com_jacomment" . DS . "themes" . DS . $templateJaName . DS . "html";
		if (file_exists($templateDirectory . DS . $fileName)) {
			return $templateDirectory . DS . $fileName;
		} else {
			if (file_exists('components/com_jacomment/themes/' . $templateJaName . '/html/' . $fileName)) {
				return 'components/com_jacomment/themes/' . $templateJaName . '/html/' . $fileName;
			} else {
				return 'components/com_jacomment/themes/default/html/' . $fileName;
			}
		}
	}
	
	/**
	 * Load image
	 * 
	 * @param string $fileName File name
	 * @param string $theme	   Theme name
	 * 
	 * @return string Path of image in theme
	 */
	function jacLoadImge($fileName, $theme)
	{
		$app = Factory::getApplication();
		$fileTemplate = JPATH_BASE . DS . 'templates' . DS . $app->getTemplate() . DS . 'html' . DS . "com_jacomment" . DS . "themes" . DS . $theme . DS . "images" . DS . $fileName;
		$linkFile = "";
		if (file_exists($fileTemplate)) {
			$linkFile = JURI::base() . 'templates/' . $app->getTemplate() . '/html/com_jacomment/themes/' . $theme . '/images/' . $fileName;
		} else {
			if (file_exists(JPATH_BASE . DS . 'components/com_jacomment/themes/' . $theme . '/images/' . $fileName)) {
				$linkFile = JURI::base() . 'components/com_jacomment/themes/' . $theme . '/images/' . $fileName;
			} else {
				$linkFile = JURI::base() . 'components/com_jacomment/themes/default/images/' . $fileName;
			}
		}
		return $linkFile;
	}
	
	/**
	 * Load block
	 * 
	 * @param string $fileName  Block file name
	 * @param string $themePass Override theme
	 * 
	 * @return string Path of block
	 */
	function jaLoadBlock($fileName, $themePass = '')
	{
		global $jacconfig;
		$app = Factory::getApplication();
		if (isset($jacconfig) && isset($jacconfig["layout"])) {
			$templateJaName = $jacconfig["layout"]->get("theme", "default");
		} else {
			$templateJaName = "default";
		}
		
		$session = Factory::getSession();
		$inputs = Factory::getApplication()->input;
		if ($inputs->get("jacomment_theme", '')) {
			jimport('joomla.filesystem.folder');
			$themeURL = $inputs->getString("jacomment_theme");
			if (JFolder::exists('components/com_jacomment/themes/' . $themeURL) || (JFolder::exists('templates/' . $app->getTemplate() . '/html/com_jacomment/themes/' . $themeURL))) {
				$templateJaName = $themeURL;
			}
			$session->set('jacomment_theme', $templateJaName);
		} else {
			if ($session->get('jacomment_theme', null)) {
				$templateJaName = $session->get('jacomment_theme', $templateJaName);
			}
		}
		if ($themePass != '') {
			$templateJaName = $themePass;
		}
		$templateDirectory = JPATH_BASE . DS . 'templates' . DS . $app->getTemplate() . DS . 'html' . DS . "com_jacomment" . DS . "themes" . DS . $templateJaName . DS . "html";
		if (file_exists($templateDirectory . DS . $fileName)) {
			return $templateDirectory . DS . $fileName;
		} else {
			if (file_exists('components/com_jacomment/themes/' . $templateJaName . '/html/' . $fileName)) {
				return 'components/com_jacomment/themes/' . $templateJaName . '/html/' . $fileName;
			} else {
				return 'components/com_jacomment/themes/default/html/' . $fileName;
			}
		}
	}
	
	/**
	 * Parse XML template files
	 * 
	 * @param string $templateBaseDir Template folder
	 * 
	 * @return array Data after parsing
	 */
	static function parseXMLTemplateFiles($templateBaseDir)
	{
		// Read the template folder to find templates
		jimport('joomla.filesystem.folder');
		$templateDirs = JFolder::folders($templateBaseDir);
		
		$rows = array();
		
		// Check that the directory contains an xml file
		foreach ($templateDirs as $templateDir) {
			if (! $data = JACommentHelpers::parseXMLTemplateFile($templateBaseDir, $templateDir)) {
				continue;
			} else {
				$rows[] = $data;
			}
		}
		
		return $rows;
	}
	
	/**
	 * Get real length of comment (omit bbcode)
	 * 
	 * @param string $comment Comment text
	 * 
	 * @return integer Real length of comment
	 */
	function getRealLengthOfComment($comment)
	{
		$tags = array('/\[LARGE\]/isUs', '/\[\/LARGE\]/iUs', '/\[MEDIUM\]/isUs', '/\[\/MEDIUM\]/iUs', '/\[HR\]/iUs', '/\[B\]/isUs', '/\[\/B\]/iUs', '/\[I\]/isUs', '/\[\/I\]/iUs', '/\[U]/isUs', '/\[\/U\]/iUs', '/\[S\]/isUs', '/\[\/S\]/iUs', '/\[\*\]/isUs', '/\[\/\*\]/iUs', '/\[\#\]/isUs', '/\[\/\#\]/iUs', '/\[SUB\]/isUs', '/\[\/SUB\]/iUs', '/\[SUP\]/isUs', '/\[\/SUP\]/iUs', '/\[QUOTE]/isUs', '/\[\/QUOTE\]/iUs', '/\[LINK=(.*)\]/isUs', '/\[\/LINK\]/iUs', '/\[IMG\]/isUs', '/\[\/IMG\]/iUs', '/\[YOUTUBE\]/isUs', '/\[\/YOUTUBE\]/iUs');
		$comment = preg_replace($tags, '', $comment);
		return strlen(trim($comment));
	}
	
	/**
	 * Remove empty bbcode
	 * 
	 * @param string $comment Comment text
	 * 
	 * @return string Comment after remove empty bbcode
	 */
	function removeEmptyBBCode($comment)
	{
		$tags = array('/\[LARGE\]\s*\[\/LARGE\]/iUs', '/\[MEDIUM\]\s*\[\/MEDIUM\]/iUs', '/\[B\]\s*\[\/B\]/iUs', '/\[I\]\s*\[\/I\]/iUs', '/\[U]\s*\[\/U\]/iUs', '/\[S\]\s*\[\/S\]/iUs', '/\[\*\]\s*\[\/\*\]/iUs', '/\[\#\]\s*\[\/\#\]/iUs', '/\[SUB\]\s*\[\/SUB\]/iUs', '/\[SUP\]\s*\[\/SUP\]/iUs', '/\[QUOTE]\s*\[\/QUOTE\]/iUs', '/\[LINK\]\s*\[\/LINK\]/iUs', '/\[IMG\]\s*\[\/IMG\]/iUs', '/\[YOUTUBE\]\s*\[\/YOUTUBE\]/iUs');
		while (1) {
			$comment = preg_replace($tags, '', $comment);
			
			for ($i = 0; $i < count($tags); $i++) {
				preg_match($tags[$i], $comment, $matched);
				if ($matched) {
					break;
				}
			}
			
			if ($i == count($tags)) {
				break;
			}
		}
		return $comment;
	}
	
	/**
	 * Check if it is a default template
	 * 
	 * @param string  $template Template name
	 * @param integer $clientId Client id
	 * 
	 * @return integer 1 if it is a default template and 0 for another case
	 */
	static function isTemplateDefault($template, $clientId)
	{
		$db = Factory::getDBO();
		
		// Get the current default template
		$query = ' SELECT template ' . ' FROM #__templates_menu ' . ' WHERE client_id = ' . (int) $clientId . ' AND menuid = 0 ';
		$db->setQuery($query);
		$defaultemplate = $db->loadResult();
		
		return $defaultemplate == $template ? 1 : 0;
	}
	
	/**
	 * Parse XML template file
	 * 
	 * @param string $templateBaseDir Template base folder
	 * @param string $templateDir 	  Template folder
	 * 
	 * @return array Data after parsing
	 */
	static function parseXMLTemplateFile($templateBaseDir, $templateDir)
	{
		// Check of the xml file exists
		if (! is_file($templateBaseDir . DS . $templateDir . DS . 'templateDetails.xml')) {
			return false;
		}
		
		$xml = ApplicationHelper::parseXMLInstallFile($templateBaseDir . DS . $templateDir . DS . 'templateDetails.xml');
		
		if ($xml['type'] != 'template') {
			return false;
		}
		
		$data = new StdClass();
		$data->directory = $templateDir;
		
		foreach ($xml as $key => $value) {
			$data->$key = $value;
		}
		
		$data->checked_out = 0;
		$data->mosname = JString::strtolower(str_replace(' ', '_', $data->name));
		
		return $data;
	}
	
	/**
	 * Export e-mail template
	 * 
	 * @param array $item Comment item
	 * 
	 * @return string E-mail template content
	 */
	function temp_export($item)
	{
		$content = '## ************** ' . JText::_('BEGIN_EMAIL_TEMPLATE') . ': ' . $item['name'] . ' ****************##' . "\r\n\r\n";
		
		$content .= '[Email_Template name="' . $item['name'] . '"';
		
		$content .= ' published="' . $item['published'] . '" group="' . (int) $item['group'] . '" language="' . $item['language'] . '"]' . "\r\n";
		
		$content .= '[title]' . "\r\n";
		$content .= $item['title'] . "\r\n";
		
		$content .= '[subject]' . "\r\n";
		$content .= $item['subject'] . "\r\n";
		
		$content .= '[content]' . "\r\n";
		$content .= $item['content'] . "\r\n";
		
		$content .= '[EmailFromName]' . "\r\n";
		$content .= $item['email_from_name'] . "\r\n";
		
		$content .= '[EmailFromAddress]' . "\r\n";
		$content .= $item['email_from_address'] . "\r\n";
		$content .= '[/Email_Template]' . "\r\n\r\n";
		$content .= '## ************** ' . JText::_('END_EMAIL_TEMPLATE') . ': ' . $item['name'] . ' ****************##' . "\r\n\r\n\r\n\r\n\r\n\r\n";
		
		return $content;
	}
	
	/**
	 * Get user group
	 * 
	 * @param string  $where    Criteria
	 * @param string  $name 	Name of list
	 * @param string  $attr 	Attributes of list
	 * @param string  $selected Selected item
	 * @param integer $default  Has default item or not
	 * 
	 * @return string HTML select box for user group list
	 */
	function getGroupUser($where = '', $name = '', $attr = '', $selected = '', $default = 0)
	{
		$db = Factory::getDBO();
		$query = 'SELECT a.id as value,a.title as text
				  FROM `#__usergroups` AS a
				  LEFT OUTER JOIN `#__usergroups` AS c2 ON a.lft > c2.lft AND a.rgt < c2.rgt
				  LEFT JOIN `#__user_usergroup_map` AS map ON map.group_id = a.id
				  GROUP BY a.id
				  ORDER BY a.lft asc ' . $where;
		$db->setQuery($query);
		$types = $db->loadObjectList();
		if ($default) {
			if ($types) {
				$types = array_merge(array(JHTML::_('select.option', '0', JText::_('SELECT_GROUP'), 'value', 'text')), $types);
			} else {
				$types = array(JHTML::_('select.option', '0', JText::_('SELECT_GROUP'), 'value', 'text'));
			}
		}
		
		$lists = JHTML::_('select.genericlist', $types, $name, $attr, 'value', 'text', $selected);
		
		return $lists;
	}
	
	/**
	 * Display note
	 * 
	 * @param string $message Note message
	 * @param string $type 	  Type of message
	 * 
	 * @return void
	 */
	static function displayNote($message, $type)
	{
		?>
		<div id="jac-system-message"><?php echo $message; ?></div>
		<script type="text/javascript">
		jQuery(document).ready( function($) {
			var coo = getCookie('hidden_message_<?php echo $type?>');
			if(coo==1)
				$('#jac-system-message').attr('style','display:none');
			else
				$('#jac_help').html('<?php echo JText::_('CLOSE_TEXT')?>');
		});	
		</script>
		<?php
	}
	
	/**
	 * Get configuration data of system
	 * 
	 * @return void
	 */
	static function get_config_system()
	{
		global $jacconfig;
		$app = Factory::getApplication();
		
		if (defined('COMPOENT_JACOMMENT_CONFIG')) {
			return;
		}
		
		$setup = new stdClass();
		$db = Factory::getDBO();
		$setup = new stdClass();
		$q = 'SELECT * FROM #__jacomment_configs';
		$db->setQuery($q);
		$rows = $db->loadObjectList();
		if ($rows) {
			foreach ($rows as $row) {
				$jacconfig[$row->group] = new JRegistry;
				$jacconfig[$row->group]->loadString($row->data);
			}
		}
		define('COMPOENT_JACOMMENT_CONFIG', true);
	}
	
	/**
	 * Set comment URL
	 * 
	 * @param string $url URL of comment
	 * 
	 * @return void
	 */
	function setCommentUrl($url)
	{
		global $jacconfig;
		$webUrl = JURI::root() . "index.php?";
		$jacconfig["commenturl"] = $webUrl . $url;
	}
	
	/**
	 * Generate time stamp
	 * 
	 * @param integer $timeStamp The time stamp
	 * @param integer $mid 		 Use "today", "yesterday", ... or not
	 * 
	 * @return string Date time string
	 */
	function generatTimeStamp($timeStamp, $mid = 0)
	{
		$ago = 0;
		if ($mid == 0) {
			$cal = abs(time() - $timeStamp);
		} else {
			$cal = ($timeStamp - time());
			if ($cal < 0) {
				$cal = 0 - $cal;
				$ago = 1;
			}
		}
		$d = floor($cal / 24 / 60 / 60);
		$h = floor(($cal / 60 / 60 - $d * 24));
		$m = floor($cal / 24 / 60 / 60 / 30);
		
		if ($mid == 0) {
			if ($d < 3) {
				$str = "<span class=\"small\">" . ($h + $d * 24) . ' ' . JText::_('HOURS') . ' ' . JText::_('AGO') . "</span>";
			} elseif ($d < 120) {
				$str = "<span class=\"class_2dayago\"> " . $d . ' ' . JText::_('DAYS') . ' ' . JText::_('AGO') . "</span>";
			} else {
				$str = "<span class=\"time_show\"> " . $m . ' ' . JText::_('MONTHS') . ' ' . JText::_('AGO') . "</span>";
			}
			return $str;
		} else {
			if ($d == 0) {
				$str = "<span class=\"class_today\">" . JText::_('TODAY') . "</span>";
			} else {
				if ($ago == 1) {
					if ($d == 1) {
						$str = "<span class=\"class_yesterday\">" . JText::_('YESTERDAY') . "<span class=\"small\"> +" . $h . "h</span>";
					
					} else {
						//$str = generatDate($timeStamp,1);
						$str = "<span class=\"time_show\">" . $d . " " . "d," . $h . "h " . JText::_('AGO') . ".</span>";
					}
				} else {
					if ($d == 1) {
						$str = "<span class=\"class_tomorrow\">" . JText::_('TOMORROW') . "</span>";
					} else {
						//$str = generatDate($timeStamp,1);
						$str = "<span class=\"time_show\">" . $d . " " . "d," . $h . "h.</span>";
					}
				}
			}
			return $str;
		}
	}
	
	/**
	 * Check user access
	 * 
	 * @param string &$artileText Article text
	 * 
	 * @return boolean True if user has access to view the full article and vice versa
	 */
	static function check_access(&$artileText = '')
	{
		global $jacconfig;
		$sourceArticle = $artileText;
		// Check to see if the user has access to view the full article				
		if (JACommentHelpers::isSpecialUser()) {
			return true;
		}
		
		$app = Factory::getApplication();
		$access = isset($jacconfig['general']) ? $jacconfig['general']->get('access', 0) : 0;
		
		$user = Factory::getUser();
		$levAccess = $user->getAuthorisedViewLevels();
		
		if (in_array($access, $levAccess)) {
			return true;
		} else {
			$artileText .= '<div class="jac-offline">';
			if (JPluginHelper::isEnabled('system', 'janrain')) {
				$artileText .= '<div id="jac-login-form" style="margin:20px auto;padding:20px;width:400px;">';
				$artileText .= '<h4>' . $jacconfig['general']->get('display_message') . '</h4>&nbsp;';
				$artileText .= '{janrain}';
				$artileText .= '</div>';
			} else {
				$module = JModuleHelper::getModule('mod_login', 'Login Form');
				if ($module && $module->id) {
					$artileText .= '<div id="jac-login-form">';
					$artileText .= '<h4>' . $jacconfig['general']->get('display_message') . '</h4>&nbsp;';
					$artileText .= JModuleHelper::renderModule($module);
					$artileText .= '</div>';
				} else {
					$artileText .= '<div>';
					$artileText .= '<h4>' . $jacconfig['general']->get('display_message', JText::_('THIS_SITE_IS_DOWN_FOR_MAINTENANCE_PLEASE_CHECK_BACK_AGAIN_SOON')) . '</h4>';
					$artileText .= '</div>';
				}
			}
			$artileText .= '</div>';
			if ($sourceArticle == "") {
				echo $artileText;
			}
			return false;
		}
	}
	
	/**
	 * Check user permission
	 * 
	 * @return boolean True if user has permission to view comments and vice versa
	 */
	static function check_permissions()
	{
		global $jacconfig;
		$app = Factory::getApplication();
		$permissions = isset($jacconfig['permissions']) ? $jacconfig['permissions']->get('view', 'all') : 'all';
		
		if ($permissions == "all") {
			return true;
		}
		
		$user = Factory::getUser();
		
		if (! $user->guest) {
			return true;
		} else {
			$inputs = Factory::getApplication()->input;
			$inputs->set("option", "com_jacomment");
			echo '<div id="jac-wrapper"><div id="jac-login-form" style="margin:0px auto;width:400px;">';
			echo JText::_("PLEASE_LOGIN_TO_VIEW_COMMENT");
			echo '&nbsp;<input type="button" name="btlLogin" value="' . JText::_("LOGIN_NOW") . '" onclick="open_login(\'' . JText::_("LOGIN_NOW") . '\')" />';
			echo '</div></div>';
			return false;
		}
	}
	
	/**
	 * Check if user is special user
	 * 
	 * @param integer $userID User id
	 * @param string  $action User action
	 * 
	 * @return boolean True if user is special user and vice versaS
	 */
	static function isSpecialUser($userID = 0, $action = '')
	{
		global $jacconfig;
		
		if ($userID == 0) {
			if ($action) {
				return false;
			}
			$user = Factory::getUser();
		} else {
			$user = Factory::getUser($userID);
		}
		
		$result = new JObject();
		
		$actions = array('core.admin', 'core.manage');
		
		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, 'com_users'));
		}
		if ($result->get("core.admin") == 1 || $result->get("core.manage") == 1) {
			return true;
		} else {
			if (isset($jacconfig['moderator'])) {
				$strModerator = $jacconfig['moderator']->get("moderator", 0);
				if ($strModerator) {
					$moderators = explode(",", $strModerator);
					foreach ($moderators as $moderator) {
						if ($moderator == $user->id) {
							return true;
						}
					}
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Parse JSON object to HTML string
	 * 
	 * @param object $objects JSON object
	 * 
	 * @return string HTML string after parsing
	 */
	function parse_JSON_new($objects)
	{
		if (! $objects) {
			return;
		}
		if (function_exists("json_decode")) {
			$html = json_encode($objects);
		} else {
			include_once JPATH_COMPONENT . DS . "/helpers/JSON.php";
			$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
			$result = $json->decode($result);
		}
		return $html;
	}
	
	/**
	 * Parse JSON object to HTML string
	 * 
	 * @param object $objects JSON object
	 * 
	 * @return string HTML string after parsing
	 */
	function parse_JSON($objects)
	{
		if (! $objects) {
			return;
		}
		$db = Factory::getDBO();
		
		$html = '';
		$item_tem = array();
		foreach ($objects as $i => $row) {
			$tem = array();
			$item_tem[$i] = '{';
			foreach ($row as $k => $value) {
				$tem[$i][] = "'$k' : " . $db->Quote($value) . "";
			}
			$item_tem[$i] .= implode(',', $tem[$i]);
			$item_tem[$i] .= '}';
		}
		
		if ($item_tem) {
			$html = implode(',', $item_tem);
		}
		
		return $html;
	}
	
	/**
	 * Parse property to object
	 * 
	 * @param string  $type   Property type
	 * @param integer $id 	  Property id
	 * @param string  $value  Property value
	 * @param integer $reload Reload page or not
	 * 
	 * @return object Property object after parsing
	 */
	function parseProperty($type = 'html', $id = 0, $value = '', $reload = 0)
	{
		$object = new stdClass();
		$object->type = $type;
		$object->id = $id;
		$object->value = $value;
		if ($reload) {
			$object->reload = $reload;
		}
		return $object;
	}
	
	/**
	 * Parse property has publish or unpublish link
	 * 
	 * @param string  $type   	Property type
	 * @param integer $id 	  	Property id
	 * @param integer $publish	Item is publish or unpublish
	 * @param integer $number	Item index
	 * @param string  $function Function name
	 * @param string  $title	Publish title of anchor tag
	 * @param string  $un		Unpublish title of anchor tag
	 * 
	 * @return object Property object after parsing
	 */
	function parsePropertyPublish($type = 'html', $id = 0, $publish = 0, $number = 0, $function = 'publish', $title = 'Publish', $un = 'Unpublish')
	{
		$object = new stdClass();
		$object->type = $type;
		$object->id = $id;
		if (! $publish) {
			$html = '<a  href="jacascript:void(0);" onclick="return listItemTask(\'cb' . $number . '\',\'' . $function . '\')" title=\'' . $title . '\'><img id="i5" border="0" src="images/publish_x.png" alt="' . $title . '"/></a>';
		} else {
			$function = 'un' . $function;
			$html = '<a  href="jacascript:void(0);" onclick="return listItemTask(\'cb' . $number . '\',\'' . $function . '\')" title=\'' . $un . '\'><img id="i5" border="0" src="images/tick.png" alt="' . $un . '"/></a>';
		}
		
		$object->value = $html;
		return $object;
	}
	
	/**
	 * Add activity stream to JomSocial
	 * 
	 * @param string  $actor  Actor name
	 * @param string  $title  Title of stream
	 * @param integer $cid 	  Content id
	 * @param string  $action Action command
	 * 
	 * @return void
	 */
	static function JomSocial_addActivityStream($actor, $title, $cid, $action = 'add')
	{
		global $jacconfig;
		
		if (JACommentHelpers::checkComponent('com_community') && (! isset($jacconfig['layout']) || $jacconfig['layout']->get('enable_activity_stream', 0))) {
			include_once JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php';
			include_once JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'activities.php';
			
			include_once JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'userpoints.php';
			
			$act = new stdClass();
			$act->cmd = 'com_jacomment.comment.' . $action;
			
			$userPointModel = CFactory::getModel('Userpoints');
			// Test command, with userpoint command. If is unpublished do not proceed into adding to activity stream.
			$point = $userPointModel->getPointData($act->cmd);
			$points = 0;
			if ($point && ! $point->published) {
				$points = 1;
			} elseif ($point)
				$points = $point->points;
			
			$act->actor = $actor;
			$act->target = $actor; // no target
			$act->title = JText::_($title);
			$act->content = JText::_('THIS_IS_THE_BODY');
			$act->app = 'com_jacomment.comment';
			$act->cid = $cid;
			$act->points = $points;
			CFactory::load('libraries', 'activities');
			CActivityStream::add($act);
			
			/* Add points for user */
			CuserPoints::assignPoint($act->cmd, $actor);
		}
	}
	
	/**
	 * Show message
	 * 
	 * @param boolean $iserror  Message is error or not
	 * @param string  $messages Message content
	 * 
	 * @return string Formatted message content
	 */
	function message($iserror = 1, $messages = '')
	{
		if ($iserror) {
			$content = '<dd class="error message fade">
						<ul id="jac-error">';
			foreach ($messages as $message) {
				$content .= '<li>' . $message . '</li>';
			}
			$content .= '			</ul>
					</dd>';
		} else {
			$content = '<dt class="message">Message</dt>
						<dd class="message message fade">
						<ul>';
			if ($messages && is_array($messages)) {
				foreach ($messages as $message) {
					$content .= '<li>' . $message . '</li>';
				}
			} else {
				$content .= '<li>' . $messages . '</li>';
			}
			$content .= '			</ul>
					</dd>';
		}
		return $content;
	}
	
	/**
	 * Get e-mail template
	 *
	 * @param string $temp_name Template name
	 * 
	 * @return object E-mail template
	 */
	function getEmailTemplate($temp_name)
	{
		$db = Factory::getDBO();
		
		$client = ApplicationHelper::getClientInfo(0);
		$params = JComponentHelper::getParams('com_languages');
		$language = $params->get($client->name, 'en-GB');
		
		$query = "SELECT * FROM #__jacomment_email_templates WHERE name='$temp_name' and language='$language' and  published=1";
		$db->setQuery($query);
		$template = $db->loadObject();
		
		if (! $template && $language != 'en-GB') {
			$query = "SELECT * FROM #__jacomment_email_templates WHERE name='$temp_name' and language='en-GB' and  published=1";
			$db->setQuery($query);
			$template = $db->loadObject();
		}
		
		return $template;
	}
	
	/**
	 * Get filter configuration
	 * 
	 * @return array Filter values
	 */
	function getFilterConfig()
	{
		global $jacconfig;
		$app = Factory::getApplication();
		
		$config = new JConfig();
		$filters = array();
		
		$filters['{CONFIG_ROOT_URL}'] = $app->getCfg('live_site');
		$filters['{CONFIG_SITE_TITLE}'] = $app->getCfg('live_site');
		$filters['{ADMIN_EMAIL}'] = $jacconfig['general']->get('fromemail', $config->mailfrom);
		$filters['{SITE_CONTACT_EMAIL}'] = $jacconfig['general']->get('fromemail', $config->mailfrom);
		
		return $filters;
	}
	
	/**
	 * Get URL link
	 * 
	 * @param string $link  URL link
	 * @param string $title Link title
	 * 
	 * @return string URL link with title
	 */
	function getLink($link, $title = '')
	{
		if (! strpos('http://', $link)) {
			$link = substr($link, 1, strlen($link));
			$link = JURI::root() . $link;
		}
		if ($title != '') {
			$link = "<a href='$link'>$title</a>";
		}
		return $link;
	}
	
	/**
	 * Send mail function
	 *
	 * @param mixed  $to		 Recipient e-mail(s)
	 * @param string $nameto	 Recipient name
	 * @param string $subject	 Subject content
	 * @param string $content	 Mail content
	 * @param array  $filters	 Filters list
	 * @param string $from		 Sender e-mail(s)
	 * @param string $fromname	 Sender name
	 * @param array  $attachment List of attachments
	 * @param object $header	 Has header or not
	 * 
	 * @return boolean True if mail is sent successfully and otherwise
	 */
	function sendmail($to, $nameto, $subject, $content, $filters = "", $from = '', $fromname = '', $attachment = array(), $header = true)
	{
		global $jacconfig;
		
		if ($header) {
			$header = $this->getEmailTemplate("mailheader");
			$footer = $this->getEmailTemplate("mailfooter");
			if ($header) {
				$content = $header->content . "\n" . $content . "\n\n";
			}
			if ($footer) {
				$content .= $footer->content;
			}
		}
		
		if (is_array($filters)) {
			foreach ($filters as $key => $value) {
				$subject = str_replace($key, $value, $subject);
				$content = str_replace($key, $value, $content);
			}
		}
		
		$content = html_entity_decode(stripslashes($content));
		$subject = html_entity_decode(stripslashes($subject));
		//get admin email	
		$config = new JConfig();
		
		if (! $from) {
			$from = $jacconfig['general']->get('fromemail', $config->mailfrom);
		}
		if (! $fromname) {
			$fromname = $jacconfig['general']->get('fromname', $config->fromname);
		}
		$sendmail = $jacconfig['general']->get('mail_view_only', 0);
		$mail = null;
		//only view email
		//echo mail			
		if ($sendmail == 1) {
			//echo mail
			if (is_array($to)) {
				$to = implode(', ', $to);
			}
			echo JText::_("SENDER") . ' ' . $fromname . ' (' . $from . ")" . "<br>";
			echo JText::_("SEND_TO") . ' ' . $nameto . ' (' . $to . ")" . "<br>";
			echo JText::_("SUBJECT") . ' ' . $subject . "<br />";
			echo JText::_('CONTENT') . ' ' . str_replace("\n", "<br/>", $content) . "<br />-----------------------------<br />";
			return true;
		} else {
			//send email			
			$mail = Factory::getMailer();
			$mail->setSender(array($from, $fromname));
			$mail->addRecipient($to);
			$mail->setSubject($subject);
			$mail->setBody(str_replace("\n", "<br/>", $content));
			
			if ($jacconfig['general']->get('sendmode', 1)) {
				$mail->IsHTML(true);
			} else {
				$mail->IsHTML(false);
			}
			
			if ($jacconfig['general']->get('ccemail') != "") {
				$mail->addCc(explode(',', $jacconfig['general']->get('ccemail')));
			}
			
			if ($attachment) {
				$mail->addAttachment($attachment);
			}
			
			$sent = $mail->Send();
			
			return true;
		}
		return false;
	}
	
	/**
	 * Send mail when there is new comment approved
	 * 
	 * @param integer $commentID		   Item id
	 * @param string  $wherejatotalcomment Criteria to get all comments
	 * @param string  $type				   Add new or reply comment
	 * @param array   $post				   Post request
	 * 
	 * @return void
	 */
	function sendMailWhenNewCommentApproved($commentID, $wherejatotalcomment = '', $type = '', $post = '')
	{
		$app = Factory::getApplication();
		$url = $app->isClient('administrator') ? JURI::root() : JURI::base();
		$url = $url . $post['referer'];
		$url = str_replace('//', '/', $url);
		$url = str_replace('http:/', 'http://', $url);
		
		if ($type == "addNew") {
			if ($wherejatotalcomment) {
				//get all comment is chooise subcription is 2 								
				$itemSendMails = $this->getItemsSendMail($wherejatotalcomment . " AND c.subscription_type = 2 AND c.id <>" . $commentID);
			}
			
			if ($itemSendMails) {
				$mail = $this->getEmailTemplate("Jacommentnotifying_comment_creator_if_there_is_a_new_comment_on_the_issue");
				$filters = array();
				
				$userEmail = "";
				$userName = "";
				foreach ($itemSendMails as $itemSendMail) {
					$userEmail = $itemSendMail->email;
					$userName = $itemSendMail->name;
					
					$filters['{USERS_USERNAME}'] = $userName;
					$filters['{ITEM_DETAILS}'] = $post['comment'];
					$filters['{ITEM_TITLE_WITH_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
					$filters['{ITEM_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
					$filters['{CONFIG.SITE_TITLE}'] = $app->getCfg('sitename');
					
					$this->sendmail($userEmail, $userName, $mail->subject, $mail->content, $filters);
				}
			}
		} else {
			// type is reply comment
			$parentArray = array();
			$this->getParentCommentID($commentID, $parentArray);
			//get all comment is chooise subcription is 2
			if ($wherejatotalcomment) {
				$itemSendMails = $this->getItemsSendMail($wherejatotalcomment . " AND c.id <>" . $commentID);
			}
			if ($itemSendMails) {
				$mail = $this->getEmailTemplate("Jacommentnotifying_comment_creator_if_there_is_a_new_comment_on_the_issue");
				$mailReply = $this->getEmailTemplate("Jacommentnotifying_comment_creator_if_there_is_a_new_reply_to_his_comment");
				$filters = array();
				
				$userEmail = "";
				$userName = "";
				foreach ($itemSendMails as $itemSendMail) {
					//check in parent array
					if (isset($parentArray[$itemSendMail->id])) {
						if ($parentArray[$itemSendMail->id]['subscription_type'] == 1) {
							$userEmail = $itemSendMail->email;
							$userName = $itemSendMail->name;
							
							$filters['{USERS_USERNAME}'] = $userName;
							$filters['{ITEM_DETAILS}'] = $post['comment'];
							$filters['{REPLY_OWNER}'] = $post['name'];
							$filters['{ITEM_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
							$filters['{ITEM_TITLE_WITH_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
							$filters['{CONFIG.SITE_TITLE}'] = $app->getCfg('sitename');
							
							$this->sendmail($userEmail, $userName, $mailReply->subject, $mailReply->content, $filters);
						} else {
							if ($itemSendMail->subscription_type == 2) {
								//echo "OK -".$itemSendMail->id." --". $itemSendMail->email. "++".$itemSendMail->subscription_type."*****\n";
								$userEmail = $itemSendMail->email;
								$userName = $itemSendMail->name;
								
								$filters['{USERS_USERNAME}'] = $userName;
								$filters['{ITEM_DETAILS}'] = $post['comment'];
								$filters['{ITEM_TITLE_WITH_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
								$filters['{ITEM_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
								$filters['{CONFIG.SITE_TITLE}'] = $app->getCfg('sitename');
								
								$this->sendmail($userEmail, $userName, $mail->subject, $mail->content, $filters);
							}
						}
					} else {
						if ($itemSendMail->subscription_type == 2) {
							//echo "OK -".$itemSendMail->id." --". $itemSendMail->email. "++".$itemSendMail->subscription_type."*****\n";
							$userEmail = $itemSendMail->email;
							$userName = $itemSendMail->name;
							
							$filters['{USERS_USERNAME}'] = $userName;
							$filters['{ITEM_DETAILS}'] = $post['comment'];
							$filters['{ITEM_TITLE_WITH_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
							$filters['{ITEM_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
							$filters['{CONFIG.SITE_TITLE}'] = $app->getCfg('sitename');
							
							$this->sendmail($userEmail, $userName, $mail->subject, $mail->content, $filters);
						}
					}
				}
			}
		}
	}
	
	/**
	 * Replace special string
	 * 
	 * @param string $text Text needing to replace
	 * 
	 * @return string Text after replacing
	 */
	function replaceSpecialString($text)
	{
		$text = str_replace("<br />", "\n", $text);
		$text = preg_replace('#<img[^\>]+/>#isU', '$1', $text);
		/* $text = preg_replace('#(?<!S)<a.*?>(.*?)</a>#isU', '$1$2$3', $text); */
		$text = preg_replace('#<object.*?>(.*?)</object>#isU', '$1', $text);
		$text = preg_replace('#<code.*?>(.*?)</code>#isU', '$1', $text);
		$text = preg_replace('#<embed.*?>(.*?)</embed>#isU', '$1', $text);
		return $text;
	}
	
	/**
	 * Replace URLs by HTML links
	 * 
	 * @param string $text Text to replace
	 * 
	 * @return string Text after replacing
	 */
	function replaceURLWithHTMLLinks($text)
	{
		global $jacconfig;
		$text = " " . $text;
		if ($jacconfig["spamfilters"]->get("is_nofollow")) {
			$text = preg_replace('/(?<!S)((http(s?):\/\/)|(www.))+([a-zA-Z0-9\/*+-_?&;:%=.,#]+)/', '<a href="http$3://$4$5" target="_blank" rel="nofollow">$4$5</a>', $text);
			$text = preg_replace('/(?<!S)([a-zA-Z0-9_.\-]+\@[a-zA-Z][a-zA-Z0-9_.\-]+[a-zA-Z]{2,6})/', '<a href="mailto://$1" rel="nofollow">$1</a>', $text);
		} else {
			$text = preg_replace('/(?<!S)((http(s?):\/\/)|(www.))+([a-zA-Z0-9\/*+-_?&;:%=.,#]+)/', '<a href="http$3://$4$5" target="_blank">$4$5</a>', $text);
			$text = preg_replace('/(?<!S)([a-zA-Z0-9_.\-]+\@[a-zA-Z][a-zA-Z0-9_.\-]+[a-zA-Z]{2,6})/', '<a href="mailto://$1">$1</a>', $text);
		}
		return $text;
	}
	
	/**
	 * Replace BBCode by HTML code
	 * 
	 * @param string $text			   Text to replace
	 * @param mixed  $isEnebaleBBcode  BBCode is enabled or not
	 * @param mixed  $is_nofollow	   Link is no-follow or not
	 * 
	 * @return string Text after replacing
	 */
	function replaceBBCodeToHTML($text, $isEnebaleBBcode = '', $is_nofollow = '')
	{
		global $jacconfig;
		if ($isEnebaleBBcode == '') {
			$isEnebaleBBcode = $jacconfig["layout"]->get("enable_bbcode", 0);
		}
		if ($isEnebaleBBcode) {
			include_once JPATH_ROOT . "/components/com_jacomment/libs/dcode.php";
			if (class_exists('DCODE')) {
				$myDcode = new DCODE();
				//  (this is the full set)
				$myDcode->setTags("LARGE", "MEDIUM", "HR", "B", "I", "U", "S", "UL", "OL", "SUB", "SUP", "QUOTE", "LINK", "IMG");
				if ($is_nofollow == '') {
					$is_nofollow = $jacconfig["spamfilters"]->get("is_nofollow");
				}
				$text = $myDcode->parse($text, $is_nofollow);
			}
		} else {
			$text = str_replace("\n", "<br />", trim($text));
		}
		return $text;
	}
	
	/**
	 * Send mail when there is a comment changed type
	 * 
	 * @param string  $userName	 User name
	 * @param string  $userEmail User e-mail
	 * @param string  $content	 E-mail content
	 * @param string  $url		 Reference link
	 * @param integer $type		 Type of comment
	 * @param string  $action	 Action when change type
	 * 
	 * @return void
	 */
	function sendMailWhenChangeType($userName, $userEmail, $content, $url = "", $type = 0, $action = '')
	{
		global $jacconfig;
		$config = new JConfig();
		$app = Factory::getApplication();
		$tmpUrl = $app->isClient('administrator') ? JURI::root() : JURI::base();
		$url = JRoute::_($tmpUrl.$url);
		$url = str_replace('//', '/', $url);
		$url = str_replace('http:/', 'http://', $url);
		$url = preg_replace('/(.*?)\/http/', 'http', $url);
		if ($action) {
			if ($action == "removeSpam") {
				$mail = $this->getEmailTemplate("Jacommentnotifying_those_whose_comment_is_removed_as_spam_by_admin");
				if (! $mail) {
					return;
				}
				$filters = array();
				$filters['{USERS_USERNAME}'] = $userName;
				$filters['{ITEM_DETAILS}'] = $content;
				$filters['{MOD_REASONS}'] = JText::_("AFTER_CONSIDERATION_TEXT");
				$filters['{SITE_ADMIN}'] = JText::_("ADMINISTRATOR");
				$filters['{ITEM_TITLE_WITH_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
				$filters['{ITEM_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
				$filters['{CONFIG.SITE_TITLE}'] = $app->getCfg('sitename');
			}
			if ($action == "reportspam") {
				//send mail for admin
				$mail = $this->getEmailTemplate("Jacommentnotifying_admin_of_a_spam_report_on_a_comment");
				if (! $mail) {
					return;
				}
				$userEmail = $jacconfig['general']->get("notify_admin_email", $config->mailfrom);
				$userName = JText::_("ADMINISTRATOR");
				
				$filters = array();
				$filters['{USERS_USERNAME}'] = $userName;
				$filters['{ITEM_DETAILS}'] = $content;
				$currentUserInfo = Factory::getUser();
				if ($currentUserInfo->guest) {
					$filters['{SPAM_REPORTER}'] = JText::_("GUEST");
				} else {
					$filters['{SPAM_REPORTER}'] = $currentUserInfo->name;
				}
				
				$filters['{SITE_ADMIN}'] = JText::_("ADMINISTRATOR");
				$filters['{ITEM_TITLE_WITH_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
				$filters['{ITEM_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
				$filters['{CONFIG.SITE_TITLE}'] = $app->getCfg('sitename');
			}
		} else {
			if ($type == 1) {
				$mail = $this->getEmailTemplate("Jacommentnotifying_those_whose_comment_has_been_approved");
				if (! $mail) {
					return;
				}
				$filters = array();
				$filters['{USERS_USERNAME}'] = $userName;
				$filters['{ITEM_DETAILS}'] = $content;
				$filters['{SITE_ADMIN}'] = JText::_("ADMINISTRATOR");
				$filters['{ITEM_TITLE_WITH_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
				$filters['{ITEM_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
				$filters['{CONFIG.SITE_TITLE}'] = $app->getCfg('sitename');
			} else if ($type == 2) {
				$mail = $this->getEmailTemplate("Jacommentnotifying_those_whose_comment_is_reported_as_spam");
				if (! $mail) {
					return;
				}
				$filters = array();
				$filters['{USERS_USERNAME}'] = $userName;
				$filters['{ITEM_DETAILS}'] = $content;
				$filters['{SPAM_REPORTER}'] = JText::_("ADMINISTRATOR");
				$filters['{SITE_ADMIN}'] = JText::_("ADMINISTRATOR");
				$filters['{ITEM_TITLE_WITH_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
				$filters['{ITEM_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
				$filters['{CONFIG.SITE_TITLE}'] = $app->getCfg('sitename');
			} else {
				$mail = $this->getEmailTemplate("Jacommentnotifying_those_whose_comment_has_been_unapproved");
				if (! $mail) {
					return;
				}
				$currentUserInfo = Factory::getUser();
				$filters = array();
				$filters['{USERS_USERNAME}'] = $userName;
				$filters['{ITEM_DETAILS}'] = $content;
				$filters['{SITE_ADMIN}'] = $currentUserInfo->name;
				$filters['{UNAPPROVE_REASONS}'] = JText::_("YOUR_COMMENT_IS_REQUIRED_TO_REVIEW");
				$filters['{ITEM_TITLE_WITH_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
				$filters['{ITEM_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
				$filters['{CONFIG.SITE_TITLE}'] = $app->getCfg('sitename');
			}
		}
		$this->sendmail($userEmail, $userName, $mail->subject, $mail->content, $filters);
	}
	
	/**
	 * Send mail when delete a comment
	 * 
	 * @param string $userName	 Comment user name
	 * @param string $userEmail  Comment user e-mail
	 * @param string $content	 E-mail content
	 * @param string $url		 Reference link
	 * @param string $userDelete User name who deleted comment
	 * 
	 * @return void
	 */
	function sendMailWhenDelete($userName, $userEmail, $content, $url = '', $userDelete = '')
	{
		global $jacconfig;
		$app = Factory::getApplication();
		$tmpUrl = $app->isClient('administrator') ? JURI::root() : JURI::base();
		$url =  JRoute::_($tmpUrl.$url);
		$url = str_replace('//', '/', $url);
		$url = str_replace('http:/', 'http://', $url);
		$url = preg_replace('/(.*?)\/http/', 'http', $url);
		if ($jacconfig["general"]->get("is_enabled_email", 0) && $mail = $this->getEmailTemplate("Jacommentnotifying_those_whose_comment_has_been_deleted")) {
			//$mail = $this->getEmailTemplate ( "Jacommentnotifying_those_whose_comment_has_been_deleted" );
			$filters = array();
			$filters['{USERS_USERNAME}'] = $userName;
			$filters['{SITE_ADMIN}'] = $userDelete;
			$filters['{ITEM_DETAILS}'] = $content;
			$filters['{USERS_CURRENTUSER}'] = $userDelete;
			$filters['{ITEM_TITLE_WITH_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
			$filters['{ITEM_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
			$filters['{CONFIG.SITE_TITLE}'] = $app->getCfg('sitename');
			$filters['{MOD_REASONS}'] = JText::_("BECAUSE_YOUR_COMMENT_HAS_INVALID_CONTENTS");
			
			$this->sendmail($userEmail, $userName, $mail->subject, $mail->content, $filters);
		}
	}
	
	/**
	 * Get parent id of a comment
	 * 
	 * @param integer $commentID    Item id
	 * @param array   &$arrayParent Properties parents
	 * 
	 * @return void
	 */
	function getParentCommentID($commentID, &$arrayParent)
	{
		$db = Factory::getDBO();
		$sql = "SELECT `parentid` FROM #__jacomment_items as c WHERE id = $commentID";
		$db->setQuery($sql);
		$child = $db->loadObjectList();
		$parentID = 0;
		if(isset($child[0])){
			$parentID = $child[0]->parentid;
		}
		if ($parentID != 0) {
			$sql = "SELECT `subscription_type`,`name`, `email` FROM #__jacomment_items as c WHERE id = $parentID";
			$db->setQuery($sql);
			$parent = $db->loadObjectList();
			$arrayParent[$parentID] = array("subscription_type" => $parent[0]->subscription_type, "name" => $parent[0]->name, "email" => $parent[0]->email);
			$this->getParentCommentID($parentID, $arrayParent);
		}
	}
	
	/**
	 * Get items to send mail
	 * 
	 * @param string $wherejatotalcomment Criteria to get all comments
	 * 
	 * @return array Object list
	 */
	function getItemsSendMail($wherejatotalcomment)
	{
		$db = Factory::getDBO();
		
		$order = ' c.id';
		$fields = "c.id, c.name, c.email, c.subscription_type, c.date, c.parentid";
		
		$sql = "SELECT $fields " . "\n FROM #__jacomment_items as c INNER JOIN (SELECT MAX(id) AS id FROM #__jacomment_items GROUP BY email) ids ON c.id = ids.id WHERE 1=1 $wherejatotalcomment" . "\n ORDER BY $order";
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
	
	/**
	 * Send mail when new comment added
	 * 
	 * @param integer $commentID		   Comment id
	 * @param string  $wherejatotalcomment Criteria to get all comments
	 * @param string  $type				   Type of comment is add new or reply
	 * @param array   $post				   Post request
	 * 
	 * @return void
	 */
	function sendAddNewMail($commentID, $wherejatotalcomment = '', $type = '', $post = '')
	{
		global $jacconfig;
		
		$app = Factory::getApplication();
		$config = new JConfig();

		$url = trim($post['referer']);
		if(preg_match('#^https?\://#i', $url)) {
			$url = JRoute::_($url);
		} else {
			$url = JRoute::_(JURI::root().$url);
		}
		
		$post["comment"] = $this->replaceBBCodeToHTML($post["comment"]);
		
		if ($jacconfig["general"]->get("is_enabled_email", 0)) {
			//is_enabled_email
			if ($jacconfig['general']->get("is_notify_admin", 1) && $mail = $this->getEmailTemplate("Jacommentnotifying_admin_on_a_new_comment_posted")) {
				//send email admin
				$userEmail = $jacconfig['general']->get("notify_admin_email", $config->mailfrom);
				$userName = JText::_("ADMINISTRATOR");
				$filters['{USERS_USERNAME}'] = $userName;
				$filters['{ITEM_DETAILS}'] = $post['comment'];
				$filters['{ITEM_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
				$filters['{ITEM_TITLE_WITH_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
				
				$filters['{ITEM_CREATE_BY}'] = $post['name'];
				$filters['{CONFIG.SITE_TITLE}'] = $app->getCfg('sitename');
				$this->sendmail($userEmail, $userName, $mail->subject, $mail->content, $filters);
			}
			
			if ($jacconfig['general']->get("is_notify_author")) {
				//send email user post
				if ($post["type"] == 1) {
					//dont need admin approved
					$mail = $this->getEmailTemplate("Jacommentconfirmation_sent_to_new_comment_creator_dont_need_admin_approved");
					if (! $mail) {
						return;
					}
					$userEmail = $post['email'];
					$userName = $post['name'];
					
					$filters['{USERS_USERNAME}'] = $userName;
					$filters['{ITEM_DETAILS}'] = $post['comment'];
					$filters['{ITEM_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
					$filters['{ITEM_TITLE_WITH_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
					
					$filters['{CONFIG.SITE_TITLE}'] = $app->getCfg('sitename');
					$this->sendmail($userEmail, $userName, $mail->subject, $mail->content, $filters);
				} else {
					$mail = $this->getEmailTemplate("Jacommentconfirmation_sent_to_new_comment_creator_need_admin_approved");
					if (! $mail) {
						return;
					}
					$userEmail = $post['email'];
					$userName = $post['name'];
					
					$filters['{USERS_USERNAME}'] = $userName;
					$filters['{ITEM_DETAILS}'] = $post['comment'];
					$filters['{ITEM_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
					$filters['{ITEM_TITLE_WITH_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
					
					$filters['{CONFIG.SITE_TITLE}'] = $app->getCfg('sitename');
					$this->sendmail($userEmail, $userName, $mail->subject, $mail->content, $filters);
				}
			}
			
			if ($jacconfig['general']->get("is_notify_content_author")) {
				$mail = $this->getEmailTemplate("Jacommentnotify_when_new_item_was_post");
				if (! $mail) {
					return;
				}
				
				$author = Factory::getUser($post['author_id']);
				
				$authorEmail = $author->email;
				$authorName = $author->name;
				
				$contentTitle = $post['contenttitle'];
				
				$filters['{USERS_USERNAME}'] = $authorName;
				$filters['{ITEM_TITLE_WITH_LINK}'] = '<a href="' . $url . '" target="_blank">' . $contentTitle . '</a>';
				$filters['{ITEM_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
				$filters['{ITEM_DETAILS}'] = $post['comment'];
				
				$filters['{CONFIG.SITE_TITLE}'] = $app->getCfg('sitename');
				$this->sendmail($authorEmail, $authorName, $mail->subject, $mail->content, $filters);
			}
			
			if ($post["type"] == 1) {
				if ($type == "addNew") {
					if ($wherejatotalcomment) {
						//get all comment is chooise subcription is 2 								
						$itemSendMails = $this->getItemsSendMail($wherejatotalcomment . " AND c.subscription_type = 2 AND c.id <>" . $commentID);
					}
					
					if ($itemSendMails) {
						$mail = $this->getEmailTemplate("Jacommentnotifying_comment_creator_if_there_is_a_new_comment_on_the_issue");
						if (! $mail) {
							return;
						}
						$filters = array();
						
						$userEmail = "";
						$userName = "";
						foreach ($itemSendMails as $itemSendMail) {
							$userEmail = $itemSendMail->email;
							$userName = $itemSendMail->name;
							
							$filters['{USERS_USERNAME}'] = $userName;
							$filters['{ITEM_DETAILS}'] = $post['comment'];
							$filters['{ITEM_TITLE_WITH_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
							$filters['{ITEM_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
							$filters['{CONFIG.SITE_TITLE}'] = $app->getCfg('sitename');
							
							$this->sendmail($userEmail, $userName, $mail->subject, $mail->content, $filters);
						}
					}
				} else {
					//type is reply comment
					$parentArray = array();
					$this->getParentCommentID($commentID, $parentArray);
					if ($wherejatotalcomment) {
						//get all comment is chooise subcription is 2
						$itemSendMails = $this->getItemsSendMail($wherejatotalcomment . " AND c.id <>" . $commentID);
					}
					if ($itemSendMails) {
						$mail = $this->getEmailTemplate("Jacommentnotifying_comment_creator_if_there_is_a_new_comment_on_the_issue");
						$mailReply = $this->getEmailTemplate("Jacommentnotifying_comment_creator_if_there_is_a_new_reply_to_his_comment");
						$filters = array();
						
						$userEmail = "";
						$userName = "";
						foreach ($itemSendMails as $itemSendMail) {
							if (isset($parentArray[$itemSendMail->id])) {
								//check in parent array
								if ($parentArray[$itemSendMail->id]['subscription_type'] == 1 && $mailReply) {
									$userEmail = $itemSendMail->email;
									$userName = $itemSendMail->name;
									
									$filters['{USERS_USERNAME}'] = $userName;
									$filters['{ITEM_DETAILS}'] = $post['comment'];
									$filters['{REPLY_OWNER}'] = $post['name'];
									$filters['{ITEM_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
									$filters['{ITEM_TITLE_WITH_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
									$filters['{CONFIG.SITE_TITLE}'] = $app->getCfg('sitename');
									
									$this->sendmail($userEmail, $userName, $mailReply->subject, $mailReply->content, $filters);
								} else {
									if ($itemSendMail->subscription_type == 2 && $mail) {
										$userEmail = $itemSendMail->email;
										$userName = $itemSendMail->name;
										
										$filters['{USERS_USERNAME}'] = $userName;
										$filters['{ITEM_DETAILS}'] = $post['comment'];
										$filters['{ITEM_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
										$filters['{ITEM_TITLE_WITH_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
										$filters['{CONFIG.SITE_TITLE}'] = $app->getCfg('sitename');
										
										$this->sendmail($userEmail, $userName, $mail->subject, $mail->content, $filters);
									}
								}
							} else {
								if ($itemSendMail->subscription_type == 2 && $mail) {
									$userEmail = $itemSendMail->email;
									$userName = $itemSendMail->name;
									
									$filters['{USERS_USERNAME}'] = $userName;
									$filters['{ITEM_DETAILS}'] = $post['comment'];
									$filters['{ITEM_TITLE_WITH_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
									$filters['{ITEM_LINK}'] = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
									$filters['{CONFIG.SITE_TITLE}'] = $app->getCfg('sitename');
									
									$this->sendmail($userEmail, $userName, $mail->subject, $mail->content, $filters);
								}
							}
						}
					}
				}
			}
		}
	}
	
	/**
	 * This function validate one email address
	 * 
	 * @param string $email Email to validate
	 * 
	 * @return 1 if this email is valid, 0 otherwise.
	 */
	function validate_email($email)
	{
		// Create the syntactical validation regular expression
		$regexp = "^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$";
		
		// Presume that the email is invalid
		$valid = 0;
		
		// Validate the syntax
		if (eregi($regexp, $email)) {
			$valid = 1;
		} else {
			$valid = 0;
		}
		
		return $valid;
	}
	
	/**
	 * Check admin permission
	 * 
	 * @return boolean True if user has permission and otherwise
	 */
	function checkPermissionAdmin()
	{
		global $jacconfig;
		$user = Factory::getUser();
		$permissions = isset($jacconfig['permissions']) ? $jacconfig['permissions'] : null;
		
		if (isset($jacconfig['permissions'])) {
			$permissions = $jacconfig['permissions']->get('permissions');
			$permissions = explode(',', $permissions); //print_r($permissions);exit;
			if (in_array($user->id, $permissions) && $user->id) {
				return true;
			} else {
				return false;
			}
		} else {
			if (in_array($user->usertype, array('Manager', 'Administrator', 'Super Administrator'))) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Get data by using CURL
	 *
	 * @param string $URL URL to get data
	 * @param array  $req Post request
	 * 
	 * @return mixed Returned data
	 */
	static function curl_getdata($URL, $req)
	{
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, $URL);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);
		
		return $result;
	}
	
	/**
	 * Get data by using socket
	 *
	 * @param string $host Socket host
	 * @param string $path Socket path
	 * @param array  $req  Post request
	 * 
	 * @return mixed Returned data
	 */
	static function socket_getdata($host, $path, $req)
	{
		$header = "POST $path HTTP/1.0\r\n";
		$header .= "Host: " . $host . "\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "User-Agent:      Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1) Gecko/20061010 Firefox/2.0\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
		$header .= $req;
		$fp = @fsockopen($host, 80, $errno, $errstr, 60);
		if (! $fp) {
			return;
		}
		@fwrite($fp, $header);
		$data = '';
		$i = 0;
		do {
			$header .= @fread($fp, 1);
		} while (! preg_match('/\\r\\n\\r\\n$/', $header));
		
		while (! @feof($fp)) {
			$data .= @fgets($fp, 128);
		}
		fclose($fp);
		return $data;
	}
	
	/**
	 * Get information of current version
	 * 
	 * @return array Current version information
	 */
	static function get_Version_Link()
	{
		$link = array();
		
		$link['current_version']['info'] = 'http://wiki.joomlart.com/wiki/JA_Comment/Overview';
		$link['current_version']['upgrade'] = 'http://www.joomlart.com/forums/downloads.php?do=cat&id=163';
		
		return $link;
	}
	
	/**
	 * Get license type
	 * 
	 * @return string License type
	 */
	function get_license_type()
	{
		global $jacconfig;
		
		if ($jacconfig['license']->get('type') == md5('professional')) {
			return 'Professional';
		} elseif ($jacconfig['license']->get('type') == md5('standard')) {
			return 'Standard';
		} else {
			return 'Trial';
		}
	}
	
	/**
	 * Run queries
	 * 
	 * @param string $sqlfile SQL file
	 * @param object &$db	  Database object
	 * @param array  &$error  Error messages
	 * 
	 * @return array Error messages
	 */
	static function populateDB($sqlfile, &$db, &$error)
	{
		$change_md_sqls = JACommentHelpers::splitSql($sqlfile);
		foreach ($change_md_sqls as $query) {
			$query = trim($query);
			if ($query != '') {
				$db->setQuery($query);
				if (! $db->execute()) {
					$error[] = " Not run " . $query;
				}
			}
		}
		return $error;
	}
	
	/**
	 * Split SQL queries
	 * 
	 * @param string $sqlfile SQL file
	 * 
	 * @return array SQL queries list
	 */
	static function splitSql($sqlfile)
	{
		$sql = file_get_contents($sqlfile);
		$sql = trim($sql);
		$sql = preg_replace("/\n\#[^\n]*/", '', "\n" . $sql);
		$buffer = array();
		$ret = array();
		$in_string = false;
		
		for ($i = 0; $i < strlen($sql) - 1; $i++) {
			if ($sql[$i] == ";" && ! $in_string) {
				$ret[] = substr($sql, 0, $i);
				$sql = substr($sql, $i + 1);
				$i = 0;
			}
			
			if ($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\") {
				$in_string = false;
			} elseif (! $in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (! isset($buffer[0]) || $buffer[0] != "\\")) {
				$in_string = $sql[$i];
			}
			if (isset($buffer[1])) {
				$buffer[0] = $buffer[1];
			}
			$buffer[1] = $sql[$i];
		}
		
		if (! empty($sql)) {
			$ret[] = $sql;
		}
		return ($ret);
	}
	
	/**
	 * Install database
	 * 
	 * @return string Error string if it has
	 */
	static function Install_Db()
	{
		global $JACVERSION;
		
		$version_list = array();
		$db = Factory::getDBO();
		
		$q = "SELECT data FROM #__jacomment_configs";
		$db->setQuery($q);
		$data = $db->loadResult();
		
		if (! $data) {
			$path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jacomment' . DS . 'installer' . DS . 'sql' . DS . 'install.configData.sql';
			
			$error = null;
			if (file_exists($path)) {
				JACommentHelpers::populateDB($path, $db, $error);
				if ($error) {
					$error = implode("<br/>", $error);
					//return JError::raiseError(1, $error);
					Factory::getApplication()->enqueueMessage(
						$error,
						'error'
					);
				}
			} else {
				//JError::raiseWarning(1, JText::_('SQL_FILE_NOT_FOUND_ERROR') . '<br /><br />');
				Factory::getApplication()->enqueueMessage(
					JText::_('SQL_FILE_NOT_FOUND_ERROR'),
					'warning'
				);
			}
		}
		
		$lis_sql_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jacomment' . DS . 'installer' . DS . 'sql';
		$versions = array('1.2.0', '1.2.1', '1.3.0', '2.5.1');

		foreach($versions as $version) {
			$filename = sprintf('upgrade_v%s.sql', $version);
			if (JACommentHelpers::table_exists('#__jacomment_items') && $filename == 'upgrade_v1.2.0.sql') {
				if (! JACommentHelpers::checkField_inserted('jacomment_items', 'children') && ! JACommentHelpers::checkField_inserted('jacomment_items', 'active_children')) {
					JACommentHelpers::populateDB($lis_sql_path . DS . $filename, $db, $error);
				}
			}
			if (JACommentHelpers::table_exists('#__jacomment_items') && $filename == 'upgrade_v1.2.1.sql') {
				if (! JACommentHelpers::checkField_inserted('jacomment_items', 'p0')) {
					JACommentHelpers::populateDB($lis_sql_path . DS . $filename, $db, $error);
				}
			}
			if (JACommentHelpers::table_exists('#__jacomment_items') && $filename == 'upgrade_v1.3.0.sql') {
				if (JACommentHelpers::checkField_inserted('jacomment_items', 'referer')) {
					JACommentHelpers::populateDB($lis_sql_path . DS . $filename, $db, $error);
				}
			}
			if (JACommentHelpers::table_exists('#__jacomment_items') && $filename == 'upgrade_v2.5.1.sql') {
				if (! JACommentHelpers::checkField_inserted('jacomment_items', 'latitude') &&
					! JACommentHelpers::checkField_inserted('jacomment_items', 'longitude') &&
					! JACommentHelpers::checkField_inserted('jacomment_items', 'address')
				) {
					JACommentHelpers::populateDB($lis_sql_path . DS . $filename, $db, $error);
				}
			}
		}
	}
	
	/**
	 * Check if table is existed in database
	 * 
	 * @param string $table Table name
	 * 
	 * @return string Table if it is existed
	 */
	static function table_exists($table)
	{
		$db = Factory::getDBO();
		
		$table = JACommentHelpers::jareplacePrefix($table);
		
		$query = "SHOW TABLES LIKE '$table'";
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	/**
	 * Check field is existed in table or not
	 * 
	 * @param string $tableName  Table name
	 * @param string $columnName Field name
	 * 
	 * @return integer 1 if field is existed in table, otherwise 0
	 */
	static function checkField_inserted($tableName, $columnName)
	{
		$db = Factory::getDBO();
		
		$jconfig = new JConfig();
		
		$query = "SHOW COLUMNS FROM " . $jconfig->dbprefix . $tableName;
		$db->setQuery($query);
		$tableFields = $db->loadObjectList();
		
		//loop to traverse tableFields result set
		for ($i = 0; $i < count($tableFields); $i++) {
			$tableField = $tableFields[$i];
			
			if ($tableField->Field == $columnName) {
				return 1;
			}
		} //end of loop
		return 0;
	}
	
	/**
	 * Check value of a field is existed or not
	 * 
	 * @param string $table		 Table name
	 * @param string $columnName Column name
	 * @param string $value		 Value of column
	 * 
	 * @return mixed 1 if value is existed, otherwise null
	 */
	function check_value_exists($table, $columnName, $value)
	{
		$db = Factory::getDBO();
		
		if (! JAVoiceHelpers::table_exists($table)) {
			return false;
		}
		
		$query = "SELECT count(*) FROM " . $table . " WHERE " . $columnName . " ='" . $value . "'";
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	// ++ added by congtq 28/10/2009
	/**
	 * Get CSS URL
	 * 
	 * @param string  $theme 	  Theme name
	 * @param boolean $isFilePath Is file path or not
	 * 
	 * @return string CSS URL
	 */
	function getCss($theme = '', $isFilePath = false)
	{
		$app = Factory::getApplication();
		
		$cssUrl = ($isFilePath ? JPATH_SITE : JURI::root()) . 'templates/' . $app->getTemplate() . '/html/com_jacomment/' . $theme . '/css/style.css';
		
		$cssPath = JPATH_SITE . DS . 'templates' . DS . $app->getTemplate() . DS . '/html/com_jacomment/' . DS . $theme . DS . 'css' . DS . 'style.css';
		if (! is_file($cssPath)) {
			$cssUrl = ($isFilePath ? JPATH_SITE : JURI::root()) . 'components/com_jacomment/views/themes/' . $theme . '/css/style.css';
		}
		
		return $cssUrl;
	}
	
	/**
	 * Get template name from database
	 * 
	 * @param integer $client_id Client id
	 * 
	 * @return string Template name
	 */
	static function getTemplate($client_id = 0)
	{
		static $template = null;
		if (! isset($template)) {
			// Load the template name from the database
			$db = Factory::getDBO();
			$query = 'SELECT template' . ' FROM #__template_styles' . ' WHERE client_id = ' . $client_id . '' . ' AND home = 1';
			$db->setQuery($query);
			$template = $db->loadResult();
			
			$_filter = new JFilterInput();
			$template = $_filter->clean($template, 'cmd');
		}
		
		return $template;
	}
	
	/**
	 * Get type status as tree list
	 * 
	 * @param integer $type		  Type of comment
	 * @param integer $parentType Parent type
	 * 
	 * @return array List as tree style
	 */
	function getListTreeStatus($type, $parentType)
	{
		$treeTypes = array();
		switch ($type) {
			case 0:
				if ($parentType == 1) {
					$treeTypes[1] = JText::_("APPROVE");
					$treeTypes[2] = JText::_("MARK_SPAM");
				}
				break;
			case 2:
				if ($parentType == 1) {
					$treeTypes[0] = JText::_("UNAPPROVE");
					$treeTypes[1] = JText::_("APPROVE");
				}
				break;
			default:
				$treeTypes[0] = JText::_("UNAPPROVE");
				$treeTypes[2] = JText::_("MARK_SPAM");
				break;
		}
		return $treeTypes;
	}
	
	/**
	 * Build status as tree
	 * 
	 * @param integer $type			Type of comment
	 * @param integer $itemid		Item id
	 * @param integer $currentTabID	Current tab id
	 * @param integer $userName		User id
	 * @param integer $parentType	Parent type
	 * 
	 * @return string HTML string in a tree of statuses
	 */
	function builtTreeStatus($type, $itemid, $currentTabID = 0, $userName = 0, $parentType = 0)
	{
		$treeTypes = array();
		$treeTypes = $this->getListTreeStatus($type, $parentType);
		$output = '';
		
		$output = '<ul>';
		
		foreach ($treeTypes as $key => $value) {
			if ($key == 1) {
				$output .= '<li>
							<a onclick="changeTypeOfComment(' . $key . ',' . $itemid . ',' . $type . ',' . $currentTabID . ');return false;" title="' . JText::_("APPROVE") . '" href="#" class="approve">' . $value . '</a>
					    </li>';
			} else if ($key == 2) {
				$output .= '<li>
							<a onclick="changeTypeOfComment(' . $key . ',' . $itemid . ',' . $type . ',' . $currentTabID . ');return false;" title="' . JText::_("MARK_SPAM") . '" href="#" class="mark-spam">' . $value . '</a>
					    </li>';
			} else {
				$output .= '<li>
							<a onclick="changeTypeOfComment(' . $key . ',' . $itemid . ',' . $type . ',' . $currentTabID . ');return false;" title="' . JText::_("UNAPPROVE") . '" href="#" class="unapprove">' . $value . '</a>
					    </li>';
			}
		}
		
		if ($userName) {
			if ($type == 1) {
				$output .= '<li>
								<a href="javascript:replyComment(' . $currentTabID . ',' . $itemid . ',\'' . $userName . '\')" class="reply" title="' . JText::_("REPLY") . '"> ' . JText::_("REPLY") . ' </a>																								
							</li>';
			}
			
			$output .= '<li>
							<a href="javascript:editComment(' . $itemid . ',' . $currentTabID . ')" class="edit" title="' . JText::_("EDIT") . '"> ' . JText::_("EDIT") . ' </a>
						</li>';
			
			$output .= '<li>
							<a href="javascript:deleteComment(' . $itemid . ',' . $currentTabID . ',' . $type . ')" class="delete" title="' . JText::_("DELETE") . '">' . JText::_("DELETE") . '</a>																								
						</li>';
		} else {
			if ($type == 1) {
				$output .= '<li>
								<a href="javascript:replyComment(' . $itemid . ',\'' . JText::_("POSTING") . '\',\'' . JText::_("REPLY") . '\')" class="repply" title="' . JText::_("REPLY") . '"> ' . JText::_("REPLY") . ' </a>
							</li>';
			}
			
			$output .= '<li>
							<a href="javascript:editComment(' . $itemid . ',\'' . JText::_("REPLY") . '\')" class="edit" title="' . JText::_("EDIT") . '"> ' . JText::_("EDIT") . ' </a>
						</li>';
			$output .= '<li>
							<a href="javascript:deleteComment(' . $itemid . ')" class="delete" title="' . JText::_("DELETE") . '">' . JText::_("DELETE") . '</a>
						</li>';
		}
		$output .= '</ul>';
		return $output;
	}
	
	/**
	 * Build change type form
	 * 
	 * @param integer $itemID		Item id
	 * @param integer $itemType		Item type
	 * @param integer $currentTabID	Current tab id
	 * @param integer $userName		User id
	 * @param integer $parentType	Parent type
	 * 
	 * @return string HTML string
	 */
	function builFormChangeType($itemID, $itemType, $currentTabID = 0, $userName = 0, $parentType = 0)
	{
		$output = '<span class="jac-status-title-' . $itemType . '">							
					<a onclick="jac_show_all_status(\'' . $itemID . '\', \'' . $itemType . '\'); return false;" href="#" class="jav-tag inline-edit">&nbsp;&nbsp;&nbsp;&nbsp;</a>							
				   </span>
				   <div class="statuses layer" style="display: none;">' . $this->builtTreeStatus($itemType, $itemID, $currentTabID, $userName, $parentType) . '</div>';
		return $output;
	}
	// -- added by congtq 28/10/2009
	

	/**
	 * Check YouTube link after parsing
	 * 
	 * @param string $url YouTube URL
	 * 
	 * @return boolean True if the link is correct and vice versa
	 */
	function checkYoutubeAfterParse($url)
	{
		if (! preg_match('/.*youtube.*(v=|\/v\/)([^&\/]*).*/i', $url)) {
			return false;
		}
		return true;
	}
	
	// ++ added by congtq 26/11/2009 
	/**
	 * Check YouTube link
	 * 
	 * @param string $url YouTube URL
	 * 
	 * @return boolean True if the link is correct and vice versa
	 */
	function checkYoutubeLink($url)
	{
		//http://www.youtube.com/watch?v=KwA-0_dG1H8
		//http://www.youtube.com/watch?v=KwA-0_dG1H8
		//echo $url;
		if (! preg_match('/(\?|&)v=([0-9a-z_-]+)(&|$)/si', $url)) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Repair YouTube link
	 * 
	 * @param string $url YouTube link
	 * 
	 * @return string Correct YouTube link
	 */
	function repairYoutubeLink($url)
	{
		if (stristr($url, 'watch') === false) {
			return $url;
		} else {
			//http://www.youtube.com/watch?v=KwA-0_dG1H8
			if (strpos($url, "watch_popup") === false) {
				$arr = explode("watch?v=", $url);
			} else {
				//http://www.youtube.com/watch_popup?v=HPmbVBfPc94
				$arr = explode("watch_popup?v=", $url);
			}
			if (stristr($url, '&') === false) {
				$code = $arr[1];
			} else {
				$arr2 = explode("&", $arr[1]);
				$code = $arr2[0];
			}
			return 'http://www.youtube.com/v/' . $code;
		}
	}
	
	/**
	 * Show YouTube video
	 * 
	 * @param string  $str		   Comment content
	 * @param boolean $showYoutube Show YouTube video or only show link
	 * 
	 * @return string Comment content after parsing to YouTube HTML string
	 */
	function showYoutube($str, $showYoutube = true,$width=450)
	{
		global $jacconfig;
		
		if ($this->checkYoutubeAfterParse($str)) {
			$pattern = "/\[youtube (.*?) youtube\]/";
			preg_match_all($pattern, $str, $matches);
			
			$arr0 = $matches[0];
			$arr1 = '';
			foreach ($matches[1] as $v) {
				if ($showYoutube) {
					$arr1[] = '<p><object type="application/x-shockwave-flash" width="'.$width.'" height="295"
                                data="' . $this->repairYoutubeLink($v) . '">
                                <param name="movie" value="' . $this->repairYoutubeLink($v) . '" />
                                <param name="wmode" value="transparent" />
                                </object></p>';
				
				} else {
					if ($jacconfig && $jacconfig["spamfilters"]->get("is_nofollow")) {
						$arr1[] = '<a target="_blank" href="' . $this->repairYoutubeLink($v) . '" rel="nofollow">' . $this->repairYoutubeLink($v) . '</a>';
					} else {
						$arr1[] = '<a target="_blank" href="' . $this->repairYoutubeLink($v) . '">' . $this->repairYoutubeLink($v) . '</a>';
					}
				}
			}
			
			$obj = str_replace($arr0, $arr1, $str);
			return $obj;
		} else {
			return $str;
		}
	
	}
	// -- added by congtq 26/11/2009 
	

	// ++ added by congtq 01/12/2009
	/**
	 * Show smiley
	 * 
	 * @param string $str		   Comment content
	 * @param mixed  $isShowSmiley Is show smiley or not
	 * 
	 * @return string Comment content after parsing smiley
	 */
	function showSmiley($str, $isShowSmiley = '')
	{
		global $jacconfig;
		if ($isShowSmiley == '') {
			$isShowSmiley = $jacconfig["layout"]->get("enable_smileys", 0);
		}
		if ($isShowSmiley) {
			$array = array(':)' => '0px 0px', ':D' => '-12px 0px', 'xD' => '-24px 0px', ';)' => '-36px 0px', ':p' => '-48px 0px', '^_^' => '0px -12px', ':$' => '-12px -12px', 'B)' => '-24px -12px', ':*' => '-36px -12px', '(3' => '-48px -12px', ':S' => '0px -24px', ':|' => '-12px -24px', '=/' => '-24px -24px', ':x' => '-36px -24px', 'o.0' => '-48px -24px', ':o' => '0px -36px', ':(' => '-12px -36px', ':@' => '-24px -36px', ":'(" => '-36px -36px');
			
			$key = array_keys($array);
			
			// fix for some special characters, e.g. cyrillic
			$str = html_entity_decode($str, ENT_QUOTES, "ISO-8859-1");
			
			foreach ($array as $k => $v) {
				$smiley = '<span class="smiley"><span style="background-position: ' . $v . ';"><span>' . $k . '</span></span></span>';
				$pattern = '/(\>[^\<]*?)('.preg_replace('/[^a-z0-9]/i','\\\\\0',$k).')/';
				$str = preg_replace($pattern, '\1'.$smiley, $str);
			
			}
			// $str = str_replace($key, $span, $str);
		}
		return $str;
	}
	
	/**
	 * Show comment content
	 * 
	 * @param string  $str			Comment content
	 * @param boolean $showYoutube	Is show YouTube video or not
	 * @param boolean $isShowSmiley	Is show smiley or not
	 * 
	 * @return string Comment content after parsing
	 */
	function showComment($str, $showYoutube = true, $isShowSmiley = '',$width=450)
	{
		$comment = $this->showYoutube($this->showSmiley($str, $isShowSmiley), $showYoutube,$width);
		return $comment;
	}
	/*
	 * Check items comment is a answer
	 * Using only for component javoice
	 * */
	function checkItem($itemid,$limited = 1){
		$app = Factory::getApplication();
		$parentArray = array();
		$this->getParentCommentID($itemid,$parentArray);
		if(!$parentArray){
			return true;
		}else if(count($parentArray) < $limited){
			return TRUE;
		}else{
			return false;
		}
		
	}
	/*
	 * Get total answer javoice
	 * */
	function getTotalAnswer($items){
		global $jacconfig;
		$comment_javoice_level = $jacconfig["general"]->get("comment_javoice_level", 1);
		$total = 0;
		foreach ($items AS $i){
			if($this->checkItem($i->id,$comment_javoice_level)){
				$total++;
			}
		}
		return $total;
	}
	/**
	 * Get authentication information of user in rpxnow
	 * 
	 * @param array   $data	  Post data
	 * @param boolean $iscurl Use cURL or not
	 * 
	 * @return array List of information
	 */
	function get_Authinfo($data, $iscurl)
	{
		if ($iscurl) {
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_URL, 'https://rpxnow.com/api/v2/auth_info/?token=' . $data['token'] . '&apiKey=' . $data['apiKey'] . '&format=json');
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$result = curl_exec($curl);
			curl_close($curl);
		} else {
			$result = file_get_contents("https://rpxnow.com/api/v2/auth_info/?token=" . $data['token'] . "&apiKey=" . $data['apiKey'] . "&format=json");
		}
		if (function_exists("json_decode")) {
			$result = json_decode($result, true);
		} else {
			include_once JPATH_ROOT . "/components/com_jacomment/libs/JSON.php";
			$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
			$result = $json->decode($result);
		}
		return $result;
	}
	
	/**
	 * Show other fields
	 * 
	 * @param string  $jq 	   Content of other field
	 * @param integer &$k	   Index of object
	 * @param array   &$object Object to load
	 * 
	 * @return void
	 */
	function showOtherField($jq, &$k, &$object)
	{
		$object[$k] = new stdClass();
		$object[$k]->id = '#other_field';
		$object[$k]->type = 'html';
		$object[$k]->content = $jq;
		$k++;
	}
	// -- added by congtq 01/12/2009
	

	// ++ added by congtq 04/12/2009 
	/**
	 * Serialize a string
	 * 
	 * @param string $str String to serialize
	 * 
	 * @return string String after serializing
	 */
	function ja_serialize($str)
	{
		return base64_encode(serialize($str));
	}
	
	/**
	 * Unserialize a string
	 * 
	 * @param string $str String to unserialize
	 * 
	 * @return string String after unserializing
	 */
	function ja_unserialize($str)
	{
		return @unserialize(base64_decode($str));
	}
	// -- added by congtq 04/12/2009
	

	/**
	 * Display message in form
	 * 
	 * @param string  $error 	 		 Error string to show
	 * @param integer &$k				 Index of object
	 * @param array   &$object			 Object to load
	 * @param integer $timeDelay		 Delay time
	 * @param boolean $clearPopupContent Clear pop-up content or not
	 * 
	 * @return void
	 */
	function displayInform($error, &$k, &$object, $timeDelay = 0, $clearPopupContent = false)
	{
		if ($timeDelay) {
			$message = '<script type="text/javascript" id="script_error">jacdisplaymessage("' . $timeDelay . '"); ';
			$message .= $clearPopupContent ? "jQuery('#ja-popup-content').html('');" : '';
			$message .= '</script>';
		} else {
			$message = '<script type="text/javascript" id="script_error">jacdisplaymessage(); ';
			$message .= $clearPopupContent ? "jQuery('#ja-popup-content').html('');" : '';
			$message .= '</script>';
		}
		$object[$k] = new stdClass();
		$object[$k]->id = '#jac-msg-succesfull';
		$object[$k]->type = 'html';
		$object[$k]->content = $error . $message;
		$k++;
	}
	
	/**
	 * Get user avatar
	 * 
	 * @param integer $userID	  User id
	 * @param integer $ismodule   Comment from module or others
	 * @param integer $avatarSize Avatar size
	 * @param string  $typeAvatar Type of avatar
	 * @param string  $itemEmail  User e-mail
	 * 
	 * @return array Array of avatar source and size
	 */
	static function getAvatar($userID = 0, $ismodule = 0, $avatarSize = 0, $typeAvatar = '', $itemEmail = '')
	{
		global $jacconfig;
		$app = Factory::getApplication();
		
		$avatar = '';
		if (! $ismodule && isset($jacconfig['layout']) && ! $jacconfig['layout']->get('enable_avatar')) {
			return $avatar;
		}
		
		$src = JURI::root() . 'components/com_jacomment/asset/images/avatar-large.png';
		if (! $avatarSize) {
			$avatarSize = $jacconfig['layout']->get('avatar_size', 1);
			
			if ($avatarSize == 1) {
				$size = "height:18px; width:18px;";
			} else if ($avatarSize == 2) {
				$size = "height:24px; width:24px;";
			} else if ($avatarSize == 3) {
				$size = "height:40px; width:40px;";
			}
		} else {
			$size = "height:{$avatarSize}px; width:{$avatarSize}px;";
		}
		
		if (! $userID && $typeAvatar == 3) {
			$avatar = JACommentHelpers::getAvatarGravatar($itemEmail, $avatarSize, $src, $ismodule);
			if (! $avatar) {
				$avatar = $src;
			}
			
			return $avatar = array($avatar, $size);
		} else if (! $userID) {
			return array($src, $size);
		}
		
		$user = Factory::getUser($userID);
		$params = new JRegistry;
		$params->loadString($user->params);
		
		if ($params->get('providerName', '') == 'Twitter' || $params->get('providerName', '') == 'Facebook') {
			if ($params->get('photo')) {
				$avatar = $params->get('photo', '');
			}
		}
		
		if (! $typeAvatar) {
			if (isset($jacconfig['layout'])) {
				$typeAvatar = $jacconfig['layout']->get('type_avatar');
			}
		}
		
		if (! $avatar) {
			switch ($typeAvatar) {
				case 1:
					if (JACommentHelpers::checkComponent('com_comprofiler')) {
						$avatar = JACommentHelpers::getAvatarCB($userID);
					}
					break;
				case 2:
					if (JACommentHelpers::checkComponent('com_kunena')) {
						$avatar = JACommentHelpers::getAvatarKunena($userID);
					} else if (JACommentHelpers::checkComponent('com_fireboard')) {
						$avatar = JACommentHelpers::getAvatarFireboard($userID);
					}
					break;
				case 4:
					if (JACommentHelpers::checkComponent('com_community')) {
						$avatar = JACommentHelpers::getAvatarJomSocial($userID);
					}
					break;
				case 3:
					$avatar = JACommentHelpers::getAvatarGravatar($user->email, $avatarSize, $src, $ismodule);
					break;
				case 5:
					if (JACommentHelpers::checkComponent('com_k2')) {
						$avatar = JACommentHelpers::getAvatarK2($userID, $user->email, $avatarSize, $ismodule);
					}
					break;
				case 6:
					if (JACommentHelpers::checkComponent('com_alphauserpoints')) {
						$avatar = JACommentHelpers::getAvatarAUP($userID, $avatarSize, $ismodule);
					}
					break;
				case 7:
					if (JACommentHelpers::checkComponent('com_easyblog')) {
						$avatar = JACommentHelpers::getAvatarEasyBlog($userID);
					}
					break;
				default:
					$avatar = null;
					break;
			}
		}
		if (! $avatar) {
			$avatar = $src;
		}
		
		return $avatar = array($avatar, $size);
	}
	
	/**
	 * Check a component is existed in system or not
	 * 
	 * @param string $component Component name
	 * 
	 * @return integer 1 if component is existed, otherwise 0
	 */
	static function checkComponent($component)
	{
		$db = Factory::getDBO();
		$query = " SELECT Count(*) FROM #__extensions as c WHERE c.element ='$component' ";
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	/**
	 * Get Kunena avatar
	 * 
	 * @param integer $userID User id
	 * 
	 * @return mixed Avatar location if it exists, otherwise false
	 */
	static function getAvatarKunena($userID)
	{
		if (file_exists(JPATH_SITE . DS . "components" . DS . "com_kunena" . DS . "lib" . DS . "kunena.config.class.php")) {
			include_once JPATH_SITE . DS . "components" . DS . "com_kunena" . DS . "lib" . DS . "kunena.config.class.php";
			$fbConfig = CKunenaConfig::getInstance();
			
			if ($fbConfig->avatar_src == 'fb') {
				$fbProfile = KunenaFactory::getUser($userID);
				$avatarlink = $fbProfile->getAvatarLink('kavatar', 'profile');
				$re = '/src=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/is';
				if (preg_match($re, $avatarlink, $match)) {
					return urldecode($match[2]);
				}
				return false;
			}
		}
		return false;
	}
	
	/**
	 * Get Fireboard avatar
	 * 
	 * @param integer $userID User id
	 * 
	 * @return mixed Avatar location if it exists, otherwise false
	 */
	static function getAvatarFireboard($userID)
	{
		$fireConfig = JPATH_SITE . '/administrator/components/com_fireboard/fireboard_config.php';
		
		//Version is 1.0.5
		if (! file_exists($fireConfig)) {
			$fireConfig = JPATH_SITE . '/components/com_fireboard/sources/fb_config.class.php';
			if (file_exists($fireConfig)) {
				include_once $fireConfig;
				global $fbConfig;
				
				$fireConfig = new fb_config();
				$fireConfig->load();
			}
		}
		
		//check 
		if (! is_object($fireConfig) && ! file_exists($fireConfig)) {
			return false;
		}
		
		//Version < 1.0.5
		if (! is_object($fireConfig)) {
			include $fireConfig;
			$fireArray = new stdclass();
			global $fbConfig;
			$fireArray->avatar_src = $fbConfig['avatar_src'];
			$fireArray->version = $fbConfig['version'];
			$fireConfig = $fireArray;
		}
		
		if ($fireConfig->avatar_src == 'fb') {
			//get avatar image from database			
			$db = Factory::getDBO();
			
			$sql = "SELECT `avatar` FROM #__fb_users WHERE `userid`='{$userID}'";
			
			$db->setQuery($sql);
			
			$imgPath = $db->loadResult();
			
			if ($imgPath) {
				$fireboardAvatar = '';
				if (@! is_null($fireConfig->version) && @isset($fireConfig->version) && @$fireConfig->version == '1.0.1') {
					$fireboardAvatar = 'components/com_fireboard/avatars/' . $imgPath;
				} else {
					$fireboardAvatar = 'images/fbfiles/avatars/' . $imgPath;
				}
				
				//check exist image of user
				if (file_exists(JPATH_SITE . DS . $fireboardAvatar)) {
					return JURI::root() . $fireboardAvatar;
				} else {
					// Return false if Image file doesn't exist.
					return false;
				}
			} else {
				// user don't use avatar.
				return false;
			}
		}
		return false;
	}
	
	/**
	 * Get Gravatar avatar
	 * 
	 * @param string  $email		 User e-mail
	 * @param integer $avatarSize	 Avatar size
	 * @param string  $defaultAvatar Default avatar
	 * @param integer $ismodule		 Comment from module or others
	 * 
	 * @return string Avatar location
	 */
	static function getAvatarGravatar($email, $avatarSize, $defaultAvatar, $ismodule)
	{
		$imgSource = false;
		if ($ismodule) {
			$imgSource = 'http://www.gravatar.com/avatar.php?gravatar_id=' . md5($email) . '&amp;default=' . urlencode($defaultAvatar) . '&amp;size=' . $avatarSize;
		} else {
			switch ($avatarSize) {
				case 1:
					$imgSource = 'http://www.gravatar.com/avatar.php?gravatar_id=' . md5($email) . '&amp;default=' . urlencode($defaultAvatar) . '&amp;size=18';
					break;
				case 2:
					$imgSource = 'http://www.gravatar.com/avatar.php?gravatar_id=' . md5($email) . '&amp;default=' . urlencode($defaultAvatar) . '&amp;size=26';
					break;
				default:
					$imgSource = 'http://www.gravatar.com/avatar.php?gravatar_id=' . md5($email) . '&amp;default=' . urlencode($defaultAvatar) . '&amp;size=42';
					break;
			}
		}
		
		return $imgSource;
	}
	
	/**
	 * Get Community Builder avatar
	 * 
	 * @param integer $userID User id
	 * 
	 * @return mixed Avatar location if it exists, otherwise false
	 */
	static function getAvatarCB($userID)
	{
		// Load the template name from the database
		$db = Factory::getDBO();
		
		$sql = "SELECT `avatar` FROM #__comprofiler WHERE `user_id`='{$userID}' AND `avatarapproved`='1'";
		
		$db->setQuery($sql);
		$imgName = $db->loadResult();
		if ($imgName) {
			if (file_exists(JPATH_SITE . '/components/com_comprofiler/images/' . $imgName)) {
				$imgPath = JURI::root() . 'components/com_comprofiler/images/' . $imgName;
				return $imgPath;
			} else if (file_exists(JPATH_SITE . '/images/comprofiler/' . $imgName)) {
				$imgPath = JURI::root() . 'images/comprofiler/' . $imgName;
				return $imgPath;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * Get JomSocial avatar
	 * 
	 * @param integer $userID User id
	 * 
	 * @return string Avatar location
	 */
	static function getAvatarJomSocial($userID)
	{
		$jspath = JPATH_ROOT . DS . 'components' . DS . 'com_community';
		include_once $jspath . DS . 'libraries' . DS . 'core.php';
		include_once $jspath . DS . 'helpers' . DS . 'url.php';
		
		// Get CUser object
		$user = CFactory::getUser($userID);
		$avatar = array();
		$avatarUrl = $user->getThumbAvatar();
		$avatarUrl = str_replace("/administrator/", "/", $avatarUrl);
		$avatar[] = $avatarUrl;
		$avatar[] = cUserLink($userID);
		
		return $avatar;
	}
	
	/**
	 * Get K2 avatar
	 * 
	 * @param integer $userID 		 User id
	 * @param string  $email		 User e-mail
	 * @param integer $avatarSize	 Avatar size
	 * @param integer $ismodule		 Comment from module or others
	 * 
	 * @return string Avatar location
	 */
	static function getAvatarK2($userID, $email, $avatarSize, $ismodule)
	{
		$app = Factory::getApplication();
		$db = Factory::getDBO();
		
		if (file_exists(JPATH_SITE . DS . 'components' . DS . 'com_k2' . DS . 'helpers' . DS . 'utilities.php')) {
			if ($app->isClient('administrator')) {
				$query = 'SELECT * FROM #__k2_users WHERE userID = ' . $userID;
				$db->setQuery($query);
				$row = $db->loadObject();
				
				if (isset($row->image)) {
					$imgSource = JURI::root() . 'media/k2/users/' . $row->image;
				}
				else {
					$imgSource = JURI::root() . 'components/com_k2/images/placeholder/user.png';
				}
			}
			else {
				include_once JPATH_SITE . DS . 'components' . DS . 'com_k2' . DS . 'helpers' . DS . 'utilities.php';
				
				$imgSource = false;
				if ($ismodule) {
					$imgSource = K2HelperUtilities::getAvatar($userID, $email);
				} else {
					switch ($avatarSize) {
						case 1:
							$imgSource = K2HelperUtilities::getAvatar($userID, $email, 18);
							break;
						case 2:
							$imgSource = K2HelperUtilities::getAvatar($userID, $email, 26);
							break;
						default:
							$imgSource = K2HelperUtilities::getAvatar($userID, $email, 42);
							break;
					}
				}
			}
			
			return $imgSource;
		}
		
		return false;
	}
	
	/**
	 * Get Alpha User Points avatar
	 * 
	 * @param integer $userID 		 User id
	 * @param integer $avatarSize	 Avatar size
	 * @param integer $ismodule		 Comment from module or others
	 * 
	 * @return string Avatar location
	 */
	static function getAvatarAUP($userID, $avatarSize, $ismodule)
	{
		if (file_exists(JPATH_SITE . DS . 'components' . DS . 'com_alphauserpoints' . DS . 'helper.php')) {
			include_once JPATH_SITE . DS . 'components' . DS . 'com_alphauserpoints' . DS . 'helper.php';
			$imgSource = false;
			if ($ismodule) {
				$imgSource = AlphaUserPointsHelper::getAupAvatar($userID, 0);
			} else {
				switch ($avatarSize) {
					case 1:
						$imgSource = AlphaUserPointsHelper::getAupAvatar($userID, 0, 18);
						break;
					case 2:
						$imgSource = AlphaUserPointsHelper::getAupAvatar($userID, 0, 26);
						break;
					default:
						$imgSource = AlphaUserPointsHelper::getAupAvatar($userID, 0, 42);
						break;
				}
			}
			
			$re = '/src=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/is';
			if (preg_match($re, $imgSource, $match)) {
				return urldecode($match[2]);
			}
			return false;
		}
		
		return false;
	}
	
	/**
	 * Get Easy Blog avatar
	 * 
	 * @param integer $userID User id
	 * 
	 * @return string Avatar location
	 */
	static function getAvatarEasyBlog($userID)
	{
		if (file_exists(JPATH_SITE . DS . 'components' . DS . 'com_easyblog' . DS . 'helpers' . DS . 'helper.php')) {
			include_once JPATH_ROOT . DS . 'components' . DS . 'com_easyblog' . DS . 'helpers' . DS . 'helper.php';
			
			$profile = EasyBlogHelper::getTable('Profile', 'Table');
			$profile->load($userID);
			return $profile->getAvatar();
		}
		
		return false;
	}
	
	/**
	 * Get upload file size
	 * 
	 * @param string $action Action when uploading file
	 * 
	 * @return mixed Maximum file size in bytes or megabytes
	 */
	function getSizeUploadFile($action = '')
	{
		global $jacconfig;
		$maxSizeServer = (int) $this->checkUploadSize();
		$maxSize = $jacconfig["comments"]->get("max_size_attach_file", $maxSizeServer);
		$maxSizeAttach = min($maxSize, $maxSizeServer);
		if ($action) {
			return min($maxSize, $maxSizeServer) * 1000000;
		} else {
			return min($maxSize, $maxSizeServer) . "M";
		}
	}
	
	/**
	 * Check upload maximum file size
	 * 
	 * @return integer Maximum upload file size
	 */
	function checkUploadSize()
	{
		if (! $filesize = ini_get('upload_max_filesize')) {
			$filesize = "5M";
		}
		
		if ($postsize = ini_get('post_max_size')) {
			return min($filesize, $postsize);
		} else {
			return $filesize;
		}
	}
	
	/**
	 * Replace prefix for table in SQL query
	 *  
	 * @param string $sql	 SQL query
	 * @param string $prefix Prefix string
	 * 
	 * @return string SQL query after replacing prefix
	 */
	public static function jareplacePrefix($sql, $prefix='#__')
	{
		$jconfig = new JConfig();
		$ablePrefix = $jconfig->dbprefix;
		// Initialize variables.
		$escaped = false;
		$startPos = 0;
		$quoteChar = '';
		$literal = '';

		$sql = trim($sql);
		$n = strlen($sql);

		while ($startPos < $n) {
			$ip = strpos($sql, $prefix, $startPos);
			if ($ip === false) {
				break;
			}

			$j = strpos($sql, "'", $startPos);
			$k = strpos($sql, '"', $startPos);
			if (($k !== false) && (($k < $j) || ($j === false))) {
				$quoteChar = '"';
				$j = $k;
			} else {
				$quoteChar = "'";
			}

			if ($j === false) {
				$j = $n;
			}

			$literal .= str_replace($prefix, $ablePrefix, substr($sql, $startPos, $j - $startPos));
			$startPos = $j;

			$j = $startPos + 1;

			if ($j >= $n) {
				break;
			}

			// quote comes first, find end of quote
			while (true) {
				$k = strpos($sql, $quoteChar, $j);
				$escaped = false;
				if ($k === false) {
					break;
				}
				$l = $k - 1;
				while ($l >= 0 && $sql[$l] == '\\') {
					$l--;
					$escaped = !$escaped;
				}
				if ($escaped) {
					$j = $k+1;
					continue;
				}
				break;
			}
			if ($k === false) {
				// error in the query - no end quote; ignore it
				break;
			}
			$literal .= substr($sql, $startPos, $k - $startPos + 1);
			$startPos = $k+1;
		}
		if ($startPos < $n) {
			$literal .= substr($sql, $startPos, $n - $startPos);
		}

		return $literal;
	}
	
	/**
	 * Get article author of content
	 *  
	 * @param string $contentOption	Is com_content or com_k2
	 * @param string $contentId		Content id
	 * 
	 * @return integer Article author id
	 */
	function getArticleAuthor($contentOption, $contentId)
	{
		$db = Factory::getDBO();
		
		if ($contentOption == 'com_content') {
			$query = "SELECT created_by 
					\n FROM #__content 
					\n WHERE id = '$contentId'";
		} else if ($contentOption == 'com_k2') {
			$query = "SELECT created_by 
					\n FROM #__k2_items 
					\n WHERE id = '$contentId'";
		}
		else {
			return 0;
		}
		
		$db->setQuery($query);
		$authorId = $db->loadResult();
		
		return intval($authorId);
	}
	
	/**
	 * Show comment form in 3rd party component API
	 *  
	 * @param string $option		Component name
	 * @param string $content_id	Content id
	 * @param string $content_title	Content title
	 * 
	 * @return void
	 */
	/*
	Usage:
		$api_JAC = JPATH_SITE.DS.'components'.DS.'com_jacomment'.DS.'helpers'.DS.'jahelper.php';
		if (file_exists($api_JAC)) {
			require_once($api_JAC);
			JACommentHelpers::showComments('<option>', '<content_id>', '<content_title>');
		}
	*/
	public static function showComments($option, $content_id, $content_title)
	{
		$plgPath = JPATH_PLUGINS.DS.'system'.DS.'system_jacomment';
		if (JFolder::exists($plgPath)) {
			if (JPluginHelper::isEnabled('system', 'system_jacomment')) {
				echo '{jacomment contentid='.$content_id.' option='.$option.' contenttitle='.$content_title.'}';
			}
			else {
				echo '<p style="color:#FF0000;">Please enable JA Comment system plug-in first.</p>';
			}
		}
		else {
			echo '<p style="color:#FF0000;">Please install JA Comment system plug-in first.</p>';
		}
		return;
	} 
	
	/**
	 * Get authors of conversation
	 *  
	 * @param string	$contentOption	Component name
	 * @param integer 	$contentId		Content id
	 * 
	 * @return string Author id or name in 2 groups: registered and guest
	 */
	function getConversationAuthors($contentOption, $contentId)
	{
		$db = Factory::getDBO();
		
		$contentOption = $db->Quote($contentOption);
		$contentId = $db->Quote($contentId);
		
		$query = "SELECT DISTINCT userid, `name` FROM #__jacomment_items WHERE `option` = $contentOption AND `contentid` = $contentId AND `type` = '1'";
		$db->setQuery($query);
		$tmpList = $db->loadObjectList();
		
		if ($tmpList) {
			$authors = new stdClass();
			$authors->registered = array();
			$authors->guest = array();
			
			foreach ($tmpList as $tmpItem) {
				if ($tmpItem->userid == 0) {
					$authors->guest[] = $tmpItem->name;
				}
				else {
					$authors->registered[] = $tmpItem->userid;
				}
			}
		}
		else {
			$authors = NULL;
		}
		
		return $authors;
	}
	
	/**
	 * Get author name
	 *  
	 * @param integer	$authorId	Author id
	 * 
	 * @return string Author name
	 */
	function getAuthorName($authorId)
	{
		$db = Factory::getDBO();
		
		$authorId = $db->Quote($authorId);
		
		$query = "SELECT `name` FROM #__users WHERE `id` = $authorId";
		$db->setQuery($query);
		$authorName = $db->loadResult();
		
		return $authorName;
	}
	
	/**
	 * Get author email
	 *  
	 * @param integer	$authorId	Author id (0 for guest)
	 * $param string	$authorName	Author name (used for guest)
	 * 
	 * @return string Author email
	 */
	function getAuthorEmail($authorId, $authorName = '')
	{
		$db = Factory::getDBO();
		
		$authorId = $db->Quote($authorId);
		$authorName = $db->Quote($authorName);
		
		if ($authorName == '') {
			$query = "SELECT `email` FROM #__jacomment_items WHERE `id` = $authorId";
		}
		else {
			$query = "SELECT `email` FROM #__jacomment_items WHERE `name` = $authorName";
		}
		$db->setQuery($query);
		$authorEmail = $db->loadResult();
		
		return $authorEmail;
	}
}

//--------------------------------------------------------------BEGIN - license -------------------------------------------
/**
 * JACommentLicense class
 *
 * @package		Joomla.Site
 * @subpackage	JAComment
 */
class JACommentLicense
{
	var $host = 'www.joomlart.com';
	var $path = "/member/jaeclicense.php";
	
	/**
	 * Verify license
	 * 
	 * @param string $email 	 User e-mail
	 * @param string $payment_id Payment id
	 * 
	 * @return void
	 */
	function verify_license($email = '', $payment_id = '')
	{
		$app = Factory::getApplication();
		$inputs = Factory::getApplication()->input;
		$post = $inputs->get('request', JREQUEST_ALLOWHTML);
		if ($email == '') {
			$email = isset($post['email']) ? trim($post['email']) : '';
		}
		if ($payment_id == '') {
			$payment_id = isset($post['payment_id']) ? trim($post['payment_id']) : '';
		}
		
		$domain = $_SERVER['HTTP_HOST'];
		$base = $app->getSiteUrl();
		
		if (! $email || ! $domain || ! $payment_id) {
			//JError::raiseWarning(1, JText::_('PLEASE_CHECK_YOUR_INPUT_DATA'));
			Factory::getApplication()->enqueueMessage(
				JText::_('PLEASE_CHECK_YOUR_INPUT_DATA'),
				'warning'
			);
			return;
		}
		
		if (strtolower(substr($domain, 0, 3)) == 'www') {
			$domain = substr($domain, strpos($domain, '.') + 1);
		}
		
		$req = 'domain=' . $domain;
		$req .= '&email=' . rawurlencode($email);
		$req .= '&payment_id=' . rawurlencode($payment_id);
		$req .= '&action=verify_license';
		
		$URL = "http://{$this->host}{$this->path}";
		
		if (! function_exists('curl_version')) {
			if (! ini_get('allow_url_fopen')) {
				//JError::raiseWarning(1, JText::_('BUT_YOUR_SERVER_DOES_NOT_CURRENTLY_SUPPORT_OPEN_METHOD') . '.');
				Factory::getApplication()->enqueueMessage(
					JText::_('BUT_YOUR_SERVER_DOES_NOT_CURRENTLY_SUPPORT_OPEN_METHOD'),
					'warning'
				);
				return;
			} else {
				$result = $this->socket_getdata($req, $this->path, $this->host);
			}
		} else {
			$result = $this->curl_getdata($URL, $req);
		}
		
		if (! $result) {
			//Not connected to server			
			//JError::raiseWarning(1, JText::_('YOUR_LICENSE_KEY_COULD_NOT_BE_VERIFIED' . '. <a href="http://joomlart.com"> ' . JText::_('OR_CONTACT_JOOMLART') . ' ' . JText::_('FOR_FURTHER_ASSISTANCE') . '.</a>'));
			Factory::getApplication()->enqueueMessage(
				JText::_('YOUR_LICENSE_KEY_COULD_NOT_BE_VERIFIED' . '. <a href="http://joomlart.com"> ' . JText::_('OR_CONTACT_JOOMLART') . ' ' . JText::_('FOR_FURTHER_ASSISTANCE') . '.</a>'),
				'warning'
			);

		} else {
			if (function_exists("json_decode")) {
				$result = json_decode($result, true);
			} else {
				include_once JPATH_ROOT . "/components/com_jacomment/libs/JSON.php";
				$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
				$result = $json->decode($result);
			}
			$statusMes = $result["status"];
			
			switch ($statusMes) {
				case 'invalid_domain':
					$this->updateFail();
					//JError::raiseWarning(1, JText::_('YOUR_DOMAIN_IS_NOT_ACCEPTED') . ' <a href="http://joomlart.com">JoomlArt</a> ' . JText::_('FOR_FURTHER_ASSISTANCE'));
					Factory::getApplication()->enqueueMessage(
						JText::_('YOUR_DOMAIN_IS_NOT_ACCEPTED') . ' <a href="http://joomlart.com">JoomlArt</a> ' . JText::_('FOR_FURTHER_ASSISTANCE'),
						'warning'
					);
					return;
					break;
				
				case 'expired':
					$this->updateFail();
					//JError::raiseWarning(1, JText::_('YOUR_LICENSE_HAS_EXPIRED') . ' ' . JText::_('PLEASE_CONTACT') . ' <a href="http://joomlart.com">JoomlArt</a> ' . JText::_('FOR_FURTHER_ASSISTANCE'));
					Factory::getApplication()->enqueueMessage(
						JText::_('YOUR_LICENSE_HAS_EXPIRED') . ' ' . JText::_('PLEASE_CONTACT') . ' <a href="http://joomlart.com">JoomlArt</a> ' . JText::_('FOR_FURTHER_ASSISTANCE'),
						'warning'
					);
					return;
					break;
				
				case 'invalid_payment_id':
					$this->updateFail();
					//JError::raiseWarning(1, JText::_('YOUR_PAYMENT_IS_NOT_CORRECTED_FOR_THIS_PRODUCT') . ' ' . JText::_('PLEASE_CONTACT') . ' <a href="http://joomlart.com">JoomlArt</a> ' . JText::_('FOR_FURTHER_ASSISTANCE'));
					Factory::getApplication()->enqueueMessage(
						JText::_('YOUR_PAYMENT_IS_NOT_CORRECTED_FOR_THIS_PRODUCT') . ' ' . JText::_('PLEASE_CONTACT') . ' <a href="http://joomlart.com">JoomlArt</a> ' . JText::_('FOR_FURTHER_ASSISTANCE'),
						'warning'
					);
					return;
					break;
				
				case 'payment_not_completed':
					$this->updateFail();
					//JError::raiseWarning(1, JText::_('YOUR_PAYMENT_IS_NOT_COMPLETED') . ' ' . JText::_('PLEASE_CONTACT') . ' <a href="http://joomlart.com">JoomlArt</a> ' . JText::_('FOR_FURTHER_ASSISTANCE'));
					Factory::getApplication()->enqueueMessage(
						JText::_('YOUR_PAYMENT_IS_NOT_COMPLETED') . ' ' . JText::_('PLEASE_CONTACT') . ' <a href="http://joomlart.com">JoomlArt</a> ' . JText::_('FOR_FURTHER_ASSISTANCE'),
						'warning'
					);
					return;
					break;
				
				case 'disabled_domain':
					$this->updateFail();
					//JError::raiseWarning(1, JText::_('YOUR_DOMAIN_IS_DISABLED'));
					Factory::getApplication()->enqueueMessage(
						JText::_('YOUR_DOMAIN_IS_DISABLED'),
						'warning'
					);
					return;
					break;
				
				case 'limited_domain':
					$this->updateFail();
					//JError::raiseWarning(1, JText::_('LIMITED_DOMAIN'));
					Factory::getApplication()->enqueueMessage(
						JText::_('LIMITED_DOMAIN'),
						'warning'
					);
					return;
					break;
				
				case 'invalid_member':
					$this->updateFail();
					//JError::raiseWarning(1, JText::_('YOUR_PAYMENT_IS_NOT_CORRECTED_FOR_THIS_MEMBER') . ' ' . JText::_('PLEASE_CONTACT') . ' <a href="http://joomlart.com">JoomlArt</a> ' . JText::_('FOR_FURTHER_ASSISTANCE'));
					Factory::getApplication()->enqueueMessage(
						JText::_('YOUR_PAYMENT_IS_NOT_CORRECTED_FOR_THIS_MEMBER') . ' ' . JText::_('PLEASE_CONTACT') . ' <a href="http://joomlart.com">JoomlArt</a> ' . JText::_('FOR_FURTHER_ASSISTANCE'),
						'warning'
					);
					return;
					break;
				
				case 'successful':
					$this->updateSuccess($payment_id, $email, $result["product_type"]);
					$app->redirect('index.php?option=com_jacomment&view=comment&layout=licenseandsupport');
					break;
				case 'error':
				default:
					//JError::raiseWarning(1, JText::_('HAVE_AN_ERROR_WHEN_PROCESSING'));
					Factory::getApplication()->enqueueMessage(
						JText::_('HAVE_AN_ERROR_WHEN_PROCESSING'),
						'warning'
					);
					return;
					break;
			}
		}
	}
	
	/**
	 * Update fail function
	 * 
	 * @return void
	 */
	function updateFail()
	{
		$db = Factory::getDBO();
		$query = "SELECT data FROM #__jacomment_configs WHERE `group`='license'";
		$db->setQuery($query);
		$data = $db->loadResult();
		if (! $data) {
			$query = "INSERT INTO  #__jacomment_configs (`group`, data) VALUES ('license', 'verify_is_passed=0')";
		} else {
			$data = explode("\n", $data);
			$str = "";
			foreach ($data as $item) {
				if (strpos($item, "verify_is_passed" !== false)) {
					$item = "verify_is_passed=0";
				}
				$str = $item . "\n";
			}
			$db = Factory::getDBO();
			$query = "UPDATE  #__jacomment_configs SET data = '" . $str . "' WHERE  group = 'license'";
			$db->setQuery($query);
			$db->execute();
		}
		$_SESSION['JACOMMENT_VERIFY_PASSED'] = 0;
	}
	
	/**
	 * Update successfully function
	 * 
	 * @param string $payment_id Payment id
	 * @param string $email 	 User e-mail
	 * @param string $type		 Product type
	 * 
	 * @return void
	 */
	function updateSuccess($payment_id, $email, $type)
	{
		$db = Factory::getDBO();
		$create_date = date('Y-m-d H:i:s');
		$last_verify = date('Y-m-d H:i:s');
		
		$query = "SELECT data FROM #__jacomment_configs WHERE `group`='license'";
		$db->setQuery($query);
		$data = $db->loadObjectList();
		$str = "payment_id=" . $payment_id . "\nemail=" . $email . "\ncreate_date=" . $create_date . "\nlast_verify=" . $last_verify . "\nverify_is_passed=1";
		
		if (! $data) {
			$query = "INSERT INTO #__jacomment_configs (`group`, data) VALUES ('license', '" . $str . "')";
			
			$db->setQuery($query);
			$db->execute();
		} else {
			$db = Factory::getDBO();
			$query = "UPDATE #__jacomment_configs SET data = '" . $str . "' WHERE `group` = 'license'";
			$db->setQuery($query);
			$db->execute();
		}
		
		$_SESSION['JATOOLBAR_VERIFY_PASSED'] = 1;
	}
	
	/**
	 * Get data using cURL
	 *
	 * @param string $URL URL to get data
	 * @param array  $req Post request
	 * 
	 * @return object Return data
	 */
	static function curl_getdata($URL, $req)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, $URL);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);
		
		return $result;
	
	}
	
	/**
	 * Get data using socket
	 *
	 * @param string $host Host to get data
	 * @param string $path Path to get data
	 * @param array  $req  Post request
	 * 
	 * @return object Return data
	 */
	static function socket_getdata($host, $path, $req)
	{
		$header = "POST $path HTTP/1.0\r\n";
		$header .= "Host: " . $host . "\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "User-Agent:      Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1) Gecko/20061010 Firefox/2.0\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
		$header .= $req;
		$fp = @fsockopen($host, 80, $errno, $errstr, 60);
		if (! $fp) {
			return;
		}
		@fwrite($fp, $header);
		$data = '';
		$i = 0;
		do {
			$header .= @fread($fp, 1);
		} while (! preg_match('/\\r\\n\\r\\n$/', $header));
		
		while (! @feof($fp)) {
			$data .= @fgets($fp, 128);
		}
		fclose($fp);
		return $data;
	}
}
//--------------------------------------------------------------END - license -------------------------------------------


if (! class_exists('JACSmartTrim')) {
	/**
	 * JACSmartTrim class
	 *
	 * @package		Joomla.Site
	 * @subpackage	JAComment
	 */
	class JACSmartTrim
	{
		/**
		 * Multi-byte trimming function
		 * 
		 * @param string  $strin		 String to trim
		 * @param integer $pos			 Position to start trimming
		 * @param integer $len			 Length to trim
		 * @param string  $hiddenClasses Class that have property display: none or invisible
		 * @param string  $encoding		 Encoding of string
		 * 
		 * @return string String after trimming
		 */
		function mb_trim($strin, $pos = 0, $len = 10000, $hiddenClasses = '', $encoding = 'utf-8')
		{
			mb_internal_encoding($encoding);
			$strout = trim($strin);
			
			$pattern = '/(<[^>]*>)/';
			$arr = preg_split($pattern, $strout, - 1, PREG_SPLIT_DELIM_CAPTURE);
			$left = $pos;
			$length = $len;
			$strout = '';
			for ($i = 0; $i < count($arr); $i++) {
				$arr[$i] = trim($arr[$i]);
				if ($arr[$i] == '') {
					continue;
				}
				if ($i % 2 == 0) {
					if ($left > 0) {
						$t = $arr[$i];
						$arr[$i] = mb_substr($t, $left);
						$left -= (mb_strlen($t) - mb_strlen($arr[$i]));
					}
					
					if ($left <= 0) {
						if ($length > 0) {
							$t = $arr[$i];
							$arr[$i] = mb_substr($t, 0, $length);
							$length -= mb_strlen($arr[$i]);
							if ($length <= 0) {
								$arr[$i] .= '...';
							}
						
						} else {
							$arr[$i] = '';
						}
					}
				} else {
					if (JACSmartTrim::isHiddenTag($arr[$i], $hiddenClasses)) {
						if ($endTag = JACSmartTrim::getCloseTag($arr, $i)) {
							while ($i < $endTag) {
								$strout .= $arr[$i++] . "\n";
							}
						}
					}
				}
				$strout .= $arr[$i] . "\n";
			}
			//echo $strout;  
			return JACSmartTrim::toString($arr, $len);
		}
		
		/**
		 * Trim function
		 * 
		 * @param string  $strin		 String to trim
		 * @param integer $pos			 Position to start trimming
		 * @param integer $len			 Length to trim
		 * @param string  $hiddenClasses Class that have property display: none or invisible
		 * 
		 * @return string String after trimming
		 */
		function trim($strin, $pos = 0, $len = 10000, $hiddenClasses = '')
		{
			$strout = trim($strin);
			
			$pattern = '/(<[^>]*>)/';
			$arr = preg_split($pattern, $strout, - 1, PREG_SPLIT_DELIM_CAPTURE);
			$left = $pos;
			$length = $len;
			$strout = '';
			for ($i = 0; $i < count($arr); $i++) {
				$arr[$i] = trim($arr[$i]);
				if ($arr[$i] == '') {
					continue;
				}
				if ($i % 2 == 0) {
					if ($left > 0) {
						$t = $arr[$i];
						$arr[$i] = substr($t, $left);
						$left -= (strlen($t) - strlen($arr[$i]));
					}
					
					if ($left <= 0) {
						if ($length > 0) {
							$t = $arr[$i];
							$arr[$i] = substr($t, 0, $length);
							$length -= strlen($arr[$i]);
							if ($length <= 0) {
								$arr[$i] .= '...';
							}
						
						} else {
							$arr[$i] = '';
						}
					}
				} else {
					if (JACSmartTrim::isHiddenTag($arr[$i], $hiddenClasses)) {
						if ($endTag = JACSmartTrim::getCloseTag($arr, $i)) {
							while ($i < $endTag) {
								$strout .= $arr[$i++] . "\n";
							}
						}
					}
				}
				$strout .= $arr[$i] . "\n";
			}
			//echo $strout;  
			return JACSmartTrim::toString($arr, $len);
		}
		
		/**
		 * Check if it is a hidden tag
		 * 
		 * @param string $tag			HTML tag
		 * @param string $hiddenClasses Class that have property display: none or invisible
		 * 
		 * @return boolean True if it is a hidden tag and vice versa
		 */
		static function isHiddenTag($tag, $hiddenClasses = '')
		{
			//By pass full tag like img
			if (substr($tag, - 2) == '/>') {
				return false;
			}
			if (in_array(JACSmartTrim::getTag($tag), array('script', 'style'))) {
				return true;
			}
			if (preg_match('/display\s*:\s*none/', $tag)) {
				return true;
			}
			if ($hiddenClasses && preg_match('/class\s*=[\s"\']*(' . $hiddenClasses . ')[\s"\']*/', $tag)) {
				return true;
			}
		}
		
		/**
		 * Get close tag
		 * 
		 * @param array	  $arr	   Array of tags
		 * @param integer $openidx Index of open tag
		 * 
		 * @return integer Index of end tag
		 */
		static function getCloseTag($arr, $openidx)
		{
			$tag = trim($arr[$openidx]);
			if (! $openTag = JACSmartTrim::getTag($tag)) {
				return 0;
			}
			
			$endTag = "</$openTag>";
			$endidx = $openidx + 1;
			$i = 1;
			while ($endidx < count($arr)) {
				if (trim($arr[$endidx]) == $endTag) {
					$i--;
				}
				if (JACSmartTrim::getTag($arr[$endidx]) == $openTag) {
					$i++;
				}
				if ($i == 0) {
					return $endidx;
				}
				$endidx++;
			}
			return 0;
		}
		
		/**
		 * Get tag
		 * 
		 * @param string $tag Tag to get
		 * 
		 * @return string Return tag if it is found, otherwise return empty string 
		 */
		static function getTag($tag)
		{
			if (preg_match('/\A<([^\/>]*)\/>\Z/', trim($tag), $matches)) {
				// full tag
				return '';
			}
			if (preg_match('/\A<([^ \/>]*)([^>]*)>\Z/', trim($tag), $matches)) {
				return strtolower($matches[1]);
			}
			
			return '';
		}
		
		/**
		 * Write to string
		 * 
		 * @param array   $arr Array of tags
		 * @param integer $len Length to get string
		 * 
		 * @return string String after processing
		 */
		static function toString($arr, $len)
		{
			$i = 0;
			$stack = new JACStack();
			$length = 0;
			while ($i < count($arr)) {
				$tag = trim($arr[$i++]);
				if ($tag == '') {
					continue;
				}
				if (JACSmartTrim::isCloseTag($tag)) {
					if ($ltag = $stack->getLast()) {
						if ('</' . JACSmartTrim::getTag($ltag) . '>' == $tag) {
							$stack->pop();
						} else {
							$stack->push($tag);
						}
					}
				} else if (JACSmartTrim::isOpenTag($tag)) {
					$stack->push($tag);
				} else if (JACSmartTrim::isFullTag($tag)) {
					if ($length < $len) {
						$stack->push($tag);
					}
				} else {
					$length += strlen($tag);
					$stack->push($tag);
				}
			}
			
			return $stack->toString();
		}
		
		/**
		 * Check if a tag is open tag or not
		 * 
		 * @param string $tag Tag to check
		 * 
		 * @return boolean True if tag is open tag, otherwise false
		 */
		static function isOpenTag($tag)
		{
			if (preg_match('/\A<([^\/>]+)\/>\Z/', trim($tag), $matches)) {
				// full tag
				return false;
			}
			if (preg_match('/\A<([^ \/>]+)([^>]*)>\Z/', trim($tag), $matches)) {
				return true;
			}
			return false;
		}
		
		/**
		 * Check if a tag is full tag or not
		 * 
		 * @param string $tag Tag to check
		 * 
		 * @return boolean True if a tag is full tag, otherwise false
		 */
		static function isFullTag($tag)
		{
			if (preg_match('/\A<([^\/>]*)\/>\Z/', trim($tag), $matches)) {
				// full tag
				return true;
			}
			return false;
		}
		
		/**
		 * Check if a tag is close tag or not
		 * 
		 * @param string $tag Tag to check
		 * 
		 * @return boolean True if a tag if close tag, otherwise false
		 */
		static function isCloseTag($tag)
		{
			if (preg_match('/<\/(.*)>/', $tag)) {
				return true;
			}
			return false;
		}
	}
}

if (! class_exists('JACStack')) {
	/**
	 * JACStack class
	 *
	 * @package		Joomla.Site
	 * @subpackage	JAComment
	 */
	class JACStack
	{
		var $_arr = null;
		
		/**
		 * Constructor
		 * 
		 * @return void
		 */
		function __construct()
		{
			$this->_arr = array();
		}
		
		/**
		 * Push function
		 * 
		 * @param object $item Item to push to stack
		 * 
		 * @return void
		 */
		function push($item)
		{
			$this->_arr[count($this->_arr)] = $item;
		}
		
		/**
		 * Pop function
		 * 
		 * @return object Item from stack
		 */
		function pop()
		{
			if (! $c = count($this->_arr)) {
				return null;
			}
			$ret = $this->_arr[$c - 1];
			unset($this->_arr[$c - 1]);
			return $ret;
		}
		
		/**
		 * Get last item
		 * 
		 * @return object Last item
		 */
		function getLast()
		{
			if (! $c = count($this->_arr)) {
				return null;
			}
			return $this->_arr[$c - 1];
		}
		
		/**
		 * Write stack content to string
		 * 
		 * @return string Stack content
		 */
		function toString()
		{
			$output = '';
			foreach ($this->_arr as $item) {
				$output .= $item . "\n";
			}
			return $output;
		}
	}
}

if (! class_exists('extractor')) {
	/**
	 * Extractor class
	 *
	 * @package		Joomla.Site
	 * @subpackage	JAComment
	 */
	class extractor
	{
		var $cookiefile;
		var $timeout;
		var $error;
		var $hdr;
		var $status;
		var $proxyaddr;
		var $proxyport;
		var $proxyuser;
		var $proxypass;
		
		/**
		 * Constructor
		 * 
		 * @param boolean $cookies 		  True if cookie exist
		 * @param integer $timeout 		  Cookie timeout
		 * @param string  $sesscookiefile Cookie file
		 * 
		 * @return void
		 */
		function __construct($cookies = false, $timeout = 5, $sesscookiefile = "")
		{
			$this->timeout = $timeout;
			if ($cookies) {
				if ($mycookiefile) {
					$this->cookiefile = "cookies/" . $sesscookiefile;
					if (! is_file($this->cookiefile)) {
						$fp = fopen($this->cookiefile, "w");
						fclose($fp);
					}
				} else {
					$this->cookiefile = tempnam("tmp", "EXT");
				}
			}
			
			$this->cleanupOldCookies();
		}
		
		/**
		 * Get data function
		 * 
		 * @param string  $url 					 URL to get data
		 * @param array   $post 				 Post request
		 * @param string  $referer 				 Referer URL
		 * @param boolean $setcookie 			 Set cookie or not
		 * @param boolean $usecookie 			 Use cookie or not
		 * @param string  $useragent 			 User agent
		 * @param boolean $alternate_post_format Alternate post format or not
		 * 
		 * @return array Data
		 */
		function getdata($url, $post = array(), $referer = "", $setcookie = false, $usecookie = false, $useragent = "", $alternate_post_format = false)
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			
			if ($proxyport && $proxyaddr) {
				curl_setopt($ch, CURLOPT_PROXY, trim($proxyaddr) . ":" . trim($proxyport));
			}
			if ($proxyuser && $proxypass) {
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, trim($proxyuser) . ":" . trim($proxypass));
			}
			
			if ($setcookie) {
				curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookiefile);
			}
			if ($usecookie) {
				curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiefile);
			}
			if ($this->timeout) {
				curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
			}
			
			if ($referer) {
				curl_setopt($ch, CURLOPT_REFERER, trim($referer));
			} else {
				curl_setopt($ch, CURLOPT_REFERER, trim($url));
			}
			
			if (trim($useragent)) {
				curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
			} else {
				curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0;Windows NT 5.1)");
			}
			
			if (substr_count($url, "https://")) {
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			}
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, $url);
			
			if (count($post)) {
				curl_setopt($ch, CURLOPT_POST, true);
				
				if ($alternate_post_format) {
					foreach ($post as $key => $val) {
						$str .= "$key=" . urlencode($val) . "&";
					}
					$str = substr($str, 0, - 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
				} else {
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
				}
			}
			
			$data = curl_exec($ch);
			$err = curl_error($ch);
			curl_close($ch);
			unset($ch);
			
			$theData = preg_split("/(\r\n){2,2}/", $data, 2);
			$showData = $theData[1];
			
			$this->error = $err;
			$this->hdr = $theData[0];
			$this->parseHeader($theData[0]);
			
			return $showData;
		}
		
		/**
		 * Parse header
		 * 
		 * @param string $theHeader The header
		 * 
		 * @return void
		 */
		function parseHeader($theHeader)
		{
			$theArray = preg_split("/(\r\n)+/", $theHeader);
			foreach ($theArray as $theHeaderString) {
				$theHeaderStringArray = preg_split("/\s*:\s*/", $theHeaderString, 2);
				if (preg_match('/^HTTP/', $theHeaderStringArray[0])) {
					$this->status = $theHeaderStringArray[0];
				}
			}
		}
		
		//--- this doesnt really belong here , but mostly when this class is used , i use this function as well , so i have placed it here
		/**
		 * Search in a string
		 * 
		 * @param integer $start   Start position to search
		 * @param integer $end	   End position to search
		 * @param string  $string  String to search
		 * @param boolean $borders Get string from border or not
		 * 
		 * @return string String if it is matched
		 */
		function search($start, $end, $string, $borders = true)
		{
			$reg = "!" . preg_quote($start) . "(.*?)" . preg_quote($end) . "!is";
			preg_match_all($reg, $string, $matches);
			
			if ($borders) {
				return $matches[0];
			} else {
				return $matches[1];
			}
		}
		
		/**
		 * Clear old cookies 
		 * 
		 * @return void
		 */
		function cleanupOldCookies()
		{
			$delbefore = 86400; ///-- delete cookies older than 1 day
			$tmpdir = "cookies";
			
			if ($dir = @opendir($tmpdir)) {
				while (($file = readdir($dir)) !== false) {
					if ($file != "." && $file != "..") {
						$stat = stat($tmpdir . "/" . $file);
						if ($stat[atime] < (mktime() - $delbefore)) {
							unlink($tmpdir . "/" . $file);
						}
					}
				}
				closedir($dir);
			}
		}
	}
}
?>