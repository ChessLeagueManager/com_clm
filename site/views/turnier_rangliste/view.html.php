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

class CLMViewTurnier_Rangliste extends JViewLegacy {
	
	function display($tpl = null) {
		
		$model		= $this->getModel();
		
		$document =Factory::getDocument();
		
//		$document->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js');
		clm_core::$cms->addScript(clm_core::$url."includes/jquery-3.7.1.min.js");
		$document->addScript(URI::base(true).'components/com_clm/javascript/updateTableHeaders.js');
		
		// Title in Browser
		$headTitle = CLMText::composeHeadTitle( array( $model->turnier->name, Text::_('TOURNAMENT_RANKING') ) );
		$document->setTitle( $headTitle );
		
		$this->turnier = $model->turnier;

		$this->players = $model->players;
		$this->posToPlayers = $model->posToPlayers;
		
		$this->matches = $model->matches;
		$this->matrix = $model->matrix;
		
		parent::display($tpl);
	
	}
}
?>
