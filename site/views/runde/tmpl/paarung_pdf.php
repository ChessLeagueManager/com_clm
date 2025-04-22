<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
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
//Liga-Parameter aufbereiten
$paramsStringArray = explode("\n", $liga[0]->params);
$params = array();
foreach ($paramsStringArray as $value) {
    $ipos = strpos($value, '=');
    if ($ipos !== false) {
        $key = substr($value, 0, $ipos);
        if (substr($key, 0, 2) == "\'") {
            $key = substr($key, 2, strlen($key) - 4);
        }
        if (substr($key, 0, 1) == "'") {
            $key = substr($key, 1, strlen($key) - 2);
        }
        $params[$key] = substr($value, $ipos + 1);
    }
}
if (!isset($params['color_order'])) {   //Standardbelegung
    $params['color_order'] = '1';
}
switch ($params['color_order']) {
    case '1': $colorstr = '01';
        break;
    case '2': $colorstr = '10';
        break;
    case '3': $colorstr = '0110';
        break;
    case '4': $colorstr = '1001';
        break;
    case '5': $colorstr = '00';
        break;
    case '6': $colorstr = '11';
        break;
    default: $colorstr = '01';
}
if (!isset($params['ReportForm'])) {   // sollte nicht vorkommen!Standardbelegung
    $params['ReportForm'] = '1';
}
if (!isset($params['round_date'])) {   // sollte nicht vorkommen!Standardbelegung
    $params['round_date'] = '0';
}

$paar = $this->paar;
$ok = $this->ok;
// Variblen aus URL holen
$sid 		= clm_core::$load->request_int('saison', 1);
$runde		= clm_core::$load->request_int('runde', 1);
$dg		= clm_core::$load->request_int('dg', 1);
$paarung		= clm_core::$load->request_int('paarung', 1);
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
}

function RotatedText($x, $y, $txt, $angle)
{
    //Text rotated around its origin
    $this->Rotate($angle, $x, $y);
    $this->Text($x, $y, $txt);
    $this->Rotate(0);
}

function RotatedImage($file, $x, $y, $w, $h, $angle)
{
    //Image rotated around its upper-left corner
    $this->Rotate($angle, $x, $y);
    $this->Image($file, $x, $y, $w, $h);
    $this->Rotate(0);
}

$pdf_orientation = 'L';
$_POST['pdf_orientation'] = $pdf_orientation;
//echo "<br>pdf_orientation: ".clm_core::$load->request_string('pdf_orientation','X'); die();
if ($pdf_orientation == 'L') {
    $pdf_width = 295;
    $pdf_length = 180;
    // Seitenlänge
    $lspalte_paar = 170;
    $lspalte_tab = 150;
    $lspalte_comment = 140;
    $lspalte = 180;
} else {
    $pdf_width = 195;
    $pdf_length = 240;
    // Seitenlänge
    $lspalte_paar = 230;
    $lspalte_tab = 210;
    $lspalte_comment = 200;
    $lspalte = 240;
}
// Zellenhöhe -> Standard 6
$zelle = 8;
// Zellenbreiten
if ($pdf_orientation == 'L') {
    $breite1 = 38;  //name
    $breite = 89;   //spalte
} else {
    $breite1 = 38;  //name
    $breite = 89;   //spalte
}
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
$pdf->AddPage($pdf_orientation);

$pdf->SetFont('Times', '', $date_font);
$pdf->Cell(10, 4, ' ', 0, 0);
if ($pdf_orientation == 'L') {
    $pdf->Cell(265, 4, clm_core::$load->utf8decode(JText::_('WRITTEN')).' '.clm_core::$load->utf8decode(JText::_('ON_DAY')).' '.clm_core::$load->utf8decode(JHTML::_('date', $now, JText::_('DATE_FORMAT_CLM_PDF'))), 0, 1, 'R');
} else {
    $pdf->Cell(175, 4, clm_core::$load->utf8decode(JText::_('WRITTEN')).' '.clm_core::$load->utf8decode(JText::_('ON_DAY')).' '.clm_core::$load->utf8decode(JHTML::_('date', $now, JText::_('DATE_FORMAT_CLM_PDF'))), 0, 1, 'R');
}
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

    $pdf->SetFont('Times', 'B', $head_font + 4);
    $pdf->Cell(10, 6, ' ', 0, 0);
    $pdf->Cell(50, 6, clm_core::$load->utf8decode(JText::_('PAIRING_FORM')), 0, 0, 'C');
    $pdf->SetFont('Times', 'B', $head_font);
    $pdf->Cell(($pdf_width - 125), 6, clm_core::$load->utf8decode($liga[0]->name)." ".$liga[0]->saison_name, 0, 0, 'C');
    $pdf->Cell(50, 6, '', 0, 1);
    $pdf->SetFont('Times', '', $head_font - 1);
    $pdf->Cell(10, 5, ' ', 0, 0);
    //	if ($params['round_date'] == '1' AND $paar[$paarung]->pdate > '1970-01-01') {
    if ($params['round_date'] == '1' and $paar[$paarung - 1]->pdate > '1970-01-01') {
        $pdf_title = clm_core::$load->utf8decode($runden_text).JText::_('PAIRING').$paarung.' '.JText::_('ON_DAY').' '.clm_core::$load->utf8decode(JHTML::_('date', $paar[$paarung - 1]->pdate, JText::_('DATE_FORMAT_CLM_F')));
        if (isset($paar[$paarung - 1]->ptime) and $paar[$paarung - 1]->ptime != '00:00:00') {
            $pdf_title .= '  '.substr($paar[$paarung - 1]->ptime, 0, 5).' Uhr';
        }
        $pdf->Cell(($pdf_width - 25), 5, $pdf_title, 0, 1, 'C');
    } elseif ($liga[$runde - 1]->datum > '1970-01-01') {
        $pdf_title = clm_core::$load->utf8decode($runden_text).JText::_('PAIRING').$paarung.' '.JText::_('ON_DAY').' '.clm_core::$load->utf8decode(JHTML::_('date', $liga[$runde - 1]->datum, JText::_('DATE_FORMAT_CLM_F')));
        if (isset($liga[$runde - 1]->startzeit) and $liga[$runde - 1]->startzeit != '00:00:00') {
            $pdf_title .= '  '.substr($liga[$runde - 1]->startzeit, 0, 5).' Uhr';
        }
        $pdf->Cell(($pdf_width - 25), 5, $pdf_title, 0, 1, 'C');
    } else {
        $pdf->Cell(($pdf_width - 25), 5, clm_core::$load->utf8decode($runden_text).JText::_('PAIRING').$paarung, 0, 1, 'C');
    }
    $pdf->SetFont('Times', '', $font);
    $pdf->Cell(10, 3, ' ', 0, 1);
    $pdf->Cell(10, $zelle, ' ', 0, 0);
    //$pdf->Ln();
    // Teilnehmerschleife
    if ($pdf_orientation == 'L') {
        $xxl = 15; // linke Spalte
        $xxm = 67; // mittlere Spalte
        $xxr = 243; // rechte Spalte
    } else {
        $xxl = 3; // linke Spalte
        $xxm = 51; // mittlere Spalte
        $xxr = 166; // rechte Spalte
    }
    for ($y = 0; $y < ($liga[0]->teil) / 2; $y++) {
        if (!isset($paar[$y])) {
            break;
        }
        if (($y + 1) != $paarung) {
            continue;
        }

        $pdf->SetFont('Times', 'B', $date_font + 4);
        $breite0 = 7;
        $breite1 = 71;
        $pdf->SetX($xxm);
        if (isset($paar[$y]->hpublished) and $paar[$y]->hpublished == 1 and isset($paar[$y]->hname)) {
            //		if ($ms) {
            $pdf->Cell($breite0, $zelle + 1, '', 'LTB', 0);
            //			$pdf->Cell($breite1,$zelle+1,clm_core::$load->utf8decode($paar[$y]->hname),'TB',0,'L');
            $pdf->Cell($breite1, $zelle + 1, clm_core::$load->utf8decode($paar[$y]->hname), 'TB', 0, 'R');
        } else {
            $pdf->Cell($breite1 + 7, $zelle + 1, '', 0, 0);
        }

        $pdf->Cell(16, $zelle + 1, ' - ', 'TB', 0, 'C');
        $breite1 = 78;

        if (isset($paar[$y]->gpublished) and $paar[$y]->gpublished == 1 and isset($paar[$y]->gname)) {
            $pdf->Cell($breite1, $zelle + 1, clm_core::$load->utf8decode($paar[$y]->gname), 'RTB', 0, 'L');
        } else {
            $pdf->Cell($breite1 + 7, $zelle + 1, '', 'RTB', 0);
        }
        $pdf->Cell(5, $zelle + 1, '', 0, 1, 'C');
        $pdf->SetFont('Times', '', $date_font);
        // Bretter
        $yy = $pdf->GetY();
        $y1 = 0;
        $pdf->SetFillColor(230);
        $pdf->SetTextColor(0);
        for ($x = 0; $x < $liga[0]->stamm; $x++) {
            if ($x % 2 != 0) {
                $fc = 1;
            } else {
                $fc = 0;
            }
            $schwarz = substr($colorstr, $y1, 1);
            if ($schwarz == 1) {
                $weiss = 0;
            } else {
                $weiss = 1;
            }
            $y1++;
            if ($y1 >= strlen($colorstr)) {
                $y1 = 0;
            }
            if ($params['ReportForm'] == '1') { 	//mit Melde-Nr.
                $breite2 = 7;
                $breite1 = 71;
                if ($ms) {
                    $htext = JText::_('PAIRING_ST_NO');
                } else {
                    $htext = JText::_('PAIRING_RANK_NO');
                    $breite2 = 14;
                    $breite1 = 64;
                }
            } else {								// 2 mit Mitgl.Nr.
                $breite2 = 11;
                $breite1 = 67;
                $htext = JText::_('PAIRING_PA_NO');
            }
            if ($x == 0) {
                $pdf->SetX($xxm - 5);
                $pdf->SetFont('Times', '', $font - 3);
                $zelle1 = 4;
                $pdf->Cell(5, $zelle1, JText::_('PAIRING_BOARD'), 1, 0, 'C');
                $pdf->Cell($breite2, $zelle1, $htext, 1, 0, 'C');
                $pdf->Cell($breite1, $zelle1, JText::_('PAIRING_NAME'), 1, 0, 'C');

                $pdf->Cell(16, $zelle1, JText::_('PAIRING_RESULT'), 1, 0, 'C');

                $pdf->Cell($breite1, $zelle1, JText::_('PAIRING_NAME'), 1, 0, 'C');
                $pdf->Cell($breite2, $zelle1, $htext, 1, 1, 'C');
                $pdf->SetFont('Times', '', $font);
            }
            $pdf->SetX($xxm - 5);
            $pdf->Cell(5, $zelle, $x + 1, 1, 0, 'C');
            $pdf->Cell($breite2, $zelle, '', 1, 0, 'C', $weiss);
            $pdf->Cell($breite1, $zelle, '', 1, 0, 'C', $weiss);

            $pdf->Cell(8, $zelle, '', 1, 0, 'C', $weiss);
            $pdf->Cell(8, $zelle, '', 1, 0, 'C', $schwarz);

            $pdf->Cell($breite1, $zelle, '', 1, 0, 'C', $schwarz);
            $pdf->Cell($breite2, $zelle, '', 1, 1, 'C', $schwarz);
        }
        // Ergebnis Mannschaft
        $breite1 = 74;
        $pdf->SetX($xxm - 5);
        //if (($hsum + $gsum) != 0) {
        $pdf->Cell(5, $zelle + 3, 'G', 1, 0);
        $pdf->Cell(4, $zelle + 3, '', 'LTB', 0);
        $pdf->Cell($breite1, $zelle + 3, '.....', 'TB', 0, 'R');
        $pdf->Cell(16, $zelle + 3, ' - ', 'TB', 0, 'C');
        $pdf->Cell($breite1, $zelle + 3, '.....', 'TB', 0, 'L');
        $pdf->Cell(4, $zelle + 3, '', 'RTB', 1);
    }

    // Kommentar zur Paarung
    $pdf->SetX($xxm - 5);
    $pdf->Cell($breite, 2, '', 0, 1, 'C');
    $pdf->SetX($xxm - 5);
    $pdf->Cell($breite + 5, 6, clm_core::$load->utf8decode(JText::_('PAIRING_COMMENT')), 0, 1, 'L');
    $pdf->SetX($xxm - 5);
    $pdf->Ln();
    $pdf->Ln();

    // Unterschrift
    $pdf->SetX($xxm);
    $pdf->Ln();
    $pdf->SetX($xxm);
    $pdf->Cell($breite2, $zelle, '', 0, 0, 'C');
    $pdf->Cell($breite1, $zelle, '.................', 0, 0, 'C');

    $pdf->Cell(10, $zelle, '', 0, 0, 'C');

    $pdf->Cell($breite1, $zelle, '.................', 0, 0, 'C');
    $pdf->Cell($breite2, $zelle, '', 0, 1, 'C');
    $pdf->SetX($xxm);
    $pdf->Cell($breite2, $zelle - 4, '', 0, 0, 'C');
    $pdf->Cell($breite1, $zelle - 4, JText::_('PAIRING_TCHOME'), 0, 0, 'C');

    $pdf->Cell(10, $zelle - 4, '', 0, 0, 'C');

    $pdf->Cell($breite1, $zelle - 4, JText::_('PAIRING_TCGUEST'), 0, 0, 'C');
    $pdf->Cell($breite2, $zelle - 4, '', 0, 1, 'C');

    // Schiedsrichter
    $pdf->SetX($xxm - 5);
    $pdf->Cell($breite, $zelle, '', 0, 1, 'C');
    $pdf->SetX($xxm - 5);
    $pdf->Cell(22, 6, clm_core::$load->utf8decode(JText::_('PAIRING_ARBITER_NAME').': '), 0, 0, 'L');
    $pdf->Cell(50, $zelle, '..........................................', 0, 0, 'L');
    $pdf->Cell(1, $zelle, '', 0, 0, 'C');

    $pdf->Cell(18, 6, clm_core::$load->utf8decode(JText::_('PAIRING_ARBITER').': '), 0, 0, 'L');
    $pdf->Cell(24, $zelle, '...................', 0, 0, 'L');
    $pdf->Cell(10, $zelle, '', 0, 1, 'C');

    if ($liga[0]->anzeige_ma == 0) {
        $pdf->SetY($yy);
        $pdf->SetFont('Times', '', $font - 2);
        $team = CLMModelRunde::team_tlnr($liga[0]->id, $paar[$paarung - 1]->htln);
        //echo "<br>liga: ".$liga[0]->id."  tln: ".$paar[$paarung-1]->htln."  <br>"; var_dump($team);
        for ($i = 0; $i < count($team); $i++) {
            if ($i > 30) {
                break;
            }
            $pdf->SetX($xxl);
            if ($params['ReportForm'] == '1') { 	//mit Melde-Nr.
                if ($ms) {
                    $pdf->Cell(3, $zelle / 2, $team[$i]->snr, 0, 0, 'C');
                } else {
                    // oder Rangnummer
                    // $pdf->Cell(3,$zelle/2,$team[$i]->tman_nr . "-" . $team[$i]->trang,0,0,'C');
                    $pdf->Cell(6, $zelle / 2, $team[$i]->trang, 0, 0, 'C');
                }
                $strikeout_x = $pdf->GetX();
                $pdf->Cell(32, $zelle / 2, clm_core::$load->utf8decode($team[$i]->Spielername), 0, 0, 'L');
                if ($team[$i]->gesperrt == 1) {
                    $pdf->SetX($strikeout_x);
                    $pdf->Cell(32, $zelle / 2, '------------------------', 0, 0, 'L');
                }
            } else {								// 2 mit Mitgl.Nr.
                $pdf->Cell(5, $zelle / 2, $team[$i]->mgl_nr, 0, 0, 'C');
                $strikeout_x = $pdf->GetX();
                $pdf->Cell(30, $zelle / 2, clm_core::$load->utf8decode($team[$i]->Spielername), 0, 0, 'L');
                if ($team[$i]->gesperrt == 1) {
                    $pdf->SetX($strikeout_x);
                    $pdf->Cell(30, $zelle / 2, '------------------------', 0, 0, 'L');
                }
            }
            $pdf->Cell(7, $zelle / 2, $team[$i]->dwz, 0, 1, 'C');
        }

        $pdf->SetY($yy);
        $team = CLMModelRunde::team_tlnr($liga[0]->id, $paar[$paarung - 1]->gtln);
        //echo "<br>liga: ".$liga[0]->id."  tln: ".$paar[$paarung-1]->htln."  <br>"; var_dump($team);
        for ($i = 0; $i < count($team); $i++) {
            if ($i > 30) {
                break;
            }
            $pdf->SetX($xxr);
            if ($params['ReportForm'] == '1') { 	//mit Melde-Nr.
                if ($ms) {
                    $pdf->Cell(3,$zelle / 2,$team[$i]->snr,0,0,'C');
                } else {
                    // oder Rangnummer
                    // $pdf->Cell(3,$zelle/2,$team[$i]->tman_nr . "-" . $team[$i]->trang,0,0,'C');
                    $pdf->Cell(6,$zelle / 2,$team[$i]->trang,0,0,'C');
                }
                $strikeout_x = $pdf->GetX();
                $pdf->Cell(32,$zelle / 2,clm_core::$load->utf8decode($team[$i]->Spielername),0,0,'L');
                if ($team[$i]->gesperrt == 1) {
                    $pdf->SetX($strikeout_x);
                    $pdf->Cell(32,$zelle / 2,'------------------------',0,0,'L');
                }
            } else {								// 2 mit Mitgl.Nr.
                $pdf->Cell(5,$zelle / 2,$team[$i]->mgl_nr,0,0,'C');
                $strikeout_x = $pdf->GetX();
                $pdf->Cell(30,$zelle / 2,clm_core::$load->utf8decode($team[$i]->Spielername),0,0,'L');
                if ($team[$i]->gesperrt == 1) {
                    $pdf->SetX($strikeout_x);
                    $pdf->Cell(30,$zelle / 2,'------------------------',0,0,'L');
                }
            }
            $pdf->Cell(7,$zelle / 2,$team[$i]->dwz,0,1,'C');
        }
    }
}
// Ausgabe
$pdf->Output(clm_core::$load->utf8decode($runde.".".$paarung.".".clm_core::$load->utf8decode(JText::_('PAIRING_FORM')).' ').clm_core::$load->utf8decode($liga[0]->name).'.pdf','D');
exit;
