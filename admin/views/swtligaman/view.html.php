<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;

class CLMViewSWTLigaman extends JViewLegacy {

	function display ($tpl = null) {
		
		// Daten vom Model
		$state			= $this->get( 'state' );

		$swt_data		= $this->get( 'dataSWT' );
		$swt_db_data	= $this->get( 'dataSWTdb' );
		
		$db_vlist		= $this->get( 'vereinsliste' );
		$db_splist		= $this->get( 'spielerliste' );
		// !!! WICHTIG !!!
		// getSpielerliste muss *nach* getDataSWT aufgerufen werden, damit die
		// aus der SWT-Datei ausgelesene ZPS bekannt ist und danach gefiltert
		// werden kann!!

		// $db_man_nr	=& $state->get( 'db_man_nr' ); fuer update

		// Der nächste Task ist von der aktuell bearbeiteten Mannschaft abhängig
		$man = clm_core::$load->request_int('man', 0);		
		$noOrgReference = clm_core::$load->request_string('noOrgReference', '0', 'default', 'string');		
		$noBoardResults = clm_core::$load->request_string('noBoardResults', '0', 'default', 'string');		
		$anz_mannschaften = $swt_db_data['anz_mannschaften'];

		// Toolbar
		clm_core::$load->load_css("icons_images");
		ToolBarHelper::title( Text::_('TITLE_SWT_LEAGUE_MAN') ,'clm_headmenu_manager.png' );
		
		//echo "man: [$man]";
		//echo "anz_mannschaften: [$anz_mannschaften]";
		if ($man + 1 < $anz_mannschaften) {
			//ToolBarHelper::custom('nextTeam','next.png','next_f2.png', Text::_('SWT_LEAGUE_NEXT_TEAM'), false);
			ToolBarHelper::custom('nextTeam','forward.png','forward_f2.png', Text::_('SWT_LEAGUE_NEXT_TEAM'), false);
		}
		else {
			//ToolBarHelper::custom('next','next.png','next_f2.png', Text::_('SWT_LEAGUE_NEXT_STEP'), false);
			ToolBarHelper::custom('next','forward.png','forward_f2.png', Text::_('SWT_LEAGUE_NEXT_STEP'), false);
		}
		ToolBarHelper::custom('cancel','cancel.png','cancel_f2.png', Text::_('SWT_LEAGUE_CANCEL'), false);


		// Listen
		//echo "GET1: "; print_r ($_GET); //DBG
		//echo "swt_data[zps]: ".$swt_data['zps']; //DBG
		$filter_zps = clm_core::$load->request_string('filter_zps', $swt_data['zps']);
		$filter_sg_zps = clm_core::$load->request_string('filter_sg_zps', $swt_data['sg_zps']);
		//echo "filter_zps: $filter_zps"; //DBG
		//echo "GET: "; print_r ($_GET); //DBG
		// Vereinsliste
		$vlist[] = HTMLHelper::_('select.option', '0', Text::_( 'SWT_LEAGUE_CLUB_SELECT' ), 'zps', 'name');
		$vlist = array_merge( $vlist, $db_vlist );
		//$lists['vereine'] = HTMLHelper::_('select.genericlist', $vlist, 'filter_zps', 'class="inputbox" size="1" onchange="document.adminForm.submit()"', 'zps', 'name', $filter_zps);
		$lists['vereine'] = HTMLHelper::_('select.genericlist', $vlist, 'filter_zps', 'class="inputbox" size="1"', 'zps', 'name', $filter_zps);
		// Partnervereine für Spielgemeinschaft
		$sg_vlist[] = HTMLHelper::_('select.option', '0', Text::_( 'SWT_LEAGUE_CLUB_SELECT' ), 'zps', 'name');
		$sg_vlist = array_merge( $vlist, $db_vlist );
		//$lists['sg_vereine'] = HTMLHelper::_('select.genericlist', $vlist, 'filter_sg_zps', 'class="inputbox" size="1" onchange="document.adminForm.submit()"', 'zps', 'name', $filter_sg_zps);
		$sg_string = $filter_sg_zps;
		$afilter_sg_zps = array();
		$afilter_sg_zps = explode(',',$sg_string);
		for ($i = 0; $i < $swt_db_data['anz_sgp']; $i++) { 
			if (!isset($afilter_sg_zps[$i]) OR $afilter_sg_zps[$i] === 0 OR $afilter_sg_zps[$i] == '') $afilter_sg_zps[$i] = '0';
			$lists['sg'.$i]= HTMLHelper::_('select.genericlist',   $vlist, 'sg_zps'.$i , 'class="inputbox" size="1" ','zps', 'name', $afilter_sg_zps[$i] );
		}
		// Liste der Teilnehmer-Nummern
		$tlist = array ();
		for ($i = 1; $i <= $swt_db_data['anz_mannschaften']; $i++) {
			$tlist[] = HTMLHelper::_('select.option', $i, $i, 'value', 'text');
		}
		$lists['tln_nr'] = HTMLHelper::_('select.genericlist', $tlist, 'tln_nr', 'class="inputbox" size="1"', 'value', 'text', $man + 1);

		// Tabellen
		// Stammspieler-Auswahl
		$stammtable = '';
		for ($i = 1; $i <= $swt_db_data['anz_bretter']; $i++) {
			if (isset($swt_data['spieler_'.$i])) {
				$dwzid		= $swt_data['spieler_'.$i]['dwzid'];
				$swt_spieler = $swt_data['spieler_'.$i]['name'];
			} else {
				$dwzid		= 0;
				$swt_spieler = '';
			}
			$splist		= array ();
			$splist[]	= HTMLHelper::_('select.option', '0', Text::_( 'SWT_LEAGUE_PLAYER_SELECT' ), 'id', 'name');
			$splist		= array_merge( $splist, $db_splist );
			if ($noOrgReference == '0')
//				$splist[]	= HTMLHelper::_('select.option', '-1', $swt_data['spieler_'.$i]['name'] . " " . Text::_( 'SWT_LEAGUE_NEW' ), 'id', 'name');
				$splist[]	= HTMLHelper::_('select.option', '-1', $swt_spieler . " " . Text::_( 'SWT_LEAGUE_NEW' ), 'id', 'name');
			else
//				$splist[]	= HTMLHelper::_('select.option', '-1', $swt_data['spieler_'.$i]['name'], 'id', 'name');
				$splist[]	= HTMLHelper::_('select.option', '-1', $swt_spieler, 'id', 'name');
			$blist		= HTMLHelper::_('select.genericlist', $splist, 'dwzid_'.$i, 'class="inputbox" size="1"', 'id', 'name', $dwzid);
			
//			$swt_spieler = $swt_data['spieler_'.$i]['name'];
			if ($dwzid == -1) {
				$swt_spieler = '<b style="color: #f00">' . $swt_spieler . '</b>';
				$swt_spieler .= '<br>'.$swt_data['spieler_'.$i]['zps'].($swt_data['spieler_'.$i]['mgl_nr']!="" ? "/".$swt_data['spieler_'.$i]['mgl_nr'] : "");
			}
			if ($noOrgReference == '0')
			$tablerow = '<tr>'
					  . '<td nowrap="nowrap">'
					  . '<label for="dwzid_'.$i.'">' . Text::_( 'SWT_LEAGUE_PLAYER_NR' ).' '.$i.'</label>'
					  . '</td>'
					  . '<td>' . $blist . '</td>'
					  . '<td>' . $swt_spieler . '</td>'
					  . '</tr>';
			elseif ($noBoardResults == '0')
				$tablerow = '<tr>'
					  . '<td nowrap="nowrap">'
					  . '<label for="dwzid_'.$i.'">' . Text::_( 'SWT_LEAGUE_PLAYER_NR' ).' '.$i.'</label>'
					  . '</td>'
					  . '<td>' . $blist . '</td>'
					  . '</tr>';
			else
				$tablerow = '<tr>'
					  . '<td style="display:none;">' . $blist . '</td>'
					  . '</tr>';
			$stammtable .= $tablerow;
		}
		$tables['stammspieler'] = $stammtable;

		// Ersatzspieler-Auswahl
		$ersatztable = '';
		$start_ersatz = $swt_db_data['anz_bretter'] + 1;
		$ende_ersatz = $start_ersatz + $swt_db_data['anz_ersatzspieler'] - 1;

		for ($i = $start_ersatz; $i <= $ende_ersatz; $i++) {
			if (!isset($swt_data['spieler_'.$i])) continue;
			$dwzid		= $swt_data['spieler_'.$i]['dwzid'];
			$splist		= array ();
			$splist[]	= HTMLHelper::_('select.option', '0', Text::_( 'SWT_LEAGUE_PLAYER_SELECT' ), 'id', 'name');
			$splist		= array_merge( $splist, $db_splist );
			if ($noOrgReference == '0')
				$splist[]	= HTMLHelper::_('select.option', '-1', $swt_data['spieler_'.$i]['name'] . " " . Text::_( 'SWT_LEAGUE_NEW' ), 'id', 'name');
			else
				$splist[]	= HTMLHelper::_('select.option', '-1', $swt_data['spieler_'.$i]['name'], 'id', 'name');
			$blist		= HTMLHelper::_('select.genericlist', $splist, 'dwzid_'.$i, 'class="inputbox" size="1"', 'id', 'name', $dwzid);

			$swt_spieler = $swt_data['spieler_'.$i]['name'];
			if ($dwzid == -1) {
				$swt_spieler = '<b style="color: #f00">' . $swt_spieler . '</b>';
				$swt_spieler .= '<br>'.$swt_data['spieler_'.$i]['zps'].($swt_data['spieler_'.$i]['mgl_nr']!="" ? "/".$swt_data['spieler_'.$i]['mgl_nr'] : "");
			}
			if ($noOrgReference == '0')
			$tablerow = '<tr>'
					  . '<td nowrap="nowrap">'
					  . '<label for="dwzid_'.$i.'">' . Text::_( 'SWT_LEAGUE_PLAYER_NR' ).' '.$i.'</label>'
					  . '</td>'
					  . '<td>' . $blist . '</td>'
					  . '<td>' . $swt_spieler . '</td>'
					  . '</tr>';
			elseif ($noBoardResults == '0')
				$tablerow = '<tr>'
					  . '<td nowrap="nowrap">'
					  . '<label for="dwzid_'.$i.'">' . Text::_( 'SWT_LEAGUE_PLAYER_NR' ).' '.$i.'</label>'
					  . '</td>'
					  . '<td>' . $blist . '</td>'
					  . '</tr>';
			else
				$tablerow = '<tr>'
					  . '<td style="display:none;">' . $blist . '</td>'
					  . '</tr>';
			$ersatztable .= $tablerow;
		}
		$tables['ersatzspieler'] = $ersatztable;


		// Daten an Template
		$this->lists = $lists;
		$this->tables = $tables;
		$this->swt_data = $swt_data;
		$this->swt_db_data = $swt_db_data;

		parent::display ($tpl);

	}

}

?>
