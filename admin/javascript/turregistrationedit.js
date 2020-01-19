/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2019 CLM Team  All rights reserved
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
		if (form.name.value == "" || form.name.value == ',') {
			alert( jtext['enter_name'] );
		} else if (form.birthYear.value == "" || form.birthYear.value < "1000") {
			alert( jtext['enter_birthyear'] );
		} else if (isNaN(form.birthYear.value)) {
			alert( jtext['number_birthyear'] );
		} else if (form.club.value == "" ) {
			alert( jtext['enter_club'] );
		} else if (form.email.value == "" ) {
			alert( jtext['enter_email'] );
		} else if (isNaN(form.dwz.value)) {
			alert( jtext['number_dwz'] );
		} else if (isNaN(form.dwz_I0.value)) {
			alert( jtext['number_dwzindex'] );
		} else if (isNaN(form.elo.value)) {
			alert( jtext['number_elo'] );
		} else if (isNaN(form.FIDEid.value)) {
			alert( jtext['number_fideid'] );
		} else if (isNaN(form.mgl_nr.value)) {
			alert( jtext['number_mglnr'] );
		} else {
			Joomla.submitform( pressbutton );
		}
	}
