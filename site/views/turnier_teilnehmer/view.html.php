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

class CLMViewTurnier_Teilnehmer extends JView {
	
	function display($tpl = null) {
		
		$config	= &JComponentHelper::getParams( 'com_clm' );
		
		$model		= &$this->getModel();
		
		$document =& JFactory::getDocument();
		
		$document->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js');
		$document->addScript(JURI::base().'components/com_clm/javascript/updateTableHeaders.js');
		
		// Title in Browser
		$headTitle = CLMText::composeHeadTitle( array( $model->turnier->name, JText::_('TOURNAMENT_PARTICIPANTLIST') ) );
		$document->setTitle( $headTitle );
		
		$this->assignRef('turnier', $model->turnier);

		$this->assignRef('tourn_linkclub', $config->get('tourn_linkclub', 1));

		$this->assignRef('players', $model->players);
		
		parent::display($tpl);
	
	}
	
}

?>
