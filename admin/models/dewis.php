<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class CLMModelDewis extends JModel {

	var $_swtFiles;

function __construct(){
		parent::__construct();
	}


function dewis_verein($zps) {

	// Check for request forgeries
	//JRequest::checkToken() or die( 'Invalid Token' );

	$db	=& JFactory::getDBO();
	$app	= JFactory::getApplication();

	// Dewis Tabellen leeren
	$sql = " DELETE FROM #__clm_dwz_dewis "
		." WHERE zps = '$zps'"
		;
	$db->setQuery( $sql);
	$db->query();
	
	$sql = " DELETE FROM #__clm_dwz_dewis_merge "
		." WHERE zps = '$zps'"
		;
	$db->setQuery( $sql);
	$db->query();

		try
		{
		$client = new SOAPClient( "https://dwz.svw.info/services/files/dewis.wsdl" );
		/*$client = new SOAPClient(
			NULL,
			array(
				'location' => 'https://dwz.svw.info/services/soap/index.php',
				'uri'      => 'https://soap',
				'style'    => SOAP_RPC,
				'use'      => SOAP_ENCODED
				)
			);
*/
		// VKZ des Vereins
		$unionRatingList = $client->unionRatingList($zps);

		
		foreach ($unionRatingList->members as $m) {

		$tcard = $client->tournamentCardForId($m->pid);
			$sql = " INSERT INTO #__clm_dwz_dewis (`pkz`,`nachname`, `vorname`,`zps`,`mgl_nr`, `dwz` ,`dwz_index` ,`status` "
				." ,`geschlecht`,`geburtsjahr`,`fide_elo`,`fide_land`,`fide_id`) VALUES"
				." ('$m->pid','$m->surname','$m->firstname','$zps','$m->membership','$m->rating','$m->ratingIndex','$m->state' "
				." ,'".$tcard->member->gender."' "
				." ,'".$tcard->member->yearOfBirth."' ,'".$tcard->member->elo."' "
				." ,'".$tcard->member->fideNation."' ,'".$tcard->member->idfide."' "
				//." ,'1','2','3','','' "
				." )"
				;
				$db->setQuery($sql);
				$db->query();
				$sql = " INSERT INTO #__clm_dwz_dewis_merge (`zps`,`mgl`) VALUES ('$zps','$m->membership')" ;
				$db->setQuery($sql);
				$db->query();

		}
		}
	catch (SOAPFault $f) {  print $f->faultstring;  }
	
	// aktuelle Saison finden
	$sql = " SELECT * FROM #__clm_saison "
		." WHERE published = 1 AND archiv = 0"
		." ORDER BY id ASC LIMIT 1 "
		;
	$db->setQuery( $sql);
	$sid_data=$db->loadObjectList();
	$sid = $sid_data[0]->id;
	
	// Spieler aus der CLM DB holen
	$sql = " SELECT CAST(Mgl_Nr AS DECIMAL) as Mgl_Nr FROM #__clm_dwz_spieler as a"
		." WHERE ZPS = '$zps'"
		." AND sid = ".$sid
		;
	$db->setQuery( $sql);
	$data=$db->loadObjectList();
	
	foreach ($data as $merge) {

	$clm_mgl = (int)$merge->Mgl_Nr;
	$sql = " REPLACE INTO #__clm_dwz_dewis_merge (`zps`,`mgl`) VALUES ('$zps','$clm_mgl')" ;
	$db->setQuery($sql);
	$db->query();
	}
	
	$sql = " SELECT a.zps as mzps, a.mgl as mmgl, "
		." d.pkz as dpkz, CONCAT(d.nachname,',', d.vorname) as dname, d.geschlecht as dgeschlecht, d.zps as dzps, "
		." d.mgl_nr as dmgl, d.dwz as ddwz, d.dwz_index as dindex, d.status as dstatus, "
		." s.PKZ as spkz, s.Spielername as sname, s.ZPS as szps, s.Mgl_nr as smgl, s.DWZ as sdwz, s.DWZ_Index as sindex, s.Status as sstatus "
		." FROM #__clm_dwz_dewis_merge as a "
		." LEFT JOIN #__clm_dwz_spieler as s ON a.zps = s.ZPS  AND CAST(a.mgl AS DECIMAL)= CAST(s.Mgl_Nr AS DECIMAL) "
		." AND s.sid =".$sid
		." LEFT JOIN #__clm_dwz_dewis as d ON a.zps = d.zps AND CAST(a.mgl AS DECIMAL)= CAST(d.mgl_nr AS DECIMAL) "
		." WHERE a.zps = '$zps' " 
		." GROUP BY a.mgl "
		." ORDER BY a.mgl "
		;
	$db->setQuery( $sql);
	$merge_data=$db->loadObjectList();
	//$exist = $exist_id[0]->id;
	
	return $merge_data;
	}

function update_verein($zps) {
	/*
	$app	= JFactory::getApplication();
	$db =& JFactory::getDBO();
	
	$sql = " SELECT sid, Vereinname FROM #__clm_dwz_vereine "
		." WHERE ZPS = '".$zps."'"
		." ORDER BY sid DESC LIMIT 1 "
		;
		$db->setQuery( $sql);
	$sid_data=$db->loadObjectList();
	$name = $sid_data[0]->Vereinname;

	$app->enqueueMessage( 'Update Verein Modell ! '.$zps, 'warning');
	return $name;
	*/
	}

function verein_detail() {
	
	$app	= JFactory::getApplication();
	$jinput = $app->input;
	$zps	= $jinput->get('zps', null, null);

	try { $client = new SOAPClient( "https://dwz.svw.info/services/files/dewis.wsdl" );

		$unionRatingList = $client->unionRatingList($zps);

	}
	catch (SOAPFault $f) {  print $f->faultstring; $unionRatingList="";}
	
	return $unionRatingList;
}  

function spieler_suchen() {
	
	$app	= JFactory::getApplication();
	$jinput = $app->input;
	$name	= $jinput->get('name', null, null);
	$teile	= explode(",", $name);

	$merge_data = '';

	if((count($teile)) >1) { 
		$vorname = trim($teile[0]);
		$nachname = trim($teile[1]);
	}else{ 
		$vorname = trim($teile[0]);
		$nachname = '';
	}

	try { $client = new SOAPClient( "https://dwz.svw.info/services/files/dewis.wsdl" );

	$members = $client->searchByName($vorname,$nachname);
	  }
	catch (SOAPFault $f) { print $f->faultstring; $members="";}

	return $members;
}
  
  
function spieler_detail() {

	$app	= JFactory::getApplication();
	$jinput = $app->input;
	$pkz	= $jinput->get('pkz', null, null);

	try { $client = new SOAPClient( "https://dwz.svw.info/services/files/dewis.wsdl" );

	$tcard = $client->tournamentCardForId($pkz);
	}
	catch (SOAPFault $f) {  print $f->faultstring; $tcard =""; }
	
	return $tcard;
}
  
  
function turnier_suchen() {
	
	$app	= JFactory::getApplication();
	$jinput = $app->input;
	$turnier= $jinput->get('turnier', null, null);
	$sdatum	= $jinput->get('sdate', null, null);
	$edatum	= $jinput->get('edate', null, null);

	try { $client = new SOAPClient( "https://dwz.svw.info/services/files/dewis.wsdl" );
	
		$result = $client->tournamentsByPeriod($sdatum,$edatum,"000", true, "",$turnier );
	}
	catch (SOAPFault $f) {  print $f->faultstring; $result =""; }
	
	return $result;
}  
 

function turnier_detail() {
	
	$app	= JFactory::getApplication();
	$jinput = $app->input;
	$turnier= $jinput->get('turnier', null, null);

	try { $client = new SOAPClient( "https://dwz.svw.info/services/files/dewis.wsdl" );
	
		$tournament = $client->tournamentPairings($turnier);
	  }
	catch (SOAPFault $f) {  print $f->faultstring; $tournament =""; }
	
	return $tournament;
}  


function turnier_auswertung() {
	
	$app	= JFactory::getApplication();
	$jinput = $app->input;
	$turnier= $jinput->get('turnier', null, null);

	try { $client = new SOAPClient( "https://dwz.svw.info/services/files/dewis.wsdl" );
	
		$tournament = $client->tournament($turnier);
	  }
	catch (SOAPFault $f) {  print $f->faultstring; $tournament =""; }
	
	return $tournament;
}  


	
function verein_name($zps) {
	$db =& JFactory::getDBO();
	
	$sql = " SELECT sid, Vereinname FROM #__clm_dwz_vereine "
		." WHERE ZPS = '".$zps."'"
		." ORDER BY sid DESC LIMIT 1 "
		;
		$db->setQuery( $sql);
	$sid_data=$db->loadObjectList();
	$name = $sid_data[0]->Vereinname;

	return $name;
	}
	

function liga_filter() {
	$db =& JFactory::getDBO();	
	// Ligafilter
	$sql = 'SELECT d.id AS cid, d.name FROM #__clm_liga as d'
		." LEFT JOIN #__clm_saison as s ON s.id = d.sid"
		." WHERE s.archiv = 0 ";
	$db->setQuery($sql);
	$ligalist[]	= JHTML::_('select.option',  '0', JText::_( 'MANNSCHAFTEN_LIGA' ), 'cid', 'name' );
	$ligalist	= array_merge( $ligalist, $db->loadObjectList() );
	$lists['lid']	= JHTML::_('select.genericlist', $ligalist, 'filter_lid', 'class="inputbox" size="1" onchange=""','cid', 'name', '' );
	
	return $lists['lid'];
	}
	
function vereine_filter() {

	require_once(JPATH_COMPONENT.DS.'controllers'.DS.'filter_vereine.php');
		$vlist	= CLMFilterVerein::vereine_filter(0);
		$lists['vid']	= JHTML::_('select.genericlist', $vlist, 'filter_vid', 'class="inputbox"','zps', 'name', '' );
	
	return $lists['vid'];
	}

}

?>