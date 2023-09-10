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

class CLMViewTurRoundMatches extends JViewLegacy {

	function display($tpl = NULL) {

		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();
		
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( $model->turnier->name.", ".$model->round->name.": ".JText::_('MATCHES'), 'clm_turnier.png'  );
	
		if ($model->round->tl_ok != 1) {
			JToolBarHelper::addNew('add', JText::_('MATCH_ADD'));
			JToolBarHelper::custom( 'delete', 'minus.png', 'minus.png', JText::_('MATCH_DELETE'), false);
			JToolBarHelper::spacer();
			
			JToolBarHelper::save( 'save' );
			JToolBarHelper::apply( 'apply' );
			JToolBarHelper::spacer();
			JToolBarHelper::trash( 'reset', JText::_('RESET_RESULTS'), FALSE );
			JToolBarHelper::trash( 'p_reset', JText::_('RESET_PAIRINGS'), FALSE );
			JToolBarHelper::spacer();
		}
		if ($model->round->tl_ok == 1) {
			JToolBarHelper::custom( 'unapprove', 'default.png', 'default.png', JText::_('REMOVE_APPROVAL'), FALSE );
		} else {
			JToolBarHelper::custom( 'approve', 'default.png', 'default.png', JText::_('SET_APPROVAL'), FALSE );
		}
		JToolBarHelper::spacer();
		
		//drawing parameter auslesen
		$turParams = new clm_class_params($model->turnier->params);
		$drawing_mode = $turParams->get('drawing_mode', 0);
//		if (JComponentHelper::isInstalled ( 'com_clm_pairing' ) AND $model->turnier->typ == 1) {
		if (JPluginHelper::isEnabled('xxx', 'clm_pairing_files') AND $model->turnier->typ == 1) { 
			if ($drawing_mode > 0) {
				JToolBarHelper::custom('draw','add.png','add_f2.png', JText::_('DRAW_PAIRINGS'), false);
				JToolBarHelper::spacer();
			}
		}
		// Goto Teilnehmern
		JToolBarHelper::custom( 'goto_players', 'forward.png', 'forward_f2.png', JText::_('GOTO_PLAYERS'), false);
		JToolBarHelper::cancel();

		// das MainMenu abschalten
		$_GET['hidemainmenu'] = 1;


		// Document/Seite
		$document =JFactory::getDocument();

		// Script
		$document->addScript(CLM_PATH_JAVASCRIPT.'turroundmatches.js');

		// preg_replace("/\r|\n/s", "", addslashes($value->pgn))


		// Daten an Template übergeben
		$this->user = $model->user;
		
		$this->turnier = $model->turnier;
		$this->matches = $model->matches;
		$this->ergebnisse = $model->ergebnisse;
		$this->players = $model->players;
		$this->round = $model->round;

		$this->param = $model->param;

		// zusätzliche Funktionalitäten
//		JHtml::_('behavior.tooltip');
		require_once (JPATH_COMPONENT_SITE . DS . 'includes' . DS . 'tooltip.php');


		parent::display();

	}

}
?>
