<?php
function clm_api_db_tournament_player_del($id,$player) {
	$id = clm_core::$load->make_valid($id, 0, -1);
	if(!is_array($player) || count($player)==0) {
		return array(false,"e_unexpectedInput");
	}

	$playerNo=false;
	$playerUsed=false;

	for($i=0;$i<count($player);$i++) {
		$playerId = intval($player[$i]);
		if(!clm_core::$db->turniere_tlnr->get($playerId)->isNew())
		{
			$snr = clm_core::$db->turniere_tlnr->get($playerId)->snr;
			// Lese alle beteiligten Spieler aus
			$query='SELECT COUNT(id)'
				. ' FROM #__clm_turniere_rnd_spl'
				. ' WHERE (spieler = '. $snr . ' OR gegner = '.$snr.")"
				. ' AND turnier = '. $id;
			$count = clm_core::$db->count($query);
			if($count>0) {
				$playerUsed=true;	
			} else {
				clm_core::$db->turniere->get($id)->teil=clm_core::$db->turniere->get($id)->teil-1;
				$query='DELETE FROM #__clm_turniere_tlnr WHERE id = '.$playerId;
				clm_core::$db->query($query);
			}
		} else {
			$playerNo=true;
		}
	}
	if($playerNo) {
		return array(true, "w_tournamentPlayerDeleteNoPlayer"); 
	}
	if($playerUsed) {
		return array(true,"w_tournamentPlayerDeleteUsed");
	}
	return array(true,"m_tournamentPlayerDeleteSuccess");
}
?>
