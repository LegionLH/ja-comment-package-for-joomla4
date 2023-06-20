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

// JavaScript Document
$jaCmt = jQuery.noConflict();

var jac_activePopIn = 0;
var jac_idActive = '';
var timeout = '';
var jac_ajax = '';
var isExpandFormAddNew 		= 0;
var isExpandFormEdit		= 0;
var isAutoExpandFormAddNew  = 0;
var jac_header = 'jac-header';
var jac_textarea_cursor = -1;

/**
 * Initiate JA Comment
 * 
 * @return void
 */
function jac_init() {	
	jQuery(document).ready(
        function($) {
            $(this).click( function() {
                if (jac_idActive != '' && jac_activePopIn == 1) {
                    $(jac_idActive).removeClass('jac-active');
                    jac_activePopIn = 0;
                }
                jac_activePopIn = 1;
            });
            //$('#jav-dialog').hide('slow');
        });
}

/**
 * Initiate comment form
 * 
 * @param object form Form object
 * 
 * @return void
 */
function jac_init_expand(form){		
	jQuery(document).ready(function($) {		
		var formName = "#jac-post-new-comment";
		if (typeof form != 'undefined'){
			formName = "#jac-edit-comment";
		}
		//add action onclick or onblur in guest name and guest email
		else{					
			$(document).on('focus',"#jac-post-new-comment .jac-inner-text", function() {
				if($(this).attr('id') == "guestName" && $(this).val() == $("#jac_hid_text_name").val())
					$(this).val("");
				
				if($(this).attr('id') == "guestEmail" && $(this).val() == $("#jac_hid_text_email").val())
					$(this).val("");
				
				if($(this).attr('id') == "guestWebsite" && $(this).val() == $("#jac_hid_text_website").val())
					$(this).val("http://");
				
				if($(this).attr('id') == "textCaptcha" && $(this).val() == $("#jac_hid_text_captcha").val())
					$(this).val("");
				
				if($(this).attr('id') == "newcomment" && $(this).val() == $("#jac_hid_text_comment").val())
					$(this).val("");
			});
			
			$(document).on('blur',"#jac-post-new-comment .jac-inner-text",function() {
				if($(this).val() == "" || $(this).attr('id') == "guestWebsite"){
					if($(this).attr('id') == "guestName")
						$(this).val($("#jac_hid_text_name").val());
					
					if($(this).attr('id') == "guestEmail")
						$(this).val($("#jac_hid_text_email").val());
					
					if($(this).attr('id') == "guestWebsite" && ($(this).val() == "" || $(this).val() == "http://"))
						$(this).val($("#jac_hid_text_website").val());
					
					if($(this).attr('id') == "textCaptcha")
						$(this).val($("#jac_hid_text_captcha").val());
					
					if($(this).attr('id') == "newcomment")
						$(this).val($("#jac_hid_text_comment").val());
				}				
			});
		}
		
		if($.trim($(formName+ ' .jac-expand-form').html()) == "") return false;
		//if didn't find text area with class jac-expand-field - return
		if($(formName +" textarea.jac-expand-field").length <=0) return false;		
		
		//add event onclick for textare
		$(formName +" textarea.jac-expand-field").click(function() {			
			//if this form is collapse - expand it			
			if($(formName +"  .jac-expand-form").css("display") == "none"){
				$(formName +"  .jac-expand-form").slideDown("slow", function() {			    
					$(formName +" .jac-act-button > a").parent().removeClass("loading");
					$(formName +" .jac-act-button > a").html(JACommentConfig.mesCollapseForm);
					$(formName +" .jac-act-button > a").attr('title', JACommentConfig.mesCollapseForm);
					$(formName +" .jac-act-button").addClass("loaded");
				});				
			}
			return false;
	    });
		addActionButton(formName);
		
		// Remove action button then reappend it to 'li' element, so it can work on IE7
		$(formName +' .jac-act-button').remove();
		addActionButton(formName);
		
		$(formName +' .jac-act-button > a').click(function() {			
			//if form is being slide
			if($(this).parent().attr("class").indexOf("loading") != -1) return false;			
			$(this).parent().addClass("loading");			
			//if form is expand
			if($(this).parent().attr("class").indexOf("loaded") != -1){				
				$(this).parent().removeClass("loaded");
				$(formName +"  .jac-expand-form").slideUp('slow', function() {					
					$(formName +' .jac-act-button > a').html(JACommentConfig.mesExpandForm);
					$(formName +" .jac-act-button > a").attr('title', JACommentConfig.mesExpandForm);
					$(formName +' .jac-act-button').removeClass("loading");
				});
			}
			//if form is collapse
			else{				
				$(this).parent().addClass("loaded");
				$(formName +"  .jac-expand-form").slideDown('slow', function() {
					$(formName +' .jac-act-button > a').html(JACommentConfig.mesCollapseForm);
					$(formName +" .jac-act-button > a").attr('title', JACommentConfig.mesCollapseForm);
					$(formName +' .jac-act-button').removeClass("loading");
				});	
			}	
			return false;
	    });						
	});		
}

/**
 * Add action button to editor form
 *
 * @param string formName Form name
 *
 * @return void
 */
function addActionButton(formName){
	//check if exist toolbar.
	if(jQuery(formName +" .jac-wrapper-actions").length >0 && jQuery(formName +" .jac-act-button").length <=0){
		if(jQuery(formName +"  .jac-expand-form").css("display") == "none"){
			jQuery('<li class="jac-act-button"><a title="'+JACommentConfig.mesExpandForm+'" href="javascript:void(0)">'+ JACommentConfig.mesExpandForm +'</a></li>').appendTo(formName +"  .jac-wrapper-actions");
		}else{
			jQuery('<li class="jac-act-button loaded"><a title="'+JACommentConfig.mesCollapseForm+'" href="javascript:void(0)">'+ JACommentConfig.mesCollapseForm +'</a></li>').appendTo(formName +"  .jac-wrapper-actions");
		}
	}else{
		//don't exist toolbar.
		//don't add action button
		if(jQuery(formName +"  .jac-act-button").length <=0){
			//find element allow add button act
			if(jQuery(formName +" .jac-act-form").length >0){
				jQuery(formName +" .jac-act-form").show();
				if(jQuery(formName +"  .jac-expand-form").css('display') == "none"){
					jQuery('<div class="jac-act-button jac-li-act-only"><a title="'+JACommentConfig.mesExpandForm+'" href="javascript:void(0)">'+ JACommentConfig.mesExpandForm +'</a></div>').appendTo(formName +" .jac-act-form");
				}else{
					jQuery('<div class="jac-act-button jac-li-act-only loaded"><a title="'+JACommentConfig.mesCollapseForm+'" href="javascript:void(0)">'+  JACommentConfig.mesCollapseForm +'</a></div>').appendTo(formName +" .jac-act-form");
				}
			}else{
				if(jQuery(formName +"  .jac-expand-form").css('display') == "none"){
					jQuery('<li class="jac-act-button jac-li-act-only"><a title="'+JACommentConfig.mesExpandForm+'" href="javascript:void(0)">'+ JACommentConfig.mesExpandForm +'</a></li>').appendTo(formName +"  ul.form-comment");
				}else{
					jQuery('<li class="jac-act-button jac-li-act-only loaded"><a title="'+JACommentConfig.mesCollapseForm+'" href="javascript:void(0)">'+  JACommentConfig.mesCollapseForm +'</a></li>').appendTo(formName +"  ul.form-comment");
				}
			}
		}
	}
	return;
}

/**
 * Expand text area automatically
 * 
 * @param string  id  Text area id
 * @param boolean atd Use Auto the Deadline or not
 * 
 * @return void
 */
function jac_auto_expand_textarea(id, atd){
	jQuery(document).ready( function($) {
		if(id){
			var idTextArea = "newcommentedit";
		}else{
			var idTextArea = "newcomment";
		}
		
		if (atd == undefined) {
			atd = false;
		}
		
		var arrayText = $("#jac-container-textarea").find("textarea");
		var textArea = '';
		$.each(arrayText, function() {
		   if(this.id == idTextArea){
			   textArea = this;
		   }
		});
		if(idTextArea == "newcomment"){
			if ($("#guestName")) {
				$("#guestName").val($("#jac_hid_text_name").val());
			}
			if ($("#guestEmail")) {
				$("#guestEmail").val($("#jac_hid_text_email").val());
			}
			if ($("#textCaptcha")) {
				$("#textCaptcha").val('');
			}
			
			if (!atd) {
				textArea.value = '';
			}
			$("#jac-container-textarea").html();
			$("#jac-container-textarea").html(textArea);
		}
		
		if( JACommentConfig.isEnableAutoexpanding != 0){
		   jQuery('textarea#' + idTextArea).autoResize({
				// On resize:
				onResize : function() {
					$(this).css({opacity:0.8}); 		       
				},
				// After resize:
				animateCallback : function() {
					$(this).css({opacity:1});
				},
				// Quite slow animation:
				animateDuration : 300,
				// More extra space:
				extraSpace : 40,
				limit:300
			});
		}
		
		if(JACommentConfig.isEnableBBCode != 0){		
			DCODE.setTags (["LARGE", "MEDIUM", "HR", "B", "I", "U", "S", "UL", "OL", "SUB", "SUP", "QUOTE", "LINK", "IMG", "YOUTUBE", "HELP"]);			     			     
			DCODE.activate (idTextArea);
		}
		if(id){
			jac_init_expand("edit");
		}else{
			jac_init_expand();
		}
    });
}

/**
 * Insert smiley
 * 
 * @param object which Which object
 * 
 * @return void
 */
function jacInsertSmiley(which) {
    var cmt = $jaCmt('#newcomment');
	var text = cmt.val();
	if(cmt.attr('class').indexOf("jac-inner-text") != -1){
		if(text == $jaCmt("#jac_hid_text_comment").val()){
			text = "";
		}
	}	
	cmt.val(text.substring(0, jac_textarea_cursor) + which + text.substring(jac_textarea_cursor, text.length));
	jac_textarea_cursor = jac_textarea_cursor + which.length; 
}

/**
 * Insert smiley when edit
 * 
 * @param object which Which object
 * 
 * @return void
 */
function jacInsertSmileyEdit(which) {
    var cmt = $jaCmt('#newcommentedit');
    var text = cmt.val();
    cmt.val(text.substring(0, jac_textarea_cursor) + which + text.substring(jac_textarea_cursor, text.length));
	jac_textarea_cursor = jac_textarea_cursor + which.length;
}

/**
 * AJAX loading
 * 
 * @param string url	 AJAX URL
 * @param string type_id Type of loading
 * 
 * @return void
 */
function jac_ajax_load(url, type_id) {
	jac_displayLoadingSpan();	
	jav_option_type = type_id;

    jac_displayLoadingSpan();
    jac_ajax = $jaCmt.getJSON(url, function(res) {
        jac_parseData(res);
    });
}

/**
 * AJAX updating
 * 
 * @param string url AJAX URL
 * 
 * @return void
 */
function jac_ajax_update(url) {
    jac_ajax = $jaCmt.getJSON(url, function(res) {
    });
}

/**
 * AJAX loading for voting
 * 
 * @param string url AJAX URL
 * 
 * @return void
 */
function jac_ajax_load_vote(url) {
	jac_displayLoadingSpan();	
	jQuery(document).ready(
        function($) {
            jac_ajax = $.getJSON(url, function(res) {
                jac_parseData(res);
                jav_vote_total = parseInt($('#votes-left-' + jav_option_type).attr('value').trim());
                if(jav_vote_total==-1) jav_vote_total = 1000;
                if (jav_vote_total == 0) {
                    checkTypeOfTooltip('#jav-dialog', jav_option_type, 400, 'auto', 3000);
                }
            });
    });
}

/**
 * Check type of tooltip
 * 
 * @param string  divId 	 Div id of tooltip
 * @param string  type 		 Type of tooltip
 * @param integer width 	 Tooltip width
 * @param integer height 	 Tooltip height
 * @param integer time_delay Delay time
 * 
 * @return void
 */
function checkTypeOfTooltip(divId, type, width, height, time_delay) {
	jQuery(document).ready( function($) {
		$(divId).css( {
			'width' :width,
			'height' :height
		});
		switch (type) {
		case 'none':
			$(divId).hide('fast');
			break;
		case 'auto_hide':
			$(divId).show('slow');
			timeout = ( function() {
				$(divId).hide('slow');
			}).delay(time_delay);

			$(divId).hover( function() {
				$clear(timeout);
			}, function() {
				timeout = ( function() {
					$(divId).hide('slow');
				}).delay(time_delay);
			});
			break;
		case 'normal':
		default:
			$(divId).show('slow');
		}
	});
}

/**
 * Parse data
 * 
 * @param object  response Response object
 * @param boolean isParse  Parse data to display or not
 * 
 * @return void
 */
function jac_parseData(response, isParse) {	
	( function($) {
		if($('#jac-loader')) {
			var id='#'+jac_header;
			$(id).css('z-index','10');			
			$('#jac-loader').hide();
		}
		
		if(isParse){
			var reload = 0;
			var myResponse = null;
			if(response.data){
				var myResponse = response.data;
			}else{
				var myResponse = response;
			}
			jQuery.each(myResponse, function(i, item) {										
				var divId = item.id;
				var type = item.type;
				var value = item.content;
				
				if ($(divId).length) {
					if (type == 'html') {						
						if ($(divId)){												
							if (item.action != undefined && item.action=='newComment' && divId=='#jac-container-new-comment' && $(divId).html().trim() != '') {
								if ($('#jac-container-comment .comment-listwrap').html().trim() != '') {
									var tmp_oldComment = $('#jac-container-new-comment li.jac-row-comment').clone();
									$('#jac-container-new-comment li.jac-row-comment').remove();
									tmp_oldComment.appendTo('#jac-container-comment ol.comment-list');
									$(divId).html('');
								}
								else{
									var tmp_oldCommentBox = $('#jac-container-new-comment ol.comment-list').clone();
									$('#jac-container-new-comment ol.comment-list').remove();
									tmp_oldCommentBox.appendTo('#jac-container-comment div.comment-listwrap');
									$(divId).html('');
								}
								
							}
							$(divId).html(value);							
						}
						else
							alert('not found element');
					} else {
						if (type == 'reload') {
							if (value == 1)
								reload = 1;
						} else {
							if(type=='val'){
								$(divId).val(value);
							}else{
								if(type=="append"){
									if ($(divId, window.parent.document)){										
										if(divId == "#newcomment"){											
											if($(divId, window.parent.document).attr("class").indexOf("jac-inner-text") != -1){
												if($(divId, window.parent.document).val() == $("#jac_hid_text_comment", window.parent.document)) $(divId, window.parent.document).val(""); 
											}
										}
				                        $(divId, window.parent.document).val($(divId, window.parent.document).val() + value);				                        				                                    				                       
				                    }else{
				                        alert('not found element');
				                    }   
								}else if(type == "appendAfter"){
									if ($(divId, window.parent.document)){				                        
				                        $(divId, window.parent.document).val(value + "\n" + $(divId, window.parent.document).val());				                        
				                    }else{
				                        alert('not found element');
				                    }   
								}else if(type == "setdisplay"){
									if(value == "show"){
										$(divId).show();
									}else{
										$(divId).hide();
									}
								}else{
									$(divId).attr(type, value);
								}
							}
						}
					}
				}
			});
		}
	})($jaCmt);
}

/**
 * Show div
 * 
 * @param string divId Div id
 * 
 * @return void
 */
function jav_showDiv(divId) {
	( function($) {
		var objDiv = $(divId);		
		var clsDiv = objDiv.attr('class');		
		jac_idActive = divId;

		if (clsDiv != "undefined") {				
			var mainClass = clsDiv.split(' ');
			$('.' + mainClass[0]).removeClass('jac-active');
		}

		if ($chk(objDiv)) {			
			if (clsDiv != "undefined" && clsDiv.indexOf('jac-active') != -1) {
				objDiv.removeClass('jac-active');				
			} else {
				objDiv.addClass('jac-active');				
			}
		}

		jac_activePopIn = 0;
	})($jaCmt);
}

/**
 * Hide div
 * 
 * @param string divId Div id
 * 
 * @return void
 */
function jac_hideDiv(divId) {
	( function($) {
		var objDiv = $(divId);
		if ($chk(objDiv)) {
			objDiv.removeClass('jac-active');
		}

		jac_idActive = '';
		jac_activePopIn = 0;
	})($jaCmt);
}

/**
 * Show Terms and Conditions
 * 
 * @param string title Title of window
 * 
 * @return void
 */
function showWebsiteRules(title){
	jacCreatForm('showwebsiterules&view=comments&layout=showterm',0,600,400,0,0,title,1,'');
}

/**
 * Check e-mail address
 * 
 * @param string string E-mail address
 * 
 * @return boolean True if it is e-mail address, otherwise false
 */
function jac_isEmail(string) {
	return (string.search(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,5}|[0-9]{1,3})(\]?)$/) != -1);
}

/**
 * Show all statuses
 * 
 * @param integer itemid Item id
 * 
 * @return void
 */
function jac_show_all_status( itemid ){		
	jQuery(document).ready( function($) {		
		jav_showDiv('#jac-change-type-' + itemid + ' .statuses');
		$('#jac-change-type-' + itemid + ' .statuses').css('top', '-65px');
	});
}

function jacGetBaseUrl() {
    var url = JACommentConfig.jac_base_url;
    if(url.indexOf("?") == -1){
        url += "?";
    } else {
        url += "&";
    }
    if (location.href.indexOf("option=com_jacomment") == -1) {
        url += "option=com_jacomment&";
    }
    return url;
}
/**
 * Vote comment
 * 
 * @param integer ID 	   Comment id
 * @param integer typeVote Type of vote
 * 
 * @return void
 */
function voteComment(ID, typeVote){
    var url = jacGetBaseUrl();
	url +=  "tmpl=component&view=comments&task=votecomment&id=" + ID + "&typevote="+typeVote;
	jacomment_ajax_load(url);
}

/**
 * Build comment URL
 * 
 * @param string url 	URL
 * @param string action Action
 * 
 * @return string URL after building
 */
function buildCommentUrl(url, action){	
	url += "&contentoption=" + JACommentConfig.contentoption;
	url += "&contentid=" + JACommentConfig.contentid;
	url += "&commenttype=" + JACommentConfig.commenttype;
	if(action == "sentParent"){
		if(JACommentConfig.hdCurrentComment != 0) {
			url += "&parentid=" + JACommentConfig.hdCurrentComment;
		}
	}
	return url;
}

/**
 * Display children items
 * 
 * @param integer parentID Parent id
 * 
 * @return void
 */
function displayChild(parentID){		
	if($jaCmt('#childen-comment-of-'+parentID).css('display') != "none"){
		$jaCmt('#childen-comment-of-'+parentID).css('display', 'none');
		$jaCmt('#a-show-childen-comment-of-'+parentID).css('display', 'block');
		$jaCmt('#a-hide-childen-comment-of-'+parentID).css('display', 'none');

        if($jaCmt('#jac-loader')) {
            var id='#'+jac_header;
            $jaCmt(id).css('z-index','10');
            $jaCmt('#jac-loader').hide();

        }
		changeClassName("jac-row-comment-"+parentID, "isshowchild", "");
	}else{		
		$jaCmt('#childen-comment-of-'+parentID).css('display', 'block');
		
		$jaCmt('#a-show-childen-comment-of-'+parentID).css('display', 'none');
		$jaCmt('#a-hide-childen-comment-of-'+parentID).css('display', 'block');
		changeClassName("jac-row-comment-"+parentID, "", "isshowchild");

        var url = jacGetBaseUrl(); 
		url += "tmpl=component&view=comments&layout=showchild&parentid=" + parentID;
		url = buildCommentUrl(url);

        $jaCmt.ajaxSetup({ cache: false });
        var clicked = $jaCmt('#childen-comment-of-'+parentID).attr('class');
        if (clicked != "undefined" && clicked.indexOf('loaded') == -1) {
            jac_displayLoadingSpan();
            $jaCmt.getJSON(url, function(response){
                var reload = 0;
                jac_parseData(response,1);
            });

            $jaCmt('#childen-comment-of-'+parentID).addClass('loaded');
        }
	}
}

/**
 * Do paging
 * 
 * @param integer limitstart Start offset position
 * @param integer limit 	 Limit records
 * @param string  order 	 Order by string
 * @param string  key 	 	 Keyword to search
 * 
 * @return void
 */
function jac_doPaging( limitstart, limit, order, key ){	
	cancelComment("cancelReply",0,"Reply","Posting");
	jac_displayLoadingSpan();
    var url = jacGetBaseUrl();
	var mainUrl = url + "tmpl=component&view=comments&layout=paging&limitstart=0&limit=" + eval(limit);
		
	if(order){		
		mainUrl += "&orderby=" + escape(order);
	}else{		
		if($jaCmt('#orderby').length){
			mainUrl += "&orderby=" + escape($jaCmt('#orderby').val());
			var typeorderby = getSortType($jaCmt('#orderby').val());
			mainUrl += "&typeorderby=" + typeorderby;
		}
		
	}	
	if(key){
		mainUrl += "&key=" + escape(key);
	}	
	mainUrl = buildCommentUrl(mainUrl);	
	
	jacomment_ajax_load(mainUrl);	
}

/**
 * AJAX load function
 * 
 * @param string url  AJAX URL
 * @param array  data GET data
 * 
 * @return void
 */
function jacomment_ajax_load(url, data, callback) {
	if(data){			 
		jQuery.post(url, data, function(response){				 
			jac_parseData(response, 1);												
			if(jQuery.isFunction(callback)){
			callback();
			}
	     }, 'json');	
	}else{						
		jQuery.getJSON(url, data, function(response){
			jac_parseData(response, 1);												
			if(jQuery.isFunction(callback)){
				callback();
			}
		});	
	}
}

/**
 * AJAX pagination
 * 
 * @param string url   AJAX URL
 * @param string divid Div id
 * 
 * @return void
 */
function jac_ajaxPagination(url, divid) {		
	cancelComment("cancelReply",0,"Reply","Posting");
	jac_displayLoadingSpan();
	if(url.indexOf('?') > 0) {
		url = url + '&amp;';
	}else {		
		url = url + '?';
	}
	
	url = url + "&limit=" + $jaCmt('#list').val();
	if(url.indexOf('limitstart')<=0){
		url = url + "&limitstart=0";
	}
	
	if($jaCmt('#orderby').length){
		url += "&orderby=" + escape($jaCmt('#orderby').val());
		var typeorderby = getSortType($jaCmt('#orderby').val());
		url += "&typeorderby=" + typeorderby;
	}
	
	url = buildCommentUrl(url);
	url = url.replace(/&amp;/g,"&");	
	jacomment_ajax_load(url,"pa");
}

/**
 * Close message
 * 
 * @param string IDE IDE
 * 
 * @return void
 */
function jacclosemessage(IDE){	
	jQuery(document).ready(function($) {
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
	jQuery(document).ready(function($) {
		var id='#'+jac_header;
		$(id).css('z-index','1');
		$(IDE).css('display','');
		$('#jac-msg-succesfull').css('display','');
	});		
	setTimeout('jacclosemessage('+ IDE +')', 2500);	
}

/**
 * Get sort type
 * 
 * @param string Sort by date or voted
 * 
 * @return string Type of sorting
 */
function getSortType(sort){	
	return (function($) {
        if(sort == "date"){
            var sortID 		= $("#jac-sort-by-date");
        }else{
            var sortID 		= $("#jac-sort-by-voted");
        }

        if(sortID.attr("class") == "jac-sort-by"){
            var typeorderby = "ASC";
        }else if(sortID.attr("class") == "jac-sort-by-active-desc"){
            var typeorderby = "DESC";
        }else{
            var typeorderby = "ASC";
        }
        return typeorderby;
    })($jaCmt);
}

/**
 * Sort comment
 * 
 * @param string sort Type of sort
 * @param object obj  Object
 * 
 * @return void
 */
function sortComment(sort, obj){
	( function($) {
        cancelComment("cancelReply",0,"Reply","Posting");
        jac_displayLoadingSpan();
        var limit = 10;
        if($('#list').length)
            limit = $('#list').val();
        var url = jacGetBaseUrl();
        url += "tmpl=component&view=comments&layout=sort&orderby=" + sort + "&limit=" + limit;

		if(sort=="date"){
			var sortID 		= $("#jac-sort-by-date");
			var sortBackID  = $("#jac-sort-by-voted");
		}else{
			var sortID 		= $("#jac-sort-by-voted");
			var sortBackID  = $("#jac-sort-by-date");
		}
				
		if(sortID.attr("class") == "jac-sort-by"){			
			sortID.removeClass("jac-sort-by");
			sortID.addClass("jac-sort-by-active-asc");
			//edit title
			
			if(sort=="date"){
				sortID.attr("title", JACommentConfig.dateDESC);
			}else{
				sortID.attr("title", JACommentConfig.votedDESC);
			}
			
			var typeorderby = "ASC";
		}else if(sortID.attr("class") == "jac-sort-by-active-desc"){			
			sortID.removeClass("jac-sort-by-active-desc");
			sortID.addClass("jac-sort-by-active-asc");
			var typeorderby = "ASC";
			
			if(sort=="date"){
				sortID.attr("title", JACommentConfig.dateDESC);
			}else{
				sortID.attr("title", JACommentConfig.votedDESC);
			}
		}else{				
			sortID.removeClass("jac-sort-by-active-asc");
			sortID.addClass("jac-sort-by-active-desc");
			var typeorderby = "DESC";
			
			if(sort=="date"){
				sortID.attr("title", JACommentConfig.dateASC);
			}else{
				sortID.attr("title", JACommentConfig.votedASC);
			}
		}	
		
		if(sortBackID.attr("class") != "jac-sort-by"){			
			if(sortBackID.attr("class") == "jac-sort-by-active-asc")
				sortBackID.removeClass("jac-sort-by-active-asc");			
			else
				sortBackID.removeClass("jac-sort-by-active-desc");
			
			sortBackID.addClass("jac-sort-by");
		}

        url += "&typeorderby="+typeorderby;
        url = buildCommentUrl(url);

        $('#orderby').val(sort);
        jacomment_ajax_load(url);
	}) ($jaCmt);
}

/**
 * Check for errors when add new comment
 * 
 * @param string ID Editor id
 * 
 * @return boolean True if it has errors, otherwise false
 */
function checkErrorNewComment(ID){
    return (function($){
        var checkError = 0;
        var textEnd				  = "";
        $("#ja-addnew-error").html('');
        //check if user is check spelling edit text.
        if($("#newcomment").html() != undefined && $("#newcomment").val() == undefined && $("#checkLink").length){
            $("#err_newcomment").css('display', 'block');
            $("#err_newcomment").html(JACommentConfig.hidEndEditText);
            changeClassName("newcomment","ja-error", "ja-error");
            changeClassName("jac-editor-addnew","ja-error", "ja-error");
            i++;
            checkError = 1;
        }

        if($('#newcomment').length && checkError == 0){
            currentID = JACommentConfig.hdCurrentComment;
            var realText  = "";
            if($("#jac-a-reply-" +currentID).length && $("#jac-a-reply-" +currentID).css('display') == "none"){
                if($('#newcomment').val() != undefined)
                    realText = trim(stripcode($('#newcomment').val(), false, true));
            }else{
                if($('#newcomment').val() != undefined)
                    realText = trim(stripcode($('#newcomment').val(), false, false));
            }

            if($('#newcomment').val() == '' || realText == '' || $('#newcomment') == undefined){
                $("#err_newcomment").css('display', 'block');
                $("#err_newcomment").html(JACommentConfig.hidInputComment);
                changeClassName("newcomment","ja-error", "ja-error");
                changeClassName("jac-editor-addnew","ja-error", "ja-error");
                if(checkError == 0)
                    $('#newcomment').focus();
                checkError = 1;
            }else{
                //check length of comment.
                //alert(JACommentConfig.minLengthComment + "aa" + realText.length);
                if(realText.length < JACommentConfig.minLengthComment){
                    changeClassName("newcomment","ja-error", "ja-error");
                    changeClassName("jac-editor-addnew","ja-error", "ja-error");
                    $("#err_newcomment").css('display', 'block');
                    $("#err_newcomment").html(JACommentConfig.errorMinLength);
                    if(checkError == 0)
                        $('#newcomment').focus();
                    checkError = 1;
                }

                if(realText.length > JACommentConfig.maxLengthComment){
                    //tinyMCE.execInstanceCommand("comment-editor-new"+ID, "mceFocus");
                    changeClassName("newcomment","ja-error", "ja-error");
                    changeClassName("jac-editor-addnew","ja-error", "ja-error");
                    $("#err_newcomment").css('display', 'block');
                    $("#err_newcomment").html(JACommentConfig.errorMaxLength);
                    if(checkError == 0)
                        $('#newcomment').focus();
                    checkError = 1;
                }
            }
        }
        if(checkError == 0){
            changeClassName("newcomment","ja-error", "");
            changeClassName("jac-editor-addnew","ja-error", "");
            $("#err_newcomment").css('display', 'none');
            $("#err_newcomment").html("");
        }

        //is user is guest
        if($('#guestName').length) {
            if($('#guestName').val() == "" || ($('#guestName').val() == $('#jac_hid_text_name').val() && $("#guestName").attr('class').indexOf("jac-inner-text"))){
                changeClassName("guestName","ja-error", "ja-error");
                $("#err_guestName").css('display', 'block');
                $("#err_guestName").html(JACommentConfig.hidInputName);

                if(checkError == 0)
                    $('#guestName').focus();
                checkError = 1;
            }else{
                changeClassName("guestName","ja-error", "");
                $("#err_guestName").html("");
                $("#err_guestName").css('display', 'none');
            }

            if($('#guestEmail').val() == "" || ($('#guestEmail').val() == $('#jac_hid_text_email').val() && $("#guestEmail").attr('class').indexOf("jac-inner-text"))){
                changeClassName("guestEmail","ja-error", "ja-error");
                $("#err_guestEmail").css('display', 'block');
                $("#err_guestEmail").html(JACommentConfig.hidInputEmail);
                if(checkError == 0)
                    $('#guestEmail').focus();
                checkError = 1;
            }else{
                //changeClassName("guestEmail","ja-error", "");
                $("#err_guestEmail").html('');
                var filter = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
                if (!filter.test($('#guestEmail').val())) {
                    changeClassName("guestEmail","ja-error", "ja-error");
                    $("#err_guestEmail").css('display', 'block');
                    $("#err_guestEmail").html(JACommentConfig.hidValidEmail);
                    if(checkError == 0)
                        $('#guestEmail').focus();
                    checkError = 1;
                }else{
                    changeClassName("guestEmail","ja-error", "");
                    $("#err_guestEmail").html('');
                    $("#err_guestEmail").css('display', 'none');
                }
            }

        }

        //check input captcha
        if($("#textCaptcha").length){
            if($("#textCaptcha").val() == "" || ($('#textCaptcha').val() == $("#jac_hid_text_captcha").val() && $("#textCaptcha").attr('class').indexOf("jac-inner-text"))){
                changeClassName("textCaptcha","ja-error", "ja-error");
                $("#err_textCaptcha").css('display', 'block');
                $("#err_textCaptcha").html(JACommentConfig.hidInputCaptcha);
                if(checkError == 0)
                    $('#textCaptcha').focus();
                checkError = 1;
            }else{
                changeClassName("textCaptcha","ja-error", "");
                $("#err_textCaptcha").html('');
                $("#err_textCaptcha").css('display', 'none');
            }
        }

        if($("#chkTermsAddnew").length){
            if($("#chkTermsAddnew").attr('checked') == undefined || $("#chkTermsAddnew").attr('checked') == 'undefined'){
                changeClassName("jac-terms","ja-error", "ja-error");
                $("#err_TermsAddnew").css('display', 'block');
                $("#err_TermsAddnew").html(JACommentConfig.hidAgreeToAbide);
                checkError = 1;
            }else{
                changeClassName("jac-terms","ja-error", "");
                $("#err_TermsAddnew").html('');
                $("#err_TermsAddnew").css('display', 'none');
            }
        }

        if(checkError == 1){
            jacLoadNewCaptcha(0);
            return false;
        }

        return true;
    })($jaCmt);

}

/**
 * Refresh page
 * 
 * @return void
 */
function refreshPage(){
	window.location = document.location;
}

/**
 * Post new comment
 * 
 * @param string id Editor id
 * 
 * @return void
 */
function postNewComment(id){
    (function($) {
        var flag = checkErrorNewComment(id);
        if (flag) {
            if($("#btlAddNewComment").length)
                $("#btlAddNewComment").attr('disabled', true);
            else{
                $("#jac_post_new_comment").css('display', "none");
                $("#jac_span_post_new_comment").css('display', "block");
            }
            jac_displayLoadingSpan();
            var url = jacGetBaseUrl();
            url  += "view=comments&task=addnewcomment&tmpl=component";
            var data = '';
            data += "newcomment=" + encodeURIComponent($("#newcomment").val());

            if($("#subscription_type").val() != undefined)
                data +="&subscription_type=" + $("#subscription_type").val();

            if($("#textCaptcha").val() != undefined)
                data +="&captcha=" + escape($("#textCaptcha").val());

            if($("#guestName").val() != undefined){
                data +="&name=" + encodeURIComponent($("#guestName").val());
            }else{
                if($("#jac-text-user").html() != undefined){
                    data +="&islogin=1";
                }
            }

            if($("#guestEmail").val() != undefined){
                data +="&email=" + escape($("#guestEmail").val());
            }

            if($("#guestWebsite").val() != undefined && CheckValidUrl($("#guestWebsite").val())){
                data +="&website=" + escape($("#guestWebsite").val());
            }

            if ($("#comment-location").val() != undefined) {
                data += "&address=" + $("#comment-location").val();
            }

            if ($("#latitude").val() != undefined) {
                data += "&lat=" + escape($("#latitude").val());
                data += "&lng=" + escape($("#longitude").val());
            }

            url = buildCommentUrl(url, "sentParent");
            data += "&jacomentUrl=" + escape(JACommentConfig.jacomentUrl);
            data += "&contenttitle=" + encodeURIComponent(JACommentConfig.contenttitle);
            data +="&currenttotal=" + $("#jac-number-total-comment").html();

            if($("#form1").length){
                data += "&"+$("#form1").serialize();
            }

            jacomment_ajax_load(url, data, function() {
                if (parseInt(JACommentConfig.isEnableCharacterCounter)) {
                    $jaCmt('#newcomment').counter({
                        count: 'up',
                        goal: JACommentConfig.maxLengthComment,
                        msg: '&nbsp;/&nbsp;' + JACommentConfig.maxLengthComment
                    });
                    $jaCmt('#jac-container-textarea #newcomment_counter').remove();
                }
            });
        } else {
            jac_init_expand();
        }
    }) ($jaCmt);
}

/**
 * Check for valid URL
 * 
 * @param string strUrl URL
 * 
 * @return boolean True if URL is correct, otherwise false
 */
function CheckValidUrl(strUrl){
    var RegexUrl = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
    return RegexUrl.test(strUrl);
}

/**
 * Show add new button
 * 
 * @param object obj1 Object 1
 * @param object obj2 Object 2
 * 
 * @return void
 */
function showButtonAddNew(obj1, obj2){	
    $jaCmt(obj2).attr('disabled', !$jaCmt(obj1).attr('checked'));
}

/**
 * Report a comment
 * 
 * @param integer ID Comment id
 * 
 * @return void
 */
function reportComment(ID){	
	jac_displayLoadingSpan();
    var url = jacGetBaseUrl();
	url += "tmpl=component&view=comments&task=reportcomment&id=" + ID;
	url = buildCommentUrl(url);		
	url += "&orderby=" + $jaCmt('#orderby').val();
	url += "&limitstart=" + $jaCmt('#limitstart').val();
	if($jaCmt('#list').length) {
		url += "&limit=" + $jaCmt('#list').val();
	}
	jacomment_ajax_load(url);
}

/**
 * Undo report action
 * 
 * @param integer ID Comment id
 * 
 * @return void
 */
function undoReportComment(ID){	
	jac_displayLoadingSpan();
    var url = jacGetBaseUrl();
	url += "tmpl=component&view=comments&task=undoReportComment&id=" + ID;
	url = buildCommentUrl(url);	
	url += "&orderby=" + $jaCmt('#orderby').val();
	url += "&limitstart=" + $jaCmt('#limitstart').val();
	if($jaCmt('#list').length) {
		url += "&limit=" + $jaCmt('#list').val();
	}
	
	jacomment_ajax_load(url);
}

/**
 * Delete a comment
 * 
 * @param integer ID 		Comment id
 * @param integer UserID 	User id
 * @param string  UserEmail User e-mail
 * @param string  UserName  User name
 * 
 * @return void
 */
function deleteComment(ID, UserID, UserEmail, UserName){	
	cancelComment("delete");	
	if($jaCmt('#list').length)
		var limit = $jaCmt('#list').val();
    var url = jacGetBaseUrl();
	url += "tmpl=component&view=comments&task=deletecomment&id="+ID;
	url += "&orderby=" + $jaCmt('#orderby').val();
	var typeorderby = getSortType($jaCmt('#orderby').val());
	url += "&typeorderby=" + typeorderby;
	url += "&limitstart=" + $jaCmt('#limitstart').val();
	if($jaCmt('#list').length) {
		url += "&limit=" + $jaCmt('#list').val();
	}
	
	url = buildCommentUrl(url);
	
	jacomment_ajax_load(url);
}

/**
 * Edit a comment
 * 
 * @param integer ID 	Comment id
 * @param string  reply Action when edit
 * 
 * @return void
 */
function editComment(ID, reply){	
	jac_displayLoadingSpan();		
	cancelComment("edit", ID, reply);
    var url = jacGetBaseUrl();
	
	if (location.href.indexOf("com_jacomment") == -1) {
		url += "option=com_jacomment&";
	}
	
	url += "tmpl=component&view=comments&layout=showformedit&id="+ID+"&ran="+Math.random();
	jacomment_ajax_load(url, null, function() {
		if (parseInt(JACommentConfig.isEnableCharacterCounter)) {
			$jacJQuery('.form-character-count').html('');
			$jacJQuery('.form-character-count').popover({placement:'top'});
			
			$jaCmt('#newcommentedit').counter({
				count: 'up',
				goal: JACommentConfig.maxLengthComment,
				msg: '&nbsp;/&nbsp;' + JACommentConfig.maxLengthComment
			});
			if (parseInt(JACommentConfig.commentFormPosition) == 1) {
				// Comment form position is TOP
				$jaCmt('#newcommentedit_counter').appendTo($jaCmt('.form-character-count')[1]);
			}
			else {
				// Comment form position is BOTTOM
				$jaCmt('#newcommentedit_counter').appendTo($jaCmt('.form-character-count')[0]);
			}
		}
	
		if (parseInt(JACommentConfig.isEnableLocationDetection)) {
			JALocation.initAutocomplete(1);
		}
	});
	JACommentConfig.hdCurrentComment = ID; 
}

/**
 * Cancel when edit a comment
 * 
 * @param integer ID Comment id
 * 
 * @return void
 */
function cancelEditComment(ID){
    var url = jacGetBaseUrl();
	url += "tmpl=component&view=comments&task=cancelUploadComment";
	jacomment_ajax_load(url);	
	$jaCmt('#jac-div-footer-'+ID).css('display', "block");
	$jaCmt('#jac-content-of-comment-'+ID).css('display', "block");
	$jaCmt('#jac-edit-comment-'+ID).html('');
	$jaCmt('#jac-edit-comment-'+ID).css('display', 'none');
	JACommentConfig.hdCurrentComment = 0;
	jac_init_expand();
	if($jaCmt('#jac-attach-file-'+ID).length)
		$jaCmt('#jac-attach-file-'+ID).css('display', 'block');
	if ($jaCmt('#jac-wrapper-form-add-new'))
		$jaCmt('#jac-wrapper-form-add-new').css('display', 'block');
	isExpandFormEdit = 0;  
	
	// Clear all error messages
	if ($jaCmt('#err_newcomment').length && trim($jaCmt('#err_newcomment').html()) != '') {
		$jaCmt('#err_newcomment').html('');
		changeClassName('newcomment', 'ja-error', '');
	}
	if ($jaCmt('#err_guestName').length && trim($jaCmt('#err_guestName').html()) != '') {
		$jaCmt('#err_guestName').html('');
		changeClassName('guestName', 'ja-error', '');
	}
	if ($jaCmt('#err_guestEmail').length && trim($jaCmt('#err_guestEmail').html()) != '') {
		$jaCmt('#err_guestEmail').html('');
		changeClassName('guestEmail', 'ja-error', '');
	}
	if ($jaCmt('#err_textCaptcha').length && trim($jaCmt('#err_textCaptcha').html()) != '') {
		$jaCmt('#err_textCaptcha').html('');
		changeClassName('textCaptcha', 'ja-error', '');
	}
	
	if (parseInt(JACommentConfig.isEnableCharacterCounter)) {
		$jacJQuery('.form-character-count').html('');
		$jacJQuery('.form-character-count').popover({placement:'top'});
		
		$jaCmt('#newcomment').counter({
			count: 'up', 
			goal: JACommentConfig.maxLengthComment,
			msg: '&nbsp;/&nbsp;' + JACommentConfig.maxLengthComment
		});
		$jaCmt('#newcomment_counter').appendTo('.form-character-count');
	}
	
	if (parseInt(JACommentConfig.isEnableLocationDetection)) {
		JALocation.initAutocomplete();
	}
}

/**
 * Display new captcha if it has errors when add new comment 
 * 
 * @return void
 */
function displayErrorAddNew(){	
	$jaCmt('#textCaptcha').focus();
}

/**
 * Save a comment
 * 
 * @param integer ID Comment id
 * 
 * @return void
 */
function saveComment(ID){
	return (function($) {
        var checkError = 0;

        //check if user is check spelling edit text.
        if($("#newcommentedit").html() != undefined && $("#newcommentedit").val() == undefined && $("#checkLink").length){
            changeClassName("newcommentedit","ja-error", "ja-error");
            //$("#ja-edit-error").html(JACommentConfig.hidEndEditText);
            $("#err_newcommentedit").html(JACommentConfig.hidEndEditText);
            checkError = 1;
        }

        if($('#newcommentedit').val() != undefined){
            var realText  = "";
            if($('#newcommentedit').val() != undefined){
                if($('#newcomment') && $('#newcomment').val().indexOf("[QUOTE") != -1)
                    realText = trim(stripcode($('#newcommentedit').val(), false, true));
                else
                    realText = trim(stripcode($('#newcommentedit').val(), false, false));
            }

            if(realText == '' || $('#newcommentedit') == undefined){
                changeClassName("newcommentedit","ja-error", "ja-error");
                $("#err_newcommentedit").html(JACommentConfig.hidInputComment);

                if(checkError == 0)
                    $('#newcommentedit').focus();
                checkError = 1
            }else{
                if(realText.length < JACommentConfig.minLengthComment){
                    changeClassName("newcommentedit","ja-error", "ja-error");
                    $("#err_newcommentedit").html(JACommentConfig.errorMinLength);

                    if(checkError == 0)
                        $('#newcommentedit').focus();

                    checkError = 1;
                }

                if(realText.length > JACommentConfig.maxLengthComment){
                    changeClassName("newcommentedit","ja-error", "ja-error");
                    $("#err_newcommentedit").html(JACommentConfig.errorMaxLength);

                    if(checkError == 0)
                        $('#newcommentedit').focus();
                    checkError = 1
                }
            }
        }

        if(checkError == 1)
            return;

        if($('#btlEditComment').length){
            $('#btlEditComment').attr('disabled', true);
        }else{
            $('#jac_edit_comment').css('display', 'none');
            $('#jac_span_edit_comment').css('display', 'block');
        }

        var url = jacGetBaseUrl();
        url += "view=comments&task=saveEditComment&tmpl=component&id=" + ID;
        var data = '';
        data += "&newcomment=" + encodeURIComponent($("#newcommentedit").val());

        if($("#subscription_type").val() != undefined)
            data +="&subscription_type=" + $("#subscription_type").val();

        data = buildCommentUrl(data);

        if($("#form1edit")[0] && $("#form1edit").serialize()){
            data += "&"+$("#form1edit").serialize();
        }

        jacomment_ajax_load(url, data, function() {
            if (parseInt(JACommentConfig.isEnableCharacterCounter)) {
                $jacJQuery('.form-character-count').html('');
                $jacJQuery('.form-character-count').popover({placement:'top'});

                $('#newcomment').counter({
                    count: 'up',
                    goal: JACommentConfig.maxLengthComment,
                    msg: '&nbsp;/&nbsp;' + JACommentConfig.maxLengthComment
                });
                $('#newcomment_counter').appendTo('.form-character-count');
            }

            if (parseInt(JACommentConfig.isEnableLocationDetection)) {
                JALocation.initAutocomplete();
            }

        });
	}) ($jaCmt);
}

/**
 * Action when edit successfully
 * 
 * @param integer ID Comment id
 * 
 * @return void
 */
function actionWhenEditSuccess(ID){	
	$jaCmt('#jac-edit-comment-'+ID).html('');
	$jaCmt('#jac-edit-comment-'+ID).css('display', 'none');
	$jaCmt('#jac-div-footer-'+ID).css('display', "block");
	$jaCmt('#jac-content-of-comment-'+ID).css('display', "block");
	if($jaCmt('#jac-attach-file-'+ID).length)
		$jaCmt('#jac-attach-file-'+ID).css('display', 'block');
	if($jaCmt("#jac-wrapper-form-add-new").length){
		$jaCmt("#jac-wrapper-form-add-new").css('display', 'block');
	}
	isExpandFormEdit = 0;
}

/**
 * Parse data in admin site
 * 
 * @param object response Response data
 * 
 * @return void
 */
function parseData_admin(response) {
    jQuery(document, window.parent.document).ready( function($) {    	
    	var reload = 0;    	
    	var myResponse = null;
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
                            jacFormHideIFrame(); 
                        }  
                        
                    }else{
                        alert('not found element');
                    }    
                }else if (type == 'append') {
                    if ($(divId, window.parent.document)){                    	
                    	if(divId == "#newcomment"){											
							if($(divId, window.parent.document).attr("class").indexOf("jac-inner-text") != -1){
								if($(divId, window.parent.document).val() == $("#jac_hid_text_comment", window.parent.document).val()) $(divId, window.parent.document).val(""); 
							}
						}
                        $(divId, window.parent.document).val($(divId, window.parent.document).val() + value);
                        
                        if(item.status!='ok'){
                            $('#ja-popup-wait').css( {
                                'display' :'none'
                            });
                        }else{                            
                            jacFormHideIFrame(); 
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

//function actionBeforEditReply(ID, reply, action, post){}

/**
 * Reply a comment
 * 
 * @param integer ID 	 Comment id
 * @param array	  post   Post data
 * @param string  reply  Reply or quote
 * @param string  action Action when reply
 * 
 * @return void
 */
function replyComment(ID, post, reply, action){
	if($jaCmt('#reply-'+ID).html() != undefined){
		if(post == $jaCmt('#reply-'+ID).html()){
			return;
		}	
	}
	if($jaCmt('#quote-'+ID).html() != undefined){
		if(post == $jaCmt('#quote-'+ID).html()){
			return;
		}	
	}	
	
	if(action){
		//load content of comment.
        //cancelComment("quote", ID, reply, post);
        cancelComment('cancelReply',ID,reply,post);
        var url = jacGetBaseUrl();
		url += "view=comments&task=show_quote&tmpl=component&id=" + ID;
		jacomment_ajax_load(url);
	}else{
		cancelComment("reply", ID, reply, post);
	}
	var strNewComment = $jaCmt("#newcomment").val();
	if($jaCmt('#guestName').length){strGuestName = $jaCmt("#guestName").val();}
	if($jaCmt('#guestEmail').length){strGuestEmail = $jaCmt("#guestEmail").val();}
	if($jaCmt('#guestWebsite').length){strGuestWebsite = $jaCmt("#guestWebsite").val();}
	if($jaCmt('#chkTermsAddnew').length){isChkTermsAddnew = $jaCmt("#chkTermsAddnew").attr('checked');}
	
	//load form reply from reply to reply.
	if(JACommentConfig.hdCurrentComment != 0 && $jaCmt("#jac-result-reply-comment-" + JACommentConfig.hdCurrentComment).html() != ""){
		$jaCmt("#jac-result-reply-comment-" + ID).html($jaCmt("#jac-result-reply-comment-" + JACommentConfig.hdCurrentComment).html());
		$jaCmt("#jac-result-reply-comment-" + ID).css('display', 'block');
		$jaCmt("#jac-result-reply-comment-" + JACommentConfig.hdCurrentComment).html('');
		$jaCmt("#jac-result-reply-comment-" + JACommentConfig.hdCurrentComment).css('display', 'none');
		
		if($jaCmt("#jac_cancel_comment_link").length)
			$jaCmt("#jac_cancel_comment_link").css('display', 'block');						
		if($jaCmt("#btlCancelComment").length)
			$jaCmt("#btlCancelComment").css('display', 'block');							
	}
	//load from add new form to reply form.
	else{			
		if($jaCmt("#jac-wrapper-form-add-new").css('display') == "none"){
			$jaCmt("#jac-wrapper-form-add-new").css('display', 'block');
		}								
		$jaCmt("#jac-result-reply-comment-" + ID).html($jaCmt("#jac-wrapper-form-add-new").html());
		$jaCmt("#jac-result-reply-comment-" + ID).css('display', 'block');
		$jaCmt("#jac-wrapper-form-add-new").html('');
			
		if($jaCmt("#jac_cancel_comment_link").length)
			$jaCmt("#jac_cancel_comment_link").css('display', 'block');
			
		if($jaCmt("#btlCancelComment").length)
			$jaCmt("#btlCancelComment").css('display', 'block');								
	}
	
	$jaCmt("#newcomment").val(strNewComment);
	if($jaCmt('#guestName').length){$jaCmt("#guestName").val(strGuestName);}
	if($jaCmt('#guestEmail').length){$jaCmt("#guestEmail").val(strGuestEmail);}
	if($jaCmt('#guestWebsite').length){$jaCmt("#guestWebsite").val(strGuestWebsite);}
	if($jaCmt('#chkTermsAddnew').length){$jaCmt("#chkTermsAddnew").attr('checked', isChkTermsAddnew);}
	
	// Clear all error messages
	if (trim($jaCmt('#err_newcomment').html()) != '') {
		$jaCmt('#err_newcomment').html('');
		changeClassName('newcomment', 'ja-error', '');
	}
	if ($jaCmt('#err_guestName').length && trim($jaCmt('#err_guestName').html()) != '') {
		$jaCmt('#err_guestName').html('');
		changeClassName('guestName', 'ja-error', '');
	}
	if ($jaCmt('#err_guestEmail').length && trim($jaCmt('#err_guestEmail').html()) != '') {
		$jaCmt('#err_guestEmail').html('');
		changeClassName('guestEmail', 'ja-error', '');
	}
	if ($jaCmt('#err_textCaptcha').length && trim($jaCmt('#err_textCaptcha').html()) != '') {
		$jaCmt('#err_textCaptcha').html('');
		changeClassName('textCaptcha', 'ja-error', '');
	}
	
	var url = location.href.split('#')[0];
	location.href=url+"#jacommentid:"+ID;
	
	//setHrefInPage(ID);
	JACommentConfig.hdCurrentComment = ID;	
    
    $jaCmt("#newcomment").focus();
    
    jac_auto_expand_textarea();
	
	if (parseInt(JACommentConfig.isEnableCharacterCounter)) {
		$jacJQuery('.form-character-count').html('');
		$jacJQuery('.form-character-count').popover({placement:'top'});
		
		$jaCmt('#newcomment').counter({
			count: 'up', 
			goal: JACommentConfig.maxLengthComment,
			msg: '&nbsp;/&nbsp;' + JACommentConfig.maxLengthComment
		});
		$jaCmt('#newcomment_counter').appendTo('.form-character-count');
	}
	
	if (parseInt(JACommentConfig.isEnableLocationDetection)) {
		JALocation.initAutocomplete();
	}
}

/**
 * Set href in page
 * 
 * @param integer ID Comment id
 * 
 * @return void
 */
function setHrefInPage(ID){	
	var link = document.location.href;
	var lastIndex = link.lastIndexOf("#");
	if(lastIndex == -1){
		link = link + "#jacommentid:"+ID;
	}else{
		link = link.substring(0, lastIndex);
		link = link + "#jacommentid:"+ID;		
		//remove old link
	}
	window.location = link;	
}

/**
 * Restore add new form
 * 
 * @return void
 */
function restoreAddnewForm(){
	currentID = JACommentConfig.hdCurrentComment; 	
	JACommentConfig.hdCurrentComment = 0;
	if(currentID != 0){
		if($jaCmt("#jac-a-quote-" + currentID).css('display') == "none"){
			;
		}
		
		if($jaCmt("#jac-a-reply-" + currentID).css('display') == "none"){
			;
		}
	}
}

/**
 * Cancel when edit or reply a comment
 * 
 * @param string  action Action when cancel
 * @param integer ID 	 Comment id
 * @param string  reply  Reply or quote
 * @param array	  post	 Post data
 * 
 * @return void
 */
function cancelComment(action, ID,  reply, post){					
	currentID = JACommentConfig.hdCurrentComment;	
	if(currentID != 0){		
		//undo display when user is editting
		if($jaCmt("#jac-edit-comment-" + currentID).html() != ""){
			$jaCmt("#jac-edit-comment-" + currentID).html('');
			$jaCmt('#jac-edit-comment-'+currentID).css('display', 'none');
			$jaCmt('#jac-content-of-comment-'+currentID).css('display', "block");
			$jaCmt('#jac-div-footer-'+currentID).css('display', 'block');
			if($jaCmt('#jac-attach-file-'+currentID).length)
				$jaCmt('#jac-attach-file-'+currentID).css('display', 'block');
			JACommentConfig.hdCurrentComment = 0;
		}		
		
		if($jaCmt("#reply-"+currentID).length){
			//undo display when user is replying
			if($jaCmt("#reply-"+currentID).attr('disabled') == true){
				$jaCmt("#reply-"+currentID).val(reply);
				$jaCmt("#reply-"+currentID).attr('disabled', false);
				$jaCmt("#quote-"+currentID).attr('disabled', false);
			}else if($jaCmt("#reply-"+currentID).html().toUpperCase() == JACommentConfig.textPosting.toUpperCase()){
				$jaCmt("#reply-"+currentID).html(JACommentConfig.textReply);
				if($jaCmt("#jac-a-quote-"+currentID).css('display') == "none"){					
					$jaCmt("#jac-a-quote-"+currentID).css('display', 'block');						
				}
				if($jaCmt("#jac-change-type-" +currentID).length && $jaCmt("#jac-change-type-" +currentID).css('display') == "none"){
					$jaCmt("#jac-change-type-" +currentID).css('display', 'block');
				}
			}
		}
		//undo display when user is quoting
		if($jaCmt("#quote-"+currentID).length){
			if($jaCmt("#quote-"+currentID).attr('disabled') == true){
				$jaCmt("#quote-"+currentID).val(reply);
				$jaCmt("#quote-"+currentID).attr('disabled', false);
				$jaCmt("#reply-"+currentID).attr('disabled', false);
			}else if($jaCmt("#quote-"+currentID).html().toUpperCase() == JACommentConfig.textQuoting.toUpperCase()){
				$jaCmt("#quote-"+currentID).html(JACommentConfig.textQuote);
				if($jaCmt("#jac-a-reply-"+currentID).css('display') == "none"){					
					$jaCmt("#jac-a-reply-"+currentID).css('display', 'block');						
				}
				if($jaCmt("#jac-change-type-" +currentID).length && $jaCmt("#jac-change-type-" +currentID).css('display') == "none"){
					$jaCmt("#jac-change-type-" +currentID).css('display', 'block');
				}
				$jaCmt("#newcomment").val('');
			}
		}
	}
			
	if(action == "edit" && action == 'quote'){
		$jaCmt('#jac-div-footer-'+ID).css('display', 'none');
		$jaCmt('#jac-content-of-comment-'+ID).css('display', 'none');
		
		if($jaCmt('#jac-attach-file-'+ID).length) {
			$jaCmt('#jac-attach-file-'+ID).css('display', 'none');
		}
		
		if(currentID != 0){			
			if($jaCmt("#jac-wrapper-form-add-new").length && $jaCmt("#jac-wrapper-form-add-new").html() == ""){
				var strNewComment = $jaCmt("#newcomment").val();
				if($jaCmt('#guestName').length){strGuestName = $jaCmt("#guestName").val();}
				if($jaCmt('#guestEmail').length){strGuestEmail = $jaCmt("#guestEmail").val();}
				if($jaCmt('#guestWebsite').length){strGuestWebsite = $jaCmt("#guestWebsite").val();}
				if($jaCmt('#chkTermsAddnew').length){isChkTermsAddnew = $jaCmt("#chkTermsAddnew").attr('checked');}
				
				$jaCmt("#jac-wrapper-form-add-new").html($jaCmt("#jac-result-reply-comment-" + currentID).html());
				$jaCmt("#jac-result-reply-comment-" + currentID).html('');
				$jaCmt("#jac-result-reply-comment-" + currentID).css('display', 'none');
				
				$jaCmt("#newcomment").val(strNewComment);
				if($jaCmt('#guestName').length){$jaCmt("#guestName").val(strGuestName);}
				if($jaCmt('#guestEmail').length){$jaCmt("#guestEmail").val(strGuestEmail);}
				if($jaCmt('#guestWebsite').length){$jaCmt("#guestWebsite").val(strGuestWebsite);}
				if($jaCmt('#chkTermsAddnew').length){$jaCmt("#chkTermsAddnew").attr('checked',isChkTermsAddnew);}
				jac_auto_expand_textarea();
			}
		}
		if ($jaCmt("#jac-wrapper-form-add-new")) {
			$jaCmt("#jac-wrapper-form-add-new").css('display', 'none');
		}
		$jaCmt("#jac-edit-comment-"+ID).css('display', 'block');
	}
	
	if(action == "reply"){			
		if($jaCmt("#jac-change-type-" +ID).length){
			$jaCmt("#jac-change-type-" +ID).css('display', 'none');
		}	
		if($jaCmt("#reply-"+ID).val().length){
			$jaCmt("#reply-"+ID).val(post);
			$jaCmt("#reply-"+ID).attr('disabled', 'true');
		}else{
			if($jaCmt("#reply-"+ID).html() != undefined){
				$jaCmt("#reply-"+ID).html(post);
			}
			if($jaCmt("#quote-"+ID).val() == undefined){
				if($jaCmt("#quote-"+ID).html() != undefined){
					$jaCmt("#jac-a-quote-"+ID).css('display', 'none');
				}
			}
		}
	}	
	
	if(action == "quote"){
		if($jaCmt("#jac-change-type-" +ID).length){
			$jaCmt("#jac-change-type-" +ID).css('display', 'none');
		}
		if($jaCmt("#quote-"+ID).val() != undefined){
			$jaCmt("#quote-"+ID).val(post);
			$jaCmt("#quote-"+ID).attr('disabled', 'true');
		}else{
			if($jaCmt("#quote-"+ID).html() != undefined){
				$jaCmt("#quote-"+ID).html(post);
			}
			
			if($jaCmt("#reply-"+ID).val() == undefined){
				if($jaCmt("#reply-"+ID).html() != undefined){
					$jaCmt("#jac-a-reply-"+ID).css('display', 'none');
				}
			}										
		}		
	}
	
	if(action == "delete"){
		if(currentID != 0){
			action = "cancelReply";
		}		
		if ($jaCmt("#jac-wrapper-form-add-new")) {
			$jaCmt("#jac-wrapper-form-add-new").css('display', 'block');
		}
		return;				
	}
	
	if(action == "completeReply"){		
		if($jaCmt('#err_myfile').length && $jaCmt('#err_myfile').html() != "") {
			$jaCmt('#err_myfile').html('');
		}

		if($jaCmt("#childen-comment-of-" + currentID).length){
			$jaCmt("#childen-comment-of-" + currentID).css('display', 'block');
		}
		
		if(currentID != 0){			
			strNewComment = $jaCmt("#newcomment").val();
		}
		if($jaCmt("#jac_cancel_comment_link").length) {
			$jaCmt("#jac_cancel_comment_link").css('display', 'none');
		}
		
		if($jaCmt("#btlCancelComment").length) {
			$jaCmt("#btlCancelComment").css('display', 'none');
		}
		
		if($jaCmt("#quote-"+currentID).length &&  $jaCmt("#quote-"+currentID).val() == undefined){
			if($jaCmt("#quote-"+currentID).html() != undefined){
				if($jaCmt("#quote-"+currentID).html(JACommentConfig.textQuoting)){
					$jaCmt("#quote-"+currentID).html(JACommentConfig.textQuote);
					$jaCmt("#newcomment").val('');
				}
				if($jaCmt("#jac-a-quote-"+currentID).css('display') == "none"){					
					$jaCmt("#jac-a-quote-"+currentID).css('display', 'block');						
				}
			}
		}
		
		if($jaCmt("#reply-"+currentID).length && $jaCmt("#reply-"+currentID).val() == undefined){
			if($jaCmt("#reply-"+currentID).html() != undefined){
				if($jaCmt("#jac-a-reply-"+currentID).length && $jaCmt("#jac-a-reply-"+currentID).css('display') == "none"){
					$jaCmt("#jac-a-reply-"+currentID).css('display', 'block');
				}
			}
		}
		
		$jaCmt("#jac-wrapper-form-add-new").html($jaCmt("#jac-result-reply-comment-" + currentID).html());
		$jaCmt("#jac-result-reply-comment-" + currentID).html('');
		$jaCmt("#jac-result-reply-comment-" + currentID).css('display', 'none');
		
		//textCounter('newcomment', 'jaCountText');
		
		jac_auto_expand_textarea();
		
		JACommentConfig.hdCurrentComment = 0;
	}
	
	if(action == "cancelReply"){		
		if($jaCmt("#newcomment").length && $jaCmt("#newcomment").html() != undefined && $jaCmt("#newcomment").val() == undefined && $jaCmt("#checkLink").length){
			jacRestoreTextArea();
		}		
		if(JACommentConfig.hdCurrentComment == 0){
			$jaCmt("#jac-wrapper-form-add-new").css('display', 'block');
		}
		if(currentID != 0 && JACommentConfig.hdCurrentComment != 0){						
			strNewComment = $jaCmt("#newcomment").val();
			if($jaCmt('#guestName').length){strGuestName = $jaCmt("#guestName").val();}
			if($jaCmt('#guestEmail').length){strGuestEmail = $jaCmt("#guestEmail").val();}
			if($jaCmt('#guestWebsite').length){strGuestWebsite = $jaCmt("#guestWebsite").val();}
			if($jaCmt('#chkTermsAddnew').length){isChkTermsAddnew = $jaCmt("#chkTermsAddnew").attr('checked');}
			
			$jaCmt("#jac-wrapper-form-add-new").html($jaCmt("#jac-result-reply-comment-" + currentID).html());
			$jaCmt("#jac-result-reply-comment-" + currentID).html('');
			$jaCmt("#jac-result-reply-comment-" + currentID).css('display', 'none');			
			$jaCmt("#newcomment").val(strNewComment);
			if($jaCmt('#guestName').length){$jaCmt("#guestName").val(strGuestName);}
			if($jaCmt('#guestEmail').length){$jaCmt("#guestEmail").val(strGuestEmail);}
			if($jaCmt('#guestWebsite').length){$jaCmt("#guestWebsite").val(strGuestWebsite);}
			if($jaCmt('#chkTermsAddnew').length){$jaCmt("#chkTermsAddnew").attr('checked', isChkTermsAddnew);}
			
			jac_auto_expand_textarea();
			
			if($jaCmt("#quote-"+currentID).length &&  $jaCmt("#quote-"+currentID).val() == undefined){
				if($jaCmt("#quote-"+currentID).html() != undefined){
					if($jaCmt("#quote-"+currentID).html(JACommentConfig.textQuoting)){
						$jaCmt("#quote-"+currentID).html(JACommentConfig.textQuote);
						$jaCmt("#newcomment").val('');
					}
					if($jaCmt("#jac-a-quote-"+currentID).css('display') == "none"){					
						$jaCmt("#jac-a-quote-"+currentID).css('display', 'block');						
					}
				}
			}
			
			if($jaCmt("#reply-"+currentID).length && $jaCmt("#reply-"+currentID).val() == undefined){
				if($jaCmt("#reply-"+currentID).html() != undefined){
					if($jaCmt("#jac-a-reply-"+currentID).length && $jaCmt("#jac-a-reply-"+currentID).css('display') == "none"){
						$jaCmt("#jac-a-reply-"+currentID).css('display', 'block');
						$jaCmt("#reply-"+currentID).html(JACommentConfig.textPosting);
					}
				}
			}
			
		}
										
		if($jaCmt("#jac_cancel_comment_link").length) {
			$jaCmt("#jac_cancel_comment_link").css('display', 'none');
		}
		
		if($jaCmt("#btlCancelComment").length){
			$jaCmt("#btlCancelComment").css('display', 'none');
		}				
		
		jacLoadNewCaptcha(0);
		
		JACommentConfig.hdCurrentComment = 0;
	}    	
	
	// Clear all error messages
	if ($jaCmt('#err_newcomment').length && trim($jaCmt('#err_newcomment').html()) != '') {
		$jaCmt('#err_newcomment').html('');
		changeClassName('newcomment', 'ja-error', '');
	}
	if ($jaCmt('#err_guestName').length && trim($jaCmt('#err_guestName').html()) != '') {
		$jaCmt('#err_guestName').html('');
		changeClassName('guestName', 'ja-error', '');
	}
	if ($jaCmt('#err_guestEmail').length && trim($jaCmt('#err_guestEmail').html()) != '') {
		$jaCmt('#err_guestEmail').html('');
		changeClassName('guestEmail', 'ja-error', '');
	}
	if ($jaCmt('#err_textCaptcha').length && trim($jaCmt('#err_textCaptcha').html()) != '') {
		$jaCmt('#err_textCaptcha').html('');
		changeClassName('textCaptcha', 'ja-error', '');
	}
	
	if (parseInt(JACommentConfig.isEnableCharacterCounter)) {
		$jacJQuery('.form-character-count').html('');
		$jacJQuery('.form-character-count').popover({placement:'top'});
		
		$jaCmt('#newcomment').counter({
			count: 'up', 
			goal: JACommentConfig.maxLengthComment,
			msg: '&nbsp;/&nbsp;' + JACommentConfig.maxLengthComment
		});
		$jaCmt('#newcomment_counter').appendTo('.form-character-count');
	}
	
	if (parseInt(JACommentConfig.isEnableLocationDetection)) {
		JALocation.initAutocomplete();
	}
}

/**
 * Enable add new comment button
 * 
 * @param string id Button id
 * 
 * @return void
 */
function enableAddNewComment(id){	
	if(id == "btlAddNewComment"){
		if($jaCmt("#btlAddNewComment").length)
			$jaCmt("#btlAddNewComment").attr('disabled', false);
		else{
			$jaCmt("#jac_post_new_comment").css('display', 'block');
			$jaCmt("#jac_span_post_new_comment").css('display', 'none');
		}
	}else if(id == "btlEditComment"){
		if($jaCmt("#btlEditComment").length)
			$jaCmt("#btlEditComment").attr('disabled', false);
		else{
			$jaCmt("#jac_edit_comment").css('display', 'block');
			$jaCmt("#jac_span_edit_comment").css('display', 'none');
		}
	}
	else{
		$jaCmt('#'+id).attr('disabled', false);
	}
}

/**
 * Display loading span
 * 
 * @return void
 */
function jac_displayLoadingSpan() {	
	jQuery(document).ready( function($) {
		var id='#'+jac_header;
		$(id).css('z-index','1');		
		$('#jac-loader').show();
	});		
}

/**
 * Disable reply button
 * 
 * @return void
 */
function disableReplyButton(){
	jQuery(document).ready(function($) {											
		 var buttonReply = jQuery("input[name='jac-button-Reply']");
		 $jaCmt("#reply-10").attr("style", "display='block'");
		 jQuery.each(buttonReply, function(i, item) {	
			 //item.css("display", "none");			
		 });		 
	});		
}

/**
 * Change background of an element
 * 
 * @param integer ID Comment id
 * 
 * @return void
 */
function attachFile(ID){		
	var str=$jaCmt('#userfile').val();
	var ext=str.substring(str.length,str.length-3);
	if ( ext == "exe" || ext == "php") {
		alert("File is invalid");
		return false;
	} else {
        if(!ID){
            var url = "index.php?" + $jaCmt("#uploadForm").serialize();
        }else{
            var url = "index.php?" + $jaCmt("#uploadForm"+ID).serialize();
        }
        jacomment_ajax_load(url);
    }
	return false;
}

// ++ Edited by NhatNX for IPhone
/**
 * Open login form
 * 
 * @param string  title Title of window
 * @param boolean isIP	Form is loaded from iPhone or not
 * 
 * @return void
 */
function open_login(title, isIP){
	if (isIP == undefined || isIP == '') {
		if(title)
			jacCreatForm('login&view=users&layout=login&createlink=1',0,650,400,0,0,title,1,'');
		else
			jacCreatForm('login&view=users&layout=login&createlink=1',0,650,400,0,0,JACommentConfig.strLogin,1,'');
	}
	else {
		if (title)
			jacCreatForm('login&view=users&layout=login&createlink=1',0,'91%',260,0,0,title,1,'');
		else
			jacCreatForm('login&view=users&layout=login&createlink=1',0,'91%',260,0,0,JACommentConfig.strLogin,1,'');
	}
}
// -- Edited by NhatNX for IPhone

/**
 * Complete add new comment
 * 
 * @return void
 */
function completeAddNew(){
	if($jaCmt("#newcomment").val() != undefined){
		$jaCmt("#newcomment").val('');
	}
	
	if (jQuery("comment-location").val() != undefined && jQuery("comment-location").val() != '') {
		jQuery("comment-location").val('');
	}
	
	if (jQuery("newcomment_count").html() != undefined  && jQuery("newcomment_count").html() != '0') {
		jQuery("newcomment_count").html('0');
	}
	
	if($jaCmt("#guestName").length){
		if($jaCmt("#guestName").attr('class').indexOf("jac-inner-text") != -1){
			$jaCmt("#guestName").val($jaCmt("#jac_hid_text_name").val());
		}else{			
			$jaCmt("#guestName").val('');
		}
	}
	
	if($jaCmt("#guestEmail").length){
		if($jaCmt("#guestEmail").attr('class').indexOf("jac-inner-text") != -1){
			$jaCmt("#guestEmail").val($jaCmt("#jac_hid_text_email").val());
		}else{			
			$jaCmt("#guestEmail").val('');
		}
	}
	
	if($jaCmt("#guestWebsite").length){
		if($jaCmt("#guestWebsite").attr('class').indexOf("jac-inner-text") != -1){
			$jaCmt("#guestWebsite").val($jaCmt("#jac_hid_text_website").val());
		}else{			
			$jaCmt("#guestWebsite").val("http://");
		}
	}	
	
	if($jaCmt('#chkTermsAddnew').length){
		$jaCmt('#chkTermsAddnew').attr('checked', false);
	}
	
	if($jaCmt('#err_myfile').length && $jaCmt('#err_myfile').html() != ""){
		$jaCmt('#err_myfile').html('');
	}
	
	if($jaCmt('#jac_image_captcha').length){
		jacLoadNewCaptcha();		
		if($jaCmt("#textCaptcha").attr('class').indexOf("jac-inner-text") !== -1){
			$jaCmt("#textCaptcha").val($jaCmt("#jac_hid_text_captcha").val());
		}else{			
			$jaCmt("#textCaptcha").val('');
		}
	}
	
	if($jaCmt("#myfile").length){
		$jaCmt("#myfile").attr('disabled', false);
	}	
	jac_auto_expand_textarea();
	//textCounter('newcomment', 'jaCountText');
}

/**
 * Moving background of an element
 *
 * @param integer id 	  Comment id 
 * @param string  rooturl Root URL
 * 
 * @return void
 */
function moveBackground(id, rooturl){		
	if(id == 0 || $jaCmt('#jac-row-comment-'+id) == undefined) return;
	var url = location.href.split('#')[0];
	location.href=url+"#jacommentid:"+id;
	
	$jaCmt("#jac-content-of-comment-"+id).addClass("just-reply");
	$jaCmt("#jac-content-of-comment-"+id).addClass("jac-move-back");
	
	var heightOfComment = $jaCmt("#jac-row-comment-"+id).offsetHeight;
	setTimeout("fadeBackGround('"+id+"', '" + heightOfComment  + "');", 2000);
}

/**
 * Fade background
 * 
 * @param integer id 			  Comment id
 * @param integer heightOfComment Height of comment
 * 
 * @return void
 */
function fadeBackGround(id, heightOfComment){
	var count = 0;
	
	for(i = (500 - heightOfComment); i<=500; i++ ){
		//moving back ground
		setTimeout("movingBack('"+id+"', '"+ -i +"');", count * 12);
		count++;
		//remove back ground
		if(i == 500){
			setTimeout("removeFace('"+id+"');", ((count*12)+250));
		}
	}	
}

/**
 * Move background up
 * 
 * @param integer id Comment id
 * @param integer i  Top position
 * 
 * @return void
 */
function movingBack(id, i){
	if ($jaCmt("#jac-content-of-comment-"+id))
		$jaCmt("#jac-content-of-comment-"+id).css('backgroundPosition', "0 " + i + "px");
}

/**
 * Remove style
 * 
 * @param integer id Comment id
 * 
 * @return void
 */
function removeFace(id){
	jQuery(document).ready(function($) {
		if($jaCmt("#jac-content-of-comment-"+id).length){
			$("#jac-content-of-comment-"+id).removeAttr("style");			    				    				    		
			$("#jac-content-of-comment-"+id).removeClass("jac-move-back");
		}	    		
	});	
}

/**
 * Load new captcha
 * 
 * @param string action Action when load new captcha
 * 
 * @return void
 */
function actionjacLoadNewCaptcha(action){
	//show image load new
	if(action){
		$jaCmt('#jac-refresh-image').css('display', 'block');
	}
	//dis able image load new
	else{
		$jaCmt('#jac-refresh-image').css('display', 'none');
	}
}

/**
 * Check URL is correct or not
 * 
 * @param string str URL string
 * 
 * @return boolean True if URL is correct, otherwise false
 */
function urlCheck(str) { 
	var tomatch= /^(https?|ftp):\/\/.*$/i;	
	if (tomatch.test(str)){               
		return true;
    }
	return false;
}

/**
 * Check word length
 * 
 * @param string text 		 Text to check
 * @param string action 	 Element id
 * @param string countTextID Textbox id for counting
 * 
 * @return void
 */
function checkWordLength(text, action, countTextID){				
	//changeClassName("newcomment", "jac-error", "");	
	var str1 			= "";
	var str2 			= "";
	var tmp  			= 0;
	var checkTag 		= 0;
	//[B]123[B]
	for(i = 0; i< text.length; i++){								
		if(text.charAt(i) != " " && text.charAt(i) != "\n" && text.charAt(i) != "[" && text.charAt(i) != "]" && text.charAt(i) != "="){												
			if(str1.length <100){				
				str1 += text.charAt(i);								
			}else{											
				//check str1 is link
				if(!urlCheck(str1)){
					str2 += str1 + " ";					
					tmp = 1;
					str1 = text.charAt(i);						
				}else{
					str1 += text.charAt(i);
				}				
			}			
		}else{						
			str2 += str1 + text.charAt(i);			
			str1 = "";				
		}			
	}	
	str2 += str1;
	if(tmp == 1){
		$jaCmt('#'+action).val(str2);
		alert(JACommentConfig.hidInputWordInComment);
	}
}

/**
 * Load new captcha
 * 
 * @return void
 */
function jacLoadNewCaptcha(){	            				
	if($jaCmt("#jac_image_captcha").length){
        var url = jacGetBaseUrl();
		url += "task=displaycaptchaaddnew&view=comments&tmpl=component&ran=" + Math.random()
		$jaCmt("#jac_image_captcha").attr('src', url);
		
	}
}

/**
 * Remove style of div
 * 
 * @param string id Div id
 * 
 * @return void
 */
function removeAttrOfDiv(id){
	jQuery(id).removeAttr("style");
}

/**
 * Set height of div
 * 
 * @param string id Div id
 * 
 * @return void
 */
function setHeight(id){
	jQuery(id).attr("style", "height:auto;");	
}

/**
 * Change type of comment
 * 
 * @param integer type 		   Type of comment
 * @param integer itemID 	   Comment id
 * @param integer currentType  Current type
 * @param integer currentTabID Current tab id
 * 
 * @return void
 */
function changeTypeOfComment(type, itemID, currentType , currentTabID){
	jac_displayLoadingSpan();
	var url = jacGetBaseUrl();
	url += "task=changeType&type="+ type +"&id="+ itemID +"&tmpl=component&currenttype="+currentType;
	jacomment_ajax_load(url);		
}

/**
 * Change class name of an element
 * 
 * @param string divID 		 Div id
 * @param string removeClass Remove class
 * @param string addClass 	 Added class
 * 
 * @return void
 */
function changeClassName(divID, removeClass, addClass){
    if($jaCmt('#'+divID).length){
        $jaCmt('#'+divID).removeClass(removeClass);
        $jaCmt('#'+divID).addClass(addClass);
    }
}

/**
 * Open attached file
 * 
 * @param integer id Comment id
 * 
 * @return void
 */
function openAttachFile(id){
	if(id !=0 || id != ""){
		//edit
		if($jaCmt("#jac-form-uploadedit").css('display') == "none") {
			$jaCmt("#jac-form-uploadedit").css('display', 'block');
		} else {
			$jaCmt("#jac-form-uploadedit").css('display', 'none');
		}
	} else{
		//add new
		if($jaCmt("#jac-form-upload").css('display') == "none") {
			$jaCmt("#jac-form-upload").css('display', 'block');
		} else {
			$jaCmt("#jac-form-upload").css('display', 'none');
		}
	}
}

/**
 * Update total children
 * 
 * @param string id Comment id
 * 
 * @return void
 */
function updateTotalChild(id, txtReplies){
	var parentID = $jaCmt("#jac-parent-of-comment-" + id).val();
	
	if ($jaCmt("#jac-show-text-childen-" + id).length && $jaCmt("#jac-hide-text-childen-" + id).length) {
		if (parseInt($jaCmt("#jac-show-total-childen-" + id).html(''), 10) > 1) {
			$jaCmt("#jac-show-text-childen-" + id).html(txtReplies);
		}
		else {
			$jaCmt("#jac-show-text-childen-" + id).html(JACommentConfig.textReply);
		}
		
		if (parseInt($jaCmt("#jac-hide-total-childen-" + id).html(''), 10) > 1) {
			$jaCmt("#jac-hide-text-childen-" + id).html(txtReplies);
		}
		else {
			$jaCmt("#jac-hide-text-childen-" + id).html(JACommentConfig.textReply);
		}
	}
	
	if(parentID != 0){
		if($jaCmt("#jac-show-total-childen-" + parentID).length){
			$jaCmt("#jac-show-total-childen-" + parentID).html(parseInt($jaCmt("#jac-show-total-childen-" + parentID).html(), 10) + 1);
		}
		if($jaCmt("#jac-hide-total-childen-" + parentID).length) {
			$jaCmt("#jac-hide-total-childen-" + parentID).html(parseInt($jaCmt("#jac-hide-total-childen-" + parentID).html(), 10) + 1);
		}
		
		updateTotalChild(parentID, txtReplies);
	}
}

/**
 * Change display
 * 
 * @param integer id 	   Comment id
 * @param string  action   Action when change display
 * @param boolean isSmiley Display is smiley or not
 * 
 * @return void
 */
function jacChangeDisplay(id, action, isSmiley){	
	if($jaCmt('#'+id)){
		$jaCmt('#'+id).css('display', action);
	}	
	//if click on smiley - save cursor in texarea
	if(isSmiley){
		if(id == "jacSmileys-"){
			if(jQuery("#newcomment")[0].selectionStart == undefined){
				//jac_textarea_cursor = jQuery("#newcomment")[0].selectionStart;
				jQuery("#newcomment")[0].focus ();
				var range = document.selection.createRange();
				var stored_range = range.duplicate ();
				stored_range.moveToElementText (el);
				stored_range.setEndPoint ('EndToEnd', range);
				jac_textarea_cursor = stored_range.text.length - range.text.length;
				
			}else{
				jac_textarea_cursor = jQuery("#newcomment")[0].selectionStart;
			}			
		}else{
			if(jQuery("#newcommentedit")[0].selectionStart == undefined){
				//jac_textarea_cursor = jQuery("#newcomment")[0].selectionStart;
				jQuery("#newcommentedit")[0].focus ();
				var range = document.selection.createRange();
				var stored_range = range.duplicate ();
				stored_range.moveToElementText (el);
				stored_range.setEndPoint ('EndToEnd', range);
				jac_textarea_cursor = stored_range.text.length - range.text.length;
				
			}else{
				jac_textarea_cursor = jQuery("#newcommentedit")[0].selectionStart;
			}
		}					
	}
}

/**
 * Parse link
 * 
 * @return void
 */
function parseLink(){
	var url = window.location.href;
	var c_url = url.split('#');
	var id = 0;
	var tmp = 0;
	if(c_url.length >= 1){		
		for(i=1; i< c_url.length; i++){			
			if(c_url[i].indexOf("jacommentid:") >-1){				
				tmp = c_url[i].split('-')[1];				
				if(tmp != ""){
					id = parseInt(tmp, 10);
				}
			}
		}
	}
	
	document.cookie = 'JACurrentComment=' + id;
}

/**
 * Select range
 * 
 * @param string textAreaID Textarea id
 * @param integer start 	Start position
 * @param integer end 		End position
 * 
 * @return void
 */
function selectRange(textAreaID,start,end){
	jQuery(document).ready(function($) {
		$(textAreaID).focus();
		alert(Browser);
		if(Browser.Engine.trident){
			var range=this.createTextRange();
			range.collapse(true);
			range.moveStart('character', start);
			range.moveEnd('character', end-start);
			range.select();
			return this;
		}
		this.setSelectionRange(start, end);
	});
}

/**
 * Insert text into textarea
 * 
 * @param string textAreaID Textarea id
 * @param string tag		Tag
 * 
 * @return void
 */
function insertIntoTextare(textAreaID, tag){
	(function($) {
		var text      	= $(textAreaID).val();
		var len   		= text.length;
		var start 		= $(textAreaID)[0].selectionStart;
		var end   		= $(textAreaID)[0].selectionEnd;
		var textSelect  = text.substring(start, end);
		var textAdded = text.substring(0, start) + tag + text.substring(end, text.length);
		$(textAreaID).val(textAdded);
	})($jaCmt);
}
//END -- BBJACODE

/**
 * Strip code
 * 
 * @param string  F 	  String to strip
 * @param boolean G 	  Strip images
 * @param boolean isQuote String is quote or not
 * 
 * @return string String after stripping
 */
function stripcode(F, G, isQuote){
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
 * @param string A String to trim
 * 
 * @return string String after trimming
 */
function trim(A){
    if(A == null) return '';
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
 * Display voted comments
 * 
 * @return string Voted comment list
 */
function displayVotedComments(option, contentid) {
	var url = jacGetBaseUrl();
	
	if (location.href.indexOf("com_jacomment") == -1) {
		url += "option=com_jacomment&";
	}
	
	url += "tmpl=component&view=comments&layout=showvotedlist&contentoption=" + option + "&contentid=" + contentid + "&ran=" + Math.random();
	
	jQuery.get(url, function(response){
		jQuery('#jac-container-voted-comment').html(response);
	});
}