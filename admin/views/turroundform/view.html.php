<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMViewTurRoundForm extends JViewLegacy {

	function display($tpl = NULL) {

		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();

		// das MainMenu abschalten
		$app = JFactory::getApplication();
		$app->input->set('hidemainmenu', 1);
		
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( $model->turnierData->name.", ".JText::_('ROUND').": ".$model->roundData->name, 'clm_turnier.png'  );
	
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();

		
		// Document/Seite
		$document =JFactory::getDocument();

		// JS-Array jtext -> Fehlertexte
		$document->addScriptDeclaration("var jtext = new Array();");
		$document->addScriptDeclaration("jtext['enter_name'] = '".JText::_('PLEASE_ENTER')." ".JText::_('ROUND_NAME')."';");
		$document->addScriptDeclaration("jtext['enter_nr'] = '".JText::_('PLEASE_ENTER')." ".JText::_('ROUND_NR')."';");
		$document->addScriptDeclaration("jtext['number_nr'] = '".JText::_('PLEASE_NUMBER')." ".JText::_('ROUND_NR')."';");
		$document->addScriptDeclaration("jtext['enter_date'] = '".JText::_('PLEASE_ENTER')." ".JText::_('JDATE')."';");

		// Script
		$document->addScript(CLM_PATH_JAVASCRIPT.'turroundform.js');


		// Daten an Template übergeben
		$this->user = $model->user;
		
		$this->roundData = $model->roundData;

		$this->param = $model->param;

		// zusätzliche Funktionalitäten
//		JHtml::_('behavior.tooltip');
		require_once (JPATH_COMPONENT_SITE . DS . 'includes' . DS . 'tooltip.php');


		parent::display();

	}

}
?>
