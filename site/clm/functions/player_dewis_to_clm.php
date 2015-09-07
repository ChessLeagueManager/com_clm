<?php
// Anpassen der von DeWIS übertragenen Daten in die normale CLM Tabelle
function clm_function_player_dewis_to_clm($dsbnachname, $dsbvorname, $dsbmglnr, $dsbgeschlecht, $dsbfideid, $dsbfideland) {
	// Die Mitgliedsnummer müssen mindestens dreistellig sein, mit führenden Nullen auffüllen
	if (strlen($dsbmglnr) == 1) {
		$dsbmglnr = "00" . $dsbmglnr;
	} elseif (strlen($dsbmglnr) == 2) {
		$dsbmglnr = "0" . $dsbmglnr;
	}
	// CLM trennt die Namen der Spieler nicht in vor und Nachname
	if(is_null($dsbvorname))
	{
		$clmName = $dsbnachname;
	} else {
		$clmName = $dsbnachname . "," . $dsbvorname;
	}
	$clmNameG = strtoupper($clmName);
	$search = array("ä", "ö", "ü", "ß", "é");
	$replace = array("AE", "OE", "UE", "SS", "É");
	$clmNameG = str_replace($search, $replace, $clmNameG);
	// Geschlecht besitzt andere Zeichen
	if ($dsbgeschlecht == 'm') $dsbgeschlecht = 'M';
	if ($dsbgeschlecht == 'f') $dsbgeschlecht = 'W';
	// Ohne FideId keine FideLand
	if ($dsbfideid == '' OR $dsbfideid == '0') $dsbfideland = '';
	return array($dsbmglnr, $clmName, $clmNameG, $dsbgeschlecht, $dsbfideland);
}
?>
