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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

$mainframe	= Factory::getApplication();

// Include the AddressHandler class
require_once JPATH_COMPONENT_ADMINISTRATOR. '/helpers/addresshandler.php';

// Variablen holen
$sid = clm_core::$load->request_int('saison');
$zps = clm_core::$load->request_string('zps');
$tlnr = clm_core::$load->request_string('tlnr');
$liga = clm_core::$load->request_string('liga');
$config = clm_core::$db->config();

// Variablen initialisieren
$clmuser = $this->clmuser;
$mannschaft = $this->mannschaft;

$user =Factory::getUser();
$link = URI::base(true) .'index.php?option=com_clm&view=mannschaft&saison='. $sid . '&liga=' . $liga . '&tlnr=' . $tlnr;
// Login Status prüfen

if (!$user->get('id')) {
	$msg = Text::_( 'TEAM_DATA_SENT_LOGIN' );
	$mainframe->enqueueMessage( $msg );
	$mainframe->redirect( $link );
}
if ($clmuser[0]->published < 1) { 
	$msg = Text::_( 'TEAM_DATA_SENT_ACCOUNT' );
	$mainframe->enqueueMessage( $msg );
	$mainframe->redirect( $link );
}
if ($clmuser[0]->zps <> $zps  OR $clmuser[0]->usertype == "spl") {
	$msg = Text::_( 'TEAM_DATA_SENT_FALSE' );
	$mainframe->enqueueMessage( $msg );
	$mainframe->redirect( $link );
}
if ($user->get('id') > 0 AND  $clmuser[0]->published > 0 AND $clmuser[0]->zps == $zps OR $clmuser[0]->usertype == "admin") {
	// Prüfen ob Datensatz schon vorhanden ist
	$db = Factory::getDBO();

	// Datensätze in Meldelistentabelle schreiben

	// Variablen holen
	$lokal = clm_core::$load->request_string('lokal');
	$adresse = clm_core::$load->request_string('adresse');
	$newmf = clm_core::$load->request_int('newteammf');

	$query	= "UPDATE #__clm_mannschaften SET mf=$newmf, lokal='$lokal', lokal_coord=null, adresse='$adresse' WHERE sid=$sid AND liga=$liga AND tln_nr=$tlnr";
	clm_core::$db->query($query);
	
	// Log
	$aktion = "Mannschaftsdaten FE";
	$callid = uniqid ( "", false );
	$userid = clm_core::$access->getId ();	
	$parray = array('sid' => $sid, 'zps' => $zps, 'liga' => $liga, 'tln_nr' => $tlnr, 'mf' => $newmf);
	$query	= "INSERT INTO #__clm_logging ( `callid`, `userid`, `timestamp` , `type` ,`name`, `content`) "
		." VALUES ('".$callid."','".$userid."',".time().",5,'".$aktion."','".json_encode($parray)."') ";
	clm_core::$db->query($query);

	$msg = Text::_( 'TEAM_DATA_SENT_SAVED' );
	
	// Mails verschicken ?
	// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	// Zur Abwärtskompatibilität mit CLM <= 1.0.3 werden alte Daten aus Language-Datei als Default eingelesen
	$from = $config->email_from;
	$fromname = $config->email_fromname;
	$bcc = $config->email_bcc;
	$bcc_mail = $config->bcc;
	$sl_mail = $config->sl_mail;
	$verein_fe_mail	= $config->verein_fe_mail;

	if (!clm_core::$load->is_email($bcc)) $bcc = NULL;
	$send = 1;
	if (!clm_core::$load->is_email($from)) $send = 0;
	elseif ($fromname == '') $send = 0;
	if ($verein_fe_mail == 0) $send = 0;
	
	if ( $send == 1 ) {
	
		// Daten für Email sammeln
		// Melder
		$query	= "SELECT a.* FROM #__clm_user as a WHERE a.sid=" . $sid . " AND a.jid = ".$user->get('id');
		$db->setQuery($query);
		$melder = $db->loadObjectList();
		// Saison
		$query	= "SELECT a.* FROM #__clm_saison as a WHERE a.id=" . $sid;
		$db->setQuery($query);
		$saison = $db->loadObjectList();
		// Verein
		$query	= "SELECT a.* FROM #__clm_vereine as a WHERE a.sid=" . $sid ." AND a.zps = '$zps'";
		$db->setQuery($query);
		$verein = $db->loadObjectList();
		// Vereinsmitglieder mit Benutzeraccount
		$query	= "SELECT a.* FROM #__clm_user as a WHERE a.sid=" . $sid . " AND a.zps = '$zps'";
		$db->setQuery($query);
		$benutzer = $db->loadObjectList();
		// Staffelleiter über Mannschaften	
		$query	= "SELECT m.*, u.email as sl_email, u.name as sl_name FROM #__clm_mannschaften as m "
			." LEFT JOIN #__clm_liga as l ON l.id = m.liga "  
			." LEFT JOIN #__clm_user as u ON u.jid = l.sl AND u.sid = l.sid "  
			." WHERE m.sid =".$sid
			." AND m.zps = '$zps' "
			." AND m.published = 1 "
			." AND l.published = 1 ";
		$db->setQuery($query);
		$staffelleiter = $db->loadObjectList();
		//echo "<br>staffelleiter"; var_dump($staffelleiter);

		// Datum der Erstellung
		$date =Factory::getDate();
		$now = $date->toSQL();

		// Mailbody HTML Header
		$body_html_header = '
			<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
			<title>'.Text::_( 'TEAM_DATA_MAIL_HEADLINE' ).'</title>
			</head>
			<body>';
		$body_html_footer = '
			</body>
			</html>';	
		// Mailbody HTML Vereinsdatenpflege im FE
		$body_html = '
			<table width="700" border="0" cellspacing="0" cellpadding="3" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;">
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;" colspan="7">
				<div align="center" style="font-size: 12px;">
					<strong>'
			.Text::_( 'TEAM_DATA_MAIL_HEADLINE' ).' '.Text::_( 'OF_DAY' ).HTMLHelper::_('date', $now, Text::_('DATE_FORMAT_CLM_PDF')).
					'</strong>
				</div>
			</td>
		</tr>
		<tr>
			<td width="120">&nbsp;</td>
			<td>&nbsp;</td>
			<td width="5">&nbsp;</td>
			<td width="5">&nbsp;</td>
			<td width="80">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="120" style="border-bottom: solid 1px #999999;"><strong>'.Text::_( 'TEAM_LIST_MAIL_CLUB' ).'</strong></td>
			<td width="200" style="border-bottom: solid 1px #999999;">' .$verein[0]->name. '&nbsp;</td>
			<td width="40" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="40" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="80" style="border-bottom: solid 1px #999999;"><strong>'.Text::_( 'TEAM_LIST_MAIL_SEASON' ).'</strong></td>
			<td width="120" style="border-bottom: solid 1px #999999;">' .$saison[0]->name. '&nbsp;</td>
			<td>&nbsp;</td>
		</tr>';	
		$body_html .=	' 
		<tr>
			<td width="120">&nbsp;</td>
			<td>&nbsp;</td>
			<td width="5">&nbsp;</td>
			<td width="5">&nbsp;</td>
			<td width="80">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		</table>';

		$body_html .=	'
		<table width="700" border="0" cellspacing="0" cellpadding="3" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;">
		<tr>
			<td width="100" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>'.Text::_( 'TEAM_DATA_LOCATION' ).'</strong></div></td>
			<td width="400" nowrap="nowrap" valign="top" size="1" colspan="7">
				<textarea cols="30" rows="2" style="border: solid 1px #999999; width:90%">'.str_replace('&','&amp;',$verein[0]->lokal).'</textarea>
			</td>
		</tr>
		<tr>
			<td width="100" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>'.Text::_( 'TEAM_DATA_ADRESS' ).'</strong></div></td>
			<td width="400" nowrap="nowrap" valign="top" size="1" colspan="7">
				<textarea cols="30" rows="2" style="border: solid 1px #999999; width:90%">'.str_replace('&','&amp;',$verein[0]->adresse).'</textarea>
			</td>
		</tr>
		<tr>
			<td width="100" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>'.Text::_( 'TEAM_DATA_DATE' ).'</strong></div></td>
			<td width="400" nowrap="nowrap" valign="top" size="1" colspan="7">
				<textarea cols="30" rows="2" style="border: solid 1px #999999; width:90%">'.str_replace('&','&amp;',$verein[0]->termine).'</textarea>
			</td>
		</tr>
		<tr>
			<td width="100" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>'.Text::_( 'TEAM_DATA_HOMEPAGE' ).'</strong></div></td>
			<td width="400" nowrap="nowrap" valign="top" size="1" colspan="7">
				<textarea cols="30" rows="2" style="border: solid 1px #999999; width:90%">'.str_replace('&','&amp;',$verein[0]->homepage).'</textarea>
			</td>
		</tr>';
		$body_html .=	' 
		<tr>
			<td width="100">&nbsp;</td>
			<td width="400">&nbsp;</td>
		</tr>
		</table>';

		// Mailbody HTML Vereinsdatenpflege im FE
		$body_html .=	' 
		<tr>
			<td width="120">&nbsp;</td>
			<td>&nbsp;</td>
			<td width="5">&nbsp;</td>
			<td width="5">&nbsp;</td>
			<td width="80">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="120" style="border-bottom: solid 1px #999999;"><strong>'.Text::_( 'Melder :' ).'</strong></td>
			<td width="140" style="border-bottom: solid 1px #999999;">' .$melder[0]->name. '&nbsp;</td>
			<td width="20" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="160" style="border-bottom: solid 1px #999999;">' .''. '&nbsp;</td>
			<td width="20" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="85" style="border-bottom: solid 1px #999999;">' .''. '&nbsp;</td>
			<td>&nbsp;</td>
		</tr>	
		</table>';

		$subject = $fromname.': '.Text::_('TEAM_DATA_SUBJECT').' '.$verein[0]->name.'  -  '.Text::_( 'TEAM_LIST_MAIL_SEASON' ).'  '.$saison[0]->name;
		$countmail = 0;
		$body_name = Text::_('RESULT_NAME').$melder[0]->name.",";

		// Mail Melder
		if (isset($melder[0]->email) AND clm_core::$load->is_email($melder[0]->email)) {
			$recipient = $melder[0]->email;
			$body_html_md = '
			<table width="700" border="0" cellspacing="0" cellpadding="3" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px;">
				<tr>
		  			<td>'.Text::_('TEAM_DATA_MAIL_MD1').'</td>
  				</tr>
				<tr>
		  			<td>'.Text::_('TEAM_DATA_MAIL_MD2').'</td>
  				</tr>
				<tr>
		  			<td>'.Text::_('TEAM_DATA_MAIL_MD3').'</td>
				</tr>
			</table>';
		
			$body_name = Text::_('RESULT_NAME').$melder[0]->name.",";
			$body = $body_html_header.$body_name.$body_html_md.$body_html.$body_html_footer;
			$result = clm_core::$api->mail_send($recipient,$subject,$body,1,null,$bcc);
			if ($result[0] !== true) $msg .= '<br>'.Text::_('MAIL_ERROR').' '.$recipient;
			else $countmail++;
		}
	
		// Mail Staffelleiter
		$a_sl = array();
		foreach ($staffelleiter as $staffelleiter1) { 
	  		if ($sl_mail == 1 AND isset($staffelleiter1->sl_email) AND clm_core::$load->is_email($staffelleiter1->sl_email)) {
				$recipient = $staffelleiter1->sl_email;
				if (!in_array($recipient, $a_sl)) {
					$a_sl[] = $recipient;
					$body_html_sl = '
					<table width="700" border="0" cellspacing="0" cellpadding="3" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px;">
					<tr>
					<td>'.Text::_('TEAM_DATA_MAIL_SL1').'</td>
					</tr>
					<tr>
					<td>'.Text::_('TEAM_DATA_MAIL_SL2').'</td>
					</tr>
					<tr>
					<td>'.Text::_('TEAM_DATA_MAIL_SL3').'</td>
					</tr>
					</table>';
		
					$body_name = Text::_('RESULT_NAME').$staffelleiter1->sl_name.",";
					$body = $body_html_header.$body_name.$body_html_sl.$body_html.$body_html_footer;
					$result = clm_core::$api->mail_send($recipient,$subject,$body,1,null,$bcc);
					if ($result[0] !== true) $msg .= '<br>'.Text::_('MAIL_ERROR').' '.$recipient;
					else $countmail++;
				}
	  		}
		}
	//die('ende');
	}	
	$mainframe->enqueueMessage( $msg );
	$mainframe->redirect( $link );
}
?>
