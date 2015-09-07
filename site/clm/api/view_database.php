<?php
function clm_api_view_database() {
$lang1 = clm_core::$lang->dewis_import;
$lang2 = clm_core::$lang->dsb_import;

$fix1 = clm_core::$load->load_view("dewis_import",array());
$fix2 = clm_core::$load->load_view("dsb_import",array());

$dewis_import = clm_core::$load->load_view("spoiler",array($lang1->title,$fix1[1])); // array dereferencing fix php 5.3
$dewis_import = $dewis_import[1]; // array dereferencing fix php 5.3
$dsb_import = clm_core::$load->load_view("spoiler",array($lang2->title,$fix2[1])); // array dereferencing fix php 5.3
$dsb_import = $dsb_import[1]; // array dereferencing fix php 5.3

$output = '<div class="clm"><div class="clm_api_view_database">'.$dewis_import.$dsb_import.'</div></div>';
	
	
return array(true, "",$output);
}
?>
