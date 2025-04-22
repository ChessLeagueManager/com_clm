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

$document = JFactory::getDocument();
$cssDir = JURI::base().DS. 'components'.DS.'com_clm'.DS.'includes';
$document->addStyleSheet($cssDir.DS.'clm_content.css', 'text/css', null, array());

// Variablen holen
$sid = clm_core::$load->request_int('saison', '1');
$zps = clm_core::$load->request_string('zps', '1');
$man = clm_core::$load->request_int('man');
$gid	= clm_core::$load->request_int('gid');

// Login Status prüfen
$clmuser 	= $this->clmuser;
$user		= JFactory::getUser();
$mainframe	= JFactory::getApplication();
$link = "index.php?option=com_clm&view=info";
// Konfigurationsparameter auslesen
$config		= clm_core::$db->config();
$conf_meldeliste = $config->conf_meldeliste;

if ($conf_meldeliste != 1) {
    $msg = JText::_('<h2>Die Eingabe von Ranglisten wurde durch den Administrator gesperrt !</h2>');
    $mainframe->enqueueMessage($msg);
    $mainframe->redirect($link);
}
if (!$user->get('id')) {
    $msg = JText::_('<h1>Sie sind nicht angemeldet !</h1> <h2>Loggen Sie sich zuerst ein, bevor Sie eine Rangliste abgeben.</h2>');
    $mainframe->enqueueMessage($msg);
    $mainframe->redirect($link);
}
if ($clmuser[0]->published < 1) {
    $msg = JText::_('<h1>Ihr Account wurde gesperrt !</h1> <h2>Wenden Sie sich umgehend an einen Administrator.</h2>');
    $mainframe->enqueueMessage($msg);
    $mainframe->redirect($link);
}
if ($clmuser[0]->zps <> $zps) {
    $msg = JText::_('<h1>Sie können nicht für einen anderen Verein melden !</h1>');
    $mainframe->enqueueMessage($msg);
    $mainframe->redirect($link);
}
// Prüfen ob Pflege im FE noch zulässig ist
$ligen 		= $this->ligen;
$a_ligen = array();
$today = date("Y-m-d");
foreach ($ligen as $lig01) {
    // Ligaparameter bereitstellen
    $params = new clm_class_params($lig01->params);
    $deadline_roster = $params->get('deadline_roster', '1970-01-01');
    if ($deadline_roster >= $today) {
        $a_ligen[] = $lig01->lid;
    }
}
if (count($a_ligen) < 1) {
    $msg = JText::_('<h2>Sie können diese Rangfolge nicht (mehr) pflegen. Wenden Sie sich an einen Staffelleiter.</h2>');
    $mainframe->enqueueMessage($msg);
    $mainframe->redirect($link);
}
// Prüfen ob Datensatz schon vorhanden ist
$abgabe	= $this->abgabe;

//if (isset($abgabe[0]->id) AND $abgabe[0]->id != "") {
//	$msg = JText::_( '<h1>Diese Rangliste wurde bereits abgegeben ! </h1><h2>Bitte schauen Sie in die entsprechende Mannschaftsübersicht</h2>' );
//	$mainframe->redirect( $link, $msg );
// 			}
// NICHT vorhanden
// Variablen initialisieren
$liga 		= $this->liga;
$spieler	= $this->spieler;
$count		= $this->count;
?>

<script language="javascript" type="text/javascript">

	function submitbutton(pressbutton) {
		var form = document.adminForm;

		if (pressbutton == 'sortieren') { Sortieren() }
		else if (pressbutton == 'pruefen') { Pruefbutton() }
		else if (pressbutton == 'neu_laden') { location.reload() }
	}

function Zcheck(Wert)
 {
  var chkZ = 1;
  for (i = 0; i < Wert.length; ++i)
    if (Wert.charAt(i) < "0" ||
        Wert.charAt(i) > "9")
      chkZ = -1;
  if (chkZ == -1) {
   return false
   }
  else {return true};
 }
function Mcheck(Ob)
 {
  if (!Zcheck(Ob.value)) {
    alert("Mannschaft keine Zahl!");
    Ob.focus();
    return false;
   }
 }
function Rcheck(Ob)
 {
  if (Zcheck(Ob.value)) return true;
  var i=Ob.value.indexOf("/")
  if (i>0)
   {
    var M=Ob.value.slice(0,i);
    var R=Ob.value.slice(i+1);
    if ((R=='')||(!Zcheck(M))||(!Zcheck(R)))
     {
      Ob.focus();
      alert("Rang ist keine Zahl");
      return false;
     }
    else
     {
      Ob.value=M*1000+R*1;
      return true;
     }
   }
  Ob.focus();
  alert("Rang ist keine Zahl");
  return false;
 }
function Spielergroesser(S1,S2)
 {
  if (S1[0]>S2[0]) return true; //0=Mannschaft
  if (S1[0]<S2[0]) return false;
  if (S1[1]>S2[1]) return true; //1=Rang
  if (S1[1]<S2[1]) return false;
  if (S1[4]<S2[4]) return true; //4=DWZ
  if (S1[4]>S2[4]) return false;
  if (S1[5]<S2[5]) return true; //5=DWZ-Index
  if (S1[5]>S2[5]) return false;
  if (S1[2]>S2[2]) return true; //2=Name
  if (S1[2]<S2[2]) return false;
  if (S1[3]>S2[3]) return true; //3=Mgl
  return false;
 }
function Spielerschreiben(i)
{
    if (Spieler[i][0]==999)  document.getElementsByName('MA'+i)[0].value='';
    else document.getElementsByName('MA'+i)[0].value=Spieler[i][0];
    if (Spieler[i][1]==9999) document.getElementsByName('RA'+i)[0].value='';
    else document.getElementsByName('RA'+i)[0].value=Spieler[i][1];
    document.getElementById('SP'+i).innerHTML=Spieler[i][2];
    document.getElementsByName('MGL'+i)[0].value=Spieler[i][3];
    document.getElementById('MGL'+i).innerHTML=Spieler[i][3];
    document.getElementById('DWZ'+i).innerHTML=Spieler[i][4];
    document.getElementById('DWI'+i).innerHTML=Spieler[i][5];
    document.getElementsByName('ZPSM'+i)[0].value=Spieler[i][6];
    document.getElementById('ZPSM'+i).innerHTML=Spieler[i][6];
    <?php if (isset($UsePKZ) and $UsePKZ == 1) { ?>document.getElementById('PKZ'+i).innerHTML=Spieler[i][7];
    document.getElementsByName('PKZ'+i)[0].innerHTML=Spieler[i][7];
    <?php } ?>
    document.getElementById('Status'+i).innerHTML=Spieler[i][8];
    document.getElementById('check'+i).checked=Spieler[i][9];
    if (Spieler[i][9] == 1) 
		document.getElementById('check'+i).style["display"]="none";
    else 
		document.getElementById('check'+i).style["display"]="";
}
function QSort(l,r,Tiefe)
 {
  var i=l;
  var j=r;
  var MittelSpieler=Spieler[Math.floor((l+r)/2)];
  do
   {
    while (Spielergroesser(MittelSpieler,Spieler[i])) i++;
    while (Spielergroesser(Spieler[j],MittelSpieler)) j--;
    if (!(i>j))
     {
      var VS2=Spieler[i];
      Spieler[i]=Spieler[j];
      Spieler[j]=VS2;
      i++;
      j--;
     }
   }
  while (!(i>j));
  if (l<j) QSort(l,j,Tiefe+1);
  if (i<r) QSort(i,r,Tiefe+1); 
 }
function Sortieren()
 {
  Spieler=new Array;
  i=0;
  while (document.getElementsByName('MA'+i)[0]) {
    Spieler[i]=new Array(10)
    Spieler[i][0]=document.getElementsByName('MA'+i)[0].value-0;
    if (Spieler[i][0]==0) Spieler[i][0]=999;
    Spieler[i][1]=document.getElementsByName('RA'+i)[0].value-0;
    if (Spieler[i][1]==0) Spieler[i][1]=9999;
    Spieler[i][2]=document.getElementById('SP'+i).innerHTML;
    Spieler[i][3]=document.getElementById('MGL'+i).innerHTML-0;
    Spieler[i][4]=document.getElementById('DWZ'+i).innerHTML-0;
    Spieler[i][5]=document.getElementById('DWI'+i).innerHTML-0;
    Spieler[i][6]=document.getElementById('ZPSM'+i).innerHTML;
    <?php if (isset($UsePKZ) and $UsePKZ == 1) { ?>Spieler[i][7]=document.getElementById('PKZ'+i).innerHTML-0;<?php } ?>
    Spieler[i][8]=document.getElementById('Status'+i).innerHTML;
    Spieler[i][9]=document.getElementById('check'+i).checked;
    i++;    
   }
  QSort(0,i-1,0)
  i=0;
  while (document.getElementsByName('MA'+i)[0])
   {
    Spielerschreiben(i);
    i++;    
   }
 }
function Pruefen()
 {
  Sortieren();
  var Ma=0;
  var Ra=0;
  var Sp=0;
  var Ersatz=1;
  i=0;
  var TempMa01=document.getElementsByName('MA'+i)[0].value;
  if (TempMa01==0) {
	alert('Keine Daten!');
	return false;
  }
  while(document.getElementsByName('MA'+i)[0]) {
    var TempMa=document.getElementsByName('MA'+i)[0].value;
    var TempRa=document.getElementsByName('RA'+i)[0].value;
	if (TempMa==0) {
		break;
	}		
	//Hilfsprüfung: nur auf doppelte Einträge
    if ((TempMa==Ma) && (TempRa==Ra)) {
        alert('Doppelte Rangnummer! \n Mannschaft: '+Ma+'  Rang: '+Ra);
		return false;
	} else {
        Ma=TempMa;
        Ra=TempRa;
		i++;
		continue;
	}
	return true;
	//Ende Hilfsprüfung
    if (TempMa==Ma) {
      if (TempRa==(Sp+1)) {
        i++;
        Sp++;
        continue; }
      else if (TempRa==(1000*Ma+Ersatz)) {
        i++;
        Ersatz++;
        continue; }
      else {
        if (window.confirm('Die Rangnummer von Spieler '+document.getElementsByName('SP'+i)[0].innerHTML+' ist '+TempRa+'!\nErwartet wird '+(Sp+1)+'!\nFortsetzen?')) {
          Sp=TempRa;
          i++;
          continue; }
        document.getElementsByName('RA'+i)[0].focus();
        return false; } }
    else if (TempMa==(Ma+1)) {
      Ma++;
      Ersatz=1;
      if (TempRa!=(Sp+1)) {
        if (window.confirm('Der erste Spieler ('+document.getElementsByName('SP'+i)[0].innerHTML+') der '+Ma+'.Mannschaft hat Rangnummer '+TempRa+'!\nErwartet wird '+(Sp+1)+'\nFortsetzen?')) {
          Sp=TempRa;
          i++;
          continue; }
        document.getElementsByName('RA'+i)[0].focus();
        return false; }
      else {
        Sp++;
        i++;
        continue; } }
    else if (TempMa==90) {
      Ma=90;
      Sp=90000;
      continue; }
    else if (TempMa==99) {
      Ma=99;
      Sp=99000;
      continue; }
    else {
      if (Ma!=0) var Text='Die Mannschaftsnummern müssen aufsteigend sein!\nLetzte Mannschaftsnummer war '+Ma;
      else var Text='Die erste Mannschaft muß die Nummer 1 haben!';
      if (window.confirm(Text+'\nFortsetzen?')) {
        Sp=TempRa;
        i++;
        continue; }
      document.getElementsByName('MA'+i)[0].focus();
      return false; }
    if (window.confirm('Unbekannter Aufstellungsfehler!\nMannschaft: '+TempMa+'\nRangnummer: '+TempRa+'\nSpielername: "'+document.getElementsByName('SP'+i)[0].innerHTML+'"\nFortsetzen?')) {
      Sp=TempRa;
      i++;
      continue; }
    document.getElementsByName('MA'+i)[0].focus();
    return false; }
  return true;
 } // end: function Pruefen()
function Pruefbutton()
 {
  if (Pruefen()==true) alert('Alles in Ordnung');
 }
function Sendbutton()
 {
  if (Pruefen()==true) document.adminForm.submit();
 }

</script>

<?php
$a_sg_vname = array();
if ($liga[0]->sg_zps > '00000') {
    $a_sg_zps = explode(',', $liga[0]->sg_zps);
    if (is_array($a_sg_zps) and count($a_sg_zps) > 0) {
        for ($i = 0; $i <= count($a_sg_zps); $i++) {
            if (!isset($a_sg_zps[$i])) {
                continue;
            }
            $query = "SELECT * FROM #__clm_dwz_vereine "
                ." WHERE sid = $sid AND ZPS = '".$a_sg_zps[$i]."'";
            $sg_vname = clm_core::$db->loadObject($query);
            if (isset($sg_vname->Vereinname)) {
                $a_sg_vname[] = $sg_vname->Vereinname;
            }
        }
    }
}
?>

<div class="componentheading">Rangliste abgeben : <?php if (isset($liga[0]->vname)) {
    echo $liga[0]->vname;
} else {
    echo $zps;
} ?>
<?php if (count($a_sg_vname) > 0) { ?>
<br>in Spielgemeinschaft mit <?php echo $a_sg_vname[0];
} ?>
<?php if (count($a_sg_vname) > 1) { ?>
, <?php echo $a_sg_vname[1];
} ?>
</div>

<div><h2>Gruppe : <?php if (isset($liga[0]->gruppe)) {
    echo $liga[0]->gruppe;
} else {
    echo $gid;
} ?></h2></div>
<br>
<u><b>Hinweise</u></b> : 
<br><b>(1)</b> Setzen Sie Mannschaftsnummer und Rang bei allen Spielern, die Sie in die Rangliste aufnehmen möchten.
<br><b>(2)</b> Mit dem "Prüfen" Knopf können Sie die Liste auf doppelte Rangnummern kontrollieren.
<br><b>(3)</b> Mit dem "Sortieren" Knopf können Sie die Liste in die aktuelle Reihenfolge bringen.
<br><b>(4)</b> Der "Neu laden" Knopf <u><i>verwirft ALLE Änderungen</i></u> und lädt die Seite neu !
<br><b>(5)</b> Sobald "Liste absenden" gedrückt wurde ist die Rangliste verbindlich gemeldet.
<br><br>
<?php if (is_array($abgabe) and count($abgabe) == 0 and $liga[0]->anz_sgp > 0) { ?>
<u><b>Hinweis zu Spielgemeinschaften</u></b> : 
<br>Die Erstanlage von Rangfolgen für Spielgemeinschaften muss in Admin-Bereich durch Admin oder Spielleiter erfolgen.
<br>Die weitere Pflege, also Erfassen aller Spieler bzw. Korrekturen sind hier im Frontend möglich.
<br><br>
<?php } ?>
<small><u><b>Update-Hinweis</u></b> : 
<br><b>(*)</b> Spieler, die den Verein während der Saison verlassen haben, sollten nicht aus der Rangliste gelöscht werden, sondern 'gesperrt' ist zu setzen.
<br>Damit wird die Zuordnung bereits gespielter Partien ermöglicht und gleichzeitig der aktive Einsatz während der restlichen Saison verhindert.
</small><br><br><br>

<center>

<table class="toolbar"><tr>
 
<td class="button" id="Ranglisten-pruefen" width="15%" style="background-color:#E6E6E6;">
<a href="#" onclick="javascript:Pruefbutton(); return false;" class="toolbar">
<span class="icon-32-trash" title="Prüfen">
</span>
Prüfen
</a>
</td>
<td width="10%">&nbsp;&nbsp;&nbsp;</td>
 
<td class="button" id="Ranglisten-sortieren" width="15%" style="background-color:#E6E6E6;">
<a href="#" onclick="javascript:Sortieren(); return false;" class="toolbar">
<span class="icon-32-pruefen" title="Sortieren">
</span>
Sortieren
</a>
</td>
<td width="10%">&nbsp;&nbsp;&nbsp;</td>

<td class="button" id="Ranglisten-neuladen" width="15%" style="background-color:#E6E6E6;">
<a href="#" onclick="javascript: window.location.href=window.location.href; return false;" class="toolbar">
<span class="icon-32-pruefen" title="Neu laden">
</span>
Neu laden
</a>
</td>
<td width="10%">&nbsp;&nbsp;&nbsp;</td>

<td class="button" id="Ranglisten-speichern" width="18%" style="background-color:#E6E6E6;">
<!-- <a href="#" onclick="javascript:document.adminForm.submit(); return false;" class="toolbar"> -->
<a href="#" onclick="javascript:Sendbutton(); return false;" class="toolbar"> 

<span class="icon-32-pruefen" title="Speichern">
</span>
Liste absenden !
</a>
</td>
<td>&nbsp;&nbsp;&nbsp;</td>

</tr>
</table>

<br><br>

<form action="index.php?option=com_clm&amp;view=meldeliste&amp;layout=sent_rangliste&amp;saison=<?php echo $sid ?>&amp;gid=<?php echo $gid ?>&amp;zps=<?php echo $zps ?>&amp;count=<?php echo count($spieler) ?>" method="post" name="adminForm">

<style type="text/css">table { width:60%; }</style>

<div class="col">
  <fieldset class="adminform">
   <legend><?php echo "Rangliste Platz 1 - ".count($spieler); ?></legend>
	
	<table class="admintable meldeliste_rangliste">

	<tr>
		<td width="6%" class="key" nowrap="nowrap">Mnr</td>
		<td width="7%" class="key" nowrap="nowrap">Rang</td>
		<td class="key" nowrap="nowrap">Name</td>
		<td width="10%" class="key" nowrap="nowrap">Verein</td>
		<td width="9%" class="key" nowrap="nowrap">MglNr</td>
		<td width="14%" class="key" nowrap="nowrap">PKZ</td>
		<td width="4%" class="key" nowrap="nowrap">ST</td>
		<td colspan="2" width="12%" class="key" nowrap="nowrap">DWZ</td>
		<td width="8" class="key" nowrap="nowrap"><?php echo JText::_('gesperrt'); ?></td>
	</tr>

<?php

    for ($x = 0; $x < count($spieler); $x++) { ?>

	<input type="hidden" name="PKZ<?php echo $x; ?>" value="<?php echo $spieler[$x]->PKZ; ?>" />
	<input type="hidden" name="ZPSM<?php echo $x; ?>" value="<?php echo $spieler[$x]->ZPS; ?>" />
	<input type="hidden" name="MGL<?php echo $x; ?>" value="<?php echo $spieler[$x]->Mgl_Nr; ?>" />
	<input type="hidden" name="BLOCK_A<?php echo $x; ?>" value="<?php echo $spieler[$x]->gesperrt; ?>" />

	<tr>
	<td class="key" nowrap="nowrap">
		<input type="text" name="MA<?php echo $x ?>" size="3" maxLength="3" value="<?php if (isset($spieler[$x]->man_nr)) {
		    echo $spieler[$x]->man_nr;
		} ?>" onChange="Mcheck(this)">
	</td>
	<td class="key" nowrap="nowrap">
	<input type="text" name="RA<?php echo $x ?>" size="5" maxLength="5" value="<?php if (isset($spieler[$x]->Rang)) {
	    echo $spieler[$x]->Rang;
	} ?>" onChange="Rcheck(this)">
	</td>
	<td id="SP<?php echo $x; ?>" name="SP<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php if ($spieler[$x]->gesperrt != "1") {
		    echo $spieler[$x]->Spielername;
		} else {
		    echo '<del>'.$spieler[$x]->Spielername.'</del>';
		} ?></td>
	<td id="ZPSM<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->ZPS; ?></td>
	<td id="MGL<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->Mgl_Nr; ?></td>
	<td id="PKZ<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->PKZ; ?></td>
	<td id="Status<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->Status; ?></td>
	<td id="DWZ<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->DWZ; ?></td>
	<td id="DWI<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->DWZ_Index; ?></td>
	<td align="center">
		<input type="checkbox" name="check<?php echo $x; ?>" id="check<?php echo $x; ?>" value="1" <?php if ($spieler[$x]->gesperrt == "1") {
		    echo 'checked="checked" style="display:none;"';
		} ?>>
	</td>
	</tr>

<?php } ?>
	</table>
	
	<table class="adminlist">
	<legend><?php echo 'Bemerkung'; ?></legend>
	<tr>
	<td width="100%" valign="top">
	<?php if (is_null($liga[0]->bemerkungen)) {
	    $liga[0]->bemerkungen = '';
	} ?>
	<textarea class="inputbox" name="bemerkungen" id="bemerkungen" cols="40" rows="4" style="width:90%"><?php echo str_replace('&', '&amp;', $liga[0]->bemerkungen);?></textarea>
	</td>
	</tr>
	</table>
	
  </fieldset>
  </div>
		<div class="clr"></div>
		<input type="hidden" name="section" value="ranglisten" />
		<input type="hidden" name="option" value="com_clm" />

		<input type="hidden" name="count" value="<?php echo count($spieler); ?>" />
		<input type="hidden" name="zps" value="<?php echo $spieler[0]->ZPS; ?>" />
		<input type="hidden" name="saison" value="<?php echo $spieler[0]->sid; ?>" />
		<input type="hidden" name="gid" value="<?php echo clm_core::$load->request_int('gid'); ?>" />
		<?php if (is_null($liga[0]->published)) {
		    $liga[0]->published = '1';
		} ?>
		<input type="hidden" name="published" value="<?php echo $liga[0]->published; ?>" />
		<?php if (is_null($liga[0]->ordering)) {
		    $liga[0]->ordering = '0';
		} ?>
		<input type="hidden" name="ordering" value="<?php echo $liga[0]->ordering; ?>" />
		<?php if (is_null($liga[0]->bem_int)) {
		    $liga[0]->bem_int = '';
		} ?>
		<input type="hidden" name="bem_int" value="<?php echo $liga[0]->bem_int; ?>" />

		<?php echo JHTML::_('form.token'); ?>
		</form>
</center>

<br>
<div style=" text-align:right; padding-right:1%"><label for="name" class="hasTip" title="<?php echo JText::_('Das Chess League Manager (CLM) Projekt ist freie, kostenlose Software unter der GNU / GPL. Besuchen Sie unsere Projektseite www.chessleaguemanager.de für die neueste Version, Dokumentationen und Fragen. Wenn Sie an der Entwicklung des CLM teilnehmen wollen melden Sie sich bei uns per E-mail. Wir sind für jede Hilfe dankbar !'); ?>">Sie wollen am Projekt teilnehmen oder haben Verbesserungsvorschläge - <a href="http://www.chessleaguemanager.de">CLM Projektseite</a></label></div>
