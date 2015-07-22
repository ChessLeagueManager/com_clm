<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team  All rights reserved
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

// Konfigurationsparameter auslesen
$itemid 		= JRequest::getVar( 'Itemid' );
// $turnierid		= JRequest::getInt('turnier','1');
$config = clm_core::$db->config();
// $pdf_melde = $config->pdf_meldelisten;
$pgn		= JRequest::getInt('pgn','0'); 

// Userkennung holen
$user	=JFactory::getUser();
$jid	= $user->get('id');

  if ($pgn == 1) { 
	$nl = "\n";
	$file_name = utf8_decode($this->turnier->name);
	$file_name = strtr($file_name,' ./','___');
	$file_name .= '.pgn'; 
	$pdatei = fopen($file_name,"wt");
	// alle Runden durchgehen
	foreach ($this->rounds as $value) {
	  // alle Matches durchgehen
	  foreach ($this->matches[$value->nr] as $matches) {
		if ( ($matches->spieler != 0 AND $matches->gegner != 0) OR $matches->ergebnis != NULL) {
			$gtmarker = "*";
			$resulthint = "";
			fputs($pdatei, '[Event "'.utf8_decode($this->turnier->name).'"]'.$nl);
			fputs($pdatei, '[Site "?"]'.$nl);
			fputs($pdatei, '[Date "'.JHTML::_('date',  $value->datum, JText::_('Y.m.d')).'"]'.$nl);
			fputs($pdatei, '[Round "'.$value->nr.'"]'.$nl);
			fputs($pdatei, '[Board "'.$matches->brett.'"]'.$nl);
			fputs($pdatei, '[White "'.utf8_decode($matches->wname).'"]'.$nl);
			fputs($pdatei, '[Black "'.utf8_decode($matches->sname).'"]'.$nl);
			fputs($pdatei, '[WhiteTeam "'.utf8_decode($matches->wverein).'"]'.$nl);
			fputs($pdatei, '[BlackTeam "'.utf8_decode($matches->sverein).'"]'.$nl);
			fputs($pdatei, '[WhiteElo "'.$matches->welo.'"]'.$nl);
			fputs($pdatei, '[BlackElo "'.$matches->selo.'"]'.$nl);
			fputs($pdatei, '[WhiteDWZ "'.$matches->wdwz.'"]'.$nl);
			fputs($pdatei, '[BlackDWZ "'.$matches->sdwz.'"]'.$nl);
			if ($matches->ergebnis == "2") { fputs($pdatei, '[Result "1/2-1/2"]'.$nl); $gtmarker = "1/2-1/2"; }
			elseif ($matches->ergebnis == "0") { fputs($pdatei, '[Result "0-1"]'.$nl); $gtmarker = "0-1"; }
			elseif ($matches->ergebnis == "1") { fputs($pdatei, '[Result "1-0"]'.$nl); $gtmarker = "1-0"; }
			elseif ($matches->ergebnis == "5") { fputs($pdatei, '[Result "1-0"]'.$nl); $resulthint = "{".utf8_decode('Weiß gewinnt kampflos')."}"; $gtmarker = "1-0"; }
			elseif ($matches->ergebnis == "4") { fputs($pdatei, '[Result "0-1"]'.$nl); $resulthint = "{Schwarz gewinnt kampflos}"; $gtmarker = "0-1"; }
			elseif ($matches->ergebnis == "6") { fputs($pdatei, '[Result "*"]'.$nl); $resulthint = "{beide verlieren kampflos}"; $gtmarker = "*"; }
			else fputs($pdatei, '[Result "'.$matches->ergebnis.'"]'.$nl);		
			fputs($pdatei, '[PlyCount "0"]'.$nl);
			fputs($pdatei, '[EventDate "'.JHTML::_('date',  $this->turnier->dateStart, JText::_('Y.m.d')).'"]'.$nl);
			fputs($pdatei, '[SourceDate "'.JHTML::_('date',  $value->datum, JText::_('Y.m.d')).'"]'.$nl);
			fputs($pdatei, ' '.$nl);
			fputs($pdatei, $resulthint.' '.$gtmarker.$nl);
			fputs($pdatei, ' '.$nl);
		}
	  }
	}
	fclose($pdatei);
    header('Content-Disposition: attachment; filename='.$file_name);
		header('Content-type: text/html');
		header('Cache-Control:');
		header('Pragma:');
		readfile($file_name);
		flush();
		JFactory::getApplication()->close();
  }	

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');


// CLM-Container
echo '<div ><div id="turnier_paarungsliste">';

// componentheading vorbereiten
$heading = $this->turnier->name.": ".JText::_('TOURNAMENT_PAIRINGLIST');

if ( $this->turnier->published == 0) { 
	echo CLMContent::componentheading($heading);
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOTPUBLISHED')."<br/>".JText::_('TOURNAMENT_PATIENCE'));

} elseif ($this->turnier->rnd == 0) {
	echo CLMContent::componentheading($heading);
	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOROUNDS'));

} else {
	// PDF-Link
	echo CLMContent::createPDFLink('turnier_paarungsliste', JText::_('TOURNAMENT_PAIRINGLIST_PRINT'), array('turnier' => $this->turnier->id, 'layout' => 'paarungsliste'));
	
	if ($jid != 0) {
		echo CLMContent::createPGNLink('turnier_paarungsliste', JText::_('TOURNAMENT_PGN_ALL'), array('turnier' => $this->turnier->id));
	}
   
   echo CLMContent::componentheading($heading);
	
	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');

	$turParams = new clm_class_params($this->turnier->params);

	// alle Runden durchgehen
	foreach ($this->rounds as $value) {
		
		// published?
		if ($value->published == 1) {
			
			// Table aufziehen
			echo '<table cellpadding="0" cellspacing="0" class="runde">';
			
			// Kopfzeile
			echo '<tr><td colspan="9">';
				echo '<div style="text-align:left; padding-left:1%">';
					echo '<b>';
					echo $value->name;
					if ($value->datum != "0000-00-00" AND $turParams->get('displayRoundDate', 1) == 1) {
						echo ',&nbsp;'.JHTML::_('date',  $value->datum, JText::_('DATE_FORMAT_CLM_F'));
						if(isset($value->startzeit) and $value->startzeit != '00:00:00') { echo '  '.substr($value->startzeit,0,5).' Uhr'; }
					}
					echo '</b>';
				echo '</div>';
			echo '</td></tr>';
			// Ende Kopfzeile
		
			// Spaltenüberschriften
			?>
			<tr>
				<th align="center"><?php echo JText::_('TOURNAMENT_TNR'); ?></th>
				<th align="center"><?php echo JText::_('TOURNAMENT_WHITE'); ?></th>
				<th align="center"><?php echo JText::_('TOURNAMENT_TWZ'); ?></th>
				<th align="center">-</th>
				<th align="center"><?php echo JText::_('TOURNAMENT_BLACK'); ?></th>
				<th align="center"><?php echo JText::_('TOURNAMENT_TWZ'); ?></th>
				<th align="center"><?php echo JText::_('RESULT'); ?></th>
			</tr>
			<?php
		
			// alle Matches eintragen
			$m=0; // CounterFlag für Farbe
			$nb=0; //Tischnummer
			foreach ($this->matches[$value->nr + (($value->dg - 1) * $this->turnier->runden)] as $matches) {
				
				$m++;
				// Farbe
				if ($m%2 != 0) { 
					$zeilenr = "zeile1"; 
				} else { 
					$zeilenr = "zeile2"; 
				}
				
				if ( ($matches->spieler != 0 AND $matches->gegner != 0) OR $matches->ergebnis != NULL) {
					echo '<tr class="'.$zeilenr.'">';
						$nb++;
						echo '<td align="center">'.$nb.'</td>';
						echo '<td>';
						if (isset($this->points[$value->nr + (($value->dg - 1) * $this->turnier->runden)][$matches->spieler])) { 
							$points = $this->points[$value->nr + (($value->dg - 1) * $this->turnier->runden)][$matches->spieler]; }
						else { $points = 0; }
						if (isset($this->players[$matches->spieler]->name)) {
							$link = new CLMcLink();
							$link->view = 'turnier_player';
							$link->more = array('turnier' => $this->turnier->id, 'snr' => $matches->spieler, 'Itemid' => $itemid);
							$link->makeURL();
							if ($this->turnier->typ != '3' AND $this->turnier->typ != '5')
								echo $link->makeLink($this->players[$matches->spieler]->name). " (".$points.")";
							else
								echo $link->makeLink($this->players[$matches->spieler]->name);
						}
						echo '</td>';
						if (isset($this->players[$matches->spieler]->twz) and $this->players[$matches->spieler]->twz > 0) 
							echo '<td align="center">'.CLMText::formatRating($this->players[$matches->spieler]->twz).'</td>';
						else echo '<td align="center">-</td>';
						echo '<td align="center">-</td>';
						echo '<td>';
						if (isset($this->points[$value->nr + (($value->dg - 1) * $this->turnier->runden)][$matches->gegner])) { 
							$points = $this->points[$value->nr + (($value->dg - 1) * $this->turnier->runden)][$matches->gegner]; 
						} else { 
							$points = 0; 
						}
						if (isset($this->players[$matches->gegner]->name) AND strlen($this->players[$matches->gegner]->name) > 0) {
							$link = new CLMcLink();
							$link->view = 'turnier_player';
							$link->more = array('turnier' => $this->turnier->id, 'snr' => $matches->gegner, 'Itemid' => $itemid);
							$link->makeURL();
							if ($this->turnier->typ != '3' AND $this->turnier->typ != '5') {
								echo $link->makeLink($this->players[$matches->gegner]->name). " (".$points.")";
							} else {
								echo $link->makeLink($this->players[$matches->gegner]->name);
							}
						}
						echo '</td>';
						if (isset($this->players[$matches->gegner]->twz) and $this->players[$matches->gegner]->twz > 0) {
							echo '<td align="center">'.CLMText::formatRating($this->players[$matches->gegner]->twz).'</td>';
						} else  //if (strlen($this->players[$matches->gegner]->name) > 0) {
							echo '<td align="center">-</td>';
						//} else {
						//	echo '<td align="center">&nbsp;</td>';
						//}

						if ($matches->ergebnis != NULL) {
							echo '<td align="center">';
							if ($matches->pgn == '' OR !$this->pgnShow) {
								echo CLMText::getResultString($matches->ergebnis);
							} else {
								echo '<span class="editlinktip hasTip" title="'.JText::_( 'PGN_SHOWMATCH' ).'">';
									echo '<a onclick="startPgnMatch('.$matches->id.', \'pgnArea'.$value->nr.'\');" class="pgn">'.CLMText::getResultString($matches->ergebnis).'</a>';
								echo '</span>';
								?>
								<input type='hidden' name='pgn[<?php echo $matches->id; ?>]' id='pgnhidden<?php echo $matches->id; ?>' value='<?php echo $matches->pgn; ?>'>
								<?php
							}
							
							// echo CLMText::getResultString($matches->ergebnis);
							if (($this->turnier->typ == 3 OR $this->turnier->typ == '5') AND ($matches->tiebrS > 0 OR $matches->tiebrG > 0)) {
								echo '<br /><small>'.$matches->tiebrS.':'.$matches->tiebrG.'</small>';
							}
							echo '</td>';
						} else {
							echo '<td align="center"></td>';
						}
					echo '</tr>';
				}
				
			}
			
			// tl_ok? Haken anzeigen!
			if ($this->displayTlOK AND $value->tl_ok > 0) {
				echo '<tr><td colspan="9">';
					echo '<div style="float:right; padding-right:1%;"><label for="name" class="hasTip" title="'.JText::_('TOURNAMENT_ROUNDOK').'"><img  src="'.CLMImage::imageURL('accept.png').'" /></label></div>';
				echo '</td></tr>';
			}
			
			
			echo '</table>';
		
			// Bereich für pgn-Viewer
			echo '<span id="pgnArea'.$value->nr.'"></span>';
		
			echo '<br>';
		
		} else {
			echo '<table cellpadding="0" cellspacing="0" class="runde">';
			echo '<tr><td colspan="9"><div style="text-align:left; padding-left:1%"><b>'.$value->name.'</b>&nbsp;&nbsp;&nbsp;</div></tr>';
			echo '<tr><td><font color="#ff0000">'.JText::_('TOURNAMENT_ROUNDNOTPUBLISHED').'</font></td></tr>';
			echo '</table><br>';
		}
	
	}
	

}

	
require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); 

echo '</div></div>';
?>
