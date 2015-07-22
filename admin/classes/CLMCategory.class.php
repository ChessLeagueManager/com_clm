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


/**
 * Category
*/
	
class CLMCategory {

	function __construct($catid, $getData = FALSE) {
		// $catid übergibt id der category
		// $getData, ob die Turneirdaten aus clm_categories sofort ausgelesen werden sollen

		// DB
		$this->_db				= JFactory::getDBO();
		
		// catid
		$this->catid = $catid;	
	
		// get data?
		if ($getData) {
			$this->_getData();
		}
	
	}


	function _getData() {
	
		$this->data = JTable::getInstance( 'categories', 'TableCLM' );
		$this->data->load($this->catid);
	
	}


	/**
	* check, ob User Zugriff hat
	* drei Zugangsmöichgkeiten - aller per Default auf TRUE
	*/
	function checkAccess($usertype_admin = TRUE, $usertype_tl = TRUE, $id_tl = TRUE) {
	
		// admin?
		if ($usertype_admin AND clm_core::$access->getType() == 'admin') {
			return TRUE;
		}
		// tl?
		if ($usertype_tl AND clm_core::$access->getType() == 'tl') {
			return TRUE;
		}
		// category->tl
		if ($id_tl AND clm_core::$access->getJid() == $this->data->tl) {
			return TRUE;
		}
		// nichts hat zugetroffen
		return FALSE;
	
	}
	
	function checkDelete() {
	
		$query = "SELECT COUNT(*) FROM #__clm_categories WHERE parentid = '".$this->catid."'";
		$this->_db->setQuery($query);
		if ($this->_db->loadResult() > 0) {
			return FALSE;
		}
		
		return TRUE;
	
	}


	
}
?>