/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team  All rights reserved
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
			alert( unescape(clm_mannschaft_name) ); return;
		} else if (form.man_nr.value == "") {
			alert( unescape(clm_mannschaft_mnr) ); return;
		} else if (form.tln_nr.value == "") {
			alert( unescape(clm_mannschaft_tnr) ); return;
		} else {
			// get references to select list and display text box
			var sel = document.getElementById('sid');			
			var opt;
			var val = 0;
			for ( var i = 0, len = sel.options.length; i < len; i++ ) {
				opt = sel.options[i];
				if ( opt.selected === true ) {
					val = opt.value;
					break;
				}
			}
		}
		if ( val == 0 ) {
			alert( unescape(clm_mannschaft_sid) ); return;
		} else {
			// get references to select list and display text box
			var sel = document.getElementById('liga');			
			var opt;
			var val = 0;
			for ( var i = 0, len = sel.options.length; i < len; i++ ) {
				opt = sel.options[i];
				if ( opt.selected === true ) {
					val = opt.value;
					break;
				}
			}
		}
		if ( val == 0 ) {
			alert( unescape(clm_mannschaft_lid) ); return;
		} else {
			// get references to select list and display text box
			var sel = document.getElementById('zps');			
			var opt;
			var val = 0;
			for ( var i = 0, len = sel.options.length; i < len; i++ ) {
				opt = sel.options[i];
				if ( opt.selected === true ) {
					val = opt.value;
					break;
				}
			}
		}
		if ( val == 0 && clm_mannschaft_noOrg == 0 ) {
			if (form.man_nr.value != "0") {								  
				alert( unescape(clm_mannschaft_zps) ); return;
			}
		} 
		Joomla.submitform( pressbutton );
	}
