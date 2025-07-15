<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

jimport( 'joomla.application.component.view');

class CLMViewRunde extends JViewLegacy
{
	function display($tpl = "raw")
	{
		$model	  = $this->getModel();
  		$liga     = $model->getCLMLiga();
		$this->liga = $liga;

		$model	  = $this->getModel();
  		$mannschaft     = $model->getCLMMannschaft();
		$this->mannschaft = $mannschaft;

		$model	  = $this->getModel();
  		$paar     = $model->getCLMPaar();
		$this->paar = $paar;

		$model	  = $this->getModel();
		$offen     = $model->getCLMOffen();
		$this->offen = $offen;

		$model	= $this->getModel();
  		$einzel	= $model->getCLMEinzel();
		$this->einzel = $einzel;

		$model	= $this->getModel();
  		$summe	= $model->getCLMSumme();
		$this->summe = $summe;

		$model	= $this->getModel();
  		$ok	= $model->getCLMOK();
		$this->ok = $ok;

		$model	= $this->getModel();
		$punkte	= $model->getCLMPunkte();
		$this->punkte = $punkte;

		$model		= $this->getModel();
		$spielfrei	= $model->getCLMSpielfrei();
		$this->spielfrei = $spielfrei;

		$html	= clm_core::$load->request_string('html','1');
		if ($html !="1") {
			$document =JFactory::getDocument();
			$document->setMimeEncoding('text/css');
		}

		parent::display($tpl);
	}	
}
?>
