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

class CLMViewDB extends JViewLegacy {

	function display($tpl = null) {
		$app	= JFactory::getApplication();
		$jinput = $app->input;
		$task 	= $jinput->get('task', null, null);
		$tpl = null;
		//$app->enqueueMessage( 'TASK VIEW : '.$task, 'warning');
 		$model = $this->getModel('db');
		$id = JRequest::getVar( 'id');
		
		// Menubilder laden
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title(   JText::_( 'TITLE_DATABASE' ), 'clm_headmenu_datenbank.png' );

		$clmAccess = clm_core::$access;
		if ( $clmAccess->access('BE_database_general') ) {
			//JToolBarHelper::custom('convert_db','refresh.png','refresh_f2.png',JTEXT::_('DB_BUTTON_ADAPT'),false);
			JToolBarHelper::custom('export','download.png','download_f2.png',JTEXT::_('DB_BUTTON_EXPORT'),false);
			JToolBarHelper::custom('import','upload.png','upload_f2.png',JTEXT::_('DB_BUTTON_IMPORT'),false);
			JToolBarHelper::custom('delete','delete.png','delete_f2.png',JTEXT::_('DB_BUTTON_DEL'),false);
			JToolBarHelper::custom('upload','upload.png','upload_f2.png',JTEXT::_('DB_BUTTON_FILE_UPLOAD'),false);

		}
		JToolBarHelper::help( 'screen.clm.info' );

		// Daten an Template
		$this->assignRef( 'lists', $lists );
		$this->assignRef( 'data', $data );
		$this->assignRef( 'zps', $zps );
		
		parent::display($tpl);

	}

}
?>
