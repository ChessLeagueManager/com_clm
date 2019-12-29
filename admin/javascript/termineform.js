/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2019 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
	Joomla.submitbutton = function (pressbutton) { 
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			Joomla.submitform( pressbutton );
			return;
		}
		
		// do field validation
		if (form.name.value == "") {
			alert( jserror['enter_name'] );
		} else if (form.startdate.value == "" || form.startdate.value == "0000-00-00" || form.startdate.value == "1970-01-01") {
			alert( jserror['enter_startdate'] );
		} else if (form.startdate.value == "0000-00-00" && form.startdate.value == "1970-01-01" && form.starttime.value != "00:00") {
			alert( jserror['dont_starttime'] );
		} else if ((form.startdate.value == "0000-00-00" || form.startdate.value == "1970-01-01") && (form.enddate.value != "0000-00-00" && form.enddate.value != "1970-01-01")) {
			alert( jserror['dont_enddate'] );
		} else if (form.enddate.value > "1970-01-01" && form.startdate.value > form.enddate.value) {
			alert( jserror['enddate_wrong'] );
		} else if (form.starttime.value == "00:00" && form.endtime.value != "00:00") {
			alert( jserror['dont_endtime'] );
		} else if (form.endtime.value != "00:00" && form.allday.checked == true) {
			alert( jserror['dont_allday'] );
		} else if (form.starttime.value == "00:00" && form.noendtime.checked == true) {
			alert( jserror['dont_noendtime'] );
		} else {
			Joomla.submitform( pressbutton );
		}
	}
