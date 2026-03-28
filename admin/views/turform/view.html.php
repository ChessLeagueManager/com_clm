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
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Toolbar\ToolbarHelper;

class CLMViewTurForm extends JViewLegacy {

	function display($tpl = null) {
		$lang = clm_core::$lang->arbiter;
		$task = clm_core::$load->request_string('task');
		$id = clm_core::$load->request_int('id');
		
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		if ($id > 0) { 
			$text = Text::_( 'TOURNAMENT_EDIT' );
		} else { 
			$text = Text::_( 'TOURNAMENT_CREATE' );
		}
		
		clm_core::$load->load_css("icons_images");
		ToolBarHelper::title( $text, 'clm_turnier.png' );
		
		$row = Table::getInstance( 'turniere', 'TableCLM' );
		$row->load($id);
		$clmAccess = clm_core::$access;
		if (($row->tl == $clmAccess->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== false) OR ($clmAccess->access('BE_tournament_edit_detail') === true) OR ($clmAccess->access('BE_tournament_create') === true)) {
			ToolBarHelper::save( 'save' );
			ToolBarHelper::apply( 'apply' );
			ToolBarHelper::custom('arbiter','edit.png','edit_f2.png',$lang->arbiter_assign,false);
		}
		ToolBarHelper::spacer();
		ToolBarHelper::cancel('cancel');

		// das MainMenu abschalten
		Factory::getApplication()->input->set('hidemainmenu', true);

		$config = clm_core::$db->config();
		$params['tourn_showtlok'] = $config->tourn_showtlok;

		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();

		// Document/Seite
		$document =Factory::getDocument();

		// JS-Array jtext -> Fehlertexte
		$document->addScriptDeclaration("var jserror = new Array();");
		$document->addScriptDeclaration("jserror['enter_name'] = '".Text::_('PLEASE_ENTER')." ".Text::_('TOURNAMENT_NAME')."';");
		$document->addScriptDeclaration("jserror['select_season'] = '".Text::_('PLEASE_SELECT')." ".Text::_('SEASON')."';");
		$document->addScriptDeclaration("jserror['select_modus'] = '".Text::_('PLEASE_SELECT')." ".Text::_('MODUS')."';");
		$document->addScriptDeclaration("jserror['enter_rounds'] = '".Text::_('PLEASE_ENTER')." ".Text::_('ROUNDS_COUNT')."';");
		$document->addScriptDeclaration("jserror['number_rounds'] = '".Text::_('PLEASE_NUMBER')." ".Text::_('ROUNDS_COUNT')."';");
		$document->addScriptDeclaration("jserror['enter_participants'] = '".Text::_('PLEASE_ENTER')." ".Text::_('PARTICIPANT_COUNT')."';");
		$document->addScriptDeclaration("jserror['number_participants'] = '".Text::_('PLEASE_NUMBER')." ".Text::_('PARTICIPANT_COUNT')."';");
		$document->addScriptDeclaration("jserror['select_director'] = '".Text::_('PLEASE_SELECT')." ".Text::_('TOURNAMENT_DIRECTOR')."';");
		$document->addScriptDeclaration("jserror['select_tiebreakers_12'] = '".Text::_('PLEASE_SELECT')." ".Text::_('TIEBREAKERS')." 1 & 2';");
		$document->addScriptDeclaration("jserror['select_tiebreakers_13'] = '".Text::_('PLEASE_SELECT')." ".Text::_('TIEBREAKERS')." 1 & 3';");
		$document->addScriptDeclaration("jserror['select_tiebreakers_23'] = '".Text::_('PLEASE_SELECT')." ".Text::_('TIEBREAKERS')." 2 & 3';");
		$document->addScriptDeclaration("jserror['enddatetoolow'] = '".Text::_('ENDDATE_TOOLOW')."';");
		$document->addScriptDeclaration("jserror['nostartdate'] = '".Text::_('NO_STARTDATE')."';");
 
		$document->addScriptDeclaration("var jstext = new Array();");
		$document->addScriptDeclaration("jstext['roundscountgenerated'] = '(".Text::_('ROUNDS_COUNT_GENERATED').")';");

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

		// Auswahlfelder durchsuchbar machen
		clm_core::$load->load_js("suche_liste");

		parent::display($tpl);

	}

}
?>
