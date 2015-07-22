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

class CLMViewTurnier_Rangliste extends JViewLegacy {
	
	function display($tpl = null) {
		
		$model		= $this->getModel();
		
		$document =JFactory::getDocument();
		
		$document->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js');
		$document->addScript(JURI::base().'components/com_clm/javascript/updateTableHeaders.js');
		
		// Title in Browser
		$headTitle = CLMText::composeHeadTitle( array( $model->turnier->name, JText::_('TOURNAMENT_RANKING') ) );
		$document->setTitle( $headTitle );
		
		$this->assignRef('turnier', $model->turnier);

		$this->assignRef('players', $model->players);
		$this->assignRef('posToPlayers', $model->posToPlayers);
		
		$this->assignRef('matches', $model->matches);
		$this->assignRef('matrix', $model->matrix);
		
		parent::display($tpl);
	
	}
}
?>
