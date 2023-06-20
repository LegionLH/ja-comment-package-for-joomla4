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

$items = $this->items;
$currentTypeID = $this->currentTypeID;	
$helper = new JACommentHelpers();
?>                            
<?php 	
	require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jacomment'.DS.'views'.DS.'comments'.DS.'tmpl'.DS.'comments.php';	  
?>