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
use Joomla\CMS\Toolbar\ToolbarHelper;

class CLMViewTurRoundForm extends JViewLegacy {

	function display($tpl = NULL) {

		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();

		// das MainMenu abschalten
		$app = Factory::getApplication();
		$app->input->set('hidemainmenu', 1);
		
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		clm_core::$load->load_css("icons_images");
		ToolBarHelper::title( $model->turnierData->name.", ".Text::_('ROUND').": ".$model->roundData->name, 'clm_turnier.png'  );
	
		ToolBarHelper::save();
		ToolBarHelper::apply();
		ToolBarHelper::spacer();
		ToolBarHelper::cancel();

		
		// Document/Seite
		$document =Factory::getDocument();

		// JS-Array jtext -> Fehlertexte
		$document->addScriptDeclaration("var jtext = new Array();");
		$document->addScriptDeclaration("jtext['enter_name'] = '".Text::_('PLEASE_ENTER')." ".Text::_('ROUND_NAME')."';");
		$document->addScriptDeclaration("jtext['enter_nr'] = '".Text::_('PLEASE_ENTER')." ".Text::_('ROUND_NR')."';");
		$document->addScriptDeclaration("jtext['number_nr'] = '".Text::_('PLEASE_NUMBER')." ".Text::_('ROUND_NR')."';");
		$document->addScriptDeclaration("jtext['enter_date'] = '".Text::_('PLEASE_ENTER')." ".Text::_('JDATE')."';");

		// Script
		$document->addScript(CLM_PATH_JAVASCRIPT.'turroundform.js');


		// Daten an Template übergeben
		$this->user = $model->user;
		
		$this->roundData = $model->roundData;
		$this->turnierData = $model->turnierData;
		$document->addScriptDeclaration("dateStart = '".$this->turnierData->dateStart."';");
		$document->addScriptDeclaration("dateEnd = '".$this->turnierData->dateEnd."';");

		$this->param = $model->param;

		// zusätzliche Funktionalitäten
//		HTMLHelper::_('behavior.tooltip');
		require_once (JPATH_COMPONENT_SITE . DS . 'includes' . DS . 'tooltip.php');


		parent::display();

	}

}
?>
