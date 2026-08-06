<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
jimport( 'joomla.application.component.view');

use Joomla\CMS\Factory;

class CLMViewMannschaft extends JViewLegacy
{
	function display($tpl = "raw")
	{
		$model	  = $this->getModel();
  		$mannschaft     = $model->getCLMMannschaft();
		$this->mannschaft = $mannschaft;

  		$vereine     = $model->getCLMVereine();
		$this->vereine = $vereine;

  		$count     = $model->getCLMCount();
		$this->count = $count;

  		$bp     = $model->getCLMBP();
		$this->bp = $bp;

  		$sumbp     = $model->getCLMSumBP();
		$this->sumbp = $sumbp;

  		$plan     = $model->getCLMSumPlan();
		$this->plan = $plan;

		$termin     = $model->getCLMTermin();
		$this->termin = $termin;

		$saison     = $model->getCLMSaison();
		$this->saison = $saison;

		$clmuser     = $model->getCLMClmuser();
		$this->clmuser = $clmuser;

		$einzel     = $model->getCLMEinzel();
		$this->einzel = $einzel;
		
		$html	= clm_core::$load->request_string('html','1');
		if($html !="1"){
			$document =Factory::getDocument();
			$document->setMimeEncoding('text/css');
		}

		parent::display($tpl);
	}	
}
?>
