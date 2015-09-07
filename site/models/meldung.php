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

class CLMModelMeldung extends JModelLegacy
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

	$row = JTable::getInstance( 'ligen', 'TableCLM' );
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

	$row = JTable::getInstance( 'ligen', 'TableCLM' );
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

	$row = JTable::getInstance( 'ligen', 'TableCLM' );
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

	$row = JTable::getInstance( 'ligen', 'TableCLM' );
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

	
	
	public static function punkte_text ($lid)
	{
	defined('_JEXEC') or die('Restricted access'); 
	// Ergebnisliste laden
	$sql = "SELECT a.id, a.erg_text "
		." FROM #__clm_ergebnis as a "
		;
	$db 		=JFactory::getDBO();
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
	$user	= JFactory::getUser();
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
	$user	= JFactory::getUser();
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
		clm_core::$api->direct("db_liga_calculateDWZ",array($Liga));
 	}
}
?>
