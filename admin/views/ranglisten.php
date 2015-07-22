<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

class CLMViewRanglisten
{
public static function setRanglistenToolbar($check)
	{
	JToolBarHelper::title(   JText::_( 'TITLE_RANGLISTE' ), 'generic.png' );	
	JToolBarHelper::publish();
	JToolBarHelper::unpublish();
	$clmAccess = clm_core::$access;
	if ($clmAccess->access('BE_club_edit_ranking') === true) {
	JToolBarHelper::custom( 'copy', 'copy.png', 'copy_f2.png', JText::_('COPY') ); 
	}
	JToolBarHelper::deleteList();
	JToolBarHelper::editList();
	JToolBarHelper::addNew();
	JToolBarHelper::help( 'screen.clm.info' );
	}

public static function Ranglisten ( &$rows, &$lists, &$pageNav, $option )
	{
	$db		= JFactory::getDBO();
	$user 		= JFactory::getUser();
	$jid 		= $user->get('id');
	$sql = " SELECT usertype FROM #__clm_user "
		." WHERE jid =".$jid
		." AND published = 1 "
		;
	$db->setQuery($sql);
	$check = $db->loadObjectList();
	CLMViewRanglisten::setRanglistenToolbar($check);

		//Ordering allowed ?
		$ordering = ($lists['order'] == 'a.ordering');

?>
		<form action="index.php?option=com_clm&section=ranglisten" method="post" name="adminForm" id="adminForm">

		<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JText::_( 'Filter' ); ?>:
		<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_( 'GO' ); ?></button>
		<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_catid').value='0';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'RESET' ); ?></button>
			</td>
			<td nowrap="nowrap">
				<?php
		// eigenes Dropdown Menue
			echo "&nbsp;&nbsp;&nbsp;".$lists['sid'];
			echo "&nbsp;&nbsp;&nbsp;".$lists['vid'];
			?>
			</td>
		</tr>
		</table>

			<table class="adminlist">
			<thead>
				<tr>
					<th width="10">
						#
					</th>
					<th width="10">
						<?php echo $GLOBALS["clm"]["grid.checkall"]; ?>
					</th>
					<th class="title">
						<?php echo JHtml::_('grid.sort',   'RANGLISTE_VEREIN', 'c.vname', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="15%">
						<?php echo JHtml::_('grid.sort',   'RANGLISTE_GRUPPE', 'a.Meldelschluss', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="10%">
						<?php echo JHtml::_('grid.sort',   'RANGLISTE_AUTOR', 'a.rang', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="11%">
						<?php echo JHtml::_('grid.sort',   'RANGLISTE_SAISON', 'c.name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="6%">
						<?php echo JHtml::_('grid.sort',   'JPUBLISHED', 'a.published', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="8%" nowrap="nowrap">
						<?php echo JHtml::_('grid.sort',   'JGRID_HEADING_ORDERING', 'a.ordering', @$lists['order_Dir'], @$lists['order'] ); ?>
						<?php echo JHtml::_('grid.order',  $rows ); ?>
					</th>

					<th width="1%" nowrap="nowrap">
						<?php echo JHtml::_('grid.sort',   'JGRID_HEADING_ID', 'a.id', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="13">
						<?php echo $pageNav->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count( $rows ); $i < $n; $i++) {
				$row = &$rows[$i];

				$link 		= JRoute::_( 'index.php?option=com_clm&section=ranglisten&task=edit&cid[]='. $row->id );

				$checked 	= JHtml::_('grid.checkedout',   $row, $i );
				$published 	= JHtml::_('grid.published', $row, $i );

				?>
				<tr class="<?php echo 'row'. $k; ?>">

					<td align="center">
						<?php echo $pageNav->getRowOffset( $i ); ?>
					</td>

					<td>
						<?php echo $checked; ?>
					</td>

					<td>

								<span class="editlinktip hasTip" title="<?php echo JText::_( 'RANGLISTE_EDIT' ).' ';?>: <?php echo $row->vname; ?>">
							<a href="<?php echo $link; ?>">
								<?php echo $row->vname; ?></a></span>

					</td>

					<td align="center">
						<?php echo $row->gname;?>
					</td>
					<td align="center">
						<?php if ($row->rang == 0) 
							{ ?><img width="16" height="16" src="components/com_clm/images/apply_f2.png" /> <?php }
						else 	{ ?><img width="16" height="16" src="components/com_clm/images/cancel_f2.png" /> <?php }?>
					</td>
					<td align="center">
						<?php echo $row->saison;?>
					</td>

					<td align="center">
						<?php echo $published;?>
					</td>
	<td class="order">
	<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
	<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
					</td>

					<td align="center">
						<?php echo $row->id; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
			</table>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}

public static function setRanglisteToolbar($vname)
	{

		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		if (JRequest::getVar( 'task') == 'edit') { $text = JText::_( 'Edit' );}
			else { $text = JText::_( 'New' );}
		JToolBarHelper::title(  JText::_( 'RANGLISTE' )." $vname : [ ". $text.' ]' );
		JToolBarHelper::custom('sortieren','back.png','edit_f2.png','REORDER',false);
		JToolBarHelper::custom('pruefen','back.png','edit_f2.png','RANGLISTE_CHECK',false);	
		JToolBarHelper::custom('neu_laden','back.png','edit_f2.png','RANGLISTE_LOAD',false);
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
		JToolBarHelper::help( 'screen.clm.edit' );
	}

public static function Rangliste( $spieler, &$row,&$lists,$option,$jid,$vname,$gname,$sname,$cid,$exist,$count,$gid_exist)
	{
		CLMViewRanglisten::setRanglisteToolbar($vname);
		JRequest::setVar( 'hidemainmenu', 1 );
		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'extrainfo' );
		?>

<script language="javascript" type="text/javascript">
	 
	function edit()
	{
	var task 	= document.getElementsByName ( "task") [0];
	var pre_task 	= document.getElementsByName ( "pre_task") [0];
	task.value 	= "add";
	pre_task.value 	= "add";
	document.adminForm.submit();
	}

		 Joomla.submitbutton = function (pressbutton) { 		
		var form = document.adminForm;
		var pre_task = document.getElementsByName ( "pre_task") [0];

		if (pressbutton == 'sortieren') { 
			//alert( 'sort:'+pressbutton+'test2' );
			Sortieren(); return true; }
		if (pressbutton == 'pruefen') { 
	//alert( 'test1'+pressbutton+'test2' );
			Pruefbutton(); 	return true; }
		if (pressbutton == 'neu_laden') { 
			location.reload(); return true; }
		if (pressbutton == 'save') { 
			if (Pruefen()==false) return; }
		if (pressbutton == 'apply') { 
			if (Pruefen()==false) return; }
		if (pre_task.value == 'add') {
		if (pressbutton == 'cancel') {
				submitform( pressbutton ); return; }
			if (pressbutton == 'save') { 
				if (Pruefen()==false) return; }
			if (pressbutton == 'apply') { 
				if (Pruefen()==false) return; }
		// do field validation
		if (form.filter_vid.value == "0") {
			alert( "<?php echo JText::_( 'RANGLISTE_VEREIN_ANGEBEN', true ); ?>" );
		} else if (form.filter_sid.value == "0") {
			alert( "<?php echo JText::_( 'RANGLISTE_SAISON_ANGEBEN', true ); ?>" );
		} else if (form.filter_gid.value == "0") {
			alert( "<?php echo JText::_( 'RANGLISTE_GRUPPE_ANGEBEN', true ); ?>" );
		} else {
			submitform( pressbutton );
		}
		} else {
			if (pressbutton == 'save') { 
				if (Pruefen()==false) return; }
			if (pressbutton == 'apply') { 
				if (Pruefen()==false) return; }
			submitform( pressbutton );
		}
	}

function Zcheck(Wert)
 {
  var chkZ = 1;
  for (i = 0; i < Wert.length; ++i)
    if (Wert.charAt(i) < "0" ||
        Wert.charAt(i) > "9")
      chkZ = -1;
  if (chkZ == -1) {
   return false;
   }
  else {return true};
 }
function Mcheck(Ob)
 {
  if (!Zcheck(Ob.value)) {
    alert("Mannschaft keine Zahl!");
    Ob.focus();
    return false;
   }
 }
function Rcheck(Ob)
 {
  if (Zcheck(Ob.value)) return true;
  var i=Ob.value.indexOf("/")
  if (i>0)
   {
    var M=Ob.value.slice(0,i);
    var R=Ob.value.slice(i+1);
    if ((R=='')||(!Zcheck(M))||(!Zcheck(R)))
     {
      Ob.focus();
      alert("<?php echo JText::_( 'RANGLISTE_MCHECK', true ); ?>");
      return false;
     }
    else
     {
      Ob.value=M*1000+R*1;
      return true;
     }
   }
  Ob.focus();
  alert("Rang ist keine Zahl");
  return false;
 }
function Spielergroesser(S1,S2)
 {
  if (S1[0]>S2[0]) return true; //0=Mannschaft
  if (S1[0]<S2[0]) return false;
  if (S1[1]>S2[1]) return true; //1=Rang
  if (S1[1]<S2[1]) return false;
  if (S1[4]<S2[4]) return true; //4=DWZ
  if (S1[4]>S2[4]) return false;
  if (S1[5]<S2[5]) return true; //5=DWZ-Index
  if (S1[5]>S2[5]) return false;
  if (S1[2]>S2[2]) return true; //2=Name
  if (S1[2]<S2[2]) return false;
  if (S1[3]>S2[3]) return true; //3=Mgl
  return false;
 }
function Spielerschreiben(i)
{
    if (Spieler[i][0]==999)  document.getElementsByName('MA'+i)[0].value=0;
    else document.getElementsByName('MA'+i)[0].value=Spieler[i][0];
    if (Spieler[i][1]==9999) document.getElementsByName('RA'+i)[0].value=0;
    else document.getElementsByName('RA'+i)[0].value=Spieler[i][1];
    document.getElementById('SP'+i).innerHTML=Spieler[i][2];
    document.getElementsByName('MGL'+i)[0].value=Spieler[i][3];
    document.getElementById('MGL'+i).innerHTML=Spieler[i][3];
    document.getElementById('DWZ'+i).innerHTML=Spieler[i][4];
    document.getElementById('DWI'+i).innerHTML=Spieler[i][5];
    <?php if (isset($UsePKZ) AND $UsePKZ == 1) { ?>document.getElementById('PKZ'+i).innerHTML=Spieler[i][6];
    document.getElementsByName('PKZ'+i)[0].innerHTML=Spieler[i][6];
    <?php } ?>
}
function QSort(l,r,Tiefe)
 {
  var i=l;
  var j=r;
  var MittelSpieler=Spieler[Math.floor((l+r)/2)];
  do
   {
    while (Spielergroesser(MittelSpieler,Spieler[i])) i++;
    while (Spielergroesser(Spieler[j],MittelSpieler)) j--;
    if (!(i>j))
     {
      var VS2=Spieler[i];
      Spieler[i]=Spieler[j];
      Spieler[j]=VS2;
      i++;
      j--;
     }
   }
  while (!(i>j));
  if (l<j) QSort(l,j,Tiefe+1);
  if (i<r) QSort(i,r,Tiefe+1); 
 }
function Sortieren()
 {
  Spieler=new Array;
  i=0;
  while (document.getElementsByName('MA'+i)[0]) {
    Spieler[i]=new Array(5)
    Spieler[i][0]=document.getElementsByName('MA'+i)[0].value-0;
    if (Spieler[i][0]==0) Spieler[i][0]=999;
    Spieler[i][1]=document.getElementsByName('RA'+i)[0].value-0;
    if (Spieler[i][1]==0) Spieler[i][1]=9999;
    Spieler[i][2]=document.getElementById('SP'+i).innerHTML;
    Spieler[i][3]=document.getElementById('MGL'+i).innerHTML-0;
    Spieler[i][4]=document.getElementById('DWZ'+i).innerHTML-0;
    Spieler[i][5]=document.getElementById('DWI'+i).innerHTML-0;
    <?php if (isset($UsePKZ) AND $UsePKZ == 1) { ?>Spieler[i][6]=document.getElementById('PKZ'+i).innerHTML-0;<?php } ?>
    i++;    
   }
  QSort(0,i-1,0)
  i=0;
  while (document.getElementsByName('MA'+i)[0])
   {
    Spielerschreiben(i);
    i++;    
   }
 }
function Pruefen()
 {
  Sortieren();
  var Ma=0;
  var Ra=0;
  var Sp=0;
  var Ersatz=1;
  i=0;
  var TempMa01=document.getElementsByName('MA'+i)[0].value;
  if (TempMa01==0) {
	alert('Keine Daten!');
	return false;
  }
  while(document.getElementsByName('MA'+i)[0]) {
    var TempMa=document.getElementsByName('MA'+i)[0].value;
    var TempRa=document.getElementsByName('RA'+i)[0].value;
	if (TempMa==0) {
		break;
	}
	//Hilfsprüfung: nur auf doppelte Einträge
    if ((TempMa==Ma) && (TempRa==Ra)) {
        alert('Doppelte Rangnummer! \n Mannschaft: '+Ma+'  Rang: '+Ra);
		return false;
	} else {
        Ma=TempMa;
        Ra=TempRa;
		i++;
		continue;
	}
	return true;
	//Ende Hilfsprüfung

    if (TempMa==Ma) {
      if (TempRa==(Sp+1)) {
        i++;
        Sp++;
        continue; }
      else if (TempRa==(1000*Ma+Ersatz)) {
        i++;
        Ersatz++;
        continue; }
      else {
        if (window.confirm('Die Rangnummer von Spieler '+document.getElementsByName('SP'+i)[0].innerHTML+' ist '+TempRa+'!\nErwartet wird '+(Sp+1)+'!\nFortsetzen?')) {
          Sp=TempRa;
          i++;
          continue; }
        document.getElementsByName('RA'+i)[0].focus();
        return false; } }
    else if (TempMa==(Ma+1)) {
      Ma++;
      Ersatz=1;
      if (TempRa!=(Sp+1)) {
        if (window.confirm('Der erste Spieler ('+document.getElementsByName('SP'+i)[0].innerHTML+') der '+Ma+'.Mannschaft hat Rangnummer '+TempRa+'!\nErwartet wird '+(Sp+1)+'\nFortsetzen?')) {
          Sp=TempRa;
          i++;
          continue; }
        document.getElementsByName('RA'+i)[0].focus();
        return false; }
      else {
        Sp++;
        i++;
        continue; } }
    else if (TempMa==90) {
      Ma=90;
      Sp=90000;
      continue; }
    else if (TempMa==99) {
      Ma=99;
      Sp=99000;
      continue; }
    else {
      if (Ma!=0) var Text='Die Mannschaftsnummern müssen aufsteigend sein!\nLetzte Mannschaftsnummer war '+Ma;
      else var Text='Die erste Mannschaft muß die Nummer 1 haben!';
      if (window.confirm(Text+'\nFortsetzen?')) {
        Sp=TempRa;
        i++;
        continue; }
      document.getElementsByName('MA'+i)[0].focus();
      return false; }
    if (window.confirm('Unbekannter Aufstellungsfehler!\nMannschaft: '+TempMa+'\nRangnummer: '+TempRa+'\nSpielername: "'+document.getElementsByName('SP'+i)[0].innerHTML+'"\nFortsetzen?')) {
      Sp=TempRa;
      i++;
      continue; }
    document.getElementsByName('MA'+i)[0].focus();
    return false; }
  return true;
 } // end: function Pruefen()
function Pruefbutton()
 {
  if (Pruefen()==true) alert('Alles in Ordnung');
 }

 
</script>

<?php if ($exist AND JRequest::getVar( 'task') == "add") { ?>

	<div class="col width-100">
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'RANGLISTE_TIP' ); ?></legend>

	<h1><?php echo JText::_( 'RANGLISTE_TIP_LINE1' ); ?></h1>
	<h2><?php echo JText::_( 'RANGLISTE_TIP_LINE2' ); ?></h2>
	<br>

	</fieldset>
	</div>

<?php } ?>
<form action="index.php" method="post" name="adminForm" id="adminForm">

<div>
		<div class="width-50 fltlft">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'RANGLISTE_DETAILS' ); ?></legend>

		<table class="admintable">
		<tr>
			<td class="key" nowrap="nowrap"><label for="filter_vid"><?php echo JText::_( 'RANGLISTE_VEREIN' ).' : '; ?></label>
			</td>
			<td>
			<?php if (JRequest::getVar( 'task') == 'edit' ) { echo $vname; }
				else { echo $lists['vid']; } ?>
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="filter_sid"><?php echo JText::_( 'RANGLISTE_SAISON' ).' : '; ?></label>
			</td>
			<td>
			<?php if (JRequest::getVar( 'task') == 'edit' ) { echo $sname; }
				else { echo $lists['sid']; } ?>
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="filter_gid"><?php echo JText::_( 'RANGLISTE_GRUPPE' ).' : '; ?></label>
			</td>
			<td>
			<?php	if (JRequest::getVar( 'task') == 'edit' ) { echo $gname;}
				else { echo $lists['gruppe'];} ?>
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="published"><?php echo JText::_( 'JPUBLISHED' ).' : '; ?></label>
			</td>
			<td><fieldset class="radio">
			<?php echo $lists['published']; ?>
			</fieldset></td>
		</tr>

		</table>
		</fieldset>
		</div>

 <div class="width-50 fltrt">
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'REMARKS' ); ?></legend>
	<table class="adminlist">
	<legend><?php echo JText::_( 'REMARKS_PUBLIC' ); ?></legend>
	<tr>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bemerkungen" id="bemerkungen" cols="40" rows="1" style="width:90%"><?php echo str_replace('&','&amp;',$row->bemerkungen);?></textarea>
	</td>
	</tr>
	</table>
	<table class="adminlist">
	<legend><?php echo JText::_( 'REMARKS_INTERNAL' ); ?></legend>
	<tr>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bem_int" id="bem_int" cols="40" rows="1" style="width:90%"><?php echo str_replace('&','&amp;',$row->bem_int);?></textarea>
	</td>
	</tr>
	</table>
  </fieldset>
  </div>
</div>

<?php if ((!$exist AND JRequest::getVar( 'task') == "add") OR (JRequest::getVar( 'task') == "edit")) {
	$mainframe	= JFactory::getApplication();

	$filter_vid	= $mainframe->getUserStateFromRequest( "$option.filter_vid",'filter_vid',0,'int' );
	$filter_sid	= $mainframe->getUserStateFromRequest( "$option.filter_sid",'filter_sid',0,'int' );
	$filter_gid	= $mainframe->getUserStateFromRequest( "$option.filter_gid",'filter_gid',0,'int' );

	if(JRequest::getVar( 'task') == "add" AND (!$spieler or !$gid_exist or !$filter_gid)) { ?>
	<br><br><br><br><br><br><br><br><br><br>
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'RANGLISTE_TIP' ); ?></legend>
	<?php if (!$gid_exist AND $filter_sid AND $filter_vid){ ?>
	<h2><?php echo JText::_( 'RANGLISTE_TIP_LINE11' ); ?></h2>
	<br>
	<?php } else { ?>
	<?php if (!$spieler AND $filter_vid AND $filter_sid AND $filter_gid ) { ?>
	<h2><?php echo JText::_( 'RANGLISTE_TIP_LINE21' ); ?></h2>
	<?php } else { ?>
	<h2><?php echo JText::_( 'RANGLISTE_TIP_LINE31' ); ?></h2>
	<br>
	<?php } ?>
	</fieldset>
	
 <?php }} else { ?>
<br><br><br><br><br><br><br><br><br><br>
<div>
<div class="width-50 fltlft">
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'RANGLISTE_PLATZ' ).' 1 - '.(1+intval(((count($spieler))/2))); ?></legend>
	
	<table class="admintable">

	<tr>
		<td width="8%" class="key" nowrap="nowrap"><?php echo JText::_( 'RANGLISTE_M_NR' ); ?></td>
		<td width="10%" class="key" nowrap="nowrap"><?php echo JText::_( 'RANGLISTE_RANG' ); ?></td>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'RANGLISTE_NAME' ); ?></td>
		<td width="7%" class="key" nowrap="nowrap"><?php echo JText::_( 'RANGLISTE_MGL_NR' ); ?></td>
		<td width="7%" class="key" nowrap="nowrap"><?php echo JText::_( 'RANGLISTE_PKZ' ); ?></td>
		<td colspan="2" width="10%" class="key" nowrap="nowrap"><?php echo JText::_( 'RANGLISTE_DWZ' ); ?></td>
	</tr>

<?php 

if (JRequest::getVar( 'task') == 'edit' ) {

	$rang	= array();
	$cnt	= 0;
for ($x=(count($spieler)-$count); $x < count($spieler); $x++) {
	$rang_x	= array($cnt => $x);
	$rang	= $rang + $rang_x;
	$cnt++;
	}
for ($x=0; $x < (count($spieler)-$count); $x++) {
	$rang_x	= array($cnt => $x);
	$rang	= $rang + $rang_x;
	$cnt++;
	}

	for($x=0; $x < (1+intval((count($spieler))/2)); $x++) { ?>

	<input type="hidden" name="PKZ<?php echo $x; ?>" value="<?php echo $spieler[$rang[$x]]->PKZ; ?>" />
	<input type="hidden" name="MGL<?php echo $x; ?>" value="<?php echo $spieler[$rang[$x]]->Mgl_Nr; ?>" />

	<tr>
	<td class="key" nowrap="nowrap">
		<input type="text" name="MA<?php echo $x ?>" size="3" maxLength="3" value="<?php echo $spieler[$rang[$x]]->man_nr; ?>" onChange="Mcheck(this)">
	</td>
	<td class="key" nowrap="nowrap">
	<input type="text" name="RA<?php echo $x ?>" size="5" maxLength="5" value="<?php echo $spieler[$rang[$x]]->Rang; ?>" onChange="Rcheck(this)">
	</td>
	<td id="SP<?php echo $x; ?>" name="SP<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$rang[$x]]->Spielername; ?></td>
	<td id="MGL<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$rang[$x]]->Mgl_Nr; ?></td>
	<td id="PKZ<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$rang[$x]]->PKZ; ?></td>
	<td id="DWZ<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$rang[$x]]->DWZ; ?></td>
	<td id="DWI<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$rang[$x]]->DWZ_Index; ?></td>
	</tr>

<?php }} else {

	for($x=0; $x < (1+intval((count($spieler))/2)); $x++) { ?>

	<input type="hidden" name="PKZ<?php echo $x; ?>" value="<?php echo $spieler[$x]->PKZ; ?>" />
	<input type="hidden" name="MGL<?php echo $x; ?>" value="<?php echo $spieler[$x]->Mgl_Nr; ?>" />

	<tr>
	<td class="key" nowrap="nowrap">
		<input type="text" name="MA<?php echo $x ?>" size="3" maxLength="3" <?php if(isset($spieler[$x]->man_nr)) { ?> value="<?php echo $spieler[$x]->man_nr; ?>"<?php } ?> onChange="Mcheck(this)">
	</td>
	<td class="key" nowrap="nowrap">
	<input type="text" name="RA<?php echo $x ?>" size="5" maxLength="5" <?php if(isset($spieler[$x]->Rang)) { ?> value="<?php echo $spieler[$x]->Rang; ?>" <?php } ?> onChange="Rcheck(this)">
	</td>
	<td id="SP<?php echo $x; ?>" name="SP<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->Spielername; ?></td>
	<td id="MGL<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->Mgl_Nr; ?></td>
	<td id="PKZ<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->PKZ; ?></td>
	<td id="DWZ<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->DWZ; ?></td>
	<td id="DWI<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->DWZ_Index; ?></td>
	</tr>

<?php }} ?>
	</table>
  </fieldset>
  </div>


<div class="width-50 fltlft">
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'RANGLISTE_PLATZ' ).' '.intval(((count($spieler))/2)+2)." - ".count($spieler); ?></legend>

	<table class="admintable">

	<tr>
		<td width="8%" class="key" nowrap="nowrap"><?php echo JText::_( 'RANGLISTE_M_NR' ); ?></td>
		<td width="10%" class="key" nowrap="nowrap"><?php echo JText::_( 'RANGLISTE_RANG' ); ?></td>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'RANGLISTE_NAME' ); ?></td>
		<td width="7%" class="key" nowrap="nowrap"><?php echo JText::_( 'RANGLISTE_MGL_NR' ); ?></td>
		<td width="7%" class="key" nowrap="nowrap"><?php echo JText::_( 'RANGLISTE_PKZ' ); ?></td>
		<td colspan="2" width="10%" class="key" nowrap="nowrap"><?php echo JText::_( 'RANGLISTE_DWZ' ); ?></td>
	</tr>
	
<?php
if (JRequest::getVar( 'task') == 'edit' ) {

	for($x=(1+intval((count($spieler))/2)); $x < count($spieler); $x++) { ?>

	<input type="hidden" name="PKZ<?php echo $x; ?>" value="<?php echo $spieler[$rang[$x]]->PKZ; ?>" />
	<input type="hidden" name="MGL<?php echo $x; ?>" value="<?php echo $spieler[$rang[$x]]->Mgl_Nr; ?>" />

	<tr>
	<td class="key" nowrap="nowrap">
		<input type="text" name="MA<?php echo $x ?>" size="3" maxLength="3" value="<?php echo $spieler[$rang[$x]]->man_nr; ?>" onChange="Mcheck(this)">
	</td>
	<td class="key" nowrap="nowrap">
	<input type="text" name="RA<?php echo $x ?>" size="5" maxLength="5" value="<?php echo $spieler[$rang[$x]]->Rang; ?>" onChange="Rcheck(this)">
	</td>
	<td id="SP<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$rang[$x]]->Spielername; ?></td>
	<td id="MGL<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$rang[$x]]->Mgl_Nr; ?></td>
	<td id="PKZ<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$rang[$x]]->PKZ; ?></td>
	<td id="DWZ<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$rang[$x]]->DWZ; ?></td>
	<td id="DWI<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$rang[$x]]->DWZ_Index; ?></td>
	</tr>

<?php }} else {

	for($x=(1+intval((count($spieler))/2)); $x < count($spieler); $x++) { ?>

	<input type="hidden" name="PKZ<?php echo $x; ?>" value="<?php echo $spieler[$x]->PKZ; ?>" />
	<input type="hidden" name="MGL<?php echo $x; ?>" value="<?php echo $spieler[$x]->Mgl_Nr; ?>" />

	<tr>
	<td class="key" nowrap="nowrap">
		<input type="text" name="MA<?php echo $x ?>" size="3" maxLength="3" <?php if(isset($spieler[$x]->man_nr)) { ?> value="<?php echo $spieler[$x]->man_nr; ?>"<?php } ?> onChange="Mcheck(this)">
	</td>
	<td class="key" nowrap="nowrap">
	<input type="text" name="RA<?php echo $x ?>" size="5" maxLength="5" <?php if(isset($spieler[$x]->Rang)) { ?> value="<?php echo $spieler[$x]->Rang; ?>" <?php } ?> onChange="Rcheck(this)">
	</td>
	<td id="SP<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->Spielername; ?></td>
	<td id="MGL<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->Mgl_Nr; ?></td>
	<td id="PKZ<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->PKZ; ?></td>
	<td id="DWZ<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->DWZ; ?></td>
	<td id="DWI<?php echo $x; ?>" class="key" nowrap="nowrap">
		<?php echo $spieler[$x]->DWZ_Index; ?></td>
	</tr>

<?php }} ?>
	</table>

  </fieldset>
  </div>
  </div>

<?php }} ?>
		<div class="clr"></div>
		<input type="hidden" name="section" value="ranglisten" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />

		<input type="hidden" name="count" value="<?php echo count($spieler); ?>" />
		<input type="hidden" name="zps" value="<?php echo $spieler[0]->ZPS; ?>" />
		<input type="hidden" name="sid" value="<?php echo $spieler[0]->sid; ?>" />
		<input type="hidden" name="gid" value="<?php echo $row->gid; ?>" />
		<input type="hidden" name="exist" value="<?php echo $exist; ?>" />

		<input type="hidden" name="pre_task" value="<?php echo JRequest::getVar( 'task'); ?>" />
		<input type="hidden" name="task" value="edit" />

		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}
}
?>
