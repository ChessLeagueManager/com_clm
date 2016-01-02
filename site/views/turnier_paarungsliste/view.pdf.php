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

class CLMViewTurnier_Paarungsliste extends JViewLegacy
{
	function display($tpl = 'pdf')
	// Man beachte den Unterschied zum Standard View "$tpl = null" !!
	{
		$config = clm_core::$db->config();
		$model	  = $this->getModel();
  		
		$this->assignRef('turnier', $model->turnier);
		
		$this->assignRef('pgnShow', $model->pgnShow);
		$this->assignRef('displayTlOK', $model->displayTlOK);

		$this->assignRef('players', $model->players);
		
		$this->assignRef('rounds', $model->rounds);
		
		$this->assignRef('matches', $model->matches);
		$this->assignRef('points', $model->points);
		
	// Dokumenttyp setzen
		$document =JFactory::getDocument();
		$document->setMimeEncoding('application/pdf');
		parent::display($tpl);
	}	
}
?>
