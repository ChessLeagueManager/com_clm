<?php
// http://www.php.net/manual/en/function.is-numeric.php, thanks to  jamespam at hotmail dot com
function clm_function_is_whole_number($var){
  return (is_numeric($var)&&(intval($var)==floatval($var)));
}
?>
