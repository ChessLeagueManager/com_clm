<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_db_ecf_org($orgs = array())
{
    @set_time_limit(0); // hope
    if (!is_array($orgs) || count($orgs) == 0) {
        return array(true, "w_noOrgToUpdate");
    }
    // Umbau der DB von deutscher Version auf englische Version, einmalige Aktion
    //	$db = JFactory::getDBO();
    //	$keys = $db->getTableKeys($db->getPrefix()."clm_dwz_spieler");
    $query = 'SHOW KEYS FROM #__clm_dwz_spieler';
    $keys = clm_core::$db->loadObjectList($query);
    $ks_mglnr = 0;
    $ks_pkz = 0;
    foreach ($keys as $key) {
        if ($key->Key_name == "sid_zps_mglnr" and $key->Non_unique == "0") {
            $ks_mglnr = 1;
        }
        if ($key->Key_name == "sid_zps_pkz") {
            $ks_pkz = 1;
        }
    }
    //	$columns = $db->getTableColumns($db->getPrefix()."clm_dwz_verbaende");
    $query = 'SHOW FULL COLUMNS FROM #__clm_dwz_verbaende';
    $columns = clm_core::$db->loadObjectList($query);
    $s_allocation = false;
    foreach ($columns as $column) {
        if ($column->Field == "Allocation") {
            $s_allocation = true;
        }
    }
    //	if (!isset($columns["Allocation"])) {
    if ($s_allocation == false) {
        //Leeren Verbaende, da standardm��ig mit DSB-Struktur gef�llt
        $sql = "TRUNCATE #__clm_dwz_verbaende";
        clm_core::$db->query($sql);
        $sql = "ALTER TABLE `#__clm_dwz_verbaende` ADD `Allocation` varchar(30) NOT NULL DEFAULT '' AFTER `Verbandname`;";
        clm_core::$db->query($sql);
        if ($ks_mglnr) {
            $sql = "ALTER TABLE `#__clm_dwz_spieler` DROP INDEX sid_zps_mglnr;";
            clm_core::$db->query($sql);
        }
        if (!$ks_pkz) {
            $sql = "ALTER TABLE `#__clm_dwz_spieler` ADD UNIQUE sid_zps_pkz (`sid`, `ZPS`, `PKZ`);";
            clm_core::$db->query($sql);
        }
    }
    $counter = 0;
    $sql = "REPLACE INTO #__clm_dwz_verbaende (`Verband`,`LV`, `Uebergeordnet`, `Verbandname`, `Allocation`) VALUES (?, ?, ?, ?, ?)";
    $stmt = clm_core::$db->prepare($sql);
    for ($i = 0;$i < count($orgs);$i++) {
        //Verband,LV,Uebergeordnet,Verbandname
        $einOrg = str_getcsv($orgs[$i], ",", '"');
        if (!isset($einOrg[4])) {
            $einOrg[4] = "";
        }
        if (count($einOrg) != 5) {
            continue;
        }
        $stmt->bind_param('sssss', $einOrg[0], $einOrg[1], $einOrg[2], $einOrg[3], $einOrg[4]);
        $stmt->execute();
        $counter++;
    }
    $stmt->close();
    return array(true, "m_ecfOrgSuccess", $counter);
}
