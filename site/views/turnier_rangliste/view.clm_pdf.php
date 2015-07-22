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

class CLMViewTurnier_Rangliste extends JViewLegacy
{
	function display($tpl = 'pdf')
	// Man beachte den Unterschied zum Standard View "$tpl = null" !!
	{
		$model	  = $this->getModel();
  		
		$this->assignRef('turnier', $model->turnier);

		$this->assignRef('players', $model->players);
		$this->assignRef('posToPlayers', $model->posToPlayers);
		
		$this->assignRef('matches', $model->matches);
		$this->assignRef('matrix', $model->matrix);
		
	// Dokumenttyp setzen
		$document =JFactory::getDocument();
		$document->setMimeEncoding('application/pdf');
		parent::display($tpl);
	}	
}
?>
