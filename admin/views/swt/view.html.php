<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
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
		$swmFiles 	= $this->get( 'swmFiles' );
		$pgnFiles 	= $this->get( 'pgnFiles' );
		$trfFiles 	= $this->get( 'trfFiles' );
		
		//Toolbar
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( JText::_('TITLE_SWT') ,'clm_headmenu_manager.png' );
		
		JToolBarHelper::custom( 'import', 'upload.png', 'upload_f2.png', JText::_('SWT_IMPORT'), false);
		JToolBarHelper::custom('delete','delete.png','delete_f2.png', JText::_('SWT_DELETE'), false);
		JToolBarHelper::custom( 'upload', 'upload.png', 'upload_f2.png', JText::_('SWT_UPLOAD'), false);
		
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'swm_import', 'upload.png', 'upload_f2.png', JText::_('SWM_IMPORT'), false);
		JToolBarHelper::custom('swm_delete','delete.png','delete_f2.png', JText::_('SWM_DELETE'), false);
		JToolBarHelper::custom( 'swm_upload', 'upload.png', 'upload_f2.png', JText::_('SWM_UPLOAD'), false);
		
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'pgn_import', 'upload.png', 'upload_f2.png', JText::_('PGN_IMPORT'), false);
		JToolBarHelper::custom('pgn_delete','delete.png','delete_f2.png', JText::_('PGN_DELETE'), false);
		JToolBarHelper::custom( 'pgn_upload', 'upload.png', 'upload_f2.png', JText::_('PGN_UPLOAD'), false);
		
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'trf_import', 'upload.png', 'upload_f2.png', JText::_('TRF_IMPORT'), false);
		JToolBarHelper::custom('trf_delete','delete.png','delete_f2.png', JText::_('TRF_DELETE'), false);
		JToolBarHelper::custom( 'trf_upload', 'upload.png', 'upload_f2.png', JText::_('TRF_UPLOAD'), false);

		//SWT-File-Auswahl erstellen
		jimport( 'joomla.filesystem.file' );
		
		$filename = clm_core::$load->request_string('filename', '');

		$options_swt_files[]		= JHtml::_('select.option', '', JText::_( 'SWT_FILES' ));
		if (isset($swtFiles)) {
		foreach($swtFiles as $i => $file)	{
			$options_swt_files[]		= JHtml::_('select.option', basename($file), basename($file));
		} 	}
		$lists['swt_files']	= JHtml::_('select.genericlist', $options_swt_files, 'swt_file', 'class="inputbox"', 'value', 'text', $filename );
	
		//PGN-File-Auswahl erstellen
		$pgn_filename = clm_core::$load->request_string('pgn_filename', '');

		$options_pgn_files[]		= JHtml::_('select.option', '', JText::_( 'PGN_FILES' ));
		if (isset($pgnFiles)) {
		foreach($pgnFiles as $i => $file)	{
			$options_pgn_files[]		= JHtml::_('select.option', basename($file), basename($file));
		} 	}
		$lists['pgn_files']	= JHtml::_('select.genericlist', $options_pgn_files, 'pgn_file', 'class="inputbox"', 'value', 'text', $pgn_filename );
		
		//SWM-File-Auswahl erstellen
		$swm_filename = clm_core::$load->request_string('swm_filename', '');

		$options_swm_files[]		= JHtml::_('select.option', '', JText::_( 'SWM_FILES' ));
		if (isset($swmFiles)) {
		foreach($swmFiles as $i => $file)	{
			$options_swm_files[]		= JHtml::_('select.option', basename($file), basename($file));
		} 	}
		$lists['swm_files']	= JHtml::_('select.genericlist', $options_swm_files, 'swm_file', 'class="inputbox"', 'value', 'text', $swm_filename );
		
		//TRF-File-Auswahl erstellen
		$trf_filename = clm_core::$load->request_string('trf_filename', '');

		$options_trf_files[]		= JHtml::_('select.option', '', JText::_( 'TRF_FILES' ));
		if (isset($trfFiles)) {
		foreach($trfFiles as $i => $file)	{
			$options_trf_files[]		= JHtml::_('select.option', basename($file), basename($file));
		} 	}
		$lists['trf_files']	= JHtml::_('select.genericlist', $options_trf_files, 'trf_file', 'class="inputbox"', 'value', 'text', $trf_filename );
		
		//Daten an Template
		$this->lists = $lists;
	
		parent::display($tpl);
	}
	
}

?>
