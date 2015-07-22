<?php
function clm_api_view_report($lid,$rnd,$dg,$paar) {
	$out = clm_core::$api->db_report($lid,$rnd,$dg,$paar);
	if (!$out[0]) {
		if($out[1]=="e_reportError") {
			$out = clm_core::$api->db_report_overview();
			$fix = clm_core::$load->load_view("report_overview", array($out[2]));
			return array(true, $out[1], '<div class="clm">'.$fix[1].'</div>');
		} else if(count($out)==3) {
			return array(false, $out[1], $out[2]);
		} else {
			return array(false, $out[1]);
		}
	}
	$fix = clm_core::$load->load_view("report", array($out[2]));
	return array(true, $out[1], '<div class="clm">'.$fix[1].'</div>');
}
?>
