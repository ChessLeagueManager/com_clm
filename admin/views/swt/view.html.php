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

use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

class CLMViewSWT extends JViewLegacy {
	function display($tpl = null) { 
				
		//Daten vom Model
		$swtFiles 	= $this->get( 'swtFiles' );
		
		//Toolbar
		clm_core::$load->load_css("icons_images");
		ToolBarHelper::title( Text::_('SWT') ,'clm_headmenu_manager.png' );
		
		ToolBarHelper::custom( 'import', 'upload.png', 'upload_f2.png', Text::_('SWT_IMPORT'), false);
		ToolBarHelper::custom('delete','delete.png','delete_f2.png', Text::_('SWT_DELETE'), false);
		ToolBarHelper::custom( 'upload', 'upload.png', 'upload_f2.png', Text::_('SWT_UPLOAD'), false);
		
		ToolBarHelper::spacer();
		ToolBarHelper::custom( 'trf_import', 'forward.png', 'forward_f2.png', Text::_('GOTO_TRF_IMPORT'), false);
		ToolBarHelper::custom( 'swm_import', 'forward.png', 'forward_f2.png', Text::_('GOTO_SWM_IMPORT'), false);
		ToolBarHelper::custom( 'pgn_import', 'forward.png', 'forward_f2.png', Text::_('GOTO_PGN_IMPORT'), false);
		ToolBarHelper::custom( 'arena_import', 'forward.png', 'forward_f2.png', Text::_('GOTO_LICHESS_IMPORT'), false);

		//SWT-File-Auswahl erstellen
		jimport( 'joomla.filesystem.file' );
		
		$filename = clm_core::$load->request_string('filename', '');

		$options_swt_files[]		= HTMLHelper::_('select.option', '', Text::_( 'SWT_FILES' ));
		if (isset($swtFiles)) {
		foreach($swtFiles as $i => $file)	{
			$options_swt_files[]		= HTMLHelper::_('select.option', basename($file), basename($file));
		} 	}
		$lists['swt_files']	= HTMLHelper::_('select.genericlist', $options_swt_files, 'swt_file', 'class="inputbox"', 'value', 'text', $filename );
				
		//Daten an Template
		$this->lists = $lists;
	
		parent::display($tpl);
	}
	
}

?>
