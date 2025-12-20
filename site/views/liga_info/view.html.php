<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
jimport( 'joomla.application.component.view');

class CLMViewLiga_Info extends JViewLegacy
{
	function display($tpl = null)
	{
		$model	  = $this->getModel();
  		$liga     = $model->getCLMLiga();
		$this->liga = $liga;

		$model	  = $this->getModel();
  		$spielfrei     = $model->getCLMSpielfrei();
		$this->spielfrei = $spielfrei;

		$model	  = $this->getModel();
  		$punkte     = $model->getCLMPunkte();
		$this->punkte = $punkte;

		$model	  = $this->getModel();
		$dwzschnitt     = $model->getCLMDWZSchnitt();
		$this->dwzschnitt = $dwzschnitt;

		$model	  = $this->getModel();
		$arbiter     = $model->getCLMArbiter();
		$this->arbiter = $arbiter;
		
		$config = clm_core::$db->config();
		$googlemaps     = $config->googlemaps;
		$googlemaps_ver     = $config->googlemaps_ver;
		
		if (isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] != 'off') {
			$prot = 'https';
		} else {
			$prot = 'http';
		}
		
		$document =JFactory::getDocument();
		
		if ($googlemaps == 1) {
			if ($googlemaps_ver == 1){ //Load Leaflet
				$document->addScript($prot.'://unpkg.com/leaflet@1.9.4/dist/leaflet.js');
				$document->addStyleSheet($prot.'://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
			}
			elseif ($googlemaps_ver == 3){ //Load OSM
				$document->addScript($prot.'://cdn.rawgit.com/openlayers/openlayers.github.io/master/en/v5.3.0/build/ol.js');
				$document->addStyleSheet($prot.'://cdn.rawgit.com/openlayers/openlayers.github.io/master/en/v5.3.0/css/ol.css');
			}
		}
		
		parent::display($tpl);
	}	
}
?>
