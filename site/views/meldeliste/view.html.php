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

class CLMViewMeldeliste extends JViewLegacy
{
	function display($tpl = null)
	{
	$layout = JRequest::getVar('layout');

		$model	= $this->getModel();
		$liga	= $model->getCLMLiga();
		$this->assignRef('liga',$liga);

		$model	= $this->getModel();
		$spieler= $model->getCLMSpieler();
		$this->assignRef('spieler',$spieler);

		$model	= $this->getModel();
		$count	= $model->getCLMCount();
		$this->assignRef('count',$count);

		$model	= $this->getModel();
		$clmuser= $model->getCLMCLMuser();
		$this->assignRef('clmuser',$clmuser);

	if (!isset($layout) OR $layout == '') {
		$model	= $this->getModel();
		$access	= $model->getCLMAccess();
		$this->assignRef('access',$access);
			}

		$model	= $this->getModel();
		$abgabe	= $model->getCLMAbgabe();
		$this->assignRef('abgabe',$abgabe);

		$model	= $this->getModel();
		$mllist	= $model->getCLMML();
		$this->assignRef('mllist',$mllist);

		parent::display($tpl);
	}	
}
?>
