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
// require_once(JPATH_COMPONENT.DS.'includes'.DS.'image_path.php');

echo '<div id="clm"><div id="rangliste">';

// schon veröffentlicht
if (!$liga OR $liga[0]->published == 0) {
	
	echo CLMContent::clmWarning(JText::_('NOT_PUBLISHED')."<br/>".JText::_('GEDULD'));

// falscher Modus
} elseif (!in_array($liga[0]->runden_modus, array(1,2,3)) ) {

	$link = new CLMcLink();
	$link->view = 'paarungsliste';
	$link->more = array('saison' => $sid, 'liga' => $lid, 'Itemid' => $item);
	$link->makeURL();
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_TABLENOTAVAILABLE')."<br />".$link->makeLink(JText::_('PAAR_OVERVIEW')));

} else {

	// Browsertitelzeile setzen
	$doc =JFactory::getDocument();
	$doc->setTitle(JText::_('Tabelle').' '.$liga[0]->name);


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
			$dwz[$dwzschnitt[($y-1)]->tlnr] = $dwzschnitt[($y-1)]->dwz; }
		} else {
			if(isset($dwzschnitt[($y-1)]->start_dwz)) {
			$dwz[$dwzschnitt[($y-1)]->tlnr] = $dwzschnitt[($y-1)]->start_dwz; }
		}
	}

	// Spielfreie Teilnehmer finden //
	$diff = $spielfrei[0]->count;
	?>

	<div class="componentheading">

	<?php echo JText::_('Tabelle'); echo "&nbsp;".$liga[0]->name; ?>

	<div id="pdf">
	<!--<img src="printButton.png" alt="drucken"  /></a>-->

	<?php
	echo CLMContent::createPDFLink('tabelle', JText::_('TABELLE_PDF'), array('saison' => $sid, 'layout' => 'tabelle', 'liga' => $lid));
	echo CLMContent::createViewLink('rangliste', JText::_('TABELLE_GOTO_RANGLISTE'), array('saison' => $sid, 'liga' => $lid) );
	?>

	</div></div>
	<div class="clr"></div>

	<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu.php'); ?>

	<br>

	<table cellpadding="0" cellspacing="0" class="rangliste">
		<tr>
			<th class="rang"><div><?php echo JText::_('RANG') ?></div></th>
			<th class="team"><div><?php echo JText::_('TEAM') ?></div></th>
				
			<th class="gsrv"><div><?php echo JText::_('TABELLE_GAMES_PLAYED') ?></div></th>
			<th class="gsrv"><div><?php echo JText::_('TABELLE_WINS') ?></div></th>
			<th class="gsrv"><div><?php echo JText::_('TABELLE_DRAW') ?></div></th>
			<th class="gsrv"><div><?php echo JText::_('TABELLE_LOST') ?></div></th>
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
						if (isset($dwz[($punkte[$x]->tln_nr)])) {
							echo "(".round($dwz[($punkte[$x]->tln_nr)]).")"; 
						} else {
							echo "(-)";
						}
						echo '</div>';
					}
				echo '</td>';
				
				// MP
				echo '<td class="gsrv"><div>'.$punkte[$x]->count_G; echo '</div></td>';
				echo '<td class="gsrv"><div>'.$punkte[$x]->count_S; echo '</div></td>';
				echo '<td class="gsrv"><div>'.$punkte[$x]->count_R; echo '</div></td>';
				echo '<td class="gsrv"><div>'.$punkte[$x]->count_V; echo '</div></td>';
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
