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

use Joomla\CMS\Factory;

$inputs = Factory::getApplication()->input;

$option	= $inputs->getCmd('option');
?>
<ul class="javtabs-title">
    <li class="jav-mainbox_99 first <?php echo $this->filtercurrentTypeID=='99' ? 'loaded active':'';?>" id="jav-typeid_99">    
        <a class="jav-mainbox-99" id="tab-comment_99" title="<?php echo JText::_("SHOW_ALL"); ?>" href="index.php?option=<?php echo $option;?>&amp;view=comments&amp;curenttypeid=99&amp;layout=comments&amp;tmpl=component&amp;keyword=<?php echo $keyword;?>&amp;reported=<?php echo $reported;?>&amp;optionsearch=<?php echo $this->searchComponent; ?>&amp;sourcesearch=<?php echo $this->searchSource; ?>">
            <?php echo JText::_( 'All' ); ?>&nbsp;(<span id='number-of-tab-99'><?php echo $totalAll; ?></span>)
        </a>
    </li>
    <li class="jav-mainbox_0 <?php echo $this->filtercurrentTypeID=='0' ? 'loaded active':'';?>" id="jav-typeid_0">				
		<a class="jav-mainbox-0" id="tab-comment-0" title="<?php echo JText::_("SHOW_UNAPPROVE"); ?>" href="index.php?option=<?php echo $option;?>&amp;view=comments&amp;curenttypeid=0&amp;layout=comments&amp;tmpl=component&amp;keyword=<?php echo $keyword;?>&amp;reported=<?php echo $reported;?>&amp;optionsearch=<?php echo $this->searchComponent; ?>&amp;sourcesearch=<?php echo $this->searchSource; ?>">
			<?php echo JText::_( 'Unapproved' );?>&nbsp;(<span id='number-of-tab-0'><?php echo $totalUnApproved; ?></span>)
		</a>
	</li>						
	<li class="jav-mainbox_1 <?php echo $this->filtercurrentTypeID=='1' ? 'loaded active':'';?>" id="jav-typeid_1">
		<a class="jav-mainbox-1" id="tab-comment-1" title="<?php echo JText::_("SHOW_APPROVED"); ?>" href="index.php?option=<?php echo $option;?>&amp;view=comments&amp;curenttypeid=1&amp;layout=comments&amp;tmpl=component&amp;keyword=<?php echo $keyword;?>&amp;reported=<?php echo $reported;?>&amp;optionsearch=<?php echo $this->searchComponent; ?>&amp;sourcesearch=<?php echo $this->searchSource; ?>">	
			<?php echo JText::_( 'Approved' ); ?>&nbsp;(<span id='number-of-tab-1'><?php echo $totalApproved;?></span>)
		</a>
	</li>
	<li class="jav-mainbox_2 last <?php echo $this->filtercurrentTypeID=='2' ? 'loaded active':'';?>" id="jav-typeid_2">				
		<a  class="jav-mainbox-2" id="tab-comment-2" title="<?php echo JText::_("SHOW_SPAM"); ?>" href="index.php?option=<?php echo $option;?>&amp;view=comments&amp;curenttypeid=2&amp;layout=comments&amp;tmpl=component&amp;keyword=<?php echo $keyword;?>&amp;reported=<?php echo $reported;?>&amp;optionsearch=<?php echo $this->searchComponent; ?>&amp;sourcesearch=<?php echo $this->searchSource; ?>">
			<?php echo JText::_( 'Spam' ); ?>&nbsp;(<span id='number-of-tab-2'><?php echo $totalSpam; ?></span>)
		</a>
	</li>
</ul>