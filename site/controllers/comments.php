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
 * JACommentControllerComments Controller
 *
 * @package		Joomla.Site
 * @subpackage	JAComment
 */
class JACommentControllerComments extends JACommentController
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
		
		$this->registerTask('preview', 'previewItems');
	
	}
	
	/**
	 * View items
	 * 
	 * @return void
	 */
	function previewItems()
	{
		$inputs = Factory::getApplication()->input;
		$inputs->set('option', 'com_jacomment');
		$inputs->set('view', 'comments');
		$inputs->set('layout', 'default');
		parent::display();
	
	}
	
	/**
	 * Edit form
	 * 
	 * @return void
	 */
	function edit()
	{
		$inputs = Factory::getApplication()->input;
		$inputs->set('edit', true);
		$inputs->set('layout', 'form');
		parent::display();
	}
	
	/**
	 * Cancel a post
	 * 
	 * @return boolean True if have no error and vice versa
	 */
	function cancel()
	{
		$this->setRedirect('index.php?option=com_jacomment&view=comments');
		return true;
	}
	
	/**
	 * Captcha for add new form
	 * 
	 * @return void
	 */
	function displaycaptchaaddnew()
	{
		global $jacconfig;
		$helper = new JACommentHelpers();
		
		$captcha = new jacapcha();
		
		//custimize captcha image
		include_once $helper->jaLoadBlock("captcha/captcha.php");
		
		$captcha->buildImage("addnew");
		die();
	}
	/**
	 * Captcha for reply form
	 * 
	 * @return void
	 */
	function displaycaptchareply()
	{
		$captcha = new jacapcha();
		$captcha->buildImage("reply");
		die();
	}
	
	/**
	 * Validate captcha from add new form
	 * 
	 * @param string $arg Entered captcha text
	 * 
	 * @return boolean True if captcha is correct and vice versa
	 */
	function validatecaptchaaddnew($arg)
	{
		$captcha = new jacapcha();
		$captcha->text_entered = $arg;
		$captcha->validateText("addnew");
		if ($captcha->valid_text) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Validate captcha from reply form
	 * 
	 * @param string $arg Entered captcha text
	 * 
	 * @return boolean True if captcha is correct and vice versa
	 */
	function validatecaptchareply($arg)
	{
		$captcha = new jacapcha();
		$captcha->text_entered = $arg;
		$captcha->validateText("reply");
		if ($captcha->valid_text) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Download file
	 * 
	 * @return void
	 */
	function downloadfile()
	{
		$inputs = Factory::getApplication()->input;
		$commentID = $inputs->getInt("id", 0);
		$filename = $inputs->getString("filename", "");
		$dlfilename = 'images/stories/ja_comment/' . $commentID . '/' . $filename;
		if ($filename == '') {
			echo "<html><head><title>Downloads</title></head><body>" . JText::_('DOWNLOAD_FILE_NOT_SPECIFIED') . "</body></html>";
			exit();
		} else if (! file_exists($dlfilename)) {
			echo "<html><head><title>Downloads</title></head><body>ERROR: " . JText::_('FILE_NOT_FOUND') . "<!--" . $filename . "--></body></html>";
			exit();
		}
		
		$file_extension = strtolower(substr(strrchr($filename, "."), 1));
		
		switch ($file_extension) {
			case "asf":
				$ctype = "video/x-ms-asf";
				break;
			case "avi":
				$ctype = "video/avi";
				break;
			case "doc":
				$ctype = "application/msword";
				break;
			case "exe":
				$ctype = "application/octet-stream";
				break;
			case "gif":
				$ctype = "image/gif";
				break;
			case "html":
				$ctype = "text/html";
				break;
			case "htm":
				$ctype = "text/html";
				break;
			case "jpeg":
				$ctype = "image/jpg";
				break;
			case "jpg":
				$ctype = "image/jpg";
				break;
			case "mp3":
				$ctype = "audio/mpeg3";
				break;
			case "pdf":
				$ctype = "application/pdf";
				break;
			case "ppt":
				$ctype = "application/vnd.ms-powerpoint";
				break;
			case "png":
				$ctype = "image/png";
				break;
			case "wav":
				$ctype = "audio/wav";
				break;
			case "xls":
				$ctype = "application/vnd.ms-excel";
				break;
			case "zip":
				$ctype = "application/zip";
				break;
			default:
				$ctype = "application/force-download";
				break;
		}
		
		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false); // required for certain browsers
		header("Content-Type: $ctype; name=\"" . basename($filename) . "\";");
		// change, added quotes to allow spaces in filenames, by Rajkumar Singh
		header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\";");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " . filesize($dlfilename));
		readfile("$dlfilename");
		exit();
	}
	
	/**
	 * Cancel upload file in comment
	 * 
	 * @return void
	 */
	function cancelUploadComment()
	{
		if (isset($_SESSION['jactemp'])) {
			jimport('joomla.filesystem.folder');
			JFolder::delete($_SESSION['jactemp']);
			unset($_SESSION['jaccount']);
			unset($_SESSION['tempreply']);
			unset($_SESSION['nameFolderreply']);
			echo '<script language="javascript" type="text/javascript">alert("1");</script>';
		}
	}
	
	/**
	 * Show Terms of usage
	 * 
	 * @return void
	 */
	function showwebsiterules()
	{
		global $jacconfig;
		echo '<div class="jac-text-terms" id="jac-text-terms">' . $jacconfig['spamfilters']->get('terms_of_usage', 0) . '</div>';
		exit();
	}
	
	/**
	 * Upload file when edit comment
	 * 
	 * @return void
	 */
	function uploadFileEdit()
	{
		global $jacconfig;
		$app = Factory::getApplication();
		$helper = new JACommentHelpers();
		$maxSize = (int) $helper->getSizeUploadFile("byte");
		$theme = $jacconfig["layout"]->get("theme", "default");
		$session = Factory::getSession();
		$inputs = Factory::getApplication()->input;
		if ($inputs->getCmd("jacomment_theme", '')) {
			jimport('joomla.filesystem.folder');
			$themeURL = $inputs->getCmd("jacomment_theme");
			if (JFolder::exists('components/com_jacomment/themes/' . $themeURL) || (JFolder::exists('templates/' . $app->getTemplate() . '/html/com_jacomment/themes/' . $themeURL))) {
				$theme = $themeURL;
			}
			$session->set('jacomment_theme', $theme);
		} else {
			if ($session->get('jacomment_theme', null)) {
				$theme = $session->get('jacomment_theme', $theme);
			}
		}
		
		if (isset($_FILES['myfileedit']['name']) && $_FILES['myfileedit']['size'] > 0 && $_FILES['myfileedit']['size'] <= $maxSize && $_FILES['myfileedit']['tmp_name'] != '') {
			if ($this->checkFileUpload($_FILES, "edit")) {
				jimport('joomla.filesystem.folder');
				if (isset($_SESSION['jaccountedit'])) {
					$_SESSION['jaccountedit'] = 0;
				}
				$fileexist = 0;
				$img = '';
				$link = '';
				
				// Edit upload location here
				$fname = basename($_FILES['myfileedit']['name']);
				$fname = strtolower(str_replace(' ', '', $fname));
				$folder = time() . rand() . DIRECTORY_SEPARATOR;
				//$folder = JPATH_ROOT.DS."images".DS."stories".DS."ja_comment";						
				

				if (! isset($_SESSION['jacnameFolderedit'])) {
					$_SESSION['jacnameFolderedit'] = $folder;
				} else {
					$folder = $_SESSION['jacnameFolderedit'];
				}
				
				$destination_path = JPATH_ROOT . DS . "tmp" . DS . "ja_comments" . DS . $folder;
				
				if (! isset($_SESSION['jactempedit'])) {
					$_SESSION['jactempedit'] = $destination_path;
				}
				
				$target_path = $destination_path . '/' . $fname;
				
				if (! is_dir($destination_path)) {
					JFolder::create($destination_path);
				}
				$id = $inputs->getInt("id", 0);
				$listFiles = $inputs->get("listfile",'');
				if (count($listFiles) < $jacconfig['comments']->get("total_attach_file")) {
					//rebuilt listfile					
					foreach ($listFiles as $listFile) {
						$type = substr(strtolower(trim($listFile)), - 3, 3);
						if ($type == 'ocx') {
							$type = "doc";
						}
						$_SESSION['jaccountedit'] += 1;
						
						$_path = JPATH_BASE . DS . "/components/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
						if (file_exists($_path)) {
							$_link = JURI::root() . "/components/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
						} else {
							$_link = JURI::root() . 'templates/' . $app->getTemplate() . "/html/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
						}
						
						$img .= "<div style='float: left; clear: both; white-space: nowrap;'><input type='checkbox' onclick='checkTotalFileEdit()' name='listfile[]' value='$listFile' checked> &nbsp;&nbsp;<img src='" . $_link . "' alt='" . $type . "' /> " . $listFile . "</div>";
					}
					//load file uncheck
					$listFilesInFolders = JFolder::files($destination_path);
					foreach ($listFilesInFolders as $listFilesInFolder) {
						if (! in_array($listFilesInFolder, $listFiles)) {
							$type = substr(strtolower(trim($listFilesInFolder)), - 3, 3);
							if ($type == 'ocx') {
								$type = "doc";
							}
							
							$_path = JPATH_BASE . DS . "/components/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
							if (file_exists($_path)) {
								$_link = JURI::root() . "/components/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
							} else {
								$_link = JURI::root() . 'templates/' . $app->getTemplate() . "/html/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
							}
							
							$img .= "<div style='float: left; clear: both;'><span><input type='checkbox' onclick='checkTotalFileEdit()' name='listfile[]' value='$listFilesInFolder' disabled='disabled'></span>&nbsp;&nbsp;<img src='" . $_link . "' alt='" . $type . "' /> " . $listFilesInFolder . "</div>";
						}
					}
					$listFilesInFolders = JFolder::files(JPATH_ROOT . DS . "images" . DS . "stories" . DS . "ja_comment" . DS . $id);
					foreach ($listFilesInFolders as $listFilesInFolder) {
						if (! in_array($listFilesInFolder, $listFiles)) {
							$type = substr(strtolower(trim($listFilesInFolder)), - 3, 3);
							if ($type == 'ocx') {
								$type = "doc";
							}
							
							$_path = JPATH_BASE . DS . "/components/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
							if (file_exists($_path)) {
								$_link = JURI::root() . "/components/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
							} else {
								$_link = JURI::root() . 'templates/' . $app->getTemplate() . "/html/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
							}
							
							$img .= "<div style='float: left; clear: both;'><span><input type='checkbox' onclick='checkTotalFileEdit()' name='listfile[]' value='$listFilesInFolder' disabled='disabled'></span>&nbsp;&nbsp;<img src='" . $_link . "' alt='" . $type . "' /> " . $listFilesInFolder . "</div>";
						}
					}
					
					if (file_exists($target_path) || file_exists(JPATH_ROOT . DS . "images" . DS . "stories" . DS . "ja_comment" . DS . $id . DS . $fname)) {
						$fileexist = 1;
					} elseif (@move_uploaded_file($_FILES['myfileedit']['tmp_name'], $target_path)) {
						$_SESSION['jaccountedit'] += 1;
						$type = substr(strtolower(trim($_FILES['myfileedit']['name'])), - 3, 3);
						
						if ($type == 'ocx') {
							$type = "doc";
						}
						
						$_path = JPATH_BASE . DS . "/components/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
						if (file_exists($_path)) {
							$_link = JURI::root() . "/components/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
						} else {
							$_link = JURI::root() . 'templates/' . $app->getTemplate() . "/html/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
						}
						
						$img .= "<div style='float: left; clear: both;'><span><input type='checkbox' onclick='checkTotalFileEdit()' name='listfile[]' value='$fname' checked></span>&nbsp;&nbsp;<img src='" . $_link . "' alt='" . $type . "' /> " . $fname . "</div>";
					}
				}
				
				echo '<script language="javascript" type="text/javascript">
		   		var par = window.parent.document;		
				function stopUpload(par, listfile, count, totalUpload){					  		  
						  par.getElementById(\'err_myfileedit\').innerHTML = "";   			  					  
						  par.form1edit.target = "_self";
						  
						  par.getElementById(\'jac_upload_processedit\').style.display=\'none\';						  
						  par.getElementById(\'result_uploadedit\').innerHTML = listfile;
						  par.form1edit.myfileedit.value = "";
						  if(eval(count)>=totalUpload){
						  		if(totalUpload<=1){
						  			par.form1edit.myfileedit.disabled = true;
									par.getElementById(\'err_myfileedit\').innerHTML = "' . JText::_("YOU_ADDED") . '" +" "+ totalUpload + " ' . JText::_("FILE") . '!";
						  		}else{						  		
						  			par.form1edit.myfileedit.disabled = true;
									par.getElementById(\'err_myfileedit\').innerHTML = "' . JText::_("YOU_ADDED") . '" + " " + totalUpload + " ' . JText::_("FILES") . '!";
								}  																
						  }
						  return true;   
				}</script>';
				if ($fileexist) {
					echo '<script language="javascript" type="text/javascript">								
							var par = window.parent.document;		
							par.getElementById(\'jac_upload_processedit\').style.display=\'none\';											
							par.getElementById(\'err_myfileedit\').innerHTML = "<span class=\'err\' style=\'color:red\'>' . JText::_("THIS_FILE_EXISTED") . '</span>";									
							par.getElementById("jac_upload_processedit").style.display="none";
							jQuery(document).ready(function($) {
								var listFiles =  window.parent.$("result_uploadedit").getElements("input[name^=listfile]");
								var currentTotal = 0;
								for(i = 0 ; i< listFiles.length; i++){
									if(listFiles[i].checked == true){
										currentTotal+=1;
									}
								}
								if(currentTotal < '.$jacconfig['comments']->get("total_attach_file").'){
									window.parent.document.getElementById("myfileedit").disabled = false;
									for(i = 0 ; i< listFiles.length; i++){
										if(listFiles[i].checked == false){
											listFiles[i].disabled = false;
										}
									}
								}else{
									window.parent.document.getElementById("myfileedit").disabled = true;
									for(i = 0 ; i< listFiles.length; i++){
										if(listFiles[i].checked == false){
											listFiles[i].disabled = true;
										}
									}
								}
							});
						  </script>';
				} else {
					echo '<script language="javascript" type="text/javascript">stopUpload(par, "' . $img . '", ' . $_SESSION['jaccountedit'] . ', ' . $jacconfig['comments']->get("total_attach_file") . ')</script>';
				}
			} else {
				$attachFileTypes = $jacconfig['comments']->get('attach_file_type', "doc,docx,pdf,txt,zip,rar,jpg,bmp,gif,png");
				$strTypeFile = JText::_("SUPPORT_FILE_TYPE") . ": " . $attachFileTypes . " " . JText::_("ONLY");
				echo '<script language="javascript" type="text/javascript">
							var par = window.parent.document;
							par.getElementById(\'err_myfileedit\').innerHTML = "<span class=\'err\' style=\'color:red\'>' . $strTypeFile . '</span>";
							par.getElementById("jac_upload_processedit").style.display="none";
						  </script>';
			
			}
		} else {
			echo '<script type="text/javascript">					
					var par = window.parent.document;
					var content = "";
					if(document.body){
						document.body.innerHTML = "";
					}		
					par.getElementById(\'jac_upload_processedit\').style.display=\'none\';
					par.form1edit.myfileedit.value = "";
					par.getElementById(\'err_myfileedit\').innerHTML = "' . JText::_("LIMITATION_OF_UPLOAD_IS") . " " . $helper->getSizeUploadFile() . '.";  		
					par.form1edit.myfileedit.focus();
					
				</script>';
		}
	}
	
	/**
	 * Check file type if it is acceptable or not
	 * 
	 * @param array  $file 	 Uploaded file
	 * @param string $action Edit form or add new form
	 * 
	 * @return boolean True if file type is acceptable and vice versa
	 */
	function checkFileUpload($file, $action = '')
	{
		global $jacconfig;
		if ($action) {
			$filename = strtolower(basename($file['myfileedit']['name']));
		} else {
			$filename = strtolower(basename($file['myfile']['name']));
		}
		$ext = substr($filename, strrpos($filename, '.') + 1);
		$attachFileTypes = $jacconfig['comments']->get('attach_file_type', "doc,docx,pdf,txt,zip,rar,jpg,bmp,gif,png");
		$attachFileTypes = explode(",", $attachFileTypes);
		
		if (in_array($ext, $attachFileTypes)) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Upload file
	 * 
	 * @return void
	 */
	function uploadFile()
	{
		global $jacconfig;
		$app = Factory::getApplication();
		$helper = new JACommentHelpers();
		$maxSize = (int) $helper->getSizeUploadFile("byte");
		$theme = $jacconfig["layout"]->get("theme", "default");
		$session = Factory::getSession();
		$inputs = Factory::getApplication()->input;
		if ($inputs->getCmd("jacomment_theme", '')) {
			jimport('joomla.filesystem.folder');
			$themeURL = $inputs->getCmd("jacomment_theme");
			if (JFolder::exists('components/com_jacomment/themes/' . $themeURL) || (JFolder::exists('templates/' . $app->getTemplate() . '/html/com_jacomment/themes/' . $themeURL))) {
				$theme = $themeURL;
			}
			$session->set('jacomment_theme', $theme);
		} else {
			if ($session->get('jacomment_theme', null)) {
				$theme = $session->get('jacomment_theme', $theme);
			}
		}
		//check is valid file type - size of file
		if (isset($_FILES['myfile']['name']) && $_FILES['myfile']['size'] > 0 && $_FILES['myfile']['size'] <= $maxSize && $_FILES['myfile']['tmp_name'] != '') {
			//check extension of file			
			if ($this->checkFileUpload($_FILES)) {
				jimport('joomla.filesystem.folder');
				jimport('joomla.filesystem.file');
				$_SESSION['jaccount'] = 0;
				//echo '<script language="javascript" type="text/javascript">alert("000");</script>';											
				$fileexist = 0;
				$img = '';
				$link = '';
				
				// Edit upload location here
				

				$fname = basename($_FILES['myfile']['name']);
				$fname = strtolower(str_replace(' ', '', $fname));
				$folder = time() . rand() . DIRECTORY_SEPARATOR;
				//$folder = JPATH_ROOT.DS."images".DS."stories".DS."ja_comment";						
				

				if (! isset($_SESSION['jacnameFolder'])) {
					$_SESSION['jacnameFolder'] = $folder;
				} else {
					$folder = $_SESSION['jacnameFolder'];
				}
				
				$destination_path = JPATH_ROOT . DS . "tmp" . DS . "ja_comments" . DS . $folder;
				
				if (! isset($_SESSION['jactemp'])) {
					$_SESSION['jactemp'] = $destination_path;
				}
				
				$target_path = $destination_path . '/' . $fname;
				
				if (! is_dir($destination_path)) {
					JFolder::create($destination_path);
				}
				
				//get array listfile and rebuilt
				$listFiles = $inputs->get("listfile",'');
				$numberOfFile = count($listFiles);
				if ($numberOfFile < $jacconfig['comments']->get("total_attach_file")) {
					//rebuilt listfile					
					foreach ($listFiles as $listFile) {
						$type = substr(strtolower(trim($listFile)), - 3, 3);
						if ($type == 'ocx') {
							$type = "doc";
						}
						$_SESSION['jaccount'] += 1;
						
						$_path = JPATH_BASE . DS . "/components/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
						if (file_exists($_path)) {
							$_link = JURI::root() . "/components/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
						} else {
							$_link = JURI::root() . 'templates/' . $app->getTemplate() . "/html/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
						}
						
						$img .= "<div style='float: left; clear: both; white-space: nowrap;'><!-- <span> --><input type='checkbox' onclick='checkTotalFile()' name='listfile[]' value='$listFile' checked><!-- </span> --> &nbsp;&nbsp;<img src='" . $_link . "' alt='" . $type . "' /> " . $listFile . "</div>";
					}
					//load file uncheck
					$listFilesInFolders = JFolder::files($destination_path);
					foreach ($listFilesInFolders as $listFilesInFolder) {
						if (! in_array($listFilesInFolder, $listFiles)) {
							$type = substr(strtolower(trim($listFilesInFolder)), - 3, 3);
							if ($type == 'ocx') {
								$type = "doc";
							}
							
							$_path = JPATH_BASE . DS . "/components/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
							if (file_exists($_path)) {
								$_link = JURI::root() . "/components/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
							} else {
								$_link = JURI::root() . 'templates/' . $app->getTemplate() . "/html/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
							}
							
							$img .= "<div style='float: left; clear: both;'><span><input type='checkbox' onclick='checkTotalFile()' name='listfile[]' value='$listFilesInFolder' disabled='disabled'></span>&nbsp;&nbsp;<img src='" . $_link . "' alt='" . $type . "' /> " . $listFilesInFolder . "</div>";
						}
					}
					
					if (file_exists($target_path) || in_array($fname, $listFiles)) {
						$fileexist = 1;
					} elseif (@move_uploaded_file($_FILES['myfile']['tmp_name'], $target_path)) {
						// $_SESSION['jaccount'] += 1;	  
						$numberOfFile++;
						$type = substr(strtolower(trim($_FILES['myfile']['name'])), - 3, 3);
						
						if ($type == 'ocx') {
							$type = "doc";
						}
						$_SESSION['jaccount'] += 1;
						
						$_path = JPATH_BASE . DS . "/components/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
						if (file_exists($_path)) {
							$_link = JURI::root() . "/components/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
						} else {
							$_link = JURI::root() . 'templates/' . $app->getTemplate() . "/html/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
						}
						
						$img .= "<div style='float: left; clear: both;'><span><input type='checkbox' onclick='checkTotalFile()' name='listfile[]' value='$fname' checked></span>&nbsp;&nbsp;<img src='" . $_link . "' alt='" . $type . "' /> " . $fname . "</div>";
					}
				}
				
				echo '<script language="javascript" type="text/javascript">
		   		var par = window.parent.document;		
				function stopUpload(par, listfile, count, totalUpload){					  		  
						  par.getElementById(\'err_myfile\').innerHTML = "";   			  					  
						  par.form1.target = "_self";						 
						  par.getElementById(\'jac_upload_process\').style.display=\'none\';
						  par.getElementById(\'result_upload\').innerHTML = listfile;						   
						  par.form1.myfile.value = "";
						  if(eval(count)>=totalUpload){
						  		if(totalUpload<=1){
						  			par.form1.myfile.disabled = true;
									par.getElementById(\'err_myfile\').innerHTML = "' . JText::_("YOU_ADDED") . '" +" "+ totalUpload + " ' . JText::_("FILE") . '!";
						  		}else{
						  			par.form1.myfile.disabled = true;
									par.getElementById(\'err_myfile\').innerHTML = "' . JText::_("YOU_ADDED") . '" +" "+ totalUpload + " ' . JText::_("FILES") . '!";
						  		}
						  		  																
						  }						  		  						  						  						 
						  return true;   
				}</script>';
				
				if ($fileexist) {
					echo '<script language="javascript" type="text/javascript">							
							var par = window.parent.document;
							par.getElementById(\'jac_upload_process\').style.display=\'none\';
							par.getElementById(\'err_myfile\').innerHTML = "<span class=\'err\' style=\'color:red\'>' . JText::_("THIS_FILE_EXISTED") . '</span>";
							par.getElementById("jac_upload_process").style.display="none";
							jQuery(document).ready(function($) {
								var listFiles =  window.parent.$("result_upload").getElements("input[name^=listfile]");
								var currentTotal = 0;
								for(i = 0 ; i< listFiles.length; i++){
									if(listFiles[i].checked == true){
										currentTotal+=1;
									}
								}
								if(currentTotal < '.$jacconfig['comments']->get("total_attach_file").'){
									window.parent.document.getElementById("myfile").disabled = false;
									for(i = 0 ; i< listFiles.length; i++){
										if(listFiles[i].checked == false){
											listFiles[i].disabled = false;
										}
									}
								}else{
									window.parent.document.getElementById("myfile").disabled = true;
									for(i = 0 ; i< listFiles.length; i++){
										if(listFiles[i].checked == false){
											listFiles[i].disabled = true;
										}
									}
								}
							});
						  </script>';
				} else {
					echo '<script language="javascript" type="text/javascript">stopUpload(par, "' . $img . '", ' . $numberOfFile . ', ' . $jacconfig['comments']->get("total_attach_file") . ')</script>';
				}
			
			} else {
				// if extension don't valid
				$attachFileTypes = $jacconfig['comments']->get('attach_file_type', "doc,docx,pdf,txt,zip,rar,jpg,bmp,gif,png");
				$strTypeFile = JText::_("SUPPORT_FILE_TYPE") . ": " . $attachFileTypes . " " . JText::_("ONLY");
				echo '<script language="javascript" type="text/javascript">
							var par = window.parent.document;
							par.getElementById(\'err_myfile\').innerHTML = "<span class=\'err\' style=\'color:red\'>' . $strTypeFile . '</span>";
							par.getElementById("jac_upload_process").style.display="none";
						  </script>';
			}
		} else {
			echo '<script type="text/javascript">
					var par = window.parent.document;
					var content = "";					
					par.getElementById(\'jac_upload_process\').setStyle(\'display\',\'none\');
					par.form1.myfile.value = "";
					par.getElementById(\'err_myfile\').innerHTML = "' . JText::_("LIMITATION_OF_UPLOAD_IS") . ' ' . $helper->getSizeUploadFile() . '!";  		
					par.form1.myfile.focus();
					
				</script>';
		}
	}
	
	/**
	 * Show result after change type
	 * 
	 * @param integer &$k 	   Object index
	 * @param object  &$object HTML object
	 * @param integer $id 	   Comment id
	 * @param string  $action  Delete comment or others
	 * 
	 * @return void
	 */
	function resultChangeType(&$k, &$object, $id, $action = '')
	{
		global $jacconfig;
		
		if ($action == "delete") {
			//disable button reply
			$object[$k] = new stdClass();
			$object[$k]->id = '#jac-span-reply-' . $id;
			$object[$k]->type = 'html';
			$object[$k]->content = "";
			$k++;
			
			$object[$k] = new stdClass();
			$object[$k]->id = '#jac-div-quote-' . $id;
			$object[$k]->type = 'html';
			$object[$k]->content = "";
			$k++;
		} else {
			//enable button reply
			$object[$k] = new stdClass();
			$object[$k]->id = '#jac-span-reply-' . $id;
			$object[$k]->type = 'html';
			$object[$k]->content = '<a id="jac-a-reply-' . $id . '" href="javascript:replyComment(' . $id . ',\'' . JText::_("POSTING") . '\',\'' . JText::_("REPLY") . '\')" title="' . JText::_("REPLY_COMMENT") . '"><span id="reply-' . $id . '">' . JText::_("REPLY") . '</span></a>';
			$k++;
			
			$object[$k] = new stdClass();
			$object[$k]->id = '#jac-div-quote-' . $id;
			$object[$k]->type = 'html';
			$object[$k]->content = '<a id="jac-a-quote-' . $id . '" href="javascript:replyComment(\'' . $id . '\',\'' . JText::_("QUOTING") . '\',\'' . JText::_("QUOTE") . '\',\'quote\')" title="' . JText::_("QUOTE_THIS_COMMENT_AND_REPLY") . '"><span id="quote-' . $id . '">' . JText::_("QUOTE") . '</span></a>';
			$k++;
		}
	}
	
	/**
	 * Save comment id to session
	 * 
	 * @return void
	 */
	function savetosession()
	{
		$inputs = Factory::getApplication()->input;
		$id = $inputs->getInt("id", 0);
		if ($id != 0) {
			$session = Factory::getSession();
			// Put a value in a session var
			$session->set('jaCommentID', $id);
			//jac-lll	
			$jaCommentID = $session->get('jaCommentID');
			echo $jaCommentID;
			exit();
		} else {
			$session = Factory::getSession();
			// Put a value in a session var
			$session->set('jaCommentID', 0);
			echo $session->get('jaCommentID', 0);
			exit();
		}
	}
	
	/**
	 * Rebuild content action after changing type
	 * 
	 * @param integer $type 		 Type of comment
	 * @param integer $itemID 		 Comment id
	 * @param boolean $isSpecialUser Is special user or not
	 * 
	 * @return string Return content after rebuilding
	 */
	function reBuildChangeType($type, $itemID, $isSpecialUser)
	{
		$outputHtml = "";
		$helper = new JACommentHelpers();
		$isAllowEditComment = 1;
		$parentType = 1;
		$model = $this->getModel('comments');
		$parentType = $model->getParentType($itemID);
		
		ob_start();
		include $helper->jaLoadBlock("comments/actions.php");
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	/**
	 * Set css class for element depending on code
	 * 
	 * @param integer $code Code to change css class
	 * 
	 * @return string CSS class name
	 */
	function changeCodeToClass($code)
	{
		if ($code == 1) {
			return "status-isapproved";
		} else if ($code == 2) {
			return "status-isspam";
		} else {
			return "status-isunapproved";
		}
	}
	
	/**
	 * Change type of comment
	 * 
	 * @return void
	 */
	function changeType()
	{
		global $jacconfig;
		
		$helper = new JACommentHelpers();
		$isSpecialUser = $helper->isSpecialUser();
		$model = $this->getModel('comments');
		$inputs = Factory::getApplication()->input;
		$id = $inputs->getInt("id", 0);
		$type = $inputs->getInt("type", 1);
		$currentType = $inputs->getInt("currenttype", 1);
		
		//check current user is special user
		if ($isSpecialUser && $id) {
			//change type of comment
			$model->changeTypeOfComment($id, $type);
			
			$document = Factory::getDocument();
			//get the view name from the query string
			$viewName = $inputs->getString('view', 'comments');
			$viewType = $document->getType();
			//get our view
			$view = $this->getView($viewName, $viewType);
			//some error chec     
			
				$view->setModel($model, true);
			
			
			//get data
			$object = array();
			$k = 0;
			
			$removeClass = $this->changeCodeToClass($currentType);
			$addClass = $this->changeCodeToClass($type);
			
			$message = '<script type="text/javascript">changeClassName(\'jac-change-type-' . $id . '\', "' . $removeClass . '", "' . $addClass . '");</script>';
			$object[$k] = new stdClass();
			$object[$k]->id = '#jac-change-type-' . $id;
			$object[$k]->type = 'html';
			$object[$k]->content = $this->reBuildChangeType($type, $id, $isSpecialUser) . $message;
			$k++;
			
			//disable reply button when change type
			if ($currentType == 1) {
				if ($type != 1) {
					$this->resultChangeType($k, $object, $id, "delete");
				}
			} else {
				//show button reply when approved comment
				if ($type == 1) {
					$this->resultChangeType($k, $object, $id);
				}
			}
			
			if ($currentType == 0) {
				$message = '<script type="text/javascript">changeClassName(\'jac-content-of-comment-' . $id . '\', "comment-ispending", "");</script>';
				$object[$k] = new stdClass();
				$object[$k]->id = '#jac-msg-succesfull';
				$object[$k]->type = 'html';
				$object[$k]->content = $message;
				$k++;
				
				$message = '<script type="text/javascript">jacChangeDisplay(\'jac-badge-pending-' . $id . '\', "none");</script>';
				$object[$k] = new stdClass();
				$object[$k]->id = '#jac-msg-succesfull';
				$object[$k]->type = 'html';
				$object[$k]->content = $message;
				$k++;
			} else {
				if ($type == 0) {
					$message = '<script type="text/javascript">changeClassName(\'jac-content-of-comment-' . $id . '\', "", "comment-ispending");</script>';
					$object[$k] = new stdClass();
					$object[$k]->id = '#jac-msg-succesfull';
					$object[$k]->type = 'html';
					$object[$k]->content = $message;
					$k++;
					
					$message = '<script type="text/javascript">jacChangeDisplay(\'jac-badge-pending-' . $id . '\', "block");</script>';
					$object[$k] = new stdClass();
					$object[$k]->id = '#jac-msg-succesfull';
					$object[$k]->type = 'html';
					$object[$k]->content = $message;
					$k++;
				}
			}
			
			$childArrays = null;
			$model->getChildArray($id, $childArrays);
			if (count($childArrays) > 0) {
				foreach ($childArrays as $childArray) {
					if ($addClass == "status-isspam") {
						$message .= '<script type="text/javascript">changeClassName(\'jac-change-type-' . $childArray->id . '\', "status-isunapproved", "");</script>';
						$message = '<script type="text/javascript">changeClassName(\'jac-change-type-' . $childArray->id . '\', "status-isapproved", "' . $addClass . '");</script>';
					} else if ($addClass == "status-isapproved") {
						$message = '<script type="text/javascript">changeClassName(\'jac-change-type-' . $childArray->id . '\', "status-isspam", "");</script>';
						$message .= '<script type="text/javascript">changeClassName(\'jac-change-type-' . $childArray->id . '\', "status-isunapproved", "' . $addClass . '");</script>';
					} else {
						$message = '<script type="text/javascript">changeClassName(\'jac-change-type-' . $childArray->id . '\', "status-isspam", "' . $addClass . '");</script>';
						$message .= '<script type="text/javascript">changeClassName(\'jac-change-type-' . $childArray->id . '\', "status-isunapproved", "' . $addClass . '");</script>';
					}
					$object[$k] = new stdClass();
					$object[$k]->id = '#jac-change-type-' . $childArray->id;
					$object[$k]->type = 'html';
					$object[$k]->content = $this->reBuildChangeType($type, $childArray->id, $isSpecialUser) . $message;
					$k++;
					
					//disable reply button when change type
					if ($currentType == 1) {
						if ($type != 1) {
							$this->resultChangeType($k, $object, $childArray->id, "delete");
						}
					} else {
						//show button reply when approved comment
						if ($type == 1) {
							$this->resultChangeType($k, $object, $childArray->id);
						}
					}
					if ($addClass == "status-isspam" || $addClass == "status-isapproved") {
						$message = '<script type="text/javascript">changeClassName(\'jac-content-of-comment-' . $childArray->id . '\', "comment-ispending", "");</script>';
						$message .= '<script type="text/javascript">jacChangeDisplay(\'jac-badge-pending-' . $childArray->id . '\', "none");</script>';
					} else {
						$message = '<script type="text/javascript">changeClassName(\'jac-content-of-comment-' . $childArray->id . '\', "", "comment-ispending");</script>';
						$message .= '<script type="text/javascript">jacChangeDisplay(\'jac-badge-pending-' . $childArray->id . '\', "block");</script>';
					}
					$object[$k] = new stdClass();
					$object[$k]->id = '#jac-msg-succesfull';
					$object[$k]->type = 'html';
					$object[$k]->content = $message;
					$k++;
				
				}
			}
			
			echo $helper->parse_JSON_new($object);
			exit();
		}
		exit();
	}
	
	/**
	 * Delete comment
	 * 
	 * @return void
	 */
	function deleteComment()
	{
		global $jacconfig;
		$isCommentJavoice = $jacconfig["general"]->get("is_comment_javoice", 0);
		$inputs = Factory::getApplication()->input;
		$contentoption = $inputs->getCmd('contentoption');
		$id = $inputs->getInt('id', 0);
		$model = $this->getModel('comments');
		
		$wherejatotalcomment = "";
		$wherejacomment = "";
		$this->buildWhereComment($wherejatotalcomment, $wherejacomment);
		
		$limit = $inputs->getInt('limit', $jacconfig["comments"]->get("number_comment_in_page", 10));
		$limitstart = $inputs->getInt('limitstart', 0);
		$helper = new JACommentHelpers();
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		
		$object = array();
		$k = 0;
		if ($id != '') {
			$result = $model->checkSubOfComment($id);
			if (count($result) > 0) {
				$message = '<script type="text/javascript">jacdisplaymessage();</script>';
				$object[$k] = new stdClass();
				$object[$k]->id = '#jac-msg-succesfull';
				$object[$k]->type = 'html';
				$object[$k]->content = $message . JText::_("ERROR_HAS_SUB_COMMENT");
				$k++;
			} else {
				//delete comment
				$comment = $model->deleteComment($id);
				//send mail for author of comment
				

				$userID = $comment->userid;
				
				if ($userID == 0) {
					$userEmail = $comment->email;
					$userName = $comment->name;
				} else {
					$userInfo = Factory::getUser($userID);
					$userEmail = $userInfo->email;
					$userName = $userInfo->name;
				}
				$currentUserInfo = Factory::getUser();
				$content = $helper->replaceBBCodeToHTML($comment->comment);
				
				if ($jacconfig["general"]->get("is_enabled_email", 0)) {
					$helper->sendMailWhenDelete($userName, $userEmail, $content, $comment->referer, $currentUserInfo->name);
				}
				
				if ($jacconfig['comments']->get("is_attach_image", 0)) {
					$file_path = JPATH_ROOT . DS . "images" . DS . "stories" . DS . "ja_comment" . DS . $id;
					if (is_dir($file_path)) {
						JFolder::delete($file_path);
					}
				}
				
				$helper->displayInform(JText::_("COMMENT_WAS_DELETED"), $k, $object);
				
				$document = Factory::getDocument();
				//get the view name from the query string
				$viewName = $inputs->getCmd('view', 'comments');
				$viewType = $document->getType();
				//get our view
				$view = $this->getView($viewName, $viewType);
				//some error chec     
				
					$view->setModel($model, true);
				
				$totalType = $model->getTotalByType($wherejatotalcomment);
				if ($totalType) {
					$totalAll = (int) array_sum($totalType);
				} else {
					$totalAll = 0;
				}
				
				$object[$k] = new stdClass();
				$object[$k]->id = '#jac-number-total-comment';
				$object[$k]->type = 'html';
				if ($totalAll <= 1) {
					$message = JText::_("COMMENT");
				} else {
					$message = JText::_("COMMENTS");
				}
				if($isCommentJavoice && $contentoption){
					$message= JText::_("TOTAL_ANSWER");
					
					$search  = '';
					$search .= ' AND c.option="'.$contentoption.'"';
					$search .= ' AND c.contentid='.$inputs->getInt('contentid', 0).'';
					//get all Item is approved
					if(!$helper->isSpecialUser()){	
						$search .= ' AND type=1';
					}
					$items = $model->getItems($search);
					$items = $model->getItems($search);
					$totalAll = $helper->getTotalAnswer($items);
					
				}
				$object[$k]->content = $totalAll . " " . $message;
				$k++;
				
				$object[$k] = new stdClass();
				$object[$k]->id = '#jac-container-comment';
				$object[$k]->type = 'html';
				$object[$k]->content = $view->loadContentChangeData($wherejatotalcomment, $wherejacomment, $limit, $limitstart, 'paging');
				$k++;
				
				$object[$k] = new stdClass();
				$object[$k]->id = '#jac-container-new-comment';
				$object[$k]->type = 'setdisplay';
				$object[$k]->content = 'show';
				$k++;
				
				$object[$k] = new stdClass();
				$object[$k]->id = '#jac-container-new-comment';
				$object[$k]->type = 'html';
				$object[$k]->content = '';
				$k++;
				
				$object[$k] = new stdClass();
				$object[$k]->id = '#limitstart';
				$object[$k]->type = 'value';
				$object[$k]->content = $limitstart;
				$k++;
				$view->getObjectPaging($object, $k);
			}
		}
		
		echo $helper->parse_JSON_new($object);
		exit();
	}
	
	/**
	 * Show vote text
	 * 
	 * @param integer $id 		  Comment id
	 * @param integer $avatarSize Avatar size from configuration
	 * @param integer $voted 	  Vote status
	 * 
	 * @return string Vote text
	 */
	function resultTextVote($id, $avatarSize, $voted)
	{
		if ($voted == 0) {
			$jacVoteClass = "jac-vote0";
			$totalVote = "(+" . $voted . ")";
		} else if ($voted > 0) {
			$totalVote = "(+" . $voted . ")";
			$jacVoteClass = "jac-vote1";
		} else {
			$totalVote = "(" . $voted . ")";
			$jacVoteClass = "jac-vote-1";
		}
		$txt = '<span class="vote-comment-' . $avatarSize . ' ' . $jacVoteClass . '" id="voted-of-' . $id . '">' . $totalVote . '&nbsp;<strong>' . JText::_("THANKS_FOR_YOUR_VOTE") . '</strong></span>';
		return $txt;
	}
	
	/**
	 * Vote a comment
	 * 
	 * @return void
	 */
	function voteComment()
	{
		global $jacconfig;
		$app = Factory::getApplication();
		
		if (! $jacconfig['comments']->get('is_allow_voting', 1)) {
			//if don't enable voting
			exit();
		}
		
		$helper = new JACommentHelpers();
		$model = $this->getModel('comments');
		
		$avatarSize = $jacconfig['layout']->get('avatar_size');
		;
		
		$currentUserInfo = Factory::getUser();
		
		$inputs = Factory::getApplication()->input;
		$id = $inputs->getInt('id', 0);
		$typeVote = $inputs->getCmd('typevote', '1');
		if ($typeVote == "up") {
			$numberVote = 1;
		} else {
			$numberVote = - 1;
		}
		
		$object = array();
		$k = 0;
		
		$typeVote = $jacconfig['permissions']->get('type_voting', 1);
		
		if (! $currentUserInfo->guest) {
			//if user is loged
			$modelLogs = $this->getModel('logs');
			$logs = $modelLogs->getItemByUser($currentUserInfo->id, $id);
			
			//----------Only one for each comment item---------- 
			if ($typeVote == 1) {
				//if user don't vote
				if (! $logs || $logs->votes == 0) {
					//insert or update voted in table Logs
					if (isset($logs->id)) {
						$post["id"] = $logs->id;
					}
					$post["userid"] = $currentUserInfo->id;
					$post["votes"] = 1;
					$post["itemid"] = $id;
					$post["remote_addr"] = $_SERVER["REMOTE_ADDR"];
					
					//update voted in table comments
					$numberVote = $model->voteComment($id, $numberVote);
					
					//if data binds have error
					if (! $modelLogs->store($post)) {
						$helper->displayInform(JText::_("ERROR_OCCURRED_NOT_SAVE"), $k, $object);
					} else {
						//if data binds is successful
						$object[$k] = new stdClass();
						$object[$k]->id = '#jac-vote-comment-' . $id;
						$object[$k]->type = 'html';
						$object[$k]->content = $this->resultTextVote($id, $avatarSize, $numberVote);
						$k++;
						
						$helper->displayInform(JText::_("THANKS_FOR_YOUR_VOTE"), $k, $object);
					}
				} else {
					$helper->displayInform(JText::_("YOU_ALREADY_VOTED_FOR_THIS_COMMENT"), $k, $object);
				}
			} else if ($typeVote == 2) {
				//----------Only one for each comment item in a session-------- 
				// Returns a reference to the global JSession object, only creating it if it doesn't already exist
				$session = Factory::getSession();
				
				// Get a value from a session var
				$sessionVote = $session->get('vote', null);
				
				//if comment don't exit in session vote												
				if (! isset($sessionVote[$id])) {
					$sessionVote[$id] = $numberVote;
					// Put a value in a session var
					$session->set('vote', $sessionVote);
					//insert or update voted in table Logs						
					$post["userid"] = $currentUserInfo->id;
					if ($logs) {
						$post["id"] = $logs->id;
						$post["votes"] = $logs->votes + 1;
					} else {
						$post["votes"] = 1;
					}
					$post["itemid"] = $id;
					$post["remote_addr"] = $_SERVER["REMOTE_ADDR"];
					
					//update voted in table comments
					$numberVote = $model->voteComment($id, $numberVote);
					
					//if data binds have error
					if (! $modelLogs->store($post)) {
						$helper->displayInform(JText::_("ERROR_OCCURRED_NOT_SAVE"), $k, $object);
					} else {
						//if data binds is successful
						$object[$k] = new stdClass();
						$object[$k]->id = '#jac-vote-comment-' . $id;
						$object[$k]->type = 'html';
						$object[$k]->content = $this->resultTextVote($id, $avatarSize, $numberVote);
						$k++;
						
						$helper->displayInform(JText::_("THANKS_FOR_YOUR_VOTE"), $k, $object);
					}
				} else {
					$helper->displayInform(JText::_("YOU_ALREADY_VOTED_FOR_THIS_COMMENT"), $k, $object);
				}
			} else {
				//----------use lag to voting----------------------
				$lagUserVoting = $jacconfig['permissions']->get('lag_voting', 0);
				//if user don't vote comment
				if (! $logs || $logs->votes == 0) {
					//insert or update voted in table Logs						
					$post["userid"] = $currentUserInfo->id;
					$post["votes"] = 1;
					$post["itemid"] = $id;
					$post["time_expired"] = time() + $lagUserVoting;
					$post["remote_addr"] = $_SERVER["REMOTE_ADDR"];
					
					//update voted in table comments
					$numberVote = $model->voteComment($id, $numberVote);
					
					//if data binds have error
					if (! $modelLogs->store($post)) {
						$helper->displayInform(JText::_("ERROR_OCCURRED_NOT_SAVE"), $k, $object);
					} else {
						//if data binds is successful
						$object[$k] = new stdClass();
						$object[$k]->id = '#jac-vote-comment-' . $id;
						$object[$k]->type = 'html';
						$object[$k]->content = $this->resultTextVote($id, $avatarSize, $numberVote);
						$k++;
						
						$helper->displayInform(JText::_("THANKS_FOR_YOUR_VOTE"), $k, $object);
					}
				} else {
					//if user already voted for comment
					$timeExpired = $logs->time_expired;
					//don't allow user vote because the lag don't over
					if (time() < $timeExpired) {
						$helper->displayInform(JText::_("YOU_ALREADY_VOTED_FOR_THIS_COMMENT"), $k, $object);
					} else {
						$post["userid"] = $currentUserInfo->id;
						if ($logs) {
							$post["id"] = $logs->id;
							$post["votes"] = $logs->votes + 1;
						} else {
							$post["votes"] = 1;
						}
						$post["itemid"] = $id;
						$post["time_expired"] = time() + $lagUserVoting;
						$post["remote_addr"] = $_SERVER["REMOTE_ADDR"];
						
						//update voted in table comments
						$numberVote = $model->voteComment($id, $numberVote);
						
						//if data binds have error
						if (! $modelLogs->store($post)) {
							$helper->displayInform(JText::_("ERROR_OCCURRED_NOT_SAVE"), $k, $object);
						} else {
							//if data binds is successful
							$object[$k] = new stdClass();
							$object[$k]->id = '#jac-vote-comment-' . $id;
							$object[$k]->type = 'html';
							$object[$k]->content = $this->resultTextVote($id, $avatarSize, $numberVote);
							$k++;
							
							$helper->displayInform(JText::_("THANKS_FOR_YOUR_VOTE"), $k, $object);
						}
					
					}
				}
			}
		} else {
			//if user is guest
			//param guest voting	
			$allowGuestVoting = $jacconfig['permissions']->get('vote', 0);
			//allow guest voting
			if ($allowGuestVoting == "all") {
				//----Only one for each comment item------//
				if ($typeVote == "1") {
					$lagGuestVoting = $jacconfig['permissions']->get('lag_voting', 0);
					$cookieName = JApplicationHelper::getHash($app->getName() . 'comments' . $id);
					
					// ToDo - may be adding those information to the session?
					$voted = $inputs->getInt($cookieName, 0);
					if ($voted) {
						$helper->displayInform(JText::_("YOU_ALREADY_VOTED_FOR_THIS_COMMENT"), $k, $object);
					} else {
						setcookie($cookieName, '1', 0);
						
						$numberVote = $model->voteComment($id, $numberVote);
						
						$object[$k] = new stdClass();
						$object[$k]->id = '#jac-vote-comment-' . $id;
						$object[$k]->type = 'html';
						$object[$k]->content = $this->resultTextVote($id, $avatarSize, $numberVote);
						$k++;
						
						$helper->displayInform(JText::_("THANKS_FOR_YOUR_VOTE"), $k, $object);
					}
				} else if ($typeVote == "2") {
					//-Only one for each comment item in a session
					// Returns a reference to the global JSession object, only creating it if it doesn't already exist
					$session = Factory::getSession();
					
					// Get a value from a session var
					$sessionVote = $session->get('vote', null);
					
					//if comment don't exit in session vote												
					if (! isset($sessionVote[$id])) {
						$sessionVote[$id] = $numberVote;
						// Put a value in a session var
						$session->set('vote', $sessionVote);
						
						//update voted in table comments
						$numberVote = $model->voteComment($id, $numberVote);
						
						$helper->displayInform(JText::_("THANKS_FOR_YOUR_VOTE"), $k, $object);
						
						$object[$k] = new stdClass();
						$object[$k]->id = '#jac-vote-comment-' . $id;
						$object[$k]->type = 'html';
						$object[$k]->content = $this->resultTextVote($id, $avatarSize, $numberVote);
						$k++;
					
					} else {
						$helper->displayInform(JText::_("YOU_ALREADY_VOTED_FOR_THIS_COMMENT"), $k, $object);
					}
				} else {
					//-set lag voting
					$lagGuestVoting = $jacconfig['permissions']->get('lag_voting', 0);
					$cookieName = JApplicationHelper::getHash($app->getName() . 'comments' . $id);
					
					// ToDo - may be adding those information to the session?
					$voted = $inputs->getInt($cookieName, 0);
					if ($voted) {
						$helper->displayInform(JText::_("YOU_ALREADY_VOTED_FOR_THIS_COMMENT TODAY"), $k, $object);
					} else {
						setcookie($cookieName, '1', time() + $lagGuestVoting);
						
						$numberVote = $model->voteComment($id, $numberVote);
						
						$object[$k] = new stdClass();
						$object[$k]->id = '#jac-vote-comment-' . $id;
						$object[$k]->type = 'html';
						$object[$k]->content = $this->resultTextVote($id, $avatarSize, $numberVote);
						$k++;
						
						$helper->displayInform(JText::_("THANKS_FOR_YOUR_VOTE"), $k, $object);
					}
				}
			} else {
				//don't allow guest voting
				$helper->displayInform(JText::_("YOU_MUST_LOGIN_TO_VOTE"), $k, $object);
			}
		}
		
		$helper = new JACommentHelpers();
		echo $helper->parse_JSON_new($object);
		exit();
	}
	
	/**
	 * Save edited comment
	 * 
	 * @return void
	 */
	function saveEditComment()
	{
		global $jacconfig;
		
		$app = Factory::getApplication();
		$model = $this->getModel('comments');
		$helper = new JACommentHelpers();
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		$object = array();
		$k = 0;
		$validateData = 1;
		
		$inputs = Factory::getApplication()->input;
		$post["id"] = $inputs->getInt("id", 0);
		$ip = $_SERVER["REMOTE_ADDR"];
		$currentUserInfo = Factory::getUser();
		$email = $currentUserInfo->email;
		
		$messEnableButton = '<script type="text/javascript">enableAddNewComment("btlEditComment");</script>';
		
		$post["comment"] = $inputs->getString('newcomment', '');
		$post["comment"] = $helper->removeEmptyBBCode($post["comment"]);
		$lengthOfComment = $helper->getRealLengthOfComment($post["comment"]);
		
		if ($post["comment"] == "" || $lengthOfComment == 0) {
			$helper->displayInform(JText::_("YOU_MUST_INPUT_YOUR_COMMENT") . $messEnableButton, $k, $object);
			$validateData = 0;
		} else if ($lengthOfComment < $jacconfig['spamfilters']->get("min_length", 0)) {
			if ($validateData) {
				$helper->displayInform(JText::_("YOUR_COMMENT_IS_TOO_SHORT") . $messEnableButton, $k, $object);
			}
			$validateData = 0;
		} else if ($lengthOfComment > $jacconfig['spamfilters']->get("max_length", 500)) {
			if ($validateData) {
				$helper->displayInform(JText::_("YOUR_COMMENT_IS_TOO_LONG") . $messEnableButton, $k, $object);
			}
			$validateData = 0;
		} else {
			//check link in comment	
			//$post["comment"] = $helper ->replaceURLWithHTMLLinks($post["comment"]);
			//$post['comment'] = $helper->replaceBBCodeToHTML($post['comment']);
			if ($model->checkMaxLink($post['comment'], $jacconfig['spamfilters']->get("number_of_links", 5))) {
				if ($validateData) {
					$helper->displayInform(JText::_("NUMBER_OF_LINKS_IN_THE_COMMENT_EXCEED_ITS_MAXIMUM") . $messEnableButton, $k, $object);
				}
				$validateData = 0;
			}
		}
		
		$listFile = $inputs->get('listfile', 0);
		
		if ($validateData) {
			$checkComment = $model->checkBlockedWord($ip, $email, $post['comment']);
			switch ($checkComment) {
				case "IP Blocked":
					$helper->displayInform(JText::_("YOUR_IP_IS_BLOCKED_TEXT") . $messEnableButton, $k, $object);
					break;
				case "Email Blocked":
					$helper->displayInform(JText::_("YOUR_EMAIL_IS_BLOCKED_TEXT") . $messEnableButton, $k, $object);
					break;
				case "Word Blocked":
					$helper->displayInform(JText::_("YOUR_WORD_IN_THE_COMMENT_IS_BLOCKED_TEXT") . $messEnableButton, $k, $object);
					break;
				default:
					$messageBlacklist = "";
					if ($checkComment == "IP Blacklist") {
						$post['type'] = 2;
						$messageBlacklist = JText::_("YOUR_IP_IS_INCLUDED_IN_THE_BLACKLIST");
					} else if ($checkComment == "Email Blacklist") {
						$post['type'] = 2;
						$messageBlacklist = JText::_("YOUR_EMAIL_IS_INCLUDED_IN_THE_BLACKLIST");
					} else if ($checkComment == "Word Blacklist") {
						$post['type'] = 2;
						$messageBlacklist = JText::_("YOUR_WORD_IN_THE_COMMENT_IS_INCLUDED_IN_THE_BLACKLIST");
					} else {
						if ($jacconfig['comments']->get("is_allow_approve_new_comment", 0) 
							&& (($jacconfig['comments']->get("is_allow_approve_member_comment", 0) && ! $helper->isSpecialUser())
								|| ($jacconfig['comments']->get("is_allow_approve_guest_comment", 0) && ! $currentUserInfo->id)
								)
							) {
							$messageBlacklist = JText::_("YOUR_COMMENT_SHALL_BE_APPROVED_BEFORE_BEING_SHOWN");
							$post['type'] = 0;
						} else {
							$post['type'] = 1;
							$post['date_active'] = date("Y-m-d H:i:s");
						}
					}
					//replace censored words
					$post['comment'] = $model->checkCensoredWord($post['comment'], $jacconfig['spamfilters']->get("censored_words", ""), $jacconfig['spamfilters']->get("censored_words_replace", ""));
					
					if ($jacconfig["comments"]->get("is_enable_email_subscription", 0)) {
						$post["subscription_type"] = $inputs->getInt("subscription_type", 0);
					}
					
					$commentID = $model->store($post);
					
					if (! $commentID) {
						$helper->displayInform(JText::_("ERROR_OCCURRED_NOT_SAVE") . $messEnableButton, $k, $object);
					} else {
						$message = '<script type="text/javascript">actionWhenEditSuccess("' . $commentID . '");</script>';
						$helper->displayInform(JText::_("POSTED_COMMENT_SUCCESSFULLY") . ' ' . $message . ' ' . $messageBlacklist, $k, $object);
						
						if ($jacconfig['comments']->get("is_attach_image", 0)) {
							//delete file in store image if remove file
							$listFile = $inputs->get('listfile', 0);
							
							$file_path = JPATH_ROOT . DS . "images" . DS . "stories" . DS . "ja_comment" . DS . $post["id"];
							$listFileOfComments = JFolder::files($file_path);
							
							$stringAttach = "";
							
							//merger and delete if exit file submit in list file. 
							if ($listFileOfComments) {
								foreach ($listFileOfComments as $listFileOfComment) {
									if ($listFile) {
										if (! in_array($listFileOfComment, $listFile)) {
											JFile::delete($file_path . DS . $listFileOfComment);
										}
									} else {
										JFile::delete($file_path . DS . $listFileOfComment);
									}
								}
							}
							
							if ($listFile) {
								if (isset($_SESSION['jactempedit']) && $_SESSION['jaccountedit'] > 0) {
									$listFileTemp = JFolder::files($_SESSION['jactempedit']);
									if ($listFileTemp) {
										foreach ($listFileTemp as $file) {
											if (! in_array($file, $listFile, true)) {
												JFile::delete($_SESSION['jactempedit'] . DS . $file);
											}
										}
									}
									$inputs->set("listfile", implode(',', $listFile));
									
									//move file
									$target_path = JPATH_ROOT . DS . "images" . DS . "stories" . DS . "ja_comment" . DS . $commentID;
									if (! is_dir($target_path)) {
										JFolder::create($target_path);
									}
									
									if ($listFileTemp) {
										JFolder::copy($_SESSION['jactempedit'], $target_path, '', true);
									}
									JFolder::delete($_SESSION['jactempedit']);
									
									unset($_SESSION['jaccountedit']);
									unset($_SESSION['jactempedit']);
									unset($_SESSION['jacnameFolderedit']);
								}
							}
							
							$listFileOfComments = JFolder::files($file_path);
							if ($listFileOfComments) {
								$theme = $jacconfig['layout']->get('theme', 'default');
								$session = Factory::getSession();
								if ($inputs->getCmd("jacomment_theme", '')) {
									jimport('joomla.filesystem.folder');
									$themeURL = $inputs->getCmd("jacomment_theme");
									if (JFolder::exists('components/com_jacomment/themes/' . $themeURL) || (JFolder::exists('templates/' . $app->getTemplate() . '/html/com_jacomment/themes/' . $themeURL))) {
										$theme = $themeURL;
									}
									$session->set('jacomment_theme', $theme);
								} else {
									if ($session->get('jacomment_theme', null)) {
										$theme = $session->get('jacomment_theme', $theme);
									}
								}
								foreach ($listFileOfComments as $listFileOfComment) {
									$linkOfFile = "index.php?tmpl=component&amp;option=com_jacomment&amp;view=comments&amp;task=downloadfile&amp;id=" . $post['id'] . "&amp;filename=" . $listFileOfComment;
									$type = substr(strtolower(trim($listFileOfComment)), - 3, 3);
						
									$_path = JPATH_BASE . DS . "/components/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
									if (file_exists($_path)) {
										$_link = JURI::root() . "/components/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
									} else {
										$_link = JURI::root() . 'templates/' . $app->getTemplate() . "/html/com_jacomment/themes/" . $theme . "/images/" . $type . ".gif";
									}
									
									$stringAttach .= "<img src='" . $_link . "' alt='" . $type . "' /> <a href='" . JRoute::_($linkOfFile) . "'>" . $listFileOfComment . "</a><br />";
								}
							}
							// if ($stringAttach) {
								// $stringAttach = "<div class='jac-list-upload-title'>" . JText::_('LIST_UPLOAD_FILE') . "</div><div class='jac-list-upload-title'>" . $stringAttach . "</div>";
							// }
							
							$object[$k] = new stdClass();
							$object[$k]->id = '#jac-list-attach-file-' . $post["id"];
							$object[$k]->type = 'html';
							$object[$k]->content = $stringAttach;
							$k++;
						}
						//$post["comment"] = $helper ->replaceURLWithHTMLLinks($post["comment"]);
						$post['comment'] = $helper->replaceBBCodeToHTML($post['comment']);
						$object[$k] = new stdClass();
						$object[$k]->id = '#jac-text-' . $post["id"];
						$object[$k]->type = 'html';
						$object[$k]->content = html_entity_decode($helper->showComment($post["comment"]));
						$k++;
					}
					break;
			}
		}
		echo $helper->parse_JSON_new($object);
		exit();
	}
	
	/**
	 * Add new comment
	 * 
	 * @return void
	 */
	function addNewComment()
	{
		global $jacconfig, $session_auth;
		
		$isCommentJavoice = $jacconfig["general"]->get("is_comment_javoice", 0);
		$comment_javoice_level = $jacconfig["general"]->get("comment_javoice_level", 1);
		$inputs = Factory::getApplication()->input;
		$contentoption = $inputs->getCmd('contentoption');
		
		$app = Factory::getApplication();
		if (! isset($jacconfig['comments'])) {
			$jacconfig['comments'] = new JRegistry;
			$jacconfig['comments']->loadString('{}');
		}
		if (! isset($jacconfig['permissions'])) {
			$jacconfig['permissions'] = new JRegistry;
			$jacconfig['permissions']->loadString('{}');
		}
		if (! isset($jacconfig['spamfilters'])) {
			$jacconfig['spamfilters'] = new JRegistry;
			$jacconfig['spamfilters']->loadString('{}');
		}
		//set cid is 0 when add new comment
		$inputs->set("cid", 0);
		$model = $this->getModel('comments');
		$currentUserInfo = Factory::getUser();
		$helper = new JACommentHelpers();
		
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		
		$post['parentid'] = $inputs->getInt('parentid', 0);
		$object = array();
		$k = 0;
		
		$messEnableButton = '<script type="text/javascript">enableAddNewComment("btlAddNewComment");</script>';
		
		$validateData = 1;
		//check total		
		$currentTotal = $inputs->getInt("currenttotal", 1);
		if ($currentTotal >= $jacconfig["comments"]->get("maximum_comment_in_item", 20)) {
			$helper->displayInform(JText::_("COMMENT_IN_THIS_ARTICLE_IS_FULL_TEXT") . $messEnableButton, $k, $object);
			$validateData = 0;
		}
		
		//check permission of user post
		if ($currentUserInfo->guest) {
			if ($jacconfig['permissions']->get("post", "all") != "all") {
				if ($validateData) {
					$helper->displayInform(JText::_("YOU_MUST_LOGIN_TO_POST_COMMENT") . $messEnableButton, $k, $object);
				}
				$validateData = 0;
			}
		}
		if (! $helper->isSpecialUser()) {
			if ($duration = $jacconfig["comments"]->get("duration_post_comment", 0)) {
				$session = Factory::getSession();
				//if have posted
				if ($latest_post_time = $session->get('jacomment_latest_post', null)) {
					if ((time() - $latest_post_time) <= $duration) {
						$helper->displayInform(JText::_("YOU_CAN_WAIT_TO_ADD_NEW_COMMENT") . $messEnableButton, $k, $object);
						$validateData = 0;
					}
				}
				
				if ($validateData) {
					$session->set('jacomment_latest_post', time());
				}
			}
			//check captcha
			$isEnableCaptcha = $jacconfig['spamfilters']->get("is_enable_captcha", 1);
			$isEnableCaptchaUser = $jacconfig['spamfilters']->get("is_enable_captcha_user", 0);
			
			if (($currentUserInfo->guest && $isEnableCaptcha) || (! $currentUserInfo->guest && $isEnableCaptcha && $isEnableCaptchaUser)) {
				if ($jacconfig['spamfilters']->get("is_enable_captcha", 1)) {
					$captcha = $inputs->getString('captcha', '');
					if (! $this->validatecaptchaaddnew($captcha)) {
						if ($validateData) {
							//$helper->displayInform(JText::_ ( "Retype captcha!" ), $k, $object);
							$object[$k] = new stdClass();
							$object[$k]->id = '#err_textCaptcha';
							$object[$k]->type = 'setdisplay';
							$object[$k]->content = 'show';
							$k++;
							
							$message = '<script type="text/javascript">displayErrorAddNew();jacLoadNewCaptcha();</script>';
							$object[$k] = new stdClass();
							$object[$k]->id = '#err_textCaptcha';
							$object[$k]->type = 'html';
							$object[$k]->content = JText::_("YOUR_CAPTCHA_WAS_INVALID_TEXT") . $message . $messEnableButton;
							$k++;
							$validateData = 0;
						}
					}
				}
			}
		}
		
		//get data
		$post["comment"] = $inputs->getString('newcomment', '', '', 'JREQUEST_ALLOWHTML');
		$post["comment"] = trim($helper->removeEmptyBBCode($post["comment"]));
		$lengthOfComment = $helper->getRealLengthOfComment($post["comment"]);
		if ($post["comment"] == "" || $lengthOfComment == 0) {
			if ($validateData) {
				$helper->displayInform(JText::_("YOU_MUST_INPUT_YOUR_COMMENT") . $messEnableButton, $k, $object);
			}
			$validateData = 0;
		} else if ($lengthOfComment < $jacconfig['spamfilters']->get("min_length", 0)) {
			if ($validateData) {
				$helper->displayInform(JText::_("YOUR_COMMENT_IS_TOO_SHORT") . $messEnableButton, $k, $object);
			}
			$validateData = 0;
		} else if ($lengthOfComment > $jacconfig['spamfilters']->get("max_length", 1000)) {
			if ($validateData) {
				$helper->displayInform(JText::_("YOUR_COMMENT_IS_TOO_LONG") . $messEnableButton, $k, $object);
			}
			$validateData = 0;
		} else {
			//check link in comment
			if ($model->checkMaxLink($post['comment'], $jacconfig['spamfilters']->get("number_of_links", 5))) {
				if ($validateData) {
					$helper->displayInform(JText::_("NUMBER_OF_LINKS_IN_THE_COMMENT_EXCEED_ITS_MAXIMUM") . $messEnableButton, $k, $object);
				}
				$validateData = 0;
			}
		}
		
		$post['ip'] = $_SERVER["REMOTE_ADDR"];
		$post['date'] = date("Y-m-d H:i:s");
		$post['contentid'] = $inputs->getInt('contentid', 0);
		$post['option'] = $inputs->getString('contentoption', '0');
		$post['contenttitle'] = $inputs->getString('contenttitle', '');
		
		if ($post['option'] == 'com_content' || $post['option'] == 'com_k2') {
			// get author of current content
			$post['author_id'] = $helper->getArticleAuthor($post['option'], $post['contentid']);
		}
		
		$session = Factory::getSession();
		//$post['referer']  		= $session->get('commenturl', null);						
		$post['referer'] = $inputs->getString('jacomentUrl', $session->get('commenturl', null));
		if ($jacconfig['comments']->get("is_enable_email_subscription")) {
			$post['subscription_type'] = $inputs->getInt('subscription_type', 0);
		}
		$post['source'] = "";
		$post['usertype'] = "";
		//if user is loged
		if (! $currentUserInfo->guest) {
			$post['userid'] = $currentUserInfo->id;
			$post['name'] = $currentUserInfo->name;
			$post['email'] = $currentUserInfo->email;
			
			// ++ add by congtq 03/12/2009
			if ($currentUserInfo->params) {
				$params = new JRegistry;
				$params->loadString($currentUserInfo->params);
				if (array_key_exists('providerName', $params)) {
					$post['usertype'] = $params->get("providerName");
				}
			}
			// -- add by congtq 03/12/2009
		} else {
			//if user is a guest
			$post['name'] = $inputs->getString('name', '');
			$post['email'] = $inputs->getString('email', '');
			
			if ($post['name'] == '') {
				if ($validateData) {
					$helper->displayInform(JText::_("YOU_MUST_INPUT_YOUR_NAME") . $messEnableButton, $k, $object);
				}
				$validateData = 0;
				//jac-text-user
				//islogin
				if ($inputs->getInt('islogin', 0) == 1) {
					$object[$k] = new stdClass();
					$object[$k]->id = '#jac-text-user';
					$object[$k]->type = 'html';
					$object[$k]->content = '<script type="text/javascript">refreshPage();</script>';
					$k++;
				}
			}
			
			if ($post['email'] == '') {
				if ($validateData) {
					$helper->displayInform(JText::_("YOU_MUST_INPUT_YOUR_EMAIL") . $messEnableButton, $k, $object);
				}
				$validateData = 0;
			}
			
			$post['website'] = $inputs->getString('website', '');
		}
		
		if ($validateData) {
			$checkComment = $model->checkBlockedWord($post['ip'], $post['email'], $post['comment']);
			switch ($checkComment) {
				case "IP Blocked":
					$helper->displayInform(JText::_("YOUR_IP_IS_BLOCKED_TEXT") . $messEnableButton, $k, $object);
					break;
				case "Email Blocked":
					$helper->displayInform(JText::_("YOUR_EMAIL_IS_BLOCKED_TEXT") . $messEnableButton, $k, $object);
					break;
				case "Word Blocked":
					$helper->displayInform(JText::_('YOUR_WORD_IN_THE_COMMENT_IS_BLOCKED_TEXT') . $messEnableButton, $k, $object);
					break;
				default:
					$messageBlacklist = "";
					if ($checkComment == "IP Blacklist") {
						$post['type'] = 2;
						$messageBlacklist = JText::_('NOTIFY_IP_BLACK_LIST');
					} else if ($checkComment == "Email Blacklist") {
						$post['type'] = 2;
						$messageBlacklist = JText::_('NOTIFY_EMAIL_BLACK_LIST');
					} else if ($checkComment == "Word Blacklist") {
						$post['type'] = 2;
						$messageBlacklist = JText::_('NOTIFY_WORD_BLACK_LIST');
					} else {
						//if admin need approve comment and this user isn't special user
						if ($jacconfig['comments']->get("is_allow_approve_new_comment", 0) 
							&& (($jacconfig['comments']->get("is_allow_approve_member_comment", 0) && ! $helper->isSpecialUser())
								|| ($jacconfig['comments']->get("is_allow_approve_guest_comment", 0) && ! $currentUserInfo->id)
								)
							) {
							$messageBlacklist = JText::_('YOUR_COMMENT_SHALL_BE_APPROVED_BEFORE_BEING_SHOWN');
							$post['type'] = 0;
						} else {
							$post['type'] = 1;
							$post['date_active'] = date('Y-m-d H:i:s');
						}
					}
					
					//replace censored words
					$post['comment'] = $model->checkCensoredWord($post['comment'], $jacconfig['spamfilters']->get("censored_words", ""), $jacconfig['spamfilters']->get("censored_words_replace", ""));
					$post['voted'] = 0;
					
					// Add value for children, active_children, p0 field
					$post['children'] = 0;
					$post['active_children'] = 0;
					
					// Store location
					if ($jacconfig['layout']->get('enable_location_detection', 0) == 1) {
						$post['address'] = $inputs->getString('address', '');
						$post['latitude'] = $inputs->getString('lat', '');
						$post['longitude'] = $inputs->getString('lng', '');
						
						if (trim($post['address']) == '' || trim($post['address']) == trim(JText::_('LOCATION_WHERE_ARE_YOU'))) {
							$post['address'] = '';
							$post['latitude'] = '';
							$post['longitude'] = '';
						}
					}
					if (! $commentID = $model->store($post)) {
						$helper->displayInform(JText::_("ERROR_OCCURRED_NOT_SAVE"), $k, $object);
					} //if data binds is successful
var_dump($commentID);
var_dump($post);
					//$commentID = 269;
					//if data binds have error					
					if (! $commentID) {
						$helper->displayInform(JText::_("ERROR_OCCURRED_NOT_SAVE") . $messEnableButton, $k, $object);
					} else {
						$post['referer'] = $post['referer'] . "#jacommentid:" . $commentID;
						$model->updateUrl($commentID, $post['referer']);
						
						// Update p0 field
						$model->updateP0FromParent($post['parentid'], $commentID);

						//assign value edit comment.
						if (! $helper->isSpecialUser() && ! $currentUserInfo->guest) {
							$typeEditing = $jacconfig["permissions"]->get("type_editing", 1);
							if ($typeEditing == 2) {
								// Returns a reference to the global JSession object, only creating it if it doesn't already exist
								$session = Factory::getSession();
								// Get a value from a session var
								$sessionAddnew = $session->get('jacaddNew', null);
								//if comment don't exit in session addNew												
								if (! in_array($commentID, $sessionAddnew)) {
									$sessionAddnew[] = $commentID;
									$session->set('jacaddNew', $sessionAddnew);
								}
							}
						}
						
						//information result												
						if ($post["parentid"]) {
							$message = '<script type="text/javascript">cancelComment("completeReply",0,"' . JText::_("REPLY") . '","' . JText::_("POSTING") . '");</script>';
						} else {
							$message = '<script type="text/javascript">completeAddNew(' . $commentID . ');</script>';
						}
						if ($messageBlacklist) {
							$helper->displayInform($messageBlacklist . $message . $messEnableButton, $k, $object, "8000");
						} else {
							$helper->displayInform(JText::_("POSTED_COMMENT_SUCCESSFULLY") . ' ' . $message . ' ' . $messEnableButton, $k, $object);
						}
						
						//insert vote in log						
						if (! $currentUserInfo->guest) {
							$modelLogs = $this->getModel('logs');
							$postLogs["userid"] = $currentUserInfo->id;
							$postLogs["votes"] = 1;
							$postLogs["itemid"] = $commentID;
							$postLogs["remote_addr"] = $_SERVER["REMOTE_ADDR"];
							
							$modelLogs->store($postLogs);
						}
						
						//BEGIN--save upload
						if ($jacconfig['comments']->get("is_attach_image", 0)) {
							//post in reply
							$listFile = $inputs->get('listfile', 0);
							
							if ($listFile) {
								if (isset($_SESSION['jaccount']) && $_SESSION['jaccount'] > 0) {
									//delete some file not in array
									$listFileTemp = JFolder::files($_SESSION['jactemp']);
									//print_r($listFile);
									//print_r($listFileTemp);die();
									if ($listFileTemp) {
										foreach ($listFileTemp as $file) {
											if (! in_array($file, $listFile, true)) {
												JFile::delete($_SESSION['jactemp'] . DS . $file);
											}
										}
									}
									$inputs->set("listfile", implode(',', $listFile));
									
									//move file
									$target_path = JPATH_ROOT . DS . "images" . DS . "stories" . DS . "ja_comment" . DS . $commentID;
									
									if (! is_dir($target_path)) {
										JFolder::create($target_path);
									}
									
									if ($listFileTemp) {
										JFolder::copy($_SESSION['jactemp'], $target_path, '', true);
									}
									
									JFolder::delete($_SESSION['jactemp']);
									
									unset($_SESSION['jaccount']);
									unset($_SESSION['jactemp']);
									unset($_SESSION['jacnameFolder']);
									
									$object[$k] = new stdClass();
									$object[$k]->id = '#result_upload';
									$object[$k]->type = 'html';
									$object[$k]->content = "";
									$k++;
								}
							}
						}
						//END -- save upload
						

						$wherejatotalcomment = "";
						$wherejacomment = "";
						$this->buildWhereComment($wherejatotalcomment, $wherejacomment);
						
						$totalType = $model->getTotalByType($wherejatotalcomment);
						if ($totalType) {
							$totalAll = (int) array_sum($totalType);
						} else {
							$totalAll = 0;
						}
						
						//check current total of comment in currentItems -
						//if total of comment is bigger - don't allow post new comment
						if ($totalAll >= $jacconfig['comments']->get("maximum_comment_in_item")) {
							$script = '<script type="text/javascript">disableReplyButton();</script>';
							$object[$k] = new stdClass();
							$object[$k]->id = '#jac-post-new-comment';
							$object[$k]->type = 'html';
							$object[$k]->content = $script;
							$k++;
						}
						
						$object[$k] = new stdClass();
						$object[$k]->id = '#jac-number-total-comment';
						$object[$k]->type = 'html';
						if ($totalAll <= 1) {
							$message = JText::_("COMMENT");
						} else {
							$message = JText::_("COMMENTS");
						}
						if($isCommentJavoice && $contentoption){
							$message= JText::_("TOTAL_ANSWER");
							$search  = '';
							$search .= ' AND c.option="'.$contentoption.'"';
							$search .= ' AND c.contentid='.$inputs->getInt('contentid', 0).'';
							//get all Item is approved
							if(!$helper->isSpecialUser()){	
								$search .= ' AND type=1';
							}
							$items = $model->getItems($search);
							$items = $model->getItems($search);
							$totalAll = $helper->getTotalAnswer($items);
						}
						$object[$k]->content = $totalAll . " " . $message;
						$k++;
						
						$document = Factory::getDocument();
						//get the view name from the query string
						$viewName = $inputs->getString('view', 'comments');
						$viewType = $document->getType();
						//get our view
						$view = $this->getView($viewName, $viewType);
						//some error chec     
						
							$view->setModel($model, true);
						
						
						$item = $model->getItemFrontEnd($commentID);
						
						//reply comment
						if ($post['parentid']) {
							$numberOfChild = $model->getNumberChildOfItems($post['parentid']);
							
							// After adding comment, update number of children
							$model->updateChildren($post['parentid']);

							$script = '<script type="text/javascript">updateTotalChild("' . $post['parentid'] . '", "' . JText::_("REPLIES") . '");</script>';
							if ($numberOfChild == 1) {
								$object[$k] = new stdClass();
								$object[$k]->id = '#jac-div-show-child-' . $post['parentid'];
								$object[$k]->type = 'html';
								$object[$k]->content = '<a href="javascript:displayChild(\'' . $post['parentid'] . '\')" title="' . JText::_("SHOW_ALL_CHILDREN_COMMENT_OF_THIS_COMMENT") . '" class="showreply-btn" id="a-show-childen-comment-of-' . $post['parentid'] . '" style="display: none;">' . JText::_('SHOW') . '&nbsp;<span id=\'jac-show-total-childen-' . $post['parentid'] . '\'>1</span>&nbsp;<span id=\'jac-show-text-childen-' . $post['parentid'] . '\'>' . JText::_("REPLY") . '</span></a>
								<a href="javascript:displayChild(\'' . $post['parentid'] . '\')" title="' . JText::_("HIDE_ALL_CHILDREN_COMMENT_OF_THIS_COMMENT") . '" class="hidereply-btn" id="a-hide-childen-comment-of-' . $post['parentid'] . '">' . JText::_('HIDE') . '&nbsp;<span id=\'jac-hide-total-childen-' . $post['parentid'] . '\'>1</span>&nbsp;<span id=\'jac-hide-text-childen-' . $post['parentid'] . '\'>' . JText::_("REPLY") . '</span></a>' . $script;
								
								$k++;
							} else {
								$object[$k] = new stdClass();
								$object[$k]->id = '#jac-show-total-childen-' . $post['parentid'];
								$object[$k]->type = 'html';
								$object[$k]->content = $numberOfChild . $script;
								$k++;
								
								$object[$k] = new stdClass();
								$object[$k]->id = '#jac-hide-total-childen-' . $post['parentid'];
								$object[$k]->type = 'html';
								$object[$k]->content = $numberOfChild . $script;
								$k++;
							}
							
							if ($jacconfig['general']->get("is_enabled_email", 0)) {
								//if enable email subcription - send mail
								$helper->sendAddNewMail($commentID, $wherejatotalcomment, 'reply', $post);
							}
							
							$message = '<script type="text/javascript">moveBackground(' . $commentID . ', "' . JURI::root() . '")</script>';
							$object[$k] = new stdClass();
							$object[$k]->id = '#childen-comment-of-' . $post['parentid'];
							$object[$k]->type = 'html';
							
							if ($post["type"] == 0) {
								$object[$k]->content = $view->loadContentChangeData($wherejatotalcomment, $wherejacomment, '', '', 'getChilds', $commentID) . $message;
							} else {
								$object[$k]->content = $view->loadContentChangeData($wherejatotalcomment, $wherejacomment, '', '', 'getChilds') . $message;
							}
							$k++;
						} else {
							//add new comment
							$message = '<script type="text/javascript">moveBackground(' . $commentID . ', "' . JURI::root() . '")</script>';
							$object[$k] = new stdClass();
							$object[$k]->id = '#jac-container-new-comment';
							$object[$k]->type = 'setdisplay';
							$object[$k]->content = 'show';
							$k++;
							
							$object[$k] = new stdClass();
							$object[$k]->id = '#jac-container-new-comment';
							$object[$k]->type = 'html';
							$object[$k]->content = $view->showComment($item) . $message;
							$object[$k]->action = 'newComment';
							$k++;
							
							if ($jacconfig['general']->get("is_enabled_email")) {
								//if enable email subcription - send mail
								$helper->sendAddNewMail($commentID, $wherejatotalcomment, 'addNew', $post);
							}
						}
					}
					break;
			}
		}
		
		echo $helper->parse_JSON_new($object);
		exit();
	}
	
	/**
	 * Build WHERE criteria for showing comment items
	 * 
	 * @param string &$wherejatotalcomment Criteria for getting total of comments query
	 * @param string &$wherejacomment 	   Criteria for getting comments query
	 * 
	 * @return void
	 */
	function buildWhereComment(&$wherejatotalcomment, &$wherejacomment)
	{
		$helper = new JACommentHelpers();
		$inputs = Factory::getApplication()->input;
		$contentOption = $inputs->getString('contentoption', '');
		$contentID = $inputs->getInt('contentid', 0);
		$commentType = $inputs->getInt('commenttype', 1);
		$parentID = $inputs->getInt('parentid', 0);
		
		$wherejatotalcomment = " AND c.option= '" . $contentOption . "'";
		$wherejatotalcomment .= " AND c.contentid= '" . $contentID . "'";
		//check user is specialUser
		$isSpecialUser = $helper->isSpecialUser();
		//get aproved comment if user isn't special User
		if (! $isSpecialUser) {
			$wherejatotalcomment .= " AND c.type = " . $commentType;
		}
		$wherejacomment = $wherejatotalcomment;
		$wherejacomment .= " AND c.parentid = " . $parentID . "";
	}
	
	/**
	 * Show result after reporting a comment
	 * 
	 * @param integer $id Comment id
	 * 
	 * @return string HTML string result after reporting a comment
	 */
	function resultReportComment($id)
	{
		//$result = '<input type="button" disabled="disabled" id="btl-jac-report-'.$id.'" value="'.JText::_("REPORT").'" onclick="reportComment('.$id.')">';
		$result = "<a href='javascript:undoReportComment(" . $id . ")' class='jac-undo-report' title='" . JText::_("UNDO_REPORT") . "'>[" . JText::_("UNDO") . "]</a>";
		return $result;
	}
	
	/**
	 * Show result after undoing a report
	 * 
	 * @param integer $id Comment id
	 * 
	 * @return string HTML string result after undoing a report
	 */
	function resultUndoReportComment($id)
	{
		global $jacconfig;
		//layout[button_type]		
		$result = '<a class="report-btn" href="javascript:reportComment(' . $id . ')" title="' . JText::_("FLAGGED_REPORT_TEXT") . '">' . JText::_("REPORT") . '</a>';
		return $result;
	}
	
	/**
	 * Undo report
	 * 
	 * @return void
	 */
	function undoReportComment()
	{
		global $jacconfig;
		
		$app = Factory::getApplication();
		if (! $jacconfig["comments"]->get("is_allow_report", 0)) {
			exit();
		}
		
		$helper = new JACommentHelpers();
		
		$model = $this->getModel('comments');
		$totalToReportSpam = $jacconfig['permissions']->get('total_to_report_spam');
		$currentUserInfo = Factory::getUser();
		
		//id of comment
		$inputs = Factory::getApplication()->input;
		$id = $inputs->getInt('id', 0);
		
		$object = array();
		$k = 0;
		
		//if user is loged
		if (! $currentUserInfo->guest) {
			$isAllowReport = $jacconfig['comments']->get('is_allow_report', 0);
			//if allow user voting comment
			if ($isAllowReport) {
				$modelLogs = $this->getModel('logs');
				$logs = $modelLogs->getItemByUser($currentUserInfo->id, $id);
				
				//update voted in table comments
				$numberReport = $model->undoReportComment($id);
				
				$modelLogs->updateReport($logs->id, 0);
				
				if ($numberReport == ($totalToReportSpam - 1)) {
					$model->changeTypeOfComment($id, 1);
				}
				
				$object[$k] = new stdClass();
				$object[$k]->id = '#jac-show-report-' . $id;
				$object[$k]->type = 'html';
				$object[$k]->content = $this->resultUndoReportComment($id);
				$k++;
				
				$helper->displayInform(JText::_("UNDO_REPORT_SUCCESSFUL"), $k, $object);
			} else {
				//don't allow user voting comment
				$helper->displayInform(JText::_("NOT_ALLOW_REPORT_COMMENT"), $k, $object);
			}
		} else {
			//if user is guest
			//param guest voting	
			$isAllowReport = $jacconfig['comments']->get('is_allow_report', 0);
			
			//allow guest voting
			if ($isAllowReport) {
				$cookieName = JApplicationHelper::getHash($app->getName() . 'reportcomments' . $id);
				
				// ToDo - may be adding those information to the session?
				$voted = $inputs->getInt($cookieName, 0);
				if ($voted) {
					setcookie($cookieName, '1', time() - 3600);
					
					$numberReport = $model->undoReportComment($id);
					
					if ($numberReport == ($totalToReportSpam - 1)) {
						$model->changeTypeOfComment($id, 1);
					}
					
					$object[$k] = new stdClass();
					$object[$k]->id = '#jac-show-report-' . $id;
					$object[$k]->type = 'html';
					$object[$k]->content = $this->resultUndoReportComment($id);
					$k++;
					
					$helper->displayInform(JText::_("UNDO_REPORT_SUCCESSFUL"), $k, $object);
				}
			} else {
				//don't allow guest voting
				$helper->displayInform(JText::_("YOU_MUST_LOGIN_TO_REPORT"), $k, $object);
			}
		}
		
		$helper = new JACommentHelpers();
		echo $helper->parse_JSON_new($object);
		exit();
	}
	
	/**
	 * Report a comment
	 * 
	 * @return void
	 */
	function reportcomment()
	{
		global $jacconfig;
		$app = Factory::getApplication();
		$helper = new JACommentHelpers();
		
		if (! $jacconfig["comments"]->get("is_allow_report", 0)) {
			exit();
		}
		
		$model = $this->getModel('comments');
		$totalToReportSpam = $jacconfig['permissions']->get('total_to_report_spam');
		$currentUserInfo = Factory::getUser();
		
		//id of comment
		$inputs = Factory::getApplication()->input;
		$id = $inputs->getInt('id', 0);
		
		$object = array();
		$k = 0;
		
		//if user is loged
		if (! $currentUserInfo->guest) {
			$isAllowReport = $jacconfig['comments']->get('is_allow_report', 0);
			//if allow user report comment
			if ($isAllowReport) {
				$modelLogs = $this->getModel('logs');
				$logs = $modelLogs->getItemByUser($currentUserInfo->id, $id);
				
				//----------Only one for each comment item----------
				//if user don't report
				if (! $logs || $logs->reports == 0) {
					//insert or update voted in table Logs
					if (isset($logs)) {
						$post["id"] = $logs->id;
					}
					
					$post["userid"] = $currentUserInfo->id;
					$post["reports"] = 1;
					$post["itemid"] = $id;
					$post["remote_addr"] = $_SERVER["REMOTE_ADDR"];
					
					//update voted in table comments
					$numberReport = $model->reportComment($id);
					//set comment is spam comment if number report of comment equal totalReport
					

					//if data binds have error
					if (! $modelLogs->store($post)) {
						$helper->displayInform(JText::_("ERROR_OCCURRED_NOT_SAVE"), $k, $object);
					} else {
						//if data binds is successful
						//mask spam comment in database
						if ($numberReport == $totalToReportSpam) {
							$model->changeTypeOfComment($id, 2, false, "reportspam");
						}
						$object[$k] = new stdClass();
						$object[$k]->id = '#jac-show-report-' . $id;
						$object[$k]->type = 'html';
						$object[$k]->content = $this->resultReportComment($id);
						$k++;
						
						$helper->displayInform(JText::_("THANKS_FOR_YOUR_REPORT"), $k, $object);
					}
				} else {
					$helper->displayInform(JText::_("YOU_ALREADY_REPORT_FOR_THIS_COMMENT"), $k, $object);
				}
			
			} else {
				//don't allow user voting comment
				$helper->displayInform(JText::_("NOT_ALLOW_REPORT_COMMENT"), $k, $object);
			}
		} else {
			//if user is guest
			//param guest voting	
			$isAllowReport = $jacconfig['comments']->get('is_allow_report', 0);
			
			//allow guest voting
			if ($isAllowReport) {
				$cookieName = JApplicationHelper::getHash($app->getName() . 'reportcomments' . $id);
				
				// ToDo - may be adding those information to the session?
				$voted = $inputs->getInt($cookieName, 0);
				if ($voted) {
					$helper->displayInform(JText::_("YOU_ALREADY_REPORTED_FOR_THIS_COMMENT"), $k, $object);
				} else {
					setcookie($cookieName, '1', 0);
					
					$numberReport = $model->reportComment($id);
					//mask spam comment in database
					if ($numberReport == $totalToReportSpam) {
						$model->changeTypeOfComment($id, 2, false, "reportspam");
					}
					//set comment is spam comment if number report of comment equal totalReport										
					$object[$k] = new stdClass();
					$object[$k]->id = '#jac-show-report-' . $id;
					$object[$k]->type = 'html';
					$object[$k]->content = $this->resultReportComment($id);
					$k++;
					
					$helper->displayInform(JText::_("THANKS_FOR_YOUR_REPORT"), $k, $object);
				
				}
			} else {
				//don't allow guest voting
				$helper->displayInform(JText::_("YOU_MUST_LOGIN_TO_REPORT"), $k, $object);
			}
		}
		
		echo $helper->parse_JSON_new($object);
		exit();
	}
	
	/**
	 * Attach file
	 * 
	 * @return void
	 */
	function attachFile()
	{
		$app = Factory::getApplication();
		$model = $this->getModel('comments');
		$inputs = Factory::getApplication()->input;
		$option	= $inputs->getCmd('option', '');
		
		jimport('joomla.filesystem.file');
		if (isset($_FILES['userfile']) && $_FILES['userfile']['name'] != '') {
			$desk = JPATH_COMPONENT_ADMINISTRATOR . DS . 'temp' . DS . substr($_FILES['userfile']['name'], 0, strlen($_FILES['userfile']['name']) - 4) . time() . rand() . substr($_FILES['userfile']['name'], - 4, 4);
			
			if (JFile::upload($_FILES['userfile']['tmp_name'], $desk)) {
				$filecontent = JFile::read($desk);
				if (! $model->import($filecontent)) {
					return $this->setRedirect("index.php?option=$option&view=jaemail");
				}
				
				$filter_lang = $app->getUserStateFromRequest($option . '.jaemail.filter_lang', 'filter_lang', 'en-GB', 'string');
				return $this->setRedirect("index.php?option=$option&view=jaemail&filter_lang=$filter_lang", JText::_('IMPORT_SUCCESS'));
			}
			unset($_FILES['userfile']);
			$app->enqueueMessage(
				JText::_('UPLOAD_FILE_NOT_SUCCESS'),
				'warning'
			);
			//JError::raiseWarning(1, JText::_('UPLOAD_FILE_NOT_SUCCESS'));
			return $this->setRedirect("index.php?option=$option&view=jaemail&task=show_import");
		}
	}
	
	// ++ add by congtq 26/11/2009
	/**
	 * Open YouTube form
	 * 
	 * @return void
	 */
	function open_youtube()
	{
		global $jacconfig;
		$inputs = Factory::getApplication()->input;
		$cid = $inputs->get('cid', array(0), 'array');
		JArrayHelper::toInteger($cid, array(0));
		$id = $cid[0] ? $cid[0] : '';
		
		$helper = new JACommentHelpers();
		include_once $helper->jaLoadBlock("comments/youtube.php");
	}
	
	/**
	 * Embed YouTube link
	 * 
	 * @return void
	 */
	function embed_youtube()
	{
		$helper = new JACommentHelpers();
		
		$inputs = Factory::getApplication()->input;
		$post = $inputs->get('request');
		
		$object = array();
		$k = 0;
		
		if (! $helper->checkYoutubeLink($post['txtYouTubeUrl'])) {
			$helper->displayInform(JText::_("YOUTUBE_VIDEO_URL_IS_INCORRECT"), $k, $object);
		} else {
			$element = $post['id'] ? "edit" : "";
			
			$k = 0;
			$object[$k] = new stdClass();
			$object[$k]->id = '#newcomment' . $element;
			$object[$k]->type = 'append';
			$object[$k]->status = 'ok';
			$object[$k]->content = '[youtube ' . $post['txtYouTubeUrl'] . ' youtube]';
			$k++;
			
			$helper->displayInform(JText::_("VIDEO_IS_EMBED"), $k, $object, 0, true);
		}
		
		echo $helper->parse_JSON_new($object);
		exit();
	}
	// -- add by congtq 26/11/2009
	

	// ++ add by congtq 01/12/2009
	/**
	 * Open log in form
	 * 
	 * @return void
	 */
	function open_login()
	{
		$currentUserInfo = Factory::getUser();
		$inputs = Factory::getApplication()->input;
		if ($currentUserInfo->id) {
			$ses_url = $_SESSION['ses_url'];
			$this->setRedirect($ses_url);
		} else {
			$inputs->set('view', 'users');
			$inputs->set('layout', 'login');
			parent::display();
		}
	}
	// -- add by congtq 01/12/2009
	

	/**
	 * Open attach file layout when add new comment
	 * 
	 * @return void
	 */
	function open_attach_file()
	{
		global $jacconfig;
		$inputs = Factory::getApplication()->input;
		$cid = $inputs->get('cid', array(0), 'array');
		JArrayHelper::toInteger($cid, array(0));
		$id = $cid[0] ? $cid[0] : '';
		$action = "addnew";
		$totalAttachFile = $jacconfig["comments"]->get("total_attach_file", 5);
		$theme = $jacconfig["layout"]->get("theme", "default");
		$session = Factory::getSession();
		if ($inputs->getCmd("jacomment_theme", '')) {
			jimport('joomla.filesystem.folder');
			$themeURL = $inputs->getCmd("jacomment_theme");
			if (JFolder::exists('components/com_jacomment/themes/' . $themeURL) || (JFolder::exists('templates/' . $app->getTemplate() . '/html/com_jacomment/themes/' . $themeURL))) {
				$theme = $themeURL;
			}
			$session->set('jacomment_theme', $theme);
		} else {
			if ($session->get('jacomment_theme', null)) {
				$theme = $session->get('jacomment_theme', $theme);
			}
		}
		$attachFileType = $jacconfig["comments"]->get("attach_file_type", "doc,docx,pdf,txt,zip,rar,jpg,bmp,gif,png");
		$listFiles = $inputs->get("listfile",'');
		
		$helper = new JACommentHelpers();
		include_once $helper->jaLoadBlock("comments/attach.php");
	}
	
	/**
	 * Open attach file layout when edit comment
	 * 
	 * @return void
	 */
	function open_attach_file_edit()
	{
		global $jacconfig;
		$inputs = Factory::getApplication()->input;
		$cid = $inputs->get('cid', array(0), 'array');
		JArrayHelper::toInteger($cid, array(0));
		$id = $cid[0] ? $cid[0] : '';
		$action = "edit";
		$totalAttachFile = $jacconfig["comments"]->get("total_attach_file", 5);
		$theme = $jacconfig["layout"]->get("theme", "default");
		$session = Factory::getSession();
		if ($inputs->getCmd("jacomment_theme", '')) {
			jimport('joomla.filesystem.folder');
			$themeURL = $inputs->getCmd("jacomment_theme");
			if (JFolder::exists('components/com_jacomment/themes/' . $themeURL) || (JFolder::exists('templates/' . $app->getTemplate() . '/html/com_jacomment/themes/' . $themeURL))) {
				$theme = $themeURL;
			}
			$session->set('jacomment_theme', $theme);
		} else {
			if ($session->get('jacomment_theme', null)) {
				$theme = $session->get('jacomment_theme', $theme);
			}
		}
		$attachFileType = $jacconfig["comments"]->get("attach_file_type", "doc,docx,pdf,txt,zip,rar,jpg,bmp,gif,png");
		$listFiles = $inputs->get("listfile",'');
		
		$helper = new JACommentHelpers();
		include_once $helper->jaLoadBlock("comments/attach.php");
	}
	
	/**
	 * Show quote when comment
	 * 
	 * @return void
	 */
	function show_quote()
	{
		global $jacconfig;
		$object = array();
		$k = 0;
		$inputs = Factory::getApplication()->input;
		$id = $inputs->getInt("id", 0);
		$model = $this->getModel('comments');
		$helper = new JACommentHelpers();
		$item = $model->getItem($id);
		//textCounter('newcomment', 'jaCountText');
		$displayUserInfo = $jacconfig['comments']->get('display_user_info', 'fullname');
		$userInfo = Factory::getUser($item->userid);
		$item->strUser = $item->name;
		if($userInfo->name && $userInfo->username){
			if ($displayUserInfo == "fullname") {
				$item->strUser = $userInfo->name;
			} else {
				$item->strUser = $userInfo->username;
			}
		}

		$object[$k] = new stdClass();
		$object[$k]->id = '#newcomment';
		$object[$k]->type = 'appendAfter';
		$object[$k]->status = 'ok';
		if (strpos($item->comment, "[QUOTE") !== false && strpos($item->comment, "[/QUOTE") !== false) {
			$item->comment = preg_replace("/\[QUOTE(.*)\[\/QUOTE\]/iUs", "", $item->comment);
		}
		
		$object[$k]->content = '[QUOTE=' . $item->strUser . ':' . $item->id . ']' . trim($item->comment) . '[/QUOTE]';
		
		echo $helper->parse_JSON_new($object);
		exit();
	}
	
	/**
	 * Get comment id
	 * 
	 * @return void
	 */
	function getCommentAnchor()
	{
		$inputs = Factory::getApplication()->input;
		$id = $inputs->getInt("id", 0);
		setcookie('commentid1', $id);
		$_COOKIE['commentid1'] = $id;
		echo $_COOKIE['commentid1'];
		exit();
	}
}
?>