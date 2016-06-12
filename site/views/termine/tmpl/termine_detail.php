<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Fjodor Schäfer
 * @email ich@vonfio.de
*/

defined('_JEXEC') or die('Restricted access');

// Vorbereitung Rücksprung
$referer = $_SERVER['HTTP_REFERER']; 
$sreferer = strpos($referer, 'termine_detail');
// current season
	$db = JFactory::getDbo();
	$db->setQuery("SELECT id FROM #__clm_saison WHERE published = 1 AND archiv = 0 ORDER BY name DESC LIMIT 1 ");
	$sid = $db->loadObject()->id;
$nr			= JRequest::getVar('nr');
$itemid		= JRequest::getInt('Itemid');
$categoryid	= JRequest::getInt('categoryid',0);

$termine_detail	= $this->termine_detail;

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

// Browsertitelzeile setzen
$doc =JFactory::getDocument();
$doc->setTitle(JText::_('TERMINE_HEAD'));
           
 // Datumsberechnungen
$startdate[0] = strtotime($termine_detail[0]->startdate);
$enddate[0] = strtotime($termine_detail[0]->enddate);
    
    $arrWochentag = array( 
		"Monday" => JText::_('MOD_CLM_TERMINE_T01'), 
		"Tuesday" => JText::_('MOD_CLM_TERMINE_T02'), 
		"Wednesday" => JText::_('MOD_CLM_TERMINE_T03'), 
		"Thursday" => JText::_('MOD_CLM_TERMINE_T04'), 
		"Friday" => JText::_('MOD_CLM_TERMINE_T05'), 
		"Saturday" => JText::_('MOD_CLM_TERMINE_T06'), 
		"Sunday" => JText::_('MOD_CLM_TERMINE_T07') );
       
?>
<div >
<div id="termine">
    <div class="componentheading"><?php echo JText::_('TERMINE_HEAD') ?></div>
    
	<!-- Navigationsmenu -->
    <?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu.php'); ?>
    <br />
    
    <?php if ($termine_detail[0]->published == 0) {	?>
    <div class="wrong"><?php echo JText::_('NO_ROUNDS') ?></div>
    <?php  } else {  ?>
    	<table>
        	<tr>
            	<td width="200"><?php echo JText::_('TERMINE_TITLE') ?></td>
            	<td><?php echo $termine_detail[0]->name; ?></td>
            </tr>
            <?php if ($termine_detail[0]->hostname <>'') { ?>
        	<tr>
            	<td><?php echo JText::_('TERMINE_HOST') ?></td>
				<?php if (strlen($termine_detail[0]->host) == 5) { ?>
            	<td><a href="index.php?option=com_clm&view=verein&saison=<?php echo $sid; ?>&zps=<?php echo $termine_detail[0]->host; if ($itemid <>'') { echo "&Itemid=". $itemid; } ?>"><?php echo $termine_detail[0]->hostname; ?></a></td>
        		<?php } else { ?>
            	<td><?php echo $termine_detail[0]->hostname; ?></td>
        		<?php } ?>
            </tr>
            <?php } if ($termine_detail[0]->address <> '') { ?>
        	<tr>
            	<td><?php echo JText::_('TERMINE_ADRESS') ?></td>
            	<td><?php echo $termine_detail[0]->address; ?></td>
            </tr>
            <?php } if ($termine_detail[0]->event_link <>'') { ?>
        	<tr>
            	<td><?php echo JText::_('TERMINE_EVENT_LINK') ?></td>
            	<td><a href="<?php echo $termine_detail[0]->event_link; ?>"><?php echo $termine_detail[0]->event_link; ?></a></td>
            </tr>
            <?php } if ($termine_detail[0]->category <> '') { ?>
        	<tr>
            	<td><?php echo JText::_('TERMINE_KATEGORIE') ?></td>
            	<td><?php echo $termine_detail[0]->category; ?></td>
            </tr>
            <?php } ?>
        	<tr>
            	<td><?php echo JText::_('TERMINE_DATUM') ?></td>
            	<td>
					<?php  echo $arrWochentag[date("l",$startdate[0])]. ",&nbsp;". JHTML::_( 'date', $termine_detail[0]->startdate, JText::_('DATE_FORMAT_CLM'));
							if ($termine_detail[0]->starttime != '00:00:00') echo '&nbsp;&nbsp;'.substr($termine_detail[0]->starttime,0,5).'&nbsp;Uhr'; 
							if ($termine_detail[0]->allday == 1) echo '&nbsp;&nbsp;'.'&nbsp;ganzt&auml;gig'; 
					if ($termine_detail[0]->enddate != 0) { 
						if (($termine_detail[0]->enddate != $termine_detail[0]->startdate) or ($termine_detail[0]->endtime != '00:00:00') or ($termine_detail[0]->noendtime == 1))	echo "&nbsp;-&nbsp;";  
						if ($termine_detail[0]->enddate != $termine_detail[0]->startdate) echo $arrWochentag[date("l",$enddate[0])]. ",&nbsp;". JHTML::_( 'date', $termine_detail[0]->enddate, JText::_('DATE_FORMAT_CLM')).'&nbsp;&nbsp;';  
						if ($termine_detail[0]->endtime != '00:00:00') {
							if ($termine_detail[0]->noendtime == 1) echo 'ca.&nbsp;'; 
							echo substr($termine_detail[0]->endtime,0,5).'&nbsp;Uhr';  
						} else {
							if ($termine_detail[0]->noendtime == 1) echo 'Ende offen'; }
					} ?></td>
            </tr>
        </table>
        
		<?php if ($termine_detail[0]->beschreibung <>"") {	?>
        <table>
        	<tr>
            	<td colspan="2"><?php echo JText::_('TERMINE_DESC') ?></td>
            </tr>
        	<tr>
            	<td colspan="2"><?php echo $termine_detail[0]->beschreibung; ?></td>
            </tr>
        </table>
		<?php  } ?>
    <?php  } ?>
    
    <a href="index.php?option=com_clm&amp;view=termine&amp;categoryid=<?php echo $categoryid; ?>&amp;saison=<?php echo $sid; if ($itemid <>'') { echo "&Itemid=". $itemid; } ?>"><?php echo JText::_('TERMINE_BACK') ?></a>
	<br>
	<?php  if ($sreferer === false) { ?>
    <a href="<?php echo $referer; ?>"><?php echo JText::_('TERMINE_BACK_TOTAL') ?></a>
	<?php  } ?>	
    <br>
    <br>
    <?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>
    <div class="clr"></div>
</div>
</div>
