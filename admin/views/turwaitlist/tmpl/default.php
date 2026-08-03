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

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

$clmAccess = clm_core::$access;
$turParams = new clm_class_params($this->turnier->params);

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">

		<table>
		<tr>
			<td align="left" width="100%">
				<?php echo Text::_( 'FILTER' ); ?>:
		<input type="text" name="search" id="search" value="<?php echo $this->param['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo Text::_( 'GO' ); ?></button>
		<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_vid').value='0';this.form.submit();"><?php echo Text::_( 'RESET' ); ?></button>
			</td>
			<td nowrap="nowrap">
				<?php
					echo "&nbsp;&nbsp;&nbsp;".CLMForm::selectVereinTournament('filter_vid', $this->param['vid'], $this->turnier->id, TRUE);
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
					<th width="3%">
						<?php echo HTMLHelper::_('grid.sort', Text::_('CLM_NUMBER_ABB'), 'snr', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<?php 
					if ($turParams->get('displayPlayerTitle', 1) == 1) {
					?>
					<th width="3%">
						<?php echo HTMLHelper::_('grid.sort', Text::_('PLAYER_TITLE'), 'titel', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<?php 
					} 
					?>
					<th width="20%" class="title">
						<?php echo HTMLHelper::_('grid.sort', Text::_('PLAYER_NAME'), 'name', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<th width="20%">
						<?php echo HTMLHelper::_('grid.sort', Text::_('CLUB'), 'verein', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<th width="5%">
						<?php echo HTMLHelper::_('grid.sort', Text::_('PLAYER_ZPS'), 'twz', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<th width="5%">
						<?php echo HTMLHelper::_('grid.sort', Text::_('PLAYER_MGLNR'), 'start_dwz', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<?php
					if ($turParams->get('displayPlayerFederation', 0) == 1) {
					?>
					<th width="5%">
						<?php echo HTMLHelper::_('grid.sort', Text::_('FEDERATION'), 'FIDEcco', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<?php
					}
					?>
					<th width="5%">
						<?php echo HTMLHelper::_('grid.sort', Text::_('TWZ'), 'twz', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<th width="5%">
						<?php echo HTMLHelper::_('grid.sort', Text::_('RATING'), 'start_dwz', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<th width="5%">
						<?php echo HTMLHelper::_('grid.sort', Text::_('FIDE_ELO'), 'FIDEelo', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<?php 
					if ($turParams->get('optionEloAnalysis', 0) == 1) {
					?>
					<th width="3%">
						<?php echo HTMLHelper::_('grid.sort', Text::_('FIDE_ID'), 'FIDEid', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<?php 
					} 
					?>

					<th width="10%" nowrap="nowrap">
						<?php echo HTMLHelper::_('grid.sort', Text::_('JGRID_HEADING_ORDERING'), 'ordering', $this->param['order_Dir'], $this->param['order'] ); ?>
						<?php echo HTMLHelper::_('grid.order', $this->turplayers ); ?>
					</th>
					<th width="1%" nowrap="nowrap">
						<?php echo HTMLHelper::_('grid.sort',   'ID', 'a.id', $this->param['order_Dir'], $this->param['order'] ); ?>
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
			
			$n=count( $this->turplayers );
			foreach ($this->turplayers as $i => $value) {
				$row = &$value;
			// for ($i=0, $n=count( $rows ); $i < $n; $i++) {
				// $row = &$rows[$i];			
				//$link 		= Route::_( 'index.php?option=com_clm&section=t&task=edit&cid[]='. $row->id );
				$checked 	= HTMLHelper::_('grid.checkedout',   $row, $i );
//				$published 	= HTMLHelper::_('grid.published', $row, $i );
				$published 	= HTMLHelper::_('jgrid.published', $row->published, $i );
				?>
				<tr class="<?php echo 'row'. $k; ?>">
					<td align="center">
						<?php echo $this->pagination->getRowOffset( $i ); ?>
					</td>
					<td>
						<?php echo $checked; ?>
					</td>
					<td align="center">
						<?php echo $row->snr;?>
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
							$adminLink->view = "turwaitlistedit";
							$adminLink->more = array('playerid' => $row->id );
							$adminLink->makeURL();
							?>
							<span class="editlinktip hasTip" title="<?php echo Text::_( 'PLAYER_EDIT' );?>">
								<a href="<?php echo $adminLink->url; ?>">
									<?php echo $row->name; ?>
								</a>
							</span>
						<?php
						} else {
							echo $row->name;
						}
						?>
					</td>
					<td align="left">
						<?php echo $row->verein; ?>
					</td>
					<td align="center">
						<?php 
						if ($row->zps > '') {
							echo $row->zps;
						} else {
							echo '-';
						}
						?>
					</td>
					<td align="center">
						<?php 
						if ($row->mgl_nr > 0) {
							echo $row->mgl_nr;
						} else {
							echo '-';
						}
						?>
					</td>
					<?php
					if ($turParams->get('displayPlayerFederation', 0) == 1) {
					?>
					<td align="center">
						<?php echo $row->FIDEcco; ?>
					</td>
					<?php
					}
					?>
					<td align="center">
						<b>
						<?php 
						if ($row->twz > 0) {
							echo $row->twz;
						} else {
							echo '-';
						}
						?>
						</b>
					</td>
					<td align="center">
						<?php 
						if ($row->start_dwz > 0) {
							echo $row->start_dwz;
						} else {
							echo '-';
						}
						?>
					</td>
					<td align="center">
						<?php 
						if ($row->FIDEelo > 0) {
							echo $row->FIDEelo;
						} else {
							echo '-';
						}
						?>
					</td>
					<?php 
					if ($turParams->get('optionEloAnalysis', 0) == 1) {
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
					
					<td class="order" width="10%">
						<span><?php echo $this->pagination->orderUpIcon($i, true, 'orderup', 'Move Up', $this->param['order'] ); ?></span>
						<span><?php echo $this->pagination->orderDownIcon($i, $n, true, 'orderdown', 'Move Down', $this->param['order'] ); ?></span>
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
		<input type="hidden" name="view" value="turwaitlist" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="turwaitlist" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->param['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->param['order_Dir']; ?>" />
		<input type="hidden" name="id" value="<?php echo $this->param['id']; ?>" />
		<?php echo HTMLHelper::_( 'form.token' ); ?>

</form>
