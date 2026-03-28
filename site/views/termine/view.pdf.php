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
jimport( 'joomla.application.component.view');

use Joomla\CMS\Factory;

class CLMViewTermine extends JViewLegacy
{
	function display($tpl = 'pdf')
	// Man beachte den Unterschied zum Standard View "$tpl = null" !!
	{
		$model	  		= $this->getModel();
		$termine     	= $model->getTermine();
		$this->termine = $termine;
		
		$model	  		= $this->getModel();
		$termine_detail     	= $model->getTermine_Detail();
		$this->termine_detail = $termine_detail;
		
		$model	  		= $this->getModel();
		$plan  			= $model->getCLMSumPlan();
		$this->plan = $plan;
		
		$model	  		= $this->getModel();
		$schnellmenu  	= $model->getSchnellmenu();
		$this->schnellmenu = $schnellmenu;
		
	// Dokumenttyp setzen
		$document =Factory::getDocument();
		$document->setMimeEncoding('application/pdf');
		parent::display($tpl);
	}	
}
?>
