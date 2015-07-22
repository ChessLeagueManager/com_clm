	function submitbutton(pressbutton) {
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
