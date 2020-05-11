/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
		function Tausch (x)
		{
			var w = document.getElementById(x).selectedIndex;
			var selected_text = document.getElementById(x).options[w].text;

			document.getElementById('name').value=selected_text;
		}

		function VSTausch (x)
		{
			var w = document.getElementById(x).selectedIndex;
			var selected_text = document.getElementById(x).options[w].text;

			document.getElementById('vs').value=selected_text;
		}

	
	Joomla.submitbutton = function (pressbutton) { 
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			Joomla.submitform( pressbutton );
			return;
		}
		
		// do field validation
		if (form.name.value == "") {
			alert( unescape(clm_vereine_name) ); return;
		} else {
			// get references to select list and display text box
			var sel = document.getElementById('zps');			
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
			alert( unescape(clm_vereine_zps) ); return;
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
			alert( unescape(clm_vereine_sid) );
		} else {
			Joomla.submitform( pressbutton );
		}
	}
