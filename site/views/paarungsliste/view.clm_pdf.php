<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

jimport( 'joomla.application.component.view');
/**
 * HTML View class for the auto Component
 */
class CLMViewPaarungsliste extends JViewLegacy
{
	function display($tpl = 'pdf')
	{
		$model	  = $this->getModel();
		$liga     = $model->getCLMLiga();
		$this->assignRef('liga'  , $liga);

		$model	  = $this->getModel();
		$termin     = $model->getCLMTermin();
		$this->assignRef('termin'  , $termin);

		$model	  = $this->getModel();
		$paar     = $model->getCLMPaar();
		$this->assignRef('paar'  , $paar);

		$model	  = $this->getModel();
		$dwzschnitt     = $model->getCLMDWZSchnitt();
		$this->assignRef('dwzschnitt'  , $dwzschnitt);

		$model	  = $this->getModel();
		$dwzgespielt     = $model->getCLMDWZgespielt();
		$this->assignRef('dwzgespielt'  , $dwzgespielt);

		$model	  = $this->getModel();
		$summe     = $model->getCLMSumme();
		$this->assignRef('summe'  , $summe);

		$model	  = $this->getModel();
		$rundensumme     = $model->getCLMRundensumme();
		$this->assignRef('rundensumme'  , $rundensumme);

		$document =JFactory::getDocument();
		$document->setMimeEncoding('application/pdf');

		parent::display($tpl);
	}	
}
?>
