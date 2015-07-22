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

jimport( 'joomla.application.component.view');

class CLMViewTurnier_Tabelle extends JViewLegacy
{
	function display($tpl = 'pdf')
	// Man beachte den Unterschied zum Standard View "$tpl = null" !!
	{
		$config = clm_core::$db->config();
		$model	  = $this->getModel();
  		
		$this->assignRef('turnier', $model->turnier);

		$tourn_linkclub = $config->tourn_linkclub;
		$this->assignRef('tourn_linkclub', $tourn_linkclub);

		$this->assignRef('players', $model->players);
		
	// Dokumenttyp setzen
		$document =JFactory::getDocument();
		$document->setMimeEncoding('application/pdf');
		parent::display($tpl);
	}	
}
?>
