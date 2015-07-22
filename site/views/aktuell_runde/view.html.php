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

class CLMViewAktuell_Runde extends JView
{
	function display($tpl = null)
	{		
		$model	  = &$this->getModel();
  		$liga     = $model->getCLMLiga();
		$this->assignRef('liga'  , $liga);

		$model	  = &$this->getModel();
  		$mannschaft     = $model->getCLMMannschaft();
		$this->assignRef('mannschaft'  , $mannschaft);

		$model	  = &$this->getModel();
  		$paar     = $model->getCLMPaar();
		$this->assignRef('paar'  , $paar);

		$model	  = &$this->getModel();
  		$dwzschnitt     = $model->getCLMDWZSchnitt();
		$this->assignRef('dwzschnitt'  , $dwzschnitt);

		$model	  = &$this->getModel();
		$dwzgespielt     = $model->getCLMDWZgespielt();
		$this->assignRef('dwzgespielt'  , $dwzgespielt);

		$model	= &$this->getModel();
  		$einzel	= $model->getCLMEinzel();
		$this->assignRef('einzel'  , $einzel);

		$model	= &$this->getModel();
  		$summe	= $model->getCLMSumme();
		$this->assignRef('summe'  , $summe);

		$model	= &$this->getModel();
  		$ok	= $model->getCLMOK();
		$this->assignRef('ok'  , $ok);

	$model			= &$this->getModel();
	$dwzschnitt_rang	= $model->getCLMDWZSchnitt_rang();
	$this->assignRef('dwzschnitt_rang'  , $dwzschnitt_rang);

	$model	= &$this->getModel();
	$punkte	= $model->getCLMPunkte();
	$this->assignRef('punkte'  , $punkte);

	$model		= &$this->getModel();
	$spielfrei	= $model->getCLMSpielfrei();
	$this->assignRef('spielfrei'  , $spielfrei);

	parent::display($tpl);
	}	
}
?>
