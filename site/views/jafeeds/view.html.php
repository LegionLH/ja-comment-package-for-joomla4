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
 * JACommentViewJAfeeds View
 *
 * @package		Joomla.Site
 * @subpackage	JAComment
 */
class JACommentViewJAfeeds extends JACView
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
		switch ($this->getLayout()) {
			case "guide":
				;
				break;
			default:
				$this->displayItems();
				break;
		}
		parent::display($tmpl);
	}
	
	/**
	 * Display feed items
	 * 
	 * @return void
	 */
	function displayItems()
	{
		global $jacconfig;
		$inputs = Factory::getApplication()->input;
		$contentID = $inputs->get("contentid", 0);
		$contentOption = $inputs->getCmd("contentoption", "com_content");
		$search = "";
		
		$search .= " AND `option` = '" . $contentOption . "'";
		$search .= " AND `contentid` = '" . $contentID . "'";
		$search .= " AND `type` =1 ";
		
		$items = $this->builtTreeComment($search);
		$this->enableAvatar = $jacconfig['layout']->get('enable_avatar', 0);
		$avatarSize = $jacconfig['layout']->get('avatar_size', 1);
		if ($avatarSize == 1) {
			//$size = 'height:18px; width:18px;';
			$this->itemImageSize = "18px";
		} else if ($avatarSize == 2) {
			//$size = 'height:26px; width:26px;';
			$this->itemImageSize = "26px";
		} else if ($avatarSize == 3) {
			//$size = 'height:42px; width:42px;';
			$this->itemImageSize = "42px";
		}
		$this->cache = 0;
		$this->type = "RSS2.0";
		$this->title = "JAComment rss";
		$this->items = &$items;
		$this->imgUrl = "";
		$this->setLayout('rss');
	}
	
	/**
	 * Built tree
	 * 
	 * @param string $search  Criteria string to search items
	 * @param string $orderBy Order by string
	 * 
	 * @return array List of items in tree style
	 */
	function builtTreeComment($search, $orderBy = '')
	{
		// get data items
		$model = JACModel::getInstance('comments', 'JACommentModel');
		$items = $model->getItemsRSS($search, $orderBy, 1);
		
		$children = array();
		// first pass - collect children
		$list = array();
		if ($items) {
			foreach ($items as $v) {
				// $pt = $v->parentid;
				if (isset($children[$v->parentid])) {
					$children[$v->parentid][] = $v;
				} else {
					$children[$v->parentid] = array($v);
				}
			}
			
			$list = $this->treerecurse(0, '', array(), $children);
			
			if ($list) {
				foreach ($list as $i => $item) {
					$treename = $item->treename;
					$treename = JFilterOutput::ampReplace($treename);
					$treename = str_replace('"', '&quot;', $treename);
					
					$list[$i]->treename = $treename;
				}
			}
		}
		return $list;
	}
	
	/**
	 * Recursive build tree
	 * 
	 * @param integer $id		 Item id
	 * @param string  $indent	 Indent of item
	 * @param array	  $list		 List of item in tree style
	 * @param array	  &$children Children items
	 * @param integer $maxlevel	 Maximum level of tree
	 * @param integer $level	 Current level of item in tree
	 * @param integer $type		 Type of indent string
	 * 
	 * @return array List of items in tree style
	 */
	function treeRecurse($id, $indent, $list, &$children, $maxlevel = 9999, $level = 0, $type = 1)
	{
		if (@$children[$id] && $level <= $maxlevel) {
			foreach ($children[$id] as $v) {
				$id = $v->id;
				$txt = "";
				if ($type) {
					$pre = '|_&nbsp;';
					$spacer = '';
					if ($level > 0) {
						$spacer = '.';
					}
				} else {
					$pre = '- ';
					$spacer = '&nbsp;&nbsp;';
				}
				
				if ($v->parentid != 0) {
					$txt = $pre;
				}
				
				$pt = $v->parentid;
				$list[$id] = $v;
				
				$list[$id]->treename = "$indent$txt";
				$list[$id]->children = count(@$children[$id]);
				$list[$id]->level = $level + 1;
				$list = $this->treeRecurse($id, $indent . $spacer, $list, $children, $maxlevel, $level + 1, $type);
			}
		}
		return $list;
	}
}
?>