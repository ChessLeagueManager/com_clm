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

class CLMViewVerein extends JView
{
	function display($tpl = 'raw')
	{
		$model	  = &$this->getModel();
		$verein     = $model->getCLMVerein();
		$this->assignRef('verein'  , $verein);

		$model	  = &$this->getModel();
		$mannschaft     = $model->getCLMMannschaft();
		$this->assignRef('mannschaft'  , $mannschaft);

	$html	= JRequest::getInt('html','1');
	if($html !="1"){
		$document =& JFactory::getDocument();
		$document->setMimeEncoding('text/css');
		}

		parent::display($tpl);
	}	
}
?>
