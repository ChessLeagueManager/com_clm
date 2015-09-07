--
-- 3.1.19 Sonderrangliste neu mit Kombiauswahl m/w mit unterschiedlichen Geburtsjahren
--
ALTER TABLE `#__clm_turniere_sonderranglisten` ADD `use_sex_year_filter` enum('0','1') default '0' AFTER `zps_lower_than`;
ALTER TABLE `#__clm_turniere_sonderranglisten` ADD `maleYear_younger_than` year(4) default NULL AFTER `use_sex_year_filter`;
ALTER TABLE `#__clm_turniere_sonderranglisten` ADD `maleYear_older_than` year(4) default NULL AFTER `maleYear_younger_than`;
ALTER TABLE `#__clm_turniere_sonderranglisten` ADD `femaleYear_younger_than` year(4) default NULL AFTER `maleYear_older_than`;
ALTER TABLE `#__clm_turniere_sonderranglisten` ADD `femaleYear_older_than` year(4) default NULL AFTER `femaleYear_younger_than`;
