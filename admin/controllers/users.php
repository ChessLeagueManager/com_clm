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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class CLMControllerUsers extends JController
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

function display()
	{
	$mainframe	= JFactory::getApplication();
	$option 	= JRequest::getCmd( 'option' );
	$section	= JRequest::getVar('section');
	$db		=& JFactory::getDBO();

	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'classes'.DS.'CLMAccess.class.php');
	$clmAccess = new CLMAccess();
	$usertypestring = $clmAccess->usertypelist();  // usertypes, die der aktive user ändern darf
	
	$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",'filter_order','a.id',	'cmd' );
	$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir','','word' );
	$filter_state		= $mainframe->getUserStateFromRequest( "$option.filter_state",'filter_state','','word' );
	$filter_sid		= $mainframe->getUserStateFromRequest( "$option.filter_sid",'filter_sid',0,'int' );
	$filter_vid		= $mainframe->getUserStateFromRequest( "$option.filter_vid",'filter_vid',0,'string' );
	$filter_usertype	= $mainframe->getUserStateFromRequest( "$option.filter_usertype",'filter_usertype',0,'string' );
	$search			= $mainframe->getUserStateFromRequest( "$option.search",'search','','string' );
	$search			= JString::strtolower( $search );
	$limit			= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
	$limitstart		= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

	$where = array();
	$where[]=' c.archiv = 0'; // AND c.published = 1';
	if ( $filter_usertype ) {	$where[] = "a.usertype = '$filter_usertype'"; }
	if ( $filter_sid ) {	$where[] = 'a.sid = '.(int) $filter_sid; }
	if ( $filter_vid ) {	$where[] = "a.zps = '$filter_vid'"; }
	if ($search) {	$where[] = 'LOWER(a.name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );	}

	if ( $filter_state ) {
		if ( $filter_state == 'P' ) {
			$where[] = 'a.published = 1';
		} else if ($filter_state == 'U' ) {
			$where[] = 'a.published = 0';
		}
	}
	$where[]=' a.usertype IN ('.$usertypestring.' ) '; 

	$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
	if ($filter_order == 'a.id') {
		$orderby 	= ' ORDER BY  d.ordering ASC, c.id '.$filter_order_Dir;
	} elseif ($filter_order == 'd.name') { 
		$orderby 	= ' ORDER BY d.ordering '.$filter_order_Dir.', a.id';
	} elseif ($filter_order =='name' OR $filter_order == 'd.name' OR $filter_order == 'b.Vereinname' OR $filter_order == 'c.name' OR $filter_order == 'u.lastvisitDate' OR $filter_order == 'a.aktive' OR  $filter_order == 'a.published' OR $filter_order == 'a.ordering') { 
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', a.id';
	} else { $filter_order = 'a.id'; }
 
	// get the total number of records
	$query = ' SELECT COUNT(*) '
		.' FROM #__clm_user AS a'
		.' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
		. $where
		;
	$db->setQuery( $query );
	$total = $db->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );

	// nur wegen leistungsschwacher Provider
	$query	= " SET SQL_BIG_SELECTS=1";
	$db->setQuery($query);
	$db->query();

	// get the subset (based on limits) of required records
	$query = 'SELECT a.*, c.name AS saison, b.Vereinname as verein, u.name AS editor, d.name as funktion'
		.' , d.ordering as ut_ordering, u.lastvisitDate as date'
		. ' FROM #__clm_user AS a'
		. ' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
		. ' LEFT JOIN #__users AS u ON u.id = a.jid'
		. ' LEFT JOIN #__clm_dwz_vereine AS b ON a.zps = b.ZPS AND a.sid = b.sid'
		. ' LEFT JOIN #__clm_vereine AS e ON e.zps = a.zps AND e.sid = a.sid'
		. ' LEFT JOIN #__clm_usertype AS d ON d.usertype = a.usertype'
	. $where
	. $orderby	;

	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );

	$rows = $db->loadObjectList();
	if ($db->getErrorNum()) {
		echo $db->stderr();
		return false;
	}

	// Statusfilter
	$lists['state']	= JHTML::_('grid.state',  $filter_state );
	// Saisonfilter
	$sql = 'SELECT id, name FROM #__clm_saison WHERE archiv =0';
	$db->setQuery($sql);
	$saisonlist[]		= JHTML::_('select.option',  '0', JText::_( 'USERS_SAISON' ), 'id', 'name' );
	$saisonlist		= array_merge( $saisonlist, $db->loadObjectList() );
	$lists['sid']		= JHTML::_('select.genericlist', $saisonlist, 'filter_sid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','id', 'name', intval( $filter_sid ) );

	// Vereinefilter laden
	require_once(JPATH_COMPONENT.DS.'controllers'.DS.'filter_vereine.php');
	$vereinlist	= CLMFilterVerein::vereine_filter(0);
	$lists['vid']	= JHTML::_('select.genericlist', $vereinlist, 'filter_vid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','zps', 'name', $filter_vid );

	// Funktionsliste
	$sql = 'SELECT usertype, name FROM #__clm_usertype '
			.' WHERE usertype IN ('.$usertypestring.' ) ' 
			.' ORDER BY ordering ASC ';
	$db->setQuery($sql);
	$usertypelist[]	= JHTML::_('select.option',  '0', JText::_( 'USERS_BENUTZER_DD' ), 'usertype', 'name' );
	$usertypelist		= array_merge( $usertypelist, $db->loadObjectList() );
	$lists['usertype']	= JHTML::_('select.genericlist',   $usertypelist, 'filter_usertype', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','usertype', 'name', intval ($filter_usertype) );
	// Ordering
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;
	// Suchefilter
	$lists['search']= $search;

	require_once(JPATH_COMPONENT.DS.'views'.DS.'users.php');
	CLMViewUsers::users( $rows, $lists, $pageNav, $option );
}


function edit()
	{
	$mainframe = JFactory::getApplication();

	$db 		=& JFactory::getDBO();
	$user 		=& JFactory::getUser();
	$task 		= JRequest::getVar( 'task');
	$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
	$option 	= JRequest::getCmd( 'option' );
	$section 	= JRequest::getVar( 'section' );
	JArrayHelper::toInteger($cid, array(0));

	// Prüfen ob User Berechtigung zum editieren hat //
	$row	= & JTable::getInstance( 'users', 'TableCLM' );
	$row->load( $cid[0] );
	$id	= $row->jid;
	$jid	= $user->get('id');
	//$gid 	= key($user->get('groups')); // 6 = Manager ; 7 = Admin; 8 = Superadmin ; 2= registered
 	// mit key wird hier nur der erste Schluessel (nicht content, obwohl derzeit identisch!)
 	// geliefert, beim späteren Zurückschreiben fehlen dann alle weiteren Rechte.
 	// besser also folgende Version nutzen:
	$gids	= $user->get('groups');
 	$gid	= 0;
 	foreach ($gids as $key => $value) {
 		$ivalue = intval($value);
 		if (($ivalue == 2) || ($ivalue == 6) || ($ivalue == 7) || ($ivalue == 8)) {
 			if ($ivalue > $gid) {	// Reihenfolge der Values von oben beachten !
 				$gid = $ivalue;
 			}
 		}
 	}
 	$sid	= $row->sid;

	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'classes'.DS.'CLMAccess.class.php');
	$clmAccess = new CLMAccess();
	$usertypestring = $clmAccess->usertypelist();		// usertypes, die der aktive user ändern darf

	// illegaler Einbruchversuch über URL !
	// evtl. mitschneiden !?!
	$saison		=& JTable::getInstance( 'saisons', 'TableCLM' );
	$saison->load( $sid );
	$clmAccess->accesspoint = 'BE_user_general';
	if ( $saison->archiv == "1" AND $clmAccess->access() ) {
	//if ($saison->archiv == "1" AND CLM_usertype !== 'admin') {
		JError::raiseWarning( 500, JText::_( 'USERS_USER_BEAR' ));
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
				}
	if ($cid[0]== "" AND $task =='edit') {
		JError::raiseWarning( 500, JText::_( 'USERS_FALSCH' ));
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
				}

	// User 62 (1. Superadmin) kann von niemanden geändert werden
	//	 - Warum wird dann denn ausgerechnet 42 geprüft? -> FB
 	$user_publish = new JUser($id);
	if ((JVersion::isCompatible("1.6.0") AND $user_publish->get('id') == 42 AND $user->get( 'id' ) != 42 ) OR
		(JVersion::isCompatible("1.5.0") AND $user_publish->get('id') == 62 AND $user->get( 'id' ) != 62 ) )   {
	JError::raiseWarning( 500, JText::_( 'USERS_USER_NO') );
	$link = 'index.php?option='.$option.'&section='.$section;
	$mainframe->redirect( $link, $msg );
	}
	// Es können keine Admin / Superadmin geändert werden von nicht-Superadmin-User
 	// Fehler: get('gid') existiert nicht mehr
 	// also erst wie oben gid laden, dann mit neuer gid prüfen
 	//if ( $user_publish->get('gid') > 6 AND $gid < 8 )
 	$newgid = 0;
 	$newgids = $user_publish->get('groups');
 	foreach ($newgids as $key => $value) {
		$ivalue = intval($value);
		if (($ivalue == 2) || ($ivalue == 6) || ($ivalue == 7) || ($ivalue == 8)) {
			if ($ivalue > $newgid) { // Reihenfolge der Values von oben beachten !
				$newgid = $ivalue;
			}
		}
	}
	if ( $newgid > 6 AND $gid < 8 )
	{
	JError::raiseWarning( 500, JText::_( 'USERS_NO_JOMMLA_ADMIN') );
	$link = 'index.php?option='.$option.'&section='.$section;
	$mainframe->redirect( $link, $msg );
	}
	// User kann nur niedrigere CLM-Berechtigungen ändern
	//$sql = "SELECT usertype, user_clm, jid FROM #__clm_user WHERE jid =".$jid;
	//$db->setQuery($sql);
	//$clmuser = $db->loadObjectList();

	//if ( $clmuser[0]->user_clm <= $row->user_clm AND $jid != $row->jid AND $gid != 8)
	$clmAccess->accessusertype = $row->usertype;
	if ( !$clmAccess->comparison() ) {
		JError::raiseWarning( 500, JText::_( 'USERS_BENUTZER') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}
 
	if ($task == 'edit') {
	// do stuff for existing records
		$row->checkout( $user->get('id') );
	} else {
	// do stuff for new records
		$row->published 	= 0;
		$row->aktive	 	= 0;
	}

	// Vereinefilter laden
	require_once(JPATH_COMPONENT.DS.'controllers'.DS.'filter_vereine.php');
	$vereinlist	= CLMFilterVerein::vereine_filter(0);

	$filter_vid		= $mainframe->getUserStateFromRequest( "$option.filter_vid",'filter_vid',0,'string' );
	if ($filter_vid !="0") {
		$lists['verein']= JHTML::_('select.genericlist',$vereinlist,'zps','class="inputbox" size="1"','zps', 'name', $filter_vid );
		} else {
		$lists['verein']= JHTML::_('select.genericlist',$vereinlist,'zps','class="inputbox" size="1"','zps', 'name', $row->zps );
		}

	// Publishliste
	$lists['published']	= JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $row->published );
	// Saisonliste
	if($task =="edit"){ $sql = 'SELECT id as sid, name FROM #__clm_saison WHERE id='.$sid;} 
	else { $sql = 'SELECT id as sid, name FROM #__clm_saison WHERE archiv =0'; }
	$db->setQuery($sql);
	if (!$db->query()){$this->setRedirect( 'index.php?option='.$option.'&section='.$section );
		return JError::raiseWarning( 500, $db->getErrorMsg() );}
	if ($task !="edit") {
	$saisonlist[]	= JHTML::_('select.option',  '0', JText::_( 'USERS_SAISON' ), 'sid', 'name' );
	$saisonlist	= array_merge( $saisonlist, $db->loadObjectList() );
		} else { $saisonlist	= $db->loadObjectList(); }
	$lists['saison']= JHTML::_('select.genericlist',   $saisonlist, 'sid', 'class="inputbox" size="1"','sid', 'name', $row->sid );
	// Joomla Nutzer ohne CLM Account
	$saisonids	= $db->loadObjectList();
	$saisonidstring = "";
 	foreach ($saisonids as $saisonid) {
		$saisonidstring .= $saisonid->sid.",";
	}
	$sql = " SELECT u.* FROM #__users as u "
		." LEFT JOIN #__clm_user as a ON u.id = a.jid AND a.sid IN ('".$saisonidstring."')"
		//." LEFT JOIN #__clm_user as a ON u.id = a.jid "
		." WHERE a.name IS NULL";
	$db->setQuery($sql);
	if (!$db->query()){
		$this->setRedirect( 'index.php?option='.$option.'&section='.$section );
		return JError::raiseWarning( 500, $db->getErrorMsg() ); }
	$jid_list[]	= JHTML::_('select.option',  '0', JText::_( 'USERS_USER_AUSW' ), 'id', 'name' );
	$jid_list	= array_merge( $jid_list, $db->loadObjectList() );
	$lists['jid']	= JHTML::_('select.genericlist',   $jid_list, 'pid', 'class="inputbox" size="1"','id', 'name', $row->jid );

	// Funktionsliste
	$sql = 'SELECT usertype, name FROM #__clm_usertype '
			.' WHERE published = 1 AND usertype IN ('.$usertypestring.' ) '
			.' ORDER BY ordering ';
	$db->setQuery($sql);
	$usertypelist[]		= JHTML::_('select.option',  '0', JText::_( 'USERS_TYP' ), 'usertype', 'name' );
	$usertypelist		= array_merge( $usertypelist, $db->loadObjectList() );
	$lists['usertype']	= JHTML::_('select.genericlist',   $usertypelist, 'usertype', 'class="inputbox" size="1"','usertype', 'name', $row->usertype );

	require_once(JPATH_COMPONENT.DS.'views'.DS.'users.php');
	CLMViewUsers::user( $row, $lists, $option);
	}


function save() {
	$mainframe = JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$db 		= & JFactory::getDBO();
	$task 		= JRequest::getVar( 'task');
	$row 		= & JTable::getInstance( 'users', 'TableCLM' );
	$clm_id		= JRequest::getVar( 'id');
	$jid_clm	= JRequest::getInt( 'pid');

	if (!$row->bind(JRequest::get('post'))) {
		JError::raiseError(500, $row->getError() );
	}

	$name		= JRequest::getVar('name');
	$username	= JRequest::getVar('username');
	$email		= JRequest::getVar('email');
	$mglnr		= JRequest::getVar('mglnr');
 	$usertype	= JRequest::getVar('usertype');
	$published	= JRequest::getVar('published');

	// Vorbereitung Admin-Zugang setzen oder prüfen
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'classes'.DS.'CLMAccess.class.php');
	$clmAccess = new CLMAccess();

	// Zuordnen Zugangswert zu gewählten Usertype
	$query = "SELECT user_clm FROM #__clm_usertype WHERE usertype = '$row->usertype' ";
	$db->setQuery($query);
	$row->user_clm=$db->loadResult();
	
	////////////////
	// Neuer User //
	////////////////
	if (!$row->id) {
		// User wird nicht aus Joomla DB übernommen
 		if ($jid_clm == "0") {
			// prüfen ob Email schon vergeben wurde
			$query = "SELECT COUNT(email) as countmail FROM #__users WHERE email = '$email'";
			$db->setQuery($query);
			$count_mail=$db->loadObjectList();
			if ($count_mail[0]->countmail > 0) {
				JError::raiseWarning( 500, JText::_( 'USERS_MAIL') );
				$link = 'index.php?option='.$option.'&section='.$section;
				$mainframe->redirect( $link );
			}
			// prüfen ob Username schon vergeben wurde
			$query = "SELECT COUNT(username) as username FROM #__users WHERE username = '$username'";
			$db->setQuery($query);
			$count_uname=$db->loadObjectList();
			if ($count_uname[0]->username > 0) {
				JError::raiseWarning( 500, JText::_( 'USERS_NAME_IST') );
				$link = 'index.php?option='.$option.'&section='.$section;
				$mainframe->redirect( $link );
			}

			$aktion = "User angelegt";
			$where = "sid = " . (int) $row->sid;
			$row->ordering = $row->getNextOrder( $where );
			// Joomla User anlegen !!
			jimport('joomla.user.helper');
			$activation= md5(JUserHelper::genRandomPassword());

			// 6 = Manager ; 7 = Admin; 8 = Superadmin ; 2= registered
			//if ($funktion > 69){ $group = '6'; } else { $group = '2'; }
			$clmAccess->accessusertype = $usertype;
			$clmAccess->accesspoint = 'BE_general_general';
			if($clmAccess->accesstype() === true) { $group = '6'; } else { $group = '2'; }
			if ($published == 1) { $block = 0; } else { $block = 1; }

			$user_new		= new JUser();
			$data			= array();
			$data['name']		= $name;
			$data['username']	= $username;
			$data['email']		= $email;
			$groups			= array( $group => $group);
			$data['groups']		= $groups;
			$data['block']		= $block;

			if (!$user_new->bind($data)) {
		    		JError::raiseWarning('', JText::_($user_new->getError()));
		    		return false;
			}
			if (!$user_new->save()) {
		    		JError::raiseWarning('', JText::_($user_new->getError()));
		    		return false;
			}
			$row->jid = $user_new->id;
		} else {
			// User wird aus Joomla DB eingelesen
			$query = "SELECT * FROM #__users WHERE id = " . $jid_clm;
			$db->setQuery( $query );
			$j_data=$db->loadObjectList();
			$row->name	= $j_data[0]->name;
			$row->username	= $j_data[0]->username;
			$row->email	= $j_data[0]->email;
			$row->mglnr	= $mglnr;
 			$row->jid	= $jid_clm;
			$row->aktive	= "1";
	
			// Joomla User updaten
			if ($published == 1) { $block = 0; } else { $block = 1; }
			$jid = $row->jid;

			$user_edit	= new JUser($jid_clm);
			$user 		= &JFactory::getUser($jid_clm);
			$gids 		= $user->get('groups');
			$gid	= 0;
			foreach ($gids as $key => $value) {
				$ivalue = intval($value);
				if (($ivalue == 2) || ($ivalue == 6) || ($ivalue == 7) || ($ivalue == 8)) {
					if ($ivalue > $gid) {	// Reihenfolge der Values von oben beachten !
						$gid = $ivalue;
					}
				}
			}
			$data			= array();
			$data['name']		= $j_data[0]->name;
			$data['username']	= $j_data[0]->username;
			$data['email']		= $j_data[0]->email;	
			$gids['2']		= 2;	// Registered immer setzen
			//if ($funktion > 69) {		// Wenn clm-Funktion >69, dann setze Admin ggf. zusätzlich
			$clmAccess->accessusertype = $usertype;
			$clmAccess->accesspoint = 'BE_general_general';
			if($clmAccess->accesstype() === true) { 
				$gids['6']		= 6;
			} else {
				unset($gids['6']);	// Ansonsten entferne Admin (und nur Admin!)
			}
			$data['groups']		= $gids;
			$data['block']		= $block;
		
			if (!$user_edit->bind($data)) {
		    		JError::raiseWarning('', JText::_( $user_edit->getError()));
		    		return false;
			}
			if (!$user_edit->save()) {
		    		JError::raiseWarning('', JText::_( $user_edit->getError()));
		    		return false;
			}
		}
	} else {
		/////////////////////
		// User wird editiert
		/////////////////////
		$aktion = "User editiert";

		// Joomla User updaten
		if ($published == 1) { $block = 0; }
		else { $block = 1; }
		$jid = $row->jid;
 
		$user_edit	= new JUser($jid);
		$user 		= &JFactory::getUser($jid);
		$gids 		= $user->get('groups');
		$gid	= 0;
		foreach ($gids as $key => $value) {
			$ivalue = intval($value);
			if (($ivalue == 2) || ($ivalue == 6) || ($ivalue == 7) || ($ivalue == 8)) {
				if ($ivalue > $gid) {	// Reihenfolge der Values von oben beachten !
					$gid = $ivalue;
				}
			}
		}

		$data			= array();
		$data['name']		= $name;
		$data['username']	= $username;
		$data['email']		= $email;
		$gids['2']		= 2;	// Registered immer setzen
		
		//if ($funktion > 69) {				// Wenn clm-Funktion >69, dann setze Admin ggf. zusätzlich
		$clmAccess->accessusertype = $usertype;
		$clmAccess->accesspoint = 'BE_general_general';
		if ($clmAccess->accesstype()) {		// Wenn clm-usertype Admin-Zugang hat, dann setze Admin ggf. zusätzlich
			$gids['6']		= 6;
		} else {
			unset($gids['6']);	// Ansonsten entferne Admin (und nur Admin!)
		}
		$data['groups']		= $gids;
		$data['block']		= $block;

		if (!$user_edit->bind($data)) {
	    		JError::raiseWarning('', JText::_( $user_edit->getError()));
	    		return false;
		}
		if (!$user_edit->save()) {
	    		JError::raiseWarning('', JText::_( $user_edit->getError()));
	    		return false;
		}
	}

	// save the changes
	if (!$row->store()) {
		JError::raiseError(500, $row->getError() );
	}

	$row->checkin();

	switch ($task) {
		// 6 = Manager ; 7 = Admin; 8 = Superadmin ; 2= registered
		case 'apply':
		if ( $gid > 6 ) {
			JError::raiseNotice( 6000,  JText::_( 'USERS_CLM' ));
		}
		//if ( $funktion > 69 AND $gid == 2 ) {
		$clmAccess->accessusertype = $usertype;
		$clmAccess->accesspoint = 'BE_general_general';
		if ( $clmAccess->accesstype() AND $gid == 2 ) {
			JError::raiseNotice( 6000,  JText::_( 'USERS_GO_ADMIN' ));
		}
		//if ( $funktion < 70 AND $gid == 6 ) {
		if ( !$clmAccess->accesstype() AND $gid == 6 ) {
			JError::raiseNotice( 6000,  JText::_( 'USERS_NO_ADMIN' ));
		}
		$msg = JText::_( 'USERS_AENDERN');
		$link = 'index.php?option='.$option.'&section='.$section.'&task=edit&cid[]='. $row->id ;
		break;
		case 'save':
		default:
		if ( $gid > 6 ) {
			JError::raiseNotice( 6000, JText::_( 'USERS_CLM') );
		}
		//if ( $funktion > 69 AND $gid == 2 ) {
		$clmAccess->accessusertype = $usertype;
		$clmAccess->accesspoint = 'BE_general_general';
		if ( $clmAccess->accesstype() AND $gid == 2 ) {
			JError::raiseNotice( 6000, JText::_( 'USERS_GO_ADMIN' ));
		}
		//if ( $funktion < 70 AND $gid == 6 ) {
		if ( !$clmAccess->accesstype() AND $gid == 6 ) {
			JError::raiseNotice( 6000,  JText::_( 'USERS_NO_ADMIN' ));
		}
		$msg = JText::_( 'USERS_BENUTZER_GESPEI');
		$link = 'index.php?option='.$option.'&section='.$section;
		break;
	}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = $aktion;
	$clmLog->params = array('sid' => $row->sid, 'jid' => $row->jid);
	$clmLog->write();
	
	$mainframe->redirect( $link, $msg );
}


function cancel() {
	$mainframe = JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$id		= JRequest::getVar('id');	
	$row 		=& JTable::getInstance( 'users', 'TableCLM' );
	$row->checkin( $id);

	$msg = JText::_( 'USERS_AKTION');
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
	}


function remove() {
	$mainframe = JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		= & JFactory::getDBO();
	$cid 		= JRequest::getVar('cid', array(), '', 'array');
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$user 		= & JFactory::getUser();
	JArrayHelper::toInteger($cid);

	if (count($cid) < 1) {
		JError::raiseWarning(500, JText::_( 'USERS_SELECT', true ) );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'classes'.DS.'CLMAccess.class.php');
	$clmAccess = new CLMAccess();

	// Prüfen ob User Berechtigung zum Löschen hat
	$row =& JTable::getInstance( 'users', 'TableCLM' );
	$row->load( $cid[0] );
	$id	= $row->jid;
	$jid	= $user->get('id');
	$gid	= $user->get('gid');
 
	// User kann sich nicht selbst löschen
	$user_publish = new JUser($id);
	if ( $user_publish->get('id') == $jid ) {
		JError::raiseWarning( 500, JText::_( 'USERS_NO_LOESCH') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}
	// User 62 (1. Superadmin) kann von niemanden gelöscht werden
	//if ( $user_publish->get('id') == 62 ) {
	if ((JVersion::isCompatible("1.6.0") AND $user_publish->get('id') == 42 ) OR
		(JVersion::isCompatible("1.5.0") AND $user_publish->get('id') == 62 ) )   {
		JError::raiseWarning( 500, JText::_( 'USERS_USER_NO_LOESCH') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}
	// Es können keine Admin / Superadmin gelöscht werden von nicht-Superadmin-User
	if ( $user_publish->get('gid') > 23 AND $gid < 25 ) {
		JError::raiseWarning( 500, JText::_( 'USERS_NO_ADMIN_LOESCH') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}
	// User kann nur niedrigere CLM-Berechtigungen löschen
	//$sql = "SELECT user_clm, jid FROM #__clm_user WHERE jid =".$jid;
	//$db->setQuery($sql);
	//$clmuser = $db->loadObjectList();;

	//if ( $clmuser[0]->user_clm <= $row->user_clm AND CLM_usertype !== 'admin' ) {
	$clmAccess->accessusertype = $row->usertype;
	if ( !$clmAccess->comparison() ) {
		JError::raiseWarning( 500, JText::_( 'USERS_BENUTZER_LOESCH') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}
	// aktuelle Saison holen
	$query = 'SELECT id FROM #__clm_saison WHERE archiv=0 AND published=1 ORDER BY id DESC LIMIT 1';
	$db->setQuery( $query );
	$sid = $db->loadResult();

	// keine Saison aktuell !
	if ( !$sid ) {
	JError::raiseWarning( 500, JText::_( 'USERS_NO_SAISON') );
	$link = 'index.php?option='.$option.'&section='.$section;
	$mainframe->redirect( $link, $msg );
	}

	$user_edit = new JUser($id);
	$gid= $user_edit->get('gid');

	// Joomla Account auf unpublish
	if ($gid == 23) {
		$query	= "UPDATE #__users SET block = 1 WHERE id = " . $id ;
		$db->setQuery($query);
		$db->query();
	}
	// CLM User löschen
	$query = ' DELETE FROM #__clm_user WHERE jid = ' . $id . ' AND sid = ' .$row->sid;
	$db->setQuery( $query );
	if (!$db->query()) {
		echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
	}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = "User gelöscht";
	$clmLog->params = array('sid' => $row->sid, 'jid' => $row->jid, 'cids' => $cids);
	$clmLog->write();
	
	if ($gid == 23) {
		JError::raiseNotice( 6000,  JText::_( 'USERS_JOOMLA_ACCOUNT' ));
	}
	$msg = "CLM Account wurde gelöscht !";
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
	}


function publish()
	{
	$mainframe = JFactory::getApplication();

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

	// nichts ausgewählt
	if (empty( $cid ))
	{
	JError::raiseWarning( 500, 'No items selected' );
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'classes'.DS.'CLMAccess.class.php');
	$clmAccess = new CLMAccess();

	// Prüfen ob User Berechtigung zum (un-)publishen hat
	$row =& JTable::getInstance( 'users', 'TableCLM' );
	$row->load( $cid[0] );
	$id = $row->jid;
	$jid = $user->get('id');
	$gid = $user->get('gid');
 
	// User kann sich nicht selbst blocken
	$user_publish = new JUser($id);
	if ( $user_publish->get('id') == $user->get( 'id' ) AND $task !="publish")
	{
	JError::raiseWarning( 500, JText::_( 'USERS_NO_BLOCK') );
	$link = 'index.php?option='.$option.'&section='.$section;
	$mainframe->redirect( $link, $msg );
	}
	// User 62 (1. Superadmin) kann von niemanden geblockt werden
	if ( $user_publish->get('id') == 62 AND $task !="publish")
	{
	JError::raiseWarning( 500, JText::_( 'USERS_ZURUECKZIEHEN') );
	$link = 'index.php?option='.$option.'&section='.$section;
	$mainframe->redirect( $link, $msg );
	}
	// Es können keine Admin / Superadmin geblockt werden von nicht-Superadmin-User
	if ( $user_publish->get('gid') > 23 AND $gid < 25 )
	{
	JError::raiseWarning( 500, JText::_( 'USERS_NO_JOOMLA') );
	$link = 'index.php?option='.$option.'&section='.$section;
	$mainframe->redirect( $link, $msg );
	}
	// User kann nur niedrigere CLM-Berechtigungen blocken
	//$sql = "SELECT user_clm, jid FROM #__clm_user WHERE jid =".$jid;
	//$db->setQuery($sql);
	//$clmuser = $db->loadObjectList();;
	//if ( $clmuser[0]->user_clm <= $row->user_clm AND $gid != 25 ) 	{
	$clmAccess->accessusertype = $row->usertype;
	if ( !$clmAccess->comparison() ) {
		JError::raiseWarning( 500, JText::_( 'USERS_NO_ZURUECK') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}

	$cids = implode( ',', $cid );
	$query = ' UPDATE #__clm_user'
		.' SET published = '.(int) $publish
		.' WHERE id IN ( '. $cids .' )'
		.' AND jid <> '.CLM_ID
		.' AND user_clm <= '.CLM_user
		.' AND ( checked_out = 0 OR ( checked_out = '.(int) $user->get('id') .' ) )'
		;
	if ($task =='publish') { $block = 0; }
	else { $block = 1; }

	for ($x=0; $x <count($cid); $x++) {
		$row->load( $cid[$x] );
		$block_id = $row->jid;
	$user_block =& JUser::getInstance( $block_id );
	if ($user_block->gid < 24 ) {
		$user_block->set('block', $block);
		$user_block->save();
	}
	else { $err = 1 ;}
	}
	if ($err =="1") {
	JError::raiseNotice( 6000,  JText::_( 'USERS_GEWAEHLTER'));
	}

	$db->setQuery( $query );
	if (!$db->query()) { JError::raiseError(500, $db->getErrorMsg() );
	}
	if (count( $cid ) == 1) {
		$row =& JTable::getInstance( 'users', 'TableCLM' );
		$row->load( $cid[0] );
		$row->checkin( $cid[0] );
	}

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = "User ".$task;
	$clmLog->params = array('jid' => $cid[0], 'cids' => $cids);
	$clmLog->write();
	
	if ( $task == 'publish') { $msg = JText::_( 'USERS_VEROEFFENTLICH') ;}
	else { $msg = JText::_( 'USERS_ZURUECK') ;}
	if ( $row->aktive == 0 ) {
	JError::raiseNotice( 6000, JText::_( 'USERS_INAKTIVE') );
	}
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
	}
/**
* Moves the record up one position
*/
function orderdown(  ) {
	CLMControllerUsers::order( 1 );
}

/**
* Moves the record down one position
*/
function orderup(  ) {
	CLMControllerUsers::order( -1 );
}

/**
* Moves the order of a record
* @param integer The direction to reorder, +1 down, -1 up
*/
function order( $inc )
	{
	$mainframe = JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db		=& JFactory::getDBO();
	$cid		= JRequest::getVar('cid', array(0), '', 'array');
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	JArrayHelper::toInteger($cid, array(0));

	$limit 		= JRequest::getVar( 'limit', 0, '', 'int' );
	$limitstart 	= JRequest::getVar( 'limitstart', 0, '', 'int' );

	$row =& JTable::getInstance( 'users', 'TableCLM' );
	$row->load( $cid[0] );
	$row->move( $inc, 'sid = '.(int) $row->sid.' AND published != 0' );

	$msg 	= 'Liste umsortiert !'.$cid[0];
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
	}

/**
* Saves user reordering entry
*/
function saveOrder(  )
	{
	$mainframe = JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db		=& JFactory::getDBO();
	$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	JArrayHelper::toInteger($cid);

	$total		= count( $cid );
	$order		= JRequest::getVar( 'order', array(0), 'post', 'array' );
	JArrayHelper::toInteger($order, array(0));

	$row =& JTable::getInstance( 'users', 'TableCLM' );
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

function copy()
	{
	$mainframe = JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$cid		= JRequest::getVar( 'cid', null, 'post', 'array' );
	$db		= & JFactory::getDBO();
	$this->setRedirect( 'index.php?option='.$option.'&section='.$section );

	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'classes'.DS.'CLMAccess.class.php');
	$clmAccess = new CLMAccess();
	
	JArrayHelper::toInteger($cid);
	$n	= count( $cid );
	$cids 	= implode( ',', $cid );

	if ($n < 1) {
		JError::raiseWarning( 500, JText::_( 'USERS_KOPIE') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
	}
	// Prüfen ob User Berechtigung zum kopieren hat
	$query = " SELECT MAX(user_clm) as max FROM #__clm_user as a"
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid"
		." WHERE a.id IN ( $cids )"
		." AND s.published = 1 AND s.archiv = 0 "
		;
	$db->setQuery( $query );
	$err = $db->loadObjectList();

	//	if ( $usertype != 'admin' AND ($table->usertype == 'admin' OR $table->usertype == sl) AND $table->jid != $jid ) {
	//if ($err[0]->max > CLM_user AND CLM_usertype != 'admin') {
	$clmAccess->accesspoint = 'BE_user_copy';
	if($clmAccess->access() === false) {
		JError::raiseWarning( 500, JText::_( 'USERS_NO_KOPIE') );
		//JError::raiseNotice( 6000,  JText::_( 'USERS_IST_HOEHER') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
					}

	// id nächste Saison bestimmen
	$sql	=" SELECT id FROM #__clm_saison "
		." WHERE archiv = 0 AND published = 0"
		." ORDER BY id ASC LIMIT 1"
		;
	$db->setQuery($sql);
	$check	= $db->loadResult();

	// keine nächste Saison existent !
	if(!$check ) {
	JError::raiseWarning( 500, JText::_( 'USERS_NO_KOPIE') );
	JError::raiseNotice( 6000,  JText::_( 'USERS_NO_SAISON') );
	$mainframe->redirect( 'index.php?option='.$option.'&section='.$section );
		}

	// Jid's der aktuellen Saison zum Abgleich verfügbar machen
	$query = " SELECT a.id, a.jid FROM #__clm_user as a"
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid"
		." WHERE a.id IN ( $cids )"
		." AND s.published = 1 AND s.archiv = 0 "
		;
	$db->setQuery( $query );
	$jids = $db->loadObjectList();

	$cnt = 0;
	$row = & JTable::getInstance( 'users', 'TableCLM' );

	foreach ($jids as $jids) {
	// schon kopiert ?
	$query = " SELECT a.jid FROM #__clm_user as a"
		." WHERE a.jid = ".$jids->jid
		." AND a.sid = ".$check
		;
	$db->setQuery( $query );
	$jid_neu = $db->loadObjectList();
	
	if(!$jid_neu OR $jid_neu[0]->jid =="") {
	$cnt++;
		$row->load( ($jids->id));
			$row->id	= "0";
			$row->sid	= $check;
		if (!$row->store()) {	return JError::raiseWarning( $row->getError() );}
	}}

	if ($cnt == "0") {
	JError::raiseWarning( 500, JText::_( 'USERS_NO_KOPIE') );
	JError::raiseNotice( 6000,  JText::_( 'USERS_IST_KOPIE') );
	$mainframe->redirect( 'index.php?option='.$option.'&section='.$section );
		}

	if ($cnt >1) { $msg= $cnt.' Einträge kopiert !';}
		else {$msg='Eintrag kopiert !';}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = "User kopiert";
	$clmLog->params = array('sid' => $check, 'jid' => $cid[0], 'cids' => implode( ',', $cid ));
	$clmLog->write();
	
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
	}

function send()
	{
	$mainframe = JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$db		=& JFactory::getDBO();
	$cid 		= JRequest::getVar('cid', array(), '', 'array');
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$user	= &JFactory::getUser();
	JArrayHelper::toInteger($cid);
	$n = count($cid);

	// minimum 1 Empfänger
	if ($n < 1) {
		JError::raiseWarning(500, JText::_( 'USERS_AN_WEN', true ) );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section ); 
			}
	// Prüfen ob User Berechtigung zum Accountdaten schicken / erneuern hat
	$row =& JTable::getInstance( 'users', 'TableCLM' );
	//$row->load( $cid[0] );

	//$jid		=  $user->get('id');
	//$sql		= "SELECT usertype,user_clm FROM #__clm_user WHERE jid =".$jid;
	//$db->setQuery($sql);
	//$clmuser	= $db->loadObjectList();
	//$usertype	= $clmuser[0]->usertype;
	//$user_clm	= $clmuser[0]->user_clm;

	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'classes'.DS.'CLMAccess.class.php');
	$clmAccess = new CLMAccess();
	$clmAccess->accesspoint = 'BE_user_general';
	if ($clmAccess->access() === false) {
	//if ( $usertype != 'admin' AND ($table->usertype == 'admin' OR $table->usertype == 'sl') AND $table->jid != $jid ) {
		JError::raiseWarning( 500, JText::_( 'USERS_NO_SEND') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link, $msg );
					}

	$clmAccess->accesspoint = 'BE_user_copy';
	if ($clmAccess->access() === true) {
	//if ($user_clm == '100') {
	$cids = implode( ',', $cid );
	$query = "SELECT a.jid,a.name,a.email,a.username,a.aktive, b.name as funktion, u.activation"
	." FROM #__clm_user as a"
	." LEFT JOIN #__clm_usertype AS b ON b.user_clm = a.user_clm"
	." LEFT JOIN #__users AS u ON u.id = a.jid "
	. " WHERE a.id IN ( '". $cids ."' )";
	} else {
	$query = "SELECT a.jid,a.name,a.email,a.username,a.aktive, b.name as funktion, u.activation"
	." FROM #__clm_user as a"
	." LEFT JOIN #__clm_usertype AS b ON b.user_clm = a.user_clm"
	." LEFT JOIN #__users AS u ON u.id = a.jid "
	." WHERE a.id = ".$cid[0];
	$n=1;
		}
	$db->setQuery( $query );
	$rows = $db->loadObjectList();
	if ($db->getErrorNum()) {echo $db->stderr(); return false;}
	
	// Generiere neuen Aktivierungscode
	jimport('joomla.user.helper');

	// BCC Adresse aus Konfiguration holen
	// Konfigurationsparameter auslesen
	$config = &JComponentHelper::getParams( 'com_clm' );

	// Zur Abwärtskompatibilität mit CLM <= 1.0.3 werden alte Daten aus Language-Datei als Default eingelesen
	$from = $config->get('email_from', JText::_('USER_MAIL_FROM'));
	$fromname = $config->get('email_fromname', JText::_('USER_MAIL_FROM_NAME'));
	$bcc	= $config->get('email_bcc', $config->get('bcc'));
	
	$subject_neu = "[".$config->get('email_fromname', JText::_('USER_MAIL_FROM_NAME'))."]: ".JText::_('USER_MAIL_SUBJECT_NEWACCOUNT');
	
	$msg = JText::_( 'USERS_VERSCHICKT');

	
	for ($i=0; $i<$n; $i++){
		//////////////////////////////////////////
		// User NICHT aktiv  -> E-Mail schicken //
		//////////////////////////////////////////
		if ($rows[$i]->aktive == '0') {
			$row->load( $cid[$i] );
			$row->aktive = 1;
			$activation = md5(JUserHelper::genRandomPassword());
			$row->store();

			$recipient = $rows[$i]->email;
			$body = JText::_('USER_MAIL_1')." ".$rows[$i]->name."," 
				.JText::_('USER_MAIL_2')." ".$rows[$i]->funktion." ".JText::_('USER_MAIL_3')
				.JText::_('USER_MAIL_4')
				."\r\n\r\n ".JURI::root()."index.php?option=$option&view=reset&layout=complete&token=".$activation
				.JText::_('USER_MAIL_5')
				.JText::_('USER_MAIL_6')
				.JText::_('USER_MAIL_7')." ".$rows[$i]->username
				.JText::_('USER_MAIL_8')
				.JText::_('USER_MAIL_9')
				.JText::_('USER_MAIL_10')
				;
			// Email mit Accountdaten schicken
			JUtility::sendMail ($from, $fromname, $recipient, $subject_neu, $body, 0, $cc, $bcc);

		}
		////////////////////////////////////////////////
		// User ist AKTIV --> Mail mit neuen Passwort //
		////////////////////////////////////////////////
		elseif ($rows[$i]->aktive == '1') {
			$activation = md5(JUserHelper::genRandomPassword());
			//$jid = $rows[$i]->jid;

			$recipient = $rows[$i]->email;
			$subject_remind = "[".$config->get('email_fromname', JText::_('USER_PASSWORD_SUBJECT'))."]: ".JText::_('USER_PASSWORD_SUBJECT');
			$body = JText::_('USER_PASSWORD_MAIL_1')." ".$rows[$i]->name."," 
				.JText::_('USER_PASSWORD_MAIL_2')
				.JText::_('USER_PASSWORD_MAIL_3')
				."\r\n\r\n ".JURI::root()."index.php?option=$option&view=reset&layout=complete&token=".$activation
				.JText::_('USER_PASSWORD_MAIL_4')
				.JText::_('USER_PASSWORD_MAIL_5')
				.JText::_('USER_PASSWORD_MAIL_6')." ".$rows[$i]->username
				.JText::_('USER_PASSWORD_MAIL_7')
				.JText::_('USER_PASSWORD_MAIL_8')
				.JText::_('USER_PASSWORD_MAIL_9')
				.JText::_('USER_PASSWORD_MAIL_10')
				;

			// Erinnerungsmail schicken
			JUtility::sendMail ($from,$fromname,$recipient,$subject_remind,$body,0,$cc,$bcc);

			//$msg = JText::_( 'USERS_MIDESTENS'.$bcc);
			$msg = JText::_( 'USERS_MIDESTENS');
		}
		// set password = NULL and activiation code as md5 hash
		$jid = $rows[$i]->jid;
		$query	= "UPDATE #__users "
			." SET password = '' "
			." , activation = '$activation' "
			." WHERE id = $jid "
			;
		$db->setQuery($query);
		$db->query();
	}
		
	$link = 'index.php?option='.$option.'&section='.$section;

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = "Accountdaten geschickt";
	$clmLog->params = array('jid' => $cid[0], 'cids' => $cids);
	$clmLog->write();
	
	$mainframe->redirect( $link, $msg );
	}

function copy_saison()
	{
	$mainframe = JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	$db		= & JFactory::getDBO();
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');

	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'classes'.DS.'CLMAccess.class.php');
	$clmAccess = new CLMAccess();

	//if (CLM_usertype !="admin" ) {
	$clmAccess->accesspoint = 'BE_user_copy';
	if($clmAccess->access() === false) {
		JError::raiseWarning(500, JText::_( 'USERS_ADMIN', true ) );
		$mainframe->redirect( 'index.php?option='.$option.'&section='.$section );
			}

	// id Vorsaison bestimmen
	$sql	=" SELECT id FROM #__clm_saison "
		." WHERE archiv = 1 "
		." ORDER BY id DESC LIMIT 1"
		;
	$db->setQuery($sql);
	$check	= $db->loadResult();

	// keine Vorsaison existent !
	if(!$check ) {
	JError::raiseWarning(500, JText::_( 'USERS_NO_VORSAISON') );
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
	JError::raiseWarning(500, JText::_( 'USERS_NO_AKTUELLE_SAISON') );
	$mainframe->redirect( 'index.php?option='.$option.'&section='.$section );
		}

	// Anzahl User bestimmen
	$sql	= " SELECT COUNT(id) FROM #__clm_user WHERE sid = ".$check;
	$db->setQuery($sql);
	$count	= $db->loadResult();

	// keine User gefunden
	if(!$count) {
	JError::raiseWarning(500, JText::_( 'USERS_NO') );
	$mainframe->redirect( 'index.php?option='.$option.'&section='.$section );
		}

	// schon vorhandenen Benutzer in aktueller Saison bestimmen und in Array
	$sql	=" SELECT jid FROM #__clm_user "
		." WHERE sid =".$sid
		." ORDER BY jid ASC "
		;
	$db->setQuery($sql);
	$akt_user	= $db->loadObjectList();

	$arr_user = array();
	foreach ($akt_user as $jid_user) {
		$arr_user[] = $jid_user->jid;
		}
	$users = implode( ',', $arr_user );

	if(!$users) { $users = 0; }
	// Alle User aus Vorsaison ohne Account in der aktuellen Saison laden
	$sql	=" SELECT id FROM #__clm_user "
		." WHERE sid = ".$check
		.' AND jid NOT IN ('.$users.') '
		." ORDER BY id ASC "
		;
	$db->setQuery($sql);
	$spieler	= $db->loadObjectList();

	// keine User zu kopieren
	if(count($spieler) == "0") {
	JError::raiseWarning(500, JText::_( 'USERS_ALLE_IST') );
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
		}

	// User laden und mit neuer Saison speichern
	$row =& JTable::getInstance( 'users', 'TableCLM' );

	for($x=0; $x < count($spieler); $x++) {
		$row->load( ($spieler[$x]->id));
			$row->id	= "0";
			$row->sid	= $sid;
		if (!$row->store()) {	return JError::raiseWarning( $row->getError() );}
	}

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = "User Vorsaison kopiert";
	$clmLog->params = array('jid' => $jid, 'cids' => $users);
	$clmLog->write();
	
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
	}
	
	//UserAccessGroups
	function showaccessgroups() {	
		$cid		= JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		
		if (!empty( $cid )) {
			$this->adminLink->more = array("filter_accessgroup" => $cid[0]);
		}
		
		//$this->adminLink->view = "accessgroupmain";
		//$this->adminLink->makeURL();
		//$this->setRedirect( $this->adminLink->url );
		//$this->setRedirect( 'index.php?option='.$option.'&section='.$section );
		$this->setRedirect( 'index.php?option=com_clm&view=accessgroupsmain' );
		//echo "<br>LINK: ".$this->adminLink->url;
		//die('link');

	}

}
 