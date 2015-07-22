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

class CLMViewTurnier_Rangliste extends JViewLegacy
{
	function display($tpl = ch)
	{
		$model		= $this->getModel();
		$daten		= $model->getCLMTurnier();
		$this->assignRef('daten'  , $daten);

		$model		= $this->getModel();
		$rang		= $model->getCLMRang();
		$this->assignRef('rang'  , $rang);

		$model		= $this->getModel();
		$runde		= $model->getCLMRunde();
		$this->assignRef('runde'  , $runde);

		parent::display($tpl);
	}
}
?>
