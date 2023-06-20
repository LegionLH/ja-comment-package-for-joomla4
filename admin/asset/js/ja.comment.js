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

var jac_textarea_cursor = 0;
var jav_header = 'ja-header';
var jav_idActive = '';
var jac_header = 'jac-header';
var jav_activePopIn = 0;
/**
 * jav_init function
 * 
 * @return void
 */
function jav_init()
{
	$jac(document).ready(function($) {
			$(this).click(function() {
				if (jav_idActive != '' && jav_activePopIn == 1) {
					$(jav_idActive).removeClass('jav-active');
					jav_activePopIn = 0;
				}
				jav_activePopIn = 1;
			});
			// $('#jav-dialog').hide('slow');
	});
}

/**
 * jacChangeDisplay function
 * 
 * @param string 	id 			Element id
 * @param string 	action 		Display action
 * @param boolean 	isSmiley 	Has smiley or not
 * 
 * @return void
 */
function jacChangeDisplay(id, action, isSmiley)
{
	if ($jac('#'+id).length) {
        $jac('#'+id).css('display', action);
	}
	// if click on smiley - save cursor in texarea
	jac_textarea_cursor = $jac("#newcomment")[0].selectionStart;
}

/**
 * Insert smiley
 * 
 * @param which
 * 
 * @return void
 */
function jacInsertSmiley(which)
{
	var text = $jac("#newcomment").val();
    $jac("#newcomment").val(text.substring(0, jac_textarea_cursor) + which + text.substring(jac_textarea_cursor, text.length));
	jac_textarea_cursor = jac_textarea_cursor + which.length;
}

/**
 * jav_showDiv function
 * 
 * @param string divId Div id
 * 
 * @return void
 */
function jav_showDiv(divId)
{
    (function($) {
        var objDiv = $(divId);
        var clsDiv = objDiv.attr('class');
        jav_idActive = divId;

        if (clsDiv != "undefined") {
            var mainClass = clsDiv.split(' ');
            $('.' + mainClass[0]).removeClass('jav-active');
        }

        if (objDiv) {
            if (clsDiv != "undefined" && clsDiv.indexOf('jav-active') != -1) {
                objDiv.removeClass('jav-active');
            } else {
                objDiv.addClass('jav-active');
            }
        }

        jav_activePopIn = 0;
    })($jac);
}

/**
 * jac_show_all_status function
 * 
 * @param integer itemid Item id
 * 
 * @return void
 */
function jac_show_all_status(itemid)
{
    jav_showDiv('#jac-change-type-' + itemid + ' .statuses');
    $jac('#jac-change-type-' + itemid + ' .statuses').css('top', '-65px');
}

/**
 * Show preview bar at bottom
 * 
 * @param text_preview
 * @param text_cancel
 * 
 * @return void
 */
function show_bar_preview(text_preview, text_cancel)
{
    return (function($){
        var bottomOffset = $('#status').length ? '31px' : '0px';//fix hide under bottom status bar in Joomla 3.x
        if ($.browser.msie) {
            if ($.browser.version == '6.0') {
                $('#ja-box-action').show();
                $(window).scroll(function() {
                    $('#ja-box-action').css({'top' : $(window).height()-45 + $(this).scrollTop() + "px", 'right' : '0'});
                });
            }
        }

        if ($('#ja-box-action').length) {
            $('#ja-box-action').animate({
                bottom : bottomOffset
            }, 300);
            return;
        }
        if (text_preview == null) text_preview = 'Preview';
        if (text_cancel == null) text_cancel = 'Cancel';

        var box_action = $('<div>', {'id' : 'ja-box-action'});
        var button_preview = $('<button>', {
            'name' : 'ja-preview',
            'id' : 'ja-preview',
            'class' : 'button_b'
        })
            .html(text_preview)
            .click(function(e) {
            // layout & plugin

            var theme = 'default';
            var config_text = 0;
            var enable_avatar = 0;
            var use_default_avatar = 0;
            var avatar_size = 1;
            var button_type = 1;
            var enable_comment_form = 0;
            var form_position = 1;

            var enable_login_button = 0;
            var enable_subscribe_menu = 0;
            var enable_sorting_options = 0;
            var default_sort = 1;

            var enable_timestamp = 0;
            var enable_user_rep_indicator = 1;
            var footer_text = '';

            var enable_addthis = 0;
            var enable_addtoany = 0;
            var enable_tweetmeme = 0;

            var enable_youtube = 0;
            var enable_bbcode  = 0;
            var enable_activity_stream = 0;
            var enable_after_the_deadline = 0;
            var enable_smileys = 0;

            if ($("#default").is(':checked')) {
                theme = 'default';
            } else if ($("#classicA").is(':checked')) {
                theme = 'classicA';
            } else if ($("#classicB").is(':checked')) {
                theme = 'classicB';
            }

            if ($("#config_text_1").is(':checked')) {
                config_text = 1;
            }

            if ($("#enable_avatar").is(':checked')) {
                enable_avatar = 1;
            }
            if ($("#use_default_avatar").is(':checked')) {
                use_default_avatar = 1;
            }
            if ($("#avatar_size_1").is(':checked')) {
                avatar_size = 1;
            } else if ($("#avatar_size_2").is(':checked')) {
                avatar_size = 2;
            } else if ($("#avatar_size_3").is(':checked')) {
                avatar_size = 3;
            }

            if ($("#button_type_1").is(':checked')) {
                button_type = 1;
            } else if ($("#button_type_2").is(':checked')) {
                button_type = 2;
            }

            if ($("#enable_comment_form").is(':checked')) {
                enable_comment_form = 1;
            }
            if ($("#form_position_1").is(':checked')) {
                form_position = 1;
            } else if ($("#form_position_2").is(':checked')) {
                form_position = 2;
            }

            if ($("#enable_login_button").is(':checked')) {
                enable_login_button = 1;
            }
            if ($("#enable_subscribe_menu").is(':checked')) {
                enable_subscribe_menu = 1;
            }
            if ($("#enable_sorting_options").is(':checked')) {
                enable_sorting_options = 1;
            }

            if ($("#default_sort_1").is(':checked')) {
                default_sort = 1;
            } else if ($("#default_sort_2").is(':checked')) {
                default_sort = 2;
            }

            if ($("#enable_timestamp").is(':checked')) {
                enable_timestamp = 1;
            }
            if ($("#enable_user_rep_indicator").is(':checked')) {
                enable_user_rep_indicator = 1;
            }

            footer_text = $("#footer_text").val();

            if ($("#enable_addthis").is(':checked')) {
                enable_addthis = 1;
            }
            if ($("#enable_addtoany").is(':checked')) {
                enable_addtoany = 1;
            }

            if ($("#enable_youtube").is(':checked')) {
                enable_youtube = 1;
            }
            if ($("#enable_bbcode").is(':checked')) {
                enable_bbcode = 1;
            }
            if ($("#enable_activity_stream").is(':checked')) {
                enable_activity_stream = 1;
            }
            if ($("#enable_after_the_deadline").is(':checked')) {
                enable_after_the_deadline = 1;
            }
            if ($("#enable_smileys").is(':checked')) {
                enable_smileys = 1;
            }

            var url = "&theme=" + theme + "&config_text=" + config_text + "&enable_avatar=" + enable_avatar + "&use_default_avatar=" + use_default_avatar + "&avatar_size=" + avatar_size + "&button_type=" + button_type + "&enable_comment_form=" + enable_comment_form + "&form_position=" + form_position + "&enable_login_button=" + enable_login_button + "&enable_subscribe_menu=" + enable_subscribe_menu + "&enable_sorting_options=" + enable_sorting_options + "&default_sort=" + default_sort + "&enable_timestamp=" + enable_timestamp + "&enable_user_rep_indicator=" + enable_user_rep_indicator + "&footer_text=" + footer_text + "&enable_addthis=" + enable_addthis + "&enable_addtoany=" + enable_addtoany + "&enable_tweetmeme=" + enable_tweetmeme + "&enable_youtube=" + enable_youtube + "&enable_bbcode=" + enable_bbcode + "&enable_activity_stream=" + enable_activity_stream + "&enable_after_the_deadline=" + enable_after_the_deadline + "&enable_smileys=" + enable_smileys;
            // comment
            var is_enable_threads       = ($("#is_enable_threads").is(':checked')) ? 1 : 0;
            var is_show_child_comment   = ($("#is_show_child_comment").is(':checked')) ? 1 : 0;
            var is_allow_voting         =  ($("#is_allow_voting").is(':checked')) ? 1 : 0;
            var is_attach_image         = ($("#is_attach_image").is(':checked')) ? 1 : 0;
            var is_enable_website_field = ($("#is_enable_website_field").is(':checked')) ? 1 : 0;
            var is_enable_autoexpanding = ($("#is_enable_autoexpanding").is(':checked')) ? 1 : 0;
            var is_enable_email_subscription = ($("#is_enable_email_subscription").is(':checked')) ? 1 : 0;
            var is_allow_report         = ($("#is_allow_report").is(':checked')) ? 1 : 0;

            url = url+"&is_enable_threads="+is_enable_threads+"&is_show_child_comment="+is_show_child_comment+"&is_allow_voting="+is_allow_voting+"&is_attach_image="+is_attach_image+"&is_enable_website_field="+is_enable_website_field+"&is_enable_autoexpanding="+is_enable_autoexpanding+"&is_enable_email_subscription="+is_enable_email_subscription+"&is_allow_report="+is_allow_report;

            // spamfilter
            var is_enable_captcha   = ($("#is_enable_captcha").is(':checked'))   ? 1 : 0;
            var is_enable_terms     = ($("#is_enable_terms").is(':checked')) ? 1 : 0;

            url = url+"&is_enable_captcha="+is_enable_captcha+"&is_enable_terms="+is_enable_terms;

            preview_theme('../index.php?tmpl=component&option=com_jacomment&view=comments&task=preview'+url,740,460,'Preview Layout',1);
        });

        var button_cancel = $('<button>', {
            'name':'ja-cancel',
            'id':'ja-cancel',
            'class':'button_b',
            'href':'#'
        })
        .html(text_cancel)
        .click(function(){
            if($.browser.msie){
                if($.browser.version=='6.0'){
                    $('#ja-box-action').hide();
                }
            }else{
                $('#ja-box-action').animate( {
                    bottom :"-45px"
                }, 300);
            }
        });

        box_action.append(button_preview);
        box_action.append(button_cancel);

        $('#jacom-maincontent').prepend(box_action);

        $('#ja-box-action').animate( {
            bottom :bottomOffset
        }, 300);
        // $jac('#ja-wrap-content').fadeIn('fast');
    })($jac);
}

/**
 * Preview window
 * 
 * @param string  url 	   Link URL
 * @param integer jaWidth  Window width
 * @param integer jaHeight Window height
 * @param string  title	   Window title
 * @param boolean drag	   Can drag or not
 * 
 * @return void
 */
function preview_theme(url, jaWidth, jaHeight, title, drag) {
    if (!$jac('#ja-popup-wrap').length) {
        var content = $jac('<div>').attr( {
            'id' :'ja-popup'
        }).appendTo(document.body);
        var jaForm = $jac('<div>').attr( {
            'id' :'ja-popup-wrap',
            'style' :'top: 0px;display:none;'
        }).appendTo(content);
        // jaForm.appendTo(content);
        
        /* JA POPUP HEADER */
        $jac('<div>').attr( {
            'id' :'ja-popup-header-wrap'
        }).appendTo(jaForm);
        $jac('<div>').attr( {
            'id' :'ja-popup-tl'
        }).appendTo($jac('#ja-popup-header-wrap'));
        $jac('<div>').attr( {
            'id' :'ja-popup-tr'
        }).appendTo($jac('#ja-popup-header-wrap'));
        $jac('<div>').attr( {
            'id' :'ja-popup-header'
        }).appendTo($jac('#ja-popup-header-wrap'));
        $jac('<div>').attr( {
            'class' :'inner'
        }).appendTo($jac('#ja-popup-header'));

        if (title) {
            $jac('<h3>').attr( {
                'class' :'ja-popup-title'
            }).appendTo($jac('#ja-popup-header .inner'));

            $jac('.ja-popup-title').html(title);
        }
        $jac('<a>').attr( {
            'id' :'ja-close-button'
        }).html('Close').appendTo($jac('#ja-popup-header .inner'));
        $jac("#ja-close-button").click( function() { jaFormHide(); } );
        
        /* end JA POPUP HEADER */

        /* JA POPUP CONTENT */
        $jac('<div>').attr( {
            'id' :'ja-popup-content-wrap'
        }).appendTo(jaForm);
        $jac('<div>').attr( {
            'id' :'ja-popup-wait',
            'width' :jaWidth
        }).appendTo($jac('#ja-popup-content-wrap'));
        $jac('<div>').attr( {
            'id' :'ja-popup-content'
        }).appendTo($jac('#ja-popup-content-wrap'));
        $jac('<div>').attr( {
            'class' :'inner'
        }).appendTo($jac('#ja-popup-content'));
        /* end JA POPUP CONTENT */
        
        /* JA POPUP FOOTER */
        $jac('<div>').attr( {
            'id' :'ja-popup-footer-wrap'
        }).appendTo(jaForm);
        $jac('<div>').attr( {
            'id' :'ja-popup-bl'
        }).appendTo($jac('#ja-popup-footer-wrap'));
        $jac('<div>').attr( {
            'id' :'ja-popup-br'
        }).appendTo($jac('#ja-popup-footer-wrap'));
        $jac('<div>').attr( {
            'id' :'ja-popup-footer'
        }).appendTo($jac('#ja-popup-footer-wrap'));
        $jac('<div>').attr( {
            'class' :'inner'
        }).appendTo($jac('#ja-popup-footer'));
       
        $jac('<span>').appendTo($jac('#ja-popup-footer .inner'));
        $jac('#ja-popup-footer .inner span').html('&copy; Copyright by JA Comment');
        /* end JA POPUP FOOTER */
    }

    // Set jaFormWidth + 40
    if (title)
        $jac('#ja-popup-title').width(jaWidth-20);

    var myWidth = 0, myHeight = 0;
    var yPos;

    myWidth = $jac(window).width();
    myHeight = $jac(window).height();

    if ($jac.browser.opera && $jac.browser.version > "9.5"
            && $jac.fn.jquery <= "1.2.6") {
        var yPos = document.documentElement['clientHeight'] - 20;
    } else {
        var yPos = $jac(window).height() - 20;
    }

    var leftPos = (myWidth - jaWidth) / 2;

    $jac('#ja-popup-wrap').css('zIndex', cGetZIndexMax() + 1);

    /*
	 * jQuery.ajax({ url: jatask, cache: false, success: function(html){
	 * jQuery("#ja-popup-content").append(html); } });
	 */
    
    if ($jac('#iContent').length >0){
        $jac('#iContent').attr('src',url);
        $jac('#ja-popup-title').html(title);
    }
    else{
        $jac('<iframe>').attr( {
            'id' :'iContent',
            'src' :url,
            'width' :jaWidth,
            'height' :jaHeight-80
        }).appendTo($jac('#ja-popup-content .inner'));
        $jac("#iContent").load( function() { loadIFrameComplete(); } );
    }
    /*
	 * Set editor position, center it in screen regardless of the scroll
	 * position
	 */
    $jac("#ja-popup-wrap").css('marginTop', '5px');
    $jac('#ja-popup-wrap').css('left', leftPos);

    if($jac.browser.msie){
        if($jac.browser.version=='6.0'){
            $jac(window).scroll(function() {
                $jac('#ja-popup-wrap').css({'top': $jac(this).scrollTop() + "px", 'left': leftPos});
            });
            
            $jac("#ja-popup-wrap").css('top', $jac(this).scrollTop() + 'px');
            $jac('#ja-popup-wrap').css('left', leftPos);
        }
    }
    /*
	 * Dragable
	 */
    if(drag){
        $jac('#ja-popup-header-wrap').css('cursor', 'move');
        $jac('#preview').css('overflow', 'hidden');
        $jac('#ja-popup')
            .bind('drag',function( event ){
                    $jac( this ).css({
                            left: event.offsetX
                            });
                    });
    }
    /*
	 * Set height and width for transparent window
	 */
    $jac('#iContent').css('border', '0px');
    $jac('#ja-popup-header-wrap').css('width', (jaWidth));
    $jac('#ja-popup-content-wrap').css('width', (jaWidth));
    $jac('#ja-popup-footer-wrap').css('width', (jaWidth));

    $jac('#ja-popup-wrap').fadeIn();
}

/**
 * scrollEditor function
 * 
 * @param object e Element
 * 
 * @return void
 */
function scrollEditor(e) {
    var offset = $jac(e).scrollTop();
    offset = offset * -1;
    offset = '0 ' + offset + 'px';
    $jac(e).css('background-position', offset);
}

/**
 * checkError function
 * 
 * @return boolean True if there is no error and otherwise
 */
function checkError() {
    var flag = true;
    var requireds = $jac('#iContent').contents().find('input.required');
    $jac.each(requireds, function(i, item) {
        if ($jac(item).attr('value') == '') {
            var li_parent = $jac(item.parentNode.parentNode);
            li_parent.addClass('error');
        }
    });
    var errors = $jac('#iContent').contents().find('li.error');
    errors.each( function() {
        flag = false;
        return;
    });
    return flag;
}

/**
 * Submit adminForm
 * 
 * @return void
 */
function submitbuttonAdmin() {
    var flag = checkError();
    if (flag) {
        $jac('#ja-popup-wait').css( {
            'display' :''
        });

        $jac.post("index.php", $jac("#iContent").contents().find(
            "#adminForm").serialize(), function(res) {
            jaFormHideIFrame();
            parseData_admin(res);
        }, 'json');
    } else {
        alert("Invalid data! Please insert information again!");
    }
}

/**
 * Parse data posted from adminForm
 * 
 * @param object response Response object
 * 
 * @return void
 */
function parseData_admin(response) {
	$jac(document, window.parent.document).ready( function($) {
    	var reload = 0;
		if(response.data){
			var myResponse = response.data;
		}else{
			var myResponse = response;
		}
        $.each(myResponse, function(i, item) {
            var divId = item.id;
            var type = item.type;
            var value = item.content;

            if ($(divId, window.parent.document).length) {
                if (type == 'html') {
                    if ($(divId, window.parent.document)){
                        $(divId, window.parent.document).html(value);
                        
                        if(item.status!='ok'){
                            $('#ja-popup-wait').css( {
                                'display' :'none'
                            });
                        }else{
                            jaFormHideIFrame();
                        }
                    }else{
                        alert('not found element');
                    }
                }else if (type == 'append') {
                    if ($(divId, window.parent.document)){
                        $(divId, window.parent.document).val($(divId, window.parent.document).val() + value);
                        
                        if(item.status!='ok'){
                            $('#ja-popup-wait').css( {
                                'display' :'none'
                            });
                        }else{
                            jaFormHideIFrame();
                        }
                    }else{
                        alert('not found element');
                    }
                }else if (type == 'append_id') {
                    if ($(divId, window.parent.document)){
                        $(divId, window.parent.document).append(value);
                        
                        if(item.status!='ok'){
                            $('#ja-popup-wait').css( {
                                'display' :'none'
                            });
                        }else{
                            jaFormHideIFrame();
                        }
                    }else{
                        alert('not found element');
                    }
                } else {
                    if (type == 'reload') {
                        if (value == 1)
                            reload = 1;
                    } else {
                        if(type=='val'){
                            $(divId, window.parent.document).val(value);
                        }else{
                            $(divId, window.parent.document).attr(type, value);
                        }
                    }
                }
            }
        });
    });
}

/**
 * Hide message
 * 
 * @return void
 */
function hiddenMessage() {
    $jac('#system-message', window.parent.document).html('');
}

/**
 * Get cookie value from name
 * 
 * @param string name Cookie name
 * 
 * @return string Cookie string
 */
function getCookie(name) {
    var start = document.cookie.indexOf(name + "=");
    var len = start + name.length + 1;
    if ((!start) && (name != document.cookie.substring(0, name.length))) {
        return null;
    }
    if (start == -1)
        return null;
    var end = document.cookie.indexOf(";", len);
    if (end == -1)
        end = document.cookie.length;
    return unescape(document.cookie.substring(len, end));
}

/**
 * Hide note
 *
 * @param string type 	 Note type
 * @param string display Display text
 * @param string hidden  Hidden text
 *
 * @return void
 */
function hiddenNote(type, display, hidden) {
    ( function($) {
        var value = 0;
        if ($('#jac-system-message').css('display') == 'block') {
            $('#jac-system-message').attr('style', 'display:none');
            value = 1;
            $('#jac_help').html(display);
        } else {
            $('#jac-system-message').attr('style', 'display:block');
            value = 0;
            $('#jac_help').html(hidden);
        }
        setCookie('hidden_message_' + type, value, 365);
    }) ($jac);
}

/**
 * Set cookie value
 *
 * @param string   name    Cookie name
 * @param string   value   Cookie value
 * @param datetime expires Expire date
 * @param string   path    Cookie path
 * @param string   domain  Cookie domain
 * @param boolean  secure  Secure or not
 *
 * @return void
 */
function setCookie(name, value, expires, path, domain, secure) {
    var today = new Date();
    today.setTime(today.getTime());
    if (expires) {
        expires = expires * 1000 * 60 * 60 * 24;
    }
    var expires_date = new Date(today.getTime() + (expires));
    document.cookie = name + "=" + escape(value)
            + ((expires) ? ";expires=" + expires_date.toGMTString() : "")
            + ((path) ? ";path=" + path : "")
            + ((domain) ? ";domain=" + domain : "")
            + ((secure) ? ";secure" : "");
}

/**
 * Create JComment tab
 *
 * @param string tabId Tab id
 *
 * @return void
 */
function jcomment_createTabs(tabId){
	( function($) {
        $("ul.javtabs-title li")
        .click( function() {
                var activeTab = '#' + $(this).find("a").attr("class");
                var clicked = $(this).attr('class');
                var obj = $(this);
                var clstype_id = $(this).attr('id');
                if (clicked != "undefined" && clicked.indexOf('loaded') == -1) {
                    jac_displayLoadingSpan();
                    var jav_ajax = $.getJSON($(this).find("a").attr("href"),
                                    function(res) {
                                        jav_parseData(res);
                                        if (clstype_id != "undefined"  && clstype_id!='') {
                                            var clstype = clstype_id.split('_');
                                            var type_id = parseInt(clstype[1]);
                                            var jav_pathway = $('#jav-pathway-' + type_id);
                                            if (jav_pathway) {
                                                $('.jav-pathway-main').hide();
                                                jav_pathway.show();

                                            }
                                            $('#filetercurrentTypeID').val(type_id);
                                        }

                                        // Remove any "active" class
                                        $("ul.javtabs-title li").removeClass("active");
                                        // class to selected tab
                                        $(".javtabs-panel").hide(); // Hide all tab content

                                        $(activeTab).show();
                                        obj.addClass("active"); // Add "active"
                                    });
                                    $(this).addClass('loaded');
                } else {
                    if (clstype_id != "undefined"  && clstype_id!='') {
                        var clstype = clstype_id.split('_');
                        var type_id = parseInt(clstype[1]);
                        var jav_pathway = $('#jav-pathway-' + type_id);
                        if (jav_pathway) {
                            $('.jav-pathway-main').hide();
                            jav_pathway.show();
                        }
                        // set current tab in hidden
                        $('#currentTypeID').val(type_id);
                        $('#filetercurrentTypeID').val(type_id);
                    }

                    $("ul.javtabs-title li").removeClass("active"); // Remove
                                                                    // any
                                                                    // "active"
                                                                    // class
                    // class to selected tab
                    $(".javtabs-panel").hide(); // Hide all tab
                                                // content
                    $(activeTab).show();
                    $(this).addClass("active"); // Add "active"
                }
                return false;
            });
		}) ($jac);
}

/**
 * Parse data
 *
 * @param object response Response data
 *
 * @return void
 */
function jav_parseData(response, doReload) {
	return ( function($) {
        jac_hideLoadingSpan();
        if(response.data){
			var myResponse = response.data;
		}else{
			var myResponse = response;
		}
        var reload = 0;
        $.each(myResponse, function(i, item) {
            var divId = item.id;
            var type = item.attr;
            if(type == undefined){
				type = item.type;
			}
            var content = item.content;
            if ($(divId).length) {
                if (type == 'html') {
                    $(divId).html(content);
                } else if (type == 'class') {
                    $(divId).attr('class', '');
                    $(divId).addClass(content);
                } else if (type == 'css') {
                    var arr = content.split(',');
                    $(divId).css(arr[0], arr[1]);
                } else if(type=='reload'){
                    if(doReload) {
                        window.location.href = content;
                    }
                    reload = 1;
                } else {
                    $(divId).attr(type, content);
                }
            }
        });
        return reload;
    })($jac);
}

/**
 * Save comment to cookie
 * 
 * @param integer id 	 Comment id
 * @param string  action Action name
 *
 * @return void
 */
function saveCommentToCokie(id, action){
	if(!$jac.isFunction($jac.cookie)) return;
	var strJaCokie = $jac.cookie("jac-status-comment");
	// delete session
	if(action){
		if(strJaCokie){
			// delete the first comment in cookie
			if(strJaCokie.indexOf(id) == 0){				
				if(strJaCokie.indexOf(id + "-") != -1){					
					strJaCokie = strJaCokie.replace(id + "-", "" );					
				}else{					
					strJaCokie =  strJaCokie.replace(id, "" );					
				}								
			}else{				
				if(strJaCokie.indexOf(id) != -1){					
					strJaCokie = strJaCokie.replace("-"+id, "" );
				}
			}																
		}
	}else{
		if(strJaCokie){
			if(strJaCokie.lastIndexOf(id) == -1)
				strJaCokie += "-" + id;			
		}else{			
				strJaCokie = id;
		}
	}
	$jac.cookie("jac-status-comment", null);
	$jac.cookie("jac-status-comment", strJaCokie);		
}

/**
 * Disable collapse/expand feature
 *
 * @param integer commentID Comment id
 * @param integer typeID Type id
 *
 * @return void
 */
function disableActionComment(commentID, typeID){
	if($jac("#expandComment"+typeID+"-"+commentID).css('display') == "none"){
		$jac("#actionCollapseComment"+typeID+"-"+commentID).css('display', "none");
	}else{
		$jac("#actionExpandComment"+typeID+"-"+commentID).css('display', "none");
	}
}

/**
 * Get object by name
 *
 * @param string divName Div name
 * @param string tab	 Tag name
 * 
 * @return array Array of elements
 */
function getObjectByName(divName, tab) {
    var allTds = document.getElementsByTagName(tab);    
    var matchingDivs = new Array();    
    
    for (var i = 0; i < allTds.length; i++){    	
    	if(allTds.item(i).getAttribute( 'name' ) == divName){
    		matchingDivs.push( allTds.item(i) );
    	}    	    	  
    }        
    return matchingDivs;
}

/**
 * Expand or collapse feature
 *
 * @param integer currentTypeID Current type id
 *
 * @return void
 */
function performExpandOrCollapse(currentTypeID) {
	var divCollapseComment =  $jac('div[id^=collapseComment'+currentTypeID+']');
	var divExpandComment   =  $jac('div[id^=expandComment'+currentTypeID+']');
	
	var hiddenstatus = $jac('#jav-mainbox-'+currentTypeID +' input[name^=hidStatus'+ currentTypeID +'-]');

	if($jac("#hidAllStatus"+currentTypeID).val() != 1){
        $jac("#expandOrCollapse"+currentTypeID).html("[-] "+ $jac('#hidCollapseAll').val());
		for(i=0; i< divCollapseComment.length; i++){
			$jac(divCollapseComment[i]).css('display', "none");
			$jac(divExpandComment[i]).css('display', "block");
			// set status of comment is 1
			$jac(hiddenstatus[i]).val( 1);
			var indexOfComment = $jac(divCollapseComment[i]).attr('id').lastIndexOf("-") + 1;
			var commentID = $jac(divCollapseComment[i]).attr('id').substring(indexOfComment);
			saveCommentToCokie(commentID);
		}		
		//
		// set status of all comment is collapse
		$jac("#hidAllStatus"+currentTypeID).val( 1);
	}
	// collapse all if choise collapse
	else{
		$jac("#expandOrCollapse"+currentTypeID).html("[+] " + $jac('#hidExpandAll').val());
		for(i=0; i< divCollapseComment.length; i++){
            $jac(divCollapseComment[i]).css('display', "block");
            $jac(divExpandComment[i]).css('display', "none");
			
			// set status of comment is 1
			$jac(hiddenstatus[i]).val( 0);
			var indexOfComment = $jac(divCollapseComment[i]).attr('id').lastIndexOf("-") + 1;
			var commentID = $jac(divCollapseComment[i]).attr('id').substring(indexOfComment);
			saveCommentToCokie(commentID, "delete");
		}
		// set status of all comment is expand
	
		$jac("#hidAllStatus"+currentTypeID).val( 2);
	}
}

/**
 * Do expand or collapse on comment
 *
 * @param integer commentID Comment id
 * @param integer typeID 	Type id
 *
 * @return void
 */
function actionInComment(commentID, typeID){			
	// collapseComment expandComment actionComment
	// alert("actionComment"+commentID);
	// $jac("#actionComment"+commentID).html("action now");
	if($jac("#expandComment"+typeID+"-"+commentID).css('display') == "none"){
		$jac("#collapseComment"+typeID+"-"+commentID).css('display', "none");
		$jac("#actionCollapseComment"+typeID+"-"+commentID).css('display', "none");
		$jac("#expandComment"+typeID+"-"+commentID).css('display', "block");
		$jac("#hidStatus"+typeID+"-"+commentID).val( 1);
		
		var divExpandComment   =  $jac('div[id^=expandComment'+typeID+']');
		var checkExpandAll = 1;
		for(i=0; i< divExpandComment.length; i++){
			if($jac(divExpandComment[i]).css('display') == "none"){
				checkExpandAll = 0;
				break;
			}
		}
		if(checkExpandAll == 1){			
			$jac("#expandOrCollapse"+typeID).html("[-] "+ $jac('#hidCollapseAll').val());
			$jac("#hidAllStatus"+typeID).val( 1);
		}
		
		// save status of comment to session
		saveCommentToCokie(commentID);
	}else{
		$jac("#expandComment"+typeID+"-"+commentID).css('display', "none");
		$jac("#actionExpandComment"+typeID+"-"+commentID).css('display', "none");
		$jac("#collapseComment"+typeID+"-"+commentID).css('display', "block");
		$jac("#hidStatus"+typeID+"-"+commentID).val( 0);
		
		var divCollapseComment =  $jac('div[id^=collapseComment'+typeID+']');
		var checkCollapse = 1;
		for(i=0; i< divCollapseComment.length; i++){
			if($jac(divCollapseComment[i]).css('display') == "none"){
				checkCollapse = 0;				
				break;
			}
		}
		
		if(checkCollapse == 1){
			$jac("#expandOrCollapse"+typeID).html("[+] " + $jac('#hidExpandAll').val());
			$jac("#hidAllStatus"+typeID).val( 2);
		}
		
		saveCommentToCokie(commentID, "delete");
	}
}

/**
 * Swap between expand or collapse text
 *
 * @param integer commentID Comment id
 * @param integer typeID 	Type id
 *
 * @return void
 */
function showActionComment(commentID, typeID){	
	if($jac("#expandComment"+typeID+"-"+commentID).css('display') == "none"){
		$jac("#actionCollapseComment"+typeID+"-"+commentID).css('display', "block");
		$jac("#actionCollapseComment"+typeID+"-"+commentID).html("[+] " + $jac('#hidClickToExpand').val());
	}else{
		$jac("#actionExpandComment"+typeID+"-"+commentID).css('display', "block");
		$jac("#actionExpandComment"+typeID+"-"+commentID).html("[-] " + $jac('#hidClickToCollapse').val());
	}			
}

/**
 * Change type of comment
 *
 * @param string  type 		  	Type of comment
 * @param integer id   		  	Comment id
 * @param integer removeTabID 	Add class "loaded" to comment tab for preventing it is loaded again
 * @param integer currentTypeID Current type id
 *
 * @return void
 */
function changeTypeOfComment(type,id,removeTabID,currentTypeID){
	( function($) {
        jac_displayLoadingSpan();

        var url = "index.php?option=com_jacomment&view=comments&type="+ type +"&layout=changetype&id="+ id +"&curenttypeid="+ currentTypeID +"&tmpl=component";

        // collapse comment
        saveCommentToCokie(id, "delete");

        if($('#limitstart'+currentTypeID).length)
            var limitstart = $('#limitstart'+currentTypeID).val();

        if($('#list'+currentTypeID).length)
            var limit = $('#list'+currentTypeID).val();

        if($('#keywordsearch').length && $('#keywordsearch').val()!= ""){
            url += "&keyword=" + $('#keywordsearch').val();
        }

        if($('#slComponent').length && $('#slComponent').val() != ""){
            url += "&optionsearch=" + escape($('#slComponent').val());
        }
        if($('#slSource').length){
            url += "&sourcesearch=" + escape($('#slSource').val());
        }

        if($('#jacReported').length){
            if($('#jacReported').attr('checked') == true)
                url += "&reported=" + escape($('#jacReported').val());
        }

        url = getUrlSort(url);

		// url += "&"+$("#adminForm" + currentTypeID).serialize();
		// url = getCheckBoxSelected(url);
		// remove class loaded - reload comment of spam
		$.getJSON(url, function(response){
			var reload = jav_parseData(response, false);
			
			if(currentTypeID != 99){
				var clicked = $("#jav-typeid_99").attr('class');			
				if(clicked.indexOf('loaded') != -1){
					$("#jav-typeid_99").removeClass('loaded');
				}
				
				clicked = $("#jav-typeid_" + type).attr('class');			
				if(clicked.indexOf('loaded') != -1){
					$("#jav-typeid_" + type).removeClass('loaded');
				}
			}else{				
				clicked = $("#jav-typeid_" + type).attr('class');			
				if(clicked.indexOf('loaded') != -1){
					$("#jav-typeid_" + type).removeClass('loaded');
				}
				
				clicked = $("#jav-typeid_" + removeTabID).attr('class');			
				if(clicked.indexOf('loaded') != -1){
					$("#jav-typeid_" + removeTabID).removeClass('loaded');
				}
			}											
	     });
	}) ($jac);
}

/**
 * Delete comment
 *
 * @param integer id 			Comment id
 * @param integer currentTypeID Current type id
 * @param integer parentType 	Parent type id
 *
 * @return void
 */
function deleteComment(id, currentTypeID, parentType){	
	var action  = confirm($jac('#hidDeleteComment').val());
	var errorDelete = $jac("#hidYouMustDelete").val();
	var reload = 0;
	
	if (action){		
		// check sub of comment
		var url = "index.php?option=com_jacomment&view=comments&type=delete&layout=checksubofcomment&id="+ id +"&curenttypeid="+ currentTypeID +"&tmpl=component";
		$jac.ajax({
            type: "POST",
            url: url,
            success: function(msg){
                var msg = $jac.trim(msg);
                if(msg != "OK"){
                    // alert($jac("#jav-typeid_0").attr('class'));
                    // alert($jac('#hidYouMustDelete').val());
                    alert(errorDelete);
                    return;
                } else {
                    var url = "index.php?option=com_jacomment&view=comments&type=delete&layout=changetype&id="+ id +"&curenttypeid="+ currentTypeID +"&tmpl=component";
                    if($jac('#keywordsearch').length && $jac('#keywordsearch').val()!= ""){
                        url += "&keyword=" + $jac('#keywordsearch').val();
                    }

                    if($jac('#slComponent').length && $jac('#slComponent').val() != ""){
                        url += "&optionsearch=" + escape($jac('#slComponent').val());
                    }
                    if($jac('#slSource').length){
                        url += "&sourcesearch=" + escape($jac('#slSource').val());
                    }
                    if($jac('#jacReported').length){
                        if($jac('#jacReported').attr('checked') == true)
                            url += "&reported=" + escape($jac('#jacReported').val());
                    }

                    url = getUrlSort(url);

                    if($jac('#limitstart'+currentTypeID).length)
                        url += "&limitstart="+$jac('#limitstart'+currentTypeID).val();
                    if($jac('#list'+currentTypeID).length)
                        url += "&limit="+ $jac('#list'+currentTypeID).val();

                    var reload = jcomment_ajax_load(url);

                    if(currentTypeID != 0){
                        var clicked = $jac("#jav-typeid_0").attr('class');
                        if(clicked.indexOf('loaded') != -1){
                            $jac("#jav-typeid_0").removeClass('loaded');
                        }
                    }else{
                        var clicked = $jac("#jav-typeid_" + parentType).attr('class');
                        if(clicked.indexOf('loaded') != -1){
                            $jac("#jav-typeid_" + parentType).removeClass('loaded');
                        }
                    }

                    if (reload == 1)
                        window.document.adminForm.submit();
                    else
                        setTimeout("hiddenMessage()", 5000);

                }
            }
		});						
				
	}
}

/**
 * Get code of comment tab
 *
 * @param string task Task of current page
 *
 * @return mixed 
 */
function getCodeTypeOfTab(task){
	if(task == "approve"){
		return 1;
	}else if(task == "unapprove"){
		return 0;
	}else if(task == "delete"){
		return "delete";
	}else{
		return 2;
	}
}

/**
 * Load items by paging
 *
 * @param integer limitstart Start offset position
 * @param integer limit		 Limit items
 * @param string  order		 Order type
 * @param string  keyword	 Keyword for searching
 *
 * @return void
 */
function jac_doPaging( limitstart, limit, order, keyword ){
	var mainUrl = "index.php?tmpl=component&option=com_jacomment&view=comments&layout=paging&limitstart=0&limit=" + eval(limit) + "&curenttypeid="+ $jac('#currentTypeID').val();
	
	if(order){
		mainUrl += "&order=" + escape(order);
	}
	
	if(keyword){
		mainUrl += "&keyword=" + escape(keyword);
	}
	
	if($jac('#slComponent').length && $jac('#slComponent').val() != ""){
		mainUrl += "&optionsearch=" + escape($jac('#slComponent').val());
	}
    if($jac('#slSource').length){
        mainUrl += "&sourcesearch=" + escape($jac('#slSource').val());
    }	
	
	if($jac('#jacReported').length){
		if($jac('#jacReported').attr('checked') == true)
			mainUrl += "&reported=" + escape($jac('#jacReported').val());
	}	
	
	mainUrl = getUrlSort(mainUrl);
	
	jcomment_ajax_load(mainUrl, $jac('#currentTypeID').val());
}

/**
 * Ajax pagination
 * 
 * @param string url   Current page URL
 * @param string divid Div id
 *
 * @return void
 */
function jac_ajaxPagination(url, divid) {	
	if(url.indexOf('?') > 0) {
		url = url + '&curenttypeid='+ $jac('#currentTypeID').val();
	}else {		
		url = url + '?curenttypeid='+ $jac('#currentTypeID').val();
	}	
	listID = "#list" + $jac("#currentTypeID").val();
	url = url + "&limit=" + $jac(listID).val();
	if(url.indexOf('limitstart')<=0){
		url = url + "&limitstart=0";
	}
	
	if($jac('#keywordsearch').length && $jac('#keywordsearch').val()!= ""){
		url += "&keyword=" + $jac('#keywordsearch').val();
	}
	
	if($jac('#slComponent').length && $jac('#slComponent').val() != ""){
		url += "&optionsearch=" + escape($jac('#slComponent').val());
	}
    if($jac('#slSource').length){
        url += "&sourcesearch=" + escape($jac('#slSource').val());
    }
	
    if($jac('#jacReported').length){
		if($jac('#jacReported').attr('checked') == true)
			url += "&reported=" + escape($jac('#jacReported').val());
	}	
	
	url = getUrlSort(url);
	jcomment_ajax_load(url, $jac('#currentTypeID').val());
	// pr_ajax = new Ajax(url,{method:'get', update:divid,
	// onComplete:update}).request();
}

/**
 * Check string data
 *
 * @param object el 	   Element
 * @param string class_css CSS class
 *
 * @return void
 */
function checkdataString(el, class_css) {
	var li_parent = $jac(el.parentNode.parentNode);
	if (el.value != '')
		li_parent.removeClass(class_css);
	else
		li_parent.addClass(class_css);
}

/**
 * Close message
 *
 * @return void
 */
function closemessage(){
    $jac(document).ready(function($) {
        var id='#'+jav_header;
        if($(id).length) {
            $(id).css('z-index','10');
            $('#jac-msg-succesfull').css('display','none');
        }
    });
}

/**
 * Show message in back-end
 *
 * @return void
 */
function displaymessageadmin(){
    $jac(document).ready(function($) {
        var id='#'+jav_header;
        if($(id).length) {
            $(id).css('z-index','1');
            $('#jac-msg-succesfull').css('display','');
        }
        setTimeout('closemessage()', 4500);
    });
}

/**
 * Display message with timeout
 *
 * @param integer timeDelay Mili-second to close message
 *
 * @return void
 */
function displaymessage(timeDelay){
    $jac(document).ready(function($) {
        var id='#'+jav_header;
        if($(id).length) {
            $(id).css('z-index','1');
            $('#jac-msg-succesfull').css('display','');
        }
        if(timeDelay)
            setTimeout('closemessage()', timeDelay);
        else
            setTimeout('closemessage()', 4000);
    });
}

/**
 * Close message
 *
 * @param string IDE IDE
 *
 * @return void
 */
function jacclosemessage(IDE){
    $jac(document).ready(function($) {
        var id='#'+jac_header;
        $(id).css('z-index','10');
        $(IDE).css('display','none');
        $('#jac-msg-succesfull').css('display','none');
    });
}
/**
 * Display message
 *
 * @param string IDE IDE
 *
 * @return void
 */
function jacdisplaymessage(IDE){
    $jac(document).ready(function($) {
        var id='#'+jac_header;
        $(id).css('z-index','1');
        $(IDE).css('display','');
        $('#jac-msg-succesfull').css('display','');
    });
    setTimeout('jacclosemessage('+ IDE +')', 2500);
}

/**
 * AJAX load
 *
 * @param string url URL to load data
 *
 * @return void
 */
function jcomment_ajax_load(url, doReload) {
    return (function($) {
        jac_displayLoadingSpan();
        $.getJSON(url, function(response){
            return jav_parseData(response, doReload);
        });
    })($jac);
}

/**
 * AJAX submit
 *
 * @param string url 	URL to submit data
 * @param array  params Data as parameters
 *
 * @return void
 */
function jcomment_ajax_submit(url, params) {	
    return (function($) {
        jac_displayLoadingSpan();
        $.post(url, params, function(response){
            var myResponse = eval(response);
            return jav_parseData(myResponse);
        });
    })($jac);
}

/**
 * Save reply comment
 *
 * @param integer currentTypeID Current type id
 *
 * @return void
 */
function saveReplyComment(currentTypeID){
	var parentID = $jac("#currentCommentID").val();
	// checking spelling - return
	if($jac("#newcomment").html()!= undefined && $jac("#newcomment").val() == undefined && $jac("#checkLink").length){
		$jac("#err_newcomment").html($jac("#hidEndEditText").val());
		return;
	}
	
	if($jac("#newcomment").val() != undefined){
		var realText = "";
		if($jac('#newcomment').val() != undefined)
			realText = trim(stripcode($jac('#newcomment').val(), false, false));
		if(realText == ""){
			$jac("#newcomment").focus();
			$jac("#err_newcomment").html($jac("#hidInputComment").val());
			return;
		}else if(realText.length <   minLengthComment){
			$jac("#newcomment").focus();
			$jac("#err_newcomment").html($jac("#hidShortComment").val());
			return;
		}else if(realText.length > maxLengthComment){
			$jac("#newcomment").focus();
			$jac("#err_newcomment").html($jac("#hidLongComment").val());
			return;
		}
	}
    (function($) {
        var params = '';
        params = 'curenttypeid='+currentTypeID+'&option=com_jacomment&view=comments&task=saveComment&tmpl=component&parentid='+parentID+'&newcomment='+$("#newcomment").val()+'&subscription_type='+$("#subscription_type").val();
        if($("#formreply").length){
            params += '&'+$("#formreply").serialize();
        }
        jcomment_ajax_submit('index.php', params);
    })($jac);
}

/**
 * Pre-process before editing or reply comment
 *
 * @param integer id 			Comment id
 * @param integer currentTypeID Current type id
 * @param string  action		Pre-process action
 * 
 * @return void
 */
function actionBeforEditReply(id, currentTypeID, action){		
	// disable form edit of current id
	if($jac("#currentCommentID").val() != 0 && $jac("#currentCommentID").length){
		var currentID = $jac("#currentCommentID").val();
		var currentTypeArray = new Array (99, 0, 1, 20);		
		for(i = 0 ; i < currentTypeArray.length; i++){			
			if($jac("#fotter-comment-right-"+currentTypeArray[i]+"-"+currentID).length){
				if($jac("#fotter-comment-right-"+currentTypeArray[i]+"-"+currentID).css('display') == "none"){
					// when edit
					if($jac("#commentExpand"+currentTypeArray[i]+"_"+currentID ).css('display') == "none"){
						$jac("#jac-edit-comment-"+currentTypeArray[i]+"-"+currentID ).html("");
						$jac("#commentExpand"+currentTypeArray[i]+"_"+currentID ).css('display', "block");
						if($jac("#jac-attach-file-"+currentTypeArray[i]+"-"+currentID))
							$jac("#jac-attach-file-"+currentTypeArray[i]+"-"+currentID).css('display', "block");
					}else{
						$jac("#jac-result-reply-comment-"+currentTypeArray[i]+"-"+currentID ).html("");
					}
					$jac("#fotter-comment-right-"+currentTypeArray[i]+"-"+currentID ).css('display', "block");
				}				
			}			
		}				
		
	}
	
	// disable form edit:
	
	$jac("#fotter-comment-right-"+currentTypeID+"-"+id).css('display', "none");
	
	if(!action){
		$jac("#commentExpand"+currentTypeID+"_"+id).css('display', "none");
		if($jac("#jac-attach-file-"+currentTypeID+"-"+id).length)
			$jac("#jac-attach-file-"+currentTypeID+"-"+id).css('display', "none");
	}
		
	$jac("#currentCommentID").val(id);
}

/**
 * Load edit form
 *
 * @param integer id 			Comment id
 * @param integer currentTypeID Current type id
 *
 * @return void
 */
function editComment(id,currentTypeID){	
	if($jac("#expandComment"+currentTypeID+"-"+id).css('display') == "none"){
		$jac("#collapseComment"+currentTypeID+"-"+id).css('display', "none");
		$jac("#actionCollapseComment"+currentTypeID+"-"+id).css('display', "none");
		$jac("#expandComment"+currentTypeID+"-"+id).css('display', "block");
		$jac("#hidStatus"+currentTypeID+"-"+id).val(1);
		
		// save status of comment to session
		saveCommentToCokie(id);
	}
	var url = "index.php?tmpl=component&option=com_jacomment&view=comments&layout=editcomment";
	url += "&id="+id+"&currenttypeid="+currentTypeID;	
	
	actionBeforEditReply(id, currentTypeID);

	jcomment_ajax_load(url);		  
}

/**
 * Cancel edit comment
 *
 * @param integer id 			Comment id
 * @param integer currentTypeID Type id
 *
 * @return void
 */
function cancelEditComment(id, currentTypeID){	
	var url = "index.php?tmpl=component&option=com_jacomment&view=comments&task=cancelUploadComment";
	jcomment_ajax_load(url);
	
	$jac("#commentExpand"+currentTypeID+"_"+id).css('display', "block");
	$jac("#fotter-comment-right-"+currentTypeID+"-"+id).css('display', "block");
	$jac("#jac-edit-comment-"+currentTypeID+"-"+id).html("");
	if($jac("#jac-attach-file-"+currentTypeID+"-"+id).length)
		$jac("#jac-attach-file-"+currentTypeID+"-"+id).css('display', "block");
	$jac("#currentCommentID").val(0);
}

/**
 * Update comment
 *
 * @param integer id 			Comment id
 * @param integer currentTypeID Type id
 *
 * @return void
 */
function updateComment(id, currentTypeID) {		
	if($jac("#newcomment").html()!= undefined && $jac("#newcomment").val()== undefined && $jac("#checkLink").length){
		$jac("#err_newcomment").html($jac("#hidEndEditText").val());
		return;
	}		
	
	if($jac("#newcomment").val()!= undefined){
		var realText = trim(stripcode($jac('#newcomment').val(), false, false));
		
		if(realText == ""){
			$jac("#newcomment").focus();
			$jac("#err_newcomment").html($jac("#hidInputComment").val());
			return;
		}else if(realText.length <   minLengthComment){
			$jac("#newcomment").focus();
			$jac("#err_newcomment").html($jac("#hidShortComment").val());
			return;
		}else if(realText.length > maxLengthComment){
			$jac("#newcomment").focus();
			$jac("#err_newcomment").html($jac("#hidLongComment").val());
			return;
		}
	}else{
		return;
	}
	
    (function($) {
        var params = 'curenttypeid='+currentTypeID;
        params = '&'+$("#adminForm"+currentTypeID+"-"+id).serialize();

        if($("#formreply").length){
            params += '&'+$("#formreply").serialize();
        }

        jcomment_ajax_submit('index.php', params);
    })($jac);
}

/**
 * Post-process after reply
 *
 * @return void
 */
function successWhenReply(){
	var id = $jac("#currentCommentID").val();
	var currentTypeIDReply = $jac("#currentTypeID").val();
	$jac("#fotter-comment-right-"+currentTypeIDReply+"-"+id).css('display', "block");
	$jac("#currentCommentID").val(0);
}

/**
 * Post-process after edit
 *
 * @return void
 */
function successWhenEdit(){
	var id 			  = $jac("#currentCommentID").val();
	var currentTypeIDEdit = $jac("#currentTypeID").val();
	$jac("#commentExpand"+currentTypeIDEdit+"_"+id).css('display', "block");
	$jac("#fotter-comment-right-"+currentTypeIDEdit+"-"+id).css('display', "block");
	$jac("#jac-edit-comment-"+currentTypeIDEdit+"-"+id).html("");
	if($jac("#jac-attach-file-"+currentTypeIDEdit+"-"+id).length)
		$jac("#jac-attach-file-"+currentTypeIDEdit+"-"+id).css('display', "block");
	$jac("#currentCommentID").val(0);
}

/**
 * Cancel reply comment
 *
 * @param integer currentTypeID Current type id
 *
 * @return void
 */
function cancelReplyComment(currentTypeID){
	var id = $jac("#currentCommentID").val();
	$jac("#fotter-comment-right-"+currentTypeID+"-"+id).css('display', "block");
	$jac("#jac-result-reply-comment-"+currentTypeID+"-"+id).html("");
}

/**
 * Reply comment
 *
 * @param integer currentTypeID Current type id
 * @param integer id 			Comment id
 * @param string replyto 		Reply to
 *
 * @return void
 */
function replyComment(currentTypeID, id, replyto){
	if($jac("#expandComment"+currentTypeID+"-"+id).css('display') == "none"){
		$jac("#collapseComment"+currentTypeID+"-"+id).css('display', "none");
		$jac("#actionCollapseComment"+currentTypeID+"-"+id).css('display', "none");
		$jac("#expandComment"+currentTypeID+"-"+id).css('display', "block");
		$jac("#hidStatus"+currentTypeID+"-"+id).val(1);
		
		// save status of comment to session
		saveCommentToCokie(id);
	}
	var url = "index.php?tmpl=component&option=com_jacomment&view=comments&layout=replycomment";
	url += "&id="+id+"&currenttypeid="+currentTypeID;	
	url += "&replyto="+replyto;		
	
	if($jac('#keywordsearch').length && $jac('#keywordsearch').val()!= ""){
		url += "&keyword=" + $jac('#keywordsearch').val();
	}
	
	if($jac('#slComponent').length && $jac('#slComponent').val() != ""){
		url += "&optionsearch=" + escape($jac('#slComponent').val());
	}	
    if($jac('#slSource').length){
        url += "&sourcesearch=" + escape($jac('#slSource').val());
    }
    if($jac('#jacReported').length){
		if($jac('#jacReported').attr('checked') == true)
			url += "&reported=" + escape($jac('#jacReported').val());
	}	
	
	url = getUrlSort(url);
	
	actionBeforEditReply(id, currentTypeID, "reply");
		
	jcomment_ajax_load(url);
}

/**
 * Cancel comment
 *
 * @param integer curenttypeid Current type id
 * @param integer id 		   Comment id
 *
 * @return void
 */
function cancelComment(curenttypeid, id){
	var url = "index.php?tmpl=component&amp;option=com_jacomment&amp;view=comments&amp;task=cancelUploadComment";
	jcomment_ajax_load(url);
	
    $jac("#reply_comment_"+ curenttypeid + "_" + id).hide("");
}

/**
 * Save reply
 *
 * @param integer curenttypeid Current type id
 * @param integer id   		   Comment id
 *
 * @return void
 */
function saveReply(curenttypeid, id){
    var comment = $jac("#ta_reply_comment_"+ curenttypeid + "_" +id).val();
	var url = "index.php?tmpl=component&amp;option=com_jacomment&amp;view=comments&amp;task=savereply&no_html=1&displaymessage=show&comment=" + comment + "&parentid=" + id;
    if(comment){
        $jac.ajax({
            type: "POST",
            url:url,
            success: function(html){            	
            	$jac("#ta_reply_comment_"+ curenttypeid + "_" + id).val("");            	            	
                $jac("#show_reply_"+ curenttypeid + "_" + id).html($jac("#show_reply_"+ curenttypeid + "_" + id).html() + html);                
                $jac("#reply_comment_"+ curenttypeid + "_" + id).hide("");
            }
        });
    }
}

/**
 * Display loading span
 *
 * @return void
 */
function jac_displayLoadingSpan() {
    var id='#'+jav_header;
    if($jac(id).length) {
        $jac(id).css('z-index','1');
        $jac('#loader').show();
    }
}

function jac_hideLoadingSpan() {
    if($jac('#loader').length) {
        var id='#'+jav_header;
        $jac(id).css('z-index','10');
        $jac('#loader').hide();
    }
}

/**
 * Get sorting URL
 *
 * @param string url URL to load data
 *
 * @return string URL after add sorting parameter
 */
function getUrlSort(url){
	return (function($){
        if($("#jac_sort_comment").length){
            if($("#jac_sort_comment").attr("class") == "jac_sort_by_oldest"){
                url += "&sorttype=DESC";
            }else{
                url += "&sorttype=ASC";
            }
        }
        return url;
    })($jac);
}

/**
 * Sort comment
 *
 * @param string type 	  Sort type
 * @param string textDESC DESC text
 * @param string textASC  ASC text
 *
 * @return void
 */
function sortComment(type, textDESC, textASC){		
	var getcurrentTypeID = $jac("#currentTypeID").val();
	jac_displayLoadingSpan();
	if(type == "DESC"){	 				
		var url = "index.php?tmpl=component&option=com_jacomment&view=comments&layout=sortcomment&sorttype=DESC";
	}else{
		var url = "index.php?tmpl=component&option=com_jacomment&view=comments&layout=sortcomment&sorttype=ASC";
	}
		url += "&curenttypeid="+getcurrentTypeID;
		
		$jac("#jac_sort_comment").css('display', "none");
        $jac("#jac_span_sort_comment").removeAttr("style");
				
		if($jac('#limitstart'+getcurrentTypeID).length)
            url += "&limitstart=" + $jac('#limitstart'+getcurrentTypeID).val();
		if($jac('#list'+getcurrentTypeID).val()!= undefined)
            url += "&limit=" + $jac('#list'+getcurrentTypeID).val();
		
		if($jac('#keywordsearch').length && $jac('#keywordsearch').val()!= ""){
			url += "&keyword=" + $jac('#keywordsearch').val();
		}
		
		if($jac('#slComponent').length && $jac('#slComponent').val() != ""){
			url += "&optionsearch=" + escape($jac('#slComponent').val());
		}
        
        if($jac('#slSource').length){
            url += "&sourcesearch=" + escape($jac('#slSource').val());
        }
		if($jac('#jacReported').length){
			if($jac('#jacReported').attr('checked') == true)
			url += "&reported=" + escape($jac('#jacReported').val());
		}	
		jcomment_ajax_load(url);			
}

/**
 * Get URL with all selected check boxes
 *
 * @param string url Action URL
 *
 * @return string URL after adding selected check boxes
 */
function getCheckBoxSelected(url){
	var getCurrentTypeID = $jac("#currentTypeID").val();
	var arrayCheckBox = $jac('#jav-mainbox-'+getCurrentTypeID + ' input[name^=cid]');
	arrayCheckBox.each(function(i){
		if(arrayCheckBox[i].checked == true){
			url +="&cid[]=" + arrayCheckBox[i].value;
			saveCommentToCokie(arrayCheckBox[i].value, "delete");
		}
	});
	// hidAllStatus
	url += "&hidAllStatus"+ getCurrentTypeID +"=" + $jac("#hidAllStatus" + getCurrentTypeID).val();

	return url;
}

/**
 * Show list of Disqus comments
 *
 * @param string source Source of comment
 * @param string type 	Type of comment
 *
 * @return boolean True if it hasn't error and otherwise
 */
function showListCommentFromDisqus(source, type){
	// err-select-disqus
	if($jac("#select-file-disqus").val() == ""){
		$jac("#err-select-disqus").css('display', "block");
		return false;
	}
	var frm = document.adminForm;
    frm.source.value = source;
    frm.type.value = "showcomment";
	frm.group.value = "showcomment";
	frm.task.value = "showcomment";
    frm.action = "index.php?option=com_jacomment&view=imexport&task=showcomment";
    frm.submit();
    return true;
}
// END -- BBJACODE

/**
 * Strip code
 *
 * @param string  F 	  String before stripping
 * @param boolean G		  
 * @param boolean isQuote Strip quote or not
 *
 * @return string String after stripping
 */
function stripcode(F,G,isQuote){	
	if(isQuote){
		var C=new Date().getTime();
		
		while((startindex=F.indexOf("[QUOTE")) != -1){
			if(new Date().getTime()-C>2000){break;}			
			if((stopindex=F.indexOf("[/QUOTE]"))!= -1){
					fragment=F.substr(startindex,stopindex-startindex+8);
					F=F.replace(fragment,"");
			}else{
				break;
			}			
			F=trim(F);			
		}
	}
	if(G){
		F=F.replace(/<img[^>]+src="([^"]+)"[^>]*>/gi,"$1");
		var H=new RegExp("<(\\w+)[^>]*>","gi");
		var E=new RegExp("<\\/\\w+>","gi");
		F=F.replace(H,"");
		F=F.replace(E,"");
		var D=new RegExp("(&nbsp;)","gi");
		F=F.replace(D," ");
	}else{
		var A=new RegExp("\\[(\\w+)(=[^\\]]*)?\\]","gi");
		var I=new RegExp("\\[\\/(\\w+)\\]","gi");
		F=F.replace(A,"");
		F=F.replace(I,"");
	}
	return F;
}

/**
 * Trim string
 *
 * @param string A String before trimming
 *
 * @return string String after trimming
 */
function trim(A){
	while(A.substring(0,1)==" "){
		A=A.substring(1,A.length);
	}
	while(A.substring(A.length-1,A.length)==" "){
		A=A.substring(0,A.length-1);
	}
	while(A.substring(0,1)=="\n"){
		A=A.substring(1,A.length);
	}
	while(A.substring(A.length-1,A.length)=="\n"){
		A=A.substring(0,A.length-1);
	}
	return A;
}

/**
 * Reset filter comment list
 * 
 * @return void
 */
function resetFilter() {
	document.adminForm.keyword.value = "";
	document.adminForm.reported.checked = false;
	
	document.adminForm.submit();
	return;
}