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

class CLMViewStatistik extends JViewLegacy
{
	function display($tpl = 'pdf')
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
		$rbrett		= $model->getCLMRBrett();
		$this->assignRef('rbrett'  , $rbrett);

		$model		= $this->getModel();
		$kbrett		= $model->getCLMKBrett();
		$this->assignRef('kbrett'  , $kbrett);

	// Dokumenttyp setzen
		$document =JFactory::getDocument();
		$document->setMimeEncoding('application/pdf');

		parent::display($tpl);
	}	
}
?>
