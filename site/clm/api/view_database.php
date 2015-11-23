<?php
function clm_api_view_database() {
$config = clm_core::$db->config();

if ($config->countryversion =="de") { // direct update only for Germany
$lang1 = clm_core::$lang->dewis_import;
$fix1 = clm_core::$load->load_view("dewis_import",array());
$dewis_import = clm_core::$load->load_view("spoiler",array($lang1->title,$fix1[1])); // array dereferencing fix php 5.3
$dewis_import = $dewis_import[1]; // array dereferencing fix php 5.3
} else {
$dewis_import = "";
}
if ($config->countryversion =="de") { // for Germany
$lang2 = clm_core::$lang->dsb_import;
$fix2 = clm_core::$load->load_view("dsb_import",array());
$country_import = clm_core::$load->load_view("spoiler",array($lang2->title,$fix2[1])); // array dereferencing fix php 5.3
$country_import = $country_import[1]; // array dereferencing fix php 5.3
}
if ($config->countryversion =="en") { // for Great Britain
$lang2 = clm_core::$lang->ecf_import;
$fix2 = clm_core::$load->load_view("ecf_import",array());
$country_import = clm_core::$load->load_view("spoiler",array($lang2->title,$fix2[1])); // array dereferencing fix php 5.3
$country_import = $country_import[1]; // array dereferencing fix php 5.3
}

$output = '<div class="clm"><div class="clm_api_view_database">'.$dewis_import.$country_import.'</div></div>';
	
	
return array(true, "",$output);
}
?>
