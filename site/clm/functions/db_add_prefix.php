<?php
	// http://hg.joomla.org/joomla-platform/src/247ba8d88526/libraries/joomla/database/database.php, thanks to Joomla
	function clm_function_db_add_prefix($sql, $db_prefix, $prefix = '#__') {
		// Initialize variables.
		$escaped = false;
		$startPos = 0;
		$quoteChar = '';
		$literal = '';
		$sql = trim($sql);
		$n = strlen($sql);
		while ($startPos < $n) {
			$ip = strpos($sql, $prefix, $startPos);
			if ($ip === false) {
				break;
			}
			$j = strpos($sql, "'", $startPos);
			$k = strpos($sql, '"', $startPos);
			if (($k !== false) && (($k < $j) || ($j === false))) {
				$quoteChar = '"';
				$j = $k;
			} else {
				$quoteChar = "'";
			}
			if ($j === false) {
				$j = $n;
			}
			$literal.= str_replace($prefix, $db_prefix, substr($sql, $startPos, $j - $startPos));
			$startPos = $j;
			$j = $startPos + 1;
			if ($j >= $n) {
				break;
			}
			// quote comes first, find end of quote
			while (true) {
				$k = strpos($sql, $quoteChar, $j);
				$escaped = false;
				if ($k === false) {
					break;
				}
				$l = $k - 1;
				while ($l >= 0 && $sql{$l} == '\\') {
					$l--;
					$escaped = !$escaped;
				}
				if ($escaped) {
					$j = $k + 1;
					continue;
				}
				break;
			}
			if ($k === false) {
				// error in the query - no end quote; ignore it
				break;
			}
			$literal.= substr($sql, $startPos, $k - $startPos + 1);
			$startPos = $k + 1;
		}
		if ($startPos < $n) {
			$literal.= substr($sql, $startPos, $n - $startPos);
		}
		return $literal;
	}
?>