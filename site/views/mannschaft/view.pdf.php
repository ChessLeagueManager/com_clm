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
	function display($tpl = 'pdf')
	{
		$model	  = $this->getModel();
  		$mannschaft     = $model->getCLMMannschaft();
		$this->mannschaft = $mannschaft;

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
		
		//neu Einzelergebnisse (klkl)
		$einzel     = $model->getCLMEinzel();
		$this->einzel = $einzel;

		//neu Saison (klkl)
		$saison     = $model->getCLMSaison();
		$this->saison = $saison;

	// Dokumenttyp setzen
		$document =Factory::getDocument();
		$document->setMimeEncoding('application/pdf');

		parent::display($tpl);
	}	
}
?>
