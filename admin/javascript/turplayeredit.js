	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
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
			submitform( pressbutton );
		}
	}
