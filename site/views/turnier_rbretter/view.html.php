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
use Joomla\CMS\Uri\Uri;

class CLMViewTurnier_RBretter extends JViewLegacy {
	
	function display($tpl = null) {
		
		$config = clm_core::$db->config();
		
		$model		= $this->getModel();


		$document =Factory::getDocument();
		
		// Title in Browser
		$headTitle = CLMText::composeHeadTitle( array( $model->turnier->name, Text::_('Brettzuordnung')." ".$model->round->nr ) );
		$document->setTitle( $headTitle );
		
		$this->turnier = $model->turnier;
		
		$this->displayTlOK = $model->displayTlOK;

		$this->round = $model->round;
		
		$this->matches = $model->matches;
		$this->points = $model->points;
		
		// zusätzliche Funktionalitäten
		require_once (JPATH_COMPONENT . DS . 'includes' . DS . 'tooltip.php');
		
		
		parent::display($tpl);
	
	}
	
}
?>
