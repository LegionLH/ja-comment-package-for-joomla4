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
defined('_JEXEC') or die();

if (! defined('JAC_REGISTERED')) {
	JLoader::register('JACModel', JPATH_ADMINISTRATOR.'/components/com_jacomment/models/model.php');
}
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;

/**
 * This model is used for JAEmailtemplates feature of the component
 * 
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */
class JACommentModelEmailtemplates extends JACModel
{
	var $_data;
	var $_table;
	var $_pagination;
	
	/**
	 * Get email Item limit row
	 * 
	 * @param string $filter_lang Language filter
	 * 
	 * @return Email Table object
	 */
	function getItems($filter_lang = '')
	{
		$app = Factory::getApplication();
		$inputs = Factory::getApplication()->input;
		$option = $inputs->getCmd('option');
		$db = Factory::getDBO();
		$option_1 = $option . 'emailtemplates';
		$app = Factory::getApplication('administrator');
		$search = $app->getUserStateFromRequest($option_1 . '.search', 'search', '', 'string');
		$filter_order = $app->getUserStateFromRequest($option_1 . '.filter_order', 'filter_order', 'name', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest($option_1 . '.filter_order_Dir', 'filter_order_Dir', 'ASC', 'word');
		$filter_state = $app->getUserStateFromRequest($option_1 . '.filter_state', 'filter_state', '', 'word');
		
		if ($filter_lang == '') {
			$filter_lang = $app->getUserStateFromRequest($option_1 . '.filter_lang', 'filter_lang', 'en-GB', 'string');
		}
		
		$search = StringHelper::strtolower($search);
		$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir . ', id desc';
		
		$where = ' WHERE 1=1 ';
		if ($search) {
			$where .= " AND LOWER(`name`) like " . $db->Quote('%' . $db->getEscaped($search, true) . '%', false) . " or LOWER(`subject`) like " . $db->Quote('%' . $db->getEscaped($search, true) . '%', false);
		}
		if ($filter_state) {
			if ($filter_state == 'P') {
				$where .= ' AND published = 1';
			} else if ($filter_state == 'U') {
				$where .= ' AND published = 0';
			}
		}
		
		if ($filter_lang != '') {
			$where .= " AND language = '$filter_lang'";
		}
		
		$query = "SELECT  * " . "FROM 	`#__jacomment_email_templates` " . $where . $orderby;
		$db->setQuery($query);
		$items = $db->loadAssocList();
		return $items;
	}
	
	/**
	 * Remove email record
	 * 
	 * @return Number of removed records
	 */
	function remove()
	{
		$inputs = Factory::getApplication()->input;
		$cid = $inputs->get('cid', array(), 'post', 'array');
		$n = count($cid);
		ArrayHelper::toInteger($cid);
		
		if ($n) {
			$query = "DELETE FROM #__jacomment_email_templates WHERE id = " . implode(" OR id = ", $cid);
			$db = Factory::getDBO();
			$db->setQuery($query);
			if ($db->query()) {
				return $n;
			}
		}
		
		return - 1;
	}
	
	/**
	 * Get email item by ID
	 * 
	 * @return Email Table object
	 */
	function getItem()
	{
		static $item = null;
		if (isset($item)) {
			return $item;
		}
		
		$table = $this->getTable('emailtemplates','Table');
		// Load the current item if it has been defined
		$inputs = Factory::getApplication()->input;
		$edit = $inputs->getBool('edit', true);
		$cid = $inputs->get('cid', array(0), '', 'array');
		ArrayHelper::toInteger($cid, array(0));
		
		$table->load($cid[0]);
		
		if (! $item) {
			$item = $table;
		}
		
		return $item;
	}
	
	/** 
	 * Store email item
	 * 
	 * @return boolean True if have no error and vice versa
	 */
	function store()
	{
		// Initialize variables
		$db = Factory::getDBO();
		$row = $this->getItem();
		$post = $this->getState('request');
		
		$inputs = Factory::getApplication()->input;
		$post['content'] = $inputs->getString('content', '', 'post', JREQUEST_ALLOWRAW);
		$post['subject'] = $inputs->getString('subject', '', 'post', JREQUEST_ALLOWRAW);
		
		if (! $row->bind($post)) {
			JError::raiseWarning(1, JText::_('ERROR_OCCURRED_CAN_NOT_BIND_THE_DATA'));
			return false;
		}
		
		if (! $row->check()) {
			JError::raiseWarning(1, JText::_('ERROR_INVALID_DATAS'));
			return false;
		}
		
		if (! $row->store()) {
			JError::raiseWarning(1, JText::_('ERROR_DATA_NOT_SAVED'));
			return false;
		}
		
		$row->checkin();
		return $row->id;
	}
	
	/**
	 * Publish or unpublish records
	 * 
	 * @param integer $publish Publish status
	 * 
	 * @return boolean True if have no error and vice versa
	 */
	function dopublish($publish)
	{
		$db = Factory::getDBO();
		
		$inputs = Factory::getApplication()->input;
		$ids = $inputs->get('cid', array());
		ArrayHelper::toInteger($ids, array());
		$ids = implode(',', $ids);
		
		$query = "UPDATE #__jacomment_email_templates" . "\n SET published = " . intval($publish) . "\n WHERE id IN ( $ids )";
		$db->setQuery($query);
		if (! $db->query()) {
			return false;
		}
		return true;
	}
	
	/**
	 * Get language from language file
	 * 
	 * @param integer $client Location of language file is in front-end or back-end
	 * 
	 * @return array Language data
	 */
	function getLanguages($client = 0)
	{
		
		
		$client = JApplicationHelper::getClientInfo($client);
		$path = LanguageHelper::getLanguagePath($client->path);
		$dirs = JFolder::folders($path);
		$i = 0;
		$data['name'] = '';
		
		foreach ($dirs as $dir) {
			
			$files = JFolder::files($path . DS . $dir, '^([-_A-Za-z]*)\.xml$');
			foreach ($files as $file) {
				$data = JInstaller::parseXMLInstallFile($path . DS . $dir . DS . $file);
				$row = new StdClass();
				$row->id = $i;
				$row->language = substr($file, 0, - 4);
				
				if (! is_array($data)) {
					continue;
				}
				foreach ($data as $key => $value) {
					$row->$key = $value;
				}
				$rows[] = $row;
			}
			$i++;
		
		}
		return $rows;
	}
	
	/**
	 * Parse e-mail configuration template
	 * 
	 * @return array Parsed e-mail configuration template
	 */
	function parse_email_config()
	{
		$inputs = Factory::getApplication()->input;
		$option = $inputs->getCmd('option');
		$EMAIL_TEMPLATES_CONFIG = null;
		$PARSED_EMAIL_TEMPLATES_CONFIG = null;
		
		include_once JPATH_ADMINISTRATOR . DS . 'components' . DS . $option . DS . 'asset' . DS . 'jaemail_config.php';
		
		$rrrr = preg_split('/^==\s*(.+?)\s*==\s*$/ms', $EMAIL_TEMPLATES_CONFIG, - 1, PREG_SPLIT_DELIM_CAPTURE);
		$rrrr = array_map('trim', $rrrr);
		if ($rrrr[0] == '') {
			array_shift($rrrr);
		}
		for ($i = 0; $i < count($rrrr); $i += 2) {
			$head = $rrrr[$i];
			$body = $rrrr[$i + 1];
			$tags = array();
			if (preg_match('/^TAGSET\s+(.+)\s*/', $head, $regs)) { // tagset found
				foreach (preg_split('/[\r\n]+/', $body) as $l) {
					if ($l == '') {
						continue;
					}
					list($k, $v) = explode(' - ', $l);
					if (($k == '') || ($v == '')) {
						JError::raiseWarning(1001, "Error in line: $l, no tag keyword or description present, check spaces around dash");
					}
					$tags[trim($k)] = trim($v);
				}
				$PARSED_EMAIL_TEMPLATES_CONFIG['tagset'][$regs[1]] = $tags;
			} elseif (preg_match('/EMAIL TAGS\s+(.+)\s*/', $head, $regs)) { // email record found	        		        
				$tagsets = array_map('trim', explode(',', $regs[1]));
				$lines = preg_split('/[\r\n]+/', $body);
				preg_match('/^(.+?) - (.+)/', $x = trim(array_shift($lines)), $regs);
				if (! $regs) {
					return $PARSED_EMAIL_TEMPLATES_CONFIG;
				}
				$em = isset($regs[1]) ? $regs[1] : '';
				
				list($em, $filename) = explode(':', $em, 2);
				$comment = isset($regs[2]) ? $regs[2] : '';
				foreach ($lines as $l) {
					if ($l == '') {
						continue;
					}
					list($k, $v) = explode(' - ', $l);
					if (($k == '') || ($v == '')) {
						JError::raiseWarning(1001, "Error in line: $l, no tag keyword or description present, check spaces around dash");
					}
					$tags[trim($k)] = trim($v);
				}
				$PARSED_EMAIL_TEMPLATES_CONFIG['emails'][$em] = array('comment' => $comment, 'file' => $filename, 'tagsets' => $tagsets, 'tags' => $tags);
			} else {
				JError::raiseWarning(1001, 'wrong EMAIL_TEMPLATES_CONFIG definition: ' . $head);
			}
		}
		return $PARSED_EMAIL_TEMPLATES_CONFIG;
	}
	
	
	/**
	 * Duplicate e-mail template
	 * 
	 * @return boolean True if have no error and vice versa
	 */
	function do_duplicate()
	{
		$app = Factory::getApplication();
		$inputs = Factory::getApplication()->input;
		$option = $inputs->getCmd('option');
		$cid = $inputs->get('cid', '');
		if (! $cid) {
			JError::raiseWarning(1, JText::_('NO_TEMPLATE_IS_SELECTED'));
			return false;
		}
		
		$db = Factory::getDBO();
		
		$items = $this->getItemsbyWhere($cid);
		$app = Factory::getApplication('administrator');
		$filter_lang = $app->getUserStateFromRequest($option . '.emailtemplates.filter_lang', 'filter_lang', 'en-GB', 'string');
		$overwrite = $inputs->getInt('overwrite', 0);
		
		if ($items && $filter_lang) {
			foreach ($items as $k => $item) {
				$id = $this->getItembyWhere(" and language='$filter_lang' and name='{$item['name']}' and subscription_id='{$item['subscription_id']}'");
				if ($overwrite || (! $overwrite && ! $id)) {
					if (! $overwrite) {
						unset($item['id']);
					} else if ($id) {
						$item['id'] = $id;
					}
					$item['language'] = $filter_lang;
					$this->setState('request', $item);
					if (! $this->store_duplicate()) {
						JError::raiseWarning(1, JText::_('ERROR_DATA_NOT_SAVED'));
						return false;
					}
				} else {
					JError::raiseNotice(1, JText::_('EMAIL_TEMPLATE_HAS_REALLY_EXIST') . ' ID=' . $id);
				}
			}
			
			return true;
		}
		JError::raiseWarning(1, JText::_('NOT_FIND_EMAIL_TEMPLATE_FOR_COPTY_OR_YOU_HAVE_NOT_SELECTED_A_LANGUAGE_TO_COPY'));
		return false;
	}
	
	
	/**
	 * Get items by criteria
	 * 
	 * @param string $cid List of item id
	 * 
	 * @return array List of item
	 */
	function getItemsbyWhere($cid = '')
	{
		$db = Factory::getDBO();
		$sql = "select * from #__jacomment_email_templates where id in ($cid) ";
		$db->setQuery($sql);
		return $db->loadAssocList();
	}
	
	/**
	 * Get item by criteria
	 * 
	 * @param string $where Criteria
	 * 
	 * @return integer Item id
	 */
	function getItembyWhere($where = '')
	{
		$db = Factory::getDBO();
		$sql = "select id from #__jacomment_email_templates where 1=1 $where";
		$db->setQuery($sql);
		return $db->loadResult();
	}
	
	/** 
	 * Store email item
	 * 
	 * @return boolean True if have no error and vice versa
	 */
	function store_duplicate()
	{
		// Initialize variables
		$db = Factory::getDBO();
		$row = $this->getTable('emailtemplates','Table');
		$post = $this->getState('request');
		
		if ($post['id']) {
			$row->id = $post['id'];
		}
		
		if (! $row->bind($post)) {
			JError::raiseWarning(1, JText::_('ERROR_OCCURRED_CAN_NOT_BIND_THE_DATA'));
			return false;
		}
		
		if (! $row->check()) {
			JError::raiseWarning(1, JText::_('ERROR_INVALID_DATAS'));
			return false;
		}
		//print_r($row);exit;
		if (! $row->store()) {
			JError::raiseWarning(1, JText::_('ERROR_DATA_NOT_SAVED'));
			return false;
		}
		
		$row->checkin();
		return $row->id;
	}
	
	/**
	 * Import e-mail template
	 * 
	 * @param string $filecontent File content
	 * 
	 * @return boolean True if have no error and vice versa
	 */
	function import($filecontent)
	{
		$app = Factory::getApplication();
		$inputs = Factory::getApplication()->input;
		$option = $inputs->getCmd('option');
		$list = $this->parseItems($filecontent);
		if (! $list) {
			JError::raiseWarning(1, JText::_('FILE_IS_EMPTY_OR_DATA_IS_NOT_CORRECT_FORMAT'));
			return false;
		}
		$app = Factory::getApplication('administrator');
		$overwrite = $inputs->getInt('overwrite', 0);
		$filter_lang = $app->getUserStateFromRequest($option . '.emailtemplates.filter_lang', 'filter_lang', 'en-GB', 'string');
		
		foreach ($list as $item) {
			if (isset($item['name']) && $item['name'] != '' && $filter_lang != '') {
				$item['subscription_id'] = isset($item['subscription_id']) ? $item['subscription_id'] : 'NULL';
				$id = $this->getItembyWhere(" and language='{$filter_lang}' and name='{$item['name']}' and subscription_id='{$item['subscription_id']}'");
				
				if ($overwrite || (! $overwrite && ! $id)) {
					if ($overwrite && $id) {
						$item['id'] = $id;
					}
					$item['language'] = $filter_lang;
					$this->setState('request', $item);
					if (! $this->store_duplicate()) {
						JError::raiseWarning(1, JText::_('ERROR_DATA_NOT_SAVED'));
						return false;
					}
				} else {
					JError::raiseWarning(1, JText::_('EMAIL_TEMPLATE_HAS_REALLY_EXIST') . ' ID=' . $id);
					return false;
				}
			
			}
		}
		
		return true;
	}
	
	/**
	 * Parse file content into list of parameters
	 * 
	 * @param string $filecontent File content
	 * 
	 * @return array List of parameters
	 */
	function parseItems($filecontent)
	{
		$source = $filecontent;
		$list = array();
		
		while ($source != '') {
			$start = strpos($source, "[Email_Template");
			$end = strpos($source, "[/Email_Template]");
			$params = array();
			if ($start !== false && $end !== false) {
				$emailtemplates = trim(substr($source, $start + strlen('[Email_Template'), $end - $start - strlen('[/Email_Template]')));
				$end_first_tag = strpos($emailtemplates, "]");
				
				$source = trim(substr($source, $end + strlen('[/Email_Template]')));
				
				if ($end_first_tag !== false) {
					$property = substr($emailtemplates, 0, $end_first_tag);
					$params = $this->parseParams($property);
					
					$mid_content = ' ' . trim(substr($emailtemplates, $end_first_tag + 1));
					
					$arr_field = array('title', 'subject', 'content', 'EmailFromAddress', 'EmailFromName');
					$arr_pos = array();
					foreach ($arr_field as $k => $f) {
						$pos = strpos($mid_content, "[$f]");
						$arr_pos[$k] = array('key' => $f, 'pos' => $pos ? $pos : - 1);
					
					}
					//usort($arr_pos, array($this, 'cmp'));	

					for ($i = 0; $i < count($arr_pos) - 1; $i++) {
						if ($arr_pos[$i]['pos'] > 0) {
							$istart = $arr_pos[$i]['pos'] + strlen("[{$arr_pos[$i]['key']}]");
							$ileng = $arr_pos[$i + 1]['pos'] - $arr_pos[$i]['pos'] - strlen("[{$arr_pos[$i]['key']}]");
							
							$params[$arr_pos[$i]['key']] = trim(substr($mid_content, $istart, $ileng));
						
						}
					}
					
					if ($arr_pos[$i]['pos'] > 0) {
						$istart = $arr_pos[$i]['pos'] + strlen("[{$arr_pos[$i]['key']}]");
						$ileng = strlen($mid_content) - $arr_pos[$i]['pos'] - strlen("[{$arr_pos[$i]['key']}]");
						$params[$arr_pos[$i]['key']] = trim(substr($mid_content, $istart, $ileng));
					}
					$list[] = $params;
				}
			} else {
				break;
			}
		}
		
		return $list;
	}
	
	/**
	 * Compare 2 strings
	 * 
	 * @param array $a Array 1
	 * @param array $b Array 2
	 * 
	 * @return integer < 0 if str1 is less than str2; > 0 if str1 is greater than str2, and 0 if they are equal
	 */
	function cmp($a, $b)
	{
		return strcmp($a['pos'], $b['pos']);
	}
	
	/**
	 * Parse parameter string into array
	 * 
	 * @param string $params Parameter string
	 * 
	 * @return array Array of parameters
	 */
	function parseParams($params)
	{
		$params = html_entity_decode($params, ENT_QUOTES);
		$regex = "/\s*([^=\s]+)\s*=\s*('([^']*)'|\"([^\"]*)\"|([^\s]*))/";
		preg_match_all($regex, $params, $matches);
		
		$paramarray = null;
		if (count($matches)) {
			$paramarray = array();
			for ($i = 0; $i < count($matches[1]); $i++) {
				$key = $matches[1][$i];
				$val = $matches[3][$i] ? $matches[3][$i] : ($matches[4][$i] ? $matches[4][$i] : $matches[5][$i]);
				$paramarray[$key] = $val;
			}
		}
		return $paramarray;
	}
	
	/**
	 * Group filter array
	 * 
	 * @param array 	$array1 Array 1
	 * @param array 	$array2 Array 2
	 * @param string 	$key	Key of array 1
	 * 
	 * @return array Array after grouping
	 */
	function group_filter($array1, $array2, $key)
	{
		if (! $array1 || ! $array2 || ! $key) {
			return array();
		}
		$list = array();
		foreach ($array2 as $value => $text) {
			foreach ($array1 as $k => $item) {
				if ($item[$key] == $value) {
					$list[$value][] = $item;
				}
			
			}
		
		}
		
		return $list;
	}
	
	/**
	 * Check different item between 2 array
	 * 
	 * @param array 	$array1 Array 1
	 * @param array		$array2 Array 2
	 * @param string 	$key	Key of arrays
	 * 
	 * @return array Array of items
	 */
	function diff_multi_array($array1, $array2, $key)
	{
		if (! $array2) {
			return $array1;
		}
		if (! $array1) {
			return array();
		}
		
		$diff = array();
		foreach ($array1 as $a1) {
			$check = false;
			foreach ($array2 as $a2) {
				if ($a1[$key] == $a2[$key]) {
					$check = true;
					break;
				}
			}
			if (! $check) {
				$diff[] = $a1;
			}
		}
		
		return $diff;
	}
}
?>