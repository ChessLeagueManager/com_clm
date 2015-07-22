<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class CLMControllerDewis extends JController
{
	function __construct() {		
		parent::__construct();		
	}
	
	function display() { 
		JRequest::setVar('view','dewis');
		parent::display(); 
	} 

function verein() {
		$app	= JFactory::getApplication();
		$jinput = $app->input;
		$zps	= $jinput->get('filter_vid', null, null);
		//$app->enqueueMessage( 'Verein cntrl '.$zps, 'warning');
		//$pokes_var3 = $jinput->get('task', null, null);
		//$app->enqueueMessage( 'TASK cntrl '.$pokes_var3, 'warning');

		$addy_url = '&task=verein&zps='.$zps;
		$adminLink = new AdminLink();
		$adminLink->view = "dewis";
		$adminLink->makeURL();
		//$this->setRedirect( $adminLink->url.$addy_url,$msg_link."-->".$dat_pkz );
		$this->setRedirect( $adminLink->url.$addy_url, $msg);
	}

function update_verein() {
		$app	= JFactory::getApplication();
		$jinput = $app->input;
		$zps	= $jinput->get('zps', null, null);
		$cid	= JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		$db	=& JFactory::getDBO();
		
		if(empty($cid)){
			$app->enqueueMessage( 'Keine Auswahl getroffen ', 'warning');
			//JRequest::set('task', 'verein', 'post');
			$addy_url = '&task=verein&zps='.$zps;
			$adminLink = new AdminLink();
			$adminLink->view = "dewis";
			$adminLink->makeURL();
			$app->redirect( $adminLink->url.$addy_url);
		}
		//$implode = implode(",", $cid);
		//$app->enqueueMessage( 'Update Verein Controller '.$zps, 'warning');
		
		// aktuelle Saison finden
		$sql = " SELECT id FROM #__clm_saison "
			." WHERE published = 1 AND archiv = 0"
			." ORDER BY id ASC LIMIT 1 "
			;
		$db->setQuery( $sql);
		$sid_data=$db->loadObjectList();
		$sid = $sid_data[0]->id;

		foreach($cid as $cnt){
		$arr1 = str_split($cnt,5);
		//$app->enqueueMessage( 'Update '.$arr1[0].'--'.$arr1[1], 'warning');
		
		$sql = " SELECT d.pkz, CONCAT(d.nachname,',', d.vorname) as name, d.zps, "
			." d.mgl_nr, d.dwz, d.dwz_index, d.status "
			." ,d.geschlecht, d.geburtsjahr, d.fide_elo, d.fide_land, d.fide_id "
			." FROM #__clm_dwz_dewis as d "
			." LEFT JOIN #__clm_dwz_spieler as s ON d.zps = s.ZPS  AND CAST(d.mgl_nr AS DECIMAL)= CAST(s.Mgl_Nr AS DECIMAL) "
			." AND s.sid =".$sid
			." WHERE d.zps = '$arr1[0]' " 
			." AND CAST(d.mgl_nr AS DECIMAL) =".$arr1[1]
			;

		$db->setQuery($sql);
		$replace=$db->loadObjectList();
		//$app->enqueueMessage( 'Update Verein Controller '.$sql, 'warning');
		
		if($replace[0]->pkz >0){
		
		$sql = " DELETE FROM #__clm_dwz_spieler "
			." WHERE sid=".$sid
			." AND ZPS =".$zps
			." AND CAST(`Mgl_Nr` AS DECIMAL)= ".(int)$replace[0]->mgl_nr
			;
		$db->setQuery($sql);
		$db->query();
		
		//$app->enqueueMessage( 'Update Verein Controller '.$sql, 'warning');
		
		$sql = " REPLACE INTO #__clm_dwz_spieler ( `sid`, `ZPS`, `Mgl_Nr` , `PKZ`, `Status`, `Spielername`, `DWZ`, `DWZ_Index` "
			.",`Geschlecht`,`Geburtsjahr`,`FIDE_Elo`,`FIDE_Land`,`FIDE_ID`) "
			." VALUES ( '".$sid."', '".$replace[0]->zps."', '".$replace[0]->mgl_nr."', '".$replace[0]->pkz."' , "
			." '".$replace[0]->status."', '".$replace[0]->name."', '".$replace[0]->dwz."', '".$replace[0]->dwz_index."' "
			.",'".$replace[0]->geschlecht."','".$replace[0]->geburtsjahr."','".$replace[0]->fide_elo."' "
			.",'".$replace[0]->fide_land."','".$replace[0]->fide_id."'"
			." ) "
			;
		$db->setQuery($sql);
		$db->query();
		$app->enqueueMessage( 'Erfolgreiches Update : '.$replace[0]->name.' ('.$replace[0]->zps.'-'.$replace[0]->mgl_nr.')', 'message');
		}
		}
		//$app->enqueueMessage( 'Update Verein Controller '.$sql, 'warning');
		
		$addy_url = '&task=verein&zps='.$zps;
		$adminLink = new AdminLink();
		$adminLink->view = "dewis";
		$adminLink->makeURL();
		$app->redirect( $adminLink->url.$addy_url);
	}
	
function spieler_suchen() {
		$app	= JFactory::getApplication();
		$jinput = $app->input;
		$name	= $jinput->get('name', null, null);

		if(empty($name)){
			$app->enqueueMessage( 'Keine Auswahl getroffen ', 'warning');
			$adminLink = new AdminLink();
			$adminLink->view = "dewis";
			$adminLink->makeURL();
			$app->redirect( $adminLink->url);
		}
		
		$addy_url = '&task=spieler_suchen&name='.$name;
		$adminLink = new AdminLink();
		$adminLink->view = "dewis";
		$adminLink->makeURL();
		//$this->setRedirect( $adminLink->url.$addy_url,$msg_link."-->".$dat_pkz );
		$this->setRedirect( $adminLink->url.$addy_url, $msg);
	}

function zurueck_spieler_suche() {
	$app	= JFactory::getApplication();
	$jinput = $app->input;
	$name	= $jinput->get('name', null, null);
	$addy_url = '&task=spieler_suchen&name='.$name;
	$adminLink = new AdminLink();
	$adminLink->view = "dewis";
	$adminLink->makeURL();
	//$app->enqueueMessage( 'Zurück Controller '.$name, 'warning');
	$app->redirect( $adminLink->url.$addy_url);
}

function turnier_suchen() {
		$app	= JFactory::getApplication();
		$jinput = $app->input;
		$turnier= $jinput->get('turnier', null, null);
		$sdatum	= $jinput->get('sdatum', null, null);
		$edatum	= $jinput->get('edatum', null, null);

		if(empty($sdatum) OR empty($edatum) ){
			$app->enqueueMessage( 'Bitte Start- und Enddatum wählen ', 'warning');
			$adminLink = new AdminLink();
			$adminLink->view = "dewis";
			$adminLink->makeURL();
			$app->redirect( $adminLink->url);
		}
		
		$addy_url = '&task=turnier_suchen&turnier='.$turnier.'&sdate='.$sdatum.'&edate='.$edatum;
		$adminLink = new AdminLink();
		$adminLink->view = "dewis";
		$adminLink->makeURL();
		//$this->setRedirect( $adminLink->url.$addy_url,$msg_link."-->".$dat_pkz );
		$this->setRedirect( $adminLink->url.$addy_url, $msg);
	}


	
function zurueck($msg) {
		//$model = $this->getModel('dewis');
		//$model->upload();
		
		$adminLink = new AdminLink();
		$adminLink->view = "dewis";
		$adminLink->makeURL();

		$this->setRedirect($adminLink->url,$msg); 		
	}

	
}
?>