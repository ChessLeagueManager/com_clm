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

$lid = clm_core::$load->request_int('liga', 1);
$sid = clm_core::$load->request_int('saison', 1);
$view = clm_core::$load->request_string('view');
// Variablen ohne foreach setzen
$liga = $this->liga;
//Liga-Parameter aufbereiten
$paramsStringArray = explode("\n", $liga[0]->params);
$params = array();
foreach ($paramsStringArray as $value) {
    $ipos = strpos($value, '=');
    if ($ipos !== false) {
        $params[substr($value, 0, $ipos)] = substr($value, $ipos + 1);
    }
}
if (!isset($params['dwz_date'])) {
    $params['dwz_date'] = '1970-01-01';
}
$punkte = $this->punkte;
$spielfrei = $this->spielfrei;
$mannschaft	= $this->mannschaft;
$mleiter	= $this->mleiter;

//Bereinigung count
foreach ($this->count as $c1) {
    if (is_null($c1->name)) {
        $c1->name = '';
    }
    if (!is_null($c1->tln_nr)) {
        $count[] = $c1;
    }
}
if ($params['dwz_date'] != '0000-00-00' and $params['dwz_date'] != '1970-01-01') {
    for ($i = 0; $i < (count($count)); $i++) {
        $count[$i]->dwz = $count[$i]->start_dwz;
    }
}
$saison     = $this->saison;
$name_liga = $liga[0]->name;

require_once(clm_core::$path.DS.'classes'.DS.'fpdf.php');

class PDF extends FPDF
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
}

// Konfigurationsparameter auslesen
$config = clm_core::$db->config();
$countryversion = $config->countryversion;
$telefon = $config->man_tel;
$mobil	= $config->man_mobil;
$mail	= $config->man_mail;
$show_sl_mail = $config->show_sl_mail;

// Userkennung holen
$user	= JFactory::getUser();
$jid	= $user->get('id');

// Spielfreie Teilnehmer finden
$diff = $spielfrei[0]->count;

// Zellenhöhe -> Standard 5
$zelle = 4;
// Wert von Zellenbreite abziehen
// Bspl. für Standard (Null) für Liga mit 7 Runden und 1 Durchgang
$breite = 0;
$breite1 = 30;
// Fontgröße Standard = 12
$font = 9;

// Datum der Erstellung
$date = JFactory::getDate();
$now = $date->toSQL();

// Löschen der Aufstellungen, falls Druck geblockt
if ($liga[0]->anzeige_ma == 1) {
    $count = '';
}

$pdf = new PDF();
$pdf->AliasNbPages();

// Anzahl der Mannschaften durchlaufen
$zz = -1;
$yy1 = 230;
for ($x = 0; $x < ($liga[0]->teil); $x++) {
    //Anzahl gemeldetet Spieler
    $xx1 = 0;
    $xx2 = 0;
    while ((isset($count[$xx1])) and ($countryversion == "de") and ($count[$xx1]->mgl_nr == "0")) {     //mtmt!!!
        if ($count[$xx1]->tln_nr == $mannschaft[$x]->tln_nr) {
            $xx2++;
        }
        $xx1++;
        if ($xx1 > 10) {
            die($xx1."FF");
        }
    }
    $xx2 = $xx2 - ($liga[0]->stamm + 12);
    if ($xx2 > 0) {
        $yy0 = $yy1 - (4 * $xx2);
    } else {
        $yy0 = $yy1;
    }
    $xx = 0;
    if ($x == 0) {
        $xx = 1;
    } elseif ($pdf->GetY() > $yy0) {
        $xx = 1;
    }
    if ($xx == 1) {
        //if (($x == 0)||($x == 4)||($x == 8)) {
        $pdf->AddPage();

        $pdf->SetFont('Times', '', 7);
        $pdf->Cell(10, 3, ' ', 0, 0);
        $pdf->Cell(175, 3, clm_core::$load->utf8decode(JText::_('WRITTEN')).' '.clm_core::$load->utf8decode(JText::_('ON_DAY')).' '.clm_core::$load->utf8decode(JHTML::_('date', $now, JText::_('DATE_FORMAT_CLM_PDF'))), 0, 1, 'R');

        $pdf->SetFont('Times', '', 14);
        $pdf->Cell(10, 15, ' ', 0, 0);
        $pdf->Cell(80, 15, clm_core::$load->utf8decode($liga[0]->name)." ".clm_core::$load->utf8decode($saison[0]->name), 0, 1, 'L');

        $pdf->SetFont('Times', '', $font);
        $pdf->Cell(10, $zelle, JText::_('MELDELISTE_NR'), 0, 0, 'C');
        $pdf->Cell(65, $zelle, JText::_('TEAM')."/".JText::_('CLUB_LOCATION'), 0, 0, 'L');
        $pdf->Cell(10, $zelle, JText::_('MELDELISTE_REGULAR'), 0, 0, 'C');
        $pdf->Cell($breite1, $zelle, JText::_('MELDELISTE_NAME'), 0, 0, 'L');
        $pdf->Cell(10, $zelle, JText::_('DWZ'), 0, 0, 'R');
        $pdf->Cell(10, $zelle, '', 0, 0, 'R');
        $pdf->Cell(10, $zelle, JText::_('MELDELISTE_SUBSTITUTE'), 0, 0, 'C');
        $pdf->Cell($breite1, $zelle, JText::_('MELDELISTE_NAME'), 0, 0, 'L');
        $pdf->Cell(10, $zelle, JText::_('DWZ'), 0, 1, 'R');
        $pdf->Ln();
    }
    // Anpassung Index ML-Tabelle
    for ($ml = 0; $ml < ($liga[0]->teil); $ml++) {
        if ($mannschaft[$x]->tln_nr == $mleiter[$ml]->tln_nr) {
            break;
        }
    }
    if ($ml == ($liga[0]->teil)) {
        $ml = 99;
    }
    // Test auf Mannschaft spielfrei
    if ($mannschaft[$x]->name == "spielfrei") {
        continue;
    }
    // Anzahl der Mannschaften durchlaufen
    while (isset($count[$zz + 1]) and $countryversion == "de" and $count[$zz + 1]->mgl_nr == '0') {
        $zz++;
    }
    $zl = $zz + 1;
    $yl = $zl + $liga[0]->stamm - 1;
    $zn = 0;
    $yn = $liga[0]->stamm;
    $zzc = 0;
    //echo "<br>zl: ".$zl." name: ".$count[$zl]->name." zn: ".$zn." stamm: ".$liga[0]->stamm." c_tln_nr: ".$count[$zl]->tln_nr." m_tln_nr: ".$mannschaft[$x]->tln_nr;
    //Zeile 01
    $pdf->SetFont('Times', 'BU', $font + 1);
    $pdf->Cell(10, $zelle, $x + 1, 0, 0, 'C');
    $pdf->Cell(65, $zelle, clm_core::$load->utf8decode($mannschaft[$x]->name), 0, 0, 'L');
    $pdf->SetFont('Times', '', $font);
    $zn++;
    if ($liga[0]->anzeige_ma == 1) {
        $pdf->Cell(80, 8, clm_core::$load->utf8decode(JText::_('TEAM_FORMATION_BLOCKED')), 0, 0, 'C');
    }
    if (isset($count[$zl]) and (($countryversion == "de" and $count[$zl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$zl]->PKZ > ''))
        && ($zn <= $liga[0]->stamm) && ($count[$zl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$zl]->rrang)) {
            $pdf->Cell(10, $zelle, $zn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$zl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$zl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$zl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$zl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$zl]->dwz, 0, 0, 'R');
    } else {
        $pdf->Cell(50, $zelle, '', 0, 0, 'C');
        $zl--;
    }
    $pdf->Cell(10, $zelle, '', 0, 0, 'R');
    $yn++;
    $yl++;
    if (isset($count[$yl]) and (($countryversion == "de" and $count[$yl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$yl]->PKZ > ''))
        && ($count[$yl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$yl]->rrang)) {
            $pdf->Cell(10, $zelle, $yn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$yl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$yl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$yl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$yl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$yl]->dwz, 0, 1, 'R');
    } else {
        $yl--;
        $pdf->Cell(50, $zelle, '', 0, 1, 'C');
    }
    //Zeile 02
    $pdf->Cell(10, $zelle, '', 0, 0, 'C');
    $pdf->Cell(65, $zelle, '', 0, 0, 'L');
    $zn++;
    $zl++;
    if (isset($count[$zl]) and (($countryversion == "de" and $count[$zl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$zl]->PKZ > ''))
        && ($zn <= $liga[0]->stamm) && ($count[$zl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$zl]->rrang)) {
            $pdf->Cell(10, $zelle, $zn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$zl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$zl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$zl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$zl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$zl]->dwz, 0, 0, 'R');
    } else {
        $pdf->Cell(50, $zelle, '', 0, 0, 'C');
        $zl--;
    }
    $pdf->Cell(10, $zelle, '', 0, 0, 'R');
    $yn++;
    $yl++;
    if (isset($count[$yl]) and (($countryversion == "de" and $count[$yl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$yl]->PKZ > ''))
        && ($count[$yl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$yl]->rrang)) {
            $pdf->Cell(10, $zelle, $yn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$yl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$yl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$yl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$yl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$yl]->dwz, 0, 1, 'R');
    } else {
        $yl--;
        $pdf->Cell(50, $zelle, '', 0, 1, 'C');
    }
    //Zeile 03
    $pdf->Cell(10, $zelle, '', 0, 0, 'C');
    $pdf->Cell(14, $zelle, JText::_('MELDELISTE_CAPTAIN'), 0, 0, 'L');
    if (is_null($mleiter[$ml]->mf_name)) {
        $mleiter[$ml]->mf_name = '';
    }
    if ($ml < 99) {
        $pdf->Cell(51, $zelle, clm_core::$load->utf8decode($mleiter[$ml]->mf_name), 0, 0, 'L');
    } else {
        $pdf->Cell(51, $zelle, JText::_('MELDELISTE_NOT_YET'), 0, 0, 'L');
    }
    $zn++;
    $zl++;
    if (isset($count[$zl]) and (($countryversion == "de" and $count[$zl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$zl]->PKZ > ''))
        && ($zn <= $liga[0]->stamm) && ($count[$zl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$zl]->rrang)) {
            $pdf->Cell(10, $zelle, $zn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$zl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$zl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$zl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$zl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$zl]->dwz, 0, 0, 'R');
    } else {
        $pdf->Cell(50, $zelle, '', 0, 0, 'C');
        $zl--;
    }
    $pdf->Cell(10, $zelle, '', 0, 0, 'R');
    $yn++;
    $yl++;
    if (isset($count[$yl]) and (($countryversion == "de" and $count[$yl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$yl]->PKZ > ''))
        && ($count[$yl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$yl]->rrang)) {
            $pdf->Cell(10, $zelle, $yn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$yl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$yl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$yl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$yl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$yl]->dwz, 0, 1, 'R');
    } else {
        $yl--;
        $pdf->Cell(50, $zelle, '', 0, 1, 'C');
    }
    //Zeile 04
    $pdf->Cell(10, $zelle, '', 0, 0, 'C');
    $pdf->Cell(14, $zelle, JText::_('MELDELISTE_PHONE'), 0, 0, 'L');
    if ($ml < 99) {
        if ($telefon == "1" or ($telefon == "0" and $jid != "0")) {
            if (($mleiter[$ml]->tel_fest) <> '') {
                $pdf->Cell(51, $zelle, clm_core::$load->utf8decode($mleiter[$ml]->tel_fest), 0, 0, 'L');
            } else {
                $pdf->Cell(51, $zelle, JText::_('MELDELISTE_NO_DATA'), 0, 0, 'L');
            }
        } else {
            $pdf->Cell(51, $zelle, clm_core::$load->utf8decode(JText::_('TEAM_REGISTERED')), 0, 0, 'L');
        }
    } else {
        $pdf->Cell(51, $zelle, '', 0, 0, 'L');
    }
    $zn++;
    $zl++;
    if (isset($count[$zl]) and (($countryversion == "de" and $count[$zl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$zl]->PKZ > ''))
        && ($zn <= $liga[0]->stamm) && ($count[$zl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$zl]->rrang)) {
            $pdf->Cell(10, $zelle, $zn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$zl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$zl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$zl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$zl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$zl]->dwz, 0, 0, 'R');
    } else {
        $pdf->Cell(50, $zelle, '', 0, 0, 'C');
        $zl--;
    }
    $pdf->Cell(10, $zelle, '', 0, 0, 'R');
    $yn++;
    $yl++;
    if (isset($count[$yl]) and (($countryversion == "de" and $count[$yl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$yl]->PKZ > ''))
        && ($count[$yl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$yl]->rrang)) {
            $pdf->Cell(10, $zelle, $yn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$yl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$yl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$yl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$yl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$yl]->dwz, 0, 1, 'R');
    } else {
        $yl--;
        $pdf->Cell(50, $zelle, '', 0, 1, 'C');
    }
    //Zeile 05
    $pdf->Cell(10, $zelle, '', 0, 0, 'C');
    $pdf->Cell(14, $zelle, JText::_('MELDELISTE_MOBIL'), 0, 0, 'L');
    if ($ml < 99) {
        if ($mobil == "1" or ($mobil == "0" and $jid != "0")) {
            if (($mleiter[$ml]->tel_mobil) <> '') {
                $pdf->Cell(51, $zelle, clm_core::$load->utf8decode($mleiter[$ml]->tel_mobil), 0, 0, 'L');
            } else {
                $pdf->Cell(51, $zelle, JText::_('MELDELISTE_NO_DATA'), 0, 0, 'L');
            }
        } else {
            $pdf->Cell(51, $zelle, clm_core::$load->utf8decode(JText::_('TEAM_REGISTERED')), 0, 0, 'L');
        }
    } else {
        $pdf->Cell(51, $zelle, '', 0, 0, 'L');
    }
    $zn++;
    $zl++;
    if (isset($count[$zl]) and (($countryversion == "de" and $count[$zl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$zl]->PKZ > ''))
        && ($zn <= $liga[0]->stamm) && ($count[$zl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$zl]->rrang)) {
            $pdf->Cell(10, $zelle, $zn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$zl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$zl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$zl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$zl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$zl]->dwz, 0, 0, 'R');
    } else {
        $pdf->Cell(50, $zelle, '', 0, 0, 'C');
        $zl--;
    }
    $pdf->Cell(10, $zelle, '', 0, 0, 'R');
    $yn++;
    $yl++;
    if (isset($count[$yl]) and (($countryversion == "de" and $count[$yl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$yl]->PKZ > ''))
        && ($count[$yl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$yl]->rrang)) {
            $pdf->Cell(10, $zelle, $yn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$yl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$yl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$yl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$yl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$yl]->dwz, 0, 1, 'R');
    } else {
        $yl--;
        $pdf->Cell(50, $zelle, '', 0, 1, 'C');
    }
    //Zeile 06
    $pdf->Cell(10, $zelle, '', 0, 0, 'C');
    $pdf->Cell(14, $zelle, JText::_('MELDELISTE_MAIL'), 0, 0, 'L');
    if (($ml < 99) && ($mleiter[$ml]->email <> '')) {
        if ($mail == "1" or ($mail == "0" and $jid != "0")) {
            $pdf->SetFont('Times', 'U', $font);
            $pdf->Cell(51, $zelle, clm_core::$load->utf8decode($mleiter[$ml]->email), 0, 0, 'L', 0, 'mailto:'.clm_core::$load->utf8decode($mleiter[$x]->email));
            $pdf->SetFont('Times', '', $font);
        } else {
            $pdf->Cell(51, $zelle, clm_core::$load->utf8decode(JText::_('TEAM_REGISTERED')), 0, 0, 'L');
        }
    } else {
        $pdf->Cell(51, $zelle, '', 0, 0, 'L');
    }
    $zn++;
    $zl++;
    if (isset($count[$zl]) and (($countryversion == "de" and $count[$zl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$zl]->PKZ > ''))
        && ($zn <= $liga[0]->stamm) && ($count[$zl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$zl]->rrang)) {
            $pdf->Cell(10, $zelle, $zn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$zl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$zl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$zl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$zl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$zl]->dwz, 0, 0, 'R');
    } else {
        $pdf->Cell(50, $zelle, '', 0, 0, 'C');
        $zl--;
    }
    $pdf->Cell(10, $zelle, '', 0, 0, 'R');
    $yn++;
    $yl++;
    if (isset($count[$yl]) and (($countryversion == "de" and $count[$yl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$yl]->PKZ > ''))
        && ($count[$yl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$yl]->rrang)) {
            $pdf->Cell(10, $zelle, $yn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$yl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$yl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$yl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$yl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$yl]->dwz, 0, 1, 'R');
    } else {
        $yl--;
        $pdf->Cell(50, $zelle, '', 0, 1, 'C');
    }
    //Zeile 07
    $pdf->Cell(10, $zelle, '', 0, 0, 'C');
    $pdf->Cell(65, $zelle, '', 0, 0, 'L');
    $zn++;
    $zl++;
    if (isset($count[$zl]) and (($countryversion == "de" and $count[$zl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$zl]->PKZ > ''))
    && ($zn <= $liga[0]->stamm) && ($count[$zl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$zl]->rrang)) {
            $pdf->Cell(10, $zelle, $zn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$zl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$zl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$zl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$zl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$zl]->dwz, 0, 0, 'R');
    } else {
        $pdf->Cell(50, $zelle, '', 0, 0, 'C');
        $zl--;
    }
    $pdf->Cell(10, $zelle, '', 0, 0, 'R');
    $yn++;
    $yl++;
    if (isset($count[$yl]) and (($countryversion == "de" and $count[$yl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$yl]->PKZ > ''))
        && ($count[$yl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$yl]->rrang)) {
            $pdf->Cell(10, $zelle, $yn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$yl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$yl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$yl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$yl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$yl]->dwz, 0, 1, 'R');
    } else {
        $yl--;
        $pdf->Cell(50, $zelle, '', 0, 1, 'C');
    }
    //Zeile 08
    $pdf->Cell(10, $zelle, '', 0, 0, 'C');
    $pdf->Cell(14, $zelle, JText::_('MELDELISTE_LOCATION'), 0, 0, 'L');
    if (is_null($mannschaft[$x]->lokal)) {
        $mannschaft[$x]->lokal = '';
    }
    $man = explode(",", $mannschaft[$x]->lokal);
    //if (isset($man[0])) $pdf->Cell(51,$zelle,clm_core::$load->utf8decode($man[0]),0,0);
    //else
    $pdf->Cell(51, $zelle, '', 0, 0);
    $zn++;
    $zl++;
    if (isset($count[$zl]) and (($countryversion == "de" and $count[$zl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$zl]->PKZ > ''))
        && ($zn <= $liga[0]->stamm) && ($count[$zl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$zl]->rrang)) {
            $pdf->Cell(10, $zelle, $zn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$zl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$zl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$zl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$zl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$zl]->dwz, 0, 0, 'R');
    } else {
        $pdf->Cell(50, $zelle, '', 0, 0, 'C');
        $zl--;
    }
    $pdf->Cell(10, $zelle, '', 0, 0, 'R');
    $yn++;
    $yl++;
    if (isset($count[$yl]) and (($countryversion == "de" and $count[$yl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$yl]->PKZ > ''))
        && ($count[$yl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$yl]->rrang)) {
            $pdf->Cell(10, $zelle, $yn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$yl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$yl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$yl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$yl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$yl]->dwz, 0, 1, 'R');
    } else {
        $yl--;
        $pdf->Cell(50, $zelle, '', 0, 1, 'C');
    }
    //Zeile 09
    $pdf->Cell(14, $zelle, '', 0, 0, 'C');
    if (isset($man[0])) {
        $pdf->Cell(61, $zelle, ltrim(clm_core::$load->utf8decode($man[0])), 0, 0);
    } else {
        $pdf->Cell(61, $zelle, '', 0, 0);
    }
    $zn++;
    $zl++;
    if (isset($count[$zl]) and (($countryversion == "de" and $count[$zl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$zl]->PKZ > ''))
        && ($zn <= $liga[0]->stamm) && ($count[$zl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$zl]->rrang)) {
            $pdf->Cell(10, $zelle, $zn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$zl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$zl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$zl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$zl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$zl]->dwz, 0, 0, 'R');
    } else {
        $pdf->Cell(50, $zelle, '', 0, 0, 'C');
        $zl--;
    }
    $pdf->Cell(10, $zelle, '', 0, 0, 'R');
    $yn++;
    $yl++;
    if (isset($count[$yl]) and (($countryversion == "de" and $count[$yl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$yl]->PKZ > ''))
        and ($count[$yl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$yl]->rrang)) {
            $pdf->Cell(10, $zelle, $yn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$yl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$yl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$yl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$yl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$yl]->dwz, 0, 1, 'R');
    } else {
        $yl--;
        $pdf->Cell(50, $zelle, '', 0, 1, 'C');
    }
    //Zeile 10
    $pdf->Cell(14, $zelle, '', 0, 0, 'C');
    if (isset($man[1])) {
        $pdf->Cell(61, $zelle, ltrim(clm_core::$load->utf8decode($man[1])), 0, 0);
    } else {
        $pdf->Cell(61, $zelle, '', 0, 0);
    }
    $zn++;
    $zl++;
    if (isset($count[$zl]) and (($countryversion == "de" and $count[$zl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$zl]->PKZ > ''))
        && ($zn <= $liga[0]->stamm) && ($count[$zl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$zl]->rrang)) {
            $pdf->Cell(10, $zelle, $zn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$zl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$zl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$zl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$zl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$zl]->dwz, 0, 0, 'R');
    } else {
        $pdf->Cell(50, $zelle, '', 0, 0, 'C');
        $zl--;
    }
    $pdf->Cell(10, $zelle, '', 0, 0, 'R');
    $yn++;
    $yl++;
    if (isset($count[$yl]) and (($countryversion == "de" and $count[$yl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$yl]->PKZ > ''))
        and ($count[$yl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$yl]->rrang)) {
            $pdf->Cell(10, $zelle, $yn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$yl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$yl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$yl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$yl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$yl]->dwz, 0, 1, 'R');
    } else {
        $yl--;
        $pdf->Cell(50, $zelle, '', 0, 1, 'C');
    }
    //Zeile 11
    $pdf->Cell(14, $zelle, '', 0, 0, 'C');
    if (isset($man[2])) {
        $pdf->Cell(61, $zelle, ltrim(clm_core::$load->utf8decode($man[2])), 0, 0);
    } else {
        $pdf->Cell(61, $zelle, '', 0, 0);
    }
    $zn++;
    $zl++;
    if (isset($count[$zl]) and (($countryversion == "de" and $count[$zl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$zl]->PKZ > ''))
        && ($zn <= $liga[0]->stamm) && ($count[$zl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$zl]->rrang)) {
            $pdf->Cell(10, $zelle, $zn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$zl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$zl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$zl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$zl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$zl]->dwz, 0, 0, 'R');
    } else {
        $pdf->Cell(50, $zelle, '', 0, 0, 'C');
        $zl--;
    }
    $pdf->Cell(10, $zelle, '', 0, 0, 'R');
    $yn++;
    $yl++;
    if (isset($count[$yl]) and (($countryversion == "de" and $count[$yl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$yl]->PKZ > ''))
        && ($count[$yl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$yl]->rrang)) {
            $pdf->Cell(10, $zelle, $yn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$yl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$yl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$yl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$yl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$yl]->dwz, 0, 1, 'R');
    } else {
        $yl--;
        $pdf->Cell(50, $zelle, '', 0, 1, 'C');
    }
    //Zeile 12
    $pdf->Cell(14, $zelle, '', 0, 0, 'C');
    if (isset($man[3])) {
        $pdf->Cell(61, $zelle, ltrim(clm_core::$load->utf8decode($man[3])), 0, 0);
    } else {
        $pdf->Cell(61, $zelle, '', 0, 0);
    }
    $zn++;
    $zl++;
    if (isset($count[$zl]) and (($countryversion == "de" and $count[$zl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$zl]->PKZ > ''))
        && ($zn <= $liga[0]->stamm) && ($count[$zl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$zl]->rrang)) {
            $pdf->Cell(10, $zelle, $zn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$zl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$zl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$zl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$zl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$zl]->dwz, 0, 0, 'R');
    } else {
        $pdf->Cell(50, $zelle, '', 0, 0, 'C');
        $zl--;
    }
    $pdf->Cell(10, $zelle, '', 0, 0, 'R');
    $yn++;
    $yl++;
    if (isset($count[$yl]) and (($countryversion == "de" and $count[$yl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$yl]->PKZ > ''))
        && ($count[$yl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$yl]->rrang)) {
            $pdf->Cell(10, $zelle, $yn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$yl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$yl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$yl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$yl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$yl]->dwz, 0, 1, 'R');
    } else {
        $yl--;
        $pdf->Cell(50, $zelle, '', 0, 1, 'C');
    }
    //Zeile 13
    $pdf->Cell(14, $zelle, '', 0, 0, 'C');
    if (isset($man[4])) {
        $pdf->Cell(61, $zelle, ltrim(clm_core::$load->utf8decode($man[4])), 0, 0);
    } else {
        $pdf->Cell(61, $zelle, '', 0, 0);
    }
    $zn++;
    $zl++;
    if (isset($count[$zl]) and (($countryversion == "de" and $count[$zl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$zl]->PKZ > ''))
        && ($zn <= $liga[0]->stamm) && ($count[$zl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$zl]->rrang)) {
            $pdf->Cell(10, $zelle, $zn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$zl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$zl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$zl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$zl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$zl]->dwz, 0, 0, 'R');
    } else {
        $pdf->Cell(50, $zelle, '', 0, 0, 'C');
        $zl--;
    }
    $pdf->Cell(10, $zelle, '', 0, 0, 'R');
    $yn++;
    $yl++;
    if (isset($count[$yl]) and (($countryversion == "de" and $count[$yl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$yl]->PKZ > ''))
        && ($count[$yl]->tln_nr == $mannschaft[$x]->tln_nr)) {
        $zzc++;
        if (!isset($count[$yl]->rrang)) {
            $pdf->Cell(10, $zelle, $yn, 0, 0, 'C');
        } else {
            $pdf->Cell(10, $zelle, ($count[$yl]->rrang), 0, 0, 'C');
        }
        if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$yl]->name)) > $breite1) {
            $htext = clm_core::$load->utf8decode($count[$yl]->name);
            while ($pdf->GetStringWidth($htext) > $breite1) {
                $htext = substr($htext, 0, -1);
            }
            $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
        } else {
            $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$yl]->name), 0, 0, 'L');
        }
        $pdf->Cell(10, $zelle, $count[$yl]->dwz, 0, 1, 'R');
    } else {
        $yl--;
        $pdf->Cell(50, $zelle, '', 0, 1, 'C');
    }
    //Zeile 14ff
    if ($zl < 0) {
        $zl = 0;
    }
    while ((isset($count[$zl + 1]) and (($countryversion == "de" and $count[$zl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$zl]->PKZ > '')) and (($zn + 1) <= $liga[0]->stamm)) or
           (isset($count[$yl + 1]) and (($countryversion == "de" and $count[$yl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$yl]->PKZ > '')) and ($count[$yl + 1]->tln_nr == $mannschaft[$x]->tln_nr))) {
        $pdf->Cell(10, $zelle, '', 0, 0, 'C');
        $pdf->Cell(65, $zelle, '', 0, 0, 'L');
        $zn++;
        $zl++;
        if (isset($count[$zl]) and (($countryversion == "de" and $count[$zl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$zl]->PKZ > ''))
            && ($zn <= $liga[0]->stamm) && ($count[$zl]->tln_nr == $mannschaft[$x]->tln_nr)) {
            $zzc++;
            if (!isset($count[$zl]->rrang)) {
                $pdf->Cell(10, $zelle, $zn, 0, 0, 'C');
            } else {
                $pdf->Cell(10, $zelle, ($count[$zl]->rrang), 0, 0, 'C');
            }
            if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$zl]->name)) > $breite1) {
                $htext = clm_core::$load->utf8decode($count[$zl]->name);
                while ($pdf->GetStringWidth($htext) > $breite1) {
                    $htext = substr($htext, 0, -1);
                }
                $pdf->Cell($breite1, $zelle, $htext, 0, 0, 'L');
            } else {
                $pdf->Cell($breite1, $zelle, clm_core::$load->utf8decode($count[$zl]->name), 0, 0, 'L');
            }
            $pdf->Cell(10, $zelle, $count[$zl]->dwz, 0, 0, 'R');
        } else {
            $pdf->Cell(50, $zelle, '', 0, 0, 'C');
            $zl--;
        }
        $pdf->Cell(10, $zelle, '', 0, 0, 'R');
        $yn++;
        $yl++;
        if ((($countryversion == "de" and $count[$yl]->mgl_nr !== '0') or ($countryversion != "de" and $count[$yl]->PKZ > ''))
            && ($count[$yl]->tln_nr == $mannschaft[$x]->tln_nr)) {
            $zzc++;
            if (!isset($count[$yl]->rrang)) {
                $pdf->Cell(10, $zelle, $yn, 0, 0, 'C');
            } else {
                $pdf->Cell(10,$zelle,($count[$yl]->rrang),0,0,'C');
            }
            if ($pdf->GetStringWidth(clm_core::$load->utf8decode($count[$yl]->name)) > $breite1) {
                $htext = clm_core::$load->utf8decode($count[$yl]->name);
                while ($pdf->GetStringWidth($htext) > $breite1) {
                    $htext = substr($htext,0,-1);
                }
                $pdf->Cell($breite1,$zelle,$htext,0,0,'L');
            } else {
                $pdf->Cell($breite1,$zelle,clm_core::$load->utf8decode($count[$yl]->name),0,0,'L');
            }
            $pdf->Cell(10,$zelle,$count[$yl]->dwz,0,1,'R');
        } else {
            $yl--;
            $pdf->Cell(50,$zelle,'',0,1,'C');
        }
    }
    $pdf->Ln();

    $zz = $zz + $zzc;
}
// Ende Teilnehmer
$pdf->Ln();

if ($liga[0]->bemerkungen <> "") {
    $yy1 = 320;
    $xx2 = (strlen($liga[0]->bemerkungen) / 100) + 1;
    $yy0 = $yy1 - ((4 * $xx2) + 40);
    if ($pdf->GetY() > $yy0) {
        $pdf->AddPage();

        $pdf->SetFont('Times','',7);
        $pdf->Cell(10,3,' ',0,0);
        $pdf->Cell(175,3,clm_core::$load->utf8decode(JText::_('WRITTEN')).' '.clm_core::$load->utf8decode(JText::_('ON_DAY')).' '.clm_core::$load->utf8decode(JHTML::_('date',  $now, JText::_('DATE_FORMAT_CLM_PDF'))),0,1,'R');

        $pdf->SetFont('Times','',14);
        $pdf->Cell(10,15,' ',0,0);
        $pdf->Cell(80,15,clm_core::$load->utf8decode($liga[0]->name)." ".clm_core::$load->utf8decode($saison[0]->name),0,1,'L');
        $pdf->Ln();
    }
    $pdf->SetFont('Times','',$font + 1);
    $pdf->Cell(10,$zelle,' ',0,0,'L');
    $pdf->Cell(150,$zelle,JText::_('NOTICE_SL').' :',0,1,'B');
    $pdf->SetFont('Times','',$font);
    $pdf->Cell(15,$zelle,' ',0,0,'L');
    if (is_null($liga[0]->bemerkungen)) {
        $liga[0]->bemerkungen = '';
    }
    if ($liga[0]->bemerkungen == clm_core::$load->utf8decode($liga[0]->bemerkungen)) {
        $pdf->MultiCell(150,$zelle,clm_core::$load->utf8decode($liga[0]->bemerkungen),0,'L',0);
    } else {
        $pdf->MultiCell(150,$zelle,iconv("UTF-8", "CP1252", $liga[0]->bemerkungen),0,'L',0);
    }
    $pdf->Ln();
}
if (isset($liga[0]->sl)) {
    $yy1 = 320;
    $yy0 = $yy1 - 54;
    if ($pdf->GetY() > $yy0) {
        $pdf->AddPage();

        $pdf->SetFont('Times','',7);
        $pdf->Cell(10,3,' ',0,0);
        $pdf->Cell(175,3,clm_core::$load->utf8decode(JText::_('WRITTEN')).' '.clm_core::$load->utf8decode(JText::_('ON_DAY')).' '.clm_core::$load->utf8decode(JHTML::_('date',  $now, JText::_('DATE_FORMAT_CLM_PDF'))),0,1,'R');

        $pdf->SetFont('Times','',14);
        $pdf->Cell(10,15,' ',0,0);
        $pdf->Cell(80,15,clm_core::$load->utf8decode($liga[0]->name)." ".clm_core::$load->utf8decode($saison[0]->name),0,1,'L');
        $pdf->Ln();
    }
    $pdf->SetFont('Times','',$font + 1);
    $pdf->Cell(10,$zelle,' ',0,0,'L');
    $pdf->Cell(150,$zelle,JText::_('CHIEF').' :',0,1,'L');
    $pdf->SetFont('Times','',$font);
    $pdf->Cell(15,$zelle,' ',0,0,'L');
    if (is_null($liga[0]->sl)) {
        $liga[0]->sl = '';
    }
    $pdf->Cell(150,$zelle,clm_core::$load->utf8decode($liga[0]->sl),0,1,'L');
    $pdf->Cell(15,$zelle,' ',0,0,'L');
    if ($jid > 0 or $show_sl_mail > 0) {
        $pdf->Cell(150,$zelle,$liga[0]->email,0,1,'L');
    } else {
        $pdf->Cell(150,$zelle,'',0,1,'L');
    }
    $pdf->Ln();
}

// Ausgabe
$pdf->Output(JText::_('MELDELISTE').' '.clm_core::$load->utf8decode($liga[0]->name).'.pdf','D');
exit;
