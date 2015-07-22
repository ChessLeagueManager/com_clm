<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2013 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class CLMModelDB extends JModel {

function __construct(){
		parent::__construct();
	}

function json_update() {
	$db 	=& JFactory::getDBO();
	$app	= JFactory::getApplication();
	$jinput = $app->input;

	$start_data	= $jinput->get('start', null, null);
	$verband	= $jinput->get('verband', null, null);
	$incl_pd 	= $jinput->get('incl_pd', 0, 0);
	$sid		= CLM_SEASON;

	if ($start_data > 0)	{
		$s_provide	= $jinput->get('provide', 0, 0);
		$s_update	= $jinput->get('update', 0, 0);
		$s_insert	= $jinput->get('insert', 0, 0);
		$msg		= $jinput->get('msg', '', '');
		$t_beginn	= $jinput->get('beginn', '', '');
	} else {	
		$s_provide  = 0;
		$s_update   = 0;
		$s_insert   = 0;
		$msg		= '';
		$t_beginn = microtime(true); 
	} 
 
 // Verbandseingliederung ermitteln
	if (substr($verband,1,2) == '00') {   		// ausgewählter Verband ist Landesverband 
		$vug = $verband;
		$vog = substr($verband,0,1).'ZZ';
	} else {								
		$sql = " SELECT * FROM dwz_verbaende "
			." WHERE Uebergeordnet = '$verband' "
			;
		$db->setQuery( $sql);
		$averbaende=$db->loadObjectList();

		if (count($averbaende) == 0) {     	// ausgewählter Verband ist Schachbezirk der untersten Ebene und enthält Vereine
			$vug = $verband;
			$vog = $verband;
		} else {				// ausgewählter Verband ist Gebietsverband enthält direkt keine Vereine
			$vug = $verband;
			$vog = substr($verband,0,2).'Z';
		}
	}
	$php_array['vug'] = $vug;
	$php_array['vog'] = $vog;

	// Vereine aus der CLM Vereinstabelle holen
	$sql = " SELECT * FROM #__clm_dwz_vereine "
		." WHERE Verband >= '$vug' AND Verband <= '$vog' "
		." AND sid = $sid "
		." ORDER BY ZPS "
		;
	$db->setQuery( $sql);
	$vereine=$db->loadObjectList();
	
	$php_array['error'] = 0; 	
	if (count($vereine) == 0) {
		//JError::raiseWarning( 500, JText::_( 'DWZ_CLUBS_NO')." ( ".$verband." )" );
		//$link = 'index.php?option='.$option.'&view=db';
		$msg = JText::_( 'DWZ_CLUBS_NO')." ( ".$verband." )";
		$php_array['error'] = 1; 	
		$php_array['msg'] = $msg;
		return $php_array;
	}

	 $php_array['counter'] = count($vereine); 	
	
	$php_array['start'] = 1 + $start_data;
	$php_array['verband'] = $vereine[$start_data]->ZPS.' - '.$vereine[$start_data]->Vereinname;
	$php_array['status'] = round(($start_data + 1)/($php_array['counter'])*100);
	$php_array['incl_pd'] = $incl_pd;
	
	//$zufall = rand(1,3); // Zufällige länge der Aktion simulieren
	//sleep($zufall); 
	
	//----------------------------
		$zps = $vereine[$start_data]->ZPS;
		$v_beginn = microtime(true); 
		// Dewis Tabelle leeren
		$sql = " DELETE FROM #__clm_dwz_dewis "
			." WHERE zps = '$zps'"
			;
		$db->setQuery( $sql);
		$db->query();


		// SOAP Webservice
		try {
			$client = new SOAPClient( "https://dwz.svw.info/services/files/dewis.wsdl" );
 
			// VKZ des Vereins --> Vereinsliste
			$unionRatingList = $client->unionRatingList($zps);
		
			// Detaildaten zu Mitgliedern lesen
			foreach ($unionRatingList->members as $m) {
				if ($m->state == 'P' AND $incl_pd == 1) continue;
				$tcard = $client->tournamentCardForId($m->pid);
				$sql = " INSERT INTO #__clm_dwz_dewis (`pkz`,`nachname`, `vorname`,`zps`,`mgl_nr`, `dwz` ,`dwz_index` ,`status` "
					." ,`geschlecht`,`geburtsjahr`,`fide_elo`,`fide_land`,`fide_id`) VALUES"
					." ('$m->pid','$m->surname','$m->firstname','$zps','$m->membership','$m->rating','$m->ratingIndex','$m->state' "
				." ,'".$m->gender."' "
				." ,'".$m->yearOfBirth."' ,'".$m->elo."' "
				." ,'".$tcard->member->fideNation."' ,'".$m->idfide."' "
					." )"
				;
				$db->setQuery($sql);
				$db->query();
				unset($tcard);
			}
			unset($client);
			unset($unionRatingList);
		}
		catch (SOAPFault $f) {  //print $f->faultstring;  
			$clmLog = new CLMLog();
			$clmLog->aktion = "DWZ-Update Error";
			$clmLog->params = array('sid' => $sid, 'zps' => $zps, 'cids' => 'error:'.$f->faultstring);
			$clmLog->write();
		}
		
		// Spieler aus der CLM DEWIS Tabelle holen
		$sql = " SELECT a.* FROM #__clm_dwz_dewis as a"
			." WHERE a.ZPS = '$zps'"
			;
		$db->setQuery( $sql);
		$dsbdaten=$db->loadObjectList();

		$c_update = 0;
		$c_insert = 0;
		$c_provide = 0;

		foreach($dsbdaten as $value)
		{	$c_provide++;
			$dsbid = $value->pkz;
			$dsbnachname = $value->nachname;
			$dsbvorname = $value->vorname;
			$dsbdwz = $value->dwz;
			$dsbdwzindex = $value->dwz_index;
			$dsbzps = $value->zps;
			$dsbmglnr = $value->mgl_nr;
			$dsbstatus = $value->status;
			$dsbgeschlecht = $value->geschlecht;
			$dsbgeburtsjahr = $value->geburtsjahr;
			$dsbfideid = $value->fide_id;
			$dsbfideelo = $value->fide_elo;
			$dsbfideland = $value->fide_land;

			// Die Mitgliedsnummer müssen mindestens dreistellig sein, mit führenden Nullen auffüllen
			if (strlen ($dsbmglnr) == 1) {
				$dsbmglnr= "00" . $dsbmglnr;
			} elseif (strlen ($dsbmglnr) == 2) {
				$dsbmglnr= "0" . $dsbmglnr;
			}
			// Falls Namensänderungen anliegen (Heirat)
			$name = $dsbnachname.",".$dsbvorname;
			$name_g = strtoupper($name);
			$search = array("ä", "ö", "ü", "ß", "é");
			$replace = array("AE", "OE", "UE", "SS", "É");
			$name_g =  str_replace($search, $replace, $name_g);
			if ($dsbgeschlecht == 'm') $dsbgeschlecht = 'M';
			if ($dsbgeschlecht == 'f') $dsbgeschlecht = 'W';
			if ($dsbfideid == '' OR $dsbfideid == '0') $dsbfideland = '';
	
			// Prüfen ob Mitgliedsnummer schon vergeben wurde
			$query	= "SELECT Mgl_Nr FROM #__clm_dwz_spieler "
				." WHERE ZPS ='$zps'"
				." AND sid = '$sid'"
				." AND Mgl_Nr = '$dsbmglnr'"
				;
			$db->setQuery($query);
			$mgl_exist = $db->loadObjectList();

			if(isset($mgl_exist[0])) {
			  //DWZ-Updaten
			  if ($dsbdwz != '0')	
				$query	= "UPDATE #__clm_dwz_spieler "
					." SET DWZ = '$dsbdwz' "
					." , DWZ_Index = '$dsbdwzindex' "
					." , PKZ = '$dsbid' "
					." , Spielername = '$name' "
					." , Spielername_G = '$name_g' "
					." , Geschlecht = '$dsbgeschlecht' "
					." , Geburtsjahr = '$dsbgeburtsjahr' "
					." , FIDE_Elo = '$dsbfideelo' "
					." , FIDE_Land = '$dsbfideland' "
					." , FIDE_ID = '$dsbfideid' "
					." , Status = '$dsbstatus' "
					." WHERE ZPS = '$dsbzps' "
					." AND sid = '$sid' "
					." AND Mgl_Nr = '$dsbmglnr' "
				;
			  else	
				$query	= "UPDATE #__clm_dwz_spieler "
					." SET DWZ = NULL "
					." , DWZ_Index = NULL "
					." , PKZ = '$dsbid' "
					." , Spielername = '$name' "
					." , Spielername_G = '$name_g' "
					." , Geschlecht = '$dsbgeschlecht' "
					." , Geburtsjahr = '$dsbgeburtsjahr' "
					." , FIDE_Elo = '$dsbfideelo' "
					." , FIDE_Land = '$dsbfideland' "
					." , FIDE_ID = '$dsbfideid' "
					." , Status = '$dsbstatus' "
					." WHERE ZPS = '$dsbzps' "
					." AND sid = '$sid' "
					." AND Mgl_Nr = '$dsbmglnr' "
					;
			$db->setQuery($query);
			$db->query();
			if (mysql_errno() == 0) $c_update++;
		} else {
			//Neu
			if ($dsbdwz != '0')	
				$query	= "INSERT INTO #__clm_dwz_spieler"
					." ( `sid`,`ZPS`, `Mgl_Nr`, `PKZ`, `Spielername`, `DWZ`, `DWZ_Index`, `Spielername_G`"
					." , `Geschlecht`, `Geburtsjahr`, `FIDE_Elo`, `FIDE_Land`, `FIDE_ID`, `Status`) "
					." VALUES ('$sid','$dsbzps','$dsbmglnr','$dsbid','$name','$dsbdwz','$dsbdwzindex','$name_g'"
					." ,'$dsbgeschlecht','$dsbgeburtsjahr','$dsbfideelo','$dsbfideland','$dsbfideid','$dsbstatus')"
					;
			else	
				$query	= "INSERT INTO #__clm_dwz_spieler"
					." ( `sid`,`ZPS`, `Mgl_Nr`, `PKZ`, `Spielername`, `DWZ`, `DWZ_Index`, `Spielername_G`"
					." , `Geschlecht`, `Geburtsjahr`, `FIDE_Elo`, `FIDE_Land`, `FIDE_ID`, `Status`) "
					." VALUES ('$sid','$dsbzps','$dsbmglnr','$dsbid','$name',NULL,NULL,'$name_g'"
					." ,'$dsbgeschlecht','$dsbgeburtsjahr','$dsbfideelo','$dsbfideland','$dsbfideid','$dsbstatus')"
					;
			$db->setQuery($query);
			$db->query();
			if (mysql_errno() == 0) {
				$c_insert++;
				$s_insert++;
				if ($s_insert < 9) $msg .= "/n/n Neues Mitglied: ".$dsbzps." / ".$dsbmglnr. " - ".$name;
			}
		}	
	}
	unset($dsbdaten);

  	// Log schreiben
	$d1 = round((microtime(true) - $v_beginn),1); 
	$d2 = round((microtime(true) - $t_beginn),1); 
	$s_provide	+= $c_provide; 
	$s_update	+= $c_update;
 	$clmLog = new CLMLog();
 	$clmLog->aktion = "DWZ-Update Verein";
 	$clmLog->params = array('sid' => $sid, 'zps' => $zps, 'cids' => 'g:'.$c_provide.',u:'.$c_update.',i:'.$c_insert.', t:'.$d1.'/'.$d2);
 	$clmLog->write();

	$php_array['provide'] = $s_provide;
	$php_array['update'] = $s_update;
	$php_array['insert'] = $s_insert;
	$php_array['beginn'] = $t_beginn;


	if (($start_data + 1) == count($vereine)) {
	  $d1 = round((microtime(true) - $t_beginn),1); 
 	  $clmLog = new CLMLog();
 	  $clmLog->aktion = "DWZ-Update Verband";
 	  $clmLog->params = array('sid' => $sid, 'zps' => $verband, 'cids' => 'g:'.$s_provide.',u:'.$s_update.',i:'.$s_insert.', t:'.$d1);
 	  $clmLog->write();
	  $msg =  str_replace('/n/n', '<br>', $msg);
	  if ($s_insert > 8) $msg .= "<br>und weitere ".($s_insert - 8)." neue Mitglieder";
      $msg = JText::_( 'DWZ_SPIELER_UPDATE' ).' ( '.count($vereine).' Vereine, siehe Logfile )'.$msg;
	}
	$php_array['msg'] = $msg;
	//----------------------------
	return $php_array;
	}
	
}
?>