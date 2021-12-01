<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
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
		$option = clm_core::$load->request_string('option', '');
		$db = JFactory::getDBO();
		$filter_order = $mainframe->getUserStateFromRequest("$option.filter_order", 'filter_order', 'a.id', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest("$option.filter_order_Dir", 'filter_order_Dir', '', 'word');
		$filter_state = $mainframe->getUserStateFromRequest("$option.filter_state", 'filter_state', '', 'word');
		$search = $mainframe->getUserStateFromRequest("$option.search", 'search', '', 'string');
		$search = strtolower($search);
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
		$rows = clm_core::$db->loadObjectList($query);	

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
			$this->setMessage(JText::_('SAISON_GIBT') . ' ' . $counter . ' ' . JText::_('SAISON_AKTIVE') . ' ' . ($counter - 1) . ' ' . JText::_('SAISON') . ' ' . $s . ' ' . JText::_('SAISON_ZURUECK'), 'notice');		
		}
		// state filter
		//$lists['state']	= JHTML::_('grid.state',  $filter_state );
		$lists['state'] = CLMForm::selectState( $filter_state );
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
		$task = clm_core::$load->request_string('task', '');
		$cid = clm_core::$load->request_array_int('cid');
		if (is_null($cid)) 
			$cid[0] = clm_core::$load->request_int('id');
		$option = clm_core::$load->request_string('option', '');
		$row = JTable::getInstance('saisons', 'TableCLM');
		if ($task == 'edit') {
			$row->load($cid[0]);
			$row->checkout($user->get('id'));
		}
		//CLM parameter auslesen
		$config = clm_core::$db->config();
		$countryversion = $config->countryversion;
		if ($countryversion == 'en' AND $row->id == 0) {
			$row->rating_type = 1;
		}
		// Archiv
		$lists['archiv'] = JHtml::_('select.booleanlist', 'archiv', 'class="inputbox"', $row->archiv);
		$lists['published'] = JHtml::_('select.booleanlist', 'published', 'class="inputbox"', $row->published);
		$lists['rating_type'] = JHtml::_('select.booleanlist', 'rating_type', 'class="inputbox"', $row->rating_type);
		require_once (JPATH_COMPONENT . DS . 'views' . DS . 'saisons.php');
		CLMViewSaisons::saison($row, $lists, $option);
	}
	function save() {
		$mainframe = JFactory::getApplication();
		$emessage = '';
		$option = clm_core::$load->request_string('option', '');
		// Check for request forgeries
		defined('_JEXEC') or die('Invalid Token');
		$section = clm_core::$load->request_string('section', '');
		$db = JFactory::getDBO();
		$task = clm_core::$load->request_string('task', '');
		$row = JTable::getInstance('saisons', 'TableCLM');
		$post = $_POST;
		if (!$row->bind($post)) {
			$emassage = $row->getError();
			$etype = 'error';
		}
		if ($emessage == '') {
			// pre-save checks
			if (!$row->check()) {
				$emessage = $row->getError();
				$etype = 'warning';
			}
			if ($emessage == '') {
				if (!$row->id) {
					$id = - 1;
				} else {
					$id = $row->id;
				}
				$out = clm_core::$api->db_season_save($id, $row->published, $row->archiv, $row->name, $row->bemerkungen, $row->bem_int, $row->datum, $row->rating_type);
				if ($task == 'save' && $out[0]) {
					$link = 'index.php?option=' . $option . '&section=' . $section;
				} else if ($out[0]) {
					$link = 'index.php?option=' . $option . '&section=' . $section . '&task=edit&id=' . $out[2];
				} else {
					$message = clm_core::$load->load_view("notification", array($out[1], false));
					$message = $message[0];  // array dereferencing fix php 5.3
					$emessage = $message[0];
					$etype = $message[1];
				}
			}
		}
		if ($emessage != '') {
			$mainframe->enqueueMessage( $emessage,$etype );
			$lists['archiv'] = JHtml::_('select.booleanlist', 'archiv', 'class="inputbox"', $row->archiv);
			$lists['published'] = JHtml::_('select.booleanlist', 'published', 'class="inputbox"', $row->published);
			$lists['rating_type'] = JHtml::_('select.booleanlist', 'rating_type', 'class="inputbox"', $row->rating_type);
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
		$this->setRedirect($link);
		$this->setMessage($message[0], $message[1]);
	}
	function cancel() {
		$mainframe = JFactory::getApplication();
		// Check for request forgeries
		defined('_JEXEC') or die('Invalid Token');
		$option = clm_core::$load->request_string('option', '');
		$section = clm_core::$load->request_string('section', '');
		$id = clm_core::$load->request_int('id', 0);
		$row = JTable::getInstance('saisons', 'TableCLM');
		$msg = JText::_('SAISON_AKTION');
		$this->setRedirect('index.php?option=' . $option . '&section=' . $section);
		$this->setMessage($msg);
	}
	function remove() {
		$mainframe = JFactory::getApplication();
		// Check for request forgeries
		defined('_JEXEC') or die('Invalid Token');
		$db = JFactory::getDBO();
		$cids = clm_core::$load->request_array_int('cid');
		$option = clm_core::$load->request_string('option', '');
		$section = clm_core::$load->request_string('section', '');
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
		$this->setRedirect('index.php?option=' . $option . '&section=' . $section);
	}
	function publish() {
		$mainframe = JFactory::getApplication();
		// Check for request forgeries
		defined('_JEXEC') or die('Invalid Token');
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$cids = clm_core::$load->request_array_int('cid');
		$task = clm_core::$load->request_string('task', '');
		$option = clm_core::$load->request_string('option', '');
		$section = clm_core::$load->request_string('section', '');
		foreach ($cids as $cid) {
			if ($task == "publish") {
				$out = clm_core::$api->db_season_save($cid, 1);
				$message = clm_core::$load->load_view("notification", array($out[1], false));
				$message = $message[0];  // array dereferencing fix php 5.3
				$this->setMessage($message[0], $message[1]);
			} else {
				$out = clm_core::$api->db_season_save($cid, 0);
				$message = clm_core::$load->load_view("notification", array($out[1], false));
				$message = $message[0];  // array dereferencing fix php 5.3
				$this->setMessage($message[0], $message[1]);
			}
			if ($out[0]) {
				// Log schreiben
				$clmLog = new CLMLog();
				$clmLog->aktion = JText::_('SAISON_AKTION_SEASON') . " " . $task;
				$clmLog->params = array('sid' => clm_core::$access->getSeason(), 'cids' => $cid);
				$clmLog->write();
			}
		}
		$this->setRedirect('index.php?option=' . $option . '&section=' . $section);
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
		defined('_JEXEC') or die('Invalid Token');
		$db = JFactory::getDBO();
		$cid = clm_core::$load->request_array_int('cid');
		$option  = clm_core::$load->request_string('option', '');
		$section = clm_core::$load->request_string('section', '');
										  
		$limit = clm_core::$load->request_int('limit', 0);
		$limitstart = clm_core::$load->request_int('limitstart', 0);
		$sid = clm_core::$load->request_int('sid', 0);
		$row = JTable::getInstance('saisons', 'TableCLM');
		$row->load($sid[0]);
		$row->move($inc, 'id = ' . (int)$row->catid . ' AND published != 0');
		$this->setRedirect('index.php?option=' . $option . '&section=' . $section);
	}
	/**
	 * Saves user reordering entry
	 */
	function saveOrder() {
		$mainframe = JFactory::getApplication();
		// Check for request forgeries
		defined('_JEXEC') or die('Invalid Token');
		$db = JFactory::getDBO();
		$cid = clm_core::$load->request_array_int('cid');
		$option  = clm_core::$load->request_string('option', '');
		$section = clm_core::$load->request_string('section', '');
								
		$total = count($cid);
		$order = clm_core::$load->request_array_int('order');
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
					$this->setMessage($db->getErrorMsg(), 'error');
					return;
				}
			}
		}
		// execute updateOrder for each parent group
		$groupings = array_unique($groupings);
		foreach ($groupings as $group) {
			$row->reorder('id = ' . (int)$group);
		}
		$this->setMessage(JText::_('CLM_NEW_ORDERING_SAVED'));
		$this->setRedirect('index.php?option=' . $option . '&section=' . $section);
	}
	function copy() {
		// Check for request forgeries
		defined('_JEXEC') or die('Invalid Token');
		$option  = clm_core::$load->request_string('option', '');
		$section = clm_core::$load->request_string('section', '');
		$this->setRedirect('index.php?option=' . $option . '&section=' . $section);
		$cid = clm_core::$load->request_array_int('cid');
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
						$this->setMessage($table->getError(), 'warning');
						return;
					}
				} else {
					$this->setMessage($table->getError(), 'warning');
					return;
				}
			}
		} else {
			$this->setMessage(JText::_('SAISON_NO_SELECT'), 'warning');
			return;
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
		defined('_JEXEC') or die('Invalid Token');
		$option  = clm_core::$load->request_string('option', '');
		$section = clm_core::$load->request_string('section', '');
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
		$this->setMessage($msg);
		$this->setRedirect('index.php?option=' . $option . '&section=' . $section);
	}
 
function dwz_start()          
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	defined('_JEXEC') or die( 'Invalid Token' );

	$db 		= JFactory::getDBO();
	$cid 		= clm_core::$load->request_array_int('cid');
	$option 	= clm_core::$load->request_string('option', '');
	$section	= clm_core::$load->request_string('section', '');

	// keine Saison gewählt
	if ($cid[0] < 1 ) {
		$this->setMessage(JText::_('SAISON_NO_AUSWERTEN'), 'warning');
		$this->setRedirect('index.php?option=' . $option . '&section=' . $section);
		return;
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
	$this->setMessage($msg);
	$this->setRedirect('index.php?option=' . $option . '&section=' . $section);
 
}

function dwz_del()
	{
	$mainframe	= JFactory::getApplication();
	// Check for request forgeries
	defined('_JEXEC') or die( 'Invalid Token' );

	$db 		= JFactory::getDBO();
	$cid 		= clm_core::$load->request_array_int('cid');
	$option 	= clm_core::$load->request_string('option', '');
	$section	= clm_core::$load->request_string('section', '');

	// keine Saison gewählt
	if ($cid[0] < 1 ) {
		$this->setMessage(JText::_('SAISON_NO_LOESCH'), 'warning');
		$this->setRedirect('index.php?option=' . $option . '&section=' . $section);
		return;
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
	$this->setMessage($msg);
	$this->setRedirect('index.php?option=' . $option . '&section=' . $section);
	}
}
