/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team  All rights reserved
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
			alert( jtext['enter_name'] );
		} else if (form.nr.value == "") {
			alert( jtext['enter_nr'] );
		} else if (isNaN(form.nr.value)) {
			alert( jtext['number_nr'] );
		} else if (form.datum.value == "") {
			alert( jtext['enter_date'] );
		} else if (form.datum.value > dateEnd ) {
			alert( 'Rundendatum größer als Enddatum des Turniers' );
		} else if (form.datum.value < dateStart ) {
			alert( 'Rundendatum kleiner als Startdatum des Turniers' );
		} else {
			Joomla.submitform( pressbutton );
		}
	}
