<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Toolbar\ToolbarHelper;

class CLMViewTurDecode extends JViewLegacy {

	function display($tpl = NULL) {

		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();
		
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		clm_core::$load->load_css("icons_images");
		ToolBarHelper::title( $model->turnier->name.": ".Text::_('DECODE_PLAYERS'), 'clm_turnier.png'  );
	
		// Instanz der Tabelle
		$row = Table::getInstance( 'turniere', 'TableCLM' );
		$row->load( $model->turnier->id ); // Daten zu dieser ID laden

		$clmAccess = clm_core::$access;
		if (($row->tl == clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') == 2) OR $clmAccess->access('BE_tournament_edit_detail') === true) {
			ToolBarHelper::save( 'save' );
			ToolBarHelper::apply( 'apply' );
		}
		ToolBarHelper::spacer();
		ToolBarHelper::cancel();

		// das MainMenu abschalten
		$_REQUEST['hidemainmenu'] = 1;
		

		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();

		// Document/Seite
		$document =Factory::getDocument();


		// Daten an Template übergeben
		$this->user = $model->user;
		
		$this->turplayers = $model->turPlayers;
		$this->turnier = $model->turnier;
		$this->a_names = $model->a_names;
		$this->spielernamen = $model->dwzData;

		$this->param = $model->param;
		$this->lists = $model->lists;

		$this->pagination = $model->pagination;

		parent::display();

	}

}
?>
