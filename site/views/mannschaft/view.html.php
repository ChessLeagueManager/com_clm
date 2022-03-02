<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

jimport( 'joomla.application.component.view');

class CLMViewMannschaft extends JViewLegacy
{
	function display($tpl = null)
	{
		$config = clm_core::$db->config();
		$googlemaps     = $config->googlemaps;
		
		if (isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] != 'off') {
			$prot = 'https';
		} else {
			$prot = 'http';
		}
		$document =JFactory::getDocument();
		if ($googlemaps == 1) {
			$document->addScript($prot.'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js');
			$document->addStyleSheet($prot.'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css');
		}
		
		$document->addScript($prot.'://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js');
		$document->addScript(JURI::base().'components/com_clm/javascript/updateTableHeaders.js');
		
		$model	  = $this->getModel();
  		$mannschaft     = $model->getCLMMannschaft();
		$this->mannschaft = $mannschaft;

		$model	  = $this->getModel();
  		$vereine     = $model->getCLMVereine();
		$this->vereine = $vereine;

		$model	  = $this->getModel();
  		$count     = $model->getCLMCount();
		$this->count = $count;

		$model	  = $this->getModel();
  		$bp     = $model->getCLMBP();
		$this->bp = $bp;

		$model	  = $this->getModel();
  		$sumbp     = $model->getCLMSumBP();
		$this->sumbp = $sumbp;

		$model	  = $this->getModel();
  		$plan     = $model->getCLMSumPlan();
		$this->plan = $plan;

		$model	  = $this->getModel();
		$termin     = $model->getCLMTermin();
		$this->termin = $termin;

		$model	  = $this->getModel();
		$saison     = $model->getCLMSaison();
		$this->saison = $saison;

		$model	  = $this->getModel();
		$einzel     = $model->getCLMEinzel();
		$this->einzel = $einzel;

		parent::display($tpl);
	}	
}
?>
