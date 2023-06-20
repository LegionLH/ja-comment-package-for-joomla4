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

if (! defined('JAC_REGISTERED')) {
	JLoader::register('JACView', JPATH_ADMINISTRATOR.'/components/com_jacomment/views/view.php');
}

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;

/**
 * This view is used for JAComment feature of the component
 * 
 * @package		Joomla.Administrator
 * @subpackage	JAComment
 */
class jacommentViewcomment extends JACView
{
	/**
	 * Display the view
	 * 
	 * @param string $tpl The name of the template file
	 * 
	 * @return void
	 */
	function display($tpl = null)
	{
		$inputs = Factory::getApplication()->input;
		// Display menu header
		if (! $inputs->get("ajax",'') && $inputs->get('tmpl','') != 'component' && $inputs->get('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT_ADMINISTRATOR . DS . "views" . DS . "jaview" . DS . "tmpl" . DS . "main_header.php";
			if (file_exists($file)) {
				include_once($file);
			}
		}
		
		$layout = $inputs->getCmd('layout', 'statistic');
		
		switch ($layout) {
			case 'statistic':
				$this->statistic();
				break;
			case 'licenseandsupport':
				$this->displayLicense();
				break;
			case 'verify':
				$this->form();
				break;
			default:
				$this->statistic();
				break;
		}
		
		$this->setLayout($layout);
		
		$this->addToolbar();
		parent::display($tpl);
		
		// Display menu footer
		if (!$inputs->get("ajax",'') && $inputs->get('tmpl','') != 'component' && $inputs->get('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT_ADMINISTRATOR . DS . "views" . DS . "jaview" . DS . "tmpl" . DS . "main_footer.php";
			if (file_exists($file)) {
				include_once($file);
			}
		}
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		// Set the titlebar text
		JToolBarHelper::title(JText::_('JA_COMMENT'), 'generic.png');
		
		JToolBarHelper::help('screen.jacomment', true);
	}
	
	/**
	 * Statistic view
	 * 
	 * @return void
	 */
	function statistic()
	{
		include_once JPATH_SITE . DS . 'components' . DS . 'com_jacomment' . DS . 'models' . DS . 'comments.php';
		$model = new JACommentModelComments();
		 
		$lastvisitDate = (isset($_SESSION['__default'])) ? $_SESSION['__default']['user']->lastvisitDate : date('Y-m-y');
		
		// calculate
		$total_new = $model->getTotal(' AND date > "' . $lastvisitDate . '"');
		$total_today = $model->getTotal(' AND TO_DAYS(NOW()) = TO_DAYS(date)');
		$total_30day = $model->getTotal(' AND TO_DAYS(NOW()) - TO_DAYS(date) <= 30');
		$total = $model->getTotal('');
		
		// assign
		$this->total_new = $total_new;
		$this->total_today = $total_today;
		$this->total_30day = $total_30day;
		$this->total = $total;
	}
	
	/**
	 * Verify view
	 * 
	 * @return void
	 */
	function form()
	{
		$row = $this->getInfo();
		$this->row = $row;
	}
	
	/**
	 * Get payment info
	 * 
	 * @return array Payment info
	 */
	function getInfo()
	{
		global $jacconfig;
		$row["email"] = "";
		$row["payment_id"] = "";
		if (isset($jacconfig["license"])) {
			$row["email"] = $jacconfig["license"]->get("email", "");
			$row["payment_id"] = $jacconfig["license"]->get("payment_id", "");
		}
		return $row;
	}
	
	/**
	 * Display license view
	 * 
	 * @return void
	 */
	function displayLicense()
	{
		$row = $this->getInfo();
		$this->row = $row;
	}
	
	/**
	 * Display menu and component version
	 * 
	 * @return void
	 */
	function menu()
	{
		global $JACVERSION;
		
		$latest_version = '';
		$version_link = JACommentHelpers::get_Version_Link();
		$inputs = Factory::getApplication()->input;
		$layout = $inputs->getCmd('layout', 'statistic');
		$cid = $inputs->get('cid','');
		if (is_array($cid)) {
			ArrayHelper::toInteger($cid);
			$cid = $cid[0];
		}
		
		$latest_version = $this->get('LatestVersion');
		if ($latest_version) {
			$version_link['latest_version']['info'] = 'http://wiki.joomlart.com/wiki/JA_Comment/Overview';
			$version_link['latest_version']['upgrade'] = 'http://www.joomlart.com/forums/downloads.php?do=cat&id=163';
			
			$iLatest_version = str_replace('.', '', $latest_version);
			$iLatest_version = trim($iLatest_version);
			$iLatest_version = intval($iLatest_version);
		} else {
			$version_link['latest_version']['info'] = '';
			$version_link['latest_version']['upgrade'] = '';
		}
		
		if (version_compare(JVERSION, '3.0', '<')) {
			jimport('joomla.utilities.simplexml');
		}
		$xml = new JSimpleXML();
		$file = JPATH_COMPONENT . '/' . 'jacomment.xml';
		$xml->loadFile($file);
		if (!$xml->document) {
			$current_version = $JACVERSION;
		} else {
			$allComments = $xml->document->children();
			foreach ($allComments as $blogpost) {
				if ($blogpost->name() == "version") {
					$current_version = $blogpost->data();
					break;
				}
			}
		}

		$iCurrent_version = str_replace('.', '', $current_version);
		$iCurrent_version = trim($iCurrent_version);
		$iCurrent_version = intval($iCurrent_version);
		?>
		<?php 
		if ($layout == 'licenseandsupport' || $layout == 'verify') :
		?>
		<div id="comment-header-search-left">
		<ul id="submenu">
			<li><a
				href="index.php?option=com_jacomment&view=comment&amp;layout=licenseandsupport"
				class="<?php
				if ($layout == 'licenseandsupport' || $layout == 'verify') :
					echo 'active';
				endif;
				?>">
				<?php
				echo JText::_('DOCUMENTATION_AND_SUPPORT');
				?>
				</a>
			</li>
		</ul>
		</div>
		<?php endif;?>
		<div id="comment-header-search-right">
			<?php
			if (empty($iLatest_version)) :
				echo JText::_('VERSION') . ' <b>' . $current_version . '</b>';
			elseif (! empty($iLatest_version) && $iLatest_version <= $iCurrent_version) :
				echo JText::_('YOUR_VERSION') . ': <b><a href="' . $version_link['current_version']['info'] . '" target="_blank">' . $current_version . '</a></b>&nbsp;&nbsp;' . JText::_('LATEST_VERSION') . ': <b><a href="' . $version_link['latest_version']['info'] . '" target="_blank">' . $current_version . '</a></b>&nbsp;&nbsp;<font color="Blue"> <i>(' . JText::_('SYSTEM_RUNNING_THE_LATEST_VERSION') . ')</i></font>';
			elseif (! empty($iLatest_version) && $iLatest_version > $iCurrent_version) :
				echo JText::_('YOUR_VERSION') . ': <b><a href="' . $version_link['current_version']['info'] . '" target="_blank">' . $current_version . '</a></b>&nbsp;&nbsp;' . JText::_('LATEST_VERSION') . ': <b>';
				echo isset($version_link['latest_version']) ? '<a href="' . $version_link['latest_version']['info'] . '" target="_blank">' . $latest_version . '</a>' : $latest_version;
				echo '</b>&nbsp;&nbsp;<span style="background-color:rgb(255,255,0);color:Red;font-weight:bold;">' . JText::_('NEW_VERSION_AVAILABLE') . '</span> ';
				if (isset($version_link['latest_version'])) :
					echo '<a target="_blank" href="' . $version_link['latest_version']['upgrade'] . '" title="' . JText::_('CLICK_HERE_TO_DOWNLOAD_LATEST_VERSION') . '">' . JText::_('UPGRADE_NOW') . '</a>';	
				endif;
			endif;
			?>						
		</div>
		<?php
	}
}
?>