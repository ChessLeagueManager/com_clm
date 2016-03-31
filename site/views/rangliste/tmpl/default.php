<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access');
//JHtml::_('behavior.tooltip', '.CLMTooltip', $params);
JHtml::_('behavior.tooltip', '.CLMTooltip');
 
$lid		= JRequest::getInt('liga','1'); 
$sid		= JRequest::getInt('saison',0);
$runde		= JRequest::getInt('runde');
$item		= JRequest::getInt('Itemid','1');
$liga		= $this->liga;

//Liga-Parameter aufbereiten
$paramsStringArray = explode("\n", $liga[0]->params);
$params = array();
foreach ($paramsStringArray as $value) {
	$ipos = strpos ($value, '=');
	if ($ipos !==false) {
		$params[substr($value,0,$ipos)] = substr($value,$ipos+1);
	}
}	
if (!isset($params['dwz_date'])) $params['dwz_date'] = '0000-00-00';
if (!isset($params['noBoardResults'])) $params['noBoardResults'] = '0';

$punkte		= $this->punkte;
$spielfrei	= $this->spielfrei;
$dwzschnitt	= $this->dwzschnitt;

if ($sid == 0) {
	$db	= JFactory::getDBO();
	$query = " SELECT a.* FROM #__clm_liga as a"
			." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
			." WHERE a.id = ".$lid
			." AND s.published = 1"
			;
	$db->setQuery($query);
	$zz	=$db->loadObjectList();
	if (isset($zz)) {
		JRequest::setVar('saison', $zz[0]->sid);
		$sid = $zz[0]->sid;
	}
}
 
// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

echo '<div ><div id="rangliste">';

if (!$liga OR $liga[0]->published == "0") {
	
	echo "<div id='wrong'>".JText::_('NOT_PUBLISHED')."<br>".JText::_('GEDULD')."</div>";

} else {

	// Browsertitelzeile setzen
	$doc =JFactory::getDocument();
	$doc->setTitle(JText::_('RANGLISTE').' '.$liga[0]->name);

	// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	$pdf_melde = $config->pdf_meldelisten;
	$man_showdwz = $config->man_showdwz;

		// Userkennung holen
	$user	=JFactory::getUser();
	$jid	= $user->get('id');

	// Array für DWZ Schnitt setzen
	$dwz = array();
	for ($y=1; $y< ($liga[0]->teil)+1; $y++) {
		if ($params['dwz_date'] == '0000-00-00') {
			if(isset($dwzschnitt[($y-1)]->dwz)) {
				$dwz[$dwzschnitt[($y-1)]->tlnr] = $dwzschnitt[($y-1)]->dwz; 
			}
		} else {
			if(isset($dwzschnitt[($y-1)]->start_dwz)) {
				$dwz[$dwzschnitt[($y-1)]->tlnr] = $dwzschnitt[($y-1)]->start_dwz; 
			}
		}
	}

	// Spielfreie Teilnehmer finden //
	$diff = $spielfrei[0]->count;
	?>

	<div class="componentheading">

	<?php echo JText::_('RANGLISTE'); echo "&nbsp;".$liga[0]->name; ?>

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
	?>
	</div></div>
	<div class="clr"></div>

	<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu.php'); ?>

	<br>

	<table cellpadding="0" cellspacing="0" class="rangliste">
		<tr>
			<th class="rang"><div><?php echo JText::_('RANG') ?></div></th>
			<th class="team"><div><?php echo JText::_('TEAM') ?></div></th>
			
			<?php 
			// vollrundig
			if (($liga[0]->durchgang * ($liga[0]->teil-$diff)) < 21) $estyle = '';
			else $estyle = ' style="font-size:85%; width:17px;"';

			if ($liga[0]->runden_modus == 1 OR $liga[0]->runden_modus == 2) {
				// alle Durchgänge
				for ($dg=1; $dg<=$liga[0]->durchgang; $dg++) {
					for ($rnd=0; $rnd < $liga[0]->teil-$diff ; $rnd++) { 
						echo '<th class="rnd" '.$estyle.'><div>'.($rnd+1).'</div></th>';
					}
				}
			}
	
			// Schweizer System
			if ($liga[0]->runden_modus == 3) { 
				for ($rnd=0; $rnd < $liga[0]->runden ; $rnd++) { 
					echo '<th class="rndch"><div>'.($rnd+1).'</div></th>';
				}
			}
			?>
			
			<th class="mp"><div><?php echo JText::_('MP') ?></div></th>
			
			<?php 
			if ( $liga[0]->liga_mt == 0) { 
				echo '<th class="bp"><div>'.JText::_('BP').'</div></th>';
				if ( $liga[0]->b_wertung > 0) { 
					echo '<th class="bp"><div>'.JText::_('BW').'</div></th>';
				}
			} else {
				if ( $liga[0]->tiebr1 > 0 AND $liga[0]->tiebr1 < 50) { 
					echo '<th class="bp"><div>'.JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr1).'</div></th>';
				}
				if ( $liga[0]->tiebr2 > 0 AND $liga[0]->tiebr2 < 50) {  
					echo '<th class="bp"><div>'.JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr2).'</div></th>';
				}
				if ( $liga[0]->tiebr3 > 0 AND $liga[0]->tiebr3 < 50) {  
					echo '<th class="bp"><div>'.JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr3).'</div></th>';
				}
			}
			?>	
		</tr>

		<?php
		// Anzahl der Teilnehmer durchlaufen
		for ($x=0; $x< ($liga[0]->teil)-$diff; $x++) {
			// Farbgebung der Zeilen //
			if ($x%2 != 0) { 
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
				} elseif ($x >= $liga[0]->auf AND $x < ($liga[0]->auf + $liga[0]->auf_evtl)) { 
					$class .= "_auf_evtl"; 
				} elseif ($x >= ($liga[0]->teil-$liga[0]->ab)) { 
					$class .= "_ab"; 
				} elseif ($x >= ($liga[0]->teil-($liga[0]->ab_evtl + $liga[0]->ab)) AND $x < ($liga[0]->teil-$liga[0]->ab) ) { 
					$class .= "_ab_evtl"; 
				}
			
				echo '<td class="'.$class.'">'.($x+1).'</td>';
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
						if (isset($dwz[($punkte[$x]->tln_nr)])) {
							echo "(".round($dwz[($punkte[$x]->tln_nr)]).")"; 
						} else {
							echo "(-)";
						}
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
			for ($dg=1; $dg<=$liga[0]->durchgang; $dg++) {
					
				// Runden
				$runden = CLMModelRangliste::punkte_tlnr($sid, $lid, $punkte[$x]->tln_nr, $dg, $liga[0]->runden_modus);
				if ($liga[0]->runden_modus == 1 OR $liga[0]->runden_modus == 2) {
					for ($y=0; $y< $liga[0]->teil-$diff; $y++) {
						if ($y == $x) { 
							echo '<td class="trenner" '.$estyle.'>X</td>';
						} else { 
							// veränderte CSS für weitere Durchgänge ermöglichen
							if ($dg%2 != 0) { 
								echo '<td class="'.$zeilenr.'" '.$estyle.'>';
							} else {
								echo '<td class="'.$zeilenr_dg2.'" '.$estyle.'>';
							}
							
							// nur erster Durchgang
							// TODO: muß man das wirklich unterscheiden?
						//	if ($dg == 1) {
								if ($punkte[$y]->tln_nr > $runden[0]->tln_nr) {
									if (isset($runden[($punkte[$y]->tln_nr)-2]) AND $runde != "" AND $runden[($punkte[$y]->tln_nr)-2]->runde <= $runde) {
										$link = new CLMcLink();
										$link->view = 'runde';
										$link->more = array('saison' => $sid, 'liga' => $lid, 'runde' => $runden[($punkte[$y]->tln_nr)-2]->runde, 'dg' => $dg, 'Itemid' => $item);
										$link->makeURL();
										echo $link->makeLink($runden[($punkte[$y]->tln_nr)-2]->brettpunkte);
										// echo $runden[($punkte[$y]->tln_nr)-2]->brettpunkte; 
									}
									if (isset($runden[($punkte[$y]->tln_nr)-2]) AND $runde == "") { 
										$link = new CLMcLink();
										$link->view = 'runde';
										$link->more = array('saison' => $sid, 'liga' => $lid, 'runde' => $runden[($punkte[$y]->tln_nr)-2]->runde, 'dg' => $dg, 'Itemid' => $item);
										$link->makeURL();
										echo $link->makeLink($runden[($punkte[$y]->tln_nr)-2]->brettpunkte);
										// echo $runden[($punkte[$y]->tln_nr)-2]->brettpunkte;
									}
								}
								if ($punkte[$y]->tln_nr < $runden[0]->tln_nr) {
									if (isset($runden[($punkte[$y]->tln_nr)-1]) AND $runde != "" AND $runden[($punkte[$y]->tln_nr)-1]->runde <= $runde) {
										$link = new CLMcLink();
										$link->view = 'runde';
										$link->more = array('saison' => $sid, 'liga' => $lid, 'runde' => $runden[($punkte[$y]->tln_nr)-1]->runde, 'dg' => $dg, 'Itemid' => $item);
										$link->makeURL();
										echo $link->makeLink($runden[($punkte[$y]->tln_nr)-1]->brettpunkte);
										// echo $runden[($punkte[$y]->tln_nr)-1]->brettpunkte; 
									}
									if (isset($runden[($punkte[$y]->tln_nr)-1]) AND $runde == "") { 
										$link = new CLMcLink();
										$link->view = 'runde';
										$link->more = array('saison' => $sid, 'liga' => $lid, 'runde' => $runden[($punkte[$y]->tln_nr)-1]->runde, 'dg' => $dg, 'Itemid' => $item);
										$link->makeURL();
										echo $link->makeLink($runden[($punkte[$y]->tln_nr)-1]->brettpunkte);
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
						*/	echo '</td>';
			 			}
					}
				}
					// Ende Runden
					
					// 'spielfrei' - 
					// TODO: nur nach Durchgang 1?
					if ($dg == 1 AND $liga[0]->runden_modus == 3) {
						for ($y=0; $y< $liga[0]->runden; $y++) {
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

/*
				// Anzahl der Runden durchlaufen 1.Durchgang
				$runden = CLMModelRangliste::punkte_tlnr($sid,$lid,$punkte[$x]->tln_nr,1,$liga[0]->runden_modus);
				// $count = 0;

				if ($liga[0]->runden_modus == 1 OR $liga[0]->runden_modus == 2) {
					for ($y=0; $y< $liga[0]->teil-$diff; $y++) {
						if ($y == $x) { 
							echo '<td class="trenner">X</td>';
						} else { 
							echo '<td class="'.$zeilenr.'">';
							if ($punkte[$y]->tln_nr > $runden[0]->tln_nr) {
								if (isset($runden[($punkte[$y]->tln_nr)-2]) AND $runde != "" AND $runden[($punkte[$y]->tln_nr)-2]->runde <= $runde) {
									echo $runden[($punkte[$y]->tln_nr)-2]->brettpunkte; 
								}
								if (isset($runden[($punkte[$y]->tln_nr)-2]) AND $runde == "") { 
									echo $runden[($punkte[$y]->tln_nr)-2]->brettpunkte; 
								}
							}
							if ($punkte[$y]->tln_nr < $runden[0]->tln_nr) {
								if (isset($runden[($punkte[$y]->tln_nr)-1]) AND $runde != "" AND $runden[($punkte[$y]->tln_nr)-1]->runde <= $runde) {
									echo $runden[($punkte[$y]->tln_nr)-1]->brettpunkte; 
								}
								if (isset($runden[($punkte[$y]->tln_nr)-1]) AND $runde == "") { 
									echo $runden[($punkte[$y]->tln_nr)-1]->brettpunkte; 
								}
							} 
							echo '</td>';
						}
					}
				}
				// 

				if ($liga[0]->runden_modus == 3) {
					for ($y=0; $y< $liga[0]->runden; $y++) {
						echo '<td class="'.$zeilenr.'">';
						if ($runden[$y]->name == "spielfrei") {
							echo "  +";
						} elseif (!isset($runden[$y])) {
							echo " ";
						} else {
							echo $runden[$y]->brettpunkte." (".$runden[$y]->rankingpos.")";
						}
					echo '</td>';
					}
					// Ende Schleife
				}
				// Ende Modus 3
				
				// Anzahl der Runden durchlaufen 2.Durchgang
				if ($liga[0]->durchgang > 1) {
					$runden_dg2 = CLMModelRangliste::punkte_tlnr($sid,$lid,$punkte[$x]->tln_nr,2,$liga[0]->runden_modus);
					for ($y=0; $y< $liga[0]->teil-$diff; $y++) {
						if ($y == $x) { 
							echo '<td class="trenner">X</td>';
						} else { 
							echo '<td class="'.$zeilenr_dg2.'">';
							if (isset($runden_dg2[($punkte[$y]->tln_nr)-2]) AND isset($runden_dg2[0]) AND $punkte[$y]->tln_nr > $runden_dg2[0]->tln_nr) {
								echo $runden_dg2[($punkte[$y]->tln_nr)-2]->brettpunkte;
							}
							if (isset($runden_dg2[($punkte[$y]->tln_nr)-1]) AND isset($runden_dg2[0]) AND $punkte[$y]->tln_nr < $runden_dg2[0]->tln_nr) {
								echo $runden_dg2[($punkte[$y]->tln_nr)-1]->brettpunkte;
							} 
							echo '</td>';
			 			}
					}
				}

				// Anzahl der Runden durchlaufen 3.Durchgang
				if ($liga[0]->durchgang > 2) {
					$runden_dg3 = CLMModelRangliste::punkte_tlnr($sid,$lid,$punkte[$x]->tln_nr,3,$liga[0]->runden_modus);
					for ($y=0; $y< $liga[0]->teil-$diff; $y++) {
						if ($y == $x) { 
							echo '<td class="trenner">X</td>';
						} else { 
							echo '<td class="'.$zeilenr.'">';
							if (isset($runden_dg3[($punkte[$y]->tln_nr)-2]) AND isset($runden_dg3[0]) AND $punkte[$y]->tln_nr > $runden_dg3[0]->tln_nr) {
								echo $runden_dg3[($punkte[$y]->tln_nr)-2]->brettpunkte;
							}
							if (isset($runden_dg3[($punkte[$y]->tln_nr)-1]) AND isset($runden_dg3[0]) AND $punkte[$y]->tln_nr < $runden_dg3[0]->tln_nr) {
								echo $runden_dg3[($punkte[$y]->tln_nr)-1]->brettpunkte;
							} 
							echo '</td>';
			 			}
					}
				}

				// Anzahl der Runden durchlaufen 4.Durchgang
				if ($liga[0]->durchgang > 3) {
					$runden_dg4 = CLMModelRangliste::punkte_tlnr($sid,$lid,$punkte[$x]->tln_nr,4,$liga[0]->runden_modus);
					for ($y=0; $y< $liga[0]->teil-$diff; $y++) {
						if ($y == $x) { 
							echo '<td class="trenner">X</td>';
						} else { 
							echo '<td class="'.$zeilenr_dg2.'">';
							if (isset($runden_dg4[($punkte[$y]->tln_nr)-2]) AND isset($runden_dg4[0]) AND $punkte[$y]->tln_nr > $runden_dg4[0]->tln_nr) {
								echo $runden_dg4[($punkte[$y]->tln_nr)-2]->brettpunkte;
							}
							if (isset($runden_dg4[($punkte[$y]->tln_nr)-1]) AND isset($runden_dg4[0]) AND $punkte[$y]->tln_nr < $runden_dg4[0]->tln_nr) {
								echo $runden_dg4[($punkte[$y]->tln_nr)-1]->brettpunkte;
							} 
							echo '</td>';
			 			}
					}
				}
				// Ende Runden
				*/
				
				// MP
				//echo '<td class="mp"><div>'.$punkte[$x]->mp.'</div></td>';
				echo '<td class="mp"><div>'.$punkte[$x]->mp; if ($punkte[$x]->abzug > 0) echo '*'; echo '</div></td>';
 	
				// BP
				if ( $liga[0]->liga_mt == 0) {
					echo '<td class="bp"><div>'.$punkte[$x]->bp; if ($punkte[$x]->bpabzug > 0) echo '*'; echo '</div></td>';
					// B-Wertung
					if ( $liga[0]->b_wertung > 0) { 
						echo '<td class="bp"><div>'.$punkte[$x]->wp.'</div></td>';
					}
				} else {
					// TBs
					if ( $liga[0]->tiebr1 == 5 ) { // Brettpunkte
						echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr1, $punkte[$x]->sumtiebr1); if ($punkte[$x]->bpabzug > 0) echo '*'; echo '</div></td>';
					} elseif ( $liga[0]->tiebr1 > 0 AND $liga[0]->tiebr1 < 50) { 
						echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr1, $punkte[$x]->sumtiebr1).'</div></td>';
					}
					if ( $liga[0]->tiebr2 == 5 ) { // Brettpunkte
						echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr2, $punkte[$x]->sumtiebr2); if ($punkte[$x]->bpabzug > 0) echo '*'; echo '</div></td>';
					} elseif ( $liga[0]->tiebr2 > 0 AND $liga[0]->tiebr2 < 50) { 
						echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr2, $punkte[$x]->sumtiebr2).'</div></td>';
					}
					if ( $liga[0]->tiebr3 == 5 ) { // Brettpunkte
						echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr3, $punkte[$x]->sumtiebr3); if ($punkte[$x]->bpabzug > 0) echo '*'; echo '</div></td>';
					} elseif ( $liga[0]->tiebr3 > 0 AND $liga[0]->tiebr3 < 50) { 
						echo '<td class="bp"><div>'.CLMText::tiebrFormat($liga[0]->tiebr3, $punkte[$x]->sumtiebr3).'</div></td>';
					}
				}
			echo '</tr>';
		}
		// Ende Teilnehmer

	?>
	</table>


	<?php 
	if ( ($liga[0]->sl <> "") or ($liga[0]->bemerkungen <> "") ) {
		?>
		<div id="desc">
			
			<?php 
			if ( $liga[0]->sl <> "" ) { 
				?>
				<div class="ran_chief">
					<div class="ran_chief_left"><?php echo JText::_('CHIEF') ?></div>
					<div class="ran_chief_right"><?php echo $liga[0]->sl; ?> | <?php echo JHTML::_( 'email.cloak', $liga[0]->email ); ?></div>	
				</div>
				<div class="clr"></div>
				<?php  
			} 
		 
			// Kommentare zur Liga
			if ($liga[0]->bemerkungen <> "") { 
				?>
				<div class="ran_note">
					<div class="ran_note_left"><?php echo JText::_('NOTICE') ?></div>
					<div class="ran_note_right"><?php echo nl2br($liga[0]->bemerkungen); ?></div>
				</div>
				<div class="clr"></div>
			
				<?php 
				if ($diff == 1 AND $liga[0]->ab ==1 ) { echo JText::_('ROUND_NO_RELEGATED_TEAM'); }
				if ($diff == 1 AND $liga[0]->ab >1 ) { echo JText::_('ROUND_LESS_RELEGATED_TEAM'); }
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
