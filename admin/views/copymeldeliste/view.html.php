<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\ToolbarHelper;

class CLMViewCopyMeldeliste extends JViewLegacy {

	function display($tpl = NULL) {

		$text = 'Meldeliste kopieren von';
		ToolBarHelper::title( $text );
		
		if (clm_core::$access->getType() == 'admin' OR clm_core::$access->getType() == 'tl') {
			ToolBarHelper::save( 'save' );
//			ToolBarHelper::apply( 'apply' );
		}
		ToolBarHelper::spacer();
		ToolBarHelper::cancel('cancel');

		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();

		// Document/Seite
		$document =Factory::getDocument();

		// Daten an Template übergeben
		$this->user = $model->user;
		
		$this->mannschaft = $model->mannschaft;
		$this->mannschaften = $model->mannschaften;

		$this->form = $model->form;

		// Auswahlfelder durchsuchbar machen
		clm_core::$load->load_js("suche_liste");

		parent::display();

	}

}
?>
