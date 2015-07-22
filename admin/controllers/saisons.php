<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 - 2014 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class CLMControllerSaisons extends JController
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

function display()
	{
	$mainframe	= JFactory::getApplication();
	$option 	= JRequest::getCmd( 'option' );

	$db	=& JFactory::getDBO();

	$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",'filter_order','a.id',	'cmd' );
	$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir','','word' );
	$filter_state		= $mainframe->getUserStateFromRequest( "$option.filter_state",'filter_state','','word' );
	$search			= $mainframe->getUserStateFromRequest( "$option.search",'search','','string' );
	$search			= JString::strtolower( $search );
	$limit			= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
	$limitstart		= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

	$where = array();
	$orderby = "";
	if ($search) {	$where[] = 'LOWER(a.name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );	}

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
	if ($filter_order =='a.name' OR $filter_order == 'a.published' OR $filter_order == 'a.ordering' OR $filter_order == 'a.archiv') { 
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', a.id';
			}
		else { $filter_order = 'a.id'; }

	}

	// get the total number of records
	$query = 'SELECT COUNT(*) '
	. ' FROM #__clm_saison AS a'
	. $where
	;
	$db->setQuery( $query );
	$total = $db->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );

	// get the subset (based on limits) of required records
	$query = 'SELECT a.*,u.name AS editor '
		. ' FROM #__clm_saison AS a'
		. ' LEFT JOIN #__users AS u ON u.id = a.checked_out'
		. $where
		. $orderby	;
	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );

	$rows = $db->loadObjectList();
	if ($db->getErrorNum()) {
		echo $db->stderr();
		return false;
	}

	// aktive Saisons zählen
	$query = ' SELECT COUNT(id) as id FROM #__clm_saison '
		.' WHERE archiv = 0 AND published = 1'
		;
	$db->setQuery( $query);
	$counter = $db->loadResult();
	if($counter > 1){
		if($counter >2) { $s="s"; }
		JError::raiseNotice( 6000,  JText::_( 'SAISON_GIBT').' '.$counter.' '.JText::_( 'SAISON_AKTIVE').' '.($counter-1).' '.JText::_( 'SAISON').' '.$s.' '.JText::_( 'SAISON_ZURUECK' )); }

	// state filter
	$lists['state']	= JHtml::_('grid.state',  $filter_state );

	// table ordering
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;

	// search filter
	$lists['search']= $search;
	require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'saisons.php');
	CLMViewSaisons::saisons( $rows, $lists, $pageNav, $option );
}


function edit()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries

	$db 		=& JFactory::getDBO();
	$user 		=& JFactory::getUser();
	$task 		= JRequest::getVar( 'task');
	$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
	$option 	= JRequest::getCmd( 'option' );
	JArrayHelper::toInteger($cid, array(0));
	$row =& JTable::getInstance( 'saisons', 'TableCLM' );
	// load the row from the db table //
	$row->load( $cid[0] );
	if ($task == 'edit') {
	// do stuff for existing records
		$row->checkout( $user->get('id') );
	} else {
	// do stuff for new records  //
		$row->published 	= 0;
	}

	// Archiv
	$lists['archiv']	= JHtml::_('select.booleanlist',  'archiv', 'class="inputbox"', $row->archiv );

	$lists['published']	= JHtml::_('select.booleanlist',  'published', 'class="inputbox"', $row->published );

	require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'saisons.php');
	CLMViewSaisons::saison( $row, $lists, $option );
	}


function save()
	{
	$mainframe	= JFactory::getApplication();
$option 	= JRequest::getCmd( 'option' );

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');

	$db 		= & JFactory::getDBO();
	$task 		= JRequest::getVar( 'task');
	$row 		= & JTable::getInstance( 'saisons', 'TableCLM' );
	$row_old 	= & JTable::getInstance( 'saisons', 'TableCLM' );

	if (!$row->bind(JRequest::get('post'))) {
		JError::raiseError(500, $row->getError() );
	}
	// pre-save checks
	if (!$row->check()) {
		JError::raiseError(500, $row->getError() );
	}
	// if new item, order last in appropriate group
	$new_saison = 0;
	if (!$row->id) {
		$aktion = JText::_( 'SAISON_AKTION_NEW_SEASON');
		$where = "sid = " . (int) $row->sid;
		$row->ordering = $row->getNextOrder( $where );
		
		// Marker setzen um CLM-User für neue Saison anlegen
		$new_saison	= 1; 
	}
	// Prüfen ob eine aktive Saison mit mindestens einem Admin existiert
	else {
		$aktion = JText::_( 'SAISON_AKTION_SEASON_EDIT');
		$row_old->load($row->id);

		if($row_old->archiv =="0" OR $row_old->published =="1"){
			if($row->archiv =="1" OR $row->published=="0"){
				$query	= "SELECT COUNT(id) as id FROM #__clm_saison "
					." WHERE published=1 AND archiv =0 "
					;
				$db->setQuery($query);
				$sid_count = $db->loadObject();

				// es gibt noch mindestens eine veröffentlichte Saison
				if($sid_count->id =="1" AND $row_old->published =="1" AND $row_old->archiv =="0") {
					JError::raiseNotice( 500, JText::_( 'SAISON_LETZTE'));
					JError::raiseWarning( 500, JText::_( 'SAISON_NO'));
					$row->checkin();
					$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
				}

				// es gibt mindestens einen Admin in einer der neuen Saisons
					$query	= "SELECT COUNT(a.jid) as id FROM #__clm_user as a"
						." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
						." WHERE user_clm = 100 "
						." AND jid = ".CLM_ID
						." AND s.published = 1 AND s.archiv=0"
						." AND s.id <> ".$row->id
						;
					$db->setQuery($query);
					$count_user = $db->loadObject();

					if($count_user->id < "1") {
						JError::raiseNotice( 500, JText::_( 'SAISON_SPERREN_AUS'));
						JError::raiseWarning( 500, JText::_( 'SAISON_NO'));
						$msg=JText::_( 'SAISON_MSG_ACCOUNT_COPY');
						$row->checkin();
						$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
					}
			}
		}
	}
	// save the changes
	if (!$row->store()) {
		JError::raiseError(500, $row->getError() );
	}
	$row->checkin();

	if ($new_saison	== 1) { 
		// CLM-User für neue Saison anlegen
		$query	= "SELECT * FROM #__clm_user "
				." WHERE jid = ".CLM_ID
				." ORDER BY sid DESC  LIMIT 1"
				;
		$db->setQuery($query);
		$act_user = $db->loadObjectList();
		$query = " INSERT INTO #__clm_user "
			." (  `sid`, `jid`, `name`, `username` ,`aktive` ,`email` ,`tel_fest` "
			." ,`tel_mobil`, `usertype`,`user_clm`, `zps`, `mglnr` "
			." ,`mannschaft`, `published`, `bemerkungen`, `bem_int` ) " 
		.' VALUES ( '.$row->id.', '.$act_user[0]->jid.',"'.$act_user[0]->name.'", "'.$act_user[0]->username.'", 1, "'.$act_user[0]->email.'", "'.$act_user[0]->tel_fest.'" ' 
		.' ,"'.$act_user[0]->tel_mobil.'", "'.$act_user[0]->usertype.'", 100, "'.$act_user[0]->zps.'", "'.$act_user[0]->mglnr.'" ' 
		.' , 0, 1, "'.$act_user[0]->bemerkungen.'", "'.$act_user[0]->bem_int.'" )';
  		$db->setQuery( $query );
  		$db->query();
	}

	// set current season 
	$db->setQuery("SELECT id FROM #__clm_saison WHERE published = 1 AND archiv = 0 ORDER BY name ASC LIMIT 1 ");
	$c_season = $db->loadObject()->id;
	DEFINE ('CLM_SEASON', $c_season);

	switch ($task)
	{
		case 'apply':
			$msg = JText::_( 'SAISON_AENDERN' );
			$link = 'index.php?option='.$option.'&section='.$section.'&task=edit&cid[]='. $row->id ;
			break;
		case 'save':
		default:
			$msg = JText::_( 'SAISON_SAISON_GESPEI' );
			$link = 'index.php?option='.$option.'&section='.$section;
			break;
	}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = $aktion;
	$clmLog->params = array('sid' => $row->sid);
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
	$row 		=& JTable::getInstance( 'saisons', 'TableCLM' );
	$row->checkin( $id);

	$msg = JText::_( 'SAISON_AKTION');
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
	}


function remove()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		=& JFactory::getDBO();
	$cid 		= JRequest::getVar('cid', array(), '', 'array');
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	JArrayHelper::toInteger($cid);

	if (count($cid) < 1) {
		JError::raiseWarning(500, JText::_( 'SAISON_SELECT', true ) );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
	//$cids = implode( ',', $cid );

		$query	= "SELECT COUNT(u.jid) as jid FROM #__clm_saison as a "
			." LEFT JOIN #__clm_user as u ON u.sid = a.id "
			." WHERE a.id NOT IN ( $cid[0] )"
			." AND ( a.checked_out = 0 OR ( a.checked_out = ".CLM_ID." ) )"
			." AND u.user_clm = 100 AND u.jid = ".CLM_ID
			." AND a.published = 1 AND a.archiv=0"
			;
		$db->setQuery($query);
		$count = $db->loadObject();

	if($count->jid < "1") {
		JError::raiseWarning( 500, JText::_( 'SAISON_AKTION_NO'));
		JError::raiseNotice( 500, JText::_( 'SAISON_MIN'));
		$msg = JText::_( 'SAISON_MSG_MEASURES');
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
		}

	// Datensätze löschen
	$query = 'DELETE FROM #__clm_saison'
		. ' WHERE id IN ( '. $cid[0] .' )';
	$db->setQuery( $query );
	$db->query();

	$query = " DELETE FROM #__clm_dwz_spieler "
		." WHERE sid = ".$cid[0]
		;
	$db->setQuery( $query );
	$db->query();

	$query = " DELETE FROM #__clm_dwz_vereine "
		." WHERE sid = ".$cid[0]
		;
	$db->setQuery( $query );
	$db->query();

	$query = " DELETE FROM #__clm_liga "
		." WHERE sid = ".$cid[0]
		;
	$db->setQuery( $query );
	$db->query();

	$sql = "DELETE FROM #__clm_mannschaften "
		."WHERE sid = ".$cid[0]
		;
	$db->setQuery( $sql );
	$db->query();

	$sql = "DELETE FROM #__clm_meldeliste_spieler "
		."WHERE sid = ".$cid[0]
		;
	$db->setQuery( $sql );
	$db->query();

	$query = " DELETE FROM #__clm_rangliste_id "
		." WHERE sid = ".$cid[0]
		;
	$db->setQuery( $query );
	$db->query();

	$query = " DELETE FROM #__clm_rangliste_name "
		." WHERE sid = ".$cid[0]
		;
	$db->setQuery( $query );
	$db->query();

	$query = " DELETE FROM #__clm_rangliste_spieler "
		." WHERE sid = ".$cid[0]
		;
	$db->setQuery( $query );
	$db->query();

	$sql = "DELETE FROM #__clm_rnd_man "
		."WHERE sid = ".$cid[0]
		;
	$db->setQuery( $sql );
	$db->query();

	$sql = "DELETE FROM #__clm_rnd_spl "
		."WHERE sid = ".$cid[0]
		;
	$db->setQuery( $sql );
	$db->query();

	$sql = "DELETE FROM #__clm_runden_termine "
		."WHERE sid = ".$cid[0]
		;
	$db->setQuery( $sql );
	$db->query();

	$query = " DELETE FROM #__clm_turniere "
		." WHERE sid = ".$cid[0]
		;
	$db->setQuery( $query );
	$db->query();

	$query = " DELETE FROM #__clm_turniere_rnd_spl "
		." WHERE sid = ".$cid[0]
		;
	$db->setQuery( $query );
	$db->query();

	$query = " DELETE FROM #__clm_turniere_rnd_termine "
		." WHERE sid = ".$cid[0]
		;
	$db->setQuery( $query );
	$db->query();

	$query = " DELETE FROM #__clm_turniere_tlnr "
		." WHERE sid = ".$cid[0]
		;
	$db->setQuery( $query );
	$db->query();

	$query = " DELETE FROM #__clm_vereine "
		." WHERE sid = ".$cid[0]
		;
	$db->setQuery( $query );
	$db->query();

	$sql = "DELETE FROM #__clm_user "
		."WHERE sid = '$cid[0]'"
		;
	$db->setQuery( $sql );
	$db->query();

	$sql = "DELETE FROM #__clm_swt_liga "
		."WHERE sid = '$cid[0]'"
		;
	$db->setQuery( $sql );
	$db->query();

	$sql = "DELETE FROM #__clm_swt_mannschaften "
		."WHERE sid = '$cid[0]'"
		;
	$db->setQuery( $sql );
	$db->query();

	$sql = "DELETE FROM #__clm_swt_meldeliste_soieler "
		."WHERE sid = '$cid[0]'"
		;
	$db->setQuery( $sql );
	$db->query();

	$sql = "DELETE FROM #__clm_swt_rnd_man "
		."WHERE sid = '$cid[0]'"
		;
	$db->setQuery( $sql );
	$db->query();

	$sql = "DELETE FROM #__clm_swt_rnd_spl "
		."WHERE sid = '$cid[0]'"
		;
	$db->setQuery( $sql );
	$db->query();

	$sql = "DELETE FROM #__clm_swt_turniere "
		."WHERE sid = '$cid[0]'"
		;
	$db->setQuery( $sql );
	$db->query();

	$sql = "DELETE FROM #__clm_swt_turniere_rnd_spl "
		."WHERE sid = '$cid[0]'"
		;
	$db->setQuery( $sql );
	$db->query();

	$sql = "DELETE FROM #__clm_swt_turniere_rnd_termine "
		."WHERE sid = '$cid[0]'"
		;
	$db->setQuery( $sql );
	$db->query();

	$sql = "DELETE FROM #__clm_turniere_tlnr "
		."WHERE sid = '$cid[0]'"
		;
	$db->setQuery( $sql );
	$db->query();

	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'SAISON_AKTION_SEASON_DEL');
	$clmLog->params = array('sid' => $cid[0], 'cids' => $cids);
	$clmLog->write();
	

	$msg = JText::_( 'SAISON_LOESCH');
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
	}


function publish()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		=& JFactory::getDBO();
	$user 		=& JFactory::getUser();
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

	$cids = implode( ',', $cid );
	// Prüfen ob eine aktive Saison mit mindestens einem Admin existiert
	if($task!="publish"){
		$query	= "SELECT COUNT(u.jid) as jid FROM #__clm_saison as a "
			." LEFT JOIN #__clm_user as u ON u.sid = a.id "
			." WHERE a.id NOT IN ( $cids )"
			." AND ( a.checked_out = 0 OR ( a.checked_out = ".CLM_ID." ) )"
			." AND u.user_clm = 100 AND u.jid = ".CLM_ID
			." AND a.published = 1 AND a.archiv=0"
			;
		$db->setQuery($query);
		$count = $db->loadObject();

	if($count->jid < "1") {
		JError::raiseWarning( 500, JText::_( 'SAISON_AKTION_NO'));
		JError::raiseNotice( 500, JText::_( 'SAISON_MIN'));
		$msg = JText::_( 'SAISON_MSG_MEASURES2');
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
		}
	}

	$query = 'UPDATE #__clm_saison'
	. ' SET published = '.(int) $publish
	. ' WHERE id IN ( '. $cids .' )'
	. ' AND ( checked_out = 0 OR ( checked_out = '.CLM_ID.' ) )';
	$db->setQuery( $query );
	if (!$db->query()) { JError::raiseError(500, $db->getErrorMsg() );
	}
	if (count( $cid ) == 1) {
		$row =& JTable::getInstance( 'saisons', 'TableCLM' );
		$row->checkin( $cid[0] );
	}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'SAISON_AKTION_SEASON')." ".$task;
	$clmLog->params = array('sid' => $cid[0], 'cids' => $cids);
	$clmLog->write();
	

	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
/**
* Moves the record up one position
*/
function orderdown(  ) {
	order( -1 );
}

/**
* Moves the record down one position
*/
function orderup(  ) {
	order( 1 );
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

	$db		= & JFactory::getDBO();
	$cid		= JRequest::getVar('sid', array(0), '', 'array');
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	JArrayHelper::toInteger($cid, array(0));
	$limit 		= JRequest::getVar( 'limit', 0, '', 'int' );
	$limitstart 	= JRequest::getVar( 'limitstart', 0, '', 'int' );
	$sid 		= JRequest::getVar( 'sid', 0, '', 'int' );

	$row =& JTable::getInstance( 'saisons', 'TableCLM' );
	$row->load( $sid[0] );
	$row->move( $inc, 'id = '.(int) $row->catid.' AND published != 0' );

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

	$db			=& JFactory::getDBO();
	$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	JArrayHelper::toInteger($cid);

	$total		= count( $cid );
	$order		= JRequest::getVar( 'order', array(0), 'post', 'array' );
	JArrayHelper::toInteger($order, array(0));

	$row =& JTable::getInstance( 'saisons', 'TableCLM' );
	$groupings = array();

	// update ordering values
	for( $i=0; $i < $total; $i++ ) {
		$row->load( (int) $cid[$i] );
		// track categories
		$groupings[] = $row->id;

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

function copy()
	{
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$this->setRedirect( 'index.php?option='.$option.'&section='.$section );
	$cid	= JRequest::getVar( 'cid', null, 'post', 'array' );
	$db	=& JFactory::getDBO();
	$table	=& JTable::getInstance('saisons', 'TableCLM');
	$user	= &JFactory::getUser();
	$n		= count( $cid );

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
	else {	return JError::raiseWarning( 500, JText::_( 'SAISON_NO_SELECT' ) );}

	if ($n >1) { $msg=JText::_( 'SAISON_MSG_ENTRYS_COPY');}
		else {$msg=JText::_( 'SAISON_MSG_ENTRY_COPY');}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'SAISON_AKTION_SEASON_COPY');
	$clmLog->params = array('sid' => $cid[0], 'cids' => implode( ',', $cid ));
	$clmLog->write();
	

	$this->setMessage( JText::_( $n.$msg ) );
	}

///////////////
// DEBUGGING //
///////////////
function change()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$db	=& JFactory::getDBO();
	$table	=& JTable::getInstance('saisons', 'TableCLM');

	$table->load(1);
	$pub_1 = $table->archiv;
	if ($pub_1 =="1") { $table->archiv = 0; }
		else { $table->archiv = 1; }
	$table->store();

	$table->load(2);
	$pub_2 = $table->published;
	if ($pub_2 =="1") { $table->published = 0; }
		else { $table->published = 1; }
	$table->store();

	$msg 	= JText::_( 'SAISON_MSG_STATUS');
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section ,$msg);

	}
function dwz_start()          //klkl
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		=& JFactory::getDBO();
	$cid 		= JRequest::getVar('cid', array(), '', 'array');
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	JArrayHelper::toInteger($cid);

	// keine Saison gewählt
	if ($cid[0] < 1 ) {
	JError::raiseWarning( 500, JText::_( 'SAISON_NO_AUSWERTEN' ) );
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
		}
	//Saison wird durch User im Screen bestimmt
	$row =& JTable::getInstance( 'saisons', 'TableCLM' );
	$row->load( $cid[0] );
	// load the row from the db table
		$sid= $row->id;
	
	// Zeiger setzen auf saisonweite DWZ-Auswertung
	$dwz	= 1;

	// Prüfen ob User Berechtigung zum auswerten hat
	require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
	$clmAccess = new CLMAccess();
	$clmAccess->accesspoint = 'BE_season_general';
	if ($clmAccess->access() === false) {
	//if ( CLM_usertype !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'SAISON_ST_AUSWER' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}

	require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'runden.php');
	CLMViewRunden::dwz( $option, 1, $sid, 0 );

	}
function dwz_del()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		=& JFactory::getDBO();
	$cid 		= JRequest::getVar('cid', array(), '', 'array');
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	JArrayHelper::toInteger($cid);

	// Zeiger setzen auf saisonweite DWZ-Auswertung
	$dwz	= 1;

	// keine Saison gewählt
	if ($cid[0] < 1 ) {
	JError::raiseWarning( 500, JText::_( 'SAISON_NO_LOESCH' ) );
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
		}
	//Saison wird durch User im Screen bestimmt
	$row =& JTable::getInstance( 'saisons', 'TableCLM' );
	$row->load( $cid[0] );
	// load the row from the db table
		$sid= $row->id;
	
	// Prüfen ob User Berechtigung zum löschen hat
	require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
	$clmAccess = new CLMAccess();
	$clmAccess->accesspoint = 'BE_season_general';
	if ($clmAccess->access() === false) {
	//if ( CLM_usertype !== 'admin') {
			JError::raiseWarning( 500, JText::_( 'SAISON_DWZ_LOESCH' ) );
			$link = 'index.php?option='.$option.'&section='.$section;
			$mainframe->redirect( $link);
					}
	// Löschen der DWZ-Auswertung zur Saison
	$query = "UPDATE #__clm_dwz_spieler"
		." SET DWZ_neu = 0"
		." , I0 = 0"
		." , Punkte = 0"
		." , Partien = 0"
		." , WE = 0"
		." , Leistung = 0"
		." , EFaktor = 0"
		." , Niveau = 0"
		." WHERE sid = ".$sid;
	$db->setQuery( $query );
	$db->query();

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'SAISON_LOG_DWZ_DEL');
	$clmLog->nr_aktion = 102;	//klkl
	$clmLog->params = array('sid' => $sid, 'lid' => 0);  
	$clmLog->write();
	
	$msg = JText::_( 'SAISON_DWZ_IST_LOESCH');
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
	}
}