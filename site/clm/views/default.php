<?php
function clm_view_default($data) {

$load=clm_core::$load;

var_dump($load->is_one_element("600","SELECT COUNT(Verband) FROM #__clm_dwz_verbaende WHERE Verband = ':':SELECT a.Verband AS value, a.Verbandname as name FROM #__clm_dwz_verbaende as a ORDER BY a.Verband ASC"));


echo $load=clm_core::$db->config()->erstauswerter;
clm_core::$db->config()->erstauswerter=119;


clm_core::$load->load_css("box-menu");
$fix = clm_core::$load->load_view("config_overview",array());
echo $fix[1]; // array dereferencing fix php 5.3
}
?>
