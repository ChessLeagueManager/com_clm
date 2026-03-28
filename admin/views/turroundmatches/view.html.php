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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Plugin\PluginHelper;

class CLMViewTurRoundMatches extends JViewLegacy {

	function display($tpl = NULL) {

		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();
		
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		clm_core::$load->load_css("icons_images");
		ToolBarHelper::title( $model->turnier->name.", ".$model->round->name.": ".Text::_('MATCHES'), 'clm_turnier.png'  );
	
		if ($model->round->tl_ok != 1) {
			ToolBarHelper::addNew('add', Text::_('MATCH_ADD'));
			ToolBarHelper::custom( 'delete', 'minus.png', 'minus.png', Text::_('MATCH_DELETE'), false);
			ToolBarHelper::spacer();
			
			ToolBarHelper::save( 'save' );
			ToolBarHelper::apply( 'apply' );
			ToolBarHelper::spacer();
			ToolBarHelper::trash( 'reset', Text::_('RESET_RESULTS'), FALSE );
			ToolBarHelper::trash( 'p_reset', Text::_('RESET_PAIRINGS'), FALSE );
			ToolBarHelper::spacer();
		}
		if ($model->round->tl_ok == 1) {
			ToolBarHelper::custom( 'unapprove', 'default.png', 'default.png', Text::_('REMOVE_APPROVAL'), FALSE );
		} else {
			ToolBarHelper::custom( 'approve', 'default.png', 'default.png', Text::_('SET_APPROVAL'), FALSE );
		}
		ToolBarHelper::spacer();
		
		//drawing parameter auslesen
		$turParams = new clm_class_params($model->turnier->params);
		$drawing_mode = $turParams->get('drawing_mode', 0);
//		if (JComponentHelper::isInstalled ( 'com_clm_pairing' ) AND $model->turnier->typ == 1) {
		if (PluginHelper::isEnabled('xxx', 'clm_pairing_files') AND $model->turnier->typ == 1) { 
			if ($drawing_mode > 0) {
				ToolBarHelper::custom('draw','add.png','add_f2.png', Text::_('DRAW_PAIRINGS'), false);
				ToolBarHelper::spacer();
			}
		}
		// Goto Teilnehmern
		ToolBarHelper::custom( 'goto_players', 'forward.png', 'forward_f2.png', Text::_('GOTO_PLAYERS'), false);
		ToolBarHelper::cancel();

		// das MainMenu abschalten
		$_GET['hidemainmenu'] = 1;


		// Document/Seite
		$document =Factory::getDocument();

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

		// Auswahlfelder durchsuchbar machen
		clm_core::$load->load_js("suche_liste");

		// zusätzliche Funktionalitäten
//		HTMLHelper::_('behavior.tooltip');
		require_once (JPATH_COMPONENT_SITE . DS . 'includes' . DS . 'tooltip.php');


		parent::display();

	}

}
?>
