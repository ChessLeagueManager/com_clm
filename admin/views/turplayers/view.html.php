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

jimport( 'joomla.application.component.view');

class CLMViewTurPlayers extends JView {

	function display() {

		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   &$this->getModel();
		
		$adminLink = new AdminLink();
		$adminLink->view = "turform";
		$adminLink->more = array('task' => 'edit', 'id' => $model->param['id']);
		$adminLink->makeURL();
		
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'admin_menue_images.php');
		JToolBarHelper::title( $model->turnier->name.": ".JText::_('PARTICIPANTS'), 'clm_turnier.png'  );
		
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
		$clmAccess = new CLMAccess();
		$clmAccess->accesspoint = 'BE_tournament_edit_detail';
		if (($model->turnier->tl == CLM_ID AND $clmAccess->access() !== false) OR $clmAccess->access() === true) {
		//if (CLM_usertype == 'admin' OR CLM_usertype == 'tl') {
				
			// noch Spieler möglich
			if ($model->turnier->teil > $model->playersTotal) {
				JToolBarHelper::addNew('add', JText::_('ADD'));
				JToolBarHelper::spacer();
			}
			
			// Nachzügler aufnehmen
			if (($model->turnier->teil == $model->playersTotal) AND $model->turnier->started AND
				($model->turnier->typ == 1)) { // nur bei CH-System
				JToolBarHelper::addNew('add_nz', JText::_('ADD_NZ'));
				JToolBarHelper::spacer();
			}
			
			//JToolBarHelper::custom( 'daten_dsb_API', 'refresh.png', 'refresh_f2.png', JText::_( 'DB_BUTTON_DWZ_UPDATE_API'),false );
			JToolBarHelper::custom( 'daten_dsb_SOAP', 'refresh.png', 'refresh_f2.png', JText::_( 'DB_BUTTON_DWZ_UPDATE_SOAP'),false );
			
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

		if (($model->turnier->tl == CLM_ID AND $clmAccess->access() !== false) OR $clmAccess->access() === true) {
		//if (CLM_usertype == 'admin' OR CLM_usertype == 'tl') {
		
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