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
defined('_JEXEC') or die('Restricted access');
//HTMLHelper::_('behavior.tooltip', '.CLMTooltip');
require_once (JPATH_COMPONENT . DS . 'includes' . DS . 'clm_tooltip.php');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

// Lädt die benötigten Joomla-Core-Skripte für Formulare/Listen
HTMLHelper::_('behavior.core');
HTMLHelper::_('bootstrap.tooltip'); // Optional für die Tooltips am Spaltenkopf

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

	function clm_calendar($value, $name, $id, $format = '%Y-%m-%d', $attribs = null) {
		if ($value == '0000-00-00' OR $value == '1970-01-01') $value = '';

		// Stellt sicher, dass das Datum das korrekte ISO-Format (YYYY-MM-DD) besitzt
		$formattedValue = '';
		if (!empty($value) && $value !== '0000-00-00 00:00:00') {
			$formattedValue = htmlspecialchars(substr($value, 0, 10));
		}

		// Gibt ein natives, perfekt gestyltes HTML5-Kalenderfeld aus
		return '<input type="date" name="' . htmlspecialchars($name) . '" id="' . htmlspecialchars($id) . '" value="' . $formattedValue . '" class="form-control" />';
	}

// Konfigurationsparameter auslesen
$itemid 		= clm_core::$load->request_int( 'Itemid',1 );
$config			= clm_core::$db->config();
$fixth_ttln		= $config->fixth_ttln;

$plink = '$turnier='.$this->turnier->id.'&itemid='.$itemid;
// CLM-Container
echo "<div id='clm'><div id='turnier_teilnehmer'>";

// Componentheading
$heading =  $this->turnier->name.": ".Text::_('Startgeldaktualisierung');

$archive_check = clm_core::$api->db_check_season_user($this->turnier->sid);
if (!$archive_check) {
	echo CLMContent::componentheading($heading);
	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
	echo CLMContent::clmWarning(Text::_('NO_ACCESS')."<br/>".Text::_('NOT_REGISTERED'));
} elseif ( $this->turnier->published == 0) { 
    echo CLMContent::componentheading($heading);
	echo CLMContent::clmWarning(Text::_('TOURNAMENT_NOTPUBLISHED')."<br/>".Text::_('TOURNAMENT_PATIENCE'));

} elseif (count($this->players) == 0) {
    echo CLMContent::componentheading($heading);
	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
	echo CLMContent::clmWarning(Text::_('TOURNAMENT_NOPLAYERSREGISTERED'));

	
} else {

	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');
	// PDF-Link
	echo CLMContent::createPDFLink('turnier_teilnehmer', Text::_('Startgeldaktualisierung'), array('turnier' => $this->turnier->id, 'layout' => 'teilnehmer'));
    echo CLMContent::componentheading($heading).'<span style="font-size: 70%;">Standardstartgeld: '.$this->turnier->entry_fee.' &nbsp; &nbsp; aktuelle Startgeldsumme: '.$this->turnier->sum_fee.'</span>';
	$turParams = new clm_class_params($this->turnier->params);

	?>
	

	<script language="javascript" type="text/javascript">
		
		Joomla.submitbutton = function (pressbutton) { 		
			var form = document.adminForm;
			document.getElementsByName('layout')[0].value = "update";
			if (pressbutton == 'cancel') {
				Joomla.submitform( pressbutton );
				return;
			}
			var entry_fee=document.getElementsByName('entry_fee')[0].value;
			var std_fee=<?php echo $this->turnier->entry_fee; ?>;
			// do field validation
		i = 1;
		while(document.getElementsByName('date_paid'+i)[0]) {
			var date_paid=document.getElementsByName('date_paid'+i)[0].value;
			var amount_paid=document.getElementsByName('amount_paid'+i)[0].value;
			var reason=document.getElementsByName('reason'+i)[0].value;
			var name=document.getElementsByName('name'+i)[0].value;
			var snr=document.getElementsByName('snr'+i)[0].value;
			if ((amount_paid > 0.00) && (date_paid <= "1970-01-01") ) {
				alert( snr+" "+name+": Betrag ohne Bezahldatum ist nicht zulässig" ); return false;
			}		
			if ((amount_paid != '') && (amount_paid != std_fee) && (reason == "") ) {
				alert( snr+" "+name+": Betrag weicht ab - Grund fehlt" ); return false;
			}		
			i++;
		}
		i = 10001;
		while(document.getElementsByName('date_paid'+i)[0]) {
			var date_paid=document.getElementsByName('date_paid'+i)[0].value;
			var amount_paid=document.getElementsByName('amount_paid'+i)[0].value;
			var reason=document.getElementsByName('reason'+i)[0].value;
			var name=document.getElementsByName('name'+i)[0].value;
			var snr=document.getElementsByName('snr'+i)[0].value;
			if ((amount_paid > 0.00) && (date_paid <= "1970-01-01") ) {
				alert( snr+"(WL) "+name+": Betrag ohne Bezahldatum ist nicht zulässig" ); return false;
			}		
			if ((amount_paid != '') && (amount_paid != std_fee) && (reason == "") ) {
				alert( snr+"(WL) "+name+": Betrag weicht ab - Grund fehlt" ); return false;
			}		
			i++;
		}
		Joomla.submitform( pressbutton );
		}
		</script>

	
<form action="<?php echo Route::_( 'index.php?option=com_clm&view=turnier_teilnehmer&layout=startgeld' . $plink ) ; ?>" method="post" name="adminForm" id="adminForm">

	<table cellpadding="0" cellspacing="0" id="turnier_teilnehmer" <?php if ($fixth_ttln =="1") { ?>class="tableWithFloatingHeader"<?php } ?>>

	<tr>
		
		<th class="tt_col_1"><?php echo HTMLHelper::_( 'grid.sort', 'TOURNAMENT_NUMBERABB', 'snr', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		
		<?php if ($turParams->get('displayPlayerTitle', 1) == 1) { ?>
			<th class="tt_col_2"><?php echo Text::_('TOURNAMENT_TITLE'); ?></th>
		<?php }	?>
		
		<th class="tt_col_3"><?php echo HTMLHelper::_( 'grid.sort', 'TOURNAMENT_PLAYERNAME', 'name', $this->lists['order_Dir'], $this->lists['order']); ?></th>

		<?php if ($turParams->get('displayPlayerClub', 1) == 1) { ?>
			<th class="tt_col_4"><?php echo HTMLHelper::_( 'grid.sort', 'TOURNAMENT_CLUB', 'verein', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		<?php } ?>

		<?php if ($turParams->get('displayPlayerFederation', 0) == 1) { ?>
			<th class="tt_col_5"><?php echo HTMLHelper::_( 'grid.sort', 'TOURNAMENT_FEDERATION', 'FIDEcco', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		<?php } ?>
		
		<th class="tt_col_6"><?php echo HTMLHelper::_( 'grid.sort', 'TOURNAMENT_TWZ', 'twz', $this->lists['order_Dir'], $this->lists['order']); ?></th>

		<?php if ($turParams->get('displayPlayerRating', 0) == 1) { ?>
			<th class="tt_col_7"><?php echo HTMLHelper::_( 'grid.sort', 'TOURNAMENT_RATING', 'start_dwz', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		<?php } ?>

		<?php if ($turParams->get('displayPlayerElo', 0) == 1) { ?>
			<th class="tt_col_8"><?php echo HTMLHelper::_( 'grid.sort', 'TOURNAMENT_ELO', 'FIDEelo', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		<?php } ?>
	
		<th class="tt_col_3"><?php echo HTMLHelper::_( 'grid.sort', 'TOURNAMENT_DATE_PAID', 'date_paid', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		<th class="tt_col_3"><?php echo HTMLHelper::_( 'grid.sort', 'TOURNAMENT_AMOUNT_PAID', 'amount_paid', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		<th class="tt_col_3"><?php echo HTMLHelper::_( 'grid.sort', 'TOURNAMENT_REASON', 'reason', $this->lists['order_Dir'], $this->lists['order']); ?></th>
 	
	</tr>
	
	<?php

	$p=0;

	foreach ($this->players as $key => $value) {

		$p++; // rowCount
		// Farbe anpassen
		if ($p%2 != 0) { 
			$zeilenr = "zeile1"; 
		} else { 
			$zeilenr = "zeile2"; 
		}
		?>

		<tr class="<?php echo $zeilenr; ?>">
			<td class="tt_col_1"><?php echo $value->snr; ?></td>
			<?php
			// Title
			if ($turParams->get('displayPlayerTitle', 1) == 1) {
				echo '<td class="tt_col_2">'.$value->titel.'</td>';
			}
			$link = new CLMcLink();
			$link->view = 'turnier_player';
			$link->more = array('turnier' => $this->turnier->id, 'snr' => $value->snr, 'Itemid' => $itemid );
			$link->makeURL();
			
			// Name
			echo '<td class="tt_col_3">'.$link->makeLink($value->name). '</td>';
			
			// Club
			if ($turParams->get('displayPlayerClub', 1) == 1) {
				if ($this->tourn_linkclub == 1) {
					$link = new CLMcLink();
					$link->view = 'verein';
					$link->more = array('saison' => $value->sid, 'zps' => $value->zps, 'Itemid' => $itemid );
					$link->makeURL();
					echo '<td class="tt_col_4">'.$link->makeLink($value->verein).'</td>';
				} else {
					echo '<td class="tt_col_4">'.$value->verein.'</td>';
				}
			}
			
			// Federation
			if ($turParams->get('displayPlayerFederation', 0) == 1) {
				echo '<td class="tt_col_5">'.$value->FIDEcco.'</td>';
			}
			
			// TWZ
			echo '<td class="tt_col_6">'.CLMText::formatRating($value->twz).'</td>';
			
			// start_dwz
			if ($turParams->get('displayPlayerRating', 0) == 1) {
				echo '<td class="tt_col_7">';
					echo CLMText::formatRating($value->start_dwz);
				echo '</td>';
			}
			
			// FIDEelo
			if ($turParams->get('displayPlayerElo', 0) == 1) {
				echo '<td class="tt_col_8">';
					echo CLMText::formatRating($value->FIDEelo);
				echo '</td>';
			}
			
			// Startgeld
			?>
			<td title="<?php echo Text::_( 'TOURNAMENT_DATE_PAID' );?>" >
				<?php echo clm_calendar($value->date_paid, "date_paid$value->snr", "date_paid$value->snr", '%Y-%m-%d', array('class'=>'text_area', 'size'=>'12',  'maxlength'=>'19')); ?>
			</td>
			<td title="<?php echo Text::_( 'TOURNAMENT_AMOUNT_PAID' );?>" >
				<input class="inputbox" type="text" name="amount_paid<?php echo $value->snr; ?>" id="amount_paid<?php echo $value->snr; ?>" size="5" maxlength="6" value="<?php echo $value->amount_paid; ?>" />
			</td>
			<td title="<?php echo Text::_( 'TOURNAMENT_REASON' );?>" >
				<input class="inputbox" type="text" name="reason<?php echo $value->snr; ?>" id="reason<?php echo $value->snr; ?>" size="30" maxlength="50" value="<?php echo $value->reason; ?>" />
			</td>
			<input type="hidden" name="name<?php echo $value->snr; ?>"  name="name<?php echo $value->snr; ?>" value="<?php echo $value->name; ?>" />
			<input type="hidden" name="snr<?php echo $value->snr; ?>"  name="snr<?php echo $value->snr; ?>" value="<?php echo $value->snr; ?>" />

			<td><button class="button" name="snr" id="snr" onclick="return Joomla.submitbutton('update99');" value="<?php echo $value->snr; ?>">
				<?php echo 'Update'; ?>
			</button></td>
		
		</tr>

		<?php
	}
	
	echo '</table>';
	?>

	<?php if (isset($this->wlplayers) AND is_array($this->wlplayers) AND count($this->wlplayers) > 0) { 
	
	echo $this->turnier->name.": ".Text::_('Wartelistenpositionen'); ?>
	<table cellpadding="0" cellspacing="0" id="turnier_teilnehmer" <?php if ($fixth_ttln =="1") { ?>class="tableWithFloatingHeader"<?php } ?>>

	<tr>
		
		<th class="tt_col_1"><?php echo HTMLHelper::_( 'grid.sort', 'TOURNAMENT_NUMBERABB', 'snr', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		
		<?php if ($turParams->get('displayPlayerTitle', 1) == 1) { ?>
			<th class="tt_col_2"><?php echo Text::_('TOURNAMENT_TITLE'); ?></th>
		<?php }	?>
		
		<th class="tt_col_3"><?php echo HTMLHelper::_( 'grid.sort', 'TOURNAMENT_PLAYERNAME', 'name', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		
		<?php if ($turParams->get('displayPlayerClub', 1) == 1) { ?>
			<th class="tt_col_4"><?php echo HTMLHelper::_( 'grid.sort', 'TOURNAMENT_CLUB', 'verein', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		<?php } ?>
		
		<?php if ($turParams->get('displayPlayerFederation', 0) == 1) { ?>
			<th class="tt_col_5"><?php echo HTMLHelper::_( 'grid.sort', 'TOURNAMENT_FEDERATION', 'FIDEcco', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		<?php } ?>
		
		<th class="tt_col_6"><?php echo HTMLHelper::_( 'grid.sort', 'TOURNAMENT_TWZ', 'twz', $this->lists['order_Dir'], $this->lists['order']); ?></th>
	
		<?php if ($turParams->get('displayPlayerRating', 0) == 1) { ?>
			<th class="tt_col_7"><?php echo HTMLHelper::_( 'grid.sort', 'TOURNAMENT_RATING', 'start_dwz', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		<?php } ?>
	
		<?php if ($turParams->get('displayPlayerElo', 0) == 1) { ?>
			<th class="tt_col_8"><?php echo HTMLHelper::_( 'grid.sort', 'TOURNAMENT_ELO', 'FIDEelo', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		<?php } ?>
	
		<th class="tt_col_3"><?php echo HTMLHelper::_( 'grid.sort', 'TOURNAMENT_DATE_PAID', 'date_paid', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		<th class="tt_col_3"><?php echo HTMLHelper::_( 'grid.sort', 'TOURNAMENT_AMOUNT_PAID', 'amount_paid', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		<th class="tt_col_3"><?php echo HTMLHelper::_( 'grid.sort', 'TOURNAMENT_REASON', 'reason', $this->lists['order_Dir'], $this->lists['order']); ?></th>
 	
	</tr>
	
	<?php

	$p=0;

	foreach ($this->wlplayers as $key => $value) {

		$p++; // rowCount
		// Farbe anpassen
		if ($p%2 != 0) { 
			$zeilenr = "zeile1"; 
		} else { 
			$zeilenr = "zeile2"; 
		}
		?>

		<tr class="<?php echo $zeilenr; ?>">
			<td class="tt_col_1"><?php echo $value->snr; ?></td>
			<?php
			// Title
			if ($turParams->get('displayPlayerTitle', 1) == 1) {
				echo '<td class="tt_col_2">'.$value->titel.'</td>';
			}
			$link = new CLMcLink();
			$link->view = 'turnier_player';
			$link->more = array('turnier' => $this->turnier->id, 'snr' => $value->snr, 'Itemid' => $itemid );
			$link->makeURL();
			
			// Name
			echo '<td class="tt_col_3">'.$link->makeLink($value->name). '</td>';
			
			// Club
			if ($turParams->get('displayPlayerClub', 1) == 1) {
				if ($this->tourn_linkclub == 1) {
					$link = new CLMcLink();
					$link->view = 'verein';
					$link->more = array('saison' => $value->sid, 'zps' => $value->zps, 'Itemid' => $itemid );
					$link->makeURL();
					echo '<td class="tt_col_4">'.$link->makeLink($value->verein).'</td>';
				} else {
					echo '<td class="tt_col_4">'.$value->verein.'</td>';
				}
			}
			
			// Federation
			if ($turParams->get('displayPlayerFederation', 0) == 1) {
				echo '<td class="tt_col_5">'.$value->FIDEcco.'</td>';
			}
			
			// TWZ
			echo '<td class="tt_col_6">'.CLMText::formatRating($value->twz).'</td>';
			
			// start_dwz
			if ($turParams->get('displayPlayerRating', 0) == 1) {
				echo '<td class="tt_col_7">';
					echo CLMText::formatRating($value->start_dwz);
				echo '</td>';
			}
			
			// FIDEelo
			if ($turParams->get('displayPlayerElo', 0) == 1) {
				echo '<td class="tt_col_8">';
					echo CLMText::formatRating($value->FIDEelo);
				echo '</td>';
			}
			
			// Startgeld
			$zsnr = 10000 + $value->snr;
			?>
			<td title="<?php echo Text::_( 'TOURNAMENT_DATE_PAID' );?>" >
				<?php echo clm_calendar($value->date_paid, "date_paid$zsnr", "date_paid$zsnr", '%Y-%m-%d', array('class'=>'text_area', 'size'=>'12',  'maxlength'=>'19')); ?>
			</td>
			<td title="<?php echo Text::_( 'TOURNAMENT_AMOUNT_PAID' );?>" >
				<input class="inputbox" type="text" name="amount_paid<?php echo $zsnr; ?>" id="amount_paid<?php echo $zsnr; ?>" size="5" maxlength="6" value="<?php echo $value->amount_paid; ?>" />
			</td>
			<td title="<?php echo Text::_( 'TOURNAMENT_REASON' );?>" >
				<input class="inputbox" type="text" name="reason<?php echo $zsnr; ?>" id="reason<?php echo $zsnr; ?>" size="30" maxlength="50" value="<?php echo $value->reason; ?>" />
			</td>
			<input type="hidden" name="name<?php echo $zsnr; ?>"  name="name<?php echo $zsnr; ?>" value="<?php echo $value->name; ?>" />
			<input type="hidden" name="snr<?php echo $zsnr; ?>"  name="snr<?php echo $zsnr; ?>" value="<?php echo $value->snr; ?>" />

			<td><button class="button" name="snr" id="snr" onclick="return Joomla.submitbutton('update99');" value="<?php echo (10000 + $value->snr); ?>">
				<?php echo 'Update'; ?>
			</button></td>
		
		</tr>

		<?php
	}
	
	echo '</table>';
	} ?>
	
		<input type="hidden" name="layout" value="startgeld" />
		<input type="hidden" name="view" value="turnier_teilnehmer" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="turnier" value="<?php echo $this->turnier->id; ?>" />
		<input type="hidden" name="entry_fee" value="<?php echo $this->turnier->entry_fee; ?>" />
		<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />

		<?php echo HTMLHelper::_( 'form.token' ); ?>
		</form>
	<?php
}

require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); 
echo '</div></div>';

 
?>
