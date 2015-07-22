<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class CLMViewDewis extends JView {
	function display($tpl = null) {

		$tpl		= 'json';
		$model		= $this->getModel('dewis');
		$data		= $model->json_update();
		$this->assignRef( 'data', $data );

		parent::display($tpl);
	}
}
?>