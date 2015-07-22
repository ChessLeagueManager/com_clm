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

class CLMViewDWZ_Liga extends JViewLegacy
{
	function display($tpl = null)
	{
		$model		= $this->getModel();
  		$liga		= $model->getCLMLiga();
		$this->assignRef('liga' , $liga);

		$model		= $this->getModel();
  		$spieler	= $model->getCLMSpieler();
		$this->assignRef('spieler' , $spieler);

		$model		= $this->getModel();
  		$dwz		= $model->getCLMdwz();
		$this->assignRef('dwz' , $dwz);
		
		$model		= $this->getModel();   

		parent::display($tpl);
	}
}
?>
