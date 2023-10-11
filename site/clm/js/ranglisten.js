/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
	function edit()
	{
	var task 	= document.getElementsByName ( "task") [0];
	var pre_task 	= document.getElementsByName ( "pre_task") [0];
	task.value 	= "add";
	pre_task.value 	= "add";
	document.adminForm.submit();
	}

	Joomla.submitbutton = function (pressbutton) { 		
		var form = document.adminForm;
		var pre_task = document.getElementsByName ( "pre_task") [0];

		if (pressbutton == 'sortieren') { 
			Sortieren(); return true; }
		if (pressbutton == 'pruefen') { 
			Pruefbutton(); 	return true; }
		if (pressbutton == 'neu_laden') { 
			location.reload(); return true; }
		if (pressbutton == 'save') { 
			if (Pruefen()==false) return; }
		if (pressbutton == 'apply') { 
			if (Pruefen()==false) return; }
		if (pre_task.value == 'add') {
			if (pressbutton == 'cancel') {
				Joomla.submitform( pressbutton ); return; }
			if (pressbutton == 'save') { 
				if (Pruefen()==false) return; }
			if (pressbutton == 'apply') { 
				if (Pruefen()==false) return; }
			// do field validation
			if (form.filter_vid.value == "0") {
				alert( unescape(clm_ranglisten_verein) );
			} else if (form.filter_sid.value == "0") {
				alert( unescape(clm_ranglisten_saison) );
			} else if (form.filter_gid.value == "0") {
				alert( unescape(clm_ranglisten_gruppe) );
			} else {
				Joomla.submitform( pressbutton );
			}
		} else {
			if (pressbutton == 'save') { 
				if (Pruefen()==false) return; }
			if (pressbutton == 'apply') { 
				if (Pruefen()==false) return; }
			Joomla.submitform( pressbutton );
		}
	}

function Zcheck(Wert)
 {
  var chkZ = 1;
  for (i = 0; i < Wert.length; ++i)
    if (Wert.charAt(i) < "0" ||
        Wert.charAt(i) > "9")
      chkZ = -1;
  if (chkZ == -1) {
   return false;
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
      alert( unescape(clm_ranglisten_check) );
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
    if (Spieler[i][0]==999)  document.getElementsByName('MA'+i)[0].value=0;
    else document.getElementsByName('MA'+i)[0].value=Spieler[i][0];
    if (Spieler[i][1]==9999) document.getElementsByName('RA'+i)[0].value=0;
    else document.getElementsByName('RA'+i)[0].value=Spieler[i][1];
    document.getElementById('SP'+i).innerHTML=Spieler[i][2];
    document.getElementsByName('MGL'+i)[0].value=Spieler[i][3];
    document.getElementById('MGL'+i).innerHTML=Spieler[i][3];
    document.getElementById('DWZ'+i).innerHTML=Spieler[i][4];
    document.getElementById('DWI'+i).innerHTML=Spieler[i][5];
    document.getElementsByName('ZPSM'+i)[0].value=Spieler[i][6];
    document.getElementById('ZPSM'+i).innerHTML=Spieler[i][6];
    if (clm_ranglisten_usepkz == 1) { 
		document.getElementById('PKZ'+i).innerHTML=Spieler[i][7];
		document.getElementsByName('PKZ'+i)[0].value=Spieler[i][7];
    } 
    document.getElementById('Status'+i).innerHTML=Spieler[i][8];
    document.getElementById('check'+i).checked=Spieler[i][9];
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
    if (clm_ranglisten_usepkz == 1) { 
		Spieler[i][7]=document.getElementById('PKZ'+i).innerHTML-0;
    } 
    Spieler[i][8]=document.getElementById('Status'+i).innerHTML;
    Spieler[i][9]=document.getElementById('check'+i).checked;
    i++;    
   }
  QSort(0,i-1,0);
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

