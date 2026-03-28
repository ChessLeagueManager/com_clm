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

class CLMViewDB extends JViewLegacy {

	function display($tpl = null) {
		$app	= Factory::getApplication();
		$jinput = $app->input;
		$task 	= $jinput->get('task', null, null);
		$tpl = null;
 		$model = $this->getModel('db');
		$id = clm_core::$load->request_int('id');
		
		// Menubilder laden
		clm_core::$load->load_css("icons_images");
		ToolBarHelper::title(   Text::_( 'TITLE_DATABASE' ), 'clm_headmenu_datenbank.png' );

		$clmAccess = clm_core::$access;
		if ( $clmAccess->access('BE_database_general') ) {
			ToolBarHelper::custom('export','download.png','download_f2.png',Text::_('DB_BUTTON_EXPORT'),false);
			ToolBarHelper::custom('import','upload.png','upload_f2.png',Text::_('DB_BUTTON_IMPORT'),false);
			ToolBarHelper::custom('delete','delete.png','delete_f2.png',Text::_('DB_BUTTON_DEL'),false);
			ToolBarHelper::custom('upload','upload.png','upload_f2.png',Text::_('DB_BUTTON_FILE_UPLOAD'),false);

		}
		ToolBarHelper::help( 'screen.clm.info' );

		// Daten an Template
		// keine!
		
		parent::display($tpl);

	}

}
?>
