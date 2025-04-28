<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMViewArbiterMain extends JViewLegacy {

	function display($tpl = NULL)
	{
		$lang = clm_core::$lang->arbiter;

		// Die Toolbar erstellen, die über der Seite angezeigt wird
		JToolBarHelper::title( $lang->arbiterlist );

		// nur, wenn Admin
		if (clm_core::$access->getType() == 'admin' OR clm_core::$access->getType() == 'tl') {
			JToolBarHelper::custom('add','new.png','new_f2.png', JText::_('ADD'), false);
			JToolBarHelper::custom('copy', 'copy.png', 'copy_f2.png', JText::_('JTOOLBAR_DUPLICATE'));
		}
		JToolBarHelper::editList();
		// nur, wenn Admin
		if (clm_core::$access->getType() == 'admin' OR clm_core::$access->getType() == 'tl') {
			JToolBarHelper::spacer();
			JToolBarHelper::publishList();
			JToolBarHelper::unpublishList();
		}
		
		if (clm_core::$access->getType() === 'admin') {
			JToolBarHelper::spacer();
			JToolBarHelper::custom('delete','delete.png','delete_f2.png', JText::_('DELETE'),true);
		}
		JToolBarHelper::custom('back', 'back.png', 'back_f2.png', $lang->back, false);

		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();

		

		// Daten an Template übergeben
		$this->user = $model->user;
		
		$this->arbiters = $model->arbiters;

		$this->param = $model->param;

		$this->form = $model->form;
		
		$this->pagination = $model->pagination;

		// zusätzliche Funktionalitäten
		require_once (JPATH_COMPONENT_SITE . DS . 'includes' . DS . 'tooltip.php');

		// Auswahlfelder durchsuchbar machen
		clm_core::$load->load_js("suche_liste");

		parent::display();

	}

}
?>
