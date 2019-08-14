<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2019 CLM Team.  All rights reserved
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

	$mainframe	= JFactory::getApplication();

// Variablen initialisieren
$turnier 		= $this->turnier;

$user =JFactory::getUser();
	$link = JURI::base() .'index.php?option=com_clm&view=turnier_registration&turnier='. $turnier->id .'&Itemid='; 

if (1==1)	{
// Prüfen ob Datensatz schon vorhanden ist

// Datensätze in Tabelle schreiben

// Variablen holen
$typeRegistration = JRequest::getVar('typeRegistration','');
$reg_check01 	= JRequest::getVar('reg_check01','');
$reg_name 		= JRequest::getVar('reg_name','');
$reg_vorname 	= JRequest::getVar('reg_vorname','');
$reg_jahr 		= JRequest::getVar('reg_jahr','');
$reg_mail 		= JRequest::getVar('reg_mail','');
$reg_club 		= JRequest::getVar('reg_club','');
$reg_dwz 		= JRequest::getVar('reg_dwz','');
$reg_elo 		= JRequest::getVar('reg_elo','');
$reg_comment 		= JRequest::getVar('reg_comment','');
$f_source 		= JRequest::getVar('f_source','');
if ($f_source = 'sent') {
	$reg_spieler 		= JRequest::getVar('reg_spieler',100);
}
$session = JFactory::getSession();
$reg_wert = $session->get('reg_wert');

// Überprüfen der Eingaben
$msg = '';
if ($reg_name == '') 
	$msg .= '<br>'.'Name nicht eingegeben';
if ($reg_vorname == '') 
	$msg .= '<br>'.'Vorname nicht eingegeben';
if ($reg_jahr == '') 
	$msg .= '<br>'.'Geburtsjahr nicht eingegeben';
if ($reg_jahr != '' AND (!is_numeric($reg_jahr) OR $reg_jahr < 1880 OR $reg_jahr > 2018))
	$msg .= '<br>'.'Geburtsjahr nicht korrekt';
if (!clm_core::$load->is_email($reg_mail)) 
	$msg .= '<br>'.'Mail-Adresse nicht korrekt';
if ($f_source != 'sent') {
	if ($reg_check01 == '') 
		$msg .= '<br>'.'Spam-Wert nicht eingegeben';
	elseif ($reg_check01 != $reg_wert) 
		$msg .= '<br>'.'Spam-Wert nicht korrekt';
}
if ($msg != '') {
	$link = JURI::base() .'index.php?option=com_clm&view=turnier_registration&turnier='. $turnier->id .'&Itemid='; 
	$link .= '&reg_name='.$reg_name.'&reg_vorname='.$reg_vorname.'&reg_club='.$reg_club.'&reg_mail='.$reg_mail.'&reg_jahr='.$reg_jahr;
	$link .= '&reg_dwz='.$reg_dwz.'&reg_elo='.$reg_elo.'&reg_comment='.$reg_comment;
	$msg = substr($msg,4);
	$mainframe->redirect( $link, $msg, "warning" );
}
if ($f_source != 'sent') {
	$reg_club = '';
	$reg_dwz = '';
	$reg_elo = '';
	$reg_comment = '';
}
$result = clm_core::$api->db_dewis_player_by_name($reg_name, $reg_vorname, $reg_jahr); 
$names = $result[3];
$ii = count($names);

echo "<div><div id='turnier_info'>";
// componentheading vorbereiten
$heading = $this->turnier->name;
	echo CLMContent::componentheading($heading);
		// Captcha vorbereiten 
		$sresult = clm_core::$load->session_variables('o'); 

?>
	<br />
		<table>
			<tr><th class="anfang">
				<td class="anfang" style="font-size: 120%; font-weight: bolder;">Anmeldung - Vereinszugehörigkeit und Wertzahlen</td>
			</tr>
		</table>
	Wählen Sie den für Sie richtigen Eintrag aus der DWZ-Liste.
	<br>	
		<form action="index.php?option=com_clm&amp;view=turnier_registration&amp;layout=sent" method="post" name="adminForm" id="adminForm">
		<table>
			<tr><th class="anfang">
				<td class="anfang">Name,Vorname</td><td class="anfang">DWZ</td><td class="anfang">Elo</td><td class="anfang">Verein</td>
			</th></tr>
			<?php for ($i = 0; $i < $ii; $i++) { ?>
				<tr><td style="text-align: center;"><input type="radio" id="<?php echo 'spieler'.($i); ?>" name="reg_spieler" value="<?php echo ($i); ?>"<?php if ($reg_spieler == $i) echo ' checked="checked"'; ?>></td>
				<td><?php echo $names[$i]->surname.','.$names[$i]->firstname; ?></td>
				<td><?php echo $names[$i]->rating; ?></td>
				<td><?php echo $names[$i]->elo; ?></td>
				<td><?php echo $names[$i]->club; ?></td>
				<input type="hidden" name="<?php echo 'reg_name'.($i); ?>" value="<?php echo $names[$i]->surname; ?>" />
				<input type="hidden" name="<?php echo 'reg_vorname'.($i); ?>" value="<?php echo $names[$i]->firstname; ?>" />
				<input type="hidden" name="<?php echo 'reg_club'.($i); ?>" value="<?php echo $names[$i]->club; ?>" />
				<input type="hidden" name="<?php echo 'reg_dwz'.($i); ?>" value="<?php echo $names[$i]->rating; ?>" />
				<input type="hidden" name="<?php echo 'reg_elo'.($i); ?>" value="<?php echo $names[$i]->elo; ?>" />
				</tr>
			<?php } ?>
		</table>
	Ist der richtige Eintrag oben nicht dabei, wählen Sie diesen aus und ergänzen Sie unten stehenden Daten.
		<table style="width: 50%;">
			<tr><th class="anfang">
				<td class="anfang">Name,Vorname</td>
			</th></tr>
			<tr><td style="text-align: center;"><input type="radio" id="spieler99" name="reg_spieler" value="99"<?php if ($reg_spieler == 99) echo ' checked="checked"'; ?>></td><td><?php echo $reg_name.','.$reg_vorname; ?></td>
			</tr>
		</table>
		<table style="width: 50%;">
		<tr>
			<td align="left" width="100" class="anfang"><?php echo JText::_('REGISTRATION_CLUB'); ?>(*):</td>
			<td>
			<input class="inputbox" type="text" name="reg_club" id="reg_club" size="50" maxlength="100" value="<?php echo $reg_club; ?>" />
			</td>
		</tr>
		<tr>
			<td align="left" width="100" class="anfang"><?php echo JText::_('REGISTRATION_DWZ'); ?>:</td>
			<td>
			<input class="inputbox" type="text" name="reg_dwz" id="reg_dwz" size="4" maxlength="4" value="<?php echo $reg_dwz; ?>" />
			</td>
		</tr>
		<tr>
			<td align="left" width="100" class="anfang"><?php echo JText::_('REGISTRATION_ELO'); ?>:</td>
			<td>
			<input class="inputbox" type="text" name="reg_elo" id="reg_elo" size="4" maxlength="4" value="<?php echo $reg_elo; ?>" />
			</td>
		</tr>
		</table>
		<br>
	Hier können Sie eine Nachricht an den Turnierleiter eingeben, falls notwendig oder gewünscht.
		<table style="width: 50%;">
		<tr>
			<td align="left" width="100" class="anfang"><?php echo JText::_('REGISTRATION_COMMENT'); ?>:</td>
			<td>
			<textarea class="inputbox" name="reg_comment" id="reg_comment" cols="50" rows="4" placeholder="Nachricht bitte hier eingeben"><?php echo $reg_comment; ?></textarea>
			</td>
		</tr>
		<tr>
			<td align="left" width="100" class="anfang"><?php echo JText::_('REGISTRATION_CHECK'); ?>(*):</td>
			<td><?php echo $sresult[1]." + ".$sresult[2]." = "; ?>	 
				<input class="inputbox" type="text" name="reg_check01" id="reg_check01" size="8" maxlength="10" value="" />
			</td>
		</tr>
		<tr>
			<th align="left" colspan="2" class="anfang">
				<span style="font-size: 80%; font-weight: lighter;">Nach Absenden des Formulars erhalten Sie eine Bestätigungsmail, überprüfen Sie deshalb Ihre Email-Eintragung nochmals</span></th>
		</tr>
		
		</table>

		<br>
		<input type="submit" value=" <?php echo JText::_('CLUB_DATA_SEND_BUTTON') ?> ">

		<input type="hidden" name="layout" value="sent" />
		<input type="hidden" name="view" value="turnier_registration" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="turnier" value="<?php echo $this->turnier->id; ?>" />
		<input type="hidden" name="typeRegistration" value="<?php echo $typeRegistration; ?>" />
		<input type="hidden" name="reg_name" value="<?php echo $reg_name; ?>" />
		<input type="hidden" name="reg_vorname" value="<?php echo $reg_vorname; ?>" />
		<input type="hidden" name="reg_jahr" value="<?php echo $reg_jahr; ?>" />
		<input type="hidden" name="reg_mail" value="<?php echo $reg_mail; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
		
	  </form>
<?php

require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); 
echo '</div></div>';
									
}
?>


