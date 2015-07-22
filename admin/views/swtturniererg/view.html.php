<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class CLMViewSWTTurnierErg extends JView {
	function display($tpl = null) { 
	
		//Daten vom Model
		$matches	 		= $this->get('matches');
		$teilnehmerNamen 	= $this->get('teilnehmerNamen');
		$ergebnisTexte 		= $this->get('ergebnisTexte');
		$runden				= $this->get('runden');
		
		//Toolbar
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'admin_menue_images.php');
		JToolBarHelper::title( JText::_('TITLE_SWT_TOURNAMENT_ERG') ,'clm_headmenu_manager.png' );
		
		//JToolBarHelper::custom('next','next.png','next_f2.png', JText::_('SWT_TOURNAMENT_NEXT'), false);
		//JToolBarHelper::custom('next','save.png','save_f2.png', JText::_('SWT_TOURNAMENT_SAVE'), false);
		JToolBarHelper::custom('next','forward.png','forward_f2.png', JText::_('SWT_TOURNAMENT_NEXT'), false);
		JToolBarHelper::custom('cancel','cancel.png','cancel_f2.png', JText::_('SWT_TOURNAMENT_CANCEL'), false);	
		
		//Auswahllisten für Rundenfilter
		$runden_options[] = JHtml::_('select.option',0,JText::_('SWT_ALL_ROUNDS'));
		if (count($runden) > 0) {
			foreach($runden as $rnd => $runde) {
				$runden_options[] = JHtml::_('select.option',$rnd,$runde->name); 
			}
		}
		
		//Auswahllisten für Teilnehmer
		$teilnehmer_options[] = JHtml::_('select.option',0,JText::_('SWT_LEAGUE_PLAYER_SELECT'));
		foreach($teilnehmerNamen as $teil) {
			$teilnehmer_options[] = JHtml::_('select.option',$teil->snr,$teil->name);
		}
		
		//Auswahllisten für Ergebnisse
		foreach($ergebnisTexte as $erg) {
			$ergebnis_options[] = JHtml::_('select.option',$erg->eid,$erg->erg_text);
		}
		
		//Daten ans Template
		$this->assignRef('matches',$matches);
		$this->assignRef('runden_options',$runden_options);
		$this->assignRef('teilnehmer_options',$teilnehmer_options);
		$this->assignRef('ergebnis_options',$ergebnis_options);
		$this->assignRef('runden',$runden);
		
		parent::display($tpl);
	}
	
}

?>