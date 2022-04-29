<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2022 CLM Team. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

jimport( 'joomla.application.component.view');

class CLMViewDWZ extends JViewLegacy
{
	function display($tpl = "raw")
	{
		$model	  = $this->getModel();
  		$liga     = $model->getCLMLiga();
		$this->liga = $liga;

		$model = $this->getModel();
		$zps = $model->getCLMzps();
		$this->zps = $zps;

		$html	= clm_core::$load->request_string('html','1');
		if($html !="1"){
			$document =JFactory::getDocument();
			$document->setMimeEncoding('text/css');
		}

		parent::display($tpl);
	}	
}
?>
