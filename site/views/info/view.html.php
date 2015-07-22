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

class CLMViewInfo extends JViewLegacy
{
	function display($tpl = null)
	{
		$model		= $this->getModel();
		$saison		= $model->getCLMSaison();
		$this->assignRef('saison'  , $saison);

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

		$model		= $this->getModel();
		$spieler	= $model->getCLMSpieler();
		$this->assignRef('spieler'  , $spieler);

		$model		= $this->getModel();
		$mannschaft	= $model->getCLMMannschaft();
		$this->assignRef('mannschaft'  , $mannschaft);

		$model		= $this->getModel();
		$brett		= $model->getCLMBrett();
		$this->assignRef('brett'  , $brett);

		$model		= $this->getModel();
		$wbrett		= $model->getCLMWBrett();
		$this->assignRef('wbrett'  , $wbrett);

		$model		= $this->getModel();
		$sbrett		= $model->getCLMSBrett();
		$this->assignRef('sbrett'  , $sbrett);

		$model		= $this->getModel();
		$rbrett		= $model->getCLMRBrett();
		$this->assignRef('rbrett'  , $rbrett);

		$model		= $this->getModel();
		$kbrett		= $model->getCLMKBrett();
		$this->assignRef('kbrett'  , $kbrett);

		parent::display($tpl);
	}	
}
?>
