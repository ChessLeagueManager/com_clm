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

defined('_JEXEC') or die('Restricted access');

$sid	= JRequest::getInt('saison','1');
$itemid	= JRequest::getInt('Itemid','1');
$urlzps	= JRequest::getVar('zps');
$zps	= $this->zps;
$liga	= $this->liga;
$saisons	 	= $this->saisons;
$vereinsliste 	= $this->vereinsliste;

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');
// require_once(JPATH_COMPONENT.DS.'includes'.DS.'image_path.php');

// Konfigurationsparameter auslesen
$config	= &JComponentHelper::getParams( 'com_clm' );
$fixth_dwz = $config->get('fixth_dwz',1);

	// Browsertitelzeile setzen
	$doc =& JFactory::getDocument();
	$daten['title'] = JText::_('CLUB_RATING').' '.$liga[0]->Vereinname;
	if ($doc->_type != "raw") $doc->setHeadData($daten);
	
?>
<Script language="JavaScript">
<!-- Vereinsliste
function goto(form) { var index=form.select.selectedIndex
if (form.select.options[index].value != "0") {
location=form.select.options[index].value;}}
//-->

function tableOrdering( order, dir, task )
{
	var form = document.adminForm;
 
	form.filter_order.value = order;
	form.filter_order_Dir.value = dir;
	document.adminForm.submit( task );
}
</script>

<div id="clm">
<div id="dwz">
<div class="componentheading">
<?php echo JText::_('CLUB_RATING') ?> ::: <?php echo $liga[0]->Vereinname; ?>
<div id="pdf">

<?php
echo CLMContent::createPDFLink('dwz', JText::_('PDF_CLUBRATING'), array('layout' => 'dwz', 'saison' => $sid, 'zps' => $urlzps));
?>
</div>
</div>
<div class="clr"></div>

    <div class="clmbox">
        <a href="index.php?option=com_clm&view=verein&saison=<?php echo $sid; ?>&zps=<?php echo $urlzps; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo JText::_('TEAM_DETAILS') ?></a> | <a href="index.php?option=com_clm&view=vereinsliste&saison=<?php echo $sid; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo JText::_('CLUBS_LIST') ?></a>
    	
        <span class="right">
        	<form name="form1">
            	<select name="select" onchange="goto(this.form)" class="selectteam">
                	<?php foreach ($saisons as $saisons) { ?>
                    	<option value="<?php echo JURI::base(); ?>index.php?option=com_clm&view=dwz&saison=<?php echo $saisons->id; ?>&zps=<?php echo $urlzps; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"
                        <?php if ($saisons->id == $sid) { echo 'selected="selected"'; } ?>><?php echo $saisons->name; ?> </option>
                    <?php } ?>
                </select>
            </form>
        </span>
                    
        <span class="right">
            <form name="form1">
                <select name="select" onchange="goto(this.form)" class="selectteam">
                <option value=""><?php echo JText::_('CLUB_SELECTTEAM') ?></option>
                <?php  $cnt = 0;   foreach ($vereinsliste as $vereinsliste) { $cnt++;?>
                 <option value="<?php echo JURI::base(); ?>index.php?option=com_clm&view=dwz&saison=<?php echo $sid; ?>&zps=<?php echo $vereinsliste->zps; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"
                <?php if ($vereinsliste->zps == $urlzps) { echo 'selected="selected"'; } ?>><?php echo $vereinsliste->name; ?></option>
                <?php } ?>
                </select>
            </form>
        </span>
    <div class="clear"></div>
    
    </div>

<br>

<?php
// Prüfen ob ZPS vorhanden ist
 if (!$liga[0]->Vereinname) { 
echo "<br>". CLMContent::clmWarning(JText::_('CLUB_UNKNOWN'))."<br>";
 } else { 
 
 if ($itemid <>'') {  $postlink = '&saison=' . $sid . '&zps=' . $urlzps . '&Itemid='.$itemid; }
 else { $postlink = '&saison=' . $sid . '&zps=' . $urlzps ;  }
 ?>

<form id="adminForm" action="<?php echo JRoute::_( 'index.php?option=com_clm&view=dwz' . $postlink );?>" method="post" name="adminForm">
<table cellpadding="0" cellspacing="0" id="dwz" <?php if ($fixth_dwz =="1") { ?>class="tableWithFloatingHeader"<?php } ?>>

    <tr>
    <th class="dwz_1"><?php echo JText::_('CLUB_NR') ?></th>
    <th class="dwz_2"><a href="javascript:tableOrdering('Mgl_Nr','asc','');"><?php echo JText::_('CLUB_MEMBER') ?></a></th>
    <th class="dwz_3"><a href="javascript:tableOrdering('Spielername','asc','');"><?php echo JText::_('CLUB_MEMBER_NAME') ?></a></th>
    <th class="dwz_4"><?php echo JHTML::_( 'grid.sort', 'CLUB_MEMBER_STATUS', 'Status', $this->lists['order_Dir'], $this->lists['order']); ?></th>
    <th class="dwz_5"><?php echo JHTML::_( 'grid.sort', 'CLUB_MEMBER_GESCHL', 'Geschlecht', $this->lists['order_Dir'], $this->lists['order']); ?></th>
    <th class="dwz_6"><?php echo JHTML::_( 'grid.sort', 'CLUB_MEMBER_RATING', 'DWZ', $this->lists['order_Dir'], $this->lists['order']); ?></th>
    <th class="dwz_7"><?php echo JHTML::_( 'grid.sort', 'CLUB_MEMBER_ELO', 'FIDE_Elo', $this->lists['order_Dir'], $this->lists['order']); ?></th>
    <th class="dwz_8"><?php echo JText::_('CLUB_MEMBER_TITEL') ?></th>
    </tr>

	<?php
    $x= 1;
    // Auslesen der Datensaetze im Array
    foreach ($zps as $zps) {
    
    if ($x%2 == 0) { $zeilenr = 'zeile2'; }
	else { $zeilenr = 'zeile1'; } ?>
    <tr class="<?php echo $zeilenr; ?>">
    <td class="dwz_1"><?php echo $x; ?></td>
    <td class="dwz_2"><?php echo $zps->Mgl_Nr; ?></td>
    <td class="dwz_3"><a href="index.php?option=com_clm&view=spieler&saison=<?php echo $sid; ?>&zps=<?php echo $zps->ZPS; ?>&mglnr=<?php echo $zps->Mgl_Nr; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php  echo $zps->Spielername; ?></a></td>
    <td class="dwz_4"><?php echo $zps->Status; ?></td>
    <td class="dwz_5"><?php echo $zps->Geschlecht; ?></td>
    <!---td class="dwz_6"><a href="http://schachbund.de/dwz/db/spieler.html?zps=<?php echo $zps->ZPS; ?>-<?php echo $zps->Mgl_Nr; ?>" target="_blank"><?php echo $zps->DWZ; ?></a> - <?php echo $zps->DWZ_Index; ?></td--->
    <td class="dwz_6"><a href="http://schachbund.de/spieler.html?zps=<?php echo $zps->ZPS; ?>-<?php echo $zps->Mgl_Nr; ?>" target="_blank"><?php echo $zps->DWZ; ?></a> - <?php echo $zps->DWZ_Index; ?></td>
    <td class="dwz_7"><?php if ( $zps->FIDE_Elo == 0 ) { echo "-"; } else { echo '<a href="http://ratings.fide.com/card.phtml?event=' . $zps->FIDE_ID . '" target="_blank">' . $zps->FIDE_Elo .'</a>'; } ?></td>
    <td class="dwz_8"><?php echo $zps->FIDE_Titel; ?></td>
    </tr>
    
<?php $x++; }} ?>
</table>

<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>

<!---div class="hint">DWZ Liste: <a href="http://schachbund.de/dwz/db/verein.html?zps=<?php echo $urlzps; ?>" target="_blank">http://schachbund.de/dwz/db/verein.html?zps=<?php echo $urlzps; ?></a></div--->
<div class="hint">DWZ Liste: <a href="http://schachbund.de/verein.html?zps=<?php echo $urlzps; ?>" target="_blank">http://schachbund.de/verein.html?zps=<?php echo $urlzps; ?></a></div>   
<br>

<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>

<div class="clr"></div>
</div>
</div>