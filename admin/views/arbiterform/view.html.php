<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMViewArbiterForm extends JViewLegacy {

	function display($tpl = NULL) {

		$lang = clm_core::$lang->arbiter;
		
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		if (clm_core::$load->request_string( 'task') == 'edit') { 
			$text = $lang->arbiter_edit;
		} else { 
			$text = $lang->arbiter_create;
		}
		
		JToolBarHelper::title( $text );
		
		if (clm_core::$access->getType() == 'admin' OR clm_core::$access->getType() == 'tl') {
			JToolBarHelper::save( 'save' );
			JToolBarHelper::apply( 'apply' );
		}
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('cancel');

		// das MainMenu abschalten
		$_GET['hidemainmenu'] = 1;


		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();

		// Document/Seite
		$document =JFactory::getDocument();

		// JS-Array jtext -> Fehlertexte
		$document->addScriptDeclaration("var jserror = new Array();");
		$document->addScriptDeclaration("jserror['enter_fide'] = '".JText::_('PLEASE_ENTER')." ".JText::_('FIDE_ID')."';");
		$document->addScriptDeclaration("jserror['enter_name'] = '".JText::_('PLEASE_ENTER')." ".JText::_('ARBITER_NAME')."';");

		// Daten an Template übergeben
		$this->user = $model->user;
		
		$this->arbiter = $model->arbiter;

		$this->form = $model->form;

		// Auswahlfelder durchsuchbar machen
		clm_core::$load->load_js("suche_liste");

		parent::display();

	}

}
?>
