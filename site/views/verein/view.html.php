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

class CLMViewVerein extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$config = clm_core::$db->config();
		$googlemaps_api = $config->googlemaps_api;
		$googlemaps     = $config->googlemaps;
		
		$model	  = $this->getModel();
		$verein     = $model->getCLMVerein();
		$this->assignRef('verein'  , $verein);
		
		$model	  = $this->getModel();
		$vereinstats     = $model->getCLMVereinstats();
		$this->assignRef('vereinstats'  , $vereinstats);

		$model	  = $this->getModel();
		$mannschaft     = $model->getCLMMannschaft();
		$this->assignRef('mannschaft'  , $mannschaft);
		
		$model	  = $this->getModel();
		$vereinsliste     = $model->getCLMVereinsliste();
		$this->assignRef('vereinsliste'  , $vereinsliste);
		
		$model	  = $this->getModel();
		$saisons     = $model->getCLMSaisons();
		$this->assignRef('saisons'  , $saisons);
		
		$model	  = $this->getModel();
		$turniere     = $model->getCLMTurniere();
		$this->assignRef('turniere'  , $turniere);
		
		$model	  = $this->getModel();
  		$row     = $model->getCLMData();
		$this->assignRef('row'  , $row);

		$model	  = $this->getModel();
  		$name     = $model->getCLMName();
		$this->assignRef('name'  , $name);

		$model	  = $this->getModel();
  		$clmuser     = $model->getCLMCLMuser();
		$this->assignRef('clmuser'  , $clmuser);
		
		$document =JFactory::getDocument();
		
		if ($googlemaps == 1) {
		$document->addScript('http://maps.google.com/maps?file=api&v=2&key='.$googlemaps_api.'');
		}
		
		// Title in Browser
		if (isset($verein[0])) {
			$headTitle = CLMText::composeHeadTitle( array( $verein[0]->name ) );
			$document->setTitle( $headTitle ); }
		
		parent::display($tpl);
	}	
}
?>
