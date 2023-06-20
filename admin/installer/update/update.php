<?php
if(JFile::exists(JPATH_COMPONENT."/installer/update/2.5.x/jacomment.xml") && JFile::exists(JPATH_COMPONENT."/com_jacomment.xml")){ 
	if(JFile::exists(JPATH_COMPONENT."/jacomment.xml")){
		JFile::delete(JPATH_COMPONENT."/com_jacomment.xml");
	}else{
		$oldxmlfile = JPath::clean(JPATH_COMPONENT.'/com_jacomment.xml');
		$newxmlfile =  JPATH_COMPONENT.'/installer/update/2.5.x/jacomment.xml';
		
		$newxmlfilecontent = JFile::read($newxmlfile);
		JFile::write($oldxmlfile, $newxmlfilecontent);
		rename($oldxmlfile, str_replace('com_jacomment.xml', 'jacomment.xml', $oldxmlfile));
	}
}
//Delete file update
JFile::delete(JPATH_COMPONENT.'/installer/update/update.php');
?>