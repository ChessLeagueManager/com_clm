<?php
function clm_api_view_schedule_pdf($season,$club) {
//echo "<br>club: "; var_dump($club); 		//die('club');
	$out = clm_core::$api->db_schedule($season,$club);
//echo "<br>out: "; var_dump($out); //die('outout');		
	if (!$out[0]) {
		if($out[1]=="e_scheduleError") {
			$out = clm_core::$api->db_report_overview();
			$fix = clm_core::$load->load_view("report_overview", array($out[2]));
			return array(true, $out[1], '<div class="clm">'.$fix[1].'</div>');
		} else if(count($out)==3) {
			return array(false, $out[1], $out[2]);
		} else {
			return array(false, $out[1]);
		}
	}
	$fix = clm_core::$load->load_view("schedule_pdf", array($out[2]));
	return;
	//return array(true, $out[1], '<div class="clm">'.$fix[1].'</div>');
}
?>
