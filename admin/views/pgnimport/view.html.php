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

defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMViewPGNImport extends JViewLegacy {

	function display($tpl = null) { 

		//Daten vom Model
		$state		= $this->get( 'state' );
		$saisons	= $this->get( 'saisons' );
		$ligen		= $this->get( 'ligen' );
		$turniere	= $this->get( 'turniere' );
		
/*		$task = JRequest::getVar ('task', '', 'default', 'string');
		$stask = JRequest::getVar ('stask', '', 'default', 'string');
echo "<br>html-pgnimport: task $task  stask $stask "; //die();
*/					
		//Toolbar
		clm_core::$load->load_css("icons_images");
/*		if ($task == 'import') {
			JToolBarHelper::title( JText::_('TITLE_PGN_IMPORT') ,'clm_headmenu_manager.png' );
			JToolBarHelper::custom('import','new.png','new_f2.png', JText::_('PGN_IMPORT_NEW'), false);
		} else {
			JToolBarHelper::title( JText::_('TITLE_PGN_SERVICE') ,'clm_headmenu_manager.png' );
			JToolBarHelper::custom('service','new.png','new_f2.png', JText::_('PGN_SERVICE'), false);
		}
*/
		JToolBarHelper::title( JText::_('TITLE_PGN_SERVICE') ,'clm_headmenu_manager.png' );
		JToolBarHelper::custom('import','new.png','new_f2.png', JText::_('PGN_IMPORT_NEW'), false);
		JToolBarHelper::custom('maintain','edit.png','edit_f2.png', JText::_('PGN_MAINTAIN'), false);
		JToolBarHelper::custom('delete_open','delete.png','delete_f2.png', JText::_('PGN_DELETE_OPEN'), false);
		JToolBarHelper::custom('delete_all','trash.png','trash_f2.png', JText::_('PGN_DELETE_ALL'), false);
		JToolBarHelper::custom('cancel','cancel.png','cancel_f2.png', JText::_('SWT_LEAGUE_CANCEL'), false);

	
		//Saison- und Ligen-Auswahl erstellen
		$options_saisons[] = JHtml::_('select.option', '', JText::_( 'SWT_SAISONS' ));
		foreach($saisons as $saison) {
			$options_saisons[] = JHtml::_('select.option', $saison->id, $saison->name);
		}
		
		$options_ligen[] = JHtml::_('select.option', '', JText::_( 'SWT_LEAGUES' ));
		foreach($ligen as $liga) {
			$options_ligen[] = JHtml::_('select.option', 't.'.$liga->id, $liga->name);
		}
		$options_ligen[] = JHtml::_('select.option', '', '----------');
		foreach($turniere as $turnier) {
			$options_ligen[] = JHtml::_('select.option', 's.'.$turnier->id, $turnier->name);
		}
		
		$lists['saisons']	= JHtml::_('select.genericlist', $options_saisons, 'filter_saison', 'class="inputbox" onchange="this.form.submit();"', 'value', 'text', $state->get('filter_saison') );
		$lists['ligen']	= JHtml::_('select.genericlist', $options_ligen, 'liga', 'class="inputbox"', 'value', 'text', 0 );
		
		//Daten an Template
		$this->assignRef( 'lists', $lists );
		
		parent::display($tpl);
		
	}
	
}

?>
