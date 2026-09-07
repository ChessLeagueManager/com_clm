<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;

class CLMViewTurorgAssign extends JViewLegacy {

	function display($tpl = NULL) {
		
		//CLM parameter auslesen
		$clm_config = clm_core::$db->config();
		if ($clm_config->field_search == 1) $field_search = "js-example-basic-single";
		else $field_search = "inputbox";
		$this->field_search = $field_search;

		$lang = clm_core::$lang->arbiter;
		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();

		$this->turnier = $model->turnier;
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		if (clm_core::$load->request_string( 'task') == 'edit') { 
			$text = Text::_( 'ARBITER_EDIT' );
		} else { 
			$text = Text::_( 'ARBITER_CREATE' );
		}
		
//		ToolBarHelper::title( $text );
		ToolBarHelper::title(  $lang->turorg_assign .' '.$this->turnier[0]->name );
		
		if (clm_core::$access->getType() == 'admin' OR clm_core::$access->getType() == 'tl') {
			ToolBarHelper::save( 'save' );
			ToolBarHelper::apply( 'apply' );
		}
//		ToolBarHelper::custom( 'arbitermain', 'forward.png', 'forward_f2.png', $lang->goto_arbitermain, false);
		ToolBarHelper::spacer();
		ToolBarHelper::custom('cancel', 'back.png', 'back_f2.png', $lang->back, false);

		// das MainMenu abschalten
//		JFactory::getApplication()->input->set('hidemainmenu', true);



		// Document/Seite
		$document =Factory::getDocument();

		// JS-Array jtext -> Fehlertexte
		$document->addScriptDeclaration("var jserror = new Array();");
		$document->addScriptDeclaration("jserror['enter_fide'] = '".Text::_('PLEASE_ENTER')." ".Text::_('FIDE_ID')."';");
		$document->addScriptDeclaration("jserror['enter_name'] = '".Text::_('PLEASE_ENTER')." ".Text::_('ARBITER_NAME')."';");

		// Daten an Template übergeben
		$this->user = $model->user;
		
		$this->roles   = $model->roles;
		$this->organisers = $model->organisers;
		$this->cashiers = $model->cashiers;
		$this->TTL = $model->organiser_TL;
		$this->TTLW = $model->organiser_TLW;
		$this->TTO = $model->organiser_TO;
		$this->TKA = $model->organiser_KA;

		$this->form = $model->form;

		// Auswahlfelder durchsuchbar machen
		clm_core::$load->load_js("suche_liste");

		$this->organiserlist[]	= HTMLHelper::_('select.option',  '0', $lang->select_organiser , 'jid', 'name' );
		$this->organiserlist	= array_merge( $this->organiserlist, $model->organisers );
		
		if (isset($this->TTL[0]->fideid)) $httl = $this->TTL[0]->fideid; else $httl = 0;
		$this->lists['TTL']= HTMLHelper::_('select.genericlist',   $this->organiserlist, 'ttl', 'class="'.$field_search.'" style="width:300px" size="1" onchange="this.form.submit();"',
			'jid', 'name', $httl);
		
		if (count($this->TTLW) < 1) $n = 1; else $n = count($this->TTLW) + 1;
		for ($i = 0; $i < $n; $i++) {
			if (isset($this->TTLW[$i]->fideid)) $httlw = $this->TTLW[$i]->fideid; else $httlw = 0;
			$this->lists['TTLW'.$i]= HTMLHelper::_('select.genericlist',   $this->organiserlist, 'ttlw'.$i, 'class="'.$field_search.'" style="width:300px" size="1" onchange="this.form.submit();"',
				'jid', 'name', $httlw );
		}
//echo "<br>lists"; var_dump($this->lists);
		
		if (count($this->TTO) < 1) $n = 1; else $n = count($this->TTO) + 1;
		for ($i = 0; $i < $n; $i++) {
			if (isset($this->TTO[$i]->fideid)) $htto = $this->TTO[$i]->fideid; else $htto = 0;
			$this->lists['TTO'.$i]= HTMLHelper::_('select.genericlist',   $this->organiserlist, 'tto'.$i, 'class="'.$field_search.'" style="width:300px" size="1" onchange="this.form.submit();"',
				'jid', 'name', $htto );
		}
		
		$this->cashierlist[]	= HTMLHelper::_('select.option',  '0', $lang->select_cashier , 'jid', 'name' );
		$this->cashierlist	= array_merge( $this->cashierlist, $model->cashiers );
		
		if (count($this->TKA) < 1) $n = 1; else $n = count($this->TKA) + 1;
		for ($i = 0; $i < $n; $i++) {
			if (isset($this->TKA[$i]->fideid)) $htka = $this->TKA[$i]->fideid; else $htka = 0;
			$this->lists['TKA'.$i]= HTMLHelper::_('select.genericlist',   $this->cashierlist, 'tka'.$i, 'class="'.$field_search.'" style="width:300px" size="1" onchange="this.form.submit();"',
				'jid', 'name', $htka );
		}
 	

		parent::display();

	}

}
?>
