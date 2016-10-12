<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
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
		$googlemaps_api = $config->googlemaps_api;
		$googlemaps     = $config->googlemaps;
		
		$document =JFactory::getDocument();
		if ($googlemaps == 1) {
		$document->addScript('http://maps.google.com/maps?file=api&v=2&key='.$googlemaps_api.'');
		}
		
		$document->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js');
		$document->addScript(JURI::base().'components/com_clm/javascript/updateTableHeaders.js');
		
		$model	  = $this->getModel();
  		$mannschaft     = $model->getCLMMannschaft();
		$this->assignRef('mannschaft'  , $mannschaft);

		$model	  = $this->getModel();
  		$vereine     = $model->getCLMVereine();
		$this->assignRef('vereine'  , $vereine);

		$model	  = $this->getModel();
  		$count     = $model->getCLMCount();
		$this->assignRef('count'  , $count);

		$model	  = $this->getModel();
  		$bp     = $model->getCLMBP();
		$this->assignRef('bp'  , $bp);

		$model	  = $this->getModel();
  		$sumbp     = $model->getCLMSumBP();
		$this->assignRef('sumbp'  , $sumbp);

		$model	  = $this->getModel();
  		$plan     = $model->getCLMSumPlan();
		$this->assignRef('plan'  , $plan);

		$model	  = $this->getModel();
		$termin     = $model->getCLMTermin();
		$this->assignRef('termin'  , $termin);

		$model	  = $this->getModel();
		$saison     = $model->getCLMSaison();
		$this->assignRef('saison'  , $saison);

		$model	  = $this->getModel();
		$einzel     = $model->getCLMEinzel();
		$this->assignRef('einzel'  , $einzel);

		parent::display($tpl);
	}	
}
?>
