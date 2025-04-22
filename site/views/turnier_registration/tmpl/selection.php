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

// Stylesheet laden - loas CSS
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

$mainframe	= JFactory::getApplication();

// Variablen initialisieren - install variables
$turnier 		= $this->turnier;

$user = JFactory::getUser();
$link = JURI::base() .'index.php?option=com_clm&view=turnier_registration&turnier='. $turnier->id .'&Itemid=';


// Datensätze in Tabelle schreiben - Transfer data into db-table

// Variablen holen - get variables
$typeRegistration = clm_core::$load->request_string('typeRegistration', '');
$typeAccount	= clm_core::$load->request_string('typeAccount', '');
$optionEloAnalysis	= clm_core::$load->request_string('optionEloAnalysis', 0);
$privacy_notice = clm_core::$load->request_string('privacy_notice', '');
$reg_dsgvo 		= clm_core::$load->request_int('reg_dsgvo', 0);
$reg_check01 	= clm_core::$load->request_string('reg_check01', '');
$reg_name 		= clm_core::$load->request_string('reg_name', '');
$reg_vorname 	= clm_core::$load->request_string('reg_vorname', '');
$reg_jahr 		= clm_core::$load->request_string('reg_jahr', '');
$reg_mail 		= clm_core::$load->request_string('reg_mail', '');
$reg_tel_no 	= clm_core::$load->request_string('reg_tel_no', '');
$reg_account 	= clm_core::$load->request_string('reg_account', '');
$reg_club 		= clm_core::$load->request_string('reg_club', '');
$reg_zps 		= clm_core::$load->request_string('reg_zps', '');
$reg_mgl_nr 	= clm_core::$load->request_string('reg_mgl_nr', '');
$reg_dwz 		= clm_core::$load->request_string('reg_dwz', '');
$reg_elo 		= clm_core::$load->request_string('reg_elo', '');
$reg_FIDEid 	= clm_core::$load->request_string('reg_FIDEid', '');
$reg_geschlecht	= clm_core::$load->request_string('reg_geschlecht', '');
$reg_comment 	= clm_core::$load->request_string('reg_comment', '');
$f_source 		= clm_core::$load->request_string('f_source', '');
if ($f_source = 'sent') {
    $reg_spieler 		= clm_core::$load->request_int('reg_spieler', 100);
}
$session = JFactory::getSession();
$reg_wert = $session->get('reg_wert');
$c_year = date("Y");

// Überprüfen der Eingaben - check input
$msg = '';
if ($reg_name == '') {
    $msg .= '<br>'.JText::_('REGISTRATION_E_NAME');
}
if ($reg_vorname == '') {
    $msg .= '<br>'.JText::_('REGISTRATION_E_VORNAME');
}
if ($reg_jahr == '') {
    $msg .= '<br>'.JText::_('REGISTRATION_E_YEAR');
}
if ($reg_jahr != '' and (!is_numeric($reg_jahr) or $reg_jahr < ($c_year - 110) or $reg_jahr > ($c_year - 2))) {
    $msg .= '<br>'.JText::_('REGISTRATION_E_YEARK');
}
if (!clm_core::$load->is_email($reg_mail)) {
    $msg .= '<br>'.JText::_('REGISTRATION_E_MAIL');
}
if ($typeAccount > '0') {
    if ($reg_account == '') {
        $msg .= '<br>'.JText::_('REGISTRATION_E_ACCOUNT_NO');
    } elseif ($typeAccount == '1') {
        if (substr($reg_account, 0, 22) == 'https://lichess.org/@/') {
            $reg_account1 = $reg_account;
            $s_account = 0;
        } else {
            $reg_account1 = 'https://lichess.org/@/'.$reg_account;
            $s_account = 1;
        }
        if (@file_get_contents($reg_account1, false, null, 0, 1) === false) {
            $msg .= '<br>'.JText::_('REGISTRATION_E_ACCOUNT_NK');
        }
        if ($s_account == 1) {
            $reg_account = 'https://lichess.org/@/'.$reg_account;
        }
    }
}
if ($reg_dsgvo == 0 and $privacy_notice != '') {
    $msg .= '<br>'.JText::_('REGISTRATION_E_CHECKBOX');
}
if ($f_source != 'sent') {
    if ($reg_check01 == '') {
        $msg .= '<br>'.JText::_('REGISTRATION_E_SPAM');
    } elseif ($reg_check01 != $reg_wert) {
        $msg .= '<br>'.JText::_('REGISTRATION_E_SPAM');
    }
}
if ($msg != '') {
    $link = JURI::base() .'index.php?option=com_clm&view=turnier_registration&turnier='. $turnier->id .'&Itemid=';
    $link .= '&reg_name='.$reg_name.'&reg_vorname='.$reg_vorname.'&reg_club='.$reg_club.'&reg_mail='.$reg_mail.'&reg_jahr='.$reg_jahr;
    $link .= '&reg_zps='.$reg_zps.'&reg_mgl_nr='.$reg_mgl_nr;
    $link .= '&reg_dwz='.$reg_dwz.'&reg_elo='.$reg_elo.'&reg_tel_no='.$reg_tel_no.'&reg_account='.$reg_account.'&reg_comment='.$reg_comment;
    $msg = substr($msg, 4);
    $mainframe->enqueueMessage($msg, "warning");
    $mainframe->redirect($link);
}
if ($f_source != 'sent') {
    $reg_club = '';
    $reg_zps = '';
    $reg_mgl_nr = '';
    $reg_dwz = '';
    $reg_elo = '';
    $reg_FIDEid = '';
    $reg_geschlecht = '';
    $reg_comment = '';
}
$result = clm_core::$api->db_dewis_player_by_name($reg_name, $reg_vorname, $reg_jahr);
$names = $result[3];
if (is_null($names)) {
    $ii = 0;
} else {
    $ii = count($names);
}

echo "<div><div id='turnier_info'>";
// componentheading vorbereiten - prepare componentheading
$heading = $this->turnier->name;
echo CLMContent::componentheading($heading);
// Captcha vorbereiten - prepare captcha
$sresult = clm_core::$load->session_variables('o');

?>
	<br />
		<table>
			<tr><th class="anfang">
				<td class="anfang" style="font-size: 120%; font-weight: bolder;"><?php echo JText::_('REGISTRATION_CLUB_ELO'); ?></td>
			</tr>
		</table>
	<?php echo JText::_('REGISTRATION_LIST_OF_PLAYERS'); ?>
	<br>	
		<form action="index.php?option=com_clm&amp;view=turnier_registration&amp;layout=sent" method="post" name="adminForm" id="adminForm">
		<table>
			<tr><th class="anfang">
				<td class="anfang"><?php echo JText::_('REGISTRATION_PLAYER'); ?>,<?php echo JText::_('REGISTRATION_VORNAME'); ?></td>
				<td class="anfang"><?php echo JText::_('REGISTRATION_DWZ'); ?></td>
				<td class="anfang"><?php echo JText::_('REGISTRATION_ELO'); ?></td>
				<td class="anfang"><?php echo JText::_('REGISTRATION_CLUB'); ?></td>
			</th></tr>
			<?php for ($i = 0; $i < $ii; $i++) {
			    if ($names[$i]->gender == 'm') {
			        $names[$i]->gender = 'M';
			    }
			    if ($names[$i]->gender == 'f') {
			        $names[$i]->gender = 'W';
			    } ?>
				<tr><td style="text-align: center;"><input type="radio" id="<?php echo 'spieler'.($i); ?>" name="reg_spieler" value="<?php echo($i); ?>"<?php if ($reg_spieler == $i) {
				    echo ' checked="checked"';
				} ?>></td>
				<td><?php echo $names[$i]->surname.','.$names[$i]->firstname; ?></td>
				<td><?php echo $names[$i]->rating; ?></td>
				<td><?php echo $names[$i]->elo; ?></td>
				<td><?php echo $names[$i]->club; ?></td>
				<input type="hidden" name="<?php echo 'reg_name'.($i); ?>" value="<?php echo $names[$i]->surname; ?>" />
				<input type="hidden" name="<?php echo 'reg_vorname'.($i); ?>" value="<?php echo $names[$i]->firstname; ?>" />
				<input type="hidden" name="<?php echo 'reg_club'.($i); ?>" value="<?php echo $names[$i]->club; ?>" />
				<input type="hidden" name="<?php echo 'reg_dwz'.($i); ?>" value="<?php echo $names[$i]->rating; ?>" />
				<input type="hidden" name="<?php echo 'reg_elo'.($i); ?>" value="<?php echo $names[$i]->elo; ?>" />
				<input type="hidden" name="<?php echo 'reg_PKZ'.($i); ?>" value="<?php echo $names[$i]->pid; ?>" />
				<input type="hidden" name="<?php echo 'reg_titel'.($i); ?>" value="<?php echo $names[$i]->fideTitle; ?>" />
				<input type="hidden" name="<?php echo 'reg_geschlecht'.($i); ?>" value="<?php echo $names[$i]->gender; ?>" />
				<input type="hidden" name="<?php echo 'reg_birthYear'.($i); ?>" value="<?php echo $names[$i]->yearOfBirth; ?>" />
				<input type="hidden" name="<?php echo 'reg_mgl_nr'.($i); ?>" value="<?php echo $names[$i]->membership; ?>" />
				<input type="hidden" name="<?php echo 'reg_zps'.($i); ?>" value="<?php echo $names[$i]->vkz; ?>" />
				<input type="hidden" name="<?php echo 'reg_dwz_I0'.($i); ?>" value="<?php echo $names[$i]->ratingIndex; ?>" />
				<input type="hidden" name="<?php echo 'reg_FIDEid'.($i); ?>" value="<?php echo $names[$i]->idfide; ?>" />
				<input type="hidden" name="<?php echo 'reg_FIDEcco'.($i); ?>" value="<?php echo $names[$i]->nationfide; ?>" />
				</tr>
			<?php } ?>
		</table>
	<?php echo "<br>".JText::_('REGISTRATION_EDIT_DATA'); ?><br>
		<span style="font-size: 80%; font-weight: lighter;"><?php echo JText::_('REGISTRATION_MANDATORY'); ?> </span><br>
		<span style="font-size: 80%; font-weight: lighter;"><?php echo JText::_('REGISTRATION_DWZ_EVALUATION'); ?> </span><br>
		<table>
			<tr><th class="anfang">
				<td class="anfang"><?php echo JText::_('REGISTRATION_PLAYER'); ?>,<?php echo JText::_('REGISTRATION_VORNAME'); ?></td>
			</th></tr>
			<tr><td style="text-align: center;"><input type="radio" id="spieler99" name="reg_spieler" value="99"<?php if ($reg_spieler == 99) {
			    echo ' checked="checked"';
			} ?>></td><td><?php echo $reg_name.','.$reg_vorname; ?></td>
			</tr>
		</table>
		<table>
		<tr>
			<td align="left" width="100" class="anfang"><?php echo JText::_('REGISTRATION_CLUB'); ?>(*):</td>
			<td colspan="3">
			<input class="inputbox" type="text" name="reg_club" id="reg_club" size="50" maxlength="100" value="<?php echo $reg_club; ?>" />
			</td>
		</tr>
		<tr>
			<td align="left" width="100" class="anfang"><?php echo JText::_('REGISTRATION_ZPS'); ?>(**):</td>
			<td width="40%">
			<input class="inputbox" type="text" name="reg_zps" id="reg_zps" size="5" maxlength="5" value="<?php echo $reg_zps; ?>" />
			</td>
			<td align="left" width="100" class="anfang"><?php echo JText::_('REGISTRATION_MGLNR'); ?>(**):</td>
			<td>
			<input class="inputbox" type="text" name="reg_mgl_nr" id="reg_mgl_nr" size="4" maxlength="4" value="<?php echo $reg_mgl_nr; ?>" />
			</td>
		</tr>
		<tr>
			<td align="left" width="100" class="anfang"><?php echo JText::_('REGISTRATION_DWZ'); ?>:</td>
			<td width="40%">
			<input class="inputbox" type="text" name="reg_dwz" id="reg_dwz" size="4" maxlength="4" value="<?php echo $reg_dwz; ?>" />
			</td>
			<td align="left" width="100" class="anfang"><?php echo JText::_('REGISTRATION_ELO'); ?>:</td>
			<td>
			<input class="inputbox" type="text" name="reg_elo" id="reg_elo" size="4" maxlength="4" value="<?php echo $reg_elo; ?>" />
			</td>
		</tr>
		<?php if ($optionEloAnalysis == 1) { ?>
		<tr>
			<td align="left" width="100" class="anfang"><?php echo JText::_('REGISTRATION_FIDEID'); ?>:</td>
			<td colspan="3">
			<input class="inputbox" type="text" name="reg_FIDEid" id="reg_FIDEid" size="8" maxlength="8" value="<?php echo $reg_FIDEid; ?>" />
			</td>
		</tr>
		<?php } ?>
		<tr>
			<td align="left" width="100" class="anfang"><?php echo JText::_('REGISTRATION_SEX'); ?>:</td>
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
		</table>
		<table>
		<tr>
			<td align="left" width="100" class="anfang"><?php echo JText::_('REGISTRATION_COMMENT'); ?>:</td>
			<td>
			<textarea class="inputbox" name="reg_comment" id="reg_comment" cols="47" rows="4" placeholder="<?php echo JText::_('REGISTRATION_PLACEHOLDER'); ?>"><?php echo $reg_comment; ?></textarea>
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
				<span style="font-size: 80%; font-weight: lighter;"><?php echo JText::_('REGISTRATION_SUBMITTING_2'); ?></span></th>
		</tr>
		
		</table>

		<br>
		<input type="submit" value=" <?php echo JText::_('CLUB_DATA_SEND_BUTTON') ?> ">

		<input type="hidden" name="layout" value="sent" />
		<input type="hidden" name="view" value="turnier_registration" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="turnier" value="<?php echo $this->turnier->id; ?>" />
		<input type="hidden" name="typeRegistration" value="<?php echo $typeRegistration; ?>" />
		<input type="hidden" name="optionEloAnalysis" value="<?php echo $optionEloAnalysis; ?>" />
		<input type="hidden" name="reg_name" value="<?php echo $reg_name; ?>" />
		<input type="hidden" name="reg_vorname" value="<?php echo $reg_vorname; ?>" />
		<input type="hidden" name="reg_jahr" value="<?php echo $reg_jahr; ?>" />
		<input type="hidden" name="reg_mail" value="<?php echo $reg_mail; ?>" />
		<input type="hidden" name="reg_tel_no" value="<?php echo $reg_tel_no; ?>" />
		<input type="hidden" name="reg_account" value="<?php echo $reg_account; ?>" />
		<input type="hidden" name="reg_dsgvo" value="<?php echo $reg_dsgvo; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_('form.token'); ?>
		
	  </form>
<?php

require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php');
echo '</div></div>';

?>


