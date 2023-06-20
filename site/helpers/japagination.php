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
jimport('joomla.html.pagination');

/**
 * JACPagination Controller
 *
 * @package		Joomla.Site
 * @subpackage	JAComment
 */
class JACPagination extends JPagination
{
	var $_divid = null;
	var $_link = null;
	
	/**
	 * Constructor
	 *
	 * @param int 	 $total 	 The total number of items
	 * @param int 	 $limitstart The offset of the item to start at
	 * @param int 	 $limit 	 The number of items to display per page
	 * @param string $divid   	 Div id will be updated by ajax
	 * @param string $link 	  	 Paging link
	 * 
	 * @return void
	 */
	function __construct($total, $limitstart, $limit, $divid = '', $link = '')
	{
		// Value/Type checking
		$this->total = (int) $total;
		$this->limitstart = (int) max($limitstart, 0);
		$this->limit = (int) max($limit, 0);
		
		if ($this->limit > $this->total) {
			$this->limitstart = 0;
		}
		
		if (! $this->limit) {
			$this->limit = $total;
			$this->limitstart = 0;
		}
		
		if ($this->limitstart > $this->total) {
			$this->limitstart -= $this->limitstart % $this->limit;
		}
		
		// Set the total pages and current page values
		if ($this->limit > 0) {
			//$this->set('pages_total', ceil($this->total / $this->limit));
			//$this->set('pages_current', ceil(($this->limitstart + 1) / $this->limit));
			$this->pages_total = ceil($this->total / $this->limit);
			$this->pages_current = ceil(($this->limitstart + 1) / $this->limit);
		}
		
		// Set the pagination iteration loop values
		$displayedPages = 10;
		$this->pages_start = (floor(($this->pages_current - 1) / $displayedPages)) * $displayedPages + 1;
		if ($this->pages_start + $displayedPages - 1 < $this->pages_total) {
			$this->pages_stop = $this->pages_start + $displayedPages - 1;
		} else {
			$this->pages_stop = $this->pages_total;
		}
		
		// If we are viewing all records set the view all flag to true
		$this->_viewall = false;
		if ($this->limit == $total) {
			$this->_viewall = true;
		}
		$this->_divid = $divid;
		$this->_link = $link;
	}
	
	/**
	 * Generate active item
	 * 
	 * @param object &$item Paging item
	 * 
	 * @return string HTML paging element
	 */
	function _ja_item_active(&$item)
	{
		$item->link2 = $item->link;
		$pos = strpos($item->link2, "?");
		if (! $pos) {
			$item->link2 .= "?option=com_jacomment&amp;layout=paging&amp;tmpl=component&amp;view=comments";
		} else {
			$item->link2 .= "&amp;option=com_jacomment&amp;layout=paging&amp;tmpl=component&amp;view=comments";
		}
		if ($this->_link) {
			return "<li>&nbsp;<a href=\"javascript:jac_ajaxPagination('" . $item->link2 . "','" . $this->_divid . "');\">" . $item->text . "</a>&nbsp;</li>";
		}
		
		return "<li>&nbsp;<a href=\"javascript:jac_ajaxPagination('" . $item->link2 . "','" . $this->_divid . "');\">" . $item->text . "</a>&nbsp;</li>";
	}
	
	/**
	 * Generate inactive item
	 * 
	 * @param object &$item Paging item
	 * 
	 * @return string HTML paging element
	 */
	function _ja_item_inactive(&$item)
	{
		return "<li>&nbsp;<span>" . $item->text . "</span>&nbsp;</li>";
	}
	
	/**
	 * Generate inactive item with css class
	 * 
	 * @param object &$item Paging item
	 * 
	 * @return string HTML paging element
	 */
	function _ja_item_inactive2(&$item)
	{
		return "<li class=\"active\">&nbsp;<span>" . $item->text . "</span>&nbsp;</li>";
	}
	
	/**
	 * Render paging list
	 * 
	 * @param array $list List of paging item
	 * 
	 * @return string Paging link list HTML string
	 */
	function _list_render($list)
	{
		// Initialize variables
		$lang = Factory::getLanguage();
		$html = '<ul class="pageslist pagination">';
		$html .= '';
		// Reverse output rendering for right-to-left display
		if ($lang->isRTL()) {
			$html .= $list['start']['data'];
			$html .= $list['previous']['data'];
			
			$list['pages'] = array_reverse($list['pages']);
			
			foreach ($list['pages'] as $page) {
				$html .= $page['data'];
			}
			
			$html .= $list['next']['data'];
			$html .= $list['end']['data'];
		} else {
			$html .= $list['start']['data'];
			$html .= $list['previous']['data'];
			
			foreach ($list['pages'] as $page) {
				$html .= $page['data'];
			}
			
			$html .= $list['next']['data'];
			$html .= $list['end']['data'];
		}
		$html .= '';
		$html .= "</ul>";
		return $html;
	}
	
	/**
	 * Paging footer
	 * 
	 * @param array $list List of paging item
	 * 
	 * @return string Paging footer HTML string
	 */
	function _list_footer($list)
	{
		// Initialize variables
		$lang = Factory::getLanguage();
		$html = "<div class=\"list-footer\">\n";
		
		if ($lang->isRTL()) {
			$html .= "\n<div class=\"counter\">" . $list['pagescounter'] . "</div>";
			$html .= $list['pageslinks'];
			$html .= "\n<div class=\"limit\">" . JText::_('DISPLAY_NUM') . $list['limitfield'] . "</div>";
		} else {
			$html .= "\n<div class=\"limit\">" . JText::_('DISPLAY_NUM') . $list['limitfield'] . "</div>";
			$html .= $list['pageslinks'];
			$html .= "\n<div class=\"counter\">" . $list['pagescounter'] . "</div>";
		}
		
		$html .= "\n<input type=\"hidden\" name=\"limitstart\" value=\"" . $list['limitstart'] . "\" />";
		$html .= "\n</div>";
		
		return $html;
	}
	
	/**
	 * Create and return the pagination page list string, ie. Previous, Next, 1 2 3 ... x
	 *
	 * @return string Pagination page list string
	 */
	function getPagesLinks()
	{
		$app = Factory::getApplication();
		$lang = Factory::getLanguage();
		
		// Build the page navigation list
		$data = $this->_buildDataObject();
		$list = array();
		$itemOverride = false;
		$listOverride = false;
		$chromePath = JPATH_THEMES . DS . $app->getTemplate() . DS . 'html' . DS . 'pagination.php';
		if (file_exists($chromePath)) {
			include_once $chromePath;
			if (function_exists('pagination_item_active') && function_exists('pagination_item_inactive')) {
				$itemOverride = true;
			}
			if (function_exists('pagination_list_render')) {
				$listOverride = true;
			}
		}
		
		// Build the select list
		if ($data->all->base !== null) {
			$list['all']['active'] = true;
			$list['all']['data'] = $this->_ja_item_active($data->all);
		} else {
			$list['all']['active'] = false;
			$list['all']['data'] = $this->_ja_item_inactive($data->all);
		}
		
		if ($data->start->base !== null) {
			$list['start']['active'] = true;
			$list['start']['data'] = $this->_ja_item_active($data->start);
		} else {
			$list['start']['active'] = false;
			$list['start']['data'] = $this->_ja_item_inactive($data->start);
		}
		if ($data->previous->base !== null) {
			$list['previous']['active'] = true;
			$list['previous']['data'] = $this->_ja_item_active($data->previous);
		} else {
			$list['previous']['active'] = false;
			$list['previous']['data'] = $this->_ja_item_inactive($data->previous);
		}
		
		$list['pages'] = array(); //make sure it exists
		foreach ($data->pages as $i => $page) {
			if ($page->base !== null) {
				$list['pages'][$i]['active'] = true;
				$list['pages'][$i]['data'] = $this->_ja_item_active($page);
			} else {
				$list['pages'][$i]['active'] = false;
				$list['pages'][$i]['data'] = $this->_ja_item_inactive2($page);
			}
		}
		
		if ($data->next->base !== null) {
			$list['next']['active'] = true;
			$list['next']['data'] = $this->_ja_item_active($data->next);
		} else {
			$list['next']['active'] = false;
			$list['next']['data'] = $this->_ja_item_inactive($data->next);
		}
		if ($data->end->base !== null) {
			$list['end']['active'] = true;
			$list['end']['data'] = $this->_ja_item_active($data->end);
		} else {
			$list['end']['active'] = false;
			$list['end']['data'] = $this->_ja_item_inactive($data->end);
		}
		if ($this->total > $this->limit) {
			return $this->_list_render($list);
		} else {
			return '';
		}
	}
	
	/**
	 * Delete &amp; at beginning of link if paging link has '?' in the end
	 *   
	 * @param string $link The link needs to be correct
	 * 
	 * @return string Correct link
	 */
	function _swithLink($link)
	{
		if ($this->_link) {
			if (substr($this->_link, -1) == "?") {
				$link = str_replace("&amp;", "", $link);
				return $this->_link . $link;
			} else {
				return $this->_link . $link;
			}
		} else {
			return JRoute::_($link);
		}
	}
	
	/**
	 * Build data object
	 * 
	 * @return object Data object
	 */
	function _buildDataObject()
	{
		// Initialize variables
		$data = new stdClass();
		
		$data->all = new JPaginationObject(JText::_('VIEW_ALL'));
		if (! $this->_viewall) {
			$data->all->base = '0';
			$data->all->link = $this->_swithLink("&amp;limitstart=");
		}
		
		// Set the start and previous data objects
		$data->start = new JPaginationObject(JText::_('START'));
		$data->previous = new JPaginationObject(JText::_('PREV'));
		
		if ($this->pages_current > 1) {
			$page = ($this->pages_current - 2) * $this->limit;
			
			$page = $page == 0 ? '0' : $page; //set the empty for removal from route
			

			$data->start->base = '0';
			$data->start->link = $this->_swithLink("&amp;limitstart=0");
			$data->previous->base = $page;
			$data->previous->link = $this->_swithLink("&amp;limitstart=" . $page . "&limit=" . $this->limit);
		}
		
		// Set the next and end data objects
		$data->next = new JPaginationObject(JText::_('NEXT'));
		$data->end = new JPaginationObject(JText::_('END'));
		
		if ($this->pages_current < $this->pages_total) {
			$next = $this->pages_current * $this->limit;
			$end = ($this->pages_total - 1) * $this->limit;
			
			$next = $next == 0 ? '0' : $next;
			$end = $end == 0 ? '0' : $end;
			
			$data->next->base = $next;
			$data->next->link = $this->_swithLink("&amp;limitstart=" . $next . "&limit=" . $this->limit);
			
			$data->end->base = $end;
			$data->end->link = $this->_swithLink("&amp;limitstart=" . $end . "&limit=" . $this->limit);
		}
		
		$data->pages = array();
		$stop = $this->pages_stop;
		for ($i = $this->pages_start; $i <= $stop; $i++) {
			$offset = ($i - 1) * $this->limit;
			
			$offset = $offset == 0 ? '0' : $offset; //set the empty for removal from route
			$data->pages[$i] = new JPaginationObject($i);
			if ($i != $this->pages_current || $this->_viewall) {
				$data->pages[$i]->base = $offset;
				$data->pages[$i]->link = $this->_swithLink("&amp;limitstart=" . $offset . "&limit=" . $this->limit);
			}
		}
		return $data;
	}
}
?>