<?php
function clm_api_view_config() {
	$config_order = clm_core::$db->config_order();
	$data = clm_core::$api->db_config_get($config_order,true);
	$data = $data[2]; // array dereferencing fix php 5.3
	$part = '<div class="clm"><div class="clm_api_view_config">';
	$fix= clm_core::$load->load_view("config", array($data,true));
	$part.= $fix[1]; // array dereferencing fix php 5.3
	$part.= "</div></div>";
	return array(true, "", $part);
}
?>
