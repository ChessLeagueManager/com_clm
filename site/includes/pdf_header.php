<?php
//CLM-Logo links 
    $this->Image(JPATH_COMPONENT.DS.'images'.DS.'clm_logo.png',15,6,22);

// Konfigurationsparameter auslesen
	$config = &JComponentHelper::getParams( 'com_clm' );
	// Zur Abwärtskompatibilität mit CLM <= 1.0.3 werden alte Daten aus Language-Datei als Default eingelesen
	$fromname = $config->get('email_fromname', JText::_('USER_MAIL_FROM_NAME'));
	$org_logo = $config->get('org_logo', '');

//Titel
    $this->SetFont('Arial','',12);
	$this->Cell(180,2,utf8_decode($fromname),0,1,'C');
	$this->SetFont('Arial','B',8);
// Include the class
	include_once('idna_convert.class.php');
// Instantiate it (depending on the version you are using) with
	$IDN = new idna_convert();
// The input string
	$input = $_SERVER['HTTP_HOST'];
// Encode it to its punycode presentation
	$output = $IDN->decode($input);
	$this->Cell(180,5,utf8_decode($output),0,1,'C');

//Logo der Organisation (Landesverband, Verein, ...; über Einstellungen vorgegeben)  rechts 
	$file_headers = @get_headers($org_logo);
	if($org_logo != '' AND $file_headers[0] != 'HTTP/1.1 404 Not Found') {
		$this->Image($org_logo,175,6,15); }
//Linie mit Zeilenumbruch
    $this->Line(15, 20, 195, 20);
    $this->Ln(5);
?>