<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
jimport( 'joomla.filesystem.file' );

class CLMSWT {

	static function readString ($file, $offset, $length = 1) {
		$i = 0;
		$name = '';
		while(ord($chr = file_get_contents ($file, false, null, $offset+$i, 1)) != 0 AND $i < $length){
//			if ($chr != '"' AND $chr != "'") $name .= $chr;
			$name .= $chr;
			$i++;
		}
		
		return clm_core::$load->utf8encode($name);
	}
	
	static function readName ($file, $offset, $length = 1) {
		return CLMSWT::readString ($file, $offset, $length);
	}

	static function readInt ($file, $offset, $length = 1) {
		$value = 0;
		for ($i = 0; $i < $length; $i++) {
			$cur = ord(file_get_contents ($file, false, null, $offset+$i, 1));
			$value += $cur * pow (256, $i);
		}
	return $value;
	}
	
	static function readBool ($file, $offset) {
		if (CLMSWT::readInt($file, $offset, 1) == 255) {
			return true;
		}
		elseif (CLMSWT::readInt($file, $offset, 1) == 0) {
			return false;
		}
		else { // Standardwert, wenn nicht = 00 oder FF
			return false;
		}
	}
	
	static function getFormValue($name, $default = null, $type = 'none', $keys = null) {
		if($keys !== null) {
			$data = $_POST[$name];
//			$data = clm_core::$load->request_array_string($name, null);

			if(is_array($keys)) {
				foreach($keys as $key) {
					if(isset($data[$key])){
						$data = $data[$key];
					}
				}
			} else {
				if(isset($data[$keys])){
					$data = $data[$keys];
				} else {
					$data = array();
				}
			}
		} else {
			$data = clm_core::$load->request_string($name, null);
		}
		
		if($data === null) {
			$data = $default;
		}
		
		if($type !== "none" AND gettype($data) != $type) {
			settype($data,$type);
		}
		
		return $data;
	}	

}
?>
