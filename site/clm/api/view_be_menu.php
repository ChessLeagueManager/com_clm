<?php
// Eingang: Verband
// Ausgang: Alle Vereine in diesem
function clm_api_view_be_menu() {
	$fix = clm_core::$api->db_be_menu();
	$vereine = clm_core::$load->load_view("be_menu",$fix[2]);
	return array(true, "", "<div class='clm'>".$vereine[1]."</div>");
}
?>
