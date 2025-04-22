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

$lid		= clm_core::$load->request_int('liga', 1);
$sid		= clm_core::$load->request_int('saison', 0);
$runde		= clm_core::$load->request_int('runde');
$item		= clm_core::$load->request_int('Itemid', 1);
$liga		= $this->liga;

//Liga-Parameter aufbereiten
if (isset($liga[0])) {
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
} else {
    $paramsStringArray = array();
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
            $_GET['saison'] = $zz[0]->sid;
            $sid = $zz[0]->sid;
        }
    }
}

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

// Browsertitelzeile setzen
$doc = JFactory::getDocument();
if (isset($liga[0])) {
    $doc->setTitle(JText::_('Tabelle').' '.$liga[0]->name);
} else {
    $doc->setTitle(JText::_('Tabelle'));
}

// Konfigurationsparameter auslesen
$config = clm_core::$db->config();
$pdf_melde = $config->pdf_meldelisten;
$man_showdwz = $config->man_showdwz;
$show_sl_mail = $config->show_sl_mail;

// Userkennung holen
$user	= JFactory::getUser();
$jid	= $user->get('id');

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
elseif (!$liga or $liga[0]->published == 0) {

    echo CLMContent::clmWarning(JText::_('NOT_PUBLISHED')."<br/>".JText::_('GEDULD'));

    // falscher Modus
} elseif (!in_array($liga[0]->runden_modus, array(1,2,3))) {

    $link = new CLMcLink();
    $link->view = 'paarungsliste';
    $link->more = array('saison' => $sid, 'liga' => $lid, 'Itemid' => $item);
    $link->makeURL();
    echo CLMContent::clmWarning(JText::_('TOURNAMENT_TABLENOTAVAILABLE')."<br />".$link->makeLink(JText::_('PAAR_OVERVIEW')));

} else {

    // Spielfreie Teilnehmer finden //
    $diff = $spielfrei[0]->count;
    ?>

	<div class="componentheading">

	<?php echo JText::_('Tabelle');
    echo "&nbsp;".$liga[0]->name; ?>

	<div id="pdf">
	<!--<img src="printButton.png" alt="drucken"  /></a>-->

	<?php
    echo CLMContent::createPDFLink('tabelle', JText::_('TABELLE_PDF'), array('saison' => $sid, 'layout' => 'tabelle', 'liga' => $lid));
    echo CLMContent::createViewLink('rangliste', JText::_('TABELLE_GOTO_RANGLISTE'), array('saison' => $sid, 'liga' => $lid, 'Itemid' => $item));

    // DWZ Durchschnitte - Aufstellung
    $result = clm_core::$api->db_nwz_average($lid);
    //echo "<br>lid:"; var_dump($lid);
    //echo "<br>result:"; var_dump($result);
    $a_average_dwz_lineup = $result[2];
    //echo "<br>a_average_dwz_p:"; var_dump($a_average_dwz_p);
    //die();
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
				
			<th class="gsrv"><div><?php echo JText::_('TABELLE_GAMES_PLAYED') ?></div></th>
			<th class="gsrv"><div><?php echo JText::_('TABELLE_WINS') ?></div></th>
			<th class="gsrv"><div><?php echo JText::_('TABELLE_DRAW') ?></div></th>
			<th class="gsrv"><div><?php echo JText::_('TABELLE_LOST') ?></div></th>
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
                $zeilenr_dg2	= "zeile2_dg2";
            } else {
                $zeilenr		= "zeile1";
                $zeilenr_dg2	= "zeile1_dg2";
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
            echo '<td class="team">';

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
                echo '</div>';
            }
            echo '</td>';

            // MP
            echo '<td class="gsrv"><div>'.$punkte[$x]->count_G;
            echo '</div></td>';
            echo '<td class="gsrv"><div>'.$punkte[$x]->count_S;
            echo '</div></td>';
            echo '<td class="gsrv"><div>'.$punkte[$x]->count_R;
            echo '</div></td>';
            echo '<td class="gsrv"><div>'.$punkte[$x]->count_V;
            echo '</div></td>';
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
            if ($diff == 1 and $liga[0]->ab == 1) {
                echo JText::_('ROUND_NO_RELEGATED_TEAM');
            }
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
