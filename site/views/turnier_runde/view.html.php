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
jimport( 'joomla.application.component.view');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

class CLMViewTurnier_Runde extends JViewLegacy {
	
	function display($tpl = null) {
		
		$config = clm_core::$db->config();
		
		$model		= $this->getModel();


		$document =Factory::getDocument();
		
		if ($model->pgnShow) {
			$document->addScript(URI::base(true).'components/com_clm/javascript/jsPgnViewer.js');
			$document->addScript(URI::base(true).'components/com_clm/javascript/showPgnViewer.js');
			
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
			$document->addScriptDeclaration("text['altRewind'] = '".Text::_('PGN_ALT_REWIND')."';");
			$document->addScriptDeclaration("text['altBack'] = '".Text::_('PGN_ALT_BACK')."';");
			$document->addScriptDeclaration("text['altFlip'] = '".Text::_('PGN_ALT_FLIP')."';");
			$document->addScriptDeclaration("text['altShowMoves'] = '".Text::_('PGN_ALT_SHOWMOVES')."';");
			$document->addScriptDeclaration("text['altComments'] = '".Text::_('PGN_ALT_COMMENTS')."';");
			$document->addScriptDeclaration("text['altPlayMove'] = '".Text::_('PGN_ALT_PLAYMOVE')."';");
			$document->addScriptDeclaration("text['altFastForward'] = '".Text::_('PGN_ALT_FASTFORWARD')."';");
			$document->addScriptDeclaration("text['pgnClose'] = '".Text::_('PGN_CLOSE')."';");
			// Pfad
			$document->addScriptDeclaration("var imagepath = '".URI::base(true)."components/com_clm/images/pgnviewer/'");
		}
		
		// Title in Browser
		$headTitle = CLMText::composeHeadTitle( array( $model->turnier->name, Text::_('TOURNAMENT_ROUND')." ".$model->round->nr ) );
		$document->setTitle( $headTitle );
		
		$this->turnier = $model->turnier;
		
		$this->pgnShow = $model->pgnShow;
		$this->displayTlOK = $model->displayTlOK;

		$this->round = $model->round;
		
		$this->matches = $model->matches;
		$this->points = $model->points;
		
		// zusätzliche Funktionalitäten
//		HTMLHelper::_('behavior.tooltip');
		require_once (JPATH_COMPONENT . DS . 'includes' . DS . 'tooltip.php');
		
		
		parent::display($tpl);
	
	}
	
}
?>
