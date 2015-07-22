<?php

// Hiermit kann der Footer Bereich der PDF Ausgabe angepasst werden !
// Das Copyright darf nicht entfernt werden !

	//Position 1,5 cm von unten
	$this->SetY(-15);
	//Arial kursiv 8
	$this->SetFont('Arial','I',8);
	//Seitenzahl
	$this->Cell(0,5,'Seite '.$this->PageNo().'/{nb}',0,1,'C');
	$this->Cell(10,5,'(c)CLM 2008-2015',0,0,'L');
	$this->Cell(0,5,'Erstellt mit CLM - ChessLeagueManager [ http://www.chessleaguemanager.de ]',0,0,'C');
?>