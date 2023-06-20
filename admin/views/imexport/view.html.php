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
 * This view is used for JAImexport feature of the component
 * 
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */
class JACommentViewImexport extends JACView
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
		
		$group = $inputs->getCmd('group', 'import');
		$type = $inputs->getCmd('type');
		$task = $inputs->getCmd('task', null, 'default');
		
		if ($task == "showcomment") {
			$this->setLayout("showlist");
			
			$this->showComment();
			$source = $inputs->getString('source', '');
			$this->source = &$source;
		} else if ($task == "open_content") {
			$this->showcontent();
		} else {
			$this->setLayout($group);
			$model = JACModel::getInstance('Imexport', 'JACommentModel');
			$OtherCommentSystems = $model->JAOtherCommentSystem();
			
			$tables = $model->showTables();
			
			$jconfig = new JConfig();
			$tablePrefix = $jconfig->dbprefix;
			
			if ($tables) {
				foreach ($tables as $table) {
					for ($i = 0, $n = count($OtherCommentSystems); $i < $n; $i++) {
						// $table_chk = str_replace('#_', '', $OtherCommentSystems[$i]['table']);
						$table_chk = str_replace('#__', $tablePrefix, $OtherCommentSystems[$i]['table']);
						
						// if (preg_match('/' . $table_chk . '$/i', $table)) {
						if ($table_chk == $table) {
							if(isset($OtherCommentSystems[$i]['element'])){
								if($model->checkExistComponent($OtherCommentSystems[$i]['element'])){
									$OtherCommentSystems[$i]['status'] = true;
									$OtherCommentSystems[$i]['total'] = $model->totalRecord($table);
								}
							}else{
								$OtherCommentSystems[$i]['status'] = true;
								$OtherCommentSystems[$i]['total'] = $model->totalRecord($table);
							}
						}
					}
				}
			}
			
			$this->OtherCommentSystems = $OtherCommentSystems;
			
			$source = $inputs->getString('source', '');
			$this->source = &$source;
			$this->group = &$group;
		
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
		JToolBarHelper::title(JText::_('JA_IMPORT_EXPORT'), 'generic.png');
		JToolBarHelper::help('screen.jacomment', true);
	}
	
	/**
	 * Show comment from file
	 * 
	 * @return unknown_type
	 */
	function showComment()
	{
		$inputs = Factory::getApplication()->input;
		$inputs->set('layout', 'import');
		
		$model = $this->getModel('imexport');
		
		$source = $inputs->getString('source');
		
		jimport('joomla.filesystem.file');
		
		if (isset($_FILES[$source]) && $_FILES[$source]['name'] != '' && strtolower(substr($_FILES[$source]['name'], - 3, 3)) == 'xml') {
			$file = JPATH_COMPONENT_ADMINISTRATOR . DS . 'temp' . DS . substr($_FILES[$source]['name'], 0, strlen($_FILES[$source]['name']) - 4) . time() . rand() . substr($_FILES[$source]['name'], - 4, 4);
			
			if (JFile::upload($_FILES[$source]['tmp_name'], $file)) {
				unset($_FILES[$source]);

				$xml = Factory::getXML($file, true);
				
				//check is valid xml document
				if (!$xml) {
					JError::raiseNotice(1, JText::_('PLEASE_BROWSE_A_VALID_XML_FILE'));
					return JController::setRedirect("index.php?option=com_jacomment&view=imexport&group=import", '');
				} else {
					$allComments = $xml->children();
					if ($allComments[0]->name() != "article") {
						JError::raiseNotice(1, JText::_('PLEASE_SELECT_XML_FILE_OF_DISQUS_COMMENTS'));
						return JController::setRedirect("index.php?option=com_jacomment&view=imexport&group=import");
					}
					
					//disqus
					$site_url = $inputs->getString("site_url", "joomlart");
					
					$items = array();
					$i = 0;
					$rows = array();
					foreach ($allComments as $blogpost) {
						foreach ($blogpost->children() as $comments) {
							$other[$comments->name()] = (string) $comments;
							foreach ($comments->children() as $key => $value) {
								$comment[$key] = $value->children();
								foreach ($comment[$key] as $v) {
									if ($site_url == "" || strpos($other["url"], $site_url) !== false) {
										$rows[$key][$v->name()] = (string) $v;
									}
								}
							}
						}
						if ($site_url == "" || strpos($other["url"], $site_url) !== false) {
							$items[$other["url"]] = $rows;
						}
						$rows = array();
					}
					if (! $items || count($items) < 1) {
						JError::raiseNotice(1, JText::_('NO_COMMENT_IN_XML_FILE_WAS_FOUND'));
						return JController::setRedirect("index.php?option=com_jacomment&view=imexport&group=import");
					}
					$this->items = $items;
					$this->allComponents = $this->getAllComponent();
				}
			} else {
				JError::raiseNotice(1, JText::_('CAN_NOT_IMPORT_THE_DATA'));
				return JController::setRedirect("index.php?option=com_jacomment&view=imexport&group=import");
			}
		} else {
			JError::raiseNotice(1, JText::_('CAN_NOT_IMPORT_THE_DATA_PLEASE_BROWSE_AN_XML_FILE'));
			return JController::setRedirect("index.php?option=com_jacomment&view=imexport&group=import");
		}
	}
	
	/**
	 * Check a component exists or not
	 * 
	 * @param string $componentOption Component name
	 * 
	 * @return integer Component is existed or not
	 */
	function checkExistComponent($componentOption)
	{
		$model = JACModel::getInstance('Imexport', 'JACommentModel');
		return $model->checkExistComponent($componentOption);
	}
	
	/**
	 * Get all component
	 * 
	 * @return array Array of components
	 */
	function getAllComponent()
	{
		$model = JACModel::getInstance('Imexport', 'JACommentModel');
		return $model->getAllComponent();
	}
	
	/**
	 * Get article from link
	 * 
	 * @param string $link Article link
	 * 
	 * @return object Item object
	 */
	function getComponentFromAriticle($link)
	{
		$model = JACModel::getInstance('Imexport', 'JACommentModel');
		return $model->getComponentFromAricleLink($link);
	}
	
	/**
	 * Get link of myblog component
	 * 
	 * @param integer $id Item id
	 * 
	 * @return string Link of item
	 */
	function getMyBlogLink($id)
	{
		$model = JACModel::getInstance('Imexport', 'JACommentModel');
		$permalink = $model->getMyBlogLink($id);
		return JRoute::_("index.php?option=com_myblog&show={$permalink}&Itemid={$id}");
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
		$group = $inputs->getString('group', '');
		$tabs = '<div class="submenu-box">
						<div class="submenu-pad">
							<ul id="submenu" class="configuration">
								<li><a href="index.php?option=' . $option . '&view=imexport&group=import"';
		if ($group == 'import' || $group == '') {
			$tabs .= ' class="active" ';
		}
		$tabs .= '>';
		$tabs .= JText::_('IMPORT_DATA') . '</a></li>';
		
		$tabs .= '<li><a href="index.php?option=' . $option . '&view=imexport&group=export"';
		if ($group == 'export') {
			$tabs .= ' class="active" ';
		}
		$tabs .= '>';
		$tabs .= JText::_('EXPORT_DATA') . '</a></li>';
		
		$tabs .= '				</ul>
							<div class="clr"></div>
						</div>
					</div>
					<div class="clr"></div>';
		return $tabs;
	}
	
	/**
	 * Show K2 content
	 * 
	 * @return void
	 */
	function showcontentk2()
	{
		$this->assign("component", "com_k2");
	}
	
	/**
	 * Show content from content or myblog component
	 * 
	 * @return void
	 */
	function showcontent()
	{
		$model = JACModel::getInstance('Imexport', 'JACommentModel');
		$component[] = "com_content";
		$isExitComBlog = $model->checkComBlog();
		if ($isExitComBlog) {
			$component[] = "com_myblog";
		}
		$this->assign("component", $component);
	}
	
	/**
	 * Get list of components
	 * 
	 * @return array List of components installed on system
	 */
	function getListComponent()
	{
		$list = array();
		$list[] = "com_content";
		$model = JACModel::getInstance('Imexport', 'JACommentModel');
		if ($model->checkExistComponent("com_myblog")) {
			$list[] = "com_myblog";
		}
		if ($model->checkExistComponent("com_k2")) {
			$list[] = "com_k2";
		}
		return $list;
	}
}
?>