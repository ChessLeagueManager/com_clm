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

class CLMViewTermine extends JViewLegacy
{
	function display($tpl = 'pdf')
	// Man beachte den Unterschied zum Standard View "$tpl = null" !!
	{
		$model	  		= $this->getModel();
		$termine     	= $model->getTermine();
		$this->assignRef('termine'  , $termine);
		
		$model	  		= $this->getModel();
		$termine_detail     	= $model->getTermine_Detail();
		$this->assignRef('termine_detail'  , $termine_detail);
		
		$model	  		= $this->getModel();
		$plan  			= $model->getCLMSumPlan();
		$this->assignRef('plan'  , $plan);
		
		$model	  		= $this->getModel();
		$schnellmenu  	= $model->getSchnellmenu();
		$this->assignRef('schnellmenu'  , $schnellmenu);
		
	// Dokumenttyp setzen
		$document =JFactory::getDocument();
		$document->setMimeEncoding('application/pdf');
		parent::display($tpl);
	}	
}
?>
