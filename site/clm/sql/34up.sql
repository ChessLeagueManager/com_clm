--
-- 3.7.2 neuer CLM-User: Turnierleiter mit SWT-Berechtigung
--
REPLACE INTO `#__clm_usertype` (`id`, `name`, `usertype`, `kind`, `published`, `ordering`, `params`) VALUES
(18, 'Turnierleiter+SWT', 'tlimp', 'CLM', 1, 16, 'BE_general_general=1\nBE_event_general=1\nBE_tournament_general=1\nBE_tournament_edit_detail=2\nBE_tournament_edit_round=2\nBE_tournament_edit_result=2\nBE_tournament_edit_fixture=2\nBE_team_edit=2\nBE_swt_general=1');

