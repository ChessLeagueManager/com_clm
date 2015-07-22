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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerGruppen extends JControllerLegacy
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
		$this->registerTask( 'unpublish',	'publish' );
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
	$filter_catid		= $mainframe->getUserStateFromRequest( "$option.filter_catid",'filter_catid',0,'int' );
	$search			= $mainframe->getUserStateFromRequest( "$option.search",'search','','string' );
	$search			= JString::strtolower( $search );
	$limit			= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
	$limitstart		= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

	$where = array();
	$where[]=' c.archiv = 0';
	if ( $filter_catid ) {	$where[] = 'a.published = '.(int) $filter_catid; }
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
		$orderby 	= ' ORDER BY sid '.$filter_order_Dir.', ordering';
	} else {
	if ($filter_order =='a.Gruppe' OR  $filter_order == 'a.Meldeschluss' OR  $filter_order == 'a.user' OR  $filter_order == 'a.saison' OR $filter_order == 'a.ordering' OR $filter_order == 'a.published' ) { 
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', a.id';
			}
		else { $filter_order = 'a.id'; $orderby='';}
	}

	// get the total number of records
	$query = ' SELECT COUNT(*) '
		.' FROM #__clm_rangliste_name AS a'
		.' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
		. $where
		;
	$db->setQuery( $query );
	$total = $db->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );

	// get the subset (based on limits) of required records
	$query = 'SELECT a.*, c.name AS saison '
	. ' FROM #__clm_rangliste_name AS a'
	. ' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
	//. ' LEFT JOIN #__clm_liga AS d ON a.liga = d.id'
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

	// state sid         nach Saison sortieren
	$sql = 'SELECT id, name FROM #__clm_saison WHERE archiv =0';
	$db->setQuery($sql);
	$saisonlist[]	= JHTML::_('select.option',  '0', JText::_( 'GRUPPEN_SAISON' ), 'id', 'name' );
	$saisonlist         = array_merge( $saisonlist, $db->loadObjectList() );
	$lists['sid']      = JHTML::_('select.genericlist', $saisonlist, 'filter_sid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','id', 'name', intval( $filter_sid ) );

	// table ordering
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;

	// search filter
	$lists['search']= $search;
	require_once(JPATH_COMPONENT.DS.'views'.DS.'gruppen.php');
	CLMViewGruppen::gruppen( $rows, $lists, $pageNav, $option );
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
	$row =JTable::getInstance( 'gruppen', 'TableCLM' );
	// load the row from the db table
	$row->load( $cid[0] );

	if (isset($row->liga)) {
		$sql = " SELECT sl FROM #__clm_liga "
			." WHERE id =".$row->liga
		//	." AND published = 1 "
			;
		$db->setQuery($sql);
		$lid = $db->loadObjectList();
	} else { $lid = 0; };
	$clmAccess = clm_core::$access;      
	if ($clmAccess->access('BE_club_edit_ranking') === false AND $task == 'edit') {
	//if ( $lid[0]->sl <> $jid AND clm_core::$access->getType() !== 'admin' AND $task == 'edit') {
		JError::raiseWarning( 500, JText::_( 'GRUPPEN_STAFFFEL' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}

	if ($task == 'edit') {
	// illegaler Einbruchversuch über URL !
	// evtl. mitschneiden !?!
	$saison		=JTable::getInstance( 'saisons', 'TableCLM' );
	$saison->load( $row->sid );
	if ($saison->archiv == "1" ) {   //AND clm_core::$access->getType() !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'GRUPPEN_RANG' ));
		$mainframe->redirect( 'index.php?option='. $option.'&section=vereine', $msg );
				}
	if ($cid[0]== "" AND $task =='edit') {
		JError::raiseWarning( 500, JText::_( 'GRUPPEN_FALSCH' ));
		$mainframe->redirect( 'index.php?option='. $option.'&section=vereine', $msg );
				}
	// do stuff for existing records
		$row->checkout( $user->get('id') );
	} else {
	// do stuff for new records  //
		$row->published 	= 0;
	}

	$lists['published']	= JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $row->published );

	// Saisonlist //
	$sql = 'SELECT id as sid, name FROM #__clm_saison WHERE archiv =0';
	$db->setQuery($sql);
	if (!$db->query()){$this->setRedirect( 'index.php?option='.$option.'&section='.$section );
		return JError::raiseWarning( 500, $db->getErrorMsg() );	}
	$saisonlist[]	= JHTML::_('select.option',  '0', JText::_( 'GRUPPEN_SAISON' ), 'sid', 'name' );
	$saisonlist	= array_merge( $saisonlist, $db->loadObjectList() );
	$lists['saison']= JHTML::_('select.genericlist',   $saisonlist, 'sid', 'class="inputbox" size="1"','sid', 'name', $row->sid );

	require_once(JPATH_COMPONENT.DS.'views'.DS.'gruppen.php');
	$jid = 0; $user_group = 0;
	CLMViewGruppen::gruppe( $row, $lists, $option,$jid,$user_group );
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
	$row 		= JTable::getInstance( 'gruppen', 'TableCLM' );
	$msg		= JRequest::getVar( 'id');
	$delete		= JRequest::getVar( 'delete');
	$create		= JRequest::getVar( 'create');

	if (!$row->bind(JRequest::get('post'))) {
		JError::raiseError(500, $row->getError() );
	}
	// pre-save checks
	if (!$row->check()) {
		JError::raiseError(500, $row->getError() );
	}

	$aktion = "Gruppe editiert";
	if (!$row->id) {
	$aktion = "Gruppe angelegt";
		$where = "sid = " . (int) $row->sid;
		$row->ordering = $row->getNextOrder( $where );
	}
	// save the changes
	if (!$row->store()) {
		JError::raiseError(500, $row->getError() );
	}
	

	if ($delete == 1) {
		JError::raiseWarning( 500, JText::_( 'GRUPPEN_RL_LOESCH' ) );
			}
	if ($create == 1) {
		JError::raiseNotice( 6000, JText::_( 'GRUPPEN_RL_NEU' ) );
			}

	switch ($task)
	{
		case 'apply':
			$msg = JText::_( 'GRUPPEN_AENDERN' );
			$link = 'index.php?option='.$option.'&section='.$section.'&task=edit&cid[]='. $row->id ;
			break;
		case 'save':
		default:
			$msg = JText::_( 'GRUPPEN_SPEICHERT');
			$link = 'index.php?option='.$option.'&section='.$section;
			break;
	}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = $aktion;
	$clmLog->params = array('sid' => $sid, 'lid' => $lid, 'zps' => $zps);
	$clmLog->write();
	
	
	$mainframe->redirect( $link, $msg );
	}


function cancel()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$id		= JRequest::getVar('id');	

	$msg = JText::_( 'GRUPPEN_AKTION').' '.$id;
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
	}


function remove()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		=JFactory::getDBO();
	$cid 		= JRequest::getVar('cid', array(), '', 'array');
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	JArrayHelper::toInteger($cid);

	if (count($cid) < 1) {
		JError::raiseWarning(500, JText::_( 'GRUPPEN_SELECT', true ) );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

	// Prüfen ob User Berechtigung zum löschen hat
	$sql = " SELECT l.sl FROM #__clm_mannschaften as a "
		." LEFT JOIN #__clm_liga as l ON ( l.id= a.liga AND l.sid = a.sid) "
		." WHERE a.id = ".$cid[0]
		;
	$db->setQuery($sql);
	$lid = $db->loadObjectList();

	// Wenn User nicht Admin oder DWZ prüfen ob SL der Staffel
	$clmAccess = clm_core::$access;      
	if ($clmAccess->access('BE_club_edit_ranking') === false) {
	//if (clm_core::$access->getType() !== 'admin' AND $lid[0]->sl != $jid) {
			JError::raiseWarning( 500, JText::_( 'GRUPPEN_RL_STAFFEL' ) );
			$link = 'index.php?option='.$option.'&section='.$section;
			$mainframe->redirect( $link);
					}
	else {
		$cids = implode( ',', $cid );
		$query = " DELETE FROM #__clm_rangliste_name "
		. ' WHERE id IN ( '. $cids .' )';

		$db->setQuery( $query );
		if (!$db->query()) {
		echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
		}}

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'GRUPPEN_MSG_AKTION' );
	$clmLog->params = array('cids' => $cids, 'zps' => $row->zps);
	$clmLog->write();
	
	if (count($cid) == 1) { 
		$msg = JText::_( 'GRUPPEN_MSG_DEL_ENTRY' ); 
	} else { 
		$msg = count($cid).' '.JText::_( 'GRUPPEN_MSG_DEL_ENTRYS' ); 
	}

	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
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
	//// Wenn User nicht Admin kein Zugang
	$clmAccess = clm_core::$access;      
	if ($clmAccess->access('BE_club_edit_ranking') !== true) {
	//if (clm_core::$access->getType() !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'GRUPPEN_STAFF' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}

	$cids = implode( ',', $cid );
	$query = 'UPDATE #__clm_rangliste_name'
		. ' SET published = '.(int) $publish
		. ' WHERE id IN ( '. $cids .' )'
		. ' AND ( checked_out = 0 OR ( checked_out = '.(int) $user->get('id') .' ) )';
	$db->setQuery( $query );

	if (!$db->query()) { JError::raiseError(500, $db->getErrorMsg() );
			}
	if (count( $cid ) == 1) {
		$row =JTable::getInstance( 'mannschaften', 'TableCLM' );
	}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'GRUPPEN_MSG_GROUP' );
	$table	=JTable::getInstance( 'mannschaften', 'TableCLM');
	$table->load($cid[0]);
	$clmLog->params = array('sid' => $table->sid, 'lid' => $table->liga, 'zps' => $table->zps, 'cids' => $cids);
	$clmLog->write();
	
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
/**
* Moves the record up one position
*/
function orderdown(  ) {
	CLMControllerGruppen::order( 1 );
}

/**
* Moves the record down one position
*/
function orderup(  ) {
	CLMControllerGruppen::order( -1 );
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

	$row =JTable::getInstance( 'gruppen', 'TableCLM' );
	$row->load( $cid[0]);
	$row->move( $inc, 'liga = '.(int) $row->liga.' AND published != 0' );

	$msg 	= 'Liste umsortiert !'.$cid[0];
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
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

	$row =JTable::getInstance( 'gruppen', 'TableCLM' );
	$groupings = array();

	// update ordering values
	for( $i=0; $i < $total; $i++ ) {
		$row->load( (int) $cid[$i] );
		// track categories
		$groupings[] = $row->sid;

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
		$row->reorder('sid = '.(int) $group);
	}
	$msg 	= 'New ordering saved';
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
	$cid	= JRequest::getVar( 'cid', null, 'post', 'array' );
	$db	=JFactory::getDBO();
	$table	=JTable::getInstance('gruppen', 'TableCLM');
	$user	= JFactory::getUser();
	$n		= count( $cid );

	// Prüfen ob User Berechtigung zum kopieren hat
	$sql = " SELECT l.sl FROM #__clm_mannschaften as a "
		." LEFT JOIN #__clm_liga as l ON ( l.id= a.liga AND l.sid = a.sid) "
		." WHERE a.id = ".$cid[0]
		;
	$db->setQuery($sql);
	$lid = $db->loadObjectList();

	// Wenn User nicht Admin oder DWZ prüfen ob SL der Staffel
	$clmAccess = clm_core::$access;      
	if ($clmAccess->access('BE_club_edit_ranking') === false) {
	//if (clm_core::$access->getType() !== 'admin' AND $lid[0]->sl != $jid) {
		JError::raiseWarning( 500, JText::_( 'GRUPPEN_KOPIE' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}
	else {

	if ($n > 0)
	{
		foreach ($cid as $id)
		{
			if ($table->load( (int)$id ))
			{
			$table->id			= 0;
			$table->Gruppe			= 'Kopie von ' . $table->Gruppe;
			$table->published		= 0;

			if (!$table->store()) {	return JError::raiseWarning( $table->getError() );}
			}
		else {	return JError::raiseWarning( 500, $table->getError() );	}
		}
	}
	else {	return JError::raiseWarning( 500, JText::_( 'GRUPPEN_NO_SELECT' ) );}

	if ($n >1) { $msg=' Einträge kopiert !';}
		else {$msg=' Eintrag kopiert !';}
	
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = "Gruppe(n) kopiert";
	$table	=JTable::getInstance( 'gruppen', 'TableCLM');
	$table->load($cid[0]);
	$clmLog->params = array('sid' => $table->sid, 'lid' => $table->liga, 'zps' => $table->zps, 'cids' => implode( ',', $cid ));
	$clmLog->write();
	
	$this->setMessage( JText::_( $n.$msg ) );
	}}
}
