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

class CLMControllerVereine extends JControllerLegacy
{
	/**
	 * Constructor
	 */
	 
function __construct( $config = array() )
	{
	$db	= JFactory::getDBO();
	$sql	=" SELECT COUNT(id) as count "
		." FROM #__clm_liga "
		." WHERE sl = ".clm_core::$access->getJid()
		." AND rang <> 0"
		;
	$db->setQuery($sql);
	$count_sl = $db->loadObjectList();
	
	DEFINE ('CLM_sl_count', $count_sl[0]->count);
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

	$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",'filter_order','a.id',	'cmd' );
	$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir','','word' );
	$filter_state		= $mainframe->getUserStateFromRequest( "$option.filter_state",'filter_state','','word' );
	$filter_sid		= $mainframe->getUserStateFromRequest( "$option.filter_sid",'filter_sid',0,'int' );
	$search			= $mainframe->getUserStateFromRequest( "$option.search",'search','','string' );
	$search			= JString::strtolower( $search );
	$limit			= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
	$limitstart		= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
	$filter_vid		= $mainframe->getUserStateFromRequest( "$option.filter_vid",'filter_vid',0,'var' );

	$where = array();
	$where[]=' c.archiv = 0';
	if ( $filter_sid ) {	$where[] = 'a.sid = '.(int) $filter_sid; }
	if ($search) {	$where[] = 'LOWER(a.name) LIKE "'.$db->escape('%'.$search.'%').'"';	}

	if ( $filter_state ) {
		if ( $filter_state == 'P' ) {
			$where[] = 'a.published = 1';
		} else if ($filter_state == 'U' ) {
			$where[] = 'a.published = 0';
		}
	}
	$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
	if ($filter_order == 'a.id'){
		$orderby 	= ' ORDER BY ordering';
	} else {
	if ($filter_order =='a.name' OR $filter_order == 'a.zps' OR $filter_order == 'a.homepage' OR $filter_order == 'c.name' OR $filter_order == 'a.published' OR $filter_order == 'a.ordering') { 
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', a.id';
			}
		else { $filter_order = 'a.id'; 
			   $orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir ; }
	}
	// get the total number of records
	$query = 'SELECT COUNT(*) '
		. ' FROM #__clm_vereine AS a'
		.' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
	. $where
	;
	$db->setQuery( $query );
	$total = $db->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );

	// get the subset (based on limits) of required records
	$query = 'SELECT a.*,u.name AS editor '
	. ', c.name AS saison '
	. ' FROM #__clm_vereine AS a'
	. ' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
	. ' LEFT JOIN #__users AS u ON u.id = a.checked_out'
	. $where
	. $orderby	;

	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );

	$rows = $db->loadObjectList();
	if ($db->getErrorNum()) {
		echo $db->stderr();
		return false;
	}
	// state filter
	$lists['state']	= JHTML::_('grid.state',  $filter_state );

	// Saisonfilter
	$sql = 'SELECT id, name FROM #__clm_saison WHERE archiv =0';
	$db->setQuery($sql);
	$saisonlist[]	= JHTML::_('select.option',  '0', JText::_( 'VEREINE_SAISON' ), 'id', 'name' );
	$saisonlist	= array_merge( $saisonlist, $db->loadObjectList() );
	$lists['sid']	= JHTML::_('select.genericlist', $saisonlist, 'filter_sid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','id', 'name', intval( $filter_sid ) );

	// table ordering
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;

	// search filter
	$lists['search']= $search;
	require_once(JPATH_COMPONENT.DS.'views'.DS.'vereine.php');
	CLMViewVereine::vereine ( $rows, $lists, $pageNav, $option );
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
	$row =JTable::getInstance( 'vereine', 'TableCLM' );
	// load the row from the db table
	$row->load( $cid[0] );
	$sid = $row->sid;
	// Userberechtigung abfragen
	$clmAccess = clm_core::$access;      
	if ($clmAccess->access('BE_club_create') === false AND $task =='add') {
	//if (clm_core::$access->getType() !== 'admin' AND $task =='add') {
		JError::raiseWarning( 500, JText::_( 'VEREINE_ADMIN' ));
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg,"message" );
				}
	if ($task == 'edit') {
		// illegaler Einbruchversuch über URL !
		// evtl. mitschneiden !?!
		$saison		=JTable::getInstance( 'saisons', 'TableCLM' );
		$saison->load( $sid );
		if ($saison->archiv == "1" ) {  //AND clm_core::$access->getType() !== 'admin') {
			JError::raiseWarning( 500, JText::_( 'VEREINE_NO_ARCHIV' ));
			$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg ,"message");
		}
		if ($cid[0]== "") { // AND $task =='edit') {
		JError::raiseWarning( 500, JText::_( 'VEREINE_FALSCH' ));
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg ,"message");
				}
	// do stuff for existing records
		$row->checkout( $user->get('id') );
	} else {
	// do stuff for new records
		$row->published 	= 0;
	}

	if ($clmAccess->access('BE_club_create') === false) {
	//if ( clm_core::$access->getType() !== 'sl' AND clm_core::$access->getType() !== 'admin' AND clm_core::$access->getType() !== 'dv' AND clm_core::$access->getType() !== 'dwz') {
		JError::raiseWarning( 500, JText::_( 'VEREINE_NO_BEARBEITEN' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}

	$sql = 'SELECT id FROM #__clm_saison WHERE archiv = 0 and published = 1';
	$db->setQuery($sql);
	$sid = $db->loadResult();

	// Vereinefilter laden
	// 1 = Auswahl DB obwohl manuell aktiviert wurde !
	$vereinlist	= CLMFilterVerein::vereine_filter(1);
	$lists['verein']= JHTML::_('select.genericlist',   $vereinlist, 'zps', 'class="inputbox" size="1" onchange="Tausch(this.id)"','zps', 'name', $row->zps );

	// Vereinsleiterliste
	if ($task == 'edit') { $where = "WHERE ZPS = '".$row->zps."'";}
		else { $where = 'WHERE ZPS IS NULL' ;}
	$sql = 'SELECT Spielername as name, Mgl_Nr as vl'
	.' FROM #__clm_dwz_spieler '.$where
	.' AND sid ='.$sid
	;
	$db->setQuery($sql);
	if (!$db->query()){$this->setRedirect( 'index.php?option='.$option.'&section='.$section );
		return JError::raiseWarning( 500, $db->getErrorMsg() );	}
	$vllist[]	= JHTML::_('select.option',  '0', JText::_( 'VEREINE_VEREINSLEITER' ), 'vl', 'name' );
	$vllist	= array_merge( $vllist, $db->loadObjectList() );
	$lists['vl']= JHTML::_('select.genericlist',   $vllist, 'vl', 'class="inputbox" size="1" onchange="VSTausch(this.id)"','vl', 'name', $row->vl );
	
	// Saisonliste
	if($task =="edit"){ $sql = 'SELECT id as sid, name FROM #__clm_saison WHERE id='.$sid;} 
	else { $sql = 'SELECT id as sid, name FROM #__clm_saison WHERE archiv =0'; }
	$db->setQuery($sql);
	if (!$db->query()){$this->setRedirect( 'index.php?option='.$option.'&section='.$section );
		return JError::raiseWarning( 500, $db->getErrorMsg() );}
	if ($task !="edit") {
	$saisonlist[]	= JHTML::_('select.option',  '0', JText::_( 'VEREINE_SAISON' ), 'sid', 'name' );
	$saisonlist	= array_merge( $saisonlist, $db->loadObjectList() );
		} else { $saisonlist	= $db->loadObjectList(); }
	$lists['saison']= JHTML::_('select.genericlist',   $saisonlist, 'sid', 'class="inputbox" size="1"','sid', 'name', $row->sid );
	
	$lists['published']	= JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $row->published );

	require_once(JPATH_COMPONENT.DS.'views'.DS.'vereine.php');
	CLMViewVereine::verein ( $row, $lists, $option );
	}

function save()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$option	= JRequest::getCmd('option');
	$section= JRequest::getVar('section');
	$db 	=JFactory::getDBO();
	$task 	= JRequest::getVar( 'task');
	$row 	=JTable::getInstance( 'vereine', 'TableCLM' );
	$msg	=JRequest::getVar( 'id');

	if (!$row->bind(JRequest::get('post'))) {
		JError::raiseError(500, $row->getError() );
	}
	// pre-save checks
	if (!$row->check()) {
		JError::raiseError(500, $row->getError() );
	}
	// if new item, order last in appropriate group
	$aktion = "Verein editiert";
	if (!$row->id) {
	$aktion = "Verein angelegt";
		$where = "sid = " . (int) $row->sid;
		$row->ordering = $row->getNextOrder($where);
	}
	// Kontrolle ob ZPS bereits existiert
	$query = " SELECT COUNT(zps) as count FROM #__clm_vereine "
		." WHERE ZPS = '".clm_escape($row->zps)."'"
		." AND sid = $row->sid "
		;

	$db->setQuery($query);
	$zps_exist = $db->loadObjectList();
	if ($zps_exist[0]->count > 0 AND !$row->id) {
		JError::raiseWarning( 500, JText::_( 'VEREINE_IST' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect($link);
		}
	
	// save the changes
	if (!$row->store()) {
		JError::raiseError(500, $row->getError() );
	}
	

	switch ($task)
	{
		case 'apply':
			$msg = JText::_( 'VEREINE_AENDERN' );
			$link = 'index.php?option='.$option.'&section='.$section.'&task=edit&cid[]='. $row->id ;
			break;
		case 'save':
		default:
			$msg = JText::_( 'VEREINE_SPEICHERN' );
			$link = 'index.php?option='.$option.'&section='.$section;
			break;
	}

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = $aktion;
	//$clmLog->params = array('sid' => $row->sid, 'lid' => $row->liga, 'zps' => $row->zps);
	$clmLog->params = array('sid' => $row->sid, 'zps' => $row->zps);
	$clmLog->write();
	
	$mainframe->redirect( $link, $msg ,"message");
	}

function cancel()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$id		= JRequest::getVar('id');	
	$row 		=JTable::getInstance( 'vereine', 'TableCLM' );

	$msg = JText::_( 'VEREINE_STOP');
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg ,"message");
	}

function remove()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		=JFactory::getDBO();
	$cid 		= JRequest::getVar('cid', array(0), '', 'array');
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');

	JArrayHelper::toInteger($cid, array(0));
	if (count($cid) < 1) {
		JError::raiseWarning(500, JText::_( 'VEREINE_SELECT', true ) );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

	$clmAccess = clm_core::$access;      
	if ($clmAccess->access('BE_club_create') === false) {
	//if ( clm_core::$access->getType() !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'VEREINE_NO_LOESCH' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}

	$cids = implode( ',', $cid );
	$query = 'DELETE FROM #__clm_vereine'
	. ' WHERE id IN ( '. $cids .' )';
	$db->setQuery( $query );
	if (!$db->query()) {
		echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
	}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = $aktion;
	$clmLog->params = array('sid' => $row->sid, 'zps' => $row->zps, 'cids' => $cids);
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

	$clmAccess = clm_core::$access;      
	if ($clmAccess->access('BE_club_create') === false) {
	//if ( clm_core::$access->getType() !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'VEREINE_NO_PUBLIZIEREN' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}

	$cids = implode( ',', $cid );
	$query = 'UPDATE #__clm_vereine'
	. ' SET published = '.(int) $publish
	. ' WHERE id IN ( '. $cids .' )'
	. ' AND ( checked_out = 0 OR ( checked_out = '.(int) $user->get('id') .' ) )';
	$db->setQuery( $query );
	if (!$db->query()) { JError::raiseError(500, $db->getErrorMsg() );
	}
	if (count( $cid ) == 1) {
		$row =JTable::getInstance( 'vereine', 'TableCLM' );
	}

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = "Verein ".$task;
	$table		= JTable::getInstance( 'vereine', 'TableCLM');
	$table->load($cid[0]);
	$clmLog->params = array('sid' => $table->sid, 'zps' => $table->zps, 'cids' => $cids);
	$clmLog->write();
	
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
/**
* Moves the record up one position
*/
function orderdown(  ) {
	CLMControllerVereine::order( 1 );
}

/**
* Moves the record down one position
*/
function orderup(  ) {
	CLMControllerVereine::order( -1 );
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

	$row =JTable::getInstance( 'vereine', 'TableCLM' );
	$row->load( $cid[0] );
	$row->move( $inc, 'sid = '.(int) $row->sid.' AND published != 0' );

	$msg 	= 'Liste umsortiert !';
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

	$row =JTable::getInstance( 'vereine', 'TableCLM' );
	$groupings = array();

	// update ordering values
	for( $i=0; $i < $total; $i++ ) {
		$row->load( (int) $cid[$i] );
		// track categories
		$groupings[] = $row->saison;

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
		$row->reorder('id = '.(int) $group);
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
	$this->setRedirect( 'index.php?option='.$option.'&section='.$section );
	$cid		= JRequest::getVar( 'cid', null, 'post', 'array' );
	$db		=JFactory::getDBO();
	$table		=JTable::getInstance('vereine', 'TableCLM');
	$user		= JFactory::getUser();
	$n		= count( $cid );

	$clmAccess = clm_core::$access;      
	if ($clmAccess->access('BE_club_create') === false) {
	//if ( clm_core::$access->getType() !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'VEREINE_NO_KOPIE' ) );
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
	else {	return JError::raiseWarning( 500, JText::_( 'VEREINE_NO_ITEMS' ) );}

	if ($n >1) { $msg=' Einträge kopiert !';}
		else {$msg=' Eintrag kopiert !';}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = "Verein kopiert";
	$clmLog->params = array('rnd' => $cid[0], 'cids' => implode( ',', $cid ));
	$clmLog->write();
	
	$this->setMessage( JText::_( $n.$msg ) );
	}


function dwz()
	{
	JRequest::checkToken() or die( 'Invalid Token' );
	$mainframe	= JFactory::getApplication();

	$option 	= JRequest::getCmd( 'option' );
	$section	= JRequest::getVar('section');


	$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
	JArrayHelper::toInteger($cid, array(0));

	// kein Verein gewählt
	if (!isset($cid[0])) {
		JError::raiseWarning( 500, JText::_( 'VEREINE_NO_DWZ' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}
	$row =JTable::getInstance( 'vereine', 'TableCLM' );
	// load the row from the db table
	$row->load( $cid[0] );
	$sid = $row->zps;
	$mainframe->setUserState( "$option.filter_vid", $row->zps );
	$mainframe->redirect( 'index.php?option='.$option.'&section=dwz');
	}

function gruppen()
	{
	JRequest::checkToken() or die( 'Invalid Token' );
	$mainframe	= JFactory::getApplication();
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');

	$clmAccess = clm_core::$access;      
	if ($clmAccess->access('BE_club_edit_ranking') === false) {
	////if ( CLM_sl_count === '0' AND clm_core::$access->getType() !== 'admin') {
	//if ( clm_core::$access->getType() !== 'sl' AND clm_core::$access->getType() !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'VEREINE_NO_RANG' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}
	// Link MUSS hardcodiert sein !!!
	$mainframe->redirect( 'index.php?option='.$option.'&section=gruppen');
	}

function rangliste()
	{
	JRequest::checkToken() or die( 'Invalid Token' );
	$mainframe	= JFactory::getApplication();

	$option 	= JRequest::getCmd( 'option' );
	$section	= JRequest::getVar('section');

	$clmAccess = clm_core::$access;      
	if ($clmAccess->access('BE_club_edit_ranking') === false) {
	////if ( CLM_sl_count === '0' AND clm_core::$access->getType() !== 'admin') {
	//if ( clm_core::$access->getType() !== 'sl' AND clm_core::$access->getType() !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'VEREINE_BEARBEITEN_RANG' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}

	$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
	JArrayHelper::toInteger($cid, array(0));
	$row =JTable::getInstance( 'vereine', 'TableCLM' );
	// load the row from the db table
	$row->load( $cid[0] );
	$sid = $row->zps;
	$mainframe->setUserState( "$option.filter_vid", $row->zps );
					
	$mainframe->redirect( 'index.php?option='.$option.'&section=ranglisten');
	}

function copy_saison()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$db		= JFactory::getDBO();
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$user		= JFactory::getUser();
	$jid		= $user->get('id');

	$clmAccess = clm_core::$access;    
	if ($clmAccess->access('BE_club_copy') === false) {
	//if (clm_core::$access->getType() !="admin" ) {
		JError::raiseWarning(500, JText::_( 'VEREINE_ADMIN', true ) );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
			}
	// id Vorsaison bestimmen
	$sql	=" SELECT id FROM #__clm_saison "
		." WHERE archiv = 1 "
		." ORDER BY id DESC LIMIT 1"
		;
	$db->setQuery($sql);
	$check = $db->loadResult();

	// keine Vorsaison existent !
	if(!$check ) {
	JError::raiseWarning(500, JText::_( 'VEREINE_NO_VORSAISON') );
	$mainframe->redirect( 'index.php?option='.$option.'&section='.$section );
		}

	// id aktuelle Saison bestimmen
	$sql	=" SELECT id FROM #__clm_saison "
		." WHERE archiv = 0 AND published = 1"
		." ORDER BY id ASC LIMIT 1"
		;
	$db->setQuery($sql);
	$sid	= $db->loadResult();

	// keine Sid gefunden
	if(!$sid) {
	JError::raiseWarning(500, JText::_('VEREINE_NO_SAISON') );
	$mainframe->redirect( 'index.php?option='.$option.'&section='.$section );
		}

	// Anzahl Vereine bestimmen
	$sql	= " SELECT COUNT(id) FROM #__clm_vereine WHERE sid = ".$check;
	$db->setQuery($sql);
	$count	= $db->loadResult();

	// keine User gefunden
	if(!$count) {
	JError::raiseWarning(500, JText::_('VEREINE_NO_VEREIN') );
	$mainframe->redirect( 'index.php?option='.$option.'&section='.$section );
		}

	// schon vorhandenen Vereine in aktueller Saison bestimmen und in Array
	$sql	=" SELECT zps FROM #__clm_vereine "
		." WHERE sid =".$sid
		." ORDER BY zps ASC "
		;
	$db->setQuery($sql);
	$akt_user	= $db->loadObjectList();

	$arr_user = array();
	foreach ($akt_user as $jid_user) {
		$arr_user[] = "'".$jid_user->zps."'";
		}
	$users = implode( ',', $arr_user );

	// Alle Vereine aus Vorsaison laden die noch nicht kopiert wurden
	$sql	=" SELECT id FROM #__clm_vereine "
		." WHERE sid = ".$check;
		if(count($akt_user) !="0") { 
			$sql = $sql.' AND zps NOT IN ('.$users.') ';}
		$sql = $sql." ORDER BY id ASC "
		;
	$db->setQuery($sql);
	$spieler	= $db->loadObjectList();

	// keine Vereine zu kopieren
	if(count($spieler) == "0") {
	JError::raiseWarning(500, JText::_( 'VEREINE_VORSAISON_IST') );
	$mainframe->redirect( 'index.php?option='.$option.'&section='.$section );
		}

	// Vereine laden und mit neuer Saison speichern
	$row =JTable::getInstance( 'vereine', 'TableCLM' );

	for($x=0; $x < count($spieler); $x++) {
		$row->load( ($spieler[$x]->id));
			$row->id	= "0";
			$row->sid	= $sid;
		if (!$row->store()) {	return JError::raiseWarning( $row->getError() );}
	}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = "Vereine Vorsaison kopiert";
	$clmLog->params = array('jid' => $jid, 'cids' => $users);
	$clmLog->write();
	
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg ,"message");
	}
}
