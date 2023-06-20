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

/**
 * This controller is used for JAImexport feature of the component
 * 
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */
class JACommentControllerImexport extends JACommentController
{
	/**
	 * Constructor
	 * 
	 * @param array $default Array of configuration settings
	 * 
	 * @return void
	 */
	function __construct($default = array())
	{
		parent::__construct($default);
		$this->registerTask('export', 'export');
		
		$inputs = Factory::getApplication()->input;
		$type = $inputs->get('type','');
		
		if ($type == 'xml') {
			$this->registerTask('import', 'importxml');
		} else {
			$this->registerTask('import', 'import');
		}
	}
	
	/**
	 * Display current JAImexport of the component to administrator
	 * 
	 * @return void
	 */
	function display($cachable = false, $urlparams = false)
	{
		parent::display($cachable, $urlparams);
		
		return $this;
	}
	
	/**
	 * Export to XML
	 * 
	 * @return void
	 */
	function export()
	{
		$inputs = Factory::getApplication()->input;
		$inputs->set('layout', 'export');
		
		$model = $this->getModel('imexport');
		$arr_obj = $model->getItems();
		$out = '<?xml version="1.0" encoding="utf-8"?><output>';
		foreach ($arr_obj as $k => $obj) {
			$out .= '
					<comments>';
			foreach ($obj as $key => $val) {
				if ($key != 'referer') {
					if ($val != '') {
						$val = str_replace("&amp;", "&", $val);
						$val = str_replace("&", "&amp;", $val);
						
						if ($key == "comment" || $key == "contenttitle") {
							$out .= '<' . $key . '><![CDATA[' . $val . ']]></' . $key . '>';
						} else {
							$out .= '<' . $key . '>' . $val . '</' . $key . '>';
						}
					} else {
						$out .= '<' . $key . '/>';
					}
				}
			}
			$out .= '</comments><br/>';
		
		}
		$out .= '</output>';
		$download = $model->download($out, 'jacomment.xml');
		exit();
	}
	
	/**
	 * Show comment list
	 * 
	 * @return void
	 */
	function showcomment()
	{
		parent::display();
	}
	
	/**
	 * Import an XML file
	 * 
	 * @return void
	 */
	function importxml()
	{
		$inputs = Factory::getApplication()->input;
		$inputs->set('layout', 'import');
		
		$model = $this->getModel('imexport');
		
		$source = $inputs->getCmd('source');
		
		$rows = array();
		
		jimport('joomla.filesystem.file');
		
		if (isset($_FILES[$source]) && $_FILES[$source]['name'] != '' && strtolower(substr($_FILES[$source]['name'], - 3, 3)) == 'xml') {
			$file = JPATH_COMPONENT_ADMINISTRATOR . DS . 'temp' . DS . substr($_FILES[$source]['name'], 0, strlen($_FILES[$source]['name']) - 4) . time() . rand() . substr($_FILES[$source]['name'], - 4, 4);
			
			if (JFile::upload($_FILES[$source]['tmp_name'], $file)) {
				unset($_FILES[$source]);

				$xml = Factory::getXML($file, true);
				
				//check is valid xml document
				if (!$xml) {
					JError::raiseNotice(1, JText::_('PLEASE_BROWSE_A_VALID_XML_FILE'));
					return $this->setRedirect("index.php?option=com_jacomment&view=imexport&group=import", '');
				} else {
					if ($source == "jacomment") {
						//for jacomment
						$i = 0;
						$allComments = $xml->children();
						
						//check is jacomment xml
						if ($allComments[0]->name() != "comments") {
							JError::raiseNotice(1, JText::_('PLEASE_SELECT_XML_FILE_OF_JACOMMENT_COMPONENT'));
							return $this->setRedirect("index.php?option=com_jacomment&view=imexport&group=import");
						}
						foreach ($allComments as $comments) {
							foreach ($comments->children() as $key => $value) {
								$rows[$i][$value->name()] = (string) $value;
							}
							$i++;
						}
						
						$results = $model->importDataJAComment($rows);
						if ($results == "importSuc") {
							JFile::delete($file);
							$message = JText::_("IMPORT_DATA_SUCCESSFULLY") . ' ' . sizeof($rows) . ' ' . JText::_("RECORDS");
						} else {
							$arrayExitsIDs = array();
							$arrayExitsParentIDs = array();
							
							$arrayExitsIDs = $results["errorID"];
							$arrayExitsParentIDs = $results["erorParentID"];
							$strErrorID = "";
							$strErrorParentID = "";
							if (count($arrayExitsIDs) > 0) {
								if (count($arrayExitsIDs) > 1) {
									$strErrorID = JText::_("CANT_IMPORT_SOME_COMMENT");
									foreach ($arrayExitsIDs as $arrayExitsID) {
										$strErrorID .= " " . $arrayExitsID["id"] . ",";
									}
									$strErrorID = substr($strErrorID, 0, - 1);
									$strErrorID .= JText::_("THEREFORE_THEY_WERE_EXISTED_IN_DATABASE");
								} else {
									$strErrorID = JText::_("CANT_IMPORT_COMMENT") . ' ' . $arrayExitsIDs[0]["id"] . JText::_("BECAUSE_IT_ALREADY_EXISTS") . "<br />";
								}
								JError::raiseNotice(1, $strErrorID);
							}
							if (count($arrayExitsParentIDs) > 0) {
								if (count($arrayExitsParentIDs) > 1) {
									$strErrorParentID = JText::_("CANT_IMPORT_SOME_COMMENT");
									foreach ($arrayExitsParentIDs as $arrayExitsParentID) {
										$strErrorParentID .= " " . $arrayExitsParentID["id"] . ",";
									}
									$strErrorParentID = substr($strErrorParentID, 0, - 1);
									$strErrorParentID .= JText::_("BECAUSE_WE_CANT_FIND_PARENT_OF_IT_IN_DATABASE");
								} else {
									$strErrorParentID = JText::_("CANT_IMPORT_COMMENT") . ' ' . $arrayExitsParentIDs[0]["id"] . JText::_("BECAUSE_WE_CANT_FIND_PARENT_OF_IT_IN_DATABASE") . "<br />";
								}
								JError::raiseNotice(1, $strErrorParentID);
							}
							$resultsSus = sizeof($rows) - (sizeof($arrayExitsIDs) + sizeof($arrayExitsParentIDs));
							if ($resultsSus > 0) {
								$message = JText::_("IMPORT_SUCCESSFULLY") . ' ' . $resultsSus . ' ' . JText::_("RECORDS");
							}
						}
					} else {
						$allComments = $xml->children();
						if ($source == "intensedebate") {
							if ($allComments[0]->name() != "blogpost") {
								JError::raiseNotice(1, JText::_('PLEASE_SELECT_XML_FILE_OF_INTENSEDEBATE_COMMENTS'));
								return $this->setRedirect("index.php?option=com_jacomment&view=imexport&group=import");
							}
						} else if ($source == "disqus") {
							if ($allComments[0]->name() != "article") {
								JError::raiseNotice(1, JText::_('PLEASE_SELECT_XML_FILE_OF_DISQUS_COMMENTS'));
								return $this->setRedirect("index.php?option=com_jacomment&view=imexport&group=import");
							}
						}
						// intensedebate, disqus                          	    
						foreach ($allComments as $blogpost) {
							foreach ($blogpost->children() as $comments) {
								$other[$comments->name()] = (string) $comments;
								foreach ($comments->children() as $key => $value) {
									$comment[$key] = $value->children();
									foreach ($comment[$key] as $k => $v) {
										$rows[$key][$v->name()] = (string) $v;
										if (isset($other["url"]) && $other["url"] != "") {
											$rows[$key]["link"] = $other["url"];
										}
									}
								}
							}
						}
						
						//print_r($rows);die();
						

						if (! $model->importData($source, $other, $rows)) {
							JError::raiseNotice(1, JText::_('CAN_NOT_IMPORT_THE_DATA'));
							return $this->setRedirect("index.php?option=com_jacomment&view=imexport&group=import");
						}
						
						JFile::delete($file);
						$message = JText::_("IMPORT_DATA_SUCCESSFULLY") . ' ' . sizeof($rows) . ' ' . JText::_("RECORDS");
					}
				}
			} else {
				JError::raiseNotice(1, JText::_('CAN_NOT_IMPORT_THE_DATA'));
				return $this->setRedirect("index.php?option=com_jacomment&view=imexport&group=import");
			}
		} else {
			JError::raiseNotice(1, JText::_('CAN_NOT_IMPORT_THE_DATA_PLEASE_BROWSE_AN_XML_FILE'));
		}
		
		return $this->setRedirect("index.php?option=com_jacomment&view=imexport&group=import", $message);
	
	}
	
	/**
	 * Import from other comment system
	 * 
	 * @return void
	 */
	function import()
	{
		$model = $this->getModel('imexport');
		
		$inputs = Factory::getApplication()->input;
		$source = strtolower($inputs->getCmd('source'));
		
		$db = Factory::getDBO();
		
		switch ($source) {
			case 'joomblog':
				$db->setQuery("SELECT * FROM `#__joomblog_comment`");
				break;
			case 'easyblog':
				$db->setQuery("SELECT * FROM `#__easyblog_comment`");
				break;
			case 'compojoomcomments':
				$db->setQuery("SELECT * FROM `#__comment`");
				break;
			case 'jomcomment':
				$db->setQuery("SELECT * FROM `#__jomcomment`");
				break;
            case 'k2comments':
                $db->setQuery( "SELECT * FROM `#__k2_comments`" );
                break;
			case 'jcomments':
			default:
				$db->setQuery("SELECT * FROM `#__jcomments`");
				break;
		}
		
		$rows = $db->loadAssocList();
		
		if (! $model->importData($source, '', $rows)) {
			JError::raiseNotice(1, JText::_('CAN_IMPORT_THE_DATA'));
		}
		
		$message = JText::_("IMPORT_DATA_SUCCESSFULLY") . ' ' . sizeof($rows) . ' ' . JText::_("RECORDS");
		return $this->setRedirect("index.php?option=com_jacomment&view=imexport&group=import", $message);
	}
	
	/**
	 * Show content of com_content
	 * 
	 * @return void
	 */
	function open_content()
	{
		$inputs = Factory::getApplication()->input;
		$inputs->set('layout', 'showcontent');
		parent::display();
		exit();
	}
	
	/**
	 * Show content of com_k2
	 * 
	 * @return void
	 */
	function open_k2()
	{
		$inputs = Factory::getApplication()->input;
		$inputs->set('layout', 'showcontentk2');
		parent::display();
		exit();
	}
	
	/**
	 * Show Component link
	 * 
	 * @return void
	 */
	function getComponent()
	{
		$inputs = Factory::getApplication()->input;
		$id = $inputs->get("id", 0);
		$model = $this->getModel('imexport');
		if ($inputs->getCmd("desoption") == "com_k2") {
			include_once JPATH_SITE . DS . "components" . DS . "com_k2" . DS . "helpers" . DS . "route.php";
			$result = $model->getDataFromK2($id);
			$link = K2HelperRoute::getItemRoute($result->id . ':' . urlencode($result->alias), $result->catID . ':' . urlencode($result->categoryalias));
			echo "com_k2";
			echo "---" . urldecode(JRoute::_($link));
		} else {
			$result = $model->getComponentFromAricleID($id);
			if (isset($result->name)) {
				if ($result->name == "MyBlog") {
					echo "com_myblog";
					$permalink = $model->getMyBlogLink($id);
					echo "---" . JRoute::_("index.php?option=com_myblog&show={$permalink}&Itemid={$id}");
				} else {
					echo "com_content";
					include_once JPATH_SITE . DS . 'components' . DS . 'com_content' . DS . 'helpers' . DS . 'route.php';
					echo "---" . JRoute::_(ContentHelperRoute::getArticleRoute($result->id . ":" . $result->title, $result->catID . ":" . $result->catTitle));
				}
			} else {
				echo "com_content";
				include_once JPATH_SITE . DS . 'components' . DS . 'com_content' . DS . 'helpers' . DS . 'route.php';
				echo "---" . JRoute::_(ContentHelperRoute::getArticleRoute($result->id . ":" . $result->title, $result->catID . ":" . $result->catTitle));
			}
		}
		exit();
	}
	
	/**
	 * Save import comment
	 * 
	 * @return void
	 */
	function saveImportComment()
	{
		$inputs = Factory::getApplication()->input;
		$chkComments = $inputs->get("chkComment",'');
		$comments = $inputs->get("comment",'');
		$names = $inputs->get("name",'');
		$emails = $inputs->get("email",'');
		$websites = $inputs->get("website",'');
		$dates = $inputs->get("date",'');
		$referers = $inputs->get("referer",'');
		$ip_address = $inputs->get("ip_address",'');
		$options = $inputs->get("contentoption",'');
		$titles = $inputs->get("title",'');
		$voteds = $inputs->get("voted",'');
		$contentids = $inputs->get("contentid",'');
		$post = array();
		
		foreach ($chkComments as $i) {
			$post[] = array("comment" => $comments[$i], "name" => $names[$i], "email" => $emails[$i], "website" => $websites[$i], "date" => date("Y-m-d H:i:s", strtotime($dates[$i])), "ip" => $ip_address[$i], "option" => $options[$i], "contentid" => $contentids[$i], "referer" => $referers[$i], "contenttitle" => $titles[$i], "voted" => $voteds[$i]);
		}
		
		$model = $this->getModel('imexport');
		
		if (! $model->importDataDisqus("disqus", $post)) {
			JError::raiseNotice(1, JText::_('CAN_IMPORT_THE_DATA'));
			return $this->setRedirect("index.php?option=com_jacomment&view=imexport&group=import");
		}
		
		$message = JText::_("IMPORT_DATA_SUCCESSFULLY") . ' ' . sizeof($post) . ' ' . JText::_("RECORDS");
		$this->setRedirect("index.php?option=com_jacomment&view=comments&sourcesearch=disqus", $message);
	}
}
?>