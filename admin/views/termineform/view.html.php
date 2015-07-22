<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2014 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class CLMViewTermineForm extends JView {

	function display() {

		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   &$this->getModel();
		
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'admin_menue_images.php');
		
		if (JRequest::getVar( 'task') == 'edit') { 
			$text = JText::_( 'EDIT' );
		} else {
			$text = JText::_( 'NEW' );
		}
		JToolBarHelper::title( JText::_('TITLE_TERMINE').": ".JText::_('TERMINE_TASK').': <small><small>[ '. $text.' ]</small></small>', 'clm_headmenu_termine.png' );
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();

		// das MainMenu abschalten
		JRequest::setVar( 'hidemainmenu', 1 );

		// Document/Seite
		$document =& JFactory::getDocument();

		// JS-Array jtext -> Fehlertexte
		$document->addScriptDeclaration("var jserror = new Array();");
		$document->addScriptDeclaration("jserror['enter_name'] = '".JText::_('TERMINE_ERROR_NAME')."';");
		$document->addScriptDeclaration("jserror['enter_startdate'] = '".JText::_('TERMINE_ERROR_STARTDATE')."';");
		$document->addScriptDeclaration("jserror['dont_starttime'] = '".JText::_('TERMINE_ERROR_STARTTIME')."';");
		$document->addScriptDeclaration("jserror['dont_endtime'] = '".JText::_('TERMINE_ERROR_ENDTIME')."';");
		$document->addScriptDeclaration("jserror['dont_enddate'] = '".JText::_('TERMINE_ERROR_ENDDATE')."';");
		$document->addScriptDeclaration("jserror['dont_allday'] = '".JText::_('TERMINE_ERROR_ALLDAY')."';");
		$document->addScriptDeclaration("jserror['dont_noendtime'] = '".JText::_('TERMINE_ERROR_NOENDTIME')."';");
		
		// Daten an Template übergeben
		$this->assignRef('user', $model->user);
		
		$this->assignRef('termine', $model->termine);

		$this->assignRef('form', $model->form);


		parent::display();

	}

}
?>