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

class CLMViewTurInvite extends JView {

	function display() {

		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   &$this->getModel();
		
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'admin_menue_images.php');
		JToolBarHelper::title( $model->turnier->name.": ".JText::_('INVITATION'), 'clm_turnier.png'  );
	
	
		JToolBarHelper::save( 'save' );
		JToolBarHelper::apply( 'apply' );
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();

		// das MainMenu abschalten
		JRequest::setVar( 'hidemainmenu', 1 );
	

		$this->assignRef('turnier', $model->turnier);

		$this->assignRef('param', $model->param);


		parent::display();

	}

}
?>