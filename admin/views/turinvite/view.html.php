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

class CLMViewTurInvite extends JViewLegacy {

	function display($tpl = NULL) {

		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   $this->getModel();
		
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		clm_core::$load->load_css("icons_images");
		ToolBarHelper::title( $model->turnier->name.": ".Text::_('INVITATION'), 'clm_turnier.png'  );
	
	
		ToolBarHelper::save( 'save' );
		ToolBarHelper::apply( 'apply' );
		ToolBarHelper::spacer();
		ToolBarHelper::cancel();

		// das MainMenu abschalten
		$_GET['hidemainmenu'] = 1;
	

		$this->turnier = $model->turnier;

		$this->param = $model->param;


		parent::display();

	}

}
?>
