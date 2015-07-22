<?php
function clm_function_is_date($date, $format = 'YYYY-MM-DD HH:MM:SS')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
?>
