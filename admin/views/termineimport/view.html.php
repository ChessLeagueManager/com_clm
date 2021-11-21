<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMViewtermineimport extends JViewLegacy {
	function display($tpl = null) { 
				
		//Daten vom Model
		$termineFiles 	= $this->get( 'TermineFiles' );
		
		//Toolbar
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( JText::_('TERM_IMPORT') ,'clm_headmenu_manager.png' );
		
		JToolBarHelper::custom( 'import', 'upload.png', 'upload_f2.png', JText::_('TERM_IMPORT'), false);
		JToolBarHelper::custom('delete','delete.png','delete_f2.png', JText::_('TERM_DELETE'), false);
		JToolBarHelper::custom( 'upload', 'upload.png', 'upload_f2.png', JText::_('TERM_UPLOAD'), false);
		
		

		//CSV-File-Auswahl erstellen
		jimport( 'joomla.filesystem.file' );
		
		$filename = clm_core::$load->request_string('filename', '');

		$options_termine_files[]		= JHtml::_('select.option', '', JText::_( 'TERM_FILES' ));
		if (isset($termineFiles)) {
		foreach($termineFiles as $i => $file)	{
			$options_termine_files[]		= JHtml::_('select.option', basename($file), basename($file));
		} 	}
		$lists['termine_files']	= JHtml::_('select.genericlist', $options_termine_files, 'termine_file', 'class="inputbox"', 'value', 'text', $filename );
	
		
		//Daten an Template
		$this->lists = $lists;
	
		parent::display($tpl);
	}
	
}

?>
