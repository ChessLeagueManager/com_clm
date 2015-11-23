<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access');

// Variablen holen
$sid = JRequest::getInt( 'saison', '1' ); 
$zps = JRequest::getVar( 'zps','1');
$man = JRequest::getInt( 'man' ); 
$liga 		= $this->liga;


// Login Status prüfen
$clmuser 	= $this->clmuser;
$user		=JFactory::getUser();
	$mainframe	= JFactory::getApplication();

	$link = 'index.php';
// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	$conf_meldeliste=$config->conf_meldeliste;

if ($conf_meldeliste != 1) {
	$msg = JText::_( 'CLUB_LIST_DISABLED');
	$link = "index.php?option=com_clm&view=info";
	$mainframe->redirect( $link, $msg );
			}
if (!$user->get('id')) {
	$msg = JText::_( 'CLUB_LIST_LOGIN' );
	$link = "index.php?option=com_clm&view=info";
	$mainframe->redirect( $link, $msg );
 			}
if ($clmuser[0]->published < 1) {
	$msg = JText::_( 'CLUB_LIST_ACCOUNT' );
	$link = "index.php?option=com_clm&view=info";
	$mainframe->redirect( $link, $msg );
				}
if ($clmuser[0]->zps <> $zps AND strpos($liga[0]->sg_zps,$clmuser[0]->zps) === false ) {
	$msg = JText::_( 'CLUB_LIST_FALSE' );
	$link = "index.php?option=com_clm&view=info";
	$mainframe->redirect( $link, $msg );
				}
if ($user->get('id') > 0 AND  $clmuser[0]->published > 0 AND ($clmuser[0]->zps == $zps OR strpos($liga[0]->sg_zps,$clmuser[0]->zps) !== false )) {


// Prüfen ob Datensatz schon vorhanden ist
$access		= $this->access;
$abgabe		= $this->abgabe;

if ($abgabe[0]->id < 1) {
	$msg = JText::_( 'CLUB_LIST_TEAM_DISABLED' );
	$link = "index.php?option=com_clm&view=info";
	$mainframe->redirect( $link, $msg );
 			}
	$today = date("Y-m-d"); 
	//Liga-Parameter aufbereiten
	$paramsStringArray = explode("\n", $abgabe[0]->params);
	$abgabe[0]->params = array();
	foreach ($paramsStringArray as $value) {
		$ipos = strpos ($value, '=');
		if ($ipos !==false) {
			$key = substr($value,0,$ipos);
			if (substr($key,0,2) == "\'") $key = substr($key,2,strlen($key)-4);
			if (substr($key,0,1) == "'") $key = substr($key,1,strlen($key)-2);
			$abgabe[0]->params[$key] = substr($value,$ipos+1);
			}
	}	
	if (!isset($abgabe[0]->params['deadline_roster']))  {   //Standardbelegung
		$abgabe[0]->params['deadline_roster'] = '0000-00-00'; }

if ($abgabe[0]->liste > 0 AND $abgabe[0]->params['deadline_roster'] == '0000-00-00') {
	$msg = JText::_( 'CLUB_LIST_ALREADY_EXIST' );
	$link = "index.php?option=com_clm&view=info";
	$mainframe->redirect( $link, $msg );
 			}
if ($abgabe[0]->liste > 0 AND $abgabe[0]->params['deadline_roster'] < $today) {
	$msg = JText::_( 'CLUB_LIST_TOO_LATE' );
	$link = "index.php?option=com_clm&view=info";
	$mainframe->redirect( $link, $msg );
 			}
// NICHT vorhanden
else {

// Stylesheet laden

require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

// Variablen initialisieren
//echo "<br>liga: "; var_dump($liga);
$spieler	= $this->spieler;
$count		= $this->count;
$mllist		= $this->mllist;
$mflist[]		= JHTML::_('select.option',  '0', JText::_( 'TEAM_SELECT_LEADER' ), 'mf', 'mfname' );
$mflist			= array_merge( $mflist, $mllist );
$lists['mf']	= JHTML::_('select.genericlist',   $mflist, 'mf', 'class="inputbox" size="1"', 'mf', 'mfname', $liga[0]->mf );
if ($liga[0]->lokal == '') $liga[0]->lokal = $liga[0]->vlokal;
?>
<div >
<div id="meldeliste">
<div class="componentheading"><?php echo JText::_('CLUB_LIST_LIST') ?> <?php echo $liga[0]->man_name; ?></div>
<br>
<div id="desc">
<h4><?php echo JText::_('CLUB_LIST_NOTE') ?></h4>
<ol>
<li><?php echo JText::_('CLUB_LIST_1') ?></li>
<li><?php echo JText::_('CLUB_LIST_2') ?></li>
<li><?php echo JText::_('CLUB_LIST_3') ?></li>
<li><?php echo JText::_('CLUB_LIST_4') ?></li>
<li><?php echo JText::_('CLUB_LIST_5') ?></li>
</ol>
<?php //echo JText::_('CLUB_LIST_PLANNED') ?>
</div>
<?php /** echo "<br>Saison ".$sid;
echo "<br>zps ".$zps;
echo "<br>man ".$man;
echo "<br>Stamm ".$liga[0]->stamm;
echo "<br>Ersatz ".$liga[0]->ersatz;
echo "<br>published ".$clmuser[0]->published;
**/
?>
<br>
	<script language="javascript" type="text/javascript">

	 Joomla.submitbutton = function (pressbutton) { 
		var form = document.adminForm;
		var nodeList = document.getElementsByName("cid[]");
		ii = 0;
		for (var i = 0; i < nodeList.length; i++) {
			if (document.getElementsByName('cid[]')[i].checked===true) {
				var namecid=document.getElementsByName('cid[]')[i].value;
				ii++;
			} 
		}
		// do field validation
		var stamm=document.getElementsByName('stamm')[0].value;
		var ersatz=document.getElementsByName('ersatz')[0].value;
		var sum = parseInt(stamm) + parseInt(ersatz);
		if (ii == 0) {
			alert( "Bitte Spieler auswählen" );
			return false
		} else if (ii > sum) {
			alert( "Bitte max. "+sum+" Spieler auswählen" );
			return false
		} else { 
			form.submit();
		}
	}
 
		</script>


<form action="index.php?option=com_clm&amp;view=meldeliste&amp;layout=order" method="post" name="adminForm">
<center>
<!---
<table class="adminlist" cellpadding="0" cellspacing="0">
<tr> 
	<th class="anfang"><b><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $spieler ); ?>);" /></b></th> 
	<th class="anfang"><?php echo JText::_('CLUB_LIST_NAME') ?></th>
	<th class="anfang"><?php echo JText::_('CLUB_LIST_DWZ') ?></th>
</tr>

<?php $i = 0;
	foreach($spieler as $spieler1){
		$checked = JHTML::_('grid.checkedout',   $spieler1, $i );
	?>
	<tr>
	<td id="cb<?php echo $i; ?>" name="cb<?php echo $i; ?>" ><?php echo $checked; ?>
	</td>
	<td>
		<input type="hidden" name="mglnr<?php echo $i+1; ?>" value="<?php echo $spieler1->id; ?>" />
		<?php echo $spieler1->name; ?>
	</td>
	<td>
		<?php echo $spieler1->dwz; ?>
	</td>
	</tr>
	<?php $i++; } ?> 
	</table>
--->
<table class="adminlist" cellpadding="0" cellspacing="0">
<tr> 
	<th class="anfang"><b><input type="hidden" name="toggle" value=""  /></b></th> 
	<th class="anfang"><?php echo JText::_('CLUB_LIST_NAME') ?></th>
	<th class="anfang"><?php echo JText::_('CLUB_LIST_DWZ') ?></th>
	<th class="anfang"><b><input type="hidden" name="toggle" value=""  /></b></th> 
	<th class="anfang"><?php echo JText::_('CLUB_LIST_NAME') ?></th>
	<th class="anfang"><?php echo JText::_('CLUB_LIST_DWZ') ?></th>
</tr>

<?php $i = 0;
	for ($i = 0; $i < (count($spieler)/2); $i++) {
		//$checked = JHTML::_('grid.checkedout',   $spieler[$i], $i );
		if ($spieler[$i]->snr > "0" AND $spieler[$i]->snr < "999") { $checked_marker = ' checked="checked"'; $spieler[$i]->checked_out = "1"; }
		else $checked_marker = '';
		$checked = '<input type="checkbox" id="cb'.$i.'"'.$checked_marker.' name="cid[]" value="'.$spieler[$i]->id.'" onclick="Joomla.isChecked(this.checked);" title="JGRID_CHECKBOX_ROW_N" />';
	
	?>
	<tr>
	<td id="cb<?php echo $i; ?>" name="cb<?php echo $i; ?>" ><?php echo $checked; ?>
	</td>
	<td>
		<input type="hidden" name="mglnr<?php echo $i+1; ?>" value="<?php echo $spieler[$i]->id; ?>" />
		<?php echo $spieler[$i]->name; ?>
	</td>
	<td>
		<?php echo $spieler[$i]->dwz; ?>
	</td>
	<?php $j = $i + (count($spieler)/2) + (count($spieler) % 2); // +1; 
		if (isset($spieler[$j]) AND $spieler[$j]->snr > "0" AND $spieler[$j]->snr < "999") { $checked_marker = ' checked="checked"'; $spieler[$j]->checked_out = "1"; }
		else $checked_marker = '';
		if (isset($spieler[$j])) $spieler_id = $spieler[$j]->id; else $spieler_id = '';
		$checked = '<input type="checkbox" id="cb'.$j.'"'.$checked_marker.' name="cid[]" value="'.$spieler_id.'" onclick="Joomla.isChecked(this.checked);" title="JGRID_CHECKBOX_ROW_N" />';
	?>
	<td id="cb<?php echo $j; ?>" name="cb<?php echo $j; ?>" ><?php if (isset($spieler[$j])) echo $checked; ?>
	</td>
	<td>
		<input type="hidden" name="mglnr<?php echo $j+1; ?>" value="<?php if (isset($spieler[$j])) echo $spieler[$j]->id; ?>" />
		<?php if (isset($spieler[$j])) echo $spieler[$j]->name; ?>
	</td>
	<td>
		<?php if (isset($spieler[$j])) echo $spieler[$j]->dwz; ?>
	</td>
	</tr>
	<?php } ?>  
	</table>
	
	<table class="adminlist" cellpadding="0" cellspacing="0">
		<tr>
			<td class="key" nowrap="nowrap"><label for="mf"><?php echo JText::_( 'TEAM_LEADER' )." : "; ?></label>
			</td>
			<td>
			<?php echo $lists['mf']; ?>
			</td>
			<td>
			<?php  echo JText::_( 'TEAM_LEADER_COMMENT' ) ; ?>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="lokal"><?php echo JText::_( 'TEAM_LOCATION' )." : "; ?></label>
			</td>
			<td>
			<textarea class="inputbox" name="lokal" id="lokal" cols="40" rows="3" style="width:90%"><?php echo $liga[0]->lokal; ?></textarea>
			</td>
			<td>
			<?php  echo JText::_( 'CLM_KOMMA' )."<br>".JText::_( 'CLM_ADDRESS1' ); ?>
			</td>
		</tr>
	</table>
<br>
<!---
	<input type="submit" value=" <?php echo JText::_('CLUB_LIST_SORT') ?> ">
--->
			<button class="button" onclick="return Joomla.submitbutton();">
				<?php echo JText::_('CLUB_LIST_SORT'); ?>
			</button>
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="saison" value="<?php echo $sid; ?>" />
		<input type="hidden" name="lid" value="<?php echo $liga[0]->lid; ?>" />
		<input type="hidden" name="zps" value="<?php echo $zps; ?>" />
		<input type="hidden" name="man" value="<?php echo $man; ?>" />
		<input type="hidden" name="stamm" value="<?php echo $liga[0]->stamm; ?>" />
		<input type="hidden" name="ersatz" value="<?php echo $liga[0]->ersatz; ?>" />
		<input type="hidden" name="man_name" value="<?php echo $liga[0]->man_name; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
<?php }} ?>
</center>
<br>
</div></div>
<?php	
require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); 
?>
