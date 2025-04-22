<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT . DS . 'includes' . DS . 'clm_tooltip.php');

$liga		= $this->liga;
$itemid		= clm_core::$load->request_int('Itemid', 0);
$sid		= clm_core::$load->request_int('saison', 0);
$lid		= clm_core::$load->request_int('liga', 0);

if (isset($liga[0])) {
    $sql = ' SELECT `sieg`, `remis`, `nieder`, `antritt`, `man_sieg`, `man_remis`, `man_nieder`, `man_antritt`'
        . ' FROM #__clm_liga'
        . ' WHERE `id` = "' . $lid . '"';
    $db = JFactory::getDBO();
    $db->setQuery($sql);
    $ligapunkte = $db->loadObject();

    //Parameter aufbereiten
    $paramsStringArray = explode("\n", $liga[0]->params);
    $params = array();
    foreach ($paramsStringArray as $value) {
        $ipos = strpos($value, '=');
        if ($ipos !== false) {
            $params[substr($value, 0, $ipos)] = substr($value, $ipos + 1);
        }
    }
    if (!isset($params['btiebr1']) or $params['btiebr1'] == 0) {   //Standardbelegung
        $params['btiebr1'] = 1;
        $params['btiebr2'] = 2;
        $params['btiebr3'] = 3;
        $params['btiebr4'] = 4;
        $params['btiebr5'] = 0;
        $params['btiebr6'] = 0;
    }
    if (!isset($params['bnhtml']) or $params['bnhtml'] == 0) {   //Standardbelegung
        $params['bnhtml'] = round(($liga[0]->teil) / 2);
    }
}
// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

?>
<div id="clm">
<div id="statistik">
<?php
$config = clm_core::$db->config();
$googlecharts   = $config->googlecharts;

// Browsertitelzeile setzen
$doc = JFactory::getDocument();
if (isset($liga[0])) {
    $doc->setTitle(JText::_('LEAGUE_STATISTIK').' '.$liga[0]->name);
} else {
    $doc->setTitle(JText::_('LEAGUE_STATISTIK'));
}
?>
<div class="componentheading">
<?php if (isset($liga[0])) {
    echo JText::_('LEAGUE_STATISTIK');
    echo "&nbsp;".$liga[0]->name;
} else {
    echo JText::_('LEAGUE_STATISTIK');
}
?>
<div id="pdf">
<?php
echo CLMContent::createPDFLink('statistik', JText::_('LEAGUE_STAT_PDF'), array('layout' => 'brettbeste', 'saison' => $liga[0]->sid, 'liga' => $liga[0]->id));
?>

</div></div>
<div class="clr"></div>
 
<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu.php'); ?>

<?php
$archive_check = clm_core::$api->db_check_season_user($sid);
if (!$archive_check) {
    echo "<div id='wrong'>".JText::_('NO_ACCESS')."<br>".JText::_('NOT_REGISTERED')."</div>";
} elseif (!isset($liga[0])) {
    echo "<br>". CLMContent::clmWarning(JText::_('NOT_EXIST').'<br>'.JText::_('GEDULDA'))."<br>";
} elseif (!$liga or $liga[0]->published == "0") {
    echo '<br>'.CLMContent::clmWarning(JText::_('NOT_PUBLISHED').'<br>'.JText::_('GEDULD'));
} else { ?>
	<div>
<?php
$remis		= $this->remis;
    $kampflos	= $this->kampflos;
    $heim		= $this->heim;
    $gast		= $this->gast;
    $gesamt		= $this->gesamt;
    $mannschaft	= $this->mannschaft;
    $brett		= $this->brett;
    $gbrett		= $this->gbrett;
    $rbrett		= $this->rbrett;
    $kbrett		= $this->kbrett;
    $bestenliste = $this->bestenliste;
    $kgmannschaft	= $this->kgmannschaft;
    $kvmannschaft	= $this->kvmannschaft;

    $sid 		= clm_core::$load->request_int('saison', 1);
    $lid 		= clm_core::$load->request_int('liga');

    // Konfigurationsparameter auslesen
    $config		= clm_core::$db->config();

    ?>
<br>
<h4><?php echo JText::_('LEAGUE_STAT_ALL') ?></h4>

<?php if (!$bestenliste or !$liga or ($gesamt[0]->gesamt == 0)) {
    echo CLMContent::clmWarning(JText::_('LEAGUE_NO_GAMES'));
} else { ?>
<table cellpadding="0" cellspacing="0" class="statistik">
	<tr>
		<th><?php echo JText::_('LEAGUE_STAT_BRETT') ?></th>
		<th colspan="1"><?php echo JText::_('LEAGUE_STAT_PLAYERGAMES') ?></th>
		<th colspan="4"><?php echo JText::_('LEAGUE_STAT_POINTS') ?></th>
		<th colspan="2"><?php echo JText::_('LEAGUE_STAT_WHITE') ?></th>
		<th colspan="2"><?php echo JText::_('LEAGUE_STAT_BLACK') ?></th>
		<th colspan="2"><?php echo JText::_('LEAGUE_STAT_REMIS') ?></th>
		<th colspan="2"><?php echo JText::_('LEAGUE_STAT_UNCONTESTED') ?></th>
	</tr>

	<tr class="anfang">
		<td></td>
		<td class="punkte clmborder"><?php echo JText::_('LEAGUE_STAT_SUM') ?></td>
		<td class="punkte"><?php echo JText::_('LEAGUE_STAT_HOME') ?></td>
		<td class="punkte"><?php echo JText::_('LEAGUE_STAT_PERCENT') ?></td>
		<td class="punkte"><?php echo JText::_('LEAGUE_STAT_GUEST') ?></td>
		<td class="punkte"><?php echo JText::_('LEAGUE_STAT_PERCENT') ?></td>
		<td class="white clmborder"><?php echo JText::_('LEAGUE_STAT_QUANTITY') ?></td>
		<td class="white"><?php echo JText::_('LEAGUE_STAT_PERCENT') ?></td>
		<td class="black clmborder"><?php echo JText::_('LEAGUE_STAT_QUANTITY') ?></td>
		<td class="black"><?php echo JText::_('LEAGUE_STAT_PERCENT') ?></td>
		<td class="remis clmborder"><?php echo JText::_('LEAGUE_STAT_QUANTITY') ?></td>
		<td class="remis"><?php echo JText::_('LEAGUE_STAT_PERCENT') ?></td>
		<td class="kampflos clmborder"><?php echo JText::_('LEAGUE_STAT_QUANTITY') ?></td>
		<td class="kampflos"><?php echo JText::_('LEAGUE_STAT_PERCENT') ?></td>
	</tr>

<?php

$w = 0;
    $s = 0;
    $r = 0;
    $k = 0;

    $bretter	= CLMModelStatistik::Bretter();
    $brett_all	= CLMModelStatistik::CLMBrett_all($bretter);
    $sum_weiss	= 0;
    $sum_schwarz = 0;

    for ($x = 0; $x < $bretter; $x++) {
        if ($x % 2 == 0) {
            $zeilenr = 'zeile1';
        } else {
            $zeilenr = 'zeile2';
        } ?>
	<tr class="<?php echo $zeilenr; ?>">
		<td align="center"><?php echo $x + 1; ?></td>
		<td class="punkte clmborder"><?php echo $brett[$x]->count; ?></td>
		<td class="punkte"><?php echo str_replace('.0', '', $brett[$x]->sum); ?></td>
		<td class="punkte"><?php echo round(100 * ($brett[$x]->sum - $brett[$x]->count * $ligapunkte->antritt) / ($brett[$x]->count * $ligapunkte->sieg), 1); ?></td>

		<td class="punkte"><?php echo str_replace('.0', '', $gbrett[$x]->sum); ?></td>
		<td class="punkte"><?php echo round(100 * ($gbrett[$x]->sum - $gbrett[$x]->count * $ligapunkte->antritt) / ($brett[$x]->count * $ligapunkte->sieg), 1); ?></td>

		<td class="white clmborder"><?php echo $brett_all[$x]['w'];
        $sum_weiss += $brett_all[$x]['w']; ?></td>
		<td class="white"><?php echo round((($brett_all[$x]['w'] * 100) / $brett[$x]->count), 1); ?></td>
		<td class="black clmborder"><?php echo $brett_all[$x]['s'];
        $sum_schwarz += $brett_all[$x]['s']; ?></td>
		<td class="black"><?php echo round((($brett_all[$x]['s'] * 100) / $brett[$x]->count), 1); ?></td>

<?php if (isset($rbrett[$r]->brett) and $rbrett[$r]->brett == $x + 1) {  ?>
		<td class="remis clmborder"><?php echo $rbrett[$r]->sum; ?></td>
		<td class="remis"><?php echo round((($rbrett[$r]->sum * 100) / $brett[$x]->count), 1); ?></td>
<?php $r++;
} else { ?>
		<td class="remis clmborder">0</td>
		<td class="remis">0</td>
<?php } ?>
<?php if (isset($kbrett[$k]->brett) and $kbrett[$k]->brett == $x + 1) {  ?>
		<td class="kampflos clmborder"><?php echo $kbrett[$k]->sum; ?></td>
		<td class="kampflos"><?php echo round((($kbrett[$k]->sum * 100) / $brett[$x]->count), 1); ?></td>
<?php $k++;
} else { ?>
		<td class="kampflos clmborder">0</td>
		<td class="kampflos">0</td>
<?php } ?>

	</tr>
<?php } ?>
	<tr class="ende">
		<td align="center">&sum;</td>
		<td class="punkte clmborder"><?php echo $gesamt[0]->gesamt; ?></td>
		
		<td class="punkte"><?php echo str_replace('.0', '', $heim[0]->sum); ?></td>
<!--		<td class="punkte"><?php echo round((($heim[0]->sum * 100) / $gesamt[0]->gesamt), 1); ?></td>-->
		<td class="punkte"><?php echo round(100 * ($heim[0]->sum - $gesamt[0]->gesamt * $ligapunkte->antritt) / ($gesamt[0]->gesamt * $ligapunkte->sieg), 1); ?></td>		
		<td class="punkte"><?php echo str_replace('.0', '', $gast[0]->sum); ?></td>
		
<!--		<td class="punkte"><?php echo round((($gast[0]->sum * 100) / $gesamt[0]->gesamt), 1); ?></td>-->
		<td class="punkte"><?php echo round(100 * ($gast[0]->sum - $gesamt[0]->gesamt * $ligapunkte->antritt) / ($gesamt[0]->gesamt * $ligapunkte->sieg), 0); ?></td>
		
		<td class="white clmborder"><?php echo $sum_weiss; ?></td>
		<td class="white"><?php echo round((($sum_weiss * 100) / $gesamt[0]->gesamt), 1); ?></td>
		<td class="black clmborder"><?php echo $sum_schwarz; ?></td>
		<td class="black"><?php echo round((($sum_schwarz * 100) / $gesamt[0]->gesamt), 1); ?></td>
		<td class="remis clmborder"><?php echo $remis[0]->remis; ?></td>
		<td class="remis"><?php echo round((($remis[0]->remis * 100) / $gesamt[0]->gesamt), 1); ?></td>
		<td class="kampflos clmborder"><?php echo $kampflos[0]->kampflos; ?></td>
		<td class="kampflos"><?php echo round((($kampflos[0]->kampflos * 100) / $gesamt[0]->gesamt), 1); ?></td>
	</tr>

<!-- Google Charts-->
<?php if ($googlecharts == "1") { ?>
<!-- bisherige google charts ausblenden
    <tr>
    	<td colspan="14">
        <br />
<img src="http://chart.apis.google.com/chart
?chxt=y
&chbh=a,9,12
&chs=300x225
&cht=bvs
&chco=BF7300,DF8600,F49300,FF9900,FFA928,FFB444,FFC164,FFD088
&chd=t:<?php
$w = 0;
    $s = 0;
    $r = 0;
    $k = 0;
    $bretter	= CLMModelStatistik::Bretter();
    $brett_all	= CLMModelStatistik::CLMBrett_all($bretter);
    $sum_weiss	= 0;
    $sum_schwarz = 0;

    for ($x = 0; $x < $bretter; $x++) {
        echo $brett_all[$x]['w'];
        $sum_weiss += $brett_all[$x]['w'];
        echo ",";
        echo $brett_all[$x]['s'];
        $sum_schwarz += $brett_all[$x]['s'];
        echo ",";
        if (isset($rbrett[$r]->brett) and $rbrett[$r]->brett == $x + 1) {
            echo $rbrett[$r]->sum . ",";
            $r++;
        } else {
            echo "0,";
        }
        if (isset($kbrett[$k]->brett) and $kbrett[$k]->brett == $x + 1) {
            echo $kbrett[$k]->sum;
            $k++;
        } else {
            echo "0";
        }
        if ($x < $bretter - 1) {
            echo "|";
        }
    }  ?>&chdl=<?php
    $bretter	= CLMModelStatistik::Bretter();
    $brett_all	= CLMModelStatistik::CLMBrett_all($bretter);
    for ($x = 0; $x < $bretter; $x++) {
        echo $x + 1;
        if ($x < $bretter - 1) {
            echo "|";
        }
    }  ?>
&chxt=x,y
&chxl=0:|<?php echo JText::_('LEAGUE_STAT_WHITE') ?>|<?php echo JText::_('LEAGUE_STAT_BLACK') ?>|<?php echo JText::_('LEAGUE_STAT_REMIS') ?>|<?php echo JText::_('LEAGUE_STAT_UNCONTESTED') ?>"  alt="Horizontal bar chart" />
        
<img src="http://chart.apis.google.com/chart
?chs=300x225
&cht=p
&chd=t:<?php echo $sum_weiss / 1; ?>,<?php echo $sum_schwarz / 1; ?>,<?php echo $remis[0]->remis; ?>,<?php echo $kampflos[0]->kampflos; ?>
&chdl=<?php echo JText::_('LEAGUE_STAT_WHITE') ?>|<?php echo JText::_('LEAGUE_STAT_BLACK') ?>|<?php echo JText::_('LEAGUE_STAT_REMIS') ?>|<?php echo JText::_('LEAGUE_STAT_UNCONTESTED') ?>
&chdlp=b" width="300" height="225" alt="" />
-->
	
<?php
    //------------- neuer Ansatz
    $brett_all2 = array();
    foreach ($rbrett as $rbrett1) {
        $brett_all2[($rbrett1->brett - 1)]['r'] = $rbrett1->sum;
    }
    //echo "<br><br>rstring ".$wstring; //die();
    foreach ($kbrett as $kbrett1) {
        $brett_all2[($kbrett1->brett - 1)]['k'] = $kbrett1->sum;
    }
    //echo "<br><br>brett_all2 "; var_dump($brett_all2); //die();

    for ($x = 0; $x < $bretter; $x++) {
        //echo "<br><br>$x"; echo ' w '.$brett_all[$x]['w']; echo ' s '.$brett_all[$x]['s']; var_dump($rbrett[$r]->sum);
        if ($x == 0) {
            $wstring = '';
            $wsum = 0;
            $sstring = '';
            $ssum = 0;
            $rstring = '';
            $rsum = 0;
            $kstring = '';
            $ksum = 0;
            $brettstr = '';
            $textstr = '';
        }
        $wstring .= ' '.$brett_all[$x]['w'].',';
        $sstring .= ' '.$brett_all[$x]['s'].',';
        if (isset($brett_all2[$x]['r'])) {
            $rstring .= ' '.$brett_all2[$x]['r'].',';
        } else {
            $rstring .= ' 0,';
        }
        if (isset($brett_all2[$x]['k'])) {
            $kstring .= ' '.$brett_all2[$x]['k'].',';
        } else {
            $kstring .= ' 0,';
        }
        $brettstr .= $x.',';
        $textstr .= "'Brett ".($x + 1)."', ";
    }
    ?>
    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">

      // Load the Visualization API and the corechart package.
      google.charts.load('current', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.charts.setOnLoadCallback(drawPieChart);
	  google.charts.setOnLoadCallback(drawBarChart);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawPieChart() {

        // Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Topping');
        data.addColumn('number', 'Slices');
        data.addRows([
          ['<?php echo JText::_('LEAGUE_STAT_WHITE') ?>', <?php echo $sum_weiss / 1; ?>],
          ['<?php echo JText::_('LEAGUE_STAT_BLACK') ?>', <?php echo $sum_schwarz / 1; ?>],
          ['<?php echo JText::_('LEAGUE_STAT_REMIS') ?>', <?php echo $remis[0]->remis; ?>],
          ['<?php echo JText::_('LEAGUE_STAT_UNCONTESTED') ?>', <?php echo $kampflos[0]->kampflos; ?>],
        ]);

        // Set chart options
        var options = {'title':'prozentuale Verteilung',
                       'width':350,
                       'height':350};

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }

   function drawBarChart() {
    var data = new google.visualization.arrayToDataTable([
//        ['Ergebnis', 'Brett 1', 'Brett 2', 'Brett 3', 'Brett 4',
//         'Brett 5', 'Brett 6', 'Brett 7', 'Brett 8', { role: 'annotation' } ],
        ['Ergebnis', <?php echo $textstr; ?>{ role: 'annotation' } ],
        ['<?php echo JText::_('LEAGUE_STAT_WHITE'); ?>',<?php echo $wstring; ?> ''],
        ['<?php echo JText::_('LEAGUE_STAT_BLACK') ?>',<?php echo $sstring; ?> ''],
        ['<?php echo JText::_('LEAGUE_STAT_REMIS') ?>',<?php echo $rstring; ?> ''],
        ['<?php echo JText::_('LEAGUE_STAT_UNCONTESTED') ?>',<?php echo $kstring; ?> '']
     ]);


      var view = new google.visualization.DataView(data);
//      view.setColumns([0, 1, 2, 3, 4, 5, 6, 7,
      view.setColumns([<?php echo $brettstr; ?>
                       { calc: "stringify",
//                         sourceColumn: 1,
                         type: "string",
                         role: "annotation" },
//                       8]);
                       <?php echo $bretter; ?>]);

      var options = {
        title: "in absoluten Zahlen",
        width: 350,
        height: 350,
        bar: {groupWidth: "60%"},
        isStacked: true,
		legend: { position: 'top', maxLines: 2 },
		chartArea:{
			left: 5,
			top: 50,
			width: '100%',
			height: '250',
		}
      };
      var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values"));
      chart.draw(view, options);
  }
  </script>
	<table><tr>
    <!--Div that will hold the pie chart-->
    <td><div id="chart_div"></div></td>
	<td><div id="columnchart_values"></div></td>
<!--	<td><div id="columnchart_values" style="width: 600px; height: 450px;"></div></td> -->
	</tr></table>
<?php
    //------------- Ende neuer Ansatz
?>
        </td>
    </tr>
<?php } ?>
<!-- Google Charts ENDE-->
    
</table>
<?php }
    $count = count($bestenliste);
    ?>
<br><br><br><br>

<a title="<?php echo JText::_('LEAGUE_STAT_PLAYERLIST') ?>" href="index.php?option=com_clm&amp;view=statistik&amp;saison=<?php echo $sid; ?>&amp;liga=<?php echo $lid; ?>&amp;layout=bestenliste<?php if ($itemid <> '') {
    echo "&Itemid=".$itemid;
} ?>"><h4><?php echo JText::_('LEAGUE_RATING_BEST_PLAYER_I') ?> <?php if ($count < 10 and $count > 0) {
    echo $count;
} else { ?> 10<?php } ?> <?php echo JText::_('LEAGUE_RATING_BEST_PLAYER_II') ?> <?php echo $liga[0]->name; ?></h4></a>
<?php if (!$bestenliste) {
    echo CLMContent::clmWarning(JText::_('LEAGUE_NO_GAMES'));
} else { ?>
<table cellpadding="0" cellspacing="0" class="statistik">
	<tr>
		<th><?php echo JText::_('DWZ_NR') ?></th>
		<th><?php echo JText::_('DWZ_NAME') ?></th>
		<th><?php echo JText::_('LEAGUE_STAT_DWZ') ?></th>
		<th><?php echo JText::_('LEAGUE_STAT_CLUB') ?></th>
		<?php
        $ex = 0;
    $ey = 0;
    for ($xx = 1; $xx < 7; $xx++) {   //max. 6 Spalten
        $str_btiebr = 'btiebr'.$xx;
        if (!isset($params[$str_btiebr])) {
            continue;
        }
        if ($params[$str_btiebr] == 1) {
            $hstring = JText::_('LEAGUE_STAT_PLAYERPOINTS');
        } elseif ($params[$str_btiebr] == 2) {
            $hstring = JText::_('LEAGUE_STAT_PLAYERGAMES');
        } elseif ($params[$str_btiebr] == 3) {
            $hstring = JText::_('LEAGUE_STAT_PLAYERLEVEL');
        } elseif ($params[$str_btiebr] == 4) {
            $hstring = JText::_('LEAGUE_STAT_RATING');
        } elseif ($params[$str_btiebr] == 5) {
            $hstring = JText::_('LEAGUE_STAT_PERCENT');
        } elseif ($params[$str_btiebr] == 6) {
            $hstring = JText::_('LEAGUE_STAT_POINTS_K');
        } elseif ($params[$str_btiebr] == 7) {
            $hstring = JText::_('LEAGUE_STAT_GAMES_K');
        } elseif ($params[$str_btiebr] == 8) {
            $hstring = JText::_('LEAGUE_STAT_PERCENT_K');
        } elseif ($params[$str_btiebr] == 9) {
            $hstring = JText::_('LEAGUE_STAT_BNUMBERS');
        }
        if ($params[$str_btiebr] > 0) { ?>
		<th><?php echo $hstring ?></th>
			<?php }
        } ?>
	</tr>

<?php
if ($count < 10) {
    $a = $count;
} else {
    $a = 10;
}
    for ($x = 0; $x < $a; $x++) {
        if ($x % 2 == 0) {
            $zeilenr = 'zeile1';
        } else {
            $zeilenr = 'zeile2';
        } ?>
	<tr class="<?php echo $zeilenr; ?>">
		<td><?php echo $x + 1; ?></td>
		<td><a href="index.php?option=com_clm&view=spieler&saison=<?php echo $sid; ?>&zps=<?php echo $bestenliste[$x]->zps; ?>&mglnr=<?php echo $bestenliste[$x]->mgl_nr; ?>&PKZ=<?php echo $bestenliste[$x]->PKZ; ?><?php if ($itemid <> '') {
		    echo "&Itemid=".$itemid;
		} ?>"><?php echo $bestenliste[$x]->Spielername; ?></a></td>
		<td><?php echo $bestenliste[$x]->DWZ; ?></td>
		<td><a href="index.php?option=com_clm&view=verein&saison=<?php echo $sid; ?>&zps=<?php echo $bestenliste[$x]->zps; ?><?php if ($itemid <> '') {
		    echo "&Itemid=".$itemid;
		} ?>"><?php echo $bestenliste[$x]->Vereinname; ?></a></td>
		<?php for ($xx = 1; $xx < 7; $xx++) {   //max. 6 Spalten
		    $str_btiebr = 'btiebr'.$xx;
		    if (!isset($params[$str_btiebr])) {
		        continue;
		    }
		    //if ($params[$str_btiebr] == 1) $hstring = $bestenliste[$x]->gpunkte;
		    if ($params[$str_btiebr] == 1) {
		        $hstring = $bestenliste[$x]->Punkte;
		    }
		    //elseif ($params[$str_btiebr] == 2) $hstring = $bestenliste[$x]->gpartien;
		    elseif ($params[$str_btiebr] == 2) {
		        $hstring = $bestenliste[$x]->Partien;
		    } elseif ($params[$str_btiebr] == 3) {
		        $hstring = $bestenliste[$x]->Niveau;
		    } elseif ($params[$str_btiebr] == 4) {
		        if (($bestenliste[$x]->Punkte == $bestenliste[$x]->Partien) and ($bestenliste[$x]->Leistung > 0)) {
		            $hstring = (($bestenliste[$x]->Niveau) + 667).' &sup2;';
		            $ex = 1;
		        } else {
		            $hstring = $bestenliste[$x]->Leistung;
		        }
		    }
		    //elseif ($params[$str_btiebr] == 5) $hstring = round($bestenliste[$x]->gprozent,1);
		    elseif ($params[$str_btiebr] == 5) {
		        $hstring = round($bestenliste[$x]->Prozent, 1);
		    } elseif ($params[$str_btiebr] == 6) {
		        $hstring = $bestenliste[$x]->epunkte;
		        $ey = 1;
		    } elseif ($params[$str_btiebr] == 7) {
		        $hstring = $bestenliste[$x]->epartien;
		        $ey = 1;
		    } elseif ($params[$str_btiebr] == 8) {
		        $hstring = round($bestenliste[$x]->eprozent, 1);
		        $ey = 1;
		    } elseif ($params[$str_btiebr] == 9) {
		        $hstring = $bestenliste[$x]->ebrett;
		    }
		    if ($params[$str_btiebr] > 0) { ?>
		<td align="center"><?php echo $hstring ?></td>
			<?php }
		    } ?>
	</tr>
<?php } ?>
</table>
<div class="hint"><?php echo JText::_('LEAGUE_RATING_COMMENT') ?></div>
<?php if ($ex > 0) { ?><div class="hint"><?php echo JText::_('LEAGUE_RATING_IMPOSSIBLE'); ?></div><?php } ?>
<?php if ($ey > 0) { ?><div class="hint"><?php echo JText::_('LEAGUE_WITH_UNCONTESTED'); ?></div><?php } ?>
<?php
if ($count > 9) {
    $punkte = CLMModelStatistik::checkSpieler($bestenliste[9]->Punkte);
    if ($punkte == 11) {
        ?>
<br>
<div class="hint">** <?php echo JText::_('LEAGUE_RATING_ONE_MORE') ?> <?php echo $bestenliste[9]->Punkte; ?></div>
<?php } if ($punkte > 11) { ?>
<div class="hint">** <?php echo JText::_('LEAGUE_RATING_MORE_I') ?> <?php echo $punkte - 10; ?> <?php echo JText::_('LEAGUE_RATING_MORE_II') ?> <?php echo $bestenliste[9]->Punkte; ?></div><?php }
}
} ?>
<br>

<?php $ex = 0;
    $ey = 0;
    if (!$bestenliste or !$liga or ($gesamt[0]->gesamt == 0)) {
        echo CLMContent::clmWarning(JText::_('LEAGUE_NO_GAMES'));
    } else { ?>
		<h4><?php echo JText::_('LEAGUE_STAT_BEST'); ?></h4>
<?php for ($x = 0; $x < $liga[0]->stamm + 1; $x++) {
    if ($x < $liga[0]->stamm) {
        $xtext = $x + 1;
    } else {
        $xtext = JText::_('LEAGUE_STAT_ERSATZ');
    } ?>
		<h4><?php echo JText::_('LEAGUE_STAT_BRETT')." ".$xtext ?></h4>
<table cellpadding="0" cellspacing="0" class="statistik">
	<tr>
		<th><?php echo JText::_('LEAGUE_STAT_BRETT') ?></th>
		<th><?php echo JText::_('DWZ_NAME') ?></th>
		<th><?php echo JText::_('LEAGUE_STAT_DWZ') ?></th>
		<th><?php echo JText::_('LEAGUE_STAT_CLUB') ?></th>
		<?php for ($xx = 1; $xx < 7; $xx++) {   //max. 6 Spalten
		    $str_btiebr = 'btiebr'.$xx;
		    if (!isset($params[$str_btiebr])) {
		        continue;
		    }
		    if ($params[$str_btiebr] == 1) {
		        $hstring = JText::_('DWZ_POINTS');
		    } elseif ($params[$str_btiebr] == 2) {
		        $hstring = JText::_('DWZ_GAMES');
		    } elseif ($params[$str_btiebr] == 3) {
		        $hstring = JText::_('DWZ_LEVEL');
		    } elseif ($params[$str_btiebr] == 4) {
		        $hstring = JText::_('LEAGUE_STAT_RATING');
		    } elseif ($params[$str_btiebr] == 5) {
		        $hstring = JText::_('LEAGUE_STAT_PERCENT');
		    } elseif ($params[$str_btiebr] == 6) {
		        $hstring = JText::_('LEAGUE_STAT_POINTS_K');
		    } elseif ($params[$str_btiebr] == 7) {
		        $hstring = JText::_('LEAGUE_STAT_GAMES_K');
		    } elseif ($params[$str_btiebr] == 8) {
		        $hstring = JText::_('LEAGUE_STAT_PERCENT_K');
		    } elseif ($params[$str_btiebr] == 9) {
		        $hstring = JText::_('LEAGUE_STAT_BNUMBERS');
		    }
		    if ($params[$str_btiebr] > 0) { ?>
		<th><?php echo $hstring ?></th>
			<?php }
		    } ?>
	</tr>
<?php $xb = 1;
    foreach ($bestenliste as $spielerbrett) {
        if ($xb > $params['bnhtml']) {
            break;
        }
        if ($xb % 2 == 0) {
            $zeilenr = 'zeile1';
        } else {
            $zeilenr = 'zeile2';
        }
        if (($spielerbrett->snr == ($x + 1) and $x < $liga[0]->stamm) or
            ($spielerbrett->snr > $liga[0]->stamm and $x >= $liga[0]->stamm)) {
            $xb++; ?>
	<tr class="<?php echo $zeilenr; ?>">
		<td><?php echo $spielerbrett->snr; ?></td>
		<td><a href="index.php?option=com_clm&view=spieler&saison=<?php echo $sid; ?>&zps=<?php echo $spielerbrett->zps; ?>&mglnr=<?php echo $spielerbrett->mgl_nr; ?>&PKZ=<?php echo $spielerbrett->PKZ; ?><?php if ($itemid <> '') {
		    echo "&Itemid=".$itemid;
		} ?>"><?php echo $spielerbrett->Spielername; ?></a></td>
		<td><?php echo $spielerbrett->DWZ; ?></td>
		<td><a href="index.php?option=com_clm&view=verein&saison=<?php echo $sid; ?>&zps=<?php echo $spielerbrett->zps; ?><?php if ($itemid <> '') {
		    echo "&Itemid=".$itemid;
		} ?>"><?php echo $spielerbrett->Vereinname; ?></a></td>
		<?php for ($xx = 1; $xx < 7; $xx++) {   //max. 6 Spalten
		    $str_btiebr = 'btiebr'.$xx;
		    if (!isset($params[$str_btiebr])) {
		        continue;
		    }
		    //if ($params[$str_btiebr] == 1) $hstring = $spielerbrett->gpunkte;
		    if ($params[$str_btiebr] == 1) {
		        $hstring = $spielerbrett->Punkte;
		    }
		    //elseif ($params[$str_btiebr] == 2) $hstring = $spielerbrett->gpartien;
		    elseif ($params[$str_btiebr] == 2) {
		        $hstring = $spielerbrett->Partien;
		    } elseif ($params[$str_btiebr] == 3) {
		        $hstring = $spielerbrett->Niveau;
		    } elseif ($params[$str_btiebr] == 4) {
		        if (($spielerbrett->Punkte == $spielerbrett->Partien) and ($spielerbrett->Leistung > 0)) {
		            $hstring = (($spielerbrett->Niveau) + 667).' &sup2;';
		            $ex = 1;
		        } else {
		            $hstring = $spielerbrett->Leistung;
		        }
		    }
		    //elseif ($params[$str_btiebr] == 5) $hstring = round($spielerbrett->gprozent,1);
		    elseif ($params[$str_btiebr] == 5) {
		        $hstring = round($spielerbrett->Prozent, 1);
		    } elseif ($params[$str_btiebr] == 6) {
		        $hstring = $spielerbrett->epunkte;
		    } elseif ($params[$str_btiebr] == 7) {
		        $hstring = $spielerbrett->epartien;
		    } elseif ($params[$str_btiebr] == 8) {
		        $hstring = round($spielerbrett->eprozent, 1);
		    } elseif ($params[$str_btiebr] == 9) {
		        $hstring = $spielerbrett->ebrett;
		    }
		    if ($params[$str_btiebr] > 0) { ?>
		<td align="center"><?php echo $hstring ?></td>
			<?php }
		    } ?>
	</tr>
<?php        }
    } ?>
</table>
<?php }
} ?>
<div class="hint"><?php echo JText::_('LEAGUE_RATING_COMMENT') ?></div>
<?php if ($ex > 0) { ?><div class="hint"><?php echo JText::_('LEAGUE_RATING_IMPOSSIBLE'); ?></div><?php } ?>
<?php if ($ey > 0) { ?><div class="hint"><?php echo JText::_('LEAGUE_WITH_UNCONTESTED'); ?></div><?php } ?>
<br>
<h4><?php echo JText::_('LEAGUE_STAT_UNCONTESTED_LIST') ?></h4>
<?php if (!$mannschaft) {
    echo CLMContent::clmWarning(JText::_('LEAGUE_NO_GAMES'));
} else {
    $stat_kampflos = array();
    foreach ($mannschaft as $mannschaft1) {
        $stat_kampflos[$mannschaft1->tln_nr] = new stdClass();
        $stat_kampflos[$mannschaft1->tln_nr]->name = $mannschaft1->name;
        $stat_kampflos[$mannschaft1->tln_nr]->tln_nr = $mannschaft1->tln_nr;
        $stat_kampflos[$mannschaft1->tln_nr]->kg_sum = 0;
        $stat_kampflos[$mannschaft1->tln_nr]->kv_sum = 0;
    }
    foreach ($kgmannschaft as $kgmannschaft1) {
        $stat_kampflos[$kgmannschaft1->tln_nr]->kg_sum = $kgmannschaft1->kg_sum;
    }
    foreach ($kvmannschaft as $kvmannschaft1) {
        if (isset($stat_kampflos[$kvmannschaft1->tln_nr])) {
            $stat_kampflos[$kvmannschaft1->tln_nr]->kv_sum = $kvmannschaft1->kv_sum;
        }
    }
    ?>
<table cellpadding="0" cellspacing="0" class="statistik">
	<tr>
		<th><?php echo JText::_('DWZ_NR') ?></th>
		<th><?php echo JText::_('DWZ_NAME') ?></th>
		<th><?php echo JText::_('LEAGUE_STAT_UNCONTESTED_GEWO') ?></th>
		<th><?php echo JText::_('LEAGUE_STAT_UNCONTESTED_VERL') ?></th>
	</tr>
<?php for ($x = 0; $x < count($stat_kampflos); $x++) {
    if ($x == 0) {
        $xx = 0;
    }
    if (!isset($stat_kampflos[$x + 1])) {
        continue;
    }
    if ($stat_kampflos[$x + 1]->name == 'spielfrei') {
        continue;
    }
    $xx++;
    if ($xx % 2 == 0) {
        $zeilenr = 'zeile1';
    } else {
        $zeilenr = 'zeile2';
    } ?>
	<tr class="<?php echo $zeilenr; ?>">
		<td><?php echo $xx; ?></td>
		<td><a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $sid; ?>&liga=<?php echo $lid; ?>&tlnr=<?php echo $stat_kampflos[$x + 1]->tln_nr; ?><?php if ($itemid <> '') {
		    echo "&Itemid=".$itemid;
		} ?>"><?php echo $stat_kampflos[$x + 1]->name; ?></a></td>
		<td><?php echo $stat_kampflos[$x + 1]->kg_sum; ?></td>
		<td><?php echo $stat_kampflos[$x + 1]->kv_sum; ?></td>
	</tr>
<?php } ?>
</table>
<?php } ?>
<br>
<h4><?php echo JText::_('LEAGUE_RATING_BEST_TEAM_I') ?> <?php $counter = ceil((count($mannschaft)) / 2);
    if ($counter < 2 and count($mannschaft) > 1) {
        $counter++;
    };
    echo $counter; ?> <?php echo JText::_('LEAGUE_RATING_BEST_TEAM_II') ?> <?php echo $liga[0]->name; ?></h4>
<?php if (!$mannschaft) {
    echo CLMContent::clmWarning(JText::_('LEAGUE_NO_GAMES'));
} else { ?>
<table cellpadding="0" cellspacing="0" class="statistik">
	<tr>
		<th><?php echo JText::_('DWZ_NR') ?></th>
		<th><?php echo JText::_('DWZ_NAME') ?></th>
		<th><?php echo JText::_('LEAGUE_STAT_LEAGUE') ?></th>
		<th><?php echo JText::_('LEAGUE_STAT_TEAM_POINTS') ?></th>
		<th><?php echo JText::_('LEAGUE_STAT_TEAM_POINTS_PERCENT') ?></th>
		<th><?php echo JText::_('LEAGUE_STAT_BOARD_POINTS') ?></th>
		<th><?php echo JText::_('LEAGUE_STAT_BOARD_POINTS_PERCENT') ?></th>
	</tr>

<?php for ($x = 0; $x < $counter; $x++) {
    if ($x % 2 == 0) {
        $zeilenr = 'zeile1';
    } else {
        $zeilenr = 'zeile2';
    } ?>
	<tr class="<?php echo $zeilenr; ?>">
		<td><?php echo $x + 1; ?></td>
		<td><a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $sid; ?>&liga=<?php echo $lid; ?>&tlnr=<?php echo $mannschaft[$x]->tln_nr; ?><?php if ($itemid <> '') {
		    echo "&Itemid=".$itemid;
		} ?>"><?php echo $mannschaft[$x]->name; ?></a></td>
		<td><a href="index.php?option=com_clm&view=rangliste&saison=<?php echo $sid; ?>&liga=<?php echo $lid; ?><?php if ($itemid <> '') {
		    echo "&Itemid=".$itemid;
		} ?>"><?php echo $mannschaft[$x]->liga; ?></a></td>
		<td><?php echo $mannschaft[$x]->mp; ?></td>
		
<!--		<td><?php echo round(($mannschaft[$x]->mp * 100) / (2 * $mannschaft[$x]->count), 1); ?></td>-->
		<td><?php echo round(100 * ($mannschaft[$x]->mp - $mannschaft[$x]->count * $ligapunkte->man_antritt) / ($mannschaft[$x]->count * $ligapunkte->man_sieg), 1); ?></td>
		
		<td><?php echo $mannschaft[$x]->bp; ?></td>
		
<!--		<td><?php echo round(($mannschaft[$x]->bp * 100) / ($mannschaft[$x]->stamm * $mannschaft[$x]->count), 1); ?></td>-->
		<td><?php echo round(100 * ($mannschaft[$x]->bp - $mannschaft[$x]->stamm * $mannschaft[$x]->count * $ligapunkte->antritt) / ($mannschaft[$x]->stamm * $mannschaft[$x]->count * $ligapunkte->sieg), 1); ?></td>
	</tr>
<?php } ?>
</table>
<?php }
$count = 0;
    for ($x = 5; $x < (2 * count($mannschaft)); $x++) {
        if (isset($mannschaft[$x]->mp) and $mannschaft[$x]->mp == $mannschaft[4]->mp) {
            $count++;
        } else {
            break;
        }
    }
    if ($count == 1 and $mannschaft) { ?>
<div class="hint">* <?php echo JText::_('LEAGUE_RATING_MORE_TEAM_I') ?> <?php echo $mannschaft[4]->mp; ?> <?php echo JText::_('LEAGUE_RATING_MORE_TEAM_II') ?></div><?php }
    if ($count > 1 and $mannschaft) { ?>
<div class="hint">* <?php echo JText::_('LEAGUE_RATING_MORE_I') ?> <?php echo $count; ?> <?php echo JText::_('LEAGUE_RATING_MORE_TEAMS') ?> <?php echo $mannschaft[4]->mp; ?> <?php echo JText::_('LEAGUE_RATING_MORE_TEAM_II') ?></div>
<?php } ?>

</div>
<?php } ?>

<br>

<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>


<div class="clr"></div>
</div>
</div>
 
