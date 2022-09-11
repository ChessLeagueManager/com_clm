<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2022 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMViewSonderranglistenForm extends JViewLegacy {

	function display($tpl = null) { 
		$task = clm_core::$load->request_string('task');
		$id = clm_core::$load->request_int('id');
	
		//Daten vom Model
		$sonderrangliste	= $this->get('Sonderrangliste');
		$ordering			= $this->get('Ordering');
		if (is_null($ordering)) $ordering = array();
		$turniere			= $this->get('Turniere');
		$saisons			= $this->get('Saisons');
		
		if (clm_core::$load->request_string('task') == 'add') {
			$isNew = true;
		} else { 
			$isNew = false;
		}
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		if (!$isNew) { 
			$text = JText::_( 'SPECIALRANKING_EDIT' );
		} else { 
			$text = JText::_( 'SPECIALRANKING_CREATE' );
		}
		
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( $text, 'clm_headmenu_sonderranglisten.png' );
		
		$clmAccess = clm_core::$access;
		if (isset($sonderrangliste->id) AND $sonderrangliste->id > 0) {
			if (($sonderrangliste->tl == clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== true) OR $clmAccess->access('BE_tournament_edit_detail') === true) {
				JToolBarHelper::save( 'save' );
				JToolBarHelper::apply( 'apply' );
			}
		} else {
			JToolBarHelper::save( 'save' );
			JToolBarHelper::apply( 'apply' );
		}
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		
		// das MainMenu abschalten
		$_REQUEST['hidemainmenu'] = 1; 

		$config = clm_core::$db->config();
		
		//Listen
		$lists['published']			= JHtml::_('select.booleanlist', 'published', 'class="inputbox"', $sonderrangliste->published );
		
		$lists['use_rating_filter']	= JHtml::_('select.booleanlist', 'use_rating_filter', 'class="inputbox"', $sonderrangliste->use_rating_filter );
		$options_rat[]				= JHtml::_('select.option', 0, JText::_( 'SPECIALRANKING_OPTION_RATING_TYPE_0' ));
		$options_rat[]				= JHtml::_('select.option', 1, JText::_( 'SPECIALRANKING_OPTION_RATING_TYPE_1' ));
		$options_rat[]				= JHtml::_('select.option', 2, JText::_( 'SPECIALRANKING_OPTION_RATING_TYPE_2' ));
		$options_rat[]				= JHtml::_('select.option', 3, JText::_( 'SPECIALRANKING_OPTION_RATING_TYPE_3' ));
		$options_rat[]				= JHtml::_('select.option', 4, JText::_( 'SPECIALRANKING_OPTION_RATING_TYPE_4' ));
		$options_rat[]				= JHtml::_('select.option', 5, JText::_( 'SPECIALRANKING_OPTION_RATING_TYPE_5' ));
		$lists['rating_type']		= JHtml::_('select.genericlist', $options_rat, 'rating_type', 'class="inputbox"', 'value', 'text', $sonderrangliste->rating_type );
		
		$lists['use_birthYear_filter']	= JHtml::_('select.booleanlist', 'use_birthYear_filter', 'class="inputbox"', $sonderrangliste->use_birthYear_filter );
		$lists['use_sex_year_filter']	= JHtml::_('select.booleanlist', 'use_sex_year_filter', 'class="inputbox"', $sonderrangliste->use_sex_year_filter );
		
		$lists['use_sex_filter']	= JHtml::_('select.booleanlist', 'use_sex_filter', 'class="inputbox"', $sonderrangliste->use_sex_filter );
		$options_sex[]				= JHtml::_('select.option', '', JText::_( 'SPECIALRANKING_OPTION_SEX_0' ));
		$options_sex[]				= JHtml::_('select.option', 'M', JText::_( 'SPECIALRANKING_OPTION_SEX_1' ));
		$options_sex[]				= JHtml::_('select.option', 'W', JText::_( 'SPECIALRANKING_OPTION_SEX_2' ));
		$lists['sex']				= JHtml::_('select.genericlist', $options_sex, 'sex', 'class="inputbox"', 'value', 'text', $sonderrangliste->sex );
		
		$lists['use_zps_filter']	= JHtml::_('select.booleanlist', 'use_zps_filter', 'class="inputbox"', $sonderrangliste->use_zps_filter );

		//Reihenfolge
		if (!$isNew) { 
			$options_o[] = JHtml::_('select.option',0,'0 '.JText::_('ORDERING_FIRST'));
			$orderingMax = 1;

			foreach($ordering as $rank){
				$options_o[] = JHtml::_('select.option',$rank->ordering,$rank->ordering.' ('.$rank->name.')');
				$orderingMax++;
			}
			$options_o[] = JHtml::_('select.option',$orderingMax, $orderingMax.' '.JText::_('ORDERING_LAST'));
			
			$lists['ordering']	= JHtml::_('select.genericlist',$options_o, 'ordering', 'class="inputbox"','value','text', $sonderrangliste->ordering);
		}
		else {
			$lists['ordering']	= JText::_('SPECIALRANKING_ORDERING_NEW'); // Neue Sonderranglisten werden standardmäßig an den Anfang gesetzt. Die Sortierung kann nach dem Speichern dieser Sonderrangliste geändert werden. 
		}
		
 
		//Listen für Turniere (Joomla 1.5 bietet keine wirklich befriedigende Lösung)
		$turnier_str = "<select id='turnier' class='inputbox' name='turnier'>";
		
		if($sonderrangliste->turnier == 0) {
			$selected = "selected='selected' ";
		} else {
			$selected = '';
		}
			
		$turnier_str .= "<option sid='0' value='0' ".$selected.">".JText::_('CHOOSE_TOURNAMENT')."</option>";
		$sid = null;
		foreach($turniere as $turnier){
			if($turnier->id == $sonderrangliste->turnier) {
				$selected = "selected='selected' ";
			} else {
				$selected = '';
		}
			$turnier_str .= "<option sid='".$turnier->sid."' value='".$turnier->id."' ".$selected.">".$turnier->name."</option>";
		}
		$turnier_str .= "</select>";
		$lists['turnier'] = $turnier_str;
		
		
		//Saisons
		$options_s[] = JHtml::_('select.option',0,JText::_('CHOOSE_SAISON'));
		
		foreach($saisons as $saison){
			$options_s[] = JHtml::_('select.option',$saison->id,$saison->name);
		}
		$lists['saison']	= JHtml::_('select.genericlist',$options_s, 'saison', 'class="inputbox" onchange="showTournaments()"','value','text', $sonderrangliste->sid);

				
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model = $this->getModel();

		// Document/Seite
		$document = JFactory::getDocument();

		// JS-Array jtext -> Fehlertexte
		$document->addScriptDeclaration("var jserror = new Array();");
		$document->addScriptDeclaration("jserror['enter_name'] = '".JText::_('PLEASE_ENTER')." ".JText::_('SPECIALRANKING_NAME')."';");
		$document->addScriptDeclaration("jserror['enter_saison'] = '".JText::_('PLEASE_ENTER')." ".JText::_('SPECIALRANKING_SAISON')."';");
		$document->addScriptDeclaration("jserror['enter_turnier'] = '".JText::_('PLEASE_ENTER')." ".JText::_('SPECIALRANKING_TOURNAMENT')."';");
		$document->addScriptDeclaration("jserror['check_year_ay'] = '".JText::_('PLEASE_CHECK_YEAR').":  ".JText::_( 'SPECIALRANKING_BIRTHYEAR_FILTER_OPTIONS' )." - ".JText::_( 'SPECIALRANKING_OPTION_BIRTHYEAR_YOUNGER_THAN' )."';");
		$document->addScriptDeclaration("jserror['check_year_ao'] = '".JText::_('PLEASE_CHECK_YEAR').":  ".JText::_( 'SPECIALRANKING_BIRTHYEAR_FILTER_OPTIONS' )." - ".JText::_( 'SPECIALRANKING_OPTION_BIRTHYEAR_OLDER_THAN' )."';");
		$document->addScriptDeclaration("jserror['check_year_cmy'] = '".JText::_('PLEASE_CHECK_YEAR').":  ".JText::_( 'SPECIALRANKING_SEX_YEAR_FILTER_OPTIONS' )." - ".JText::_( 'SPECIALRANKING_OPTION_MALEYEAR_YOUNGER_THAN' )."';");
		$document->addScriptDeclaration("jserror['check_year_cmo'] = '".JText::_('PLEASE_CHECK_YEAR').":  ".JText::_( 'SPECIALRANKING_SEX_YEAR_FILTER_OPTIONS' )." - ".JText::_( 'SPECIALRANKING_OPTION_MALEYEAR_OLDER_THAN' )."';");
		$document->addScriptDeclaration("jserror['check_year_cfy'] = '".JText::_('PLEASE_CHECK_YEAR').":  ".JText::_( 'SPECIALRANKING_SEX_YEAR_FILTER_OPTIONS' )." - ".JText::_( 'SPECIALRANKING_OPTION_FEMALEYEAR_YOUNGER_THAN' )."';");
		$document->addScriptDeclaration("jserror['check_year_cfo'] = '".JText::_('PLEASE_CHECK_YEAR').":  ".JText::_( 'SPECIALRANKING_SEX_YEAR_FILTER_OPTIONS' )." - ".JText::_( 'SPECIALRANKING_OPTION_FEMALEYEAR_OLDER_THAN' )."';");

		// Daten an Template übergeben
		$this->sonderrangliste = $sonderrangliste;
		$this->isNew = $isNew;
		$this->lists = $lists;

		
		parent::display($tpl); 

	}

}
?>
