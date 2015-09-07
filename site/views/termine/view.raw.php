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
	function display($tpl = "raw")
	{
		$model	  = $this->getModel();
		$termine     = $model->getTermine();
		$this->assignRef('termine'  , $termine);
		
		parent::display($tpl);
	}	
}
?>
