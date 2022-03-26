/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2022 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
	
	Joomla.submitbutton = function (pressbutton) { 		
		var form = document.adminForm;
		var double = 0;
			if (pressbutton == 'cancel') {
				Joomla.submitform( pressbutton );
				return;
			}
			// do field validation
			for ( var ispieler = 1; ispieler <= clm_meldelisten_number; ispieler++) {
				var iispieler = "spieler"+ispieler;
				for ( var jspieler = ispieler + 1; jspieler <= clm_meldelisten_number; jspieler++) {
					var jjspieler = "spieler"+jspieler;
					// get references to select list and display text box
					var sel = document.getElementById(iispieler);			
					var opt;
					var ival = 0;
					for ( var i = 0, len = sel.options.length; i < len; i++ ) {
						opt = sel.options[i];
						if ( opt.selected === true ) {
							ival = opt.value;
							break;
						}
					}
					var sel = document.getElementById(jjspieler);			
					var opt;
					var jval = 0;
					for ( var i = 0, len = sel.options.length; i < len; i++ ) {
						opt = sel.options[i];
						if ( opt.selected === true ) {
							jval = opt.value;
							break;
						}
					}
					if ((ival != "0") && (ival == jval)) {
						alert( unescape(clm_meldelisten_double)+":  "+ispieler+" = "+jspieler );
						double = 1;
					}
				}
			}
			if (double == 0) {
				Joomla.submitform( pressbutton );
			}
		}


		function insertPosition (spieler, selvalue) {
			var selectedText = spieler.options[spieler.selectedIndex].innerHTML;
			var selectedValue = spieler.value;
			var selName = spieler.name;
			var selNumber = selName.substr(7);
			if (selectedValue == "9999") {	// wurde einfügen ausgewählt?
				var spielerlast = 'spieler' + clm_meldelisten_number;
				var sel = document.getElementById(spielerlast);			
				var opt;
				var valuelast = 0;
				for ( var i = 0, len = sel.options.length; i < len; i++ ) {
					opt = sel.options[i];
					if ( opt.selected === true ) {
						valuelast = opt.value;
						break;
					}
				}
				if (valuelast != "0") {  // ist die letzte Meldeposition belegt?
					alert( clm_meldelisten_last );
					spieler.value = selvalue;
				} else {
					for ( var ispieler = Number(clm_meldelisten_number); ispieler > selNumber; ispieler--) {
						var jspieler = ispieler - 1;
						var jjspieler = "spieler"+jspieler;
						var jjattr = "attr"+jspieler;
						if (jspieler == selNumber) {
							var jjvalue = selvalue;
							var jjvaluea = "";
						} else {
							var sel = document.getElementById(jjspieler);			
							var opt;
							var jjvalue = 0;
							for ( var i = 0, len = sel.options.length; i < len; i++ ) {
								opt = sel.options[i];
								if ( opt.selected === true ) {
									jjvalue = opt.value;
									break;
								}
							}
							var elementa = document.getElementById(jjattr);
							var jjvaluea = elementa.value;
						}
						var iispieler = "spieler"+ispieler;
						var element = document.getElementById(iispieler);
						element.value = jjvalue;

						var iiattr = "attr"+ispieler;
						var elementn = document.getElementById(iiattr);
						elementn.value = jjvaluea;					
					}
					spieler.selectedIndex = 0;
				}
			}
		}
		
