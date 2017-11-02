<?php
defined('clm') or die('Restricted access');
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
//clm_core::$cms->addStyleSheet(clm_core::$url."css/schedule.css");
?>
<style type="text/css">

#clm .clm .clmbox { width:100%; margin: 10px 0 0 0; border: 1px solid #CCC; float: none !important; padding: 5px 0; background-color: #F5F5F5; color: #666666 !important;}
#clm .clm .clmbox a { padding: 0px 10px; text-decoration: none; line-height: 20px;}
#clm .clm .clmbox a:hover { text-decoration: underline; }

#clm .clm .clmbox select { margin: 2px 2px 2px 2px; border: 1px solid #CCC; float: none !important; padding: 0 0; height: auto; background-color: #FFFFFF; color: #000000 !important;}

#clm .clm .flex {
	display: -webkit-box;
	display: -moz-box;
	display: -ms-flexbox;
	display: -webkit-flex;
	display: flex;
	-webkit-flex-flow: row wrap;
	-moz-flex-flow: row wrap;
	-ms-flex-flow: row wrap;
	flex-flow: row wrap;
	width:100%;
	overflow:hidden;
}

#clm .clm .element {
	padding-left: <?php echo $clm_cellin_left; ?>;
	padding-top: <?php echo $clm_cellin_top; ?>;
	padding-right: <?php echo $clm_cellin_right; ?>;
	padding-bottom: <?php echo $clm_cellin_bottom; ?>; 
	border: <?php echo $clm_border; ?>; 
	text-align:center;
	text-align:-moz-center;
	text-align:-webkit-center;
	margin:0;
}

#clm .clm .title .element { 
	font-size: 110%; 
	font-weight: bold;
	padding-left: <?php echo $clm_cellin_left; ?>;
	padding-top: <?php echo $clm_cellin_top; ?>;
	padding-right: <?php echo $clm_cellin_right; ?>;
	padding-bottom: <?php echo $clm_cellin_bottom; ?>; 
	border: <?php echo $clm_border; ?>;
	background-color: #333333;
	color: #FFFFFF;
}

#clm .clm .zeile1 .element { 
	font-size: 110%; 
	padding-left: <?php echo $clm_cellin_left; ?>;
	padding-top: <?php echo $clm_cellin_top; ?>;
	padding-right: <?php echo $clm_cellin_right; ?>;
	padding-bottom: <?php echo $clm_cellin_bottom; ?>; 
	border: <?php echo $clm_border; ?>;
	background-color: <?php echo $clm_zeile1; ?>;
}

#clm .clm .zeile2 .element { 
	font-size: 110%; 
	padding-left: <?php echo $clm_cellin_left; ?>;
	padding-top: <?php echo $clm_cellin_top; ?>;
	padding-right: <?php echo $clm_cellin_right; ?>;
	padding-bottom: <?php echo $clm_cellin_bottom; ?>; 
	border: <?php echo $clm_border; ?>;
	background-color: <?php echo $clm_zeile2; ?>;
}

#clm .clm .clm_view_schedule .date {
/*	background-color: #f5f5f5;	*/
	text-align:center; 
	text-align:-moz-center;
	text-align:-webkit-center;
	width:16%;
}

#clm .clm .clm_view_schedule .result {
	text-align:center; 
	text-align:-moz-center;
	text-align:-webkit-center;
	width:10%;
}

#clm .clm .clm_view_schedule .home, #clm .clm .clm_view_schedule .guest {
	width:25%;
}
#clm .clm .clm_view_schedule .league {
	width:18%;
}
#clm .clm .clm_view_schedule .dg, #clm .clm .clm_view_schedule .round {
	width:3%;
}



/* Für kleine Browser Fenster oder Handys */

@media all and (max-width: 480px) {
 
	#clm .clm .clm_view_schedule .flex {
		-webkit-flex-direction: column;
		-ms-flex-direction: column;
		-webkit-box-orient: vertical;
		flex-direction: column;
	}
	
	#clm .clm .clm_view_schedule .home, #clm .clm .clm_view_report .guest {
		min-width:0;
		width:100%;
	}
 
	#clm .clm .clm_view_schedule .date {
		width:100%;
		margin-top:1.5em;
	}

	#clm .clm .clm_view_schedule .result {
		width:100%;
	}
 
 }
 
 /* Anpassung für die Buttons */
 
#clm .clm .clm_view_schedule .button_container {
	display: -webkit-box;
	display: -moz-box;
	display: -ms-flexbox;
	display: -webkit-flex;
	display: flex;
	-webkit-flex-flow: row wrap;
	-moz-flex-flow: row wrap;
	-ms-flex-flow: row wrap;
	flex-flow: row wrap;
	width:100%;
}

#clm .clm .clm_view_schedule .button_container .clm_button {
	-webkit-flex: 1 0 auto;
	-ms-flex: 1 0 auto; 
	flex: 1 0 auto; 
}

</style>

<?php } ?>
