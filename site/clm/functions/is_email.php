<?php
// http://snipplr.com/view/5226/php--isvalidemail/
function clm_function_is_email($email)
{
    return (filter_var($email, FILTER_VALIDATE_EMAIL) == $email ? true : false);
}
?>