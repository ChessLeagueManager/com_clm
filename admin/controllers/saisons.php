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
defined('_JEXEC') or die('Restricted access');
class CLMControllerSaisons extends JControllerLegacy {
	/**
	 * Constructor
	 */
	function __construct($config = array()) {
		parent::__construct($config);
		// Register Extra tasks
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('unpublish', 'publish');
	}
	function display($cachable = false, $urlparams = array()) {
		$mainframe = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		$db = JFactory::getDBO();
		$filter_order = $mainframe->getUserStateFromRequest("$option.filter_order", 'filter_order', 'a.id', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest("$option.filter_order_Dir", 'filter_order_Dir', '', 'word');
		$filter_state = $mainframe->getUserStateFromRequest("$option.filter_state", 'filter_state', '', 'word');
		$search = $mainframe->getUserStateFromRequest("$option.search", 'search', '', 'string');
		$search = JString::strtolower($search);
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option . '.limitstart', 'limitstart', 0, 'int');
		$where = array();
		$orderby = "";
		if ($search) {
			$where[] = 'LOWER(a.name) LIKE "' . $db->escape('%' . $search . '%') . '"';
		}
		if ($filter_state) {
			if ($filter_state == 'P') {
				$where[] = 'a.published = 1';
			} else if ($filter_state == 'U') {
				$where[] = 'a.published = 0';
			}
		}
		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
		if ($filter_order == 'a.id') {
			$orderby = ' ORDER BY id';
		} else {
			if ($filter_order == 'a.name' OR $filter_order == 'a.published' OR $filter_order == 'a.ordering' OR $filter_order == 'a.archiv') {
				$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir . ', a.id';
			} else {
				$filter_order = 'a.id';
			}
		}
		// get the total number of records
		$query = 'SELECT COUNT(*) ' . ' FROM #__clm_saison AS a' . $where;
		$db->setQuery($query);
		$total = $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);
		// get the subset (based on limits) of required records
		$query = 'SELECT a.*,u.name AS editor ' . ' FROM #__clm_saison AS a' . ' LEFT JOIN #__users AS u ON u.id = a.checked_out' . $where . $orderby;
		$db->setQuery($query, $pageNav->limitstart, $pageNav->limit);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		// aktive Saisons zählen
		$query = ' SELECT COUNT(id) as id FROM #__clm_saison ' . ' WHERE archiv = 0 AND published = 1';
		$db->setQuery($query);
		$counter = $db->loadResult();
		if ($counter > 1) {
			if ($counter > 2) {
				$s = "s";
			} else {
				$s = "";
			}
			JError::raiseNotice(6000, JText::_('SAISON_GIBT') . ' ' . $counter . ' ' . JText::_('SAISON_AKTIVE') . ' ' . ($counter - 1) . ' ' . JText::_('SAISON') . ' ' . $s . ' ' . JText::_('SAISON_ZURUECK'));
		}
		// state filter
		$lists['state'] = JHtml::_('grid.state', $filter_state);
		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;
		// search filter
		$lists['search'] = $search;
		require_once (JPATH_COMPONENT . DS . 'views' . DS . 'saisons.php');
		CLMViewSaisons::saisons($rows, $lists, $pageNav, $option);
	}
	function edit() {
		$mainframe = JFactory::getApplication();
		// Check for request forgeries
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$task = JRequest::getVar('task');
		$cid = JRequest::getVar('cid', array(0), '', 'array');
		$option = JRequest::getCmd('option');
		JArrayHelper::toInteger($cid, array(0));
		$row = JTable::getInstance('saisons', 'TableCLM');
		if ($task == 'edit') {
			$row->load($cid[0]);
			$row->checkout($user->get('id'));
		}
		// Archiv
		$lists['archiv'] = JHtml::_('select.booleanlist', 'archiv', 'class="inputbox"', $row->archiv);
		$lists['published'] = JHtml::_('select.booleanlist', 'published', 'class="inputbox"', $row->published);
		require_once (JPATH_COMPONENT . DS . 'views' . DS . 'saisons.php');
		CLMViewSaisons::saison($row, $lists, $option);
	}
	function save() {
		$mainframe = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');
		$section = JRequest::getVar('section');
		$db = JFactory::getDBO();
		$task = JRequest::getVar('task');
		$row = JTable::getInstance('saisons', 'TableCLM');
		if (!$row->bind(JRequest::get('post'))) {
			JError::raiseError(500, $row->getError());
		}
		// pre-save checks
		if (!$row->check()) {
			JError::raiseError(500, $row->getError());
		}
		if (!$row->id) {
			$id = - 1;
		} else {
			$id = $row->id;
		}
		$out = clm_core::$api->db_season_save($id, $row->published, $row->archiv, $row->name, $row->bemerkungen, $row->bem_int, $row->datum);
		if ($task == 'save' && $out[0]) {
			$link = 'index.php?option=' . $option . '&section=' . $section;
		} else if ($out[0]) {
			$link = 'index.php?option=' . $option . '&section=' . $section . '&task=edit&cid[]=' . $out[2];
		} else {
			$message = clm_core::$load->load_view("notification", array($out[1], false));
			$message = $message[0];  // array dereferencing fix php 5.3

			$mainframe->enqueueMessage($message[0], $message[1]);
			$option = JRequest::getCmd('option');
			$lists['archiv'] = JHtml::_('select.booleanlist', 'archiv', 'class="inputbox"', $row->archiv);
			$lists['published'] = JHtml::_('select.booleanlist', 'published', 'class="inputbox"', $row->published);
			require_once (JPATH_COMPONENT . DS . 'views' . DS . 'saisons.php');
			CLMViewSaisons::saison($row, $lists, $option);
			return;
		}
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_('SAISON_SAISON_GESPEI');
		$clmLog->params = array('sid' => $out[2]);
		$clmLog->write();
		$message = clm_core::$load->load_view("notification", array($out[1], false));
		$message = $message[0];  // array dereferencing fix php 5.3
		$mainframe->redirect($link, $message[0], $message[1]);
	}
	function cancel() {
		$mainframe = JFactory::getApplication();
		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');
		$option = JRequest::getCmd('option');
		$section = JRequest::getVar('section');
		$id = JRequest::getVar('id');
		$row = JTable::getInstance('saisons', 'TableCLM');
		$msg = JText::_('SAISON_AKTION');
		$mainframe->redirect('index.php?option=' . $option . '&section=' . $section, $msg, "message");
	}
	function remove() {
		$mainframe = JFactory::getApplication();
		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(), '', 'array');
		$option = JRequest::getCmd('option');
		$section = JRequest::getVar('section');
		JArrayHelper::toInteger($cids);
		foreach ($cids as $cid) {
			$out = clm_core::$api->db_season_delete($cid);
			$message = clm_core::$load->load_view("notification", array($out[1], false));
			$message = $message[0];  // array dereferencing fix php 5.3
			$mainframe->enqueueMessage($message[0], $message[1]);
			if ($out[0]) {
				// Log schreiben
				$clmLog = new CLMLog();
				$clmLog->aktion = JText::_('SAISON_AKTION_SEASON_DEL') . " " . $task;
				$clmLog->params = array('sid' => clm_core::$access->getSeason(), 'cids' => $cid);
				$clmLog->write();
			}
		}
		$mainframe->redirect('index.php?option=' . $option . '&section=' . $section);
	}
	function publish() {
		$mainframe = JFactory::getApplication();
		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$cids = JRequest::getVar('cid', array(), '', 'array');
		$task = JRequest::getCmd('task');
		$option = JRequest::getCmd('option');
		$section = JRequest::getVar('section');
		JArrayHelper::toInteger($cid);
		foreach ($cids as $cid) {
			if ($task == "publish") {
				$out = clm_core::$api->db_season_save($cid, 1);
				$message = clm_core::$load->load_view("notification", array($out[1], false));
				$message = $message[0];  // array dereferencing fix php 5.3
				$mainframe->enqueueMessage($message[0], $message[1]);
			} else {
				$out = clm_core::$api->db_season_save($cid, 0);
				$message = clm_core::$load->load_view("notification", array($out[1], false));
				$message = $message[0];  // array dereferencing fix php 5.3
				$mainframe->enqueueMessage($message[0], $message[1]);
			}
			if ($out[0]) {
				// Log schreiben
				$clmLog = new CLMLog();
				$clmLog->aktion = JText::_('SAISON_AKTION_SEASON') . " " . $task;
				$clmLog->params = array('sid' => clm_core::$access->getSeason(), 'cids' => $cid);
				$clmLog->write();
			}
		}
		$mainframe->redirect('index.php?option=' . $option . '&section=' . $section);
	}
	/**
	 * Moves the record up one position
	 */
	function orderdown() {
		order(-1);
	}
	/**
	 * Moves the record down one position
	 */
	function orderup() {
		order(1);
	}
	/**
	 * Moves the order of a record
	 * @param integer The direction to reorder, +1 down, -1 up
	 */
	function order($inc) {
		$mainframe = JFactory::getApplication();
		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');
		$db = JFactory::getDBO();
		$cid = JRequest::getVar('sid', array(0), '', 'array');
		$option = JRequest::getCmd('option');
		$section = JRequest::getVar('section');
		JArrayHelper::toInteger($cid, array(0));
		$limit = JRequest::getVar('limit', 0, '', 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$sid = JRequest::getVar('sid', 0, '', 'int');
		$row = JTable::getInstance('saisons', 'TableCLM');
		$row->load($sid[0]);
		$row->move($inc, 'id = ' . (int)$row->catid . ' AND published != 0');
		$mainframe->redirect('index.php?option=' . $option . '&section=' . $section);
	}
	/**
	 * Saves user reordering entry
	 */
	function saveOrder() {
		$mainframe = JFactory::getApplication();
		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');
		$db = JFactory::getDBO();
		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		$option = JRequest::getCmd('option');
		$section = JRequest::getVar('section');
		JArrayHelper::toInteger($cid);
		$total = count($cid);
		$order = JRequest::getVar('order', array(0), 'post', 'array');
		JArrayHelper::toInteger($order, array(0));
		$row = JTable::getInstance('saisons', 'TableCLM');
		$groupings = array();
		// update ordering values
		for ($i = 0;$i < $total;$i++) {
			$row->load((int)$cid[$i]);
			// track categories
			//$groupings[] = $row->id;
			$groupings[] = 0;

			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					JError::raiseError(500, $db->getErrorMsg());
				}
			}
		}
		// execute updateOrder for each parent group
		$groupings = array_unique($groupings);
		foreach ($groupings as $group) {
			$row->reorder('id = ' . (int)$group);
		}
		$app =JFactory::getApplication();
		$app->enqueueMessage( JText::_('CLM_NEW_ORDERING_SAVED') );
		$mainframe->redirect('index.php?option=' . $option . '&section=' . $section);
	}
	function copy() {
		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');
		$option = JRequest::getCmd('option');
		$section = JRequest::getVar('section');
		$this->setRedirect('index.php?option=' . $option . '&section=' . $section);
		$cid = JRequest::getVar('cid', null, 'post', 'array');
		$db = JFactory::getDBO();
		$table = JTable::getInstance('saisons', 'TableCLM');
		$user = JFactory::getUser();
		$n = count($cid);
		if ($n > 0) {
			foreach ($cid as $id) {
				if ($table->load((int)$id)) {
					$table->id = 0;
					$table->name = 'Kopie von ' . $table->name;
					$table->published = 0;
					if (!$table->store()) {
						return JError::raiseWarning($table->getError());
					}
				} else {
					return JError::raiseWarning(500, $table->getError());
				}
			}
		} else {
			return JError::raiseWarning(500, JText::_('SAISON_NO_SELECT'));
		}
		if ($n > 1) {
			$msg = JText::_('SAISON_MSG_ENTRYS_COPY');
		} else {
			$msg = JText::_('SAISON_MSG_ENTRY_COPY');
		}
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_('SAISON_AKTION_SEASON_COPY');
		$clmLog->params = array('sid' => $cid[0], 'cids' => implode(',', $cid));
		$clmLog->write();
		$this->setMessage(JText::_($n . $msg));
	}
	///////////////
	// DEBUGGING //
	///////////////
	function change() {
		$mainframe = JFactory::getApplication();
		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');
		$option = JRequest::getCmd('option');
		$section = JRequest::getVar('section');
		$db = JFactory::getDBO();
		$table = JTable::getInstance('saisons', 'TableCLM');
		$table->load(1);
		$pub_1 = $table->archiv;
		if ($pub_1 == "1") {
			$table->archiv = 0;
		} else {
			$table->archiv = 1;
		}
		$table->store();
		$table->load(2);
		$pub_2 = $table->published;
		if ($pub_2 == "1") {
			$table->published = 0;
		} else {
			$table->published = 1;
		}
		$table->store();
		$msg = JText::_('SAISON_MSG_STATUS');
		$mainframe->redirect('index.php?option=' . $option . '&section=' . $section, $msg, "message");
	}
 
function dwz_start()          
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		= JFactory::getDBO();
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
	$row = JTable::getInstance( 'saisons', 'TableCLM' );
	$row->load( $cid[0] );
	// load the row from the db table
		$sid= $row->id;
	
	// Starten der DWZ-Auswertung zur Saison
	$result = clm_core::$api->db_season_genDWZ($sid,true);

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'SAISON_LOG_DWZ');
	$clmLog->nr_aktion = 102;	
	if (isset($result[2])) $clmLog->params = array('sid' => $sid, 'lid' => $result[2]);
	else $clmLog->params = array('sid' => $sid, 'lid' => 0);  
	$clmLog->write();

	$msg = JText::_( 'SAISON_DWZ_IST');
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
 
}

function dwz_del()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		= JFactory::getDBO();
	$cid 		= JRequest::getVar('cid', array(), '', 'array');
	$option 	= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	JArrayHelper::toInteger($cid);

	// keine Saison gewählt
	if ($cid[0] < 1 ) {
	JError::raiseWarning( 500, JText::_( 'SAISON_NO_LOESCH' ) );
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
		}
	//Saison wird durch User im Screen bestimmt
	$row = JTable::getInstance( 'saisons', 'TableCLM' );
	$row->load( $cid[0] );
	// load the row from the db table
		$sid= $row->id;
	
	// Löschen der DWZ-Auswertung zur Saison
	$result = clm_core::$api->db_season_delDWZ($sid,true);

	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'SAISON_LOG_DWZ_DEL');
	$clmLog->nr_aktion = 102;	
	$clmLog->params = array('sid' => $sid, 'lid' => 0);  
	$clmLog->write();

	$msg = JText::_( 'SAISON_DWZ_IST_LOESCH');
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
	}
}
