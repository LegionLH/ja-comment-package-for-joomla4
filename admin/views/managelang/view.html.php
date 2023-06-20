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
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

if (! defined('JAC_REGISTERED')) {
	JLoader::register('JACView', JPATH_ADMINISTRATOR.'/components/com_jacomment/views/view.php');
}
use Joomla\CMS\Factory;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * This view is used for JAManagelang feature of the component
 * 
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */
class JACommentViewManagelang extends JACView
{
	/**
	 * Managelang view display method
	 * 
	 * @param string $tmpl The name of the template file
	 * 
	 * @return void
	 **/
	function display($tmpl = null)
	{
		$inputs = Factory::getApplication()->input;
		// Display menu header
		if (! $inputs->get("ajax",'') && $inputs->get('tmpl','') != 'component' && $inputs->get('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT_ADMINISTRATOR . DS . "views" . DS . "jaview" . DS . "tmpl" . DS . "main_header.php";
			if (file_exists($file)) {
				include_once($file);
			}
		}
		
		$app = Factory::getApplication();
		$option = $inputs->getCmd('option');
		
		$task = $inputs->getCmd("task", '');
		switch ($task) {
			case 'edit':
				$this->show_form();
				break;
			default:
				$this->show_list();
				break;
		}
		
		$this->addToolbar();
		parent::display();
		
		// Display menu footer
		if (!$inputs->get("ajax",'') && $inputs->get('tmpl','') != 'component' && $inputs->get('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT_ADMINISTRATOR . DS . "views" . DS . "jaview" . DS . "tmpl" . DS . "main_footer.php";
			if (file_exists($file)) {
				include_once($file);
			}
		}
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$inputs = Factory::getApplication()->input;
		$task = $inputs->getCmd('task', '');
		JToolBarHelper::title(JText::_("CUSTOM_LANGUAGE"));
		if ($task == 'edit') {
			JToolBarHelper::save();
			JToolBarHelper::cancel();
		}
	}
	
	/**
	 * Show edit form
	 * 
	 * @return void
	 */
	function show_form()
	{
		$inputs = Factory::getApplication()->input;
		$option = $inputs->getCmd('option');
		$task = $inputs->getCmd('task');
		
		$lang = $inputs->getCmd('lang', '');
		
		$cids = $inputs->get('cid', array(), '', 'array');
		ArrayHelper::toInteger($cids);
		if ($cids) {
			$lang = $cids[0];
		}
		
		$client = JApplicationHelper::getClientInfo($inputs->getVar('client', '1', '', 'int'));
		$path_dest = LanguageHelper::getLanguagePath($client->path);
		$root = $path_dest . DS . $lang . DS . $lang . '.' . $option . '.ini';
		
		$data = '';
		$root = JPath::clean($root);
		if ($lang != '' && JFile::exists($root)) {
			$data = file_get_contents($root);
		} else {
			if ($client->id) {
				$root = JPATH_COMPONENT_ADMINISTRATOR . DS . 'language' . DS . $lang . '.' . $option . '.ini';
			} else {
				$root = JPATH_COMPONENT_ADMINISTRATOR . DS . 'language' . DS . $lang . '.' . $option . '.ini';
			}
			$root = JPath::clean($root);
			if (JFile::exists($root)) {
				$data = file_get_contents($root);
			} else {
				$root = $path_dest . DS . 'en-GB' . DS . 'en-GB' . '.' . $option . '.ini';
				if ($root) {
					$data = file_get_contents($root);
				}
			}
		}
		$file = $lang . '.' . $option . '.ini';
		
		$this->data = $data;
		$this->filename = $file;
		$this->lang = $lang;
		$this->client = $client;
		$this->path_lang = $path_dest;
		$this->task = $task;
		$this->root = $root;
	
	}
	
	/**
	 * Compiles a list of installed languages
	 * 
	 * @return void
	 */
	function show_list()
	{
		$app = Factory::getApplication();
		$inputs = Factory::getApplication()->input;
		$option = $inputs->getCmd('option');
		// Initialize some variables
		$db = Factory::getDBO();
		$client = JApplicationHelper::getClientInfo($inputs->get('client', 0, 'int'));
		$rows = array();
		$app = Factory::getApplication('administrator');
		
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest($option . '.limitstart', 'limitstart', 0, 'int');
		
		//load folder filesystem class
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
				
				// if current than set published
				$params = JComponentHelper::getParams('com_languages');
				if ($params->get($client->name, 'en-GB') == $row->language) {
					$row->published = 1;
				} else {
					$row->published = 0;
				}
				
				$row->checked_out = 0;
				$row->mosname = StringHelper::strtolower(str_replace(" ", "_", $row->name));
				$rows[] = $row;
			}
			$i++;
		}
		
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($i, $limitstart, $limit);
		
		$rows = array_slice($rows, $pageNav->limitstart, $pageNav->limit);
		
		$this->rows = $rows;
		$this->page = $pageNav;
		$this->client = $client;
	}
}