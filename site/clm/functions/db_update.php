<?php
function clm_function_db_update($status) {
	$version = clm_core::$load->version();
	// fresh installation
	if ($status == 0) {
		clm_core::$load->db_import_sql(clm_core::$db, clm_core::$path . DS . "sql" . DS . "install.sql");
		clm_core::$load->db_import_sql(clm_core::$db, clm_core::$path . DS . "sql" . DS . "verband.sql");
		clm_core::$load->db_import_sql(clm_core::$db, clm_core::$path . DS . "sql" . DS . "base_data.sql");
		clm_core::$db->config()->cl_config = $version[0];
		clm_core::$db->config()->db_config = $version[2];
		return true;
	}
	// initial convert
	else if ($status == 1) {
		clm_core::$load->db_import_sql(clm_core::$db, clm_core::$path . DS . "sql" . DS . "0up.sql");
		clm_core::$load->db_import_sql(clm_core::$db, clm_core::$path . DS . "sql" . DS . "base_data.sql");
		clm_core::$db->config()->db_config = 1;
		return clm_function_db_update(2);
	}
	// update
	else if ($status == 2) {
		while(clm_core::$db->config()->db_config<$version[2])
		{
				clm_core::$load->db_import_sql(clm_core::$db, clm_core::$path . DS . "sql" . DS . clm_core::$db->config()->db_config . "up.sql");
				clm_core::$db->config()->db_config++;
		}
		// Bei jedem Update die Verbände aktualisieren
		//CLM parameter auslesen
		$config = clm_core::$db->config();
		if ($config->countryversion =="de") {
			clm_core::$load->db_import_sql(clm_core::$db, clm_core::$path . DS . "sql" . DS . "verband.sql");
		}
		clm_core::$db->config()->cl_config = $version[0];
		clm_core::$db->config()->db_config = $version[2];
		return true;
	}
	// uninstall
	else if ($status == 3) {
		clm_core::$load->db_import_sql(clm_core::$db, clm_core::$path . DS . "sql" . DS . "uninstall.sql");
		return true;
	}
	return false;
}