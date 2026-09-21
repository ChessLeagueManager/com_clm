<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;

	require_once (JPATH_COMPONENT_SITE . DS . 'includes' . DS . 'tooltip.php');
	$lang = clm_core::$lang->arbiter;
	// Auswahlfelder durchsuchbar machen
	clm_core::$load->load_js("suche_liste");

	$lid = clm_core::$load->request_int('lid');
	$tid = clm_core::$load->request_int('tid');
	$returnview = clm_core::$load->request_string('returnview');

	$turnier = $this->turnier;
	$organisers  = $this->organisers;
	$lists  = $this->lists;
	$organiserlist = $this->organiserlist;
	$field_search = $this->field_search;
		
?>

	<script language="javascript" type="text/javascript">

	Joomla.submitbutton = function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			Joomla.submitform( pressbutton );
			return;
		}
		
		// do field validation
//		if (form.fideid.value == 0) {
//			alert( jserror['enter_fideid'] );
//		} elseif (form.name.value == "") {
//			alert( jserror['enter_name'] );
//		} else {
			Joomla.submitform( pressbutton );
//		}
	}
	
	</script>
<?php
	echo '<br><h3>'.$lang->turorg_assign .' &nbsp; &nbsp; &nbsp; <span style="font-weight:normal;"></span> '.$this->turnier[0]->name.' &nbsp; &nbsp; &nbsp; <span style="font-weight:normal;">Saison:</span> '.$this->turnier[0]->sname.'</h3><br>';
	// Auswahlfelder durchsuchbar machen
	clm_core::$load->load_js("suche_liste");
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">

	<div class="width-50 fltlft">
		<fieldset class="adminform">
		<legend><?php echo $lang->turorg_roles; ?></legend>
			<table class="paramlist admintable">
			<tr>
				<td width="42%" class="paramlist_key">
					<label for="ttl"><?php echo $lang->roleTTL; ?></label>
				</td>
				<td class="paramlist_value">
					<?php echo $lists['TTL']; ?>
				</td>
			</tr>
			<?php for ($i = 0; $i <= 10; $i++) { 
				if (!isset($lists['TTLW'.$i])) break; ?>
				<tr>
					<td width="40%" class="paramlist_key">
						<label for="ttlw<?php echo $i; ?>"><?php if ($i == 0) echo $lang->roleTTLW; ?></label>
					</td>
					<td class="paramlist_value">
						<?php echo $lists['TTLW'.$i]; ?>
					</td>
				</tr>
			<?php } ?>
			<?php for ($i = 0; $i <= 10; $i++) { 
				if (!isset($lists['TTO'.$i])) break; ?>
				<tr>
					<td width="40%" class="paramlist_key">
						<label for="tto<?php echo $i; ?>"><?php if ($i == 0) echo $lang->roleTTO; ?></label>
					</td>
					<td class="paramlist_value">
						<?php echo $lists['TTO'.$i]; ?>
					</td>
				</tr>
			<?php } ?>
			<?php for ($i = 0; $i <= 10; $i++) { 
				if (!isset($lists['TKA'.$i])) break; ?>
				<tr>
					<td width="40%" class="paramlist_key">
						<label for="tka<?php echo $i; ?>"><?php if ($i == 0) echo $lang->roleTKA; ?></label>
					</td>
					<td class="paramlist_value">
						<?php echo $lists['TKA'.$i]; ?>
					</td>
				</tr>
			<?php } ?>

			<tr><td colspan="2">&nbsp;</td></tr>
			<tr><td colspan="2">&nbsp;</td></tr>

			</table>
	  </fieldset>
  </div>
		


<div class="clr"></div>


	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="turorgassign" />
	<input type="hidden" name="lid" value="<?php echo $lid; ?>" />
	<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
	<input type="hidden" name="id" value="<?php echo $tid; ?>" />
	<input type="hidden" name="returnview" value="<?php echo $returnview; ?>" />
	<input type="hidden" name="controller" value="turorgassign" />
	<input type="hidden" name="task" value="apply" />
	<?php echo HTMLHelper::_( 'form.token' ); ?>

</form>
