--
-- 3.2.4  Erweiterung Wertebereich Feinwertungen
--
ALTER TABLE `#__clm_mannschaften` CHANGE `sumtiebr1` `sumtiebr1` decimal(7,3) DEFAULT '0.000';
ALTER TABLE `#__clm_mannschaften` CHANGE `sumtiebr2` `sumtiebr2` decimal(7,3) DEFAULT '0.000';
ALTER TABLE `#__clm_mannschaften` CHANGE `sumtiebr3` `sumtiebr3` decimal(7,3) DEFAULT '0.000';
