<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerMannschaften extends JControllerLegacy
{
	/**
	 * Constructor
	 */
function __construct( $config = array() )
	{
		parent::__construct( $config );
		// Register Extra tasks
		$this->registerTask( 'add','edit' );
		$this->registerTask( 'apply','save' );
		$this->registerTask( 'unpublish','publish' );
	}

function display($cachable = false, $urlparams = array())
	{
	$mainframe	= JFactory::getApplication();
	$option 	= JRequest::getCmd( 'option' );

	$clmAccess = clm_core::$access;      

	$section = JRequest::getVar('section');
	$db=JFactory::getDBO();

	$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",'filter_order','a.id',	'cmd' );
	$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir','','word' );
	$filter_state		= $mainframe->getUserStateFromRequest( "$option.filter_state",'filter_state','','word' );
	$filter_sid		= $mainframe->getUserStateFromRequest( "$option.filter_sid",'filter_sid',0,'int' );
	$filter_lid		= $mainframe->getUserStateFromRequest( "$option.filter_lid",'filter_lid',0,'int' );
	$filter_vid		= $mainframe->getUserStateFromRequest( "$option.filter_vid",'filter_vid',0,'string' );
	$filter_catid		= $mainframe->getUserStateFromRequest( "$option.filter_catid",'filter_catid',0,'int' );
	$search			= $mainframe->getUserStateFromRequest( "$option.search",'search','','string' );
	$search			= JString::strtolower( $search );
	$limit			= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
	$limitstart		= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

	$where = array();
	$where[]=' c.archiv = 0';
	if ( $filter_catid ) {	$where[] = 'a.published = '.(int) $filter_catid; }
	if ( $filter_sid ) {	$where[] = 'a.sid = '.(int) $filter_sid.' AND c.archiv = 0'; }
	if ( $filter_lid ) {	$where[] = 'a.liga = '.(int) $filter_lid; }
	if ( $filter_vid ) {	$where[] = "a.zps = '$filter_vid'"; }
	if ($search) {	$where[] = 'LOWER(a.name) LIKE "'.$db->escape('%'.$search.'%').'"';	}

	if ( $filter_state ) {
		if ( $filter_state == 'P' ) {
			$where[] = 'a.published = 1';
		} else if ($filter_state == 'U' ) {
			$where[] = 'a.published = 0';
		}
	}
	$count_man	= ( count( $where ) ? ' WHERE ZPS =1 AND ' . implode( ' AND ', $where ) : '' );
	$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
	if ($filter_order == 'a.id'){
		$orderby 	= ' ORDER BY liga ASC, a.tln_nr '.$filter_order_Dir;
	} else {
	if ($filter_order =='a.name' OR $filter_order == 'a.man_nr' OR $filter_order == 'd.name' OR $filter_order == 'a.tln_nr' OR $filter_order == 'a.mf' OR $filter_order == 'a.liste' OR $filter_order == 'b.Vereinname' OR $filter_order == 'c.name' OR $filter_order == 'a.ordering' OR $filter_order == 'a.published' ) { 
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', a.id';
			}
		else { $filter_order = 'a.id'; $orderby="";}
	}
	// Zugangscheck
	if ($clmAccess->access('BE_team_edit') === false) {
		$section = 'info';
		JError::raiseWarning( 500, JText::_( 'TEAM_NO_ACCESS' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
	}
	if ($clmAccess->access('BE_team_edit') === true) $where_sl = '';
	else $where_sl = ' AND d.sl = '.clm_core::$access->getJid();
	// get the total number of records
	$query = ' SELECT COUNT(*) '
		.' FROM #__clm_mannschaften AS a'
		.' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
		.' LEFT JOIN #__clm_liga AS d ON a.liga = d.id'
	. $where.$where_sl
	;
	$db->setQuery( $query );
	$total = $db->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );

	// Mannschaften ohne Verein zählen
	$query = ' SELECT COUNT(a.id) as id'
		.' FROM #__clm_mannschaften AS a '
		.' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
		.' LEFT JOIN #__clm_liga AS d ON a.liga = d.id'
		. $count_man.$where_sl    //.' LIMIT '.$limitstart.','.$limit
		;
	$db->setQuery( $query);
	$counter_man = $db->loadResult();

	if($counter_man > 0){
	JError::raiseNotice( 6000,  JText::_( 'MANNSCHAFTEN_ES_GIBT').' '.$counter_man.' '.JText::_('MANNSCHAFTEN_ERROR_MANNSCHAFT_VEREIN')); } 

	// get the subset (based on limits) of required records
	$query = ' SELECT a.*, c.name AS saison, b.Vereinname as verein, u.name AS editor, d.name AS liga_name'
		.' FROM #__clm_mannschaften AS a'
		.' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
		.' LEFT JOIN #__clm_liga AS d ON a.liga = d.id'
		.' LEFT JOIN #__users AS u ON u.id = a.checked_out'
		.' LEFT JOIN #__clm_dwz_vereine AS b ON a.zps = b.ZPS AND b.sid = a.sid'
		.' LEFT JOIN #__clm_vereine AS e ON e.zps = a.zps AND e.sid = a.sid'
	. $where.$where_sl
	. $orderby
	;
	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $db->loadObjectList();
	if ($db->getErrorNum()) {
		echo $db->stderr();
		return false;
	}

	// Filter
	// Statsusfilter
	$lists['state']	= JHtml::_('grid.state',  $filter_state );
	// Saisonfilter
	$sql = 'SELECT id, name FROM #__clm_saison WHERE archiv =0';
	$db->setQuery($sql);
	$saisonlist[]	= JHtml::_('select.option',  '0', JText::_( 'MANNSCHAFTEN_SAISON' ), 'id', 'name' );
	$saisonlist         = array_merge( $saisonlist, $db->loadObjectList() );
	$lists['sid']      = JHtml::_('select.genericlist', $saisonlist, 'filter_sid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','id', 'name', intval( $filter_sid ) );
	// Ligafilter
	$sql = 'SELECT d.id AS cid, d.name FROM #__clm_liga as d'
		." LEFT JOIN #__clm_saison as s ON s.id = d.sid"
		." WHERE s.archiv = 0 ".$where_sl;
	$db->setQuery($sql);
	$ligalist[]	= JHtml::_('select.option',  '0', JText::_( 'MANNSCHAFTEN_LIGA' ), 'cid', 'name' );
	$ligalist	= array_merge( $ligalist, $db->loadObjectList() );
	$lists['lid']	= JHtml::_('select.genericlist', $ligalist, 'filter_lid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','cid', 'name', intval( $filter_lid ) );

	// Vereinefilter laden
	$vlist	= CLMFilterVerein::vereine_filter(0);
	$lists['vid']	= JHtml::_('select.genericlist', $vlist, 'filter_vid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','zps', 'name', $filter_vid );

	// Ordering
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;
	// Suchefilter
	$lists['search']= $search;
	require_once(JPATH_COMPONENT.DS.'views'.DS.'mannschaft.php');
	CLMViewMannschaften::mannschaften( $rows, $lists, $pageNav, $option );
}


function edit()
	{
	$mainframe	= JFactory::getApplication();

	$db 		=JFactory::getDBO();
	$user 		=JFactory::getUser();
	$task 		= JRequest::getVar( 'task');
	$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
	$option 	= JRequest::getCmd( 'option' );
	$section 	= JRequest::getVar( 'section' );
	JArrayHelper::toInteger($cid, array(0));

	$row =JTable::getInstance( 'mannschaften', 'TableCLM' );
	// load the row from the db table
	$row->load( $cid[0] );
	$sid = $row->sid;
	if ($task =="add"){
		$sql = 'SELECT id FROM #__clm_saison WHERE archiv = 0 and published = 1';
		$db->setQuery($sql);
		$sid = $db->loadResult();
	}
	else
	{
	// Prüfen ob User Berechtigung zum editieren hat
	$sql = " SELECT sl, params FROM #__clm_liga "
		." WHERE id =".$row->liga
		;
	$db->setQuery($sql);
	$lid = $db->loadObjectList();
	}

	$clmAccess = clm_core::$access;      

	if ($task == 'edit') {
		$saison		=JTable::getInstance( 'saisons', 'TableCLM' );
		$saison->load( $sid );
		// illegaler Einbruchversuch über URL !
		// evtl. mitschneiden !?!
		if ($saison->archiv == "1") { // AND clm_core::$access->getType() !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_ERROR_LIGA_ARCHIV' ));
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg,"message" );
				}
		if ($clmAccess->access('BE_team_edit') === false) {
			$section = 'info';
			JError::raiseWarning( 500, JText::_( 'TEAM_NO_ACCESS' ) );
			$link = 'index.php?option='.$option.'&section='.$section;
			$mainframe->redirect( $link);
		}

		if (isset($lid[0]) && $lid[0]->sl != clm_core::$access->getJid() AND $clmAccess->access('BE_team_edit') !== true ) {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_ERROR_MANNSCHAFT_STAFFEL' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}
		if ($clmAccess->access('BE_team_edit') === true) $where_sl = '';
		else $where_sl = ' AND a.sl = '.clm_core::$access->getJid();
		// do stuff for existing records
		$row->checkout( $user->get('id') );
	} else {
		if ($clmAccess->access('BE_team_create') === false) {
			$section = 'info';
			JError::raiseWarning( 500, JText::_( 'TEAM_NO_ACCESS' ) );
			$link = 'index.php?option='.$option.'&section='.$section;
			$mainframe->redirect( $link);
		}
		$where_sl = '';
	// do stuff for new records
		$row->published = 0;
	}
	// Ligaliste
	$sql = " SELECT a.id as liga, a.name FROM #__clm_liga as a"
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		." WHERE  s.archiv = 0 ".$where_sl;
		;
	$db->setQuery( $sql );
	$non_sl=$db->loadObjectList();
	// Falls kein SL einer Liga dann kann auch keine Mannschaft angelegt werden
	if (!isset($non_sl[0]->liga) AND $clmAccess->access('BE_team_create') === false) {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_ERROR_STAFFEL_MANNSCHAFT' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
	}

	$db->setQuery($sql);
	if (!$db->query()){$this->setRedirect( 'index.php?option='.$option.'&section='.$section );
		return JError::raiseWarning( 500, $db->getErrorMsg() );	}
	$ligalist[]	= JHtml::_('select.option',  '0', JText::_( 'MANNSCHAFTEN_LIGA') , 'liga', 'name' );
	$ligalist	= array_merge( $ligalist, $db->loadObjectList() );
	$lists['liga']	= JHtml::_('select.genericlist',   $ligalist, 'liga', 'class="inputbox" size="1"','liga', 'name', $row->liga );
	$lists['published']	= JHtml::_('select.booleanlist',  'published', 'class="inputbox"', $row->published );

	// Vereinefilter laden
	$vereinlist	= CLMFilterVerein::vereine_filter(0);
	$lists['verein']= JHtml::_('select.genericlist',   $vereinlist, 'zps', 'class="inputbox" size="1" ','zps', 'name', $row->zps );

	// Spielgemeinschaft
	//$lists['sg']= JHtml::_('select.genericlist',   $vereinlist, 'sg_zps', 'class="inputbox" size="1" ','zps', 'name', $row->sg_zps );
	// MFliste
	if ($task == 'edit') { $where = " AND ( a.zps = '".$row->zps."' OR FIND_IN_SET(a.zps,'".$row->sg_zps."')) AND a.published = 1";}
	else { $where = ' AND a.zps = 0 AND a.published = 1';}
	$tql = ' SELECT a.jid as mf, a.name as mfname'
		.' FROM #__clm_user AS a '
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		." WHERE s.archiv = 0 "
		.$where;
	$db->setQuery($tql);
	if (!$db->query()){$this->setRedirect( 'index.php?option='.$option.'&section='.$section );
		return JError::raiseWarning( 500, $db->getErrorMsg() );	}
	$mflist[]		= JHtml::_('select.option',  '0', JText::_( 'MANNSCHAFTEN_MANNSCHAFTFUEHRER' ), 'mf', 'mfname' );
	$mflist			= array_merge( $mflist, $db->loadObjectList() );
	$lists['mf']	= JHtml::_('select.genericlist',   $mflist, 'mf', 'class="inputbox" size="1"', 'mf', 'mfname', $row->mf );
	// Saisonliste
	if($task =="edit"){ $sql = 'SELECT id as sid, name FROM #__clm_saison WHERE id='.$sid;} 
	else { $sql = 'SELECT id as sid, name FROM #__clm_saison WHERE archiv =0'; }
	$db->setQuery($sql);
	if (!$db->query()){$this->setRedirect( 'index.php?option='.$option.'&section='.$section );
		return JError::raiseWarning( 500, $db->getErrorMsg() );}
	if ($task !="edit") {
	$saisonlist[]	= JHtml::_('select.option',  '0', JText::_( 'MANNSCHAFTEN_SAISON' ), 'sid', 'name' );
	$saisonlist	= array_merge( $saisonlist, $db->loadObjectList() );
		} else { $saisonlist	= $db->loadObjectList(); }
	$lists['saison']= JHtml::_('select.genericlist',   $saisonlist, 'sid', 'class="inputbox" size="1"','sid', 'name', $row->sid );

	//Liga-Parameter aufbereiten
	$lid_params = array();
	if (isset($lid[0]->params)) {
		$paramsStringArray = explode("\n", $lid[0]->params);
		foreach ($paramsStringArray as $value) {
			$ipos = strpos ($value, '=');
			if ($ipos !==false) {
				$lid_params[substr($value,0,$ipos)] = substr($value,$ipos+1);
			}
		}
	}
	if (isset($lid_params['pgntype'])) $lists['pgntype'] = $lid_params['pgntype'];   //pgn Parameterübernahme
	else $lists['pgntype']= 0;
	if (isset($lid_params['anz_sgp'])) $lists['anz_sgp'] = $lid_params['anz_sgp'];   //anz_sg Parameterübernahme
	else $lists['anz_sgp']= 1;
	// Spielgemeinschaft
	$sg_string = $row->sg_zps;
	$row->sg_zps = array();
	$row->sg_zps = explode(',',$sg_string);
	for ($i = 0; $i < $lists['anz_sgp']; $i++) { 
		if (!isset($row->sg_zps[$i]) OR $row->sg_zps[$i] === 0) $row->sg_zps[$i] = '0';
		$lists['sg'.$i]= JHtml::_('select.genericlist',   $vereinlist, 'sg_zps['.$i.']', 'class="inputbox" size="1" ','zps', 'name', $row->sg_zps[$i] );
	}
	require_once(JPATH_COMPONENT.DS.'views'.DS.'mannschaft.php');
	CLMViewMannschaften::mannschaft( $row, $lists, $option );
	}


function save()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');

	$db 		= JFactory::getDBO();
	$task 		= JRequest::getVar( 'task');
	$row 		= JTable::getInstance( 'mannschaften', 'TableCLM' );
	$pre_man	= JRequest::getInt( 'pre_man');

	if (!$row->bind(JRequest::get('post'))) {
		JError::raiseError(500, $row->getError() );
	}
	// Spielgemeinschaft
	$sg_array = array();
	$sg_array = $row->sg_zps;
	$row->sg_zps = '';
	$row->sg_zps = implode(',',$sg_array);
	// pre-save checks

	if (!$row->check() || empty($row->zps)) {
		JError::raiseError(500, "Die Eingaben sind unvollständig." );
	switch ($task)
	{
		case 'apply':
			if($row->id!=""){
			$link = 'index.php?option='.$option.'&section='.$section.'&task=edit&cid[]='. $row->id ;
			}else{
			$link = 'index.php?option='.$option.'&section='.$section.'&task=add';
			}
			break;
		case 'save':
		default:
			$link = 'index.php?option='.$option.'&section='.$section;
			break;
	}
	$mainframe->redirect( $link);
	return;
	}

	$liga_dat	= JTable::getInstance( 'ligen', 'TableCLM' );
	$liga_dat->load( $row->liga );

	// ich weiß nicht ob das so stimmt, aber bisher wurden die Variablen auch als leer gesetzt
	if($liga_dat->rang!="" && $row->man_nr!=""){
	// prüfen ob Mannschaftsnummer schon vergeben wurde
	$query = " SELECT COUNT(man_nr) as countman FROM #__clm_mannschaften as m "
		." LEFT JOIN #__clm_liga AS l ON m.liga = l.id"
		." WHERE m.zps = '".$row->zps."'"
		." AND m.man_nr = ".$row->man_nr
		." AND m.sid =".$row->sid
		." AND l.rang =".$liga_dat->rang
		;
	$db->setQuery($query);
	$count_mnr=$db->loadObjectList();

	$query = " SELECT m.id FROM #__clm_mannschaften as m "
		." LEFT JOIN #__clm_liga AS l ON m.liga = l.id"
		." WHERE m.zps = '".$row->zps."'"
		." AND m.man_nr = ".$row->man_nr
		." AND m.sid =".$row->sid
		." AND l.rang =".$liga_dat->rang
		." ORDER BY m.id ASC "
		." LIMIT 1 "
		;
	$db->setQuery($query);
	$count_id=$db->loadObjectList();
	}else{
	$count_id=0;$count_mnr=0;
	}



	if ($count_mnr[0]->countman > 0 AND ( !isset($row->id) OR $count_id[0]->id != $row->id)) {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_ERROR_MANNSCHAFT_IST') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link );
		}
	// Automatisches Ergänzen
	if ($row->name == JText::_( 'MANNSCHAFT' ).' '.$row->tln_nr AND $row->lokal == '' AND $task = 'apply')
	{ 	$query = " SELECT id, name, lokal, adresse FROM #__clm_vereine "
			." WHERE zps = '".$row->zps."'"
			." AND sid =".$row->sid
			." LIMIT 1 "
			;
		$db->setQuery($query);
		$club=$db->loadObjectList();
		if (isset($club[0])) {
			if ($row->name == 'Mannschaft '.$row->tln_nr) { $row->name = $club[0]->name; }
			if ($row->name == JText::_( 'MANNSCHAFT' ).' '.$row->tln_nr) { $row->name = $club[0]->name; }
			if ($row->lokal == '') { $row->lokal = $club[0]->lokal; }
			if ($task == 'save') { $task = 'apply'; }
		} else {
			JError::raiseNotice( 6000, JText::_( 'MANNSCHAFTEN_ERROR_MANNSCHAFT_CLUB') );
			//$link = 'index.php?option='.$option.'&section='.$section;
			$link = 'index.php?option='.$option.'&section='.$section.'&task=edit&cid[]='. $row->id ;
			$mainframe->redirect( $link );
		}
	}
	
	$aktion = JText::_( 'MANNSCHAFT_LOG_TEAM_EDIT');
	if (!$row->id) {
	$aktion = JText::_( 'MANNSCHAFT_LOG_TEAM_CREATE');
		$where = "sid = " . (int) $row->sid;
		$row->ordering = $row->getNextOrder( $where );
	}
	// save the changes
	if (!$row->store()) {
		JError::raiseError(500, $row->getError() );
	}

	// Wenn Meldelistenmodus dann bei Änderung der Mannschaftsnummer Meldeliste updaten
	if ($liga_dat->rang == 0 AND $pre_man != $row->man_nr) {
		$query = " UPDATE #__clm_meldeliste_spieler "
			." SET  mnr = ".$row->man_nr
			." WHERE sid = ".$row->sid
			." AND lid = ".$row->liga
			." AND mnr = ".$pre_man
			." AND zps = '".$row->zps."'"
			;
		$db->setQuery($query);
		$db->query();
	}

	// Ranking der Liga/MTurniers
	clm_core::$api->db_tournament_ranking($row->liga,true); 

	switch ($task)
	{
		case 'apply':
			$msg = JText::_( 'MANNSCHAFTEN_AENDERUNGEN' );
			$link = 'index.php?option='.$option.'&section='.$section.'&task=edit&cid[]='. $row->id ;
			break;
		case 'save':
		default:
			$msg = JText::_( 'MANNSCHAFTEN_MANNSCHAFT');
			$link = 'index.php?option='.$option.'&section='.$section;
			break;
	}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = $aktion;
	$clmLog->params = array('sid' => $row->sid, 'lid' => $row->liga, 'zps' => $row->zps);
	$clmLog->write();
	
	$mainframe->redirect( $link, $msg,"message" );
	}


function cancel()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$msg = JText::_( 'MANNSCHAFTEN_AKTION');
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg,"message" );
	}


function remove()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$clmAccess = clm_core::$access;      

	$db 		=JFactory::getDBO();
	$cid 		= JRequest::getVar('cid', array(), '', 'array');
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	JArrayHelper::toInteger($cid);

	if (count($cid) < 1) {
		JError::raiseWarning(500, JText::_( 'MANNSCHAFTEN_SELECT', true ) );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

	$row =JTable::getInstance( 'mannschaften', 'TableCLM' );
	// load the row from the db table 
	$row->load( $cid[0] );

	// Prüfen ob User Berechtigung zum editieren hat
	$sql = " SELECT sl FROM #__clm_liga "
		." WHERE id =".$row->liga
		." AND sid =".$row->sid
		;
	$db->setQuery($sql);
	$lid = $db->loadObjectList();

	// Zählen ob in den zugehörigen Ligen schon Ergebnisse gemeldet wurden
	$ligen = array();
	$vorher = 0;

	foreach($cid as $id) {
		$row->load( $id );
		if($vorher != $row->liga) {
			$ligen[]=$row->liga;
			$vorher=$row->liga;
		}}
	$counter = implode( ',', $ligen );

	if($counter!=""){
	$query = " SELECT COUNT(id) as count FROM #__clm_rnd_man "
		.' WHERE lid IN ( '. $counter .' )'
		." AND sid =".$row->sid
		.' AND gemeldet > 0';
	$db->setQuery($query);
	$liga_count = $db->loadObjectList();$count=$liga_count[0]->count;}else{$count=0;}

	if ( $count > 0 ) {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_NO_LOESCH' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}
	if ($clmAccess->access('BE_team_delete') === false) {
		$section = 'info';
		JError::raiseWarning( 500, JText::_( 'TEAM_NO_ACCESS' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
	}

	//if ( $lid[0]->sl != clm_core::$access->getJid() AND clm_core::$access->getType() !== 'admin' ) {
	if ( $lid[0]->sl != clm_core::$access->getJid() AND $clmAccess->access('BE_team_delete') !== true ) {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_MANNSCHAFT_LOESCH' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}
	else {
		if ( $clmAccess->access('BE_team_delete') === true) {
		//if ( clm_core::$access->getType() === 'admin') {
		$cids = implode( ',', $cid );
		foreach($cid as $cid) {
			$row->load( $cid );
			$query = " DELETE FROM #__clm_meldeliste_spieler "
				.' WHERE mnr ='.$row->man_nr
				.' AND lid ='.$row->liga
				." AND sid =".$row->sid
				;
			$db->setQuery($query);
			$db->query();
			}
		$query = " DELETE FROM #__clm_mannschaften "
		. ' WHERE id IN ( '. $cids .' )';

		$db->setQuery( $query );
		if (!$db->query()) {
		echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";}

		if (count($cid) == 1) { $msg = JText::_( 'MANNSCHAFT_MSG_DEL_ENTRY' ); }
		else { $msg = count($cid).JText::_( 'MANNSCHAFT_MSG_DEL_ENTRYS' ); }
			}
		else {
			$row->load( $cid[0] );
			$del++;
			$query = " DELETE FROM #__clm_meldeliste_spieler "
				.' WHERE mnr ='.$row->man_nr
				.' AND lid ='.$row->liga
				." AND sid =".$row->sid
				;
			$db->setQuery($query);
			$db->query();

		$query = " DELETE FROM #__clm_mannschaften WHERE id = ".$cid[0];
		$msg = JText::_( 'MANNSCHAFT_MSG_DEL_ENTRY' );
			}
		}
		$db->setQuery( $query );
		if (!$db->query()) {
		echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n"; }

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'MANNSCHAFT_LOG_TEAM_DELETE');
	$clmLog->params = array('cids' => $cids, 'zps' => $row->zps);
	$clmLog->write();
	
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg ,"message");
	}


function publish()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		=JFactory::getDBO();
	$user 		=JFactory::getUser();
	$cid		= JRequest::getVar('cid', array(), '', 'array');
	$task		= JRequest::getCmd( 'task' );
	$publish	= ($task == 'publish');
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	JArrayHelper::toInteger($cid);

	if (empty( $cid )) {
		JError::raiseWarning( 500, 'No items selected' );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
	// Prüfen ob User Berechtigung zum publizieren hat
	$row =JTable::getInstance( 'mannschaften', 'TableCLM' );
	$row->load( $cid[0] );

	$sql = " SELECT sl FROM #__clm_liga "
		." WHERE id =".$row->liga
		." AND sid =".$row->sid
		;
	$db->setQuery($sql);
	$lid = $db->loadObjectList();

	$clmAccess = clm_core::$access;      
	if ($clmAccess->access('BE_team_edit') === false) {
		$section = 'info';
		JError::raiseWarning( 500, JText::_( 'TEAM_NO_ACCESS' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
	}
	//if ( $lid[0]->sl != clm_core::$access->getJid() AND clm_core::$access->getType() !== 'admin' ) {
	if ( $lid[0]->sl != clm_core::$access->getJid() AND $clmAccess->access('BE_team_edit') !== true ) {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_MANNSCHAFT_PUB' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}
	else {
		//if ( clm_core::$access->getType() === 'admin' OR clm_core::$access->getType() === 'dv' ) {
		if ( $clmAccess->access('BE_team_edit') === true ) {
		$cids = implode( ',', $cid );
		$query = ' UPDATE #__clm_mannschaften'
			.' SET published = '.(int) $publish
			.' WHERE id IN ( '. $cids .' )'
			.' AND ZPS !="0" '
			.' AND ( checked_out = 0 OR ( checked_out = '.(int) $user->get('id') .' ) )';
			}
		else {
		$query = 'UPDATE #__clm_mannschaften'
			. ' SET published = '.(int) $publish
			. ' WHERE id = '.$cid[0]
			. ' AND ( checked_out = 0 OR ( checked_out = '.(int) $user->get('id') .' ) )';
			}
		}
		$db->setQuery( $query );
	if (!$db->query()) { JError::raiseError(500, $db->getErrorMsg() );
			}
	if (count( $cid ) == 1) {
		$row =JTable::getInstance( 'mannschaften', 'TableCLM' );
	}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'MANNSCHAFT_LOG_TEAM')." ".$task;
	$table		=JTable::getInstance( 'mannschaften', 'TableCLM');
	$table->load($cid[0]);
	$clmLog->params = array('sid' => $table->sid, 'lid' => $table->liga, 'zps' => $table->zps, 'cids' => $cids);
	$clmLog->write();
	
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
/**
* Moves the record up one position
*/
function orderdown(  ) {
	CLMControllerMannschaften::order( 1 );
}

/**
* Moves the record down one position
*/
function orderup(  ) {
	CLMControllerMannschaften::order( -1 );
}

/**
* Moves the order of a record
* @param integer The direction to reorder, +1 down, -1 up
*/
function order( $inc )
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db		=JFactory::getDBO();
	$cid		= JRequest::getVar('cid', array(0), '', 'array');
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	JArrayHelper::toInteger($cid, array(0));

	$limit 		= JRequest::getVar( 'limit', 0, '', 'int' );
	$limitstart 	= JRequest::getVar( 'limitstart', 0, '', 'int' );

	$row =JTable::getInstance( 'mannschaften', 'TableCLM' );
	$row->load( $cid[0]);
	$row->move( $inc, 'liga = '.(int) $row->liga.' AND published != 0' );

	$msg 	= JText::_( 'MANNSCHAFT_MSG_SORT');
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg ,"message");
	}

/**
* Saves user reordering entry
*/
function saveOrder(  )
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db			=JFactory::getDBO();
	$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	JArrayHelper::toInteger($cid);

	$total		= count( $cid );
	$order		= JRequest::getVar( 'order', array(0), 'post', 'array' );
	JArrayHelper::toInteger($order, array(0));

	$row =JTable::getInstance( 'mannschaften', 'TableCLM' );
	$groupings = array();

	// update ordering values
	for( $i=0; $i < $total; $i++ ) {
		$row->load( (int) $cid[$i] );
		// track categories
		$groupings[] = $row->liga;

		if ($row->ordering != $order[$i]) {
			$row->ordering = $order[$i];
			if (!$row->store()) {
				JError::raiseError(500, $db->getErrorMsg() );
			}
		}
	}
	// execute update Order for each parent group
	$groupings = array_unique( $groupings );
	foreach ($groupings as $group){
		$row->reorder('liga = '.(int) $group);
	}
	$app =JFactory::getApplication();
	$app->enqueueMessage( JText::_('CLM_NEW_ORDERING_SAVED') );
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

function copy()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$cid		= JRequest::getVar( 'cid', null, 'post', 'array' );
	$db		= JFactory::getDBO();
	$table		= JTable::getInstance('mannschaften', 'TableCLM');
	$user		= JFactory::getUser();
	$n		= count( $cid );
	$this->setRedirect( 'index.php?option='.$option.'&section='.$section );

	// Prüfen ob User Berechtigung zum publizieren hat
	$row = JTable::getInstance( 'mannschaften', 'TableCLM' );
	$row->load( $cid[0] );

	$sql = " SELECT sl FROM #__clm_liga "
		." WHERE id =".$row->liga
		." AND sid =".$row->sid
		;
	$db->setQuery($sql);
	$lid = $db->loadObjectList();

	$clmAccess = clm_core::$access;      
	if ($clmAccess->access('BE_team_create') === false) {
		$section = 'info';
		JError::raiseWarning( 500, JText::_( 'TEAM_NO_ACCESS' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
	}
	//if ( $lid[0]->sl != clm_core::$access->getJid() AND clm_core::$access->getType() !== 'admin' ) {
	if ( $lid[0]->sl != clm_core::$access->getJid() AND $clmAccess->access('BE_team_create') !== true ) {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_MANNSCHAFT_KOPIE' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
	}
	$query = ' SELECT man_nr FROM #__clm_mannschaften '
		.' WHERE sid ='.$row->sid
		.' ORDER BY man_nr DESC LIMIT 1'
		;
	$db->setQuery( $query );
	$high_mnr = $db->loadResult();

	$query = ' SELECT tln_nr FROM #__clm_mannschaften '
		.' WHERE sid ='.$row->sid
		.' ORDER BY tln_nr DESC LIMIT 1'
		;
	$db->setQuery( $query );
	$high_tlnr = $db->loadResult();

	$p=1;
	if ($n > 0)
	{
		foreach ($cid as $id)
		{
			if ($table->load( (int)$id ))
			{
			$table->id			= 0;
			$table->name			= 'Kopie von ' . $table->name;
			$table->published		= 0;
			$table->man_nr			= $high_mnr + $p;
			$table->tln_nr			= $high_tlnr + $p;
			$table->liste			= 0;
			$table->mf			= 0;
		$p++;
			if (!$table->store()) {	return JError::raiseWarning( $table->getError() );}
			}
		else {	return JError::raiseWarning( 500, $table->getError() );	}
		}
	}
	else {	return JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_ITEMS' ) );}

	if ($n >1) { $msg=JText::_( 'MANNSCHAFT_MSG_COPY_ENTRYS');}
		else {$msg=JText::_( 'MANNSCHAFT_MSG_COPY_ENTRY');}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'MANNSCHAFT_LOG_TEAM_COPY');
	$table =JTable::getInstance( 'mannschaften', 'TableCLM');
	$table->load($cid[0]);
	$clmLog->params = array('sid' => $table->sid, 'lid' => $table->liga, 'zps' => $table->zps, 'cids' => implode( ',', $cid ));
	$clmLog->write();
	
	$this->setMessage( JText::_( $n.$msg ) );
		}

public static function meldeliste()
	{
	JRequest::checkToken() or die( 'Invalid Token' );
	$mainframe	= JFactory::getApplication();

	$db 		=JFactory::getDBO();
	$user 		=JFactory::getUser();
	$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
	$option 	= JRequest::getCmd( 'option' );
	$section 	= JRequest::getVar( 'section' );
	JArrayHelper::toInteger($cid, array(0));
	// keine Meldeliste gewählt
	if ($cid[0] < 1) {
	$msg = JText::_( 'MANNSCHAFTEN_MELDELISTE');
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg ,"message");
		}
	// load the row from the db table
	$row =JTable::getInstance( 'mannschaften', 'TableCLM' );
		$row->load( $cid[0] );

	// Konfigurationsparameter auslesen 
	$config = clm_core::$db->config();
	$rang	= $config->rangliste;

	// load the row from the db table
	$rowliga	= JTable::getInstance( 'ligen', 'TableCLM' );
	$liga		= $row->liga;
		$rowliga->load( $liga );

	$link = 'index.php?option='.$option.'&section='.$section;

	// Prüfen ob User Berechtigung  hat
	$clmAccess = clm_core::$access;      
	if ($clmAccess->access('BE_team_registration_list') === false) {
		$section = 'info';
		JError::raiseWarning( 500, JText::_( 'TEAM_NO_ACCESS' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
	}

	if ( $rang == 0 AND $rowliga->sl != clm_core::$access->getJid() AND $clmAccess->access('BE_team_registration_list') !== true) {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_MELDELISTE_BEARBEITEN' ) );
		$mainframe->redirect( $link);
					}

/*
	if ( $row->liste >0 AND $rowliga->rang > 0) {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_KONFIG_PROBLEM' ) );
		JError::raiseNotice( 6000,  JText::_( 'MANNSCHAFTEN_MASSNAHMEN' ));
		$msg = JText::_( 'MANNSCHAFTEN_RANGLISTEN_BEARBEITEN' );
		
		$mainframe->redirect( $link, $msg);
		}
*/
	if ( $rowliga->rang > 0) {
		JError::raiseWarning( 500, JText::_('MANNSCHAFTEN_NO_MELDELISTE' ));
 		JError::raiseNotice( 6000,  JText::_('MANNSCHAFTEN_MANNSCHAFT_RANG' ) );
		$msg = JText::_( 'MANNSCHAFTEN_RANG_VEREIN' );
		$mainframe->redirect( $link, $msg,"message");
		}

/**
	if ( $rowliga->rang == 1 AND $rang == 2) {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_KONFIG_RANG' ) );
		JError::raiseNotice( 6000,  JText::_( 'MANNSCHAFTEN_MASSNAHMEN' ));
		$msg = JText::_( 'MANNSCHAFTEN_RANGLISTEN_BEARBEITEN' );
		
		$mainframe->redirect( $link, $msg);
		}
	if ( $rowliga->rang == 0 AND $rang == 1) {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_KONFIG_RANG_AK' ) );
		JError::raiseNotice( 6000,  JText::_( 'MANNSCHAFTEN_MASSNAHMEN' ));
		$msg = JText::_( 'MANNSCHAFTEN_RANG_VEREIN' );
		
		$mainframe->redirect( $link);
		}
	if ( $rowliga->rang == 1) {
	$zps = $row->zps;
	$mainframe->redirect( 'index.php?option='.$option.'&section=rangliste&task=edit&cid[]='.$zps);
		}
**/
	$row->checkout( $user->get('id') );
	// Link MUSS hardcodiert sein !!!
	$mainframe->redirect( 'index.php?option='.$option.'&section=meldelisten&task=edit&cid[]='.$cid[0]);
	}

public static function delete_meldeliste()
	{
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$mainframe	= JFactory::getApplication();

	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$db 		= JFactory::getDBO();
	$task 		= JRequest::getVar( 'task');
	$cid		= JRequest::getVar( 'cid');

	if (count($cid) < 1) {
	JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_LISTE_LOSCH') );
	$link = 'index.php?option='.$option.'&section='.$section;
	$mainframe->redirect( $link, $msg );
	}
	// Prüfen ob User Berechtigung zum löschen hat
	$clmAccess = clm_core::$access;      
	if ($clmAccess->access('BE_team_registration_list') === false) {
		$section = 'info';
		JError::raiseWarning( 500, JText::_( 'TEAM_NO_ACCESS' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
	}
	//if ( clm_core::$access->getType() !== 'admin' ) {
	if ( $rang == 0 AND $rowliga->sl != clm_core::$access->getJid() AND $clmAccess->access('BE_team_registration_list') !== true) {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_NO_MELDE_LOESCH' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
		}
	
	// load the row from the db table
	$row 		= JTable::getInstance( 'mannschaften', 'TableCLM' );
	$row->load( $cid[0]);
	$rowliga	= JTable::getInstance( 'ligen', 'TableCLM' );
	$liga		= $row->liga;
	$rowliga->load( $liga );

	$link = 'index.php?option='.$option.'&section='.$section;

	// Wenn Rangliste dann nicht löschen
	if ( $rowliga->rang > 0) {
		JError::raiseWarning( 500, JText::_('MANNSCHAFTEN_NO_LOESCH' ));
 		JError::raiseNotice( 6000,  JText::_('MANNSCHAFTEN_MANNSCHAFT_RANG' ) );
		$msg = JText::_( 'MANNSCHAFTEN_RANG_VEREIN' );
		$mainframe->redirect( $link, $msg,"message");
		}

	// Prüfen ob User Berechtigung zum publizieren hat
	if ($clmAccess->access('BE_team_registration_list') === false) {
		$section = 'info';
		JError::raiseWarning( 500, JText::_( 'TEAM_NO_ACCESS' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
	}
	if ( $rowliga->sl != clm_core::$access->getJid() AND $clmAccess->access('BE_team_registration_list') !== true) {
	//if ( $rowliga->sl != clm_core::$access->getJid() AND clm_core::$access->getType() !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_MELDE_LOESCH' ) );
		$mainframe->redirect( $link);
					}
		$zps	=$row->zps;
		$sg_zps	=$row->sg_zps;
		$man_nr	=$row->man_nr;
		$sid	=$row->sid;
		$lid	=$row->liga;

	$query	= "DELETE FROM #__clm_meldeliste_spieler"
		//." WHERE ( zps = '$zps' OR zps='$sg_zps')"
		." WHERE ( zps = '$zps' OR FIND_IN_SET(zps,'".$sg_zps."') != 0 )"
		." AND  mnr = ".$man_nr
		." AND sid = ".$sid 
		." AND lid = ".$lid
		//." AND status = 0 " 
		;
	$db->setQuery($query);
	$db->query();

	$date 		=JFactory::getDate();
	$now 		= $date->toSQL();

	$query	= "UPDATE #__clm_mannschaften"
		." SET edit_liste = ".clm_core::$access->getJid()
		." , edit_datum = '$now'"
		." , liste = 0"
		." WHERE sid = ".$sid
		." AND man_nr = ".$man_nr
		." AND zps = '$zps'"
			;
	$db->setQuery($query);
	$db->query();

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'MANNSCHAFT_LOG_LIST_DELETE');
	$clmLog->params = array('sid' => $sid, 'lid' => $lid, 'zps' => $zps, 'man' => $man_nr, 'cids' => $cid[0]);
	$clmLog->write();
	
	$msg = JText::_( 'MANNSCHAFTEN_MELDE_GELOESCHT');
	$link = 'index.php?option='.$option.'&section='.$section;
	$mainframe->redirect( $link, $msg ,"message");
	}

public static function save_meldeliste()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$user		= JFactory::getUser();
	$meldung	= $user->get('id');

	$db 		= JFactory::getDBO();
	$task 		= JRequest::getVar( 'task');
	$row 		= JTable::getInstance( 'mannschaften', 'TableCLM' );
	$cid		= JRequest::getVar( 'id');
	$row->load( $cid);

	$stamm 		= JRequest::getVar( 'stamm');
	$ersatz		= JRequest::getVar( 'ersatz');
	$zps 		= JRequest::getVar( 'zps');
	$mnr 		= JRequest::getVar( 'mnr');
	$sid 		= JRequest::getVar( 'sid');
	$max 		= JRequest::getVar( 'max');
	$editor 	= JRequest::getVar( 'editor');
	$liga 		= $row->liga;
	$sg_zps		= $row->sg_zps;

	// Datum und Uhrzeit für Meldung
	$date =JFactory::getDate();
	$now = $date->toSQL();
	
	// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	$countryversion=$config->countryversion;
	
	// Liste wurde bereits abgegeben
	if ($row->liste > 0) {
	$aktion = JText::_( 'MANNSCHAFT_LOG_LIST_EDIT');
		$query	= "UPDATE #__clm_mannschaften"
			." SET edit_liste = ".$meldung
			." , edit_datum = '$now'"
			." WHERE sid = ".$sid
			." AND man_nr = ".$mnr
			." AND zps = '$zps'"
			;
		}
	// Liste wurde noch nicht abgegeben
	else {
	$aktion = JText::_( 'MANNSCHAFT_LOG_LIST_CREATE');
		$query	= "UPDATE #__clm_mannschaften"
			." SET liste = ".$meldung
			." , datum = '$now'"
			." WHERE sid = ".$sid
			." AND man_nr = ".$mnr
			." AND zps = '$zps'"
			;
		}
	$db->setQuery($query);
	$db->query();

	$query	= "DELETE FROM #__clm_meldeliste_spieler"
		. " WHERE lid = $liga"
		. " AND mnr = ".$mnr
		. " AND sid = ".$sid
		//."  AND ( zps = '$zps' OR zps='$sg_zps')"
		. " AND ( zps ='".$zps."' OR FIND_IN_SET(zps,'".$sg_zps."') != 0 )"
		;

	$db->setQuery($query);
	$db->query();

	for ($y=1; $y< 1+($stamm+$ersatz); $y++){
	$spl	= JRequest::getVar( 'spieler'.$y);
	$block	= JRequest::getInt( 'check'.$y);

	$teil	= explode("-", $spl);
		if ($countryversion == "de") {
			$mgl_nr	= $teil[0];
			$PKZ    = '';
		} else {
			$mgl_nr	= 0;
			$PKZ    = $teil[0];
		}
		$tzps	= $teil[1];

		if ($spl >0) {
			$query	= "REPLACE INTO #__clm_meldeliste_spieler"
				." ( `sid`, `lid`, `mnr`, `snr`, `mgl_nr`, `PKZ`, `zps`, `ordering`, `gesperrt`) "
				. " VALUES ('$sid','$liga','$mnr','$y','$mgl_nr','$PKZ','$tzps','','$block')";
	$db->setQuery($query);
	$db->query();
	}
	}

	$msg = $editor;
	switch ($task)
	{
		case 'apply':
		$msg = JText::_( 'MANNSCHAFTEN_AENDERUNGN').$tzps;
	// Link MUSS hardcodiert sein !!!
		$link = 'index.php?option='.$option.'&section=meldelisten&task=edit&cid[]='. $cid ;
		break;

		case 'save':
		default:
			$msg = JText::_( 'MANNSCHAFTEN_MANNSCHAFT_GESPEICHERT' );
			$link = 'index.php?option='.$option.'&section='.$section;
			break;
	}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = $aktion;
	$clmLog->params = array('sid' => $sid, 'lid' => $liga, 'zps' => $zps, 'cids' => $cid);
	$clmLog->write();
	
	$mainframe->redirect( $link, $msg ,"message");
	}

public static function apply_meldeliste()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$user		= JFactory::getUser();
	$meldung	= $user->get('id');

	$db 		= JFactory::getDBO();
	$task 		= JRequest::getVar( 'task');
	$row 		= JTable::getInstance( 'mannschaften', 'TableCLM' );
	$cid		= JRequest::getVar( 'id');
	$row->load( $cid);

	$stamm 		= JRequest::getVar( 'stamm');
	$ersatz		= JRequest::getVar( 'ersatz');
	$zps 		= JRequest::getVar( 'zps');
	$mnr 		= JRequest::getVar( 'mnr');
	$sid 		= JRequest::getVar( 'sid');
	$max 		= JRequest::getVar( 'max');
	$editor 	= JRequest::getVar( 'editor');
	$liga 		= $row->liga;
	$sg_zps		= $row->sg_zps;

	// Datum und Uhrzeit für Meldung
	$date =JFactory::getDate();
	$now = $date->toSQL();
	
	// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	$countryversion=$config->countryversion;

	// Liste wurde bereits abgegeben
	if ($row->liste > 0) {
	$aktion = JText::_( 'MANNSCHAFT_LOG_LIST_EDIT');
		$query	= "UPDATE #__clm_mannschaften"
			." SET edit_liste = ".$meldung
			." , edit_datum = '$now'"
			." WHERE sid = ".$sid
			." AND man_nr = ".$mnr
			." AND zps = '$zps'"
			;
		}
	// Liste wurde noch nicht abgegeben
	else {
	$aktion = JText::_( 'MANNSCHAFT_LOG_LIST_CREATE');
		$query	= "UPDATE #__clm_mannschaften"
			." SET liste = ".$meldung
			." , datum = '$now'"
			." WHERE sid = ".$sid
			." AND man_nr = ".$mnr
			." AND zps = '$zps'"
			;
		}
	$db->setQuery($query);
	$db->query();

	$query	= "DELETE FROM #__clm_meldeliste_spieler"
		. " WHERE lid = $liga"
		. " AND mnr = ".$mnr
		. " AND sid = ".$sid
		//."  AND ( zps = '$zps' OR zps='$sg_zps')"
		. " AND ( zps ='".$zps."' OR FIND_IN_SET(zps,'".$sg_zps."') != 0 )"
		;
		
	$db->setQuery($query);
	$db->query();

	for ($y=1; $y< 1+($stamm+$ersatz); $y++){
	$spl	= JRequest::getVar( 'spieler'.$y);
	$block	= JRequest::getInt( 'check'.$y);

	$teil	= explode("-", $spl);
		if ($countryversion == "de") {
			$mgl_nr	= $teil[0];
			$PKZ    = '';
		} else {
			$mgl_nr	= 0;
			$PKZ    = $teil[0];
		}
		$tzps	= $teil[1];

	if($spl >0){
	$query	= "REPLACE INTO #__clm_meldeliste_spieler"
				." ( `sid`, `lid`, `mnr`, `snr`, `mgl_nr`, `PKZ`, `zps`, `ordering`, `gesperrt`) "
				. " VALUES ('$sid','$liga','$mnr','$y','$mgl_nr','$PKZ','$tzps','','$block')";
	$db->setQuery($query);
	$db->query();
	}
	}

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = $aktion;
	$clmLog->params = array('sid' => $sid, 'lid' => $liga, 'zps' => $zps, 'cids' => $cid);
	$clmLog->write();
	
	$msg = JText::_( 'MANNSCHAFTEN_AENDERUNGN' ).$tzps;
	// Link MUSS hardcodiert sein !!!
	$link = 'index.php?option=com_clm&section=meldelisten&task=edit&cid[]='. $cid ;
	$mainframe->redirect( $link, $msg ,"message");
	}

public static function spielfrei()
	{
	JRequest::checkToken() or die( 'Invalid Token' );
	$mainframe	= JFactory::getApplication();

	$db 		=JFactory::getDBO();
	$user 		=JFactory::getUser();
	$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
	$option 	= JRequest::getCmd( 'option' );
	$section 	= JRequest::getVar( 'section' );
	JArrayHelper::toInteger($cid, array(0));
	// keine Meldeliste gewählt //
	if ($cid[0] < 1) {
	$msg = JText::_( 'MANNSCHAFTEN_MANNSCHAFT_AUS');
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg ,"message");
		}
	// load the row from the db table
	$row =JTable::getInstance( 'mannschaften', 'TableCLM' );
		$row->load( $cid[0] );
	$tlnr = $row->tln_nr;


	// load the row from the db table
	$rowliga	= JTable::getInstance( 'ligen', 'TableCLM' );
	$liga		= $row->liga;
		$rowliga->load( $liga );

	$link = 'index.php?option='.$option.'&section='.$section;

	// Prüfen ob User Berechtigung zum publizieren hat
	$clmAccess = clm_core::$access;      
	if ($clmAccess->access('BE_team_edit') === false) {
		$section = 'info';
		JError::raiseWarning( 500, JText::_( 'TEAM_NO_ACCESS' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
	}
	if ( $rowliga->sl != clm_core::$access->getJid() AND $clmAccess->access('BE_team_edit') !== true) {
	//if ( $rowliga->sl != clm_core::$access->getJid() AND clm_core::$access->getType() !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'MANNSCHAFTEN_MANNSCHAFT_SPIELFREI' ) );
		$mainframe->redirect( $link);
					}

	$query	= "DELETE FROM #__clm_rnd_spl"
		." WHERE sid = ".$row->sid
		." AND lid = ".$row->liga
		." AND tln_nr = $tlnr "
		;
	$db->setQuery($query);
	$db->query();
	$query	= "UPDATE #__clm_rnd_man"
		." SET brettpunkte = NULL, manpunkte = NULL, bp_sum = NULL, mp_sum = NULL, gemeldet = 1, wertpunkte = NULL "
		." WHERE sid = ".$row->sid
		." AND lid = ".$row->liga
		." AND ( tln_nr = $tlnr OR gegner = $tlnr) "
		;
	$db->setQuery($query);
	$db->query();

	$query	= "UPDATE #__clm_mannschaften"
		." SET name = 'spielfrei', zps = '0', man_nr = 0, liste = 0, edit_liste = 0, mf = 0, sg_zps = '0', published = 0 "
		." WHERE sid = ".$row->sid
		." AND liga = ".$row->liga
		." AND tln_nr = $tlnr "
		;
	$db->setQuery($query);
	$db->query();

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'MANNSCHAFT_LOG_NO_GAMES');
	$clmLog->params = array('sid' => $row->sid, 'lid' => $row->liga, 'man' => $tlnr, 'cids' => $cid[0]);
	$clmLog->write();
	
	$msg = JText::_( 'MANNSCHAFTEN_MANNSCHAFT_SPIELF' );
	$mainframe->redirect( 'index.php?option='.$option.'&section='.$section, $msg,"message");
	}
}
