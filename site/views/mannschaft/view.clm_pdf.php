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

class CLMViewMannschaft extends JViewLegacy
{
	function display($tpl = 'pdf')
	{
		$model	  = $this->getModel();
  		$mannschaft     = $model->getCLMMannschaft();
		$this->assignRef('mannschaft'  , $mannschaft);

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
		
		//neu Einzelergebnisse (klkl)
		$model	  = $this->getModel();
		$einzel     = $model->getCLMEinzel();
		$this->assignRef('einzel'  , $einzel);

		//neu Saison (klkl)
		$model	  = $this->getModel();
		$saison     = $model->getCLMSaison();
		$this->assignRef('saison'  , $saison);

	// Dokumenttyp setzen
		$document =JFactory::getDocument();
		$document->setMimeEncoding('application/pdf');

		parent::display($tpl);
	}	
}
?>
