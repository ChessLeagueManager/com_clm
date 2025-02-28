/*
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2025 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
	var clm_dsb_import_file = new Array();
	var clm_dsb_import_button = 0;
	var clm_dsb_import_playerV = 1;

	function clm_dsb_import_chunk(array, chunkSize) {
	    var R = [];
	    for (var i = 0; i < array.length; i += chunkSize) {
	        R.push(array.slice(i, i + chunkSize));
	    }
	    return R;
	}

	function clm_dsb_import_check(object) {
	    update = object.parentElement.parentElement.getElementsByClassName("clm_view_dsb_import_update")[0];
	    update_buttons = object.parentElement.getElementsByTagName("input");
	    // Check for the various File API support.
	    if (!window.File || !window.FileReader) {
	        update.innerHTML = clm_dsb_import_browserProblem;
	        update.style.display = "block";
	        clm_dsb_import_button = 0;
	        clm_dsb_import_button_update(object);
	        return;
	    }
	    var files = object.parentElement.getElementsByTagName("input")[0].files;
	    if (!files.length) {
	        clm_dsb_import_button = 0;
	        clm_dsb_import_button_update(object);
	        return;
	    }
	    var reader = new FileReader();
	    // If we use onloadend, we need to check the readyState.
	    reader.onloadend = function(evt) {
	        if (evt.target.readyState == FileReader.DONE) { // DONE == 2
	            out = evt.target.result.split("\n");
	            if (out.length < 2) {
	                update.innerHTML = clm_dsb_import_fileProblem1;
	                update.style.display = "block";
	                clm_dsb_import_button = 0;
	                clm_dsb_import_button_update(object);
	            }
	            csv_head = out[0];
	            out.splice(0, 1);
	            if (csv_head == "ZPS,Mgl-Nr,Status,Spielername,Geschlecht,Spielberechtigung,Geburtsjahr,Letzte-Auswertung,DWZ,Index,FIDE-Elo,FIDE-Titel,FIDE-ID,FIDE-Land") {
	                update.innerHTML = clm_dsb_import_playerFile;
					clm_dsb_import_playerV = 1;
	                update.style.display = "block";
	                clm_dsb_import_file = clm_dsb_import_chunk(out, clm_dsb_import_amount);
	                clm_dsb_import_button = 2;
	                clm_dsb_import_button_update(object);
	            } else if (csv_head == "ID,VKZ,Mgl-Nr,Status,Spielername,Geschlecht,Spielberechtigung,Geburtsjahr,Letzte-Auswertung,DWZ,Index,FIDE-Elo,FIDE-Titel,FIDE-ID,FIDE-Land") {
	                update.innerHTML = clm_dsb_import_playerFileV2;
					clm_dsb_import_playerV = 2;
	                update.style.display = "block";
	                clm_dsb_import_file = clm_dsb_import_chunk(out, clm_dsb_import_amount);
	                clm_dsb_import_button = 2;
	                clm_dsb_import_button_update(object);
	            } else if (csv_head == "ZPS,LV,Verband,Vereinname") {
	                update.innerHTML = clm_dsb_import_clubFile;
	                update.style.display = "block";
	                clm_dsb_import_file = clm_dsb_import_chunk(out, clm_dsb_import_amount);
	                clm_dsb_import_button = 1;
	                clm_dsb_import_button_update(object);
	            } else {
	                update.innerHTML = clm_dsb_import_fileProblem1;
	                update.style.display = "block";
	                clm_dsb_import_button = 0;
	                clm_dsb_import_button_update(object);
	            }
	        } else {
	            update.innerHTML = clm_dsb_import_uploadProblem;
	            update.style.display = "block";
	        }
	    };
	    reader.readAsText(files[0], 'ISO-8859-1');
	}

        function clm_dsb_send_player_request(xmlhttp, file, p, format, status, key)
        {
	    xmlhttp.open("POST", clm_dsb_import_url, true);
	    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	    var command = new Array(2);
	    command[1] = new Array(2);
	    command[1][1] = new Array(5);
	    command[0] = 0;
	    command[1][0] = "db_dsb_player";
	    command[1][1][0] = file;
	    command[1][1][1] = p;
	    command[1][1][2] = format;
	    command[1][1][3] = status;
	    command[1][1][4] = key;
	    xmlhttp.send('command=' + encodeURIComponent(JSON.stringify(command)));
        }

	function clm_dsb_import_player(object, i, p, player, key) {

            if (i == 0)
              uuid= crypto.randomUUID();

	    update.innerHTML = clm_dsb_import_updatePlayer + "<br/>" + clm_dsb_import_percentage + " " + i + " " + clm_dsb_import_of + " " + clm_dsb_import_file.length + "<br/>" + clm_dsb_import_updatedPlayer + " " + player;
	    clm_dsb_import_start(object);

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
	                    update.innerHTML = clm_dsb_import_noJson;
	                    clm_dsb_import_stop(object);
	                    return;
	                }
	                if (out.length != 1 || out[0].length != 3 || out[0][0] != true) {
                            if (out[0].length == 3 && out[0][1] == 'e_DBDWZImport')
				update.innerHTML = clm_dsb_import_dberror + out[0][2];
                            else
				update.innerHTML = clm_dsb_import_wrongResponse;
	                    clm_dsb_import_stop(object);
	                    return;
	                }

	                if (i + 1 == clm_dsb_import_file.length) {
	                    update.innerHTML = clm_dsb_import_finishedPlayer + "<br/>" + clm_dsb_import_percentage + " " + (i + 1) + " " + clm_dsb_import_of + " " + clm_dsb_import_file.length + "<br/>" + clm_dsb_import_updatedPlayer + " " + (player + out[0][2]);
	                    clm_dsb_import_stop(object);
	                } else {
	                    clm_dsb_import_player(object, i + 1, p, player + out[0][2], uuid);
	                }
	            } else {
	                update.innerHTML = clm_dsb_import_noJson;
	                clm_dsb_import_stop(clm_dsb_import_errorHttp + " " + xmlhttp.status);
	            }
	        }
	    }
            if (i == 0) /* Start */
              clm_dsb_send_player_request(xmlhttp, clm_dsb_import_file[i], p, clm_dsb_import_playerV, 1, uuid);
            else if (i + 1 == clm_dsb_import_file.length)
            {
              clm_dsb_send_player_request(xmlhttp, clm_dsb_import_file[i], p, clm_dsb_import_playerV, 2, uuid);
            }
            else
              clm_dsb_send_player_request(xmlhttp, clm_dsb_import_file[i], p, clm_dsb_import_playerV, 0, uuid);
	}

	function clm_dsb_import_club(object, i, club) {
	    update.innerHTML = clm_dsb_import_updateClub + "<br/>" + clm_dsb_import_percentage + " " + i + " " + clm_dsb_import_of + " " + clm_dsb_import_file.length + "<br/>" + clm_dsb_import_updatedClub + " " + club;
	    clm_dsb_import_start(object);

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
	                    update.innerHTML = clm_dsb_import_noJson;
	                    clm_dsb_import_stop(object);
	                    return;
	                }
	                if (out.length != 1 || out[0].length != 3 || out[0][0] != true) {
	                    update.innerHTML = clm_dsb_import_wrongResponse;
	                    clm_dsb_import_stop(object);
	                    return;
	                }

	                if (i + 1 == clm_dsb_import_file.length) {
	                    update.innerHTML = clm_dsb_import_finishedClub + "<br/>" + clm_dsb_import_percentage + " " + (i + 1) + " " + clm_dsb_import_of + " " + clm_dsb_import_file.length + "<br/>" + clm_dsb_import_updatedClub + " " + (club + out[0][2]);
	                    clm_dsb_import_stop(object);
	                } else {
	                    clm_dsb_import_club(object, i + 1, club + out[0][2]);
	                }
	            } else {
	                update.innerHTML = clm_dsb_import_noJson;
	                clm_dsb_import_stop(clm_dsb_import_errorHttp + " " + xmlhttp.status);
	            }
	        }
	    }
	    xmlhttp.open("POST", clm_dsb_import_url, true);
	    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	    var command = new Array(2);
	    command[1] = new Array(2);
	    command[1][1] = new Array(1);
	    command[0] = 0;
	    command[1][0] = "db_dsb_club";
	    command[1][1][0] = clm_dsb_import_file[i];
	    xmlhttp.send('command=' + encodeURIComponent(JSON.stringify(command)));
	}

	function clm_dsb_import_button_update(object) {
	    update_buttons = object.parentElement.getElementsByTagName("button");
	    if (clm_dsb_import_button != 1) {
	        update_buttons[0].disabled = true;
	    } else {
	        update_buttons[0].disabled = false;
	    }

	    if (clm_dsb_import_button != 2) {
	        update_buttons[1].disabled = true;
	    } else {
	        update_buttons[1].disabled = false;
	    }

	    if (clm_dsb_import_button != 2) {
	        update_buttons[2].disabled = true;
	    } else {
	        update_buttons[2].disabled = false;
	    }
	}

	function clm_dsb_import_start(object) {
	    update_buttons = object.parentElement.getElementsByTagName("input");
	    update_buttons[0].disabled = true;
	    update_buttons = object.parentElement.getElementsByTagName("button");
	    update_buttons[0].disabled = true;
	    update_buttons[1].disabled = true;
	    update_buttons[2].disabled = true;
	}

	function clm_dsb_import_stop(object) {
	    update_buttons = object.parentElement.getElementsByTagName("input");
	    update_buttons[0].disabled = false;
	    clm_dsb_import_button_update(object);
	}
