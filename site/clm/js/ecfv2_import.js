/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_ecfv2_import_player(object, p = 0) {
    unit = object.parentElement.getElementsByClassName("clm_view_form_select_options")[0];  
    update = object.parentElement.parentElement.getElementsByClassName("clm_view_ecfv2_import_update")[0];
    update_buttons = object.parentElement.getElementsByTagName("button");
    update.innerHTML = clm_ecfv2_import_update+" "+unit.options[unit.selectedIndex].text.replace(/(.*-)/g, '')+"<br/>"+clm_ecfv2_import_loadingClubs;
	 clm_ecfv2_import_start(update, update_buttons);
	 
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
                    clm_ecfv2_import_end(update, update_buttons, clm_ecfv2_import_noJson+xmlhttp.responseText+'ende');
                    return;
                }
                if (out.length != 1 || out[0].length != 3 || out[0][0] != true) {
                    clm_ecfv2_import_end(update, update_buttons, clm_ecfv2_import_wrongResponse+xmlhttp.responseText+'ende');
                    return;
                }

		if(out[0][1]=="w_noAssociationFound") {
                    clm_ecfv2_import_end(update, update_buttons, clm_ecfv2_import_nothingToUpdate);
		    return;
		}

                clm_ecfv2_import_player_one(out[0][2], update, update_buttons, unit, p, 0, 0, 0)
            } else {
                clm_ecfv2_import_end(update, update_buttons, clm_ecfv2_import_errorHttp + " " + xmlhttp.status);
            }
        }
    }
    xmlhttp.open("POST", clm_ecfv2_import_url, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send('command=[0,["db_ecfv2_clubs",["' + unit.value + '"]]]');
}

function clm_ecfv2_import_club(object) {
    unit = object.parentElement.getElementsByClassName("clm_view_form_select_options")[0];  
    update = object.parentElement.parentElement.getElementsByClassName("clm_view_ecfv2_import_update")[0];
    update_buttons = object.parentElement.getElementsByTagName("button");
    update.innerHTML = clm_ecfv2_import_update+" "+
	 unit.options[unit.selectedIndex].text.replace(/(.*-)/g, '')+"<br/>"+
	 clm_ecfv2_import_updateClub;
	 clm_ecfv2_import_start(update, update_buttons);
	 
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
                    clm_ecfv2_import_end(update, update_buttons, clm_ecfv2_import_noJson+xmlhttp.responseText+'ende');
                    return;
                }

                if (out.length != 1 || out[0].length != 3 || out[0][0] != true) {
                    clm_ecfv2_import_end(update, update_buttons, clm_ecfv2_import_wrongResponse+xmlhttp.responseText+'ende');
                    return;
                }

		clm_ecfv2_import_end(update, update_buttons, clm_ecfv2_import_update+" "+unit.options[unit.selectedIndex].text.replace(/(.*-)/g, '')+"<br/>"+clm_ecfv2_import_finishedClub+" "+out[0][2]);
            } else {
                clm_ecfv2_import_end(update, update_buttons, clm_ecfv2_import_errorHttp + " " + xmlhttp.status);
            }
        }
    }
    xmlhttp.open("POST", clm_ecfv2_import_url, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send('command=[0,["db_ecfv2_club",["' + unit.value + '"]]]');
}

function clm_ecfv2_import_player_one(obj, update, update_buttons, unit, p, i, player,count) {
    update.innerHTML = clm_ecfv2_import_update+" "+unit.options[unit.selectedIndex].text.replace(/(.*-)/g, '')+"<br/>"+clm_ecfv2_import_alreadyFinishedClubs+" "+i + " " + clm_ecfv2_import_of + " " + obj.length + "<br/>" + clm_ecfv2_import_alreadyFinishedPlayers + " " + (player) +"<br/>"+clm_ecfv2_import_working+" "+obj[i].Vereinname;

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
                    clm_ecfv2_import_end(update, update_buttons, clm_ecfv2_import_noJson + xmlhttp.responseText);
                    return;
                }
                if (out.length != 1 || out[0].length != 3 || out[0][0] != true) {
		    if(count<5) {
                    	clm_ecfv2_import_player_one(obj, update, update_buttons, unit, p, i, player,count+1);
			return;
		    }
                    clm_ecfv2_import_end(update, update_buttons, clm_ecfv2_import_wrongResponse + xmlhttp.responseText);
                    return;
                }

                if (i + 1 == obj.length) {
                    clm_ecfv2_import_end(update, update_buttons, update.innerHTML = clm_ecfv2_import_finished+" "+unit.options[unit.selectedIndex].text.replace(/(.*-)/g, '')+"<br/>"+clm_ecfv2_import_alreadyFinishedClubs+" "+ (i+1) + " " + clm_ecfv2_import_of + " " + obj.length + "<br/>" + clm_ecfv2_import_alreadyFinishedPlayers + " " + (player + out[0][2]));
                } else {
                    clm_ecfv2_import_player_one(obj, update, update_buttons, unit, p, (i + 1), (player + out[0][2]),0);
                }
            } else {
                clm_ecfv2_import_end(update, update_buttons, clm_ecfv2_import_errorHttp + " " + xmlhttp.status);
            }
        }
    }
    xmlhttp.open("POST", clm_ecfv2_import_url, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send('command=[0,["db_ecfv2_player",["' + obj[i].ZPS + '",' + p + ']]]');
}
function clm_ecfv2_import_start(update, update_buttons) {
    update_buttons[0].disabled = true;
    update_buttons[1].disabled = true;
//    update_buttons[2].disabled = true;
    update.style.display = "block";
}
function clm_ecfv2_import_end(update, update_buttons, response) {
    update.innerHTML = response;
    update_buttons[0].disabled = false;
    update_buttons[1].disabled = false;
//    update_buttons[2].disabled = false;
}
