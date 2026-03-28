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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

class CLMViewCatMain extends JViewLegacy {

	function display($tpl = NULL)
	{

		// Die Toolbar erstellen, die über der Seite angezeigt wird
		ToolBarHelper::title( Text::_( 'JCATEGORIES' ) );

		// nur, wenn Admin
		if (clm_core::$access->getType() == 'admin' OR clm_core::$access->getType() == 'tl') {
			ToolBarHelper::custom('add','new.png','new_f2.png', Text::_('ADD'), false);
			ToolBarHelper::custom('copy', 'copy.png', 'copy_f2.png', Text::_('JTOOLBAR_DUPLICATE'));
		}
		ToolBarHelper::editList();
		// nur, wenn Admin
		if (clm_core::$access->getType() == 'admin' OR clm_core::$access->getType() == 'tl') {
			ToolBarHelper::spacer();
			ToolBarHelper::publishList();
			ToolBarHelper::unpublishList();
		}
		
		if (clm_core::$access->getType() === 'admin') {
			ToolBarHelper::spacer();
			ToolBarHelper::custom('delete','delete.png','delete_f2.png', Text::_('DELETE'),true);
		}

		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();

		

		// Daten an Template übergeben
		$this->user = $model->user;
		
		$this->categories = $model->categories;

		$this->param = $model->param;

		$this->form = $model->form;
		
		$this->pagination = $model->pagination;

		// zusätzliche Funktionalitäten
//		HTMLHelper::_('behavior.tooltip');
		require_once (JPATH_COMPONENT_SITE . DS . 'includes' . DS . 'tooltip.php');

		// Auswahlfelder durchsuchbar machen
		clm_core::$load->load_js("suche_liste");

		parent::display();

	}

}
?>
