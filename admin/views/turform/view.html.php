<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMViewTurForm extends JViewLegacy {

	function display($tpl = null) {
		$task = clm_core::$load->request_string('task');
		$id = clm_core::$load->request_int('id');
		
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		if ($id > 0) { 
			$text = JText::_( 'TOURNAMENT_EDIT' );
		} else { 
			$text = JText::_( 'TOURNAMENT_CREATE' );
		}
		
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( $text, 'clm_turnier.png' );
		
		$row = JTable::getInstance( 'turniere', 'TableCLM' );
		$row->load($id);
		$clmAccess = clm_core::$access;
		if (($row->tl == $clmAccess->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== false) OR ($clmAccess->access('BE_tournament_edit_detail') === true)) {
			JToolBarHelper::save( 'save' );
			JToolBarHelper::apply( 'apply' );
		}
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('cancel');

		// das MainMenu abschalten
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$config = clm_core::$db->config();
		$params['tourn_showtlok'] = $config->tourn_showtlok;

		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();

		// Document/Seite
		$document =JFactory::getDocument();

		// JS-Array jtext -> Fehlertexte
		$document->addScriptDeclaration("var jserror = new Array();");
		$document->addScriptDeclaration("jserror['enter_name'] = '".JText::_('PLEASE_ENTER')." ".JText::_('TOURNAMENT_NAME')."';");
		$document->addScriptDeclaration("jserror['select_season'] = '".JText::_('PLEASE_SELECT')." ".JText::_('SEASON')."';");
		$document->addScriptDeclaration("jserror['select_modus'] = '".JText::_('PLEASE_SELECT')." ".JText::_('MODUS')."';");
		$document->addScriptDeclaration("jserror['enter_rounds'] = '".JText::_('PLEASE_ENTER')." ".JText::_('ROUNDS_COUNT')."';");
		$document->addScriptDeclaration("jserror['number_rounds'] = '".JText::_('PLEASE_NUMBER')." ".JText::_('ROUNDS_COUNT')."';");
		$document->addScriptDeclaration("jserror['enter_participants'] = '".JText::_('PLEASE_ENTER')." ".JText::_('PARTICIPANT_COUNT')."';");
		$document->addScriptDeclaration("jserror['number_participants'] = '".JText::_('PLEASE_NUMBER')." ".JText::_('PARTICIPANT_COUNT')."';");
		$document->addScriptDeclaration("jserror['select_director'] = '".JText::_('PLEASE_SELECT')." ".JText::_('TOURNAMENT_DIRECTOR')."';");
		$document->addScriptDeclaration("jserror['select_tiebreakers_12'] = '".JText::_('PLEASE_SELECT')." ".JText::_('TIEBREAKERS')." 1 & 2';");
		$document->addScriptDeclaration("jserror['select_tiebreakers_13'] = '".JText::_('PLEASE_SELECT')." ".JText::_('TIEBREAKERS')." 1 & 3';");
		$document->addScriptDeclaration("jserror['select_tiebreakers_23'] = '".JText::_('PLEASE_SELECT')." ".JText::_('TIEBREAKERS')." 2 & 3';");
		$document->addScriptDeclaration("jserror['enddatetoolow'] = '".JText::_('ENDDATE_TOOLOW')."';");
		$document->addScriptDeclaration("jserror['nostartdate'] = '".JText::_('NO_STARTDATE')."';");
 
		$document->addScriptDeclaration("var jstext = new Array();");
		$document->addScriptDeclaration("jstext['roundscountgenerated'] = '(".JText::_('ROUNDS_COUNT_GENERATED').")';");

		$document->addScriptDeclaration("var jsform = new Array();");
		$document->addScriptDeclaration("jsform['runden'] = '<input class=\"inputbox\" type=\"text\" name=\"runden\" id=\"runden\" size=\"10\" maxlength=\"5\" value=\"".$model->turnier->runden."\" />';");
		$document->addScriptDeclaration("jsform['runden1'] = '<input class=\"inputbox\" type=\"text\" name=\"runden\" id=\"runden\" size=\"10\" maxlength=\"5\" value=\"';");
		$document->addScriptDeclaration("jsform['runden2'] = '\" />';");

		// Script
		$document->addScript(CLM_PATH_JAVASCRIPT.'turform.js');

		// Daten an Template übergeben
		$this->user = $model->user;
		
		$this->params = $params;
		
		$this->turnier = $model->turnier;

		$this->form = $model->form;

		
		parent::display($tpl);

	}

}
?>
