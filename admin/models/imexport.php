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

if (! defined('JAC_REGISTERED')) {
	JLoader::register('JACModel', JPATH_ADMINISTRATOR.'/components/com_jacomment/models/model.php');
}

use Joomla\CMS\Factory;

/**
 * This model is used for JAImexport feature of the component
 * 
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */
class JACommentModelImexport extends JACModel
{
	var $OtherCommentSystem;
	
	/**
	 * Get items
	 * 
	 * @return array Array of item objects 
	 */
	function getItems()
	{
		$db = Factory::getDBO();
		$inputs = Factory::getApplication()->input;
		$from = $inputs->getInt('from');
		$num = $inputs->getInt('num');
		
		if (! $from) {
			$from = 1;
		}
		
		if (! $num) {
			$query = "SELECT COUNT(*) FROM #__jacomment_items";
			$db->setQuery($query);
			$num = intval($db->loadResult());
		}
		
		$limit = '';
		if ($from && $num) {
			$from--;
			$limit = " LIMIT $from, $num";
		}
		
		$query = "SELECT * FROM #__jacomment_items " . $limit;
		
		$db->setQuery($query);
		$arr_obj = $db->loadObjectList();
		
		return $arr_obj;
	}
	
	/**
	 * Download a file
	 * 
	 * @param string 	$content 	File path
	 * @param string 	$file 		Download file name
	 * @param boolean 	$download 	File is downloaded or not
	 * 
	 * @return boolean True if file is downloaded and vice versa
	 */
	function download($content, $file, $download = true)
	{
		if (is_file($content)) {
			$content = file_get_contents($content);
		}
		
		if ($download) {
			header("Cache-Control: "); // leave blank to avoid IE errors
			header("Pragma: "); // leave blank to avoid IE errors
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"$file\"");
			echo $content;
			
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Update parent items
	 * 
	 * @param string $source Source of children items
	 * @param array	 $parent Parent item
	 * 
	 * @return void
	 */
	function updateParent($source, $parent)
	{
		$db = Factory::getDBO();
		
		$query = "SELECT id, parentid FROM #__jacomment_items WHERE `source`= '" . $source . "' AND parentid <> 0";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		foreach ($rows as $row) {
			if (isset($parent[$row->parentid])) {
				$db->setQuery("UPDATE #__jacomment_items SET parentid = " . $parent[$row->parentid] . " WHERE id = " . $row->id);
				$db->query();
			}
		}
	}
	
	/**
	 * Import data from JA Comment
	 * 
	 * @param array $rows List of items
	 * 
	 * @return unknown_type Import status
	 */
	function importDataJAComment($rows)
	{
		$db = Factory::getDBO();
		// Returns a reference to the global JSession object, only creating it if it doesn't already exist
		$session = Factory::getSession();
		$ArrayExitsID = array();
		$ArrayNotExitsParent = array();
		//$sessionVote = $session->get('vote', null);				
		foreach ($rows as $key => $val) {
			$model = JACModel::getInstance('comments', 'jacommentModel');
			$result = $model->isExistItemIDParentID($val["id"], $val["parentid"]);
			//if exits comment id in datase - get error.                        
			if ($result == "existID") {
				$ArrayExitsID[] = $val;
			} else if ($result == "notExistParent") {
				//if not exits parentid of comment in database - get error and not insert in database
				$ArrayNotExitsParent[] = $val;
			} else {
				//$model->setState ( 'request', $val );	            
				if (! $id = $model->store($val, 1)) {
					JError::raiseWarning(1, JText::_('DATA_CANNOT_BE_SAVED'));
					return false;
				}
				//$model->setState ( 'request', '' );	            
			}
		}
		if (sizeof($ArrayExitsID) > 0 || sizeof($ArrayNotExitsParent) > 0) {
			$ArrayError = array("errorID" => $ArrayExitsID, "erorParentID" => $ArrayNotExitsParent);
			return $ArrayError;
		}
		return "importSuc";
	}
	
	/**
	 * Import data from Disqus
	 * 
	 * @param string $source Source of comments
	 * @param array  $rows	 List of comments
	 * 
	 * @return integer Number of imported comments
	 */
	function importDataDisqus($source, $rows)
	{
		$db = Factory::getDBO();
		$inputs = Factory::getApplication()->input;
		if ($inputs->getInt("deleteDisqusComment", 0)) {
			$db->setQuery("DELETE FROM #__jacomment_items WHERE source = '" . $source . "'");
			$db->query();
		}
		$countComment = 0;
		foreach ($rows as $key => $val) {
			$val['type'] = 1; // set unapprove                                   
			$val['source'] = $source;
			
			$model = JACModel::getInstance('comments', 'jacommentModel');
			
			$model->setState('request', $val);
			
			if (! $id = $model->store()) {
				JError::raiseWarning(1, JText::_('DATA_CANNOT_BE_SAVED'));
				return false;
			}
			
			$this->updateUrl($id, $val["referer"] . "#jacommentid:" . $id);
			$countComment++;
		}
		return $countComment;
	}
	
	/**
	 * Update referer URL of a comment
	 * 
	 * @param integer $commentID Comment id
	 * @param string  $url		 Correct URL
	 * 
	 * @return void
	 */
	function updateUrl($commentID, $url)
	{
		$db = Factory::getDBO();
		$url = $db->Quote($url);
		$query = "UPDATE #__jacomment_items SET `referer` = $url WHERE `id`=$commentID";
		$db->setQuery($query);
		$db->query();
	}
	
	/**
	 * Import data from others
	 * 
	 * @param string $source Source of comments
	 * @param array  $other  Item list with key is got from XML file
	 * @param array  $rows	 Item list with key is standardised 
	 * 
	 * @return boolean True if have no error and vice versa
	 */
	function importData($source, $other, $rows)
	{
		$db = Factory::getDBO();
		// delete old comment
		if ($source != "jacomment") {
			$db->setQuery("DELETE FROM #__jacomment_items WHERE source = '" . $source . "'");
			$db->query();
		}
		// var_dump($rows);die();
		foreach ($rows as $key => $val) {
			$val['cid'] = $val['id'];
			$val['id'] = '';
			$val['type'] = 0; // set unapprove

			if ($other) {
				$val['referer'] = $other['url'];
			}
			
			$val['source'] = $source;
			
			if ($source == 'intensedebate') {
				$val['voted'] = $val['score'];
			}//import from joomblog
			else if($source=='joomblog'){
				$val['comment'] = $val['comment'];
				$val['title'] 	= $val['contenttitle'];
				$val['date']	= $val['created'];
				$val['referer'] = 'index.php?option=com_joomblog&show='.$val['contentid'];
				$val['option']  = 'com_joomblog';
				$val['date_active'] = $val['modified'];
				$val['userid'] 	= $val['user_id'];
			}
			//import from easyblog
			else if($source == 'easyblog'){
				$val['comment'] = $val['comment'];
				$val['title'] 	= $val['title'];
				$val['date']	= $val['created'];
				$val['contentid'] = $val['post_id'];
				$val['referer'] = 'index.php?option=com_easyblog&view=entry&id='.$val['contentid'];
				$val['option']  = 'com_easyblog';
				$val['date_active'] = $val['publish_up'];
				$val['parentid'] 	= $val['parent_id'];
				$val['voted']		= $val['vote'];
			}
			else if ($source == 'disqus') {
				//$val['comment']
				$val['comment'] = $val['message'];
				$val['voted'] = $val['points'];
				$val['ip'] = $val['ip_address'];
				$val['website'] = $val['url'];
				$val['date'] = date("Y-m-d H:i:s", strtotime($val['date']));
			} else if ($source == 'compojoomcomments') {
				$val['voted'] = $val['voting_yes'] - $val['voting_no'];
				if ($val['parentid'] < 0) {
					$val['parentid'] = 0;
				}
			} else if($source=='jcomments'){
            	$val['option']       = $val['object_group'];
				$val['type']         = 1;
				$val['contentid']    = $val['object_id'];
				
            	if($val['object_group'] == "com_content"){
            		$db->setQuery( "SELECT *,CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(':', a.id, a.alias) ELSE a.id END as slug FROM #__content as a WHERE id={$val['object_id']}" );
        			$resultContent = $db->loadObject();
        		    $val['contenttitle'] = $resultContent->title;
        			if (! class_exists('ContentHelperRoute')) {
						include_once(JPATH_SITE . '/components/com_content/helpers/route.php');
					}
        			$links = JRoute::_(ContentHelperRoute::getArticleRoute($resultContent->slug));
        			$val['referer'] = $links;
            	}
            } else if($source=='k2comments'){
            	$val['option']       = 'com_k2';
            	$val['comment']      = $val['commentText'];
				$val['type']         = $val['published'];
				$val['contentid']    = $val['itemID'];
				$val['name']		 = $val['userName'];
				$val['date']		 = $val['commentDate'];
				$val['date_active']	 = $val['commentDate'];
				$val['userid']		 = $val['userID'];
				$val['website']		 = $val['commentURL'];
				
				$db->setQuery( "SELECT title, alias FROM #__k2_items as i WHERE id={$val['itemID']}" );
				$resultContent = $db->loadObject();
				include_once(JPATH_SITE.DS."components".DS."com_k2".DS."helpers".DS."route.php");
				$link = '';
				$link = K2HelperRoute::getItemRoute($val['itemID'].':'.urlencode($resultContent->alias));
				$val['contenttitle'] = $resultContent->title;
				$val['referer']		 = JRoute::_($link);
			}
			
			$model = JACModel::getInstance('comments', 'jacommentModel');
			
			$model->setState('request', $val);
			
			if (! $id = $model->store()) {
				JError::raiseWarning(1, JText::_('DATA_CANNOT_BE_SAVED'));
				return false;
			}
			
			$parent[$val['cid']] = $id;
			$this->updateParent($source, $parent);
		}
		
		return true;
	}
	
	/**
	 * Check if a component is existed or not
	 * 
	 * @param string $componentOption Component name
	 * 
	 * @return integer Component is existed or not
	 */
	function checkExistComponent($componentOption)
	{
		$db = Factory::getDBO();
		$db->setQuery('SELECT COUNT(*) FROM #__extensions WHERE `element` ="' . $componentOption . '"');
		return $db->loadResult();
	}
	
	/**
	 * Get total records of a table
	 * 
	 * @param string $table Table name
	 * 
	 * @return integer Total records
	 */
	function totalRecord($table)
	{
		$db = Factory::getDBO();
		$db->setQuery('SELECT COUNT(*) FROM `' . $table .'`');
		$total = $db->loadResult();
		return $total;
	}
	
	/**
	 * Show tables name in array
	 * 
	 * @return array Array of table name
	 */
	function showTables()
	{
		$db = Factory::getDBO();
		$db->setQuery('SHOW tables');
		$tables = $db->loadColumn();
		return $tables;
	}
	
	/**
	 * Get item from K2
	 * 
	 * @param integer $itemID Item id
	 * 
	 * @return object Item object
	 */
	function getDataFromK2($itemID)
	{
		$db = Factory::getDBO();
		
		$query = 'SELECT c.id, c.title,c.alias, cc.alias as categoryalias, cc.id as catID, cc.title as catTitle' . ' FROM #__k2_items AS c' . ' LEFT JOIN #__categories AS cc ON cc.id = c.catid' . ' WHERE c.id = "' . $itemID . '"';
		
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result;
	}
	
	/**
	 * Get item from article
	 * 
	 * @param integer $link Article id
	 * 
	 * @return object Item object
	 */
	function getComponentFromAricleID($link)
	{
		$db = Factory::getDBO();
		
		$query = 'SELECT s.name, c.id, c.title, cc.id as catID, cc.title as catTitle' . ' FROM #__content AS c' . ' LEFT JOIN #__categories AS cc ON cc.id = c.catid' . ' LEFT JOIN #__sections AS s ON s.id = c.sectionid' . " WHERE c.id = '$link'";
		
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	/**
     * Get article from title
     * 
     * @param string $component Component name
     * @param string $title 	Article title
     * 
     * @return object Item object
     */
	function getArticleFromTitle($component, $title)
	{
		$db = Factory::getDBO();
		if ($component == "com_myblog") {
			$query = 'SELECT s.name, c.id, c.title, cc.id as catID, cc.title as catTitle' . ' FROM #__content AS c' . ' LEFT JOIN #__categories AS cc ON cc.id = c.catid' . ' LEFT JOIN #__sections AS s ON s.id = c.sectionid' . ' WHERE c.title = "' . $title . '"';
			$db->setQuery($query);
			$result = $db->loadObject();
			if ($result) {
				return $result;
			}
		} else if ($component == "com_k2") {
			$query = 'SELECT c.id, c.title,c.alias, cc.alias as categoryalias, cc.id as catID, cc.title as catTitle' . ' FROM #__k2_items AS c' . ' LEFT JOIN #__categories AS cc ON cc.id = c.catid' . ' WHERE c.title = "' . $title . '"';
			
			$db->setQuery($query);
			$result = $db->loadObject();
			if ($result) {
				return $result;
			}
		} else if ($component == "com_content") {
			$query = 'SELECT s.name, c.id, c.title, cc.id as catID, cc.title as catTitle' . ' FROM #__content AS c' . ' LEFT JOIN #__categories AS cc ON cc.id = c.catid' . ' LEFT JOIN #__sections AS s ON s.id = c.sectionid' . ' WHERE c.title = "' . $title . '"';
			
			$db->setQuery($query);
			$result = $db->loadObject();
			if ($result) {
				return $result;
			}
		}
		return '';
	}
	
	/**
     * Get artile
     * 
     * @param string $component Component name
     * @param string $link 		Link of article
     * 
     * @return object Item object
     */
	function getArticle($component, $link)
	{
		$db = Factory::getDBO();
		if ($component == "com_myblog") {
			for ($i = strlen($link); $i > 0; $i--) {
				if (substr($link, $i, 1) == "/") {
					break;
				}
			}
			$permalink = substr($link, $i + 1);
			
			$query = 'SELECT s.name, c.id, c.title, cc.id as catID, cc.title as catTitle' . ' FROM #__content AS c' . ' LEFT JOIN #__myblog_permalinks AS p ON p.contentid = c.id' . ' LEFT JOIN #__categories AS cc ON cc.id = c.catid' . ' LEFT JOIN #__sections AS s ON s.id = c.sectionid' . ' WHERE p.permalink = "' . $permalink . '"';
			$db->setQuery($query);
			$result = $db->loadObject();
			if ($result) {
				return $result;
			}
		} else if ($component == "com_k2") {
			$pos = strpos($link, "id=");
			if ($pos !== false) {
				$link = substr($link, $pos + 3);
				$pos = strpos($link, ":");
				$id = substr($link, 0, $pos);
				$query = 'SELECT c.id, c.title, cc.id as catID, cc.title as catTitle' . ' FROM #__k2_items AS c' . ' LEFT JOIN #__categories AS cc ON cc.id = c.catid' . " WHERE c.id = $id";
				
				$db->setQuery($query);
				$result = $db->loadObject();
				if ($result) {
					return $result;
				}
			}
			
			preg_match_all("/\/\d+/", $link, $matches);
			
			foreach ($matches[0] as $matche) {
				$id = substr($matche, 1);
				
				$query = 'SELECT c.id, c.title, cc.id as catID, cc.title as catTitle' . ' FROM #__k2_items AS c' . ' LEFT JOIN #__categories AS cc ON cc.id = c.catid' . " WHERE c.id = $id";
				
				$db->setQuery($query);
				$result = $db->loadObject();
				if ($result) {
					return $result;
				}
			}
		} else if ($component == "com_content") {
			$pos = strpos($link, "id=");
			if ($pos !== false) {
				$link = substr($link, $pos + 3);
				$pos = strpos($link, ":");
				$id = substr($link, 0, $pos);
				$query = 'SELECT s.name, c.id, c.title, cc.id as catID, cc.title as catTitle' . ' FROM #__content AS c' . ' LEFT JOIN #__categories AS cc ON cc.id = c.catid' . ' LEFT JOIN #__sections AS s ON s.id = c.sectionid' . " WHERE c.id = $id";
				
				$db->setQuery($query);
				$result = $db->loadObject();
				if ($result) {
					return $result;
				}
			}
			
			preg_match_all("/\/\d+/", $link, $matches);
			
			foreach ($matches[0] as $matche) {
				$id = substr($matche, 1);
				$query = 'SELECT s.name, c.id, c.title, cc.id as catID, cc.title as catTitle' . ' FROM #__content AS c' . ' LEFT JOIN #__categories AS cc ON cc.id = c.catid' . ' LEFT JOIN #__sections AS s ON s.id = c.sectionid' . " WHERE c.id = $id";
				$db->setQuery($query);
				$result = $db->loadObject();
				if ($result) {
					return $result;
				}
			}
		}
		return '';
	}
	
	/**
	 * Get article from link
	 * 
	 * @param string $link Article link
	 * 
	 * @return object Item object
	 */
	function getComponentFromAricleLink($link)
	{
		$db = Factory::getDBO();
		
		$inputs = Factory::getApplication()->input;
		//get destination and source component
		$desComponent = $inputs->getString("desComponent");
		$sourComponent = $inputs->getString("sourComponent");
		
		//if source component equal destination
		if ($desComponent == $sourComponent) {
			return $this->getArticle($sourComponent, $link);
		}
		
		$article = $this->getArticle($sourComponent, $link);
		if ($article) {
			return $this->getArticleFromTitle($desComponent, $article->title);
		}
		return '';
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
		$db = Factory::getDBO();
		$db->setQuery("SELECT permalink FROM #__myblog_permalinks WHERE `contentid` = '$id'");
		return $db->loadResult();
	}
	
	/**
	 * Get all component
	 * 
	 * @return array Array of components
	 */
	function getAllComponent()
	{
		$db = Factory::getDBO();
		$db->setQuery('SELECT DISTINCT(`option`) FROM #__extensions WHERE `element` != ""');
		return $db->loadObjectList();
	}
	
	/**
	 * Get other comment system
	 * 
	 * @return array Array of other comment systems
	 */
	function JAOtherCommentSystem()
	{
		$OtherCommentSystems[] = array(
								'code' => 'jomcomment', 
								'name' => 'Jom Comment', 
								'website' => 'http://www.azrul.com', 
								'table' => '#__jomcomment', 
								'status' => false, 
								'total' => 0
							);
							
		$OtherCommentSystems[] = array(
								'code' => 'jcomments', 
								'name' => 'JComments', 
								'website' => 'http://www.joomlatune.com', 
								'table' => '#__jcomments', 
								'status' => false, 
								'total' => 0
							);
							
        $OtherCommentSystems[] = array(
                                'code' => 'k2comments', 
                                'name' => 'K2 Comments', 
                                'website' => 'http://getk2.org/', 
                                'table' => '#__k2_comments', 
                                'status' => false, 
                                'total' => 0
                            );
							
        $OtherCommentSystems[] = array(
                                'code' => 'compojoomcomments', 
                                'name' => 'Compojoom Comments', 
                                'website' => 'http://compojoom.com/', 
                                'table' => '#__comment', 
                                'status' => false, 
                                'total' => 0,
        						'element'=> 'com_comment'
                            );
		
		return $OtherCommentSystems;
	}
}
?>