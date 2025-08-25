/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
	
// author: Simon Willison (addLoadEvent)
function clm_mail_addLoadEvent(func) {http://blog.simonwillison.net/
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      if (oldonload) {
        oldonload();
      }
      func();
    }
  }
}
function clm_mail_change_global() {
	boxes = document.getElementsByClassName("clm_view_mail");
	for (i = 0; i < boxes.length; i++) {
		clm_mail_fill_fields(boxes[i]);
	}
}
function clm_mail_fill_fields(box,mother) {
	if(!mother){
		box = box.parentElement.parentElement.parentElement;
	}
	if ((box.getElementsByClassName("mail_subj")[0].value > '') && (box.getElementsByClassName("mail_body")[0].value > '')) {
			box.getElementsByClassName("button_save")[0].disabled=false;
			box.getElementsByClassName("clm_view_notification")[0].getElementsByClassName("notice")[0].innerHTML="<span>"+clm_mail_data_filled+"</span>";
	} else {
			box.getElementsByClassName("button_save")[0].disabled=true;
			box.getElementsByClassName("clm_view_notification")[0].getElementsByClassName("notice")[0].innerHTML="<span>"+clm_mail_data_needed+"</span>";
	}
	
}

function clm_mail_disable(oldIndex,newIndex,siteClass,box)
{
	selects = box.getElementsByClassName(siteClass);
	for (q = 0; q < selects.length; q++) {
		selects[q].options[oldIndex].disabled=false;
		if(newIndex!=0 && newIndex!=(selects[q].length-1) && selects[q].selectedIndex!=newIndex)
		{
			selects[q].options[newIndex].disabled=true;
		}
	}
}
function clm_mail_genData(box) {
	output = new Array(6);
	output[0] = box.getElementsByClassName("return_section")[0].value;
	output[1] = box.getElementsByClassName("return_view")[0].value;
	output[2] = box.getElementsByClassName("cids")[0].value;
	if(box.getElementsByClassName("mail_to").length>0) {
		output[3] = box.getElementsByClassName("mail_to")[0].value;
	} else {
		output[3] = "";
	}
	if(box.getElementsByClassName("mail_subj").length>0) {
		output[4] = box.getElementsByClassName("mail_subj")[0].value;
	} else {
		output[4] = "";
	}
	if(box.getElementsByClassName("mail_body").length>0) {
		output[5] = box.getElementsByClassName("mail_body")[0].value;
	} else {
		output[5] = "";
	}
	return output;
}
function clm_mail_save(box) {
		 box = box.parentElement.parentElement;
		 box.getElementsByClassName("button_save")[0].disabled=true;

	    var xmlhttp;
	    if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
	        xmlhttp = new XMLHttpRequest();
	    } else { // code for IE6, IE5
	        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	    }
	    var command = new Array(2);
	    command[1] = new Array(2);
	    command[1][1] = new Array(2);
	    command[0] = 0;
	    command[1][0] = "db_mail_save";
	    command[1][1] = clm_mail_genData(box);
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4) {
	            if (xmlhttp.status == 200) {
	                try {
 	                    var out = JSON.parse(xmlhttp.responseText);
	                } catch (e) {
//							clm_mail_message(box,clm_mail_result_error1,"error",xmlhttp);
							clm_mail_message(box,e,"error",xmlhttp);
	                    return;
	                }
	                if (out.length != 1 || out[0].length != 2 || out[0][0] != true) {
	                	if(out.length == 1 && out[0].length == 2 && out[0][0] == false && out[0][1] == "e_mailLogin") {
	                		clm_mail_message(box,clm_mail_result_login,"error",xmlhttp);
	                	} else {
	                		clm_mail_message(box,clm_mail_result_error2 + " <br/>JSON:<br/><pre>" + JSON.stringify(command) + "</pre><br/>","error",xmlhttp);
	                	}
	                  return;
	                }
	                clm_mail_message(box,clm_mail_result_success,"success","");
	            } else {
	                clm_mail_message(box,clm_mail_result_error0,"error",xmlhttp);
	            }
	        }
	    }
	    xmlhttp.open("POST", clm_mail_url, true);
	    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	    xmlhttp.send('command=' + JSON.stringify(command));
}
function clm_mail_message(box,msg,stats,request) {
	do {
		childs = box.getElementsByClassName("outer_mail_subj");
		box.removeChild(childs[0]);
	} while (childs.length>0);
	do {
		childs = box.getElementsByClassName("outer_mail_body");
		box.removeChild(childs[0]);
	} while (childs.length>0);
	box.getElementsByClassName("button_save")[0].style.display="none";
	box.getElementsByClassName("button_back")[0].disabled=false;
	element = box.getElementsByClassName("clm_view_notification")[0];
	element.innerHTML = "<div class='"+stats+"'>"+msg+"</div>";
	if (exists(request.responseText)) {
		element.innerHTML = "<div class='"+stats+"'>(clm_mail)<br/>"+msg+"<br/>responseText:<br/><pre>"+request.responseText+"</pre></div>";
	} else {
		element.innerHTML = "<div class='"+stats+"'>(clm_mail)<br/>"+msg+"<br />no responseText.</div>";
	}
	element.className="clm_view_notification";
}
clm_mail_addLoadEvent(clm_mail_change_global);
