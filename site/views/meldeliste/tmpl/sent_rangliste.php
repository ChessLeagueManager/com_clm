<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('clm') or die('Restricted access');

$mainframe	= JFactory::getApplication();
// Variablen holen
$sid 	= clm_core::$load->request_int('saison', '1');
$lid 	= clm_core::$load->request_int('liga');
$zps 	= clm_core::$load->request_string('zps');
$gid 	= clm_core::$load->request_int('gid');
$count 	= clm_core::$load->request_int('count');
$published 	= clm_core::$load->request_int('published');
$ordering 	= clm_core::$load->request_int('ordering');
$bemerkungen 	= clm_core::$load->request_string('bemerkungen');
$bem_int 	= clm_core::$load->request_string('bem_int');

$option = clm_core::$load->request_string('option');
$db	= JFactory::getDBO();

$user 		= JFactory::getUser();
$meldung 	= $user->get('id');
$clmuser        = $this->clmuser;
$link = 'index.php?option=com_clm&view=info';

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

if (($clmuser[0]->zps <> $zps)  || ($clmuser[0]->usertype == "spl")) {
    $msg = JText::_('CLUB_DATA_SENT_FALSE');
    $mainframe->enqueueMessage($msg);
    $mainframe->redirect($link);
}
// Login Status prüfen
if ($user->get('id') > 0 and  $clmuser[0]->published > 0 and $clmuser[0]->zps == $zps or $clmuser[0]->usertype == "admin") {

    // Prüfen ob Datensatz schon vorhanden ist
    $query	= "SELECT * "
        ." FROM #__clm_rangliste_id "
        ." WHERE sid = $sid AND zps = '$zps' AND gid = $gid ";
    $db->setQuery($query);
    $abgabe = $db->loadObjectList();
    if (isset($abgabe[0])) {
        $sg_zps = $abgabe[0]->sg_zps;
    } else {
        $sg_zps = '0';
    }

    $today = date("Y-m-d");
    // Datum der Erstellung
    $date = JFactory::getDate();
    $now = $date->toSQL();
    /* if (count($abgabe) > 0) {

        $link = 'index.php?option=com_clm&view=info';
        $msg = JText::_( '<h2>Diese Rangliste wurde bereits abgegeben ! </h2>Bitte schauen Sie in die entsprechende Mannschaftsübersicht' );
        $mainframe->redirect( $link, $msg);
                }
    */
    // evtl. vorhandene Daten in der Tabelle löschen
    $query	= " DELETE FROM #__clm_rangliste_id "
        ." WHERE gid = ".$gid
        ." AND sid = ".$sid
        ." AND zps = '$zps'"
    ;
    $db->setQuery($query);
    clm_core::$db->query($query);

    $query	= " DELETE FROM #__clm_rangliste_spieler "
        ." WHERE Gruppe = ".$gid
        ." AND sid = ".$sid
        ." AND ZPS = '$zps'"
    ;
    $db->setQuery($query);
    clm_core::$db->query($query);

    //vor Löschen der Meldelisten Start-DWZ, u.a. sichern
    $query	= " SELECT * FROM #__clm_meldeliste_spieler "
        ." WHERE status = ".$gid
        ." AND (ZPS = '$zps' OR ZPS = '$sg_zps')"
        ." AND sid =".$sid
    ;
    $db->setQuery($query);
    $dwz_meldeliste	= $db->loadObjectList();
    $old_ml = array();
    foreach ($dwz_meldeliste as $dwz_ml) {
        $old_ml[$dwz_ml->lid.' '.$dwz_ml->mnr.' '.$dwz_ml->zps.' '.$dwz_ml->mgl_nr] = $dwz_ml;
    }

    //Löschen der Meldelisten
    $query	= " DELETE FROM #__clm_meldeliste_spieler "
        ." WHERE status = ".$gid
        ." AND sid = ".$sid
        ." AND (ZPS = '$zps' OR ZPS = '$sg_zps') "
    ;
    $db->setQuery($query);
    clm_core::$db->query($query);

    // Liganummer ermitteln
    $query	= " SELECT a.liga, a.man_nr FROM #__clm_mannschaften as a"
        ." LEFT JOIN #__clm_liga as l ON l.id = a.liga and l.sid = a.sid"
        ." WHERE a.zps = '$zps'"
        ." AND a.sid =".$sid
        ." AND l.rang = ".$gid
        ." GROUP BY a.man_nr "
        ." ORDER BY a.man_nr ASC "
    ;
    $db->setQuery($query);
    $lid_rang	= $db->loadObjectList();

    // Datum und Uhrzeit für Meldung
    $date = JFactory::getDate();
    $now = $date->toSQL();

    // Datensätze schreiben
    $liga_count	= 0;
    $liga		= $lid_rang[0]->liga;
    $change		= clm_core::$load->request_string('MA0');

    $ZPSmgl	= array();
    $mgl	= array();
    $pkz	= array();
    $mnr	= array();
    $rang	= array();
    $block	= array();

    // Rangliste und Arrays schreiben
    for ($y = 0; $y < $count; $y++) {
        $ZPSmgl[]	= trim(clm_core::$load->request_string('ZPSM'.$y));
        $mgl[]	= clm_core::$load->request_string('MGL'.$y);
        $pkz[]	= clm_core::$load->request_string('PKZ'.$y);
        $mnr[]	= clm_core::$load->request_string('MA'.$y);
        $rang[]	= clm_core::$load->request_string('RA'.$y);
        $block[]	= clm_core::$load->request_int('check'.$y);

        if ($mnr[$y] !== "99" and $mnr[$y] !== "0" and $mnr[$y] !== "") {
            $query = " INSERT INTO #__clm_rangliste_spieler "
                ." (`Gruppe`, `ZPS`, `ZPSmgl`, `Mgl_Nr`, `PKZ`, `Rang`, `man_nr`, `sid`, gesperrt) "
                ." VALUES ('$gid','$zps','$ZPSmgl[$y]','$mgl[$y]','$pkz[$y]','$rang[$y]','$mnr[$y]','$sid','$block[$y]') "
            ;
            clm_core::$db->query($query);
        }
    }

    // Meldelisten schreiben
    for ($x = 0; $x < count($lid_rang); $x++) {
        $liga	= $lid_rang[$x]->liga;
        $man_nr	= $lid_rang[$x]->man_nr;

        $sn_cnt = 1;
        $snr_counter = 1;

        for ($y = 0; $y < $count; $y++) {
            $dkey = $liga.' '.$man_nr.' '.$ZPSmgl[$y].' '.intval($mgl[$y]);
            if (isset($old_ml[$dkey])) {
                $z_ordering	 = $old_ml[$dkey]->ordering;
                $z_start_dwz = $old_ml[$dkey]->start_dwz;
                $z_start_I0	 = $old_ml[$dkey]->start_I0;
                $z_DWZ		 = $old_ml[$dkey]->DWZ;
                $z_I0	 	 = $old_ml[$dkey]->I0;
                $z_Punkte	 = $old_ml[$dkey]->Punkte;
                $z_Partien	 = $old_ml[$dkey]->Partien;
                $z_We		 = $old_ml[$dkey]->We;
                $z_Leistung	 = $old_ml[$dkey]->Leistung;
                $z_EFaktor	 = $old_ml[$dkey]->EFaktor;
                $z_Niveau	 = $old_ml[$dkey]->Niveau;
                $z_sum_saison = $old_ml[$dkey]->sum_saison;
                $z_gesperrt	 = $old_ml[$dkey]->gesperrt;
            } else {
                $z_ordering = 0;
                $z_start_dwz = null;
                $z_start_I0 = null;
                $z_DWZ		 = 0;
                $z_I0	 	 = 0;
                $z_Punkte	 = 0;
                $z_Partien	 = 0;
                $z_We		 = 0;
                $z_Leistung	 = 0;
                $z_EFaktor	 = 0;
                $z_Niveau	 = 0;
                $z_sum_saison = 0;
                $z_gesperrt	 = 0;
            }
            if (is_null($z_gesperrt) or $z_gesperrt == '') {
                $z_gesperrt = '0';
            }
            if ($mnr[$y] >= $lid_rang[$x]->man_nr) {
                if ($z_start_dwz == null or $z_start_dwz == 0) {
                    $query = " INSERT INTO #__clm_meldeliste_spieler "
                        ." (`sid`, `lid`, `mnr`, `snr`, `mgl_nr`, `zps`,`status`,`ordering`,`start_dwz`,`start_I0`"
                        .",`DWZ`, `I0`, `Punkte`, `Partien`, `We`, `Leistung`,`EFaktor`,`Niveau`,`sum_saison`,`gesperrt`)"
                        ." VALUES ('$sid','$liga','$man_nr','$snr_counter','$mgl[$y]','$ZPSmgl[$y]','$gid','$z_ordering',NULL,NULL"
                        .",'$z_DWZ','$z_I0','$z_Punkte','$z_Partien','$z_We','$z_Leistung','$z_EFaktor','$z_Niveau','$z_sum_saison','$block[$y]') "
                    ;
                } else {
                    $query = " INSERT INTO #__clm_meldeliste_spieler "
                            ." (`sid`, `lid`, `mnr`, `snr`, `mgl_nr`, `zps`,`status`,`ordering`,`start_dwz`,`start_I0`"
                            .",`DWZ`, `I0`, `Punkte`, `Partien`, `We`, `Leistung`,`EFaktor`,`Niveau`,`sum_saison`,`gesperrt`)"
                            ." VALUES ('$sid','$liga','$man_nr','$snr_counter','$mgl[$y]','$ZPSmgl[$y]','$gid','$z_ordering','$z_start_dwz','$z_start_I0'"
                            .",'$z_DWZ','$z_I0','$z_Punkte','$z_Partien','$z_We','$z_Leistung','$z_EFaktor','$z_Niveau','$z_sum_saison','$block[$y]') "
                    ;
                }
                clm_core::$db->query($query);
                $sn_cnt++;
                $snr_counter++;
            }
        }
        if ($sn_cnt > 1) {
            $query = " UPDATE #__clm_mannschaften "
                ." SET  liste = 1"
                ." WHERE sid = $sid AND liga = $liga AND man_nr = $man_nr AND zps = '$zps' "
            ;
            clm_core::$db->query($query);
        }
    }


    $query = " INSERT INTO #__clm_rangliste_id "
        ." (`gid`, `sid`, `zps`, `sg_zps`, `rang`, `published`, `bemerkungen`, `bem_int`, `ordering`) "
        ." VALUES ('$gid','$sid','$zps','$sg_zps','0','$published','".$bemerkungen."','".$bem_int."', '$ordering') "
    ;
    $db->setQuery($query);
    clm_core::$db->query($query);

    //Sperrkennzeichen synchronisieren
    for ($y = 0; $y < $count; $y++) {
        $ZPSmgl	= trim(clm_core::$load->request_string('ZPSM'.$y));
        $mgl	= clm_core::$load->request_string('MGL'.$y);
        $block	= clm_core::$load->request_int('check'.$y);
        $block_a	= clm_core::$load->request_int('BLOCK_A'.$y);
        if ($block != $block_a) {
            $rc = clm_core::$api->db_syn_player_block($sid, $ZPSmgl, $mgl, $block);
            if ($rc[0] === false) {
                $msg = "m_updateError".$rc[1];
                $mainframe->enqueueMessage($msg, 'error');
            } else {
                $msg = $rc[1];
                $mainframe->enqueueMessage($msg, 'message');
            }
        }
    }

    // Log schreiben
    $jid_aktion =  ($user->get('id'));
    $aktion = "Die Rangliste wurde im FE gespeichert!";
    $callid = uniqid("", false);
    $userid = clm_core::$access->getId();
    $parray = array('sid' => $sid, 'gid' => $gid, 'zps' => $zps);
    $query	= "INSERT INTO #__clm_logging "
        ." ( `callid`, `userid`, `timestamp` , `type` ,`name`, `content`) "
        ." VALUES ('".$callid."','".$userid."',".time().",5,'".$aktion."','".json_encode($parray)."') "
    ;
    clm_core::$db->query($query);


    $msg = '<h5>'.JText::_('Die Rangliste wurde gespeichert!').'</h5>';
    $mainframe->enqueueMessage($msg, 'message');


    // Mails verschicken ?
    // Konfigurationsparameter auslesen
    $config = clm_core::$db->config();
    // Zur Abwärtskompatibilität mit CLM <= 1.0.3 werden alte Daten aus Language-Datei als Default eingelesen
    $from = $config->email_from;
    $fromname = $config->email_fromname;
    $bcc	= $config->email_bcc;
    $bcc_mail	= null;  /* damit keine direkte Mail an Admin */
    $sl_mail	= $config->sl_mail;
    $countryversion = $config->countryversion;
    $email_suppress = $config->email_suppress;

    if (!clm_core::$load->is_email($bcc)) {
        $bcc = null;
    }
    $send = 1;
    if (!clm_core::$load->is_email($from)) {
        $send = 0;
    } elseif ($fromname == '') {
        $send = 0;
    }

    if ($send == 1) {

        // nur wegen sehr leistungsschwachen Providern
        $query	= " SET SQL_BIG_SELECTS=1";
        $db->setQuery($query);
        clm_core::$db->query($query);

        // Daten für Email sammeln
        // Melder
        $query	= "SELECT a.* FROM #__clm_user as a "
            ." WHERE a.sid =".$sid
            ."   AND a.jid =".$jid_aktion
        ;
        $db->setQuery($query);
        $melder = $db->loadObjectList();
        // Saison
        $query	= "SELECT a.* FROM #__clm_saison as a "
            ." WHERE a.id =".$sid
        ;
        $db->setQuery($query);
        $saison = $db->loadObjectList();
        // Ranglisten
        $query	= "SELECT r.*, g.Gruppe, g.Meldeschluss FROM #__clm_rangliste_id as r "
            ." LEFT JOIN #__clm_rangliste_name as g ON g.id = r.gid "
            ." WHERE r.sid = ".$sid
            ." AND r.gid = ".$gid
            ." AND r.zps = '".$zps."'"
        ;
        $db->setQuery($query);
        $rangliste_id = $db->loadObjectList();
        // Ligen mit Daten SL
        $query	= "SELECT l.*, u.email as sl_email, u.name as sl_name FROM #__clm_liga as l "
            ." LEFT JOIN #__clm_rangliste_name as g ON g.id = l.rang "
            ." LEFT JOIN #__clm_user as u ON u.jid = l.sl AND u.sid = l.sid "
            ." WHERE l.rang =".$gid
            ." AND l.published = 1 "
        ;
        $db->setQuery($query);
        $ligen = $db->loadObjectList();
        $str_ligen = '';
        $a_ligen = array();
        foreach ($ligen as $liga1) {
            $str_ligen .= $liga1->id.",";
            $a_ligen[$liga1->id] = new stdClass();
            $a_ligen[$liga1->id]->sl_email = $liga1->sl_email;
            $a_ligen[$liga1->id]->sl_name = $liga1->sl_name;
            ;
            $a_ligen[$liga1->id]->name = $liga1->name;
            ;
        }
        if ($str_ligen != '') {
            $str_ligen = substr($str_ligen, 0, -1);
        }
        // Mannschaften mit Daten ML
        $query	= "SELECT a.*, u.email as mf_email, u.name as mf_name, Vereinname FROM #__clm_mannschaften as a "
            ." LEFT JOIN #__clm_user as u ON u.jid = a.mf AND u.sid = a.sid "
            ." LEFT JOIN #__clm_dwz_vereine as v ON (v.sid = a.sid AND v.ZPS = a.zps) "
            ." WHERE a.sid =".$sid
            ." AND (FIND_IN_SET ( a.liga, '".$str_ligen."' ) != 0) "
            ." AND a.zps = '".$zps."'"
            ." ORDER BY a.liga, a.man_nr "
        ;
        $db->setQuery($query);
        $mannschaft = $db->loadObjectList();
        // Spielerrangliste
        $query	= "SELECT a.*, p.DWZ as pDWZ, Spielername, Vereinname FROM #__clm_rangliste_spieler as a ";
        if ($countryversion == "de") {
            $query .= " LEFT JOIN #__clm_dwz_spieler as p ON (p.sid = a.sid AND p.ZPS = a.ZPSmgl AND p.Mgl_Nr = a.mgl_nr) ";
        } else {
            $query .= " LEFT JOIN #__clm_dwz_spieler as p ON (p.sid = a.sid AND p.ZPS = a.ZPSmgl AND p.PKZ = a.PKZ) ";
        }
        $query .= " LEFT JOIN #__clm_dwz_vereine as v ON (v.sid = a.sid AND v.ZPS = a.ZPSmgl) "
            ." WHERE a.sid = ".$sid
            ." AND a.Gruppe = ".$gid
            ." AND a.ZPS = '".$zps."'"
            ." ORDER BY a.man_nr, a.Rang ASC "
        ;
        $db->setQuery($query);
        $rangliste = $db->loadObjectList();
        // Anzahl gesperrter Spieler
        $query	= "SELECT SUM(a.gesperrt) as sum_sp FROM #__clm_rangliste_spieler as a "
            ." WHERE a.sid = ".$sid
            ." AND a.Gruppe = ".$gid
            ." AND a.ZPS = '".$zps."'"
        ;
        $db->setQuery($query);
        $sperr_count = $db->loadObjectList();
        if ($sperr_count[0]->sum_sp < 1) {
            $str_sp = '';
        } else {
            $str_sp = 'gesperrt';
        }

        // Mailbody HTML Header
        $body_html_header = '
			<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
			<title>'.JText::_('CLUB_LIST_MAIL_HEADLINE').'</title>
			</head>
			<body>';
        $body_html_footer = '
			</body>
			</html>';
        // Mailbody HTML Ranglisteneingabe oder -änderung
        $body_html =	'
		<table width="700" border="0" cellspacing="0" cellpadding="3" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;">
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;" colspan="7"><div align="center" style="font-size: 12px;"><strong>'.JText::_('CLUB_RANG_MAIL_HEADLINE').' '.JText::_('OF_DAY').JHTML::_('date', $now, JText::_('DATE_FORMAT_CLM_PDF')). '</strong></div></td>
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
			<td width="120" style="border-bottom: solid 1px #999999;"><strong>'.JText::_('CLUB_RANG_MAIL_RANG').'</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .$rangliste_id[0]->Gruppe. '&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="80" style="border-bottom: solid 1px #999999;"><strong>'.JText::_('CLUB_LIST_MAIL_SEASON').'</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .$saison[0]->name. '&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="120" style="border-bottom: solid 1px #999999;"><strong>'.JText::_('CLUB_LIST_MAIL_CLUB').'</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .$mannschaft[0]->Vereinname. '&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="80" style="border-bottom: solid 1px #999999;"><strong>'.JText::_('').'</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .''. '&nbsp;</td>
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
			<td width="40" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>'.JText::_('CLUB_RANG_MAIL_MNR').'</strong></div></td>
			<td width="60" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>'.JText::_('CLUB_RANG_MAIL_RNR').'</strong></div></td>
			<td width="170" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>'.JText::_('CLUB_LIST_MAIL_NAME').'</strong></div></td>
			<td width="15" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>'.$str_sp.'</strong></div></td>
			<td width="60" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>'.JText::_('CLUB_LIST_MAIL_RATING').'</strong></div></td>
			<td width="60" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>'.JText::_('CLUB_LIST_MAIL_NUMBER').'</strong></div></td>
			<td width="210" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>'.JText::_('CLUB_LIST_MAIL_CLUBL').'</strong></div></td>
		</tr>
	';
        foreach ($rangliste as $rangliste1) {
            if ($rangliste1->Mgl_Nr > 0) {
                if ($rangliste1->gesperrt == 1) {
                    $sperrkz = 'ja';
                } else {
                    $sperrkz = '';
                }
                $body_html .=   '
					<tr>
					<td style="border-bottom: solid 1px #999999;"><div align="center"><strong>'.$rangliste1->man_nr.'</strong></div></td>
					<td style="border-bottom: solid 1px #999999;"><div align="center">' .$rangliste1->Rang. '&nbsp;</div></td>
					<td style="border-bottom: solid 1px #999999;"><div align="center">' .$rangliste1->Spielername. '&nbsp;</div></td>
					<td style="border-bottom: solid 1px #999999;"><div align="center">' .$sperrkz. '&nbsp;</div></td>
					<td style="border-bottom: solid 1px #999999;"><div align="center">' .$rangliste1->pDWZ. '&nbsp;</div></td>
					<td style="border-bottom: solid 1px #999999;"><div align="center">' .str_pad($rangliste1->Mgl_Nr, 3, "0", STR_PAD_LEFT). '&nbsp;</div></td>
					<td style="border-bottom: solid 1px #999999;"><div align="center">' .$rangliste1->Vereinname. '&nbsp;</div></td>
					</tr>
				';
            }
        }
        $body_html .= 	  '
		<tr>
			<td width="700" colspan="7">&nbsp;</td>
		</tr>	
		<tr>
			<td width="700" colspan="7"><div align="left" ><strong>'.'Bemerkung'.'</strong></div></td>
		</tr>	
		<tr>
			<td width="700" nowrap="nowrap" valign="top" size="1" colspan="7">
				<textarea cols="30" rows="2" style="border: solid 1px #999999; width:90%">'.str_replace('&', '&amp;', $bemerkungen).'</textarea>
			</td>
		</tr>	
		<tr>
			<td  width="700" colspan="7">&nbsp;</td>
		</tr>	
		</table>
		<table width="700" border="0" cellspacing="0" cellpadding="3" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;">
		<tr>
			<td width="100" style="border-bottom: solid 1px #999999;"><strong>'.JText::_('CLUB_LIST_MAIL_SENDER').'</strong></td>
			<td width="150" style="border-bottom: solid 1px #999999;">' .$melder[0]->name. '&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="100" style="border-bottom: solid 1px #999999;"><strong>'.JText::_('').'</strong></td>
			<td width="150" style="border-bottom: solid 1px #999999;">' .''. '&nbsp;</td>
			<td width="20" style="border-bottom: solid 1px #999999;">&nbsp;</td>
		</tr>';
        foreach ($mannschaft as $mannschaft1) {
            $z_liga = $mannschaft1->liga;
            $body_html .=	'
		<tr>
			<td style="border-bottom: solid 1px #999999;"><strong>'.JText::_('CLUB_LIST_MAIL_LEAGUE').'</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .$a_ligen[$z_liga]->name. '&nbsp;</td>
			<td style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td style="border-bottom: solid 1px #999999;"><strong>'.JText::_('CLUB_LIST_MAIL_CONTROLLER').'</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .$a_ligen[$z_liga]->sl_name. '&nbsp;</td>
			<td style="border-bottom: solid 1px #999999;">&nbsp;</td>
		</tr>
		<tr>
			<td style="border-bottom: solid 1px #999999;"><strong>'.JText::_('CLUB_LIST_MAIL_TEAM').'</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .$mannschaft1->name. '&nbsp;</td>
			<td style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td style="border-bottom: solid 1px #999999;"><strong>'.JText::_('CLUB_LIST_MAIL_CAPTAIN').'</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .$mannschaft1->mf_name. '&nbsp;</td>
			<td style="border-bottom: solid 1px #999999;">&nbsp;</td>
		</tr>
		';
        }
        $body_html .=	'  </table>';
        $subject = $fromname.': '.JTEXT::_('CLUB_RANG_SUBJECT').' '.$mannschaft[0]->Vereinname.'  '.$rangliste_id[0]->Gruppe.'   '.$saison[0]->name;

        $body_name = JText::_('RESULT_NAME').$melder[0]->name.",";
        $countmail = 0;
        $msg = '';

        // Textparameter setzen
        if (!is_null($abgabe)) {
            $erstmeldung = 0;
        }	// Erstmeldung nein
        else {
            $erstmeldung = 1;
        }  						// Erstmeldung ja
        if ($rangliste_id[0]->Meldeschluss < $today) {
            $korr_moeglich = 0; 	// Korrektur möglich im FE nein
            $deadline_roster = '';
        } else {
            $korr_moeglich = 1; 			// Korrektur möglich im FE ja
            $deadline_roster = JHTML::_('date', $rangliste_id[0]->Meldeschluss, JText::_('DATE_FORMAT_CLM_F'));
        }

        // Mail Melder
        if (isset($melder[0]->email) and clm_core::$load->is_email($melder[0]->email)) {
            $recipient = $melder[0]->email;
            $body_html_md = '
		<table width="700" border="0" cellspacing="0" cellpadding="3" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px;">
		<tr>
		  <td>'.JText::_('CLUB_RANG_MAIL_MD1').'</td>
  		</tr>
		<tr>
		  <td>';
            if ($erstmeldung == 1) {
                $body_html_md .= JText::_('CLUB_LIST_MAIL_MD2');
            } else {
                $body_html_md .= JText::_('CLUB_LIST_MAIL_MD2A');
            }
            $body_html_md .= '</td>
		</tr>
		<tr>';
            if ($korr_moeglich == 1) {
                if ($erstmeldung == 1) {
                    $body_html_md .= '<td>'.JText::_('CLUB_LIST_MAIL_MD3').$deadline_roster.'</td></tr><tr>';
                } else {
                    $body_html_md .= '<td>'.JText::_('CLUB_LIST_MAIL_MD3A').$deadline_roster.'</td></tr><tr>';
                }
            }
            $body_html_md .= '</tr>
		</table>
		';
            $body_name = JText::_('RESULT_NAME').$melder[0]->name.",";
            $body = $body_html_header.$body_name.$body_html_md.$body_html.$body_html_footer;
            $result = clm_core::$api->mail_send($recipient, $subject, $body, 1, null, $bcc);
            if ($result[0] !== true) {
                $msg .= '<br>'.'Fehler bei Mailausgabe: '.'<br>'.$result[1];
            }
            $countmail++;
        }

        // Mail Mannschaftsleiter
        $a_mail = array();
        foreach ($mannschaft as $mannschaft1) {
            if (in_array($mannschaft1->mf_email, $a_mail)) {
                continue;
            }
            if (isset($mannschaft1->mf_email) and clm_core::$load->is_email($mannschaft1->mf_email)) {
                $recipient = $mannschaft1->mf_email;
                $a_mail[] = $recipient;
                $body_html_mf = '
		<table width="700" border="0" cellspacing="0" cellpadding="3" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px;">
		<tr>
		  <td>';
                if ($erstmeldung == 1) {
                    $body_html_mf .= JText::_('CLUB_RANG_MAIL_MF1');
                } else {
                    $body_html_mf .= JText::_('CLUB_RANG_MAIL_MF1A');
                }
                $body_html_mf .= '</td>
  		</tr>
		<tr>
		  <td>'.JText::_('CLUB_LIST_MAIL_MF2').'</td>
		</tr>
		<tr>';
                if ($korr_moeglich == 1) {
                    if ($erstmeldung == 1) {
                        $body_html_mf .= '<td>'.JText::_('CLUB_LIST_MAIL_MF3').$deadline_roster.'</td></tr><tr>';
                    } else {
                        $body_html_mf .= '<td>'.JText::_('CLUB_LIST_MAIL_MF3A').$deadline_roster.'</td></tr><tr>';
                    }
                }
                $body_html_mf .= '</tr>
		</table>
		';
                $body_name = JText::_('RESULT_NAME').$mannschaft1->mf_name.",";
                $body = $body_html_header.$body_name.$body_html_mf.$body_html.$body_html_footer;
                $result = clm_core::$api->mail_send($recipient, $subject, $body, 1, null, $bcc);
                if ($result[0] !== true) {
                    $msg .= '<br>'.'Fehler bei Mailausgabe: '.'<br>'.$result[1];
                }
                $countmail++;
            }
        }

        // Mail Staffelleiter
        $a_mail = array();
        foreach ($mannschaft as $mannschaft1) {
            $z_liga = $mannschaft1->liga;
            if (in_array($a_ligen[$z_liga]->sl_email, $a_mail)) {
                continue;
            }
            if ($sl_mail == 1 and isset($a_ligen[$z_liga]->sl_email) and clm_core::$load->is_email($a_ligen[$z_liga]->sl_email)) {
                $recipient = $a_ligen[$z_liga]->sl_email;
                $a_mail[] = $recipient;
                $body_html_sl = '
		<table width="700" border="0" cellspacing="0" cellpadding="3" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px;">
		<tr>';
                if ($erstmeldung == 1) {
                    $body_html_sl .= '<td>'.JText::_('CLUB_RANG_MAIL_SL1').'</td>';
                } else {
                    $body_html_sl .= '<td>'.JText::_('CLUB_RANG_MAIL_SL1A').'</td>';
                }
                $body_html_sl .= '</tr>
		<tr>
		  <td>'.JText::_('CLUB_LIST_MAIL_SL2').'</td>
		</tr>
		<tr>
		  <td>'.JText::_('CLUB_LIST_MAIL_SL3').'</td>
		</tr>
		<tr> </tr>
		</table>
		';
                $body_name = JText::_('RESULT_NAME').$a_ligen[$z_liga]->sl_name.",";
                $body = $body_html_header.$body_name.$body_html_sl.$body_html.$body_html_footer;
                $result = clm_core::$api->mail_send($recipient, $subject, $body, 1, null, $bcc);
                if ($result[0] !== true) {
                    $msg .= '<br>'.'Fehler bei Mailausgabe: '.'<br>'.$result[1];
                }
                $countmail++;
            }
        }

        // Mail Admin
        if (clm_core::$load->is_email($bcc_mail)) {
            $recipient = $bcc_mail;
            $body_html_ad = '
		<table width="700" border="0" cellspacing="0" cellpadding="3" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px;">
		<tr>';
            if ($erstmeldung == 1) {
                $body_html_ad .= '<td>'.JText::_('CLUB_RANG_MAIL_AD1').'</td>';
            } else {
                $body_html_ad .= '<td>'.JText::_('CLUB_RANG_MAIL_AD1A').'</td>';
            }
            $body_html_ad .= '</tr>
		<tr>
		  <td>'.JText::_('CLUB_LIST_MAIL_AD2').'</td>
		</tr>
		<tr> </tr>
		</table>
		';
            $body_name = JText::_('RESULT_NAME').$bcc_name.",";
            $body = $body_html_header.$body_name.$body_html_ad.$body_html.$body_html_footer;
            $result = clm_core::$api->mail_send($recipient, $subject, $body, 1);
            if ($result[0] !== true) {
                $msg .= '<br>'.'Fehler bei Mailausgabe: '.'<br>'.$result[1];
            }
            $countmail++;
        }
        if ($email_suppress != 1) {
            $msg .= "<h5>".$countmail++. ' '.JText::_('Mail wurden gesendet')."</h5>";
        }
        $mainframe->enqueueMessage($msg, 'message');

    }
} else {
    $msg = JText::_('CLUB_DATA_SENT_FALSE');
    $mainframe->enqueueMessage($msg);
    $mainframe->redirect($link);

}
$mainframe->redirect('index.php');
