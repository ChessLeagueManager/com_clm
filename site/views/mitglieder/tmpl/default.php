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

// Variablen holen
$sid = JRequest::getInt( 'saison', '1' ); 
$zps = JRequest::getVar( 'zps' );


// Login Status prüfen
$clmuser 	= $this->clmuser;
$user		=JFactory::getUser();

$mainframe = JFactory::getApplication();
$link = 'index.php';
	
// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');
// Konfigurationsparameter auslesen
$config = clm_core::$db->config();

if (!$user->get('id')) {
	$msg = JText::_( 'CLUB_LIST_LOGIN' );
	$mainframe->redirect( $link, $msg );
 			}
if ($clmuser[0]->published < 1) {
	$msg = JText::_( 'CLUB_LIST_ACCOUNT' );
	$mainframe->redirect( $link, $msg );
				}
if ($clmuser[0]->zps <> $zps) {
	$msg = JText::_( 'CLUB_LIST_FALSE' );
	$mainframe->redirect( $link, $msg );
				}

if ($user->get('id') > 0 AND  $clmuser[0]->published > 0 AND $clmuser[0]->zps == $zps){

// Variablen initialisieren
$spieler	= $this->spieler;

?>
<script language="JavaScript">
function tableOrdering( order, dir, task )
{
	var form = document.adminForm;
 
	form.filter_order.value = order;
	form.filter_order_Dir.value = dir;
	document.adminForm.submit( task );
}
</script>
<div >
<div id="mitglieder">
    <div class="componentheading"><?php echo JText::_('Mitgliederverwaltung') ?> </div>

    <div class="clmbox">
    <span>Mitgliederliste</span> | 
    <a href="index.php?option=com_clm&amp;view=mitglieder_details&amp;saison=<?php echo $sid; ?>&amp;zps=<?php echo $zps; ?>">Mitglied nachmelden</a> | 
    <a href="#">Mannschaftsf&uuml;hrer</a>  | 
    <a href="index.php?option=com_clm&view=verein&saison=<?php echo $sid; ?>&zps=<?php echo $zps; ?>&layout=vereinsdaten">Vereinsdaten &auml;ndern</a> 
    </div>
    
    <br>
    <form action="index.php?option=com_clm&amp;view=mitglieder&amp;saison=<?php echo $sid; ?>" method="post" name="adminForm">

	<table class="admintable">
    
		<tr class="anfang">
			<th width="5%" class="key" nowrap="nowrap"><?php echo JText::_( 'Nr' ); ?></th>
			<th width="5%" class="key" nowrap="nowrap"><?php echo JHTML::_( 'grid.sort', 'Mgl Nr.', 'id', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<th width="20%" class="key" nowrap="nowrap"><a href="javascript:tableOrdering('name','asc','');"><?php echo JText::_( 'Name' ); ?></a></th>
			<th width="5%" class="key" nowrap="nowrap"><?php echo JHTML::_( 'grid.sort', 'Geburtsjahr', 'Geburtsjahr', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<th width="5%" class="key" nowrap="nowrap"><?php echo JHTML::_( 'grid.sort', 'DWZ', 'dwz', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<th width="5%" class="key" nowrap="nowrap"><?php echo JHTML::_( 'grid.sort', 'ELO', 'elo', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<th width="5%" class="key" nowrap="nowrap" align="center"><?php echo JText::_( 'Status' ); ?></th>
			<th width="5%" class="key" nowrap="nowrap" align="center"><?php echo JText::_( 'Bearbeiten' ); ?></th>
			<th width="5%" class="key" nowrap="nowrap" align="center"><?php echo JText::_( 'Löschen' ); ?></th>
		</tr>
        
		<?php $i = 0; foreach($spieler as $spieler){  ?>
        <tr>
            <td><?php echo $i+1; ?></td>
            <td><?php echo $spieler->id; ?></td>
            <td><?php echo $spieler->name; ?></td>
            <td><?php echo $spieler->Geburtsjahr; ?></td>
            <td><?php if ( $spieler->dwz < 1 ) { echo "-"; } else { echo $spieler->dwz . '&nbsp;(' . $spieler->DWZ_Index .')'; } ?></td>
            <td><?php if ( $spieler->elo < 1 ) { echo "-"; } else { echo $spieler->elo; } ?></td>
            <td><?php echo $spieler->Status; ?></td>
            <td align="center"><a href="index.php?option=com_clm&amp;view=mitglieder_details&amp;saison=<?php echo $sid; ?>&amp;zps=<?php echo $zps; ?>&amp;mglnr=<?php echo $spieler->id; ?>"><img src="<?php echo CLMImage::imageURL('edit_f2.png'); ?>"/></a></td>
            <td align="center">
            <a href="index.php?option=com_clm&amp;view=mitglieder&amp;saison=<?php echo $sid; ?>&amp;zps=<?php echo $zps; ?>&amp;mglnr=<?php echo $spieler->id; ?>&amp;layout=delete"><img src="<?php echo CLMImage::imageURL('cancel_f2.png'); ?>"/></a></td>
        </tr>
        <?php $i++;} ?> 
        
	</table>
    
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="sid" value="<?php echo $sid; ?>" />
    <input type="hidden" name="zps" value="<?php echo $zps; ?>" />
    <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
    <?php echo JHTML::_( 'form.token' ); ?>
    </form>

    <br>
<?php } require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>
</div>
</div>


