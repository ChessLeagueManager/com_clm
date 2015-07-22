<?php
defined('clm') or die('Restricted access');
$lang = clm_core::$lang->table;
clm_core::$cms->addScriptDeclaration('var clm_table_url = "'.clm_core::$url.clm_core::$load->gen_url().'";');
clm_core::$cms->addScriptDeclaration('var clm_table_error0 = "'.$lang->error0.'";');
clm_core::$cms->addScriptDeclaration('var clm_table_error1 = "'.$lang->error1.'";');
clm_core::$cms->addScriptDeclaration('var clm_table_error2 = "'.$lang->error2.'";');
clm_core::$cms->addScriptDeclaration('var clm_table_success = "'.$lang->success.'";');
clm_core::$cms->addScriptDeclaration('var clm_table_running = "'.$lang->running.'";');
clm_core::$cms->addScriptDeclaration('var clm_table_really = "'.$lang->onlySpecial("really").'";');

clm_core::$cms->addScriptDeclaration('var clm_table_sEmptyTable = "'.$lang->sEmptyTable.'";');
clm_core::$cms->addScriptDeclaration('var clm_table_sInfo = "'.$lang->sInfo.'";');
clm_core::$cms->addScriptDeclaration('var clm_table_sInfoEmpty = "'.$lang->sInfoEmpty.'";');
clm_core::$cms->addScriptDeclaration('var clm_table_sInfoFiltered = "'.$lang->sInfoFiltered.'";');
clm_core::$cms->addScriptDeclaration('var clm_table_sInfoPostFix = "'.$lang->sInfoPostFix.'";');
clm_core::$cms->addScriptDeclaration('var clm_table_sInfoThousands = "'.$lang->sInfoThousands.'";');
clm_core::$cms->addScriptDeclaration('var clm_table_sLengthMenu = "'.$lang->sLengthMenu.'";');
clm_core::$cms->addScriptDeclaration('var clm_table_sLoadingRecords = "'.$lang->sLoadingRecords.'";');
clm_core::$cms->addScriptDeclaration('var clm_table_sProcessing = "'.$lang->sProcessing.'";');
clm_core::$cms->addScriptDeclaration('var clm_table_sSearch = "'.$lang->sSearch.'";');
clm_core::$cms->addScriptDeclaration('var clm_table_sZeroRecords = "'.$lang->sZeroRecords.'";');
clm_core::$cms->addScriptDeclaration('var clm_table_sFirst = "'.$lang->sFirst.'";');
clm_core::$cms->addScriptDeclaration('var clm_table_sPrevious = "'.$lang->sPrevious.'";');
clm_core::$cms->addScriptDeclaration('var clm_table_sNext = "'.$lang->sNext.'";');
clm_core::$cms->addScriptDeclaration('var clm_table_sLast = "'.$lang->sLast.'";');
clm_core::$cms->addScriptDeclaration('var clm_table_sSortAscending = "'.$lang->sSortAscending.'";');
clm_core::$cms->addScriptDeclaration('var clm_table_sSortDescending = "'.$lang->sSortDescending.'";');

$config = clm_core::$db->config();
clm_core::$cms->addScriptDeclaration('var clm_table_pageLength = '.$config->table_pageLength.';');

clm_core::$load->load_js("jquery");
clm_core::$cms->addScript(clm_core::$url."js/DataTables.js");
clm_core::$cms->addScript(clm_core::$url."js/table.js");
?>
