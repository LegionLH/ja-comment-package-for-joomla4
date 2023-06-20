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

if (isset($this->item)) {
	$addressComment = $this->item->address;
	$latComment = $this->item->latitude;
	$lngComment = $this->item->longitude;

	if (trim($addressComment) != '') {
?>
	<div class="dropdown">
	  <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo JText::_('DROPDOWN_LOCATION'); ?><b class="caret"></b></a>
	  <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dLabel">
		<li>
			<div class="form-location">
				@&nbsp;<a href="http://maps.google.com/maps?z=15&q=<?php echo $latComment; ?>,<?php echo $lngComment; ?>" target="_blank"><?php echo $addressComment; ?></a>
			</div>
		</li>
	  </ul>
	</div>
<?php
	}
}
else {
?>
	<div class="dropdown">
	  <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo JText::_('DROPDOWN_LOCATION'); ?><b class="caret"></b></a>
	  <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dLabel">
		<li>
			<div class="form-location">
				<input class="comment-location" id="comment-location" type="text" value="" placeholder="<?php echo JText::_('LOCATION_WHERE_ARE_YOU'); ?>" name="address" autocomplete="off" />
				<button type="button" class="btn btn-small" onclick="JALocation.detectLocation();"><?php echo JText::_('LOCATION_DETECT_LOCATION'); ?></button>
				<input type="hidden" name="latitude" id="latitude" class="locationLatitude" value="" />
				<input type="hidden" name="longitude" id="longitude" class="locationLongitude" value="" />
				<div class="control-group error location-error"><span class="help-inline"><?php echo JText::_('CAN_NOT_FIND_YOUR_LOCATION'); ?></span></div>
			</div>
		</li>
	  </ul>
	</div>
<?php
}
?>