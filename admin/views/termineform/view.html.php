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

class CLMViewTermineForm extends JViewLegacy {

	function display($tpl = NULL) {

		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();
		
		clm_core::$load->load_css("icons_images");
		
		if (clm_core::$load->request_string('task', '') == 'edit') { 
			$text = Text::_( 'EDIT' );
		} else {
			$text = Text::_( 'NEW' );
		}
		ToolBarHelper::title( Text::_('TITLE_TERMINE').": ".Text::_('TERMINE_TASK').': [ '. $text.' ]', 'clm_headmenu_termine.png' );
		ToolBarHelper::save();
		ToolBarHelper::apply();
		ToolBarHelper::spacer();
		ToolBarHelper::cancel();

		// das MainMenu abschalten
		$_GET['hidemainmenu'] = 1 ;

		// Document/Seite
		$document =Factory::getDocument();

		// JS-Array jtext -> Fehlertexte
		$document->addScriptDeclaration("var jserror = new Array();");
		$document->addScriptDeclaration('jserror["enter_name"] = "'.Text::_("TERMINE_ERROR_NAME").'";');
		$document->addScriptDeclaration('jserror["enter_startdate"] = "'.Text::_("TERMINE_ERROR_STARTDATE").'";');
		$document->addScriptDeclaration('jserror["dont_starttime"] = "'.Text::_("TERMINE_ERROR_STARTTIME").'";');
		$document->addScriptDeclaration('jserror["dont_endtime"] = "'.Text::_("TERMINE_ERROR_ENDTIME").'";');
		$document->addScriptDeclaration('jserror["dont_enddate"] = "'.Text::_("TERMINE_ERROR_ENDDATE").'";');
		$document->addScriptDeclaration('jserror["enddate_wrong"] = "'.Text::_("TERMINE_ERROR_ENDDATE_WRONG").'";');
		$document->addScriptDeclaration('jserror["dont_allday"] = "'.Text::_("TERMINE_ERROR_ALLDAY").'";');
		$document->addScriptDeclaration('jserror["dont_noendtime"] = "'.Text::_("TERMINE_ERROR_NOENDTIME").'";');

		// Script
		$document->addScript(CLM_PATH_JAVASCRIPT.'termineform.js');
		
		// Auswahlfelder durchsuchbar machen
		clm_core::$load->load_js("suche_liste");

		// Daten an Template übergeben
		$this->user = $model->user;		
		$this->termine = $model->termine;
		$this->form = $model->form;


		parent::display();

	}

}
?>
