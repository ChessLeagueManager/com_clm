<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2014 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access');

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');


echo "<div id='clm'><div id='turnier_invitation'>";


// componentheading vorbereiten
$heading = $this->turnier->name;
	
echo "<div id='ti_text'>";
	
// Turnier unveröffentlicht?
if ( $this->turnier->published == 0) { 
	echo CLMContent::componentheading($heading);
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOTPUBLISHED')."<br/>".JText::_('TOURNAMENT_PATIENCE'));

// Turnier
} else {
	echo CLMContent::componentheading($heading);
	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
	echo $this->turnier->invitationText;

}
	
echo "</div>";
	
require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); 

echo '</div></div>';
?>
