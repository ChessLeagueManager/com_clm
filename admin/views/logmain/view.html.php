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

class CLMViewLogMain extends JView {

	function display() {

		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'images'.DS.'admin_menue_images.php');
	
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		JToolBarHelper::title(   JText::_( 'TITLE_LOGFILE' ), 'clm_headmenu_einstellungen.png' );

		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'classes'.DS.'CLMAccess.class.php');
		$clmAccess = new CLMAccess();
		$clmAccess->accesspoint = 'BE_logfile_delete';
		if ( $clmAccess->access() ) {
		//if ( CLM_usertype == 'admin') {
			JToolBarHelper::custom('delete', 'cancel.png', 'unarchive_f2.png', JText::_('LOGFILE_DELETE'), false); 
			JToolBarHelper::custom('deleteAll', 'cancel.png', 'unarchive_f2.png', JText::_('LOGFILE_DELETE_ALL'), false);
		}
		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   &$this->getModel();
		
		// Daten an Template übergeben
		$this->assignRef('log', $model->log);

		$this->assignRef('param', $model->param);

		$this->assignRef('forms', $model->forms);
		
		$this->assignRef('pagination', $model->pagination);


		parent::display();

	}

}
?>