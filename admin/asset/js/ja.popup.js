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

/**
 * Create pop-up form
 * Must re-initialize window position
 * 
 * @return void
 */
function jaCreatForm(jatask, cid, jaWidth, jaHeight, vmenu, number, title, dsave, titlesave, location)
{
    if (! vmenu) {
        vmenu = 0;
    }
    if (! cid) {
        cid = 0;
    }
    if (! jaWidth){
        jaWidth = 700;
    }
    if (! jaHeight){
        jaHeight = 500;
    }
    if (! number) {
        number = 0;
    }
    if (! location) {
        location = '';
    }
    if (! titlesave) {
    	titlesave = 'Save';
    }
    
    var Obj = document.getElementById('ja-popup-wrap');
    if (! Obj) {
        var content = jQuery('<div>').attr({
            'id' : 'ja-popup'
        }).appendTo(document.body);
        var jacForm = jQuery('<div>').attr({
            'id' : 'ja-popup-wrap',
            'style' : 'top:0px; display:none;'
        }).appendTo(content);
        //jacForm.appendTo(content);
        
        /* JA POPUP HEADER */
        jQuery('<div>').attr({
            'id' : 'ja-popup-header-wrap'
        }).appendTo(jacForm);
        jQuery('<div>').attr({
            'id' : 'ja-popup-tl'
        }).appendTo(jQuery('#ja-popup-header-wrap'));
        jQuery('<div>').attr({
            'id' : 'ja-popup-tr'
        }).appendTo(jQuery('#ja-popup-header-wrap'));
        jQuery('<div>').attr({
            'id' : 'ja-popup-header'
        }).appendTo(jQuery('#ja-popup-header-wrap'));
        jQuery('<div>').attr({
            'class' : 'inner'
        }).appendTo(jQuery('#ja-popup-header'));
        
        if (title) {
            jQuery('<h3>').attr({
                'class' : 'ja-popup-title'
            }).appendTo(jQuery('#ja-popup-header .inner'));
            
            jQuery('.ja-popup-title').html(title);
        }
        jQuery('<button>').attr({
            'id' : 'ja-close-button'
        }).html('Close').appendTo(jQuery('#ja-popup-header .inner'));
        jQuery("#ja-close-button").click(function() {
        	jaFormHide();
        });
        /* end JA POPUP HEADER */
        
        /* JA POPUP CONTENT */
        jQuery('<div>').attr({
            'id' : 'ja-popup-content-wrap'
        }).appendTo(jacForm);
        jQuery('<div>').attr({
            'id' : 'ja-popup-content'
        }).appendTo(jQuery('#ja-popup-content-wrap'));
        jQuery('<div>').attr({
            'class' : 'inner'
        }).appendTo(jQuery('#ja-popup-content'));
        jQuery('<div>').attr({
            'id' : 'ja-popup-wait'
        }).appendTo(jQuery('#ja-popup-content .inner'));
        /* end JA POPUP CONTENT */
        
        /* JA POPUP FOOTER */
        jQuery('<div>').attr({
            'id' : 'ja-popup-footer-wrap'
        }).appendTo(jacForm);
        jQuery('<div>').attr({
            'id' : 'ja-popup-bl'
        }).appendTo(jQuery('#ja-popup-footer-wrap'));
        jQuery('<div>').attr({
            'id' : 'ja-popup-br'
        }).appendTo(jQuery('#ja-popup-footer-wrap'));
        jQuery('<div>').attr({
            'id' : 'ja-popup-footer'
        }).appendTo(jQuery('#ja-popup-footer-wrap'));
        jQuery('<div>').attr({
            'class' : 'inner'
        }).appendTo(jQuery('#ja-popup-footer'));
        
        if (!dsave && jatask !="open_content" && jatask !="open_k2") {
            jQuery('<button>').attr({
                'id' : 'ja-save-button'
            }).html(titlesave).appendTo(jQuery('#ja-popup-footer .inner'));
            jQuery("#ja-save-button").click(function() {
            	JAsubmitbutton();
            });
        }
        
        jQuery('<span>').appendTo(jQuery('#ja-popup-footer .inner'));
        jQuery('#ja-popup-footer .inner span').html('&copy; Copyright by JA Comment');
        /* end JA POPUP FOOTER */
    }

    // Set jacFormWidth + 40
    if (title) {
        jQuery('#ja-popup-title').width(jaWidth - 20);
    }

    //jQuery('#ja-popup-content').width(jaWidth);

    var myWidth = 0, myHeight = 0;

    myWidth = jQuery(window).width();
    myHeight = jQuery(window).height();
    
    var yPos;
    
    if (jQuery.browser.opera && jQuery.browser.version > "9.5"
            && jQuery.fn.jquery <= "1.2.6") {
        yPos = document.documentElement['clientHeight'] - 20;
    } else {
        yPos = jQuery(window).height() - 20;
    }

    var leftPos = (myWidth - jaWidth) / 2;

    jQuery('#ja-popup-wrap').css('zIndex', cGetZIndexMax() + 1);

    /*
     * jQuery.ajax({ url: jatask, cache: false, success: function(html){
     * jQuery("#ja-popup-content").append(html); } });
     */
	if (jatask == "open_content") {		
		var url = "index.php?option=com_content&view=articles&layout=modal&tmpl=component&function=jSelectArticle_jform_request_id";
	} else if(jatask == "open_k2") {
		var url = "index.php?option=com_k2&view=items&task=element&tmpl=component&object=id";
	} else{
		var url = siteurl + "&task=" + jatask + "&cid[]=" + cid + "&viewmenu=0";
	}
    
    if (jQuery('#iContent').length > 0) {
        jQuery('#iContent').attr('src', url);
        jQuery('#ja-popup-title').html(title);
    } else {
        jQuery('<iframe>').attr({
            'id' : 'iContent',
            'src' : url,
            'width' : jaWidth,
            'height' : jaHeight - 80
        }).appendTo(jQuery('#ja-popup-content .inner'));
        jQuery("#iContent").load(function() {
        	loadIFrameComplete();
        });
    }

    /*
     * Set editor position, center it in screen regardless of the scroll
     * position
     */
    
    jQuery("#ja-popup-wrap").css('marginTop', '5px');
    jQuery('#ja-popup-wrap').css('left', leftPos);
    
    if(jQuery.browser.msie) {
        if(jQuery.browser.version=='6.0') {
            jQuery(window).scroll(function() {
                jQuery('#ja-popup-wrap').css({'top' : jQuery(this).scrollTop() + "px", 'left' : leftPos});
            });
            
            jQuery("#ja-popup-wrap").css('top', jQuery(this).scrollTop() + 'px');
            jQuery('#ja-popup-wrap').css('left', leftPos);
        }
    }
    
    /*
     * Set height and width for transparent window
     */
    
    jQuery('#iContent').css('border', '0px');
    jQuery('#ja-popup-header-wrap').css('width', (jaWidth));
    jQuery('#ja-popup-content-wrap').css('width', (jaWidth));
    jQuery('#ja-popup-footer-wrap').css('width', (jaWidth));
    
    jQuery('#ja-popup-wrap').fadeIn();
}

/**
 * Hide form
 * 
 * @return void
 */
function jaFormHide()
{    
	/*if (jQuery('#ja-popup-footer-wrap').get().length > 0)
        jQuery('#ja-popup-footer-wrap').animate( {
            bottom :"0px",
            height :"0px"
        }, 200);*/
    jQuery('#ja-popup').fadeOut('fast', function() {
        jQuery(this).remove();
    });
}

/**
 * Hide iframe form
 * 
 * @return void
 */
function jaFormHideIFrame()
{
    var jaForm = jQuery("#ja-popup", window.parent.document);
    /*if (jQuery('#jacomment_ar').get().length > 0)
        jQuery('#jacomment_ar').animate( {
            top :"-20px"
        }, 200, '');*/
    
    jaForm.fadeOut('slow', function() {
        jaForm.remove();
    });
}

/**
 * Load iframe completely
 * 
 * @return void
 */
function loadIFrameComplete()
{
    jQuery('#ja-popup-wait', window.parent.document).css('display', 'none');
    //jQuery('#ja-save-button', window.parent.document).css('display', 'block');
    //jQuery('#ja-cancel-button', window.parent.document).css('display', 'block');
    jaFormActions();
}

/**
 * Fade in effect form action
 * 
 * @return void
 */
function jaFormActions()
{
	/*if (jQuery('#ja-popup-footer-wrap').get().length > 0)
        jQuery('#ja-popup-footer-wrap').animate( {
            bottom :"0px",
            height :"30px"
        }, 200);*/
	
    jQuery('#ja-popup-wrap').fadeIn('fast');
}

/**
 * Resize form
 * 
 * @param integer newheight New height value
 * 
 * @return void
 */
function jaFormResize(newheight)
{
    jQuery("#ja-popup-content").animate({
        "left" :"+=50px"
    }, "slow");

    jQuery("#ja-popup-content").animate({
        "left" :"+=50px"
    }, "slow");
    
    jQuery("#iContent").animate({
        "left" :"+=50px"
    }, "slow");
    /*
     * jQuery('#iContent', window.parent.document).animate( { height:
     * jQuery(this).height()+30 });
     */
}

/**
 * Get maximum z-index value
 * 
 * @return integer Maximum z-index value
 */
function cGetZIndexMax()
{
    var allElems = document.getElementsByTagName ? document.getElementsByTagName("*") : document.all; // or test for that too
    var maxZIndex = 0;

    for ( var i = 0; i < allElems.length; i++) {
        var elem = allElems[i];
        var cStyle = null;
        if (elem.currentStyle) {
            cStyle = elem.currentStyle;
        } else if (document.defaultView && document.defaultView.getComputedStyle) {
            cStyle = document.defaultView.getComputedStyle(elem, "");
        }

        var sNum;
        if (cStyle) {
            sNum = Number(cStyle.zIndex);
        } else {
            sNum = Number(elem.style.zIndex);
        }
        if (!isNaN(sNum)) {
            maxZIndex = Math.max(maxZIndex, sNum);
        }
    }
    return maxZIndex;
}

/**
 * JAsubmitbutton function
 * 
 * @return void
 */
function JAsubmitbutton()
{
    jQuery(document).ready(function() {
            jQuery('#ja-popup-wait').css({
                'display' : ''
            });

            jQuery.post("index.php", jQuery("#iContent").contents().find("#adminForm").serialize(), function(res) {
                parseData_admin(res);
            }, 'json');
        }
    );
}