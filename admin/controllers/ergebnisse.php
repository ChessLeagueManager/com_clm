<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerErgebnisse extends JControllerLegacy
{
	/**
	 * Constructor
	 */
function __construct( $config = array() )
	{
		parent::__construct( $config );
		// Register Extra tasks
		//$this->registerTask( 'add','edit' );
		$this->registerTask( 'apply','save' );
		$this->registerTask( 'apply_wertung','save_wertung' );
	}

function display($cachable = false, $urlparams = array())
	{
	$mainframe	= JFactory::getApplication();
	$option 	= clm_core::$load->request_string('option');
	$section	= clm_core::$load->request_string('section');
	$db		= JFactory::getDBO();

	// für kaskadierende Menüführung
	// Parameter auslesen
	$config = clm_core::$db->config();
	$val	= $config->menue;
	if ($val == 1) {
		$runde	= clm_core::$load->request_string( 'runde' );
		$dg	= clm_core::$load->request_string( 'dg' );
			} else { $dg = ""; }
	if ($val == 1 AND $runde !="") { $mainframe->setUserState( "$option.filter_runde", "$runde" ); }
	if ($dg  !="") { $mainframe->setUserState( "$option.filter_dg", "$dg" ); }

	$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",'filter_order','a.id',	'cmd' );
	$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir','','word' );
	$filter_state		= $mainframe->getUserStateFromRequest( "$option.filter_state",'filter_state','','word' );
	$filter_sid		= $mainframe->getUserStateFromRequest( "$option.filter_sid",'filter_sid',0,'int' );
	$filter_lid		= $mainframe->getUserStateFromRequest( "$option.filter_lid",'filter_lid',0,'int' );
	$filter_dg		= $mainframe->getUserStateFromRequest( "$option.filter_dg",'filter_dg',0,'int' );
	$filter_runde		= $mainframe->getUserStateFromRequest( "$option.filter_runde",'filter_runde',0,'int' );
	$filter_catid		= $mainframe->getUserStateFromRequest( "$option.filter_catid",'filter_catid',0,'int' );
	$search			= $mainframe->getUserStateFromRequest( "$option.search",'search','','string' );
	$search			= strtolower( $search );
	$limit			= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
	$limitstart		= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

	$where = array();
	$where[]=' s.archiv = 0';
	if ( $filter_catid ) {	$where[] = 'a.published = '.(int) $filter_catid; }
	if ( $filter_sid ) {	$where[] = 'a.sid = '.(int) $filter_sid; }
	if ( $filter_lid ) {	$where[] = 'a.lid = '.(int) $filter_lid;

	$query = 'SELECT runden,durchgang,sl FROM #__clm_liga WHERE id = '.$filter_lid ;
	$db->setQuery( $query );
	$rnd_filter = $db->loadObjectList();
	$rnd_filter_dg	= $rnd_filter[0]->durchgang;
	$rnd_filter_rnd	= $rnd_filter[0]->runden;
	}
	else {
	$query = 'SELECT MAX(runden) as runden, MAX(durchgang) as durchgang FROM #__clm_liga ';
	$db->setQuery( $query );
	$rnd_filter = $db->loadObjectList();
	$rnd_filter_dg	= $rnd_filter[0]->durchgang;
	$rnd_filter_rnd	= $rnd_filter[0]->runden;
		}
	// Filter einstellen für verschiedene Kombinationen von DropDown Menue
	if ( $filter_runde != 0 AND $filter_lid !=0) {
		if ( $filter_runde > $rnd_filter_rnd) {
			$filter_runde = $mainframe->setUserState( "$option.filter_runde", "1" );
			$where[] = 'a.runde = '.(int) $filter_runde;
			}
		else { $where[] = 'a.runde = '.(int) $filter_runde; }
			}
	if ( $filter_runde AND !$filter_lid) { $where[] = 'a.runde = '.(int) $filter_runde; }
	if ( $filter_dg ) {
		if ( $filter_dg > $rnd_filter_dg ) {
			$filter_dg = $mainframe->setUserState( "$option.filter_dg", "1" );
			$where[] = 'a.dg = '.(int) $filter_dg; }
		else { $where[] = 'a.dg = '.(int) $filter_dg; }}

	if ($search) {	$where[] = 'LOWER(m.name) LIKE "'.$db->escape('%'.$search.'%').'"';}

	if ( isset($filter_state) AND is_string($filter_state) ) {
		if ( $filter_state == 'P' ) {
			$where[] = 'a.published = 1';
		} else if ($filter_state == 'U' ) {
			$where[] = 'a.published = 0';
		}
	}

	$where 		= ( count( $where ) ? ' AND ' . implode( ' AND ', $where ) : '' );
	if ($filter_order == 'a.id'){
		$orderby 	= ' ORDER BY a.sid ASC,a.lid ASC,a.dg ASC ,a.runde ASC ,a.paar ASC';
	} else {
	if ($filter_order =='hname' OR $filter_order == 'gname' OR $filter_order == 'a.lid' OR $filter_order == 'a.runde' OR $filter_order == 'a.paar' OR $filter_order == 'a.dg' OR $filter_order == 's.name' OR $filter_order == 'a.gemeldet' OR $filter_order == 'u.name' ) {
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', a.id';
			}
		else { $filter_order = 'a.id'; 
			$orderby 	= ' ORDER BY a.sid ASC,a.lid ASC,a.dg ASC ,a.runde ASC ,a.paar ASC';
		}
	}

	// get the total number of records
	$query = 'SELECT COUNT(*) '
		.' FROM #__clm_rnd_man AS a'
		.' LEFT JOIN #__clm_saison AS s ON s.id = a.sid'
		.' WHERE a.heim = 1 '
		. $where
	;
	$db->setQuery( $query );
	$total = $db->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );

	// get the subset (based on limits) of required records
	$query = "SELECT a.*,l.name as liga,l.teil,l.durchgang,l.sl,l.liga_mt, "
		." s.name as saison,s.published as sid_pub, u.name as uname,m.name as hname, n.name as gname "
	.' FROM #__clm_rnd_man as a '
	.' LEFT JOIN #__clm_user as u ON u.jid = a.gemeldet AND u.sid = a.sid '
	.' LEFT JOIN #__clm_mannschaften AS m ON (m.tln_nr = a.tln_nr AND m.liga = a.lid AND m.sid = a.sid) '
	.' LEFT JOIN #__clm_mannschaften AS n ON (n.tln_nr = a.gegner AND n.liga = a.lid AND n.sid = a.sid) '
	.' LEFT JOIN #__clm_liga AS l ON l.id = a.lid AND l.sid = a.sid'
	.' LEFT JOIN #__clm_saison AS s ON s.id = a.sid '
	.' WHERE a.heim = 1 '
	. $where
	. $orderby	;

	try {
		$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
		$rows = $db->loadObjectList();
	}
	catch (Exception $e) {
		$mainframe->enqueueMessage($db->stderr(), 'error');
	}
	// Filter
	// Statusfilter
	//$lists['state']	= JHTML::_('grid.state',  $filter_state );
	$lists['state'] = CLMForm::selectState( $filter_state );
	// Saisonfilter
	$sql = 'SELECT id, name FROM #__clm_saison WHERE archiv =0';
	$db->setQuery($sql);
	$saisonlist[]	= JHTML::_('select.option',  '0', JText::_( 'ERGEBNISSE_SAISON_SELECT' ), 'id', 'name' );
	$saisonlist         = array_merge( $saisonlist, $db->loadObjectList() );
	$lists['sid']      = JHTML::_('select.genericlist', $saisonlist, 'filter_sid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','id', 'name', intval( $filter_sid ) );
	// Nur ausführen wenn Saison published = 1 !!
	if ( isset($rows[0]->liga) AND is_string($rows[0]->liga) ) {
	
	//Zugangscheck
	$clmAccess = clm_core::$access;      
	if ($rows[0]->liga_mt == "0") {
		$mppoint = 'league';
		$csection = 'ligen';
	} else {
		$mppoint = 'teamtournament';
		$csection = 'mturniere';
	}
	//echo "<br>erg: "; var_dump($rows);  die('  section');
	if($clmAccess->access('BE_'.$mppoint.'_edit_result') === false) {
		$mainframe->enqueueMessage( JText::_('LIGEN_STAFFEL_TOTAL'),'warning' );
		$section = 'runden';
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	} elseif ($clmAccess->access('BE_'.$mppoint.'_edit_result') === true) $where_sl = '';
	else $where_sl = ' AND a.sl = '.clm_core::$access->getJid();
	
	if($rows[0]->sl !== clm_core::$access->getJid() AND $clmAccess->access('BE_'.$mppoint.'_edit_result') !== true) {
		$mainframe->enqueueMessage( JText::_('LIGEN_STAFFEL'),'warning' );
		$section = 'runden';
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

	// Ligafilter
	$sql = 'SELECT a.id AS cid, a.name FROM #__clm_liga as a'
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid"
		." WHERE a.rnd = 1 AND a.published = 1 AND s.archiv = 0 AND s.published = 1 ".$where_sl;
	$db->setQuery($sql);
	$ligalist[]	= JHTML::_('select.option',  '0', JText::_( 'ERGEBNISSE_LIGA' ), 'cid', 'name' );
	$ligalist	= array_merge( $ligalist, $db->loadObjectList() );
	$lists['lid']	= JHTML::_('select.genericlist', $ligalist, 'filter_lid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','cid', 'name', intval( $filter_lid ) );
	// Rundenfilter
	$sql = 'SELECT id, runde as name FROM #__clm_rnd_man '
		." WHERE lid =".($rows[0]->lid)." AND paar =1 AND heim = 1 AND dg = 1"
		." ORDER BY runde ASC ";
	$db->setQuery($sql);
	$rlist[]	= JHTML::_('select.option',  '0', JText::_( 'ERGEBNISSE_RUNDE' ), 'name', 'name' );
	$rlist		= array_merge( $rlist, $db->loadObjectList() );
	$lists['runde']	= JHTML::_('select.genericlist', $rlist, 'filter_runde', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','name', 'name', intval( $filter_runde ) );
	// Durchgangsfilter
	$dg_menu = array();
	$dg_menu[]	= JHTML::_('select.option',  '0', JText::_( 'ERGEBNISSE_DURCHGANG' ), 'name', 'name' );
	$dg_menu[]	= JHTML::_('select.option',  '1', JText::_( 'ERGEBNISSE_DGA' ), 'name', 'name' );
	$dg_menu[]	= JHTML::_('select.option',  '2', JText::_( 'ERGEBNISSE_DGB' ), 'name', 'name' );
	$dg_menu[]	= JHTML::_('select.option',  '3', JText::_( 'ERGEBNISSE_DGC' ), 'name', 'name' );
	$dg_menu[]	= JHTML::_('select.option',  '4', JText::_( 'ERGEBNISSE_DGD' ), 'name', 'name' );
	$lists['dg_menu']	= JHTML::_('select.genericlist', $dg_menu, 'filter_dg', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','name', 'name', intval( $filter_dg ) );
	}
	// Ordering
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;
	// Suchefilter
	$lists['search']= $search;
 
	require_once(JPATH_COMPONENT.DS.'views'.DS.'ergebnisse.php');
	CLMViewErgebnisse::ergebnisse( $rows, $lists, $pageNav, $option );
}


function edit()
	{
	$mainframe	= JFactory::getApplication();

	$db 		=JFactory::getDBO();
	$user 		=JFactory::getUser();
	$task 		= clm_core::$load->request_string( 'task');
	$option 	= clm_core::$load->request_string( 'option' );
	$section 	= clm_core::$load->request_string( 'section' );
	$id 		= clm_core::$load->request_int('id',0);
	$cid = clm_core::$load->request_array_int('cid');
	if (is_null($cid)) {
		$cid[0] = $id; }
	// load the row from the db table
	$row =JTable::getInstance( 'ergebnisse', 'TableCLM' );
	$row->load( $cid[0] );

	$sid =JTable::getInstance( 'saisons', 'TableCLM' );
	$sid->load($row->sid);

	// Ergebnisse einer unveröffentlichten Saison nicht bearbeiten
	if ($sid->published =="0") {
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_SAISON'),'warning' );
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_SAISON_WARTEN'),'notice' );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
	// spielfreie Runde  kann nicht gemeldet / bearbeitet werden
	if ($row->gemeldet == "1") {
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_SPIELFREIE'),'notice' );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
	// Runde durch SWT-Import auf spielfrei gesetzt -> kann nicht gemeldet / bearbeitet werden
	if ($row->gemeldet == "9997" AND is_null($row->ergebnis)) {
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_SPIELFREI_DURCH_SWT'),'warnung' );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

	// illegaler Einbruchversuch über URL !
	// evtl. mitschneiden !?!
	$saison		=JTable::getInstance( 'saisons', 'TableCLM' );
	$saison->load( $row->sid );
	if ($saison->archiv == "1") { //  AND clm_core::$access->getType() !== 'admin') {
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_ARCHIV'),'warning' );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;

	$data = "SELECT a.gemeldet,a.editor, a.id,a.sid, a.lid, a.runde, a.dg, a.tln_nr, a.ko_decision, a.comment, a.icomment," //mtmt
		." a.gegner,a.paar, a.dwz_zeit, a.dwz_editor, w.name as dwz_editor, "
		." a.zeit, a.edit_zeit, u.name as melder, v.name as name_editor, "
		." m.name as hname,m.zps as hzps,m.man_nr as hmnr,m.sg_zps as sgh_zps, "
		." n.name as gname, n.zps as gzps, n.man_nr as gmnr, n.sg_zps as sgg_zps, "
		." l.name as lname, l.stamm, l.ersatz, l.sl as sl, l.rang, l.id as lid, l.b_wertung, l.runden_modus, l.liga_mt " //mtmt
		." FROM #__clm_rnd_man as a "
		." LEFT JOIN #__clm_user as u ON u.jid = a.gemeldet AND u.sid = a.sid "
		." LEFT JOIN #__clm_user as v ON v.jid = a.editor AND v.sid = a.sid "
		." LEFT JOIN #__clm_user as w ON w.jid = a.dwz_editor AND w.sid = a.sid "
		." LEFT JOIN #__clm_liga AS l ON (l.id = a.lid ) "
		." LEFT JOIN #__clm_mannschaften AS m ON (m.liga = a.lid AND m.tln_nr = a.tln_nr) AND m.sid = a.sid "
		." LEFT JOIN #__clm_mannschaften AS n ON (n.liga = a.lid AND n.tln_nr = a.gegner) AND n.sid = a.sid "
		." WHERE a.id = ".$cid[0]
		;
	$db->setQuery( $data);
	$runde		= $db->loadObjectList();

	// Prüfen ob User Berechtigung zum editieren hat
	$clmAccess = clm_core::$access;      
	if ($runde[0]->liga_mt == "0") {
		$mppoint = 'league';
		$csection = 'ligen';
	} else {
		$mppoint = 'teamtournament';
		$csection = 'mturniere';
	}
	if($clmAccess->access('BE_'.$mppoint.'_edit_result') === false) {
		$mainframe->enqueueMessage( JText::_('LIGEN_STAFFEL_TOTAL'),'warning' );
		$section = 'runden';
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	} 
	if ($runde[0]->sl !== clm_core::$access->getJid() AND $clmAccess->access('BE_'.$mppoint.'_edit_result') !== true) {
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_IHRER'),'warning' );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}
	$row->checkout( clm_core::$access->getJid() );

	if ( $runde[0]->hmnr > ($runde[0]->lid)*10 OR $runde[0]->gmnr > ($runde[0]->lid)*10) {
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_MANNSCHAFTNUMMER'),'notice' );
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_MN_HEIM').' '.$runde[0]->hmnr.JText::_('ERGEBNISSE_MN_GAST').' '.$runde[0]->gmnr.' !','notice' );
	}
	
	// Spieler Heim
	$sql = "SELECT a.*, d.Spielername as name ";
		if($runde[0]->rang !="0") {$sql = $sql.",r.Rang as snr,r.man_nr as rmnr";}
	if ($runde[0]->hzps != '0') { //normal
		$sql = $sql
		." FROM #__clm_meldeliste_spieler as a ";
		if ($countryversion =="de") {
			$sql .= " LEFT JOIN #__clm_dwz_spieler as d ON ( d.ZPS = a.zps AND d.Mgl_Nr= a.mgl_nr AND d.sid = a.sid) ";
		} else {
			$sql .= " LEFT JOIN #__clm_dwz_spieler as d ON ( d.ZPS = a.zps AND d.PKZ= a.PKZ AND d.sid = a.sid) ";
		}
		if($runde[0]->rang !="0") {
			$sql = $sql
		." LEFT JOIN #__clm_rangliste_spieler as r ON ( r.ZPSmgl = a.zps AND r.Mgl_Nr= a.mgl_nr AND r.sid = a.sid AND a.status = r.Gruppe ) ";
		}
		$sql = $sql
		." WHERE a.sid = ".$runde[0]->sid
		." AND (a.gesperrt = 0 OR a.gesperrt IS NULL )"
		." AND (( a.zps = '".$runde[0]->hzps."' AND a.mnr = ".$runde[0]->hmnr." )"
			." OR ( FIND_IN_SET(a.zps, '".$runde[0]->sgh_zps."') != 0 AND a.mnr = ".$runde[0]->hmnr." )) ";
		if($runde[0]->rang !="0") {
			$sql = $sql
				." AND a.status = ".$runde[0]->rang
				." AND a.lid = ".$runde[0]->lid
				." AND a.mgl_nr <> '0' "
				." ORDER BY r.man_nr,r.Rang"; }
		else { $sql = $sql
				." AND a.lid = ".$runde[0]->lid
				." AND (a.mgl_nr <> '0' OR a.PKZ <> '') "
				." ORDER BY a.snr"; }
	} else {  //Schulschach u.ä.
		$zps = "-1";
		$sql .= " FROM #__clm_meldeliste_spieler as a ";
		$sql .= " LEFT JOIN #__clm_dwz_spieler as d ON ( d.ZPS = a.zps AND d.Mgl_Nr= a.mgl_nr AND d.sid = a.sid) ";
		$sql = $sql
			." WHERE a.sid = ".$runde[0]->sid
			." AND (a.gesperrt = 0 OR a.gesperrt IS NULL )"
			." AND ( a.zps = '".$zps."' AND a.mnr = ".$runde[0]->hmnr." )"
			." AND a.lid = ".$runde[0]->lid
			." AND (a.mgl_nr <> '0') "
			." ORDER BY a.snr"; 
	}
	$db->setQuery( $sql );
	$heim		= $db->loadObjectList();

	// Anzahl Spieler Heim
	$hcount = count($heim);

	// Bretter / Spieler ermitteln
	$sql = "SELECT * FROM #__clm_rnd_spl as a "
		." WHERE a.sid = ".$runde[0]->sid
		." AND a.lid = ".$runde[0]->lid
		." AND a.runde = ".$runde[0]->runde
		." AND a.paar = ".$runde[0]->paar
		." AND a.dg = ".$runde[0]->dg
		." AND heim = 1"
		." ORDER BY a.brett"
		;
	// evtl. WHERE weiss = 1

	$db->setQuery( $sql );
	$bretter	= $db->loadObjectList();

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
		." WHERE a.id = ".$runde[0]->lid
		;
	$db->setQuery($query);
	$liga = $db->loadObjectList();
		$sieg 		= $liga[0]->sieg;
		$remis 		= $liga[0]->remis;
		$nieder		= $liga[0]->nieder;
		$antritt	= $liga[0]->antritt;
////

	// Ergebnistexte nach Modus setzen
	$ergebnis[0]->erg_text = ($nieder+$antritt)." - ".($sieg+$antritt);
	$ergebnis[1]->erg_text = ($sieg+$antritt)." - ".($nieder+$antritt);
	$ergebnis[2]->erg_text = ($remis+$antritt)." - ".($remis+$antritt);
	$ergebnis[3]->erg_text = ($nieder+$antritt)." - ".($nieder+$antritt);
	if ($antritt > 0) {
		$ergebnis[4]->erg_text = "0 - ".round($antritt+$sieg)." (kampflos)";
		$ergebnis[5]->erg_text = round($antritt+$sieg)." - 0 (kampflos)";
		$ergebnis[6]->erg_text = "0 - 0 (kampflos)";
		}
	// Spieler Gast
	$sql = "SELECT a.*, d.Spielername as name";
		if($runde[0]->rang !="0") {$sql = $sql.",r.Rang as snr,r.man_nr as rmnr ";}
	if ($runde[0]->hzps != '0') { //normal
		$sql = $sql
		." FROM #__clm_meldeliste_spieler as a ";
		if ($countryversion =="de") {
			$sql .= " LEFT JOIN #__clm_dwz_spieler as d ON ( d.ZPS = a.zps AND d.Mgl_Nr= a.mgl_nr AND d.sid = a.sid) ";
		} else {
			$sql .= " LEFT JOIN #__clm_dwz_spieler as d ON ( d.ZPS = a.zps AND d.PKZ= a.PKZ AND d.sid = a.sid) ";
		}
		if($runde[0]->rang !="0") {
			$sql = $sql
		." LEFT JOIN #__clm_rangliste_spieler as r ON ( r.ZPSmgl = a.zps AND r.Mgl_Nr= a.mgl_nr AND r.sid = a.sid AND a.status = r.Gruppe ) ";
		}
		$sql = $sql
		." WHERE a.sid = ".$runde[0]->sid
		." AND (a.gesperrt = 0 OR a.gesperrt IS NULL )"
		." AND (( a.zps = '".$runde[0]->gzps."' AND a.mnr = ".$runde[0]->gmnr." ) "
		." OR ( FIND_IN_SET(a.zps, '".$runde[0]->sgg_zps."') != 0 AND a.mnr = ".$runde[0]->gmnr." )) ";
		if($runde[0]->rang !="0") {
			$sql = $sql
				." AND a.status = ".$runde[0]->rang
				." AND a.lid = ".$runde[0]->lid
				." AND a.mgl_nr > 0 "
				." ORDER BY r.man_nr,r.Rang"; }
		else { $sql = $sql
				." AND a.lid = ".$runde[0]->lid
				." AND (a.mgl_nr > 0 OR a.PKZ > '')"
				." ORDER BY a.snr"; }
	} else {  //Schulschach u.ä.
		$zps = "-1";
		$sql .= " FROM #__clm_meldeliste_spieler as a ";
		$sql .= " LEFT JOIN #__clm_dwz_spieler as d ON ( d.ZPS = a.zps AND d.Mgl_Nr= a.mgl_nr AND d.sid = a.sid) ";
		$sql = $sql
			." WHERE a.sid = ".$runde[0]->sid
			." AND (a.gesperrt = 0 OR a.gesperrt IS NULL )"
			." AND ( a.zps = '".$zps."' AND a.mnr = ".$runde[0]->gmnr." )"
			." AND a.lid = ".$runde[0]->lid
			." AND (a.mgl_nr <> '0') "
			." ORDER BY a.snr"; 
	}

	$db->setQuery( $sql );
	$gast		= $db->loadObjectList();

	// Anzahl Spieler Gast
	$gcount = count($gast);

	//if ($runde[0]->runde > 1) {
		$sql = "SELECT me.snr"
		  ." FROM #__clm_rnd_spl as a "
		  ." LEFT JOIN #__clm_mannschaften AS ma ON (ma.sid = a.sid AND ma.liga = a.lid and ma.tln_nr = a.tln_nr) "
		  ." LEFT JOIN #__clm_meldeliste_spieler AS me ON (me.sid = a.sid AND me.lid = a.lid AND me.mnr = ma.man_nr AND me.zps = a.zps AND me.mgl_nr = a.spieler) "
		  ." WHERE a.sid = ".$runde[0]->sid
		  ." AND a.lid = ".$runde[0]->lid
		  ." AND a.runde = ".($runde[0]->runde - 1)
		  ." AND a.tln_nr = ".$runde[0]->tln_nr   
		  ." AND a.dg = 1"  //.$runde[0]->dg
		  ." ORDER BY a.brett"
		  ;
		$db->setQuery( $sql );
		$hvoraufstellung	= $db->loadObjectList();
	
		$sql = "SELECT me.snr"
		  ." FROM #__clm_rnd_spl as a "
		  ." LEFT JOIN #__clm_mannschaften AS ma ON (ma.sid = a.sid AND ma.liga = a.lid and ma.tln_nr = a.tln_nr) "
		  ." LEFT JOIN #__clm_meldeliste_spieler AS me ON (me.sid = a.sid AND me.lid = a.lid AND me.mnr = ma.man_nr AND me.zps = a.zps AND me.mgl_nr = a.spieler) "
		  ." WHERE a.sid = ".$runde[0]->sid
		  ." AND a.lid = ".$runde[0]->lid
		  ." AND a.runde = ".($runde[0]->runde - 1)
		  ." AND a.tln_nr = ".$runde[0]->gegner   
		  ." AND a.dg = 1"  //.$runde[0]->dg
		  ." ORDER BY a.brett"
		  ;
		$db->setQuery( $sql );
		$gvoraufstellung	= $db->loadObjectList();
	//}	

	
	require_once(JPATH_COMPONENT.DS.'views'.DS.'ergebnisse.php');
	CLMViewErgebnisse::ergebnis( $row, $runde, $heim, $hcount, $gast, $gcount, $bretter,$ergebnis, $option, $hvoraufstellung, $gvoraufstellung);
	}

function remove()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	defined('clm') or die('Restricted access');

	$db 		=JFactory::getDBO();
	$cid 		= clm_core::$load->request_array_int('cid');
	$option 	= clm_core::$load->request_string('option');
	$section	= clm_core::$load->request_string('section');
	$user 		=JFactory::getUser();

	if (count($cid) < 1) {
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_SELECT'),'warning' );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
	
	for ($i = 0; $i < count($cid); $i++) { 
		// Daten sammeln
		$query = "SELECT a.gemeldet,l.sl as sl,a.sid, a.lid, a.runde, a.dg, a.paar, l.liga_mt "
			." FROM #__clm_rnd_man as a "
			." LEFT JOIN #__clm_liga AS l ON (l.id = a.lid ) "
			." WHERE a.id = ".$cid[$i]
			;
		$db->setQuery( $query);
		$data		= $db->loadObjectList();

		// Prüfen ob User Berechtigung zum löschen hat
		$clmAccess = clm_core::$access;      
		if ($data[0]->liga_mt == "0") {
			$mppoint = 'league';
			$csection = 'ligen';
		} else {
			$mppoint = 'teamtournament';
			$csection = 'mturniere';
		}
		if($clmAccess->access('BE_'.$mppoint.'_edit_result') === false) {
			$mainframe->enqueueMessage( JText::_('LIGEN_STAFFEL_TOTAL'),'warning' );
			$section = 'runden';
			$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
		} 
		if ($data[0]->sl !== clm_core::$access->getJid() AND $clmAccess->access('BE_'.$mppoint.'_edit_result') !== true) {
			$mainframe->enqueueMessage( JText::_('ERGEBNISSE_LOESCH'),'warning' );
			$link = 'index.php?option='.$option.'&section='.$section;
			$mainframe->redirect( $link);
		}

		// Für Heimmannschaft updaten
		$query	=" UPDATE #__clm_rnd_man"
			." SET gemeldet = NULL"
			." , editor = NULL"
			." , zeit = '1970-01-01 00:00:00'"
			." , edit_zeit = '1970-01-01 00:00:00'"
			." , ergebnis = NULL"
			." , kampflos = NULL"
			." , brettpunkte = NULL"
			." , manpunkte = NULL"
			." , ko_decision = 0"          //mtmt
			." , comment = ''"          //mtmt
			." , icomment = ''"          //mtmt
			." WHERE sid = ".$data[0]->sid
			." AND lid = ".$data[0]->lid
			." AND runde = ".$data[0]->runde
			." AND paar = ".$data[0]->paar
			." AND dg = ".$data[0]->dg
			." AND heim = 1 "
			;
		$db->setQuery($query);
		clm_core::$db->query($query);
		// Für Gastmannschaft updaten
		$query	= "UPDATE #__clm_rnd_man"
			." SET gemeldet = NULL"
			." , editor = NULL"
			." , zeit = '1970-01-01 00:00:00'"
			." , edit_zeit = '1970-01-01 00:00:00'"
			." , ergebnis = NULL"
			." , kampflos = NULL"
			." , brettpunkte = NULL"
			." , manpunkte = NULL"
			." , ko_decision = 0"          //mtmt
			." , comment = ''"          //mtmt
			." , icomment = ''"          //mtmt
			." WHERE sid = ".$data[0]->sid
			." AND lid = ".$data[0]->lid
			." AND runde = ".$data[0]->runde
			." AND paar = ".$data[0]->paar
			." AND dg = ".$data[0]->dg
			." AND heim = 0 "
			;
		$db->setQuery($query);
		clm_core::$db->query($query);

		$query = " DELETE FROM #__clm_rnd_spl "
			." WHERE sid = ".$data[0]->sid
			." AND lid = ".$data[0]->lid
			." AND runde = ".$data[0]->runde
			." AND paar = ".$data[0]->paar
			." AND dg = ".$data[0]->dg
			." AND heim = 1 "
			;
		$db->setQuery($query);
		clm_core::$db->query($query);

		$query = " DELETE FROM #__clm_rnd_spl "
			." WHERE sid = ".$data[0]->sid
			." AND lid = ".$data[0]->lid
			." AND runde = ".$data[0]->runde
			." AND paar = ".$data[0]->paar
			." AND dg = ".$data[0]->dg
			." AND heim = 0 "
			;
		$db->setQuery($query);
		clm_core::$db->query($query);

		if (!clm_core::$db->query($query)) {
			echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
		}
	}
	clm_core::$api->db_tournament_ranking($data[0]->lid,true); 
	//CLMControllerErgebnisse::calculateRanking($data[0]->sid,$data[0]->lid);
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = "Ergebnis gelöscht";
	$clmLog->params = array('cid' => $cid, 'sid' => $data[0]->sid, 'lid' => $data[0]->lid, 'rnd' => $data[0]->runde, 'paar' => $data[0]->paar, 'dg' => $data[0]->dg);
	$clmLog->write();

	$msg = JText::_( 'ERGEBNISSE_GELOESCHT');
	$mainframe->enqueueMessage( $msg );
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}


function save()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	defined('clm') or die('Restricted access');

	$option		= clm_core::$load->request_string('option');
	$section	= clm_core::$load->request_string('section');
	$db 		=JFactory::getDBO();
	$task		= clm_core::$load->request_string( 'task');
	$user 		=JFactory::getUser();
	$id_id 		= clm_core::$load->request_string( 'id');
	$date		=JFactory::getDate();

	$meldung 	= $user->get('id');
	$sid		= clm_core::$load->request_int( 'sid');
	$lid 		= clm_core::$load->request_int( 'lid');
	$rnd		= clm_core::$load->request_int( 'rnd');
	$paarung	= clm_core::$load->request_int( 'paarung');
	$dg			= clm_core::$load->request_int( 'dg');
	$gemeldet	= clm_core::$load->request_string( 'gemeldet');
	$hzps		= clm_core::$load->request_string( 'hzps');
	$gzps		= clm_core::$load->request_string( 'gzps');
	$ko_decision = clm_core::$load->request_string( 'ko_decision');
	$comment = addslashes(clm_core::$load->request_string( 'comment'));
	$icomment = addslashes(clm_core::$load->request_string( 'icomment'));
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;
	// Überprüfen ob Runde schon gemeldet ist
	$query	= "SELECT gemeldet, tln_nr, gegner "
		." FROM #__clm_rnd_man "
		." WHERE id = $id_id "
		;
	$db->setQuery( $query );
	$id = $db->loadObjectList();
	$id_tln = $id[0]->tln_nr;
	$id_geg = $id[0]->gegner;
	
	// Punktemodus aus #__clm_liga holen
	$query = " SELECT a.stamm, a.sieg, a.remis, a.nieder, a.antritt, a.runden_modus, a.runden, "
		." a.man_sieg, a.man_remis, a.man_nieder, a.man_antritt, a.sieg_bed, a.params "
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
		$runden_modus	= $liga[0]->runden_modus;
		$runden		= $liga[0]->runden;

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

	// Runde noch NICHT gemeldet
	if (!$id[0]->gemeldet) {
	
	// Datensätze in Spielertabelle schreiben
	$y1 = 0;
	for ($y=1; $y< (1+$stamm) ; $y++){
		$heim		= clm_core::$load->request_string( 'heim'.$y);
		$gast		= clm_core::$load->request_string( 'gast'.$y);
		$ergebnis	= clm_core::$load->request_string( 'ergebnis'.$y);

	$theim	= explode("-", $heim);
	$tgast	= explode("-", $gast);
	if ($countryversion == "de") {
		$thmgl	= $theim[0];
		$tgmgl	= $tgast[0];
		$thPKZ  = '';
		$tgPKZ  = '';
	} else {
		$thmgl	= 0;
		$tgmgl	= 0;
		$thPKZ  = $theim[0];
		$tgPKZ  = $tgast[0];
	}
	$thzps	= $theim[1];
	$tgzps	= $tgast[1];

	if ($ergebnis > 3) { $kampflos = 1; }
		else { $kampflos = 0; }
		
	if ($ergebnis == 1)
		{ 	$erg_h = $nieder+$antritt;
			$erg_g = $sieg+$antritt;
		}
	if ($ergebnis == 2)
		{ 	$erg_h = $sieg+$antritt;
			$erg_g = $nieder+$antritt;
		}
	if ($ergebnis == 3)
		{ 	$erg_h = $remis+$antritt;
			$erg_g = $remis+$antritt;
		}
	if ($ergebnis == 4)
		{ 	$erg_h = $antritt;
			$erg_g = $antritt;
		}
	if ($ergebnis == 5)
		{ 	$erg_h = 0;
			$erg_g = $sieg+$antritt;
		}
	if ($ergebnis == 6)
		{ 	$erg_h = $sieg+$antritt;
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
	if ($ergebnis == 9)
		{ 	$erg_h = 0;
			$erg_g = 0;
		}
	// WICHTIG wegen NULL / SELECTED Problem
	$ergebnis--;
	// ungerade Zahl für Weiss/Schwarz
	//if ($y%2 != 0) {$weiss = 0; $schwarz = 1;}
	//else { $weiss = 1; $schwarz = 0;}
	// Weiss/Schwarz nach Farbfolge-Parameter
	$weiss = substr($colorstr,$y1,1);
	if ($weiss == 1) $schwarz = 0; else $schwarz = 1;
	$y1++;
	if ($y1 >= strlen($colorstr)) $y1 = 0;

	$query	= "INSERT INTO #__clm_rnd_spl "
		." ( `sid`, `lid`, `runde`, `paar`, `dg`, `tln_nr`, `brett`, `heim`, `weiss`, `spieler`, `PKZ` "
		." , `zps`, `gegner`, `gPKZ`, `gzps`, `ergebnis` , `kampflos`, `punkte`, `gemeldet`) "
		." VALUES ('$sid','$lid','$rnd','$paarung','$dg','$id_tln','$y',1,'$weiss','$thmgl','$thPKZ','$thzps',"
		." '$tgmgl','$tgPKZ','$tgzps','$ergebnis', '$kampflos','$erg_h','$meldung') "
		." , ('$sid','$lid','$rnd','$paarung','$dg','$id_geg','$y','0','$schwarz','$tgmgl','$tgPKZ','$tgzps',"
		." '$thmgl','$thPKZ','$thzps','$ergebnis', '$kampflos','$erg_g','$meldung') "
		;
	$db->setQuery($query);
	clm_core::$db->query($query);
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

	// Teilnehmer ID bestimmen 
	$query = " SELECT a.tln_nr,a.gegner "
		." FROM #__clm_rnd_man as a"
		." WHERE a.id = ".$id_id
			;
	$db->setQuery( $query);
	$tlnr=$db->loadObjectList();
	$tln_nr	= $tlnr[0]->tln_nr;
	$gegner	= $tlnr[0]->gegner;
	$hkampflos = 0;
	$gkampflos = 0;

	// Mannschaftspunkte Heim / Gast verteilen
	// Standard : Mehrheit der BP gewinnt, BP gleich -> Punkteteilung
	if ($sieg_bed == 1) {
		if ( $hmpunkte >  $gmpunkte ) { 
			$hman_punkte = $man_sieg; 
			$gman_punkte = $man_nieder;
			$hergebnis = 1;
			$gergebnis = 0;
		}
		if ( $hmpunkte == $gmpunkte ) { 
			$hman_punkte = $man_remis; 
			$gman_punkte = $man_remis;
			$hergebnis = 2;
			$gergebnis = 2;
		}
		if ( $hmpunkte <  $gmpunkte ) { 
			$hman_punkte = $man_nieder; 
			$gman_punkte = $man_sieg;
			$hergebnis = 0;
			$gergebnis = 1;
		}
	}
	// erweiterter Standard : mehr als die H�lfte der BP -> Sieg, H�lfte der BP -> halbe MP Zahl
	if ($sieg_bed == 2) {
		if ( $hmpunkte >  (($stamm*($sieg+$antritt))/2) ) { $hman_punkte = $man_sieg; $hergebnis = 1;}
		if ( $hmpunkte == (($stamm*($sieg+$antritt))/2) ) { $hman_punkte = $man_remis; $hergebnis = 2;}
		if ( $hmpunkte <  (($stamm*($sieg+$antritt))/2) ) { $hman_punkte = $man_nieder; $hergebnis = 0;}
		
		if ( $gmpunkte >  (($stamm*($sieg+$antritt))/2) ) { $gman_punkte = $man_sieg; $gergebnis = 1;}
		if ( $gmpunkte == (($stamm*($sieg+$antritt))/2) ) { $gman_punkte = $man_remis; $gergebnis = 2;}
		if ( $gmpunkte <  (($stamm*($sieg+$antritt))/2) ) { $gman_punkte = $man_nieder; $gergebnis = 0;}
	}
	// Antrittspunkte addieren falls angetreten
	if ( $stamm > $man_kl_punkte ) { $hman_punkte = $hman_punkte + $man_antritt;}
	else { 
		$hkampflos = 1;
		$gkampflos = 1;
	}
	if ( $stamm > $gman_kl_punkte ) { $gman_punkte = $gman_punkte + $man_antritt;}
	else { 
		$hkampflos = 1;
		$gkampflos = 1;
	}
			if ($hkampflos == 1) {
				if ($hergebnis == 0) $hergebnis = 4;
				if ($hergebnis == 1) $hergebnis = 5;
				if ($hergebnis == 2) $hergebnis = 6;
			}
			if ($gkampflos == 1) {
				if ($gergebnis == 0) $gergebnis = 4;
				if ($gergebnis == 1) $gergebnis = 5;
				if ($gergebnis == 2) $gergebnis = 6;
			}
 
	// Datum und Uhrzeit für Meldung
	//$now = $date->toSQL();
	$now = clm_core::$cms->getNowDate();
	// Für Heimmannschaft updaten
	$query	= "UPDATE #__clm_rnd_man"
		." SET gemeldet = ".$meldung
		." , zeit = '$now'"
		." , ergebnis = ".$hergebnis
		." , kampflos = ".$hkampflos
		." , brettpunkte = ".$hmpunkte
		." , manpunkte = ".$hman_punkte
		." , wertpunkte = ".$hwpunkte
		." , comment = '".$comment."'"
		." , icomment = '".$icomment."'"
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		;
	$db->setQuery($query);
	clm_core::$db->query($query);

	// Für Gastmannschaft updaten
	$query	= "UPDATE #__clm_rnd_man"
		." SET gemeldet = ".$meldung
		." , zeit = '$now'"
		." , ergebnis = ".$gergebnis
		." , kampflos = ".$gkampflos
		." , brettpunkte = ".$gmpunkte
		." , manpunkte = ".$gman_punkte
		." , wertpunkte = ".$gwpunkte
		." , comment = '".$comment."'"
		." , icomment = '".$icomment."'"
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 0 "
		;
	$db->setQuery($query);
	clm_core::$db->query($query);

	}

	// Runde bereits gemeldet
	else {
	// Datensätze in Spielertabelle schreiben
	for ($y=1; $y< (1+$stamm) ; $y++){ 
		$heim		= clm_core::$load->request_string( 'heim'.$y);
		$gast		= clm_core::$load->request_string( 'gast'.$y);
		$ergebnis	= clm_core::$load->request_string( 'ergebnis'.$y);
	$kampflos = 0;
	
	if ($ergebnis > 3 AND $ergebnis < 10) { $kampflos = 1; }
		else { $kampflos = 0; }
		
	if ($ergebnis == 1)
		{ 	$erg_h = $nieder+$antritt;
			$erg_g = $sieg+$antritt;
		}
	if ($ergebnis == 2)
		{ 	$erg_h = $sieg+$antritt;
			$erg_g = $nieder+$antritt;
		}
	if ($ergebnis == 3)
		{ 	$erg_h = $remis+$antritt;
			$erg_g = $remis+$antritt;
		}
	if ($ergebnis == 4)
		{ 	$erg_h = $antritt;
			$erg_g = $antritt;
		}
	if ($ergebnis == 5)
		{ 	$erg_h = 0;
			$erg_g = $sieg+$antritt;
		}
	if ($ergebnis == 6)
		{ 	$erg_h = $sieg+$antritt;
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
	if ($ergebnis == 9)
		{ 	$erg_h = 0;
			$erg_g = 0;
		}
	if ($ergebnis == 10)
		{ 	$erg_h = $nieder+$antritt;
			$erg_g = $remis+$antritt;
		}
	if ($ergebnis == 11)
		{ 	$erg_h = $remis+$antritt;
			$erg_g = $nieder+$antritt;
		}
	// WICHTIG wegen NULL / SELECTED Problem
	$ergebnis--;

	$theim	= explode("-", $heim);
	$tgast	= explode("-", $gast);
	if ($countryversion == "de") {
		$thmgl	= $theim[0];
		$tgmgl	= $tgast[0];
		$thPKZ  = '';
		$tgPKZ  = '';
	} else {
		$thmgl	= 0;
		$tgmgl	= 0;
		$thPKZ  = $theim[0];
		$tgPKZ  = $tgast[0];
	}
	$thzps	= $theim[1];
	$tgzps	= $tgast[1];
	if ($thzps == '') $thzps = '-1';
	if ($tgzps == '') $tgzps = '-1';

	// Heim updaten
	$query	= "UPDATE #__clm_rnd_spl "
		." SET spieler = ".$thmgl
		." , PKZ = '$thPKZ'"
		." , zps = '$thzps'"
		." , gegner = ".$tgmgl
		." , gPKZ = '$tgPKZ'"
		." , gzps = '$tgzps'"
		." , ergebnis = ".$ergebnis
		." , kampflos = ".$kampflos
		." , punkte = ".$erg_h
		." , tln_nr = ".$id_tln
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND brett = ".$y
		." AND heim = 1 "
		;
	$db->setQuery($query);
	clm_core::$db->query($query);

	// Gast updaten
	$query	= "UPDATE #__clm_rnd_spl "
		." SET spieler = ".$tgmgl
		." , PKZ = '$tgPKZ'"
		." , zps = '$tgzps'"
		." , gegner = ".$thmgl
		." , gPKZ = '$thPKZ'"
		." , gzps = '$thzps'"
		." , ergebnis = ".$ergebnis
		." , kampflos = ".$kampflos
		." , punkte = ". $erg_g
		." , tln_nr = ".$id_geg
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND brett = ".$y
		." AND heim = 0 "
		;
	$db->setQuery($query);
	clm_core::$db->query($query);

	}
	// Prüfen ob Turnierergebnis geändert wurde. Wenn ja dann keine MP oder BP updaten !
	$query = " SELECT COUNT(dwz_edit) as edit FROM #__clm_rnd_spl "
		." WHERE dwz_edit IS NOT NULL "
		." AND sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		;
	$db->setQuery( $query );
	$counter = $db->loadResult();

	if($counter =="0") {
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
	
	// Anzahl kampflose Partien (Heim) zählen
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
	
	// Anzahl kampflose Partien (Gast) zählen
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
	}
	// Nachricht absetzen als Hinweis das Ergebnis nicht geändert wurde
	else {
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_ME_WERTUNG'),'notice' );
	}

	// Teilnehmer ID bestimmen 
	$query = " SELECT a.tln_nr,a.gegner "
		." FROM #__clm_rnd_man as a"
		." WHERE a.id = ".$id_id
			;
	$db->setQuery( $query);
	$tlnr=$db->loadObjectList();
	$tln_nr	= $tlnr[0]->tln_nr;
	$gegner	= $tlnr[0]->gegner;
	$hkampflos = 0;
	$gkampflos = 0;
	$hergebnis = 0;
	$gergebnis = 0;

	// Mannschaftspunkte Heim / Gast
	$hman_punkte = 0;
	$gman_punkte = 0;
	if ( $hmpunkte > 0 OR $gmpunkte > 0) {
		// Mannschaftspunkte Heim / Gast
	// Standard : Mehrheit der BP gewinnt, BP gleich -> Punkteteilung
	if ($sieg_bed == 1) {
		if ( $hmpunkte >  $gmpunkte ) { 
			$hman_punkte = $man_sieg; 
			$gman_punkte = $man_nieder;
			$hergebnis = 1;
			$gergebnis = 0;
		}
		if ( $hmpunkte == $gmpunkte ) { 
			$hman_punkte = $man_remis; 
			$gman_punkte = $man_remis;
			$hergebnis = 2;
			$gergebnis = 2;
		}
		if ( $hmpunkte <  $gmpunkte ) { 
			$hman_punkte = $man_nieder; 
			$gman_punkte = $man_sieg;
			$hergebnis = 0;
			$gergebnis = 1;
		}
	}
	// erweiterter Standard : mehr als die H�lfte der BP -> Sieg, H�lfte der BP -> halbe MP Zahl
	if ($sieg_bed == 2) {
		if ( $hmpunkte >  (($stamm*($sieg+$antritt))/2) ) { $hman_punkte = $man_sieg; $hergebnis = 1;}
		if ( $hmpunkte == (($stamm*($sieg+$antritt))/2) ) { $hman_punkte = $man_remis; $hergebnis = 2;}
		if ( $hmpunkte <  (($stamm*($sieg+$antritt))/2) ) { $hman_punkte = $man_nieder; $hergebnis = 0;}
		
		if ( $gmpunkte >  (($stamm*($sieg+$antritt))/2) ) { $gman_punkte = $man_sieg; $gergebnis = 1;}
		if ( $gmpunkte == (($stamm*($sieg+$antritt))/2) ) { $gman_punkte = $man_remis; $gergebnis = 2;}
		if ( $gmpunkte <  (($stamm*($sieg+$antritt))/2) ) { $gman_punkte = $man_nieder; $gergebnis = 0;}
	}
	// Antrittspunkte addieren falls angetreten
	if ( $stamm > $man_kl_punkte ) { $hman_punkte = $hman_punkte + $man_antritt;}
	else { 
		$hkampflos = 1;
		$gkampflos = 1;
	}
	if ( $stamm > $gman_kl_punkte ) { $gman_punkte = $gman_punkte + $man_antritt;}
	else { 
		$hkampflos = 1;
		$gkampflos = 1;
	}
			if ($hkampflos == 1) {
				if ($hergebnis == 0) $hergebnis = 4;
				if ($hergebnis == 1) $hergebnis = 5;
				if ($hergebnis == 2) $hergebnis = 6;
			}
			if ($gkampflos == 1) {
				if ($gergebnis == 0) $gergebnis = 4;
				if ($gergebnis == 1) $gergebnis = 5;
				if ($gergebnis == 2) $gergebnis = 6;
			}
	}
	// Datum und Uhrzeit für Editorzeit
	$now = $date->toSQL();
	// Für Heimmannschaft updaten
	if (is_null($hmpunkte)) $hmpunkte = 0;	// nur zur Absicherung										
	$query	= "UPDATE #__clm_rnd_man"
		." SET editor = ".$meldung
		." , edit_zeit = '$now'"
		." , ergebnis = ".$hergebnis
		." , kampflos = ".$hkampflos;
		if($counter =="0") {
			$query = $query
			." , brettpunkte = ".$hmpunkte
			." , manpunkte = ".$hman_punkte
			." , wertpunkte = ".$hwpunkte;
			}
		$query = $query
		." , comment = '".$comment."'"
		." , icomment = '".$icomment."'"
		." , tln_nr = ".$tln_nr
		." , gegner = ".$gegner
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		;
	$db->setQuery($query);
	clm_core::$db->query($query);
	// Für Gastmannschaft updaten
	if (is_null($gmpunkte)) $gmpunkte = 0; // nur zur Absicherung									
	$query	= "UPDATE #__clm_rnd_man"
		." SET editor = ".$meldung
		." , edit_zeit = '$now'"
		." , ergebnis = ".$gergebnis
		." , kampflos = ".$gkampflos;
		if($counter =="0") {
			$query = $query
			." , brettpunkte = ".$gmpunkte
			." , manpunkte = ".$gman_punkte
			." , wertpunkte = ".$gwpunkte;
			}
		$query = $query
		." , comment = '".$comment."'"
		." , icomment = '".$icomment."'"
		." , tln_nr = ".$gegner
		." , gegner = ".$tln_nr
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 0 "
		;
	$db->setQuery($query);
	clm_core::$db->query($query);
	}
	if (($runden_modus == 4) OR ($runden_modus == 5)) {    // KO Turnier
		if (($runden_modus == 4) OR ($runden_modus == 5 and $rnd < $runden)) {    // KO Turnierif ($ko_decision == 1) {
			if ($ko_decision == 1) {
				if ($hmpunkte > $gmpunkte) $ko_par = 2;			// Sieger Heim nach Brettpunkte
				elseif ($hmpunkte < $gmpunkte) $ko_par = 3;		// Sieger Gast nach Brettpunkte
				elseif ($hwpunkte > $gwpunkte) $ko_par = 2;		// Sieger Heim nach Wertpunkte
				elseif ($hwpunkte < $gwpunkte) $ko_par = 3;		// Sieger Gast nach Wertpunkte
				else { $ko_par = 3;								// Sieger Gast nach Computer --> Nacharbeit durch TL
				     $comment = JText::_('ERGEBNISSE_KO_COMMENT').$comment; }
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
				." AND tln_nr = ".$tln_nr
				;
			$db->setQuery($query);
			clm_core::$db->query($query);

			$query	= "UPDATE #__clm_mannschaften"
				." SET rankingpos = ".$ko_gast
				." WHERE sid = ".$sid
				." AND liga = ".$lid
				." AND tln_nr = ".$gegner
				;
			$db->setQuery($query);
			clm_core::$db->query($query);	
		}	
			// Für Heimmannschaft updaten
		$query	= "UPDATE #__clm_rnd_man"
			." SET ko_decision = ".$ko_decision
			." , comment = '".$comment."'"
			." , icomment = '".$icomment."'"
			." WHERE sid = ".$sid
			." AND lid = ".$lid
			." AND runde = ".$rnd
			." AND paar = ".$paarung
			." AND dg = ".$dg
			." AND heim = 1 "
		;
		$db->setQuery($query);
		clm_core::$db->query($query);
		// Für Gastmannschaft updaten
		$query	= "UPDATE #__clm_rnd_man"
			." SET ko_decision = ".$ko_decision
			." , comment = '".$comment."'"
			." , icomment = '".$icomment."'"
			." WHERE sid = ".$sid
			." AND lid = ".$lid
			." AND runde = ".$rnd
			." AND paar = ".$paarung
			." AND dg = ".$dg
			." AND heim = 0 "
		;
		$db->setQuery($query);
		clm_core::$db->query($query);
	}
 	
	clm_core::$api->db_tournament_ranking($lid,true); 
	//CLMControllerErgebnisse::calculateRanking($sid,$lid);

	switch ($task)
	{
		case 'apply':
		$msg = JText::_( 'ERGEBNISSE_AENDERUNG' );
//		$link = 'index.php?option='.$option.'&section='.$section.'&task=edit&cid[]='.$id_id;
		$link = 'index.php?option='.$option.'&section='.$section.'&task=edit&id='.$id_id;
			break;
		case 'save':
		default:
		$row =JTable::getInstance( 'ergebnisse', 'TableCLM' );
		$msg = JText::_( 'ERGEBNISSE_GESPEICHERT' );
		$link = 'index.php?option='.$option.'&section='.$section;
			break;
	}
	
	// Log schreiben
	$clmLog = new CLMLog();
	if (!$id[0]->gemeldet) {
		$clmLog->aktion = JText::_( 'ERGEBNISSE_AKTION_GEMELDET' );
	} else { 
		$clmLog->aktion = JText::_( 'ERGEBNISSE_AKTION_EDIT' );
	}
	$clmLog->params = array('sid' => $sid, 'lid' => $lid, 'rnd' => $rnd, 'paar' => $paarung, 'dg' => $dg);
	$clmLog->write();

	// errechnte/aktualisiere Rangliste & inoff. DWZ falls eingestellt (autoDWZ, autoRANKING)
//	clm_core::$api->direct("db_tournament_auto",array($liga,true,true));
	clm_core::$api->direct("db_tournament_auto",array($lid,true,true));
 	
	$mainframe->enqueueMessage( $msg );
	$mainframe->redirect( $link );
	}


function cancel()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	defined('clm') or die('Restricted access');
	
	$option		= clm_core::$load->request_string('option');
	$section	= clm_core::$load->request_string('section');
	$id		= clm_core::$load->request_string('id');	
	$row 		=JTable::getInstance( 'ergebnisse', 'TableCLM' );

	$msg = JText::_( 'ERGEBNISSE_AKTION');
	$mainframe->enqueueMessage( $msg );
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

function wertung()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	defined('clm') or die('Restricted access');

	$db 		=JFactory::getDBO();
	$user 		=JFactory::getUser();
	$task 		= clm_core::$load->request_string( 'task');
	$option 	= clm_core::$load->request_string( 'option' );
	$section 	= clm_core::$load->request_string( 'section' );
	$cid 		= clm_core::$load->request_array_int('cid');

	if (count($cid) < 1) {
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_SELECT'),'warning' );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

	// load the row from the db table
	$row =JTable::getInstance( 'ergebnisse', 'TableCLM' );
	$row->load( $cid[0] );

	$sid =JTable::getInstance( 'saisons', 'TableCLM' );
	$sid->load($row->sid);

	// Ergebnisse einer unveröffentlichten Saison nicht bearbeiten
	if ($sid->published =="0") {
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_SAISON_NO'),'warning' );
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_SAISON_WARTEN'),'notice' );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

	// spielfreie Runde  kann nicht gemeldet / bearbeitet werden
	if ($row->gemeldet == "1") {
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_TW_RUNDEN'),'notice' );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

	$data = "SELECT a.gemeldet,a.editor, a.id,a.sid, a.lid, a.runde, a.dg, a.tln_nr, a.ko_decision, a.comment, a.icomment, "
		." a.gegner,a.paar, a.dwz_zeit, a.dwz_editor as dwz_edit, w.name as dwz_editor, "
		." a.zeit, a.edit_zeit, u.name as melder, v.name as name_editor, "
		." m.name as hname,m.zps as hzps,m.man_nr as hmnr, "
		." n.name as gname, n.zps as gzps, n.man_nr as gmnr, "
		." l.name as lname, l.stamm, l.ersatz, l.sl as sl, l.b_wertung, l.liga_mt, l.runden_modus "
		." FROM #__clm_rnd_man as a "
		." LEFT JOIN #__clm_user as u ON u.jid = a.gemeldet AND u.sid = a.sid "
		." LEFT JOIN #__clm_user as v ON v.jid = a.editor AND v.sid = a.sid "
		." LEFT JOIN #__clm_user as w ON w.jid = a.dwz_editor AND w.sid = a.sid "
		." LEFT JOIN #__clm_liga AS l ON (l.id = a.lid ) "
		." LEFT JOIN #__clm_mannschaften AS m ON (m.liga = a.lid AND m.tln_nr = a.tln_nr) "
		." LEFT JOIN #__clm_mannschaften AS n ON (n.liga = a.lid AND n.tln_nr = a.gegner) "
		." WHERE a.id = ".$cid[0]
		;
	$db->setQuery( $data);
	$runde		= $db->loadObjectList();
	// Prüfen ob Ergebnis bereits gemeldet wurde
	if ($runde[0]->gemeldet < 1) {
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_DWZ'),'warning' );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
	}

	// Prüfen ob User Berechtigung zum editieren hat
	$clmAccess = clm_core::$access;      
	if ($runde[0]->liga_mt == "0") {
		$mppoint = 'league';
		$csection = 'ligen';
	} else {
		$mppoint = 'teamtournament';
		$csection = 'mturniere';
	}

	if($clmAccess->access('BE_'.$mppoint.'_edit_result') === false) {
		$mainframe->enqueueMessage( JText::_('LIGEN_STAFFEL_TOTAL'),'warning' );
		$section = 'runden';
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	} 
	if ($runde[0]->sl !== clm_core::$access->getJid() AND $clmAccess->access('BE_'.$mppoint.'_edit_result') !== true) {
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_DWZ_BEARBEIT'),'warning' );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}
	$row->checkout( clm_core::$access->getJid() );

	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;
	// Bretter / Spieler ermitteln
	$sql = "SELECT a.spieler, a.PKZ, a.gegner, a.gPKZ, a.ergebnis, a.punkte, g.punkte as gpunkte, a.dwz_edit, d.Spielername as hname, e.Spielername as gname, a.brett "
		." FROM #__clm_rnd_spl as a ";
	if ($countryversion =="de") {
		$sql .= " LEFT JOIN #__clm_dwz_spieler as d ON d.ZPS = a.zps AND d.Mgl_Nr = a.spieler AND d.sid = a.sid "
				." LEFT JOIN #__clm_dwz_spieler as e ON e.ZPS = a.gzps AND e.Mgl_Nr = a.gegner AND e.sid = a.sid ";
	} else {
		$sql .= " LEFT JOIN #__clm_dwz_spieler as d ON d.ZPS = a.zps AND d.PKZ = a.PKZ AND d.sid = a.sid "
				." LEFT JOIN #__clm_dwz_spieler as e ON e.ZPS = a.gzps AND e.PKZ = a.gPKZ AND e.sid = a.sid ";
	} 
	$sql .= " LEFT JOIN #__clm_rnd_spl as g ON g.lid = a.lid AND g.runde = a.runde AND g.paar = a.paar AND g.dg = a.dg AND g.brett = a.brett AND g.heim = 0 ";
	$sql .= " WHERE a.sid = ".$runde[0]->sid
		." AND a.lid = ".$runde[0]->lid
		." AND a.runde = ".$runde[0]->runde
		." AND a.paar = ".$runde[0]->paar
		." AND a.dg = ".$runde[0]->dg
		." AND a.heim = 1"
		." ORDER BY a.brett"
		;
	$bretter	= clm_core::$db->loadObjectList($sql);
	
	// Ermittlung der Vergleichswerte;
	$hpsum = 0; $gpsum = 0; $hwsum = 0; $gwsum = 0;
	foreach ($bretter as $brett) {
		$hpsum += $brett->punkte;
		$hwsum += (($runde[0]->stamm + 1 - $brett->brett) * $brett->punkte);
		$gpsum += $brett->gpunkte;
		$gwsum += (($runde[0]->stamm + 1 - $brett->brett) * $brett->gpunkte);
	}

	// Ergebnisliste laden
	$sql = "SELECT a.id, a.eid,a.erg_text "
		." FROM #__clm_ergebnis as a "
		;
	$db->setQuery( $sql );
	$ergebnis=$db->loadObjectList();
	
	// Punktemodus aus #__clm_liga holen
	$query = " SELECT a.sieg, a.remis, a.nieder, a.antritt, a.runden_modus "
		." FROM #__clm_liga as a"
		." WHERE a.id = ".$runde[0]->lid
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
		$ergebnis[4]->erg_text = "0 - ".round($antritt+$sieg)." (kampflos)";
		$ergebnis[5]->erg_text = round($antritt+$sieg)." - 0 (kampflos)";
		$ergebnis[6]->erg_text = "0 - 0 (kampflos)";
		}

	// Listen zur manuellen Änderung des Mannschaftsergebnisses generieren
	$sql = "SELECT a.brettpunkte as bp, a.wertpunkte as wp "
		." FROM #__clm_rnd_man as a "
		." WHERE a.sid = ".$runde[0]->sid
		." AND a.lid = ".$runde[0]->lid
		." AND a.runde = ".$runde[0]->runde
		." AND a.paar = ".$runde[0]->paar
		." AND a.tln_nr = ".$runde[0]->tln_nr
		." AND a.dwz_editor > 0"
		;
	$list_heim	= clm_core::$db->loadObjectList($sql);

	$sql = "SELECT a.brettpunkte as bp, a.wertpunkte as wp "
		." FROM #__clm_rnd_man as a "
		." WHERE a.sid = ".$runde[0]->sid
		." AND a.lid = ".$runde[0]->lid
		." AND a.runde = ".$runde[0]->runde
		." AND a.paar = ".$runde[0]->paar
		." AND a.gegner = ".$runde[0]->tln_nr
		." AND a.dwz_editor > 0"
		;
	$list_gast	= clm_core::$db->loadObjectList($sql);

	// Werteliste für Brettpunkte
	$wlist[]	= JHTML::_('select.option',  '-1', JText::_( 'ERGEBNISSE_SUMME' ), 'jid', 'name' );
	$wlist[]	= JHTML::_('select.option',  floatval(0), JText::_( '0' ), 'jid', 'name' );
	$until = 1+(($sieg+$antritt)*$runde[0]->stamm);
	if ($countryversion =="en" AND ($runde[0]->runden_modus == 4 OR $runde[0]->runden_modus == 5)) {
		$until = 1+(($sieg+$antritt)*$runde[0]->stamm*2);
	}
	for($x=1; $x< $until; $x++) {
		$wlist[]	= JHTML::_('select.option',  floatval($x-(0.5)), floatval($x-(0.5)), 'jid', 'name' );
		$wlist[]	= JHTML::_('select.option',  floatval($x), floatval($x), 'jid', 'name' );
	}
	// Werteliste für Wertpunkte (Berliner Wertung)
	for($x=1; $x<= $runde[0]->stamm; $x++) {
		if ($x == 1) $bw_max = 0;
		$bw_max += $x;
	}
	$bwlist[]	= JHTML::_('select.option',  '-1', JText::_( 'ERGEBNISSE_SUMME' ), 'jid', 'name' );
	$bwlist[]	= JHTML::_('select.option',  floatval(0), JText::_( '0' ), 'jid', 'name' );
	for($x=1; $x<= $bw_max; $x++) {
		$bwlist[]	= JHTML::_('select.option',  floatval($x-(0.5)), floatval($x-(0.5)), 'jid', 'name' );
		$bwlist[]	= JHTML::_('select.option',  floatval($x), floatval($x), 'jid', 'name' );
	}
	
	// Aufbereitung Eingabefelder für Brett- und Wertpunkte
	if (isset($list_heim[0]) AND $list_heim[0]->bp != $hpsum) 
		$lists['weiss']		= JHTML::_('select.genericlist',   $wlist, 'w_erg', 'class="inputbox" size="1"', 'jid', 'name', floatval($list_heim[0]->bp ));
	else
		$lists['weiss']		= JHTML::_('select.genericlist',   $wlist, 'w_erg', 'class="inputbox" size="1"', 'jid', 'name', '-1');
	
	if (isset($list_heim[0]) AND $list_heim[0]->wp != $hwsum) 
		$lists['weiss_w']		= JHTML::_('select.genericlist',   $bwlist, 'ww_erg', 'class="inputbox" size="1"', 'jid', 'name', floatval($list_heim[0]->wp ));
	else
		$lists['weiss_w']		= JHTML::_('select.genericlist',   $bwlist, 'ww_erg', 'class="inputbox" size="1"', 'jid', 'name', '-1' );
	
	if (isset($list_gast[0]) AND $list_gast[0]->bp != $gpsum) 
		$lists['schwarz']		= JHTML::_('select.genericlist',   $wlist, 's_erg', 'class="inputbox" size="1"', 'jid', 'name', floatval($list_gast[0]->bp ));
	else
		$lists['schwarz']		= JHTML::_('select.genericlist',   $wlist, 's_erg', 'class="inputbox" size="1"', 'jid', 'name', '-1');
	
	if (isset($list_gast[0]) AND $list_gast[0]->wp != $gwsum) 
		$lists['schwarz_w']		= JHTML::_('select.genericlist',   $bwlist, 'sw_erg', 'class="inputbox" size="1"', 'jid', 'name', floatval($list_gast[0]->wp ));
	else
		$lists['schwarz_w']		= JHTML::_('select.genericlist',   $bwlist, 'sw_erg', 'class="inputbox" size="1"', 'jid', 'name', '-1' );
	require_once(JPATH_COMPONENT.DS.'views'.DS.'ergebnisse.php');
	CLMViewErgebnisse::wertung( $row, $runde,$bretter,$ergebnis, $option, $lists);
	}

function save_wertung()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	defined('clm') or die('Restricted access');

	$option		= clm_core::$load->request_string('option');
	$section	= clm_core::$load->request_string('section');
	$db 		= JFactory::getDBO();
	$task 		= clm_core::$load->request_string('task');
	$user 		= JFactory::getUser();
	$id_id 		= clm_core::$load->request_string('id');
	$date 		= JFactory::getDate();

	$meldung 	= $user->get('id');
	$sid		= clm_core::$load->request_int('sid');
	$lid 		= clm_core::$load->request_int('lid');
	$rnd		= clm_core::$load->request_int('rnd');
	$paarung	= clm_core::$load->request_int('paarung');
	$dg			= clm_core::$load->request_int('dg');
	$hzps		= clm_core::$load->request_string('hzps');
	$gzps		= clm_core::$load->request_string('gzps');

	$w_erg		= clm_core::$load->request_string('w_erg');
	$s_erg		= clm_core::$load->request_string('s_erg');
	$ww_erg		= clm_core::$load->request_string('ww_erg',-1);
	$sw_erg		= clm_core::$load->request_string('sw_erg',-1);
	$ko_decision = clm_core::$load->request_string( 'ko_decision');
	$comment = addslashes(clm_core::$load->request_string( 'comment'));
	
	// Punktemodus aus #__clm_liga holen
	$query = " SELECT a.stamm, a.sieg, a.sieg_bed, a.remis, a.nieder, a.antritt, a.runden_modus, "
		." a.man_sieg, a.man_remis, a.man_nieder, a.man_antritt "
		." FROM #__clm_liga as a"
		." WHERE a.id = ".$lid
		;
	$db->setQuery($query);
	$liga = $db->loadObjectList();
		$stamm 		= $liga[0]->stamm;
		$sieg 		= $liga[0]->sieg;
		$sieg_bed	= $liga[0]->sieg_bed;
		$remis 		= $liga[0]->remis;
		$nieder		= $liga[0]->nieder;
		$antritt	= $liga[0]->antritt;
		$man_sieg 	= $liga[0]->man_sieg;
		$man_remis 	= $liga[0]->man_remis;
		$man_nieder	= $liga[0]->man_nieder;
		$man_antritt	= $liga[0]->man_antritt;
		$runden_modus	= $liga[0]->runden_modus;

	// Arrays zur Punktevergabe
	$heim_erg = array();
		$heim_erg[-1]="NULL";
		$heim_erg[0]=$nieder+$antritt;
		$heim_erg[1]=$sieg+$antritt;
		$heim_erg[2]=$remis+$antritt;
		$heim_erg[3]=$antritt;
		$heim_erg[4]="0";
		$heim_erg[5]=$sieg+$antritt;
		$heim_erg[6]="0";
		$heim_erg[7]="0";
		$heim_erg[8]="0";
		$heim_erg[9]=$nieder+$antritt;
		$heim_erg[10]=$remis+$antritt;

	$gast_erg = array();
		$gast_erg[-1]="NULL";
		$gast_erg[0]=$sieg+$antritt;
		$gast_erg[1]=$nieder+$antritt;
		$gast_erg[2]=$remis+$antritt;
		$gast_erg[3]=$antritt;
		$gast_erg[4]=$sieg+$antritt;
		$gast_erg[5]="0";
		$gast_erg[6]="0";
		$gast_erg[7]="0";
		$gast_erg[8]="0";
		$gast_erg[9]=$remis+$antritt;
		$gast_erg[10]=$nieder+$antritt;

	// Anzahl kampflose Partien (Heim) z�hlen
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

	// Anzahl kampflose Partien (Gast) z�hlen
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

	$count_einzel = 0;
	// Datensätze in Spielertabelle schreiben
	for ($y=1; $y< (1+$stamm) ; $y++){
		$ergebnis	= clm_core::$load->request_string( 'ergebnis'.$y);

	if ($ergebnis > 3 AND $ergebnis < 9) { $kampflos = 1; }
	else { $kampflos = 0; }

	// Wenn Ergebnis nicht verändert dann Original verwenden
	$change = 0;
	if ($ergebnis =="-1") {
	$change = 1;
	$query	= "SELECT ergebnis, kampflos FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND brett = ".$y
		." AND heim = 1 "
		;
	$db->setQuery($query);
	$original	= $db->loadObjectList();
	$org_erg 	= $original[0]->ergebnis;
	$kampflos 	= $original[0]->kampflos;
		}
	// Counter für geänderte Einzelergebnisse
	else { $count_einzel++; }

	// Ergebnis verändert, Eingabe verwenden
	// Heim updaten
	$query	= "UPDATE #__clm_rnd_spl ";
	if ($change == "1") {
		$query = $query
		." SET dwz_edit = NULL"
		." , dwz_editor = NULL"
		." , punkte = ".$heim_erg[$org_erg]
		." , kampflos = ".$kampflos;
		} else {
		$query = $query
		." SET dwz_edit = ".$ergebnis
		." , dwz_editor = ".$meldung
		." , punkte = ".$heim_erg[$ergebnis]
		." , kampflos = ".$kampflos;
		}
		$query = $query
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND brett = ".$y
		." AND heim = 1 "
		;
	$db->setQuery($query);
	clm_core::$db->query($query);

	// Gast updaten
	$query	= "UPDATE #__clm_rnd_spl ";
	if ($change == "1") {
		$query = $query
		." SET dwz_edit = NULL"
		." , dwz_editor = NULL"
		." , punkte = ".$gast_erg[$org_erg]
		." , kampflos = ".$kampflos;
		} else {
		$query = $query
		." SET dwz_edit = ".$ergebnis
		." , dwz_editor = ".$meldung
		." , punkte = ".$gast_erg[$ergebnis]
		." , kampflos = ".$kampflos;
		}
		$query = $query
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND brett = ".$y
		." AND heim = 0 "
		;
	$db->setQuery($query);
	clm_core::$db->query($query);
	}

	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;
	// Optionales Mannschaftsergebnis prüfen ggf. Nachricht absetzen
	if ($countryversion == "en" AND ($runden_modus == 4 OR $runden_modus == 5)) $limit = $stamm * 2;
//	else $limit = $stamm;
	else $limit = $stamm * ($sieg+$antritt);
	$err = 0;
	if($w_erg + $s_erg > $limit ) {
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_ME_HOCH'),'warning' );
		$err=1;
	}
	if($w_erg =="-1" AND $s_erg !="-1" ) {
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_GEAENDERT_HM'),'warning' );
	}
	if($w_erg !="-1" AND $s_erg =="-1" ) {
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_GEAENDERT_GM'),'warning' );
	}
	if($count_einzel > 0) {
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_EE'),'notice' );
	}
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
	$man		= $db->loadObjectList();
	$hmpunkte	= $man[0]->punkte;

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
	$gman		= $db->loadObjectList();
	$gmpunkte	= $gman[0]->punkte;
	
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
	
	//} else {
	if ($w_erg != -1) $hmpunkte = $w_erg;
	if ($s_erg != -1) $gmpunkte = $s_erg;
	if ($ww_erg != -1) $hwpunkte = $ww_erg;
	if ($sw_erg != -1) $gwpunkte = $sw_erg;
	//}
	// Mannschaftspunkte Heim / Gast
	// Standard : Mehrheit der BP gewinnt, BP gleich -> Punkteteilung
	if ($sieg_bed == 1) {
		if ( $hmpunkte >  $gmpunkte ) { $hman_punkte = $man_sieg; $gman_punkte = $man_nieder;}
		if ( $hmpunkte == $gmpunkte AND $hmpunkte > 0) { $hman_punkte = $man_remis; $gman_punkte = $man_remis;}
		if ( $hmpunkte == $gmpunkte AND $hmpunkte == 0) { $hman_punkte = $man_nieder; $gman_punkte = $man_nieder;}
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
	$now = $date->toSQL();

	// Für Heimmannschaft updaten
	$query	= "UPDATE #__clm_rnd_man";
		// Wenn nichts geändert wurde (keine Einzelergebnis, keine Mannschaftswertung)
		if($w_erg =="-1" AND $s_erg =="-1" AND $ww_erg =="-1" AND $sw_erg =="-1" AND $count_einzel =="0") {
			$mainframe->enqueueMessage( JText::_('ERGEBNISSE_TW_GELOESCHT'),'notice' );
			$query = $query
			." SET dwz_editor = NULL"
			." , dwz_zeit = '1970-01-01 00:00:00'";
		} else {
			$query = $query
			." SET dwz_editor = ".$meldung
			." , dwz_zeit = '$now'";
			}
		$query = $query
		." , brettpunkte = '".$hmpunkte."'"
		." , manpunkte = '".$hman_punkte."'"
		." , wertpunkte = '".$hwpunkte."'"
		." , comment = '".$comment."'"
		." , icomment = '".$icomment."'"
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		;
	clm_core::$db->query($query);

	// Für Gastmannschaft updaten
	$query	= "UPDATE #__clm_rnd_man";
		if($w_erg =="-1" AND $s_erg =="-1" AND $ww_erg =="-1" AND $sw_erg =="-1" AND $count_einzel =="0") {
			$query = $query
			." SET dwz_editor = NULL"
			." , dwz_zeit = '1970-01-01 00:00:00'";
		} else {
			$query = $query
			." SET dwz_editor = ".$meldung
			." , dwz_zeit = '$now'";
			}
		$query = $query
		." , brettpunkte = '".$gmpunkte."'"
		." , manpunkte = '".$gman_punkte."'"
		." , wertpunkte = '".$gwpunkte."'"
		." , comment = '".$comment."'"
		." , icomment = '".$icomment."'"
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 0 "
		;
	clm_core::$db->query($query);

		if (($runden_modus == 4) OR ($runden_modus == 5)) {    // KO Turnier
		if (($runden_modus == 4) OR ($runden_modus == 5 and $rnd < $runden)) {    // KO Turnierif ($ko_decision == 1) {
			if ($ko_decision == 1) {
				if ($hmpunkte > $gmpunkte) $ko_par = 2;			// Sieger Heim nach Brettpunkte
				elseif ($hmpunkte < $gmpunkte) $ko_par = 3;		// Sieger Gast nach Brettpunkte
				elseif ($hwpunkte > $gwpunkte) $ko_par = 2;		// Sieger Heim nach Wertpunkte
				elseif ($hwpunkte < $gwpunkte) $ko_par = 3;		// Sieger Gast nach Wertpunkte
				else { $ko_par = 3;								// Sieger Gast nach Computer --> Nacharbeit durch TL
				     if ($comment == '') $comment = JText::_('ERGEBNISSE_KO_COMMENT').$comment; }
			}
			elseif ($ko_decision == 2) $ko_par = 2;				// Sieger Heim nach Blitz-Entscheid
			elseif ($ko_decision == 4) $ko_par = 2;				// Sieger Heim nach Los-Entscheid
			else $ko_par = 3;									// Sieger Gast nach Blitz-,Los-Entscheid
			if ($ko_par == 2) { $ko_heim = $rnd; $ko_gast = $rnd -1; }
			else { $ko_heim = $rnd -1; $ko_gast = $rnd; }

	// Teilnehmer ID bestimmen 
	$query = " SELECT a.tln_nr,a.gegner "
		." FROM #__clm_rnd_man as a"
		." WHERE a.id = ".$id_id
			;
	$db->setQuery( $query);
	$tlnr=$db->loadObjectList();
	$tln_nr	= $tlnr[0]->tln_nr;
	$gegner	= $tlnr[0]->gegner;
			// Für Heimmannschaft updaten
			$query	= "UPDATE #__clm_mannschaften"
				." SET rankingpos = ".$ko_heim
				." WHERE sid = ".$sid
				." AND liga = ".$lid
				." AND tln_nr = ".$tln_nr
				;
			$db->setQuery($query);
			clm_core::$db->query($query);

			$query	= "UPDATE #__clm_mannschaften"
				." SET rankingpos = ".$ko_gast
				." WHERE sid = ".$sid
				." AND liga = ".$lid
				." AND tln_nr = ".$gegner
				;
			$db->setQuery($query);
			clm_core::$db->query($query);	
		}	
			// Für Heimmannschaft updaten
		$query	= "UPDATE #__clm_rnd_man"
			." SET ko_decision = ".$ko_decision
			." , comment = '".$comment."'"
			." , icomment = '".$icomment."'"
			." WHERE sid = ".$sid
			." AND lid = ".$lid
			." AND runde = ".$rnd
			." AND paar = ".$paarung
			." AND dg = ".$dg
			." AND heim = 1 "
		;
		$db->setQuery($query);
		clm_core::$db->query($query);
		// Für Gastmannschaft updaten
		$query	= "UPDATE #__clm_rnd_man"
			." SET ko_decision = ".$ko_decision
			." , comment = '".$comment."'"
			." , icomment = '".$icomment."'"
			." WHERE sid = ".$sid
			." AND lid = ".$lid
			." AND runde = ".$rnd
			." AND paar = ".$paarung
			." AND dg = ".$dg
		." AND heim = 0 "
		;
		$db->setQuery($query);
		clm_core::$db->query($query);
	}

	// errechnet/aktulisiert Rangliste/Punktesummen
	clm_core::$api->db_tournament_ranking($lid,true); 

	$msg = JText::_( 'ERGEBNISSE_AW' );
	$link = 'index.php?option='.$option.'&section='.$section;

	switch ($task)
	{
		case 'apply_wertung':
		$msg = JText::_( 'ERGEBNISSE_TW_ANGEWENDET' );
//		$link = 'index.php?option='.$option.'&section='.$section.'&task=wertung&cid[]='.$id_id;
		$link = 'index.php?option='.$option.'&section='.$section.'&task=wertung&id='.$id_id;
			break;
		case 'save_wertung':
		default:
		$row =JTable::getInstance( 'ergebnisse', 'TableCLM' );
		$msg = JText::_( 'ERGEBNISSE_TW_GESPEICHERT' );
		$link = 'index.php?option='.$option.'&section='.$section;
			break;
	}

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'ERGEBNISSE_AKTION_VALUATION' );
	$clmLog->params = array('sid' => $sid, 'lid' => $lid, 'rnd' => $rnd, 'paar' => $paarung, 'dg' => $dg);
	$clmLog->write();
	
	$mainframe->enqueueMessage( $msg );
	$mainframe->redirect( $link );
	}

function delete_wertung()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	defined('clm') or die('Restricted access');

	$option		= clm_core::$load->request_string('option');
	$section	= clm_core::$load->request_string('section');
	$db 		=JFactory::getDBO();
	$task 		= clm_core::$load->request_string( 'task');
	$user 		=JFactory::getUser();
	$id_id 		= clm_core::$load->request_string( 'id');
	$date 		=JFactory::getDate();

	$meldung 	= $user->get('id');
	$sid		= clm_core::$load->request_int( 'sid');
	$lid 		= clm_core::$load->request_int( 'lid');
	$rnd		= clm_core::$load->request_int( 'rnd');
	$paarung	= clm_core::$load->request_int( 'paarung');
	$dg			= clm_core::$load->request_int( 'dg');
	$hzps		= clm_core::$load->request_string( 'hzps');
	$gzps		= clm_core::$load->request_string( 'gzps');

	$liga_sl	=JTable::getInstance( 'ligen', 'TableCLM' );
	$liga_sl->load( $lid );

	// Prüfen ob User Berechtigung zum löschen hat
	$clmAccess = clm_core::$access;      
	if ($liga_sl->liga_mt == "0") {
		$mppoint = 'league';
		$csection = 'ligen';
	} else {
		$mppoint = 'teamtournament';
		$csection = 'mturniere';
	}
	if($clmAccess->access('BE_'.$mppoint.'_edit_result') === false) {
		$mainframe->enqueueMessage( JText::_('LIGEN_STAFFEL_TOTAL'),'warning' );
		$section = 'runden';
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	} 
	if ($liga_sl->sl !== clm_core::$access->getJid() AND $clmAccess->access('BE_'.$mppoint.'_edit_result') !== true) {
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_DWZ_LOESCHEN'),'warning' );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
	}

	// Datum und Uhrzeit für Editorzeit
	$now = $date->toSQL();

	// Mannschaftsergebnis holen
		$stamm 		= $liga_sl->stamm;
		$sieg 		= $liga_sl->sieg;
		$sieg_bed	= $liga_sl->sieg_bed;
		$remis 		= $liga_sl->remis;
		$nieder		= $liga_sl->nieder;
		$antritt	= $liga_sl->antritt;
		$man_sieg 	= $liga_sl->man_sieg;
		$man_remis 	= $liga_sl->man_remis;
		$man_nieder	= $liga_sl->man_nieder;
		$man_antritt	= $liga_sl->man_antritt;

	// Arrays zur Punktevergabe
	$heim_erg = array();
		$heim_erg[-1]="NULL";
		$heim_erg[0]=$nieder+$antritt;
		$heim_erg[1]=$sieg+$antritt;
		$heim_erg[2]=$remis+$antritt;
		$heim_erg[3]=$antritt;
		$heim_erg[4]="0";
		$heim_erg[5]=$sieg+$antritt;
		$heim_erg[6]="0";
		$heim_erg[7]="0";
		$heim_erg[8]="0";

	$gast_erg = array();
		$gast_erg[-1]="NULL";
		$gast_erg[0]=$sieg+$antritt;
		$gast_erg[1]=$nieder+$antritt;
		$gast_erg[2]=$remis+$antritt;
		$gast_erg[3]=$antritt;
		$gast_erg[4]=$sieg+$antritt;
		$gast_erg[5]="0";
		$gast_erg[6]="0";
		$gast_erg[7]="0";
		$gast_erg[8]="0";

	// Anzahl kampflose Partien (Heim) zählen
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

	// Anzahl kampflose Partien (Gast) zählen
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

	// Ergebnisse holen
	$query	= "SELECT ergebnis, brett FROM #__clm_rnd_spl "
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		." ORDER BY brett ASC "
		;
	$db->setQuery($query);
	$original	= $db->loadObjectList();

	// Ergebnisse summieren
	for ($y=0; $y< ($liga_sl->stamm) ; $y++){
	$hmpunkte = $hmpunkte + $heim_erg[$original[$y]->ergebnis];
	$gmpunkte = $gmpunkte + $gast_erg[$original[$y]->ergebnis];
	$hwpunkte = $hwpunkte + ($heim_erg[$original[$y]->ergebnis] * ($liga_sl->stamm + 1 - $original[$y]->brett));
	$gwpunkte = $gwpunkte + ($gast_erg[$original[$y]->ergebnis] * ($liga_sl->stamm + 1 - $original[$y]->brett));
	}
	
	// Mannschaftspunkte Heim / Gast
	// Standard : Mehrheit der BP gewinnt, BP gleich -> Punkteteilung
	if ($sieg_bed == 1) {
		if ( $hmpunkte >  $gmpunkte ) { $hman_punkte = $man_sieg; $gman_punkte = $man_nieder;}
		if ( $hmpunkte == $gmpunkte ) { $hman_punkte = $man_remis; $gman_punkte = $man_remis;}
		if ( $hmpunkte <  $gmpunkte ) { $hman_punkte = $man_nieder; $gman_punkte = $man_sieg;}
	}
	// erweiterter Standard : mehr als die Hälfte der BP -> Sieg, Hälfte der BP -> halbe MP Zahl
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

	// Für Heimmannschaft updaten
	$query	= "UPDATE #__clm_rnd_man"
		." SET dwz_editor = NULL"
		." , dwz_zeit = '1970-01-01 00:00:00'"
		." , brettpunkte = '".$hmpunkte."'"
		." , manpunkte = '".$hman_punkte."'"
		." , wertpunkte = '".$hwpunkte."'"
		." , comment = '".$comment."'"
		." , icomment = '".$icomment."'"
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		;
	$db->setQuery($query);
	clm_core::$db->query($query);

	// Für Gastmannschaft updaten
	$query	= "UPDATE #__clm_rnd_man"
		." SET dwz_editor = NULL"
		." , dwz_zeit = '1970-01-01 00:00:00'"
		." , brettpunkte = '".$gmpunkte."'"
		." , manpunkte = '".$gman_punkte."'"
		." , wertpunkte = '".$gwpunkte."'"
		." , comment = '".$comment."'"
		." , icomment = '".$icomment."'"
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 0 "
		;
	$db->setQuery($query);
	clm_core::$db->query($query);

	// Datensätze in Spielertabelle schreiben
	$query	=" UPDATE #__clm_rnd_spl "
		." SET dwz_editor = NULL"
		." , dwz_edit = NULL"
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		;
	$db->setQuery($query);
	clm_core::$db->query($query);

	// errechnet/aktulisiert Rangliste/Punktesummen
	clm_core::$api->db_tournament_ranking($lid,true); 

	$msg = JText::_( 'ERGEBNISSE_AW_GELOESCHT' );
	$link = 'index.php?option='.$option.'&section='.$section;

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'ERGEBNISSE_AKTION_VALUATION_DEL' );
	$clmLog->params = array('sid' => $sid, 'lid' => $lid, 'rnd' => $rnd, 'paar' => $paarung, 'dg' => $dg);
	$clmLog->write();
	
	$mainframe->enqueueMessage( $msg );
	$mainframe->redirect( $link );
	}

function back()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	defined('clm') or die('Restricted access');

	$option		= clm_core::$load->request_string('option');
	$link = 'index.php?option='.$option.'&section=runden';
	//$mainframe->redirect( $link, $msg );
	$mainframe->redirect( $link );
	}

function gast_kampflos()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	defined('clm') or die('Restricted access');

	$option		= clm_core::$load->request_string('option');
	$section	= clm_core::$load->request_string('section');
	$link		= 'index.php?option='.$option.'&section='.$section;

	$gast =JText::_( 'ERGEBNISSE_MSG_GUEST' );
	CLMControllerErgebnisse::kampflos($gast);

	$msg	= JText::_( 'ERGEBNISSE_MSG_GUEST_KL' );
	$mainframe->enqueueMessage( $msg );
	$mainframe->redirect( $link );
	}

function heim_kampflos()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	defined('clm') or die('Restricted access');

	$option		= clm_core::$load->request_string('option');
	$section	= clm_core::$load->request_string('section');
	$link		= 'index.php?option='.$option.'&section='.$section;

	$gast = JText::_( 'ERGEBNISSE_MSG_HOME' );
	CLMControllerErgebnisse::kampflos($gast);

	$msg	= JText::_( 'ERGEBNISSE_MSG_HOME_KL' );
	$mainframe->enqueueMessage( $msg );
	$mainframe->redirect( $link );
	}

function kampflos($gast)
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	defined('clm') or die('Restricted access');

	$option		= clm_core::$load->request_string('option');
	$section	= clm_core::$load->request_string('section');
	$db 		= JFactory::getDBO();
	$link		= 'index.php?option='.$option.'&section='.$section;
	$cid 		= clm_core::$load->request_array_int('cid');
 
 if (count($cid) < 1) {
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_SELECT'), 'warning' );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

	// load the row from the db table
	$row =JTable::getInstance( 'ergebnisse', 'TableCLM' );
	$row->load( $cid[0] );
		$sid	= $row->sid;
		$lid 	= $row->lid;
		$rnd	= $row->runde;
		$paar	= $row->paar;
		$dg	= $row->dg;

	$liga_sl	=JTable::getInstance( 'ligen', 'TableCLM' );
	$liga_sl->load( $row->lid );
		$bretter	= $liga_sl->stamm;
		$sieg		= $liga_sl->sieg;
		$antritt	= $liga_sl->antritt;
		$man_sieg	= $liga_sl->man_sieg;
		$man_antritt	= $liga_sl->man_antritt;
	//Liga-Parameter aufbereiten
		$paramsStringArray = explode("\n", $liga_sl->params);
		$params = array();
		foreach ($paramsStringArray as $value) {
			$ipos = strpos ($value, '=');
			if ($ipos !==false) {
				$key = substr($value,0,$ipos);
				$params[$key] = substr($value,$ipos+1);
			}
		}	
		if (!isset($params['noOrgReference']))  {   //Standardbelegung
			$params['noOrgReference'] = '0'; }

	// Prüfen ob User Berechtigung zum editieren hat
	$clmAccess = clm_core::$access;      
	if ($liga_sl->liga_mt == "0") {
		$mppoint = 'league';
		$csection = 'ligen';
	} else {
		$mppoint = 'teamtournament';
		$csection = 'mturniere';
	}
	if($clmAccess->access('BE_'.$mppoint.'_edit_result') === false) {
		$mainframe->enqueueMessage( JText::_('LIGEN_STAFFEL_TOTAL'), 'warning' );
		$section = 'runden';
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	} 
	if ($liga_sl->sl !== clm_core::$access->getJid() AND $clmAccess->access('BE_'.$mppoint.'_edit_result') !== true) {
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_LIGEN_ARBEIT'),'warning' );
		$mainframe->redirect( $link);
					}

	$query	=" SELECT a.sid,a.lid,a.runde, a.paar,a.dg,a.heim,a.tln_nr, a.gegner, m.zps as hzps, g.zps as gzps FROM #__clm_rnd_man as a "
		." LEFT JOIN #__clm_mannschaften as m ON m.liga = a.lid AND m.sid = a.sid AND m.tln_nr = a.tln_nr "
		." LEFT JOIN #__clm_mannschaften as g ON g.liga = a.lid AND g.sid = a.sid AND g.tln_nr = a.gegner "
		." WHERE a.lid = $lid AND a.runde = $rnd AND a.paar = $paar AND a.dg = $dg"
		//." AND ( m.zps =0 OR g.zps = 0) AND a.heim = 1"
		." AND a.heim = 1"
		;
	$db->setQuery($query);
	$data	= $db->loadObjectList();

	// Wenn "Spielfrei" kampflos gesetzt wurde
	if ((($data[0]->hzps =="0" AND ($gast == "heim" OR $gast=="home")) OR ( $data[0]->gzps =="0" AND ($gast == "gast" OR $gast=="away"))) AND $params['noOrgReference'] == '0') {
		$mainframe->enqueueMessage( JText::_('ERGEBNISSE_SPIELFREI'),'warning' );
		$mainframe->redirect( $link);
					}
	// Datum und Uhrzeit für Meldezeitpunkt
	$date		=JFactory::getDate();
	$now		= $date->toSQL();
	$user		=JFactory::getUser();
	$meldung	= $user->get('id');
	if ($gast=="heim" OR $gast=="home") { $comment = JText::_( 'ERGEBNISSE_COMMENT_HOME_KL' ); }
	else { $comment = JText::_( 'ERGEBNISSE_COMMENT_GUEST_KL' ); }

	$brett_punkte	= $bretter * ($sieg + $antritt);
	$man_punkte	= $man_sieg + $man_antritt;

	$query	= "UPDATE #__clm_rnd_man"
		." SET brettpunkte = '".$brett_punkte."'"
		." , manpunkte = '".$man_punkte."'"
		." , ergebnis = 5 "
		." , kampflos = 1 "
		." , zeit = '$now'"
		." , gemeldet = '$meldung'"
		." , comment = '$comment'"
		." , icomment = '$icomment'"
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paar
		." AND dg = ".$dg;
	if ($gast=="heim" OR $gast=="home") { $query = $query." AND heim = 1 ";}
		else { $query = $query." AND heim = 0 ";}

	$db->setQuery($query);
	clm_core::$db->query($query);

	$query	= "UPDATE #__clm_rnd_man"
		." SET brettpunkte = '0'"
		." , manpunkte = '0'"
		." , ergebnis = 4 "
		." , kampflos = 1 "
		." , zeit = '$now'"
		." , gemeldet = '$meldung'"
		." , comment = '$comment'"
		." , icomment = '$icomment'"
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paar
		." AND dg = ".$dg;
	if($gast=="heim" OR $gast=="home") { $query = $query." AND heim = 0 ";}
		else { $query = $query." AND heim = 1 ";}

	$db->setQuery($query);
	clm_core::$db->query($query);

	if (($liga_sl->runden_modus == 4) OR ($liga_sl->runden_modus == 5 and $rnd < $liga_sl->runden)) {    // KO Turnier
		if ($gast=="heim" OR $gast=="home") { $ko_heim = $rnd; $ko_gast = $rnd -1; }
		else { $ko_heim = $rnd -1; $ko_gast = $rnd; }
		// Für Heimmannschaft updaten
		$query	= "UPDATE #__clm_mannschaften"
			." SET rankingpos = ".$ko_heim
			." WHERE sid = ".$sid
			." AND liga = ".$lid
			." AND tln_nr = ".$data[0]->tln_nr
		;
		$db->setQuery($query);
		clm_core::$db->query($query);
		$query	= "UPDATE #__clm_mannschaften"
			." SET rankingpos = ".$ko_gast
			." WHERE sid = ".$sid
			." AND liga = ".$lid
			." AND tln_nr = ".$data[0]->gegner
		;
		$db->setQuery($query);
		clm_core::$db->query($query);	
	}

	// errechnet/aktualisiere Rangliste 
	clm_core::$api->db_tournament_ranking($data[0]->lid,true); 

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'ERGEBNISSE_AKTION_KL' );
	$clmLog->params = array('sid' => $sid, 'lid' => $lid, 'rnd' => $rnd, 'paar' => $paar, 'dg' => $dg);
	$clmLog->write();
	
	}

	/**
	* alt: errechnet/aktualisiert Rangliste/Punktesummen eines Turniers
	* neu: errechnte/aktualisiere Rangliste & inoff. DWZ falls eingestellt (autoDWZ, autoRANKING)
	*/
	function calculateRanking($sid,$liga) {
		clm_core::$api->direct("db_tournament_auto",array($liga,true,false));
	}


function update_remarks()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	defined('clm') or die('Restricted access');

	$option		= clm_core::$load->request_string('option');
	$section	= clm_core::$load->request_string('section');
	$db 		=JFactory::getDBO();
	$task		= clm_core::$load->request_string( 'task');
	$user 		=JFactory::getUser();
	$id_id 		= clm_core::$load->request_string( 'id');
	$date		=JFactory::getDate();

	$meldung 	= $user->get('id');
	$sid		= clm_core::$load->request_int( 'sid');
	$lid 		= clm_core::$load->request_int( 'lid');
	$rnd		= clm_core::$load->request_int( 'rnd');
	$paarung	= clm_core::$load->request_int( 'paarung');
	$dg			= clm_core::$load->request_int( 'dg');
	$comment = addslashes(clm_core::$load->request_string( 'comment'));
	$icomment = addslashes(clm_core::$load->request_string( 'icomment'));

	// Für Heimmannschaft updaten
	$query	= "UPDATE #__clm_rnd_man"
		." SET comment = '".$comment."'"
		." , icomment = '".$icomment."'"
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 1 "
		;
	clm_core::$db->query($query);

	// Für Gastmannschaft updaten
	$query	= "UPDATE #__clm_rnd_man"
		." SET comment = '".$comment."'"
		." , icomment = '".$icomment."'"
		." WHERE sid = ".$sid
		." AND lid = ".$lid
		." AND runde = ".$rnd
		." AND paar = ".$paarung
		." AND dg = ".$dg
		." AND heim = 0 "
		;
	clm_core::$db->query($query);

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'ERGEBNISSE_UPDATE_REMARKS' );
	$clmLog->params = array('sid' => $sid, 'lid' => $lid, 'rnd' => $rnd, 'paar' => $paarung, 'dg' => $dg);
	$clmLog->write();

	$msg = JText::_( 'ERGEBNISSE_UPDATE_REMARKS' );
	$link = 'index.php?option='.$option.'&section='.$section;
	$mainframe->enqueueMessage( $msg );
	$mainframe->redirect( $link );
	}


}
