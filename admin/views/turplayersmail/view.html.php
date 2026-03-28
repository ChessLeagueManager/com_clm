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
use Joomla\CMS\Table\Table;
use Joomla\CMS\Toolbar\ToolbarHelper;

class CLMViewTurPlayersMail extends JViewLegacy {

	function display($tpl = NULL) {

		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();
		
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		clm_core::$load->load_css("icons_images");
		ToolBarHelper::title( $model->turnierData->name.": ".Text::_('MAIL_TO_ALL'), 'clm_turnier.png'  );
	
		// Instanz der Tabelle
		$row = Table::getInstance( 'turniere', 'TableCLM' );
		$row->load( $model->turnierData->id ); // Daten zu dieser ID laden

		$clmAccess = clm_core::$access;
		if (($row->tl == clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') == 2) OR $clmAccess->access('BE_tournament_edit_detail') === true) {
			ToolBarHelper::custom('mail_send', 'copy.png', 'copy_f2.png', Text::_('MAIL_SEND'),false);
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
		
		$this->players = $model->playersData;
		$this->turnier = $model->turnierData;
		$this->tl = $model->tlData;

		$this->param = $model->param;


		parent::display();

	}

}
?>
