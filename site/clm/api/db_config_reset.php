<?php
function clm_api_db_config_reset() {
	// Lese die Versionsnummern aus
	$cl_config = clm_core::$db->config()->cl_config;
	$db_config = clm_core::$db->config()->db_config;
	// Schreibe alle Änderungen
	clm_core::$db->config->write();
	// Leere die Tabelle
	clm_core::$db->query("TRUNCATE TABLE #__clm_config");
	// Ausgelesene Elemente verwerfen
	clm_core::$db->config->reset();
	// Setze die Versionsnummern wieder
	clm_core::$db->config()->cl_config = $cl_config;
	clm_core::$db->config()->db_config = $db_config;
	// Schreibe alle Änderungen
	clm_core::$db->config->write();
	return array(true, "m_configResetSuccess");
}
?>
