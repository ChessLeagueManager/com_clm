<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMViewAuswertung extends JViewLegacy {
	function display($tpl = null) {
		
		$app	= JFactory::getApplication();
		$jinput = $app->input;
		$task 	= $jinput->get('task', null, null);
 		$model	= $this->getModel('auswertung');

		//Toolbar
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( JText::_('DB_RATING_TITLE') ,'clm_headmenu_manager.png' );
		$tpl = null;
		
		if($task =='datei'){
			$liga	= $jinput->get('filter_lid', null, null);
			$format	= $jinput->get('filter_format', null, null);
			$et	= $jinput->get('filter_et', null, null);
			$mt	= $jinput->get('filter_mt', null, null);
			if(!$liga AND !$et AND !$mt){
				$app->enqueueMessage( JText::_('DB_RATING_SELECT'), 'warning');
			}
			if(!$format AND $liga){
				$app->enqueueMessage( JText::_('DB_RATING_FORMAT'), 'warning');
			} 
			if(($liga !="0" AND $format !="0") OR ($et OR $mt) ){

				$data	= $model->datei();
			}
		}

		if($task =='delete'){
			$data	= $model->delete();
		}
		if($task =='download'){
			$data	= $model->download();
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
