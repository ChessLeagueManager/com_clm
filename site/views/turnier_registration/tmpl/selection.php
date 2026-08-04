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
 * Kommentare in Deutsch - Comments in English
*/
defined('_JEXEC') or die('Restricted access'); 

// Stylesheet laden - loas CSS
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;

$mainframe	= Factory::getApplication();

// Variablen initialisieren - install variables
$turnier 		= $this->turnier;

$user =Factory::getUser();
//	$link = URI::base(true) .'index.php?option=com_clm&view=turnier_registration&turnier='. $turnier->id .'&Itemid='; 

// Datensätze in Tabelle schreiben - Transfer data into db-table

// Variablen holen - get variables
$typeRegistration = clm_core::$load->request_string('typeRegistration','');
$typeAccount	= clm_core::$load->request_string('typeAccount','');
$optionEloAnalysis	= clm_core::$load->request_string('optionEloAnalysis', 0);
$reg_dsgvo 		= clm_core::$load->request_int('reg_dsgvo',0);
$reg_check01 	= clm_core::$load->request_string('reg_check01','');
$reg_name 		= clm_core::$load->request_string('reg_name','');
$reg_vorname 	= clm_core::$load->request_string('reg_vorname','');
$reg_jahr 		= clm_core::$load->request_string('reg_jahr','');
$reg_mail 		= clm_core::$load->request_string('reg_mail','');
$reg_tel_no 	= clm_core::$load->request_string('reg_tel_no','');
$reg_account 	= clm_core::$load->request_string('reg_account','');
$reg_club 		= clm_core::$load->request_string('reg_club','');
$reg_zps 		= clm_core::$load->request_string('reg_zps','');
$reg_mgl_nr 	= clm_core::$load->request_string('reg_mgl_nr','');
$reg_dwz 		= clm_core::$load->request_string('reg_dwz','');
$reg_elo 		= clm_core::$load->request_string('reg_elo','');
$reg_FIDEid 	= clm_core::$load->request_string('reg_FIDEid','');
$reg_geschlecht	= clm_core::$load->request_string('reg_geschlecht','');
$reg_comment 	= clm_core::$load->request_string('reg_comment','');
$f_source 		= clm_core::$load->request_string('f_source','');
if ($f_source = 'sent') {
	$reg_spieler 		= clm_core::$load->request_int('reg_spieler',100);
}
$session = Factory::getSession();
$reg_wert = $session->get('reg_wert');
$c_year = date("Y"); 
$today = date("Y-m-d");
// Konfigurationsparameter auslesen - get configuration parameter
$config = clm_core::$db->config();
$clm_key = $config->clmorg_data_key;
If (strlen($clm_key) > 30) $s_clm_key = 1; else $s_clm_key = 0;
$clm_domain = $config->request_domain;
$test_button = $config->test_button;

// Überprüfen der Eingaben - check input
$msg = '';
$success_clm = false;

if ($s_clm_key == 1) { // Spielersuche auf CLM-Server
	$success_clm = true;
	$spielerdatenurl = 'https://spielerdaten.chessleaguemanager.org/spieler.php';
	$clm_name = $reg_name.','.$reg_vorname;
	// Webservice
	try {
		$ch = curl_init($spielerdatenurl);
		$post_data = array('clm_key' => $clm_key, 'clm_name' => $clm_name, 'clm_domain' => $clm_domain);
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($httpCode !== 200) {
			$error = json_encode(['error php_spieler' => 'Token ist ung&uuml;ltig oder abgelaufen (httpCode=' . $httpCode . "/" . json_encode($response) . ")."]);
			clm_core::$api->test_print('httpCode-Error',$error);
			$success_clm = false;
		} else {
			// Mitglieder eines Vereins
			$playerlist = json_decode($response, true);
		}
	}
	catch (RuntimeException $e) {
		$error = json_encode(['runtime error test_php_verein' => "❌ Fehler: " . $e->getMessage()]);
		clm_core::$api->test_print('RuntimeException',$error);
		$success_clm = false;
	}
	if ($success_clm == true) {
		$names = array();
		if ($test_button > 0) {
			clm_core::$api->test_print('data',$playerlist['data']); }
		if (isset($playerlist['data'])) {				// notwendig, wenn Verein keine Mitglieder hat
			foreach ($playerlist['data'] as $player) {
				if (isset($player['memberships'][0])) {
					$membership = $player['memberships'][0];
					if (isset($membership['leavingdate']) AND $membership['leavingdate'] > '1970-01-01' AND $membership['leavingdate'] < $today) 
						continue;
				}
				if ( $player['birthYear'] == $reg_jahr ) {
					$names[] = $player;
				}	
			}
		} else {
			$names = NULL;
		}
	}
}
if ($success_clm == false) {					// Spielersuche auf DSB-Server	
	$result = clm_core::$api->db_dewis_player_by_name($reg_name, $reg_vorname, $reg_jahr); 
	if (!isset($result[3])) $names = NULL; $names = $result[3];
}
if (is_null($names)) $ii = 0;
else $ii = count($names);
if (isset($names) AND ($test_button > 0)) {				// notwendig, wenn Verein keine Mitglieder hat
	foreach ($names as $player) {
		if ($success_clm == false) echo "<br><br>DSB:"; else echo "<br><br>CLM:"; 
		var_dump($player);
	}
}

echo "<div><div id='turnier_info'>";
// componentheading vorbereiten - prepare componentheading
$heading = $this->turnier->name;
	echo CLMContent::componentheading($heading);
		// Captcha vorbereiten - prepare captcha
		$sresult = clm_core::$load->session_variables('o'); 
		$session = Factory::getSession();
		$reg_wert = $session->get('reg_wert');
?>
	<br />
		<table>
			<tr><th class="anfang">
				<td class="anfang" style="font-size: 120%; font-weight: bolder;"><?php echo Text::_('REGISTRATION_CLUB_ELO'); ?></td>
			</tr>
		</table>
	<?php echo Text::_('REGISTRATION_LIST_OF_PLAYERS'); ?>
	<br>	

	<script language="javascript" type="text/javascript">

		 Joomla.submitbutton = function (pressbutton) { 		
			var form = document.adminForm;
			// do field validation
			if (form.reg_spieler.value == 99) {
				if (form.reg_club.value == "") {
					alert( "<?php echo Text::_( 'REGISTRATION_E_CLUB', true ); ?>" ); return false;
				} else if ((form.reg_mgl_nr.value != "") && (isNaN(form.reg_mgl_nr.value))) {
					alert( "<?php echo 'Mitgliedsnummer ist keine Zahl'; ?>" ); return false;
				} else if ((form.reg_dwz.value != "") && (isNaN(form.reg_dwz.value))) {
					alert( "<?php echo Text::_( 'REGISTRATION_E_NWZ', true ); ?>" ); return false;
				} else if ((form.reg_elo.value != "") && (isNaN(form.reg_elo.value))) {
					alert( "<?php echo Text::_( 'REGISTRATION_E_ELO', true ); ?>" ); return false;
				} else if (<?php echo $optionEloAnalysis; ?> == '1') {
					if (form.reg_FIDEid.value == "") {
						alert( "<?php echo 'Das Turnier wird ELO-ausgewertet. Bitte tragen Sie Ihre FIDE-ID ein. Haben Sie noch keine ID, tragen Sie bitte 0 ein und kontaktieren Sie den Turnierleiter, z.B. über das Info-Feld dieses Formulars'; ?>" ); return false;
					} else if ((form.reg_FIDEid.value != "") && (isNaN(form.reg_FIDEid.value))) {
						alert( "<?php echo Text::_( 'REGISTRATION_E_FIDEID', true ); ?>" ); return false;
					} else if ((form.reg_FIDEid.value != "") && (form.reg_FIDEid.value != 0) && (form.reg_FIDEid.value < 10000)) {
						alert( "<?php echo Text::_( 'REGISTRATION_E_FIDEID', true ); ?>" ); return false;
					}
				}
			}
			if (form.reg_check01.value == "") {
				alert( "<?php echo Text::_( 'REGISTRATION_E_SPAM', true ); ?>" ); return false;
			} else if (form.reg_wert.value != form.reg_check01.value) {
				alert( "<?php echo Text::_( 'REGISTRATION_E_SPAMK', true ); ?>" ); return false;
			} else {
				Joomla.submitform( pressbutton ); 
			}
		}
 
		</script>

<!--		<form action="index.php?option=com_clm&amp;view=turnier_registration&amp;layout=sent" method="post" name="adminForm" id="adminForm">
-->	  <form action="<?php echo Route::_('index.php'); ?>" method="post" name="adminForm" id="adminForm">
		<table>
			<tr><th class="anfang">
				<td class="anfang"><?php echo Text::_('REGISTRATION_PLAYER'); ?>,<?php echo Text::_('REGISTRATION_VORNAME'); ?></td>
				<td class="anfang"><?php echo Text::_('REGISTRATION_JAHR'); ?></td>
				<td class="anfang"><?php echo Text::_('REGISTRATION_DWZ'); ?></td>
				<?php if ($success_clm == true) { ?>
					<td class="anfang"><?php echo Text::_('REGISTRATION_ELO'); ?></td>
				<?php } ?>
				<td class="anfang"><?php echo Text::_('REGISTRATION_CLUB'); ?></td>
			</th></tr>
			<?php 
			for ($i = 0; $i < $ii; $i++) { 
				$jj = 0;
				foreach ($names[$i]["memberships"] as $membership1) {
					if (!isset($membership1['clubName'])) $membership1['clubName'] = 'xyz-'.$membership1['vkz'];
					if ($jj == 0) $member = $membership1;
					$jj++;
					if (isset($membership1["licenceState"]) AND ($membership1["licenceState"] == 'ACTIVE')) {
						$member = $membership1;
						break;
					}
				}
				if (!isset($names[$i]['fideId'])) $names[$i]['fideId'] = $names[$i]['fide_id'];
				if (!isset($names[$i]['birthyear'])) $names[$i]['birthyear'] = $names[$i]['birthYear'];
				if ($names[$i]['gender'] == 'MALE') $names[$i]['gender'] = 'M';
				elseif ($names[$i]['gender'] == 'FEMALE') $names[$i]['gender'] = 'W'; 
				else $names[$i]['gender'] = ''; ?>
				<tr><td style="text-align: center;"><input type="radio" id="<?php echo 'spieler'.($i); ?>" name="reg_spieler" value="<?php echo ($i); ?>"<?php if ($reg_spieler == $i) echo ' checked="checked"'; ?>></td>
				<td><?php echo $names[$i]['lastname'].','.$names[$i]['firstname']; ?></td>
				<td><?php echo $names[$i]['birthyear']; ?></td>
				<td><?php if (isset($names[$i]['rating'])) echo $names[$i]['rating']; else $names[$i]['rating'] = 0; ?></td>
				<?php if ($success_clm == true) { ?>
					<td><?php if (isset($names[$i]['fide_rating'])) echo $names[$i]['fide_rating']; ?></td>
				<?php } ?>
				<td><?php echo $member['clubName']; ?></td>
				<input type="hidden" name="<?php echo 'reg_name'.($i); ?>" value="<?php echo $names[$i]['lastname']; ?>" />
				<input type="hidden" name="<?php echo 'reg_vorname'.($i); ?>" value="<?php echo $names[$i]['firstname']; ?>" />
				<input type="hidden" name="<?php echo 'reg_club'.($i); ?>" value="<?php echo $member['clubName']; ?>" />
				<input type="hidden" name="<?php echo 'reg_dwz'.($i); ?>" value="<?php echo $names[$i]['rating']; ?>" />
				<input type="hidden" name="<?php echo 'reg_PKZ'.($i); ?>" value="<?php echo $names[$i]['nuLigaPersonId']; ?>" />
				<input type="hidden" name="<?php echo 'reg_geschlecht'.($i); ?>" value="<?php echo $names[$i]['gender']; ?>" />
				<input type="hidden" name="<?php echo 'reg_birthYear'.($i); ?>" value="<?php echo $names[$i]['birthyear']; ?>" />
				<input type="hidden" name="<?php echo 'reg_mgl_nr'.($i); ?>" value="<?php echo $member['memberNo']; ?>" />
				<input type="hidden" name="<?php echo 'reg_zps'.($i); ?>" value="<?php echo $member['vkz']; ?>" />
				<input type="hidden" name="<?php echo 'reg_dwz_I0'.($i); ?>" value="<?php echo $names[$i]['index']; ?>" />
				<input type="hidden" name="<?php echo 'reg_FIDEid'.($i); ?>" value="<?php echo $names[$i]['fideId']; ?>" />
				<input type="hidden" name="<?php echo 'reg_elo'.($i); ?>" value="<?php if (isset($names[$i]['fide_rating'])) echo $names[$i]['fide_rating']; else echo '0'; ?>" />
				<input type="hidden" name="<?php echo 'reg_FIDEcco'.($i); ?>" value="<?php if (isset($names[$i]['fide_federation'])) echo $names[$i]['fide_federation']; else ''; ?>" />
				<input type="hidden" name="<?php echo 'reg_titel'.($i); ?>" value="<?php if (isset($names[$i]['fide_title'])) echo $names[$i]['fide_title']; else ''; ?>" />
				</tr>
			<?php } 
				if ($ii == 0) { ?>
				<tr><td colspan=6>Es wurde kein passender Spieler in den DSB-Daten gefunden</td><tr>
			<?php } 				?>
		</table>
	<?php echo "<br>".Text::_('REGISTRATION_EDIT_DATA'); ?><br>
		<span style="font-size: 80%; font-weight: lighter;"><?php echo Text::_('REGISTRATION_MANDATORY'); ?> </span><br>
		<span style="font-size: 80%; font-weight: lighter;"><?php echo Text::_('REGISTRATION_DWZ_EVALUATION'); ?> </span><br>
		<table>
			<tr><th class="anfang">
				<td class="anfang"><?php echo Text::_('REGISTRATION_PLAYER'); ?>,<?php echo Text::_('REGISTRATION_VORNAME'); ?></td>
				<td class="anfang"><?php echo Text::_('REGISTRATION_JAHR'); ?></td>
			</th></tr>
			<tr><td style="text-align: center;"><input type="radio" id="spieler99" name="reg_spieler" value="99"<?php if ($reg_spieler == 99) echo ' checked="checked"'; ?>></td>
			<td><?php echo $reg_name.','.$reg_vorname; ?></td>
			<td><?php echo $reg_jahr; ?></td>
			</tr>
		</table>
		<table>
		<tr>
			<td align="left" width="100" class="anfang"><?php echo Text::_('REGISTRATION_CLUB'); ?>(*):</td>
			<td colspan="3">
			<input class="inputbox" type="text" name="reg_club" id="reg_club" size="50" maxlength="100" value="<?php echo $reg_club; ?>" />
			</td>
		</tr>
		<tr>
			<td align="left" width="100" class="anfang"><?php echo Text::_('REGISTRATION_ZPS'); ?>(**):</td>
			<td width="40%">
			<input class="inputbox" type="text" name="reg_zps" id="reg_zps" size="5" maxlength="5" value="<?php echo $reg_zps; ?>" />
			</td>
			<td align="left" width="100" class="anfang"><?php echo Text::_('REGISTRATION_MGLNR'); ?>(**):</td>
			<td>
			<input class="inputbox" type="text" name="reg_mgl_nr" id="reg_mgl_nr" size="4" maxlength="4" value="<?php echo $reg_mgl_nr; ?>" />
			</td>
		</tr>
		<tr>
			<td align="left" width="100" class="anfang"><?php echo Text::_('REGISTRATION_DWZ'); ?>:</td>
			<td width="40%">
			<input class="inputbox" type="text" name="reg_dwz" id="reg_dwz" size="4" maxlength="4" value="<?php echo $reg_dwz; ?>" />
			</td>
			<td align="left" width="100" class="anfang"><?php echo Text::_('REGISTRATION_ELO'); ?>:</td>
			<td>
			<input class="inputbox" type="text" name="reg_elo" id="reg_elo" size="4" maxlength="4" value="<?php echo $reg_elo; ?>" />
			</td>
		</tr>
		<?php if ($optionEloAnalysis == 1) { ?>
		<tr>
			<td align="left" width="100" class="anfang"><?php echo Text::_('REGISTRATION_FIDEID'); ?>(*):</td>
			<td colspan="3">
			<input class="inputbox" type="text" name="reg_FIDEid" id="reg_FIDEid" size="9" maxlength="9" value="<?php echo $reg_FIDEid; ?>" />
			</td>
		</tr>
		<?php } ?>
		<tr>
			<td align="left" width="100" class="anfang"><?php echo Text::_('REGISTRATION_SEX'); ?>:</td>
			<td class="paramlist_value" colspan="3">
					<?php
					$options = array();
					$options[''] = '';
					$options['M'] = Text::_('REGISTRATION_SEX_M');
					$options['W'] = Text::_('REGISTRATION_SEX_W');
					$optionlist = array();
					foreach ($options as $key => $val) {
						$optionlist[]	= HTMLHelper::_('select.option', $key, $val, 'id', 'name' );
					}
					echo HTMLHelper::_('select.genericlist', $optionlist, 'reg_geschlecht', 'class="inputbox"', 'id', 'name', $reg_geschlecht);
					?>
				</td>
		</tr>
		</table>
		<br>
		<table>
		<tr>
			<td align="left" width="100" class="anfang"><?php echo Text::_('REGISTRATION_COMMENT'); ?>:</td>
			<td>
			<textarea class="inputbox" name="reg_comment" id="reg_comment" cols="47" rows="4" placeholder="<?php echo Text::_('REGISTRATION_PLACEHOLDER'); ?>"><?php echo $reg_comment; ?></textarea>
			</td>
		</tr>
		<tr>
			<td align="left" width="100" class="anfang"><?php echo Text::_('REGISTRATION_CHECK'); ?>(*):</td>
			<td><?php echo $sresult[1]." + ".$sresult[2]." = "; ?>	 
				<input class="inputbox" type="text" name="reg_check01" id="reg_check01" size="8" maxlength="10" value="" />
			</td>
		</tr>
		<tr>
			<th align="left" colspan="2" class="anfang">
				<span style="font-size: 80%; font-weight: lighter;"><?php echo Text::_('REGISTRATION_SUBMITTING_2'); ?></span></th>
		</tr>
		
		</table>

		<br>

			<button class="button" onclick="return Joomla.submitbutton();">
				<?php echo Text::_('CLUB_DATA_SEND_BUTTON') ?>
			</button>

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
		<input type="hidden" name="reg_wert" value="<?php echo $reg_wert; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo HTMLHelper::_( 'form.token' ); ?>
		
	  </form>
<?php

require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); 
echo '</div></div>';
									
?>


