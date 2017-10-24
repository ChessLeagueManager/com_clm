<?php
$pdf_orientation = JRequest::getVar( 'pdf_orientation');
//CLM-Logo links 
    $this->Image(JPATH_COMPONENT.DS.'images'.DS.'clm_logo.png',15,6,22);

// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	// Zur Abwärtskompatibilität mit CLM <= 1.0.3 werden alte Daten aus Language-Datei als Default eingelesen
	$fromname = $config->email_fromname;
	$org_logo = $config->org_logo;
// Orientation Portrait/Landscape
	if (!isset($pdf_orientation) OR (strpos('PpLl', $pdf_orientation) === false)) $pdf_orientation = 'P';
	if ($pdf_orientation == 'L' OR $pdf_orientation == 'l') {
		$pdf_width = 285;
	} else {
		$pdf_width = 195;
	}
//Titel
    $this->SetFont('Arial','',12);
	$this->Cell($pdf_width-15,2,utf8_decode($fromname),0,1,'C');
	$this->SetFont('Arial','B',8);
// Include the class
	//include_once('idna_convert.class.php');
	if (!class_exists('idna_convert')) {
		$path = clm_core::$path . DS . "includes" . DS . "idna_convert.class" . '.php';
		require_once ($path);
	}
// Instantiate it (depending on the version you are using) with
	$IDN = new idna_convert();
// The input string
	$input = $_SERVER['HTTP_HOST'];
// Encode it to its punycode presentation
	$output = $IDN->decode($input);
	$this->Cell($pdf_width-15,5,utf8_decode($output),0,1,'C');

//Logo der Organisation (Landesverband, Verein, ...; über Einstellungen vorgegeben)  rechts 
	$file_headers = @get_headers($org_logo);
	if($org_logo != '' AND $file_headers[0] != 'HTTP/1.1 404 Not Found') {
		$this->Image($org_logo,$pdf_width-20,6,15); }
//Linie mit Zeilenumbruch
    $this->Line(15, 20, $pdf_width, 20);
    $this->Ln(5);
?>