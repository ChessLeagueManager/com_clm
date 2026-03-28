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

class CLMViewTermineMain extends JViewLegacy {

	function display($tpl = NULL) {

		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();
		$_GET['hidemainmenu'] = 1;
		clm_core::$load->load_css("icons_images");
		ToolBarHelper::title( Text::_( 'TITLE_TERMINE' ), 'clm_headmenu_termine.png' );
	
		ToolBarHelper::custom('catmain','forward.png','forward_f2.png', Text::_('JCATEGORIES'), false);

		ToolBarHelper::addNew();
		ToolBarHelper::custom( 'copy', 'copy.png', 'copy_f2.png', Text::_('TERMINE_COPY'));
		
		ToolBarHelper::spacer();
		ToolBarHelper::editList();
		
		ToolBarHelper::spacer();
		ToolBarHelper::publishList();
		ToolBarHelper::unpublishList();

		$clmAccess = clm_core::$access;
		if ( $clmAccess->access('BE_event_delete') !== false) {
			ToolBarHelper::spacer();
			ToolBarHelper::custom('delete','delete.png','delete_f2.png', Text::_('TERMINE_DELETE')); 
		}
		ToolBarHelper::custom( 'import', 'upload.png', 'upload_f2.png', Text::_('TERMINE_IMPORT'), false);
		ToolBarHelper::custom( 'export', 'copy.png', 'copy_f2.png', Text::_('TERMINE_EXPORT'), false);
		ToolBarHelper::custom( 'download', 'download.png', 'download_f2.png', Text::_('TERMINE_DOWNLOAD'), false);
		
		// Daten an Template übergeben
		$this->user = $model->user;		
		$this->termine = $model->termine;
		$this->param = $model->param;
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
