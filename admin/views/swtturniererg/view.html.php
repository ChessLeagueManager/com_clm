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

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;

class CLMViewSWTTurnierErg extends JViewLegacy {
	function display($tpl = null) { 
	
		//Daten vom Model
		$matches	 		= $this->get('matches');
		$teilnehmerNamen 	= $this->get('teilnehmerNamen');
		$ergebnisTexte 		= $this->get('ergebnisTexte');
		$runden				= $this->get('runden');
		
		//Toolbar
		clm_core::$load->load_css("icons_images");
		ToolBarHelper::title( Text::_('TITLE_SWT_TOURNAMENT_ERG') ,'clm_headmenu_manager.png' );
		
		//ToolBarHelper::custom('next','next.png','next_f2.png', Text::_('SWT_TOURNAMENT_NEXT'), false);
		//ToolBarHelper::custom('next','save.png','save_f2.png', Text::_('SWT_TOURNAMENT_SAVE'), false);
		ToolBarHelper::custom('next','forward.png','forward_f2.png', Text::_('SWT_TOURNAMENT_NEXT'), false);
		ToolBarHelper::custom('cancel','cancel.png','cancel_f2.png', Text::_('SWT_TOURNAMENT_CANCEL'), false);	
		
		//Auswahllisten f�r Rundenfilter
		$runden_options[] = HTMLHelper::_('select.option',0,Text::_('SWT_ALL_ROUNDS'));
		if (isset($runden) AND count($runden) > 0) {
			foreach($runden as $rnd => $runde) {
				$runden_options[] = HTMLHelper::_('select.option',$rnd,$runde->name); 
			}
		}
		
		//Auswahllisten f�r Teilnehmer
		$teilnehmer_options[] = HTMLHelper::_('select.option',0,Text::_('SWT_LEAGUE_PLAYER_SELECT'));
		foreach($teilnehmerNamen as $teil) {
			$teilnehmer_options[] = HTMLHelper::_('select.option',$teil->snr,$teil->name);
		}
		
		//Auswahllisten f�r Ergebnisse
		foreach($ergebnisTexte as $erg) {
			$ergebnis_options[] = HTMLHelper::_('select.option',$erg->eid,$erg->erg_text);
		}
		
		//Daten ans Template
		$this->matches = $matches;
		$this->runden_options = $runden_options;
		$this->teilnehmer_options = $teilnehmer_options;
		$this->ergebnis_options = $ergebnis_options;
		$this->runden = $runden;
		
		parent::display($tpl);
	}
	
}

?>
