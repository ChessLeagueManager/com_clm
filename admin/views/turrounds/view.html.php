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

class CLMViewTurRounds extends JViewLegacy {

	function display($tpl = NULL) {

		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();
		
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( $model->turnier->name.": ".JText::_('ROUNDS'), 'clm_turnier.png'  );
	
		JToolBarHelper::spacer();
		$clmAccess = clm_core::$access;
		if (($model->turnier->tl == clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_round') !== false) OR $clmAccess->access('BE_tournament_edit_round') === true) {
			JToolBarHelper::spacer();
			JToolBarHelper::publishList();
			JToolBarHelper::unpublishList();
		}
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();

		if (($model->turnier->tl == clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_round') !== false) OR $clmAccess->access('BE_tournament_edit_round') === true) {
			JToolBarHelper::divider();
			JToolBarHelper::spacer();
			JToolBarHelper::custom( 'turform', 'config.png', 'config_f2.png', JText::_('TOURNAMENT'), false);
		}

		// Daten an Template übergeben
		$this->user = $model->user;
		
		$this->turrounds = $model->turRounds;
		$this->turnier = $model->turnier;
 
		$this->param = $model->param;

		$this->pagination = $model->pagination;
		
		// zusätzliche Funktionalitäten
//		JHtml::_('behavior.tooltip');
		require_once (JPATH_COMPONENT_SITE . DS . 'includes' . DS . 'tooltip.php');

		parent::display();

	}

}
?>
