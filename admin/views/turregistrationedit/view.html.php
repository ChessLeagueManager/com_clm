<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMViewTurRegistrationEdit extends JViewLegacy {

	function display($tpl = NULL) {

		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();
		
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( $model->turnierData->name.", ".JText::_('REGISTRATION').": ".$model->registrationData->name, 'clm_turnier.png'  );
	
		// Instanz der Tabelle
		$row = JTable::getInstance( 'registrations', 'TableCLM' );
		$row->load( $model->registrationData->id ); // Daten zu dieser Registration-ID laden
		$rowt = JTable::getInstance( 'turniere', 'TableCLM' );
		$rowt->load( $model->registrationData->tid ); // Daten zu dieser Turnier-ID laden

		$clmAccess = clm_core::$access;
		if (($rowt->tl == clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') == 2) OR $clmAccess->access('BE_tournament_edit_detail') === true) {
			JToolBarHelper::save( 'save' );
			JToolBarHelper::apply( 'apply' );
			JToolBarHelper::custom('copy_to', 'copy.png', 'copy_f2.png', JText::_('REGISTRATION_COPY_TO'),false);
		}
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();

		// das MainMenu abschalten
		$_GET['hidemainmenu'] = 1;
		

		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();

		// Document/Seite
		$document =JFactory::getDocument();

		// JS-Array jtext -> Fehlertexte
		$document->addScriptDeclaration("var jtext = new Array();");
		$document->addScriptDeclaration("jtext['enter_name'] = '".JText::_('PLEASE_ENTER')." ".JText::_('PLAYER_NAME')."';");
		$document->addScriptDeclaration("jtext['enter_birthyear'] = '".JText::_('PLEASE_ENTER')." ".JText::_('PLAYER_BIRTH_YEAR')."';");
		$document->addScriptDeclaration("jtext['number_birthyear'] = '".JText::_('PLEASE_NUMBER')." ".JText::_('PLAYER_BIRTH_YEAR')."';");
		$document->addScriptDeclaration("jtext['enter_club'] = '".JText::_('PLEASE_ENTER')." ".JText::_('PLAYER_NAME')."';");
		$document->addScriptDeclaration("jtext['enter_email'] = '".JText::_('PLEASE_ENTER')." ".JText::_('CLUB')."';");
		$document->addScriptDeclaration("jtext['number_dwz'] = '".JText::_('PLEASE_NUMBER')." ".JText::_('RATING')."';");
		$document->addScriptDeclaration("jtext['number_dwzindex'] = '".JText::_('PLEASE_NUMBER')." ".JText::_('RATING_INDEX')."';");
		$document->addScriptDeclaration("jtext['number_elo'] = '".JText::_('PLEASE_NUMBER')." ".JText::_('FIDE_ELO')."';");
		$document->addScriptDeclaration("jtext['number_fideid'] = '".JText::_('PLEASE_NUMBER')." ".JText::_('PLAYER_FIDE_ID')."';");
		$document->addScriptDeclaration("jtext['number_mglnr'] = '".JText::_('PLEASE_NUMBER')." ".JText::_('PLAYER_MGLNR')."';");

		// Script
		$document->addScript(CLM_PATH_JAVASCRIPT.'turregistrationedit.js');


		// Daten an Template übergeben
		$this->user = $model->user;
		
		$this->registration = $model->registrationData;
		$this->turnier = $model->turnierData;
		$this->snrmax = $model->turnierSnrMax->snrmax;

		$this->param = $model->param;


		parent::display();

	}

}
?>
