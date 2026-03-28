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

class CLMViewTurRounds extends JViewLegacy {

	function display($tpl = NULL) {

		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();
		
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		clm_core::$load->load_css("icons_images");
		ToolBarHelper::title( $model->turnier->name.": ".Text::_('ROUNDS'), 'clm_turnier.png'  );
	
		ToolBarHelper::spacer();
		$clmAccess = clm_core::$access;
		if (($model->turnier->tl == clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_round') !== false) OR $clmAccess->access('BE_tournament_edit_round') === true) {
			ToolBarHelper::spacer();
			ToolBarHelper::publishList();
			ToolBarHelper::unpublishList();
		}
		ToolBarHelper::spacer();
		ToolBarHelper::cancel();

		if (($model->turnier->tl == clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_round') !== false) OR $clmAccess->access('BE_tournament_edit_round') === true) {
			ToolBarHelper::divider();
			ToolBarHelper::spacer();
			ToolBarHelper::custom( 'turform', 'config.png', 'config_f2.png', Text::_('TOURNAMENT'), false);
		}

		// Daten an Template übergeben
		$this->user = $model->user;
		
		$this->turrounds = $model->turRounds;
		$this->turnier = $model->turnier;
 
		$this->param = $model->param;

		$this->pagination = $model->pagination;
		
		// zusätzliche Funktionalitäten
//		HTMLHelper::_('behavior.tooltip');
		require_once (JPATH_COMPONENT_SITE . DS . 'includes' . DS . 'tooltip.php');

		parent::display();

	}

}
?>
