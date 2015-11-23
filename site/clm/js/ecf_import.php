<?php
defined('clm') or die('Restricted access');

$lang = clm_core::$lang->ecf_import;
clm_core::$cms->addScriptDeclaration('var clm_ecf_import_url = "'.clm_core::$url.clm_core::$load->gen_url().'";');
clm_core::$cms->addScriptDeclaration('var clm_ecf_import_noJson = "'.$lang->noJson.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecf_import_wrongResponse = "'.$lang->wrongResponse.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecf_import_errorHttp = "'.$lang->errorHttp.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecf_import_browserProblem = "'.$lang->browserProblem.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecf_import_fileProblem1 = "'.$lang->fileProblem1.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecf_import_fileProblem2 = "'.$lang->fileProblem2.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecf_import_uploadProblem = "'.$lang->uploadProblem.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecf_import_playerFile = "'.$lang->playerFile.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecf_import_clubFile = "'.$lang->clubFile.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecf_import_orgFile = "'.$lang->orgFile.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecf_import_updatePlayer = "'.$lang->updatePlayer.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecf_import_percentage = "'.$lang->percentage.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecf_import_of = "'.$lang->of.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecf_import_updatedPlayer = "'.$lang->updatedPlayer.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecf_import_finishedPlayer = "'.$lang->finishedPlayer.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecf_import_updatedClub = "'.$lang->updatedClub.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecf_import_updateClub = "'.$lang->updateClub.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecf_import_finishedClub = "'.$lang->finishedClub.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecf_import_updatedOrg = "'.$lang->updatedOrg.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecf_import_updateOrg = "'.$lang->updateOrg.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecf_import_finishedOrg = "'.$lang->finishedOrg.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecf_import_amount = '.clm_core::$db->config()->dsb_import_amount.';');
clm_core::$cms->addScript(clm_core::$url."js/ecf_import.js");
?>
