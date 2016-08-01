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

class CLMViewSWT extends JViewLegacy {
	function display($tpl = null) { 
				
		//Daten vom Model
		$swtFiles 	= $this->get( 'swtFiles' );
		
		//Toolbar
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( JText::_('TITLE_SWT') ,'clm_headmenu_manager.png' );
		
		JToolBarHelper::custom( 'import', 'upload.png', 'upload_f2.png', JText::_('SWT_IMPORT'), false);
		JToolBarHelper::custom('delete','delete.png','delete_f2.png', JText::_('SWT_DELETE'), false);
		JToolBarHelper::custom( 'upload', 'upload.png', 'upload_f2.png', JText::_('SWT_UPLOAD'), false);
		
		//SWT-File-Auswahl erstellen
		jimport( 'joomla.filesystem.file' );
		
		$filename = JRequest::getVar('filename', '');

		$options_swt_files[]		= JHtml::_('select.option', '', JText::_( 'SWT_FILES' ));
		if (isset($swtFiles)) {
		foreach($swtFiles as $i => $file)	{
			$options_swt_files[]		= JHtml::_('select.option', JFile::getName($file), JFile::getName($file));
		} 	}
		$lists['swt_files']	= JHtml::_('select.genericlist', $options_swt_files, 'swt_file', 'class="inputbox"', 'value', 'text', $filename );
	
		//Daten an Template
		$this->assignRef( 'lists', $lists );
	
		parent::display($tpl);
	}
	
}

?>
