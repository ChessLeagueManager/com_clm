<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.chessleaguemanager.de
*/
// Hiermit kann der Footer Bereich der PDF Ausgabe angepasst werden !
// Das Copyright darf nicht entfernt werden !

//Position 1,5 cm von unten
$this->SetY(-15);
//Arial kursiv 8
$this->SetFont('Arial', 'I', 8);
//Seitenzahl
$this->Cell(0, 5, JText::_('PDF_PAGE').$this->PageNo().'/{nb}', 0, 1, 'C');
$this->Cell(10, 5, '(c)CLM 2008-2025', 0, 0, 'L');
//	$this->Cell(0,5,'Erstellt mit CLM - ChessLeagueManager [ https://www.chessleaguemanager.de ]',0,0,'C');
$this->Cell(0, 5, JText::_('GENERATED_BY_CLM'), 0, 0, 'C');
