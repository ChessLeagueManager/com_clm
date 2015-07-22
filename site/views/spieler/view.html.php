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

class CLMViewSpieler extends JView
{
	function display($tpl = null)
	{
		$model	  = &$this->getModel();
  		$spieler     = $model->getCLMSpieler();
		$this->assignRef('spieler'  , $spieler);

		$model	  = &$this->getModel();
  		$runden     = $model->getCLMRunden();
		$this->assignRef('runden'  , $runden);
		
		$model		= &$this->getModel();   
  		$dwz_date_new		= $model->getCLMlog();
		$this->assignRef('dwz_date_new' , $dwz_date_new);
		
		$model	  = &$this->getModel();
		$spielerliste     = $model->getCLMSpielerliste();
		$this->assignRef('spielerliste'  , $spielerliste);
		
		$model	  = &$this->getModel();
		$vereinsliste     = $model->getCLMVereinsliste();
		$this->assignRef('vereinsliste'  , $vereinsliste);
		
		$model	  = &$this->getModel();
		$saisons     = $model->getCLMSaisons();
		$this->assignRef('saisons'  , $saisons);
		
		$document =& JFactory::getDocument();
		
		// Title in Browser
		$headTitle = CLMText::composeHeadTitle( array( $spieler[0]->Vereinname, $spieler[0]->Spielername ) );
		$document->setTitle( $headTitle );


		parent::display($tpl);
	}	
}
?>
