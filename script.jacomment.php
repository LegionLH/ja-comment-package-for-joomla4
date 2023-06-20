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

/**
 * Install sub packages and show installation result to user
 *
 * @return void
 */
class com_jacommentInstallerScript
{
	public function postflight($type, $parent)
	{
		if (version_compare(JVERSION, '3.0.0', 'ge')) {
			require_once JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jacomment' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'jahelper.php';

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
	}

	public function uninstall($parent)
	{
		if (version_compare(JVERSION, '3.0.0', 'ge')) {
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
		<?php
		}
	}
}
?>