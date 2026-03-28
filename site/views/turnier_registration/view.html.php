<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
jimport( 'joomla.application.component.view');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class CLMViewTurnier_Registration extends JViewLegacy {
	
	function display($tpl = null) {
		
		$model		= $this->getModel();
		
		$document =Factory::getDocument();
		
		// Title in Browser
		$headTitle = CLMText::composeHeadTitle( array( $model->turnier->name, Text::_('TOURNAMENT_INFO') ) );
		$document->setTitle( $headTitle );
		
		$this->turnier = $model->turnier;

		parent::display($tpl);
	
	}
	
}

?>
