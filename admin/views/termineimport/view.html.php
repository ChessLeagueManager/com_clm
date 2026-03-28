<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;

class CLMViewtermineimport extends JViewLegacy {
	function display($tpl = null) { 
				
		//Daten vom Model
		$termineFiles 	= $this->get( 'TermineFiles' );
		
		//Toolbar
		clm_core::$load->load_css("icons_images");
		ToolBarHelper::title( Text::_('TERM_IMPORT') ,'clm_headmenu_manager.png' );
		
		ToolBarHelper::custom( 'import', 'upload.png', 'upload_f2.png', Text::_('TERM_IMPORT'), false);
		ToolBarHelper::custom('delete','delete.png','delete_f2.png', Text::_('TERM_DELETE'), false);
		ToolBarHelper::custom( 'upload', 'upload.png', 'upload_f2.png', Text::_('TERM_UPLOAD'), false);
		
		

		//CSV-File-Auswahl erstellen
		jimport( 'joomla.filesystem.file' );
		
		$filename = clm_core::$load->request_string('filename', '');

		$options_termine_files[]		= HTMLHelper::_('select.option', '', Text::_( 'TERM_FILES' ));
		if (isset($termineFiles)) {
		foreach($termineFiles as $i => $file)	{
			$options_termine_files[]		= HTMLHelper::_('select.option', basename($file), basename($file));
		} 	}
		$lists['termine_files']	= HTMLHelper::_('select.genericlist', $options_termine_files, 'termine_file', 'class="inputbox"', 'value', 'text', $filename );
	
		
		//Daten an Template
		$this->lists = $lists;
	
		parent::display($tpl);
	}
	
}

?>
