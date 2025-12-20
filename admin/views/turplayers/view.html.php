<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMViewTurPlayers extends JViewLegacy {

	function display($tpl = NULL) {

		$dview = clm_core::$load->request_string('dview','std');
		//CLM parameter auslesen
		$clm_config = clm_core::$db->config();
		$turnier_entry_fee = $clm_config->turnier_entry_fee;
		$testbutton = $clm_config->test_button;

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
					JToolBarHelper::custom('del_player', 'cancel.png', 'copy_f2.png', JText::_('DEL_PLAYER'),false);
					JToolBarHelper::custom( 'plusTln', 'upload.png', 'upload_f2.png', JText::_('PARTICIPANT_PLUS'), false);
					JToolBarHelper::spacer();
				}
				if (!$model->turnier->rnd) { 
					JToolBarHelper::custom( 'sortByTWZ', 'copy.png', 'copy_f2.png', JText::_('SNR_BY_TWZ'), false);
					JToolBarHelper::custom( 'sortByRandom', 'copy.png', 'copy_f2.png', JText::_('SNR_BY_RANDOM'), false);
					JToolBarHelper::custom( 'sortByOrdering', 'copy.png', 'copy_f2.png', JText::_('SNR_BY_ORDERING'), false );
					JToolBarHelper::spacer();
					JToolBarHelper::deleteList();
					JToolBarHelper::spacer();
				}
			} else {
				if ($model->turnier->typ != 3) { // nicht bei KO
					JToolBarHelper::custom( 'setRanking', 'copy.png', 'copy_f2.png', JText::_('SET_RANKING'), false);
					JToolBarHelper::spacer();
				}
			}
			
			// Online-Anmeldungen bearbeiten
			if ($model->turnier->dateRegistration > '1970-01-01') { 
				JToolBarHelper::custom( 'onlineRegList', 'copy.png', 'copy_f2.png', JText::_('ONLINE_REG_LIST'), false);
				JToolBarHelper::spacer();
			}
			// Email an Teilnehmer (TL muss gesetzt sein)
			if ($model->turnier->tl != '0') {
				JToolBarHelper::custom( 'mail_to_all', 'copy.png', 'copy_f2.png', JText::_('MAIL_TO_ALL'), false);
			}
			// Turnier mit Mannschaftswertung
			$turParams = new clm_class_params($model->turnier->params);
			$param_teamranking = $turParams->get('teamranking', 0);
			$param_teamranking  = preg_replace("/[^a-z\d_äöü ]/si" , '' , $param_teamranking); 
			if ($param_teamranking > '0') {
				JToolBarHelper::custom( 'edit_teams', 'copy.png', 'copy_f2.png', JText::_('EDIT_TEAMS'), false);
			}
			// Turnier mit Umschlüsslung von Namen
			$param_import_source = $turParams->get('import_source', 0);
			$param_import_source  = preg_replace("/[^a-z\d_äöü ]/si" , '' , $param_import_source); 
			if ($param_import_source > '0') {
				JToolBarHelper::custom( 'player_decode', 'copy.png', 'copy_f2.png', JText::_('DECODE_PLAYERS'), false);
				JToolBarHelper::custom( 'player_decode_copy', 'copy.png', 'copy_f2.png', JText::_('DECODE_SEASON'), false);
			}
			// Detail-View wechseln, nur wenn Startgeldverwaltung aktiv ist
			if (($turnier_entry_fee == 1) AND !is_null($model->turnier->entry_fee) AND ($model->turnier->entry_fee > 0)) {
				if ($dview == 'std') {
					JToolBarHelper::custom( 'view_entry_fee', 'forward.png', 'forward_f2.png', JText::_('VIEW_ENTRY_FEE'), false);
				} else {
					JToolBarHelper::custom( 'view_std', 'forward.png', 'forward_f2.png', JText::_('VIEW_STD'), false);
					JToolBarHelper::custom( 'export_pdf', 'copy.png', 'copy_f2.png', 'pdf-Startgeldexport', false);
					JToolBarHelper::custom( 'export_csv', 'copy.png', 'copy_f2.png', 'csv-Startgeldexport', false);
					if ($testbutton == 1) JToolBarHelper::custom( 'export_tc', 'copy.png', 'copy_f2.png', 'Zeitmodi-Liste', false);
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
		$this->user = $model->user;
		
		$this->turnier = $model->turnier;
		
		$this->turplayers = $model->turPlayers;
		$this->sum_fee = $model->sum_fee;

		$this->param = $model->param;

		$this->pagination = $model->pagination;

		// Auswahlfelder durchsuchbar machen
		clm_core::$load->load_js("suche_liste");

		// zusätzliche Funktionalitäten
		require_once (JPATH_COMPONENT_SITE . DS . 'includes' . DS . 'tooltip.php');


		parent::display();

	}

}
?>
