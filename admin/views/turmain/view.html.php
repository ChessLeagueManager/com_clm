<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class CLMViewTurMain extends JView {

	function display()
	{
	require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
	$clmAccess = new CLMAccess();
	// Nur CLM-Admin hat Zugriff auf Toolbar
	//if (JFactory::getUser()->authorise('core.manage.clm', 'com_clm')) 	{       
	 		// Die Toolbar erstellen, die über der Seite angezeigt wird
	require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'admin_menue_images.php');
	JToolBarHelper::title( JText::_( 'TOURNAMENTS' ), 'clm_turnier.png'  );
	$clmAccess->accesspoint = 'BE_tournament_create';
	if($clmAccess->access() !== false) {				
		JToolBarHelper::custom('catmain','forward.png','forward_f2.png', JText::_('JCATEGORIES'), false);
		JToolBarHelper::custom('showSpecialrankings','specialrankings.png','specialrankings_f2.png', JText::_('SPECIALRANKINGS_BUTTON'), false);
		JToolBarHelper::spacer();
		JToolBarHelper::spacer();
		JToolBarHelper::custom('add','new.png','new_f2.png', JText::_('TOURNAMENT_CREATE'), false);
		JToolBarHelper::customX('copy', 'copy.png', 'copy_f2.png', JText::_('JTOOLBAR_DUPLICATE'));
	}	
	$clmAccess->accesspoint = 'BE_tournament_edit_detail';
	if($clmAccess->access() !== false) {	
		JToolBarHelper::editListX();
		JToolBarHelper::spacer();
	}	
	$clmAccess->accesspoint = 'BE_tournament_edit_round';
	if($clmAccess->access() !== false) {	
		JToolBarHelper::custom('createRounds', 'back.png', 'edit_f2.png', JText::_('ROUNDS_CREATE'), TRUE);
		JToolBarHelper::custom('deleteRounds', 'cancel.png', 'unarchive_f2.png', JText::_('ROUNDS_DELETE'), TRUE);
		JToolBarHelper::spacer();
	}
	$clmAccess->accesspoint = 'BE_tournament_edit_detail';
	if($clmAccess->access() !== false) {	
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
	}	
	$clmAccess->accesspoint = 'BE_tournament_delete';
	if($clmAccess->access() !== false) {	
		JToolBarHelper::spacer();
		JToolBarHelper::custom('delete','delete.png','delete_f2.png', JText::_('TOURNAMENT_DELETE'), true);
	}

		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   &$this->getModel();

		

		// Daten an Template übergeben
		$this->assignRef('user', $model->user);
		
		$this->assignRef('turniere', $model->turniere);

		$this->assignRef('param', $model->param);

		$this->assignRef('form', $model->form);
		
		$this->assignRef('pagination', $model->pagination);

		// zusätzliche Funktionalitäten
		JHtml::_('behavior.tooltip');

		parent::display();

	}

}
?>