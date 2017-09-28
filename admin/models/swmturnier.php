<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2017 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanaager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

class CLMModelSWMTurnier extends JModelLegacy {

	var $_saisons;
	var $_turniere;
	
	function __construct(){
		parent::__construct();
		
		$filter_saison	= JRequest::getVar( 'filter_saison' , $this->_getAktuelleSaison() , 'default' , 'int' );
		
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
		if (empty( $this->_turniere )) { 
			$query =  ' SELECT id, name, bem_int
						FROM 
							#__clm_turniere 
						WHERE 
							sid = '.$this->getState( 'filter_saison' ).'';
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