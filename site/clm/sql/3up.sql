--
-- Bei einer Neuinstallation fehlten diese Einträge bisher 
--

TRUNCATE TABLE `#__clm_ergebnis`;
INSERT INTO `#__clm_ergebnis` (`id`, `eid`, `erg_text`) VALUES
(1, 0, '0-1'),
(2, 1, '1-0'),
(3, 2, '0,5-0,5'),
(4, 3, '0-0'),
(5, 4, '-/+'),
(6, 5, '+/-'),
(7, 6, '-/-'),
(8, 7, '---'),
(9, 8, 'spielfrei');
