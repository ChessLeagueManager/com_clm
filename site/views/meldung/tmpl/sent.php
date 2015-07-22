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

JRequest::checkToken() or die( 'Invalid Token' );

	$mainframe	= JFactory::getApplication();

function mb_str_pad($input, $pad_length, $pad_string=' ', $pad_type=STR_PAD_RIGHT) {
    $diff = substr_count($input,'ä') + substr_count($input,'ö') + substr_count($input,'ü') + substr_count($input,'ß') +
	        substr_count($input,'Ä') + substr_count($input,'Ö') + substr_count($input,'Ü');
    $newstr = str_pad($input, $pad_length+$diff, $pad_string, $pad_type);
	return $newstr;
}

$h1		= JRequest::getVar('heim1');
$sid		= JRequest::getVar('sid');
$lid		= JRequest::getVar('lid');
$rnd		= JRequest::getVar('runde');
$paarung	= JRequest::getVar('paarung');
$stamm		= JRequest::getVar('stamm');
$htln		= JRequest::getVar('htln');
$gtln		= JRequest::getVar('gtln');
$dg		= JRequest::getVar('dg');
$itemid		= JRequest::getInt('Itemid');
$ko_decision = JRequest::getVar( 'ko_decision'); //mtmt
$comment = JRequest::getVar( 'comment');
$user =& JFactory::getUser();
$meldung = $user->get('id');
if (!$user->get('id')) {
echo '<h1>'.JText::_('RESULT_DATA_LOGIN').'</h1>'; }

else {

	$db 	=& JFactory::getDBO();
// Überprüfen ob Runde schon gemeldet ist
	$query	= "SELECT gemeldet "
		." FROM #__clm_rnd_man "
		." WHERE sid = $sid AND lid = $lid AND runde = $rnd "
		." AND paar = $paarung AND dg = $dg AND heim = 1"
		;
	$db->setQuery( $query );
	$id = $db->loadObjectList();

//if (!$id[0]->gemeldet) {
// altes Ergebnis löschen bei Bedarf
	if (isset($id[0]->gemeldet)) {
		$query	= "DELETE FROM #__clm_rnd_spl"
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		;
	$db->setQuery($query);
	$db->query();
	}

	// Punktemodus aus #__clm_liga holen
	$query = " SELECT a.stamm, a.sieg, a.remis, a.nieder, a.antritt, a.runden_modus, a.runden, a.params, "
		." a.man_sieg, a.man_remis, a.man_nieder, a.man_antritt, a.sieg_bed "
		." FROM #__clm_liga as a"
		." WHERE a.id = ".$lid
		;
	$db->setQuery($query);
	$liga = $db->loadObjectList();
		$stamm 		= $liga[0]->stamm;
		$sieg_bed	= $liga[0]->sieg_bed;
		$sieg 		= $liga[0]->sieg;
		$remis 		= $liga[0]->remis;
		$nieder		= $liga[0]->nieder;
		$antritt	= $liga[0]->antritt;
		$man_sieg 	= $liga[0]->man_sieg;
		$man_remis 	= $liga[0]->man_remis;
		$man_nieder	= $liga[0]->man_nieder;
		$man_antritt	= $liga[0]->man_antritt;
		$runden_modus	= $liga[0]->runden_modus;   //mtmt
		$runden		= $liga[0]->runden;   //mtmt
	//Liga-Parameter aufbereiten
	$paramsStringArray = explode("\n", $liga[0]->params);
	$params = array();
	foreach ($paramsStringArray as $value) {
		$ipos = strpos ($value, '=');
		if ($ipos !==false) {
			$key = substr($value,0,$ipos);
			if (substr($key,0,2) == "\'") $key = substr($key,2,strlen($key)-4);
			if (substr($key,0,1) == "'") $key = substr($key,1,strlen($key)-2);
			$params[$key] = substr($value,$ipos+1);
		}
	}	
	if (!isset($params['color_order']))  {   //Standardbelegung
		$params['color_order'] = '1'; }
	switch ($params['color_order']) {
		case '1': $colorstr = '01'; break;
		case '2': $colorstr = '10'; break;
		case '3': $colorstr = '0110'; break;
		case '4': $colorstr = '1001'; break;
		case '5': $colorstr = '00'; break;
		case '6': $colorstr = '11'; break;
		default: $colorstr = '01';	
	}
 
// Datensätze in Spielertabelle schreiben
$y1 = 0;
for ($y=1; $y< (1+$stamm) ; $y++){
	$heim		= JRequest::getVar( 'heim'.$y);
	$gast		= JRequest::getVar( 'gast'.$y);
	$ergebnis	= JRequest::getVar( 'ergebnis'.$y);

	$teil_heim 	= explode("-", $heim);
	$hmgl 		= $teil_heim[0];
	$hzps 		= $teil_heim[1];

	$teil_gast 	= explode("-", $gast);
	$gmgl 		= $teil_gast[0];
	$gzps 		= $teil_gast[1];

if ($ergebnis > 2) { $kampflos = 1; }
	else { $kampflos = 0; }

	if ($ergebnis == 0)
		{ 	$erg_h = $nieder+$antritt;
			$erg_g = $sieg+$antritt;
		}
	if ($ergebnis == 1)
		{ 	$erg_h = $sieg+$antritt;
			$erg_g = $nieder+$antritt;
		}
	if ($ergebnis == 2)
		{ 	$erg_h = $remis+$antritt;
			$erg_g = $remis+$antritt;
		}
	if ($ergebnis == 3)
		{ 	$erg_h = $antritt;
			$erg_g = $antritt;
		}
	if ($ergebnis == 4)
		{ 	$erg_h = 0;
			$erg_g = $sieg+$antritt;
		}
	if ($ergebnis == 5)
		{ 	$erg_h = $sieg+$antritt;
			$erg_g = 0;
		}
	if ($ergebnis == 6)
		{ 	$erg_h = 0;
			$erg_g = 0;
		}
	if ($ergebnis == 7)
		{ 	$erg_h = 0;
			$erg_g = 0;
		}
	if ($ergebnis == 8)
		{ 	$erg_h = 0;
			$erg_g = 0;
		}

// ungerade Zahl für Weiss/Schwarz
//if ($y%2 != 0) {$weiss = 0; $schwarz = 1;}
//else { $weiss = 1; $schwarz = 0;}
	// Weiss/Schwarz entsp. Farbfolge-Parameter
	$weiss = substr($colorstr,$y1,1);
	if ($weiss == 1) $schwarz = 0; else $schwarz = 1;
	$y1++;
	if ($y1 >= strlen($colorstr)) $y1 = 0;

	if($heim =='') { $hzps ='NULL';$hmgl='NULL';}
	if($gast =='') { $gzps ='NULL';$gmgl='NULL';}
	if($ergebnis !='NULL') {

	$query	= "INSERT INTO #__clm_rnd_spl "
		." ( `sid`, `lid`, `runde`, `paar`, `dg`, `tln_nr`, `brett`, `heim`, `weiss`, `spieler` "
		." , `zps`, `gegner`, `gzps`, `ergebnis` , `kampflos`, `punkte`, `gemeldet`) "
		." VALUES ('$sid','$lid','$rnd','$paarung','$dg','$htln','$y',1,'$weiss','$hmgl','$hzps',"
		." '$gmgl','$gzps','$ergebnis', '$kampflos','$erg_h','$meldung') "
		." , ('$sid','$lid','$rnd','$paarung','$dg','$gtln','$y','0','$schwarz','$gmgl','$gzps',"
		." '$hmgl','$hzps','$ergebnis', '$kampflos','$erg_g','$meldung') "
		;
	}
	else {
	$query	= "INSERT INTO #__clm_rnd_spl "
		." ( `sid`, `lid`, `runde`, `paar`, `dg`, `tln_nr`, `brett`, `heim`, `weiss`, `spieler` "
		." , `zps`, `gegner`, `gzps`, `gemeldet`) "
		." VALUES ('$sid','$lid','$rnd','$paarung','$dg','$htln','$y',1,'$weiss','$hmgl','$hzps',"
		." '$gmgl','$gzps', '$meldung') "
		." , ('$sid','$lid','$rnd','$paarung','$dg','$gtln','$y','0','$schwarz','$gmgl','$gzps',"
		." '$hmgl','$hzps', '$meldung') "
		;
	}
	$db->setQuery($query);
	$db->query();

	$heim ='';
	$gast ='';
	}
// in Runden Mannschaftstabelle als gemeldet schreiben

// Brettpunkte Heim summieren
	$query	= "SELECT SUM(punkte) as punkte "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		;
	$db->setQuery($query);
	$man=$db->loadObjectList();
	$hmpunkte=$man[0]->punkte;
	if (!isset($hmpunkte)) $hmpunkte = 0; //klkl
	
	// Wertpunkte Heim berechnen
	$query	= "SELECT punkte, brett "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		;
	$db->setQuery($query);
	$man_wp=$db->loadObjectList();
	$hwpunkte=0;
	foreach ($man_wp as $man_wp) {
		$hwpunkte = $hwpunkte + (($stamm + 1 - $man_wp->brett) * $man_wp->punkte);
	}
	
	$query	= "SELECT COUNT(kampflos) as kl "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		." AND kampflos > 0 "
		;
	$db->setQuery($query);
	$man_kl=$db->loadObjectList();
	$man_kl_punkte=$man_kl[0]->kl;

// Brettpunkte Gast summieren
	$query	= "SELECT SUM(punkte) as punkte "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 0 "
		;
	$db->setQuery($query);
	$gman=$db->loadObjectList();
	$gmpunkte=$gman[0]->punkte;
	if (!isset($gmpunkte)) $gmpunkte = 0; //klkl

	// Wertpunkte Gast berechnen
	$query	= "SELECT punkte, brett "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 0 "
		;
	$db->setQuery($query);
	$man_wp=$db->loadObjectList();
	$gwpunkte=0;
	foreach ($man_wp as $man_wp) {
		$gwpunkte = $gwpunkte + (($stamm + 1 - $man_wp->brett) * $man_wp->punkte);
	}
	
	$query	= "SELECT COUNT(kampflos) as kl "
		." FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 0 "
		." AND kampflos > 0 "
		;
	$db->setQuery($query);
	$gman_kl=$db->loadObjectList();
	$gman_kl_punkte=$gman_kl[0]->kl;


	// Mannschaftspunkte Heim / Gast verteilen
	// Standard : Mehrheit der BP gewinnt, BP gleich -> Punkteteilung
	if ($sieg_bed == 1) {
		if ( $hmpunkte >  $gmpunkte ) { $hman_punkte = $man_sieg; $gman_punkte = $man_nieder;}
		if ( $hmpunkte == $gmpunkte ) { $hman_punkte = $man_remis; $gman_punkte = $man_remis;}
		if ( $hmpunkte <  $gmpunkte ) { $hman_punkte = $man_nieder; $gman_punkte = $man_sieg;}
	}
	// erweiterter Standard : mehr als die H�lfte der BP -> Sieg, H�lfte der BP -> halbe MP Zahl
	if ($sieg_bed == 2) {
		if ( $hmpunkte >  (($stamm*($sieg+$antritt))/2) ) { $hman_punkte = $man_sieg;}
		if ( $hmpunkte == (($stamm*($sieg+$antritt))/2) ) { $hman_punkte = $man_remis;}
		if ( $hmpunkte <  (($stamm*($sieg+$antritt))/2) ) { $hman_punkte = $man_nieder;}
		
		if ( $gmpunkte >  (($stamm*($sieg+$antritt))/2) ) { $gman_punkte = $man_sieg;}
		if ( $gmpunkte == (($stamm*($sieg+$antritt))/2) ) { $gman_punkte = $man_remis;}
		if ( $gmpunkte <  (($stamm*($sieg+$antritt))/2) ) { $gman_punkte = $man_nieder;}
	}
	// Antrittspunkte addieren falls angetreten
	if ( $stamm > $man_kl_punkte ) { $hman_punkte = $hman_punkte + $man_antritt;}
	if ( $stamm > $gman_kl_punkte ) { $gman_punkte = $gman_punkte + $man_antritt;}
	
// Datum und Uhrzeit für Meldung
	$date =& JFactory::getDate();
	$now = $date->toMySQL();

// Für Heimmannschaft updaten
	$query	= "UPDATE #__clm_rnd_man"
		." SET gemeldet = ".$meldung
		." , zeit = '$now'"
		." , brettpunkte = ".$hmpunkte
		." , manpunkte = ".$hman_punkte
		." , wertpunkte = ".$hwpunkte
		." , comment = '".$comment."'"
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		;
	$db->setQuery($query);
	$db->query();
// Für Gastmannschaft updaten
	$query	= "UPDATE #__clm_rnd_man"
		." SET gemeldet = ".$meldung
		." , zeit = '$now'"
		." , brettpunkte = ".$gmpunkte
		." , manpunkte = ".$gman_punkte
		." , wertpunkte = ".$gwpunkte
		." , comment = '".$comment."'"
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 0 "
		;
	$db->setQuery($query);
	$db->query();

	//mtmt start
	if ($runden_modus == 4 OR $runden_modus == 5) {    // KO Turnier
	  if (($runden_modus == 4) OR ($runden_modus == 5 and $rnd < $runden)) {    // KO Turnierif ($ko_decision == 1) {
		if ($ko_decision == 1) {
			if ($hmpunkte > $gmpunkte) $ko_par = 2;			// Sieger Heim nach Brettpunkte
			elseif ($hmpunkte < $gmpunkte) $ko_par = 3;		// Sieger Gast nach Brettpunkte
			elseif ($hwpunkte > $gwpunkte) $ko_par = 2;		// Sieger Heim nach Wertpunkte
			elseif ($hwpunkte < $gwpunkte) $ko_par = 3;		// Sieger Gast nach Wertpunkte
			else { $ko_par = 3;								// Sieger Gast nach Computer --> Nacharbeit durch TL
				$comment = '<b>Keine KO-Entscheidung eingegeben, Gast als Sieger angenommen, Nacharbeit durch Turnierleiter nötig</b><br>'.$comment; }
		}	
		elseif ($ko_decision == 2) $ko_par = 2;				// Sieger Heim nach Blitz-Entscheid
		elseif ($ko_decision == 4) $ko_par = 2;				// Sieger Heim nach Los-Entscheid
		else $ko_par = 3;									// Sieger Gast nach Blitz-,Los-Entscheid
		if ($ko_par == 2) { $ko_heim = $rnd; $ko_gast = $rnd -1; }
		else { $ko_heim = $rnd -1; $ko_gast = $rnd; }
		// Für Heimmannschaft updaten
		$query	= "UPDATE #__clm_mannschaften"
			." SET rankingpos = ".$ko_heim
			." WHERE sid = ".$sid
			." AND liga = ".$lid
			." AND tln_nr = ".$htln
//			." AND tln_nr = ".$tln_nr
		;
		$db->setQuery($query);
		$db->query();
		// Für Gastmannschaft updaten
		$query	= "UPDATE #__clm_mannschaften"
			." SET rankingpos = ".$ko_gast
			." WHERE sid = ".$sid
			." AND liga = ".$lid
			." AND tln_nr = ".$gtln
//			." AND tln_nr = ".$gegner
		;
		$db->setQuery($query);
		$db->query();	
	  }
		// Für Heimmannschaft updaten
		$query	= "UPDATE #__clm_rnd_man"
			." SET ko_decision = ".$ko_decision
			." , comment = '".$comment."'"
			." WHERE sid = ".$sid
			." AND lid = ".$lid
			." AND runde = ".$rnd
			." AND paar = ".$paarung
			." AND dg = ".$dg
			." AND heim = 1 "
		;
		$db->setQuery($query);
		$db->query();
		// Für Gastmannschaft updaten
		$query	= "UPDATE #__clm_rnd_man"
			." SET ko_decision = ".$ko_decision
			." , comment = '".$comment."'"
			." WHERE sid = ".$sid
			." AND lid = ".$lid
			." AND runde = ".$rnd
			." AND paar = ".$paarung
			." AND dg = ".$dg
			." AND heim = 0 "
		;
		$db->setQuery($query);
		$db->query();
	}
	
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'controllers'.DS.'ergebnisse.php');
	//require_once(JPATH_COMPONENT.DS.'controllers'.DS.'ergebnisse.php');
	CLMControllerErgebnisse::calculateRanking($sid,$lid);
	//require_once(JPATH_COMPONENT.DS.'views'.DS.'runden.php');
	//CLMViewRunden::dwz( $option, 0, $sid, $lid );
	
	//mtmt ende

// Mails verschicken ?
	$query	= "SELECT mail, name, runden FROM #__clm_liga"
		." WHERE sid =".$sid
		." AND id =".$lid
		;
	$db->setQuery($query);
	$mail = $db->loadObjectList();

if ( $mail[0]->mail > 0 ) {
	// Konfigurationsparameter auslesen
	$config = &JComponentHelper::getParams( 'com_clm' );
	// Zur Abwärtskompatibilität mit CLM <= 1.0.3 werden alte Daten aus Language-Datei als Default eingelesen
	$from = $config->get('email_from', JText::_('RESULT_DATA_MAIL'));
	$fromname = $config->get('email_fromname', JText::_('RESULT_DATA_FROM'));
	$bcc	= $config->get('email_bcc', $config->get('bcc'));
	
// nur wegen sehr leistungsschwachen Providern
	$query	= " SET SQL_BIG_SELECTS=1";
	$db->setQuery($query);
	$db->query();

// Daten für Email sammeln
	$query	= "SELECT a.name,email, a.tln_nr,u.name as empfang FROM #__clm_mannschaften as a "
		." LEFT JOIN #__clm_user as u ON u.jid = a.mf AND u.sid = a.sid"  //klkl
		." WHERE a.sid =".$sid
		." AND a.liga =".$lid
		." AND (tln_nr = $htln OR tln_nr = $gtln )"
		." AND u.jid > 0 "
		;
	$db->setQuery($query);
	$empfang = $db->loadObjectList();
// Mannschaften
	$query	= "SELECT m.name FROM #__clm_rnd_man as a"
		." LEFT JOIN #__clm_mannschaften as m ON (m.sid = a.sid AND m.liga = a.lid AND m.tln_nr = a.tln_nr) "
		." WHERE a.sid = ".$sid
		." AND a.lid = ".$lid
		." AND a.runde = ".$rnd
		." AND a.paar = ".$paarung
		." AND a.dg = ".$dg
		." ORDER BY a.heim DESC "
		;
	$db->setQuery($query);
	$begegnung=$db->loadObjectList();

	//Paar
	$query = "SELECT a.*,g.id as gid, g.name as gname, g.tln_nr as gtln,g.published as gpublished, g.rankingpos as grank, "
		." h.id as hid, h.name as hname, h.tln_nr as htln, h.published as hpublished, h.rankingpos as hrank, b.wertpunkte as gwertpunkte, "
		." u.name as gmf, v.name as hmf, w.name as melder "
		." FROM #__clm_rnd_man as a"
		." LEFT JOIN #__clm_mannschaften AS g ON g.tln_nr = a.gegner AND g.liga = a.lid AND g.sid = a.sid"
		." LEFT JOIN #__clm_mannschaften AS h ON h.tln_nr = a.tln_nr AND h.liga = a.lid AND h.sid = a.sid"
		." LEFT JOIN #__clm_rnd_man AS b ON b.sid = ".$sid." AND b.lid = ".$lid." AND b.runde = ".$rnd." AND b.dg = ".$dg." AND b.paar = a.paar AND b.heim = 0 "
		." LEFT JOIN #__clm_user as u ON u.jid = g.mf AND u.sid = a.sid"  
		." LEFT JOIN #__clm_user as v ON v.jid = h.mf AND v.sid = a.sid"  
		." LEFT JOIN #__clm_user as w ON w.jid = a.gemeldet AND w.sid = a.sid"  
			." WHERE a.sid = ".$sid
			." AND a.lid = ".$lid
			." AND a.runde = ".$rnd
			." AND a.dg = ".$dg
			." AND a.paar = ".$paarung
			." AND a.heim = 1 "
			;
	$db->setQuery($query);
	$paar=$db->loadObjectList();
	//echo "<br>error: ".mysql_errno() . ": " . mysql_error(). "\n";

	//Einzel
	$query = "SELECT a.zps, a.gzps, a.paar,a.brett,a.spieler,a.gegner,a.ergebnis,a.kampflos, a.dwz_edit, a.dwz_editor, m.name,"
		." n.name, d.Spielername as hname, d.DWZ as hdwz, "
		." p.erg_text as erg_text, e.Spielername as gname, e.DWZ as gdwz, q.erg_text as dwz_text, "
		." k.snr as hsnr, l.snr as gsnr, k.mgl_nr as hmglnr, l.mgl_nr as gmglnr"                                                                                     
		." FROM #__clm_rnd_spl as a "
		." LEFT JOIN #__clm_rnd_man as r ON ( r.lid = a.lid AND r.runde = a.runde AND r.tln_nr = a.tln_nr AND  r.dg = a.dg) "
		." LEFT JOIN #__clm_mannschaften AS m ON ( m.tln_nr = r.tln_nr AND m.liga = a.lid) "
		." LEFT JOIN #__clm_mannschaften AS n ON ( n.tln_nr = r.gegner AND n.liga = a.lid) "
        	." LEFT JOIN #__clm_dwz_spieler AS d ON ( d.Mgl_Nr = a.spieler AND d.ZPS = a.zps AND d.sid = a.sid) "
        	." LEFT JOIN #__clm_dwz_spieler AS e ON ( e.Mgl_Nr = a.gegner AND e.ZPS = a.gzps AND e.sid = a.sid) "
		." LEFT JOIN #__clm_ergebnis AS p ON p.eid = a.ergebnis "
		." LEFT JOIN #__clm_ergebnis AS q ON q.eid = a.dwz_edit "
		." LEFT JOIN #__clm_meldeliste_spieler as k ON k.sid = a.sid AND k.lid = a.lid AND k.mgl_nr = a.spieler AND k.zps = a.zps AND k.mnr=m.man_nr "  //klkl2
		." LEFT JOIN #__clm_meldeliste_spieler as l ON l.sid = a.sid AND l.lid = a.lid AND l.mgl_nr = a.gegner AND l.zps = a.gzps AND l.mnr=n.man_nr "  //klkl2
			." WHERE a.sid =  ".$sid
			." AND a.lid = ".$lid
			." AND a.runde = ".$rnd
			." AND a.dg = ".$dg
			." AND a.paar = ".$paarung
			." AND a.heim = 1"
			." ORDER BY a.brett ASC"
			;
	$db->setQuery($query);
	$einzel=$db->loadObjectList();
	//echo "<br>error: ".mysql_errno() . ": " . mysql_error(). "\n";
 
//Mannschaftspunkte
	$query = " SELECT u.name,a.paar as paarung,a.runde as runde,a.brettpunkte as sum, dwz_editor "
		." FROM #__clm_rnd_man as a "
		." LEFT JOIN #__clm_user as u ON (u.jid = a.gemeldet AND a.heim = 1 AND u.sid = $sid)"
			." WHERE a.lid = ".$lid
			." AND a.sid = ".$sid
			." AND a.runde = ".$rnd
			." AND a.dg = ".$dg
			." AND a.paar = ".$paarung
			." ORDER BY a.heim DESC"
			;
	$db->setQuery($query);
	$summe=$db->loadObjectList();
	//echo "<br>error: ".mysql_errno() . ": " . mysql_error(). "\n";

//Rundendaten
	$rnd1 = $rnd + (($dg-1) * $mail[0]->runden);
	$query = " SELECT name, datum "
		." FROM #__clm_runden_termine as a "
			." WHERE a.liga = ".$lid
			." AND a.nr = ".$rnd1
			." AND a.sid = ".$sid
			;
	$db->setQuery($query);
	$rundeterm=$db->loadObjectList();
	//echo "<br>error: ".mysql_errno() . ": " . mysql_error(). "\n";
//DWZ gespielt
	$query = " SELECT a.sid,a.lid,a.runde,a.paar,a.dg, AVG(d.DWZ) as dwz,AVG(g.DWZ) as gdwz "
		." FROM #__clm_rnd_man as a "
		." LEFT JOIN #__clm_rnd_spl AS r ON (r.sid=a.sid AND r.lid= a.lid AND r.runde=a.runde AND r.paar = a.paar AND r.dg = a.dg) "
		." LEFT JOIN #__clm_dwz_spieler AS d ON (d.ZPS = r.zps AND d.Mgl_Nr = r.spieler AND d.sid = r.sid) "
		." LEFT JOIN #__clm_dwz_spieler AS g ON (g.ZPS = r.gzps AND g.Mgl_Nr = r.gegner AND g.sid = r.sid) "
			." WHERE a.lid = $lid  AND a.sid = $sid AND a.heim = 1 AND r.heim = 1 "
			." AND a.runde = ".$rnd
			." AND a.dg = ".$dg
			." AND a.sid = ".$sid
			." AND a.paar = ".$paarung
			." GROUP BY a.paar ASC"
			;
	$db->setQuery($query);
	$dwzgespielt=$db->loadObjectList();
	//echo "<br>error: ".mysql_errno() . ": " . mysql_error(). "\n";
// Ergebnistext für flexibele Punktevergabe holen
	$erg_text = CLMModelMeldung::punkte_text($lid);

// Pfad für Mail
	$uri =& JFactory::getURI();
	$url = &JURI::getInstance($uri->toString());
	$pfad = $url->getHost();
 // SUBJECT
	$subject_neu = JTEXT::_('RESULT_DATA_SUBJECT_1').$fromname.' '.$mail[0]->name.': '.$begegnung[0]->name." - ".$begegnung[1]->name."  ".$summe[0]->sum.' : '.$summe[1]->sum;
 
// Mailbody TXT
	$body_msg = JText::_('RESULT_DATA_BODY1')." ".$begegnung[0]->name." - ".$begegnung[1]->name
	.JText::_('RESULT_DATA_BODY2_1').$fromname.JText::_('RESULT_DATA_BODY2_2')
	."\r\n\r\n http://$pfad/index.php?option=com_clm&view=runde&saison=$sid&liga=$lid&runde=$rnd&dg=$dg"
	.JText::_('RESULT_DATA_BODY3')
	.JText::_('RESULT_DATA_BODY4')
	."\r\n\r\n http://$pfad/index.php?option=com_clm&view=rangliste&saison=$sid&liga=$lid"
	.JText::_('RESULT_DATA_BODY5')
	;
// Mailbody - TXT Ergänzung
	$body_msg .= "\r\n\r\n "; 
	$zeile .= $mail[0]->name.', '.$rundeterm[0]->name;  
	if(isset($rundeterm[0]->datum)) { 
	  $zeile .= ' '.JText::_('ON_DAY').' '.JHTML::_('date',  $rundeterm[0]->datum, JText::_('%d. %B %Y')); }
    $body_msg .= $zeile;
	$zeile  = "\r\n "; 
	$zeile = mb_str_pad($zeile, 7, " "); 
	$zeile = mb_str_pad($zeile.$paar[0]->htln, 10, " "); 
	$zeile = mb_str_pad($zeile.$paar[0]->hname, 34, " "); 
	$zeile = mb_str_pad($zeile.'('.round($dwzgespielt[0]->dwz).')', 41, " "); 
	$zeile = mb_str_pad($zeile.$summe[0]->sum.' : '.$summe[1]->sum, 53, " "); 
	$zeile = mb_str_pad($zeile.$paar[0]->gtln, 56, " "); 
	$zeile = mb_str_pad($zeile.$paar[0]->gname, 80, " "); 
	$zeile = mb_str_pad($zeile.'('.round($dwzgespielt[0]->gdwz).')', 87, " "); 
	$body_msg .= $zeile;
	$body_msg .= "\r\n "; 
	for ($x=0; $x<$stamm; $x++) {
		$zeile = "\r\n ";
		$zeile = mb_str_pad($zeile.($x+1), 5, " "); 
		if (mb_strlen($einzel[$x]->hsnr) < 3) $zeile .= '  ';
		$zeile = mb_str_pad($zeile.$einzel[$x]->hsnr, 10, " "); 
		$zeile = mb_str_pad($zeile.$einzel[$x]->hname, 34, " "); 
		$zeile = mb_str_pad($zeile.'('.$einzel[$x]->hdwz.')', 41, " "); 
		if (mb_strlen($erg_text[$einzel[$x]->ergebnis]->erg_text) == 3) $zeile .= '   ';
		if (mb_strlen($erg_text[$einzel[$x]->ergebnis]->erg_text) == 5) $zeile .= '  ';
		$zeile = mb_str_pad($zeile.$erg_text[$einzel[$x]->ergebnis]->erg_text, 51, " "); 
		if (mb_strlen($einzel[$x]->gsnr) < 3) $zeile .= '  ';
		$zeile = mb_str_pad($zeile.$einzel[$x]->gsnr, 56, " "); 
		$zeile = mb_str_pad($zeile.$einzel[$x]->gname, 80, " "); 
		$zeile = mb_str_pad($zeile.'('.$einzel[$x]->gdwz.')', 87, " "); 
		$body_msg .= $zeile;
	}
	if ($paar[0]->comment != "") { 
		$body_msg .= "\r\n ";
		$zeile = "\r\n ".JText::_('PAAR_COMMENT_L');
		$body_msg .= $zeile;
		$zeile = $paar[0]->comment;
		$body_msg .= $zeile;
	}
// Mailbody HTML Header
	$body_html_header = '
			<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
			<title>Online Spielbericht</title>
			</head>
			<body>';
	$body_html_footer = '
			</body>
			</html>';	
// Mailbody HTML Spielbericht
	$body_html =	'
		<table width="700" border="0" cellspacing="0" cellpadding="3" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;">
		<tr>
			<td bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;" colspan="6"><div align="center" style="font-size: 12px;"><strong>Online Spielbericht vom ' .JHTML::_('date', date('Y-m-d H:i:s'), JText::_('DATE_FORMAT_CLM_PDF')). '</strong></div></td>
		</tr>
		<tr>
			<td width="120">&nbsp;</td>
			<td>&nbsp;</td>
			<td width="5">&nbsp;</td>
			<td width="5">&nbsp;</td>
			<td width="80">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="120" style="border-bottom: solid 1px #999999;"><strong>Liga:</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .$mail[0]->name. '&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="80" style="border-bottom: solid 1px #999999;"><strong>Spieltag:</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .JHTML::_('date',  $rundeterm[0]->datum, JText::_('DATE_FORMAT_CLM_F')). '&nbsp;</td>
		</tr>
		<tr>
			<td width="120" style="border-bottom: solid 1px #999999;"><strong>Heim:</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .$paar[0]->hname. '&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="80" style="border-bottom: solid 1px #999999;"><strong>Gast:</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .$paar[0]->gname. '&nbsp;</td>
		</tr>
		<tr>
			<td width="120" style="border-bottom: solid 1px #999999;"><strong>MF-Heim:</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .$paar[0]->hmf. '&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="5" style="border-bottom: solid 1px #999999;">&nbsp;</td>
			<td width="80" style="border-bottom: solid 1px #999999;"><strong>MF-Gast:</strong></td>
			<td style="border-bottom: solid 1px #999999;">' .$paar[0]->gmf. '&nbsp;</td>
		</tr>
		<tr>
			<td width="120">&nbsp;</td>
			<td>&nbsp;</td>
			<td width="5">&nbsp;</td>
			<td width="5">&nbsp;</td>
			<td width="80">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="120">&nbsp;</td>
			<td>&nbsp;</td>
			<td width="5">&nbsp;</td>
			<td width="5">&nbsp;</td>
			<td width="80">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		</table>
		
		<table width="700" border="0" cellspacing="0" cellpadding="3" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;">
		<tr>
			<td width="50" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>Brett</strong></div></td>
			<td width="75" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>Melde Nr. </strong></div></td>
			<td width="60" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>Mgl. Nr. </strong></div></td>
			<td width="210" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>Spieler</strong> (Heim)</div></td>
			<td width="75" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>Ergebnis</strong></div></td>
			<td width="75" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>Melde Nr. </strong></div></td>
			<td width="60" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>Mgl. Nr. </strong></div></td>
			<td width="215" bgcolor="#F2F2F2" style="border-bottom: solid 1px #000000; border-top: solid 1px #000000; padding: 3px;"><div align="center" style="font-size: 12px;"><strong>Spieler</strong> (Gast)</div></td>
		</tr>
	';
	for ($x=0; $x<$stamm; $x++) {
  	  $body_html .=   '
		<tr>
			<td width="50" style="border-bottom: solid 1px #999999;"><div align="center"><strong>'.($x+1).'</strong></div></td>
			<td width="75" style="border-bottom: solid 1px #999999;"><div align="center">' .$einzel[$x]->hsnr. '&nbsp;</div></td>
			<td width="60" style="border-bottom: solid 1px #999999;"><div align="center">' .str_pad($einzel[$x]->hmglnr,3,"0",STR_PAD_LEFT). '&nbsp;</div></td>
			<td width="210" style="border-bottom: solid 1px #999999;"><div align="center">' .$einzel[$x]->hname. '&nbsp;</div></td>
			<td width="75" style="border-bottom: solid 1px #999999; border-left: solid 1px #999999; border-right: solid 1px #999999;"><div align="center">' .$erg_text[$einzel[$x]->ergebnis]->erg_text. '&nbsp;</div></td>
			<td width="75" style="border-bottom: solid 1px #999999;"><div align="center">' .$einzel[$x]->gsnr. '&nbsp;</div></td>
			<td width="60" style="border-bottom: solid 1px #999999;"><div align="center">' .str_pad($einzel[$x]->gmglnr,3,"0",STR_PAD_LEFT). '&nbsp;</div></td>
			<td width="215" style="border-bottom: solid 1px #999999;"><div align="center">' .$einzel[$x]->gname. '&nbsp;</div></td>
		</tr>
	  ';
	}
	$body_html .= 	  '
		<tr>
			<td width="50"><div align="center"></div></td>
			<td width="75"><div align="center"></div></td>
			<td width="60"><div align="center"></div></td>
			<td width="210"><div align="right"><strong>Gesamtergebnis: </strong></div></td>
			<td style="border-bottom: solid 1px #999999; border-left: solid 1px #999999; border-right: solid 1px #999999;" width="75"><div align="center" style="color:#FF0000"><strong>' .$summe[0]->sum.' : '.$summe[1]->sum. '&nbsp;</strong></div></td>
			<td width="75"><div align="center"></div></td>
			<td width="60"><div align="center"></div></td>
			<td width="215">&nbsp;</td>
		</tr>
		</table>
		<table width="700" border="0" cellspacing="0" cellpadding="3" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;">
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	';
	if ($paar[0]->comment != "") { 
		$paar[0]->comment = ereg_replace('
','<br>',$paar[0]->comment);

      $body_html .= 	'
		<tr>
			<td width="80" valign="top"><strong>'.JText::_('PAAR_COMMENT_L').'</strong></td>
			<td  width="420" nowrap="nowrap" valign="top" size="1">
				<textarea cols="30" rows="2" style="width:90%">'.str_replace('&','&amp;',$paar[0]->comment).'</textarea>
			</td>
  		</tr>
	  ';
	}
	$body_html .= 	  '
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="80" valign="top"><strong>Ergebnismelder:</strong></td>
			<td>' .$paar[0]->melder. '&nbsp;</td>
		</tr>
	
		</table>
	';
// BCC
	// Konfigurationsparameter auslesen
	//$config		= &JComponentHelper::getParams( 'com_clm' );
	$bcc_mail	= $config->get('bcc');
	$sl_mail	= $config->get('sl_mail',0);

	if($sl_mail =="1") {
	$query	= "SELECT sl,sl_mail,u.email FROM #__clm_liga a"
		." LEFT JOIN #__clm_user as u ON u.sid = a.sid AND u.jid = a.sl"
		." WHERE a.sid = ".$sid
		." AND a.id = ".$lid
		;
	$db->setQuery($query);
	$slmail	= $db->loadObjectList();
	$sl_bcc	= $slmail[0]->email;
			}
	// Mailbody HTML ML
	$body_html_mf = '
	  <table width="700" border="0" cellspacing="0" cellpadding="3" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;">
		<tr>
		  <td>'.JText::_('RESULT_DATA_BODY1')." ".$begegnung[0]->name." - ".$begegnung[1]->name
			.JText::_('RESULT_DATA_BODY2_1').$fromname;
	if (isset($id[0]->gemeldet)) $body_html_mf .= JText::_('RESULT_DATA_BODY2_2A'); else $body_html_mf .= JText::_('RESULT_DATA_BODY2_2');
	$body_html_mf .= ' siehe unten oder <a href="http://'.$pfad.'/index.php?option=com_clm&view=runde&saison='.$sid.'&liga='.$lid.'&runde='.$rnd.'&dg='.$dg.'">hier</a>
		  </td>
		</tr>
		<tr>
		  <td><a href="mailto:'.$sl_bcc.'">'.JText::_('RESULT_DATA_BODY3').'</a></td>
		</tr>
		<tr>
 		  <td>'.JText::_('RESULT_DATA_BODY4').'<a href="http://'.$pfad.'/index.php?option=com_clm&view=rangliste&saison='.$sid.'&liga='.$lid.'"> Liga</a></td>
  		</tr>
		<tr>
		  <td>'.JText::_('RESULT_DATA_BODY5').'</td>
		</tr>
		</table>
	';

	//echo "<br><br>body_html_mf:"; var_dump($body_html_mf);
 
if (!$empfang[0]->email) {
	JError::raiseWarning( 500, JText::_( '<h2>Es sind KEINE Email Adressen für die Mannschaftsführer hinterlegt. Keine Email verschickt !</h2>') );
	}
if (!$empfang[1]->email AND $empfang[0]->tln_nr == $htln) {
	JError::raiseWarning( 500, JText::_(RESULT_NO_MAIL_GUEST).'</h2>');
	
	// Mail Heim
	$recipient = $empfang[0]->email;
	$body_name1 = JText::_('RESULT_NAME').$empfang[0]->empfang.",";
		$body = $body_html_header.$body_name1.$body_html_mf.$body_html.$body_html_footer;
	}
if (!$empfang[1]->email AND $empfang[0]->tln_nr == $gtln) {
	JError::raiseWarning( 500, '<h2>'.JText::_(RESULT_NO_MAIL_HOME).'</h2>');
	
	// Mail Gast
	$recipient = $empfang[0]->email;
	$body_name1 = JText::_('RESULT_NAME').$empfang[0]->empfang.",";
		$body = $body_html_header.$body_name1.$body_html_mf.$body_html.$body_html_footer;
	}

if ($empfang[0]->email AND $empfang[1]->email ) {
	$body_name1 = JText::_('RESULT_NAME').$empfang[0]->empfang.",";
	$body_name2 = JText::_('RESULT_NAME').$empfang[1]->empfang.",";
	// Mail Heim
	$body = $body_html_header.$body_name1.$body_html_mf.$body_html.$body_html_footer;
	$recipient = $empfang[0]->email;
	JUtility::sendMail ($from,$fromname,$recipient,$subject_neu,$body,1,$cc,$bcc);
	// Mail Gast
	$body = $body_html_header.$body_name2.$body_html_mf.$body_html.$body_html_footer;
	$recipient = $empfang[1]->email;
	}
	JUtility::sendMail ($from,$fromname,$recipient,$subject_neu,$body,1,$cc,$bcc);
	//		}

	jimport( 'joomla.mail.helper' );
	// Mail Admin
	if ($bcc_mail != "") {
		// Text Admin
		$body_html_ad = '<br>'
			.JText::_('RESULT_ADMIN_COPY1')
			.'<br>'.JText::_('RESULT_ADMIN_COPY2')." ".$begegnung[0]->name." - ".$begegnung[1]->name
			.'<br>'.JText::_('RESULT_ADMIN_COPY3_1').$fromname;
		if (isset($id[0]->gemeldet)) $body_html_ad .= JText::_('RESULT_ADMIN_COPY3_2A'); else $body_html_ad .= JText::_('RESULT_ADMIN_COPY3_2');
		$body_html_ad .= '<br>'.'<br>'
		;

		$body = $body_html_header.$body_html_ad.$body_html.$body_html_footer;
	JUtility::sendMail ($from,$fromname,$bcc_mail,$subject_neu,$body,1);
	}

	// Mail Staffelleiter
	if($sl_mail =="1" AND $slmail[0]->sl_mail !="") {
		// Text Staffelleiter
		$body_html_sl = '<br>'
			.JText::_('RESULT_SL_COPY1')
			.'<br>'.JText::_('RESULT_ADMIN_COPY2')." ".$begegnung[0]->name." - ".$begegnung[1]->name
			.'<br>'.JText::_('RESULT_ADMIN_COPY3_1').$fromname;
		if (isset($id[0]->gemeldet)) $body_html_ad .= JText::_('RESULT_ADMIN_COPY3_2A'); else $body_html_ad .= JText::_('RESULT_ADMIN_COPY3_2');
		$body_html_ad .= '<br>'.'<br>'
	;
		$body = $body_html_header.$body_html_sl.$body_html.$body_html_footer;
	
	JUtility::sendMail ($from,$fromname,$sl_bcc,$subject_neu,$body,1);
	}
	}
	// Log
	$date		= & JFactory::getDate();
	$now		= $date->toMySQL();
	$user		= & JFactory::getUser();
	$jid_aktion	=  ($user->get('id'));
	$aktion		= "Ergebnis FE";

	$query	= "INSERT INTO #__clm_log "
		." ( `aktion`, `jid_aktion`, `sid` , `lid` ,`rnd`,`paar`,`dg`, `datum`) "
		." VALUES ('$aktion','$jid_aktion','$sid','$lid','$rnd','$paarung','$dg','$now') "
		;
	$db->setQuery($query);
	$db->query();

	// Auswertung der DWZ bei aktivierter Option !
	// Konfigurationsparameter auslesen
	$config		= &JComponentHelper::getParams( 'com_clm' );
	$dwz_fe		= $config->get('dwz_fe',0);

	if($dwz_fe =="1") {
	CLMModelMeldung::dwz($sid,$lid);
	}
	// Ende DWZ


	$msg = "<h1>".JText::_('RESULT_TIP_POSITIV_SH')."</h1>";
if ( $mail[0]->mail > 0 ) {
	$msg = "<h1>".JText::_('RESULT_TIP_POSITIV')."</h1>";
			}
	//$link = 'index.php?option='.$option.'&view=runde&liga='.$lid.'&runde='.$rnd.'&saison='.$sid.'&dg='.$dg.'&Itemid='.$itemid;
	$link = 'index.php/component/clm/?view=runde&liga='.$lid.'&runde='.$rnd.'&saison='.$sid.'&dg='.$dg.'&Itemid='.$itemid;
	$mainframe->redirect( $link, $msg );
/*	}
else {
	$msg = "<h1>".JText::_('RESULT_TIP_NEGATIV')."</h1>";
	JError::raiseWarning( 500, $msg);	
	//$link = 'index.php?option='.$option.'&view=runde&liga='.$lid.'&runde='.$rnd.'&saison='.$sid.'&dg='.$dg.'&Itemid='.$itemid;
	$link = 'index.php/component/clm/?view=runde&liga='.$lid.'&runde='.$rnd.'&saison='.$sid.'&dg='.$dg.'&Itemid='.$itemid;
	$mainframe->redirect( $link, $msg );
} */
} ?>


