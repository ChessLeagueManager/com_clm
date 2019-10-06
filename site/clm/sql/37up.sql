--
-- @ Chess League Manager (CLM) Component 
-- @Copyright (C) 2008-2019 CLM Team.  All rights reserved
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
-- @link http://www.chessleaguemanager.de
--
-- 3.7.3  Joomla-User-ID angepasst 
--
ALTER TABLE `#__clm_user` MODIFY `checked_out_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00';
ALTER TABLE `#__clm_user` MODIFY `jid` int(11) unsigned DEFAULT NULL;

