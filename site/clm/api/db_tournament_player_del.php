<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
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
				$query='UPDATE #__clm_turniere_tlnr SET snr=snr-1 WHERE snr>' . $snr . ' AND turnier='.$id;
				clm_core::$db->query($query);
				$query='DELETE FROM #__clm_turniere_rnd_spl WHERE tln_nr = ' . $snr . ' AND turnier='.$id;
				clm_core::$db->query($query);
				$query='UPDATE #__clm_turniere_rnd_spl SET tln_nr=tln_nr-1 WHERE tln_nr>' . $snr . ' AND turnier='.$id;
				clm_core::$db->query($query);
				$query='UPDATE #__clm_turniere_rnd_spl SET spieler=spieler-1 WHERE spieler>' . $snr . ' AND turnier='.$id;
				clm_core::$db->query($query);
				$query='UPDATE #__clm_turniere_rnd_spl SET gegner=gegner-1 WHERE gegner>' . $snr . ' AND turnier='.$id;
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
