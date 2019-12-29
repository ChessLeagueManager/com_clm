<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2019 CLM Team.  All rights reserved
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
 		$model = $this->getModel('db');
		$id = clm_core::$load->request_int('id');
		
		// Menubilder laden
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title(   JText::_( 'TITLE_DATABASE' ), 'clm_headmenu_datenbank.png' );

		$clmAccess = clm_core::$access;
		if ( $clmAccess->access('BE_database_general') ) {
			JToolBarHelper::custom('export','download.png','download_f2.png',JTEXT::_('DB_BUTTON_EXPORT'),false);
			JToolBarHelper::custom('import','upload.png','upload_f2.png',JTEXT::_('DB_BUTTON_IMPORT'),false);
			JToolBarHelper::custom('delete','delete.png','delete_f2.png',JTEXT::_('DB_BUTTON_DEL'),false);
			JToolBarHelper::custom('upload','upload.png','upload_f2.png',JTEXT::_('DB_BUTTON_FILE_UPLOAD'),false);

		}
		JToolBarHelper::help( 'screen.clm.info' );

		// Daten an Template
		// keine!
		
		parent::display($tpl);

	}

}
?>
