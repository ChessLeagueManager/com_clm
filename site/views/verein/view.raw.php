<?php
/**
 * @ Chess League Manager (CLM) Component 
  * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
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
	function display($tpl = "raw")
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

	$html	= JRequest::getInt('html','1');
	if($html !="1"){
		$document =JFactory::getDocument();
		$document->setMimeEncoding('text/css');
		}

		parent::display($tpl);
	}	
}
?>
