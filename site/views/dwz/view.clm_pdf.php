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

class CLMViewDWZ extends JViewLegacy
{
	function display($tpl = 'pdf')
	{
		$model	  = $this->getModel();
  		$liga     = $model->getCLMLiga();
		$this->assignRef('liga'  , $liga);

		$model	= $this->getModel();
		$zps	= $model->getCLMzps();
		$this->assignRef('zps'  , $zps);

$document =JFactory::getDocument();
$document->setMimeEncoding('application/pdf');

		parent::display($tpl);
	}	
}
?>
