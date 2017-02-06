<?php
// Input: Liga
// Output: Spielerattribut in dieser Liga genutzt? ja/nein
function clm_api_db_lineup_attr($lid) {
	$lid = clm_core::$load->make_valid($lid, 0, -1);

	$query = "SELECT COUNT(attr) "
			." FROM #__clm_meldeliste_spieler as a "
			." WHERE a.lid = ".$lid;
	$count = clm_core::$db->count($query);

	if ($count < 1)	return false;
	return true;
}
?>
