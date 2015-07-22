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

jimport( 'joomla.filesystem.file' );

class CLMSWT {

	static function readString ($file, $offset, $length = 1) {
		$i = 0;
		$name = '';
		while(ord($chr = JFile::read($file,false,1,8192,$offset+$i)) != 0 AND $i < $length){
			if ($chr != '"' AND $chr != "'") $name .= $chr;
			$i++;
		}
		
		return utf8_encode($name);
	}
	
	static function readName ($file, $offset, $length = 1) {
		return CLMSWT::readString ($file, $offset, $length);
	}

	static function readInt ($file, $offset, $length = 1) {
		$value = 0;
		for ($i = 0; $i < $length; $i++) {
			$cur = ord(JFile::read ($file, false, 1, 8192, $offset+$i));
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
			$data = JRequest::getVar($name);

			if(is_array($keys)) {
				foreach($keys as $key) {
					if(isset($data[$key])){
						$data = $data[$key];
					}
				}
			} else {
				if(isset($data[$keys])){
					$data = $data[$keys];
				}
			}
		} else {
			$data = JRequest::getVar($name);
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
