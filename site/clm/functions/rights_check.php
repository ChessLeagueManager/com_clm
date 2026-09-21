<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
// Rechteprüfung unter Einbeziehung von clm_arbiter_turnier
// Parameter: 
//  Kennzeichen für Art der Prüfung z.B. TL, SL, ...
//  Id des Wettbewerbs (Einzelturnier oder Mannschaftswettbewerb)
//  möglicher Aufruf: clm_core::$load->rights_check('TL',$row->id)

	function clm_function_rights_check($flag,$turnier) {
		$turnier = clm_core::$load->make_valid($turnier, 0, -1);
		$clmAccess = clm_core::$access;
		$jid = $clmAccess->getJid();
		if ($flag == 'TL') {
			$query = 'SELECT * FROM #__clm_turniere'
			.' WHERE id = '.$turnier;
			$result	= clm_core::$db->loadObject($query);
			if (isset($result->tl) AND $result->tl == $jid) return true;
			if (isset($result->torg) AND $result->torg == $jid) return true;
			$query = 'SELECT * FROM #__clm_arbiter_turnier'
				." WHERE turnier = $turnier "
				." AND trole = 'T' AND role <> 'KA' ";
			$result	= clm_core::$db->loadObjectList($query);
			foreach ($result as $res) {
				if (isset($res->fideid) AND $res->fideid == $jid) return true;
			}
		} elseif ($flag == 'SL') {
			$query = 'SELECT * FROM #__clm_liga'
			.' WHERE id = '.$turnier;
			$result	= clm_core::$db->loadObject($query);
			if (isset($result->sl) AND $result->sl == $jid) return true;
			$query = 'SELECT * FROM #__clm_arbiter_turnier'
				." WHERE liga = $turnier "
				." AND trole = 'T' AND role <> 'KA' ";
			$result	= clm_core::$db->loadObjectList($query);
			foreach ($result as $res) {
				if (isset($res->fideid) AND $res->fideid == $jid) return true;
			}
		}
		return false;
	}
?>
