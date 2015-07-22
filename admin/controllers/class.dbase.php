<?php
class dbase
 {
  var $tablename;         // Der Name der Tabelle
  var $tabledata;         // Inhalt der Tabelle/Datei
  var $headerlen;         // L�nge des Headers
  var $recordlen;         // L�nge eines Datensatzes
  var $fielddata;         // Array f�r Felddaten
  var $recorddata;        // Ein Datensatz
  var $recordcount;       // Anzahl der Datens�tze
  var $fp;                // Dateinummer
  var $vmode;             // 0 = dBASE als Datei (default), 1 = dBASE als String
  
  //  #####################################################################
  //  #
  //  # Constructor der Klasse. Initialisiert die Variablen.
  //  # Wird einmal f�r jede Tabelle aufgerufen.
  //  #
  //  #####################################################################
  
  function dbase ()
   {
   }
  
  function open ($tablename,$vmode=0)
   {
    $this->vmode = $vmode;
    if($this->vmode==0)
     {
      if(file_exists($tablename))
       { 
        $this->fp = fopen($tablename,"rb+");
        $this->tabledata = fread ($this->fp, 2000); // erste 2000 Zeichen (Header) lesen
        $this->tablename = trim($tablename);
       }
      else return false;
     }
    elseif($this->vmode==1)
     {
      $this->tabledata = substr($tablename,0,2000); // erste 2000 Zeichen (Header) lesen
      $this->tablename = $tablename;
     }
    $this->headerlen = $this->header_len();
    $this->recordlen = $this->record_len();
    $this->recordcount = $this->record_count();
    $this->fielddata = $this->field_desc();
    $this->recorddata = str_pad($this->recorddata,$this->recordlen);
    return true;
   }
  function create ($tablename, $fields)
   {
    if(!$this->fp = fopen($tablename,"wb+")) return false;
    // Header mit Nullbytes f�llen
    fseek($this->fp,0);
    fwrite($this->fp,$this->dec2bin(0,32));
    // dBASE-Version setzen
    fseek($this->fp,0);
    fwrite($this->fp,chr(3),1);   
    // Datum setzen
    $datum = @getdate(); 
    fwrite($this->fp,chr($datum['year']-2000),1);   
    fwrite($this->fp,chr($datum['mon']),1);   
    fwrite($this->fp,chr($datum['mday']),1);
    // Anzahl Datens�tze auf 0
    $var = strrev($this->dec2bin(0,4));
    fwrite($this->fp,$var,4);
    // Feldbeschreibungen schreiben
    $Start = 32;
    $satzlaenge = 0;
    for($x=0;$x<count($fields);$x++)
     {
      // Feld mit Nullbytes f�llen
      fseek($this->fp,$Start);
      fwrite($this->fp,$this->dec2bin(0,32));
      // Feld schreiben
      fseek($this->fp,$Start);
      fwrite($this->fp,$fields[$x][0],10);
      fseek($this->fp,$Start+11);
      fwrite($this->fp,$fields[$x][1],1);
      fseek($this->fp,$Start+16);
      fwrite($this->fp,chr($fields[$x][2]),1);
      $satzlaenge = $satzlaenge + $fields[$x][2];
      $Start = $Start + 32;    
     }	    
    fseek($this->fp,$Start);
    fwrite($this->fp,chr(13).chr(0),2);
    fseek($this->fp,8);
    fwrite($this->fp,strrev($this->dec2bin($Start+2,2)));
    fseek($this->fp,10);
    fwrite($this->fp,strrev($this->dec2bin($satzlaenge+1,2)));
    fclose($this->fp);
    $this->open($tablename);
    return true;   
   }
  function version ()
   {
    return ord(substr($this->tabledata,0,1));
   }
  function date ()
   {
    $Jahr = ord(substr($this->tabledata,1,1));
    if ($Jahr > 79) $Jahr = $Jahr + 1900; else $Jahr = $Jahr + 2000;
    $Monat = sprintf("%02d",ord(substr($this->tabledata,2,1)));
    $Tag = sprintf("%02d",ord(substr($this->tabledata,3,1)));
    return "$Tag.$Monat.$Jahr";
   }  
  function record_count ()
   {
    // Extrahierte Bytes umdrehen, nach Hex konvertieren, nach Dezimal konvertieren
    return hexdec(bin2hex(strrev(substr($this->tabledata,4,4))));
   }
  function set_record_count ($count)
   {
    $var = strrev($this->dec2bin($count,4));  
    if($this->vmode==0)
     {
      fseek($this->fp,4);
      $status = fwrite($this->fp,$var,4);
     }
    if($this->vmode==1) $this->tabledata = substr_replace($this->tabledata,$var,4,4);
    return $status;
   }  
  function unload ()
   {
    unset($this->tabledata); // Arbeitsspeicher leeren, DBF-Datei entladen
    if($this->vmode==0) fclose ($this->fp);
   } 
  function header_len ()
   {
    // Extrahierte Bytes umdrehen, nach Hex konvertieren, nach Dezimal konvertieren
    return hexdec(bin2hex(strrev(substr($this->tabledata,8,2))));
   }
  
  function record_len ()
   {
    // Extrahierte Bytes umdrehen, nach Hex konvertieren, nach Dezimal konvertieren
    return hexdec(bin2hex(strrev(substr($this->tabledata,10,2))));
   }  
  function field_desc ()
   {
    $fields = array();
    $Start = 32;
    for ($x=0;$x<50;$x++) // auf max. 50 Felder pr�fen
     {
      $feldname = substr($this->tabledata,$Start,11); // Feldname holen
      $feldtyp = substr($this->tabledata,$Start+11,1); // Feldtyp holen
      $feldlaenge = substr($this->tabledata,$Start+16,1); // Feldl�nge holen
      if (substr($feldname,0,1) == chr(13)) break; // Headerende erreicht
      $nullbyte = strpos($feldname,chr(0));
      if ($nullbyte) $feldname = substr($feldname,0,$nullbyte); // Nullbytes im Feldnamen entfernen
      $fields[$x]["NAME"] = $feldname;
      $fields[$x]["TYP"] = $feldtyp;
      $fields[$x]["LEN"] = ord($feldlaenge);
      $Start = $Start + 32;
     }
    return $fields;
   }
  function get_record ($recordnumber)
   {
    if($this->vmode==0)
     {
      fseek($this->fp,$this->headerlen + (($recordnumber) * $this->recordlen));
      $this->recorddata = fread($this->fp,$this->recordlen);
     }
    if($this->vmode==1)
     {
      $fpos = $this->headerlen + (($recordnumber) * $this->recordlen);
      $this->recorddata = substr($this->tablename,$fpos,$this->recordlen);
     }
    return $this->recorddata;
   }
  function set_record ($recordnumber)
   {
  if($this->vmode==0)
   {
    fseek($this->fp,$this->headerlen + (($recordnumber) * $this->recordlen));
    $status = fwrite($this->fp,$this->recorddata,$this->recordlen);
   }
  if($this->vmode==1)
   {
    $fpos = $this->headerlen + (($recordnumber) * $this->recordlen);
    $this->tablename = substr_replace($this->tablename,$this->recorddata,$fpos,$this->recordlen);
   }
  return $status;
   }
  function add_record ()
   {
  if($this->vmode==0){
  fseek($this->fp,$this->headerlen + (($this->recordcount) * $this->recordlen));
  $status = fwrite($this->fp,$this->recorddata,$this->recordlen);
   }
  if($this->vmode==1){
  $fpos = $this->headerlen + (($this->recordcount) * $this->recordlen);
  $this->tablename = substr_replace($this->tablename,$this->recorddata,$fpos,$this->recordlen);
   }
  $this->recordcount++;
  $this->set_record_count($this->recordcount);
  return $status;
   }
  function get_field ($fieldname)
   {
    $x = 1; $found = 0;
    for ($y=0;$y<count($this->fielddata);$y++)
     {
      if ($this->fielddata[$y]["NAME"] == $fieldname)
       {
        $found = $x;
        $flen = $this->fielddata[$y]["LEN"];
        break;
       }
      else $x = $x + $this->fielddata[$y]["LEN"];
     }
    return substr($this->recorddata,$found,$flen);
   }  
  function set_field ($fieldname,$fieldvalue)
   {
    $x = 1; $found = 0;
    for ($y=0;$y<count($this->fielddata);$y++)
     {
      if ($this->fielddata[$y]["NAME"] == $fieldname)
       {
        $found = $x;
        $flen = $this->fielddata[$y]["LEN"];
        if(strlen($fieldvalue)<$flen) $fieldvalue = str_pad($fieldvalue,$flen); 
        $this->recorddata = substr_replace($this->recorddata,$fieldvalue,$found,$flen);
        return true;
       }
      else $x = $x + $this->fielddata[$y]["LEN"];
     }
    return false;
   }
  function deleted ()
   {
    if(substr($this->recorddata,0,1)=="*") return true;
    return false;
   }
  function dec2bin($dezdata,$bytes=1)
   {
    // Wandelt Dezimalzahl in Bytewert(e) um 
    $bindata='';;
    // Hexadezimalzahl aus Dezimalzahl erstellen
    $hexdata = dechex($dezdata);
    // Hex-String anpassen (gerade L�nge)
    if(bcmod(strlen($hexdata),2)!=0) $hexdata = "0".$hexdata;
    // Bytewerte ermitteln
    for ($i=0;$i<strlen($hexdata);$i+=2)
     {
      $bindata.=chr(hexdec(substr($hexdata,$i,2)));
     }
    // L�nge des Strings anpassen
    if(strlen($bindata)<$bytes) $bindata = str_pad($bindata,$bytes,chr(0),STR_PAD_LEFT);     
    return $bindata;
   }
 }
?>