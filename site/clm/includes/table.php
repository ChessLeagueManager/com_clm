<?php
defined('clm') or die('Restricted access');
// **** Tabellen und ihre unique Spalten definieren **** //
// unique Feld, Typ des Feldes, AUTO_INCREMENT
$table["config"] = array("id", "i",false);
$table["usertype"] = array("id", "i",true);
$table["user"] = array("id", "i",true);
$table["saison"] = array("id", "i",true);
$table["turniere"] = array("id", "i",true);
$table["turniere_tlnr"] = array("id", "i",true);
$table["liga"] = array("id", "i",true);
$table["dwz_vereine"] = array("ZPS", "s",false);
$table["dwz_verbaende"] = array("Verband", "s",false);
$table["mannschaften"] = array("id", "i",true);
$table["logging"] = array("id", "i",true);
?>
