/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
	
// author: Simon Willison (addLoadEvent)
function clm_report_addLoadEvent(func) {http://blog.simonwillison.net/
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
function clm_report_change_global() {
	boxes = document.getElementsByClassName("clm_view_report");
	for (i = 0; i < boxes.length; i++) {
		home = boxes[i].getElementsByClassName("home_select");
		for (p = 0; p < home.length; p++) {
			home[p].name=home[p].selectedIndex;
			clm_report_disable(0,home[p].selectedIndex,home[p].className,boxes[i]);
		}
		guest = boxes[i].getElementsByClassName("guest_select");
		for (p = 0; p < guest.length; p++) {
			guest[p].name=guest[p].selectedIndex;
			clm_report_disable(0,guest[p].selectedIndex,guest[p].className,boxes[i]);
		}
		clm_report_change_result(boxes[i],true);
	}
}
function clm_report_change_result(box,mother) {
	if(!mother){
		box = box.parentElement.parentElement.parentElement;
	}
	elements = box.getElementsByClassName("result_select");

	h = 0;
	g = 0;
	nothing = 0;
	for (i = 0; i < elements.length; i++) {
		r = elements[i].options[elements[i].selectedIndex].value;
		if(r == "1") {
			h = h + clm_p_sieg*1 + clm_p_antritt*1; 
			g = g + clm_p_antritt*1; 
		} else if (r == "5") {
			h = h + clm_p_sieg*1 + clm_p_antritt*1; 
		} else if (r == "0") {
			h = h + clm_p_antritt*1; 
			g = g + clm_p_sieg*1 + clm_p_antritt*1; 
		} else if (r == "4") {
			g = g + clm_p_sieg*1 + clm_p_antritt*1; 
		} else if (r == "2") {
			h = h + clm_p_remis*1 + clm_p_antritt*1; 
			g = g + clm_p_remis*1 + clm_p_antritt*1; 
		} else if (r == "9") {
			g = g + clm_p_remis*1 + clm_p_antritt*1; 
		} else if (r == "10") {
			h = h + clm_p_remis*1 + clm_p_antritt*1; 
		} else if (r == "-2") {
			nothing++;
		}
	}

	if(h==g && nothing == 0 && box.getElementsByClassName("ko").length > 0) {
		box.getElementsByClassName("ko")[0].style.display="block";
	} else if(box.getElementsByClassName("ko").length > 0) {
		box.getElementsByClassName("ko")[0].style.display="none";
	}

	box.getElementsByClassName("result_final")[0].innerHTML= h.toString() + " : " + g.toString();
	
	if(nothing==0) {
		clm_report_change_player(box,true);
	} else {
		clm_report_isReady(box,false);
	}
}

function clm_report_change_player(box,mother) {
	if(!mother){
		oldIndex=box.name;	
		newIndex=box.selectedIndex;
		siteClass=box.className;
		box.name=box.selectedIndex;
		box = box.parentElement.parentElement.parentElement;
		clm_report_disable(oldIndex,newIndex,siteClass,box);
	}
	home = box.getElementsByClassName("home_select");
	guest = box.getElementsByClassName("guest_select");
	result = box.getElementsByClassName("result_select");
		
	complete = true;
	for (i = 0; i < guest.length; i++) {
		r1 = home[i].options[home[i].selectedIndex].value;
		r2 = guest[i].options[guest[i].selectedIndex].value;
		r3 = result[i].options[result[i].selectedIndex].value;
		if(r1 == "-2" || r2 == "-2" || r3 == "-2") {
			complete=false;
			break;
		}
	}
	clm_report_isReady(box,complete)
}

function clm_report_isReady(box,complete) {
	if(complete) {
			box.getElementsByClassName("button_block")[0].disabled=false;
			box.getElementsByClassName("clm_view_notification")[0].getElementsByClassName("notice")[0].innerHTML="<span>"+clm_report_data_filled+"</span>";
	} else {
			box.getElementsByClassName("button_block")[0].disabled=true;
			box.getElementsByClassName("clm_view_notification")[0].getElementsByClassName("notice")[0].innerHTML="<span>"+clm_report_data_needed+"</span>";
	}
}

function clm_report_disable(oldIndex,newIndex,siteClass,box)
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
function clm_report_block(box) {
	box = box.parentElement.parentElement;
	if(box.getElementsByClassName("button_save")[0].disabled) {
		disable=true;
		box.getElementsByClassName("button_save")[0].disabled=false;
		box.getElementsByClassName("button_back")[0].disabled=true;
		box.getElementsByClassName("button_block")[0].innerHTML=clm_report_button_unblock;
		box.getElementsByClassName("clm_view_notification")[0].getElementsByClassName("notice")[0].innerHTML="<span>"+clm_report_data_ready+"</span>";
		//box.scrollIntoView(true);
	} else {
		disable=false;
		box.getElementsByClassName("button_save")[0].disabled=true;
		box.getElementsByClassName("button_back")[0].disabled=false;
		box.getElementsByClassName("button_block")[0].innerHTML=clm_report_button_block;
		box.getElementsByClassName("clm_view_notification")[0].getElementsByClassName("notice")[0].innerHTML="<span>"+clm_report_data_filled+"</span>";
	}
		
	home = box.getElementsByClassName("home_select");
	guest = box.getElementsByClassName("guest_select");
	result = box.getElementsByClassName("result_select");
	
	for (i = 0; i < guest.length; i++) {
		home[i].disabled=disable;
		guest[i].disabled=disable;
		result[i].disabled=disable;
	}
	if(box.getElementsByClassName("comment").length>0) {
		box.getElementsByClassName("comment")[0].disabled=disable;
	}
	if(box.getElementsByClassName("ko_decision").length>0) {
		box.getElementsByClassName("ko_decision")[0].disabled=disable;
	}
	if(box.getElementsByClassName("icomment").length>0) {
		box.getElementsByClassName("icomment")[0].disabled=disable;
	}
}
function clm_report_genData(box) {
	output = new Array(10);
	output[0] = box.getElementsByClassName("liga")[0].value;
	output[1] = box.getElementsByClassName("runde")[0].value;
	output[2] = box.getElementsByClassName("dg")[0].value;
	output[3] = box.getElementsByClassName("paar")[0].value;
	if(box.getElementsByClassName("comment").length>0) {
		output[4] = box.getElementsByClassName("comment")[0].value;
	}
	if(box.getElementsByClassName("ko_decision").length>0) {
		output[5] = box.getElementsByClassName("ko_decision")[0].options[box.getElementsByClassName("ko_decision")[0].selectedIndex].value;
	}
	home = box.getElementsByClassName("home_select");
	guest = box.getElementsByClassName("guest_select");
	result = box.getElementsByClassName("result_select");	
	
	output[6] = new Array(home.length);
	output[7] = new Array(result.length);
	output[8] = new Array(guest.length);
	for (i = 0; i < home.length; i++) {
		output[6][i] = home[i].options[home[i].selectedIndex].value;
		output[7][i] = guest[i].options[guest[i].selectedIndex].value;
		output[8][i] = result[i].options[result[i].selectedIndex].value;
	}
	if(box.getElementsByClassName("icomment").length>0) {
		output[9] = box.getElementsByClassName("icomment")[0].value;
	} else {
		output[9] = "";
	}
	return output;
}
function clm_report_save(box) {
		 box = box.parentElement.parentElement;
		 box.getElementsByClassName("button_save")[0].disabled=true;
		 box.getElementsByClassName("button_block")[0].disabled=true;

	    var xmlhttp;
	    if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
	        xmlhttp = new XMLHttpRequest();
	    } else { // code for IE6, IE5
	        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	    }
	    xmlhttp.onreadystatechange = function() {
	        if (xmlhttp.readyState == 4) {
	            if (xmlhttp.status == 200) {
	                try {
	                    var out = JSON.parse(xmlhttp.responseText);
	                } catch (e) {
							  clm_report_message(box,clm_report_result_error1,"error");
	                    return;
	                }
	                if (out.length != 1 || out[0].length != 2 || out[0][0] != true) {
	                	if(out.length == 1 && out[0].length == 2 && out[0][0] == false && out[0][1] == "e_reportLogin") {
	                		clm_report_message(box,clm_report_result_login,"error");
	                	} else {
	                		clm_report_message(box,clm_report_result_error2,"error");
	                	}
	                  return;
	                }
	                clm_report_message(box,clm_report_result_success,"success");
	            } else {
	                clm_report_message(box,clm_report_result_error0,"error");
	            }
	        }
	    }
	    xmlhttp.open("POST", clm_report_url, true);
	    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	    var command = new Array(2);
	    command[1] = new Array(2);
	    command[1][1] = new Array(2);
	    command[0] = 0;
	    command[1][0] = "db_report_save";
	    command[1][1] = clm_report_genData(box);
	    xmlhttp.send('command=' + JSON.stringify(command));
}
function clm_report_message(box,msg,stats) {
	do {
		childs = box.getElementsByClassName("flex");
		box.removeChild(childs[0]);
	} while (childs.length>0);
	if(box.getElementsByClassName("outer_comment").length>0) {
		box.removeChild(box.getElementsByClassName("outer_comment")[0]);
	}
	if(box.getElementsByClassName("outer_icomment").length>0) {
		box.removeChild(box.getElementsByClassName("outer_icomment")[0]);
	}
	if(box.getElementsByClassName("ko").length>0) {
		box.removeChild(box.getElementsByClassName("ko")[0]);
	}
	box.getElementsByClassName("button_save")[0].style.display="none";
	box.getElementsByClassName("button_back")[0].disabled=false;
	box.getElementsByClassName("button_block")[0].style.display="none";
	element = box.getElementsByClassName("clm_view_notification")[0];
	element.innerHTML = "<div class='"+stats+"'>"+msg+"</div>";
	element.className="clm_view_notification";
}
clm_report_addLoadEvent(clm_report_change_global);
