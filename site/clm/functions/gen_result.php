<?php
// 0 -> 0 - 1
// 1 -> 1 - 0
// 2 -> 0.5 - 0.5
// 3 -> 0 - 0
// 4 -> - / +
// 5 -> + / -
// 6 -> - / -
// 7 -> ---
// 8 -> spielfrei
// 9 -> 0 - 0,5
//10 -> 0,5 - 0
// Umwandeln des Spielergebnis (Nummer) in die passende Punktzahl
function clm_function_gen_result($result,$kind) {
if(is_null($result)) {
	return -1;
}
switch ($kind) {
	// Für die DWZ Berechnung sind nur erfolgte Spiele relevant, 
	// enstprechende Punktzahl wird zurückgegeben oder Fehler (-2)
   case 0:
			if($result==0) {
				return array(0,1);
			} else if($result==1) {
				return array(1,0);
			} else if($result==2) {
				return array(0.5,0.5);
			} else if($result==9) {
				return array(0,0.5);
			} else if($result==10) {
				return array(0.5,0);
			} else {
				return array(-1,-1);
			}
}

}
// Wird vermutlich bald gebraucht:
/*
		$query='SELECT sieg, antritt, remis, nieder, sid'
			.' FROM #__clm_liga'
			.' WHERE id='.$id;
		$liga = clm_core::$db->loadObjectList($query);
		if	($partien[$i]->punkte == $liga->sieg + $liga->antritt && $partien[$i]->kampflos == 0) { 
			$result = 1; 
		}
		else if ($partien[$i]->punkte == $liga->remis + $liga->antritt && $partien[$i]->kampflos == 0) { 
			$result = 0.5; 
		}
		else if ($partien[$i]->punkte == $liga->nieder + $liga->antritt && $partien[$i]->kampflos == 0) { 
			$result = 0; 
		}
		else { 
			continue; // Keine zu wertende Partie!
		}
*/
?>
