<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2017 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

class CLMModelSonderranglistenCopy extends JModelLegacy {
	var $_turniere;
 
	function __construct() { 
		parent::__construct(); 
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
	
	
} 
?>