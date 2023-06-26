<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

$clmAccess = clm_core::$access;
$turParams = new clm_class_params($this->turnier->params);

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">

			<table class="adminlist">
			<thead>
				<tr>
					<th width="3%">
						#
					</th>
					<th width="3%">
					<?php echo $GLOBALS["clm"]["grid.checkall"]; ?>
					</th>
					<?php 
					if ($turParams->get('displayPlayerTitle', 1) == 1) {
					?>
					<th width="3%">
						<?php echo JHtml::_('grid.sort', JText::_('PLAYER_TITLE'), 'titel', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<?php 
					} 
					?>
					<th width="20%" class="title">
						<?php echo JHtml::_('grid.sort', JText::_('PLAYER_NAME'), 'name', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<th width="20%">
						<?php echo JHtml::_('grid.sort', JText::_('CLUB'), 'club', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<?php
					?>
					<th width="5%">
						<?php echo JHtml::_('grid.sort', JText::_('FEDERATION'), 'FIDEcco', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<?php
					?>
					<th width="5%">
						<?php echo JHtml::_('grid.sort', JText::_('RATING'), 'dwz', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<th width="5%">
						<?php echo JHtml::_('grid.sort', JText::_('FIDE_ELO'), 'elo', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<?php 
					if ($turParams->get('optionEloAnalysis', 0) == 1) {
					?>
					<th width="3%">
						<?php echo JHtml::_('grid.sort', JText::_('FIDE_ID'), 'FIDEid', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<?php 
					} 
					?>
					<th width="20%" class="title">
						<?php echo JHtml::_('grid.sort', JText::_('STATUS'), 'status', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<th width="20%" class="title">
						<?php echo JHtml::_('grid.sort', JText::_('TIMESTAMP'), 'timestamp', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
															
					<th width="10%" nowrap="nowrap">
						<?php echo JHtml::_('grid.sort', JText::_('JGRID_HEADING_ORDERING'), 'ordering', $this->param['order_Dir'], $this->param['order'] ); ?>
						<?php echo JHtml::_('grid.order', $this->turplayers ); ?>
					</th>
					<th width="1%" nowrap="nowrap">
						<?php echo JHtml::_('grid.sort',   'ID', 'a.id', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>

				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="17">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			
			$n=count( $this->turregistrations );
			foreach ($this->turregistrations as $i => $value) {
				$row = &$value;				
				$checked 	= JHtml::_('grid.checkedout',   $row, $i );
				?>
				<tr class="<?php echo 'row'. $k; ?>">
					<td align="center">
						<?php echo $this->pagination->getRowOffset( $i ); ?>
					</td>
					<td>
						<?php echo $checked; ?>
					</td>
					<?php 
					if ($turParams->get('displayPlayerTitle', 1) == 1) {
					?>
					<td align="center">
						<?php echo $row->titel;?>
					</td>
					<?php 
					} 
					?>
					<td align="left">
						<?php 
						
						// admin/tl kann Spieler editieren
						if (($this->turnier->tl == clm_core::$access->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== false) OR $clmAccess->access('BE_tournament_edit_detail') === true) {
							$adminLink = new AdminLink();
							$adminLink->view = "turregistrationedit";
							$adminLink->more = array('registrationid' => $row->id);
							$adminLink->makeURL();
							?>
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'PLAYER_EDIT' );?>">
								<a href="<?php echo $adminLink->url; ?>">
									<?php echo $row->name.','.$row->vorname; ?>
								</a>
							</span>
						<?php
						} else {
							echo $row->name;
						}
						?>
					</td>
					<td align="left">
						<?php echo $row->club; ?>
					</td>
					<?php
					?>
					<td align="center">
						<?php echo $row->FIDEcco; ?>
					</td>
					<?php
					?>
					<td align="center">
						<?php 
						if ($row->dwz > 0) {
							echo $row->dwz;
						} else {
							echo '-';
						}
						?>
					</td>
					<td align="center">
						<?php 
						if ($row->elo > 0) {
							echo $row->elo;
						} else {
							echo '-';
						}
						?>
					</td>
					<?php if ($turParams->get('optionEloAnalysis', 0) == 1) {
					?>
					<td align="center">
						<?php if ($row->FIDEid > 0) { ?>
							<a href="https://ratings.fide.com/profile/<?php echo $row->FIDEid;?>" target="_blank"><?php echo $row->FIDEid; ?></a>
						<?php } else {  
							echo '-'; } ?>
					</td>
					<?php 
					} 
					?>
					<td align="center">
						<?php 
						if ($row->status > 2) {
							echo $row->status;
						} else {
							if ($row->approved == '0')
								echo JText::_('REGISTRATION_STATUS_0'.$row->status);
							else
								echo JText::_('REGISTRATION_STATUS_'.$row->status);
						}
						?>
					</td>
					<td align="center">
						<?php echo date("d.m.y - H:i", $row->timestamp); ?>
					</td>
					<td class="order" width="10%">
						<span><?php echo $this->pagination->orderUpIcon($i, true, 'orderup()', 'Move Up', $this->param['order'] ); ?></span>
						<span><?php echo $this->pagination->orderDownIcon($i, $n, true, 'orderdown()', 'Move Down', $this->param['order'] ); ?></span>
						<?php $disabled = $this->param['order'] ?  '' : 'disabled="disabled"'; ?>
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

		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="view" value="turregistrations" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="turregistrations" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->param['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->param['order_Dir']; ?>" />
		<input type="hidden" name="id" value="<?php echo $this->param['id']; ?>" />
		<?php echo JHtml::_( 'form.token' ); ?>

</form>
