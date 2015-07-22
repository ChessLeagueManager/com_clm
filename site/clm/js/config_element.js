var clm_config_element_data = new Array();
var clm_config_element_counter = new Array();

function  clm_config_element_endsWith(str, suffix) {
    return str.indexOf(suffix, str.length - suffix.length) !== -1;
}

function clm_config_element_data_remove(key, object) {
	while(object.getElementsByClassName("clm_view_config_button").length==0)
	{
		object = object.parentElement;
	}
	id = object.getElementsByClassName("clm_view_config_button")[0].getAttribute("id");
	
	if( clm_config_element_data[id] === undefined ) {
		return;
	}	
	
	for (var i=0; i<clm_config_element_data[id].length; i++) {
		if(clm_config_element_data[id][i][0]==key)
		{
			clm_config_element_data[id].splice(i,1);
			clm_config_element_counter[id]--;
			if(clm_config_element_counter[id]==0) {
					object.getElementsByClassName("clm_view_config_button")[0].disabled = true;
			}
			return;
		}
	}
}

function clm_config_element_email(object,email,key) { 
	if(email == "" || clm_isMail(email))
	{
		clm_config_element_data_change(key, email, object);
		object.style.backgroundColor="#ffffff";
	} else {
		clm_config_element_data_remove(key, object);
		object.style.backgroundColor="#F5BCA9";
	}
}

function clm_config_element_length(object,string,key) { 
	if(string=="0"){
		clm_config_element_data_change(key, string, object);
		object.style.backgroundColor="#ffffff";
		return;
	}
	units = new Array("em", "cm", "mm", "in", "pt", "pc", "%", "px", "ex");
	for(var i=0;i<units.length;i++)
	{
		if(clm_config_element_endsWith(string,units[i]))
		{
			split = string.split(units[i]);
			if(split.length==2 && clm_isNumber(split[0]))
			{
				clm_config_element_data_change(key, string, object);
				object.style.backgroundColor="#ffffff";
			} else { 
				clm_config_element_data_remove(key, object);
				object.style.backgroundColor="#F5BCA9";
			}
			return;
		}
	}
		clm_config_element_data_remove(key, object);
		object.style.backgroundColor="#F5BCA9";
}

function convert_string(str)
{
  dummy=document.createElement('textarea');
  dummy.innerHTML=str;
  return(dummy.value);
}

function clm_config_element_data_change(key, value, object) {
	while(object.getElementsByClassName("clm_view_config_button").length==0)
	{
		object = object.parentElement;
	}
	id = object.getElementsByClassName("clm_view_config_button")[0].getAttribute("id");
	
	if( clm_config_element_data[id] === undefined ) {
		clm_config_element_counter[id]=0;
		clm_config_element_data[id] = new Array();
	}
	already=false;
	for (var i=0; i<clm_config_element_data[id].length; i++) {
		if(clm_config_element_data[id][i][0]==key)
		{
			clm_config_element_data[id][i][1]=value;
			already=true;
			break;
		}
	}
	if(!already) {
	clm_config_element_data[id][clm_config_element_counter[id]]=new Array(key,value);
	clm_config_element_counter[id]++;
	}
	object.getElementsByClassName("clm_view_config_button")[0].disabled = false;
}

function clm_config_element_save(object) { 
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
                	 object.parentElement.parentElement.getElementsByClassName("clm_view_notification")[0].innerHTML="<div class='error'>"+clm_config_element_save_error_JSON+"</div>";
			 object.disabled = false;               	 
                	 return;
                }      
                if (out.length != 1 || out[0].length != 3 || out[0][0] != true || out[0][2].length != clm_config_element_data[object.getAttribute("id")].length) {
                	 object.parentElement.parentElement.getElementsByClassName("clm_view_notification")[0].innerHTML="<div class='error'>"+clm_config_element_save_error_CONTENT+"</div>";
			 object.disabled = false;               	 
               		 return;
                }
                clm_config_element_counter[object.getAttribute("id")]=0;
					 clm_config_element_data[object.getAttribute("id")] = new Array();
					 object.parentElement.parentElement.getElementsByClassName("clm_view_notification")[0].innerHTML="<div class='success'>"+clm_config_element_save_success+"</div>";
            } else {
            	 object.parentElement.parentElement.getElementsByClassName("clm_view_notification")[0].innerHTML="<div class='error'>"+clm_config_element_save_error_HTTP+"</div>";
            	 object.disabled = false;
            }
        }
    }
    xmlhttp.open("POST", clm_config_element_url, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	    var command = new Array(2);
	    command[1] = new Array(2);
	    command[1][1] = new Array(1);
	    command[0] = 0;
	    command[1][0] = "db_config_save";
	    command[1][1][0] = clm_config_element_data[object.getAttribute("id")];
	    object.disabled = true;
	    object.parentElement.parentElement.getElementsByClassName("clm_view_notification")[0].innerHTML="<div class='notice'>"+clm_config_element_working+"</div>";
	    xmlhttp.send('command=' + JSON.stringify(command));
}

function clm_config_element_reset(object) { 
               	 
if(confirm(convert_string(clm_config_element_reset_request)) == false){
	return;
}

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
                	  object.parentElement.parentElement.getElementsByClassName("clm_view_notification")[0].innerHTML="<div class='error'>"+clm_config_element_save_error_JSON+"</div>";
			  object.disabled = false;               	 
                	  return;
                }      
                if (out.length != 1 || out[0].length != 2 || out[0][0] != true) {
                	object.parentElement.parentElement.getElementsByClassName("clm_view_notification")[0].innerHTML="<div class='error'>"+clm_config_element_save_error_CONTENT+"</div>";
			object.disabled = false;               	 
               	 	return;
                }
            	object.disabled = false;
                window.location.reload();
            } else {
            	 object.parentElement.parentElement.getElementsByClassName("clm_view_notification")[0].innerHTML="<div class='error'>"+clm_config_element_save_error_HTTP+"</div>";
            	 object.disabled = false;
            }
        }
    }
    xmlhttp.open("POST", clm_config_element_url, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	    var command = new Array(2);
	    command[1] = new Array(1);
	    command[0] = 0;
	    command[1][0] = "db_config_reset";
	    object.disabled = true;
	    object.parentElement.parentElement.getElementsByClassName("clm_view_notification")[0].innerHTML="<div class='notice'>"+clm_config_element_working+"</div>";
	    xmlhttp.send('command=' + JSON.stringify(command));
}
