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

class CLMViewAktuell_Runde extends JViewLegacy
{
	function display($tpl = "raw")
	{

		$html	= JRequest::getInt('html','1');
		if($html !="1"){
			$document =JFactory::getDocument();
			$document->setMimeEncoding('text/css');
		}
		parent::display($tpl);
	}	
}
?>
