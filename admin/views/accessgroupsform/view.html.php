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

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class CLMViewAccessgroupsForm extends JView {

	function display($tpl = null) { 
	
		//Daten vom Model
		$accessgroup	= & $this->get('Accessgroup');
		$accessgroups	= & $this->get('Accessgroups');
		$ordering		= &	$this->get('Ordering');
		$accesspoints	= &	$this->get('Accesspoints');
		
		if (JRequest::getVar( 'task') == 'add') {
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
		
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'classes'.DS.'CLMAccess.class.php');
		$clmAccess = new CLMAccess();
		
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'images'.DS.'admin_menue_images.php');
		JToolBarHelper::title( $text, 'clm_headmenu_sonderranglisten.png' );
		
		//if (CLM_usertype == 'admin' OR CLM_usertype == 'tl') {
		$clmAccess->accesspoint = 'BE_accessgroup_general';
		if ( $accessgroup->kind !== 'CLM' AND $clmAccess->access() ) {
			JToolBarHelper::save( 'save' );
			JToolBarHelper::apply( 'apply' );
		}
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		
		$config	= &JComponentHelper::getParams( 'com_clm' );
		
		//Listen
		$lists['published']			= JHtml::_('select.booleanlist', 'published', 'class="inputbox"', $accessgroup->published );
		
		//Reihenfolge
		/*
		if (!$isNew) { 
			$options_o[] = JHtml::_('select.option',0,'0 '.JText::_('ORDERING_FIRST'));
			$orderingMax = 1;
			
			foreach($ordering as $rank){
				$options_o[] = JHtml::_('select.option',$rank->ordering,$rank->ordering.' ('.$rank->name.')');
				$orderingMax++;
			}
			$options_o[] = JHtml::_('select.option',$orderingMax, $orderingMax.' '.JText::_('ORDERING_LAST'));
			
			$lists['ordering']	= JHtml::_('select.genericlist',$options_o, 'ordering', 'class="inputbox"','value','text', $accessgroup->ordering);
		}
		else {
		*/	$lists['ordering']	= JText::_('ACCESSGROUP_ORDERING_NEW'); // Neue Sonderranglisten werden standardmäßig an den Anfang gesetzt. Die Sortierung kann nach dem Speichern dieser Sonderrangliste geändert werden. 
		//}
					
		// Daten an Template übergeben
		$this->assignRef('params', $params);
		$this->assignRef('accessgroup', $accessgroup);
		$this->assignRef('accessgroups', $accessgroups);
		$this->assignRef('accesspoints', $accesspoints);
		$this->assignRef('isNew', $isNew );
		$this->assignRef('lists' , $lists);
		
		parent::display($tpl); 

	}

}
?>