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
JRequest::checkToken() or die( 'Invalid Token' );

$mainframe	= JFactory::getApplication();

// Stylesheet laden

require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

// Variablen holen
$sid 		= JRequest::getInt('saison','1');
$lid 		= JRequest::getInt('lid','1');
$zps 		= JRequest::getVar('zps');
$man 		= JRequest::getInt('man');
$stamm 		= JRequest::getInt('stamm');
$ersatz		= JRequest::getInt('ersatz');
$cid 		= JRequest::getVar('cid', array(), '', 'array');
$man_name 	= JRequest::getVar('man_name');
$liga_lokal	= JRequest::getVar('lokal');
$liga_mf 	= JRequest::getVar('mf');
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;
 
$cid_sql = array();
foreach($cid as $cid_a) {
        $cid_sql[] = '\''.$cid_a.'\'';
    }
$cids = implode(',',$cid_sql);

// Login Status prüfen
// Prüfen ob Datensatz schon vorhanden ist
	$db			= JFactory::getDBO();
	$query	= "SELECT id, liste "
		." FROM #__clm_mannschaften "
		." WHERE sid = $sid AND zps = '".$zps."' "
		." AND liga = $lid AND man_nr = $man AND published = 1 "
		;
	$db->setQuery( $query );
	$test=$db->loadObjectList();

if ($test[0]->id < 1) {
	$link = "index.php?option=com_clm&view=info";
	$msg = JText::_( 'CLUB_LIST_TEAM_DISABLED' );
	$mainframe->redirect( $link, $msg );
 			}

$abgabe		= $this->abgabe;
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
	$msg = JText::_( 'CLUB_LIST_ALREADY_EXIST' ).'XX';
	$link = "index.php?option=com_clm&view=info";
	$mainframe->redirect( $link, $msg );
}
if ($abgabe[0]->liste > 0 AND $abgabe[0]->params['deadline_roster'] < $today) {
	$msg = JText::_( 'CLUB_LIST_TOO_LATE' );
	$link = "index.php?option=com_clm&view=info";
	$mainframe->redirect( $link, $msg );
 			}
// NICHT vorhanden //

// Variablen initialisieren
$liga 		= $this->liga;
$mllist		= $this->mllist;
$spieler		= $this->spieler;
$mflist[]		= JHTML::_('select.option',  '0', JText::_( 'TEAM_SELECT_LEADER' ), 'mf', 'mfname' );
$mflist			= array_merge( $mflist, $mllist );
$lists['mf']	= JHTML::_('select.genericlist',   $mflist, 'mf', 'class="inputbox" size="1"', 'mf', 'mfname', $liga_mf );

	// Textparameter setzen
	if ($abgabe[0]->liste > 0) $erstmeldung = 0;	// Erstmeldung nein
	else $erstmeldung = 1;  						// Erstmeldung ja
	if ($abgabe[0]->params['deadline_roster'] < $today) { $korr_moeglich = 0; 	// Korrektur möglich im FE nein
														$deadline_roster = ''; }
	else { $korr_moeglich = 1; 			// Korrektur möglich im FE ja
		$deadline_roster = JHTML::_('date', $abgabe[0]->params['deadline_roster'], JText::_('DATE_FORMAT_CLM_F')); }

?>
<div >
<div id="meldeliste">
<div class="componentheading"><?php echo JText::_('CLUB_LIST_SORT_LIST') ?> <?php echo $man_name; ?></div>
<br>
<div id="desc">
<h4><?php echo JText::_('CLUB_LIST_NOTE') ?></h4>
<ol>
<li><?php echo JText::_('CLUB_LIST_HINT_S1') ?></li>
<?php if ($korr_moeglich == 0) { ?>
	<li><?php echo JText::_('CLUB_LIST_HINT_S2') ?></li>
<?php } else { ?>
	<li><?php echo JText::_('CLUB_LIST_HINT_S2A1').$deadline_roster.JText::_('CLUB_LIST_HINT_S2A2').' '.JText::_('CLUB_LIST_HINT_S2A3') ?></li>
<?php } ?>
<li><?php echo JText::_('CLUB_LIST_HINT_S3') ?></li>
<li><?php echo JText::_('CLUB_LIST_HINT_S4') ?></li>
</ol>
<?php //echo JText::_('CLUB_LIST_PLANNED') ?>
</div>
<?php 

?>
<?php $sort= CLMModelMeldeliste::Sortierung ($cids); ?>
<br>
<script type="text/javascript"><!--
        function Tausch ( idA, idB )
        {
          // Name tauschen
          var nameA = document.getElementById ( "name" + idA );
          var nameB = document.getElementById ( "name" + idB );
          tmp1 = nameA.innerHTML;
          nameA.innerHTML = nameB.innerHTML;
          nameB.innerHTML = tmp1;

          // DWZ tauschen
          var dwzA = document.getElementById ( "dwz" + idA );
          var dwzB = document.getElementById ( "dwz" + idB );
          tmp2 = dwzA.innerHTML;
          dwzA.innerHTML = dwzB.innerHTML;
          dwzB.innerHTML = tmp2;

          // club tauschen
          var zpsnameA = document.getElementById ( "zpsname" + idA );
          var zpsnameB = document.getElementById ( "zpsname" + idB );
          tmp3 = zpsnameA.innerHTML;
          zpsnameA.innerHTML = zpsnameB.innerHTML;
          zpsnameB.innerHTML = tmp3;

          // mglnr tauschen
          var mglnrA = document.getElementById ( "mglnr" + idA );
          var mglnrB = document.getElementById ( "mglnr" + idB );
          tmp4 = mglnrA.innerHTML;
          mglnrA.innerHTML = mglnrB.innerHTML;
          mglnrB.innerHTML = tmp4;

          // (hidden) club zps tauschen
          var hiddenzpsA = document.getElementById ( "hidden_zps" + idA );
          var hiddenzpsB = document.getElementById ( "hidden_zps" + idB );
          tmp5 = hiddenzpsA.value;
          hiddenzpsA.value = hiddenzpsB.value;
          hiddenzpsB.value = tmp5;

          // (hidden) Mgl_Nr tauschen
          var hiddenA = document.getElementById ( "hidden_mglnr" + idA );
          var hiddenB = document.getElementById ( "hidden_mglnr" + idB );
          tmp6 = hiddenA.value;
          hiddenA.value = hiddenB.value;
          hiddenB.value = tmp6;
          // PKZ tauschen
          var mglnrA = document.getElementById ( "PKZ" + idA );
          var mglnrB = document.getElementById ( "PKZ" + idB );
          tmp7 = mglnrA.innerHTML;
          mglnrA.innerHTML = mglnrB.innerHTML;
          mglnrB.innerHTML = tmp7;
          // (hidden) PKZ tauschen
          var hiddenA = document.getElementById ( "hidden_PKZ" + idA );
          var hiddenB = document.getElementById ( "hidden_PKZ" + idB );
          tmp8 = hiddenA.value;
          hiddenA.value = hiddenB.value;
          hiddenB.value = tmp8;

        }

        function NachUnten ( $id, $nofocus )
        {
		if ( document.getElementsByName ( "hidden_mglnr" + ( $id + 1 ) ).length )
          {
            Tausch ( $id, $id + 1 );
		if ( arguments.length == 2 ) 
		document.getElementById ( "runter" + ( $id + 1 ) ).focus ();
          }
        }

        function NachOben ( $id, $nofocus )
        {
          if ( $id > 1 )
          {
            Tausch ( $id - 1, $id );
		if ( arguments.length == 2 ) 
		document.getElementById ( "hoch" + ( $id - 1 ) ).focus ();
          }
        }
--></script>

<form action="index.php?option=com_clm&amp;view=meldeliste&amp;layout=sent" method="post" name="adminForm">
<center>
<table class="adminlist" cellpadding="0" cellspacing="0">
	<tr> 
		<th class="anfang" width="5%"><?php echo JText::_('CLUB_LIST_NR') ?></th>
		<th class="anfang"><?php echo JText::_('CLUB_LIST_NAME') ?></th>
		<th class="anfang" width="8%"><?php echo JText::_('CLUB_LIST_DWZ') ?></th>
		<?php if ($countryversion =="de") { ?>
			<th class="anfang" width="35%"><?php echo JText::_('CLUBS_LIST_NAME') ?></th>
			<th class="anfang" width="8%"><?php echo JText::_('CLUB_LIST_MGL') ?></th>
		<?php } else { ?>
			<th class="anfang" width="28%"><?php echo JText::_('CLUBS_LIST_NAME') ?></th>
			<th class="anfang" width="15%"><?php echo JText::_('CLUB_LIST_PKZ') ?></th>
		<?php } ?>
		<th class="anfang" width="12%"><?php echo JText::_('CLUB_LIST_SORT_DIR') ?></th>
	</tr>

<?php $i = 0;
	foreach($cid as $cid){ 
	if ($i< ($stamm+$ersatz)) {
	?>
	<tr>
		<td><?php echo $i+1; ?></td>
		<td><span id="name<?php echo $i+1; ?>"><?php echo $sort[$i]->name; ?></span><input type="hidden" name="hidden_zps<?php echo $i+1; ?>" id="hidden_zps<?php echo $i+1; ?>" value="<?php echo $sort[$i]->zps; ?>" /></td>
		<td id="dwz<?php echo $i+1; ?>"><?php echo $sort[$i]->dwz; ?></td>
		<td id="zpsname<?php echo $i+1; ?>"><?php echo $sort[$i]->Vereinname; ?></td>
		<?php if ($countryversion =="de") { ?>
		  <td id="mglnr<?php echo $i+1; ?>"><?php echo $sort[$i]->Mgl_Nr; ?>
			<input type="hidden" name="hidden_mglnr<?php echo $i+1; ?>" id="hidden_mglnr<?php echo $i+1; ?>" value="<?php echo $sort[$i]->Mgl_Nr; ?>" />
			<input type="hidden" name="PKZ<?php echo $i+1; ?>" id="PKZ<?php echo $i+1; ?>" value="<?php echo $sort[$i]->PKZ; ?>" />
			<input type="hidden" name="hidden_PKZ<?php echo $i+1; ?>" id="hidden_PKZ<?php echo $i+1; ?>" value="<?php echo $sort[$i]->PKZ; ?>" />
			</td>
		<?php } else { ?>
		  <td id="PKZ<?php echo $i+1; ?>"><?php echo $sort[$i]->PKZ; ?>
			<input type="hidden" name="hidden_PKZ<?php echo $i+1; ?>" id="hidden_PKZ<?php echo $i+1; ?>" value="<?php echo $sort[$i]->PKZ; ?>" /></td>
			<input type="hidden" name="mglnr<?php echo $i+1; ?>" id="mglnr<?php echo $i+1; ?>" value="<?php echo $sort[$i]->Mgl_Nr; ?>" />
			<input type="hidden" name="hidden_mglnr<?php echo $i+1; ?>" id="hidden_mglnr<?php echo $i+1; ?>" value="<?php echo $sort[$i]->Mgl_Nr; ?>" />
			</td>
		<?php } ?>
		<td>&nbsp;&nbsp;
		<?php if ($i == 0) { ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php } else { ?>
		<a href="javascript:NachOben(<?php echo $i+1; ?>);" id="hoch<?php echo $i+1; ?>"><img  src="components/com_clm/images/uparrow.png" alt="NachOben" /></a>
		<?php } ?>&nbsp;&nbsp;&nbsp;
		<?php if ($i == ($stamm+$ersatz-1)) echo "   "; else { ?>
		<a href="javascript:NachUnten(<?php echo $i+1; ?>);" id="runter<?php echo $i+1; ?>"><img  src="components/com_clm/images/downarrow.png" alt="NachUnten" /></a>
		<?php } ?></td>
	</tr>
	<?php $i++;}} ?> 
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
			<textarea class="inputbox" name="lokal" id="lokal" cols="40" rows="3" style="width:90%"><?php echo $liga_lokal; ?></textarea>
			</td>
			<td>
			<?php  echo JText::_( 'CLM_KOMMA' )."<br>".JText::_( 'CLM_ADDRESS1' ); ?>
			</td>
		</tr>
		</table>
<br />
	<input type="submit" value=" <?php echo JText::_('CLUB_LIST_SEND') ?> ">
		<input type="hidden" name="saison" value="<?php echo $sid; ?>" />
		<input type="hidden" name="lid" value="<?php echo $lid; ?>" />
		<input type="hidden" name="man" value="<?php echo $man; ?>" />
		<input type="hidden" name="zps" value="<?php echo $zps; ?>" />
		<input type="hidden" name="stamm" value="<?php echo $stamm; ?>" />
		<input type="hidden" name="ersatz" value="<?php echo $ersatz; ?>" />
		<input type="hidden" name="man_name" value="<?php echo $man_name; ?>" />
		<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</center>
<br>
</div>
</div>
<?php	require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>