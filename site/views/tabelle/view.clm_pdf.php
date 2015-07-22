<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

jimport( 'joomla.application.component.view');

class CLMViewTabelle extends JViewLegacy
{
	function display($tpl = 'pdf')
	// Man beachte den Unterschied zum Standard View "$tpl = null" !!
	{
		$model	  = $this->getModel();
  		$liga     = $model->getCLMLiga();
		$this->assignRef('liga'  , $liga);

		$model	  = $this->getModel();
  		$spielfrei     = $model->getCLMSpielfrei();
		$this->assignRef('spielfrei'  , $spielfrei);

		$model	  = $this->getModel();
  		$punkte     = $model->getCLMPunkte();
		$this->assignRef('punkte'  , $punkte);

		$model	  = $this->getModel();
		$dwzschnitt     = $model->getCLMDWZSchnitt();
		$this->assignRef('dwzschnitt'  , $dwzschnitt);
		
		//neu: Mannschaftsliste (klkl)
		$model	  = $this->getModel();
  		$mannschaft     = $model->getCLMMannschaft();
		$this->assignRef('mannschaft'  , $mannschaft);
		
		//neu: Mannschaftsleiterliste (klkl)
		$model	  = $this->getModel();
  		$mleiter     = $model->getCLMMLeiter();
		$this->assignRef('mleiter'  , $mleiter);
		
		//neu: Meldeliste (klkl)
		$model	  = $this->getModel();
  		$count     = $model->getCLMCount();
		$this->assignRef('count'  , $count);
		
		//neu Saison (klkl)
		$model	  = $this->getModel();
		$saison     = $model->getCLMSaison();
		$this->assignRef('saison'  , $saison);

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

	// Dokumenttyp setzen
		$document =JFactory::getDocument();
		$document->setMimeEncoding('application/pdf');
 
		parent::display($tpl);
	}	
}
?>
