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

class CLMViewVerein extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$config = clm_core::$db->config();
		$googlemaps     = $config->googlemaps;
		
		$model	  = $this->getModel();
		$verein     = $model->getCLMVerein();
		$this->verein = $verein;
		
		$model	  = $this->getModel();
		$vereinstats     = $model->getCLMVereinstats();
		$this->vereinstats = $vereinstats;

		$model	  = $this->getModel();
		$mannschaft     = $model->getCLMMannschaft();
		$this->mannschaft = $mannschaft;
		
		$model	  = $this->getModel();
		$vereinsliste     = $model->getCLMVereinsliste();
		$this->vereinsliste = $vereinsliste;
		
		$model	  = $this->getModel();
		$saisons     = $model->getCLMSaisons();
		$this->saisons = $saisons;
		
		$model	  = $this->getModel();
		$turniere     = $model->getCLMTurniere();
		$this->turniere = $turniere;
		
		$model	  = $this->getModel();
  		$row     = $model->getCLMData();
		$this->row = $row;

		$model	  = $this->getModel();
  		$name     = $model->getCLMName();
		$this->name = $name;

		$model	  = $this->getModel();
  		$clmuser     = $model->getCLMCLMuser();
		$this->clmuser = $clmuser;
		
		if (isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] != 'off') {
			$prot = 'https';
		} else {
			$prot = 'http';
		}
		$document =JFactory::getDocument();
		
		if ($googlemaps == 1) {
			$document->addScript($prot.'://unpkg.com/leaflet@1.7.1/dist/leaflet.js');
			$document->addStyleSheet($prot.'://unpkg.com/leaflet@1.7.1/dist/leaflet.css');
		}
		
		// Title in Browser
		if (isset($verein[0])) {
			$headTitle = CLMText::composeHeadTitle( array( $verein[0]->name ) );
			$document->setTitle( $headTitle ); }
		
		parent::display($tpl);
	}	
}
?>
