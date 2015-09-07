<?php
function clm_function_is_url($url)
{
    return (filter_var($url, FILTER_VALIDATE_URL) == $url ? true : false);
}
?>
