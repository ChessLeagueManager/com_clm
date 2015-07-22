<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
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
			//JToolBarHelper::deleteList('delete', JText::_('DELETE'));
			JToolBarHelper::custom( 'delete', 'minus.png', 'minus.png', JText::_('MATCH_DELETE'), false);
			JToolBarHelper::spacer();
			
			JToolBarHelper::save( 'save' );
			JToolBarHelper::apply( 'apply' );
			JToolBarHelper::spacer();
			JToolBarHelper::trash( 'reset', JText::_('RESET_RESULTS'), FALSE );
			JToolBarHelper::spacer();
		}
		if ($model->round->tl_ok == 1) {
			JToolBarHelper::custom( 'unapprove', 'default.png', 'default.png', JText::_('REMOVE_APPROVAL'), FALSE );
		} else {
			JToolBarHelper::custom( 'approve', 'default.png', 'default.png', JText::_('SET_APPROVAL'), FALSE );
		}
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();

		// das MainMenu abschalten
		JRequest::setVar( 'hidemainmenu', 1 );


		// Document/Seite
		$document =JFactory::getDocument();

		// Script
		$document->addScript(CLM_PATH_JAVASCRIPT.'turroundmatches.js');

		// preg_replace("/\r|\n/s", "", addslashes($value->pgn))


		// Daten an Template übergeben
		$this->assignRef('user', $model->user);
		
		$this->assignRef('turnier', $model->turnier);
		$this->assignRef('matches', $model->matches);
		$this->assignRef('ergebnisse', $model->ergebnisse);
		$this->assignRef('players', $model->players);
		$this->assignRef('round', $model->round);

		$this->assignRef('form', $model->form);
		$this->assignRef('param', $model->param);

		$this->assignRef('pagination', $model->pagination);
		
		// zusätzliche Funktionalitäten
		JHtml::_('behavior.tooltip');


		parent::display();

	}

}
?>
