// @ Chess League Manager (CLM) Component 
// @Copyright (C) 2008-2019 CLM Team.  All rights reserved
// @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
// @link http://www.chessleaguemanager.de
	Joomla.submitbutton = function (pressbutton) { 
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		
		// do field validation
		if (form.name.value == "") {
			alert( jserror['enter_name'] );
		} else {
			submitform( pressbutton );
		}
	}
