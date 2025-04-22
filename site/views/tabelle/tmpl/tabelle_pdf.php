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

$lid = clm_core::$load->request_int('liga', '1');
$sid = clm_core::$load->request_int('saison', '1');
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
//$dwzschnitt=$this->dwzschnitt;
$saison     = $this->saison;

$name_liga = $liga[0]->name;
// Test MP als Feinwertung -> d.h. Spalte MP als Hauptwertung wird dann unterdrückt
if ($liga[0]->tiebr1 == 9 or $liga[0]->tiebr2 == 9 or $liga[0]->tiebr3 == 9) {
    $columnMP = 0;
} else {
    $columnMP = 1;
}

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

// DWZ Durchschnitte - Aufstellung
$result = clm_core::$api->db_nwz_average($lid);
//echo "<br>lid:"; var_dump($lid);
//echo "<br>result:"; var_dump($result);
$a_average_dwz_lineup = $result[2];
//echo "<br>a_average_dwz_p:"; var_dump($a_average_dwz_p);

// Konfigurationsparameter auslesen
$config = clm_core::$db->config();
$show_sl_mail = $config->show_sl_mail;

// Userkennung holen
$user	= JFactory::getUser();
$jid	= $user->get('id');

// Spielfreie Teilnehmer finden
$diff = $spielfrei[0]->count;

// Zellenhöhe -> Standard 6
$zelle = 6;
// Wert von Zellenbreite abziehen
// Bspl. für Standard (Null) für Liga mit 11 Runden und 1 Durchgang
$breite = 0;
$rbreite = 0;
$nbreite = 0;
/*if ((($liga[0]->teil - $diff) * $liga[0]->durchgang) > 11) {
    $breite = 1;
    $rbreite = 1;
    $nbreite = 2; }
if ((($liga[0]->teil - $diff) * $liga[0]->durchgang) > 14) {
    $rbreite = 2;
    $nbreite = 10; } */
// Überschrift Fontgröße Standard = 14
$head_font = 14;
// Fontgröße Standard = 9
$font = 9;
// Fontgröße Standard = 8
$date_font = 8;
// Leere Zelle zum zentrieren
$leer = (3 * (10 - ($liga[0]->teil - $diff))) - $rbreite;
if ($liga[0]->b_wertung == 0) {
    $leer = $leer + 4;
}
if ($leer < 3) {
    $leer = 2;
}

// Datum der Erstellung
$date = JFactory::getDate();
$now = $date->toSQL();


$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Times', '', $date_font);
$pdf->Cell(10, 3, ' ', 0, 0);
$pdf->Cell(175, 2, clm_core::$load->utf8decode(JText::_('WRITTEN')).' '.clm_core::$load->utf8decode(JText::_('ON_DAY')).' '.clm_core::$load->utf8decode(JHTML::_('date', $now, JText::_('DATE_FORMAT_CLM_PDF'))), 0, 1, 'R');

$pdf->SetFont('Times', 'B', $head_font + 2);
$pdf->Cell(180, 15, clm_core::$load->utf8decode($liga[0]->name), 0, 1, 'C');
$pdf->Cell(180, 10, clm_core::$load->utf8decode($saison[0]->name), 0, 1, 'C');
$pdf->Ln(30);
$pdf->SetFont('Times', '', $font + 2);
$pdf->SetFillColor(100);
$pdf->SetTextColor(255);
// max. Länge des Names bestimmen
$lmax = 0;
for ($x = 0; $x < ($liga[0]->teil) - $diff; $x++) {
    $n = $pdf->GetStringWidth(clm_core::$load->utf8decode($punkte[$x]->name));
    if ($n > $lmax) {
        $lmax = $n;
    }
}
if ($lmax < (50 - $nbreite)) {
    $lmax = 50 - $nbreite;
}
if ($lmax > 66) {
    $lmax = 66;
}
$pdf->Cell($leer, $zelle, ' ', 0, 0, 'L');
$pdf->Cell(7 - $rbreite, $zelle, JText::_('RANG'), 1, 0, 'C', 1);
$pdf->Cell(7 - $rbreite, $zelle, JText::_('TLN'), 1, 0, 'C', 1);
//$pdf->Cell(60-$nbreite-$breite,$zelle,JText::_('TEAM'),1,0,'L',1);
$pdf->Cell($lmax + 12 - $breite, $zelle, JText::_('TEAM'), 1, 0, 'L', 1);

$pdf->Cell(7 - $rbreite, $zelle, JText::_('TABELLE_GAMES_PLAYED'), 1, 0, 'C', 1);
$pdf->Cell(7 - $rbreite, $zelle, JText::_('TABELLE_WINS'), 1, 0, 'C', 1);
$pdf->Cell(7 - $rbreite, $zelle, JText::_('TABELLE_DRAW'), 1, 0, 'C', 1);
$pdf->Cell(7 - $rbreite, $zelle, JText::_('TABELLE_LOST'), 1, 0, 'C', 1);
if ($columnMP == 1) {
    $pdf->Cell(8 - $rbreite, $zelle, JText::_('MP'), 1, 0, 'C', 1);
}
if ($liga[0]->liga_mt == 0) {
    $pdf->Cell(10 - $breite, $zelle, JText::_('BP'), 1, 0, 'C', 1);
    if ($liga[0]->b_wertung > 0) {
        $pdf->Cell(10 - $breite, $zelle, JText::_('WP'), 1, 0, 'C', 1);
    }
} else {
    if ($liga[0]->tiebr1 > 0 and $liga[0]->tiebr1 < 50) {
        $pdf->Cell(13 - $breite, $zelle, JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr1), 1, 0, 'C', 1);
    }
    if ($liga[0]->tiebr2 > 0 and $liga[0]->tiebr2 < 50) {
        $pdf->Cell(13 - $breite, $zelle, JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr2), 1, 0, 'C', 1);
    }
    if ($liga[0]->tiebr3 > 0 and $liga[0]->tiebr3 < 50) {
        $pdf->Cell(13 - $breite, $zelle, JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr3), 1, 0, 'C', 1);
    }
}
$pdf->Ln();

// Anzahl der Teilnehmer durchlaufen
$pdf->SetFillColor(240);
$pdf->SetTextColor(0);
for ($x = 0; $x < ($liga[0]->teil) - $diff; $x++) {
    if (!isset($punkte[$x])) {
        continue;
    }
    if ($x % 2 != 0) {
        $fc = 1;
    } else {
        $fc = 0;
    }
    $pdf->Cell($leer, $zelle, ' ', 0, 0, 'L');
    //	$pdf->Cell(7-$rbreite,$zelle,$x+1,1,0,'C',$fc);
    $pdf->Cell(7 - $rbreite, $zelle, $punkte[$x]->rankingpos, 1, 0, 'C', $fc);
    $pdf->Cell(7 - $rbreite, $zelle, $punkte[$x]->tln_nr, 1, 0, 'C', $fc);
    //$pdf->Cell(50-$nbreite,$zelle,clm_core::$load->utf8decode($punkte[$x]->name),1,0,'L',$fc);
    while (($lmax) < $pdf->GetStringWidth(clm_core::$load->utf8decode($punkte[$x]->name))) {
        $punkte[$x]->name = substr($punkte[$x]->name, 0, -1);
    }
    $pdf->Cell($lmax + 2, $zelle, clm_core::$load->utf8decode($punkte[$x]->name), 1, 0, 'L', $fc);
    //if (isset($dwz[($punkte[$x]->tln_nr)])) $pdf->Cell(10-$breite,$zelle,round($dwz[($punkte[$x]->tln_nr)]),1,0,'C',$fc);
    //else $pdf->Cell(10-$breite,$zelle,'',1,0,'C',$fc);
    $pdf->Cell(10 - $breite, $zelle, $a_average_dwz_lineup[$punkte[$x]->tln_nr], 1, 0, 'C', $fc);

    $pdf->Cell(7 - $rbreite, $zelle, $punkte[$x]->count_G, 1, 0, 'C', $fc);
    $pdf->Cell(7 - $rbreite, $zelle, $punkte[$x]->count_S, 1, 0, 'C', $fc);
    $pdf->Cell(7 - $rbreite, $zelle, $punkte[$x]->count_R, 1, 0, 'C', $fc);
    $pdf->Cell(7 - $rbreite, $zelle, $punkte[$x]->count_V, 1, 0, 'C', $fc);
    if ($columnMP == 1) {
        if ($punkte[$x]->abzug > 0) {
            $pdf->Cell(8 - $rbreite, $zelle, $punkte[$x]->mp.'*', 1, 0, 'C', $fc);
        } else {
            $pdf->Cell(8 - $rbreite, $zelle, $punkte[$x]->mp, 1, 0, 'C', $fc);
        }
    }
    if ($liga[0]->liga_mt == 0) {
        if ($punkte[$x]->bpabzug > 0) {
            $pdf->Cell(10 - $rbreite, $zelle, $punkte[$x]->bp.'*', 1, 0, 'C', $fc);
        } else {
            $pdf->Cell(10 - $breite, $zelle, $punkte[$x]->bp, 1, 0, 'C', $fc);
        }
        if ($liga[0]->b_wertung > 0) {
            $pdf->Cell(10 - $breite, $zelle, $punkte[$x]->wp, 1, 0, 'C', $fc);
        }
    } else {
        if ($liga[0]->tiebr1 == 5) { // Brettpunkte
            if ($punkte[$x]->bpabzug > 0) {
                $pdf->Cell(13 - $breite, $zelle, CLMText::tiebrFormat($liga[0]->tiebr1, $punkte[$x]->sumtiebr1).'*', 1, 0, 'C', $fc);
            } else {
                $pdf->Cell(13 - $breite, $zelle, CLMText::tiebrFormat($liga[0]->tiebr1, $punkte[$x]->sumtiebr1), 1, 0, 'C', $fc);
            }
        } elseif ($liga[0]->tiebr1 > 0 and $liga[0]->tiebr1 < 50) {
            $pdf->Cell(13 - $breite, $zelle, CLMText::tiebrFormat($liga[0]->tiebr1, $punkte[$x]->sumtiebr1), 1, 0, 'C', $fc);
        }
        if ($liga[0]->tiebr2 == 5) { // Brettpunkte
            if ($punkte[$x]->bpabzug > 0) {
                $pdf->Cell(13 - $breite, $zelle, CLMText::tiebrFormat($liga[0]->tiebr2, $punkte[$x]->sumtiebr2).'*', 1, 0, 'C', $fc);
            } else {
                $pdf->Cell(13 - $breite, $zelle, CLMText::tiebrFormat($liga[0]->tiebr2, $punkte[$x]->sumtiebr2), 1, 0, 'C', $fc);
            }
        } elseif ($liga[0]->tiebr2 > 0 and $liga[0]->tiebr2 < 50) {
            $pdf->Cell(13 - $breite, $zelle, CLMText::tiebrFormat($liga[0]->tiebr2, $punkte[$x]->sumtiebr2), 1, 0, 'C', $fc);
        }
        if ($liga[0]->tiebr3 == 5) { // Brettpunkte
            if ($punkte[$x]->bpabzug > 0) {
                $pdf->Cell(13 - $breite, $zelle, CLMText::tiebrFormat($liga[0]->tiebr3, $punkte[$x]->sumtiebr3).'*', 1, 0, 'C', $fc);
            } else {
                $pdf->Cell(13 - $breite, $zelle, CLMText::tiebrFormat($liga[0]->tiebr3, $punkte[$x]->sumtiebr3), 1, 0, 'C', $fc);
            }
        } elseif ($liga[0]->tiebr3 > 0 and $liga[0]->tiebr3 < 50) {
            $pdf->Cell(13 - $breite, $zelle, CLMText::tiebrFormat($liga[0]->tiebr3, $punkte[$x]->sumtiebr3), 1, 0, 'C', $fc);
        }
    }
    $pdf->Ln();
}
$pdf->Ln();
$pdf->Ln();

if (is_null($liga[0]->bemerkungen)) {
    $liga[0]->bemerkungen = '';
}
if ($liga[0]->bemerkungen <> "") {
    $pdf->SetFont('Times', 'B', $font + 2);
    $pdf->Cell(10, $zelle, ' ', 0, 0, 'L');
    $pdf->Cell(150, $zelle, ' '.clm_core::$load->utf8decode(JText::_('NOTICE_SL')).' :', 0, 1, 'B');
    $pdf->SetFont('Times', '', $font);
    $pdf->Cell(15, $zelle, ' ', 0, 0, 'L');
    $pdf->MultiCell(150, $zelle, clm_core::$load->utf8decode($liga[0]->bemerkungen), 0, 'L', 0);
    $pdf->Ln();
}

if (is_null($liga[0]->sl)) {
    $liga[0]->sl = '';
}
$pdf->SetFont('Times','B',$font + 2);
$pdf->Cell(10,$zelle,' ',0,0,'L');
$pdf->Cell(150,$zelle,JText::_('CHIEF').' :',0,1,'L');
$pdf->SetFont('Times','',$font);
$pdf->Cell(15,$zelle,' ',0,0,'L');
$pdf->Cell(150,$zelle,clm_core::$load->utf8decode($liga[0]->sl),0,1,'L');
$pdf->Cell(15,$zelle,' ',0,0,'L');
if ($jid > 0 or $show_sl_mail > 0) {
    $pdf->Cell(150,$zelle,$liga[0]->email,0,1,'L');
} else {
    $pdf->Cell(150,$zelle,'',0,1,'L');
}
$pdf->Ln();

// Ausgabe
$pdf->Output(JText::_('TABELLE').' '.clm_core::$load->utf8decode($liga[0]->name).'.pdf','D');
exit;
?>

