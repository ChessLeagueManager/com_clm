<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMViewSWT extends JViewLegacy {
	function display($tpl = null) { 
				
		//Daten vom Model
		$swtFiles 	= $this->get( 'swtFiles' );
		
		//Toolbar
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( JText::_('SWT') ,'clm_headmenu_manager.png' );
		
		JToolBarHelper::custom( 'import', 'upload.png', 'upload_f2.png', JText::_('SWT_IMPORT'), false);
		JToolBarHelper::custom('delete','delete.png','delete_f2.png', JText::_('SWT_DELETE'), false);
		JToolBarHelper::custom( 'upload', 'upload.png', 'upload_f2.png', JText::_('SWT_UPLOAD'), false);
		
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'trf_import', 'forward.png', 'forward_f2.png', JText::_('GOTO_TRF_IMPORT'), false);
		JToolBarHelper::custom( 'swm_import', 'forward.png', 'forward_f2.png', JText::_('GOTO_SWM_IMPORT'), false);
		JToolBarHelper::custom( 'pgn_import', 'forward.png', 'forward_f2.png', JText::_('GOTO_PGN_IMPORT'), false);
		JToolBarHelper::custom( 'arena_import', 'forward.png', 'forward_f2.png', JText::_('GOTO_LICHESS_IMPORT'), false);

		//SWT-File-Auswahl erstellen
		jimport( 'joomla.filesystem.file' );
		
		$filename = clm_core::$load->request_string('filename', '');

		$options_swt_files[]		= JHtml::_('select.option', '', JText::_( 'SWT_FILES' ));
		if (isset($swtFiles)) {
		foreach($swtFiles as $i => $file)	{
			$options_swt_files[]		= JHtml::_('select.option', basename($file), basename($file));
		} 	}
		$lists['swt_files']	= JHtml::_('select.genericlist', $options_swt_files, 'swt_file', 'class="inputbox"', 'value', 'text', $filename );
				
		//Daten an Template
		$this->lists = $lists;
	
		parent::display($tpl);
	}
	
}

?>
