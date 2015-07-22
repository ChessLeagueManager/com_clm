<?php
function clm_function_is_timestamp($timestamp)
{
if(!clm_core::$load->is_whole_number($timestamp) || $timestamp < 0 || $timestamp > 9999999999)
{
return false;
}
return true;
}
?>
