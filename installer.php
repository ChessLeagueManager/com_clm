<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
if(!defined("DS")){define('DS', DIRECTORY_SEPARATOR);} // fix for Joomla 3.x
class com_clmInstallerScript {
	private $params = array();
	private $version;
	private $redirect = false;
	function preflight($type, $parent) {
		define('clm_install', "42");
		$jversion = new JVersion();
		if ($jversion->getShortVersion() < '2.5.0') {
			$application = JFactory::getApplication();
			$application->enqueueMessage('Cannot install com_clm in a Joomla release prior to 2.5.0.', 'error');
			return false;
		}
		if ($type == 'update') {
			$this->version = $this->getParam('version');
			if ($this->version < '1.5.2') {
				$application = JFactory::getApplication();
				$application->enqueueMessage($type.'Please install com_clm 1.5.2 before this update.', 'error');
				return false;
			}
			// Alte Konfiguration auslesen
			if($this->version < '3.1.0') {
				$this->retrieveOldComponentParams();
			}
		} else {
			$status = self::tableStatus();
			// kleiner als 1.5.2
			if($status == 0) {
				$application = JFactory::getApplication();
				$application->enqueueMessage($type.'Please install com_clm 1.5.2 before this update.', 'error');
				return false;
			} else if($status == 1) {
				$this->version = "1.5.2";
				// Das sichern der Parameter ist so nicht möglich,
				// da diese von Joomla verwaltet werden und die Komponente nicht installiert ist
				// Gut, dass dies in 3.2 nicht mehr passieren kann :)
				//$this->retrieveOldComponentParams();
				$this->redirect = true;				
			} else if($status == 2) {
				// Die korrekte Version wird automatisch aus der DB gelesen
				$this->version = "3.1.0";	
				$this->redirect = true;		
			}
		}
	   return true;
	}
	function install($parent) {
		if($this->redirect) {
			return $this->update($parent);		
		}
		require_once (JPATH_SITE . DS . "components" . DS . "com_clm" . DS . "" . "clm" . DS . "index.php");
		$out = clm_core::$load->db_update(0);
		if (!$out) {
			$application = JFactory::getApplication();
			$application->enqueueMessage('There is a problem with the database, is there an old table?', 'error');
		} else {
			$this->add(4,"installer_install",json_encode(array("old"=>"","new"=>clm)));
		}
		return $out;
	}
	function update($parent) {
		require_once (JPATH_SITE . DS . "components" . DS . "com_clm" . DS . "" . "clm" . DS . "index.php");
		if ($this->version < '3.1.0') {
			$out = clm_core::$load->db_update(1);
			if($out) {
				// Alte Konfiguration speichern
				$this->saveParams();
				$this->add(4,"installer_upgrade",json_encode(array("old"=>$this->version,"new"=>clm_core::$db->config()->cl_config.":".clm_core::$db->config()->db_config)));
			}
		} else {
			$old = clm_core::$db->config()->cl_config.":".clm_core::$db->config()->db_config;
			$out = clm_core::$load->db_update(2); 
			$new = clm_core::$db->config()->cl_config.":".clm_core::$db->config()->db_config;
			if($out && $old != $new) {
				$this->add(4,"installer_update",json_encode(array("old"=>$old,"new"=>$new)));
			}
		}
		if (!$out) {
			$application = JFactory::getApplication();
			$application->enqueueMessage('There is a problem with the database, have you something changed?', 'error');
		}
		return $out;
	}
	function uninstall($parent) {
		require_once (JPATH_SITE . DS . "components" . DS . "com_clm" . DS . "" . "clm" . DS . "index.php");
		if(!clm_core::$db->config()->database_safe) {
			$out = clm_core::$load->db_update(3);
		}
	}
	function postflight($type, $parent) {}

	function retrieveOldComponentParams() {
		jimport('joomla.application.component.helper');
		$this->params['lv'] = JComponentHelper::getParams('com_clm')->get('lv',null);
		$this->params['menue'] = JComponentHelper::getParams('com_clm')->get('menue',null);
		$this->params['dropdown'] = JComponentHelper::getParams('com_clm')->get('dropdown',null);
		$this->params['vereineliste'] = JComponentHelper::getParams('com_clm')->get('vereineliste',null);
		$this->params['verein_sort'] = JComponentHelper::getParams('com_clm')->get('verein_sort',null);
		$this->params['logfile'] = JComponentHelper::getParams('com_clm')->get('logfile',null);
		$this->params['liga_saison'] = JComponentHelper::getParams('com_clm')->get('liga_saison',null);
		$this->params['sl_mail'] = JComponentHelper::getParams('com_clm')->get('sl_mail',null);
		$this->params['meldeliste'] = JComponentHelper::getParams('com_clm')->get('meldeliste',null);
		$this->params['rangliste'] = JComponentHelper::getParams('com_clm')->get('rangliste',null);
		$this->params['kommentarfeld'] = JComponentHelper::getParams('com_clm')->get('kommentarfeld',null);
		$this->params['email_from'] = JComponentHelper::getParams('com_clm')->get('email_from',null);
		$this->params['email_bcc'] = JComponentHelper::getParams('com_clm')->get('email_bcc',null);
		$this->params['email_fromname'] = JComponentHelper::getParams('com_clm')->get('email_fromname',null);
		$this->params['org_logo'] = JComponentHelper::getParams('com_clm')->get('org_logo',null);
		$this->params['upload_swt'] = JComponentHelper::getParams('com_clm')->get('upload_swt',null);
		$this->params['execute_swt'] = JComponentHelper::getParams('com_clm')->get('execute_swt',null);
		$this->params['fe_pgn_show'] = JComponentHelper::getParams('com_clm')->get('fe_pgn_show',null);
		$this->params['fe_pgn_moveFont'] = JComponentHelper::getParams('com_clm')->get('fe_pgn_moveFont',null);
		$this->params['conf_ergebnisse'] = JComponentHelper::getParams('com_clm')->get('conf_ergebnisse',null);
		$this->params['meldung_heim'] = JComponentHelper::getParams('com_clm')->get('meldung_heim',null);
		$this->params['meldung_verein'] = JComponentHelper::getParams('com_clm')->get('meldung_verein',null);
		$this->params['conf_meldeliste'] = JComponentHelper::getParams('com_clm')->get('conf_meldeliste',null);
		$this->params['pdf_meldelisten'] = JComponentHelper::getParams('com_clm')->get('pdf_meldelisten',null);
		$this->params['fe_submenu'] = JComponentHelper::getParams('com_clm')->get('fe_submenu',null);
		$this->params['fe_submenu_t'] = JComponentHelper::getParams('com_clm')->get('fe_submenu_t',null);
		$this->params['conf_vereinsdaten'] = JComponentHelper::getParams('com_clm')->get('conf_vereinsdaten',null);
		$this->params['man_manleader'] = JComponentHelper::getParams('com_clm')->get('man_manleader',null);
		$this->params['man_mail'] = JComponentHelper::getParams('com_clm')->get('man_mail',null);
		$this->params['man_tel'] = JComponentHelper::getParams('com_clm')->get('man_tel',null);
		$this->params['man_mobil'] = JComponentHelper::getParams('com_clm')->get('man_mobil',null);
		$this->params['man_spiellokal'] = JComponentHelper::getParams('com_clm')->get('man_spiellokal',null);
		$this->params['man_spielplan'] = JComponentHelper::getParams('com_clm')->get('man_spielplan',null);
		$this->params['man_showdwz'] = JComponentHelper::getParams('com_clm')->get('man_showdwz',null);
		$this->params['fe_vereinsliste_vs'] = JComponentHelper::getParams('com_clm')->get('fe_vereinsliste_vs',null);
		$this->params['fe_vereinsliste_hpage'] = JComponentHelper::getParams('com_clm')->get('fe_vereinsliste_hpage',null);
		$this->params['fe_vereinsliste_dwz'] = JComponentHelper::getParams('com_clm')->get('fe_vereinsliste_dwz',null);
		$this->params['fe_vereinsliste_elo'] = JComponentHelper::getParams('com_clm')->get('fe_vereinsliste_elo',null);
		$this->params['fe_runde_rang'] = JComponentHelper::getParams('com_clm')->get('fe_runde_rang',null);
		$this->params['runde_aktuell'] = JComponentHelper::getParams('com_clm')->get('runde_aktuell',null);
		$this->params['fe_runde_tln'] = JComponentHelper::getParams('com_clm')->get('fe_runde_tln',null);
		$this->params['fe_pgn_commentFont'] = JComponentHelper::getParams('com_clm')->get('fe_pgn_commentFont',null);
		$this->params['fe_pgn_style'] = JComponentHelper::getParams('com_clm')->get('fe_pgn_style',null);
		$this->params['fixth_msch'] = JComponentHelper::getParams('com_clm')->get('fixth_msch',null);
		$this->params['fixth_dwz'] = JComponentHelper::getParams('com_clm')->get('fixth_dwz',null);
		$this->params['fixth_tkreuz'] = JComponentHelper::getParams('com_clm')->get('fixth_tkreuz',null);
		$this->params['fixth_ttab'] = JComponentHelper::getParams('com_clm')->get('fixth_ttab',null);
		$this->params['fixth_ttln'] = JComponentHelper::getParams('com_clm')->get('fixth_ttln',null);
		$this->params['fe_display_lose_by_default'] = JComponentHelper::getParams('com_clm')->get('fe_display_lose_by_default',null);
		$this->params['googlemaps'] = JComponentHelper::getParams('com_clm')->get('googlemaps',null);
		$this->params['googlemaps_api'] = JComponentHelper::getParams('com_clm')->get('googlemaps_api',null);
		$this->params['googlemaps_rtype'] = JComponentHelper::getParams('com_clm')->get('googlemaps_rtype',null);
		$this->params['googlemaps_ver'] = JComponentHelper::getParams('com_clm')->get('googlemaps_ver',null);
		$this->params['googlemaps_vrout'] = JComponentHelper::getParams('com_clm')->get('googlemaps_vrout',null);
		$this->params['googlecharts'] = JComponentHelper::getParams('com_clm')->get('googlecharts',null);
		$this->params['tourn_linkclub'] = JComponentHelper::getParams('com_clm')->get('tourn_linkclub',null);
		$this->params['tourn_showtlok'] = JComponentHelper::getParams('com_clm')->get('tourn_showtlok',null);
		$this->params['tourn_comment_parse'] = JComponentHelper::getParams('com_clm')->get('tourn_comment_parse',null);
		$this->params['menue'] = JComponentHelper::getParams('com_clm')->get('menue',null);
		$this->params['maps_resolver'] = JComponentHelper::getParams('com_clm')->get('maps_resolver',null);
		$this->params['maps_zoom'] = JComponentHelper::getParams('com_clm')->get('maps_zoom',null);
	}
	
	function saveParams() {
		foreach($this->params as $key => $value) {
			if($value!=null) {
				clm_core::$db->config()->$key=$value;
			}
		}
	}
	
	private function getParam($name) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE element = "com_clm"');
		$manifest = json_decode($db->loadResult(), true);
		return $manifest[$name];
	}
	
	private function add($type,$name,$content) {
		$sql = 'INSERT INTO #__clm_logging (`callid`, `userid`,`timestamp`,`type`,`name`,`content`) VALUES ("'.uniqid ( "", false ).'", -1, '.time().', '.$type.',  "'.clm_core::$db->escape(htmlentities($name,ENT_QUOTES, "UTF-8")).'", "'.clm_core::$db->escape(htmlentities($content, ENT_QUOTES, "UTF-8")).'")';
		clm_core::$db->query($sql);
	}
	
	// -1 => no clm
	// 0 => < 1.5.2
	// 1 => >= 1.5.2 < 3.x
	// 2 => >= 3.x
	public static function tableStatus() {
		if(!self::isTable("clm_meldeliste_spieler")) {
			return -1;
		}
		if(self::isTable("clm_config")) {
			return 2;
		}
		if(!self::isColumn("clm_meldeliste_spieler","start_dwz")) {
			return 0;
		}
		return 1;
	}
	
	public static function isTable($table) {
		$db = JFactory::getDBO();
		$tables = $db->getTableList();
		if(in_array($db->getPrefix().$table,$tables)) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function isColumn($table,$column) {
		if(!self::isTable($table)) {
			return false;		
		}
		$db = JFactory::getDBO();
		$columns = $db->getTableColumns($db->getPrefix().$table);
		if(isset($columns[$column])){
			return true;
		} else {
			return false;
		}
	}
}
