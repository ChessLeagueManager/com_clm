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

jimport( 'joomla.application.component.view');

class CLMViewVereinsliste extends JViewLegacy
{
	function display($tpl = "raw") {
	
		$model	  = $this->getModel();
		$vereinsliste     = $model->getCLMVereinsliste();
		$this->assignRef('vereinsliste'  , $vereinsliste);
		
		$model	  = $this->getModel();
		$vereine     = $model->getCLMVereine();
		$this->assignRef('vereine'  , $vereine);
		
		$model		= $this->getModel();
		
		$mainframe = JFactory::getApplication();
		$document =JFactory::getDocument();
		$document->setTitle($mainframe->getCfg('sitename')." - ".JText::_('CLUBS_LIST'));
		
		parent::display($tpl);
	}	
}
?>
