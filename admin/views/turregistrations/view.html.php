<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMViewTurRegistrations extends JViewLegacy {

	function display($tpl = NULL) {

		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();
		
		$adminLink = new AdminLink();
		$adminLink->view = "turform";
		$adminLink->more = array('task' => 'edit', 'id' => $model->param['id']);
		$adminLink->makeURL();
		
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( $model->turnier->name.": ".JText::_('ONLINE_REGISTRATIONS'), 'clm_turnier.png'  );
		
		$clmAccess = clm_core::$access;
		if (($model->turnier->tl == clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== false) OR $clmAccess->access('BE_tournament_edit_detail') === true) {
				
			// Online-Anmeldungen gearbeiten
			//JToolBarHelper::custom('move_registration', 'upload.png', 'upload_f2.png', JText::_('REGISTRATION_MOVE'),false);
			JToolBarHelper::custom('edit_registration', 'copy.png', 'copy_f2.png', JText::_('REGISTRATION_EDIT'),false);
			JToolBarHelper::custom('del_registrations', 'cancel.png', 'cancel_f2.png', JText::_('REGISTRATION_DEL'), false);
			JToolBarHelper::spacer();
		
		}
		
		JToolBarHelper::cancel();

		// Daten an Template übergeben
		$this->user = $model->user;
		
		$this->turnier = $model->turnier;
		
		$this->turplayers = $model->turPlayers;
		$this->turregistrations = $model->turRegistrations;

		$this->param = $model->param;

		$this->pagination = $model->pagination;
		
		// zusätzliche Funktionalitäten
//		JHtml::_('behavior.tooltip');
		require_once (JPATH_COMPONENT_SITE . DS . 'includes' . DS . 'tooltip.php');

		parent::display();

	}

}
?>
