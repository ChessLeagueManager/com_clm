<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT . DS . 'includes' . DS . 'clm_tooltip.php');

function RGB($Hex)
{
    if (substr($Hex, 0, 1) == "#") {
        $Hex = substr($Hex, 1);
    }
    $R = substr($Hex, 0, 2);
    $G = substr($Hex, 2, 2);
    $B = substr($Hex, 4, 2);
    $R = hexdec($R);
    $G = hexdec($G);
    $B = hexdec($B);
    $R = $R - 32;
    if ($R < 0) {
        $R = 0;
    }
    $G = $G - 32;
    if ($G < 0) {
        $G = 0;
    }
    $B = $B - 32;
    if ($B < 0) {
        $B = 0;
    }
    $R = dechex($R);
    if (strlen($R) < 2) {
        $R = '0'.$R;
    }
    $G = dechex($G);
    if (strlen($G) < 2) {
        $G = '0'.$G;
    }
    $B = dechex($B);
    if (strlen($B) < 2) {
        $B = '0'.$B;
    }
    return '#'.$R.$G.$B;
}
// ist die Aktuelle Runde abgeschlossen //
$NO_RESULT_YET = 0;
$RESULT_YET = 0;

$lid		= clm_core::$load->request_int('liga', 1);
$sid		= clm_core::$load->request_int('saison', 1);
$runde		= clm_core::$load->request_int('runde', 1);
$dg			= clm_core::$load->request_int('dg', 1);
$item		= clm_core::$load->request_int('Itemid', 0);
$typeid		= clm_core::$load->request_int('typeid', 0);
$liga		= $this->liga;
$option 	= 'com_clm';
$mainframe	= JFactory::getApplication();
$pgn		= clm_core::$load->request_int('pgn', 0);

$config		= clm_core::$db->config();

if (isset($liga[0])) {

    if (($pgn == 1) or ($pgn == 2)) {
        $result = clm_core::$api->db_pgn_template($lid, $dg, $runde, $pgn, true);
        if (!$result[1]) {
            $msg = JText::_(strtoupper($result[1])).'<br><br>';
        } else {
            $msg = '';
        }
        $link = 'index.php?option='.$option.'&view=runde&saison='.$sid.'&liga='.$lid.'&dg='.$dg.'&runde='.$runde.'&pgn=0';
        if ($item != 0) {
            $link .= '&Itemid='.$item;
        }
        if ($typeid != 0) {
            $link .= '&typeid='.$typeid;
        }
        $mainframe->enqueueMessage($msg);
        $mainframe->redirect($link);
    }

    $attr = clm_core::$api->db_lineup_attr($lid);

    //Liga-Parameter aufbereiten
    $paramsStringArray = explode("\n", $liga[0]->params);
    $params = array();
    foreach ($paramsStringArray as $value) {
        $ipos = strpos($value, '=');
        if ($ipos !== false) {
            $params[substr($value, 0, $ipos)] = substr($value, $ipos + 1);
        }
    }
    if (!isset($params['pgntype'])) {
        $params['pgntype'] = 0;
    }
    if (!isset($params['dwz_date'])) {
        $params['dwz_date'] = '1970-01-01';
    }
    if (!isset($params['round_date'])) {
        $params['round_date'] = '0';
    }
    if (!isset($params['noBoardResults'])) {
        $params['noBoardResults'] = '0';
    }
    if (!isset($params['ReportForm'])) {
        $params['ReportForm'] = '0';
    }
    if (!isset($params['pgnPublic'])) {
        $params['pgnPublic'] = '0';
    }

    $einzel		= $this->einzel;
    $detail		= clm_core::$load->request_int('detail', 0);
    if ($detail == 0) {
        $detailp = '1';
    } else {
        $detailp = '0';
    }

    // Userkennung holen
    $user	= JFactory::getUser();
    $jid	= $user->get('id');
    // Check ob User Mitglied eines Vereins dieser Liga ist
    if ($jid != 0) {
        $clmuser = $this->clmuser;
        $club_jid = false;
        foreach ($einzel as $einz) {
            if (isset($clmuser[0])) {
                if ($einz->zps == $clmuser[0]->zps or $einz->gzps == $clmuser[0]->zps) {
                    $club_jid = true;
                }
            }
        }
    }

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

    $runden_modus = $liga[0]->runden_modus;
    $runde_orig = $runde;
    if ($dg == 2) {
        $runde = $runde + $liga[0]->runden;
    }
    if ($dg == 3) {
        $runde = $runde + (2 * $liga[0]->runden);
    }
    if ($dg == 4) {
        $runde = $runde + (3 * $liga[0]->runden);
    }
}
// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

// Browsertitelzeile setzen
$doc = JFactory::getDocument();
if (isset($liga[0])) {
    $daten['title'] = $liga[0]->name.', '.$liga[$runde - 1]->rname;      // JText::_('ROUND').' '.$runde;
    if (isset($liga[$runde - 1]->datum)) {
        $daten['title'] .= ' '.JText::_('ON_DAY').' '.JHTML::_('date', $liga[$runde - 1]->datum, JText::_('DATE_FORMAT_CLM_F'));
        if (isset($liga[$runde - 1]->startzeit)) {
            $daten['title'] .= '  '.substr($liga[$runde - 1]->startzeit, 0, 5).' Uhr';
        }
    }
} else {
    $daten['title'] = '';
}
$doc->setTitle($daten['title']);

$doc->addScript(JURI::base().'components/com_clm/javascript/jsPgnViewer.js');
$doc->addScript(JURI::base().'components/com_clm/javascript/showPgnViewer.js');

// Zufallszahl
$now = time() + mt_rand();
$doc->addScriptDeclaration("var randomid = $now;");
// pgn-params
$doc->addScriptDeclaration("var param = new Array();");
$doc->addScriptDeclaration("param['fe_pgn_moveFont'] = '".$config->fe_pgn_moveFont."'");
$doc->addScriptDeclaration("param['fe_pgn_commentFont'] = '".$config->fe_pgn_commentFont."'");
$doc->addScriptDeclaration("param['fe_pgn_style'] = '".$config->fe_pgn_style."'");
// Tooltip-Texte
$doc->addScriptDeclaration("var text = new Array();");
$doc->addScriptDeclaration("text['altRewind'] = '".JText::_('PGN_ALT_REWIND')."';");
$doc->addScriptDeclaration("text['altBack'] = '".JText::_('PGN_ALT_BACK')."';");
$doc->addScriptDeclaration("text['altFlip'] = '".JText::_('PGN_ALT_FLIP')."';");
$doc->addScriptDeclaration("text['altShowMoves'] = '".JText::_('PGN_ALT_SHOWMOVES')."';");
$doc->addScriptDeclaration("text['altComments'] = '".JText::_('PGN_ALT_COMMENTS')."';");
$doc->addScriptDeclaration("text['altPlayMove'] = '".JText::_('PGN_ALT_PLAYMOVE')."';");
$doc->addScriptDeclaration("text['altFastForward'] = '".JText::_('PGN_ALT_FASTFORWARD')."';");
$doc->addScriptDeclaration("text['pgnClose'] = '".JText::_('PGN_CLOSE')."';");
// Pfad
$doc->addScriptDeclaration("var imagepath = '".JURI::base()."components/com_clm/images/pgnviewer/'");


// Konfigurationsparameter auslesen
$config		= clm_core::$db->config();
$rang_runde	= $config->fe_runde_rang;
$clm_zeile1			= $config->zeile1;
$clm_zeile2			= $config->zeile2;
$clm_zeile1D			= RGB($clm_zeile1);
$clm_zeile2D			= RGB($clm_zeile2);

if (isset($liga[0])) {
    // DWZ Durchschnitte - Aufstellung
    $result = clm_core::$api->db_nwz_average($lid);
    $a_average_dwz_lineup = $result[2];
    // DWZ Durchschnitte - gespielt in Runde
    $result = clm_core::$api->db_nwz_average($lid, $runde_orig, $dg);
    $a_average_dwz_round = $result[2];
}
?>

<div id="clm">
<div id="runde">

<?php
if (isset($liga[0])) {
    $ok = $this->ok;

    if ((isset($ok[0]->sl_ok)) and ($ok[0]->sl_ok > 0)) {
        $hint_freenew = JText::_('CHIEF_OK');
    }
    if ((isset($ok[0]->sl_ok)) and ($ok[0]->sl_ok == 0)) {
        $hint_freenew = JText::_('CHIEF_NOK');
    }
    if ((!isset($ok[0]->sl_ok))) {
        $hint_freenew = JText::_('CHIEF_NOK');
    }

    if (isset($liga[$runde - 1]->datum) and ($liga[$runde - 1]->datum == '0000-00-00' or $liga[$runde - 1]->datum == '1970-01-01')) {
        ?>
		<div class="componentheading"><?php echo $liga[0]->name.', '.$liga[$runde - 1]->rname;      // JText::_('ROUND').' '.$runde;?>

<?php } else { ?>
		<div class="componentheading"><?php echo $liga[0]->name.', '.$liga[$runde - 1]->rname;      // JText::_('ROUND').' '.$runde;
    if (isset($liga[$runde - 1]->datum)) {
        echo ' '.JText::_('ON_DAY').' '.JHTML::_('date', $liga[$runde - 1]->datum, JText::_('DATE_FORMAT_CLM_F'));
        if ($params['round_date'] == '0' and isset($liga[$runde - 1]->startzeit) and $liga[$runde - 1]->startzeit != '00:00:00') {
            echo '  '.substr($liga[$runde - 1]->startzeit, 0, 5);
        }
        if ($params['round_date'] == '1' and isset($liga[$runde - 1]->enddatum) and $liga[$runde - 1]->enddatum > '1970-01-01' and $liga[$runde - 1]->enddatum != $liga[$runde - 1]->datum) {
            echo ' - '.JHTML::_('date', $liga[$runde - 1]->enddatum, JText::_('DATE_FORMAT_CLM_F'));
        }
    }
}	?>
    
    <?php if (isset($liga) and $liga[0]->published == 1 and $liga[0]->rnd == 1 and $liga[$runde - 1]->pub == 1) { ?>
		<div id="pdf">	
			<?php
    // PGN eigene Paarung
    if (($params['pgntype'] > 0) and ($jid != 0) and ($club_jid == true)) {
        echo CLMContent::createPGNLink('runde', JText::_('ROUND_PGN_CLUB'), array('saison' => $liga[0]->sid, 'liga' => $liga[0]->id, 'runde' => $runde_orig, 'dg' => $dg));
    }
        // PGN gesamte Runde
        if (($params['pgntype'] > 0) and ($jid != 0)) {
            echo CLMContent::createPGNLink('runde', JText::_('ROUND_PGN_ALL'), array('saison' => $liga[0]->sid, 'liga' => $liga[0]->id, 'runde' => $runde_orig, 'dg' => $dg), 2);
        }
        // PDF
        echo CLMContent::createPDFLink('runde', JText::_('PDF_ROUND'), array('saison' => $liga[0]->sid, 'layout' => 'runde', 'liga' => $liga[0]->id, 'runde' => $runde_orig, 'dg' => $dg));

        if ($liga[0]->runden_modus != 4 or (isset($liga[$runde - 1]->datum) and ($liga[$runde - 1]->datum > '2014-05-31'))) { ?>
			<div class="pdf"><a href="index.php?option=com_clm&view=runde&Itemid=<?php echo $item ?>&saison=<?php echo $liga[0]->sid ?>&liga=<?php echo $liga[0]->id ?>&runde=<?php echo $runde_orig ?>&dg=<?php echo $dg ?>&detail=<?php echo $detailp ?>"><img src="<?php echo CLMImage::imageURL('lupe.png') ?>" width="16" height="19" alt="PDF" class="CLMTooltip" title="<?php echo JText::_('Details ein/aus') ?>"  /></a>
			</div>
			<?php } ?>
		</div>
    <?php }
    } else {	?>

		<div class="componentheading"><?php echo JText::_('ROUND'); ?>
<?php } ?>
</div>
<div class="clr"></div>

<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu.php');

$archive_check = clm_core::$api->db_check_season_user($sid);
if (!$archive_check) {
    echo "<div id='wrong'>".JText::_('NO_ACCESS')."<br>".JText::_('NOT_REGISTERED')."</div>";
} elseif (!isset($liga[0])) {
    echo "<br>". CLMContent::clmWarning(JText::_('NOT_EXIST').'<br>'.JText::_('GEDULDA'))."<br>";
}
// schon veröffentlicht
elseif (!$liga or $liga[0]->published == 0) {
    echo "<br>". CLMContent::clmWarning(JText::_('NOT_PUBLISHED').'<br>'.JText::_('GEDULD'))."<br>";
} elseif ($liga[0]->rnd == 0) {
    echo "<br>". CLMContent::clmWarning(JText::_('NO_ROUND_CREATED').'<br>'.JText::_('NO_ROUND_CREATED_HINT'))."<br>";
} elseif ($liga[$runde - 1]->pub == 0) {
    echo "<br>". CLMContent::clmWarning(JText::_('ROUND_UNPUBLISHED').'<br>'.JText::_('ROUND_UNPUBLISHED_HINT'))."<br>";
} else {   ?>

<?php // Kommentare zur Liga
if (is_null($liga[$runde - 1]->comment)) {
    $liga[$runde - 1]->comment = '';
}
    if (isset($liga[$runde - 1]->comment) and $liga[$runde - 1]->comment <> "") { ?>
<div id="desc">
    <p class="run_note_title"><?php echo JText::_('NOTICE_SL') ?></p>
    <p><?php echo nl2br($liga[$runde - 1]->comment); ?></p>
</div>
<?php }

    // Variablen ohne foreach setzen
    //$dwzschnitt	=$this->dwzschnitt;
    //$dwzgespielt=$this->dwzgespielt;
    $paar		= $this->paar;

    $summe		= $this->summe;

    // Ergebnistext für flexibele Punktevergabe holen
    $erg_text = CLMModelRunde::punkte_text($liga[0]->id);

    // Array für DWZ Schnitt setzen
    /* $dwz = array();
    for ($y=1; $y< ($liga[0]->teil)+1; $y++){
        if ($params['dwz_date'] == '0000-00-00' OR $params['dwz_date'] == '1970-01-01') {
            if(isset($dwzschnitt[($y-1)]->dwz)) {
            $dwz[$dwzschnitt[($y-1)]->tlnr] = $dwzschnitt[($y-1)]->dwz; }
        } else {
            if(isset($dwzschnitt[($y-1)]->start_dwz)) {
            $dwz[$dwzschnitt[($y-1)]->tlnr] = $dwzschnitt[($y-1)]->start_dwz; }
        }
    }
    */
    // Anzahl Spalten für Mannschaftsnamen
    if ($detail == 1) {
        $col_m = 2;
    } else {
        $col_m = 1;
    }
    if ($attr) {
        $col_m++;
    }
    $col_m1 = $col_m + 1;
    $col_h = (2 * $col_m) + 4;

    // Rundenschleife
    ?>
<br>

<table cellpadding="0" cellspacing="0" class="runde">
    <tr><td colspan="<?php echo $col_h; ?>">
    <div>
        <?php // Wenn SL_OK dann Haken anzeigen (nur wenn Staffelleiter eingegeben ist)
             if (isset($liga[0]->mf_name)) {
                 if (isset($ok[0]->sl_ok) and ($ok[0]->sl_ok > 0)) { ?>
            <div class="run_admit"><img  src="<?php echo CLMImage::imageURL('accept.png'); ?>" class="CLMTooltip" title="<?php echo $hint_freenew; 	//echo JText::_('CHIEF_OK');?>" /></div>
            <?php } else { ?>
            <div class="run_admit"><img  src="<?php echo CLMImage::imageURL('con_info.png'); ?>" class="CLMTooltip" title="<?php echo $hint_freenew; 	//echo JText::_('CHIEF_OK');?>" /></div>
        <?php }
            } ?>
        <div class="run_titel">
            <a href="index.php?option=com_clm&amp;view=paarungsliste&amp;liga=<?php echo $liga[0]->id ?>&amp;saison=<?php echo $liga[0]->sid; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $liga[$runde - 1]->rname; ?><img src="<?php echo CLMImage::imageURL('cancel_f2.png'); ?>" title="<?php echo JText::_('ROUND_BACK') ?>"/></a>
        </div>
    </div>
    </td></tr>
<?php
// Teilnehmerschleife
$w = 0;
    $z2 = 0;
    $zz = 0;
    for ($y = 0; $y < ($liga[0]->teil) / 2; $y++) {

        if (isset($paar[$y]->htln)) {  // Leere Begegnungen ausblenden
            if ($params['round_date'] == '1' and $paar[$y]->pdate > '1970-01-01') { ?>
		<tr><th colspan="<?php echo $col_h; ?>" class="paarung2" style="text-align: right;">
			<?php
                    echo JHTML::_('date', $paar[$y]->pdate, JText::_('DATE_FORMAT_CLM_F'));
                if ($paar[$y]->ptime > '00:00:00') {
                    echo '  '.substr($paar[$y]->ptime, 0, 5);
                }
                ?>
		</th></tr>
	<?php } ?>
    <tr>
        <th class="paarung2" colspan="<?php echo $col_m1; ?>">
        <?php
        $edit = 0;
            $medit = 0;
            ?> <div class=paarung> <?php	if ($paar[$y]->hname != 'spielfrei' and $paar[$y]->gname != 'spielfrei' and $params['ReportForm'] != '0') {   // $jid != 0 AND
                ?>
            <div class="run_admit"><a href="index.php?option=com_clm&view=runde&saison=<?php echo $liga[0]->sid; ?>&liga=<?php echo $liga[0]->id; ?>&amp;layout=paarung&amp;runde=<?php echo $runde_orig; ?>&amp;dg=<?php echo $dg; ?>&amp;paarung=<?php echo($y + 1); ?>&amp;format=pdf"><label for="name" class="hasTip"><img  src="<?php echo CLMImage::imageURL('pdf_button.png'); ?>"  class="CLMTooltip" title="<?php echo JText::_('PAIRING_PDF'); ?>" /></label></a>
			<?php } ?>
		</div> <?php
                // Meldenden einfügen wenn Runde eingegeben wurde
                if (isset($einzel[$w]->paar) and $einzel[$w]->paar == ($y + 1)) { ?>
            <div class="run_admit"><label for="name" class="hasTip"><img  src="<?php echo CLMImage::imageURL('edit_f2.png'); ?>"  class="CLMTooltip" title="<?php echo JText::_('REPORTED_BY').' '.$summe[$z2]->name; ?>" /></label>
            </div>
        <?php }
                if (isset($paar[$y]->hpublished) and $paar[$y]->hpublished == 1 and $params['noBoardResults'] == '0') { ?>
        <a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $liga[0]->sid; ?>&liga=<?php echo $liga[0]->id; ?>&tlnr=<?php echo $paar[$y]->htln; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $paar[$y]->hname; ?></a>
        <?php } else {
            if (isset($paar[$y]->hname)) {
                echo $paar[$y]->hname;
            }
        } ?>
        </th>
        <th class="paarung">
        <?php if ($a_average_dwz_round[$paar[$y]->htln] != '-' and $paar[$y]->htln != 0 and $paar[$y]->gtln != 0) {
            echo $a_average_dwz_round[$paar[$y]->htln];
        } else {
            echo $a_average_dwz_lineup[$paar[$y]->htln];
        } ?>
        </th>
        <th class="paarung">
        <?php
        // Ergebnis Mannschaft
        $paar_exist = 0;
            $remis_com = 0;
            //        if ($summe[$z2]->sum !="" AND $summe[$z2]->paarung == ($y+1)) {
            if ($summe[$z2]->paarung < $paar[$y]->paar) {
                $z2 = $z2 + 2;
            }
            if ($summe[$z2]->sum != "" and $summe[$z2]->paarung == $paar[$y]->paar) {
                $paar_exist = 1;
                echo $summe[$z2]->sum.' : '.$summe[$z2 + 1]->sum;
                if (($runden_modus == 4 or $runden_modus == 5) and ($summe[$z2]->sum == $summe[$z2 + 1]->sum)) {
                    $remis_com = 1;
                } else {
                    $remis_com = 0;
                }
                if (!is_null($summe[$z2]->dwz_editor) and $summe[$z2]->dwz_editor > '0') {
                    $medit++;
                }
            } else { ?> : <?php }
            $z2 = $z2 + 2; ?>
        </th>
        <th class="paarung" colspan="<?php echo $col_m; ?>">
        <?php // Name Gastmannschaft
            if (isset($paar[$y]->gpublished) and $paar[$y]->gpublished == 1 and $params['noBoardResults'] == '0') { ?>
        <a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $liga[0]->sid; ?>&liga=<?php echo $liga[0]->id; ?>&tlnr=<?php echo $paar[$y]->gtln; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $paar[$y]->gname; ?></a>
        <?php } else {
            if (isset($paar[$y]->gname)) {
                echo $paar[$y]->gname;
            }
        } ?>
        </th>
        <th class="paarung">
        <?php if ($a_average_dwz_round[$paar[$y]->gtln] != '-' and $paar[$y]->htln != 0 and $paar[$y]->gtln != 0) {
            echo $a_average_dwz_round[$paar[$y]->gtln];
            $zz++;
        } else {
            echo $a_average_dwz_lineup[$paar[$y]->gtln];
        } ?>
        </th>
    </tr>
<?php
        }
        //if (isset($einzel[$w]->paar) AND $einzel[$w]->paar == ($y+1)) {
        if (isset($einzel[$w]->paar) and $einzel[$w]->paar == $paar[$y]->paar) {
            // Bretter
            for ($x = 0; $x < $liga[0]->stamm; $x++) {

                if ($x % 2 != 0) {
                    $zeilenr = 'zeile1';
                    $zeiled = $clm_zeile1D;
                } else {
                    $zeilenr = 'zeile2';
                    $zeiled = $clm_zeile2D;
                }
                if ($einzel[$w]->ergebnis != 8) {
                    $RESULT_YET++;
                    ?>
    <tr class="<?php echo $zeilenr; ?>">
    <td class="paarung"><div><?php echo $einzel[$w]->brett; ?></div></td>
	<?php if ($detail == 1) {
	    if ($liga[0]->rang > 0) {
	        $einzel[$w]->hsnr = $einzel[$w]->tmnr.'-'.$einzel[$w]->trang;
	    } ?>
    <td class="paarung" style="border-right: none;<?php if ($einzel[$w]->weiss == 0) {
        echo 'background-color:'.$zeiled.';';
    }?>"><div><font size=-2><?php echo $einzel[$w]->hsnr; ?></font></div></td>
	<?php } ?>
	<?php if ($attr) { ?>
		<td class="paarung" ><div><?php echo $einzel[$w]->hattr; ?></div></td>
	<?php } ?>
    <td class="paarung2" colspan ="1"><div><?php if ($einzel[$w]->zps == "ZZZZZ") {
        echo "N.N.";
    } else {
        if ($einzel[$w]->zps != "-2") { ?>
			<a href="index.php?option=com_clm&view=spieler&saison=<?php echo $liga[0]->sid; ?>&zps=<?php echo $einzel[$w]->zps; ?>&mglnr=<?php echo $einzel[$w]->spieler; ?>&PKZ=<?php echo $einzel[$w]->PKZ; ?>&amp;Itemid=<?php echo $item; ?>">
			<?php echo $einzel[$w]->hname;
            if ($einzel[$w]->hstatus != 'A' and $einzel[$w]->hstatus != '') {
                echo ' ('.$einzel[$w]->hstatus.')';
            }
        } else {
            echo $einzel[$w]->hname;
        }
    } ?></div></td>
 
 <td class="paarung"><div><?php if ($params['dwz_date'] == '0000-00-00' or $params['dwz_date'] == '1970-01-01') {
     echo $einzel[$w]->hdwz;
 } else {
     echo $einzel[$w]->hstart_dwz;
 }?></div></td>
        <?php if ($einzel[$w]->dwz_edit != "") {
            $edit++; ?>
<!--    <td class="paarung"><div><b><?php echo $einzel[$w]->dwz_text; ?><font size="1"><br>( <?php echo $erg_text[$einzel[$w]->ergebnis]->erg_text; ?> )</font></b></div></td> -->
    <td class="paarung"><div><b><?php echo $erg_text[$einzel[$w]->dwz_edit]->erg_text; ?><font size="1"><br>( <?php echo $erg_text[$einzel[$w]->ergebnis]->erg_text; ?> )</font></b></div></td>
        <?php } else { ?>
		
		<?php if ($einzel[$w]->pgnnr == 0 or $params['pgnPublic'] == 0) { ?>
		<td class="paarung"><div><b><?php echo $erg_text[$einzel[$w]->ergebnis]->erg_text; ?></b></div></td>
		<?php } else { ?>
 		<td class="paarung"><span class="editlinktip hasTip" title="<?php echo JText::_('PGN_SHOWMATCH'); //echo $erg_text[$einzel[$w]->ergebnis]->erg_text;?>">
			<a onclick="startPgnMatch(<?php echo $w; ?>, 'pgnArea<?php echo $y ?>');" class="pgn"><?php echo $erg_text[$einzel[$w]->ergebnis]->erg_text; ?></a>
			</span>
			<?php if (is_null($einzel[$w]->text)) {
			    $einzel[$w]->text = '';
			} ;?>
			<input type='hidden' name='pgn[<?php echo $w; ?>]' id='pgnhidden<?php echo $w; ?>' value='<?php echo str_replace("'", "&#039", $einzel[$w]->text); ?>'>
		</td>
       <?php } ?>
		
        <?php } ?>
	<?php if ($detail == 1) {
	    if ($liga[0]->rang > 0) {
	        $einzel[$w]->gsnr = $einzel[$w]->smnr.'-'.$einzel[$w]->srang;
	    } ?>
    <td class="paarung" style="border-right: none;<?php if ($einzel[$w]->weiss != 0) {
        echo 'background-color:'.$zeiled.';';
    }?>"><div><font size=-2><?php echo $einzel[$w]->gsnr; ?></font></div></td>
	<?php } ?>
	<?php if ($attr) { ?>
		<td class="paarung" ><div><?php echo $einzel[$w]->gattr; ?></div></td>
	<?php } ?>
    <td class="paarung2" colspan ="1"><div><?php if ($einzel[$w]->gzps == "ZZZZZ") {
        echo "N.N.";
    } else {
        if ($einzel[$w]->gzps != "-2") { ?>
			<a href="index.php?option=com_clm&view=spieler&saison=<?php echo $liga[0]->sid; ?>&zps=<?php echo $einzel[$w]->gzps; ?>&mglnr=<?php echo $einzel[$w]->gegner; ?>&PKZ=<?php echo $einzel[$w]->gPKZ; ?>&amp;Itemid=<?php echo $item; ?>">
			<?php echo $einzel[$w]->gname;
            if ($einzel[$w]->gstatus != 'A' and $einzel[$w]->gstatus != '') {
                echo ' ('.$einzel[$w]->gstatus.')';
            }
        } else {
            echo $einzel[$w]->gname;
        }
    } ?></div></td>
    <td class="paarung"><div><?php if ($params['dwz_date'] == '0000-00-00' or $params['dwz_date'] == '1970-01-01') {
        echo $einzel[$w]->gdwz;
    } else {
        echo $einzel[$w]->gstart_dwz;
    } ?></div></td>
    </tr>
<?php }
                $w++;
            }

            if ($edit > 0 or $medit > 0) { ?>
	<tr><td colspan ="<?php echo $col_h; ?>"><?php if ($medit > 0 and $edit == "0") {
	    echo JText::_('CHIEF_EDIT_TEAM');
	} else {
	    echo JText::_('CHIEF_EDIT_SINGLE');
	}
                echo JText::_('BREACH_TO') ?>
	<?php if ($edit > 0) { ?><br><?php echo JText::_('CHIEF_EDIT_DWZ') ?></b><?php } ?>
	</td></tr>
<?php } elseif ($remis_com == 1) { ?>
	<tr><td colspan ="<?php echo $col_h; ?>"><?php  if ($paar[$y]->ko_decision == 1) { //1
	    if ($paar[$y]->wertpunkte > $paar[$y]->gwertpunkte) {
	        echo JText::_('ROUND_DECISION_WP_HEIM')." ".$paar[$y]->wertpunkte." : ".$paar[$y]->gwertpunkte." für ".$paar[$y]->hname;
	    } else {
	        echo JText::_('ROUND_DECISION_WP_GAST')." ".$paar[$y]->gwertpunkte." : ".$paar[$y]->wertpunkte." für ".$paar[$y]->gname;
	    }
	}
    if ($paar[$y]->ko_decision == 2) {
        echo JText::_('ROUND_DECISION_BLITZ_HEIM')." ".$paar[$y]->hname;
    }
    if ($paar[$y]->ko_decision == 3) {
        echo JText::_('ROUND_DECISION_BLITZ_GAST')." ".$paar[$y]->gname;
    }
    if ($paar[$y]->ko_decision == 4) {
        echo JText::_('ROUND_DECISION_LOS_HEIM')." ".$paar[$y]->hname;
    }
    if ($paar[$y]->ko_decision == 5) {
        echo JText::_('ROUND_DECISION_LOS_GAST')." ".$paar[$y]->gname;
    } ?>		
	</td></tr>
<?php } ?>

<?php if ($paar[$y]->comment != "") { ?>
<tr><td colspan ="<?php echo $col_h; ?>"><?php  echo JText::_('PAAR_COMMENT').$paar[$y]->comment; ?>		
	</td></tr>
<?php } ?>

<?php } elseif ((isset($paar[$y]->gpublished) and $paar[$y]->gpublished == 1 and $paar[$y]->hpublished == 1) and ($paar_exist == 0)) { ?>
<!--    <tr><td colspan ="<?php echo $col_h; ?>" align="left"><?php echo JText::_('NO_RESULT_YET');
    $NO_RESULT_YET++; ?></td></tr> -->
    <tr><td colspan ="<?php echo $col_h; ?>" align="left"><?php echo JText::_('NO_RESULT_YET');
    if ($paar[$y]->comment != "") {
        echo "<br>".JText::_('PAAR_COMMENT').$paar[$y]->comment;
    } $NO_RESULT_YET++; ?></td></tr>
    <?php } elseif (isset($paar[$y]) and $paar[$y]->comment != "") { ?>
	<tr><td colspan ="<?php echo $col_h; ?>"><?php  echo JText::_('PAAR_COMMENT').$paar[$y]->comment; ?></td></tr>
	<?php } else { ?><tr><td colspan ="<?php echo $col_h; ?>" class="noborder">&nbsp;</td></tr><?php } ?>

	<!--Bereich für pgn-Viewer-->
<tr><td colspan ="<?php echo $col_h; ?>" class="noborder"><span id="pgnArea<?php echo $y ?>"></span></td></tr>
<tr><td colspan ="<?php echo $col_h; ?>" class="noborder">&nbsp;</td></tr>

<?php } ?>
</table>

<div class="legend">
    <p><img src="<?php echo CLMImage::imageURL('cancel_f2.png'); ?>" /> = <?php echo JText::_('HIDE_DETAILS') ?></p>
    <p><img src="<?php echo CLMImage::imageURL('edit_f2.png'); ?>" /> = <?php echo JText::_('REPORTED_BY') ?></p>
</div>

<?php
// Rangliste
if (($rang_runde == "1") and ($liga[0]->runden_modus != 4 and $liga[0]->runden_modus != 5)) {

    $lid		= $liga[0]->id;
    $sid		= clm_core::$load->request_int('saison', 1);
    $punkte		= $this->punkte;
    $spielfrei	= $this->spielfrei;

    // Spielfreie Teilnehmer finden //
    $diff = $spielfrei[0]->count; ?>

<br>
<div id="rangliste">
<table cellpadding="0" cellspacing="0" class="rangliste">
	<?php
        if ($liga[0]->liga_mt == 0) {
            $columns = 4;     //liga
            if ($liga[0]->b_wertung > 0) {
                $columns++;
            }
        } else {
            $columns = 3;
            if ($liga[0]->tiebr1 > 0 and $liga[0]->tiebr1 < 50) {
                $columns++;
            }
            if ($liga[0]->tiebr2 > 0 and $liga[0]->tiebr2 < 50) {
                $columns++;
            }
            if ($liga[0]->tiebr3 > 0 and $liga[0]->tiebr3 < 50) {
                $columns++;
            }
        } ?>
	<tr><th colspan="<?php echo $columns + (($liga[0]->teil - $diff) * $liga[0]->durchgang); ?>"><?php

if ($RESULT_YET > 0 && $NO_RESULT_YET > 0) {
    echo JText::_('RANGLISTE').' '.$liga[$runde - 1]->rname.' '.JText::_('NOT_FINISH');
} elseif ($RESULT_YET > 0 && $NO_RESULT_YET == 0) {
    echo JText::_('RANGLISTE').' '.JText::_('AFTER').' '.$liga[$runde - 1]->rname;
} elseif ($RESULT_YET == 0 && $NO_RESULT_YET == 0) {
    echo JText::_('RANGLISTE').' '.$liga[$runde - 1]->rname;
} else {
    echo JText::_('RANGLISTE').' '.JText::_('BEFORE').' '.$liga[$runde - 1]->rname;
}
    ?></th></tr><?php //}?>
	<th class="rang"><div><?php echo JText::_('RANG') ?></div></th>
	<th class="team"><div><?php echo JText::_('TEAM') ?></div></th>
	<?php if ($liga[0]->runden_modus == 1 or $liga[0]->runden_modus == 2) { // vollrundig
	    // erster Durchgang
	    for ($rnd = 0; $rnd < $liga[0]->teil - $diff ; $rnd++) { ?>
			<th class="rnd"><div><?php echo $rnd + 1;?></div></th>
		<?php }
	    //  zweiter Durchgang
	    if ($liga[0]->durchgang > 1) {
	        for ($rnd = 0; $rnd < $liga[0]->teil - $diff ; $rnd++) { ?>
		<th class="rnd"><div><?php echo $rnd + 1; ?></div></th>
			<?php }
	        //  dritter Durchgang
	        if ($liga[0]->durchgang > 2) {
	            for ($rnd = 0; $rnd < $liga[0]->teil - $diff ; $rnd++) { ?>
				<th class="rnd"><div><?php echo $rnd + 1; ?></div></th>
				<?php }
	            //  vierter Durchgang
	            if ($liga[0]->durchgang > 3) {
	                for ($rnd = 0; $rnd < $liga[0]->teil - $diff ; $rnd++) { ?>
					<th class="rnd"><div><?php echo $rnd + 1; ?></div></th>
				<?php }
	                }
	        }
	    }
	} ?>
	<?php if ($liga[0]->runden_modus == 3) {    // Schweizer System
	    for ($rnd = 0; $rnd < $liga[0]->runden ; $rnd++) { ?>
			<th class="rndch"><div><?php echo $rnd + 1;?></div></th>
		<?php }
	    } ?>
	<th class="mp"><div><?php echo JText::_('MP') ?></div></th>
	<?php if ($liga[0]->liga_mt == 0) { 		// Liga?>
		<th class="bp"><div><?php echo JText::_('BP') ?></div></th>
			<?php if ($liga[0]->b_wertung > 0) { ?><th class="bp"><div><?php echo JText::_('BW') ?></div></th><?php } ?>
	<?php } else {										// CH-Turniere?>
		<?php if ($liga[0]->tiebr1 > 0 and $liga[0]->tiebr1 < 50) { ?><th class="bp"><div><?php echo JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr1) ?></div></th><?php } ?>
		<?php if ($liga[0]->tiebr2 > 0 and $liga[0]->tiebr2 < 50) { ?><th class="bp"><div><?php echo JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr2) ?></div></th><?php } ?>
		<?php if ($liga[0]->tiebr3 > 0 and $liga[0]->tiebr3 < 50) { ?><th class="bp"><div><?php echo JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr3) ?></div></th><?php } ?>
	<?php } ?>
</tr>

<?php
// Anzahl der Teilnehmer durchlaufen
for ($x = 0; $x < ($liga[0]->teil) - $diff; $x++) {
    if (!isset($punkte[$x])) {
        break;
    }
    // Farbgebung der Zeilen //
    if ($x % 2 != 0) {
        $zeilenr	= "zeile2";
        //		$zeilenr_dg2	= "zeile2_dg2";}
        $zeilenr_dg2	= "zeile2";
    } else {
        $zeilenr		= "zeile1";
        //		$zeilenr_dg2	= "zeile1_dg2";}
        $zeilenr_dg2	= "zeile1";
    }
    ?>
<tr class="<?php echo $zeilenr; ?>">
<td class="rang<?php
        if ($x < $liga[0]->auf) {
            echo "_auf";
        }
    if ($x >= $liga[0]->auf and $x < ($liga[0]->auf + $liga[0]->auf_evtl)) {
        echo "_auf_evtl";
    }
    if ($x >= ($liga[0]->teil - $liga[0]->ab)) {
        echo "_ab";
    }
    if ($x >= ($liga[0]->teil - ($liga[0]->ab_evtl + $liga[0]->ab)) and $x < ($liga[0]->teil - $liga[0]->ab)) {
        echo "_ab_evtl";
    }
    ?>"><?php // echo $x+1;
        echo $punkte[$x]->rankingpos; ?></td>
	<td class="team">
	<?php if ($punkte[$x]->published == 1 and $params['noBoardResults'] == '0') { ?>
	<div><a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $sid; ?>&liga=<?php echo $lid; ?>&tlnr=<?php echo $punkte[$x]->tln_nr; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $punkte[$x]->name; ?></a></div>
	<div class="dwz"><?php echo "(".$a_average_dwz_lineup[$punkte[$x]->tln_nr].")"; ?></div>
	<?php } else { ?>
	<div><?php	echo $punkte[$x]->name; ?></div>
	<div class="dwz"><?php echo "(".$a_average_dwz_lineup[$punkte[$x]->tln_nr].")";
	} ?></div>
	</td>
<?php
// Anzahl der Runden durchlaufen 1.Durchgang
$runden = CLMModelRunde::punkte_tlnr($sid, $lid, $punkte[$x]->tln_nr, 1, $liga[0]->runden_modus);
    $count = 0;
    if ($liga[0]->runden_modus == 1 or $liga[0]->runden_modus == 2) {
        for ($y = 0; $y < $liga[0]->teil - $diff; $y++) {
            if ($y == $x) { ?><td class="trenner">X</td><?php } else { ?>
	<td class="<?php echo $zeilenr; ?>"><?php
    if (isset($punkte[$y]) and $punkte[$y]->tln_nr > $runden[0]->tln_nr) {
        if ($runde != "" and $runden[($punkte[$y]->tln_nr) - 2]->runde <= $runde) {
            echo $runden[($punkte[$y]->tln_nr) - 2]->brettpunkte;
        }
        if ($runde == "") {
            echo $runden[($punkte[$y]->tln_nr) - 2]->brettpunkte;
        }
    }
                if (isset($punkte[$y]) and $punkte[$y]->tln_nr < $runden[0]->tln_nr) {
                    if ($runde != "" and $runden[($punkte[$y]->tln_nr) - 1]->runde <= $runde) {
                        echo $runden[($punkte[$y]->tln_nr) - 1]->brettpunkte;
                    }
                    if ($runde == "") {
                        echo $runden[($punkte[$y]->tln_nr) - 1]->brettpunkte;
                    }
                } ?>
	</td>
	<?php }
            }
    }
    if ($liga[0]->runden_modus == 3) {
        for ($y = 0; $y < $liga[0]->runden; $y++) { ?>
			<td class="<?php echo $zeilenr; ?>"><?php
                if (!isset($runden[$y])) {
                    echo " ";
                } elseif ($runden[$y]->name == "spielfrei") {
                    echo "  +";
                }
                //else echo $runden[$y]->rankingpos."/".$runden[$y]->brettpunkte;
                else {
                    echo $runden[$y]->brettpunkte." (".$runden[$y]->rankingpos.")";
                }  ?>
			</td>
			<?php }
        }
    // Anzahl der Runden durchlaufen 2.Durchgang
    if ($liga[0]->durchgang > 1) {
        $runden_dg2 = CLMModelRunde::punkte_tlnr($sid, $lid, $punkte[$x]->tln_nr, 2, $liga[0]->runden_modus);
        for ($y = 0; $y < $liga[0]->teil - $diff; $y++) {
            if ($y == $x) { ?><td class="trenner">X</td><?php } else { ?>
	<td class="<?php echo $zeilenr_dg2; ?>"><?php
                if (isset($runden_dg2[($punkte[$y]->tln_nr) - 2]) and isset($runden_dg2[0]) and $punkte[$y]->tln_nr > $runden_dg2[0]->tln_nr) {
                    if ($runde != "" and ($runden_dg2[($punkte[$y]->tln_nr) - 2]->dg < $dg or
                        ($runden_dg2[($punkte[$y]->tln_nr) - 2]->dg == $dg and $runden_dg2[($punkte[$y]->tln_nr) - 2]->runde <= $runde_orig))) {
                        echo $runden_dg2[($punkte[$y]->tln_nr) - 2]->brettpunkte;
                    }
                    if ($runde == "") {
                        echo $runden_dg2[($punkte[$y]->tln_nr) - 2]->brettpunkte;
                    }
                    //echo $runden_dg2[($punkte[$y]->tln_nr)-2]->brettpunkte;
                }
                if (isset($runden_dg2[($punkte[$y]->tln_nr) - 1]) and isset($runden_dg2[0]) and $punkte[$y]->tln_nr < $runden_dg2[0]->tln_nr) {
                    if ($runde != "" and ($runden_dg2[($punkte[$y]->tln_nr) - 1]->dg < $dg or
                        ($runden_dg2[($punkte[$y]->tln_nr) - 1]->dg == $dg and $runden_dg2[($punkte[$y]->tln_nr) - 1]->runde <= $runde_orig))) {
                        echo $runden_dg2[($punkte[$y]->tln_nr) - 1]->brettpunkte;
                    }
                    if ($runde == "") {
                        echo $runden_dg2[($punkte[$y]->tln_nr) - 1]->brettpunkte;
                    }
                    //echo $runden_dg2[($punkte[$y]->tln_nr)-1]->brettpunkte;
                } ?>
	</td>
	<?php }
            }
    }
    // Anzahl der Runden durchlaufen 3.Durchgang
    if ($liga[0]->durchgang > 2) {
        $runden_dg3 = CLMModelRunde::punkte_tlnr($sid, $lid, $punkte[$x]->tln_nr, 3, $liga[0]->runden_modus);
        for ($y = 0; $y < $liga[0]->teil - $diff; $y++) {
            if ($y == $x) { ?><td class="trenner">X</td><?php } else { ?>
	<td class="<?php echo $zeilenr_dg2; ?>"><?php
    if (isset($runden_dg3[($punkte[$y]->tln_nr) - 2]) and isset($runden_dg3[0]) and $punkte[$y]->tln_nr > $runden_dg3[0]->tln_nr) {
        if ($runde != "" and ($runden_dg3[($punkte[$y]->tln_nr) - 2]->dg < $dg or
            ($runden_dg3[($punkte[$y]->tln_nr) - 2]->dg == $dg and $runden_dg3[($punkte[$y]->tln_nr) - 2]->runde <= $runde_orig))) {
            echo $runden_dg3[($punkte[$y]->tln_nr) - 2]->brettpunkte;
        }
        if ($runde == "") {
            echo $runden_dg3[($punkte[$y]->tln_nr) - 2]->brettpunkte;
        }
        //echo $runden_dg3[($punkte[$y]->tln_nr)-2]->brettpunkte;
    }
                if (isset($runden_dg3[($punkte[$y]->tln_nr) - 1]) and isset($runden_dg3[0]) and $punkte[$y]->tln_nr < $runden_dg3[0]->tln_nr) {
                    if ($runde != "" and ($runden_dg3[($punkte[$y]->tln_nr) - 1]->dg < $dg or
                        ($runden_dg3[($punkte[$y]->tln_nr) - 1]->dg == $dg and $runden_dg3[($punkte[$y]->tln_nr) - 1]->runde <= $runde_orig))) {
                        echo $runden_dg3[($punkte[$y]->tln_nr) - 1]->brettpunkte;
                    }
                    if ($runde == "") {
                        echo $runden_dg3[($punkte[$y]->tln_nr) - 1]->brettpunkte;
                    }
                    //echo $runden_dg3[($punkte[$y]->tln_nr)-1]->brettpunkte;
                } ?>
	</td>
	<?php }
            }
    }
    // Anzahl der Runden durchlaufen 4.Durchgang
    if ($liga[0]->durchgang > 3) {
        $runden_dg4 = CLMModelRunde::punkte_tlnr($sid, $lid, $punkte[$x]->tln_nr, 4, $liga[0]->runden_modus);
        for ($y = 0; $y < $liga[0]->teil - $diff; $y++) {
            if ($y == $x) { ?><td class="trenner">X</td><?php } else { ?>
	<td class="<?php echo $zeilenr_dg2; ?>"><?php
    if (isset($runden_dg4[($punkte[$y]->tln_nr) - 2]) and isset($runden_dg4[0]) and $punkte[$y]->tln_nr > $runden_dg4[0]->tln_nr) {
        if ($runde != "" and ($runden_dg4[($punkte[$y]->tln_nr) - 2]->dg < $dg or
            ($runden_dg4[($punkte[$y]->tln_nr) - 2]->dg == $dg and $runden_dg4[($punkte[$y]->tln_nr) - 2]->runde <= $runde_orig))) {
            echo $runden_dg4[($punkte[$y]->tln_nr) - 2]->brettpunkte;
        }
        if ($runde == "") {
            echo $runden_dg4[($punkte[$y]->tln_nr) - 2]->brettpunkte;
        }
        //echo $runden_dg4[($punkte[$y]->tln_nr)-2]->brettpunkte;
    }
                if (isset($runden_dg4[($punkte[$y]->tln_nr) - 1]) and isset($runden_dg4[0]) and $punkte[$y]->tln_nr < $runden_dg4[0]->tln_nr) {
                    if ($runde != "" and ($runden_dg4[($punkte[$y]->tln_nr) - 1]->dg < $dg or
                        ($runden_dg4[($punkte[$y]->tln_nr) - 1]->dg == $dg and $runden_dg4[($punkte[$y]->tln_nr) - 1]->runde <= $runde_orig))) {
                        echo $runden_dg4[($punkte[$y]->tln_nr) - 1]->brettpunkte;
                    }
                    if ($runde == "") {
                        echo $runden_dg4[($punkte[$y]->tln_nr) - 1]->brettpunkte;
                    }
                    //echo $runden_dg4[($punkte[$y]->tln_nr)-1]->brettpunkte;
                } ?>
	</td>
	<?php }
            }
    }
    // Ende Runden
    ?>
	<td class="mp"><div><?php echo $punkte[$x]->mp;
    if ($punkte[$x]->abzug > 0) {
        echo '*';
    } ?></div></td>
	<?php if ($liga[0]->liga_mt == 0) { // Liga?>				
		<td class="bp"><div><?php echo $punkte[$x]->bp;
	    if ($punkte[$x]->bpabzug > 0) {
	        echo '*';
	    } ?></div></td>
		<?php if ($liga[0]->b_wertung > 0) { ?><td class="bp"><div><?php echo $punkte[$x]->wp; ?></div></td><?php } ?>
	<?php } else { 								// Turniere?>
		<?php if ($liga[0]->tiebr1 == 5) { // Brettpunkte
		    echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr1, $punkte[$x]->sumtiebr1);
		    if ($punkte[$x]->bpabzug > 0) {
		        echo '*';
		    } echo '</div></td>';
		} elseif ($liga[0]->tiebr1 > 0 and $liga[0]->tiebr1 < 50) {
		    echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr1, $punkte[$x]->sumtiebr1).'</div></td>';
		} ?>
		<?php if ($liga[0]->tiebr2 == 5) { // Brettpunkte
		    echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr2, $punkte[$x]->sumtiebr2);
		    if ($punkte[$x]->bpabzug > 0) {
		        echo '*';
		    } echo '</div></td>';
		} elseif ($liga[0]->tiebr2 > 0 and $liga[0]->tiebr2 < 50) {
		    echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr2, $punkte[$x]->sumtiebr2).'</div></td>';
		} ?>
		<?php if ($liga[0]->tiebr3 == 5) { // Brettpunkte
		    echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr3, $punkte[$x]->sumtiebr3);
		    if ($punkte[$x]->bpabzug > 0) {
		        echo '*';
		    } echo '</div></td>';
		} elseif ($liga[0]->tiebr3 > 0 and $liga[0]->tiebr3 < 50) {
		    echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr3, $punkte[$x]->sumtiebr3).'</div></td>';
		} ?>
	<?php } ?>
</tr>
<?php }
// Ende Teilnehmer
    ?>
</table>


<?php if ($diff == 1 and $liga[0]->ab == 1) {
    echo JText::_('ROUND_NO_RELEGATED_TEAM');
}
    //if ($diff == 1 AND $liga[0]->ab >1 ) { echo JText::_('ROUND_LESS_RELEGATED_TEAM'); }
    ?>
</div>
<?php } // Ende Rangliste
    ?>
<?php // Wenn SL_OK dann Erklärung für Haken anzeigen (nur wenn Staffelleiter eingegeben ist)
    if (isset($liga[0]->mf_name)) {
        if (isset($ok[0]->sl_ok) and $ok[0]->sl_ok > 0) { ?>

<div class="legend"><p><img src="<?php echo CLMImage::imageURL('accept.png'); ?>" width="16" height="16"/> = <?php echo JText::_('CHIEF_OK') ?></p></div>
<?php }  ?>

<br>
<?php } ?>

<?php } ?>
<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>

<div class="clr"></div>
</div>
</div>
