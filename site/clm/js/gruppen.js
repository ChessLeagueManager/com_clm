/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
	Joomla.submitbutton = function (pressbutton) { 	
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			Joomla.submitform( pressbutton );
			return;
		}
		// do field validation
		if (form.Gruppe.value == "") {
			alert( unescape(clm_gruppen_name) ); return;
		} else if (form.Meldeschluss.value == "") {
			alert( unescape(clm_gruppen_date) ); return;
		} else if (form.stichtag_regel.value == "9") {
			alert( unescape('Bitte waehlen Sie eine Stichtagsregel!') ); return;
		} else if ((form.stichtag_regel.value == "1" || form.stichtag_regel.value == "2") && form.stichtag.value <= "1970-01-01") {
			alert( unescape('Bitte tragen Sie den Stichtag ein!') ); return;
		} else if ((form.stichtag_regel.value == "1" || form.stichtag_regel.value == "2") && (form.alter_grenze.value == "1" || form.alter_grenze.value == "2")) {
			alert( unescape('Eine Auswahl nach Stichtag und gleichzeitig Alter ist nicht sinnvoll!') ); return;
		} else if (form.geschlecht.value == "9") {
			alert( unescape(clm_gruppen_geschlecht) ); return;
		} else if (form.alter_grenze.value == "9") {
			alert( unescape(clm_gruppen_alter_grenze) ); return;
		} else if (isNaN(form.alter.value) || (form.alter.value == "") ) {
			alert( unescape(clm_gruppen_alter) ); return;
		} else if (isNaN(form.anz_sgp.value) || (form.anz_sgp.value > 4) ) {
			alert( unescape('Nur 0 bis 4 Partnervereine sind unterstützt') ); return;
		} else {
			// get references to select list and display text box
			var sel = document.getElementById('sid');			
			var opt;
			for ( var i = 0, len = sel.options.length; i < len; i++ ) {
				opt = sel.options[i];
				if ( opt.selected === true ) {
					val = opt.value;
					break;
				}
			}
		}
		if ( val == 0 ) {
			alert( unescape(clm_gruppen_sid) );
		} else {
			Joomla.submitform( pressbutton );
		}
	}
