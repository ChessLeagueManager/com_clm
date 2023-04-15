<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleagueamanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMViewSWMTurnier extends JViewLegacy {
	function display($tpl = null) { 
		
		//Daten vom Model
		$state 		= $this->get( 'State' );
		$saisons 	= $this->get( 'saisons' );
		$turniere 	= $this->get( 'turniere' );
		$swmFiles 	= $this->get( 'swmFiles' );		
		$swm_file	= clm_core::$load->request_string('swm_file', '');
				
		//Toolbar
		clm_core::$load->load_css("icons_images");
		if ($swm_file == '')
			JToolBarHelper::title( JText::_('TITLE_SWM_FILE_IMPORT') ,'clm_headmenu_manager.png' );
		elseif (strtolower(JFile::getExt($swm_file) ) == 'tumx' OR strtolower(JFile::getExt($swm_file) ) == 'tutx') 
			JToolBarHelper::title( JText::_('TITLE_SWT_LEAGUE')." - ".$swm_file ,'clm_headmenu_manager.png' );
		else
			JToolBarHelper::title( JText::_('TITLE_SWT_TOURNAMENT')." - ".$swm_file ,'clm_headmenu_manager.png' );
		
		JToolBarHelper::custom('update','refresh.png','refresh_f2.png', JText::_('SWT_TOURNAMENT_UPDATE'), false);
		$clmAccess = clm_core::$access;
		if ($clmAccess->access('BE_tournament_create') === true) {
			JToolBarHelper::custom('add','new.png','new_f2.png', JText::_('SWT_TOURNAMENT_NEW'), false);
		}
		//CLM parameter auslesen
		$config = clm_core::$db->config();
		$test_button = $config->test_button;
		if ($test_button) {
			JToolBarHelper::custom('test','delete.png','delete_f2.png', JText::_('SWT_TOURNAMENT_TEST'), false);
		}
		JToolBarHelper::custom('swm_delete','delete.png','delete_f2.png', JText::_('SWM_DELETE'), false);
		JToolBarHelper::custom( 'swm_upload', 'upload.png', 'upload_f2.png', JText::_('SWM_UPLOAD'), false);
		JToolBarHelper::cancel();
		
		//Saison- und Turnier-Auswahl erstellen
		$options_saisons[]		= JHtml::_('select.option', '', JText::_( 'SWT_SAISONS' ));
		foreach($saisons as $saison)	{
			$options_saisons[]		= JHtml::_('select.option', $saison->id, $saison->name);
		}
		
		$options_turniere[]		= JHtml::_('select.option', '', JText::_( 'SWT_TOURNAMENTS' ));
		$current_turnier	= clm_core::$load->request_string('turnier', '');
		
		foreach($turniere as $turnier)	{
			if (is_null($turnier->bem_int)) $turnier->bem_int = '';
			$sf1 = strpos($turnier->bem_int, 'SWT-Importfile:');
			$sf2 = strpos($turnier->bem_int, '.SWT');
			if ($sf2 === false) $sf2 = strpos($turnier->bem_int, '.swt');
			if (!($sf1 === false) AND !($sf2 === false) AND ($sf1 < $sf2))
				$filename = substr($turnier->bem_int, ($sf1 + 15), ($sf2 + 4 - ($sf1 + 15) ));
			else $filename = '';
			if ($filename == $swm_file)	$current_turnier = $turnier->id;

			$options_turniere[]		= JHtml::_('select.option', $turnier->id, $turnier->name);
		}
		
		$lists['saisons']	= JHtml::_('select.genericlist', $options_saisons, 'filter_saison', 'class="inputbox" onchange="this.form.submit();"', 'value', 'text', $state->get('filter_saison') );
		$lists['turniere']	= JHtml::_('select.genericlist', $options_turniere, 'turnier', 'class="inputbox"', 'value', 'text', $current_turnier );
		
		//SWM-File-Auswahl erstellen
		$options_swm_files[]		= JHtml::_('select.option', '', JText::_( 'SWM_FILES' ));
		if (isset($swmFiles)) {
		foreach($swmFiles as $i => $file)	{
			$options_swm_files[]		= JHtml::_('select.option', basename($file), basename($file));
		} 	}
		$lists['swm_files']	= JHtml::_('select.genericlist', $options_swm_files, 'swm_file', 'class="inputbox" onchange="document.adminForm.submit();"', 'value', 'text', $swm_file );

		//Daten an Template
		$this->lists = $lists;
				
		parent::display($tpl);
	}
	
}

?>
