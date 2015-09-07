<?php
// Ausgang: Alle Verbänd
function clm_api_db_units($niceNames = true,$asArray = false) {
	$sql = "SELECT * FROM #__clm_dwz_verbaende";
	$verbaende = clm_core::$db->loadObjectList($sql);
	if($niceNames) {
		for($i=0;$i<count($verbaende);$i++) {
			if(clm_core::$load->ends_with($verbaende[$i]->Verband,"000")) {
				$verbaende[$i]->Verbandname = $verbaende[$i]->Verbandname." (".$verbaende[$i]->Verband.")";
			} else if(clm_core::$load->ends_with($verbaende[$i]->Verband,"00")) {
				$verbaende[$i]->Verbandname = "---".$verbaende[$i]->Verbandname." (".$verbaende[$i]->Verband.")";
			} else if(clm_core::$load->ends_with($verbaende[$i]->Verband,"0")) {
				$verbaende[$i]->Verbandname = "------".$verbaende[$i]->Verbandname." (".$verbaende[$i]->Verband.")";
			} else {
				$verbaende[$i]->Verbandname = "---------".$verbaende[$i]->Verbandname." (".$verbaende[$i]->Verband.")";
			}
		}
	}
	if($asArray) {
		$out = array();
		for($i=0;$i<count($verbaende);$i++) {
			$out[$verbaende[$i]->Verband]=$verbaende[$i]->Verbandname;
		}
		$verbaende = $out;
	}
	return array(true, (count($verbaende) == 0 ? 'w_noAssociationList' : ''), $verbaende);
}
?>
