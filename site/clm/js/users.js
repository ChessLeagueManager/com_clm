/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function emailsyntax(element) {
	let regex = new RegExp("([!#-'*+/-9=?A-Z^-~-]+(\.[!#-'*+/-9=?A-Z^-~-]+)*|\"\(\[\]!#-[^-~ \t]|(\\[\t -~]))+\")@([!#-'*+/-9=?A-Z^-~-]+\\\.([!#-'*+/-9=?A-Z^-~-]+)|\[[\t -Z^-~]*])");
	address = element.value;
	if (regex.test(address)) {
		return true;
	}
	return false;
}

	Joomla.submitbutton = function (pressbutton) { 	
	  var form = document.adminForm;
	  if (pressbutton == 'cancel') {
		  Joomla.submitform( pressbutton );
		  return;
	  }
		if (form.pid.value =="0") {
			// do field validation
			if (form.name.value == "") {
				alert( unescape(clm_users_name) ); return;
			} else if (form.username.value == "") {
				alert( unescape(clm_users_user) ); return;
			} else if (form.email.value == "") {
				alert( unescape(clm_users_mail) ); return;
			}
			let zmail = form.email.value;
			let ymail = zmail.trim();
			form.email.value = ymail;
			element = form.email;
			let rv = emailsyntax(element);
			if (rv === false) {
				alert( unescape(clm_users_wrong_mail) ); return;
			} else {	
				// restliche Prüfungen nur für freigegebene Benutzer
				if (form.published.value =="0") { Joomla.submitform( pressbutton ); 
												  return; }
				// get references to select list and display text box
				var sel = document.getElementById('usertype');			
				var opt;
				var val = 0;
				for ( var i = 0, len = sel.options.length; i < len; i++ ) {
					opt = sel.options[i];
					if ( opt.selected === true ) {
						val = opt.value;
						break;
					}
				}
				if ( val == 0 ) {
					alert( unescape(clm_users_func) ); return;
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
				if ( val == 0 ) {
					alert( unescape(clm_users_zps) ); return;
				} else if ( clm_user_member == "1" && form.org_exc.value == "0" && form.mglnr.value == "" && form.PKZ.value == "") {
					alert( unescape(clm_users_pkz) ); return;
				} else if ( clm_user_member == "1" && form.org_exc.value == "1" && form.bem_int.value == "") {
					alert( unescape(clm_users_bem) ); return;
				}
			}
		} else {
			// do field validation
			// get references to select list and display text box
			var sel = document.getElementById('usertype');			
			var opt;
			var val = 0;
			for ( var i = 0, len = sel.options.length; i < len; i++ ) {
				opt = sel.options[i];
				if ( opt.selected === true ) {
					val = opt.value;
					break;
				}
			}
			if ( val == 0 ) {
				alert( unescape(clm_users_func) ); return;
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
			if ( val == 0 ) {
				alert( unescape(clm_users_zps) ); return;
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
				alert( unescape(clm_users_sid) ); return;
			} else if ( clm_user_member == "1" && form.org_exc.value == "1" && form.bem_int.value == "") {
				alert( unescape(clm_users_bem) ); return;
			}
		}
		Joomla.submitform( pressbutton );
	}

	