<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class CLMViewDewis extends JView {
	function display($tpl = null) {
		
		$app	= JFactory::getApplication();
		$jinput = $app->input;
		$task 	= $jinput->get('task', null, null);
		//$app->enqueueMessage( 'TASK VIEW : '.$task, 'warning');
 		$model = $this->getModel('dewis');
		//Toolbar
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'images'.DS.'admin_menue_images.php');
		JToolBarHelper::title( JText::_('DeWiS Daten & Update') ,'clm_headmenu_manager.png' );
		
		if($task ==''){
			$tpl = null;
			JToolBarHelper::custom( 'verein', 'upload.png', 'upload_f2.png', JText::_('Verein auslesen'), false);
			JToolBarHelper::custom( 'spieler_suchen', 'send.png', 'send_f2.png', JText::_('Spieler suchen'), false);
			JToolBarHelper::custom( 'turnier_suchen', 'send.png', 'send_f2.png', JText::_('Turnier suchen'), false);

		}
		
		
		if($task =='verein'){
			$tpl = $task;
			JToolBarHelper::custom( 'update_verein', 'upload.png', 'upload_f2.png', JText::_('Update Auswahl'), false);
			JToolBarHelper::custom( 'zurueck', 'back.png', 'back_f2.png', JText::_('Zurück zum Dewis'), false);

			$zps	= $jinput->get('zps', null, null);
				if(!$zps){
					$msg = 'Kein Verein gewählt';
					$link = 'index.php?option=com_clm&view=dewis';
					$app->redirect($link, $msg);
				}
			$data	= $model->dewis_verein($zps);
			$name	= $model->verein_name($zps);
			$this->assignRef( 'name', $name );
		}

		if($task =='update_verein'){
			$tpl = 'verein';
			$zps	= $jinput->get('zps', null, null);
					}
					
		if($task =='verein_detail'){
			$tpl = $task;
			JToolBarHelper::custom( 'zurueck', 'back.png', 'back_f2.png', JText::_('Zurück zum Dewis'), false);
			//$app->enqueueMessage( 'TASK VIEW : '.$task, 'warning');
			$data	= $model->verein_detail();
		}
	
		if($task =='spieler_suchen'){
			$tpl = $task;
			JToolBarHelper::custom( 'zurueck', 'back.png', 'back_f2.png', JText::_('Zurück zum Dewis'), false);

			$name	= $jinput->get('name', null, null);
			$data	= $model->spieler_suchen();

			$this->assignRef( 'name', $name );
		}
		
		
		if($task =='spieler_detail'){
			$tpl = $task;
			JToolBarHelper::custom( 'zurueck_spieler_suche', 'send.png', 'send_f2.png', JText::_('Zurück zur Suche'), false);
			JToolBarHelper::custom( 'zurueck', 'back.png', 'back_f2.png', JText::_('Zurück zum Dewis'), false);

			$name	= $jinput->get('name', null, null);
			$pkz	= $jinput->get('pkz', null, null);
			$data	= $model->spieler_detail();

			$this->assignRef( 'name', $name );
			$this->assignRef( 'pkz', $pkz );
		}


		if($task =='turnier_suchen'){
			$tpl = $task;
			JToolBarHelper::custom( 'zurueck', 'back.png', 'back_f2.png', JText::_('Zurück zum Dewis'), false);

			$sdatum	= $jinput->get('sdate', null, null);
			$edatum	= $jinput->get('edate', null, null);
			$turnier= $jinput->get('turnier', null, null);

			$data	= $model->turnier_suchen();
			
			$this->assignRef( 'sdatum', $sdatum );
			$this->assignRef( 'edatum', $edatum );
			$this->assignRef( 'turnier', $turnier );
		}
		
		if($task =='turnier_detail'){
			$tpl = $task;
			JToolBarHelper::custom( 'zurueck', 'back.png', 'back_f2.png', JText::_('Zurück zum Dewis'), false);
			
			$data	= $model->turnier_detail();
		}

		if($task =='turnier_auswertung'){
			$tpl = $task;
			JToolBarHelper::custom( 'zurueck', 'back.png', 'back_f2.png', JText::_('Zurück zum Dewis'), false);
			$data	= $model->turnier_auswertung();
		}
		

		// Vereinefilter laden
		$lists['vid']	= $model->vereine_filter();
		// Liga Filter laden
		$lists['lid']	= $model->liga_filter();
		
		// Daten an Template
		$this->assignRef( 'lists', $lists );
		$this->assignRef( 'data', $data );
		$this->assignRef( 'zps', $zps );

		$document = &JFactory::getDocument();  
		$document->addScript("includes/js/joomla.javascript.js");  
		JHTML::_('behavior.calendar');
		
		parent::display($tpl);
	}
	
}

?>