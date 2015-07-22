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

class CLMViewDB extends JView {

	function display($tpl = null) {
		$app	= JFactory::getApplication();
		$jinput = $app->input;
		$task 	= $jinput->get('task', null, null);
		$tpl = null;
		//$app->enqueueMessage( 'TASK VIEW : '.$task, 'warning');
 		$model = $this->getModel('db');
		$id = JRequest::getVar( 'id');
	
		// Konfigurationsparameter auslesen
		$config		= &JComponentHelper::getParams( 'com_clm' );
		$upload		= $config->get('upload_sql',0);
		$execute	= $config->get('execute_sql',0);
		
		// Menubilder laden
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'admin_menue_images.php');
		JToolBarHelper::title(   JText::_( 'TITLE_DATABASE' ), 'clm_headmenu_datenbank.png' );

		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
		$clmAccess = new CLMAccess();
		$clmAccess->accesspoint = 'BE_database_general';
		if ( $clmAccess->access() ) {
			JToolBarHelper::custom('convert_db','refresh.png','refresh_f2.png',JTEXT::_('DB_BUTTON_ADAPT'),false);
			JToolBarHelper::custom('liga_export','download.png','download_f2.png',JTEXT::_('DB_BUTTON_EXPORT'),false);
			JToolBarHelper::custom('import','upload.png','upload_f2.png',JTEXT::_('DB_BUTTON_IMPORT'),false);
			JToolBarHelper::custom('othersDB','specialrankings.png','specialrankings_f2.png', JText::_('DB_BUTTON_OTHERS'), false);
			JToolBarHelper::custom('delete','delete.png','delete_f2.png',JTEXT::_('DB_BUTTON_DEL'),false);
			if ($execute == 1) {
				JToolBarHelper::custom('sql_db','apply.png','apply_f2.png',JTEXT::_('DB_BUTTON_SQL_EXECUTE'),false);
			}
			if ($upload == 1) {
				JToolBarHelper::custom('upload_jfile','upload.png','upload_f2.png',JTEXT::_('DB_BUTTON_FILE_UPLOAD'),false);
			}
			JToolBarHelper::custom('update_clm','default.png','default_f2.png',JTEXT::_('DB_BUTTON_DWZ_DB_UPDATE'),false);
		}
		JToolBarHelper::help( 'screen.clm.info' );

		$config	= &JComponentHelper::getParams( 'com_clm' );
		$params['tourn_showtlok'] = $config->get('tourn_showtlok',0);


		// Document/Seite
		$document =& JFactory::getDocument();
		$document->addScript("includes/js/joomla.javascript.js");  
		JHtml::_('behavior.calendar');

		// Daten an Template
		$this->assignRef( 'lists', $lists );
		$this->assignRef( 'data', $data );
		$this->assignRef( 'zps', $zps );
		$this->assignRef('params', $params);
		
		parent::display($tpl);

	}

}
?>