<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMViewArbiterAssign extends JViewLegacy {

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
			$text = JText::_( 'ARBITER_EDIT' );
		} else { 
			$text = JText::_( 'ARBITER_CREATE' );
		}
		
//		JToolBarHelper::title( $text );
		JToolBarHelper::title(  $lang->arbiter_assign .' '.$this->turnier[0]->name );
		
		if (clm_core::$access->getType() == 'admin' OR clm_core::$access->getType() == 'tl') {
			JToolBarHelper::save( 'save' );
			JToolBarHelper::apply( 'apply' );
		}
		JToolBarHelper::custom( 'arbitermain', 'forward.png', 'forward_f2.png', $lang->goto_arbitermain, false);
		JToolBarHelper::spacer();
		JToolBarHelper::custom('cancel', 'back.png', 'back_f2.png', $lang->back, false);

		// das MainMenu abschalten
		$_GET['hidemainmenu'] = 1;



		// Document/Seite
		$document =JFactory::getDocument();

		// JS-Array jtext -> Fehlertexte
		$document->addScriptDeclaration("var jserror = new Array();");
		$document->addScriptDeclaration("jserror['enter_fide'] = '".JText::_('PLEASE_ENTER')." ".JText::_('FIDE_ID')."';");
		$document->addScriptDeclaration("jserror['enter_name'] = '".JText::_('PLEASE_ENTER')." ".JText::_('ARBITER_NAME')."';");

		// Daten an Template übergeben
		$this->user = $model->user;
		
		$this->roles   = $model->roles;
		$this->arbiters = $model->arbiters;
		$this->ACA = $model->arbiter_CA;
		$this->ADCA = $model->arbiter_DCA;
		$this->APO = $model->arbiter_PO;
		$this->ASA = $model->arbiter_SA;
		$this->AASA = $model->arbiter_ASA;
		$this->AACA = $model->arbiter_ACA;
		$this->All = $model->arbiter_All;
		$this->paarung = $model->paarung;
		$this->array_A00 = $model->array_A00;
		$this->array_A00U = $model->array_A00U;
		$this->array_All = $model->array_All;

		$this->form = $model->form;

		// Auswahlfelder durchsuchbar machen
		clm_core::$load->load_js("suche_liste");

		$this->arbiterlist[]	= JHTML::_('select.option',  '0', $lang->select_arbiter , 'fideid', 'fname' );
		$this->arbiterlist	= array_merge( $this->arbiterlist, $model->arbiters );
		
		if (isset($this->ACA[0]->fideid)) $haca = $this->ACA[0]->fideid; else $haca = 0;
		$this->lists['ACA']= JHTML::_('select.genericlist',   $this->arbiterlist, 'aca', 'class="'.$field_search.'" style="width:300px" size="1" onchange="this.form.submit();"',
			'fideid', 'fname', $haca);
		if (isset($this->array_All[$haca])) $this->lists['ACAU'] = 1; else $this->lists['ACAU'] = 0;
		
		if (count($this->ADCA) < 1) $n = 1; else $n = count($this->ADCA) + 1;
		for ($i = 0; $i < $n; $i++) {
			if (isset($this->ADCA[$i]->fideid)) $hadca = $this->ADCA[$i]->fideid; else $hadca = 0;
			$this->lists['ADCA'.$i]= JHTML::_('select.genericlist',   $this->arbiterlist, 'adca'.$i, 'class="'.$field_search.'" style="width:300px" size="1" onchange="this.form.submit();"',
				'fideid', 'fname', $hadca );
			if (isset($this->array_All[$hadca])) $this->lists['ADCA'.$i.'U'] = 1; else $this->lists['ADCA'.$i.'U'] = 0;	
		}
		
		if (count($this->APO) < 1) $n = 1; else $n = count($this->APO) + 1;
		for ($i = 0; $i < $n; $i++) {
			if (isset($this->APO[$i]->fideid)) $hapo = $this->APO[$i]->fideid; else $hapo = 0;
			$this->lists['APO'.$i]= JHTML::_('select.genericlist',   $this->arbiterlist, 'apo'.$i, 'class="'.$field_search.'" style="width:300px" size="1" onchange="this.form.submit();"',
				'fideid', 'fname', $hapo );
			if (isset($this->array_All[$hapo])) $this->lists['APO'.$i.'U'] = 1; else $this->lists['APO'.$i.'U'] = 0;	
		}
		
		if (count($this->ASA) < 1) $n = 1; else $n = count($this->ASA) + 1;
		for ($i = 0; $i < $n; $i++) {
			if (isset($this->ASA[$i]->fideid)) $hasa = $this->ASA[$i]->fideid; else $hasa = 0;
			$this->lists['ASA'.$i]= JHTML::_('select.genericlist',   $this->arbiterlist, 'asa'.$i, 'class="'.$field_search.'" style="width:300px" size="1" onchange="this.form.submit();"',
				'fideid', 'fname', $hasa );
			if (isset($this->array_All[$hasa])) $this->lists['ASA'.$i.'U'] = 1; else $this->lists['ASA'.$i.'U'] = 0;	
		}
 	
		if (count($this->AASA) < 1) $n = 1; else $n = count($this->AASA) + 1;
		for ($i = 0; $i < $n; $i++) {
			if (isset($this->AASA[$i]->fideid)) $haasa = $this->AASA[$i]->fideid; else $haasa = 0;
			$this->lists['AASA'.$i]= JHTML::_('select.genericlist',   $this->arbiterlist, 'aasa'.$i, 'class="'.$field_search.'" style="width:300px" size="1" onchange="this.form.submit();"',
				'fideid', 'fname', $haasa );
			if (isset($this->array_All[$haasa])) $this->lists['AASA'.$i.'U'] = 1; else $this->lists['AASA'.$i.'U'] = 0;	
		}
 	
		if (count($this->AACA) < 1) $n = 1; else $n = count($this->AACA) + 1;
		for ($i = 0; $i < $n; $i++) {
			if (isset($this->AACA[$i]->fideid)) $haaca = $this->AACA[$i]->fideid; else $haaca = 0;
			$this->lists['AACA'.$i]= JHTML::_('select.genericlist',   $this->arbiterlist, 'aaca'.$i, 'class="'.$field_search.'" style="width:300px" size="1" onchange="this.form.submit();"',
				'fideid', 'fname', $haaca );
			if (isset($this->array_All[$haaca])) $this->lists['AACA'.$i.'U'] = 1; else $this->lists['AACA'.$i.'U'] = 0;	
		}

		parent::display();

	}

}
?>
