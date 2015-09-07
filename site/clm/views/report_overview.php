<?php
function clm_view_report_overview($liga) {
	$lang = clm_core::$lang->report_overview;
	clm_core::$load->load_css("report_overview");
	$now = date('Y-m-d H:i:s');
	$c_rang = 0;
	$c_lid = 0;
	$c_tln_nr = 0;
	$something = false;
	echo '<div class="element">';
	foreach ($liga as $liga) {
		// Wenn NICHT gemeldet oder noch Zeit zu korrigieren dann Runde anzeigen
		$mdt = $liga->deadlineday . ' ';
		$mdt.= $liga->deadlinetime;
		if (($liga->gemeldet < 1 || $mdt >= $now) && ($liga->liste > 0 || ($liga->rang == 1 && isset($liga->gid)))) {
			if (!($liga->meldung == 0)) {
				$something = true; 
				// Abständ, nur im ersten Durchlauf nicht notwendig
				if($c_rang!=0) {
					echo '<br/>';				
				}
				if ($c_rang != $liga->rang || $c_lid != $liga->lid || $c_tln_nr != $liga->tln_nr) {
					echo "<h4>" . $liga->name;
					if (1 == 1) {
						echo ' - ' . $liga->lname;
					}
					echo '</h4>';
					$c_rang = $liga->rang;
					$c_lid = $liga->lid;
					$c_tln_nr = $liga->tln_nr;
				}
				echo '<a class="link" href="' . clm_core::$load->gen_url(array("liga" => $liga->liga, "runde" => $liga->runde, "dg" => $liga->dg, "paar" => $liga->paar)) . '">';
				echo $lang->round . " " . ($liga->runde);
				if (($liga->dg == 1) and ($liga->durchgang == 2)) echo " " . $lang->first;
				if (($liga->dg == 2) and ($liga->durchgang == 2)) echo " " . $lang->second;
				if (($liga->dg >= 1) and ($liga->durchgang > 2)) echo " " . $lang->passage . " " .$liga->dg;
				echo '</a>';
			}
		}
	}
	echo '</div>';
	if(!$something) {
		clm_core::$load->load_css("notification");
		echo "<div class='clm_view_notification'><div class='warning'>".$lang->nothing."</div></div>";
	}
}
?>
