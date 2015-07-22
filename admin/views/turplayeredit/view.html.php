<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMViewTurPlayerEdit extends JViewLegacy {

	function display($tpl = NULL) {

		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();
		
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( $model->turnierData->name.", ".JText::_('PLAYER').": ".$model->playerData->name, 'clm_turnier.png'  );
	
		// Instanz der Tabelle
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load( $model->playerData->turnier ); // Daten zu dieser ID laden

		$clmAccess = clm_core::$access;
		if (($row->tl == clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') == 2) OR $clmAccess->access('BE_tournament_edit_detail') === true) {
			JToolBarHelper::save( 'save' );
			JToolBarHelper::apply( 'apply' );
		}
		JToolBarHelper::spacer();
		//JToolBarHelper::cancel('cancel', JText::_('CLOSE'));
		JToolBarHelper::cancel();

		// das MainMenu abschalten
		JRequest::setVar( 'hidemainmenu', 1 );
		

		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();

		// Document/Seite
		$document =JFactory::getDocument();

		// JS-Array jtext -> Fehlertexte
		$document->addScriptDeclaration("var jtext = new Array();");
		$document->addScriptDeclaration("jtext['enter_name'] = '".JText::_('PLEASE_ENTER')." ".JText::_('PLAYER_NAME')."';");
		$document->addScriptDeclaration("jtext['enter_twz'] = '".JText::_('PLEASE_ENTER')." ".JText::_('TWZ')."';");
		$document->addScriptDeclaration("jtext['number_twz'] = '".JText::_('PLEASE_NUMBER')." ".JText::_('TWZ')."';");

		// Script
		$document->addScript(CLM_PATH_JAVASCRIPT.'turplayeredit.js');


		// Daten an Template übergeben
		$this->assignRef('user', $model->user);
		
		$this->assignRef('player', $model->playerData);
		$this->assignRef('turnier', $model->turnierData);

		$this->assignRef('param', $model->param);


		parent::display();

	}

}
?>
