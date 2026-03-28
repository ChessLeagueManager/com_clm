<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

class CLMViewArbiterForm extends JViewLegacy {

	function display($tpl = NULL) {

		$lang = clm_core::$lang->arbiter;
		
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		if (clm_core::$load->request_string( 'task') == 'edit') { 
			$text = $lang->arbiter_edit;
		} else { 
			$text = $lang->arbiter_create;
		}
		
		ToolBarHelper::title( $text );
		
		if (clm_core::$access->getType() == 'admin' OR clm_core::$access->getType() == 'tl') {
			ToolBarHelper::save( 'save' );
			ToolBarHelper::apply( 'apply' );
		}
		ToolBarHelper::spacer();
		ToolBarHelper::cancel('cancel');

		// das MainMenu abschalten
		$_GET['hidemainmenu'] = 1;


		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();

		// Document/Seite
		$document =Factory::getDocument();

		// JS-Array jtext -> Fehlertexte
		$document->addScriptDeclaration("var jserror = new Array();");
		$document->addScriptDeclaration("jserror['enter_fide'] = '".Text::_('PLEASE_ENTER')." ".Text::_('FIDE_ID')."';");
		$document->addScriptDeclaration("jserror['enter_name'] = '".Text::_('PLEASE_ENTER')." ".Text::_('ARBITER_NAME')."';");

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
