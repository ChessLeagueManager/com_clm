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

class CLMViewTurnier_Teilnehmer extends JView
{
	function display($tpl = 'pdf')
	// Man beachte den Unterschied zum Standard View "$tpl = null" !!
	{
		$config	= &JComponentHelper::getParams( 'com_clm' );
		$model	  = &$this->getModel();
  		
		$this->assignRef('turnier', $model->turnier);
		$this->assignRef('tourn_linkclub', $config->get('tourn_linkclub', 1));
		$this->assignRef('players', $model->players);
		
	// Dokumenttyp setzen
		$document =& JFactory::getDocument();
		$document->setMimeEncoding('application/pdf');
		parent::display($tpl);
	}	
}
?>
