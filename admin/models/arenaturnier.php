<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanaager.de
*/
defined('_JEXEC') or die('Restricted access');

class CLMModelArenaTurnier extends JModelLegacy {

	var $_saisons;
	var $_turniere;
	
	function __construct(){
		parent::__construct();
		
		$filter_saison	= clm_core::$load->request_int( 'filter_saison' , $this->_getAktuelleSaison() );
		
		$this->setState( 'filter_saison' , $filter_saison );
	}
	
	function getSaisons() {
		if (empty( $this->_saisons )) { 
			$query =  ' SELECT id, name 
						FROM #__clm_saison
						WHERE id = '.$this->getState( 'filter_saison' ).'';
			$this->_saisons = $this->_getList( $query );
		} 
		return $this->_saisons;
	}
	
	function getTurniere() {
		$arena	= clm_core::$load->request_string( 'arena' );
		$group = false;
		if (empty( $this->_turniere )) { 
			if ($group) {
				$query =  ' SELECT id, name, bem_int FROM #__clm_liga ' 
						.' WHERE sid = '.$this->getState( 'filter_saison' ).'';
			} else {	
				$query =  ' SELECT id, name, bem_int FROM #__clm_turniere ' 
						.' WHERE sid = '.$this->getState( 'filter_saison' ).'';
			}
			$this->_turniere = $this->_getList( $query );
		} 
		return $this->_turniere;
	}
	
	function _getAktuelleSaison() {
		if (empty( $this->_aktuelleSaison )) { 
		
			$query =  ' SELECT 
							id,
							name,
							published
						FROM 
							#__clm_saison 
						WHERE
							published = 1 AND archiv = 0';
			$var = $this->_getList( $query );
		} 
		return $var[0]->id;
	}
	
}

?>