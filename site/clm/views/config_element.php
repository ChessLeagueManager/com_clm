<?php
function clm_view_config_element($number,$name,$now,$configExtern=null) {
	$lang = clm_core::$lang->config_element;
	clm_core::$load->load_css("config_element");
	clm_core::$load->load_js("is");
	clm_core::$load->load_js("config_element");
	clm_core::$load->load_css("modal");
	clm_core::$load->load_js("modal");
	$config = clm_core::$db->config()->getConfig();
	$config = $config[$name][3]; // array dereferencing fix php 5.3

	$name_hint = $name."_hint";
	if($lang->exist($name_hint))
	{
		//echo $lang->$name_hint;
		//die();
		$text = '<div class="clm_view_config_text"><p><a onclick="clm_modal_display('."'".$lang->$name_hint."'".')" href="javascript:;">' . $lang->$name . '</a></div>';
	} else {
		$text = '<div class="clm_view_config_text">'. $lang->$name . '</div>';
	}


	// Auswahl zwischen festgelegten Elementen
	// bsp. $config["menue"]=array(2,9,1,array(0,1));
	if ($number == 9) {
		echo '<div class="clm_view_config_9">'.$text.'<select onchange='."'".'clm_config_element_data_change("' . $name . '",this.options[this.selectedIndex].value,this);'."'".' name="' . $name . '" size="1">';
		for ($i = 0;$i < count($config);$i++) {
			$langTag = $name . "_element_" . $config[$i];
			echo '<option value="' . $config[$i] . '"' . ($config[$i] == $now ? " selected" : "") . '>' . $lang->$langTag . '</option>';
		}
		echo '</select></div>';
	} 
	// Auswahl zwischen festgelegten Elementen (mithilfe von SQL)
	// bsp. $config["lv"]=array(1,11,-1,array("SELECT Count(Verband)FROM #__clm_dwz_verbaende WHERE Verband = '","'","SELECT a.Verband AS value, a.Verbandname as name FROM #__clm_dwz_verbaende as a ORDER BY a.Verband ASC"));
	else if ($number == 11) {
		// Laden der Auswahl --> value/name
		echo '<div class="clm_view_config_11">'.$text.'<select onchange='."'".'clm_config_element_data_change("' . $name . '",this.options[this.selectedIndex].value,this);'."'".' name="' . $name . '" size="1">';
		foreach ($configExtern[2] as $key => $value) {
			echo '<option value="' . $key . '"' . ($key == $now ? " selected" : "") . '>' . $value . '</option>';
		}
		echo '</select></div>';
	}
	else
	{
		if($number==4)
		{
			clm_core::$load->load_js("jscolor");
		}
		echo '<div class="clm_view_config_'.$number.'">'.$text;
		echo '<input '.($number==4 ? 'class="clm_color_picker" ' : '').($number==4 ? 'onchange' : 'onkeyup') .'='."'".($number==12 ? 'clm_config_element_email(this,this.value,"' . $name . '");' : ($number==13 ? 'clm_config_element_length(this,this.value,"' . $name . '");' : 'clm_config_element_data_change("' . $name . '",this.value,this);'))."'".' name="' . $name . '" type="text" size="30" value="' . $now .'"></div>';
	}

}
?>
