	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
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
		} else {
			submitform( pressbutton );
		}
	}
