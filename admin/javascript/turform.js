/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
	function showFormRoundscount(){
		if (document.getElementById('typ').value == 1) { // CH
			document.getElementById('formRoundscountValue').innerHTML = jsform['runden1'] + form.runden.value + jsform['runden2'];
			document.getElementById('formRoundscountText').innerHTML = '';
			document.getElementById('formStagecount').innerHTML = '-';
			//document.getElementById('formTiebreakers').innerHTML = jsform['tiebreakers'];
		} else if (document.getElementById('typ').value == 2) { // voll
			changeRoundscountModus2();
			document.getElementById('formRoundscountText').innerHTML = jstext['roundscountgenerated'];
			//document.getElementById('formStagecount').innerHTML = jsform['stages'];
			//document.getElementById('formTiebreakers').innerHTML = jsform['tiebreakers'];
		} else if (document.getElementById('typ').value == 3) { // KO
			changeRoundscountModus3();
			document.getElementById('formRoundscountText').innerHTML = jstext['roundscountgenerated'];
			document.getElementById('formStagecount').innerHTML = '-';
			document.getElementById('formTiebreakers').innerHTML = '-';
		} else { // keine Auswahl
			document.getElementById('formRoundscountValue').innerHTML = jsform['runden'];
			document.getElementById('formRoundscountText').innerHTML = '';
			//document.getElementById('formStagecount').innerHTML = jsform['stages'];
			//document.getElementById('formTiebreakers').innerHTML = jsform['tiebreakers'];
		}
	}	
	
	
	function changeRoundscountModus2 () {
		if (document.getElementById('teil').value > 0) {
			var tempTeil = document.getElementById('teil').value;
			if (document.getElementById('teil').value%2 != 0) { // gerade machen
				tempTeil++;
			}
			var tempRunden = tempTeil-1;
			document.getElementById('formRoundscountValue').innerHTML = tempRunden;
		} else {
			document.getElementById('formRoundscountValue').innerHTML = '?';
		}
	}
	
	function changeRoundscountModus3 () {
		if (document.getElementById('teil').value > 0) {
			var tempRunden = Math.ceil(Math.log(document.getElementById('teil').value)/Math.log(2));
			document.getElementById('formRoundscountValue').innerHTML = tempRunden;
		} else {
			document.getElementById('formRoundscountValue').innerHTML = '?';
		}
	}
	
	Joomla.submitbutton = function (pressbutton) { 
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			Joomla.submitform( pressbutton );
			return;
		}
		
		// do field validation
		if (form.name.value == "") {
			alert( jserror['enter_name'] );
//		} else if ( getSelectedValue('adminForm','sid') == 0 ) {
//			alert( jserror['select_season'] );
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
			alert( jserror['select_season'] );
		} else if (form.typ.value == 0) {
			alert( jserror['select_modus'] );
		} else if (form.typ.value != 2 && form.typ.value != 3 && form.typ.value != 5 && (form.runden.value == "" || form.runden.value == 0)) {
			alert( jserror['enter_rounds'] );
		} else if (form.typ.value != 2 && form.typ.value != 3 && form.typ.value != 5 && isNaN(form.runden.value)) {
			alert( jserror['number_rounds'] );
		} else if (form.teil.value == "" || form.teil.value == 0) {
			alert( jserror['enter_participants'] );
		} else if (isNaN(form.teil.value)) {
			alert( jserror['number_participants'] );
		} else if (form.typ.value != 3 && form.typ.value != 5 && form.tiebr2.value != 0 && form.tiebr2.value == form.tiebr1.value) {
			alert( jserror['select_tiebreakers_12'] );
		} else if (form.typ.value != 3 && form.typ.value != 5 && form.tiebr3.value != 0 && form.tiebr3.value == form.tiebr1.value) {
			alert( jserror['select_tiebreakers_13'] );
		} else if (form.typ.value != 3 && form.typ.value != 5 && form.tiebr3.value != 0 && form.tiebr3.value == form.tiebr2.value) {
			alert( jserror['select_tiebreakers_23'] );
		} else {
			Joomla.submitform( pressbutton );
		}
	}
