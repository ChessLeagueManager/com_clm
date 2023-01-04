<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2022 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMControllerRanglisten extends JControllerLegacy
{
	/**
	 * Constructor
	 */
function __construct( $config = array() )
	{
		parent::__construct( $config );
		// Register Extra tasks
		$this->registerTask( 'add','edit');
		$this->registerTask( 'apply','save' );
		$this->registerTask( 'unpublish','publish' );
		$this->registerTask( 'update_clm','update_clm');
	}

function display($cachable = false, $urlparams = array())
	{
	$mainframe	= JFactory::getApplication();
	$option 	= clm_core::$load->request_string('option');
	$section	= clm_core::$load->request_string('section');
	$db		= JFactory::getDBO();

	$filter_gid		= $mainframe->getUserStateFromRequest( "$option.filter_gid",'filter_gid',0,'int' );
	$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",'filter_order','a.id',	'cmd' );
	$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir','','word' );
	$filter_state		= $mainframe->getUserStateFromRequest( "$option.filter_state",'filter_state','','word' );
	$filter_sid		= $mainframe->getUserStateFromRequest( "$option.filter_sid",'filter_sid',0,'int' );
	$filter_vid		= $mainframe->getUserStateFromRequest( "$option.filter_vid",'filter_vid',0,'var' );
	$filter_catid		= $mainframe->getUserStateFromRequest( "$option.filter_catid",'filter_catid',0,'int' );
	$search			= $mainframe->getUserStateFromRequest( "$option.search",'search','','string' );
	$search			= strtolower( $search );
	$limit			= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
	$limitstart		= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

	$where = array();
	$where[]=' c.archiv = 0';
	$zps	= clm_core::$load->request_string( 'zps' );
	if ($zps > 0) {
		$filter_vid = $zps;
			}
	if ( $filter_catid ) {	$where[] = 'a.published = '.(int) $filter_catid; }
	if ( $filter_sid ) {	$where[] = 'a.sid = '.(int) $filter_sid; }
	if ( $filter_vid ) {	$where[] = "a.zps = '$filter_vid'"; }


	if ( $filter_state ) {
		if ( $filter_state == 'P' ) {
			$where[] = 'a.published = 1';
		} else if ($filter_state == 'U' ) {
			$where[] = 'a.published = 0';
		}
	}

	$where2 = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

	if ($search) {	$where[] = 'LOWER(v.Vereinname) LIKE "'.$db->escape('%'.$search.'%').'"';	}
	$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

	// get the total number of records
	$query = ' SELECT COUNT(*) '
		.' FROM #__clm_rangliste_id AS a'
		.' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
		. $where2
		;
	$db->setQuery( $query );
	$total = $db->loadResult();


	if ($filter_order == 'a.id'){
		$orderby 	= ' ORDER BY sid '.$filter_order_Dir.', n.id '.$filter_order_Dir;
	} else {
	if ($filter_order =='a.Gruppe' OR  $filter_order == 'a.Meldeschluss' OR $filter_order == 'a.rang' OR  $filter_order == 'a.user' OR  $filter_order == 'a.saison' OR $filter_order == 'a.ordering' OR $filter_order == 'a.published' ) { 
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', a.id';
			}
		else { $filter_order = 'a.id'; $orderby 	= '';}
	}
	

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit );

	// get the subset (based on limits) of required records
	$query = 'SELECT a.*, c.name AS saison, v.Vereinname as vname, n.Gruppe as gname '
	. ' FROM #__clm_rangliste_id AS a'
	. ' LEFT JOIN #__clm_saison AS c ON c.id = a.sid'
	. ' LEFT JOIN #__clm_dwz_vereine AS v ON v.ZPS = a.zps AND v.sid = a.sid'
	. ' LEFT JOIN #__clm_rangliste_name AS n ON n.sid = a.sid AND n.id = a.gid'
	//. ' LEFT JOIN #__clm_liga AS d ON a.liga = d.id'
	. ' LEFT JOIN #__users AS u ON u.id = a.checked_out'
	. $where
	. $orderby;

	try {
		$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
		$rows = $db->loadObjectList();
	}
	catch (Exception $e) {
		$mainframe->enqueueMessage($db->stderr(), 'error');
	}

	// Filter
	// Saisonfilter
	$sql = 'SELECT id, name FROM #__clm_saison WHERE archiv =0';
	$db->setQuery($sql);
	$saisonlist[]	= JHtml::_('select.option',  '0', JText::_( 'RANGLISTE_SAISON_WAE' ), 'id', 'name' );
	$saisonlist         = array_merge( $saisonlist, $db->loadObjectList() );
	$lists['sid']      = JHtml::_('select.genericlist', $saisonlist, 'filter_sid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','id', 'name', intval( $filter_sid ) );

	// Vereinefilter laden
	$vereinefilter = CLMFilterVerein::vereine_filter(0);
	$lists['vid']	= JHtml::_('select.genericlist', $vereinefilter, 'filter_vid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','zps', 'name', $filter_vid );

	// Suchefilter
	$lists['search']= $search;
	// Ordering
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;

	require_once(JPATH_COMPONENT.DS.'views'.DS.'ranglisten.php');
	CLMViewRanglisten::Ranglisten( $rows, $lists, $pageNav, $option );
	}

function edit()
	{
	$mainframe	= JFactory::getApplication();
	$option 	= clm_core::$load->request_string('option');
	$section	= clm_core::$load->request_string('section');

	$filter_vid	= $mainframe->getUserStateFromRequest( "$option.filter_vid",'filter_vid',0,'var' );
	$filter_sid	= $mainframe->getUserStateFromRequest( "$option.filter_sid",'filter_sid',0,'int' );
	$filter_gid	= $mainframe->getUserStateFromRequest( "$option.filter_gid",'filter_gid',0,'int' );

	$db 		=JFactory::getDBO();
	$user 		=JFactory::getUser();
	$task 		= clm_core::$load->request_string( 'task');
	$cid 		= clm_core::$load->request_array_int('cid');
	if (is_null($cid)) $cid[0] = clm_core::$load->request_int('id');
										 
	$row =JTable::getInstance( 'ranglisten', 'TableCLM' );
	$vname="";
	$gname="";
	$sname="";

	if ($task == 'edit') {
	// illegaler Einbruchversuch über URL !
	// evtl. mitschneiden !?!
	$saison		=JTable::getInstance( 'saisons', 'TableCLM' );
	$saison->load( $row->sid );
	if ($saison->archiv == "1") {  // AND clm_core::$access->getType() !== 'admin') {
		$mainframe->enqueueMessage( JText::_( 'RANGLISTE_ARCHIV' ), 'warning' );		
		$mainframe->redirect( 'index.php?option='. $option.'&section=vereine' );
	}
	if ($cid[0]== "" AND $task =='edit') {
		$mainframe->enqueueMessage( JText::_( 'RANGLISTE_FALSCH' ), 'warning' );		
		$mainframe->redirect( 'index.php?option='. $option.'&section=vereine' );
	}
	// load the row from the db table
	$row->load( $cid[0] );

	$sql = " SELECT Vereinname as vname FROM #__clm_dwz_vereine "
		." WHERE ZPS ='".$row->zps."'"
		." AND sid =".$row->sid
		;
	$db->setQuery($sql);
	$vid 	= $db->loadObjectList();
	$vname	= $vid[0]->vname;

	$sql = " SELECT Gruppe as gname FROM #__clm_rangliste_name "
		." WHERE id =".$row->gid
		." AND sid =".$row->sid
		;
	$db->setQuery($sql);
	$gruppe	= $db->loadObjectList();
	$gname	= $gruppe[0]->gname;

	$sql = " SELECT name as sname FROM #__clm_saison "
		." WHERE id =".$row->sid
		;
	$db->setQuery($sql);
	$saison	= $db->loadObjectList();
	$sname	= $saison[0]->sname;
		}
	// Prüfen ob die gewählte Liste bereits existiert
	if($filter_gid AND $filter_vid AND $filter_sid ) {
	$sql = " SELECT id FROM #__clm_rangliste_id "
		." WHERE gid =".intval( $filter_gid )
		." AND sid = ".intval( $filter_sid )
		." AND zps = '$filter_vid'"
		;
	$db->setQuery($sql);
	$gid_check = $db->loadObjectList();
		if(count($gid_check) == 0) { $exist = 0; }
		else { $exist = $gid_check[0]->id; }
	}
	else { $exist = 0; }
	// Prüfen ob Gruppe existiert
	if($filter_gid AND $filter_sid) {
	$sql = " SELECT id FROM #__clm_rangliste_name "
		." WHERE sid = ".intval( $filter_sid )
		;
	$db->setQuery($sql);
	$gid_id		= $db->loadObjectList();
	$gid_exist	= $gid_id[0]->id;
	}
	else { $gid_exist = 0; }
	// Rangliste in Abhängigkeit der Auswahl von vid,lid,sid ausgeben
	if ($task == 'edit') {
	$sql = " ALTER TABLE #__clm_rangliste_spieler order by sid desc,gruppe asc,zps asc,man_nr asc,Rang asc ";
	//$db->setQuery($sql);
	clm_core::$db->query($sql);

	$sql = " SELECT Meldeschluss, geschlecht, alter_grenze, `alter` "
		." FROM #__clm_rangliste_name"
		." WHERE id =".$row->gid
		." AND sid = ".$row->sid
		;
	$sql_zps	= $row->zps;
	$sql_gid	= $row->gid;
	$sql_sid	= $row->sid;
		}
	else {
	$sql = " SELECT Meldeschluss, geschlecht, alter_grenze, `alter` "
		." FROM #__clm_rangliste_name"
		." WHERE id =".intval( $filter_gid )
		." AND sid = ".intval( $filter_sid )
		;
	$sql_zps	= $filter_vid;
	$sql_gid	= intval( $filter_gid );
	$sql_sid	= intval( $filter_sid );
		}
	$db->setQuery($sql);
	$gid	= $db->loadObjectList();

	$ges ="";
	$geb ="";
	if($gid) {
	$melde = explode ("-",$gid[0]->Meldeschluss);
	$jahr = $melde[0];

	if ($gid[0]->alter_grenze == "1") {
		$geb = " AND a.Geburtsjahr < ".($jahr - $gid[0]->alter);
		}
	if ($gid[0]->alter_grenze == "2") {
		$geb = " AND a.Geburtsjahr > ".($jahr - ( $gid[0]->alter + 1));
		}
	if ($gid[0]->geschlecht == 1) {
		$ges = " AND a.Geschlecht = 'W' ";
		}
	if ($gid[0]->geschlecht == 2) {
		$ges = " AND a.Geschlecht = 'M' ";
		}
	}
	if ($task == 'edit') {
	$sql = " SELECT r.Rang, r.man_nr, a.sid,a.ZPS,a.Mgl_Nr,a.PKZ, a.DWZ,"
		." a.DWZ_Index,a.Geburtsjahr,a.Spielername"
		." FROM #__clm_dwz_spieler as a"
		." LEFT JOIN #__clm_rangliste_id as i ON i.sid = a.sid AND i.zps = a.ZPS "
		." LEFT JOIN #__clm_rangliste_spieler as r ON r.sid = a.sid AND r.ZPS = a.ZPS AND r.Mgl_Nr = a.Mgl_Nr AND r.Gruppe = i.gid "
		." WHERE a.ZPS = '$sql_zps'"
		." AND i.id = ".$cid[0]
		.$geb.$ges
		." ORDER BY r.man_nr,r.Rang ASC, a.DWZ DESC, a.DWZ_Index ASC, a.Spielername ASC "
		;
		}
	else {
	$sql = " SELECT a.sid,a.ZPS,a.Mgl_Nr,a.PKZ,a.DWZ,a.DWZ_Index,a.Geburtsjahr,a.Spielername"
		." FROM #__clm_dwz_spieler as a"
		." WHERE a.ZPS = '$sql_zps'"
		." AND sid =".$sql_sid
		.$geb.$ges
		." ORDER BY a.DWZ DESC, a.DWZ_Index ASC, a.Spielername ASC "
		;
		}
	$db->setQuery($sql);
	$spieler = $db->loadObjectList();

	// Anzahl Einträge zählen
	$sql = " SELECT COUNT(ZPS) as ZPS FROM #__clm_rangliste_spieler "
		." WHERE Gruppe =".$sql_gid
		." AND sid = ".$sql_sid
		." AND zps = '$sql_zps'"
		;
	$db->setQuery($sql);
	$count_id	= $db->loadObjectList();
	$count		= $count_id[0]->ZPS;

	if (isset($row->liga)) {
		$sql = " SELECT sl FROM #__clm_liga "
			." WHERE id =".$row->liga
			;
		$db->setQuery($sql);
		$lid = $db->loadObjectList();
	} else { $lid = 0; }
	$clmAccess = clm_core::$access;
	if ($clmAccess->access('BE_club_edit_ranking') === false AND $task == 'edit') {
		$mainframe->enqueueMessage( JText::_( 'RANGLISTE_STAFFEL' ), 'warning' );		
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
					}
	if ($task == 'edit') {
	// do stuff for existing records
		$row->checkout( $user->get('id') );
	} else {
	// do stuff for new records
		$row->published 	= 0;
	}

	$lists['published']	= JHtml::_('select.booleanlist',  'published', 'class="inputbox"', $row->published );

	// Saisonliste //
	$sql = "SELECT id, name FROM #__clm_saison WHERE archiv =0";
	$db->setQuery($sql);
	$saisonlist[]	= JHtml::_('select.option',  '0', JText::_( 'RANGLISTE_SAISON_WAE' ), 'id', 'name' );
	$saisonlist         = array_merge( $saisonlist, $db->loadObjectList() );
	$lists['sid']      = JHtml::_('select.genericlist', $saisonlist, 'filter_sid', 'class="inputbox" size="1" onchange="javascript:edit();"','id', 'name', intval( $filter_sid ) );

	if ($filter_sid == 0) $filter_sid = clm_core::$access->getSeason();
	// Gruppenliste //
	$sql = "SELECT id as gid, Gruppe FROM #__clm_rangliste_name"
		." WHERE sid =".intval( $filter_sid )
		." AND published = 1"
		;
	$db->setQuery($sql);
	if (!clm_core::$db->query($sql)){
		$mainframe->enqueueMessage( $db->getErrorMsg(), 'warning' );		
		$mainframe->redirect( 'index.php?option='.$option.'&section='.$section );
	}
	$gruppenlist[]	= JHtml::_('select.option',  '0', JText::_( 'RANGLISTE_GRUPPE_AUS' ), 'gid', 'Gruppe' );
	$gruppenlist	= array_merge( $gruppenlist, $db->loadObjectList() );
	$lists['gruppe']= JHtml::_('select.genericlist',   $gruppenlist, 'filter_gid', 'class="inputbox" size="1" onchange="javascript:edit();"','gid', 'Gruppe', intval( $filter_gid ) );

	// Vereinliste
	// Vereinefilter laden
	$vereinlist	= CLMFilterVerein::vereine_filter(0);
	$lists['vid']	= JHtml::_('select.genericlist', $vereinlist, 'filter_vid', 'class="inputbox" size="1" onchange="javascript:edit();"','zps', 'name', $filter_vid );

	require_once(JPATH_COMPONENT.DS.'views'.DS.'ranglisten.php');
	$jid = 0;
	CLMViewRanglisten::Rangliste( $spieler,$row,$lists,$option,$jid,$vname,$gname,$sname,$cid,$exist,$count,$gid_exist);
	}

function save()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	defined('clm') or die( 'Invalid Token' );

	$option 	= clm_core::$load->request_string('option');
	$section	= clm_core::$load->request_string('section');

	$db		= JFactory::getDBO();
	$task		= clm_core::$load->request_string( 'task');
	$pre_task	= clm_core::$load->request_string( 'pre_task');
	$row		= JTable::getInstance( 'ranglisten', 'TableCLM' );
	$msg		= clm_core::$load->request_int( 'id');
	$exist		= clm_core::$load->request_string( 'exist');

	if ( $exist != "0" AND $pre_task !="edit") {
		$mainframe->enqueueMessage( JText::_( 'RANGLISTE_LISTE_IST' ), 'warning' );		
		$mainframe->enqueueMessage( JText::_( 'RANGLISTE_AENDERN' ), 'notice' );		
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
	}

	$post = $_POST; 
	if (!$row->bind($post)) {
		$mainframe->enqueueMessage( $row->getError(), 'error' );		
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
	}
	// pre-save checks
	if (!$row->check()) {
		$mainframe->enqueueMessage( $row->getError(), 'error' );		
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
	}
	if ($pre_task == "add" ) {
		$filter_vid	= $mainframe->getUserStateFromRequest( "$option.filter_vid",'filter_vid',0,'var' );
		$filter_sid	= $mainframe->getUserStateFromRequest( "$option.filter_sid",'filter_sid',0,'int' );
		$filter_gid	= $mainframe->getUserStateFromRequest( "$option.filter_gid",'filter_gid',0,'int' );

		$zps = $filter_vid ;
		$sid = intval( $filter_sid );
		$gid = intval( $filter_gid );

		$row->zps = $zps;
		$row->sid = $sid;
		$row->gid = $gid;

		$aktion = JText::_( 'RANGLISTE_LOG_ADDED');
	}
	else {
		$zps = $row->zps;
		$sid = $row->sid;
		$gid = $row->gid;

		$aktion = JText::_( 'RANGLISTE_LOG_EDIT');
	}

	if (!$row->id) {
		$where = "sid = " . (int) $row->sid;
		$row->ordering = $row->getNextOrder( $where );
	}
	// save the changes
	if (!$row->store()) {
		$mainframe->enqueueMessage( $row->getError(), 'error' );		
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
	}
	

	// Rangnummern in #__clm_rangliste_spieler abspeichern
	$count		= clm_core::$load->request_int( 'count');

	$query	=" DELETE FROM #__clm_rangliste_spieler "
		." WHERE Gruppe = ".$gid
		." AND ZPS = '$zps'"
		." AND sid =".$sid
		;
	//$db->setQuery($query);
	clm_core::$db->query($query);

	//vor Löschen der Meldelisten Start-DWZ, u.a. sichern
	$query	=" SELECT * FROM #__clm_meldeliste_spieler "
		." WHERE status = ".$gid
		." AND ZPS = '$zps'"
		." AND sid =".$sid
		;
	$db->setQuery($query);
	$dwz_meldeliste	= $db->loadObjectList();
	$old_ml = array();
	foreach ($dwz_meldeliste as $dwz_ml){
		$old_ml[$dwz_ml->lid.' '.$dwz_ml->mnr.' '.$dwz_ml->zps.' '.$dwz_ml->mgl_nr] = $dwz_ml;
	}

	//Löschen der Meldelisten
	$query	=" DELETE FROM #__clm_meldeliste_spieler "
		." WHERE status = ".$gid
		." AND ZPS = '$zps'"
		." AND sid =".$sid
		;
	//$db->setQuery($query);
	clm_core::$db->query($query);

	$query	=" SELECT a.liga, a.man_nr FROM #__clm_mannschaften as a"
		." LEFT JOIN #__clm_liga as l ON l.id = a.liga and l.sid = a.sid"
		." WHERE a.zps = '$zps'"
		." AND a.sid =".$sid
		." AND l.rang = ".$gid
		." GROUP BY a.man_nr "
		." ORDER BY a.man_nr ASC "
		;
	$db->setQuery($query);
	$lid_rang	= $db->loadObjectList();

	$mgl	= array();
	$pkz	= array();
	$mnr	= array();
	$rang	= array();

	// Rangliste und Arrays schreiben
	for ($y=0; $y < $count; $y++) {
		$mgl[]	= clm_core::$load->request_string('MGL'.$y);
		$pkz[]	= clm_core::$load->request_string('PKZ'.$y);
		$mnr[]	= clm_core::$load->request_string('MA'.$y);
		$rang[]	= clm_core::$load->request_string('RA'.$y);

		if ($mnr[$y] !=="99" AND $mnr[$y] !=="0" AND $mnr[$y] !=="") {
			$query = " INSERT INTO #__clm_rangliste_spieler "
				." (`Gruppe`, `ZPS`, `Mgl_Nr`, `PKZ`, `Rang`, `man_nr`, `sid`) "
				." VALUES ('$gid','$zps','$mgl[$y]','$pkz[$y]','$rang[$y]','$mnr[$y]','$sid') "
				;
			//$db->setQuery($query);
			clm_core::$db->query($query);
		}
	}

	// Meldelisten schreiben
	for ($x=0; $x < count($lid_rang); $x++) {
		$liga	= $lid_rang[$x]->liga;
		$man_nr	= $lid_rang[$x]->man_nr;

	$sn_cnt = 1;
	$snr_counter =1;

	for ($y=0; $y < $count; $y++) {
		$dkey = $liga.' '.$man_nr.' '.$zps.' '.intval($mgl[$y]);
		if (isset($old_ml[$dkey])) {
			$z_ordering	 = $old_ml[$dkey]->ordering; 
			$z_start_dwz = $old_ml[$dkey]->start_dwz; 
			$z_start_I0	 = $old_ml[$dkey]->start_I0; 
			$z_DWZ		 = $old_ml[$dkey]->DWZ; 
			$z_I0	 	 = $old_ml[$dkey]->I0; 
			$z_Punkte	 = $old_ml[$dkey]->Punkte; 
			$z_Partien	 = $old_ml[$dkey]->Partien; 
			$z_We		 = $old_ml[$dkey]->We; 
			$z_Leistung	 = $old_ml[$dkey]->Leistung; 
			$z_EFaktor	 = $old_ml[$dkey]->EFaktor; 
			$z_Niveau	 = $old_ml[$dkey]->Niveau; 
			$z_sum_saison = $old_ml[$dkey]->sum_saison; 
			$z_gesperrt	 = $old_ml[$dkey]->gesperrt; 
		} else {
			$z_ordering = 0;
			$z_start_dwz = NULL;
			$z_start_I0 = NULL;
			//echo "<br>n:".$dkey."  :"; var_dump($z_dwz);
			$z_DWZ		 = 0; 
			$z_I0	 	 = 0; 
			$z_Punkte	 = 0; 
			$z_Partien	 = 0; 
			$z_We		 = 0; 
			$z_Leistung	 = 0; 
			$z_EFaktor	 = 0; 
			$z_Niveau	 = 0; 
			$z_sum_saison = 0; 
			$z_gesperrt	 = 0; 
		}
	//if (($mnr[$y] >= $lid_rang[$x]->man_nr AND $rang[$y] < 1000 ) OR ($mnr[$y] == $lid_rang[$x]->man_nr) ) {
	if ($mnr[$y] >= $lid_rang[$x]->man_nr) {
	  if ($z_start_dwz == NULL OR $z_start_dwz == 0) 
		$query = " INSERT INTO #__clm_meldeliste_spieler "
			." (`sid`, `lid`, `mnr`, `snr`, `mgl_nr`, `zps`,`status`,`ordering`,`start_dwz`,`start_I0`"
			.",`DWZ`, `I0`, `Punkte`, `Partien`, `We`, `Leistung`,`EFaktor`,`Niveau`,`sum_saison`,`gesperrt`)"
			." VALUES ('$sid','$liga','$man_nr','$snr_counter','$mgl[$y]','$zps','$gid','$z_ordering',NULL,NULL"
			.",'$z_DWZ','$z_I0','$z_Punkte','$z_Partien','$z_We','$z_Leistung','$z_EFaktor','$z_Niveau','$z_sum_saison','$z_gesperrt') "
			;
	  else
		$query = " INSERT INTO #__clm_meldeliste_spieler "
			." (`sid`, `lid`, `mnr`, `snr`, `mgl_nr`, `zps`,`status`,`ordering`,`start_dwz`,`start_I0`"
			.",`DWZ`, `I0`, `Punkte`, `Partien`, `We`, `Leistung`,`EFaktor`,`Niveau`,`sum_saison`,`gesperrt`)"
			." VALUES ('$sid','$liga','$man_nr','$snr_counter','$mgl[$y]','$zps','$gid','$z_ordering','$z_start_dwz','$z_start_I0'"
			.",'$z_DWZ','$z_I0','$z_Punkte','$z_Partien','$z_We','$z_Leistung','$z_EFaktor','$z_Niveau','$z_sum_saison','$z_gesperrt') "
			;
		//$db->setQuery($query);
		clm_core::$db->query($query);
		$sn_cnt++;
		$snr_counter++;
	}
	}
		if($sn_cnt > 1) {
				$query = " UPDATE #__clm_mannschaften "
					." SET  liste = 1"
					." WHERE sid = $sid AND liga = $liga AND man_nr = $man_nr AND zps = '$zps' "
					;
				//$db->setQuery($query);
				clm_core::$db->query($query);
		}
	}

	if($sn_cnt =="1") {
		$mainframe->enqueueMessage(JText::_( 'RANGLISTE_MN_RANG' ), 'notice');
		$mainframe->enqueueMessage(JText::_( 'RANGLISTE_ERGEBNIS' ), 'notice');
		$mainframe->enqueueMessage(JText::_( 'RANGLISTE_MN_MANAGER' ), 'notice');
	}
	
	switch ($task)
	{
		case 'apply':
			$msg = JText::_( 'RANGLISTE_MSG_CHANGES_SAVED');
			$link = 'index.php?option='.$option.'&section='.$section.'&task=edit&id='. $row->id ;
			break;
		case 'save':
		default:
			$msg = JText::_( 'RANGLISTE_MSG_SAVED');
			$link = 'index.php?option='.$option.'&section='.$section;
			break;
	}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = $aktion;
	//$clmLog->params = array('sid' => $sid, 'lid' => $lid, 'zps' => $zps);
	$clmLog->params = array('sid' => $sid, 'zps' => $zps);

	$clmLog->write();
	
	$mainframe->enqueueMessage($msg, 'message');
	$mainframe->redirect( $link );
	}

function cancel()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	defined('clm') or die( 'Invalid Token' );
	
	$option 	= clm_core::$load->request_string('option');
	$section	= clm_core::$load->request_string('section');
	$id		= clm_core::$load->request_int('id');	
	$row 		=JTable::getInstance( 'ranglisten', 'TableCLM' );
	$filter_vid	= $mainframe->getUserStateFromRequest( "$option.filter_vid",'filter_vid',0,'var' );
	$msg = JText::_( 'RANGLISTE_AKTION').$filter_vid;
	$mainframe->enqueueMessage($msg, 'message');
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
	$section	= clm_core::$load->request_string('section');

	if (count($cid) < 1) {
		$mainframe->enqueueMessage(JText::_( 'RANGLISTE_SELECT', true ), 'warning');
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

	// Prüfen ob User Berechtigung zum löschen hat //
	$sql = " SELECT l.sl FROM #__clm_mannschaften as a "
		." LEFT JOIN #__clm_liga as l ON ( l.id= a.liga AND l.sid = a.sid) "
		." WHERE a.id = ".$cid[0]
		;
	$db->setQuery($sql);
	$lid = $db->loadObjectList();

	// Wenn User nicht Admin oder DWZ prüfen ob SL der Staffel //
	$clmAccess = clm_core::$access;
	if ($clmAccess->access('BE_club_edit_ranking') === false) {
			$mainframe->enqueueMessage(JText::_( 'RANGLISTE_RL_STAFFEL', true ), 'warning');
			$link = 'index.php?option='.$option.'&section='.$section;
			$mainframe->redirect( $link);
					}
	else {
		$row	= JTable::getInstance( 'ranglisten', 'TableCLM' );
		$row->load( $cid[0] );

		$query	=" DELETE FROM #__clm_rangliste_spieler "
			." WHERE Gruppe =".$row->gid
			." AND ZPS ='$row->zps'"
			." AND sid =".$row->sid
			;
		//$db->setQuery( $query );
		if (!clm_core::$db->query($query)) {
			echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
		}

		$query = " DELETE FROM #__clm_rangliste_id "
			." WHERE id = $cid[0]"
			." AND sid =".$row->sid
			;
		//$db->setQuery( $query );
		if (!clm_core::$db->query($query)) {
			echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
		}

		$query = " DELETE FROM #__clm_meldeliste_spieler "
			." WHERE status = ".$row->gid
			." AND zps = '$row->zps'"
			." AND sid =".$row->sid
			;
		//$db->setQuery( $query );
		if (!clm_core::$db->query($query)) {
		echo "<script> alert('".$db->getErrorMsg(true)."'); window.history.go(-1); </script>\n";
		}
		}
//		}

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = $aktion;
	$clmLog->params = array('cid' => $cids, 'zps' => $row->zps);
	$clmLog->write();
	
	if (count($cid) == 1) { 
		$msg = JText::_( 'RANGLISTE_MSG_DEL_ENTRY'); 
	} else { 
		$msg = count($cid)." ".JText::_( 'RANGLISTE_MSG_DEL_ENTRYS'); 
	}

	$mainframe->enqueueMessage($msg, 'message');
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

function publish()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	defined('clm') or die( 'Invalid Token' );

	$db 		= JFactory::getDBO();
	$user 		= JFactory::getUser();
	$cid		= clm_core::$load->request_array_int('cid');
	$task		= clm_core::$load->request_string( 'task' );
	$publish	= ($task == 'publish');
	$option 	= clm_core::$load->request_string('option');
	$section	= clm_core::$load->request_string('section');

	if (empty( $cid )) {
		$mainframe->enqueueMessage('No items selected', 'warning');
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
	// Prüfen ob User Berechtigung zum publizieren hat
	$sql = " SELECT l.sl FROM #__clm_mannschaften as a "
		." LEFT JOIN #__clm_liga as l ON ( l.id= a.liga AND l.sid = a.sid) "
		." WHERE a.id = ".$cid[0]
		;
	$db->setQuery($sql);
	$lid = $db->loadObjectList();

	// Wenn User nicht Admin oder DWZ prüfen ob SL der Staffel
	$clmAccess = clm_core::$access;
	if ($clmAccess->access('BE_club_edit_ranking') === false) {
		$mainframe->enqueueMessage(JText::_( 'RANGLISTE_STAL_RANG' ), 'warning');
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
	}
	else {
		$cids = implode( ',', $cid );
		$query = 'UPDATE #__clm_rangliste_id'
			. ' SET published = '.(int) $publish
			. ' WHERE id IN ( '. $cids .' )'
			. ' AND ( checked_out = 0 OR ( checked_out = '.(int) $user->get('id') .' ) )';
		}
		//$db->setQuery( $query );
	if (!clm_core::$db->query($query)) { 
		$mainframe->enqueueMessage($db->getErrorMsg(), 'error');
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link);
	}
	if (count( $cid ) == 1) {
		$row =JTable::getInstance( 'ranglisten', 'TableCLM' );
		}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'RANGLISTE_LOG')." ".$task;
	$table		= &JTable::getInstance( 'ranglisten', 'TableCLM');
	$table->load($cid[0]);
	$clmLog->params = array('sid' => $table->sid, 'lid' => $lid, 'zps' => $table->zps, 'cids' => $cids);
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
	defined('clm') or die( 'Invalid Token' );

	$db		=JFactory::getDBO();
	$cid		= clm_core::$load->request_array_int('cid');
	$option 	= clm_core::$load->request_string('option');
	$section	= clm_core::$load->request_string('section');

	$limit 		= clm_core::$load->request_int( 'limit', 0, '', 'int' );
	$limitstart 	= clm_core::$load->request_int( 'limitstart', 0, '', 'int' );

	$row =JTable::getInstance( 'mannschaften', 'TableCLM' );
	$row->load( $cid[0]);
	$row->move( $inc, 'liga = '.(int) $row->liga.' AND published != 0' );

	$msg 	= JText::_( 'RANGLISTE_MSG_SORT');
	$mainframe->enqueueMessage($msg, 'message');
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
	$section	= clm_core::$load->request_string('section');

	$total		= count( $cid );
	$order		= clm_core::$load->request_array_int('order');

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
				$mainframe->enqueueMessage(JText::_( 'RANGLISTE_SELECT', true ), 'warning');
				$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
			}
		}
	}
	// execute update Order for each parent group
	$groupings = array_unique( $groupings );
	foreach ($groupings as $group){
		$row->reorder('liga = '.(int) $group);
	}
	$msg 	= 'New ordering saved';
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}


function copy()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	defined('clm') or die( 'Invalid Token' );
	$option 	= clm_core::$load->request_string('option');
	$section	= clm_core::$load->request_string('section');
	$cid		= clm_core::$load->request_array_int('cid');
	$db		= JFactory::getDBO();
	$table		= &JTable::getInstance('ranglisten', 'TableCLM');
	$user		= JFactory::getUser();
	$n		= count( $cid );
	$this->setRedirect( 'index.php?option='.$option.'&section='.$section );

	// Prüfen ob User Berechtigung zum kopieren hat
	$clmAccess = clm_core::$access;
	if ($clmAccess->access('BE_club_edit_ranking') === false) {
		$mainframe->enqueueMessage(JText::_( 'RANGLISTE_ADMIN', true ), 'warning');
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
	else {

	if ($n > 0)
	{
		foreach ($cid as $id)
		{
			if ($table->load( (int)$id )) {
	
				$table->id			= 0;
				$table->published		= 0;

				if (!$table->store()) {	
					$mainframe->enqueueMessage($table->getError(), 'warning');
					$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
				}
			} else {	
				$mainframe->enqueueMessage($table->getError(), 'warning');
				$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
			}
																  
		}
	}
	else {	
		$mainframe->enqueueMessage(JText::_( 'RANGLISTE_NO_SELECT', true ), 'warning');
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}

	if ($n >1) { $msg=JText::_( 'RANGLISTE_MSG_COPY_ENTRYS');}
		else {$msg=JText::_( 'RANGLISTE_MSG_COPY_ENTRY');}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'RANGLISTE_LOG_COPIED');
	$table		= &JTable::getInstance( 'ranglisten', 'TableCLM');
	$table->load($cid[0]);
	$clmLog->params = array('sid' => $table->sid, 'lid' => $lid, 'zps' => $table->zps, 'cids' => implode( ',', $cid ));
	$clmLog->write();
	
	$mainframe->enqueueMessage($n.$msg, 'message');
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
	}
	}
}
