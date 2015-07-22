--
-- Verhindert Inkonsistenz bei REPLACE
--
set session old_alter_table=1;
ALTER IGNORE TABLE `#__clm_user` ADD UNIQUE( `sid`, `jid`);
set session old_alter_table=0;
