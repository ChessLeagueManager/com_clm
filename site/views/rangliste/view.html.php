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

class CLMViewRangliste extends JViewLegacy
{
	function display($tpl = null)
	{
		$model	  = $this->getModel();
  		$liga     = $model->getCLMLiga();
		$this->assignRef('liga'  , $liga);

		$model	  = $this->getModel();
  		$spielfrei     = $model->getCLMSpielfrei();
		$this->assignRef('spielfrei'  , $spielfrei);

		$model	  = $this->getModel();
  		$punkte     = $model->getCLMPunkte();
		$this->assignRef('punkte'  , $punkte);

		$model	  = $this->getModel();
		$dwzschnitt     = $model->getCLMDWZSchnitt();
		$this->assignRef('dwzschnitt'  , $dwzschnitt);

		parent::display($tpl);
	}	
}
?>
