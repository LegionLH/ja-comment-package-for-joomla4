<?php
// No direct access
defined('_JEXEC') or die();

// Uninstall JaComment component
jimport('joomla.installer.installer');
jimport('joomla.installer.helper');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
$db = JFactory::getDBO();
$messages = array();

$arrPackages = array("mod_jaclatest_comments","plg_editors-xtd_jacommentoff","plg_editors-xtd_jacommenton","plg_content_jacomment","plg_search_search_jacomment","plg_system_system_jacomment","plg_system_janrain");

$eids = array();

foreach ($arrPackages as $package){
	$type = substr($package, 0, 3);
	switch ($type){
		case "mod":
			$db->setQuery("SELECT extension_id, `name` FROM #__extensions WHERE `type` = 'module' AND `element` = '".$package."'");
			$el = $db->loadObject();
			if ($el) {
				$eids[] = $el->extension_id;
				$installer = new JInstaller;
				$result = $installer->uninstall('module', $el->extension_id);
				if ($result) {
					$messages[] = JText::_('Uninstalling module "'.$el->name.'" was successful.');
				} else {
					$messages[] = JText::_('Uninstalling module "'.$el->name.'" was not successful.');
				}
			}

			break;
		case "plg":
			$info = explode("_", $package);
			if (count($info) >= 3) {
				$info[2] = str_replace($info[0]."_".$info[1]."_", "", $package);
				$db->setQuery("SELECT extension_id, `name` FROM #__extensions WHERE `type` = 'plugin' AND `element` = '".$info[2]."' AND `folder` = '".$info[1]."' ");
				$el = $db->loadObject();
				if ($el) {
					$eids[] = $el->extension_id;
					$installer = new JInstaller;
					$result = $installer->uninstall('plugin', $el->extension_id);
					if ($result) {
						$messages[] = JText::_('Uninstalling plugin "'.$el->name.'" was successful.');
					} else {
						$messages[] = JText::_('Uninstalling plugin "'.$el->name.'" was not successful.');
					}
				}
			}

			break;
	}
}
?>
 <div style="text-align:left;">
  	<table width="100%" border="0" style="line-height:200%; font-weight:bold;">
	    <tr>
	      <td align="center">
	      		Uninstalling JA Comment
				<?php
				if (count($messages) > 1) {
					echo ' and all related modules, plugins were';
				}
				else {
					echo ' was';
				}
				echo ' successful.<br />';
				
				echo implode("<br />", $messages);
				?>
				<br />
	      </td>
	    </tr>
    </table>
 </div>