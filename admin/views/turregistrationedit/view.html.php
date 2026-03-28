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
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Toolbar\ToolbarHelper;

class CLMViewTurRegistrationEdit extends JViewLegacy {

	function display($tpl = NULL) {

		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();
		
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		clm_core::$load->load_css("icons_images");
		ToolBarHelper::title( $model->turnierData->name.", ".Text::_('REGISTRATION').": ".$model->registrationData->name, 'clm_turnier.png'  );
	
		// Instanz der Tabelle
		$row = Table::getInstance( 'registrations', 'TableCLM' );
		$row->load( $model->registrationData->id ); // Daten zu dieser Registration-ID laden
		$rowt = Table::getInstance( 'turniere', 'TableCLM' );
		$rowt->load( $model->registrationData->tid ); // Daten zu dieser Turnier-ID laden

		$clmAccess = clm_core::$access;
		if (($rowt->tl == clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') == 2) OR $clmAccess->access('BE_tournament_edit_detail') === true) {
			ToolBarHelper::save( 'save' );
			ToolBarHelper::apply( 'apply' );
			ToolBarHelper::custom('copy_to', 'copy.png', 'copy_f2.png', Text::_('REGISTRATION_COPY_TO'),false);
		}
		ToolBarHelper::spacer();
		ToolBarHelper::cancel();

		// das MainMenu abschalten
		$_GET['hidemainmenu'] = 1;
		

		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();

		// Document/Seite
		$document =Factory::getDocument();

		// JS-Array jtext -> Fehlertexte
		$document->addScriptDeclaration("var jtext = new Array();");
		$document->addScriptDeclaration("jtext['enter_name'] = '".Text::_('PLEASE_ENTER')." ".Text::_('PLAYER_NAME')."';");
		$document->addScriptDeclaration("jtext['enter_birthyear'] = '".Text::_('PLEASE_ENTER')." ".Text::_('PLAYER_BIRTH_YEAR')."';");
		$document->addScriptDeclaration("jtext['number_birthyear'] = '".Text::_('PLEASE_NUMBER')." ".Text::_('PLAYER_BIRTH_YEAR')."';");
		$document->addScriptDeclaration("jtext['enter_club'] = '".Text::_('PLEASE_ENTER')." ".Text::_('PLAYER_NAME')."';");
		$document->addScriptDeclaration("jtext['enter_email'] = '".Text::_('PLEASE_ENTER')." ".Text::_('CLUB')."';");
		$document->addScriptDeclaration("jtext['number_dwz'] = '".Text::_('PLEASE_NUMBER')." ".Text::_('RATING')."';");
		$document->addScriptDeclaration("jtext['number_dwzindex'] = '".Text::_('PLEASE_NUMBER')." ".Text::_('RATING_INDEX')."';");
		$document->addScriptDeclaration("jtext['number_elo'] = '".Text::_('PLEASE_NUMBER')." ".Text::_('FIDE_ELO')."';");
		$document->addScriptDeclaration("jtext['number_fideid'] = '".Text::_('PLEASE_NUMBER')." ".Text::_('PLAYER_FIDE_ID')."';");
		$document->addScriptDeclaration("jtext['number_mglnr'] = '".Text::_('PLEASE_NUMBER')." ".Text::_('PLAYER_MGLNR')."';");

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
