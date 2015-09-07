<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
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
$document->addStyleSheet( $cssDir.DS.'clm_content.css', 'text/css', null, array() );
	
// Variablen holen
$sid = JRequest::getInt( 'saison', '1' );
$zps = JRequest::getVar( 'zps','1');
$man = JRequest::getInt( 'man' );
$gid	= JRequest::getInt('gid');

// Login Status prüfen
$clmuser 	= $this->clmuser;
$user		= JFactory::getUser();
	$mainframe	= JFactory::getApplication();
	$link = "index.php?option=com_clm&view=info";
// Konfigurationsparameter auslesen
	$config		= clm_core::$db->config();
	$conf_meldeliste= $config->conf_meldeliste;

if ($conf_meldeliste != 1) {
	$msg = JText::_( '<h2>Die Eingabe von Ranglisten wurde durch den Administrator gesperrt !</h2>');
	$mainframe->redirect( $link, $msg );
			}
if (!$user->get('id')) {
	$msg = JText::_( '<h1>Sie sind nicht angemeldet !</h1> <h2>Loggen Sie sich zuerst ein, bevor Sie eine Rangliste abgeben.</h2>' );
	$mainframe->redirect( $link, $msg );
 			}
if ($clmuser[0]->published < 1) {
	$msg = JText::_( '<h1>Ihr Account wurde gesperrt !</h1> <h2>Wenden Sie sich umgehend an einen Administrator.</h2>' );
	$mainframe->redirect( $link, $msg );
				}
if ($clmuser[0]->zps <> $zps) {
	$msg = JText::_( '<h1>Sie können nicht für einen anderen Verein melden !</h1>' );
	$mainframe->redirect( $link, $msg );
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
    <?php if (isset($UsePKZ) AND $UsePKZ == 1) { ?>document.getElementById('PKZ'+i).innerHTML=Spieler[i][6];
    document.getElementsByName('PKZ'+i)[0].innerHTML=Spieler[i][6];
    <?php } ?>
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
    Spieler[i]=new Array(5)
    Spieler[i][0]=document.getElementsByName('MA'+i)[0].value-0;
    if (Spieler[i][0]==0) Spieler[i][0]=999;
    Spieler[i][1]=document.getElementsByName('RA'+i)[0].value-0;
    if (Spieler[i][1]==0) Spieler[i][1]=9999;
    Spieler[i][2]=document.getElementById('SP'+i).innerHTML;
    Spieler[i][3]=document.getElementById('MGL'+i).innerHTML-0;
    Spieler[i][4]=document.getElementById('DWZ'+i).innerHTML-0;
    Spieler[i][5]=document.getElementById('DWI'+i).innerHTML-0;
    <?php if (isset($UsePKZ) AND $UsePKZ == 1) { ?>Spieler[i][6]=document.getElementById('PKZ'+i).innerHTML-0;<?php } ?>
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

<div class="componentheading">Rangliste abgeben : <?php if (isset($liga[0]->vname)) echo $liga[0]->vname; else echo $zps; ?></div>
<div><h2>Gruppe : <?php if (isset($liga[0]->gruppe)) echo $liga[0]->gruppe; else echo $gid; ?></h2></div>
<br>
<u><b>Hinweise</u></b> : 
<br><b>(1)</b> Setzen Sie Mannschaftsnummer und Rang bei allen Spielern, die Sie in die Rangliste aufnehmen möchten.
<br><b>(2)</b> Mit dem "Prüfen" Knopf können Sie die Liste auf doppelte Rangnummern kontrollieren.
<br><b>(3)</b> Mit dem "Sortieren" Knopf können Sie die Liste in die aktuelle Reihenfolge bringen.
<br><b>(4)</b> Der "Neu laden" Knopf <u><i>verwirft ALLE Änderungen</i></u> und lädt die Seite neu !
<br><b>(5)</b> Sobald "Liste absenden" gedrückt wurde ist die Rangliste verbindlich gemeldet.
<br><br><br>

<center>

<?php
/**
jimport('joomla.html.toolbar');

$bar =& new JToolBar( 'Ranglisten' );

	$bar->appendButton( 'Custom','Prüfen','IDPruefbutton','<a href="#" onclick="javascript:Pruefbutton"',true,true);
	$bar->appendButton( 'Standard','pruefen', 'Prüfen','pruefen');
	$bar->appendButton( 'Standard','review','review','review');
	$bar->appendButton( 'Standard','print', 'print','print');
	$bar->appendButton( 'Standard','save', 'save','save');

echo $bar->render();
**/
?>
<table class="toolbar"><tr>
 
<td class="button" id="Ranglisten-pruefen">
<a href="#" onclick="javascript:Pruefbutton(); return false;" class="toolbar">
<span class="icon-32-trash" title="Prüfen">
</span>
Prüfen
</a>
</td>
 
<td class="button" id="Ranglisten-sortieren">
<a href="#" onclick="javascript:Sortieren(); return false;" class="toolbar">
<span class="icon-32-pruefen" title="Sortieren">
</span>
Sortieren
</a>
</td>

<td class="button" id="Ranglisten-neuladen">
<a href="#" onclick="javascript: window.location.href=window.location.href; return false;" class="toolbar">
<span class="icon-32-pruefen" title="Neu laden">
</span>
Neu laden
</a>
</td>

<td class="button" id="Ranglisten-speichern">
<!-- <a href="#" onclick="javascript:document.adminForm.submit(); return false;" class="toolbar"> -->
<a href="#" onclick="javascript:Sendbutton(); return false;" class="toolbar"> 

<span class="icon-32-pruefen" title="Speichern">
</span>
Liste absenden !
</a>
</td>

<tr>
</table>

<br><br>

<form action="index.php?option=com_clm&amp;view=meldeliste&amp;layout=sent_rangliste" method="post" name="adminForm">

<style type="text/css">table { width:60%; }</style>

<div class="col">
  <fieldset class="adminform">
   <legend><?php echo "Rangliste Platz 1 - ".count($spieler); ?></legend>
	
	<table class="admintable meldeliste_rangliste">

	<tr>
		<td width="8%" class="key" nowrap="nowrap">Mnr</td>
		<td width="10%" class="key" nowrap="nowrap">Rang</td>
		<td class="key" nowrap="nowrap">Name</td>
		<td width="7%" class="key" nowrap="nowrap">MglNr</td>
		<td width="7%" class="key" nowrap="nowrap">PKZ</td>
		<td colspan="2" width="15%" class="key" nowrap="nowrap">DWZ</td>
	</tr>

<?php 

	for($x=0; $x < count($spieler); $x++) { ?>

	<input type="hidden" name="PKZ<?php echo $x; ?>" value="<?php echo $spieler[$x]->PKZ; ?>" />
	<input type="hidden" name="MGL<?php echo $x; ?>" value="<?php echo $spieler[$x]->Mgl_Nr; ?>" />

	<tr>
	<td class="key" nowrap="nowrap">
		<input type="text" name="MA<?php echo $x ?>" size="3" maxLength="3" value="<?php if(isset($spieler[$x]->man_nr)) { echo $spieler[$x]->man_nr; } ?>" onChange="Mcheck(this)">
	</td>
	<td class="key" nowrap="nowrap">
	<input type="text" name="RA<?php echo $x ?>" size="5" maxLength="5" value="<?php if(isset($spieler[$x]->Rang)) { echo $spieler[$x]->Rang; } ?>" onChange="Rcheck(this)">
	</td>
	<td id="SP<?php echo $x; ?>" name="SP<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->Spielername; ?></td>
	<td id="MGL<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->Mgl_Nr; ?></td>
	<td id="PKZ<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->PKZ; ?></td>
	<td id="DWZ<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->DWZ; ?></td>
	<td id="DWI<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->DWZ_Index; ?></td>
	</tr>

<?php } ?>
	</table>
  </fieldset>
  </div>
		<div class="clr"></div>
		<input type="hidden" name="section" value="ranglisten" />
		<input type="hidden" name="option" value="com_clm" />

		<input type="hidden" name="count" value="<?php echo count($spieler); ?>" />
		<input type="hidden" name="zps" value="<?php echo $spieler[0]->ZPS; ?>" />
		<input type="hidden" name="saison" value="<?php echo $spieler[0]->sid; ?>" />
		<input type="hidden" name="gid" value="<?php echo JRequest::getInt('gid'); ?>" />

		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
</center>

<br>
<div style=" text-align:right; padding-right:1%"><label for="name" class="hasTip" title="<?php echo JText::_('Das Chess League Manager (CLM) Projekt ist freie, kostenlose Software unter der GNU / GPL. Besuchen Sie unsere Projektseite www.chessleaguemanager.de für die neueste Version, Dokumentationen und Fragen. Wenn Sie an der Entwicklung des CLM teilnehmen wollen melden Sie sich bei uns per E-mail. Wir sind für jede Hilfe dankbar !'); ?>">Sie wollen am Projekt teilnehmen oder haben Verbesserungsvorschläge - <a href="http://www.chessleaguemanager.de">CLM Projektseite</a></label></div>
