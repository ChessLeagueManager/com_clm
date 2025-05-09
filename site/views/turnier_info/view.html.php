<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
jimport( 'joomla.application.component.view');

class CLMViewTurnier_Info extends JViewLegacy {
	
	function display($tpl = null) {
		
		$model		= $this->getModel();
		
		$document =JFactory::getDocument();
		
		// Title in Browser
		$headTitle = CLMText::composeHeadTitle( array( $model->turnier->name, JText::_('TOURNAMENT_INFO') ) );
		$document->setTitle( $headTitle );
		
		$this->turnier = $model->turnier;
		if (isset($model->matchStats))
			$this->matchStats = $model->matchStats;

		$arbiter     = $model->getCLMArbiter();
		$this->arbiter = $arbiter;
		parent::display($tpl);
	
	}
	
}

?>
