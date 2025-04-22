<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Eingang: Liga-Index

require("index.php");

$view = $_GET["plgview"];
$view = clm_core::$load->make_valid($view, 0, -1);
$lid = $_GET["lid"];
$lid = clm_core::$load->make_valid($lid, 0, -1);
$dg = $_GET["dg"];
$dg = clm_core::$load->make_valid($dg, 0, -1);
$runde = $_GET["runde"];
if ($view != 14) {
    $runde = clm_core::$load->make_valid($runde, 0, -1);
}
$paar = $_GET["paar"];
$paar = clm_core::$load->make_valid($paar, 0, -1);
$error_text = '';
$out = clm_core::$api->db_xml_data($lid, $dg, $runde, $paar, $view);

if (isset($out[0]) and $out[0] === false) {
    $error_text = $out[1];
} else {
    // Variablen initialisieren
    $liga 			= $out[2]["liga"];
    $params = new clm_class_params($liga[0]->params);
    $dwz_date = $params->get("dwz_date", "0000-00-00");
    $a_paar 			= $out[2]["paar"];
    if ($view == 0) {
        $mannschaft		= $out[2]["mannschaft"];
        $results = array();
        foreach ($a_paar as $paar0) {
            $results[$paar0->dg][$paar0->hrank][$paar0->grank] = $paar0->brettpunkte;
        }
        if ($liga[0]->runden_modus != 1 and $liga[0]->runden_modus != 2) {
            $error_text = "PLG_CLM_SHOW_ERR_MODUS_V0";
        }
    }
    if ($view == 1) {
        $mannschaft		= $out[2]["mannschaft"];
        $results = array();
        foreach ($a_paar as $paar0) {
            $results[$paar0->dg][$paar0->hrank][$paar0->grank] = $paar0->brettpunkte;
        }
        if ($liga[0]->runden_modus != 1 and $liga[0]->runden_modus != 2) {
            $error_text = "PLG_CLM_SHOW_ERR_MODUS_V1";
        }
    }
    if ($view == 2 or $view == 4) {
        $mannschaft		= $out[2]["mannschaft"];
        $DWZgespielt2	= $out[2]["DWZgespielt2"];
        $DWZSchnitt2	= $out[2]["DWZSchnitt2"];
        //			$DWZgespielt	= $out[2]["DWZgespielt"];
        //			$DWZSchnitt		= $out[2]["DWZSchnitt"];
        /*			foreach ($DWZgespielt as $oneDWZgespielt) {
                        $arrayDWZ[$oneDWZgespielt->dg][$oneDWZgespielt->runde][$oneDWZgespielt->paar] = (string)round($oneDWZgespielt->dwz);
                        $garrayDWZ[$oneDWZgespielt->dg][$oneDWZgespielt->runde][$oneDWZgespielt->paar] = (string)round($oneDWZgespielt->gdwz);
                    }
                    foreach ($DWZSchnitt as $oneDWZSchnitt) {
                        $marrayDWZ[$oneDWZSchnitt->tlnr] = (string)round($oneDWZSchnitt->dwz);
                    }
        */
    }
    if ($view == 3) {
        $einzel			= $out[2]["einzel"];
        if (count($einzel) == 0) {
            $error_text = "PLG_CLM_SHOW_ERR_EINZEL";
        }
        //			$DWZgespielt	= $out[2]["DWZgespielt"];
        $DWZgespielt2	= $out[2]["DWZgespielt2"];
        $termin			= $out[2]["termin"];
    }
    if ($view == 14) {
        $club			= $out[2]["club"];
        $ligen = array();
        foreach ($liga as $liga0) {
            $ligen[$liga0->id] = $liga0->name;
        }
    }
    $aconfig 			= $out[2]["aconfig"];
}
$dom = new DOMDocument('1.0', 'utf-8');

$root = $dom->createElement('tabelle');
$dom->appendChild($root);

if ($error_text > '') {
    $root->appendChild($ErrorNode = $dom->createElement("error", $error_text));
    $view = 99;
} else {
    $root->appendChild($NameNode = $dom->createElement("tname", $liga[0]->name));
    $root->appendChild($SnameNode = $dom->createElement("sname", $liga[0]->sname));
    $root->appendChild($SidNode = $dom->createElement("sid", $liga[0]->sid));

    // Config-Parameter übergeben
    foreach ($aconfig as $key => $value) {
        $root->appendChild($ClmConfig = $dom->createElement($key, $value));
    }
}
if ($view == 0 or $view == 1) {		// Rangliste (Kreuztabelle/Tabelle)
    $root->appendChild($AufNode = $dom->createElement("auf", $liga[0]->auf));
    $root->appendChild($AufEvtlNode = $dom->createElement("auf_evtl", $liga[0]->auf_evtl));
    $root->appendChild($AbNode = $dom->createElement("ab", $liga[0]->ab));
    $root->appendChild($AbEvtlNode = $dom->createElement("ab_evtl", $liga[0]->ab_evtl));

    $root->appendChild($kreuzHeaderNode = $dom->createElement("kreuzHeader"));
    for ($d = 1; $d <= $liga[0]->durchgang; $d++) {
        for ($r = 1; $r <= $liga[0]->teil; $r++) {
            $kreuzHeaderNode->appendChild($dom->createElement("eH", $r));
        }
    }
    $root->appendChild($ranglisteNode = $dom->createElement("rangliste"));
    //	for ($m = 0; $m <= $liga[0]->runden; $m++) {
    for ($m = 0; $m <= $liga[0]->teil; $m++) {
        if (!isset($mannschaft[$m])) {
            continue;
        }
        if (is_null($mannschaft[$m])) {
            continue;
        }
        $ranglisteNode->appendChild($teamsNode = $dom->createElement("teams"));
        $teamsNode->appendChild($dom->createElement("platz", $mannschaft[$m]->rankingpos));
        $teamsNode->appendChild($dom->createElement("team", $mannschaft[$m]->name));
        $teamsNode->appendChild($kreuzBodyNode = $dom->createElement("kreuzBody"));
        $spiele = 0;
        $siege = 0;
        $unentschieden = 0;
        $verlust = 0;
        for ($d = 1; $d <= $liga[0]->durchgang; $d++) {
            for ($r = 1; $r <= ($liga[0]->runden + 1); $r++) {
                if ($mannschaft[$m]->published == 0) {
                    continue;
                }

                if ($mannschaft[$m]->rankingpos == $r) {
                    $kreuzBodyNode->appendChild($dom->createElement("e", "**"));
                } else {
                    if (!isset($results[$d][$mannschaft[$m]->rankingpos][$r])) {
                        $kreuzBodyNode->appendChild($dom->createElement("e", ' '));
                    } else {
                        $kreuzBodyNode->appendChild($dom->createElement("e", $results[$d][$mannschaft[$m]->rankingpos][$r]));
                        if (is_numeric($results[$d][$mannschaft[$m]->rankingpos][$r])) {
                            $spiele++;
                            if ($results[$d][$mannschaft[$m]->rankingpos][$r] > $liga[0]->stamm / 2) {
                                $siege++;
                            }
                            if ($results[$d][$mannschaft[$m]->rankingpos][$r] == $liga[0]->stamm / 2) {
                                $unentschieden++;
                            }
                            if ($results[$d][$mannschaft[$m]->rankingpos][$r] < $liga[0]->stamm / 2) {
                                $verlust++;
                            }
                        }
                    }
                }
            }
        }
        $teamsNode->appendChild($dom->createElement("count_G", $mannschaft[$m]->count_G));
        $teamsNode->appendChild($dom->createElement("count_S", $mannschaft[$m]->count_S));
        $teamsNode->appendChild($dom->createElement("count_R", $mannschaft[$m]->count_R));
        $teamsNode->appendChild($dom->createElement("count_V", $mannschaft[$m]->count_V));
        if (is_null($mannschaft[$m]->mp)) {
            $mannschaft[$m]->mp = '';
        }
        $teamsNode->appendChild($dom->createElement("mp", $mannschaft[$m]->mp));
        if (is_null($mannschaft[$m]->bp)) {
            $mannschaft[$m]->bp = '';
        }
        $teamsNode->appendChild($dom->createElement("bp", $mannschaft[$m]->bp));
    }
}
if ($view == 2 or $view == 4 or $view == 14) {		// Paarungsliste / Spielplan Mannschaft o. Verein
    if ($view == 14) {
        $root->appendChild($ClubNode = $dom->createElement("cname", $club[0]->name));
    }
    $root->appendChild($ranglisteNode = $dom->createElement("paarungsliste"));
    foreach ($a_paar as $paar0) {
        $ranglisteNode->appendChild($teamsNode = $dom->createElement("paarung"));
        if ($view == 14) {
            if (isset($ligen[$paar0->lid])) {
                $liga_name = $ligen[$paar0->lid];
            } else {
                $liga_name = 'unbekannt';
            }
            $teamsNode->appendChild($dom->createElement("lname", $liga_name));
        }
        $teamsNode->appendChild($dom->createElement("paar", $paar0->paar));
        $teamsNode->appendChild($dom->createElement("tln_nr", $paar0->tln_nr));
        $teamsNode->appendChild($dom->createElement("hname", $paar0->hname));
        $teamsNode->appendChild($dom->createElement("gtln", $paar0->gtln));
        $teamsNode->appendChild($dom->createElement("gname", $paar0->gname));
        $teamsNode->appendChild($dom->createElement("dg", $paar0->dg));
        $teamsNode->appendChild($dom->createElement("runde", $paar0->runde));
        if (is_null($paar0->brettpunkte)) {
            $paar0->brettpunkte = '';
        }
        $teamsNode->appendChild($dom->createElement("brettpunkte", $paar0->brettpunkte));
        if (is_null($paar0->gbrettpunkte)) {
            $paar0->gbrettpunkte = '';
        }
        $teamsNode->appendChild($dom->createElement("gbrettpunkte", $paar0->gbrettpunkte));
        if ($view != 14) {
            /*			if (isset($arrayDWZ[$paar0->dg][$paar0->runde][$paar0->paar]))
                            $o_dwz = $arrayDWZ[$paar0->dg][$paar0->runde][$paar0->paar];
                        else
                            if (isset($marrayDWZ[$paar0->tln_nr])) $o_dwz = $marrayDWZ[$paar0->tln_nr];
                            else $o_dwz = '';
            */
            if (isset($DWZgespielt2[$paar0->dg][$paar0->runde][$paar0->tln_nr])
                and $DWZgespielt2[$paar0->dg][$paar0->runde][$paar0->tln_nr] != '-'
                and $paar0->tln_nr != 0 and $paar0->gtln != 0) {
                $o_dwz = $DWZgespielt2[$paar0->dg][$paar0->runde][$paar0->tln_nr];
            } else {
                $o_dwz = $DWZSchnitt2[$paar0->tln_nr];
            }
            $teamsNode->appendChild($dom->createElement("dwz", $o_dwz));
            /*			if (isset($garrayDWZ[$paar0->dg][$paar0->runde][$paar0->paar]))
                            $o_gdwz = $garrayDWZ[$paar0->dg][$paar0->runde][$paar0->paar];
                        else
                            if (isset($marrayDWZ[$paar0->gtln])) $o_gdwz = $marrayDWZ[$paar0->gtln];
                            else $o_gdwz = '';
            */
            if (isset($DWZgespielt2[$paar0->dg][$paar0->runde][$paar0->gtln])
                and $DWZgespielt2[$paar0->dg][$paar0->runde][$paar0->gtln] != '-'
                and $paar0->tln_nr != 0 and $paar0->gtln != 0) {
                $o_gdwz = $DWZgespielt2[$paar0->dg][$paar0->runde][$paar0->gtln];
            } else {
                $o_gdwz = $DWZSchnitt2[$paar0->tln_nr];
            }
            $teamsNode->appendChild($dom->createElement("gdwz", $o_gdwz));
        }
        $teamsNode->appendChild($dom->createElement("rname", $paar0->rname));
        if ($paar0->pdate > '1970-01-01') {
            $paar0->rdatum = $paar0->pdate;
            $paar0->startzeit = $paar0->ptime;
        }
        $teamsNode->appendChild($dom->createElement("rdatum", $paar0->rdatum));
        $teamsNode->appendChild($dom->createElement("startzeit", $paar0->startzeit));
        $teamsNode->appendChild($dom->createElement("comment", html_entity_decode($paar0->comment)));
    }
}
if ($view == 3) {		// Paarung
    $root->appendChild($HnameNode = $dom->createElement("hname", $a_paar[0]->hname));
    $root->appendChild($GnameNode = $dom->createElement("gname", $a_paar[0]->gname));
    $root->appendChild($HbpNode = $dom->createElement("hbp", $a_paar[0]->brettpunkte));
    $root->appendChild($GbpNode = $dom->createElement("gbp", $a_paar[0]->gbrettpunkte));
    //	if ($dwz_date == '0000-00-00' OR $dwz_date == '1970-01-01') $hdwz = round($DWZgespielt[0]->dwz);
    //	else $hdwz = round($DWZgespielt[0]->start_dwz);
    $hdwz = $DWZgespielt2[$a_paar[0]->dg][$a_paar[0]->runde][$a_paar[0]->htln];
    $root->appendChild($HdwzNode = $dom->createElement("hdwz", $hdwz));
    //	if ($dwz_date == '0000-00-00' OR $dwz_date == '1970-01-01') $gdwz = round($DWZgespielt[0]->gdwz);
    //	else $gdwz = round($DWZgespielt[0]->gstart_dwz);
    $gdwz = $DWZgespielt2[$a_paar[0]->dg][$a_paar[0]->runde][$a_paar[0]->gtln];
    $root->appendChild($GdwzNode = $dom->createElement("gdwz", $gdwz));
    $root->appendChild($RnameNode = $dom->createElement("rname", $termin[0]->name));

    $root->appendChild($EinzelBodyNode = $dom->createElement("einzelBody"));
    for ($b = 0; $b < $liga[0]->stamm; $b++) {
        $EinzelBodyNode->appendChild($brettNode = $dom->createElement("brett"));
        $brettNode->appendChild($dom->createElement("brett", $einzel[$b]->brett));
        $brettNode->appendChild($dom->createElement("hname", $einzel[$b]->hname));
        $brettNode->appendChild($dom->createElement("gname", $einzel[$b]->gname));
        $brettNode->appendChild($dom->createElement("erg_text", $einzel[$b]->erg_text));
        if (is_null($einzel[$b]->hdwz) or $einzel[$b]->hdwz == 0) {
            $hdwz = '-';
        } else {
            $hdwz = $einzel[$b]->hdwz;
        }
        $brettNode->appendChild($dom->createElement("hdwz", $hdwz));
        if (is_null($einzel[$b]->gdwz) or $einzel[$b]->gdwz == 0) {
            $gdwz = '-';
        } else {
            $gdwz = $einzel[$b]->gdwz;
        }
        $brettNode->appendChild($dom->createElement("gdwz", $gdwz));
    }
}
header('Content-type: text/xml; charset=utf-8');
echo $dom->saveXML();
