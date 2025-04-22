<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

// Variablen ohne foreach setzen
$liga	= $this->liga;
if ($liga[0]->rang == 0) {
    $ms = true;
} else {
    $ms = false;
}
$paar = $this->paar;
$paar1 = $this->paar1;
$einzel = $this->einzel;
$summe = $this->summe;
$ok = $this->ok;
//Liga-Parameter aufbereiten
$paramsStringArray = explode("\n", $liga[0]->params);
$params = array();
foreach ($paramsStringArray as $value) {
    $ipos = strpos($value, '=');
    if ($ipos !== false) {
        $params[substr($value, 0, $ipos)] = substr($value, $ipos + 1);
    }
}
if (!isset($params['round_date'])) {
    $params['round_date'] = '0';
}

// Variblen aus URL holen
$sid 		= clm_core::$load->request_int('saison', 1);
$runde		= clm_core::$load->request_int('runde', 1);
$dg		= clm_core::$load->request_int('dg', 1);
$a_html = array('<b>','</b>');
$a_pdf  = array('','');

$runde_t = $runde + (($dg - 1) * $liga[0]->runden);
// Test alte/neue Standardrundenname bei 2 Durchgängen, nur bei Ligen/Turniere vor 2013 (Archiv!)
if ($liga[$runde_t - 1]->datum < '2013-01-01') {
    if ($liga[0]->durchgang > 1) {
        if ($liga[$runde_t - 1]->rname == JText::_('ROUND').' '.$runde_t) {  //alt
            if ($dg == 1) {
                $liga[$runde_t - 1]->rname = JText::_('ROUND').' '.$runde." (".JText::_('PAAR_HIN').")";
            }
            if ($dg == 2) {
                $liga[$runde_t - 1]->rname = JText::_('ROUND').' '.$runde." (".JText::_('PAAR_RUECK').")";
            }
        }
    }
}

if ((isset($ok[0]->sl_ok)) and ($ok[0]->sl_ok > 0)) {
    $hint_freenew = JText::_('CHIEF_OK');
}
if ((isset($ok[0]->sl_ok)) and ($ok[0]->sl_ok == 0)) {
    $hint_freenew = JText::_('CHIEF_NOK');
}
if ((!isset($ok[0]->sl_ok))) {
    $hint_freenew = JText::_('CHIEF_NOK');
}

$runden_modus = $liga[0]->runden_modus;
//require_once(JPATH_COMPONENT.DS.'includes'.DS.'rotation.php');
require_once(clm_core::$path.DS.'classes'.DS.'rotation.php');

//class PDF extends FPDF
class PDF extends PDF_Rotate
{
    //Kopfzeile
    public function Header()
    {
        require(clm_core::$path.DS.'includes'.DS.'pdf_header.php');
    }
    //Fusszeile
    public function Footer()
    {
        require(clm_core::$path.DS.'includes'.DS.'pdf_footer.php');
    }

    public function RotatedText($x, $y, $txt, $angle)
    {
        //Text rotated around its origin
        $this->Rotate($angle, $x, $y);
        $this->Text($x, $y, $txt);
        $this->Rotate(0);
    }

    public function RotatedImage($file, $x, $y, $w, $h, $angle)
    {
        //Image rotated around its upper-left corner
        $this->Rotate($angle, $x, $y);
        $this->Image($file, $x, $y, $w, $h);
        $this->Rotate(0);
    }
}

// Konfigurationsparameter auslesen
$config = clm_core::$db->config();
$show_sl_mail = $config->show_sl_mail;

// Userkennung holen
$user	= JFactory::getUser();
$jid	= $user->get('id');

// Zellenhöhe -> Standard 6
$zelle = 4;
// Zellenbreiten
$breite1 = 38;  //name
$breite = 89;   //spalte
// Seitenlänge
$lspalte_paar = 230;
$lspalte_tab = 210;
if ($liga[0]->teil > 10) {
    $lspalte_tab = $lspalte_tab - (($liga[0]->teil - 10) * 8);
}
$lspalte_comment = 200;
$lspalte = 240;
// Überschrift Fontgröße Standard = 12
$head_font = 12;
// Fontgröße Standard = 10
$font = 10;
// Erstellungsdatum Fontgröße Standard = 8
$date_font = 8;

// Datum der Erstellung
$date = JFactory::getDate();
$now = $date->toSQL();

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Times', '', $date_font);
$pdf->Cell(10, 4, ' ', 0, 0);
$pdf->Cell(175, 4, clm_core::$load->utf8decode(JText::_('WRITTEN')).' '.clm_core::$load->utf8decode(JText::_('ON_DAY')).' '.clm_core::$load->utf8decode(JHTML::_('date', $now, JText::_('DATE_FORMAT_CLM_PDF'))), 0, 1, 'R');

if (!$liga or $liga[0]->published == "0") {
    $pdf->SetFont('Times', '', $font + 4);
    $pdf->Cell(10, 15, clm_core::$load->utf8decode($liga[0]->name." ".$liga[0]->saison_name.', Runde '.$runde), 0, 1);
    $pdf->Cell(10, 15, clm_core::$load->utf8decode(JText::_('NOT_PUBLISHED')), 0, 0);
    $pdf->Ln();
    $pdf->Cell(10, 15, clm_core::$load->utf8decode(JText::_('GEDULD')), 0, 0);
} elseif ($liga[0]->rnd == 0) {
    $pdf->SetFont('Times', '', $font + 4);
    $pdf->Cell(10, 15, clm_core::$load->utf8decode($liga[0]->name." ".$liga[0]->saison_name.', Runde '.$runde), 0, 1);
    $pdf->Cell(10, 15, clm_core::$load->utf8decode(JText::_('NO_ROUND_CREATED')), 0, 1);
    $pdf->Ln();
    $pdf->Cell(10, 15, clm_core::$load->utf8decode(JText::_('NO_ROUND_CREATED_HINT')), 0, 0);
} else {


    // Rundentext anpassen
    $runden_text = $liga[$runde_t - 1]->rname;
    if ($dg > 1) {
        $runde = $runde + $liga[0]->runden;
    }

    $pdf->SetFont('Times', '', $head_font);
    $pdf->Cell(10, 6, ' ', 0, 0);
    $pdf->Cell(170, 6, clm_core::$load->utf8decode($liga[0]->name)." ".$liga[0]->saison_name, 0, 1, 'C');
    $pdf->SetFont('Times', '', $head_font - 1);
    $pdf->Cell(10, 5, ' ', 0, 0);
    if ($liga[$runde - 1]->datum > 0) {
        $pdf_title = clm_core::$load->utf8decode($runden_text).' '.JText::_('ON_DAY').' '.clm_core::$load->utf8decode(JHTML::_('date', $liga[$runde - 1]->datum, JText::_('DATE_FORMAT_CLM_F')));
        if (isset($liga[$runde - 1]->startzeit) and $liga[$runde - 1]->startzeit != '00:00:00') {
            $pdf_title .= '  '.substr($liga[$runde - 1]->startzeit, 0, 5).' Uhr';
        }
        $pdf->Cell(170, 5, $pdf_title, 0, 1, 'C');
    } else {
        $pdf->Cell(170, 5, clm_core::$load->utf8decode($runden_text), 0, 1, 'C');
    }
    $pdf->SetFont('Times', '', $font);
    $pdf->Cell(10, 3, ' ', 0, 1);
    $pdf->Cell(10, $zelle, ' ', 0, 0);
    // Freigabe durch Staffelleiter oder nicht
    if (isset($liga[0]->mf_name)) {
        $pdf->Cell(175, $zelle - 1, clm_core::$load->utf8decode($hint_freenew), 0, 1, 'R');
    }
    $pdf->Ln();
    // Teilnehmerschleife
    $erg0 = substr((string)($liga[0]->nieder + $liga[0]->antritt), 0, 1)."-".substr((string)($liga[0]->sieg + $liga[0]->antritt), 0, 1);
    $erg1 = substr((string)($liga[0]->sieg + $liga[0]->antritt), 0, 1)."-".substr((string)($liga[0]->nieder + $liga[0]->antritt), 0, 1);
    if (($liga[0]->remis + $liga[0]->antritt) == 0.5) {
        $erg2 = chr(189);
        $erg9 = "0-".chr(189);
        $erg10 = chr(189)."-0";
    } else {
        $erg2 = substr((string)($liga[0]->remis + $liga[0]->antritt), 0, 1)."-".substr((string)($liga[0]->remis + $liga[0]->antritt), 0, 1);
        $erg9 = "0-".substr((string)($liga[0]->remis + $liga[0]->antritt), 0, 1);
        $erg10 = substr((string)($liga[0]->remis + $liga[0]->antritt), 0, 1)."-0";
    }
    $w = 0;
    $z2 = 0;
    $zz = 0;
    $xx0 = $pdf->GetX();
    $yy0 = $pdf->GetY();
    $xx1 = 16; // linke Spalte
    $xx2 = 107; // rechte Spalte
    $cr = false;
    for ($y = 0; $y < ($liga[0]->teil) / 2; $y++) {
        if (!isset($paar[$y])) {
            break;
        }
        if ($pdf->GetY() > $lspalte_paar) {
            if (!$cr) {
                $pdf->SetY($yy0);
                $cr = true;
            } else {
                $pdf->AddPage();
                $pdf->SetFont('Times', '', $date_font);
                $pdf->Cell(10, 4, ' ', 0, 0);
                $pdf->Cell(175, 4, clm_core::$load->utf8decode(JText::_('WRITTEN')).' '.clm_core::$load->utf8decode(JText::_('ON_DAY')).' '.clm_core::$load->utf8decode(JHTML::_('date', $now, JText::_('DATE_FORMAT_CLM_PDF'))), 0, 1, 'R');
                $cr = false;
                $pdf->SetFont('Times', '', $font);
            }
        }
        if ($ms) {
            $breite0 = 4;
            $breite1 = 38;
        } else {
            $breite0 = 0;
            $breite1 = 42;
        }
        if (!$cr) {
            $pdf->SetX($xx1);
        } else {
            $pdf->SetX($xx2);
        }
        $pdf->Cell(5, 1, '', 0, 1);
        if (!$cr) {
            $pdf->SetX($xx1);
        } else {
            $pdf->SetX($xx2);
        }

        if ($params['round_date'] == '1' and $paar[$y]->pdate > '1970-01-01') {
            $htext = JHTML::_('date', $paar[$y]->pdate, JText::_('DATE_FORMAT_CLM_F'));
            if ($paar[$y]->ptime > '00:00:00') {
                $htext .= '  '.substr($paar[$y]->ptime, 0, 5);
            }
            $pdf->SetFont('Times', '', $date_font);
            $pdf->Cell(89, $zelle, $htext, 1, 1, 'R');
            $pdf->SetFont('Times', '', $font);
            if (!$cr) {
                $pdf->SetX($xx1);
            } else {
                $pdf->SetX($xx2);
            }
        }
        $htext = clm_core::$load->utf8decode($paar[$y]->hname);
        while (($breite1 - 1) < $pdf->GetStringWidth($htext)) {
            $htext = substr($htext, 0, -1);
        }
        if (isset($paar[$y]->hpublished) and $paar[$y]->hpublished == 1 and isset($paar[$y]->hname)) {
            if ($ms) {
                $pdf->Cell($breite0, $zelle + 1, '', 'LTB', 0);
                $pdf->Cell($breite1, $zelle + 1, $htext, 'RTB', 0, 'L');
            } else {
                $pdf->Cell($breite1, $zelle + 1, $htext, 1, 0, 'L');
            }
        } elseif ($paar[$y]->hname == 'spielfrei') {
            if ($ms) {
                $pdf->Cell($breite0, $zelle + 1, '', 'LTB', 0);
                $pdf->Cell($breite1, $zelle + 1, $htext, 'RTB', 0, 'L');
            } else {
                $pdf->Cell($breite1, $zelle + 1, $htext, 1, 0, 'L');
            }
        } else {
            $pdf->Cell($breite1 + 5, $zelle + 1, '', 0, 0);
        }
        $breite1 = 42;
        $pdf->Cell(5, $zelle + 1, ' - ', 1, 0, 'C');
        $htext = clm_core::$load->utf8decode($paar[$y]->gname);
        while (($breite1 - 1) < $pdf->GetStringWidth($htext)) {
            $htext = substr($htext, 0, -1);
        }
        if (isset($paar[$y]->gpublished) and $paar[$y]->gpublished == 1 and isset($paar[$y]->gname)) {
            $pdf->Cell($breite1, $zelle + 1, $htext, 1, 0, 'L');
        } elseif ($paar[$y]->gname == 'spielfrei') {
            $pdf->Cell($breite1, $zelle + 1, $htext, 1, 0, 'L');
        } else {
            $pdf->Cell($breite1 + 5, $zelle + 1, '', 0, 0);
        }
        $pdf->Cell(5, $zelle + 1, '', 0, 1, 'C');
        if ($paar[$y]->hname == 'spielfrei' || $paar[$y]->gname == 'spielfrei') {
            $z2 += 2;
        } else {
            if ($summe[$z2]->paarung < $paar[$y]->paar) {
                $z2 = $z2 + 2;
            }
            $hsum = $summe[$z2]->sum;
            $gsum = $summe[$z2 + 1]->sum;
            //	while (isset($einzel[$w]->paar) AND $einzel[$w]->paar < ($y+1)) {
            while (isset($einzel[$w]->paar) and $einzel[$w]->paar < $paar[$y]->paar) {
                $w++;
            } //mtmt
            //	if (isset($einzel[$w]->paar) AND $einzel[$w]->paar == ($y+1)) {
            if (isset($einzel[$w]->paar) and $einzel[$w]->paar == $paar[$y]->paar) {
                // Bretter
                for ($x = 0; $x < $liga[0]->stamm; $x++) {
                    if ($x % 2 != 0) {
                        $zeilenr = 'zeile1';
                    } else {
                        $zeilenr = 'zeile2';
                    }
                    if (!$cr) {
                        $pdf->SetX($xx1);
                    } else {
                        $pdf->SetX($xx2);
                    }
                    $breite2 = 4;
                    $breite1 = 38;
                    if ($ms === false) {
                        $einzel[$w]->hsnr = (string) $einzel[$w]->tmnr.'-'.$einzel[$w]->trang;
                    }
                    if ($pdf->GetStringWidth($einzel[$w]->hsnr) > $breite2) {
                        $breite2 = $pdf->GetStringWidth($einzel[$w]->hsnr) + 1;
                        $breite1 = $breite1 + 4 - $pdf->GetStringWidth($einzel[$w]->hsnr) - 1;
                    }
                    $pdf->Cell($breite2, $zelle, $einzel[$w]->hsnr, 'L', 0);
                    if (is_null($einzel[$w]->hname)) {
                        $einzel[$w]->hname = '';
                    }
                    $htext = clm_core::$load->utf8decode($einzel[$w]->hname);
                    if ($einzel[$w]->hstatus != 'A' and $einzel[$w]->hstatus != '') {
                        $htext .= ' ('.$einzel[$w]->hstatus.')';
                    }
                    if ($htext == '') {
                        $htext = clm_core::$load->utf8decode(JText::_('RESULTS_DETAILS_NOT_NOMINATED'));
                    }
                    if ($pdf->GetStringWidth($htext) > $breite1) {
                        while ($pdf->GetStringWidth($htext) > $breite1) {
                            $htext = substr($htext, 0, -1);
                        }
                        $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
                    } else {
                        $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
                    }

                    $dr_einzel = "?";
                    if ($einzel[$w]->ergebnis == 0) {
                        $dr_einzel = $erg0;
                    }
                    if ($einzel[$w]->ergebnis == 1) {
                        $dr_einzel = $erg1;
                    }
                    if ($einzel[$w]->ergebnis == 2) {
                        $dr_einzel = $erg2;
                    }
                    if ($einzel[$w]->ergebnis == 3) {
                        $dr_einzel = "0-0";
                    }
                    if ($einzel[$w]->ergebnis == 4) {
                        $dr_einzel = "-/+";
                    }
                    if ($einzel[$w]->ergebnis == 5) {
                        $dr_einzel = "+/-";
                    }
                    if ($einzel[$w]->ergebnis == 6) {
                        $dr_einzel = "-/-";
                    }
                    if ($einzel[$w]->ergebnis == 7) {
                        $dr_einzel = "---";
                    }
                    if ($einzel[$w]->ergebnis == 8) {
                        $dr_einzel = "spf";
                    }
                    if ($einzel[$w]->ergebnis == 9) {
                        $dr_einzel = $erg9;
                    }
                    if ($einzel[$w]->ergebnis == 10) {
                        $dr_einzel = $erg10;
                    }
                    //
                    if ($einzel[$w]->dwz_edit == "") {
                        $pdf->Cell(5, $zelle, $dr_einzel, 'LR', 0, 'C');
                    } else {
                        $dr1_einzel = "?";
                        if ($einzel[$w]->dwz_edit == 0) {
                            $dr1_einzel = $erg0;
                        }
                        if ($einzel[$w]->dwz_edit == 1) {
                            $dr1_einzel = $erg1;
                        }
                        if ($einzel[$w]->dwz_edit == 2) {
                            $dr1_einzel = $erg2;
                        }
                        if ($einzel[$w]->dwz_edit == 3) {
                            $dr1_einzel = "0-0";
                        }
                        if ($einzel[$w]->dwz_edit == 4) {
                            $dr1_einzel = "-/+";
                        }
                        if ($einzel[$w]->dwz_edit == 5) {
                            $dr1_einzel = "+/-";
                        }
                        if ($einzel[$w]->dwz_edit == 6) {
                            $dr1_einzel = "-/-";
                        }
                        if ($einzel[$w]->dwz_edit == 7) {
                            $dr1_einzel = "---";
                        }
                        if ($einzel[$w]->dwz_edit == 8) {
                            $dr1_einzel = "spf";
                        }
                        if ($einzel[$w]->dwz_edit == 9) {
                            $dr1_einzel = $erg9;
                        }
                        if ($einzel[$w]->dwz_edit == 10) {
                            $dr1_einzel = $erg10;
                        }
                        $pdf->Cell(5, $zelle, $dr1_einzel, 'LR', 0, 'C');
                    }
                    //
                    $breite2 = 4;
                    $breite1 = 38;
                    if ($ms === false) {
                        $einzel[$w]->gsnr = (string) $einzel[$w]->smnr.'-'.$einzel[$w]->srang;
                    }
                    if ($pdf->GetStringWidth($einzel[$w]->gsnr) > $breite2) {
                        $breite2 = $pdf->GetStringWidth($einzel[$w]->gsnr) + 1;
                        $breite1 = $breite1 + 4 - $pdf->GetStringWidth($einzel[$w]->gsnr) - 1;
                    }
                    if (is_null($einzel[$w]->gname)) {
                        $einzel[$w]->gname = '';
                    }
                    $htext = clm_core::$load->utf8decode($einzel[$w]->gname);
                    if ($einzel[$w]->gstatus != 'A' and $einzel[$w]->gstatus != '') {
                        $htext .= ' ('.$einzel[$w]->gstatus.')';
                    }
                    if ($htext == '') {
                        $htext = clm_core::$load->utf8decode(JText::_('RESULTS_DETAILS_NOT_NOMINATED'));
                    }
                    if ($pdf->GetStringWidth($htext) > $breite1) {
                        while ($pdf->GetStringWidth($htext) > $breite1) {
                            $htext = substr($htext, 0, -1);
                        }
                    }
                    if ($ms) {
                        $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
                        $pdf->Cell($breite2, $zelle, $einzel[$w]->gsnr, 'R', 1, 'R');
                    } else {
                        $pdf->Cell($breite2, $zelle, $einzel[$w]->gsnr, 'L', 0, 'L');
                        $pdf->Cell($breite1, $zelle, $htext, 'R', 1, 'L');
                    }
                    //
                    if ($einzel[$w]->dwz_edit != "") {
                        if (!$cr) {
                            $pdf->SetX($xx1);
                        } else {
                            $pdf->SetX($xx2);
                        }
                        $pdf->SetFont('Times', '', $font - 3);
                        $breite2 = 4;
                        $breite1 = 38;
                        $pdf->Cell($breite2, $zelle - 2, " ", 'L', 0);
                        $pdf->Cell($breite1, $zelle - 2, " ", 0, 0, 'R');
                        $pdf->Cell(5, $zelle - 2, "(".$dr_einzel.")", 'LR', 0, 'C');
                        $pdf->Cell($breite1, $zelle - 2, " ", 0, 0, 'L');
                        $pdf->Cell($breite2, $zelle - 2, " ", 'R', 1, 'L');
                        $pdf->SetFont('Times', '', $font);
                    }
                    //
                    $w++;
                }
            }
            // Ergebnis Mannschaft
            $breite1 = 38;
            if (!$cr) {
                $pdf->SetX($xx1);
            } else {
                $pdf->SetX($xx2);
            }
            //if (($hsum + $gsum) != 0) {
            if (!is_null($paar[$y]->brettpunkte)) {
                $pdf->Cell(4, $zelle + 1, '', 'LTB', 0);
                $pdf->Cell($breite1, $zelle + 1, $hsum, 'TB', 0, 'C');
                $pdf->Cell(5, $zelle + 1, ' - ', 1, 0, 'C');
                $pdf->Cell($breite1, $zelle + 1, $gsum, 'TB', 0, 'C');
                $pdf->Cell(4, $zelle + 1, '', 'RTB', 1);
                //$z2+=2;
                if (($hsum == $gsum) and ($runden_modus == 4 or $runden_modus == 5)) {
                    $ztext = "";
                    if ($paar[$y]->ko_decision == 1) {
                        if ($paar[$y]->wertpunkte > $paar[$y]->gwertpunkte) {
                            $ztext = JText::_('ROUND_DECISION_WP_HEIM')." ".$paar[$y]->wertpunkte." : ".$paar[$y]->gwertpunkte." für ".$paar[$y]->hname;
                        } else {
                            $ztext = JText::_('ROUND_DECISION_WP_GAST')." ".$paar[$y]->gwertpunkte." : ".$paar[$y]->wertpunkte." für ".$paar[$y]->gname;
                        }
                    }
                    if ($paar[$y]->ko_decision == 2) {
                        $ztext = JText::_('ROUND_DECISION_BLITZ_HEIM')." ".$paar[$y]->hname;
                    }
                    if ($paar[$y]->ko_decision == 3) {
                        $ztext = JText::_('ROUND_DECISION_BLITZ_GAST')." ".$paar[$y]->gname;
                    }
                    if ($paar[$y]->ko_decision == 4) {
                        $ztext = JText::_('ROUND_DECISION_LOS_HEIM')." ".$paar[$y]->hname;
                    }
                    if ($paar[$y]->ko_decision == 5) {
                        $ztext = JText::_('ROUND_DECISION_LOS_GAST')." ".$paar[$y]->gname;
                    }
                    if (!$cr) {
                        $pdf->SetX($xx1);
                    } else {
                        $pdf->SetX($xx2);
                    }
                    $pdf->Cell(4, $zelle + 1, '', 'LTB', 0);
                    $pdf->Cell($breite1 * 2 + 5, $zelle + 1, clm_core::$load->utf8decode($ztext), 'TB', 0, 'L');
                    $pdf->Cell(4, $zelle + 1, '', 'RTB', 1);
                }
            } else {
                $pdf->Cell(4, $zelle + 1, '', 'LTB', 0);
                $pdf->Cell($breite1 * 2 + 5, $zelle + 1, JText::_('NO_RESULT_YET'), 'TB', 0, 'L');
                $pdf->Cell(4, $zelle + 1, '', 'RTB', 1);
            }
            if ($paar[$y]->comment != "") {
                $paar[$y]->comment = str_replace($a_html, $a_pdf, $paar[$y]->comment);
                $a_comment = explode('<br>', $paar[$y]->comment);
                foreach ($a_comment as $comment) {
                    $ztext = JText::_('PAAR_COMMENT').$comment;
                    if (!$cr) {
                        $pdf->SetX($xx1);
                    } else {
                        $pdf->SetX($xx2);
                    }
                    $pdf->Multicell($breite1 * 2 + 13, $zelle, clm_core::$load->utf8decode($ztext), 1);
                }
            }
            $z2 += 2;
        }
    }
    if ($runden_modus != 4) {
        // Tabelle
        $lid		= $liga[0]->id;
        $sid		= clm_core::$load->request_int('saison', 1);
        $punkte		= $this->punkte;
        $spielfrei	= $this->spielfrei;

        // Spielfreie Teilnehmer finden //
        $diff = $spielfrei[0]->count;
        if ($ms) {
            $breite1 = 43 - 10;
        } else {
            $breite1 = 56 - 10;
        }
        if ($pdf->GetY() > $lspalte_tab) {
            if (!$cr) {
                $pdf->SetY($yy0);
                $cr = true;
            } else {
                $pdf->AddPage();
                $pdf->SetFont('Times', '', $date_font);
                $pdf->Cell(10, 4, ' ', 0, 0);
                $pdf->Cell(175, 4, clm_core::$load->utf8decode(JText::_('WRITTEN')).' '.clm_core::$load->utf8decode(JText::_('ON_DAY')).' '.clm_core::$load->utf8decode(JHTML::_('date', $now, JText::_('DATE_FORMAT_CLM_PDF'))), 0, 1, 'R');
                $cr = false;
                $pdf->SetFont('Times', '', $font);
            }
        }
        if (!$cr) {
            $pdf->SetX($xx1);
        } else {
            $pdf->SetX($xx2);
        }
        $pdf->Ln();
        if (!$cr) {
            $pdf->SetX($xx1);
        } else {
            $pdf->SetX($xx2);
        }
        $pdf->Cell(5, $zelle + 1, '', 0, 0);
        $pdf->Cell($breite1, $zelle + 1, JText::_('RANGLISTE').' '.JText::_('AFTER').' '.JText::_('ROUND').' '.$runde, 0, 1, 'L');
        if (!$cr) {
            $pdf->SetX($xx1);
        } else {
            $pdf->SetX($xx2);
        }
        $pdf->Cell(5, $zelle + 1, JText::_('RANG2'), 'LTB', 0);
        $pdf->Cell($breite1 + 4, $zelle + 1, JText::_('TEAM'), 'TB', 0, 'L');
        $pdf->Cell(7, $zelle + 1, JText::_('SP'), 'TB', 0, 'C');
        $pdf->Cell(9, $zelle + 1, JText::_('MP'), 'TB', 0, 'C');
        $tbreite = $breite1 + 26;
        if ($liga[0]->liga_mt == 0) {
            $tbreite = $tbreite + 10;
            $pdf->Cell(10, $zelle + 1, JText::_('BP'), 'TB', 0, 'C');
            if ($liga[0]->b_wertung > 0) {
                $tbreite = $tbreite + 10;
                $pdf->Cell(10, $zelle + 1, JText::_('WP'), 'TB', 0, 'C');
            }
        } else {
            if ($liga[0]->tiebr1 > 0 and $liga[0]->tiebr1 < 50) {
                $tbreite = $tbreite + 10;
                $pdf->Cell(10, $zelle + 1, JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr1), 'TB', 0, 'C');
            }
            if ($liga[0]->tiebr2 > 0 and $liga[0]->tiebr2 < 50) {
                $tbreite = $tbreite + 10;
                $pdf->Cell(10, $zelle + 1, JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr2), 'TB', 0, 'C');
            }
            if ($liga[0]->tiebr3 > 0 and $liga[0]->tiebr3 < 50) {
                $tbreite = $tbreite + 10;
                $pdf->Cell(10, $zelle + 1, JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr3), 'TB', 0, 'C');
            }
        }
        $pdf->Cell(1, $zelle + 1, "", 'TBR', 1, 'C');
        // Anzahl der Teilnehmer durchlaufen
        for ($x = 0; $x < ($liga[0]->teil) - $diff; $x++) {
            if (!$cr) {
                $pdf->SetX($xx1);
            } else {
                $pdf->SetX($xx2);
            }
            //	$pdf->Cell(5,$zelle,$x+1,'L',0,'C');
            $pdf->Cell(5, $zelle, $punkte[$x]->rankingpos, 'L', 0, 'C');
            $htext = clm_core::$load->utf8decode($punkte[$x]->name);
            while (($breite1 + 3) < $pdf->GetStringWidth($htext)) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1 + 4, $zelle, $htext, 0, 0, 'L');
            $pdf->Cell(7, $zelle, $punkte[$x]->spiele, 0, 0, 'C');
            if ($punkte[$x]->abzug > 0) {
                $pdf->Cell(9, $zelle, $punkte[$x]->mp.'*', 0, 0, 'C');
            } else {
                $pdf->Cell(9, $zelle, $punkte[$x]->mp, 0, 0, 'C');
            }
            if ($liga[0]->liga_mt == 0) {
                if ($punkte[$x]->bpabzug > 0) {
                    $pdf->Cell(10, $zelle, $punkte[$x]->bp.'*', 0, 0, 'C');
                } else {
                    $pdf->Cell(10, $zelle, $punkte[$x]->bp, 0, 0, 'C');
                }
                if ($liga[0]->b_wertung > 0) {
                    $pdf->Cell(10, $zelle, $punkte[$x]->wp, 0, 0, 'C');
                }
            } else {
                if ($liga[0]->tiebr1 == 5) { // Brettpunkte
                    if ($punkte[$x]->bpabzug > 0) {
                        $pdf->Cell(10, $zelle, CLMText::tiebrFormat($liga[0]->tiebr1, $punkte[$x]->sumtiebr1).'*', 0, 0, 'C');
                    } else {
                        $pdf->Cell(10, $zelle, CLMText::tiebrFormat($liga[0]->tiebr1, $punkte[$x]->sumtiebr1), 0, 0, 'C');
                    }
                } elseif ($liga[0]->tiebr1 > 0 and $liga[0]->tiebr1 < 50) {
                    $pdf->Cell(10, $zelle, CLMText::tiebrFormat($liga[0]->tiebr1, $punkte[$x]->sumtiebr1), 0, 0, 'C');
                }
                if ($liga[0]->tiebr2 == 5) { // Brettpunkte
                    if ($punkte[$x]->bpabzug > 0) {
                        $pdf->Cell(10, $zelle, CLMText::tiebrFormat($liga[0]->tiebr2, $punkte[$x]->sumtiebr2).'*', 0, 0, 'C');
                    } else {
                        $pdf->Cell(10, $zelle, CLMText::tiebrFormat($liga[0]->tiebr2, $punkte[$x]->sumtiebr2), 0, 0, 'C');
                    }
                } elseif ($liga[0]->tiebr2 > 0 and $liga[0]->tiebr2 < 50) {
                    $pdf->Cell(10, $zelle, CLMText::tiebrFormat($liga[0]->tiebr2, $punkte[$x]->sumtiebr2), 0, 0, 'C');
                }
                if ($liga[0]->tiebr3 == 5) { // Brettpunkte
                    if ($punkte[$x]->bpabzug > 0) {
                        $pdf->Cell(10, $zelle, CLMText::tiebrFormat($liga[0]->tiebr3, $punkte[$x]->sumtiebr3).'*', 0, 0, 'C');
                    } else {
                        $pdf->Cell(10, $zelle, CLMText::tiebrFormat($liga[0]->tiebr3, $punkte[$x]->sumtiebr3), 0, 0, 'C');
                    }
                } elseif ($liga[0]->tiebr3 > 0 and $liga[0]->tiebr3 < 50) {
                    $pdf->Cell(10, $zelle, CLMText::tiebrFormat($liga[0]->tiebr3, $punkte[$x]->sumtiebr3), 0, 0, 'C');
                }
            }
            $pdf->Cell(1, $zelle + 1, "", 'R', 0, 'C');
            $pdf->SetFont('Symbol', '', $font);
            if ($x < $liga[0]->auf) {
                $pdf->Cell(6, $zelle, chr(221), 0, 1, 'C');
            } elseif ($x >= $liga[0]->auf and $x < ($liga[0]->auf + $liga[0]->auf_evtl)) {
                $xxp = $pdf->GetX() + 2;
                $yyp = $pdf->GetY() + 2;
                $pdf->RotatedText($xxp, $yyp, chr(221), 315);
                $pdf->Cell(6, $zelle, '', 0, 1, 'C');
            } elseif ($x >= ($liga[0]->teil - ($liga[0]->ab_evtl + $liga[0]->ab)) and $x < ($liga[0]->teil - $liga[0]->ab)) {
                $xxp = $pdf->GetX() + 4;
                $yyp = $pdf->GetY() + 4;
                $pdf->RotatedText($xxp, $yyp, chr(223), 45);
                $pdf->Cell(6, $zelle, '', 0, 1, 'C');
            } elseif ($x >= ($liga[0]->teil - $liga[0]->ab)) {
                $pdf->Cell(6, $zelle, chr(223), 0, 1, 'C');
            } else {
                $pdf->Cell(10, $zelle, '', 0, 1, 'C');
            }

            $pdf->SetFont('Times', '', $font);
        }
        if (!$cr) {
            $pdf->SetX($xx1);
        } else {
            $pdf->SetX($xx2);
        }
        $pdf->Cell($tbreite, 1, '', 'LRB', 1, 'C');
    }
    // Ansetzungen Folgerunde
    if (!empty($paar1)) {
        if ($pdf->GetY() > $lspalte) {
            if (!$cr) {
                $pdf->SetY($yy0);
                $cr = true;
            } else {
                $pdf->AddPage();
                $pdf->SetFont('Times', '', $date_font);
                $pdf->Cell(10, 4, ' ', 0, 0);
                $pdf->Cell(175, 4, clm_core::$load->utf8decode(JText::_('WRITTEN')).' '.clm_core::$load->utf8decode(JText::_('ON_DAY')).' '.clm_core::$load->utf8decode(JHTML::_('date', $now, JText::_('DATE_FORMAT_CLM_PDF'))), 0, 1, 'R');
                $cr = false;
                $pdf->SetFont('Times', '', $font);
            }
        }
        // Rundentext anpassen
        $runden_text = $liga[$runde_t]->rname;
        if (!$cr) {
            $pdf->SetX($xx1);
        } else {
            $pdf->SetX($xx2);
        }
        $pdf->Cell($breite, $zelle, '', 0, 1, 'C');
        if (!$cr) {
            $pdf->SetX($xx1);
        } else {
            $pdf->SetX($xx2);
        }
        $htext = clm_core::$load->utf8decode(JText::_('ROUND_PROGRAM')).' '.clm_core::$load->utf8decode($runden_text);
        if ($liga[$runde]->datum > 0) {
            $htext .= ' '.JText::_('ON_DAY').' '.clm_core::$load->utf8decode(JHTML::_('date', $liga[$runde]->datum, JText::_('DATE_FORMAT_CLM_F')));
            if (isset($liga[$runde]->startzeit) and $liga[$runde]->startzeit != '00:00:00') {
                $htext .= '  '.substr($liga[$runde]->startzeit, 0, 5).' Uhr';
            }
        }
        $pdf->Cell($breite, 6, $htext, 0, 1, 'L');
        for ($y = 0; $y < ($liga[0]->teil) / 2; $y++) {
            if (!isset($paar1[$y])) {
                break;
            }
            if ($ms) {
                $breite0 = 4;
                $breite1 = 38;
            } else {
                $breite0 = 0;
                $breite1 = 42;
            }
            if (!$cr) {
                $pdf->SetX($xx1);
            } else {
                $pdf->SetX($xx2);
            }
            $htext = clm_core::$load->utf8decode($paar1[$y]->hname);
            while (($breite1 - 1) < $pdf->GetStringWidth($htext)) {
                $htext = substr($htext, 0, -1);
            }
            if (isset($paar1[$y]->hpublished) and $paar1[$y]->hpublished == 1 and isset($paar1[$y]->hname)) {
                if ($ms) {
                    $pdf->Cell($breite0, $zelle + 1, '', 'LTB', 0);
                    $pdf->Cell($breite1, $zelle + 1, $htext, 'RTB', 0, 'L');
                } else {
                    $pdf->Cell($breite1, $zelle + 1, $htext, 1, 0, 'L');
                }
            } elseif ($paar1[$y]->hname == 'spielfrei') {
                if ($ms) {
                    $pdf->Cell($breite0, $zelle + 1, '', 'LTB', 0);
                    $pdf->Cell($breite1, $zelle + 1, $htext, 'RTB', 0, 'L');
                } else {
                    $pdf->Cell($breite1, $zelle + 1, $htext, 1, 0, 'L');
                }
            } else {
                $pdf->Cell($breite1 + 4, $zelle + 1, '', 0, 0);
            }

            $pdf->Cell(5, $zelle + 1, ' - ', 'TB', 0, 'C');
            $htext = clm_core::$load->utf8decode($paar1[$y]->gname);
            while (($breite1 - 1) < $pdf->GetStringWidth($htext)) {
                $htext = substr($htext, 0, -1);
            }
            if (isset($paar1[$y]->gpublished) and $paar1[$y]->gpublished == 1 and isset($paar1[$y]->gname)) {
                if ($ms) {
                    $pdf->Cell($breite1, $zelle + 1, $htext, 'LTB', 0, 'L');
                    $pdf->Cell($breite0, $zelle + 1, '', 'RTB', 0);
                } else {
                    $pdf->Cell($breite1, $zelle + 1, $htext, 1, 0, 'L');
                }
            } elseif ($paar1[$y]->gname == 'spielfrei') {
                if ($ms) {
                    $pdf->Cell($breite1, $zelle + 1, $htext, 'LTB', 0, 'L');
                    $pdf->Cell($breite0, $zelle + 1, '', 'RTB', 0);
                } else {
                    $pdf->Cell($breite1, $zelle + 1, $htext, 1, 0, 'L');
                }
            } else {
                $pdf->Cell($breite1 + 4, $zelle + 1, '', 0, 0);
            }
            $pdf->Cell(4, $zelle + 1, '', 0, 1, 'C');
        }
    }

    // Kommentar zur Runde
    $lspalte_comment = 240;
    if (is_null($liga[$runde - 1]->comment)) {
        $liga[$runde - 1]->comment = '';
    }
    $lcomment = strlen($liga[$runde - 1]->comment);  //klkl
    $lspalte_comment = $lspalte_comment - ($lcomment / (60 / $zelle));
    if (isset($liga[$runde - 1]->comment) and $liga[$runde - 1]->comment <> "") {
        if ($pdf->GetY() > $lspalte_comment) {
            if (!$cr) {
                $pdf->SetY($yy0);
                $cr = true;
            } else {
                $pdf->AddPage();
                $pdf->SetFont('Times', '', $date_font);
                $pdf->Cell(10, 4, ' ', 0, 0);
                $pdf->Cell(175, 4, clm_core::$load->utf8decode(JText::_('WRITTEN')).' '.clm_core::$load->utf8decode(JText::_('ON_DAY')).' '.clm_core::$load->utf8decode(JHTML::_('date', $now, JText::_('DATE_FORMAT_CLM_PDF'))), 0, 1, 'R');
                $cr = false;
                $pdf->SetFont('Times', '', $font);
            }
        }
        if (!$cr) {
            $pdf->SetX($xx1);
        } else {
            $pdf->SetX($xx2);
        }
        $pdf->Cell($breite, $zelle, '', 0, 1, 'C');
        if (!$cr) {
            $pdf->SetX($xx1);
        } else {
            $pdf->SetX($xx2);
        }
        $pdf->Cell($breite, 6, clm_core::$load->utf8decode(JText::_('NOTICE_SL')), 0, 1, 'L');
        if (!$cr) {
            $pdf->SetX($xx1);
        } else {
            $pdf->SetX($xx2);
        }
        $pdf->MultiCell($breite,$zelle,clm_core::$load->utf8decode($liga[$runde - 1]->comment),1,'L');
    }

    // Unterschrift
    if ($pdf->GetY() > $lspalte) {
        if (!$cr) {
            $pdf->SetY($yy0);
            $cr = true;
        } else {
            $pdf->AddPage();
            $pdf->SetFont('Times','',$date_font);
            $pdf->Cell(10,4,' ',0,0);
            $pdf->Cell(175,4,clm_core::$load->utf8decode(JText::_('WRITTEN')).' '.clm_core::$load->utf8decode(JText::_('ON_DAY')).' '.clm_core::$load->utf8decode(JHTML::_('date',  $now, JText::_('DATE_FORMAT_CLM_PDF'))),0,1,'R');
            $cr = false;
            $pdf->SetFont('Times','',$font);
        }
    }
    if (!$cr) {
        $pdf->SetX($xx1);
    } else {
        $pdf->SetX($xx2);
    }
    if (isset($liga[0]->mf_name)) {
        $pdf->Cell($breite,$zelle,'',0,1,'C');
        $pdf->SetFont('Times','',$font + 1);
        if (!$cr) {
            $pdf->SetX($xx1);
        } else {
            $pdf->SetX($xx2);
        }
        $pdf->Cell(3,$zelle,' ',0,0,'L');
        $pdf->Cell($breite - 3,$zelle,clm_core::$load->utf8decode(JText::_('CHIEF')),0,1,'L');
        $pdf->SetFont('Times','',$font);
        if (!$cr) {
            $pdf->SetX($xx1);
        } else {
            $pdf->SetX($xx2);
        }
        $pdf->Cell(3,$zelle,' ',0,0,'L');
        $pdf->Cell($breite - 3,$zelle,clm_core::$load->utf8decode($liga[0]->mf_name),0,1,'L');

        if (!$cr) {
            $pdf->SetX($xx1);
        } else {
            $pdf->SetX($xx2);
        }
        $pdf->Cell(3,$zelle,' ',0,0,'L');
        $pdf->SetFont('Times','U',$font);
        if ($jid > 0 or $show_sl_mail > 0) {
            $pdf->Cell($breite - 3,$zelle,clm_core::$load->utf8decode($liga[0]->email),0,1,'L');
        } else {
            $pdf->Cell($breite - 3,$zelle,'',0,1,'L');
        }
    }
}
// Ausgabe
$pdf->Output(clm_core::$load->utf8decode($runde.".".clm_core::$load->utf8decode(JText::_('ROUND_LETTER')).' ').clm_core::$load->utf8decode($liga[0]->name).'.pdf','D');
exit;
