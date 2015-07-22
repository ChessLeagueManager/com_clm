<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

jimport( 'joomla.application.component.view');

class CLMViewTurnier_Runde extends JViewLegacy {
	
	function display($tpl = null) {
		
		$config = clm_core::$db->config();
		
		$model		= $this->getModel();


		$document =JFactory::getDocument();
		
		if ($model->pgnShow) {
			$document->addScript(JURI::base().'components/com_clm/javascript/jsPgnViewer.js');
			$document->addScript(JURI::base().'components/com_clm/javascript/showPgnViewer.js');
			
			// Zufallszahl
			$now = time()+mt_rand();
			$document->addScriptDeclaration("var randomid = $now;");
			// pgn-params
			$document->addScriptDeclaration("var param = new Array();");
			$document->addScriptDeclaration("param['fe_pgn_moveFont'] = '".$config->fe_pgn_moveFont."'");
			$document->addScriptDeclaration("param['fe_pgn_commentFont'] = '".$config->fe_pgn_commentFont."'");
			$document->addScriptDeclaration("param['fe_pgn_style'] = '".$config->fe_pgn_style."'");
			// Tooltip-Texte
			$document->addScriptDeclaration("var text = new Array();");
			$document->addScriptDeclaration("text['altRewind'] = '".JText::_('PGN_ALT_REWIND')."';");
			$document->addScriptDeclaration("text['altBack'] = '".JText::_('PGN_ALT_BACK')."';");
			$document->addScriptDeclaration("text['altFlip'] = '".JText::_('PGN_ALT_FLIP')."';");
			$document->addScriptDeclaration("text['altShowMoves'] = '".JText::_('PGN_ALT_SHOWMOVES')."';");
			$document->addScriptDeclaration("text['altComments'] = '".JText::_('PGN_ALT_COMMENTS')."';");
			$document->addScriptDeclaration("text['altPlayMove'] = '".JText::_('PGN_ALT_PLAYMOVE')."';");
			$document->addScriptDeclaration("text['altFastForward'] = '".JText::_('PGN_ALT_FASTFORWARD')."';");
			$document->addScriptDeclaration("text['pgnClose'] = '".JText::_('PGN_CLOSE')."';");
			// Pfad
			$document->addScriptDeclaration("var imagepath = '".JURI::base()."components/com_clm/images/pgnviewer/'");
		}
		
		// Title in Browser
		$headTitle = CLMText::composeHeadTitle( array( $model->turnier->name, JText::_('TOURNAMENT_ROUND')." ".$model->round->nr ) );
		$document->setTitle( $headTitle );
		
		$this->assignRef('turnier', $model->turnier);
		
		$this->assignRef('pgnShow', $model->pgnShow);
		$this->assignRef('displayTlOK', $model->displayTlOK);

		$this->assignRef('round', $model->round);
		
		$this->assignRef('matches', $model->matches);
		$this->assignRef('points', $model->points);
		
		// zusätzliche Funktionalitäten
		JHTML::_('behavior.tooltip');
		
		
		parent::display($tpl);
	
	}
	
}
?>
