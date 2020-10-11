<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('clm') or die('Restricted access');

$lang = clm_core::$lang->ecfv2_import;
clm_core::$cms->addScriptDeclaration('var clm_ecfv2_import_url = "'.clm_core::$url.clm_core::$load->gen_url().'";');
clm_core::$cms->addScriptDeclaration('var clm_ecfv2_import_noJson = "'.$lang->noJson.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecfv2_import_wrongResponse = "'.$lang->wrongResponse.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecfv2_import_nothingToUpdate = "'.$lang->nothingToUpdate.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecfv2_import_errorHttp = "'.$lang->errorHttp.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecfv2_import_of = "'.$lang->of.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecfv2_import_loadingClubs = "'.$lang->loadingClubs.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecfv2_import_update = "'.$lang->update.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecfv2_import_alreadyFinishedClubs = "'.$lang->alreadyFinishedClubs.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecfv2_import_alreadyFinishedPlayers = "'.$lang->alreadyFinishedPlayers.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecfv2_import_working = "'.$lang->working.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecfv2_import_finished = "'.$lang->finished.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecfv2_import_finishedClub = "'.$lang->finishedClub.'";');
clm_core::$cms->addScriptDeclaration('var clm_ecfv2_import_updateClub = "'.$lang->updateClub.'";');
clm_core::$cms->addScript(clm_core::$url."js/ecfv2_import.js");
?>
