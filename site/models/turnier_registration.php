<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2019 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/

defined('_JEXEC') or die();

jimport('joomla.application.component.model');
jimport( 'joomla.html.parameter' );

class CLMModelTurnier_Registration extends JModelLegacy {
	
	
	function __construct() {
		
		parent::__construct();

		$this->turnierid = clm_core::$load->request_int('turnier');

		$this->_getTurnierData();

	}
	
	
	
	function _getTurnierData() {
	
		$query = "SELECT t.*, CHAR_LENGTH(t.invitationText) AS invitationLength, s.name AS saisonname, u.name AS tlname, u.email AS tlemail"
			." FROM #__clm_turniere AS t"
			." LEFT JOIN #__clm_saison AS s ON s.id = t.sid"
			." LEFT JOIN #__clm_user AS u ON u.jid = t.tl AND u.sid = t.sid"
			." WHERE t.id = ".$this->turnierid
			;
		$this->_db->setQuery( $query );
		$this->turnier = $this->_db->loadObject();

		// Ausrichter
		$this->turnier->organame = clm_core::$load->zps_to_district($this->turnier->vereinZPS);


		// turniernamen anpassen?
		$turParams = new clm_class_params($this->turnier->params);


		$addCatToName = $turParams->get('addCatToName', 0);

		if ($addCatToName != 0 AND ($this->turnier->catidAlltime > 0 OR $this->turnier->catidEdition > 0)) {
			$this->turnier->name = CLMText::addCatToName($addCatToName, $this->turnier->name, $this->turnier->catidAlltime, $this->turnier->catidEdition);
		}

	}
	

}
?>
