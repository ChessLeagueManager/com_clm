<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2014 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class CLMViewTermineMain extends JView {

	function display() {

		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   &$this->getModel();
		
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'admin_menue_images.php');
		JToolBarHelper::title( JText::_( 'TITLE_TERMINE' ), 'clm_headmenu_termine.png' );
	
		JToolBarHelper::addNewX();
		JToolBarHelper::customX( 'copy', 'copy.png', 'copy_f2.png', JText::_('TERMINE_COPY'));
		
		JToolBarHelper::spacer();
		JToolBarHelper::editListX();
		
		JToolBarHelper::spacer();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
		$clmAccess = new CLMAccess();
		$clmAccess->accesspoint = 'BE_event_delete';
		if ( $clmAccess->access() !== false) {
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