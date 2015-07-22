<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Fjodor Schäfer
 * @email ich@vonfio.de
*/

jimport( 'joomla.application.component.view');

class CLMViewTermine extends JView
{
	function display($tpl = null)
	{
		
		$model	  		= &$this->getModel();
		$termine     	= $model->getTermine();
		$this->assignRef('termine'  , $termine);
		
		$model	  		= &$this->getModel();
		$termine_detail     	= $model->getTermine_Detail();
		$this->assignRef('termine_detail'  , $termine_detail);
		
		$model	  		= &$this->getModel();
		$schnellmenu  	= $model->getSchnellmenu();
		$this->assignRef('schnellmenu'  , $schnellmenu);
		
		parent::display($tpl);
	}	
}
?>