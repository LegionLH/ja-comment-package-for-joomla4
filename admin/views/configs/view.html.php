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

/**
 * This view is used for JAConfigs feature of the component
 * 
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */
class JACommentViewConfigs extends JACView
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
		
		$task = $inputs->getCmd('task', null);
		
		if (! $inputs->getCmd('group')) {
			$inputs->getCmd('group', 'general');
		}
		$group = $inputs->getCmd('group', 'general');
		
		$model = $this->getModel('configs');
		$item = $model->getItems();
		
		$data = $item->data;
		$params = new JRegistry;
		$params->loadString($data);
		$lists = array();
		
		if ($group == "maintenance" && $task == "updatecounter") {
			$model->maintainSystem();
			return;
		} else if ($task) {
			$this->setLayout($task);
			if ($group == 'moderator') {
				$this->editmoderator($params);
			}
			
			if ($group == 'layout') {
				$theme = JRequest::getString('theme', '');
				$this->editcss($theme);
			}
		} else {
			if ($group == 'blacklisting' || $group == 'spamfilters') {
				$blockblacktab = $model->getBlockBlackByTab($group);
				
				$lists['blocked_word_list'] = '';
				$lists['blocked_ip_list'] = '';
				$lists['blocked_email_list'] = '';
				$lists['blacklist_word_list'] = '';
				$lists['blacklist_ip_list'] = '';
				$lists['blacklist_email_list'] = '';
				
				foreach ($blockblacktab as $k => $v) {
					$arr_str[$k] = explode("\n", $v);
					if (sizeof($arr_str[$k]) > 1) {
						asort($arr_str[$k]);
						$str[$k] = '';
						foreach ($arr_str[$k] as $key => $val) {
							if ($val) {
								$str[$k] .= "<li id='" . $k . "_" . $key . "' onclick='javascript: remove_blockblack(\"" . $k . "\", \"" . $key . "\");'>" . $val . "</li>";
							}
						}
					} else {
						if ($k == "blocked_word_list") {
							$str[$k] = JText::_('NO_KEYWORD_IS_CURRENTLY_BLOCKED');
						} else if ($k == "blocked_ip_list") {
							$str[$k] = JText::_('NO_IP_ADDRESS_IS_CURRENTLY_BLOCKED');
						} else if ($k == "blocked_email_list") {
							$str[$k] = JText::_('NO_EMAIL_ADDRESS_IS_CURRENTLY_BLOCKED');
						} else if ($k == "blacklist_word_list") {
							$str[$k] = JText::_('NO_KEYWORD_IS_CURRENTLY_BLACKLISTED');
						} else if ($k == "blacklist_ip_list") {
							$str[$k] = JText::_('NO_IP_ADDRESS_IS_CURRENTLY_BLACKLISTED');
						} else {
							$str[$k] = JText::_('NO_EMAIL_ADDRESS_IS_CURRENTLY_BLACKLISTED');
						}
					}
					
					$lists[$k] = $str[$k];
				}
			
			} else if ($group == 'language') {
				$helper = new JACommentHelpers();
				
				$dir_language = $helper->readFolder(JPATH_SITE . DS . 'components' . DS . 'com_jacomment' . DS . 'languages');
				$lists['language'] = $dir_language;
				
				$dir_language_admin = $helper->readFolder(JPATH_COMPONENT . DS . 'languages');
				$lists['language_admin'] = $dir_language_admin;
			
			} else if ($group == 'moderator') {
				$this->moderator($params);
			}
			$this->setLayout($group);
		}
		$this->lists = &$lists;
		$this->group = &$group;
		$this->params = &$params;
		$this->cid = &$item->id;
		/*
		 * Check component Ja Voice istalled
		 * 
		 * */
		$check_component = false;
		if(is_dir(JPATH_ROOT.'/administrator/components/com_javoice') && is_dir(JPATH_ROOT.'/components/com_javoice')){
			jimport('joomla.application.component.helper');
			if(JComponentHelper::isEnabled('com_javoice', true))
			{
			    $check_component = true;
			}
		}
		$this->check_component = &$check_component;
		
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
		$inputs = Factory::getApplication()->input;
		$group = $inputs->getCmd('group', '');
		JToolBarHelper::title(JText::_('CONFIGURATION_MANAGER'));
		
		if ($group == 'layout') {
			JToolBarHelper::save("save", JText::_("SAVE"));
		} elseif ($group == 'moderator') {
			JToolBarHelper::addNew('add', JText::_('ADD_USER'));
			JToolBarHelper::deleteList();
		} elseif ($group == 'maintenance') {
			return;
		} else {
			JToolBarHelper::save("save", JText::_("SAVE"));
		}
		
		return;
	}
	
	/**
	 * Show menu in tab view
	 * 
	 * @return string Tab view in HTML code
	 */
	function getTabs()
	{
		$inputs = Factory::getApplication()->input;
		$option = $inputs->getCmd('option');
		$group = $inputs->getCmd('group', '');
		$tabs = '<div class="submenu-box">
						<div class="submenu-pad">
							<ul id="submenu" class="configuration">
								<li><a href="index.php?option=' . $option . '&amp;view=configs&amp;group=general"';
		if ($group == 'general' || $group == '') {
			$tabs .= ' class="active" ';
		}
		$tabs .= '>';
		$tabs .= JText::_('GENERAL') . '</a></li>';
		
		$tabs .= '<li><a href="index.php?option=' . $option . '&amp;view=configs&amp;group=comments"';
		if ($group == 'comments') {
			$tabs .= ' class="active" ';
		}
		$tabs .= '>';
		$tabs .= JText::_('COMMENTS') . '</a></li>';
		
		$tabs .= '<li><a href="index.php?option=' . $option . '&amp;view=configs&amp;group=spamfilters"';
		if ($group == 'spamfilters') {
			$tabs .= ' class="active" ';
		}
		$tabs .= '>';
		$tabs .= JText::_('SPAM_FILTERS') . '</a></li>';
		
		$tabs .= '<li><a href="index.php?option=' . $option . '&amp;view=configs&amp;group=blacklisting"';
		if ($group == 'blacklisting') {
			$tabs .= ' class="active" ';
		}
		$tabs .= '>';
		$tabs .= JText::_('BLACKLISTING') . '</a></li>';
		
		$tabs .= '<li><a href="index.php?option=' . $option . '&amp;view=configs&amp;group=moderator"';
		if ($group == 'moderator') {
			$tabs .= ' class="active" ';
		}
		$tabs .= '>';
		$tabs .= JText::_('MODERATOR') . '</a></li>';
		
		$tabs .= '<li><a href="index.php?option=' . $option . '&amp;view=configs&amp;group=permissions"';
		if ($group == 'permissions') {
			$tabs .= ' class="active" ';
		}
		$tabs .= '>';
		$tabs .= JText::_('PERMISSIONS') . '</a></li>';
		
		$tabs .= '<li><a href="index.php?option=' . $option . '&amp;view=configs&amp;group=layout"';
		if ($group == 'layout') {
			$tabs .= ' class="active" ';
		}
		$tabs .= '>';
		$tabs .= JText::_('LAYOUT_AND_PLUGINS') . '</a></li>';
		
		$tabs .= '<li><a href="index.php?option=' . $option . '&amp;view=configs&amp;group=maintenance"';
		if ($group == 'maintenance') {
			$tabs .= ' class="active" ';
		}
		$tabs .= '>';
		$tabs .= JText::_('MAINTENANCE') . '</a></li>';
		
		$tabs .= '				</ul>
							<div class="clr"></div>
						</div>
					</div>
					<div class="clr"></div>';
		return $tabs;
	}
	
	/**
	 * Moderator view
	 * 
	 * @param object $params Configuration parameters
	 * 
	 * @return void
	 */
	function moderator($params)
	{
		
		$model = JACModel::getInstance('moderator', 'JACommentModel');
		$lists = $model->_getVars();
		$where_more = '';
		$order = '';
		if (isset($lists['filter_order']) && $lists['filter_order'] != '') {
			$order = $lists['filter_order'] . ' ' . @$lists['filter_order_Dir'];
		}
		$ids = $params->get('moderator', '');
		if ($ids != '') {
			$joins = "";
			$where_more .= " AND u.id IN($ids)";
		} else {
			$joins = " INNER JOIN #__user_usergroup_map map2 ON (u.id = map2.user_id)";
			$where_more .= " AND map2.group_id IN (SELECT id FROM #__usergroups WHERE parent_id=6)";
		}
		
		jimport('joomla.html.pagination');
		$total = $model->getTotal($where_more, $joins);
		$pageNav = new JPagination($total, $lists['limitstart'], $lists['limit']);
		
		$items = $model->getItems($where_more, $lists['limit'], $lists['limitstart']);
		
		$this->items = $items;
		$this->lists = $lists;
		$this->pageNav = $pageNav;
	}
	
	/**
	 * Edit moderator view
	 * 
	 * @param object $params Configuration parameters
	 * 
	 * @return void
	 */
	function editmoderator($params)
	{
		global $jacconfig;
		
		$listUser = $params->get('moderator', '');
		$app = Factory::getApplication();
		$items = '';
		$option = 'moderator';
		$helper = new JACommentHelpers();
		$postback = $helper->isPostBack();
		$model = JACModel::getInstance('moderator', 'JACommentModel');
		
		$lists = $model->_getVars();
		
		$where = "";
		$inputs = Factory::getApplication()->input;
		$lists['groupname'] = $inputs->getInt("groupname", 0);
		if ($lists['groupname']) {
			$where = ' AND map2.group_id = ' . $lists['groupname'];
		}
		$searchName = $inputs->getString("search", "");
		if ($searchName) {
			$where .= " AND u.username LIKE '%{$searchName}%'";
		}
		if ($listUser) {
			$where .= " AND u.id NOT IN(" . $listUser . ")";
		}
		if ($postback) {
			$items = $model->getItems($where);
		}
		$this->items = $items;
		
		$groupUser = $helper->getGroupUser('', 'groupname', 'class="inputbox" size="1"', $lists['groupname'], 1);
		
		$this->groupUser = $groupUser;
		$this->postback = $postback;
		$this->lists = $lists;
		$this->params = $params;
	}
	
	/**
	 * Edit CSS view
	 * 
	 * @param string $theme Theme path on site
	 * 
	 * @return void
	 */
	function editcss($theme)
	{
		$content = '';
		// Read the content of css
		jimport('joomla.filesystem.file');
		$systemTheme = JACommentHelpers::getTemplate(0);
		//$themeFolders = JPATH_SITE.'/components/com_jacomment/themes/';
		$file = $theme . '/css/style.css';
		
		if (file_exists(JPATH_SITE . '/templates/' . $systemTheme . '/html/com_jacomment/themes/' . $file)) {
			$file = JPATH_SITE . '/templates/' . $systemTheme . '/html/com_jacomment/themes/' . $file;
		} else if (file_exists(JPATH_SITE . '/components/com_jacomment/themes/' . $file)) {
			$file = JPATH_SITE . '/components/com_jacomment/themes/' . $file;
		} else {
			echo JText::_("NO_EXIST_CSS_FILE_IN_THIS_TEPLATE");
			exit();
		}
		
		$content = JFile::read($file);
		$this->theme = $theme;
		$this->content = $content;
	}
}
?>