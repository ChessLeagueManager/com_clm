	var clm_ecf_import_file = new Array();
	var clm_ecf_import_button = 0;

	function clm_ecf_import_chunk(array, chunkSize) {
	    var R = [];
	    for (var i = 0; i < array.length; i += chunkSize) {
	        R.push(array.slice(i, i + chunkSize));
	    }
	    return R;
	}

	function clm_ecf_import_check(object) {
	    update = object.parentElement.parentElement.getElementsByClassName("clm_view_ecf_import_update")[0];
	    update_buttons = object.parentElement.getElementsByTagName("input");
	    // Check for the various File API support.
	    if (!window.File || !window.FileReader) {
	        update.innerHTML = clm_ecf_import_browserProblem;
	        update.style.display = "block";
	        clm_ecf_import_button = 0;
	        clm_ecf_import_button_update(object);
	        return;
	    }
	    var files = object.parentElement.getElementsByTagName("input")[0].files;
	    if (!files.length) {
	        clm_ecf_import_button = 0;
	        clm_ecf_import_button_update(object);
	        return;
	    }
	    var reader = new FileReader();
	    // If we use onloadend, we need to check the readyState.
	    reader.onloadend = function(evt) {
	        if (evt.target.readyState == FileReader.DONE) { // DONE == 2
	            out = evt.target.result.split("\n");
	            if (out.length < 2) {
	                update.innerHTML = clm_ecf_import_fileProblem1;
	                update.style.display = "block";
	                clm_ecf_import_button = 0;
	                clm_ecf_import_button_update(object);
	            }
	            csv_head = out[0];
	            out.splice(0, 1);
				var csv_head1 = csv_head.substring(0, (csv_head.length - 1) );
	            if ((csv_head == '"Ref","Name","Sex","JrAge","Cat","Grade","Grade1","Games","RCat","RGrade","RGrade1","RGames","ClubNam1","ClubNam2","ClubNam3","ClubNam4","ClubNam5","ClubNam6","FIDECode","Nation"') ||
					(csv_head1 == '"Ref","Name","Sex","JrAge","Cat","Grade","Grade1","Games","RCat","RGrade","RGrade1","RGames","ClubNam1","ClubNam2","ClubNam3","ClubNam4","ClubNam5","ClubNam6","FIDECode","Nation"') ||
					(csv_head == 'Ref,Name,Sex,JrAge,Cat,Grade,Grade1,Games,RCat,RGrade,RGrade1,RGames,ClubNam1,ClubNam2,ClubNam3,ClubNam4,ClubNam5,ClubNam6,FIDECode,Nation') ||
					(csv_head1 == 'Ref,Name,Sex,JrAge,Cat,Grade,Grade1,Games,RCat,RGrade,RGrade1,RGames,ClubNam1,ClubNam2,ClubNam3,ClubNam4,ClubNam5,ClubNam6,FIDECode,Nation')) {
	                update.innerHTML = clm_ecf_import_playerFile;
	                update.style.display = "block";
	                clm_ecf_import_file = clm_ecf_import_chunk(out, clm_ecf_import_amount);
	                clm_ecf_import_button = 3;
	                clm_ecf_import_button_update(object);
				} else if ((csv_head == "Club,Code,Union,County") || (csv_head1 == "Club,Code,Union,County")) {
	                update.innerHTML = clm_ecf_import_clubFile;
	                update.style.display = "block";
	                clm_ecf_import_file = clm_ecf_import_chunk(out, clm_ecf_import_amount);
	                clm_ecf_import_button = 2;
	                clm_ecf_import_button_update(object);
	            } else if ((csv_head == "Code,Part,HigherLevel,Organisation,Allocation") || (csv_head1 == "Code,Part,HigherLevel,Organisation,Allocation")) {
	                update.innerHTML = clm_ecf_import_orgFile;
	                update.style.display = "block";
	                clm_ecf_import_file = clm_ecf_import_chunk(out, clm_ecf_import_amount);
	                clm_ecf_import_button = 1;
	                clm_ecf_import_button_update(object);
	            } else {
	                update.innerHTML = clm_ecf_import_fileProblem1;
	                update.style.display = "block";
	                clm_ecf_import_button = 0;
	                clm_ecf_import_button_update(object);
	            }
	        } else {
	            update.innerHTML = clm_ecf_import_uploadProblem;
	            update.style.display = "block";
	        }
	    };
	    reader.readAsText(files[0], 'ISO-8859-1');
	}

	function clm_ecf_import_player(object, i, p, player) {
	    update.innerHTML = clm_ecf_import_updatePlayer + "<br/>" + clm_ecf_import_percentage + " " + i + " " + clm_ecf_import_of + " " + clm_ecf_import_file.length + "<br/>" + clm_ecf_import_updatedPlayer + " " + player;
	    clm_ecf_import_start(object);

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
	                    update.innerHTML = clm_ecf_import_noJson;
	                    clm_ecf_import_stop(object);
	                    return;
	                }
	                if (out.length != 1 || out[0].length != 3 || out[0][0] != true) {
	                    update.innerHTML = clm_ecf_import_wrongResponse;
	                    clm_ecf_import_stop(object);
	                    return;
	                }

	                if (i + 1 == clm_ecf_import_file.length) {
	                    update.innerHTML = clm_ecf_import_finishedPlayer + "<br/>" + clm_ecf_import_percentage + " " + (i + 1) + " " + clm_ecf_import_of + " " + clm_ecf_import_file.length + "<br/>" + clm_ecf_import_updatedPlayer + " " + (player + out[0][2]);
	                    clm_ecf_import_stop(object);
	                } else {
	                    clm_ecf_import_player(object, i + 1, p, player + out[0][2]);
	                }
	            } else {
	                update.innerHTML = clm_ecf_import_noJson;
	                clm_ecf_import_stop(clm_ecf_import_errorHttp + " " + xmlhttp.status);
	            }
	        }
	    }
	    xmlhttp.open("POST", clm_ecf_import_url, true);
	    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	    var command = new Array(2);
	    command[1] = new Array(2);
	    command[1][1] = new Array(2);
	    command[0] = 0;
	    command[1][0] = "db_ecf_player";
	    command[1][1][0] = clm_ecf_import_file[i];
	    command[1][1][1] = p;
	    xmlhttp.send('command=' + encodeURIComponent(JSON.stringify(command)));
	}

	function clm_ecf_import_club(object, i, club) {
	    update.innerHTML = clm_ecf_import_updateClub + "<br/>" + clm_ecf_import_percentage + " " + i + " " + clm_ecf_import_of + " " + clm_ecf_import_file.length + "<br/>" + clm_ecf_import_updatedClub + " " + club;
	    clm_ecf_import_start(object);

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
	                    update.innerHTML = clm_ecf_import_noJson;
	                    clm_ecf_import_stop(object);
	                    return;
	                }
	                if (out.length != 1 || out[0].length != 3 || out[0][0] != true) {
	                    update.innerHTML = clm_ecf_import_wrongResponse;
	                    clm_ecf_import_stop(object);
	                    return;
	                }

	                if (i + 1 == clm_ecf_import_file.length) {
	                    update.innerHTML = clm_ecf_import_finishedClub + "<br/>" + clm_ecf_import_percentage + " " + (i + 1) + " " + clm_ecf_import_of + " " + clm_ecf_import_file.length + "<br/>" + clm_ecf_import_updatedClub + " " + (club + out[0][2]);
	                    clm_ecf_import_stop(object);
	                } else {
	                    clm_ecf_import_club(object, i + 1, club + out[0][2]);
	                }
	            } else {
	                update.innerHTML = clm_ecf_import_noJson;
	                clm_ecf_import_stop(clm_ecf_import_errorHttp + " " + xmlhttp.status);
	            }
	        }
	    }
	    xmlhttp.open("POST", clm_ecf_import_url, true);
	    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	    var command = new Array(2);
	    command[1] = new Array(2);
	    command[1][1] = new Array(1);
	    command[0] = 0;
	    command[1][0] = "db_ecf_club";
	    command[1][1][0] = clm_ecf_import_file[i];
	    xmlhttp.send('command=' + encodeURIComponent(JSON.stringify(command)));
	}

	function clm_ecf_import_org(object, i, org) {
	    update.innerHTML = clm_ecf_import_updateOrg + "<br/>" + clm_ecf_import_percentage + " " + i + " " + clm_ecf_import_of + " " + clm_ecf_import_file.length + "<br/>" + clm_ecf_import_updatedOrg + " " + org + " " + clm_ecf_import_of;
	    clm_ecf_import_start(object);

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
	                    update.innerHTML =  clm_ecf_import_noJson  + "out" + out[0];
	                    clm_ecf_import_stop(object);
	                    return;
	                }
	                if (out.length != 1 || out[0].length != 3 || out[0][0] != true) {
	                    update.innerHTML = clm_ecf_import_wrongResponse;
	                    clm_ecf_import_stop(object);
	                    return;
	                }

	                if (i + 1 == clm_ecf_import_file.length) {
	                    update.innerHTML = clm_ecf_import_finishedOrg + "<br/>" + clm_ecf_import_percentage + " " + (i + 1) + " " + clm_ecf_import_of + " " + clm_ecf_import_file.length + "<br/>" + clm_ecf_import_updatedOrg + " " + (org + out[0][2]);
	                    clm_ecf_import_stop(object);
	                } else {
	                    clm_ecf_import_org(object, i + 1, org + out[0][2]);
	                }
	            } else {
	                update.innerHTML = clm_ecf_import_noJson;
	                clm_ecf_import_stop(clm_ecf_import_errorHttp + " " + xmlhttp.status);
	            }
	        }
	    }
	    xmlhttp.open("POST", clm_ecf_import_url, true);
	    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	    var command = new Array(2);
	    command[1] = new Array(2);
	    command[1][1] = new Array(1);
	    command[0] = 0;
	    command[1][0] = "db_ecf_org";
	    command[1][1][0] = clm_ecf_import_file[i];
	    xmlhttp.send('command=' + encodeURIComponent(JSON.stringify(command)));
	}

	function clm_ecf_import_button_update(object) {
	    update_buttons = object.parentElement.getElementsByTagName("button");
	    if (clm_ecf_import_button != 1) {
	        update_buttons[0].disabled = true;
	    } else {
	        update_buttons[0].disabled = false;
	    }

	    if (clm_ecf_import_button != 2) {
	        update_buttons[1].disabled = true;
	    } else {
	        update_buttons[1].disabled = false;
	    }

	    if (clm_ecf_import_button != 3) {
	        update_buttons[2].disabled = true;
	    } else {
	        update_buttons[2].disabled = false;
	    }
	}

	function clm_ecf_import_start(object) {
	    update_buttons = object.parentElement.getElementsByTagName("input");
	    update_buttons[0].disabled = true;
	    update_buttons = object.parentElement.getElementsByTagName("button");
	    update_buttons[0].disabled = true;
	    update_buttons[1].disabled = true;
	    update_buttons[2].disabled = true;
	}

	function clm_ecf_import_stop(object) {
	    update_buttons = object.parentElement.getElementsByTagName("input");
	    update_buttons[0].disabled = false;
	    clm_ecf_import_button_update(object);
	}
