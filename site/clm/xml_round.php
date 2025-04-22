<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 *
 * Bestimmung aktuelle Runde auf Basis Liga-Ingex
*/
// Eingang: Liga-Index
// Ausgang: aktuelle Runde einschl. Durchgang

require("index.php");
$lid = $_GET["lid"];
$lid = clm_core::$load->make_valid($lid, 0, -1);
$error_text = '';
$out = clm_core::$api->db_xml_round($lid);

if (isset($out[0]) and $out[0] === false) {
    $error_text = $out[1];
} else {
    // Variablen initialisieren
    $lid 			= $out[2]["lid"];
    $runde 			= $out[2]["runde"];
    $dg 			= $out[2]["dg"];
}
$dom = new DOMDocument('1.0', 'utf-8');

$root = $dom->createElement('tabelle');
$dom->appendChild($root);

if ($error_text > '') {
    $root->appendChild($ErrorNode = $dom->createElement("error", $error_text));
    $view = 99;
} else {
    $root->appendChild($LidNode = $dom->createElement("lid", $lid));
    $root->appendChild($RundeNode = $dom->createElement("runde", $runde));
    $root->appendChild($DgNode = $dom->createElement("dg", $dg));

}

header('Content-type: text/xml; charset=utf-8');
echo $dom->saveXML();
