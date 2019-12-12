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

echo "<div><div id='turnier_info'>";

// Konfigurationsparameter auslesen
$itemid 		= JRequest::getVar( 'Itemid' );
$today = date("Y-m-d");

// componentheading vorbereiten
$heading = $this->turnier->name;

// Turnier unveröffentlicht?
if ( $this->turnier->published == 0) {
	echo CLMContent::componentheading($heading);
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOTPUBLISHED')."<br/>".JText::_('TOURNAMENT_PATIENCE'));

// Turnier Registration nicht gesetzt oder abgelaufen
} elseif (!isset($this->turnier->dateRegistration) OR $this->turnier->dateRegistration < $today)  { // Online Anmeldung vorgesehen?
	echo CLMContent::componentheading($heading);
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_NO_ONLINE_REG'));

// Turnier
} else {
	echo CLMContent::componentheading($heading);
	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
	
	?>
	
	<br />
	
	<?php

	// Online Anmeldung
	
		// Konfigurationsparameter auslesen
		$config = clm_core::$db->config();
		$privacy_notice = $config->privacy_notice;

		$turParams = new clm_class_params($this->turnier->params);
		$typeRegistration = $turParams->get('typeRegistration', 0);
		$reg_name 		= JRequest::getVar('reg_name','');		
		$reg_vorname 	= JRequest::getVar('reg_vorname','');		
		$reg_jahr 		= JRequest::getVar('reg_jahr','');		
		$reg_club 		= JRequest::getVar('reg_club','');		
		$reg_mail 		= JRequest::getVar('reg_mail','');		
		$reg_dwz 		= JRequest::getVar('reg_dwz','');		
		$reg_elo 		= JRequest::getVar('reg_elo','');		
		$reg_comment 		= JRequest::getVar('reg_comment','');		

		if ($typeRegistration < 5) { 
			$headline = JText::_('REGISTRATION_ONLINE');
			$layout = 'sent';
			$button = JText::_('CLUB_DATA_SEND_BUTTON');
		} else {
			$headline = JText::_('REGISTRATION_ONLINE_5');
			$layout = 'selection';
			$button = 'Weiter';
		}
		?>
	  <br>
	<script language="javascript" type="text/javascript">

		 Joomla.submitbutton = function (pressbutton) { 		
			var form = document.adminForm;
			// do field validation
			if (form.name99.value == "") {
				alert( "<?php echo JText::_( 'REGISTRATION_PLAYER_INPUT', true ); ?>" );
				//alert( "Bitte Spieler auswählen" );
			} else if (form.club.value == "") {
				alert( "<?php echo JText::_( 'REGISTRATION_CLUB_INPUT', true ); ?>" );
			} else {
				submitform( pressbutton );
				//form.submit();
			}
		}
 
		</script>
	  <form action="index.php?option=com_clm&amp;view=turnier_registration&amp;layout=selection" method="post" name="adminForm" id="adminForm">
		<table>
	
		<tr>
			<th align="left" colspan="2" class="anfang"><?php echo $headline; ?><br>
				<span style="font-size: 80%; font-weight: lighter;"><?php echo JText::_('REGISTRATION_MANDATORY'); ?> </span><br>
			</th>
		</tr>
	
		<tr>
			<td align="left" width="100"><?php echo JText::_('REGISTRATION_PLAYER'); ?>(*):</td>
			<td>
			<input class="inputbox" type="text" name="reg_name" id="reg_name" size="50" maxlength="100" value="<?php echo $reg_name; ?>" />
			</td>
		</tr>
	
		<tr>
			<td align="left" width="100"><?php echo JText::_('REGISTRATION_VORNAME'); ?>(*):</td>
			<td>
			<input class="inputbox" type="text" name="reg_vorname" id="reg_vorname" size="50" maxlength="100" value="<?php echo $reg_vorname; ?>" />
			</td>
		</tr>
	
		<tr>
			<td align="left" width="100"><?php echo JText::_('REGISTRATION_JAHR'); ?>(*):</td>
			<td>
			<input class="inputbox" type="text" name="reg_jahr" id="reg_jahr" size="4" maxlength="4" value="<?php echo $reg_jahr; ?>" />
			</td>
		</tr>
	
		<tr>
			<td align="left" width="100"><?php echo JText::_('REGISTRATION_MAIL'); ?>(*):</td>
			<td>
			<input class="inputbox" type="text" name="reg_mail" id="reg_mail" size="50" maxlength="100" value="<?php echo $reg_mail; ?>" />
			</td>
		</tr>
	<?php if ($typeRegistration < 5) { 
	?>
		<tr>
			<td align="left" width="100"><?php echo JText::_('REGISTRATION_CLUB'); ?>(*):</td>
			<td>
			<input class="inputbox" type="text" name="reg_club" id="reg_club" size="50" maxlength="100" value="<?php echo $reg_club; ?>" />
			</td>
		</tr>

		<tr>
			<td align="left" width="100"><?php echo JText::_('REGISTRATION_DWZ'); ?>:</td>
			<td>
			<input class="inputbox" type="text" name="reg_dwz" id="reg_dwz" size="4" maxlength="4" value="<?php echo $reg_dwz; ?>" />
			</td>
		</tr>

		<tr>
			<td align="left" width="100"><?php echo JText::_('REGISTRATION_ELO'); ?>:</td>
			<td>
			<input class="inputbox" type="text" name="reg_elo" id="reg_elo" size="4" maxlength="4" value="<?php echo $reg_elo; ?>" />
			</td>
		</tr>

		<tr>
			<td align="left" width="100"><?php echo JText::_('REGISTRATION_COMMENT'); ?>:</td>
			<td>
			<textarea class="inputbox" name="reg_comment" id="reg_comment" cols="50" rows="4" placeholder="Nachricht bitte hier eingeben"><?php echo $reg_comment; ?></textarea>
			</td>
		</tr>
	<?php } else {
		  } 
		// Formular-Ausgabe abschließen und Captcha einbinden 
		$result = clm_core::$load->session_variables('o'); 
		?>
		<tr>
			<td align="left" width="100"><?php echo JText::_('REGISTRATION_CHECK'); ?>(*):</td>
			<td><?php echo $result[1]." + ".$result[2]." = "; ?>	 
				<input class="inputbox" type="text" name="reg_check01" id="reg_check01" size="8" maxlength="10" value="" />
			</td>
		</tr>
		<tr>
			<th align="left" colspan="2" class="anfang">
				<span style="font-size: 80%; font-weight: lighter;"><?php echo JText::_('REGISTRATION_COMMENT_1'); ?></span>
				<?php if ($privacy_notice != '') { ?>
				<br><span style="font-size: 80%; font-weight: lighter;"><?php echo JText::_('REGISTRATION_COMMENT_2A'); ?><a href="<?php echo $privacy_notice; ?>" target="_blank"><span style="color: black;"><?php echo JText::_('REGISTRATION_COMMENT_2B'); ?></span></a></span>
				<span style="font-size: 80%; font-weight: lighter;"><?php echo JText::_('REGISTRATION_COMMENT_3'); ?></span>
				<?php } ?>
			</th>
		</tr>
		
		</table>

		<br>
		<input type="submit" value=" <?php echo $button ?> ">

		<input type="hidden" name="layout" value="<?php echo $layout; ?>" />
		<input type="hidden" name="view" value="turnier_registration" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="turnier" value="<?php echo $this->turnier->id; ?>" />
		<input type="hidden" name="typeRegistration" value="<?php echo $typeRegistration; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
		
	  </form>
				
<?php	}


	
require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); 
echo '</div></div>';
?>
