<?php
function clm_view_config($elements,$base=false) {
clm_core::$load->load_css("config");
clm_core::$load->load_css("buttons");
clm_core::$load->load_css("notification");
$lang = clm_core::$lang->config;
if($base)
{
	$part='<div class="clm_view_config_button_container"><div class="clm_view_config_button_container_flex"><div class="clm_view_config_flex"></div><button type="button" id="'.clm_core::$id->view_config_button.'" class="clm_view_config_button clm_button" onclick="clm_config_element_save(this)" title="'.$lang->button_hint.'" disabled>'.$lang->button_text.'</button><div class="clm_view_config_flex"></div><button type="button" class="clm_view_config_button clm_button" onclick="window.location.reload();" title="'.$lang->button_reload_hint.'">'.$lang->button_reload_text.'</button><div class="clm_view_config_flex"></div><button type="button" class="clm_view_config_button clm_button" onclick="clm_config_element_reset(this)" title="'.$lang->button_reset_hint.'">'.$lang->button_reset_text.'</button><div class="clm_view_config_flex"></div></div><div class="clm_view_notification"></div></div>';
} else {
	$part="";
}

	for ($i=0;$i<count($elements);$i++)
	{
		if($i==0 && !$base) {
			$i=2;
		}
		if(count($elements[$i])==1)
		{
			$fix= clm_core::$load->load_view("config",array($elements[$i][0]));
			$part.= $fix[1]; // array dereferencing fix php 5.3
		} else  {
			$fix = clm_core::$load->load_view("config_element", array($elements[$i][0],$elements[$i][1],$elements[$i][2],$elements[$i][3]));
			$part.=  $fix[1]; // array dereferencing fix php 5.3
		}
	}
	if($base) {
		echo $part;
	} else {
		$name_info = $elements[0]."_info";
		$fix = clm_core::$load->load_view("spoiler", array($lang->{$elements[0]}, ($lang->exist($name_info) ? '<div class="clm_view_config_info">'.$lang->$name_info."</div>" : "").$part,$elements[1]));
		echo $fix[1]; // array dereferencing fix php 5.3
	}
} 
?>
