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
 * Kommentare in Deutsch - Comments in English
*/
defined('_JEXEC') or die('Restricted access');

// Stylesheet laden - load CSS
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

echo "<div id='clm'><div id='turnier_info'>";

// Konfigurationsparameter auslesen - get configuration parameter
$itemid 		= clm_core::$load->request_int('Itemid');
$today = date("Y-m-d");

// componentheading vorbereiten - prepare componentheading
$heading = $this->turnier->name;

// Turnier unveröffentlicht? - Tournament unpublished?
if ($this->turnier->published == 0) {
    echo CLMContent::componentheading($heading);
    echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOTPUBLISHED')."<br/>".JText::_('TOURNAMENT_PATIENCE'));

    // Turnier Registration nicht gesetzt oder abgelaufen - Tournament registration not chosen or expired
} elseif (!isset($this->turnier->dateRegistration) or $this->turnier->dateRegistration < $today) { // Online Anmeldung vorgesehen? - Omline registration provided?
    echo CLMContent::componentheading($heading);
    echo CLMContent::clmWarning(JText::_('TOURNAMENT_NO_ONLINE_REG'));

    // Turnier - Tournament
} else {
    echo CLMContent::componentheading($heading);
    require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');

    ?>
	
	<br />
	
	<?php

    // Online Anmeldung - Online Registration

        // Konfigurationsparameter auslesen - get configuration parameter
        $config = clm_core::$db->config();
    $privacy_notice = $config->privacy_notice;

    $turParams = new clm_class_params($this->turnier->params);
    $typeRegistration = $turParams->get('typeRegistration', 0);
    $typeAccount 	= $turParams->get('typeAccount', 0);
    $optionEloAnalysis = $turParams->get('optionEloAnalysis', 0);
    $reg_name 		= clm_core::$load->request_string('reg_name', '');
    $reg_vorname 	= clm_core::$load->request_string('reg_vorname', '');
    $reg_jahr 		= clm_core::$load->request_string('reg_jahr', '');
    $reg_geschlecht	= clm_core::$load->request_string('reg_geschlecht', '');
    $reg_club 		= clm_core::$load->request_string('reg_club', '');
    $reg_mail 		= clm_core::$load->request_string('reg_mail', '');
    $reg_tel_no 	= clm_core::$load->request_string('reg_tel_no', '');
    $reg_account 	= clm_core::$load->request_string('reg_account', '');
    $reg_zps 		= clm_core::$load->request_string('reg_zps', '');
    $reg_mgl_nr 		= clm_core::$load->request_string('reg_mgl_nr', '');
    $reg_dwz 		= clm_core::$load->request_string('reg_dwz', '');
    $reg_elo 		= clm_core::$load->request_string('reg_elo', '');
    $reg_FIDEid 		= clm_core::$load->request_string('reg_FIDEid', '');
    $reg_comment 	= clm_core::$load->request_string('reg_comment', '');
    $reg_dsgvo 	= clm_core::$load->request_string('reg_dsgvo', 0);

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
				alert( "<?php echo JText::_('REGISTRATION_PLAYER_INPUT', true); ?>" );
			} else if (form.club.value == "") {
				alert( "<?php echo JText::_('REGISTRATION_CLUB_INPUT', true); ?>" );
			} else {
				Joomla.submitform( pressbutton );
			}
		}
 
		</script>
	  <form action="index.php?option=com_clm&amp;view=turnier_registration&amp;layout=selection" method="post" name="adminForm" id="adminForm">
		<table>
	
		<tr>
			<th align="left" colspan="4" class="anfang"><?php echo $headline; ?><br>
				<span style="font-size: 80%; font-weight: lighter;"><?php echo JText::_('REGISTRATION_MANDATORY'); ?> </span><br>
				<span style="font-size: 80%; font-weight: lighter;"><?php echo JText::_('REGISTRATION_DWZ_EVALUATION'); ?> </span><br>
			</th>
		</tr>
	
		<tr>
			<td align="left" width="100"><?php echo JText::_('REGISTRATION_PLAYER'); ?>(*):</td>
			<td colspan="3">
			<input class="inputbox" type="text" name="reg_name" id="reg_name" size="50" maxlength="100" value="<?php echo $reg_name; ?>" />
			</td>
		</tr>
	
		<tr>
			<td align="left" width="100"><?php echo JText::_('REGISTRATION_VORNAME'); ?>(*):</td>
			<td colspan="3">
			<input class="inputbox" type="text" name="reg_vorname" id="reg_vorname" size="50" maxlength="100" value="<?php echo $reg_vorname; ?>" />
			</td>
		</tr>
	
		<tr>
			<td align="left" width="100"><?php echo JText::_('REGISTRATION_JAHR'); ?>(*):</td>
			<td colspan="3">
			<input class="inputbox" type="text" name="reg_jahr" id="reg_jahr" size="4" maxlength="4" value="<?php echo $reg_jahr; ?>" />
			</td>
		</tr>
	
		<tr>
			<td align="left" width="100"><?php echo JText::_('REGISTRATION_MAIL'); ?>(*):</td>
			<td colspan="3">
			<input class="inputbox" type="text" name="reg_mail" id="reg_mail" size="50" maxlength="100" value="<?php echo $reg_mail; ?>" />
			</td>
		</tr>
		<tr>
			<td align="left" width="100"><?php echo JText::_('REGISTRATION_TEL_NO'); ?>:</td>
			<td colspan="3">
			<input class="inputbox" type="text" name="reg_tel_no" id="reg_tel_no" size="30" maxlength="30" value="<?php echo $reg_tel_no; ?>" />
			</td>
		</tr>
	<?php if ($typeAccount > 0) {
	    ?>
		<tr>
			<td align="left" width="100" title="<?php echo JText::_('REGISTRATION_ACCOUNT_HINT'); ?>"><?php echo JText::_('REGISTRATION_ACCOUNT_'.$typeAccount); ?>(*):</td>
			<td colspan="3">
			<input class="inputbox" type="text" name="reg_account" id="reg_account" size="30" maxlength="50" value="<?php echo $reg_account; ?>" />
			</td>
		</tr>
	<?php } ?>
	<?php if ($typeRegistration < 5) {
	    ?>
		<tr>
			<td align="left" width="100"><?php echo JText::_('REGISTRATION_CLUB'); ?>(*):</td>
			<td colspan="3">
			<input class="inputbox" type="text" name="reg_club" id="reg_club" size="50" maxlength="100" value="<?php echo $reg_club; ?>" />
			</td>
		</tr>

		<tr>
			<td align="left" width="100"><?php echo JText::_('REGISTRATION_ZPS'); ?>(**):</td>
			<td width="40%">
			<input class="inputbox" type="text" name="reg_zps" id="reg_zps" size="5" maxlength="5" value="<?php echo $reg_zps; ?>" />
			</td>
			<td align="left" width="100"><?php echo JText::_('REGISTRATION_MGLNR'); ?>(**):</td>
			<td>
			<input class="inputbox" type="text" name="reg_mgl_nr" id="reg_mgl_nr" size="4" maxlength="4" value="<?php echo $reg_mgl_nr; ?>" />
			</td>
		</tr>

		<tr>
			<td align="left" width="100"><?php echo JText::_('REGISTRATION_DWZ'); ?>:</td>
			<td width="40%">
			<input class="inputbox" type="text" name="reg_dwz" id="reg_dwz" size="4" maxlength="4" value="<?php echo $reg_dwz; ?>" />
			</td>
			<td align="left" width="100"><?php echo JText::_('REGISTRATION_ELO'); ?>:</td>
			<td>
			<input class="inputbox" type="text" name="reg_elo" id="reg_elo" size="4" maxlength="4" value="<?php echo $reg_elo; ?>" />
			</td>
		</tr>
		<?php if ($optionEloAnalysis == 1) { ?>
		<tr>
			<td align="left" width="100"><?php echo JText::_('REGISTRATION_FIDEID'); ?>:</td>
			<td colspan="3">
			<input class="inputbox" type="text" name="reg_FIDEid" id="reg_FIDEid" size="8" maxlength="8" value="<?php echo $reg_FIDEid; ?>" />
			</td>
		</tr>
		<?php } ?>
		<tr>
			<td align="left" width="100"><?php echo JText::_('REGISTRATION_SEX'); ?>:</td>
			<td class="paramlist_value" colspan="3">
					<?php
	                    $options = array();
	    $options[''] = '';
	    $options['M'] = JText::_('REGISTRATION_SEX_M');
	    $options['W'] = JText::_('REGISTRATION_SEX_W');
	    $optionlist = array();
	    foreach ($options as $key => $val) {
	        $optionlist[]	= JHtml::_('select.option', $key, $val, 'id', 'name');
	    }
	    echo JHtml::_('select.genericlist', $optionlist, 'reg_geschlecht', 'class="inputbox"', 'id', 'name', $reg_geschlecht);
	    ?>
				</td>
		</tr>
		<tr>
			<td align="left" width="100"><?php echo JText::_('REGISTRATION_COMMENT'); ?>:</td>
			<td colspan="3">
			<textarea class="inputbox" name="reg_comment" id="reg_comment" cols="50" rows="4" placeholder="<?php echo JText::_('REGISTRATION_PLACEHOLDER'); ?>"><?php echo $reg_comment; ?></textarea>
			</td>
		</tr>
	<?php } else {
	}
    // Formular-Ausgabe abschließen und Captcha einbinden - Finish formular output and implement captcha
    $result = clm_core::$load->session_variables('o');
    ?>
		<?php if ($privacy_notice != '') { ?>
		  <tr>
			<th style="align: center;" class="anfang">&nbsp;&nbsp;&nbsp;<input type="checkbox" id="reg_dsgvo" name="reg_dsgvo" value="1">
				<span style="font-size: 80%; font-weight: lighter;">&nbsp;<?php echo JText::_('REGISTRATION_COMMENT_0'); ?></span></th>
			<th align="left" colspan="3" class="anfang">
				<span style="font-size: 80%; font-weight: lighter;"><?php echo JText::_('REGISTRATION_COMMENT_2A'); ?><a href="<?php echo $privacy_notice; ?>" target="_blank"><span style="color: black;"><?php echo JText::_('REGISTRATION_COMMENT_2B'); ?></span></a></span>
				<span style="font-size: 80%; font-weight: lighter;"><?php echo JText::_('REGISTRATION_COMMENT_3'); ?></span>
			</th>
		  </tr>
		<?php } ?>
		<tr>
			<td align="left" width="100"><?php echo JText::_('REGISTRATION_CHECK'); ?>(*):</td>
			<td><?php echo $result[1]." + ".$result[2]." = "; ?>	 
				<input class="inputbox" type="text" name="reg_check01" id="reg_check01" size="8" maxlength="10" value="" />
			</td>
		</tr>
		<tr>
			<th align="left" colspan="2" class="anfang">
				<span style="font-size: 80%; font-weight: lighter;"><?php echo JText::_('REGISTRATION_COMMENT_1'); ?></span>
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
		<input type="hidden" name="typeAccount" value="<?php echo $typeAccount; ?>" />
		<input type="hidden" name="optionEloAnalysis" value="<?php echo $optionEloAnalysis; ?>" />
		<input type="hidden" name="privacy_notice" value="<?php echo $privacy_notice; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_('form.token'); ?>
		
	  </form>
				
<?php	}



require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php');
echo '</div></div>';
?>
