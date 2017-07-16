<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2017 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMViewSonderranglistenCopy extends JViewLegacy {

	function display($tpl = null) { 
		$task = JRequest::getVar( 'task');
		$id = JRequest::getVar( 'id');
	
		//Daten vom Model
		$turniere			= $this->get('Turniere');
		
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		$text = JText::_( 'SPECIALRANKING_CREATE' );
		
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( $text, 'clm_headmenu_sonderranglisten.png' );
		
		$clmAccess = clm_core::$access;
		if ($clmAccess->access('BE_tournament_edit_detail') === true) {
			JToolBarHelper::save( 'save' );
		//	JToolBarHelper::apply( 'apply' );
		}
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		
		// das MainMenu abschalten
		JRequest::setVar( 'hidemainmenu', 1 );

		$config = clm_core::$db->config();
		
		
 
		//Listen für Turniere 
		$turnier_str = "<select id='turnier' class='inputbox' name='turnier_source'>";
		$selected = '';
		$turnier_str .= "<option sid='0' value='0' ".$selected.">".JText::_('CHOOSE_TOURNAMENT')."</option>";
		$sid = null;
		foreach($turniere as $turnier){
			$turnier_str .= "<option sid='".$turnier->sid."' value='".$turnier->id."' ".$selected.">".$turnier->sname.' '.$turnier->name."</option>";
		}
		$turnier_str .= "</select>";
		$lists['turnier_source'] = $turnier_str;
		
		$turnier_str = "<select id='turnier' class='inputbox' name='turnier_target'>";
		$selected = '';
		$turnier_str .= "<option sid='0' value='0' ".$selected.">".JText::_('CHOOSE_TOURNAMENT')."</option>";
		$sid = null;
		foreach($turniere as $turnier){
			$turnier_str .= "<option sid='".$turnier->sid."' value='".$turnier->id."' ".$selected.">".$turnier->sname.' '.$turnier->name."</option>";
		}
		$turnier_str .= "</select>";
		$lists['turnier_target'] = $turnier_str;

				
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model = $this->getModel();

		// Document/Seite
		$document = JFactory::getDocument();

		// JS-Array jtext -> Fehlertexte
		$document->addScriptDeclaration("var jserror = new Array();");
		$document->addScriptDeclaration("jserror['enter_name'] = '".JText::_('PLEASE_ENTER')." ".JText::_('TOURNAMENT_NAME')."';");

		// Daten an Template übergeben
		$this->assignRef('lists' , $lists);

		
		parent::display($tpl); 

	}

}
?>
