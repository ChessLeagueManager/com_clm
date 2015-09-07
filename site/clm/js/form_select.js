function clm_form_select(object)
{
	var input = object.value.toLowerCase();
   len = input.length;
   output = object.parentElement.getElementsByClassName("clm_view_form_select_options")[0].options;
   for(var i=0; i<output.length; i++) {
		if (output[i].text.toLowerCase().indexOf(input) != -1 ){
			output[i].selected = true;
			break;
		}
	}
   if (input == '') {
        output[0].selected = true;
   }
}