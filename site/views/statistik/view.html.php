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

class CLMViewStatistik extends JViewLegacy
{
	function display($tpl = null)
	{
		$model		= $this->getModel();
		$liga		= $model->getCLMliga();
		$this->assignRef('liga'  , $liga);

		$model		= $this->getModel();
		$remis		= $model->getCLMRemis();
		$this->assignRef('remis'  , $remis);

		$model		= $this->getModel();
		$kampflos	= $model->getCLMKampflos();
		$this->assignRef('kampflos'  , $kampflos);

		$model		= $this->getModel();
		$heim		= $model->getCLMHeim();
		$this->assignRef('heim'  , $heim);

		$model		= $this->getModel();
		$gast		= $model->getCLMGast();
		$this->assignRef('gast'  , $gast);

		$model		= $this->getModel();
		$gesamt		= $model->getCLMGesamt();
		$this->assignRef('gesamt'  , $gesamt);

		//$model		= $this->getModel();
		//$spieler	= $model->getCLMSpieler();
		//$this->assignRef('spieler'  , $spieler);

		$model		= $this->getModel();
		$bestenliste	= $model->getCLMBestenliste();
		$this->assignRef('bestenliste'  , $bestenliste);
		
		$model		= $this->getModel();
		$mannschaft	= $model->getCLMMannschaft();
		$this->assignRef('mannschaft'  , $mannschaft);

		$model		= $this->getModel();
		$brett		= $model->getCLMBrett();
		$this->assignRef('brett'  , $brett);

		$model		= $this->getModel();
		$gbrett		= $model->getCLMGBrett();
		$this->assignRef('gbrett'  , $gbrett);

		$model		= $this->getModel();
		$rbrett		= $model->getCLMRBrett();
		$this->assignRef('rbrett'  , $rbrett);

		$model		= $this->getModel();
		$kbrett		= $model->getCLMKBrett();
		$this->assignRef('kbrett'  , $kbrett);

		$model		= $this->getModel();
		$kgmannschaft	= $model->getCLMkgMannschaft();
		$this->assignRef('kgmannschaft'  , $kgmannschaft);

		$model		= $this->getModel();
		$kvmannschaft	= $model->getCLMkvMannschaft();
		$this->assignRef('kvmannschaft'  , $kvmannschaft);

		$document =JFactory::getDocument();
		
		// Title in Browser
		$headTitle = CLMText::composeHeadTitle( array( $liga[0]->name, JText::_('LEAGUE_STATISTIK') ) );
		$document->setTitle( $headTitle );

		/* Call the state object */
		$state = $this->get( 'state' );
 
		/* Get the values from the state object that were inserted in the model's construct function */
		$lists['order']     = $state->get( 'filter_order_bl' );
		$lists['order_Dir'] = $state->get( 'filter_order_Dir_bl' );
 
		$this->assignRef( 'lists', $lists );

		parent::display($tpl);
	}	
}
?>
