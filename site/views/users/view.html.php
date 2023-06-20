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

if (! defined('JAC_REGISTERED')) {
	JLoader::register('JACView', JPATH_BASE.'/components/com_jacomment/views/view.php');
}

use Joomla\CMS\Factory;

/**
 * JACommentViewUsers View
 *
 * @package		Joomla.Site
 * @subpackage	JAComment
 */
class JACommentViewUsers extends JACView
{
	/**
	 * Display the view
	 * 
	 * @param string $tmpl The template file to include
	 * 
	 * @return void
	 */
	function display($tmpl = null)
	{
		if ($this->getLayout() == "login") {
			$this->showLogin();
		}
		parent::display($tmpl);
	}
	
	/**
	 * Show login form
	 * 
	 * @return void
	 */
	function showLogin()
	{
		// form RPX Login
		if (JPluginHelper::isEnabled('system', 'janrain')) {
			$plg = JPluginHelper::getPlugin("system", "janrain");
			$plgparams = new JRegistry;
			$plgparams->loadString($plg->params);
			$post_data = array('apiKey' => $plgparams->get("apikey"));
			
			if (function_exists("curl_init")) {
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_URL, 'https://rpxnow.com/plugin/lookup_rp');
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				$raw_json = curl_exec($curl);
				curl_close($curl);
			} else if (ini_get('allow_url_fopen') && extension_loaded('openssl')) {
				$raw_json = file_get_contents("http://rpxnow.com/plugin/lookup_rp?apiKey=" . $post_data['apiKey'] . "&format=json");
			} else {
				Factory::getApplication()->enqueueMessage(
					JText::_('BUT_YOUR_SERVER_DOES_NOT_CURRENTLY_SUPPORT_OPEN_METHOD'),
					'error'
				);
				//JError::raiseWarning(1, JText::_('BUT_YOUR_SERVER_DOES_NOT_CURRENTLY_SUPPORT_OPEN_METHOD') . '.');
				return;
			}
			
			if (! function_exists('json_decode')) {
				/**
				 * Very plain json_decode
				 * 
				 * @param string  $str	  String to decode
				 * @param boolean $ignore Ignore data in list or not
				 * 
				 * @return array Decoded data
				 */
				function json_decode($str, $ignore = true)
				{
					$str = trim($str);
					if (! preg_match('#^\{(("[\w]+":"[^"]*",?)*)\}$#i', $str, $m)) {
						return array();
					}
					$data = explode('","', substr($m[1], 1, - 1));
					$ret = array();
					for ($i = 0; $i < count($data); $i++) {
						list($k, $v) = explode(':', $data[$i], 2);
						$ret[substr($k, 0, - 1)] = substr($v, 1);
					}
					
					return $ret;
				}
			}
			// parse the json response into an associative array
			$json = json_decode($raw_json, true);
			$realm = $json['realm'];
			
			$application = $json['realm'];
			
			$token_url = urlencode($_SERVER['HTTP_REFERER']);
			
			$this->application = $application;
			$this->token_url = $token_url;
			
			$_SESSION['ses_url'] = $_SERVER['HTTP_REFERER'];
		
		}
	}
}
?>