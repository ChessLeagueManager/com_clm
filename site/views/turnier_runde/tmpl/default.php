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
// $turnierid		= JRequest::getInt('turnier','1');
$itemid = JRequest::getVar( 'Itemid' );
$config = clm_core::$db->config();
$commentParse = $config->tourn_comment_parse;
// $pdf_melde = $config->pdf_meldelisten;
$pgn		= JRequest::getInt('pgn','0'); 

// Userkennung holen
$user	=JFactory::getUser();
$jid	= $user->get('id');

  if ($pgn == 1) { 
	$nl = "\n";
	$file_name = utf8_decode($this->turnier->name).'_'.utf8_decode(JText::_('TOURNAMENT_ROUND')."_".$this->round->nr);
	$file_name = strtr($file_name,' ./','___');
	$file_name .= '.pgn'; 
	$pdatei = fopen($file_name,"wt");
	// alle Matches durchgehen
	foreach ($this->matches as $value) {
		if ( ($value->spieler != 0 AND $value->gegner != 0) OR $value->ergebnis != NULL) {
			$gtmarker = "*";
			$resulthint = "";
			fputs($pdatei, '[Event "'.utf8_decode($this->turnier->name).'"]'.$nl);
			fputs($pdatei, '[Site "?"]'.$nl);
			fputs($pdatei, '[Date "'.JHTML::_('date',  $this->round->datum, JText::_('Y.m.d')).'"]'.$nl);
			fputs($pdatei, '[Round "'.$this->round->nr.'"]'.$nl);
			fputs($pdatei, '[Board "'.$value->brett.'"]'.$nl);
			fputs($pdatei, '[White "'.utf8_decode($value->wname).'"]'.$nl);
			fputs($pdatei, '[Black "'.utf8_decode($value->sname).'"]'.$nl);
			fputs($pdatei, '[WhiteTeam "'.utf8_decode($value->wverein).'"]'.$nl);
			fputs($pdatei, '[BlackTeam "'.utf8_decode($value->sverein).'"]'.$nl);
			fputs($pdatei, '[WhiteElo "'.$value->welo.'"]'.$nl);
			fputs($pdatei, '[BlackElo "'.$value->selo.'"]'.$nl);
			fputs($pdatei, '[WhiteDWZ "'.$value->wdwz.'"]'.$nl);
			fputs($pdatei, '[BlackDWZ "'.$value->sdwz.'"]'.$nl);
			if ($value->ergebnis == "2") { fputs($pdatei, '[Result "1/2-1/2"]'.$nl); $gtmarker = "1/2-1/2"; }
			elseif ($value->ergebnis == "0") { fputs($pdatei, '[Result "0-1"]'.$nl); $gtmarker = "0-1"; }
			elseif ($value->ergebnis == "1") { fputs($pdatei, '[Result "1-0"]'.$nl); $gtmarker = "1-0"; }
			elseif ($value->ergebnis == "5") { fputs($pdatei, '[Result "1-0"]'.$nl); $resulthint = "{".utf8_decode('Weiß gewinnt kampflos')."}"; $gtmarker = "1-0"; }
			elseif ($value->ergebnis == "4") { fputs($pdatei, '[Result "0-1"]'.$nl); $resulthint = "{Schwarz gewinnt kampflos}"; $gtmarker = "0-1"; }
			elseif ($value->ergebnis == "6") { fputs($pdatei, '[Result "*"]'.$nl); $resulthint = "{beide verlieren kampflos}"; $gtmarker = "*"; }
			else fputs($pdatei, '[Result "'.$value->ergebnis.'"]'.$nl);		
			fputs($pdatei, '[PlyCount "0"]'.$nl);
			fputs($pdatei, '[EventDate "'.JHTML::_('date',  $this->turnier->dateStart, JText::_('Y.m.d')).'"]'.$nl);
			fputs($pdatei, '[SourceDate "'.JHTML::_('date',  $this->round->datum, JText::_('Y.m.d')).'"]'.$nl);
			fputs($pdatei, ' '.$nl);
			fputs($pdatei, $resulthint.' '.$gtmarker.$nl);
			fputs($pdatei, ' '.$nl);
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


echo "<div id='clm'><div id='turnier_runde'>";

	$heading = $this->turnier->name;
	//$heading .= ": ".JText::_('TOURNAMENT_ROUND')." ".$this->round->nr;
	$heading .= ": ".$this->round->name;
	
// Turnier unveröffentlicht?
if ( $this->turnier->published == 0) { 
	echo CLMContent::componentheading($heading);
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOTPUBLISHED')."<br/>".JText::_('TOURNAMENT_PATIENCE'));

// Runden nicht erstellt
} elseif ($this->turnier->rnd == 0) {
	echo CLMContent::componentheading($heading);
	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOROUNDS'));

} elseif ($this->round->published != 1) {
	echo CLMContent::componentheading($heading);
	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_ROUNDNOTPUBLISHED'));

// Turnier/Runde kann ausgegeben werden
} else {
	$turParams = new clm_class_params($this->turnier->params);
	if ($this->round->datum != "0000-00-00" AND $turParams->get('displayRoundDate', 1) == 1) {
		$heading .=  ',&nbsp;'.JHTML::_('date',  $this->round->datum, JText::_('DATE_FORMAT_CLM_F')); 
		if(isset($this->round->startzeit) and $this->round->startzeit != '00:00:00') { $heading .= '  '.substr($this->round->startzeit,0,5).' Uhr'; }
	}

	// PDF-Link
	echo CLMContent::createPDFLink('turnier_runde', JText::_('PDF_TOURNAMENTROUND'), array('turnier' => $this->turnier->id, 'layout' => 'runde', 'dg' => $this->round->dg, 'runde' => $this->round->nr) );
	
	if ($jid != 0) { 
		echo CLMContent::createPGNLink('turnier_runde', JText::_('ROUND_PGN_ALL'), array('turnier' => $this->turnier->id, 'dg' => $this->round->dg, 'runde' => $this->round->nr) );
	} 
	
	echo CLMContent::componentheading($heading);
	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
 
	// Table aufziehen
	echo '<table cellpadding="0" cellspacing="0" class="runde">';

	// Kopfzeile
	echo '<tr><td colspan="9">';
		echo '<div style="text-align:left; padding-left:1%">';
			echo '<b>'.$this->round->name.'</b>';
		echo '</div>';
	echo '</td></tr>';
	// Ende Kopfzeile


	// headers
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
	
	// alle Matches durchgehen

	foreach ($this->matches as $value) {
	
		// Farbe

		if ($value->brett%2 != 0) { 
			$zeilenr = "zeile1"; 
		} else { 
			$zeilenr = "zeile2"; 
		}

		if ( ($value->spieler != 0 AND $value->gegner != 0) OR $value->ergebnis != NULL) {

			echo '<tr class="'.$zeilenr.'">';
				echo '<td align="center">'.$value->brett.'</td>';
				echo '<td>';
				if (isset($this->points[$value->spieler])) { $points = $this->points[$value->spieler]; }
				else { $points = 0; }
				if (isset($value->wname)) {
					$link = new CLMcLink();
					$link->view = 'turnier_player';
					$link->more = array('turnier' => $this->turnier->id, 'snr' => $value->spieler, 'Itemid' => $itemid);
					$link->makeURL();
					if ($this->turnier->typ != '3' AND $this->turnier->typ != '5')
						echo $link->makeLink($value->wname). " (".$points.")";
					else
						echo $link->makeLink($value->wname);
				}
				echo '</td>';
				echo '<td align="center">'.CLMText::formatRating($value->wtwz).'</td>';
				echo '<td align="center">-</td>';
				echo '<td>';
				if (isset($this->points[$value->gegner])) { $points = $this->points[$value->gegner]; }
				else { $points = 0; }
				if (isset($value->sname)) {
					$link = new CLMcLink();
					$link->view = 'turnier_player';
					$link->more = array('turnier' => $this->turnier->id, 'snr' => $value->gegner, 'Itemid' => $itemid);
					$link->makeURL();
					if ($this->turnier->typ != '3' AND $this->turnier->typ != '5')
						echo $link->makeLink($value->sname). " (".$points.")";
					else
						echo $link->makeLink($value->sname);
				}
				echo '</td>';
				echo '<td align="center">'.CLMText::formatRating($value->stwz).'</td>';
				if ($value->ergebnis != NULL) {
					echo '<td align="center">';
					if ($value->pgn == '' OR !$this->pgnShow) {
						echo CLMText::getResultString($value->ergebnis);
					} else {
						echo '<span class="editlinktip hasTip" title="'.JText::_( 'PGN_SHOWMATCH' ).'">';
							echo '<a onclick="startPgnMatch('.$value->id.', \'pgnArea\');" class="pgn">'.CLMText::getResultString($value->ergebnis).'</a>';
						echo '</span>';
						?>
						<input type='hidden' name='pgn[<?php echo $value->id; ?>]' id='pgnhidden<?php echo $value->id; ?>' value='<?php echo $value->pgn; ?>'>
						<?php
					}
					if (($this->turnier->typ == 3 OR $this->turnier->typ == '5') AND ($value->tiebrS > 0 OR $value->tiebrG > 0)) {
						echo '<br /><small>'.$value->tiebrS.':'.$value->tiebrG.'</small>';
					}
					echo '</td>';
					?>
					<?php
				} else {
					echo '<td align="center"></td>';
				}
				
			echo '</tr>';
		}
	
	}
	
			// tl_ok? Haken anzeigen!
	if ($this->displayTlOK AND $this->round->tl_ok > 0) {
		echo '<tr><td colspan="9">';
			echo '<div style="float:right; padding-right:1%;"><label for="name" class="hasTip" title="'.JText::_('TOURNAMENT_ROUNDOK').'"><img src="'.CLMImage::imageURL('accept.png').'" /></label></div>';
		echo '</td></tr>';
	}

	echo '</table>';

	?>
	
	<!--Bereich für pgn-Viewer-->
	<span id="pgnArea"></span>

	<?php

	if ($this->round->bemerkungen != '') {
		echo "<div id='desc'>";
		if ($commentParse) {
			echo JHtml::_('content.prepare', "\n" . $this->round->bemerkungen . "\n");
		} else {
			echo CLMText::formatNote($this->round->bemerkungen);
		}
		echo "</div>";
	}
}

require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); 
echo '</div></div>';
	
?>
