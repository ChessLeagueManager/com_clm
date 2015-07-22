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

defined('_JEXEC') or die();
jimport('joomla.application.component.model');

class CLMModelMeldung extends JModel
{
	function _getCLMLiga( &$options )
	{
	$liga	= JRequest::getInt('liga');
	$sid	= JRequest::getInt('saison');
	$runde	= JRequest::getInt('runde');
	$dg	= JRequest::getInt('dg');        //klkl

		$db	= JFactory::getDBO();
		$id	= @$options['id'];
 
		$query = "SELECT a.*,t.datum FROM #__clm_liga as a"
			//." LEFT JOIN #__clm_runden_termine AS t ON t.liga = a.id AND t.nr = ".$runde
			." LEFT JOIN #__clm_runden_termine AS t ON t.liga = a.id AND t.nr = ($runde + ($dg -1) * a.runden)"  //klkl
			." WHERE a.id = ".$liga
			." AND a.sid = ".$sid
			;

		return $query;
	}

	function getCLMLiga( $options=array() )
	{
		$query	= $this->_getCLMLiga( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMPaar ( &$options )
	{
	$liga	= JRequest::getInt('liga');
	$sid	= JRequest::getInt('saison');
	$runde	= JRequest::getInt('runde');
	$tln_nr	= JRequest::getInt('tln');
	$dg	= JRequest::getInt('dg');

		$db	= JFactory::getDBO();
		$id	= @$options['id'];
/*
		$query = " SELECT teil FROM #__clm_liga "
			." WHERE id = ".$liga
			." AND sid = ".$sid
			;
		$db->setQuery( $query);
		$row_tln=$db->loadObjectList();
	if(isset($row->tln)){
		$tln	= $row_tln[0]->teil;
		}
*/
	$query = " SELECT a.*,g.id as gid, g.name as gname, g.tln_nr as gtln,"
		." h.id as hid, h.name as hname, h.tln_nr as htln, "
		." g.zps as gzps, g.sg_zps as gsgzps, g.mf as gast_mf, "
		." h.zps as hzps, h.sg_zps as hsgzps, h.mf as heim_mf "
		." FROM #__clm_rnd_man as a "
		." LEFT JOIN #__clm_mannschaften AS g ON g.tln_nr = a.gegner "
		." LEFT JOIN #__clm_mannschaften AS h ON h.tln_nr = a.tln_nr "
		." WHERE a.runde = ".$runde 
		." AND a.lid = ".$liga
		." AND a.sid = ".$sid
		." AND ( a.tln_nr = $tln_nr OR a.gegner = $tln_nr )"
		." AND a.heim = 1 "
		." AND a.dg = ".$dg
		." AND g.liga = ".$liga
		." AND g.sid = ".$sid
		." AND h.liga = ".$liga
		." AND h.sid = ".$sid
		;
		return $query;
	}

	function getCLMPaar ( $options=array() )
	{
		$query	= $this->_getCLMPaar( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMCountHeim ( &$options )
	{
	$liga	= JRequest::getInt('liga');
	$sid	= JRequest::getInt('saison');
	$runde	= JRequest::getInt('runde');
	$paar	= JRequest::getInt('paar');
	$dg	= JRequest::getInt('dg');

		$db	= JFactory::getDBO();
		$id	= @$options['id'];

	$row =& JTable::getInstance( 'ligen', 'TableCLM' );
	$row->load( $liga );

/*
	$query = " SELECT COUNT(a.id) as count "
		." FROM #__clm_rnd_man as a  "
		." LEFT JOIN #__clm_mannschaften  as m ON ( m.tln_nr = a.tln_nr AND m.liga = a.lid AND m.sid = a.sid) ";
		if($row->rang !="0") {
		$query = $query
			." LEFT JOIN #__clm_meldeliste_spieler  as n ON ( ( n.zps = m.zps OR n.zps = m.sg_zps) AND n.sid = a.sid) ";
				}
		else {
		$query = $query
		." LEFT JOIN #__clm_meldeliste_spieler  as n ON ( ( n.zps = m.zps OR n.zps = m.sg_zps) AND n.mnr = m.man_nr AND n.sid = a.sid) ";
			}
		$query = $query
			." WHERE a.sid = ".$sid
			." AND a.lid = ".$liga
			." AND a.runde = ".$runde
			." AND a.paar = ".$paar
			." AND a.dg = ".$dg
			." AND a.heim = 1 AND n.mgl_nr >0 ";
		if($row->rang !="0") {
		$query = $query
			." AND n.mnr = m.man_nr AND n.status > 0 ";
				}
*/
///////////////////////////
///////////////////////////

	$data = "SELECT a.gemeldet,a.editor, a.id,a.sid, a.lid, a.runde, a.dg, a.tln_nr,"
		." a.gegner,a.paar, a.dwz_zeit, a.dwz_editor,  "
		." a.zeit, a.edit_zeit,  "
		." m.name as hname,m.zps as hzps,m.man_nr as hmnr,m.sg_zps as sgh_zps, "
		." n.name as gname, n.zps as gzps, n.man_nr as gmnr, n.sg_zps as sgg_zps, "
		." l.name as lname, l.stamm, l.ersatz, l.sl as sl, l.rang, l.id as lid"
		." FROM #__clm_rnd_man as a "
		." LEFT JOIN #__clm_liga AS l ON (l.id = a.lid ) "
		." LEFT JOIN #__clm_mannschaften AS m ON (m.liga = a.lid AND m.tln_nr = a.tln_nr) AND m.sid = a.sid "
		." LEFT JOIN #__clm_mannschaften AS n ON (n.liga = a.lid AND n.tln_nr = a.gegner) AND n.sid = a.sid "
			." WHERE a.sid = ".$sid
			." AND a.lid = ".$liga
			." AND a.runde = ".$runde
			." AND a.paar = ".$paar
			." AND a.dg = ".$dg
			." AND a.heim = 1  ";
	$db->setQuery( $data);
	$runde		= $db->loadObjectList();

	$sql = "SELECT COUNT(a.snr) as count"
		." FROM #__clm_meldeliste_spieler as a "
		." LEFT JOIN #__clm_rangliste_spieler as r ON ( r.ZPS = a.zps AND r.Mgl_Nr= a.mgl_nr AND r.sid = a.sid AND a.status = r.Gruppe ) "
		." WHERE a.sid = ".$sid
		." AND (( a.zps = '".$runde[0]->hzps."' AND a.mnr = ".$runde[0]->hmnr." ) "
		//." OR ( a.zps ='".$runde[0]->sgh_zps."' AND a.mnr = ".$runde[0]->hmnr." )) ";
		." OR ( FIND_IN_SET(a.zps,'".$runde[0]->sgh_zps."') AND a.mnr = ".$runde[0]->hmnr." )) ";
		if($runde[0]->rang !="0") {
			$sql = $sql
				." AND a.status = ".$runde[0]->rang; }
		else { $sql = $sql
				." AND a.lid = ".$runde[0]->lid; }
		$sql = $sql
		." AND a.mgl_nr <> '0' "
		;
///////////////////////////
///////////////////////////
		return $sql;
	}
// Anzahl Spieler Heim ermitteln
	function getCLMCountHeim ( $options=array() )
	{
		$query	= $this->_getCLMCountHeim( $options );
		$result = $this->_getList( $query );
		return @$result;
	}


	function _getCLMHeim ( &$options )
	{
	$liga	= JRequest::getInt('liga');
	$sid	= JRequest::getInt('saison');
	$runde	= JRequest::getInt('runde');
	$paar	= JRequest::getInt('paar');
	$dg	= JRequest::getInt('dg');

		$db	= JFactory::getDBO();
		$id	= @$options['id'];

	$row =& JTable::getInstance( 'ligen', 'TableCLM' );
	$row->load( $liga );

	$data = "SELECT a.gemeldet,a.editor, a.id,a.sid, a.lid, a.runde, a.dg, a.tln_nr,"
		." a.gegner,a.paar, a.dwz_zeit, a.dwz_editor,  "
		." a.zeit, a.edit_zeit,  "
		." m.name as hname,m.zps as hzps,m.man_nr as hmnr,m.sg_zps as sgh_zps, "
		." n.name as gname, n.zps as gzps, n.man_nr as gmnr, n.sg_zps as sgg_zps, "
		." l.name as lname, l.stamm, l.ersatz, l.sl as sl, l.rang, l.id as lid"
		." FROM #__clm_rnd_man as a "
		." LEFT JOIN #__clm_liga AS l ON (l.id = a.lid ) "
		." LEFT JOIN #__clm_mannschaften AS m ON (m.liga = a.lid AND m.tln_nr = a.tln_nr) AND m.sid = a.sid "
		." LEFT JOIN #__clm_mannschaften AS n ON (n.liga = a.lid AND n.tln_nr = a.gegner) AND n.sid = a.sid "
			." WHERE a.sid = ".$sid
			." AND a.lid = ".$liga
			." AND a.runde = ".$runde
			." AND a.paar = ".$paar
			." AND a.dg = ".$dg
			." AND a.heim = 1  ";
	$db->setQuery( $data);
	$runde		= $db->loadObjectList();

	$sql = "SELECT a.*, d.Spielername as name ";
		if($runde[0]->rang !="0") {$sql = $sql.",r.rang ,r.man_nr as rmnr";}
		$sql = $sql
		." FROM #__clm_meldeliste_spieler as a "
		." LEFT JOIN #__clm_dwz_spieler as d ON ( d.ZPS = a.zps AND d.Mgl_Nr= a.mgl_nr AND d.sid = a.sid) ";
		if($runde[0]->rang !="0") {
			$sql = $sql
		." LEFT JOIN #__clm_rangliste_spieler as r ON ( r.ZPS = a.zps AND r.Mgl_Nr= a.mgl_nr AND r.sid = a.sid AND a.status = r.Gruppe ) ";
		}
		$sql = $sql
		." WHERE a.sid = ".$runde[0]->sid
		." AND (( a.zps = '".$runde[0]->hzps."' AND a.mnr = ".$runde[0]->hmnr." )"
		//." OR ( a.zps ='".$runde[0]->sgh_zps."' AND a.mnr = ".$runde[0]->hmnr." ))";
		." OR ( FIND_IN_SET(a.zps,'".$runde[0]->sgh_zps."') != 0 AND a.mnr = ".$runde[0]->hmnr." )) ";
		if($runde[0]->rang !="0") {
			$sql = $sql
				." AND a.status = ".$runde[0]->rang
				." AND a.lid = ".$runde[0]->lid
				." AND a.mgl_nr <> '0' "
				." ORDER BY r.man_nr,r.Rang"; }
		else { $sql = $sql
				." AND a.lid = ".$runde[0]->lid
				." AND a.mgl_nr <> '0' "
				." ORDER BY a.snr"; }
	$db->setQuery( $sql );

		return $sql;
	}

	function getCLMHeim ( $options=array() )
	{
		$query	= $this->_getCLMHeim( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

// Anzahl Spieler Gast ermitteln
	function _getCLMCountGast ( &$options )
	{
	$liga	= JRequest::getInt('liga');
	$sid	= JRequest::getInt('saison');
	$runde	= JRequest::getInt('runde');
	$paar	= JRequest::getInt('paar');
	$dg	= JRequest::getInt('dg');

		$db	= JFactory::getDBO();
		$id	= @$options['id'];

	$row =& JTable::getInstance( 'ligen', 'TableCLM' );
	$row->load( $liga );

	$data = "SELECT a.gemeldet,a.editor, a.id,a.sid, a.lid, a.runde, a.dg, a.tln_nr,"
		." a.gegner,a.paar, a.dwz_zeit, a.dwz_editor,  "
		." a.zeit, a.edit_zeit,  "
		." m.name as hname,m.zps as hzps,m.man_nr as hmnr,m.sg_zps as sgh_zps, "
		." n.name as gname, n.zps as gzps, n.man_nr as gmnr, n.sg_zps as sgg_zps, "
		." l.name as lname, l.stamm, l.ersatz, l.sl as sl, l.rang, l.id as lid"
		." FROM #__clm_rnd_man as a "
		." LEFT JOIN #__clm_liga AS l ON (l.id = a.lid ) "
		." LEFT JOIN #__clm_mannschaften AS m ON (m.liga = a.lid AND m.tln_nr = a.tln_nr) AND m.sid = a.sid "
		." LEFT JOIN #__clm_mannschaften AS n ON (n.liga = a.lid AND n.tln_nr = a.gegner) AND n.sid = a.sid "
			." WHERE a.sid = ".$sid
			." AND a.lid = ".$liga
			." AND a.runde = ".$runde
			." AND a.paar = ".$paar
			." AND a.dg = ".$dg
			." AND a.heim = 1  ";
	$db->setQuery( $data);
	$runde		= $db->loadObjectList();

	// Anzahl Spieler Gast
	$sql = "SELECT COUNT(a.snr) as count"
		." FROM #__clm_meldeliste_spieler as a "
		." LEFT JOIN #__clm_rangliste_spieler as r ON ( r.ZPS = a.zps AND r.Mgl_Nr= a.mgl_nr AND r.sid = a.sid AND a.status = r.Gruppe ) "
		." WHERE a.sid = ".$runde[0]->sid
		." AND (( a.zps = '".$runde[0]->gzps."' AND a.mnr = ".$runde[0]->gmnr." ) "
		//." OR ( a.zps ='".$runde[0]->sgg_zps."' AND a.mnr = ".$runde[0]->gmnr." )) ";
		." OR ( FIND_IN_SET(a.zps,'".$runde[0]->sgg_zps."') AND a.mnr = ".$runde[0]->gmnr." )) ";
		if($runde[0]->rang !="0") {
			$sql = $sql
				." AND a.status = ".$runde[0]->rang; }
		else { $sql = $sql
				." AND a.lid = ".$runde[0]->lid; }
		$sql = $sql
		." AND a.mgl_nr > 0 "
		;

		return $sql;
	}

	function getCLMCountGast ( $options=array() )
	{
		$query	= $this->_getCLMCountGast( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

// Namen Spieler Gast ermitteln
	function _getCLMGast ( &$options )
	{
	$liga	= JRequest::getInt('liga');
	$sid	= JRequest::getInt('saison');
	$runde	= JRequest::getInt('runde');
	$paar	= JRequest::getInt('paar');
	$dg	= JRequest::getInt('dg');

		$db	= JFactory::getDBO();
		$id	= @$options['id'];

	$row =& JTable::getInstance( 'ligen', 'TableCLM' );
	$row->load( $liga );

	$data = "SELECT a.gemeldet,a.editor, a.id,a.sid, a.lid, a.runde, a.dg, a.tln_nr,"
		." a.gegner,a.paar, a.dwz_zeit, a.dwz_editor,  "
		." a.zeit, a.edit_zeit,  "
		." m.name as hname,m.zps as hzps,m.man_nr as hmnr,m.sg_zps as sgh_zps, "
		." n.name as gname, n.zps as gzps, n.man_nr as gmnr, n.sg_zps as sgg_zps, "
		." l.name as lname, l.stamm, l.ersatz, l.sl as sl, l.rang, l.id as lid"
		." FROM #__clm_rnd_man as a "
		." LEFT JOIN #__clm_liga AS l ON (l.id = a.lid ) "
		." LEFT JOIN #__clm_mannschaften AS m ON (m.liga = a.lid AND m.tln_nr = a.tln_nr) AND m.sid = a.sid "
		." LEFT JOIN #__clm_mannschaften AS n ON (n.liga = a.lid AND n.tln_nr = a.gegner) AND n.sid = a.sid "
			." WHERE a.sid = ".$sid
			." AND a.lid = ".$liga
			." AND a.runde = ".$runde
			." AND a.paar = ".$paar
			." AND a.dg = ".$dg
			." AND a.heim = 1  ";
	$db->setQuery( $data);
	$runde		= $db->loadObjectList();

	$sql = "SELECT a.*, d.Spielername as name";
		if($runde[0]->rang !="0") {$sql = $sql.",r.rang,r.man_nr as rmnr ";}
		$sql = $sql
		." FROM #__clm_meldeliste_spieler as a "
		." LEFT JOIN #__clm_dwz_spieler as d ON ( d.ZPS = a.zps AND d.Mgl_Nr= a.mgl_nr AND d.sid = a.sid) ";
		if($runde[0]->rang !="0") {
			$sql = $sql
		." LEFT JOIN #__clm_rangliste_spieler as r ON ( r.ZPS = a.zps AND r.Mgl_Nr= a.mgl_nr AND r.sid = a.sid AND a.status = r.Gruppe ) ";
		}
		$sql = $sql
		." WHERE a.sid = ".$runde[0]->sid
		." AND (( a.zps = '".$runde[0]->gzps."' AND a.mnr = ".$runde[0]->gmnr." ) "
		//." OR ( a.zps ='".$runde[0]->sgg_zps."' AND a.mnr = ".$runde[0]->gmnr." ))";
		." OR ( FIND_IN_SET(a.zps,'".$runde[0]->sgg_zps."') AND a.mnr = ".$runde[0]->gmnr." )) ";
		if($runde[0]->rang !="0") {
			$sql = $sql
				." AND a.status = ".$runde[0]->rang
				." AND a.lid = ".$runde[0]->lid
				." AND a.mgl_nr > 0 "
				." ORDER BY r.man_nr,r.Rang"; }
		else { $sql = $sql
				." AND a.lid = ".$runde[0]->lid
				." AND a.mgl_nr > 0 "
				." ORDER BY a.snr"; }
		return $sql;
	}

	function getCLMGast ( $options=array() )
	{
		$query	= $this->_getCLMGast( $options );
		$result = $this->_getList( $query );
		return @$result;
	}
// Ergebnis ermitteln
	function _getCLMErgebnis ( &$options )
	{
	$liga	= JRequest::getInt('liga');
	$sid	= JRequest::getInt('saison');
	$runde	= JRequest::getInt('runde');
	$tln_nr	= JRequest::getInt('tln');
	$paar	= JRequest::getInt('paar');

		$db	= JFactory::getDBO();
		$id	= @$options['id'];

	$query = " SELECT eid, erg_text "
		." FROM #__clm_ergebnis "
			;
		return $query;
	}

	function getCLMErgebnis ( $options=array() )
	{
		$query	= $this->_getCLMErgebnis( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	
	
	function punkte_text ($lid)
	{
	defined('_JEXEC') or die('Restricted access'); 
	// Ergebnisliste laden
	$sql = "SELECT a.id, a.erg_text "
		." FROM #__clm_ergebnis as a "
		;
	$db 		=& JFactory::getDBO();
	$db->setQuery( $sql );
	$ergebnis	= $db->loadObjectList();

	// Punktemodus aus #__clm_liga holen
	$query = " SELECT a.sieg, a.remis, a.nieder, a.antritt, a.runden_modus "
		." FROM #__clm_liga as a"
		." WHERE a.id = ".$lid
		;
	$db->setQuery($query);
	$liga = $db->loadObjectList();
		$sieg 		= $liga[0]->sieg;
		$remis 		= $liga[0]->remis;
		$nieder		= $liga[0]->nieder;
		$antritt	= $liga[0]->antritt;

	// Ergebnistexte nach Modus setzen
	$ergebnis[0]->erg_text = ($nieder+$antritt)." - ".($sieg+$antritt);
	$ergebnis[1]->erg_text = ($sieg+$antritt)." - ".($nieder+$antritt);
	$ergebnis[2]->erg_text = ($remis+$antritt)." - ".($remis+$antritt);
	$ergebnis[3]->erg_text = ($nieder+$antritt)." - ".($nieder+$antritt);
	if ($antritt > 0) {
		$ergebnis[4]->erg_text = "0 - ".round($antritt+$sieg)." (kl)";
		$ergebnis[5]->erg_text = round($antritt+$sieg)." - 0 (kl)";
		$ergebnis[6]->erg_text = "0 - 0 (kl)";
		}
		
	return $ergebnis;
	}
	
	
	function _getCLMAccess ( &$options )
	{
	$lid	= JRequest::getInt('liga');
	$sid	= JRequest::getInt('saison');
	$runde	= JRequest::getInt('runde');
	$paar	= JRequest::getInt('paar');
	$dg	= JRequest::getInt('dg');


		$db	= JFactory::getDBO();
		$id	= @$options['id'];

	$query	= "SELECT gemeldet "
		." FROM #__clm_rnd_man "
		." WHERE sid = $sid AND lid = $lid AND runde = $runde "
		." AND paar = $paar AND dg = $dg AND heim = 1"
		;

		return $query;
	}

	function getCLMAccess ( $options=array() )
	{
		$query	= $this->_getCLMAccess( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMClmuser ( &$options )
	{
	$user	= & JFactory::getUser();
	$jid	= $user->get('id');
	$sid	= JRequest::getInt('saison');

		$db	= JFactory::getDBO();
		$id	= @$options['id'];

	$query	= "SELECT zps,published "
		." FROM #__clm_user "
		." WHERE jid = $jid "
		." AND sid = $sid "
		;
		return $query;
	}

	function getCLMClmuser ( $options=array() )
	{
		$query	= $this->_getCLMClmuser( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMMeldung ( &$options )
	{
	$user	= & JFactory::getUser();
	$jid	= $user->get('id');
	$lid 	= JRequest::getInt('liga');
	$sid 	= JRequest::getInt('saison');

		$db	= JFactory::getDBO();
		$id	= @$options['id'];

	$query	= "SELECT mf,published "
		." FROM #__clm_mannschaften "
		." WHERE jid = $jid "
		;
		return $query;
	}

	function getCLMMeldung ( $options=array() )
	{
		$query	= $this->_getCLMMeldung( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMfinish ( &$options )
	{
	$lid = JRequest::getInt('liga');
	$sid = JRequest::getInt('saison');

		$db	= JFactory::getDBO();
		$id	= @$options['id'];

	$query	= "SELECT *  "
		." FROM #__clm_runden_termine "
		." WHERE sid = $sid "
		." AND liga = ".$lid
		." ORDER BY nr ASC"
		;
		return $query;
	}

	function getCLMfinish ( $options=array() )
	{
		$query	= $this->_getCLMfinish( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMoldresult ( &$options )
	{
	$lid	= JRequest::getInt('liga');
	$sid	= JRequest::getInt('saison');
	$runde	= JRequest::getInt('runde');
	$paar	= JRequest::getInt('paar');
	$dg		= JRequest::getInt('dg');


		$db	= JFactory::getDBO();
		$id	= @$options['id'];

	$query	= "SELECT * "
		." FROM #__clm_rnd_spl "
		." WHERE sid = $sid AND lid = $lid AND runde = $runde "
		." AND paar = $paar AND dg = $dg AND heim = 1 "
		." ORDER BY brett "
		;

		return $query;
	}

	function getCLMoldresult ( $options=array() )
	{
		$query	= $this->_getCLMoldresult( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

function dwz($sid,$Liga)
	{
	////////////////////////////
	// Joomla Modifizierungen //
	////////////////////////////
	$mainframe	= JFactory::getApplication();
	$Entwanzen = true;
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	
	$db 		=& JFactory::getDBO();
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	JArrayHelper::toInteger($cid);

	// DWZ Parameter auslesen
	//$config	= &JComponentHelper::getParams( 'com_clm' );
	//$dwz	= $config->get('dwz_wertung',1);
	$dwz	= 0; //Ligaauswertung! immer
	
	$sql = ' SELECT `id` as `liga_id`, `sieg`, `remis`, `nieder`, `antritt`, params'
			. ' FROM #__clm_liga'
			. ' WHERE `sid` = "' . $sid .'"';
	if ($Liga != 0) {
		$sql .= ' AND `id` = "' . $Liga . '"';
	}	
	$db->setQuery ($sql);
	$ligapunkte_liste = $db->loadObjectList ();
	
	if ($Liga != 0) {
		$sql = ' SELECT `tln_nr`, `man_nr`'
			. ' FROM #__clm_mannschaften'
			. ' WHERE `liga` = "' . $Liga .'"';
		$db->setQuery ($sql);
		$mannr_liste = $db->loadObjectList ();
	} else $mannr_liste = array();
	
	$Turnier= $sid;
	$Saison	= $sid;

	if ($Liga != 0 AND isset($ligapunkte_liste[0])) {
		//Liga-Parameter aufbereiten
		$paramsStringArray = explode("\n", $ligapunkte_liste[0]->params);
		$params = array();
		foreach ($paramsStringArray as $value) {
			$ipos = strpos ($value, '=');
			if ($ipos !==false) {
				$params[substr($value,0,$ipos)] = substr($value,$ipos+1);
			}
		}	
	}
	if (!isset($params['dwz_date'])) $params['dwz_date'] = '0000-00-00';
	// Log
	// Konfigurationsparameter auslesen
	$config		= &JComponentHelper::getParams( 'com_clm' );
	$logfile	=$config->get('logfile',1);

	if ($logfile == 1) {
	$date		= & JFactory::getDate();
	$now		= $date->toMySQL();
	$user 		= & JFactory::getUser();
	$jid_aktion	= $user->get('id');
	$aktion		= "DWZ FE !";

	$query	= "INSERT INTO #__clm_log "
		." ( `aktion`, `jid_aktion`, `sid`, `lid` , `cids`, `datum`) "
		." VALUES ('$aktion','$jid_aktion','$Saison','$Liga','$cids','$now') "
		;
	$db->setQuery($query);
	$db->query();
	}
	//////////////////////////
	// Ende Modifizierungen //
	//////////////////////////

// Direkt geht es leider nicht. Wenn Du eine Idee hast, wie ich das aktuelle Jahr ermitteln kann, bitte
$Jahr=getdate();
$Jahr=$Jahr['year'];

////////////////////////////
///////////// Konstanten ///
////////////////////////////
$db =& JFactory::getDBO();
$prefix = $db->getPrefix();

$Tabelle_Einzelergebnisse = $prefix.'clm_rnd_spl';
//$Spalten_Einzelergebnisse =array('Saison'=>'sid','Turnier'=>'sid','Liga'=>'lid', 'SZPS'=>'zps', 'SMgl'=>'spieler', 'GZPS'=>'gzps', 'GMgl'=>'gegner','Erg'=>'punkte','KL'=>'kampflos','Egb'=>'ergebnis');
$Spalten_Einzelergebnisse =array('Saison'=>'sid','Turnier'=>'sid','Liga'=>'lid', 'tln_nr'=>'tln_nr', 'SZPS'=>'zps', 'SMgl'=>'spieler', 'GZPS'=>'gzps', 'GMgl'=>'gegner','Erg'=>'punkte','KL'=>'kampflos','Egb'=>'ergebnis');
$Tabelle_SpielerDWZ=$prefix.'clm_dwz_spieler';
$Spalten_DWZListe=array('ZPS'=>'ZPS','Mgl'=>'Mgl_Nr','Jahr'=>'Geburtsjahr','R0'=>'DWZ','I0'=>'DWZ_Index','Saison'=>'sid','Turnier'=>'sid');
$Tabelle_Meldeliste=$prefix.'clm_meldeliste_spieler';
//$Spalten_Meldeliste=array('ZPS'=>'zps','Mgl'=>'mgl_nr','Saison'=>'sid','Turnier'=>'sid','Liga'=>'lid');
$Spalten_Meldeliste=array('ZPS'=>'zps','Mgl'=>'mgl_nr','Saison'=>'sid','Turnier'=>'sid','Liga'=>'lid','tnr_nr'=>'tln_nr','mnr'=>'mnr');
/////////////////////////////////////////////////////////////////////////////////////////////////////////

foreach ($_GET as $var=>$value)
{
 $$var=$value;
}
unset($var);
/** Die Variablen in Konstanten und login lasse ich mir nicht �berschreiben: **/


/*********************************************************************
   Sicherheit:
   Mit der Festsetzung auf Zahlen ist es schwieriger (vielleicht unm�glich)
   hier unerw�nschte Parameter zu �bergeben.
   Mit der Funktion mysql_real_escape_string() wird aus dem Argument _eine_ Zeichenkette
   im Sinne der SQL-Parameter. Damit kann man nur aus einer Tabelle Daten abfragen,
   und diese Abfrage nicht illegal erweitern: Stichwort: SQL Injection
 *********************************************************************/
if (!is_numeric($Liga)) settype($Liga,'integer');
if (!is_numeric($Turnier)) settype($Turnier,'integer');
//$Spalten_Einzelergebnisse['Turnier']=mysql_real_escape_string($Spalten_Einzelergebnisse['Turnier']);
/*********************************************************************
 Alle Abfragen sind ausbauf�hig gestaltet, falls sich die Spalten noch
 �ndern sollten. Der "Ausbau" ist in Konsten.php zu machen.
 *********************************************************************/
$query='SELECT '.
       $Spalten_Einzelergebnisse['SZPS'].','.
       $Spalten_Einzelergebnisse['SMgl'].','.
       $Spalten_Einzelergebnisse['GZPS'].','.
       $Spalten_Einzelergebnisse['GMgl'].','.
       $Spalten_Einzelergebnisse['Erg'].','.
       $Spalten_Einzelergebnisse['KL'].','.
       $Spalten_Einzelergebnisse['Egb'].','.
       $Spalten_Einzelergebnisse['tln_nr'].','.
       $Spalten_Einzelergebnisse['Liga'].
       //' FROM '.mysql_real_escape_string($Tabelle_Einzelergebnisse).     klkl
       ' FROM '.$Tabelle_Einzelergebnisse.
       ' WHERE  '.$Spalten_Einzelergebnisse['Turnier'].'='.$Turnier;
if ($Liga>0) $query=$query.' AND '.$Spalten_Einzelergebnisse['Liga'].'='.$Liga;

/*
JError::raiseWarning( 500, JText::_( ' ! ' ) );
$link = 'index.php?option='.$option.'&section='.$section;
$mainframe->redirect($link,$query);
*/
$result = CLMModelMeldung::Abfrage($query);
if (!$result)
 {
 //exit('keine DWZ-Berechnung, da keine Ergebnisse');
 echo '<br><b>keine DWZ-Berechnung, da keine Ergebnisse</b>';
 }
Else
{
/*********************************************************************
  Die folgenden 4 if-Bl�cke stellen sicher, da� der jeweilige Variablen-Typ korrekt ist.
  Falls mehr als eine Korrektur erforderlich w�re, ist eine permutierte Abfrage schneller,
  aber im Code un�bersichlich, da man 15 Abfragen machen m�sste.
    
  Dies ist nur eingebaut, da sich die Seite noch im Aubau befindet.
  Wenn die Tabellen ihre entg�ltigen Typendefinitionen haben, kann man die If-Abfrage entfernen.
  Ob das auch f�r die settype-Anweisung und die Schleife gilt, h�ngt von dem Variablentyp ab.
 *********************************************************************/
If (!is_string($result[0][$Spalten_Einzelergebnisse['SZPS']]))
 { foreach ($result as &$row)
   { settype($row[$Spalten_Einzelergebnisse['SZPS']],'string');
 } }
If (!is_integer($result[0][$Spalten_Einzelergebnisse['SMgl']]))
 { foreach ($result as &$row)
   { settype($row[$Spalten_Einzelergebnisse['SMgl']],'integer');
 } }
If (!is_string($result[0][$Spalten_Einzelergebnisse['GZPS']]))
 { foreach ($result as &$row)
   { settype($row[$Spalten_Einzelergebnisse['GZPS']],'string');
 } }
If (!is_integer($result[0][$Spalten_Einzelergebnisse['GMgl']]))
 { foreach ($result as &$row)
   { settype($row[$Spalten_Einzelergebnisse['GMgl']],'integer');
 } }
/*********************************************************************
  Ich habe mich entschieden, in den Schritten lieber eine neue Tabelle aufzubauen,
  denn die alte zu modifizieren. Daher l�sche ich danach auch das alte Array
 *********************************************************************/
$Ergebnisse=array();
for ($i=0;$i<sizeof($result);$i++)
 {
  $Partie=$result[$i][$Spalten_Einzelergebnisse['Erg']];
  $Egb=$result[$i][$Spalten_Einzelergebnisse['Egb']];
  $KL=$result[$i][$Spalten_Einzelergebnisse['KL']];
  $liga_id = $result[$i][$Spalten_Einzelergebnisse['Liga']];
  // Teilnehmernummer in Mannschaftsnummer umsetzen
  $man_nr = 0;
  if ($Liga>0) {
	$tln_nr = $result[$i][$Spalten_Einzelergebnisse['tln_nr']];
	foreach ($mannr_liste as $mliste) {
		if ($mliste->tln_nr == $tln_nr) {
			$man_nr = $mliste->man_nr;
			break;
		}
	}
  }	
// Anpassung an flexible Punkteverteilung:
 
	// richtige Liga finden
	foreach ($ligapunkte_liste as $lpunkte) {
		if ($lpunkte->liga_id == $liga_id) {
			$ligapunkte = $lpunkte;
			break;
		}
	}
 
	// Fallback, falls die SQL-Abfrage oben nicht funktioniert	
	if (!isset ($ligapunkte)) {
		$ligapunkte = new stdObj ();
		$ligapunkte->sieg = 1;
		$ligapunkte->remis = 0.5;
		$ligapunkte->nieder = 0;
		$ligapunkte->antritt = 0;
	}
	
	// Punkte zur DWZ-Auswertung in Standardwertung ueberfuehren
	if		($Partie == $ligapunkte->sieg + $ligapunkte->antritt && $KL == 0)	{ $Partie = '1'; }
	elseif	($Partie == $ligapunkte->sieg + $ligapunkte->antritt && $KL== 1)	{ $Partie = '+'; }
	elseif	($Partie == $ligapunkte->remis + $ligapunkte->antritt && $KL == 0)	{ $Partie = '5'; }
	elseif	($Partie == $ligapunkte->remis + $ligapunkte->antritt && $KL == 1)	{ $Partie = '*'; }
	elseif	($Partie == $ligapunkte->nieder + $ligapunkte->antritt && $KL == 0)	{ $Partie = '0'; }
	elseif	($Partie == $ligapunkte->nieder && $KL == 1)						{ $Partie = '-'; }
	else																		{ $Partie = '?'; }


  if (($result[$i][$Spalten_Einzelergebnisse['SZPS']] > "10000") and ($result[$i][$Spalten_Einzelergebnisse['SZPS']] < "ZZZZZ") and ($result[$i][$Spalten_Einzelergebnisse['SZPS']] != "NULL")) {	
  //$Ergebnisse[$i][]=array($result[$i][$Spalten_Einzelergebnisse['SZPS']],$result[$i][$Spalten_Einzelergebnisse['SMgl']],
  $Ergebnisse[$i][]=array($man_nr,
						  $result[$i][$Spalten_Einzelergebnisse['SZPS']],$result[$i][$Spalten_Einzelergebnisse['SMgl']],
                          $result[$i][$Spalten_Einzelergebnisse['GZPS']],$result[$i][$Spalten_Einzelergebnisse['GMgl']],

                          $Partie); }
 }
unset($Partie,$KL);
unset ($ligapunkte);
sort ($Ergebnisse);
//arraydump($Ergebnisse);
$i=0;
$TempArrAuswertung=array();
//$TempArrAuswertung[0][0]=array($Ergebnisse[0][0][0],$Ergebnisse[0][0][1]);
$TempArrAuswertung[0][0]=array($Ergebnisse[0][0][0],$Ergebnisse[0][0][1],$Ergebnisse[0][0][2]);
foreach($Ergebnisse as &$EinePartie)
{
 if ($TempArrAuswertung[$i][0]!=array($EinePartie[0][0],$EinePartie[0][1],$EinePartie[0][2]))
 {
  $i++;
  $TempArrAuswertung[$i][0]=array($EinePartie[0][0],$EinePartie[0][1],$EinePartie[0][2]);
 }
 $TempArrAuswertung[$i][]=array($EinePartie[0][3],$EinePartie[0][4],$EinePartie[0][5]);
}
unset($Ergebnisse,$EinePartie);

/** Diese For-Schleife k�nnte man auch mit der folgenden foreach-Schleife kombinieren
    Man hätte ein wenig Geschwindigkeitsgewinn. ***/
unset($result1);
$result1 = array();
$key0 = ''; $key1 = ''; $key2 = ''; $key99 = '';
for ($i=0;$i<sizeof($TempArrAuswertung);$i++)
 {
  for ($j=sizeof($TempArrAuswertung[$i])-1;0<=$j;$j--)
   {
    if ($j == 0) {
		$key1 = $TempArrAuswertung[$i][$j][1]; //!=$result[$k]['ZPS'])
		$key2 = $TempArrAuswertung[$i][$j][2]; //!=$result[$k]['Mgl_Nr'])))
	} else {
		$key1 = $TempArrAuswertung[$i][$j][0]; //!=$result[$k]['ZPS'])
		$key2 = $TempArrAuswertung[$i][$j][1]; //!=$result[$k]['Mgl_Nr'])))
	}
	if (strlen($key2) == 1) $key2 = "00".$key2;
	if (strlen($key2) == 2) $key2 = "0".$key2;
	$key99 = $key1.$key2;
	if (!isset($result1[$key99])) {
	  if ($params['dwz_date'] == '0000-00-00') {
		$query='SELECT '.
			$Spalten_DWZListe['ZPS'].','.
			$Spalten_DWZListe['Mgl'].','.
			$Spalten_DWZListe['Jahr'].','.
			$Spalten_DWZListe['R0'].','.
			$Spalten_DWZListe['I0'].' ' .
			//'FROM '.mysql_real_escape_string($Tabelle_SpielerDWZ).' '.
			'FROM '.$Tabelle_SpielerDWZ.' '.
			'WHERE '.$Spalten_DWZListe['Saison'].'='.$Saison.' '.
			'AND '.$Spalten_DWZListe['ZPS'].'='."'".$key1."'".' '.
			'AND '.$Spalten_DWZListe['Mgl'].'='.$key2;
	  } else {
		$query='SELECT a.ZPS, a.Mgl_Nr, a.Geburtsjahr, ml.start_dwz as DWZ, ml.start_I0 as DWZ_Index '
			.' FROM #__clm_dwz_spieler AS a'		 
			.' LEFT JOIN #__clm_meldeliste_spieler AS ml ON (ml.zps = a.ZPS AND ml.mgl_nr = a.Mgl_Nr AND ml.lid = '.$Liga.' ) '  //AND ml.mnr = '.$man_nr.') '
			.' WHERE a.sid = '.$Saison
			.' AND a.ZPS = '."'".$key1."'"
			.' AND a.Mgl_Nr = '.$key2
			.' LIMIT 1';
	  }	
		$db 		=& JFactory::getDBO();
		$db->setQuery($query);
		$result = $db->loadObjectList();
		if (!$result) {
			// nichts
			}
		else { 
			$result1[$key99] = $result;
			} 
	} else {
		//nichts
     }

	if (isset($result1[$key99])) {
      //$TempArrAuswertung[$i][$j]['DWZ']=array($result1[$key1.$key2]['DWZ'],$result1[$key1.$key2]['DWZ_Index'],$result1[$key1.$key2]['Geburtsjahr']);
	  $TempArrAuswertung[$i][$j]['DWZ']=array($result1[$key99][0]->DWZ,$result1[$key99][0]->DWZ_Index,$result1[$key99][0]->Geburtsjahr);
     }
    Else {
	  $TempArrAuswertung[$i][$j]['DWZ']=array(0,0,0); }
  }
 }
unset($j,$k,$result1);

$ArrUebergabeDWZ=array();
foreach($TempArrAuswertung as $EinSpieler)
{
 $alter=$Jahr-$EinSpieler[0]['DWZ'][2];
 if ($alter<0) $alter=0;
 ElseIf($alter<21) $alter=1;
 ElseIf($alter<26) $alter=2;
 Else $alter=3;
 $Gegner=array();
 for($i=1;$i<sizeof($EinSpieler);$i++)
 {
  $Gegner[]=array($EinSpieler[$i]['DWZ'][0],$EinSpieler[$i][2]);
 }
 $ArrUebergabeDWZ[]=array(array('man_nr'=>$EinSpieler[0][0],'zps'=>$EinSpieler[0][1],'mgl_nr'=>$EinSpieler[0][2]),
                          CLMModelMeldung::DWZRechner($EinSpieler[0]['DWZ'][0],$EinSpieler[0]['DWZ'][1],$alter,$Gegner));
}
unset($alter,$Gegner,$i,$Jahr,$TempArrAuswertung);
foreach ($ArrUebergabeDWZ as $EinSpieler)
 {
 if ($dwz == 0) {
  $query='UPDATE '.$Tabelle_Meldeliste.' '.
       'SET DWZ='.((integer)$EinSpieler[1][0]).', '.
       'I0='.$EinSpieler[1][1].', '.
       'Punkte='.round($EinSpieler[1][2]/1000,1).', '.
       'Partien='.$EinSpieler[1][3].', '.
       'We='.round($EinSpieler[1][4]/1000,3).', '.
       'Leistung='.$EinSpieler[1][5].', '.
       'EFaktor='.$EinSpieler[1][6].', '.
       'Niveau='.$EinSpieler[1][7].' '.
       'WHERE '.$Spalten_Meldeliste['Turnier'].'='.$Turnier.' '.
       'AND '.$Spalten_Meldeliste['ZPS'].'='."'".$EinSpieler[0]['zps']."'".' '.
       'AND '.$Spalten_Meldeliste['Mgl'].'='.$EinSpieler[0]['mgl_nr'].' '.
       'AND '.$Spalten_Meldeliste['Saison'].'='.$Saison;
      // Die Spalten, die ich angelegt habe, bzw Du anlegst, nehme ich als konstant an.
  if ($Liga>0)
   {
    $query=$query.' AND '.$Spalten_Einzelergebnisse['Liga'].'='.$Liga
				 .' AND mnr = '.$EinSpieler[0]['man_nr'];    
   }
  if (($EinSpieler[1][0]=='Restp.')OR ($EinSpieler[1][0]>0))
   {
	$db->setQuery($query);
	$result = $db->query();
    //$result=mysql_query($query);
    if (!$result)   // Datensatz nicht vorhanden.
     {
      $query="INSERT INTO $Tabelle_Meldeliste ".
             'SET '.$Spalten_Meldeliste['ZPS'].'='."'".$EinSpieler[0]['zps']."'".', '.
             $Spalten_Meldeliste['Mgl'].'='.$EinSpieler[0]['mgl_nr'].
             ' ON DUPLICATE KEY UPDATE '.
             $Spalten_Meldeliste['Turnier'].'='.$Turnier.', '.
             $Spalten_Meldeliste['Saison'].'='.$Saison.
             'DWZ='.((integer)$EinSpieler[1][0]).', '.
             'I0='.$EinSpieler[1][1].', '.
             'Punkte='.$EinSpieler[1][2].', '.
             'Partien='.$EinSpieler[1][3].', '.
             'We='.$EinSpieler[1][4].', '.
             'Leistung='.$EinSpieler[1][5].', '.
             'EFaktor='.$EinSpieler[1][6].', '.
             'Niveau='.$EinSpieler[1][7];
	  $db->setQuery($query);
	  $result = $db->query();
      //$result= mysql_query($query);
      if ($result)
       {
        if ($Entwanzen) {echo 'Insert von '.$EinSpieler[0]['zps'].'-'.$EinSpieler[0]['mgl_nr'].' erfolgreich!<br>';}
        Else trigger_error($EinSpieler[0]['zps'].'-'.$EinSpieler[0]['mgl_nr'].' wurde vom SQL goes DWZ-Script in die Meldeliste eingetragen',E_User_notice);
       }
      Else
       {
        If ($Entwanzen) echo 'Insert von '.$EinSpieler[0]['zps'].'-'.$EinSpieler[0]['mgl_nr'].' gescheitert!';//,E_USER_NOTICE);
       }
     }   
   }
 }
 if ($dwz == 1) {
	$query='UPDATE '.$Tabelle_SpielerDWZ.' '.
       'SET DWZ_neu='.((integer)$EinSpieler[1][0]).', '.
       'I0='.$EinSpieler[1][1].', '.
       'Punkte='.round($EinSpieler[1][2]/1000,1).', '.
       'Partien='.$EinSpieler[1][3].', '.
       'We='.round($EinSpieler[1][4]/1000,3).', '.
       'Leistung='.$EinSpieler[1][5].', '.
       'EFaktor='.$EinSpieler[1][6].', '.
       'Niveau='.$EinSpieler[1][7].' '.
       'WHERE '.$Spalten_DWZListe['Turnier'].'='.$Turnier.' '.
       'AND '.$Spalten_DWZListe['ZPS'].'='."'".$EinSpieler[0]['zps']."'".' '.
       'AND '.$Spalten_DWZListe['Mgl'].'='.$EinSpieler[0]['mgl_nr'].' '.
       'AND '.$Spalten_DWZListe['Saison'].'='.$Saison;
      // Die Spalten, die ich angelegt habe, bzw Du anlegst, nehme ich als konstant an.
  if (($EinSpieler[1][0]=='Restp.')OR ($EinSpieler[1][0]>0))
   {
	$db->setQuery($query);
	$result = $db->query();
	//$result=mysql_query($query);
    if (!$result)   // Datensatz nicht vorhanden.
     {
      $query="INSERT INTO $Tabelle_SpielerDWZ ".
             'SET '.$Spalten_Meldeliste['ZPS'].'='."'".$EinSpieler[0]['zps']."'".', '.
             $Spalten_Meldeliste['Mgl'].'='.$EinSpieler[0]['mgl_nr'].
             ' ON DUPLICATE KEY UPDATE '.
             $Spalten_Meldeliste['Turnier'].'='.$Turnier.', '.
             $Spalten_Meldeliste['Saison'].'='.$Saison.
             'DWZ_neu='.((integer)$EinSpieler[1][0]).', '.
             'I0='.$EinSpieler[1][1].', '.
             'Punkte='.$EinSpieler[1][2].', '.
             'Partien='.$EinSpieler[1][3].', '.
             'We='.$EinSpieler[1][4].', '.
             'Leistung='.$EinSpieler[1][5].', '.
             'EFaktor='.$EinSpieler[1][6].', '.
             'Niveau='.$EinSpieler[1][7];
		$db->setQuery($query);
		$result = $db->query();
		//$result= mysql_query($query);
      if ($result)
       {
        if ($Entwanzen) {echo 'Insert von '.$EinSpieler[0]['zps'].'-'.$EinSpieler[0]['mgl_nr'].' erfolgreich!<br>';}
        Else trigger_error($EinSpieler[0]['zps'].'-'.$EinSpieler[0]['mgl_nr'].' wurde vom SQL goes DWZ-Script in die Meldeliste eingetragen',E_User_notice);
       }
      Else
       {
        If ($Entwanzen) echo 'Insert von '.$EinSpieler[0]['zps'].'-'.$EinSpieler[0]['mgl_nr'].' gescheitert!';//,E_USER_NOTICE);
       }
     }   
    Else If ($Entwanzen) echo 'Update von '.$EinSpieler[0]['zps'].'-'.$EinSpieler[0]['mgl_nr'].' erfolgreich!<br>';
   }
   }
 }
unset($EinSpieler,$result,$ArrUebergabeDWZ,$query);
} //Label: Sinnslos
/*Beim Einsetzen in die Seite mu� evtl hier das Aufr�umen getrichen werden.
Das mu� man aber in der realen Version testen.                       */

   }

function BerechneEFaktor($R0,$Index,$Alter=3,$W=0,$We=0)
 {
/*Establishment*/
/*"E"-Faktortabelle*/
/*Benutzung:
1. Wenn DWZ nicht vorhanden E=0
2. Wen Index=1, dann E=5
2. �ber das Alter die Spalte 0,1 oder 2 festlegen
3. Von Unten nach Oben erste Zeile suchen, in der die DWZ kleiner ist.
4. In dieser Zeile sucht man sich die Spalte (min(Index,5)+3. Dort steht der E-Faktor*/
/* Ich rechne das lieber aus, da ich festgestellt habe, die Tabellen des DSB sind nicht immer mit denen aus Elobase identisch*/
/*$ETab=array(array(   1,   0,   0,5, 5, 5, 5, 5, 5),
              array( 841,   0,   0,5, 6, 6, 6, 6, 6),
              array(1107,   0,   0,5, 7, 7, 7, 7, 7),
              array(1258,   0,   0,5, 8, 8, 8, 8, 8),
              array(1368,   0,   0,5, 9, 9, 9, 9, 9),
              array(1457,   1,   0,5,10,10,10,10,10),
              array(1532, 841,   0,5,10,11,11,11,11),
              array(1597,1107,   0,5,10,12,12,12,12),
              array(1655,1258,   0,5,10,13,13,13,13),
              array(1708,1368,   0,5,10,14,14,14,14),
              array(1756,1457,   1,5,10,15,15,15,15),
              array(1801,1532, 841,5,10,15,16,16,16),
              array(1842,1597,1107,5,10,15,17,17,17),
              array(1881,1655,1258,5,10,15,18,18,18),
              array(1917,1708,1368,5,10,15,19,19,19),
              array(1952,1756,1457,5,10,15,20,20,20),
              array(1985,1801,1532,5,10,15,20,21,21),
              array(2016,1842,1597,5,10,15,20,22,22),
              array(2046,1881,1655,5,10,15,20,23,23),
              array(2074,1917,1708,5,10,15,20,24,24),
              array(2102,1952,1756,5,10,15,20,25,25),
              array(2128,1985,1801,5,10,15,20,25,26),
              array(2154,2016,1842,5,10,15,20,25,27),
              array(2178,2046,1881,5,10,15,20,25,28),
              array(2202,2074,1917,5,10,15,20,25,29),
              array(2225,2102,1952,5,10,15,20,25,30));*/
  $fB=1;
  $SBr=0;
  If ($Index==0) $E=0;
  Else If ($Alter==1)
   {
    $fB = $R0 / 2000;
    If ($fB<0.5) $fB=0.5;
    If ($fB>1) $fB=1; 
    If ($R0<1300)
     {
      if (($W-$We)<=0) $SBr = exp((1300-$R0)/150)-1;
      Else $SBr=0;
     }
    Else $SBr=0;
    $E = (pow(($R0 / 1000),4)) * $fB + $SBr+5;
    If ($E<5) $E=5;
    If ($SBr>=0)
     {
      if ($E>150) $E=150;
     }
    Else If ($E>(5*$Index)) $E=5*$Index;
   }
  Else If ($Alter==2)
   {
    $E = pow($R0 / 1000,4)+10;
    if ($E<5)
     {
      $E=5;
     }
    else if ($E>30) $E=30;
    if ($E>(5*$Index)) $E=5*$Index;
   }
  else if (($Alter==3) or ($Alter==0))
   {
    $E = pow($R0 / 1000,4)+15;
    if ($E<5) $E=5;
    else if ($E>30) $E=30;
    if ($E>(5*$Index)) $E=5*$Index;
   }
  $E = round($E);
  return $E;
 }


  function DWZRechner($R0, $Index, $Alter, $Gegner)
   {
    $PTab = array (500,501,503,504,506,507,508,510,511,513,514,516,517,518,520,521,523,524,525,527,
                   528,530,531,532,534,535,537,538,539,541,542,544,545,546,548,549,551,552,553,555,
                   556,558,559,560,562,563,565,566,567,569,570,572,573,574,576,577,578,580,581,583,
                   584,585,587,588,590,591,592,594,595,596,598,599,600,602,603,605,606,607,609,610,
                   611,613,614,615,617,618,619,621,622,623,625,626,628,629,630,632,633,634,636,637,
                   638,639,641,642,643,645,646,647,649,650,651,653,654,655,657,658,659,660,662,663,
                   664,666,667,668,669,671,672,673,675,676,677,678,680,681,682,683,685,686,687,688,
                   690,691,692,693,695,696,697,698,700,701,702,703,705,706,707,708,709,711,712,713,
                   714,715,717,718,719,720,721,723,724,725,726,727,728,730,731,732,733,734,735,737,
                   738,739,740,741,742,743,745,746,747,748,749,750,751,752,754,755,756,757,758,759,
                   760,761,762,764,765,766,767,768,769,770,771,772,773,774,775,776,777,779,780,781,
                   782,783,784,785,786,787,788,789,790,791,792,793,794,795,796,797,798,799,800,801,
                   802,803,804,805,806,807,808,809,810,811,812,813,814,814,815,816,817,818,819,820,
                   821,822,823,824,825,826,827,827,828,829,830,831,832,833,834,835,835,836,837,838,
                   839,840,841,841,842,843,844,845,846,847,847,848,849,850,851,852,852,853,854,855,
                   856,856,857,858,859,860,860,861,862,863,863,864,865,866,867,867,868,869,870,870,
                   871,872,873,873,874,875,875,876,877,878,878,879,880,880,881,882,883,883,884,885,
                   885,886,887,887,888,889,889,890,891,891,892,893,893,894,895,895,896,897,897,898,
                   898,899,900,900,901,902,902,903,903,904,905,905,906,906,907,908,908,909,909,910,
                   910,911,912,912,913,913,914,914,915,915,916,917,917,918,918,919,919,920,920,921,
                   921,922,922,923,923,924,924,925,925,926,926,927,927,928,928,929,929,930,930,931,
                   931,932,932,933,933,934,934,934,935,935,936,936,937,937,938,938,938,939,939,940,
                   940,941,941,941,942,942,943,943,943,944,944,945,945,945,946,946,947,947,947,948,
                   948,948,949,949,950,950,950,951,951,951,952,952,952,953,953,953,954,954,954,955,
                   955,955,956,956,956,957,957,957,958,958,958,959,959,959,960,960,960,961,961,961,
                   961,962,962,962,963,963,963,963,964,964,964,965,965,965,965,966,966,966,966,967,
                   967,967,968,968,968,968,969,969,969,969,970,970,970,970,970,971,971,971,971,972,
                   972,972,972,973,973,973,973,973,974,974,974,974,975,975,975,975,975,976,976,976,
                   976,976,977,977,977,977,977,977,978,978,978,978,978,979,979,979,979,979,980,980,
                   980,980,980,980,981,981,981,981,981,981,982,982,982,982,982,982,982,983,983,983,
                   983,983,983,983,984,984,984,984,984,984,984,985,985,985,985,985,985,985,986,986,
                   986,986,986,986,986,986,987,987,987,987,987,987,987,987,988,988,988,988,988,988,
                   988,988,988,988,989,989,989,989,989,989,989,989,989,990,990,990,990,990,990,990,
                   990,990,990,990,991,991,991,991,991,991,991,991,991,991,991,991,992,992,992,992,
                   992,992,992,992,992,992,992,992,993,993,993,993,993,993,993,993,993,993,993,993,
                   993,993,993,994,994,994,994,994,994,994,994,994,994,994,994,994,994,994,994,994,
                   995,995,995,995,995,995,995,995,995,995,995,995,995,995,995,1000);

    $LTab=array(
          0.5=>array(1=>0,-191,-274,-325,-362,-391,-414,-434,-451,-465,-478,-490,-500,-510,-519,-527,-534,-542,-548,-554),
          1  =>array(2=>     0,-122,-191,-238,-274,-302,-325,-345,-362,-378,-391,-403,-414,-425,-434,-443,-451,-458,-465),
          1.5=>array(2=>   191,   0, -90,-148,-191,-224,-251,-274,-293,-310,-325,-339,-351,-362,-373,-382,-391,-399,-407),
          2  =>array(3=>        122,   0, -72,-122,-160,-191,-216,-238,-257,-274,-289,-302,-314,-325,-336,-345,-354,-362),
          2.5=>array(3=>        274,  90,   0, -60,-104,-138,-167,-191,-212,-230,-246,-260,-274,-286,-297,-307,-316,-325),
          3  =>array(4=>             191,  72,   0, -51, -90,-122,-148,-171,-191,-208,-224,-238,-251,-263,-274,-284,-293),
          3.5=>array(4=>             325, 148,  60,   0, -44, -80,-109,-134,-155,-174,-191,-206,-220,-232,-244,-254,-264),
          4  =>array(5=>                  238, 122,  51,   0, -40, -72, -99,-122,-142,-160,-176,-191,-204,-215,-228,-238),
          4.5=>array(5=>                  362, 191, 104,  44,   0, -36, -65, -90,-112,-131,-148,-164,-178,-191,-203,-214),
          5  =>array(6=>                       274, 160,  90,  40,   0, -32, -60, -83,-104,-122,-138,-153,-167,-179,-191),
          5.5=>array(6=>                       391, 224, 138,  80,  36,   0, -30, -55, -77, -96,-114,-130,-144,-157,-169),
          6  =>array(7=>                            302, 191, 122,  72,  32,   0, -27, -51, -72, -90,-107,-122,-136,-148),
          6.5=>array(7=>                            414, 251, 164, 106,  65,  30,   0, -25, -47, -67, -85,-101,-115,-128),
          7  =>array(8=>                                 325, 216, 148,  99,  60,  27,   0, -24, -44, -63, -80, -95,-109),
          7.5=>array(8=>                                 434, 274, 191, 134,  90,  55,  25,   0, -22, -42, -60, -76, -90),
          8 =>array(9=>                                      345, 238, 171, 122,  83,  51,  24,   0, -21, -40, -56, -72),
          8.5=>array(9=>                                      451, 293, 212, 155, 112,  77,  47,  22,   0, -20, -37, -53),
          9  =>array(10=>                                          362, 257, 191, 142, 104,  72,  44,  21,   0, -19, -36),
          9.5=>array(10=>                                          465, 310, 230, 174, 131,  90,  67,  42,  20,   0, -18),
          10 =>array(11=>                                               378, 274, 208, 160, 122,  90,  63,  40,  19,   0),
          10.5=>array(11=>                                              478, 325, 246, 191, 148, 114,  85,  60,  37,  18),
          11 =>array(12=>                                                    391, 289, 224, 176, 138, 108,  80,  56,  36),
          12.5=>array(12=>                                                   490, 339, 260, 206, 164, 130, 101,  76,  53),
          12 =>array(13=>                                                         403, 302, 238, 191, 153, 122,  95,  72),
          12.5=>array(13=>                                                        500, 351, 274, 220, 178, 144, 115,  90),
          13 =>array(14=>                                                              414, 314, 251, 204, 167, 136, 109),
          13.5=>array(14=>                                                             510, 362, 286, 232, 191, 157, 128),
          14 =>array(15=>                                                                   425, 325, 263, 216, 179, 148),
          14.5=>array(15=>                                                                  519, 373, 297, 244, 203, 169),
          15 =>array(16=>                                                                        434, 336, 274, 228, 191),
          15.5=>array(16=>                                                                       527, 382, 307, 254, 214),
          16 =>array(17=>                                                                             443, 345, 284, 238),
          16.5=>array(17=>                                                                            535, 391, 316, 264),
          17 =>array(18=>                                                                                  451, 354, 293),
          17.5=>array(18=>                                                                                 542, 399, 325),
          18 =>array(19=>                                                                                       458, 362),
          18.5=>array(19=>                                                                                      548, 407),
          19 =>array(20=>                                                                                            465),
          19.5=>array(20=>                                                                                           554));
    $n=0;
    $W=0;
    $We=0;
    $Leistung=0;
    foreach ($Gegner as $G)
     {
      if ($G[0]==0) continue;
      if (($G[1]!="1") and ($G[1]!="0") and ($G[1]!="5")) continue;
      $n++;
      if ($R0>0)
       {
        $D=$R0-$G[0];
        if ($D>sizeof($PTab))
         {
          $P=1000;
         }
        Else if ($D>=0)
         {
          $P=$PTab[$D];
         }
        Else if ($D<-sizeof($PTab))
         {
          $P=0;
         }
        Else
         {
          $P=1000-$PTab[-$D];
         }
        $We+=$P;
       }
      if ($G[1]=="1") $W+=1000;
      Else If ($G[1]=="5") $W+=500;
      $Leistung+=$G[0];
     } 
    if ($n>0) $niveau=round($Leistung/$n);
    else $niveau=0;
    if ($n>4)
     {
      if ($W==0) $Leistung=$niveau-677;
      Else If ($W==$n) $Leistung=$niveau+677;
      Else
       {
        $P=$W/1000;
		if (isset($LTab[$P][$n])) $Leistung=$niveau+$LTab[$P][$n];
		else $Leistung=$niveau;
       }
     }
    Else $Leistung=0;
    If ($R0>0)
     { 
      $E=CLMModelMeldung::BerechneEFaktor($R0, $Index, $Alter);
      $Rn=(integer) round($R0+0.8*($W-$We)/($E+$n));
      $In=$Index+1;
     }
    Else if (($Leistung>0) and ($n>4))
     {
      if ($Leistung>=800) $Rn=$Leistung;
      Else $Rn=(integer) round($Leistung/8+700);
      $In=1;
      $E=0;
     }
    Else 
     {
      $Leistung=0;
      if ($n>0)
       {
        $Rn="Restp.";
        $In=0;
        $E=0;
       }
      Else
       {
        $Rn=$R0;
        $In=$Index;
        if (!isset($E)) $E=0;
       }
     }
  $dwz=array($Rn,$In,$W,$n,$We,$Leistung,$E,$niveau);
  //if (Return0)
   //{
    if ($Rn==0) $Rn=Null;
    if ($Rn==0)$In=Null;
    if ($R0==0)$We=Null;
    if ($n==0)$Leistung=Null;
    if ($R0==0)$E=Null;
    if ($n==0)$Niveau=Null;
   //}
  return $dwz;
 }

function Abfrage($query,$db=False)
 {
  $r=array();
  $db 		=& JFactory::getDBO(); // klkl
  //if (!$db) $result = mysql_query($query); 
  //Else $result = @ mysql_query($query,$db);
  $db->setQuery($query);
  $result = $db->loadAssocList();
  if (!$result) trigger_error("Abfrage konnte nicht ausgef�hrt werden: \n" . mysql_error().'\nDie Abfrage war: "'.$query.'"',E_USER_NOTICE);
  //if (mysql_num_rows($result) > 0)
  if (count($result) > 0)
   {
    //while ($row = mysql_fetch_assoc($result))
     //{
      //$r[]=$row;
     //}
    //unset($row);
    //mysql_free_result($result);
    //unset($result);
    return $result;
	
   }
  else
   {
    unset($result);
    return false;
   }
 }
}
?>
