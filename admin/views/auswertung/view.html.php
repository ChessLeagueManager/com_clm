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

class CLMViewAuswertung extends JView {
	function display($tpl = null) {
		
		$app	= JFactory::getApplication();
		$jinput = $app->input;
		$task 	= $jinput->get('task', null, null);
 		$model	= $this->getModel('auswertung');

		//Toolbar
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'images'.DS.'admin_menue_images.php');
		JToolBarHelper::title( JText::_('Liga- und Turnierauswertung per DeWiS') ,'clm_headmenu_manager.png' );
		JToolBarHelper::custom( 'datei', 'upload.png', 'upload_f2.png', JText::_('Datei erstellen'), false);
		$tpl = null;
		
		if($task =='datei'){
			$liga	= $jinput->get('filter_lid', null, null);
			$format	= $jinput->get('filter_format', null, null);
			$et	= $jinput->get('filter_et', null, null);
			$mt	= $jinput->get('filter_mt', null, null);
			//$app->enqueueMessage( 'Format -> '.$format, 'warning');
			if(!$liga AND !$et AND !$mt){
				$app->enqueueMessage( 'Sie müssen eine Liga oder ein Turnier auswählen !', 'warning');
			}
			if(!$format AND $liga){
				$app->enqueueMessage( 'Kein Dateiformat gewählt !', 'warning');
			} 
			if(($liga !="0" AND $format !="0") OR ($et OR $mt) ){

				$data	= $model->datei();
			}
		}

		if($task =='delete'){
			$data	= $model->delete();
		}

		// Liga und Datei Filter laden
		$lists['lid']		= $model->liga_filter();
		$lists['et_lid']	= $model->turnier_filter();
		$lists['mt_lid']	= $model->mannschaftsturnier_filter();
		$lists['files']	= $model->xml_dateien();
		
		// Daten an Template
		$this->assignRef( 'lists', $lists );

		parent::display($tpl);
	}
}

?>