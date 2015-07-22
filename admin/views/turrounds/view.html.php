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

class CLMViewTurRounds extends JView {

	function display() {

		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   &$this->getModel();
		
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'admin_menue_images.php');
		JToolBarHelper::title( $model->turnier->name.": ".JText::_('ROUNDS'), 'clm_turnier.png'  );
	
		JToolBarHelper::spacer();
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
		$clmAccess = new CLMAccess();
		$clmAccess->accesspoint = 'BE_tournament_edit_round';
		if (($model->turnier->tl == CLM_ID AND $clmAccess->access() !== false) OR $clmAccess->access() === true) {
			// auslosen
			//if ($model->turnier->roundToDraw != 0) {
			//	JToolBarHelper::spacer();
			//	JToolBarHelper::custom('assignMatches', 'edit.png', 'edit_f2.png', JText::_('MATCHES_ASSIGN'), FALSE);
			//}
			JToolBarHelper::spacer();
			JToolBarHelper::publishList();
			JToolBarHelper::unpublishList();
		}
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();

		if (($model->turnier->tl == CLM_ID AND $clmAccess->access() !== false) OR $clmAccess->access() === true) {
			JToolBarHelper::divider();
			JToolBarHelper::spacer();
			JToolBarHelper::custom( 'turform', 'config.png', 'config_f2.png', JText::_('TOURNAMENT'), false);
		}

		// Daten an Template übergeben
		$this->assignRef('user', $model->user);
		
		$this->assignRef('turrounds', $model->turRounds);
		$this->assignRef('turnier', $model->turnier);
 
		$this->assignRef('form', $model->form);
		$this->assignRef('param', $model->param);

		$this->assignRef('pagination', $model->pagination);
		
		// zusätzliche Funktionalitäten
		JHtml::_('behavior.tooltip');


		parent::display();

	}

}
?>