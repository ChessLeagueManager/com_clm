<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

jimport( 'joomla.application.component.view');

use Joomla\CMS\Factory;

class CLMViewDWZ_Liga extends JViewLegacy
{
	function display($tpl = "raw")
	{
		$model		= $this->getModel();
  		$liga		= $model->getCLMLiga();
		$this->liga = $liga;

		$model		= $this->getModel();
  		$spieler	= $model->getCLMSpieler();
		$this->spieler = $spieler;

		$model		= $this->getModel();
  		$dwz		= $model->getCLMdwz();
		$this->dwz = $dwz;

		$html	= clm_core::$load->request_string('html','1');
		if($html !="1"){
			$document =Factory::getDocument();
			$document->setMimeEncoding('text/css');
		}

		parent::display($tpl);
	}
}
?>
