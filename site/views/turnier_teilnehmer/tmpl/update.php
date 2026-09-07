<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
defined('_JEXEC') or die('Restricted access'); 

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$mainframe	= Factory::getApplication();
$option 	= clm_core::$load->request_string( 'option' );

// Variablen initialisieren - initializing variables
$turnier 		= $this->turnier;

//echo "<br>GET"; var_dump($_GET);
//echo "<br>POST"; var_dump($_POST);

$filter_order     = $mainframe->getUserStateFromRequest( $option.'filter_order', 'filter_order', 'snr', 'cmd' );
$filter_order_Dir = $mainframe->getUserStateFromRequest( $option.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
echo "<br>filter_order"; var_dump($filter_order);
echo "<br>filter_order_Dir"; var_dump($filter_order_Dir);
//die('update');

//die();
?>
		<input type="hidden" name="task" value="update" />
		<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
<?php

$user =Factory::getUser();

// Datensätze in Tabelle schreiben - insert data into table

// Variablen holen - get variables
$snr 		= clm_core::$load->request_int('snr',0);
$hdate_paid = 'date_paid'.$snr;
$date_paid 		= clm_core::$load->request_string($hdate_paid,'');
$hamount_paid = 'amount_paid'.$snr;
$amount_paid 		= clm_core::$load->request_string($hamount_paid,'');
$hreason = 'reason'.$snr;
$reason 		= clm_core::$load->request_string($hreason,'');

// Konfigurationsparameter auslesen - get configuration parameter
$config = clm_core::$db->config();
$msg = '';

// Teilnehmer einlesen wegen Namen
	$query = " SELECT * FROM #__clm_turniere_tlnr WHERE turnier = ". $turnier->id . " AND snr = ".$snr.";";
	$teilnehmer = clm_core::$db->loadObjectList($query);	

// Teilnehmer aktualisieren
	$query = " UPDATE #__clm_turniere_tlnr SET date_paid='" . $date_paid . "', amount_paid='".$amount_paid."', reason='".$reason."' WHERE turnier = ". $turnier->id . " AND snr = ".$snr.";";
//echo "<br>query"; var_dump($query);
	$stmt = clm_core::$db->prepare($query);
	$result = $stmt->execute();
//echo "<br>result"; var_dump($result);

// Log - log
	$aktion = "Startgeldupdate";
	$parray = array('turnier' => $turnier->id, 'snr' => $snr, 'name' => $teilnehmer[0]->name, 'date_paid' => $date_paid, 'amount_paid' => $amount_paid, 'reason' => $reason );
	clm_core::addDeprecated($aktion, json_encode($parray));

	
	$msg = Text::_( 'Startgeld aktualisiert: ' ).$teilnehmer[0]->name;
	$mainframe->enqueueMessage( $msg );

	$link = URI::base(true) .'/index.php?option=com_clm&view=turnier_teilnehmer&layout=startgeld&turnier='. $turnier->id .'&Itemid='; 
//echo "<br>link"; var_dump($link);
//die();
	$mainframe->redirect( $link );

?>


