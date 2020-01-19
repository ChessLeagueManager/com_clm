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
		if (form.name.value == "") {
			alert( jtext['enter_name'] );
		} else if (form.twz.value == "") {
			alert( jtext['enter_twz'] );
		} else if (isNaN(form.twz.value)) {
			alert( jtext['number_twz'] );
		} else {
			Joomla.submitform( pressbutton );
		}
	}
