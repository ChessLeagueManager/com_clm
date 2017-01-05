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
defined('_JEXEC') or die('Restricted access');

class CLMModelAccessgroupsForm extends JModelLegacy {
	var $_accessgroup;
	var $_accessgroups;
	var $_ordering;
	var $_accesspoints;
 
	function __construct() { 
		parent::__construct(); 
		$id 	= clm_escape(JRequest::getVar( 'id' ));
		if (!isset($id) OR $id==0) {
			$array = JRequest::getVar('cid',  0, '', 'array'); 
			$this->setId(clm_escape($array[0])); 
		} else $this->setID($id);
	} 
	
	function setId($id) { 
		$this->_id = $id; 
		$this->_accessgroup = null; 
	}
	
	function getAccessgroup() { 
		if (empty( $this->_accessgroup )) { 
			$query = ' SELECT * FROM #__clm_usertype '
					.' WHERE id = '.$this->_id; 
			$this->_db->setQuery( $query ); 
			$this->_accessgroup = $this->_db->loadObject(); 
		} 
		if (!$this->_accessgroup) { 
			$this->_accessgroup= new stdClass(); 
			$this->_accessgroup->id				= 0;
			$this->_accessgroup->name			= '';
			$this->_accessgroup->usertype		= '';
			$this->_accessgroup->kind			= 'USER';
			$this->_accessgroup->published		= 0;
			$this->_accessgroup->ordering 		= 0;
			$this->_accessgroup->params 		= '';
		} 
		return $this->_accessgroup; 
	}

	function getAccessgroups() { 
		$query = ' SELECT id, name, usertype FROM #__clm_usertype as a'
				.' WHERE id <> '.$this->_id; 
		$this->_accessgroups = $this->_getList($query); 
		
		return $this->_accessgroups; 
	}
	
	function getOrdering() {
		if (empty( $this->_ordering )) { 
			if(!empty( $this->_accessgroup->name )) {
				$query = ' 	SELECT id, ordering, name FROM #__clm_usertype
							WHERE name = "'.$this->_accessgroup->name.'"
							ORDER BY ordering ASC';
				$this->_ordering = $this->_getList( $query );	
			}
			else {
				$this->_ordering = null;
			}
		}
		return $this->_ordering; 
	}
		
	function _getAktuelleSaison() {
		$db =& $this->getDbo();

		$query = ' 	SELECT id FROM #__clm_saison
					WHERE published = 1
					ORDER BY name DESC;';
		$db->setQuery( $query );
		return $db->loadObject()->id;	
	}
	
	function store() { 
		$row = JTable::getInstance( 'accessgroupsform', 'TableCLM' );
		$accessgroup = JRequest::get( 'post' ); 
		if (!$row->bind($accessgroup)) { 
			$this->setError($this->_db->getErrorMsg()); 
			return false; 
		} 
		if (!$row->check()) { 
			$this->setError($this->_db->getErrorMsg()); 
			return false;
		} 
		if (!$row->store()) { 
			$this->setError( $row->_db->getErrorMsg() ); 
			return false; 
		} 
		return true;
	}
	
	function delete() { 
		$cids = JRequest::getVar( 'cid', array(0),'post', 'array' ); 
		$row = JTable::getInstance( 'accessgroupsform', 'TableCLM' );
		if (count( $cids )) { 
			foreach($cids as $cid) { 
				if (!$row->delete( $cid )) { 
					$this->setError( $row->_db->getErrorMsg() ); 
					return false; 
				} 
			} 
		} 
		return true; 
	} 
	
	function publish() {
		$cids = JRequest::getVar( 'cid', array(0),'post', 'array' );
		$row = JTable::getInstance( 'accessgroupsform', 'TableCLM' );
		if (!$row->publish( $cids )) { 
			$this->setError( $row->_db->getErrorMsg() ); 
			return false; 
		}
		return true; 
	} 
	
	function unpublish() {
		$cids = JRequest::getVar( 'cid', array(0),'post', 'array' );
		$row = JTable::getInstance( 'accessgroupsform', 'TableCLM' );
		if (!$row->publish($cids,0)) { 
			$this->setError( $row->_db->getErrorMsg() ); 
			return false; 
		}
		return true; 
	} 
	
	function saveOrder() {
		$cids = JRequest::getVar( 'cid', array(0),'post', 'array' );
		$order = JRequest::getVar('order', array (0), 'post', 'array');
		$row = JTable::getInstance( 'accessgroupsform', 'TableCLM' );		
		for($i = 0; $i < count($cids); $i ++) {
			$row->load((int)$cids[$i]);
			if($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if(!$row->store()) {
					$this->setError( $this->_db->getErrorMsg() );
					return false;
				}
			}
		}
		if(!$row->reorderAll()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
	
	function orderUp() {
		$cids = JRequest::getVar( 'cid', array(0),'post', 'array' );
		if(isset($cids[0])) {
			$row = JTable::getInstance( 'accessgroupsform', 'TableCLM' );
			$row->load((int)$cids[0]);
			$row->move(-1, 'name = '.$row->name);
			$row->reorder('name = '.$row->name);
		}
		return true;
	}
	
	function orderDown() {
		$cids = JRequest::getVar( 'cid', array(0),'post', 'array' );
				
		if(isset($cids[0])) {
			$row = JTable::getInstance( 'accessgroupsform', 'TableCLM' );
			$row->load((int)$cids[0]);
			$row->move(1, 'name = '.$row->name);
			$row->reorder('name = '.$row->name);
		}
		return true;
	}
	
	function copy() {
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$n		= count( $cids );

		$row = JTable::getInstance( 'accessgroupsform', 'TableCLM' );
		$rn = 0;
		if ($n > 0) {
			foreach ($cids as $id) {
				if ($row->load( (int)$id )) {
					$row->id			= 0;
					$row->name			= JText::_( 'COPY_OF' ).' '.$row->name;
					$row->usertype			= 'c_'.$row->usertype;
					$row->published		= 0;
					$row->kind			= 'USER';
					if (!$row->store()) {	
						$this->setError( $this->_db->getErrorMsg() );
						return false;
					} 	
				}
			}
		}

	if ($n >1) { $msg=JText::_( 'LIGEN_AKTION_ENTRYS' );}
		else {$msg=JText::_( 'LIGEN_AKTION_ENTRY' );}
	
	// Log schreiben
	$clmLog = new CLMLog();
	$clmLog->aktion = JText::_( 'ACCESSGROUP_COPIED_LOG' );
	$clmLog->params = array('sid' => clm_core::$access->getSeason(), 'cids' => $row->usertype);
	$clmLog->write();
	return true;
	}
	
} 
?>