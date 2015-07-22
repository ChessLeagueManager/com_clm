<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMViewTurPlayers extends JViewLegacy {

	function display($tpl = NULL) {

		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();
		
		$adminLink = new AdminLink();
		$adminLink->view = "turform";
		$adminLink->more = array('task' => 'edit', 'id' => $model->param['id']);
		$adminLink->makeURL();
		
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( $model->turnier->name.": ".JText::_('PARTICIPANTS'), 'clm_turnier.png'  );
		
		$clmAccess = clm_core::$access;
		if (($model->turnier->tl == clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== false) OR $clmAccess->access('BE_tournament_edit_detail') === true) {

				
			// noch Spieler möglich
			if ($model->turnier->teil > $model->playersTotal) {
				JToolBarHelper::addNew('add', JText::_('ADD'));
				JToolBarHelper::spacer();
			}
			
			// Nachzügler aufnehmen
			if (($model->turnier->teil == $model->playersTotal) AND $model->turnier->started AND
				($model->turnier->typ == 1)) { // nur bei CH-System
				JToolBarHelper::addNew('add_nz', JText::_('ADD_NZ'));
				JToolBarHelper::custom('del_player', 'cancel.png', 'copy_f2.png', JText::_('DEL_PLAYER'),false);
				JToolBarHelper::spacer();
			}
			
			// noch keine Ergebnisse eingetragen
			if (!$model->turnier->started) { 
				if ($model->turnier->typ == 1) { // nur bei CH-System
					JToolBarHelper::custom( 'plusTln', 'upload.png', 'upload_f2.png', JText::_('PARTICIPANT_PLUS'), false);
					JToolBarHelper::spacer();
				}
				JToolBarHelper::custom( 'sortByTWZ', 'copy.png', 'copy_f2.png', JText::_('SNR_BY_TWZ'), false);
				JToolBarHelper::custom( 'sortByRandom', 'copy.png', 'copy_f2.png', JText::_('SNR_BY_RANDOM'), false);
				JToolBarHelper::custom( 'sortByOrdering', 'copy.png', 'copy_f2.png', JText::_('SNR_BY_ORDERING'), false );
				JToolBarHelper::spacer();
				JToolBarHelper::deleteList();
				JToolBarHelper::spacer();
			} else {
				if ($model->turnier->typ != 3) { // nicht bei KO
					JToolBarHelper::custom( 'setRanking', 'copy.png', 'copy_f2.png', JText::_('SET_RANKING'), false);
					JToolBarHelper::spacer();
				}
			}
		
		}
		
		JToolBarHelper::cancel();
		if (($model->turnier->tl == clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== false) OR $clmAccess->access('BE_tournament_edit_detail') === true) {
			JToolBarHelper::divider();
			JToolBarHelper::spacer();
			JToolBarHelper::custom( 'turform', 'config.png', 'config_f2.png', JText::_('TOURNAMENT'), false);
		
		}
		

		// Daten an Template übergeben
		$this->assignRef('user', $model->user);
		
		$this->assignRef('turnier', $model->turnier);
		
		$this->assignRef('turplayers', $model->turPlayers);

		$this->assignRef('form', $model->form);
		$this->assignRef('param', $model->param);

		$this->assignRef('pagination', $model->pagination);
		
		// zusätzliche Funktionalitäten
		JHtml::_('behavior.tooltip');


		parent::display();

	}

}
?>
