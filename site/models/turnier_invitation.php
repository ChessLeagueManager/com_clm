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

defined('_JEXEC') or die();

jimport('joomla.application.component.model');
jimport( 'joomla.html.parameter' );

class CLMModelTurnier_Invitation extends JModelLegacy {
	
	
	function __construct() {
		
		parent::__construct();

		$this->turnierid = JRequest::getInt('turnier', 0);

		$this->_getTurnierData();

		
	}
	
	
	
	function _getTurnierData() {
	
		$query = "SELECT name, invitationText, published, id, params, catidAlltime, catidEdition"
			." FROM #__clm_turniere"
			." WHERE id = ".$this->turnierid
			;
		$this->_db->setQuery( $query );
		$this->turnier = $this->_db->loadObject();

		// turniernamen anpassen?
		$turParams = new clm_class_params($this->turnier->params);

		$addCatToName = $turParams->get('addCatToName', 0);
		if ($addCatToName != 0 AND ($this->turnier->catidAlltime > 0 OR $this->turnier->catidEdition > 0)) {
			$this->turnier->name = CLMText::addCatToName($addCatToName, $this->turnier->name, $this->turnier->catidAlltime, $this->turnier->catidEdition);
		}


	}
	
	

}
?>
