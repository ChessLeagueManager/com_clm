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
use Joomla\CMS\Toolbar\ToolbarHelper;

class CLMViewTurWaitlist extends JViewLegacy {

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
		ToolBarHelper::title( $model->turnier->name.": ".Text::_('Warteliste'), 'clm_turnier.png'  );
		
		$clmAccess = clm_core::$access;
		if (($model->turnier->tl == clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== false) OR $clmAccess->access('BE_tournament_edit_detail') === true) {
			
			ToolBarHelper::addNew('add', Text::_('ADD'));
			ToolBarHelper::spacer();
			
//			ToolBarHelper::custom('del_player', 'cancel.png', 'copy_f2.png', Text::_('DEL_PLAYER'),false);
//			ToolBarHelper::spacer();

			ToolBarHelper::custom( 'sortByTWZ', 'copy.png', 'copy_f2.png', Text::_('SNR_BY_TWZ'), false);
//			ToolBarHelper::custom( 'sortByRandom', 'copy.png', 'copy_f2.png', Text::_('SNR_BY_RANDOM'), false);
			ToolBarHelper::custom( 'sortByOrdering', 'copy.png', 'copy_f2.png', Text::_('SNR_BY_ORDERING'), false );
			ToolBarHelper::spacer();
			ToolBarHelper::deleteList();
			ToolBarHelper::spacer();

			// Wechsel zur Teilnehmerlist
			ToolBarHelper::custom( 'turplayers', 'forward.png', 'forward_f2.png', Text::_('Zur Teilnehmerliste'), false);
			ToolBarHelper::spacer();

			// Online-Anmeldungen bearbeiten
			if ($model->turnier->dateRegistration > '1970-01-01') { 
				ToolBarHelper::custom( 'onlineRegList', 'forward.png', 'forward_f2.png', Text::_('Zu den Onl.Anmeldungen'), false);
				ToolBarHelper::spacer();
			}
			// Email an Teilnehmer (TL muss gesetzt sein)
			if ($model->turnier->tl != '0') {
//				ToolBarHelper::custom( 'mail_to_all', 'copy.png', 'copy_f2.png', Text::_('MAIL_TO_ALL'), false);
			}

		}

		ToolBarHelper::cancel();
		if (($model->turnier->tl == clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== false) OR $clmAccess->access('BE_tournament_edit_detail') === true) {
			ToolBarHelper::divider();
			ToolBarHelper::spacer();
			ToolBarHelper::custom( 'turform', 'config.png', 'config_f2.png', Text::_('TOURNAMENT'), false);		
		}
		

		// Daten an Template übergeben
		$this->user = $model->user;
		
		$this->turnier = $model->turnier;
		
		$this->turplayers = $model->turPlayers;
//		$this->sum_fee = $model->sum_fee;

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
