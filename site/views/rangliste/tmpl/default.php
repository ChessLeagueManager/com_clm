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

require_once(JPATH_COMPONENT . DS . 'includes' . DS . 'clm_tooltip.php');

$lid		= clm_core::$load->request_int('liga', '1');
$sid		= clm_core::$load->request_int('saison', 0);
$runde		= clm_core::$load->request_string('runde');
$item		= clm_core::$load->request_int('Itemid', 0);
$typeid		= clm_core::$load->request_int('typeid', 0);
$liga		= $this->liga;
$option 	= clm_core::$load->request_string('option');
if ($option == '') {
    $option = 'com_clm';
}
$mainframe	= JFactory::getApplication();

// Userkennung holen
$user	= JFactory::getUser();
$jid	= $user->get('id');

//Liga-Parameter aufbereiten
if (isset($liga[0])) {
    $paramsStringArray = explode("\n", $liga[0]->params);

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
    if (!isset($params['noBoardResults'])) {
        $params['noBoardResults'] = '0';
    }
    if (!isset($params['pgnPublic'])) {
        $params['pgnPublic'] = '0';
    }
    if (!isset($params['pgnDownload'])) {
        $params['pgnDownload'] = '0';
    }
} else {
    $paramsStringArray = array();
}

$pgn		= clm_core::$load->request_int('pgn', 0);
if ($pgn == 1) {
    if (($jid != 0 and $params['pgnPublic'] == '1') or $params['pgnDownload'] == '1') {
        $result = clm_core::$api->db_pgn_export($lid, true);
        if (!$result[0]) {
            $msg = JText::_(strtoupper($result[1])).'<br><br>';
        } else {
            $msg = '';
        }
    } else {
        $msg = JText::_('NO_PERMISSION');
    }
    $_POST['pgn'] = 0;
    $link = 'index.php?option='.$option.'&view=rangliste&liga='.$lid.'&pgn=0';
    if ($item != 0) {
        $link .= '&Itemid='.$item;
    }
    if ($typeid != 0) {
        $link .= '&typeid='.$typeid;
    }
    $mainframe->enqueueMessage($msg);
    $mainframe->redirect($link);

}

$punkte		= $this->punkte;
if (is_null($punkte[0]->mp) or $punkte[0]->mp == 0) {
    $s_tln = 1;
} else {
    $s_tln = 0;
}
$spielfrei	= $this->spielfrei;

if (isset($liga[0])) {
    // Test MP als Feinwertung -> d.h. Spalte MP als Hauptwertung wird dann unterdrückt
    if ($liga[0]->tiebr1 == 9 or $liga[0]->tiebr2 == 9 or $liga[0]->tiebr3 == 9) {
        $columnMP = 0;
    } else {
        $columnMP = 1;
    }

    if ($sid == 0) {
        $db	= JFactory::getDBO();
        $query = " SELECT a.* FROM #__clm_liga as a"
                ." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
                ." WHERE a.id = ".$lid
                ." AND s.published = 1"
        ;
        $db->setQuery($query);
        $zz	= $db->loadObjectList();
        if (isset($zz)) {
            $_POST['saison'] = $zz[0]->sid;
            $sid = $zz[0]->sid;
        }
    }
}

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

// Browsertitelzeile setzen
$doc = JFactory::getDocument();
if (isset($liga[0])) {
    $doc->setTitle(JText::_('RANGLISTE').' '.$liga[0]->name);
} else {
    $doc->setTitle(JText::_('RANGLISTE'));
}
// Konfigurationsparameter auslesen
$config = clm_core::$db->config();
$pdf_melde = $config->pdf_meldelisten;
$man_showdwz = $config->man_showdwz;
$show_sl_mail = $config->show_sl_mail;

echo '<div id="clm"><div id="rangliste">';

require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu.php');

$archive_check = clm_core::$api->db_check_season_user($sid);
if (!$archive_check) {
    echo "<div id='wrong'>".JText::_('NO_ACCESS')."<br>".JText::_('NOT_REGISTERED')."</div>";
}
// existiert die Liga
elseif (!$liga) {

    echo "<div id='wrong'>".JText::_('NOT_EXIST')." (".$lid.")<br>".JText::_('GEDULDA')."</div>";

}
// schon veröffentlicht
elseif ($liga[0]->published == "0") {

    echo "<div id='wrong'>".JText::_('NOT_PUBLISHED')."<br>".JText::_('GEDULD')."</div>";

}
// keine Rangliste bei KO-Modus
elseif ($liga[0]->runden_modus == "4" or $liga[0]->runden_modus == "5") {

    echo "<div id='wrong'>".JText::_('NOT_AVAILABLE')."<br>".JText::_('GO_TO_PAARUNGSLISTE')."</div>";

    // dieser Marker wird nur beim Extern Zugriff verwendet
    echo '<input type="hidden" name="extern_comment" value="GO_TO_PAARUNGSLISTE" />';
} else {

    // Spielfreie Teilnehmer finden //
    $diff = $spielfrei[0]->count;
    ?>

	<div class="componentheading">

	<?php echo JText::_('RANGLISTE');
    echo "&nbsp;".$liga[0]->name; ?>

	<div id="pdf">
	<!--<img src="printButton.png" alt="drucken"  /></a>-->

	<?php
    echo CLMContent::createPDFLink('rangliste', JText::_('PDF_RANGLISTETABLE'), array('saison' => $sid, 'layout' => 'rang', 'liga' => $lid));

    if ($pdf_melde == 1) {
        // Neue Ausgabe: Saisonstart
        echo CLMContent::createPDFLink('rangliste', JText::_('PDF_RANGLISTE_TEAM_LISTING'), array('saison' => $sid, 'layout' => 'start', 'liga' => $lid));

        if ($jid != 0) {
            echo CLMContent::createPDFLink('rangliste', JText::_('PDF_RANGLISTE_LIGAHEFT_1'), array('saison' => $sid, 'layout' => 'heft', 'o_nr' => 1, 'saison' => $sid, 'liga' => $lid));
        }

        echo CLMContent::createPDFLink('rangliste', JText::_('PDF_RANGLISTE_LIGAHEFT'), array('saison' => $sid, 'layout' => 'heft', 'o_nr' => 0, 'saison' => $sid, 'liga' => $lid));

    }
    echo CLMContent::createViewLink('tabelle', JText::_('RANGLISTE_GOTO_TABELLE'), array('saison' => $sid, 'liga' => $lid, 'Itemid' => $item));
    // PGN gesamtes Turnier
    if (($jid != 0 and $params['pgnPublic'] == '1') or $params['pgnDownload'] == '1') {
        //if ($jid != 0 AND $params['pgnDownload'] == '1') {
        echo CLMContent::createPGNLink('rangliste', JText::_('RANGLISTE_PGN_ALL'), array('liga' => $liga[0]->id), 1);
    }

    // DWZ Durchschnitte - Aufstellung
    $result = clm_core::$api->db_nwz_average($lid);
    $a_average_dwz_lineup = $result[2];

    ?>
	</div></div>
	<div class="clr"></div>

	<br>

	<table cellpadding="0" cellspacing="0" class="rangliste">
		<tr>
			<th class="rang"><div><?php echo JText::_('RANG') ?></div></th>
			<?php if ($s_tln == 1) { ?>
				<th class="rang"><div><?php echo JText::_('TLN') ?></div></th>
			<?php } ?>
			<th class="team"><div><?php echo JText::_('TEAM') ?></div></th>
			
			<?php
            // vollrundig
            if (($liga[0]->durchgang * ($liga[0]->teil - $diff)) < 21) {
                $estyle = '';
            } else {
                $estyle = ' style="font-size:85%; width:17px;"';
            }

    if ($liga[0]->runden_modus == 1 or $liga[0]->runden_modus == 2) {
        // alle Durchgänge
        for ($dg = 1; $dg <= $liga[0]->durchgang; $dg++) {
            for ($rnd = 0; $rnd < $liga[0]->teil - $diff ; $rnd++) {
                echo '<th class="rnd" '.$estyle.'><div>'.($rnd + 1).'</div></th>';
            }
        }
    }

    // Schweizer System
    if ($liga[0]->runden_modus == 3) {
        for ($rnd = 0; $rnd < $liga[0]->runden ; $rnd++) {
            echo '<th class="rndch"><div>'.($rnd + 1).'</div></th>';
        }
    }
    ?>

			<?php if ($columnMP == 1) { ?>
				<th class="mp"><div><?php echo JText::_('MP') ?></div></th>
			<?php } ?>			
			
			<?php
    if ($liga[0]->liga_mt == 0) {
        echo '<th class="bp"><div>'.JText::_('BP').'</div></th>';
        if ($liga[0]->b_wertung > 0) {
            echo '<th class="bp"><div>'.JText::_('BW').'</div></th>';
        }
    } else {
        if ($liga[0]->tiebr1 > 0 and $liga[0]->tiebr1 < 50) {
            echo '<th class="bp"><div>'.JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr1).'</div></th>';
        }
        if ($liga[0]->tiebr2 > 0 and $liga[0]->tiebr2 < 50) {
            echo '<th class="bp"><div>'.JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr2).'</div></th>';
        }
        if ($liga[0]->tiebr3 > 0 and $liga[0]->tiebr3 < 50) {
            echo '<th class="bp"><div>'.JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr3).'</div></th>';
        }
    }
    ?>	
		</tr>

		<?php
        // Anzahl der Teilnehmer durchlaufen
        for ($x = 0; $x < ($liga[0]->teil) - $diff; $x++) {
            if (!isset($punkte[$x])) {
                continue;
            }
            // Farbgebung der Zeilen //
            if ($x % 2 != 0) {
                $zeilenr	= "zeile2";
                //				$zeilenr_dg2	= "zeile2_dg2";
                $zeilenr_dg2	= "zeile2";
            } else {
                $zeilenr		= "zeile1";
                //				$zeilenr_dg2	= "zeile1_dg2";
                $zeilenr_dg2	= "zeile1";
            }

            // Zeile Start
            echo '<tr class="'.$zeilenr.'">';

            // CSS-class des Rang-Eintrags
            $class = "rang";
            if ($x < $liga[0]->auf) {
                $class .= "_auf";
            } elseif ($x >= $liga[0]->auf and $x < ($liga[0]->auf + $liga[0]->auf_evtl)) {
                $class .= "_auf_evtl";
            } elseif ($x >= ($liga[0]->teil - $liga[0]->ab)) {
                $class .= "_ab";
            } elseif ($x >= ($liga[0]->teil - ($liga[0]->ab_evtl + $liga[0]->ab)) and $x < ($liga[0]->teil - $liga[0]->ab)) {
                $class .= "_ab_evtl";
            }

            //				echo '<td class="'.$class.'">'.($x+1).'</td>';
            echo '<td class="'.$class.'">'.$punkte[$x]->rankingpos.'</td>';
            if ($s_tln == 1) {
                echo '<td class="rang">'.$punkte[$x]->tln_nr.'</td>';
            }
            /*
            ?>

                <td class="rang<?php
            if($x < $liga[0]->auf) { echo "_auf"; }
            if($x >= $liga[0]->auf AND $x < ($liga[0]->auf + $liga[0]->auf_evtl)) { echo "_auf_evtl"; }
            if($x >= ($liga[0]->teil-$liga[0]->ab)) { echo "_ab"; }
            if($x >= ($liga[0]->teil-($liga[0]->ab_evtl + $liga[0]->ab)) AND $x < ($liga[0]->teil-$liga[0]->ab) ) { echo "_ab_evtl"; }
            ?>"><?php echo $x+1; ?></td>
            */

            echo '<td class="team">';

            //if ($punkte[$x]->published == 1 AND $params['noBoardResults'] == '0') {
            if ($punkte[$x]->published == 1) {
                $link = new CLMcLink();
                $link->view = 'mannschaft';
                $link->more = array('saison' => $sid, 'liga' => $lid, 'tlnr' => $punkte[$x]->tln_nr, 'Itemid' => $item);
                $link->makeURL();
                $strName = $link->makeLink($punkte[$x]->name);
            } else {
                $strName = $punkte[$x]->name;
            }
            echo '<div>'.$strName.'</div>';
            if ($man_showdwz == 1) {
                echo '<div class="dwz">';
                /*						if (isset($dwz[($punkte[$x]->tln_nr)])) {
                                            echo "(".round($dwz[($punkte[$x]->tln_nr)]).")";
                                        } else {
                                            echo "(-)";
                                        }
                */
                echo "(".$a_average_dwz_lineup[$punkte[$x]->tln_nr].")";
                //$result = clm_core::$api->db_nwz_average($lid);
                //echo "<br>result:"; var_dump($result);
                echo '</div>';
            }
            echo '</td>';

            /*
            ?>
            <div><a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $sid; ?>&liga=<?php echo $lid; ?>&tlnr=<?php echo $punkte[$x]->tln_nr; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $punkte[$x]->name; ?></a></div>
            <div class="dwz"><?php if (isset($dwz[($punkte[$x]->tln_nr)])) echo "( ".round($dwz[($punkte[$x]->tln_nr)])." )"; else echo "( 0 )"; ?></div>
            <?php } else { ?>
            <div><?php	echo $punkte[$x]->name; ?></div>
            <div class="dwz"><?php if (isset($dwz[($punkte[$x]->tln_nr)])) echo "( ".round($dwz[($punkte[$x]->tln_nr)])." )"; else echo "( 0 )"; ?></div>
            <?php } ?>
            </td>
            <?php
            */

            // Durchgänge
            for ($dg = 1; $dg <= $liga[0]->durchgang; $dg++) {

                // Runden
                $runden = CLMModelRangliste::punkte_tlnr($sid, $lid, $punkte[$x]->tln_nr, $dg, $liga[0]->runden_modus);
                if ($liga[0]->runden_modus == 1 or $liga[0]->runden_modus == 2) {
                    for ($y = 0; $y < $liga[0]->teil - $diff; $y++) {
                        if ($y == $x) {
                            echo '<td class="trenner" '.$estyle.'>X</td>';
                        } else {
                            // veränderte CSS für weitere Durchgänge ermöglichen
                            if ($dg % 2 != 0) {
                                echo '<td class="'.$zeilenr.'" '.$estyle.'>';
                            } else {
                                echo '<td class="'.$zeilenr_dg2.'" '.$estyle.'>';
                            }

                            // nur erster Durchgang
                            // TODO: muß man das wirklich unterscheiden?
                            //	if ($dg == 1) {
                            if ($punkte[$y]->tln_nr > $runden[0]->tln_nr) {
                                if (isset($runden[($punkte[$y]->tln_nr) - 2]) and $runde != "" and $runden[($punkte[$y]->tln_nr) - 2]->runde <= $runde) {
                                    $link = new CLMcLink();
                                    $link->view = 'runde';
                                    $link->more = array('saison' => $sid, 'liga' => $lid, 'runde' => $runden[($punkte[$y]->tln_nr) - 2]->runde, 'dg' => $dg, 'Itemid' => $item);
                                    $link->makeURL();
                                    echo $link->makeLink($runden[($punkte[$y]->tln_nr) - 2]->brettpunkte);
                                    // echo $runden[($punkte[$y]->tln_nr)-2]->brettpunkte;
                                }
                                if (isset($runden[($punkte[$y]->tln_nr) - 2]) and $runde == "") {
                                    $link = new CLMcLink();
                                    $link->view = 'runde';
                                    $link->more = array('saison' => $sid, 'liga' => $lid, 'runde' => $runden[($punkte[$y]->tln_nr) - 2]->runde, 'dg' => $dg, 'Itemid' => $item);
                                    $link->makeURL();
                                    echo $link->makeLink($runden[($punkte[$y]->tln_nr) - 2]->brettpunkte);
                                    // echo $runden[($punkte[$y]->tln_nr)-2]->brettpunkte;
                                }
                            }
                            if ($punkte[$y]->tln_nr < $runden[0]->tln_nr) {
                                if (isset($runden[($punkte[$y]->tln_nr) - 1]) and $runde != "" and $runden[($punkte[$y]->tln_nr) - 1]->runde <= $runde) {
                                    $link = new CLMcLink();
                                    $link->view = 'runde';
                                    $link->more = array('saison' => $sid, 'liga' => $lid, 'runde' => $runden[($punkte[$y]->tln_nr) - 1]->runde, 'dg' => $dg, 'Itemid' => $item);
                                    $link->makeURL();
                                    echo $link->makeLink($runden[($punkte[$y]->tln_nr) - 1]->brettpunkte);
                                    // echo $runden[($punkte[$y]->tln_nr)-1]->brettpunkte;
                                }
                                if (isset($runden[($punkte[$y]->tln_nr) - 1]) and $runde == "") {
                                    $link = new CLMcLink();
                                    $link->view = 'runde';
                                    $link->more = array('saison' => $sid, 'liga' => $lid, 'runde' => $runden[($punkte[$y]->tln_nr) - 1]->runde, 'dg' => $dg, 'Itemid' => $item);
                                    $link->makeURL();
                                    echo $link->makeLink($runden[($punkte[$y]->tln_nr) - 1]->brettpunkte);
                                    // echo $runden[($punkte[$y]->tln_nr)-1]->brettpunkte;
                                }
                            }
                            /*	} else {
                                    if (isset($runden[($punkte[$y]->tln_nr)-2]) AND isset($runden[0]) AND $punkte[$y]->tln_nr > $runden[0]->tln_nr) {
                                        $link = new CLMcLink();
                                        $link->view = 'runde';
                                        $link->more = array('saison' => $sid, 'liga' => $lid, 'runde' => $runden[($punkte[$y]->tln_nr)-1]->runde, 'dg' => $dg, 'Itemid' => $item);
                                        $link->makeURL();
                                        echo $link->makeLink($runden[($punkte[$y]->tln_nr)-2]->brettpunkte);
                                        // echo $runden[($punkte[$y]->tln_nr)-2]->brettpunkte;
                                    }
                                    if (isset($runden[($punkte[$y]->tln_nr)-1]) AND isset($runden[0]) AND $punkte[$y]->tln_nr < $runden[0]->tln_nr) {
                                        $link = new CLMcLink();
                                        $link->view = 'runde';
                                        $link->more = array('saison' => $sid, 'liga' => $lid, 'runde' => $runden[($punkte[$y]->tln_nr)-1]->runde, 'dg' => $dg, 'Itemid' => $item);
                                        $link->makeURL();
                                        echo $link->makeLink($runden[($punkte[$y]->tln_nr)-1]->brettpunkte);
                                        // echo $runden[($punkte[$y]->tln_nr)-1]->brettpunkte;
                                    }
                                }
                            */    echo '</td>';
                        }
                    }
                }
                // Ende Runden

                // 'spielfrei' -
                // TODO: nur nach Durchgang 1?
                if ($dg == 1 and $liga[0]->runden_modus == 3) {
                    for ($y = 0; $y < $liga[0]->runden; $y++) {
                        echo '<td class="'.$zeilenr.'">';
                        if (!isset($runden[$y])) {
                            echo " ";
                        } elseif ($runden[$y]->name == "spielfrei") {
                            echo " +";
                        } else {
                            echo $runden[$y]->brettpunkte." (".$runden[$y]->rankingpos.")";
                        }
                        echo '</td>';
                    }
                    // Ende Schleife
                }
                // Ende Modus 3

            }
            // Ende Durchgänge


            // MP
            if ($columnMP == 1) {
                echo '<td class="mp"><div>'.$punkte[$x]->mp;
                if ($punkte[$x]->abzug > 0) {
                    echo '*';
                } echo '</div></td>';
            }
            // BP
            if ($liga[0]->liga_mt == 0) {
                echo '<td class="bp"><div>'.$punkte[$x]->bp;
                if ($punkte[$x]->bpabzug > 0) {
                    echo '*';
                } echo '</div></td>';
                // B-Wertung
                if ($liga[0]->b_wertung > 0) {
                    echo '<td class="bp"><div>'.$punkte[$x]->wp.'</div></td>';
                }
            } else {
                // TBs
                if ($liga[0]->tiebr1 == 5) { // Brettpunkte
                    echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr1, $punkte[$x]->sumtiebr1);
                    if ($punkte[$x]->bpabzug > 0) {
                        echo '*';
                    } echo '</div></td>';
                } elseif ($liga[0]->tiebr1 > 0 and $liga[0]->tiebr1 < 50) {
                    echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr1, $punkte[$x]->sumtiebr1).'</div></td>';
                }
                if ($liga[0]->tiebr2 == 5) { // Brettpunkte
                    echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr2, $punkte[$x]->sumtiebr2);
                    if ($punkte[$x]->bpabzug > 0) {
                        echo '*';
                    } echo '</div></td>';
                } elseif ($liga[0]->tiebr2 > 0 and $liga[0]->tiebr2 < 50) {
                    echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr2, $punkte[$x]->sumtiebr2).'</div></td>';
                }
                if ($liga[0]->tiebr3 == 5) { // Brettpunkte
                    echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr3, $punkte[$x]->sumtiebr3);
                    if ($punkte[$x]->bpabzug > 0) {
                        echo '*';
                    } echo '</div></td>';
                } elseif ($liga[0]->tiebr3 > 0 and $liga[0]->tiebr3 < 50) {
                    echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr3, $punkte[$x]->sumtiebr3).'</div></td>';
                }
            }
            echo '</tr>';
        }
    // Ende Teilnehmer

    ?>
	</table>


	<?php
    if (($liga[0]->sl <> "") or ($liga[0]->bemerkungen <> "")) {
        ?>
		<div id="desc">
			
			<?php
            if ($liga[0]->sl <> "") {
                ?>
				<div class="ran_chief">
					<div class="ran_chief_left"><?php echo JText::_('CHIEF') ?></div>
					<?php if ($jid > 0 or $show_sl_mail > 0) { ?>
						<div class="ran_chief_right"><?php echo $liga[0]->sl; ?> | <?php echo JHTML::_('email.cloak', $liga[0]->email); ?></div>	
					<?php } else { ?>
						<div class="ran_chief_right"><?php echo $liga[0]->sl; ?></div>	
					<?php } ?>
				</div>
				<div class="clr"></div>
				<?php
            }

        // Kommentare zur Liga
        if ($liga[0]->bemerkungen <> "") {
            ?>
				<div class="ran_note">
					<div class="ran_note_left"><?php echo JText::_('NOTICE_SL') ?></div>
					<div class="ran_note_right"><?php echo nl2br($liga[0]->bemerkungen); ?></div>
				</div>
				<div class="clr"></div>
			
				<?php
            //if ($diff == 1 AND $liga[0]->ab ==1 ) { echo JText::_('ROUND_NO_RELEGATED_TEAM'); }
            //if ($diff == 1 AND $liga[0]->ab >1 ) { echo JText::_('ROUND_LESS_RELEGATED_TEAM'); }
            ?>
			<?php
        }
        echo '</div>';
    }
}
require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php');
?>


<div class="clr"></div>

</div>
</div>
