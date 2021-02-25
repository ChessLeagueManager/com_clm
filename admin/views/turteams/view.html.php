<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMViewTurTeams extends JViewLegacy {

	function display($tpl = NULL) {

		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();
		
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( $model->turnierData->name.": ".JText::_('EDIT_TEAMS'), 'clm_turnier.png'  );
	
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load( $model->turnierData->id ); // Daten zu dieser ID laden

		$clmAccess = clm_core::$access;
		if (($row->tl == clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') == 2) OR $clmAccess->access('BE_tournament_edit_detail') === true) {
			JToolBarHelper::save( 'save' );
			JToolBarHelper::apply( 'apply' );
		}
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();

		// das MainMenu abschalten
		$_REQUEST['hidemainmenu'] = 1;
		

		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();

		// Document/Seite
		$document =JFactory::getDocument();


		// Daten an Template übergeben
		$this->user = $model->user;
		
		$this->players = $model->playersData;
		$this->turnier = $model->turnierData;
		$this->teams = $model->teamData;

		$this->param = $model->param;


		parent::display();

	}

}
?>
