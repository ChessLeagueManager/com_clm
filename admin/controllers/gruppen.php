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
	$option 	= clm_core::$load->request_string('option');
	$section 	= clm_core::$load->request_string('section');
	$db=JFactory::getDBO();

	$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",'filter_order','a.id',	'cmd' );
	$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir','','word' );
	$filter_state		= $mainframe->getUserStateFromRequest( "$option.filter_state",'filter_state','','word' );
	$filter_sid		= $mainframe->getUserStateFromRequest( "$option.filter_sid",'filter_sid',0,'int' );
	$filter_catid		= $mainframe->getUserStateFromRequest( "$option.filter_catid",'filter_catid',0,'int' );
	$search			= $mainframe->getUserStateFromRequest( "$option.search",'search','','string' );
	$search			= strtolower( $search );
	$limit			= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
	$limitstart		= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

	$where = array();
	$where[]=' c.archiv = 0';
	if ( $filter_catid ) {	$where[] = 'a.published = '.(int) $filter_catid; }
	if ( $filter_sid ) {	$where[] = 'a.sid = '.(int) $filter_sid; }
	if ($search) {	$where[] = 'LOWER(a.Gruppe) LIKE "'.$db->escape('%'.$search.'%').'"';	}

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
	
	try {
		$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
		$rows = $db->loadObjectList();
	}
	catch (Exception $e) {
		$mainframe->enqueueMessage($db->stderr(), 'error');
	}
	
	// state filter
	//$lists['state']	= JHTML::_('grid.state',  $filter_state );
	$lists['state'] = CLMForm::selectState( $filter_state );

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
	$task 		= clm_core::$load->request_string( 'task');
	$cid 		= clm_core::$load->request_array_int('cid');
	if (is_null($cid)) {
		$id = clm_core::$load->request_int('id');
		$cid[0] = $id;
	}
	$option 	= clm_core::$load->request_string('option');
	$section 	= clm_core::$load->request_string('section');
										 
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
		$mainframe->enqueueMessage( JText::_( 'GRUPPEN_STAFFFEL' ), 'warning' );		
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}

	if ($task == 'edit') {
	// illegaler Einbruchversuch über URL !
	// evtl. mitschneiden !?!
	$saison		=JTable::getInstance( 'saisons', 'TableCLM' );
	$saison->load( $row->sid );
	if ($saison->archiv == "1" ) {   //AND clm_core::$access->getType() !== 'admin') {
		$mainframe->enqueueMessage( JText::_( 'GRUPPEN_RANG' ), 'warning' );		
		$mainframe->redirect( 'index.php?option='. $option.'&section=vereine', $msg );
				}
	if ($cid[0]== "" AND $task =='edit') {
		$mainframe->enqueueMessage( JText::_( 'GRUPPEN_FALSCH' ), 'warning' );		
		$mainframe->redirect( 'index.php?option='. $option.'&section=vereine' );
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
	if (!clm_core::$db->query($sql)){
		$mainframe->enqueueMessage( $db->getErrorMsg(), 'warning' );		
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
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
	defined('clm') or die( 'Invalid Token' );

	$option 	= clm_core::$load->request_string('option');
	$section 	= clm_core::$load->request_string('section');

	$db 		= JFactory::getDBO();
	$task 		= clm_core::$load->request_string( 'task');
	$row 		= JTable::getInstance( 'gruppen', 'TableCLM' );
	$msg		= clm_core::$load->request_int( 'id');
	$delete		= clm_core::$load->request_string( 'delete');
	$create		= clm_core::$load->request_string( 'create');

	$post = $_POST; 
	if (!$row->bind($post)) {
		$mainframe->enqueueMessage( $db->getError(), 'error' );		
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
	// pre-save checks
	if (!$row->check()) {
		$mainframe->enqueueMessage( $row->getError(), 'error' );		
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

	$aktion = "Gruppe editiert";
	if (!$row->id) {
		$aktion = "Gruppe angelegt";
		$where = "sid = " . (int) $row->sid;
		$row->ordering = $row->getNextOrder( $where );
	}
	// save the changes
	if (!$row->store()) {
		$mainframe->enqueueMessage( $row->getError(), 'error' );		
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
	

	if ($delete == 1) {
		$mainframe->enqueueMessage( JText::_( 'GRUPPEN_RL_LOESCH' ), 'warning' );		
			}
	if ($create == 1) {
		$mainframe->enqueueMessage( JText::_( 'GRUPPEN_RL_NEU' ), 'notice' );		
			}

	switch ($task)
	{
		case 'apply':
			$msg = JText::_( 'GRUPPEN_AENDERN' );
			$link = 'index.php?option='.$option.'&section='.$section.'&task=edit&id='. $row->id ;
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
	$clmLog->params = array('sid' => $row->sid, 'gid' => $row->id);
	$clmLog->write();
	
	$mainframe->enqueueMessage( $msg, 'message' );		
	$mainframe->redirect( $link );
	}


function cancel()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	defined('clm') or die( 'Invalid Token' );
	
	$option 	= clm_core::$load->request_string('option');
	$section 	= clm_core::$load->request_string('section');
	$id		= clm_core::$load->request_int('id');	

	$msg = JText::_( 'GRUPPEN_AKTION').' '.$id;
	$mainframe->enqueueMessage( $msg, 'message' );		
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

function remove()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	defined('clm') or die( 'Invalid Token' );

	$db 		=JFactory::getDBO();
	$cid 		= clm_core::$load->request_array_int('cid');
	$option 	= clm_core::$load->request_string('option');
	$section 	= clm_core::$load->request_string('section');
							   

	if (count($cid) < 1) {
		$mainframe->enqueueMessage( JText::_( 'GRUPPEN_SELECT', true ), 'warning' );		
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
			$mainframe->enqueueMessage( JText::_( 'GRUPPEN_RL_STAFFEL' ), 'warning' );		
			$link = 'index.php?option='.$option.'&section='.$section;
			$mainframe->redirect( $link);
					}
	else {
		$cids = implode( ',', $cid );
		$query = " DELETE FROM #__clm_rangliste_name "
		. ' WHERE id IN ( '. $cids .' )';

		//$db->setQuery( $query );
		if (!clm_core::$db->query($query)) {
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

	$mainframe->enqueueMessage( $msg, 'message' );		
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

function publish()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	defined('clm') or die( 'Invalid Token' );

	$db 		=JFactory::getDBO();
	$user 		=JFactory::getUser();
	$cid		= clm_core::$load->request_array_int('cid');
	$task		= clm_core::$load->request_string( 'task' );
	$publish	= ($task == 'publish');
	$option 	= clm_core::$load->request_string('option');
	$section 	= clm_core::$load->request_string('section');

	if (empty( $cid )) {
		$mainframe->enqueueMessage( 'No items selected', 'warning' );		
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
	//// Wenn User nicht Admin kein Zugang
	$clmAccess = clm_core::$access;      
	if ($clmAccess->access('BE_club_edit_ranking') !== true) {
		$mainframe->enqueueMessage( JText::_( 'GRUPPEN_STAFF' ), 'warning' );		
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}

	$cids = implode( ',', $cid );
	$query = 'UPDATE #__clm_rangliste_name'
		. ' SET published = '.(int) $publish
		. ' WHERE id IN ( '. $cids .' )'
		. ' AND ( checked_out = 0 OR ( checked_out = '.(int) $user->get('id') .' ) )';
	$db->setQuery( $query );

	if (!clm_core::$db->query($query)) { 
		$mainframe->enqueueMessage( $db->getErrorMsg(), 'error' );		
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
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
	defined('clm') or die( 'Invalid Token' );

	$db		=JFactory::getDBO();
	$cid		= clm_core::$load->request_array_int('cid');
	$option 	= clm_core::$load->request_string('option');
	$section 	= clm_core::$load->request_string('section');

	$limit 		= clm_core::$load->request_int( 'limit' );
	$limitstart 	= clm_core::$load->request_int( 'limitstart' );

	$row =JTable::getInstance( 'gruppen', 'TableCLM' );
	$row->load( $cid[0]);
	$row->move( $inc, 'liga = '.(int) $row->liga.' AND published != 0' );

	$msg 	= 'Liste umsortiert !'.$cid[0];
	$mainframe->enqueueMessage( $msg, 'message' );		
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

/**
* Saves user reordering entry
*/
function saveOrder(  )
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	defined('clm') or die( 'Invalid Token' );

	$db			=JFactory::getDBO();
	$cid		= clm_core::$load->request_array_int('cid');
	$option 	= clm_core::$load->request_string('option');
	$section 	= clm_core::$load->request_string('section');
	//JArrayHelper::toInteger($cid);

	$total		= count( $cid );
	$order		= clm_core::$load->request_array_int('order');

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
				$mainframe->enqueueMessage( $db->getError(), 'error' );		
				$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
			}
		}
	}
	// execute update Order for each parent group
	$groupings = array_unique( $groupings );
	foreach ($groupings as $group){
		$row->reorder('sid = '.(int) $group);
	}
	$msg 	= 'New ordering saved';
	$mainframe->enqueueMessage( $msg, 'message' );		
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

function copy()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	defined('clm') or die( 'Invalid Token' );
	$option 	= clm_core::$load->request_string('option');
	$section 	= clm_core::$load->request_string('section');
	$cid	= clm_core::$load->request_array_int('cid');
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
		$mainframe->enqueueMessage( JText::_( 'GRUPPEN_KOPIE' ), 'warning' );		
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
	}

	if ($n > 0) {
		foreach ($cid as $id)
		{
			if ($table->load( (int)$id )) {
				$table->id			= 0;
				$table->Gruppe			= 'Kopie von ' . $table->Gruppe;
				$table->published		= 0;

				if (!$table->store()) {	
					$mainframe->enqueueMessage( $table->getError(), 'error' );		
					$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
				}
			} else {	
				$mainframe->enqueueMessage( $table->getError(), 'warning' );		
				$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
			}
		}
	} else {	
		$mainframe->enqueueMessage( JText::_( 'GRUPPEN_NO_SELECT' ), 'warning' );		
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

	if ($n >1) { $msg=' Einträge kopiert !';}
		else {$msg=' Eintrag kopiert !';}
	
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = "Gruppe(n) kopiert";
	$table	=JTable::getInstance( 'gruppen', 'TableCLM');
	$table->load($cid[0]);
	$clmLog->params = array('sid' => $table->sid, 'lid' => $table->liga, 'zps' => $table->zps, 'cids' => implode( ',', $cid ));
	$clmLog->write();
	
	$mainframe->enqueueMessage( $n.$msg, 'nessage' );		
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
}
