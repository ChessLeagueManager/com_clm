<?php
defined('clm') or die('Restricted access');

$lang = clm_core::$lang->dsb_import;
clm_core::$cms->addScriptDeclaration('var clm_dsb_import_url = "'.clm_core::$url.clm_core::$load->gen_url().'";');
clm_core::$cms->addScriptDeclaration('var clm_dsb_import_noJson = "'.$lang->noJson.'";');
clm_core::$cms->addScriptDeclaration('var clm_dsb_import_wrongResponse = "'.$lang->wrongResponse.'";');
clm_core::$cms->addScriptDeclaration('var clm_dsb_import_errorHttp = "'.$lang->errorHttp.'";');
clm_core::$cms->addScriptDeclaration('var clm_dsb_import_browserProblem = "'.$lang->browserProblem.'";');
clm_core::$cms->addScriptDeclaration('var clm_dsb_import_fileProblem1 = "'.$lang->fileProblem1.'";');
clm_core::$cms->addScriptDeclaration('var clm_dsb_import_fileProblem2 = "'.$lang->fileProblem2.'";');
clm_core::$cms->addScriptDeclaration('var clm_dsb_import_uploadProblem = "'.$lang->uploadProblem.'";');
clm_core::$cms->addScriptDeclaration('var clm_dsb_import_playerFile = "'.$lang->playerFile.'";');
clm_core::$cms->addScriptDeclaration('var clm_dsb_import_clubFile = "'.$lang->clubFile.'";');
clm_core::$cms->addScriptDeclaration('var clm_dsb_import_updatePlayer = "'.$lang->updatePlayer.'";');
clm_core::$cms->addScriptDeclaration('var clm_dsb_import_percentage = "'.$lang->percentage.'";');
clm_core::$cms->addScriptDeclaration('var clm_dsb_import_of = "'.$lang->of.'";');
clm_core::$cms->addScriptDeclaration('var clm_dsb_import_updatedPlayer = "'.$lang->updatedPlayer.'";');
clm_core::$cms->addScriptDeclaration('var clm_dsb_import_finishedPlayer = "'.$lang->finishedPlayer.'";');
clm_core::$cms->addScriptDeclaration('var clm_dsb_import_updatedClub = "'.$lang->updatedClub.'";');
clm_core::$cms->addScriptDeclaration('var clm_dsb_import_updateClub = "'.$lang->updateClub.'";');
clm_core::$cms->addScriptDeclaration('var clm_dsb_import_finishedClub = "'.$lang->finishedClub.'";');
clm_core::$cms->addScriptDeclaration('var clm_dsb_import_amount = '.clm_core::$db->config()->dsb_import_amount.';');
clm_core::$cms->addScript(clm_core::$url."js/dsb_import.js");
?>
