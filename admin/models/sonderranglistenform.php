<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

class CLMModelSonderranglistenForm extends JModelLegacy {
	var $_sonderrangliste;
	var $_ordering;
	var $_turniere;
	var $_saisons;
 
	function __construct() { 
		parent::__construct(); 
		$array = JRequest::getVar('cid',  0, '', 'array'); 
		$this->setId(clm_escape($array[0])); 
	} 
	
	function setId($id) { 
		$id1 = JRequest::getInt('id', 0);
		//echo "<br>id:$id  id1:$id1"; 
		if ($id == 0 AND $id1 > 0) $id = $id1; //die();
		$this->_id = $id; 
		$this->_sonderrangliste = null; 
	}
	
	function getSonderrangliste() { 
		if (empty( $this->_sonderrangliste )) { 
			$query = ' SELECT sl.*, t.tl FROM #__clm_turniere_sonderranglisten as sl '
					.' LEFT JOIN #__clm_turniere as t ON t.id = sl.turnier '
					.' WHERE sl.id = '.$this->_id; 
			$this->_db->setQuery( $query ); 
			$this->_sonderrangliste = $this->_db->loadObject(); 
		} 
		if (!$this->_sonderrangliste) { 
			$this->_sonderrangliste= new stdClass(); 
			$this->_sonderrangliste->id						= 0;
			$this->_sonderrangliste->turnier					= null;
			$this->_sonderrangliste->name						= '';
			$this->_sonderrangliste->use_rating_filter 		= false;
			$this->_sonderrangliste->rating_type				= 1;
			$this->_sonderrangliste->rating_higher_than 		= 0;
			$this->_sonderrangliste->rating_lower_than 		= 0;
			$this->_sonderrangliste->use_birthYear_filter		= false;
			$this->_sonderrangliste->birthYear_younger_than	= 0;
			$this->_sonderrangliste->birthYear_older_than		= 0;
			$this->_sonderrangliste->use_sex_filter			= false;
			$this->_sonderrangliste->sex						= '';
			$this->_sonderrangliste->use_sex_year_filter		= false;
			$this->_sonderrangliste->maleYear_younger_than		= 0;
			$this->_sonderrangliste->maleYear_older_than		= 0;
			$this->_sonderrangliste->femaleYear_younger_than	= 0;
			$this->_sonderrangliste->femaleYear_older_than		= 0;
			$this->_sonderrangliste->use_zps_filter 		= false;
			$this->_sonderrangliste->zps_higher_than 		= '';
			$this->_sonderrangliste->zps_lower_than 		= 'ZZZZZ';			
			$this->_sonderrangliste->published					= 0;
			$this->_sonderrangliste->checked_out				= 0;
			$this->_sonderrangliste->checked_out_time			= 0;
			$this->_sonderrangliste->ordering					= null;		
			$this->_sonderrangliste->tl						= 0;		
			
			//Wird nicht gespeichert aber mit berechnet
			$this->_sonderrangliste->sid					= $this->_getAktuelleSaison();
		} else {
			$this->_sonderrangliste->sid					= $this->_getTurnierSaison($this->_sonderrangliste->turnier);
		} 
		return $this->_sonderrangliste; 
	}
	
	function getOrdering() {
		if (empty( $this->_ordering )) { 
			if(!empty( $this->_sonderrangliste->turnier )) {
				$query = ' 	SELECT
								id,
								ordering,
								name
							FROM 
								#__clm_turniere_sonderranglisten
							WHERE
								turnier = '.$this->_sonderrangliste->turnier.'
							ORDER BY
								ordering ASC';
				$this->_ordering = $this->_getList( $query );	
			}
			else {
				$this->_ordering = null;
			}
		}
		return $this->_ordering; 
	}
	
	function getTurniere() {
		if (empty( $this->_turniere )) { 
			$query = ' 	SELECT
							t.id as id,
							t.name as name,
							s.id as sid,
							s.name as sname
						FROM 
							#__clm_turniere as t, #__clm_saison as s
						WHERE
							s.id = t.sid
						ORDER BY
							sname DESC, t.ordering ASC';
			$this->_turniere = $this->_getList( $query );	
		}
		return $this->_turniere;
	}
	
	function getSaisons() {
		if (empty( $this->_saisons )) { 
			$query = ' 	SELECT
							id,
							name
						FROM 
							#__clm_saison
						ORDER BY
							name DESC;';
			$this->_saisons = $this->_getList( $query );	
		}
		return $this->_saisons;
	}
	
	function _getAktuelleSaison() {
		$db = $this->getDbo();

		$query = ' 	SELECT
						id
					FROM 
						#__clm_saison
					WHERE
						published = 1
					ORDER BY
						name DESC;';
		$db->setQuery( $query );
		return $db->loadObject()->id;	
	}
 
	function _getTurnierSaison($tid) {
		$db = $this->getDbo();
			$query = ' 	SELECT
							sid
						FROM 
							#__clm_turniere
						WHERE
							id = '.$tid.';';
		$db->setQuery( $query );
		return $db->loadObject()->sid;	
	}
	
	function store() { 
		$row = JTable::getInstance( 'sonderranglistenform', 'TableCLM' );
		$sonderrangliste = JRequest::get( 'post' ); 
		if (!$row->bind($sonderrangliste)) { 
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
//		if(!$row->reorderAll()) {
//			$this->setError( $this->_db->getErrorMsg() );
//			return false;
//		}
		return true;
	}
	
	function delete() { 
		$cids = JRequest::getVar( 'cid', array(0),'post', 'array' ); 
		$row = JTable::getInstance( 'sonderranglistenform', 'TableCLM' );
		if (count( $cids )) { 
			foreach($cids as $cid) { 
				if (!$row->delete( $cid )) { 
					$this->setError( $row->_db->getErrorMsg() ); 
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
	
	function publish() {
		$cids = JRequest::getVar( 'cid', array(0),'post', 'array' );
		$row = JTable::getInstance( 'sonderranglistenform', 'TableCLM' );
		if (!$row->publish( $cids )) { 
			$this->setError( $row->_db->getErrorMsg() ); 
			return false; 
		}
		return true; 
	} 
	
	function unpublish() {
		$cids = JRequest::getVar( 'cid', array(0),'post', 'array' );
		$row = JTable::getInstance( 'sonderranglistenform', 'TableCLM' );

		if (!$row->publish($cids,0)) { 
			$this->setError( $row->_db->getErrorMsg() ); 
			return false; 
		}
		return true; 
	} 
	
	function saveOrder() {
		$cids = JRequest::getVar( 'cid', array(0),'post', 'array' );
		$order = JRequest::getVar('order', array (0), 'post', 'array');
		$row = JTable::getInstance( 'sonderranglistenform', 'TableCLM' );
		//var_dump(	$cids);
		
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
		$row = JTable::getInstance( 'sonderranglistenform', 'TableCLM' );
			$row->load((int)$cids[0]);
			$row->move(-1, 'turnier = '.$row->turnier);
			$row->reorder('turnier = '.$row->turnier);
		}
		return true;
	}
	
	function orderDown() {
		$cids = JRequest::getVar( 'cid', array(0),'post', 'array' );
				
		if(isset($cids[0])) {
		$row = JTable::getInstance( 'sonderranglistenform', 'TableCLM' );
			$row->load((int)$cids[0]);
			$row->move(1, 'turnier = '.$row->turnier);
			$row->reorder('turnier = '.$row->turnier);
		}
		return true;
	}
	
	
} 
?>