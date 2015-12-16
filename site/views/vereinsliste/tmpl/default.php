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

defined('_JEXEC') or die('Restricted access');

$sid			= JRequest::getInt('saison','1');
$zps			= JRequest::getVar('zps');
$itemid			= JRequest::getInt('Itemid','1');
$vereinsliste 	= $this->vereinsliste;
$vereine 		= $this->vereine;
$verband 		= $this->verband;
//$saisonid 		= $this->saisonid;
$saisons	 	= $this->saisons;

$config					= clm_core::$db->config();
$fe_vereinsliste_vs 	= $config->fe_vereinsliste_vs;
$fe_vereinsliste_hpage 	= $config->fe_vereinsliste_hpage;
$fe_vereinsliste_dwz 	= $config->fe_vereinsliste_dwz;
$fe_vereinsliste_elo 	= $config->fe_vereinsliste_elo;

// Browsertitelzeile setzen
$doc =JFactory::getDocument();
$doc->setTitle(JText::_('CLUBS_LIST'));

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

require_once(JPATH_COMPONENT.DS.'includes'.DS.'googlemaps.php');

// Sortierung
if ($itemid <>'') { $plink = '&saison=' . $sid . '&Itemid='.$itemid; }
else { $plink = '&saison=' . $sid ;  }
	
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
</SCRIPT>

<div >
<div id="vereinsliste">

<div class="componentheading"><?php echo JText::_('CLUBS_LIST'); ?></div>
    <div class="clmbox">
                <span class="right">
                <form name="form1">
                    <select name="select" onchange="goto(this.form)" class="selectteam">
						<?php foreach ($saisons as $saisons) { ?>
                            <option value="<?php echo JURI::base(); ?>index.php?option=com_clm&view=vereinsliste&saison=<?php echo $saisons->id; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"
                            <?php if ($saisons->id == $sid) { echo 'selected="selected"'; } ?>><?php echo $saisons->name; ?> </option>
                        <?php } ?>
                    </select>
                </form>
                </span>
            <span class="right">
                <form name="form1">
                    <select name="select" onchange="goto(this.form)" class="selectteam">
                    <option value=""><?php echo JText::_('CLUB_SELECTTEAM') ?></option>
                    <?php  $cnt = 0;
                     foreach ($vereinsliste as $vereinsliste) { $cnt++; if ($vereinsliste->sid == $sid) {?>
                    <option value="<?php echo JURI::base(); ?>index.php?option=com_clm&view=verein&saison=<?php echo $sid; ?>&zps=<?php echo $vereinsliste->zps; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $vereinsliste->name; ?></option>
                    <?php }} ?>
                    </select>
                </form>
            </span>
    <div class="clear"></div>
</div>
<br />

<form id="adminForm" action="<?php echo JRoute::_( 'index.php?option=com_clm&view=vereinsliste' . $plink ) ;?>" method="post" name="adminForm">
<table cellpadding="0" cellspacing="0" class="vereinsliste">
	<tr class="anfang">
 <!--       <th rowspan="2" class="col_1"><a href="javascript:tableOrdering('name','asc','');"><?php echo JText::_('CLUBS_LIST_NAME') ?></a></th> -->
        <th rowspan="2" class="col_1"><?php echo JHTML::_( 'grid.sort', 'CLUBS_LIST_NAME', 'NAME', $this->lists['order_Dir'], $this->lists['order']); ?></th>
        <?php if ($fe_vereinsliste_vs == 1) { ?><th rowspan="2" class="col_2"><?php echo JText::_('CLUB_CHIEF') ?></th><?php } ?>
        <?php if ($fe_vereinsliste_hpage == 1) { ?><th rowspan="2" class="col_3"><?php echo JText::_('CLUB_HOMEPAGE') ?></th><?php } ?>
        <th colspan="4" class="col"><?php echo JHTML::_( 'grid.sort', 'CLUBS_LIST_MEMBER', 'MGL_SUM', $this->lists['order_Dir'], $this->lists['order']); ?></th>
        <?php if ($fe_vereinsliste_dwz == 1) { ?>
            <th rowspan="2" class="col_7"><?php echo JHTML::_( 'grid.sort', 'CLUBS_LIST_DWZAV', 'DWZ', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		<?php } ?>
        <?php if ($fe_vereinsliste_elo == 1) { ?>
            <th rowspan="2" class="col_8"><?php echo JHTML::_( 'grid.sort', 'CLUBS_LIST_ELOAV', 'FIDE_Elo', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		<?php } ?>
	</tr>
	<tr>
      <th class="col_6"><?php echo JText::_('') ?></th>
	  <th class="col_4"><?php echo JHTML::_( 'grid.sort', 'CLUBS_LIST_MEMBERM', 'MGL_M', $this->lists['order_Dir'], $this->lists['order']); ?></th>
      <th class="col_5"><?php echo JHTML::_( 'grid.sort', 'CLUBS_LIST_MEMBERW', 'MGL_W', $this->lists['order_Dir'], $this->lists['order']); ?></th>
      <th class="col_5"><?php echo JHTML::_( 'grid.sort', 'p', 'MGL_P', $this->lists['order_Dir'], $this->lists['order']); ?></th>
  	</tr>
	<?php  
         for ($z = 0; $z < count ( $vereine ); $z++) { 
		 
			// Verband
			if (isset($verband[$z-1]) AND ((!isset($verband[$z-1]->LV)) OR (isset($verband[$z]) && $verband[$z]->LV != $verband[$z-1]->LV) )) { 
			echo '<tr><td colspan="9" class="noborder">&nbsp;</td></tr>';
			echo '<tr class="anfang"><td colspan="9">'. $verband[$z]->Verbandname .'</td></tr>';
			}
			
			// Verbände / Bezirke
			if ( (!isset($vereine[$z-1]->Verband)) OR (( $vereine[$z]->Verband != $vereine[$z-1]->Verband ) AND ( $vereine[$z]->Verbandname <> $verband[$z]->Verbandname )) ) { 
			echo '<tr class="anfang"><td colspan="9">'. $vereine[$z]->Verbandname .'</td></tr>'; }
			
		 ?>
    <tr>
        <td class="col_1"><a href="index.php?option=com_clm&view=verein&saison=<?php echo $sid; ?>&zps=<?php echo $vereine[$z]->zps; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $vereine[$z]->name; ?></a></td>
        <?php if ($fe_vereinsliste_vs == 1) { ?><td class="col_2"><?php // Wenn Email
		 if (  $vereine[$z]->vs_mail ==! false ) { echo '<a href="mailto:'.$vereine[$z]->vs_mail.'">'.$vereine[$z]->vs.'</a>'; }
		 else { echo $vereine[$z]->vs; }
		?></td><?php } ?>
        <?php if ($fe_vereinsliste_hpage == 1) { ?><td class="col_3"><a href="<?php echo $vereine[$z]->homepage; ?>" target="_blank"><?php echo str_replace ( "http://" , "" , $vereine[$z]->homepage ); ?></a></td><?php } ?>
        <td class="col_4"><a href="index.php?option=com_clm&view=dwz&saison=<?php echo $sid; ?>&zps=<?php echo $vereine[$z]->zps; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $vereine[$z]->MGL_SUM; ?></a></td>
        <td class="col_5"><?php echo $vereine[$z]->MGL_M; ?></td>
        <td class="col_6"><?php echo $vereine[$z]->MGL_W; ?></td>
        <td class="col_6"><?php echo $vereine[$z]->MGL_P; ?></td>
        <?php if ($fe_vereinsliste_dwz == 1) { ?><td class="col_7"><?php echo round($vereine[$z]->DWZ); ?> (<?php echo round($vereine[$z]->DWZ_SUM); ?>)</td><?php } ?>
        <?php if ($fe_vereinsliste_elo == 1) { ?>
        <td class="col_8">
        <?php if ( $vereine[$z]->FIDE_Elo == 0 ) { echo "-"; } 
		else { echo round($vereine[$z]->FIDE_Elo) . '(' . $vereine[$z]->ELO_SUM .')' ; } ?>
        </td>
		<?php } ?>
    </tr>
    <?php } ?>
</table>

<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>

</div>
</div>
