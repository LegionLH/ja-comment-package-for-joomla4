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

// Try extending time, as unziping/ftping took already quite some...
@set_time_limit(240);
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_SITE . DS . 'components' . DS . 'com_jacomment' . DS . 'helpers' . DS . 'jahelper.php';

/**
 * Install sub packages and show installation result to user
 * 
 * @return void
 */
function com_install()
{
	JACommentHelpers::Install_Db();
	
  	$messages = array();
	
	// Import required modules
	jimport('joomla.installer.installer');
	jimport('joomla.installer.helper');
	jimport('joomla.filesystem.file');
	jimport('joomla.filesystem.folder');
	
	// Get packages
	$p_dir = JPath::clean(JPATH_SITE.DS.'components'.DS.'com_jacomment'.DS.'packages');
	// Did you give us a valid directory?
	if (!is_dir($p_dir)){
		$messages[] = JText::_('Package directory(Related modules, plugins) is missing');
	}
	else {
		$subpackages = JFolder::files($p_dir);
		$result = true;
		$installer = new JInstaller();
		if ($subpackages) {			
			$app = JFactory::getApplication();
			$templateDir = 'templates/'.$app->getTemplate();
			
			foreach ($subpackages as $zpackage) {
				if (JFile::getExt($p_dir.DS.$zpackage) != "zip") {
					continue;
				}
				$subpackage = JInstallerHelper::unpack($p_dir.DS.$zpackage);
				if ($subpackage) {
					$type = JInstallerHelper::detectType($subpackage['dir']);
					if (! $type) {
						$messages[] = '<img src="'.$templateDir.'/images/admin/publish_x.png" alt="" width="16" height="16" />&nbsp;<span style="color:#FF0000;">'.JText::_($zpackage." Not valid package") . '</span>';
						$result = false;
					}
					if (! $installer->install($subpackage['dir'])) {
						// There was an error installing the package
						$messages[] = '<img src="'.$templateDir.'/images/admin/publish_x.png" alt="" width="16" height="16" />&nbsp;<span style="color:#FF0000;">'.JText::sprintf('Install %s: %s', $type." ".basename($zpackage), JText::_('Error')).'</span>';
					}
					else {
						$messages[] = '<img src="'.$templateDir.'/images/admin/tick.png" alt="" width="16" height="16" />&nbsp;<span style="color:#00FF00;">'.JText::sprintf('Install %s: %s', $type." ".basename($zpackage), JText::_('Success')).'</span>';
					}
					
					if (! is_file($subpackage['packagefile'])) {
						$subpackage['packagefile'] = $p_dir.DS.$subpackage['packagefile'];
					}
					if (is_dir($subpackage['extractdir'])) {
						JFolder::delete($subpackage['extractdir']);
					}
					if (is_file($subpackage['packagefile'])) {
						JFile::delete($subpackage['packagefile']);
					}
				}
			}			
		}
		JFolder::delete($p_dir);
	}
	?>
	<div style="text-align:left;">
		<table width="100%" border="0" style="line-height:200%; font-weight:bold;">	  
			<tr>
			  <td>
					<img src="components/com_jacomment/asset/images/jacomment.png" />
					JA Comment is installed successfully!<br/>
					<?php echo implode("<br/>", $messages)?><br/><br/>
					<a href="http://www.joomlart.com/documentation/wiki-ja-comment/install-and-upgrade" title="Read more">Read more</a>
			  </td>
			</tr>
		</table>
	</div>
	<?php
}
?>