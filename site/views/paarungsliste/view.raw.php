<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

jimport( 'joomla.application.component.view');

class CLMViewPaarungsliste extends JViewLegacy
{
	function display($tpl = "raw")
	{
		$model	  = $this->getModel();
		$liga     = $model->getCLMLiga();
		$this->liga = $liga;

		$model	  = $this->getModel();
		$termin     = $model->getCLMTermin();
		$this->termin = $termin;

		$model	  = $this->getModel();
		$paar     = $model->getCLMPaar();
		$this->paar = $paar;

		$model	  = $this->getModel();
		$summe     = $model->getCLMSumme();
		$this->summe = $summe;

		$model	  = $this->getModel();
		$rundensumme     = $model->getCLMRundensumme();
		$this->rundensumme = $rundensumme;

		$model	  = $this->getModel();
		$arbiter     = $model->getCLMArbiter();
		$this->arbiter = $arbiter;

	$html	= clm_core::$load->request_string('html','1');
	if($html !="1"){
		$document =JFactory::getDocument();
		$document->setMimeEncoding('text/css');
	}
		parent::display($tpl);
	}	
}
?>
