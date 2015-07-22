<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2014 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

jimport( 'joomla.application.component.view');

class CLMViewMeldung extends JView
{
	function display($tpl = null)
	{
	$layout= JRequest::getVar('layout');
	if ($layout != 'check' AND $layout != 'sent') {

	$model	  = &$this->getModel();
  		$liga     = $model->getCLMLiga();
		$this->assignRef('liga'  , $liga);

		$model	  = &$this->getModel();
  		$paar     = $model->getCLMPaar();
		$this->assignRef('paar'  , $paar);

		$model	  = &$this->getModel();
  		$countheim     = $model->getCLMCountHeim();
		$this->assignRef('countheim'  , $countheim);

		$model	  = &$this->getModel();
  		$heim     = $model->getCLMHeim();
		$this->assignRef('heim'  , $heim);

		$model	  = &$this->getModel();
  		$countgast     = $model->getCLMCountGast();
		$this->assignRef('countgast'  , $countgast);

		$model	  = &$this->getModel();
  		$gast     = $model->getCLMGast();
		$this->assignRef('gast'  , $gast);

		$model	  = &$this->getModel();
  		$ergebnis     = $model->getCLMErgebnis();
		$this->assignRef('ergebnis'  , $ergebnis);

		$model	  = &$this->getModel();
  		$access     = $model->getCLMAccess();
		$this->assignRef('access'  , $access);

		$model	  = &$this->getModel();
  		$clmuser     = $model->getCLMCLMuser();
		$this->assignRef('clmuser'  , $clmuser);

		$model	  = &$this->getModel();
  		$finish	  = $model->getCLMfinish();
		$this->assignRef('finish'  , $finish);

		$model	  = &$this->getModel();
  		$oldresult	  = $model->getCLMoldresult();
		$this->assignRef('oldresult'  , $oldresult);
	}

	if ($layout == 'check') {
		$model	  = &$this->getModel();
  		$liga     = $model->getCLMLiga();
		$this->assignRef('liga'  , $liga);

		$model	  = &$this->getModel();
  		$finish	  = $model->getCLMfinish();
		$this->assignRef('finish'  , $finish);
	}
		parent::display($tpl);
	}	
}
?>
