<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2023 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

$turnierid		= clm_core::$load->request_int('turnier', '1');
$config			= clm_core::$db->config();

$turParams = new clm_class_params($this->turnier->params);

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

// Zellenhöhe -> Standard 6
$zelle = 6;
// Zellenbreiten je Spalte
$breite = 0;
$br00 = 10;
$br01 = 50;
$br02 = 14;
$br03 = 10;
$br04 = 50;
$br05 = 14;
$br06 = 22;
$br1_6 = 170;
// Fontgröße Standard = 10
$font = 10;

// Datum der Erstellung
$date = JFactory::getDate();
$now = $date->toSQL();

$pdf = new PDF();
$pdf->AliasNbPages();

// alle Runden durchgehen
$print_round = 0;
$y_length = 300;
foreach ($this->rounds as $value) {
    // published?
    if ($value->published == 1) {
        $print_round++;
        if (($print_round == 1) or ($pdf->GetY() > (280 - $y_length))) {
            $pdf->AddPage();
            $pdf->SetFont('Times', '', 7);
            $pdf->Cell(10, 3, ' ', 0, 0);
            $pdf->Cell(175, 3, clm_core::$load->utf8decode(JText::_('WRITTEN')).' '.clm_core::$load->utf8decode(JText::_('ON_DAY')).' '.clm_core::$load->utf8decode(JHTML::_('date', $now, JText::_('DATE_FORMAT_CLM_PDF'))), 0, 1, 'R');

            $pdf->SetFont('Times', '', 14);
            $pdf->Cell(10, 15, ' ', 0, 0);
            $heading = clm_core::$load->utf8decode($this->turnier->name).": ".clm_core::$load->utf8decode(JText::_('TOURNAMENT_PAIRINGLIST'));
            $pdf->Cell(150, 15, $heading, 0, 1, 'L');
        }
        if ($print_round == 1) {
            $y_start = $pdf->GetY();
        }
        $pdf->SetFont('Times', '', $font);
        $pdf->Cell(1, $zelle, " ", 0, 1, 'C');
        $pdf->Cell($br00, $zelle, ' ', 0, 0);
        $heading = clm_core::$load->utf8decode($value->name);
        if ($value->datum != "0000-00-00" and $value->datum != "1970-01-01" and $turParams->get('displayRoundDate', 1) == 1) {
            $heading .= ', '.clm_core::$load->utf8decode(JHTML::_('date', $value->datum, JText::_('DATE_FORMAT_CLM_F')));
            if (isset($value->startzeit) and $value->startzeit != '00:00:00') {
                $heading .= '  '.substr($value->startzeit, 0, 5).' Uhr';
            }
        }
        $pdf->Cell($br1_6, $zelle, $heading, 1, 1, 'L');

        $pdf->SetFont('Times', '', $font);
        $pdf->SetTextColor(255);
        $pdf->SetFillColor(0);
        $pdf->Cell($br00, $zelle, " ", 0, 0, 'C');
        $pdf->Cell($br00, $zelle, clm_core::$load->utf8decode(JText::_('TOURNAMENT_TNR')), 1, 0, 'C', 1);
        $pdf->Cell($br01, $zelle, clm_core::$load->utf8decode(JText::_('TOURNAMENT_WHITE')), 1, 0, 'L', 1);
        $pdf->Cell($br02, $zelle, clm_core::$load->utf8decode(JText::_('TOURNAMENT_TWZ')), 1, 0, 'C', 1);
        $pdf->Cell($br03, $zelle, "-", 1, 0, 'C', 1);
        $pdf->Cell($br04, $zelle, clm_core::$load->utf8decode(JText::_('TOURNAMENT_BLACK')), 1, 0, 'L', 1);
        $pdf->Cell($br05, $zelle, clm_core::$load->utf8decode(JText::_('TOURNAMENT_TWZ')), 1, 0, 'C', 1);
        $pdf->Cell($br06, $zelle, JText::_('RESULT'), 1, 0, 'C', 1);
        $pdf->Cell(1, $zelle, " ", 0, 1, 'C');

        // alle Matches eintragen
        $pdf->SetFont('Times', '', $font);
        $pdf->SetTextColor(0);
        $m = 0; // CounterFlag für Farbe
        $nb = 0; //Tischnummer
        foreach ($this->matches[$value->nr + (($value->dg - 1) * $this->turnier->runden)] as $matches) {
            $m++; // Farbe
            if ($m % 2 != 0) {
                $pdf->SetFillColor(255);
            } else {
                $pdf->SetFillColor(240);
            }

            if (($matches->spieler != 0 and $matches->gegner != 0) or !is_null($matches->ergebnis)) {
                $nb++;
                $pdf->Cell($br00, $zelle, " ", 0, 0, 'C');
                $pdf->Cell($br00, $zelle, $nb, 1, 0, 'C', 1);
                if ($this->turnier->typ != '3' and $this->turnier->typ != '5') {
                    if (isset($this->points[$value->nr + (($value->dg - 1) * $this->turnier->runden)][$matches->spieler])) {
                        $points = $this->points[$value->nr + (($value->dg - 1) * $this->turnier->runden)][$matches->spieler];
                    } else {
                        $points = 0;
                    }
                    $pdf->Cell($br01, $zelle, clm_core::$load->utf8decode($this->players[$matches->spieler]->name)." (".$points.")", 1, 0, 'L', 1);
                } else {
                    $pdf->Cell($br01, $zelle, clm_core::$load->utf8decode($this->players[$matches->spieler]->name), 1, 0, 'L', 1);
                }
                $pdf->Cell($br02, $zelle, $this->players[$matches->spieler]->twz, 1, 0, 'C', 1);
                $pdf->Cell($br03, $zelle, "-", 1, 0, 'C', 1);
                if ($this->turnier->typ != '3' and $this->turnier->typ != '5') {
                    if (isset($this->points[$value->nr + (($value->dg - 1) * $this->turnier->runden)][$matches->gegner])) {
                        $points = $this->points[$value->nr + (($value->dg - 1) * $this->turnier->runden)][$matches->gegner];
                    } else {
                        $points = 0;
                    }
                    $pdf->Cell($br04, $zelle, clm_core::$load->utf8decode($this->players[$matches->gegner]->name)." (".$points.")", 1, 0, 'L', 1);
                } else {
                    $pdf->Cell($br04, $zelle, clm_core::$load->utf8decode($this->players[$matches->gegner]->name), 1, 0, 'L', 1);
                }
                $pdf->Cell($br05, $zelle, $this->players[$matches->gegner]->twz, 1, 0, 'C', 1);
                if (!is_null($matches->ergebnis)) {
                    if ($matches->ergebnis == 2) {
                        $ergebnis = chr(189).":".chr(189);
                    } elseif ($matches->ergebnis == 9) {
                        $ergebnis = "0:".chr(189);
                    } elseif ($matches->ergebnis == 10) {
                        $ergebnis = chr(189).":0";
                    } elseif ($matches->ergebnis == 12) {
                        $ergebnis = chr(189).":-";
                    } else {
                        $ergebnis = CLMText::getResultString($matches->ergebnis);
                    }
                    if (($this->turnier->typ == 3 or $this->turnier->typ == 5) and ($matches->tiebrS > 0 or $matches->tiebrG > 0)) {
                        $ergebnis .= '  ('.$matches->tiebrS.':'.$matches->tiebrG.')';
                    }
                } else {
                    $ergebnis = " ";
                }
                $pdf->Cell($br06,$zelle,$ergebnis,1,0,'C',1);
                $pdf->Cell(1,$zelle," ",0,1,'C');
            }
        }
        if ($print_round == 1) {
            $y_length = $pdf->GetY() - $y_start;
        }
    }
}

// Ausgabe
$pdf->Output(clm_core::$load->utf8decode(JText::_('TOURNAMENT_PAIRINGLIST'))." ".clm_core::$load->utf8decode($this->turnier->name).'.pdf','D');
exit;
