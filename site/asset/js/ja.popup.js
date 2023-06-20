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

// Must re-initialize window position
/**
 * Create form in pop-up
 * 
 * @param string  jatask    Task
 * @param array   cid 	    Ids list
 * @param integer jaWidth   Form width
 * @param integer jaHeight  Form height
 * @param boolean vmenu     Is vertical menu or not
 * @param integer number    Number
 * @param string  title 	Title of popup
 * @param boolean dsave 	Have save button or not
 * @param string  titlesave Title after saving
 * @param string  location  Location
 * 
 * @return void
 */
function jacCreatForm(jatask, cid, jaWidth, jaHeight, vmenu, number, title, dsave, titlesave, location) {
	if (!vmenu) {
		vmenu = 0;
	}
	if (!cid) {
		cid = 0;
	}
	if (!jaWidth) {
		jaWidth = 700;
	}
	if (!jaHeight) {
		jaHeight = 500;
	}
	if (!number) {
		number = 0;
	}
	if (!location) {
		location = '';
	}
	if (!titlesave) {
		titlesave='Save';
	}
	var Obj = document.getElementById('ja-popup-wrap');
	if (!Obj) {
		var content = jQuery('<div>').attr( {
			'id' :'ja-popup'
		}).appendTo(document.body);
		var jacForm = jQuery('<div>').attr( {
			'id' :'ja-popup-wrap',
            'style' :'top: 0px;display:none;'
		}).appendTo(content);
		//jacForm.appendTo(content);
		
		/* JA POPUP HEADER */
		jQuery('<div>').attr( {
			'id' :'ja-popup-header-wrap'
		}).appendTo(jacForm);
		jQuery('<div>').attr( {
			'id' :'ja-popup-tl'
		}).appendTo(jQuery('#ja-popup-header-wrap'));
		jQuery('<div>').attr( {
			'id' :'ja-popup-tr'
		}).appendTo(jQuery('#ja-popup-header-wrap'));
		jQuery('<div>').attr( {
			'id' :'ja-popup-header'
		}).appendTo(jQuery('#ja-popup-header-wrap'));
		jQuery('<div>').attr( {
			'class' :'inner'
		}).appendTo(jQuery('#ja-popup-header'));

		if (title) {
			jQuery('<h3>').attr( {
				'class' :'ja-popup-title'
			}).appendTo(jQuery('#ja-popup-header .inner'));

			jQuery('.ja-popup-title').html(title);
		}
		jQuery('<button>').attr( {
			'id' :'ja-close-button'
		}).html('Close').appendTo(jQuery('#ja-popup-header .inner'));
		jQuery("#ja-close-button").click( function() { jacFormHide(); } );
		/* end JA POPUP HEADER */

		/* JA POPUP CONTENT */
		jQuery('<div>').attr( {
			'id' :'ja-popup-content-wrap'
		}).appendTo(jacForm);        
		jQuery('<div>').attr( {
			'id' :'ja-popup-content'
		}).appendTo(jQuery('#ja-popup-content-wrap'));
		jQuery('<div>').attr( {
			'class' :'inner'
		}).appendTo(jQuery('#ja-popup-content'));
        jQuery('<div>').attr( {
            'id' :'ja-popup-wait'
        }).appendTo(jQuery('#ja-popup-content .inner'));
		/* end JA POPUP CONTENT */
		
		/* JA POPUP FOOTER */
		jQuery('<div>').attr( {
			'id' :'ja-popup-footer-wrap'
		}).appendTo(jacForm);
		jQuery('<div>').attr( {
			'id' :'ja-popup-bl'
		}).appendTo(jQuery('#ja-popup-footer-wrap'));		
		jQuery('<div>').attr( {
			'id' :'ja-popup-br'
		}).appendTo(jQuery('#ja-popup-footer-wrap'));
		jQuery('<div>').attr( {
			'id' :'ja-popup-footer'
		}).appendTo(jQuery('#ja-popup-footer-wrap'));
		jQuery('<div>').attr( {
			'class' :'inner'
		}).appendTo(jQuery('#ja-popup-footer'));
        
		if (!dsave) {
			jQuery('<button>').attr( {
				'id' :'ja-save-button'
			}).html(titlesave).appendTo(jQuery('#ja-popup-footer .inner'));	
			if(jatask != "open_attach_file"){				
				jQuery("#ja-save-button").click( function() { JAsubmitbutton(); } );
			}else{				
				jQuery("#ja-save-button").click( function() { JAsubmitattach(); } );
			}
            //jQuery('<button>').attr( {
			//	'id' :'ja-cancel-button'
			//}).html('Cancel').appendTo(jQuery('#ja-popup-footer .inner'));    
			//jQuery("#ja-cancel-button").click( function() { jacFormHide(); } );
		}   
		
		jQuery('<span>').appendTo(jQuery('#ja-popup-footer .inner'));
		jQuery('#ja-popup-footer .inner span').html('&copy; Copyright by JA Comment');
		/* end JA POPUP FOOTER */
	}

	// Set jacFormWidth + 40
	if (title) {
		jQuery('#ja-popup-title').width(jaWidth-20);
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
	// if (document.getElementsByTagName('body')[0].className.toString().indexOf('rtl') > 0) {
	// use for direction rtl
		// leftPos = -leftPos;
	// }

	jQuery('#ja-popup-wrap').css('zIndex', cGetZIndexMax() + 1);

	/*
	 * jQuery.ajax({ url: jatask, cache: false, success: function(html){
	 * jQuery("#ja-popup-content").append(html); } });
	 */
	
	if(jatask.indexOf("view=users") != -1){
		JACommentConfig.siteurl  = JACommentConfig.siteurl.replace("view=comments", "");
	}
    var url = JACommentConfig.siteurl + "&task=" + jatask + "&cid[]=" + cid;    
    if(jatask == "open_attach_file"){						
    	url +="&" + jQuery("#form1").serialize();    	    	
	}
    if(jatask == "open_attach_file_edit"){
    	url +="&" + jQuery("#form1edit").serialize();
    }	
	if (jQuery('#iContent').length >0){
		jQuery('#iContent').attr('src',"aaaaaa");
		jQuery('#ja-popup-title').html(title);
	}
	else{
		jQuery('<iframe>').attr( {
			'id' :'iContent',
			'src' :url,
			'width' :jaWidth,
			'height' :jaHeight-80,
			'frameborder' :0
		}).appendTo(jQuery('#ja-popup-content .inner'));
		jQuery("#iContent").one('load', function() { loadIFrameComplete(); } );
	}

	/*
	 * Set editor position, center it in screen regardless of the scroll
	 * position
	 */
    jQuery('#ja-popup-wrap').css({'marginTop': '5px', 'left': leftPos});
    
    if(jQuery.browser.msie){
    	if(jQuery.browser.version=='6.0'){

            jQuery(window).scroll(function() {
                jQuery('#ja-popup-wrap').css({'top': jQuery(this).scrollTop() + "px", 'left': leftPos});
            });
            
            jQuery("#ja-popup-wrap").css('top', jQuery(this).scrollTop() + 'px');
            jQuery('#ja-popup-wrap').css('left', leftPos);
        }
	}
	
	/*
	 * Set height and width for transparent window
	 */
	jQuery('#ja-popup-header-wrap').css('width', (jaWidth));
	jQuery('#ja-popup-content-wrap').css('width', (jaWidth));
	jQuery('#ja-popup-footer-wrap').css('width', (jaWidth));

	jQuery('#ja-popup-wrap').fadeIn();
}

/**
 * Hide form pop-up
 * 
 * @return void
 */
function jacFormHide() {
/*	if (jQuery('#ja-popup-footer-wrap').get().length > 0)
		jQuery('#ja-popup-footer-wrap').animate( {
			bottom :"0px",
			height :"0px"
		}, 200);*/
	jQuery('#ja-popup').fadeOut('fast', function() {
		jQuery(this).remove();
		jQuery('textarea[name=newcomment]').focus();
	});
}

/**
 * Hide form iframe
 * 
 * @return void
 */
function jacFormHideIFrame() {
	var jacForm = jQuery("#ja-popup", window.parent.document);
/*	if (jQuery('#jacomment_ar').get().length > 0)
		jQuery('#jacomment_ar').animate( {
			top :"-20px"
		}, 200, '');*/

	jacForm.fadeOut('slow', function() {
		jacForm.remove();
	});
}

/**
 * Action when loading iframe completely
 * 
 * @return void
 */
function loadIFrameComplete(){
	jQuery('#ja-popup-wait',window.parent.document).css('display','none');
	//jQuery('#ja-save-button',window.parent.document).css('display','block');
	//jQuery('#ja-cancel-button',window.parent.document).css('display','block');
	jacFormActions();
}

/**
 * Effect on form
 * 
 * @return void
 */
function jacFormActions() {
/*	if (jQuery('#ja-popup-footer-wrap').get().length > 0)
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
function jacFormResize(newheight) {
	jQuery("#ja-popup-content").animate( {
		"left" :"+=50px"
	}, "slow");

	jQuery("#ja-popup-content").animate( {
		"left" :"+=50px"
	}, "slow");
	jQuery("#iContent").animate( {
		"left" :"+=50px"
	}, "slow");
	/*
	 * jQuery('#iContent', window.parent.document).animate( { height:
	 * jQuery(this).height()+30 });
	 */
}

/**
 * Get maximum z-index
 * 
 * @return integer Maximum z-index
 */
function cGetZIndexMax() {
	var allElems = document.getElementsByTagName ? document
			.getElementsByTagName("*") : document.all; // or test for that too
	var maxZIndex = 0;

	for ( var i = 0; i < allElems.length; i++) {
		var elem = allElems[i];
		var cStyle = null;
		if (elem.currentStyle) {
			cStyle = elem.currentStyle;
		} else if (document.defaultView
				&& document.defaultView.getComputedStyle) {
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
 * Action when click submit button
 * 
 * @return void
 */
function JAsubmitbutton() {
    jQuery(document).ready(
        function() {
            jQuery('#ja-popup-wait').css( {
                'display' :''
            });

            jQuery.post("index.php", jQuery("#iContent").contents().find(
                    "#JAFrom").serialize(), function(res) {
                                            parseData_admin(res);
                                        }, 'json');
        }
    );
}

/**
 * Action when submit attachment
 * 
 * @return void
 */
function JAsubmitattach() {
	jQuery(document).ready(
	        function() {
	            jQuery('#ja-popup-wait').css( {
	                'display' :''
	            });	             	           
	            jQuery.post("index.php", jQuery("#iContent").contents().find(
	                    "#form1").serialize(), function(res) {
	                                            parseData_admin(res);
	                                        }, 'json');
	        }
	);
}