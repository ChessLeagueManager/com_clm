<?php
defined('clm') or die('Restricted access');

$lang = clm_core::$lang->dewis_import;
clm_core::$cms->addScriptDeclaration('var clm_dewis_import_url = "'.clm_core::$url.clm_core::$load->gen_url().'";');
clm_core::$cms->addScriptDeclaration('var clm_dewis_import_noJson = "'.$lang->noJson.'";');
clm_core::$cms->addScriptDeclaration('var clm_dewis_import_wrongResponse = "'.$lang->wrongResponse.'";');
clm_core::$cms->addScriptDeclaration('var clm_dewis_import_nothingToUpdate = "'.$lang->nothingToUpdate.'";');
clm_core::$cms->addScriptDeclaration('var clm_dewis_import_errorHttp = "'.$lang->errorHttp.'";');
clm_core::$cms->addScriptDeclaration('var clm_dewis_import_of = "'.$lang->of.'";');
clm_core::$cms->addScriptDeclaration('var clm_dewis_import_loadingClubs = "'.$lang->loadingClubs.'";');
clm_core::$cms->addScriptDeclaration('var clm_dewis_import_update = "'.$lang->update.'";');
clm_core::$cms->addScriptDeclaration('var clm_dewis_import_alreadyFinishedClubs = "'.$lang->alreadyFinishedClubs.'";');
clm_core::$cms->addScriptDeclaration('var clm_dewis_import_alreadyFinishedPlayers = "'.$lang->alreadyFinishedPlayers.'";');
clm_core::$cms->addScriptDeclaration('var clm_dewis_import_working = "'.$lang->working.'";');
clm_core::$cms->addScriptDeclaration('var clm_dewis_import_finished = "'.$lang->finished.'";');
clm_core::$cms->addScriptDeclaration('var clm_dewis_import_finishedClub = "'.$lang->finishedClub.'";');
clm_core::$cms->addScriptDeclaration('var clm_dewis_import_updateClub = "'.$lang->updateClub.'";');
clm_core::$cms->addScript(clm_core::$url."js/dewis_import.js");
?>
