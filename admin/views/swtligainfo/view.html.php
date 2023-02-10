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

class CLMViewSWTLigainfo extends JViewLegacy {
	function display ($tpl = null) {

		// Daten vom Model
		$state 			= $this->get( 'state' );
		$rang			= $state->get( 'rang' );
		$sl_mail		= $state->get( 'sl_mail' );
		$saison_id		= $state->get( 'saison_id' );
		$db_sllist		= $state->get( 'db_sllist' );
		$db_saisonlist	= $state->get( 'db_saisonlist' );
		$db_glist		= $state->get( 'db_glist' );
		
		$swt_data		= $this->get( 'dataSWT' );
		$default		= $this->get( 'default' );
		
		//Toolbar
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( JText::_('TITLE_SWT_LEAGUE_INFO') ,'clm_headmenu_manager.png' );
		
		//JToolBarHelper::custom('next','next.png','next_f2.png', JText::_('SWT_LEAGUE_NEXT'), false);
		JToolBarHelper::custom('next','forward.png','forward_f2.png', JText::_('SWT_LEAGUE_NEXT'), false);
		JToolBarHelper::custom('cancel','cancel.png','cancel_f2.png', JText::_('SWT_LEAGUE_CANCEL'), false);
		
		
		// Listen
		// Heimrecht vertauscht
		$lists['heim']	= JHtml::_('select.booleanlist',  'heim', 'class="inputbox"', $swt_data['heimrecht_vertauscht'] );
		// Published
		$lists['published']	= JHtml::_('select.booleanlist',  'published', 'class="inputbox"', $default['published'] );
		// automat. Mail
		$lists['mail']	= JHtml::_('select.booleanlist',  'mail', 'class="inputbox"', $default['mail'] );
		// Staffelleitermail als BCC
		$lists['sl_mail']	= JHtml::_('select.booleanlist',  'sl_mail', 'class="inputbox"', $sl_mail );
		// Ordering für Rangliste
		$lists['order']	= JHtml::_('select.booleanlist',  'order', 'class="inputbox"', $default['order'] );
		// Mannschaftsnamen mit Land
		$lists['name_land']	= JHtml::_('select.booleanlist',  'name_land', 'class="inputbox"', '0' );
		// SL Listen
		$sllist[]	= JHtml::_('select.option',  '0', JText::_( 'LIGEN_SL' ), 'jid', 'name' );
		$sllist		= array_merge( $sllist, $db_sllist );
		$lists['sl']	= JHtml::_('select.genericlist',   $sllist, 'sl', 'class="inputbox" size="1"', 'jid', 'name', $default['sl'] );
		// Saisonliste
		$saisonlist[]	= JHtml::_('select.option',  '0', JText::_( 'LIGEN_SAISON' ), 'sid', 'name' );
		$saisonlist	= array_merge( $saisonlist, $db_saisonlist );
		$lists['saison']= JHtml::_('select.genericlist',   $saisonlist, 'sid', 'class="inputbox" size="1"','sid', 'name', $saison_id );
		// Ranglisten
		$glist[]	= JHtml::_('select.option',  '0', JText::_( 'LIGEN_ML' ), 'id', 'Gruppe' );
		$glist		= array_merge( $glist, $db_glist );
		$lists['gruppe']= JHtml::_('select.genericlist',   $glist, 'rang', 'class="inputbox" size="1"', 'id', 'Gruppe', $rang );
		// Ersatz-Regel
		$lists['ersatz_regel']	= $default['ersatz_regel'];
		// Anzeige Mannschaftsaufstellungen
		$lists['anzeige_ma']	= JHtml::_('select.booleanlist',  'anzeige_ma', 'class="inputbox"', $default['anzeige_ma'] );
		

		// Konfigurationsparameter an Template
		$this->rang = $rang;
		$this->sl_mail = $sl_mail;
		
		// Daten an Template
		$this->lists = $lists;
		$this->default = $default;

		// SWT-Daten an Template
		$this->swt_data = $swt_data;

		parent::display($tpl);
		
	}

}

?>
