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
// Sorgt dafür das nur von innerhalb des Joomla Frameworks aufgerufen werden kann ! Serh wichtige Sicherheistmaßnahem um diversen Attacken zu entgehen
defined('_JEXEC') or die('Restricted access');

// Variablen aus URL Parametern holen
$sid	= clm_core::$load->request_int('saison', 1);
$itemid	= clm_core::$load->request_int('Itemid', 19);

// Variablen aus dem Model initialisieren
$zps	= $this->zps;
$liga	= $this->liga;
// aktuellen Benutzer ermitteln
$cuser = (int) clm_core::$access->getId();

// Prüfen ob Verein vorhanden ist
if (!$liga[0]->Vereinname) { ?>
<div class="componentheading"><?php echo clm_core::$load->utf8decode(JText::_('CLUB_UNKNOWN')) ?></div>
<?php	} else {

    // Konfigurationsparameter auslesen
    $config = clm_core::$db->config();
    $countryversion = $config->countryversion;

    // FPDF Klasse einbinden
    require(clm_core::$path.DS.'classes'.DS.'fpdf.php');

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

    // Formatierungen um später mit frei konfigurierbaren Parametern arbeiten zu können
    // Zellenhöhe -> Standard 6
    $zelle = 6;
    // Fontgröße Standard = 12
    $font = 12;
    // Leere Zelle zum zentrieren der Tabelle
    $leer = 18;

    // Datum der Erstellung mit Joomla Methode holen und in SQL Format konvertieren
    $date	= JFactory::getDate();
    $now	= $date->toSQL();

    // FPDF Objekt aufrufen und neues Dokument starten
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();

    // Datum und Uhrzeit schreiben
    $pdf->SetFont('Times', '', 5);
    $pdf->Cell(10, 2, ' ', 0, 0);
    $pdf->Cell(175, 4, clm_core::$load->utf8decode(JText::_('WRITTEN')).' '.clm_core::$load->utf8decode(JText::_('ON_DAY')).' '.clm_core::$load->utf8decode(JHTML::_('date', $now, JText::_('DATE_FORMAT_CLM_PDF'))), 0, 1, 'R');

    // Überschrift
    $pdf->SetFont('Times', '', 16);
    $pdf->Cell(10, 15, ' ', 0, 0);
    if ($countryversion == "de") {
        $pdf->Cell(80, 15, clm_core::$load->utf8decode(JText::_('CLUB_RATING')).' '.clm_core::$load->utf8decode($liga[0]->Vereinname), 0, 1, 'L');
    } else {
        $pdf->Cell(80, 15, clm_core::$load->utf8decode(JText::_('CLUB_RATING_EN')).' '.clm_core::$load->utf8decode($liga[0]->Vereinname), 0, 1, 'L');
    }
    // User und Saison
    $archive_check = clm_core::$api->db_check_season_user($sid);
    if (!$archive_check) {
        $pdf->SetFont('Times', '', 12);
        $pdf->Cell(10, 10, ' ', 0, 0);
        $pdf->Cell(80, 10, clm_core::$load->utf8decode(JText::_('NO_ACCESS')), 0, 1, 'L');
        $pdf->Cell(10, 10, ' ', 0, 0);
        $pdf->Cell(80, 10, clm_core::$load->utf8decode(JText::_('NOT_REGISTERED')), 0, 1, 'L');
        //	echo "<div id='wrong'>".JText::_('NO_ACCESS')."<br>".JText::_('NOT_REGISTERED')."</div>";
    } else {

        // Kopfzeile der Tabelle
        $pdf->SetFont('Times', '', $font);
        ///////////////////////////////////////////////////////////////
        // Referenz siehe http://www.fpdf.de/funktionsreferenz/Cell  //
        ///////////////////////////////////////////////////////////////
        //
        // $pdf->Cell(Breite, Höhe, Inhalt, Rahmen, Umbruch, Zentrierung)
        //
        // Breite : Numerischer Wert
        // Höhe : Numerischer Wert
        // Inhalt : Text in Hochkommata, PHP Code mit . ("Punkt") angefügt
        // Rahmen : 0 = aus, 1 = ein,
        //	oder Kombinationen aus L (links), T (oben), R (rechts), B (unten)
        // Umbruch : 0 = rechts, 1 = entspr.$pdf->Ln(), 2 = Umbruch ohne Zurücksetzen auf linken Rand
        // Zentrierung ; L = links, C = zentriert, R = rechts
        //
        ///////////////////////////////////////////////////////////////

        $pdf->Cell($leer, $zelle, ' ', 0, 0, 'L');
        $pdf->Cell(7, $zelle, clm_core::$load->utf8decode(JText::_('CLUB_NR')), 1, 0, 'C');
        if ($countryversion == "de") {
            if ($cuser != -1) {
                $pdf->Cell(16, $zelle, clm_core::$load->utf8decode(JText::_('CLUB_MEMBER')), 1, 0, 'C');
            }
        } else {
            $pdf->Cell(18, $zelle, clm_core::$load->utf8decode(JText::_('CLUB_MEMBER_PKZ')), 1, 0, 'C');
        }
        $pdf->Cell(80, $zelle, clm_core::$load->utf8decode(JText::_('CLUB_MEMBER_NAME')), 1, 0, 'C');
        if ($countryversion == "de") {
            $pdf->Cell(16, $zelle, clm_core::$load->utf8decode(JText::_('CLUB_MEMBER_RATING')), 1, 0, 'C');
        } else {
            $pdf->Cell(16, $zelle, clm_core::$load->utf8decode(JText::_('CLUB_MEMBER_RATING_EN')), 1, 0, 'C');
        }
        $pdf->Cell(16, $zelle, clm_core::$load->utf8decode(JText::_('CLUB_MEMBER_RATINGS')), 1, 0, 'C');
        $pdf->Cell(16, $zelle, clm_core::$load->utf8decode(JText::_('CLUB_MEMBER_ELO')), 1, 0, 'C');
        // Zeilenumbruch
        $pdf->Ln();

        // Schleife für Mitglieder durchlaufen
        $x = 1;

        foreach ($zps as $zps) {

            $pdf->Cell($leer, $zelle, ' ', 0, 0, 'L');
            $pdf->Cell(7, $zelle, $x, 1, 0, 'C');
            if ($countryversion == "de") {
                if ($cuser != -1) {
                    $pdf->Cell(16, $zelle, $zps->Mgl_Nr, 1, 0, 'C');
                }
            } else {
                $pdf->Cell(18, $zelle, $zps->PKZ, 1, 0, 'C');
            }
            if (is_null($zps->FIDE_Titel)) {
                $zps->FIDE_Titel = '';
            }
            $pdf->Cell(8, $zelle, clm_core::$load->utf8decode($zps->FIDE_Titel), 'BT', 0, 'L');
            $pdf->Cell(72, $zelle, clm_core::$load->utf8decode($zps->Spielername), 'BT', 0, 'L');
            $pdf->Cell(16, $zelle, $zps->DWZ, 1, 0, 'C');
            if ($countryversion == "de") {
                $pdf->Cell(16, $zelle, $zps->DWZ_Index, 1, 0, 'C');
            } else {
                $pdf->SetFont('Times', '', $font - 2);
                $pdf->Cell(16, $zelle, '('.(600 + ($zps->DWZ * 8)).')', 1, 0, 'C');
                $pdf->SetFont('Times','',$font);
            }
            if ($zps->FIDE_Elo > 0) {
                $pdf->Cell(16,$zelle,$zps->FIDE_Elo,1,0,'C');
            } else {
                $pdf->Cell(16,$zelle,'',1,0,'C');
            }
            // Zeilenumbruch
            $pdf->Ln();

            $x++;
        }
    }
    // PDF an Browser senden
    $pdf->Output(clm_core::$load->utf8decode(JText::_('CLUB_RATING')).' '.clm_core::$load->utf8decode($liga[0]->Vereinname).'.pdf','D');
    exit;
}
?>
