<?php
// Ausgang: Alle Verbände
function clm_api_view_units_form($name,$selected=null) {
	$out = clm_core::$api->db_units();
	$out = $out[2]; // array dereferencing fix php 5.3
	$value = array();
	$text = array ();
	for($i=0;$i<count($out);$i++) {
		$value[$i] = $out[$i]->Verband;
		$text[$i] = $out[$i]->Verbandname;
	}	
	$out = clm_core::$load->load_view("form_select",array($name,$value,$text,$selected));
	$out = $out[1]; // array dereferencing fix php 5.3
	return array(true, (count($out) == 0 ? 'w_noAssociationList' : ''), $out);
}
?>
