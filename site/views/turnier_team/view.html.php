<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
jimport( 'joomla.application.component.view');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

class CLMViewTurnier_Team extends JViewLegacy {
	
	function display($tpl = null) {
		
		$config	= clm_core::$db->config();
		
		$model		= $this->getModel();
		
		$document =Factory::getDocument();
		
//		$document->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js');
		clm_core::$cms->addScript(clm_core::$url."includes/jquery-3.7.1.min.js");
		$document->addScript(URI::base().'components/com_clm/javascript/updateTableHeaders.js');
		
		// Title in Browser
		$headTitle = CLMText::composeHeadTitle( array( $model->turnier->name, Text::_('TOURNAMENT_TABLE') ) );
		$document->setTitle( $headTitle );
		
		$this->turnier = $model->turnier;
		$tourn_linkclub=$config->tourn_linkclub;
		$this->tourn_linkclub = $tourn_linkclub;
		$this->players = $model->players;
		$this->a_teams = $model->a_teams;
		
		parent::display($tpl);
	
	}
}
?>
