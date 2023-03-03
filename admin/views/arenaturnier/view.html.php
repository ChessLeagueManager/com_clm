<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleagueamanager.de
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMViewArenaTurnier extends JViewLegacy {
	function display($tpl = null) { 
		
		//Daten vom Model
		$state 		= $this->get( 'State' );
		$saisons 	= $this->get( 'saisons' );
		$turniere 	= $this->get( 'turniere' );
		
		$lang = clm_core::$lang->imports;
		
		//Toolbar
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( JText::_('TITLE_SWT_TOURNAMENT') ,'clm_headmenu_manager.png' );
		
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
		JToolBarHelper::cancel();
		
		//Saison- und Turnier-Auswahl erstellen
		$options_saisons[]		= JHtml::_('select.option', '', JText::_( 'SWT_SAISONS' ));
		foreach($saisons as $saison)	{
			$options_saisons[]		= JHtml::_('select.option', $saison->id, $saison->name);
		}
		
		$options_turniere[]		= JHtml::_('select.option', '', JText::_( 'SWT_TOURNAMENTS' ));
		$arena_code	= clm_core::$load->request_string('arena_code', '');
		$current_turnier	= clm_core::$load->request_string('tid', '');
		$turnier_codes = array();
		
		foreach($turniere as $turnier)	{
			$sf1 = strpos($turnier->bem_int, $lang->arena_remark);
			if ($sf1 === false) $tur_code = $tur_code = '';
			else $tur_code = substr($turnier->bem_int, (strlen($lang->arena_remark)+1),8);
																 
			$current_turnier = 0;

			$turnier_codes[$turnier->id] = $tur_code;
			$options_turniere[]		= JHtml::_('select.option', $turnier->id, $turnier->name);
		}
		
		$lists['saisons']	= JHtml::_('select.genericlist', $options_saisons, 'filter_saison', 'class="inputbox" onchange="this.form.submit();"', 'value', 'text', $state->get('filter_saison') );

		$lists['turniere']	= JHtml::_('select.genericlist', $options_turniere, 'tid', 'class="inputbox" onChange="insertCode()"', 'value', 'text', 0 );

		//Daten an Template
		$this->lists = $lists;
		$this->turnier_codes = $turnier_codes;
				
		parent::display($tpl);
	}
	
}

?>
