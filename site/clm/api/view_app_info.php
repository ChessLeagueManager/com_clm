<?php
function clm_api_view_app_info() {
	$config = clm_core::$db->config();
	$configView = array();
	$configView["https"]=$config->app_security;
	$configView["url"]=str_replace(array("/components/com_clm/clm/","https://","http://"),"",clm_core::$url);
	$out = clm_core::$load->load_view("app_info",array($configView));
	return array(true, "", "<div class='clm'>".$out[1].'</div>');
}
?>