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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Router\Route;

class CLMViewMannschaften
{
public static function setMannschaftenToolbar()
	{
	$clmAccess = clm_core::$access;
	// Menubilder laden
		clm_core::$load->load_css("icons_images");

		ToolBarHelper::title( Text::_( 'TITLE_MANNSCHAFT' ), 'clm_headmenu_mannschaften.png' );
	if($clmAccess->access('BE_team_registration_list') !== false) {
		ToolBarHelper::custom('delete_meldeliste','send.png','send_f2.png', Text::_( 'MANNSCHAFT_BUTTON_ML_DEL'));
		ToolBarHelper::custom('meldeliste','send.png','send_f2.png', Text::_( 'MANNSCHAFT_BUTTON_ML_UPD'));
		ToolBarHelper::custom('copy_meldeliste','copy.png','copy_f2.png', Text::_( 'Meldeliste kopieren von'));
		ToolBarHelper::custom('spielfrei','cancel.png','cancel_f2.png', Text::_( 'MANNSCHAFT_BUTTON_SPIELFREI'));
		ToolBarHelper::custom('annull','cancel.png','cancel_f2.png', Text::_( 'MANNSCHAFT_BUTTON_ANNULL'));
		ToolBarHelper::publishList();
		ToolBarHelper::unpublishList();
		ToolBarHelper::custom( 'copy', 'copy.png', 'copy_f2.png', Text::_( 'MANNSCHAFT_BUTTON_COPY' )); 
		ToolBarHelper::deleteList();
		ToolBarHelper::editList();
		ToolBarHelper::addNew();
		ToolBarHelper::custom('geo','edit.png','delete_f2.png','MANNSCHAFT_BUTTON_GEO');

		}
		ToolBarHelper::help( 'screen.clm.mannschaft' );
	}

public static function mannschaften( $rows, $lists, $pageNav, $option )
	{
		$mainframe	= Factory::getApplication();
		CLMViewMannschaften::setMannschaftenToolbar();
		$user =Factory::getUser();
		//Ordering allowed ?
		$ordering = ($lists['order'] == 'a.ordering');

//		HTMLHelper::_('behavior.tooltip');
		require_once (JPATH_COMPONENT_SITE . DS . 'includes' . DS . 'tooltip.php');

		// Auswahlfelder durchsuchbar machen
		clm_core::$load->load_js("suche_liste");
		?>
		<form action="index.php?option=com_clm&section=mannschaften" method="post" name="adminForm" id="adminForm">

		<table>
		<tr>
			<td align="left" width="100%">
				<?php echo Text::_( 'Filter' ); ?>:
		<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo Text::_( 'Go' ); ?></button>
		<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_catid').value='0';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo Text::_( 'Reset' ); ?></button>
			</td>
			<td nowrap="nowrap">
				<?php
		// eigenes Dropdown Menue
			echo "&nbsp;&nbsp;&nbsp;".$lists['sid'];
			echo "&nbsp;&nbsp;&nbsp;".$lists['lid'];
			echo "&nbsp;&nbsp;&nbsp;".$lists['vid'];
			echo "&nbsp;&nbsp;&nbsp;".$lists['state'];
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
						<?php echo HTMLHelper::_('grid.sort',   'MANNSCHAFT', 'a.name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="3%">
						<?php echo HTMLHelper::_('grid.sort',   'MANNSCHAFT_NR', 'a.man_nr', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="15%">
						<?php echo HTMLHelper::_('grid.sort',   'MANNSCHAFT_LIGA', 'd.name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="3%">
						<?php echo HTMLHelper::_('grid.sort',   'MANNSCHAFT_T_NR', 'a.tln_nr', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="3%">
						<?php echo HTMLHelper::_('grid.sort',   'MANNSCHAFT_MF', 'a.mf', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>

					<th width="3%">
						<?php echo HTMLHelper::_('grid.sort',   'MANNSCHAFT_MELDELISTE', 'a.liste', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="22%">
						<?php echo HTMLHelper::_('grid.sort',   'MANNSCHAFT_VEREIN', 'b.Vereinname', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="11%">
						<?php echo HTMLHelper::_('grid.sort',   'MANNSCHAFT_SAISON', 'c.name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="6%">
						<?php echo HTMLHelper::_('grid.sort',   'JPUBLISHED', 'a.published', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="8%" nowrap="nowrap">
						<?php echo HTMLHelper::_('grid.sort',   'JGRID_HEADING_ORDERING', 'a.ordering', @$lists['order_Dir'], @$lists['order'] ); ?>
						<?php echo HTMLHelper::_('grid.order',  $rows ); ?>
					</th>

					<th width="1%" nowrap="nowrap">
						<?php echo HTMLHelper::_('grid.sort',   'JGRID_HEADING_ID', 'a.id', @$lists['order_Dir'], @$lists['order'] ); ?>
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
			$row =Table::getInstance( 'mannschaften', 'TableCLM' );
			for ($i=0, $n=count( $rows ); $i < $n; $i++) {
				//$row = &$rows[$i];
				// load the row from the db table 
				$row->load( $rows[$i]->id );
				$link 		= Route::_( 'index.php?option=com_clm&section=mannschaften&task=edit&id='. $row->id );
				$checked 	= HTMLHelper::_('grid.checkedout',   $row, $i );
//				$published 	= HTMLHelper::_('grid.published', $row, $i );
				$published 	= HTMLHelper::_('jgrid.published', $row->published, $i );

				?>
				<tr class="<?php echo 'row'. $k; ?>">

					<td align="center">
						<?php echo $pageNav->getRowOffset( $i ); ?>
					</td>

					<td>
						<?php echo $checked; ?>
					</td>

					<td>

								<span class="editlinktip hasTip" title="<?php echo Text::_( 'MANNSCHAFT_EDIT' );?>::<?php echo $row->name; ?>">
							<a href="<?php echo $link; ?>">
								<?php echo $row->name; ?></a></span>

					</td>

					<td align="center">
						<?php echo $row->man_nr;?>
					</td>

					<td align="center">
						<?php echo $rows[$i]->liga_name;?>
					</td>
					<td align="center">
						<?php echo $row->tln_nr;?>
					</td>
					<td align="center">
						<?php if ($row->mf > 0) 
							{ ?><img width="16" height="16" src="components/com_clm/images/apply_f2.png" /> <?php }
						else 	{ ?><img width="16" height="16" src="components/com_clm/images/cancel_f2.png" /> <?php }?>
					</td>

					<td align="center">
						<?php if ($row->liste > 0) 
							{ ?><img width="16" height="16" src="components/com_clm/images/apply_f2.png" /> <?php }
						else 	{ ?><img width="16" height="16" src="components/com_clm/images/cancel_f2.png" /> <?php }?>
					</td>
					<td align="center">
						<?php echo $rows[$i]->verein;?>
					</td>
					<td align="center">
						<?php echo $rows[$i]->saison;?>
					</td>

					<td align="center">
						<?php echo $published;?>
					</td>
	<td class="order">
	<span><?php echo $pageNav->orderUpIcon($i, ($row->liga == @$rows[$i-1]->liga), 'orderup', 'Move Up', $ordering ); ?></span>
	<span><?php echo $pageNav->orderDownIcon($i, $n, ($row->liga == @$rows[$i+1]->liga), 'orderdown', 'Move Down', $ordering ); ?></span>
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
		<?php echo HTMLHelper::_( 'form.token' ); ?>
		</form>
		<?php
	}

public static function setMannschaftToolbar()
	{
	// Menubilder laden
		clm_core::$load->load_css("icons_images");

		$cid = clm_core::$load->request_array_int('cid');
										  
		if (clm_core::$load->request_string( 'task') == 'edit') { $text = Text::_( 'Edit' );}
			else { $text = Text::_( 'New' );}
		ToolBarHelper::title(  Text::_( 'MANNSCHAFT' ).': [ '. $text.' ]' , 'clm_headmenu_mannschaften.png'  );
		ToolBarHelper::save();
		ToolBarHelper::apply();
		ToolBarHelper::cancel();
		ToolBarHelper::help( 'screen.clm.edit' );
	}
		
public static function mannschaft( &$row,$lists, $option )
	{
		CLMViewMannschaften::setMannschaftToolbar();
		$_REQUEST['hidemainmenu'] = 1;
		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'extrainfo' );

		$_POST['clm_noOrgReference'] = $lists['noOrgReference'];
		clm_core::$load->load_js("mannschaft");

		// Auswahlfelder durchsuchbar machen
		clm_core::$load->load_js("suche_liste");
		?>

		<form action="index.php" method="post" name="adminForm" id="adminForm">

		<div class="width-50 fltlft">
		<fieldset class="adminform">
		<legend><?php echo Text::_( 'Details' ); ?></legend>

		<table class="admintable">
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
				<label for="name">
				<span class="editlinktip hasTip" title="<?php echo Text::_( 'MANNSCHAFT_HINT' );?>">
				<?php echo Text::_( 'MANNSCHAFT' )." : "; ?></span></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="name" id="name" size="50" maxlength="60" value="<?php echo $row->name; ?>" />
			</td>
		</tr>
	<?php if ($lists['pgntype'] > 3) { ?>	
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="sname"><?php echo Text::_( 'MANNSCHAFT_SHORT' )." : "; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="sname" id="sname" size="50" maxlength="60" value="<?php echo $row->sname; ?>" />
			</td>
		</tr>
	<?php } ?>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="contact"><?php echo Text::_( 'MANNSCHAFT_NUMMER' )." : "; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="man_nr" id="man_nr" size="50" maxlength="60" value="<?php echo $row->man_nr; ?>" />
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="tln_nr"><?php echo Text::_( 'MANNSCHAFT_TEILNEHMER_NR' )." : "; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="tln_nr" id="tln_nr" size="50" maxlength="60" value="<?php echo $row->tln_nr; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="sid"><?php echo Text::_( 'MANNSCHAFT_SAISON' )." : "; ?></label>
			</td>
			<td>
			<?php echo $lists['saison']; ?>
			</td>
		</tr>

		<tr>
			<td class="key" ><label for="liga"><?php echo Text::_( 'MANNSCHAFT_LIGA' )." :"; ?></label>
			</td>
			<td>
			<?php echo $lists['liga']; ?>
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap">
				<label for="verein">
				<span class="editlinktip hasTip" title="<?php echo Text::_( 'MANNSCHAFT_HINT' );?>">
				<?php echo Text::_( 'MANNSCHAFT_VEREIN' )." : "; ?></span></label>
			</td>
			<td>
			<?php echo $lists['verein']; ?>
			</td>
		</tr>
		<?php for ($i = 0; $i < $lists['anz_sgp']; $i++) { ?>
		<tr>
			<td class="key" nowrap="nowrap"><label for="<?php echo 'sg_zps'.$i; ?>"><?php echo Text::_( 'MANNSCHAFT_PARTNERVEREIN' )." : "; ?></label>
			</td>
			<td>
			<?php echo $lists['sg'.$i]; ?>
			</td>
		</tr>
		<?php } ?>
		<tr>
			<td class="key" nowrap="nowrap"><label for="mf"><?php echo Text::_( 'MANNSCHAFT_FUEHRER' )." : "; ?></label>
			</td>
			<td>
			<?php echo $lists['mf']; ?>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="published"><?php echo Text::_( 'JPUBLISHED' )." : "; ?></label>
			</td>
			<td><fieldset class="radio">
			<?php echo $lists['published']; ?>
			</fieldset></td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="abzug"><?php echo Text::_( 'MANNSCHAFT_MPABZUG' )." : "; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="abzug" id="abzug" size="10" maxlength="10" value="<?php echo $row->abzug; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="bpabzug"><?php echo Text::_( 'MANNSCHAFT_BPABZUG' )." : "; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="bpabzug" id="bpabzug" size="10" maxlength="10" value="<?php echo $row->bpabzug; ?>" />
			</td>
		</tr>
		<tr><td colspan="2"><hr></td></tr>

		<tr>
			<td class="key" nowrap="nowrap">
				<label for="lokal">
				<span class="editlinktip hasTip" title="<?php echo Text::_( 'MANNSCHAFT_HINT' );?>">
				<?php echo Text::_( 'MANNSCHAFT_SPIELLOKAL' )." : "; ?></span></label>
			</td>
			<td>
			<?php  echo Text::_( 'CLM_KOMMA' ) . "<br><br>"; ?>
			<textarea class="inputbox" name="lokal" id="lokal" cols="40" rows="2" style="width:100%"><?php echo $row->lokal; ?></textarea>
			<br><?php  echo Text::_( 'CLM_ADDRESS' ) ; ?>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="lokal"><?php echo Text::_( 'MANNSCHAFT_ADRESSE' )." : "; ?></label>
			</td>
			<td>
			<textarea class="inputbox" name="adresse" id="adresse" cols="40" rows="2" style="width:100%"><?php echo $row->adresse; ?></textarea>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="lokal"><?php echo Text::_( 'MANNSCHAFT_TERMINE' )." : "; ?></label>
			</td>
			<td>
			<textarea class="inputbox" name="termine" id="termine" cols="40" rows="2" style="width:100%"><?php echo $row->termine; ?></textarea>
			<br><?php  echo Text::_( 'CLM_TERMINE' ) ; ?>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="lokal"><?php echo Text::_( 'MANNSCHAFT_HOMEPAGE' )." : "; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="homepage" id="homepage"  size="50" value="<?php echo $row->homepage; ?>" />
			</td>
		</tr>



		</table>
		</fieldset>
		</div>

 <div class="width-50 fltrt">
  <fieldset class="adminform">
   <legend><?php echo Text::_( 'REMARKS' ); ?></legend>
	<?php if (is_null($row->bemerkungen)) $row->bemerkungen = ''; ?>
	<?php if (is_null($row->bem_int)) $row->bem_int = ''; ?>
	<table class="adminlist">
	<legend><?php echo Text::_( 'REMARKS_PUBLIC' ); ?></legend>
	<br>
	<tr>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bemerkungen" id="bemerkungen" cols="40" rows="5" style="width:90%"><?php echo $row->bemerkungen;?></textarea>
	</td>
	</tr>
	</table>

	<table class="adminlist">
	<tr><legend><?php echo Text::_( 'REMARKS_INTERNAL' ); ?></legend>
	<br>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bem_int" id="bem_int" cols="40" rows="5" style="width:90%"><?php echo $row->bem_int;?></textarea>
	</td>
	</tr>
	</table>
  </fieldset>
  </div>
		<div class="clr"></div>

		<input type="hidden" name="section" value="mannschaften" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="rankingpos" value="<?php echo $row->rankingpos; ?>" />
		<input type="hidden" name="pre_man" value="<?php echo $row->man_nr; ?>" />
<!---		<input type="hidden" name="cid" value="<?php //echo $row->cid; ?>" />
		<input type="hidden" name="client_id" value="<?php //echo $row->cid; ?>" />
--->		<input type="hidden" name="liste" value="<?php echo $row->liste; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo HTMLHelper::_( 'form.token' ); ?>
		</form>
		<?php
	}
}
