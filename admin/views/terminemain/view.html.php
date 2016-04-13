<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMViewTermineMain extends JViewLegacy {

	function display($tpl = NULL) {

		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();
		
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( JText::_( 'TITLE_TERMINE' ), 'clm_headmenu_termine.png' );
	
		JToolBarHelper::custom('catmain','forward.png','forward_f2.png', JText::_('JCATEGORIES'), false);

		JToolBarHelper::addNew();
		JToolBarHelper::custom( 'copy', 'copy.png', 'copy_f2.png', JText::_('TERMINE_COPY'));
		
		JToolBarHelper::spacer();
		JToolBarHelper::editList();
		
		JToolBarHelper::spacer();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();

		$clmAccess = clm_core::$access;
		if ( $clmAccess->access('BE_event_delete') !== false) {
			JToolBarHelper::spacer();
			JToolBarHelper::custom('delete','delete.png','delete_f2.png', JText::_('TERMINE_DELETE')); 
		}

		
		// Daten an Template übergeben
		$this->assignRef('user', $model->user);
		
		$this->assignRef('termine', $model->termine);

		$this->assignRef('form', $model->form);
		$this->assignRef('param', $model->param);

		$this->assignRef('pagination', $model->pagination);
		
		// zusätzliche Funktionalitäten
		JHtml::_('behavior.tooltip');

		parent::display();
	}

}
?>
