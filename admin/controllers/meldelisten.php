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

class CLMControllerMeldelisten extends JControllerLegacy
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
	$filter_lid		= $mainframe->getUserStateFromRequest( "$option.filter_lid",'filter_lid',0,'int' );
	$filter_vid		= $mainframe->getUserStateFromRequest( "$option.filter_vid",'filter_vid',0,'int' );
	$filter_catid		= $mainframe->getUserStateFromRequest( "$option.filter_catid",'filter_catid',0,'int' );
	$search			= $mainframe->getUserStateFromRequest( "$option.search",'search','','string' );
	$search			= JString::strtolower( $search );
	$limit			= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
	$limitstart		= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

	$where = array();
	$where[]=' c.archiv = 0';
	if ( $filter_catid ) {	$where[] = 'a.published = '.(int) $filter_catid; }
	if ( $filter_sid ) {	$where[] = 'a.sid = '.(int) $filter_sid; }
	if ( $filter_lid ) {	$where[] = 'a.liga = '.(int) $filter_lid; }
	if ( $filter_vid ) {	$where[] = "e.id = '$filter_vid'"; }
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
		$orderby 	= ' ORDER BY id';
	} else {
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir .',  a.id';
	}

	// get the total number of records
	$query = ' SELECT COUNT(*) '
		.' FROM #__clm_meldeliste_mannschaft AS a'
		.' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
		. $where
		;
	$db->setQuery( $query );
	$total = $db->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );

	// get the subset (based on limits) of required records
	$query = 'SELECT a.*, c.name AS saison, b.Vereinname as verein, u.name AS editor, d.name AS liga_name'
	. ' FROM #__clm_mannschaften AS a'
	. ' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
	. ' LEFT JOIN #__clm_liga AS d ON a.liga = d.id'
	. ' LEFT JOIN #__users AS u ON u.id = a.checked_out'
	. ' LEFT JOIN #__clm_dwz_vereine AS b ON a.zps = b.ZPS AND a.sid = b.sid'
	. ' LEFT JOIN #__clm_vereine AS e ON e.zps = a.zps'
	. $where
	. $orderby	;
	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );

	$rows = $db->loadObjectList();
	if ($db->getErrorNum()) {
		echo $db->stderr();
		return false;
	}

	// state filter
	$lists['state']	= JHtml::_('grid.state',  $filter_state );

	// Saisonfilter
	$sql = 'SELECT id, name FROM #__clm_saison WHERE archiv =0';
	$db->setQuery($sql);
	$saisonlist[]	= JHtml::_('select.option',  '0', JText::_( 'MELDELISTEN_SAISON' ), 'id', 'name' );
	$saisonlist         = array_merge( $saisonlist, $db->loadObjectList() );
	$lists['sid']      = JHtml::_('select.genericlist', $saisonlist, 'filter_sid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','id', 'name', intval( $filter_sid ) );
	// Ligafilter
	$sql = 'SELECT id AS cid, name FROM #__clm_liga'
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid"
		." WHERE s.archiv = 0 ";
	$db->setQuery($sql);

	$ligalist[]	= JHtml::_('select.option',  '0', JText::_( 'MELDELISTEN_LIGA' ), 'cid', 'name' );
	$ligalist	= array_merge( $ligalist, $db->loadObjectList() );
	$lists['lid']	= JHtml::_('select.genericlist', $ligalist, 'filter_lid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','cid', 'name', intval( $filter_lid ) );
	// Vereinefilter
	$sql = 'SELECT id, name FROM #__clm_vereine'
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid"
		." WHERE s.archiv = 0 ";
	$db->setQuery($sql);

	$vlist[]	= JHtml::_('select.option',  '0', JText::_( 'MELDELISTEN_VEREIN' ), 'id', 'name' );
	$vlist		= array_merge( $vlist, $db->loadObjectList() );
	$lists['vid']	= JHtml::_('select.genericlist', $vlist, 'filter_vid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','id', 'name', $filter_vid );

	// Ordering
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;

	// search filter
	$lists['search']= $search;
	require_once(JPATH_COMPONENT.DS.'views'.DS.'meldelisten.php');
	CLMViewMeldelisten::meldelisten( $rows, $lists, $pageNav, $option );
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
	$liga 		= JRequest::getVar( 'liga');
	JArrayHelper::toInteger($cid, array(0));
	$row =JTable::getInstance( 'mannschaften', 'TableCLM' );
	// load the row from the db table
	$row->load( $cid[0] );

	// Prüfen ob User Berechtigung zum editieren hat
	$sql = " SELECT sl FROM #__clm_liga "
		." WHERE id =".$row->liga
		;
	$db->setQuery($sql);
	$lid = $db->loadObjectList();
	$clmAccess = clm_core::$access;
	if ($clmAccess->access('BE_team_registration_list') === false) {
		$section = 'info';
		JError::raiseWarning( 500, JText::_( 'TEAM_NO_ACCESS' ) );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
	}
	if ( isset($lid[0]) && $lid[0]->sl !== clm_core::$access->getJid() AND $clmAccess->access('BE_team_registration_list') !== true AND $task == 'edit') {
		JError::raiseWarning( 500, JText::_( 'MELDELISTEN_STAFFEL' ) );
		$link = 'index.php?option='.$option.'&section=mannschaften';
		$mainframe->redirect( $link);
					}
	// MaxDaten für DropDown Menue
	$maxsql = "SELECT COUNT(*) as max FROM #__clm_dwz_spieler"
		//." WHERE ( ZPS ='".$row->zps."' OR ZPS ='".$row->sg_zps."')"
		." WHERE ( ZPS ='".$row->zps."' OR FIND_IN_SET(ZPS,'".$row->sg_zps."') != 0 )"
		." AND sid =".$row->sid
		;
	$db->setQuery( $maxsql);
	$max=$db->loadObjectList();


	// Daten Stamm, Ersatz für DropDown Menue
	$ligasql = "SELECT stamm, ersatz FROM #__clm_liga"
		." WHERE id =".$row->liga
		." AND sid =".$row->sid
		;
	$db->setQuery( $ligasql);
	$liga=$db->loadObjectList();

	// Daten für DropDown Menue
	// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	$val=$config->meldeliste;
	$countryversion=$config->countryversion;
	
	if ($val == 1) { $order = "Spielername ASC";}
		else { $order = "DWZ DESC"; }
	if ($countryversion == "de") 
		$sql = "SELECT mgl_nr as id, ";
	else
		$sql = "SELECT PKZ as id, ";
	$sql .= "Spielername as name, DWZ as dwz, DWZ_Index as dwz_I0, ZPS as zps FROM #__clm_dwz_spieler"
		." WHERE ( ZPS ='".$row->zps."' OR FIND_IN_SET(ZPS,'".$row->sg_zps."') != 0 )"
		." AND sid =".$row->sid
		." ORDER BY ".$order
		;
	$db->setQuery( $sql );
	$row_spl=$db->loadObjectList();

	// Daten für Abgabe
	$sql = "SELECT u.name, a.datum,v.name as editor, a.edit_datum FROM #__clm_mannschaften as a"
		." LEFT JOIN #__clm_user as u ON  u.jid = a.liste AND u.sid = a.sid"
		." LEFT JOIN #__clm_user as v ON  v.jid = a.edit_liste AND v.sid = a.sid"
		." WHERE a.zps = '".$row->zps."'"
		." AND a.man_nr = ".$row->man_nr
		." AND a.sid =".$row->sid
		." AND u.name <> '' "
	;
	$db->setQuery( $sql );
	$abgabe=$db->loadObjectList();

	//Stammspieler
	$selsql = "SELECT mgl_nr,snr,zps,PKZ, gesperrt, attr FROM #__clm_meldeliste_spieler"
		//." WHERE ( zps = '".$row->zps."' OR zps='".$row->sg_zps."')"
		." WHERE ( ZPS ='".$row->zps."' OR FIND_IN_SET(ZPS,'".$row->sg_zps."') != 0 )"
		." AND mnr = ".$row->man_nr
		." AND lid = ".$row->liga
		." AND sid = ".$row->sid
		." ORDER BY snr ASC"
		;
	$db 		=JFactory::getDBO();
	$db->setQuery( $selsql );
	$row_sel=$db->loadObjectList();

	require_once(JPATH_COMPONENT.DS.'views'.DS.'meldelisten.php');
	CLMViewMeldelisten::meldeliste( $row, $row_spl, $row_sel, $max, $liga, $abgabe, $option);
	}

	////////////////////////////////////////////////////////
	// Save / Apply Funktion ist im MANNSCHAFTSCONTROLLER //
	////////////////////////////////////////////////////////

function cancel()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );
	
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$id		= JRequest::getVar('id');	
	$row 		=JTable::getInstance( 'meldelisten', 'TableCLM' );


	$msg = JText::_( 'MELDELISTEN_ML_ABGE').$id;
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg ,"message");
	}
}
