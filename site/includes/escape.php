<?php
function clm_escape($in)
{
	$db = JFactory::getDbo(); 
	return $db->escape($in);
}
?>
