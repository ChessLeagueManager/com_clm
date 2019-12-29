<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2019 CLM-Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMViewAccessgroupsForm extends JViewLegacy {

	function display($tpl = null) { 
	
		//Daten vom Model
		$accessgroup	=  $this->get('Accessgroup');
		$accessgroups	=  $this->get('Accessgroups');
		$ordering		= 	$this->get('Ordering');
		
		if (clm_core::$load->request_string('task') == 'add') {
			$isNew = true;
		} else { 
			$isNew = false;
		}
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		if (!$isNew) { 
			$text = JText::_( 'ACCESSGROUP_EDIT' );
		} else { 
			$text = JText::_( 'ACCESSGROUP_CREATE' );
		}
		
		$clmAccess = clm_core::$access;
		
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( $text, 'clm_headmenu_sonderranglisten.png' );
		
		if ( $accessgroup->kind !== 'CLM' AND $clmAccess->access('BE_accessgroup_general') ) {
			JToolBarHelper::save( 'save' );
			JToolBarHelper::apply( 'apply' );
		}
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		
		$config = clm_core::$db->config();
		
		//Listen
		$lists['published']			= JHtml::_('select.booleanlist', 'published', 'class="inputbox"', $accessgroup->published );

		$lists['ordering']	= JText::_('ACCESSGROUP_ORDERING_NEW'); // Neue Sonderranglisten werden standardmäßig an den Anfang gesetzt. Die Sortierung kann nach dem Speichern dieser Sonderrangliste geändert werden. 
	
					
		// Daten an Template übergeben
		$this->accessgroup = $accessgroup;
		$this->accessgroups = $accessgroups;
		$this->isNew = $isNew;
		$this->lists = $lists;
		
		parent::display($tpl); 

	}

}
?>
