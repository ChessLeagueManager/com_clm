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

class CLMControllerRunden extends JControllerLegacy
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
	$section = JRequest::getVar('section');
	$db=JFactory::getDBO();
	$liga	= JRequest::getVar( 'liga' );
 
	// Parameter auslesen
	$config = clm_core::$db->config();
	$val=$config->menue;

	if ($val == 1) {
		$liga	= JRequest::getVar( 'liga' );
			}
	// für kaskadierende Menüführung
	if ($val == 1 AND $liga > 0) { $mainframe->setUserState( "$option.filter_lid", "$liga" ); }

	$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",'filter_order','a.id',	'cmd' );
	$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir','','word' );
	$filter_state		= $mainframe->getUserStateFromRequest( "$option.filter_state",'filter_state','','word' );
	$filter_sid		= $mainframe->getUserStateFromRequest( "$option.filter_sid",'filter_sid',0,'int' );
	$filter_lid		= $mainframe->getUserStateFromRequest( "$option.filter_lid",'filter_lid',0,'int' );
	$filter_catid		= $mainframe->getUserStateFromRequest( "$option.filter_catid",'filter_catid',0,'int' );
	$search			= $mainframe->getUserStateFromRequest( "$option.search",'search','','string' );
	$search			= JString::strtolower( $search );
	$limit			= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
	$limitstart		= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

	$where = array();
	//$where[]=' c.archiv = 0';
	if ( $filter_catid ) {	$where[] = 'a.published = '.(int) $filter_catid; }
	if ( $filter_sid ) {	$where[] = 'a.sid = '.(int) $filter_sid; }
	if ( $filter_lid ) {	$where[] = 'a.liga = '.(int) $filter_lid; }
	if ($search) {	$where[] = 'LOWER(a.name) LIKE "'.$db->escape('%'.$search.'%').'"';}

	if ( $filter_state ) {
		if ( $filter_state == 'P' ) {
			$where[] = 'a.published = 1';
		} else if ($filter_state == 'U' ) {
			$where[] = 'a.published = 0';
		}
	}
	$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
	if ($filter_order == 'a.id'){
		$orderby 	= ' ORDER BY id';
	} else {
	if ($filter_order =='a.name' OR $filter_order == 'a.nr' OR $filter_order == 'a.datum' OR $filter_order == 'd.name' OR $filter_order == 'c.name' OR $filter_order == 'a.meldung' OR $filter_order == 'a.sl_ok' OR $filter_order == 'a.published' OR $filter_order == 'a.ordering') { 
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', a.id';
			}
		else { $filter_order = 'a.id'; }
		}
	// get the total number of records
	$query = ' SELECT COUNT(*) '
		.' FROM #__clm_runden_termine AS a'
		.' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
		. $where
		;
	$db->setQuery( $query );
	$total = $db->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );

	// get the subset (based on limits) of required records
	$query = "SELECT a.*, c.name AS saison, c.published as sid_pub, u.name AS editor, d.name AS liga_name, "
		." d.durchgang, d.runden ,d.rnd as erstellt, d.sl, d.liga_mt "
	. ' FROM #__clm_runden_termine AS a'
	. ' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
	. ' LEFT JOIN #__clm_liga AS d ON a.liga = d.id'
	. ' LEFT JOIN #__users AS u ON u.id = a.checked_out'
	. $where.$orderby;
	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );

	$rows = $db->loadObjectList();
	if ($db->getErrorNum()) {
		echo $db->stderr();
		return false;
	}

	// Filter
	// Statusfilter
	$lists['state']	= JHtml::_('grid.state',  $filter_state );
	// nach Saison sortieren
	$sql = 'SELECT id, name FROM #__clm_saison WHERE archiv =0';
	$db->setQuery($sql);
	$saisonlist[]	= JHtml::_('select.option',  '0', JText::_( 'RUNDE_SAISON_WAE' ), 'id', 'name' );
	$saisonlist         = array_merge( $saisonlist, $db->loadObjectList() );
	$lists['sid']      = JHtml::_('select.genericlist', $saisonlist, 'filter_sid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','id', 'name', intval( $filter_sid ) );
	// Nur ausführen wenn Saison published = 1 !!

	//Zugangscheck
	$clmAccess = clm_core::$access;      
	if (isset($rows[0]) && $rows[0]->liga_mt == "0") {
		$mppoint = 'league';
		$csection = 'ligen';
	} else {
		$mppoint = 'teamtournament';
		$csection = 'mturniere';
	}

	//echo "<br>runden: "; var_dump($rows);  die('  section');
	if($clmAccess->access('BE_'.$mppoint.'_edit_round') === false) {
		JError::raiseWarning( 500, JText::_( 'LIGEN_STAFFEL_TOTAL' ) );
		$section = $csection;
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg , "message");
	} elseif ($clmAccess->access('BE_'.$mppoint.'_edit_round') === true) $where_sl = '';
	else $where_sl = ' AND a.sl = '.clm_core::$access->getJid();
	
	// Ligafilter
	$sql = 'SELECT a.id AS cid, a.name FROM #__clm_liga as a'
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid"
		." WHERE s.archiv = 0 ".$where_sl;
	$db->setQuery($sql);

	$ligalist[]	= JHtml::_('select.option',  '0', JText::_( 'RUNDE_LIGA_WAE' ), 'cid', 'name' );
	$ligalist	= array_merge( $ligalist, $db->loadObjectList() );
	$lists['lid']	= JHtml::_('select.genericlist', $ligalist, 'filter_lid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','cid', 'name', intval( $filter_lid ) );
	// Ordering
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;
	// Suchefilter
	$lists['search']= $search;
	if(isset($rows[0]) && $rows[0]->sl !== clm_core::$access->getJid() AND $clmAccess->access('BE_'.$mppoint.'_edit_round') !== true) {
		JError::raiseWarning( 500, JText::_( 'LIGEN_STAFFEL1' ) );
		$section = 'ligen';
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg , "message");
	}
 
	require_once(JPATH_COMPONENT.DS.'views'.DS.'runden.php');
	CLMViewRunden::Runden( $rows, $lists, $pageNav, $option );
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
	$row 		=JTable::getInstance( 'runden', 'TableCLM' );
	JArrayHelper::toInteger($cid, array(0));

	// load the row from the db table
	$row->load( $cid[0] );

	$liga =JTable::getInstance( 'ligen', 'TableCLM' );
	$liga->load($row->liga);
	if ($liga->liga_mt == "0") {
		$mppoint = 'league';
		$csection = 'ligen';
	} else {
		$mppoint = 'teamtournament';
		$csection = 'mturniere';
	}

	$clmAccess = clm_core::$access;      

	if($clmAccess->access('BE_'.$mppoint.'_edit_round') === false) {
		$section = $csection;
		$msg = JText::_( 'Kein Zugriff: ').JText::_( 'RUNDE_STAFFEL_TOTAL1' );    
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg, "message" );
		}
 
	if ($task == 'edit') {
	// illegaler Einbruchversuch über URL !
	// evtl. mitschneiden !?!
	$saison		=JTable::getInstance( 'saisons', 'TableCLM' );
	$saison->load( $row->sid );
		if ($saison->archiv == "1" AND $clmAccess->access('BE_'.$mppoint.'_create') !== true) {
			JError::raiseWarning( 100, JText::_( 'RUNDE_ARCHIV' ));
			$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg, "message" );
			}
		// Prüfen ob User Berechtigung zum editieren hat
		if ( $liga->sl !== clm_core::$access->getJid() AND $clmAccess->access('BE_'.$mppoint.'_edit_round') !== true) {
		JError::raiseWarning( 500, JText::_( 'RUNDE_STAFFEL' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link );
					}
	// do stuff for existing records
		$row->checkout( $user->get('id') );
	} else {
	// do stuff for new records
		// Prüfen ob User Berechtigung zum Bearbeiten hat
		if ( $liga->sl !== clm_core::$access->getJid() AND $clmAccess->access('BE_'.$mppoint.'_edit_round') !== true) {
			JError::raiseWarning( 500, JText::_( 'RUNDE_STAFFEL' ) );
			$section = $csection;
			$link = 'index.php?option='.$option.'&section='.$section;
			$mainframe->redirect( $link );
					}
		$row->published 	= 0;
	}

	// Ligaliste
	$sql = " SELECT a.id as liga, a.name FROM #__clm_liga as a"
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		." WHERE  s.archiv = 0 AND a.sl = ".clm_core::$access->getJid()
		;
	// wenn User Admin
	//if ( clm_core::$access->getType() === 'admin') {
	if ( $clmAccess->access('BE_'.$mppoint.'_edit_result') === true) {
	$sql = "SELECT a.id as liga, a.name FROM #__clm_liga as a"
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		." WHERE  s.archiv = 0 ";
					}
	$db->setQuery($sql);
	if (!$db->query()){$this->setRedirect( 'index.php?option='.$option.'&section='.$section );
		return JError::raiseWarning( 500, $db->getErrorMsg() );	}
	$ligalist[]	= JHtml::_('select.option',  '0', JText::_( 'RUNDE_LIGA_WAE') , 'liga', 'name' );
	$ligalist	= array_merge( $ligalist, $db->loadObjectList() );
	$lists['liga']	= JHtml::_('select.genericlist',   $ligalist, 'liga', 'class="inputbox" size="1"','liga', 'name', $row->liga );
	$lists['published']	= JHtml::_('select.booleanlist',  'published', 'class="inputbox"', $row->published );
	// Saisonliste
	$sql = 'SELECT id as sid, name FROM #__clm_saison WHERE archiv = 0';
	$db->setQuery($sql);
	if (!$db->query()){$this->setRedirect( 'index.php?option='.$option.'&section='.$section );
		return JError::raiseWarning( 500, $db->getErrorMsg() );	}
	$saisonlist[]	= JHtml::_('select.option',  '0', JText::_( 'RUNDE_SAISON_WAE' ), 'sid', 'name' );
	$saisonlist	= array_merge( $saisonlist, $db->loadObjectList() );
	$lists['saison']= JHtml::_('select.genericlist',   $saisonlist, 'sid', 'class="inputbox" size="1"','sid', 'name', $row->sid );
	// Liste Meldung
	$lists['complete']= JHtml::_('select.booleanlist',  'meldung', 'class="inputbox"', $row->meldung );
	// Liste SL OK
	$lists['slok']= JHtml::_('select.booleanlist',  'sl_ok', 'class="inputbox"', $row->sl_ok );

	require_once(JPATH_COMPONENT.DS.'views'.DS.'runden.php');
	CLMViewRunden::runde( $row, $lists, $option );
	}


function save()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$cliga 		= intval(JRequest::getVar( 'liga' ));

	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$slok_old	= JRequest::getVar('slok_old');
	
	$db 		=JFactory::getDBO();
	$task 		= JRequest::getVar( 'task');
	$row 		=JTable::getInstance( 'runden', 'TableCLM' );
	$msg=JRequest::getVar( 'id');

	if (!$row->bind(JRequest::get('post'))) {
		JError::raiseError(500, $row->getError() );
	}
	// pre-save checks
	if (!$row->check()) {
		JError::raiseError(500, $row->getError() );
	}
	// if new item, order last in appropriate group
	$aktion = JText::_( 'RUNDE_LOG_EDIT');
	if (!$row->id) {
	$aktion = JText::_( 'RUNDE_LOG_ADDED');
		$where = "sid = " . (int) $row->sid;
		$row->ordering = $row->getNextOrder( $where );
	}
	// save the changes
	if (!$row->store()) {
		JError::raiseError(500, $row->getError() );
	}
	
 
	if ($row->startzeit != '00:00') {
		$startzeit = $row->startzeit.':00';
		$query = " UPDATE #__clm_runden_termine "
			." SET startzeit = '".$startzeit."' "
			." WHERE liga = ".$row->liga
			." AND sid = ".$row->sid
			." AND startzeit = '00:00:00' "
		;
		$db->setQuery($query);
		$db->query();
	}

	if ($row->datum != '0000-00-00' AND $row->deadlineday != '0000-00-00' AND $row->deadlineday >= $row->datum) {
		$ts1 = strtotime($row->deadlineday);
		$ts2 = strtotime($row->datum);
		$seconds_diff = $ts1 - $ts2;
		$day_diff = (string) $seconds_diff/3600/24;
		$deadlinetime = $row->deadlinetime.':00';
		$query = " UPDATE #__clm_runden_termine "
			." SET deadlineday = ADDDATE(datum,'".$day_diff."') "
			." WHERE liga = ".$row->liga
			." AND sid = ".$row->sid
			." AND deadlineday = '0000-00-00' "
		;
		$db->setQuery($query);
		$db->query();
	}

	if ($row->deadlinetime != '24:00') {
		$deadlinetime = $row->deadlinetime.':00';
		$query = " UPDATE #__clm_runden_termine "
			." SET deadlinetime = '".$deadlinetime."' "
			." WHERE liga = ".$row->liga
			." AND sid = ".$row->sid
			." AND deadlinetime = '24:00:00' "
		;
		$db->setQuery($query);
		$db->query();
	}
	
	switch ($task)
	{
		case 'apply':
			$msg = JText::_( 'RUNDE_AENDERUNG').$row->nr.JText::_( 'RUNDE_GESPEICHERT' );
			$link = 'index.php?option='.$option.'&section='.$section.'&task=edit&cid[]='.$row->id.'&liga='.$cliga;
			break;
		case 'save':
		default:
			$msg = JText::_( 'RUNDE ').$row->nr.JText::_( 'RUNDE_GESPEICHERT' );
			$link = 'index.php?option='.$option.'&section='.$section.'&liga='.$cliga;
			break;
	}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = $aktion;
	$clmLog->params = array('sid' => $row->sid, 'lid' => $row->liga, 'rnd' => $row->nr);
	$clmLog->write();
	// Log schreiben bei Freigabe
	if (($row->sl_ok == 1)&&($slok_old != 1)) {
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_( 'RUNDE_LOG_FREIGABE');
		$clmLog->nr_aktion = 201;							//klkl
		$clmLog->params = array('sid' => $row->sid, 'lid' => $row->liga, 'rnd' => $row->nr);
		$clmLog->write();
	}
	if (($row->sl_ok != 1)&&($slok_old == 1)) {
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_( 'RUNDE_LOG_FREIGABE_DEL');
		$clmLog->nr_aktion = 202;							//klkl
		$clmLog->params = array('sid' => $row->sid, 'lid' => $row->liga, 'rnd' => $row->nr);
		$clmLog->write();
	}

	$mainframe->redirect( $link, $msg , "message");
	}


function cancel()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$cliga 		= intval(JRequest::getVar( 'liga' ));
	
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$id		= JRequest::getVar('id');	
	$row 		=JTable::getInstance( 'runden', 'TableCLM' );

	$msg = JText::_( 'RUNDE_AKTION');
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section.'&liga='.$cliga, $msg , "message");
	}


function remove()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		=JFactory::getDBO();
	$cid		= JRequest::getVar( 'cid', null, 'post', 'array' );
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	JArrayHelper::toInteger($cid);

	if (count($cid) < 1) {
		JError::raiseWarning(500, JText::_( 'RUNDE_SELECT', true ) );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

	$row =JTable::getInstance( 'runden', 'TableCLM' );
	$row->load( $cid[0] );
	$liga =JTable::getInstance( 'ligen', 'TableCLM' );
	$liga->load($row->liga);

	if ($liga_mt == 0) {
		$mppoint = 'league';
		$csection = 'ligen';
	} else {
		$mppoint = 'teamtournament';
		$csection = 'mturniere';
	}

	$clmAccess = clm_core::$access;      

	// Prüfen ob User Berechtigung zum löschen hat
	//if ( $liga->sl !== clm_core::$access->getJid() AND clm_core::$access->getType() !== 'admin') {
	if (( $liga->sl !== clm_core::$access->getJid() AND $clmAccess->access('BE_'.$mppoint.'_edit_round') !== true) OR ($clmAccess->access('BE_'.$mppoint.'_edit_round') === false)) {
			JError::raiseWarning( 500, JText::_( 'RUNDE_ST_LOESCHEN' ) );
			$link = 'index.php?option='.$option.'&section='.$section;
			$mainframe->redirect( $link);
					}
		$cids = implode( ',', $cid );
		$query = " DELETE FROM #__clm_runden_termine "
		. ' WHERE id IN ( '. $cids .' )';
		$db->setQuery( $query );
		if (!$db->query()) {
		echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
	}

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'RUNDE_LOG_DELETED');
	$table	=JTable::getInstance( 'runden', 'TableCLM');
	$table->load($cid[0]);
	$clmLog->params = array('sid' => $table->sid, 'lid' => $table->liga, 'rnd' => $table->nr, 'cids' => $cids);
	$clmLog->write();
	
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
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
	$row =JTable::getInstance( 'runden', 'TableCLM' );
	$row->load( $cid[0] );
	$liga =JTable::getInstance( 'ligen', 'TableCLM' );
	$liga->load($row->liga);
	if ($liga_mt == 0) {
		$mppoint = 'league';
	} else {
		$mppoint = 'teamtournament';
	}

	$clmAccess = clm_core::$access;      

	// Prüfen ob User Berechtigung hat
	if (( $liga->sl !== clm_core::$access->getJid() AND $clmAccess->access('BE_'.$mppoint.'_edit_round') !== true) OR ($clmAccess->access('BE_'.$mppoint.'_edit_round') === false)) {
		JError::raiseWarning( 500, JText::_( 'RUNDE_ST_PUBLIZIEREN' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}
		$cids = implode( ',', $cid );
		$query = 'UPDATE #__clm_runden_termine'
			. ' SET published = '.(int) $publish
			. ' WHERE id IN ( '. $cids .' )'
			. ' AND ( checked_out = 0 OR ( checked_out = '.(int) $user->get('id') .' ) )';
		$db->setQuery( $query );
	if (!$db->query()) { 
		JError::raiseError(500, $db->getErrorMsg() );
			}

	if (count( $cid ) == 1) {
		$row =JTable::getInstance( 'runden', 'TableCLM' );
	}

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'RUNDE_LOG')." ".$task;
	$table	=JTable::getInstance( 'runden', 'TableCLM');
	$table->load($cid[0]);
	$clmLog->params = array('sid' => $table->sid, 'lid' => $table->liga, 'rnd' => $table->nr, 'cids' => $cids);
	$clmLog->write();
	
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
/**
* Moves the record up one position
*/
function orderdown(  ) {
	CLMControllerRunden::order( 1 );
}

/**
* Moves the record down one position
*/
function orderup(  ) {
	CLMControllerRunden::order( -1 );
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

	$row =JTable::getInstance( 'runden', 'TableCLM' );
	$row->load( $cid[0] );
	$row->move( $inc, 'liga = '.(int) $row->liga.' AND published != 0' );

	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
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

	$row =JTable::getInstance( 'runden', 'TableCLM' );
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
	// execute updateOrder for each parent group
	$groupings = array_unique( $groupings );
	foreach ($groupings as $group){
		$row->reorder('liga = '.(int) $group);
	}
	$msg 	= 'New ordering saved';
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

	public static function paarung()
	{
	JRequest::checkToken() or die( 'Invalid Token' );
	$mainframe	= JFactory::getApplication();

	$db 		=JFactory::getDBO();
	$user 		=JFactory::getUser();
	$cid 		= intval(JRequest::getVar( 'liga' ));

	$option 	= JRequest::getCmd( 'option' );
	$section 	= JRequest::getVar( 'section' );

	// keine Liga gewählt
	if ($cid < 1) {
		$msg = JText::_( 'LIGEN_PA_AENDERN');
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section.'&liga='.$cid, $msg , "message");
	}
	// Ligadaten und Paarungsdaten holen
	$query	= "SELECT a.id as lid, a.sid, a.sl "
		." FROM #__clm_liga as a"
		." WHERE a.id = ".$cid
		;
	$db->setQuery($query);
	$liga=$db->loadObjectList();

	$clmAccess = clm_core::$access;      

	// Prüfen ob User Berechtigung hat
	if (( $liga[0]->sl !== clm_core::$access->getJid() AND $clmAccess->access('BE_league_edit_fixture') !== true) OR ($clmAccess->access('BE_league_edit_fixture') === false)) {
		$msg = JText::_( 'LIGEN_NO_FIXTURE');
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section.'&liga='.$cid, $msg , "message");
	}

	// Link MUSS hardcodiert sein !!!
	$mainframe->redirect( 'index.php?option='.$option.'&section=paarung&task=edit&cid[]='.$cid);
	}

	public static function pairingdates()
	{
	JRequest::checkToken() or die( 'Invalid Token' );
	$mainframe	= JFactory::getApplication();

	$db 		=JFactory::getDBO();
	$user 		=JFactory::getUser();
	$cid 		= intval(JRequest::getVar( 'liga' ));

	$option 	= JRequest::getCmd( 'option' );
	$section 	= JRequest::getVar( 'section' );

	// keine Liga gewählt
	if ($cid < 1) {
		$msg = JText::_( 'LIGEN_PA_AENDERN');
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section.'&liga='.$cid, $msg , "message");
	}
	// Ligadaten und Paarungsdaten holen
	$query	= "SELECT a.id as lid, a.sid, a.sl "
		." FROM #__clm_liga as a"
		." WHERE a.id = ".$cid
		;
	$db->setQuery($query);
	$liga=$db->loadObjectList();

	$clmAccess = clm_core::$access;      

	// Prüfen ob User Berechtigung hat
	if (( $liga[0]->sl !== clm_core::$access->getJid() AND $clmAccess->access('BE_league_edit_fixture') !== true) OR ($clmAccess->access('BE_league_edit_fixture') === false)) {
		$msg = JText::_( 'LIGEN_NO_FIXTURE');
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section.'&liga='.$cid, $msg , "message");
	}

	// Link MUSS hardcodiert sein !!!
	$mainframe->redirect( 'index.php?option='.$option.'&section=pairingdates&task=edit&cid[]='.$cid);
	}

	function copy()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$this->setRedirect( 'index.php?option='.$option.'&section='.$section );
	$cid	= JRequest::getVar( 'cid', null, 'post', 'array' );
	$db	=JFactory::getDBO();
	$table	=JTable::getInstance( 'runden', 'TableCLM');
	$user	= JFactory::getUser();
	$n		= count( $cid );

	$row =JTable::getInstance( 'runden', 'TableCLM' );
	$row->load( $cid[0] );
	$liga =JTable::getInstance( 'ligen', 'TableCLM' );
	$liga->load($row->liga);
	if ($liga_mt == 0) {
		$mppoint = 'league';
	} else {
		$mppoint = 'teamtournament';
	}

	$clmAccess = clm_core::$access;      

	// Prüfen ob User Berechtigung hat
	//if ( $liga->sl !== clm_core::$access->getJid() AND clm_core::$access->getType() !== 'admin') {
	if (( $liga->sl !== clm_core::$access->getJid() AND $clmAccess->access('BE_'.$mppoint.'_edit_round') !== true) OR ($clmAccess->access('BE_'.$mppoint.'_edit_round') === false)) {
	JError::raiseWarning( 500, JText::_( 'RUNDE_ST_KOPIE' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}
	if ($n > 0)
	{
		foreach ($cid as $id)
		{
			if ($table->load( (int)$id ))
			{
			$table->id			= 0;
			$table->name			= 'Kopie von ' . $table->name;
			$table->published		= 0;

			if (!$table->store()) {	return JError::raiseWarning( $table->getError() );}
			}
		else {	return JError::raiseWarning( 500, $table->getError() );	}
		}
	}
	else {	return JError::raiseWarning( 500, JText::_( 'RUNDE_NO_SELCET' ) );}

	if ($n >1) { $msg=JText::_( 'RUNDE_MSG_COPY_ENTRYS');}
		else {$msg=JText::_( 'RUNDE_MSG_COPY_ENTRY');}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'RUNDE_LOG_COPIED');
	$table	=JTable::getInstance( 'runden', 'TableCLM');
	$table->load($cid[0]);
	$clmLog->params = array('sid' => $table->sid, 'lid' => $table->liga, 'rnd' => $table->nr, 'cids' => implode( ',', $cid ));
	$clmLog->write();
	
	$this->setMessage( JText::_( $n.$msg ) );
	}

	function check()
	{
	JRequest::checkToken() or die( 'Invalid Token' );
	$mainframe	= JFactory::getApplication();

	$db	=JFactory::getDBO();
	$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
	$option 	= JRequest::getCmd( 'option' );
	$section 	= JRequest::getVar( 'section' );
	JArrayHelper::toInteger($cid, array(0));

	// keine Runde gewählt
	if ($cid[0] < 1) {
		$msg = JText::_( 'RUNDE_RUNDE_PRUE');
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg , "message");
	}

	$row =JTable::getInstance( 'runden', 'TableCLM' );
	$row->load( $cid[0] );
	$liga =JTable::getInstance( 'ligen', 'TableCLM' );
	$liga->load($row->liga);
	if ($liga->liga_mt == "0") {
		$mppoint = 'league';
		$csection = 'ligen';
	} else {
		$mppoint = 'teamtournament';
		$csection = 'mturniere';
	}

	$clmAccess = clm_core::$access;      

	// Prüfen ob User Berechtigung hat
	//if ( $liga->sl !== clm_core::$access->getJid() AND clm_core::$access->getType() !== 'admin') {
	if (( $liga->sl !== clm_core::$access->getJid() AND $clmAccess->access('BE_'.$mppoint.'_edit_round') !== true) OR ($clmAccess->access('BE_'.$mppoint.'_edit_round') === false)) {
		JError::raiseWarning( 500, JText::_( 'RUNDE_ST_PRUEFEN' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
				}
	$table	=JTable::getInstance( 'runden', 'TableCLM');
	$table->load($cid[0]);

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'RUNDE_LOG_VERIFIED');
	$table	=JTable::getInstance( 'runden', 'TableCLM');
	$table->load($cid[0]);
	$clmLog->params = array('sid' => $table->sid, 'lid' => $table->liga, 'rnd' => $table->nr);
	$clmLog->write();
	
	// Link MUSS hardcodiert sein !!!
	$mainframe->redirect( 'index.php?option='.$option.'&section=check&task=edit&cid[]='.$cid[0]);
	}
}
 
