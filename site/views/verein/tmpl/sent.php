<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

$mainframe	= JFactory::getApplication();

// Include the AddressHandler class
require_once JPATH_COMPONENT_ADMINISTRATOR. '/helpers/addresshandler.php';

// Variablen holen
$sid 		= clm_core::$load->request_int('saison');
$zps 		= clm_core::$load->request_string('zps');
$name 		= clm_core::$load->request_string('name');
$new 		= clm_core::$load->request_string('new');
$config = clm_core::$db->config();

// Variablen initialisieren
$clmuser 	= $this->clmuser;
$row 		= $this->row;

$user = JFactory::getUser();
$link = JURI::base() .'index.php?option=com_clm&view=verein&saison='. $sid .'&zps='. $zps;
// Login Status prüfen
if (!$user->get('id')) {
    $msg = JText::_('CLUB_DATA_SENT_LOGIN');
    $mainframe->enqueueMessage($msg);
    $mainframe->redirect($link);
}
if ($clmuser[0]->published < 1) {
    $msg = JText::_('CLUB_DATA_SENT_ACCOUNT');
    $mainframe->enqueueMessage($msg);
    $mainframe->redirect($link);
}
if ($clmuser[0]->zps <> $zps  or $clmuser[0]->usertype == "spl") {
    $msg = JText::_('CLUB_DATA_SENT_FALSE');
    $mainframe->enqueueMessage($msg);
    $mainframe->redirect($link);
}
// Login Status prüfen
if ($user->get('id') > 0 and  $clmuser[0]->published > 0 and $clmuser[0]->zps == $zps or $clmuser[0]->usertype == "admin") {
    // Prüfen ob Datensatz schon vorhanden ist
    $db	= JFactory::getDBO();

    // Datensätze in Meldelistentabelle schreiben

    // Variablen holen
    $lokal 		= clm_core::$load->request_string('lokal');
    $lokal_coord 		= clm_core::$load->request_string('lokal_coord');
    $homepage 	= clm_core::$load->request_string('homepage');
    $adresse 	= clm_core::$load->request_string('adresse');
    $termine 	= clm_core::$load->request_string('termine');
    $vs 		= clm_core::$load->request_string('vs');
    $vs_mail	= clm_core::$load->request_string('vs_mail');
    $vs_tel		= clm_core::$load->request_string('vs_tel');
    $tl 		= clm_core::$load->request_string('tl');
    $tl_mail	= clm_core::$load->request_string('tl_mail');
    $tl_tel		= clm_core::$load->request_string('tl_tel');
    $jw 		= clm_core::$load->request_string('jw');
    $jw_mail	= clm_core::$load->request_string('jw_mail');
    $jw_tel		= clm_core::$load->request_string('jw_tel');
    $pw 		= clm_core::$load->request_string('pw');
    $pw_mail	= clm_core::$load->request_string('pw_mail');
    $pw_tel		= clm_core::$load->request_string('pw_tel');
    $kw 		= clm_core::$load->request_string('kw');
    $kw_mail	= clm_core::$load->request_string('kw_mail');
    $kw_tel		= clm_core::$load->request_string('kw_tel');
    $sw 		= clm_core::$load->request_string('sw');
    $sw_mail	= clm_core::$load->request_string('sw_mail');
    $sw_tel		= clm_core::$load->request_string('sw_tel');

    // Vereinsdaten exisitieren
    if ($new < 1) {
        // Create instance of AddressHandler
        $addressHandler = new AddressHandler();
        $lokal_coord = $addressHandler->convertAddress($lokal);
        if (is_null($lokal_coord) || $lokal_coord == -1) {
            $geo_query = " , lokal_coord = null";
            $lokal_coord = null;
            if ($config->googlemaps) {//Only output a message if geo service is enabled
                $mainframe->enqueueMessage(JText::_('CLUB_DATA_GEO_WARNING'), 'warning');
            }
        } else {
            $geo_query = " , lokal_coord = '$lokal_coord'";
        }
        $query	= "UPDATE #__clm_vereine"
            ." SET lokal = '$lokal' "
            ." $geo_query "
            ." , homepage = '$homepage' "
            ." , adresse = '$adresse' "
            ." , termine = '$termine' "
            ." , vs = '$vs' "
            ." , vs_mail = '$vs_mail' "
            ." , vs_tel = '$vs_tel' "
            ." , tl = '$tl' "
            ." , tl_mail = '$tl_mail' "
            ." , tl_tel = '$tl_tel' "
            ." , jw = '$jw' "
            ." , jw_mail = '$jw_mail' "
            ." , jw_tel = '$jw_tel' "
            ." , pw = '$pw' "
            ." , pw_mail = '$pw_mail' "
            ." , pw_tel = '$pw_tel' "
            ." , kw = '$kw' "
            ." , kw_mail = '$kw_mail' "
            ." , kw_tel = '$kw_tel' "
            ." , sw = '$sw' "
            ." , sw_mail = '$sw_mail' "
            ." , sw_tel = '$sw_tel' "
            ." WHERE zps = '$zps' "
        ;
        //$db->setQuery($query);
        clm_core::$db->query($query);
    }
    // Vereinsdaten exisitieren NICHT
    else {
        $query	= "INSERT INTO #__clm_vereine "
            ." ( `name`, `sid`, `zps`, `vl`, `lokal`, `lokal_coord`, `homepage`, `adresse`, "
            ." `vs`, `vs_mail`, `vs_tel`, `tl`, `tl_mail`, `tl_tel`, "
            ." `jw`, `jw_mail`, `jw_tel`, `pw`, `pw_mail`, `pw_tel`, "
            ." `kw`, `kw_mail`, `kw_tel`, `sw`, `sw_mail`, `sw_tel`, `termine`,`published` ) "
            ." VALUES ('$name','$sid','$zps','0','$lokal', '$lokal_coord', '$homepage','$adresse', "
            ." '$vs','$vs_mail','$vs_tel','$tl','$tl_mail','$tl_tel', "
            ." '$jw','$jw_mail','$jw_tel','$pw','$pw_mail','$pw_tel', "
            ." '$kw','$kw_mail','$kw_tel','$sw','$sw_mail','$sw_tel', '$termine', '1') "
        ;
        //$db->setQuery($query);
        clm_core::$db->query($query);
    }
    // Log
    $aktion = "Vereinsdaten FE";
    $callid = uniqid("", false);
    $userid = clm_core::$access->getId();
    $parray = array('sid' => $sid, 'zps' => $zps);
    $query	= "INSERT INTO #__clm_logging "
        ." ( `callid`, `userid`, `timestamp` , `type` ,`name`, `content`) "
        ." VALUES ('".$callid."','".$userid."',".time().",5,'".$aktion."','".json_encode($parray)."') "
    ;
    //$db->setQuery($query);
    clm_core::$db->query($query);

    $msg = JText::_('CLUB_DATA_SENT_SAVED');
    //	$mainframe->enqueueMessage( $msg );

    // Mails verschicken ?
    // Konfigurationsparameter auslesen
    $config = clm_core::$db->config();
    // Zur Abwärtskompatibilität mit CLM <= 1.0.3 werden alte Daten aus Language-Datei als Default eingelesen
    $from = $config->email_from;
    $fromname = $config->email_fromname;
    $bcc	= $config->email_bcc;
    $bcc_mail	= $config->bcc;
    $sl_mail	= $config->sl_mail;
    $verein_fe_mail	= $config->verein_fe_mail;

    if (!clm_core::$load->is_email($bcc)) {
        $bcc = null;
    }
    $send = 1;
    if (!clm_core::$load->is_email($from)) {
        $send = 0;
    } elseif ($fromname == '') {
        $send = 0;
    }
    if ($verein_fe_mail == 0) {
        $send = 0;
    }

    if ($send == 1) {

        // Daten für Email sammeln
        // Melder
        $query	= "SELECT a.* FROM #__clm_user as a "
            ." WHERE a.sid =".$sid
            ."   AND a.jid =".$user->get('id')
        ;
        $db->setQuery($query);
        $melder = $db->loadObjectList();
        // Saison
        $query	= "SELECT a.* FROM #__clm_saison as a "
            ." WHERE a.id =".$sid
        ;
        $db->setQuery($query);
        $saison = $db->loadObjectList();
        // Verein
        $query	= "SELECT a.* FROM #__clm_vereine as a "
            ." WHERE a.sid =".$sid
            ."   AND a.zps = '$zps' "
        ;
        $db->setQuery($query);
        $verein = $db->loadObjectList();

        // Vereinsmitglieder mit Benutzeraccount
        $query	= "SELECT a.* FROM #__clm_user as a "
            ." WHERE a.sid =".$sid
            ."   AND a.zps = '$zps' "
        ;
        $db->setQuery($query);
        $benutzer = $db->loadObjectList();
        // Staffelleiter über Mannschaften
        $query	= "SELECT m.*, u.email as sl_email, u.name as sl_name FROM #__clm_mannschaften as m "
            ." LEFT JOIN #__clm_liga as l ON l.id = m.liga "
            ." LEFT JOIN #__clm_user as u ON u.jid = l.sl AND u.sid = l.sid "
            ." WHERE m.sid =".$sid
            ."   AND m.zps = '$zps' "
            ."   AND m.published = 1 "
            ."   AND l.published = 1 "
        ;
        $db->setQuery($query);
        $staffelleiter = $db->loadObjectList();
        //echo "<br>staffelleiter"; var_dump($staffelleiter);

        // Datum der Erstellung
        $date = JFactory::getDate();
        $now = $date->toSQL();

        // Mailbody HTML Header
        $body_html_header = '
			<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
			<title>'.JText::_('CLUB_DATA_MAIL_HEADLINE').'</title>
			</head>
			<body>';
        $body_html_footer = '
			</body>
			</html>';
        // Mailbody HTML Vereinsdatenpflege im FE
        $body_html =	'
		<table width="700" border="0" cellspacing="0" cellpadding="3" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;">
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;" colspan="7"><div align="center" style="font-size: 12px;"><strong>'.JText::_('CLUB_DATA_MAIL_HEADLINE').' '.JText::_('OF_DAY').JHTML::_('date', $now, JText::_('DATE_FORMAT_CLM_PDF')). '</strong></div></td>
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
			<td width="120" style="border-bottom: solid 1px #999999;"><strong>'.JText::_('CLUB_LIST_MAIL_CLUB').'</strong></td>
			<td width="200" style="border-bottom: solid 1px #999999;">' .$verein[0]->name. '&nbsp;</td>
			<td width="40" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="40" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="80" style="border-bottom: solid 1px #999999;"><strong>'.JText::_('CLUB_LIST_MAIL_SEASON').'</strong></td>
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
			<td width="100" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>'.JText::_('CLUB_DATA_LOCATION').'</strong></div></td>
			<td width="400" nowrap="nowrap" valign="top" size="1" colspan="7">
				<textarea cols="30" rows="2" style="border: solid 1px #999999; width:90%">'.str_replace('&', '&amp;', $verein[0]->lokal).'</textarea>
			</td>
		</tr>
		<tr>
			<td width="100" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>'.JText::_('CLUB_DATA_ADRESS').'</strong></div></td>
			<td width="400" nowrap="nowrap" valign="top" size="1" colspan="7">
				<textarea cols="30" rows="2" style="border: solid 1px #999999; width:90%">'.str_replace('&', '&amp;', $verein[0]->adresse).'</textarea>
			</td>
		</tr>
		<tr>
			<td width="100" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>'.JText::_('CLUB_DATA_DATE').'</strong></div></td>
			<td width="400" nowrap="nowrap" valign="top" size="1" colspan="7">
				<textarea cols="30" rows="2" style="border: solid 1px #999999; width:90%">'.str_replace('&', '&amp;', $verein[0]->termine).'</textarea>
			</td>
		</tr>
		<tr>
			<td width="100" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>'.JText::_('CLUB_DATA_HOMEPAGE').'</strong></div></td>
			<td width="400" nowrap="nowrap" valign="top" size="1" colspan="7">
				<textarea cols="30" rows="2" style="border: solid 1px #999999; width:90%">'.str_replace('&', '&amp;', $verein[0]->homepage).'</textarea>
			</td>
		</tr>
	';
        $body_html .=	' 
		<tr>
			<td width="100">&nbsp;</td>
			<td width="400">&nbsp;</td>
		</tr>
		</table>';

        // Mailbody HTML Vereinsdatenpflege im FE
        $body_html .=	'
		<table width="700" border="0" cellspacing="0" cellpadding="3" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;">
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="120" style="border-bottom: solid 1px #999999;"><strong>'.JText::_('CLUB_DATA_CHIEF').'</strong></td>
			<td width="140" style="border-bottom: solid 1px #999999;">' .$verein[0]->vs. '&nbsp;</td>
			<td width="20" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="160" style="border-bottom: solid 1px #999999;">' .$verein[0]->vs_mail. '&nbsp;</td>
			<td width="20" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="85" style="border-bottom: solid 1px #999999;">' .$verein[0]->vs_tel. '&nbsp;</td>
			<td>&nbsp;</td>
		</tr>	
		<tr>
			<td width="120" style="border-bottom: solid 1px #999999;"><strong>'.JText::_('CLUB_DATA_TOURNAMENT').'</strong></td>
			<td width="140" style="border-bottom: solid 1px #999999;">' .$verein[0]->tl. '&nbsp;</td>
			<td width="20" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="160" style="border-bottom: solid 1px #999999;">' .$verein[0]->tl_mail. '&nbsp;</td>
			<td width="20" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="85" style="border-bottom: solid 1px #999999;">' .$verein[0]->tl_tel. '&nbsp;</td>
			<td>&nbsp;</td>
		</tr>	
		<tr>
			<td width="120" style="border-bottom: solid 1px #999999;"><strong>'.JText::_('CLUB_DATA_YOUTH').'</strong></td>
			<td width="140" style="border-bottom: solid 1px #999999;">' .$verein[0]->jw. '&nbsp;</td>
			<td width="20" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="160" style="border-bottom: solid 1px #999999;">' .$verein[0]->jw_mail. '&nbsp;</td>
			<td width="20" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="85" style="border-bottom: solid 1px #999999;">' .$verein[0]->jw_tel. '&nbsp;</td>
			<td>&nbsp;</td>
		</tr>	
		<tr>
			<td width="120" style="border-bottom: solid 1px #999999;"><strong>'.JText::_('CLUB_DATA_PRESS').'</strong></td>
			<td width="140" style="border-bottom: solid 1px #999999;">' .$verein[0]->pw. '&nbsp;</td>
			<td width="20" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="160" style="border-bottom: solid 1px #999999;">' .$verein[0]->pw_mail. '&nbsp;</td>
			<td width="20" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="85" style="border-bottom: solid 1px #999999;">' .$verein[0]->pw_tel. '&nbsp;</td>
			<td>&nbsp;</td>
		</tr>	
		<tr>
			<td width="120" style="border-bottom: solid 1px #999999;"><strong>'.JText::_('CLUB_DATA_MONEY').'</strong></td>
			<td width="140" style="border-bottom: solid 1px #999999;">' .$verein[0]->kw. '&nbsp;</td>
			<td width="20" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="160" style="border-bottom: solid 1px #999999;">' .$verein[0]->kw_mail. '&nbsp;</td>
			<td width="20" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="85" style="border-bottom: solid 1px #999999;">' .$verein[0]->kw_tel. '&nbsp;</td>
			<td>&nbsp;</td>
		</tr>	
		<tr>
			<td width="120" style="border-bottom: solid 1px #999999;"><strong>'.JText::_('CLUB_DATA_SENIOR').'</strong></td>
			<td width="140" style="border-bottom: solid 1px #999999;">' .$verein[0]->sw. '&nbsp;</td>
			<td width="20" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="160" style="border-bottom: solid 1px #999999;">' .$verein[0]->sw_mail. '&nbsp;</td>
			<td width="20" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="85" style="border-bottom: solid 1px #999999;">' .$verein[0]->sw_tel. '&nbsp;</td>
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
		<tr>
			<td width="120" style="border-bottom: solid 1px #999999;"><strong>'.JText::_('Melder :').'</strong></td>
			<td width="140" style="border-bottom: solid 1px #999999;">' .$melder[0]->name. '&nbsp;</td>
			<td width="20" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="160" style="border-bottom: solid 1px #999999;">' .''. '&nbsp;</td>
			<td width="20" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="85" style="border-bottom: solid 1px #999999;">' .''. '&nbsp;</td>
			<td>&nbsp;</td>
		</tr>	
		</table>';

        $subject = $fromname.': '.JTEXT::_('CLUB_DATA_SUBJECT').' '.$verein[0]->name.'  -  '.JText::_('CLUB_LIST_MAIL_SEASON').'  '.$saison[0]->name;
        $countmail = 0;
        $body_name = JText::_('RESULT_NAME').$melder[0]->name.",";

        // Mail Melder
        if (isset($melder[0]->email) and clm_core::$load->is_email($melder[0]->email)) {
            $recipient = $melder[0]->email;
            $body_html_md = '
		<table width="700" border="0" cellspacing="0" cellpadding="3" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px;">
		<tr>
		  <td>'.JText::_('CLUB_DATA_MAIL_MD1').'</td>
  		</tr>
		<tr>
		  <td>'.JText::_('CLUB_DATA_MAIL_MD2').'</td>
  		</tr>
		<tr>
		  <td>'.JText::_('CLUB_DATA_MAIL_MD3').'</td>
		</tr>
		</table>';

            $body_name = JText::_('RESULT_NAME').$melder[0]->name.",";
            $body = $body_html_header.$body_name.$body_html_md.$body_html.$body_html_footer;
            $result = clm_core::$api->mail_send($recipient, $subject, $body, 1, null, $bcc);
            if ($result[0] !== true) {
                $msg .= '<br>'.JText::_('MAIL_ERROR').' '.$recipient;
            } else {
                $countmail++;
            }
        }

        // Mail Staffelleiter
        $a_sl = array();
        foreach ($staffelleiter as $staffelleiter1) {
            if ($sl_mail == 1 and isset($staffelleiter1->sl_email) and clm_core::$load->is_email($staffelleiter1->sl_email)) {
                $recipient = $staffelleiter1->sl_email;
                if (!in_array($recipient, $a_sl)) {
                    $a_sl[] = $recipient;
                    $body_html_sl = '
			<table width="700" border="0" cellspacing="0" cellpadding="3" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px;">
			<tr>
			<td>'.JText::_('CLUB_DATA_MAIL_SL1').'</td>
			</tr>
			<tr>
			<td>'.JText::_('CLUB_DATA_MAIL_SL2').'</td>
			</tr>
			<tr>
			<td>'.JText::_('CLUB_DATA_MAIL_SL3').'</td>
			</tr>
			</table>';

                    $body_name = JText::_('RESULT_NAME').$staffelleiter1->sl_name.",";
                    $body = $body_html_header.$body_name.$body_html_sl.$body_html.$body_html_footer;
                    $result = clm_core::$api->mail_send($recipient, $subject, $body, 1, null, $bcc);
                    if ($result[0] !== true) {
                        $msg .= '<br>'.JText::_('MAIL_ERROR').' '.$recipient;
                    } else {
                        $countmail++;
                    }
                }
            }
        }


        //die('ende');
    }
    $mainframe->enqueueMessage($msg);
    $mainframe->redirect($link);
}
?>


