<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');
//HTMLHelper::_('behavior.tooltip', '.CLMTooltip');
require_once (JPATH_COMPONENT . DS . 'includes' . DS . 'clm_tooltip.php');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

// Konfigurationsparameter auslesen
$itemid = clm_core::$load->request_int( 'Itemid' );
$config = clm_core::$db->config();
$commentParse = $config->tourn_comment_parse;
$pgn		= clm_core::$load->request_int('pgn'); 

// Userkennung holen
$user	=Factory::getUser();
$jid	= $user->get('id');

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');


echo "<div id='clm'><div id='turnier_runde'>";

	$heading = $this->turnier->name;
	$heading .= ": ".$this->round->name;
	
$archive_check = clm_core::$api->db_check_season_user($this->turnier->sid);
if (!$archive_check) {
	echo CLMContent::componentheading($heading);
	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
	echo CLMContent::clmWarning(Text::_('NO_ACCESS')."<br/>".Text::_('NOT_REGISTERED'));
// Turnier unveröffentlicht?
} elseif ( $this->turnier->published == 0) { 
	echo CLMContent::componentheading($heading);
	echo CLMContent::clmWarning(Text::_('TOURNAMENT_NOTPUBLISHED')."<br/>".Text::_('TOURNAMENT_PATIENCE'));

// Runden nicht erstellt
} elseif ($this->turnier->rnd == 0) {
	echo CLMContent::componentheading($heading);
	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
	echo CLMContent::clmWarning(Text::_('TOURNAMENT_NOROUNDS'));

} elseif ($this->round->published != 1) {
	echo CLMContent::componentheading($heading);
	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
	echo CLMContent::clmWarning(Text::_('TOURNAMENT_ROUNDNOTPUBLISHED'));

// Turnier/Runde kann ausgegeben werden
} else {
	$turParams = new clm_class_params($this->turnier->params);
	if ($this->round->datum != "0000-00-00" AND $this->round->datum != "1970-01-01" AND $turParams->get('displayRoundDate', 1) == 1) {
		$heading .=  ',&nbsp;'.HTMLHelper::_('date',  $this->round->datum, Text::_('DATE_FORMAT_CLM_F')); 
		if(isset($this->round->startzeit) and $this->round->startzeit != '00:00:00') { $heading .= '  '.substr($this->round->startzeit,0,5).' Uhr'; }
	}

	// PDF-Link
	echo CLMContent::createPDFLink('turnier_rbretter', Text::_('PDF_TOURNAMENTROUND'), array('turnier' => $this->turnier->id, 'layout' => 'rbretter', 'dg' => $this->round->dg, 'runde' => $this->round->nr) );
		
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
		<th align="center"><?php echo Text::_('Nr'); ?></th>
		<th align="center"><?php echo Text::_('TOURNAMENT_PLAYER'); ?></th>
		<th align="center"><?php echo Text::_('TOURNAMENT_BOARD'); ?></th>
		<th align="center"><?php echo Text::_('TOURNAMENT_FARBE'); ?></th>
		<th align="center"><?php echo Text::_('TOURNAMENT_GEGNER'); ?></th>
		<th align="center"><?php echo Text::_('RESULT'); ?></th>
	</tr>

	<?php
	
	// alle Einträge durchgehen
	$ia = 0;
//echo "<br>points"; var_dump($this->points);
//echo "<br>s_points"; var_dump($this->s_points);
	foreach ($this->matches as $value) {
	
		if (!is_null($value->name)) {
//echo "<br>value"; var_dump($value);
//die();
			// Farbe
			$ia++;
			if ($ia%2 != 0) { 
				$zeilenr = "zeile1"; 
			} else { 
				$zeilenr = "zeile2"; 
			}

			$ic = 0;
			echo '<tr class="'.$zeilenr.'">';
				echo '<td align="center">'.$ia.'</td>';
				echo '<td>';
				if (isset($this->points[$value->spieler])) { $points = $this->points[$value->spieler]; }
				else { $points = 0; }
				if (isset($value->name)) {
					$link = new CLMcLink();
					$link->view = 'turnier_player';
					$link->more = array('turnier' => $this->turnier->id, 'snr' => $value->spieler, 'Itemid' => $itemid);
					$link->makeURL();
					if ($this->turnier->typ != '3' AND $this->turnier->typ != '5')
						echo $link->makeLink($value->name). " (".$points.")";
					else
						echo $link->makeLink($value->name);
				}
				echo '</td>';
			if ($value->gegner < 1) { 
				echo '<td align="center">'.'-'.'</td>';
				echo '<td align="center">'.'-'.'</td>';
			} else {
				echo '<td align="center">'.$value->brett.'</td>';
				if ($value->heim == 1)	echo '<td align="center">'.'Weiß'.'</td>';
				elseif ($value->heim == 0)	echo '<td align="center">'.'Schwarz'.'</td>';
				else echo '<td align="center">'.''.'</td>';
			}

			echo '<td>';
				if (isset($this->points[$value->gegner])) { $points = $this->points[$value->gegner]; }
				else { $points = 0; }
				if (isset($value->gname)) {
					$link = new CLMcLink();
					$link->view = 'turnier_player';
					$link->more = array('turnier' => $this->turnier->id, 'snr' => $value->gegner, 'Itemid' => $itemid);
					$link->makeURL();
					if ($this->turnier->typ != '3' AND $this->turnier->typ != '5')
						echo $link->makeLink($value->gname). " (".$points.")";
					else
						echo $link->makeLink($value->gname);
				}
			echo '</td>';
				if (!is_null($value->ergebnis)) {
					echo '<td align="center">';
						echo CLMText::getResultString($value->ergebnis);
					echo '</td>';
					?>
					<?php
				} else {
					echo '<td align="center"></td>';
				}
				
			echo '</tr>';
		}
	
	}
	

	echo '</table>';

}

require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); 
echo '</div></div>';
	
?>
