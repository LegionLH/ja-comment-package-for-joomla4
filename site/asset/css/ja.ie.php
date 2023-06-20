<?php
header("Content-type: text/css");
?>
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
<?php

$component_path = dirname(dirname($_SERVER['REQUEST_URI']));
global $color;
function ieversion()
{
	preg_match('/MSIE ([0-9]\.[0-9])/', $_SERVER['HTTP_USER_AGENT'], $reg);
	if (! isset($reg[1])) {
		return - 1;
	} else {
		return floatval($reg[1]);
	}
}
$iev = ieversion();

?>
<?php /*All IE*/
?>

<?php
/*IE 6*/
if ($iev == 6) {
	?>

#ja-popup { position: absolute; z-index: 99999; } 

 
#ja-box-action {
    -moz-background-clip: border;
    -moz-background-inline-policy: continuous;
    -moz-background-origin: padding;
    background: transparent url(../images/settings/layout/box-action-bg.png)
        no-repeat scroll left top;
    height: 39px;
    padding-right: 6px;
    padding-top: 6px;
    text-align: center;
    width: 229px;
    float: right;
    position: absolute;
    bottom: -39px;
    right: 0;
}

<?php
}
?>


<?php
/*IE 7*/
if ($iev == 7) {
?>
#jac-wrapper .comment-heading span.small {
	padding-left: 3px !important;
	padding-right: 0 !important;
}
#jac-wrapper .comment-admin {
	width: 90px;
}
#jac-wrapper #jac-post-new-comment ul.form-comment li.form-upload {
	margin-bottom: -3px !important;
}
<?php
}
?>


<?php
/*IE 8*/
if ($iev == 8) {
?>

<?php
}
?>