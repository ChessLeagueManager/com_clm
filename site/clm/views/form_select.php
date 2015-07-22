<?php
function clm_view_form_select($name, $value, $text,$selected = null) {
	clm_core::$load->load_js("form_select");
	clm_core::$load->load_css("form_select");
	echo '<input type="text" onclick="clm_form_select(this);" onkeyup="clm_form_select(this);"/>
';
	echo '<br/>
';
	echo '<select class="clm_view_form_select_options" '.($name!="" ? 'name="' . $name . '"' : "").'size="1" >
';
	for ($i = 0;$i < count($value);$i++) {
		echo '<option value="' . $value[$i] . '"'.($selected != null && $selected == $value[$i] ? "selected" : "").'>' . $text[$i] . '</option>
';
	}
	echo '</select>
';
} ?>
