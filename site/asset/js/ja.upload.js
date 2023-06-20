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
 * Change background of an element
 * 
 * @param object obj An element
 * 
 * @return void
 */
function changeBackground(obj){
	//obj.parentNode.parentNode.className = obj.parentNode.parentNode.className + " " +'focused';
}

/**
 * Change background of node
 * 
 * @param object obj An node
 * 
 * @return void
 */
function changeBackgroundNone(obj){
	//obj.parentNode.parentNode.className = obj.parentNode.parentNode.className.replace("focused", "");
}

/**
 * Change background of an element
 * 
 * @param mixed   needle   Element to search
 * @param array   haystack Array of elements
 * @param boolean strict   Request data type is the same or not
 * 
 * @return boolean True if it is found, otherwise false
 */
function in_array(needle, haystack, strict) {
	for(var i = 0; i < haystack.length; i++) {
		if(strict) {
			if(haystack[i] === needle) {
				return true;
			}
		} else {
			if(haystack[i] == needle) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Check file type
 * 
 * @param string value  File name
 * @param string action Action process before checking file name
 * @param string id		Div id
 * 
 * @return boolean True if file type is correct, otherwise false
 */
function checkTypeFile(value, action, id){		
	var pos = value.lastIndexOf('.');
	var type = value.substr(pos+1, value.length).toLowerCase();
	if(!in_array(type, JACommentConfig.v_array_type, false)){			
		if(action == "admin"){			
			document.getElementById('err_myfile_reply').style.display = "block";			
		}else if(action == "edit"){				
			document.getElementById('err_myfileedit').innerHTML = "<span class='err' style='color:red;'>"+JACommentConfig.error_type_file+"<\/span>" +"<br />";
		}else{			
			document.getElementById('err_myfile').innerHTML = "<span class='err' style='color:red;'>"+JACommentConfig.error_type_file+"<\/span>" +"<br />";		
		}
		return false;
	}
	
	var fileName = value.substr(0, pos+1).toLowerCase();
	if(fileName.length > 100){
		if(action == "admin"){			
			document.getElementById('err_myfile_reply').style.display = "block";			
		}else if(action == "edit"){				
			document.getElementById('err_myfileedit').innerHTML = "<span class='err' style='color:red;'>"+JACommentConfig.error_name_file+"<\/span>" +"<br />";
		}else{			
			document.getElementById('err_myfile').innerHTML = "<span class='err' style='color:red;'>"+JACommentConfig.error_name_file+"<\/span>" +"<br />";		
		}
		return false;
	}
	return true;
}

/**
 * Check total uploaded files
 * 
 * @return void
 */
function checkTotalFile(){
	var listFiles =  $("result_upload").getElements('input[name^=listfile]');
	var currentTotal = 0;
	for(i = 0 ; i< listFiles.length; i++){
		if(listFiles[i].checked == true){
			currentTotal+=1;
		}
	}
	if(currentTotal < JACommentConfig.total_attach_file){
		document.getElementById('myfile').disabled = false;
		for(i = 0 ; i< listFiles.length; i++){
			if(listFiles[i].checked == false){
				listFiles[i].disabled = false;
			}
		}
	}else{
		document.getElementById('myfile').disabled = true;
		for(i = 0 ; i< listFiles.length; i++){
			if(listFiles[i].checked == false){
				listFiles[i].disabled = true;
			}
		}
	}
}

/**
 * Check total uploaded files when edit
 * 
 * @param string action Action when check uploaded files
 * 
 * @return void
 */
function checkTotalFileEdit(action){
	action = "edit";
	var listFiles =  $("result_upload" + action).getElements('input[name^=listfile]');
	var currentTotal = 0;
	for(i = 0 ; i< listFiles.length; i++){
		if(listFiles[i].checked == true){
			currentTotal+=1;
		}
	}
	if(currentTotal < JACommentConfig.total_attach_file){
		document.getElementById('myfile'+action).disabled = false;
		for(i = 0 ; i< listFiles.length; i++){
			if(listFiles[i].checked == false){
				listFiles[i].disabled = false;
			}
		}
	}else{
		document.getElementById('myfile'+action).disabled = true;
		for(i = 0 ; i< listFiles.length; i++){
			if(listFiles[i].checked == false){
				listFiles[i].disabled = true;
			}
		}
	}
}

/**
 * Upload file
 * 
 * @param string id Div id
 * 
 * @return void
 */
function startUpload(id){
	if(!checkTypeFile(document.form1.myfile.value)) return false;
	document.form1.setAttribute( "autocomplete","off" );
	document.form1.action = "index.php?tmpl=component&option=com_jacomment&view=comments&task=uploadFile";
	document.form1.target = "upload_target";
	document.getElementById('jac_upload_process').style.display='block';
	document.form1.submit();
	document.form1.reset();
}

/**
 * Upload file when edit
 * 
 * @param string id Div id
 * 
 * @return void
 */
function startEditUpload(id){
	if(!checkTypeFile(document.form1edit.myfileedit.value, "edit")) return false;
	document.form1edit.setAttribute( "autocomplete","off" );
	document.form1edit.action = "index.php?tmpl=component&option=com_jacomment&view=comments&task=uploadFileEdit&id="+id;
	document.form1edit.target = "upload_target";
	document.getElementById('jac_upload_processedit').style.display='block';
	document.form1edit.submit();
	document.form1edit.reset();
}

/**
 * Upload file in admin
 * 
 * @param string id Div id
 * 
 * @return void
 */
function startAdminUpload(id){	
	if(!checkTypeFile(document.formreply.myfile.value, "admin", id)) return false;
	document.formreply.setAttribute( "autocomplete","off" );
	document.formreply.action = "index.php?tmpl=component&option=com_jacomment&view=comments&task=uploadFileReply";
	document.formreply.target = "upload_target";
	document.getElementById('upload_process_1_reply').style.display='block';
	document.formreply.submit();
	document.formreply.reset();
}