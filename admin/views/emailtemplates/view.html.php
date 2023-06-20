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
defined('_JEXEC') or die('Restricted access');

if (! defined('JAC_REGISTERED')) {
	JLoader::register('JACView', JPATH_ADMINISTRATOR.'/components/com_jacomment/views/view.php');
}

use Joomla\CMS\Factory;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;

/**
 * This view is used for JAEmailtemplates feature of the component
 * 
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */
class JACommentViewEmailtemplates extends JACView
{
	/**
	 * Display the view
	 * 
	 * @param string $tmpl The name of the template file
	 * 
	 * @return void
	 */
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
		
		$task = $inputs->getCmd("task", '');
		switch ($task) {
			case 'add':
			case 'edit':
				$this->displayForm();
				break;
			
			case 'show_duplicate':
				$this->show_duplicate();
				break;
			case 'show_import':
				$this->show_import();
				break;
			default:
				$this->displayListItems();
				break;
		}
		
		$this->addToolbar();
		parent::display($tmpl);
		
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
		JToolBarHelper::title(JText::_('EMAIL_TEMPLATE_MANAGER'));
		
		$inputs = Factory::getApplication()->input;
		$task = $inputs->getCmd('task', '');
		switch ($task) {
			case 'add':
			case 'edit':
				JToolBarHelper::apply();
				JToolBarHelper::save();
				JToolBarHelper::cancel();
				break;
			case 'show_duplicate':
			case 'show_import':
			case 'show_export':
				return;
			default:
				JToolBarHelper::custom('show_duplicate', 'copy', '', JText::_('COPY_TO'));
				JToolBarHelper::custom('show_import', 'upload', '', JText::_('IMPORT'), false);
				JToolBarHelper::custom('export', 'download', '', JText::_('EXPORT'));
				JToolBarHelper::publishList();
				JToolBarHelper::unpublishList();
				JToolBarHelper::deleteList(JText::_('ARE_YOU_SURE_TO_DELETE'));
				JToolBarHelper::editList();
				JToolBarHelper::addNew();
				break;
		}
	}
	
	/**
	 * Display List of items
	 * 
	 * @return void
	 */
	function displayListItems()
	{
		$inputs = Factory::getApplication()->input;
		$app = Factory::getApplication();
		$option = $inputs->getCmd('option');
		$option_1 = $option . '.emailtemplates';
		$app = Factory::getApplication('administrator');
		
		$search = $app->getUserStateFromRequest("$option_1.search", 'search', '', 'string');
		$lists['search'] = StringHelper::strtolower($search);
		$lists['order'] = $app->getUserStateFromRequest($option_1 . '.filter_order', 'filter_order', 'name', 'cmd');
		$lists['order_Dir'] = $app->getUserStateFromRequest($option_1 . '.filter_order_Dir', 'filter_order_Dir', '', 'word');
		$lists['search'] = $app->getUserStateFromRequest("$option_1.search", 'search', '', 'string');
		$lists['option'] = $option;
		
		$filter_state = $app->getUserStateFromRequest($option_1 . '.filter_state', 'filter_state', '', 'word');
		$filter_lang = $app->getUserStateFromRequest($option_1 . '.filter_lang', 'filter_lang', 'en-GB', 'string');
		// state filter
		$lists['state'] = JHTML::_('grid.state', $filter_state);
		
		$modelEmailTemplate = $this->getModel('emailtemplates');
		$languages = $modelEmailTemplate->getLanguages(0);
		
		$languages = JHTML::_('select.genericlist', $languages, 'filter_lang', 'class="inputbox" size="1" onChange="$(\'task\').value=\'\'; form.submit()"', 'language', 'name', $filter_lang);
		$this->languages = &$languages;
		$this->filter_lang = &$filter_lang;
		
		$arr_group = JACommentConstant::get_Email_Group();
		
		// get data items
		$model = $this->getModel();
		
		$items = $model->getItems();
		$this->counts = count($items);
		if ($items) {
			$items = $model->group_filter($items, $arr_group, 'group');
		}
		$this->items = &$items;
		
		$en_items = array();
		if ($filter_lang != 'en-GB') {
			$en_items = $model->getItems('en-GB');
			if ($en_items) {
				$en_items = $model->group_filter($en_items, $arr_group, 'group');
			}
		}
		$this->en_items = $en_items;
		
		$this->arr_group = &$arr_group;
		$this->lists = &$lists;
		$this->option = $option;
	}
	
	/**
	 * Display edit form
	 * 
	 * @return void
	 */
	function displayForm()
	{
		$inputs = Factory::getApplication()->input;
		$option = $inputs->getCmd('option');
		$item = $this->get('Item');
		
		if (! $item->language) {
			$item->language = 'en-GB';
		}
		$modelEmailTemplate = $this->getModel('emailtemplates');
		$languages = $modelEmailTemplate->getLanguages(0);
		
		$languages = JHTML::_('select.genericlist', $languages, 'language', 'class="inputbox" size="1"', 'language', 'name', $item->language);
		$this->languages = &$languages;
		
		$PARSED_EMAIL_TEMPLATES_CONFIG = $modelEmailTemplate->parse_email_config();
		
		$this->comment = &$PARSED_EMAIL_TEMPLATES_CONFIG['emails'][$item->name]['comment'];
		
		/// get message tags
		$tags = array();
		if (isset($PARSED_EMAIL_TEMPLATES_CONFIG['emails'][$item->name]['tagsets'])) {
			foreach ((array) $PARSED_EMAIL_TEMPLATES_CONFIG['emails'][$item->name]['tagsets'] as $ts) {
				$tags = array_merge_recursive($tags, $PARSED_EMAIL_TEMPLATES_CONFIG['tagset'][$ts]);
			}
		}
		if (isset($PARSED_EMAIL_TEMPLATES_CONFIG['emails'][$item->name]['tags'])) {
			foreach ((array) $PARSED_EMAIL_TEMPLATES_CONFIG['emails'][$item->name]['tags'] as $k => $v) {
				$tags[$k] = $v;
			}
		}
		if (count($tags) <= 1) {
			if (isset($PARSED_EMAIL_TEMPLATES_CONFIG['emails'][$item->name]['tagsets'])) {
				foreach ((array) $PARSED_EMAIL_TEMPLATES_CONFIG['emails']['default']['tagsets'] as $ts) {
					$tags = array_merge_recursive($tags, $PARSED_EMAIL_TEMPLATES_CONFIG['tagset'][$ts]);
				}
			}
		}
		$i = 0;
		$tags_to_assign = array();
		foreach ($tags as $k => $v) {
			$row = new stdClass();
			$row->value = '{' . $k . '}';
			$row->text = '{' . $k . '} - ' . $v;
			$tags_to_assign[$i] = $row;
			$i++;
		}
		$default = array();
		$default[] = JHTML::_('select.option', '', JText::_('PLEASE_CHOOSE_AN_OPTION_BELOW_AND_IT_WILL_BE_INSERTED_INTO_EMAIL_MESSAGE'));
		$tags_to_assign = array_merge($default, $tags_to_assign);
		$tags_to_assign = JHTML::_('select.genericlist', $tags_to_assign, 'tags', 'class="small" style="background-color: buttonface; width:100%; color: black;" onclick="insertVariable(this)" size="20"', 'value', 'text');
		
		$this->tags = &$tags_to_assign;
		
		// clean item data
		$put[] = JHTML::_('select.option', '1', JText::_('JYES'));
		$put[] = JHTML::_('select.option', '0', JText::_('JNO'));
		$option_group = array();
		$arr_group = JACommentConstant::get_Email_Group();
		for ($i = 0, $n = count($arr_group); $i < $n; $i++) {
			$option_group[] = JHTML::_('select.option', $i, $arr_group[$i]);
		}
		$html_group = JHTML::_('select.genericlist', $option_group, 'group', 'class="inputbox" size="1"', 'value', 'text', $item->group);
		
		// If not a new item, trash is not an option
		if (! $item->id) {
			$item->published = 1;
		}
		$published = JHTML::_('select.radiolist', $put, 'published', '', 'value', 'text', $item->published);
		
		// clean item data
		JFilterOutput::objectHTMLSafe($item, ENT_QUOTES, '');
		
		$editor = Factory::getEditor();
		
		$item->name = $inputs->getString('tpl', $item->name);
		
		$this->editor = &$editor;
		$this->group = &$html_group;
		
		$this->option = &$option;
		$this->published = &$published;
		$this->item = &$item;
	}
	
	/**
	 * Show duplicate item
	 * 
	 * @return void
	 */
	function show_duplicate()
	{
		$app = Factory::getApplication();
		$inputs = Factory::getApplication()->input;
		$option = $inputs->getCmd('option');
		$app = Factory::getApplication('administrator');
		$filter_lang = $app->getUserStateFromRequest($option . '.emailtemplates.filter_lang', 'filter_lang', 'en-GB', 'string');
		
		$this->assign('option', $option);
		$modelEmailTemplate = $this->getModel('emailtemplates');
		$languages = $modelEmailTemplate->getLanguages(0);
		$languages = JHTML::_('select.genericlist', $languages, 'filter_lang', 'class="inputbox" size="1"', 'language', 'name', $filter_lang);
		$this->assign('languages', $languages);
		
		$cid = $inputs->get('cid', array(), '', 'array');
		ArrayHelper::toInteger($cid, array());
		$cid = implode(',', $cid);
		$this->cid = $cid;
	}
	
	/**
	 * Show import item
	 * 
	 * @return void
	 */
	function show_import()
	{
		$app = Factory::getApplication();
		$inputs = Factory::getApplication()->input;
		$option = $inputs->getCmd('option');
		$this->option = $option;
		
		$modelEmailTemplate = $this->getModel('emailtemplates');
		$languages = $modelEmailTemplate->getLanguages(0);
		$languages = JHTML::_('select.genericlist', $languages, 'filter_lang', 'class="inputbox" size="1"', 'language', 'name', '');
		$this->languages = $languages;
	}
}
?>