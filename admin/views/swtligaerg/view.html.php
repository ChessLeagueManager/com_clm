<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

class CLMViewSWTLigaerg extends JViewLegacy {

	function display ($tpl = null) {
		// Daten vom Model
		$state			= $this->get( 'state' );

		$swt_data		= $this->get( 'dataSWT' );
		$swt_db_data	= $this->get( 'dataSWTdb' );
		
		$db_splist		= $this->get( 'spielerliste' );
		$db_erglist		= $this->get( 'ergebnisliste' );
		$db_erglistk	= $this->get( 'ergebnisliste' );
		// !!! WICHTIG !!!
		// getSpielerliste muss *nach* getDataSWT aufgerufen werden, damit die
		// aus der SWT-Datei ausgelesene ZPS bekannt ist und danach gefiltert
		// werden kann!!

		// $db_man_nr	=& $state->get( 'db_man_nr' ); fuer update

		// Der nächste Task ist von der aktuellen Runde abhängig
		$runde				= clm_core::$load->request_int('runde', 0);
		$dgang				= clm_core::$load->request_int('dgang', 0);
		$mturnier			= clm_core::$load->request_int('mturnier', 0);

		$anz_mannschaften	= $swt_db_data['anz_mannschaften'];
		$anz_bretter		= $swt_db_data['anz_bretter'];
		$anz_paarungen		= ceil ($anz_mannschaften / 2);
		$anz_runden			= $swt_db_data['anz_runden'];
		$anz_durchgaenge	= $swt_db_data['anz_durchgaenge'];
		$gesp_runden		= $swt_data['gesp_runden'];
		$ausgeloste_runden	= $swt_data['ausgeloste_runden'];
		
		// Toolbar
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title( JText::_('TITLE_SWT_LEAGUE_ERG') ,'clm_headmenu_manager.png' );
		
		//echo "runde, gesp, ausgelost, anz_runden: $runde, $gesp_runden, $ausgeloste_runden, $anz_runden"; //DBG
		//echo "dgang, anz_durchgaenge: $dgang, $anz_durchgaenge"; //DBG
		// "$runde < $gesp_runden - 1 && " vorne im if ggf. einsetzen. ACHTUNG! Dann müssen nicht gespielte Runden
		// beim abschließenden Speichern erstellt werden!
		$bool_mt_runden = true; // (($mturnier == 1 && $runde < $gesp_runden - 1) || $mturnier == 0);
		if ($bool_mt_runden && ($runde + 1 < $anz_runden || $dgang + 1 < $anz_durchgaenge)) {
			JToolBarHelper::custom('next','forward.png','forward_f2.png', JText::_('SWT_LEAGUE_NEXT_ROUND'), false);
		}
		else {
			JToolBarHelper::custom('finish','save.png','save_f2.png', JText::_('SWT_LEAGUE_FINISH'), false);
		}
		JToolBarHelper::custom('cancel','cancel.png','cancel_f2.png', JText::_('SWT_LEAGUE_CANCEL'), false);

		// Tabellen für Spielerauswahl
		$spielertables = array ();
		$model = $this->getModel ('swtligaerg');
		for ($p = 1; $p <= $anz_paarungen; $p++) {
			if (!isset($swt_data[$p])) break;
			$heim = $swt_data[$p]['heim'];
			$gast = $swt_data[$p]['gast'];
		
			$spielertables[$p] = '';
			for ($b = 1; $b <= $swt_db_data['anz_bretter']; $b++) {

				if (isset($swt_data[$p]['hbrett_'.$b])) $hspieler = $swt_data[$p]['hbrett_'.$b];
				else $hspieler = 0;

				$hsplist = array ();
				$hsplist[] = JHtml::_('select.option', '0', JText::_( 'SWT_LEAGUE_PLAYER_SELECT' ), 'id', 'text');
				$hsplist = array_merge( $hsplist, $db_splist[$heim] );
				$hblist = JHtml::_('select.genericlist', $hsplist, 'hbrett_'.$p.'_'.$b, 'class="inputbox" size="1"', 'id', 'text', $hspieler);

				if (isset($swt_data[$p]['gbrett_'.$b])) $gspieler = $swt_data[$p]['gbrett_'.$b];
				else $gspieler = 0;
				$gsplist = array ();
				$gsplist[] = JHtml::_('select.option', '0', JText::_( 'SWT_LEAGUE_PLAYER_SELECT' ), 'id', 'text');
				$gsplist = array_merge( $gsplist, $db_splist[$gast] );
				$ablist = JHtml::_('select.genericlist', $gsplist, 'gbrett_'.$p.'_'.$b, 'class="inputbox" size="1"', 'id', 'text', $gspieler);

				if (isset($swt_data[$p]['erg_'.$b])) $ergebnis = $swt_data[$p]['erg_'.$b]; else $ergebnis = 0;
/*				echo "<br/> erg_str: " . $swt_data[$p]['ergstr_'.$b]; //DBG
				echo " erg_$p"."_$b: " . $ergebnis; //DBG*/
				$erglist = array ();
				$erglist[] = JHtml::_('select.option', 'NULL', JText::_( 'SWT_LEAGUE_RESULT_SELECT' ), 'ergid', 'text');
				$erglist = array_merge( $erglist, $db_erglist );
				$results = JHtml::_('select.genericlist', $erglist, 'erg_'.$p.'_'.$b, 'class="inputbox" size="1"', 'ergid', 'text', $ergebnis);

				if (isset($swt_data[$p]['ergk_'.$b])) $ergebnisk = $swt_data[$p]['ergk_'.$b]; else $ergebnisk = 0;
				$erglistk = array ();
				$erglistk[] = JHtml::_('select.option', 'NULL', JText::_( 'SWT_LEAGUE_RESULT_SELECT' ), 'ergid', 'text');
				$erglistk = array_merge( $erglistk, $db_erglistk );
				$resultsk = JHtml::_('select.genericlist', $erglistk, 'ergk_'.$p.'_'.$b, 'class="inputbox" size="1"', 'ergid', 'text', $ergebnisk);

				$tablerow = '<tr>'
						  . '<td nowrap="nowrap">'
						  . '<label for="brett_'.$p.'_'.$b.'">' . JText::_( 'SWT_LEAGUE_BOARD_NR' ).' '.$b.'</label>'
						  . '</td>'
						  . '<td>' . $hblist . '</td>'
						  . '<td>' . $ablist . '</td>'
						  . '<td>' . $results . '</td>'
						  . '<td>' . $resultsk . '</td>'
						  . '</tr>';

				$spielertables[$p] .= $tablerow;
			}
			
		}
		$tables['auswahl'] = $spielertables;

		// Hidden-Felder
		$hidden['farbe'] = '';
		for ($p = 1; $p <= $anz_paarungen; $p++) {
			for ($b = 1; $b <= $anz_bretter; $b++) {
				if (isset($swt_data[$p]['hfarbe_'.$b]) AND isset($swt_data[$p]['gfarbe_'.$b])) 
				$hidden['farbe'] .= '<input type="hidden" name="hfarbe_'.$p.'_'.$b.'" value="'.$swt_data[$p]['hfarbe_'.$b].'" />'
									. '<input type="hidden" name="gfarbe_'.$p.'_'.$b.'" value="'.$swt_data[$p]['gfarbe_'.$b].'" />';
			}
		}

		// Daten an Template
		$this->tables = $tables;
		$this->hidden = $hidden;
		$this->swt_data = $swt_data;
		$this->swt_db_data = $swt_db_data;
		$this->anz_paarungen = $anz_paarungen;

		parent::display ($tpl);

	}

}
?>
