<?php
// from http://stackoverflow.com/questions/1883079/best-practice-import-mysql-file-in-php-split-queries/2011454#2011454, thanks to Kovge
function clm_function_db_import_sql($db, $file, $delimiter = ';') {
	@set_time_limit(0); // hope
	$matches = array();
	$otherDelimiter = false;
	$error[0]=false;
	if (is_file($file) === true) {
		$file = fopen($file, 'r');
		if (is_resource($file) === true) {
			$query = array();
			while (feof($file) === false) {
				$query[] = fgets($file);
				if (preg_match('~' . preg_quote('delimiter', '~') . '\s*([^\s]+)$~iS', end($query), $matches) === 1) {
					array_pop($query); //WE DON'T NEED THIS LINE IN SQL QUERY
					if ($otherDelimiter = ($matches[1] != $delimiter)) {
					} else {
						array_pop($query);
						$query[] = $delimiter;
					}
				}
				if (!$otherDelimiter && preg_match('~' . preg_quote($delimiter, '~') . '\s*$~iS', end($query)) === 1) {
					$query = trim(implode('', $query));
					if ($db->query($query) === false) {
						if(!$error[0]){
						$error[1]='ERROR: ' . htmlentities($query);
						$error[0]=true;
						}
					}
				}
				if (is_string($query) === true) {
					$query = array();
				}
			}
			return fclose($file);
		}
	}
	return $error;
}
?>
