<?php
// Pfade zum Style Sheet und Stylesheet laden !
// Es gab ein paar Probleme mit dem Joomla Dateiseperator die diese Massnahme noetig machen

	// Falls nicht bereits aktiv.
	require_once (JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_clm" . DIRECTORY_SEPARATOR . "clm" . DIRECTORY_SEPARATOR . "index.php");

	// Konfigurationsparameter auslesen
	$config				= clm_core::$db->config();
	$template			= $config->template;
	$clm_lesehilfe		= $config->lesehilfe;
	$clm_zeile1			= "#".$config->zeile1;
	$clm_zeile2			= "#".$config->zeile2;
	$clm_re_col			= "#".$config->re_col;
	$clm_tableth		= "#".$config->tableth;
	$clm_subth			= "#".$config->subth;
	$clm_tableth_s1		= "#".$config->tableth_s1;
	$clm_tableth_s2		= "#".$config->tableth_s2;
	$clm_cellin_top			= $config->cellin_top;
	$clm_cellin_left			= $config->cellin_left;
	$clm_cellin_right			= $config->cellin_right;
	$clm_cellin_bottom			= $config->cellin_bottom;
	$clm_border			= $config->border_length." ".$config->border_style." #".$config->border_color;
	$clm_wrong1 		= "#".$config->wrong1;
	$clm_wrong2			= $config->wrong2_length." ".$config->wrong2_style." #".$config->wrong2_color;
	$clm_rang_auf		= "#".$config->rang_auf;
	$clm_rang_auf_evtl	= "#".$config->rang_auf_evtl;
	$clm_rang_ab		= "#".$config->rang_ab;
	$clm_rang_ab_evtl	= "#".$config->rang_ab_evtl;
	$clm_msch_nr		= $config->msch_nr;
	$clm_msch_dwz		= $config->msch_dwz;
	$clm_msch_rnd		= $config->msch_rnd;
	$clm_msch_punkte	= $config->msch_punkte;
	$clm_msch_spiele	= $config->msch_spiele;
	$clm_msch_prozent	= $config->msch_prozent;
	$fe_pgn_show		= $config->fe_pgn_show;


	if ($template) {

	$document = JFactory::getDocument();
	$document->addStyleSheet('components/com_clm/includes/clm_content.css', 'text/css');
?>
<style type="text/css">

#clm .clm .pgn a, #clm .clm  a.pgn { color:<?php echo $fe_pgn_show; ?> !important; }

<?php if ($clm_lesehilfe =="1") { ?>#clm .clm tr.zeile1:hover td, #clm .clm tr.zeile2:hover td, #clm .clm tr.zeile1_auf:hover td, #clm .clm tr.zeile2_auf:hover td {background-color: #FFFFBB !important;}  /*-- Lesehilfe --*/
<?php } ?>

#clm .clm table th, #clm .clm table td { padding-left: <?php echo $clm_cellin_left; ?>; padding-top: <?php echo $clm_cellin_top; ?>; padding-right: <?php echo $clm_cellin_right; ?>; padding-bottom: <?php echo $clm_cellin_bottom; ?>; border: <?php echo $clm_border; ?>; }
#spieler div.title, #spieler div.spielerverlauf  { border-bottom: none !important; border: <?php echo $clm_border; ?>;}
.right .selectteam  { border: <?php echo $clm_border; ?>;}

#clm .clm .zeile1, #clm .clm .zeile1_dg2 { background-color: <?php echo $clm_zeile1; ?>;} 
#clm .clm .zeile2, #clm .clm .zeile2_dg2{ background-color: <?php echo $clm_zeile2; ?>;} 

#mannschaft td.punkte, #mannschaft td.spiele, #mannschaft td.prozent, #turnier_tabelle td.pkt, #turnier_tabelle td.sobe, #turnier_tabelle td.bhlz, #turnier_tabelle td.busu, #rangliste td.mp, #rangliste td.bp, td.fw_col {background-color: <?php echo $clm_re_col; ?> !important;} 

#clm .clm table th, #clm .clm table th div{ background-color: <?php echo $clm_tableth; ?>; color: <?php echo $clm_tableth_s1; ?> !important;}
#clm .clm table .anfang, #clm .clm table .ende, #clm .clm .clmbox, #spieler table.spielerverlauf th, #spieler div.spielerverlauf, #spieler .title, #clm .clm .clm-navigator ul li, #clm .clm .clm-navigator ul li ul{ background-color: <?php echo $clm_subth; ?>; color: <?php echo $clm_tableth_s2; ?> !important;}

#vereinsliste th.col_1, #vereinsliste th.col_2,#vereinsliste th.col_3,#vereinsliste th.col_4,#vereinsliste th.col_5,#vereinsliste th.col_6,#vereinsliste th.col_7,#vereinsliste th.col_8, #vereinsliste th.col{background-color: <?php echo $clm_subth; ?>; color: <?php echo $clm_tableth_s2; ?> !important;}

#wrong, .wrong {background:<?php echo $clm_wrong1; ?>; border:<?php echo $clm_wrong2; ?>;}

#mannschaft .nr { width: <?php echo $clm_msch_nr; ?>; } 
#mannschaft .dwz { width: <?php echo $clm_msch_dwz; ?>; } 
#mannschaft .rnd { width: <?php echo $clm_msch_rnd; ?>; }
#mannschaft .punkte { width: <?php echo $clm_msch_punkte; ?>; }
#mannschaft .spiele { width: <?php echo $clm_msch_spiele; ?>; }
#mannschaft .prozent{ width: <?php echo $clm_msch_prozent; ?>; } 

/* Lesehilfe AUS bei Einzelturniermodus */
#tableoverflow tr.zeile1:hover td, #tableoverflow tr.zeile2:hover td, #tableoverflow tr.zeile1_auf:hover td, #tableoverflow tr.zeile2_auf:hover td {background: none !important;}
#tableoverflow tr.zeile1:hover td.sobe, #tableoverflow tr.zeile2:hover td.sobe, #tableoverflow tr.zeile1:hover td.fw_col, #tableoverflow tr.zeile2:hover td.fw_col, #tableoverflow tr.zeile1:hover td.pkt, #tableoverflow tr.zeile2:hover td.pkt,#tableoverflow tr.zeile1:hover td.busu, #tableoverflow tr.zeile2:hover td.busu{background-color: #FFFFCC !important;} 
#tableoverflow tr.zeile1:hover td.trenner, #tableoverflow tr.zeile2:hover td.trenner {background-color: #E8E8E8 !important;}
/** Auf- und Abstieg **/

#clm .clm .rang_auf {background-color: <?php echo $clm_rang_auf; ?> !important;} 
#clm .clm .rang_auf_evtl {background-color: <?php echo $clm_rang_auf_evtl; ?> !important;} 
#clm .clm .rang_ab {background-color: <?php echo $clm_rang_ab; ?> !important;} 
#clm .clm .rang_ab_evtl {background-color: <?php echo $clm_rang_ab_evtl; ?> !important;}

</style>

<?php } ?>
