<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

class CLMViewUsers
{
public static function setUsersToolbar()
	{
	// Menubilder laden
		clm_core::$load->load_css("icons_images");

	JToolBarHelper::title( JText::_( 'TITLE_USER' ), 'clm_headmenu_benutzer.png' );
	$clmAccess = clm_core::$access;      

	if($clmAccess->access('BE_user_general') === true) {
		JToolBarHelper::custom('copy_saison','copy.png','copy_f2.png','USER_VORSAISON',false);
		if($clmAccess->access('BE_accessgroup_general') === true) {
			JToolBarHelper::custom('showaccessgroups','specialrankings.png','specialrankings_f2.png', JText::_('ACCESSGROUPS_BUTTON'), false);
			}
		JToolBarHelper::custom('send','send.png','send_f2.png','USER_ACCOUNT',false);
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::custom( 'copy', 'copy.png', 'copy_f2.png', JText::_('COPY') ); 
		JToolBarHelper::deleteList();
		JToolBarHelper::editList();
		JToolBarHelper::addNew();
	}
	JToolBarHelper::help( 'screen.clm.user' );
	}

public static function users( &$rows, &$lists, &$pageNav, $option )
	{
		$mainframe	= JFactory::getApplication();
		CLMViewUsers::setUsersToolbar();
		$user =JFactory::getUser();
		//Ordering allowed ?
		$ordering = ($lists['order'] == 'a.ordering');
		JHtml::_('behavior.tooltip');
		?>
		<form action="index.php?option=com_clm&section=users" method="post" name="adminForm" id="adminForm">

		<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JText::_( 'FILTER' ); ?>:
		<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_( 'GO' ); ?></button>
		<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_catid').value='0';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'RESET' ); ?></button>
			</td>
			<td nowrap="nowrap">
				<?php
		// eigenes Dropdown Menue
			echo "&nbsp;&nbsp;&nbsp;".$lists['sid'];
			echo "&nbsp;&nbsp;&nbsp;".$lists['vid'];
			echo "&nbsp;&nbsp;&nbsp;".$lists['usertype'];
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
						<?php echo JHtml::_('grid.sort',   'USER', 'name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="10%">
						<?php echo JHtml::_('grid.sort',   'USER_FUNCTION', 'd.name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="22%">
						<?php echo JHtml::_('grid.sort',   'VEREIN', 'b.Vereinname', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="11%">
						<?php echo JHtml::_('grid.sort',   'SAISON', 'c.name', @$lists['order_Dir'], @$lists['order']); ?>
					</th>
					<th width="3%">
						<?php echo JHtml::_('grid.sort',   'USER_ACTIVE', 'u.lastvisitDate', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>

					<th width="3%">
						<?php echo JHtml::_('grid.sort',   'USER_MAIL', 'a.aktive', @$lists['order_Dir'], @$lists['order'] ); ?>
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
					<td colspan="12">
						<?php echo $pageNav->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			$row	= JTable::getInstance( 'users', 'TableCLM' );
			for ($i=0, $n=count( $rows ); $i < $n; $i++) {
				//$row = &$rows[$i];
				//$row = $value;
				$row->load( $rows[$i]->id );
				$link 		= JRoute::_( 'index.php?option=com_clm&section=users&task=edit&cid[]='. $row->id );
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
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'USER_EDIT' );?>::<?php echo $row->name; ?>">
							<a href="<?php echo $link; ?>">
								<?php echo $row->name; ?></a></span>
					</td>

					<td align="center">
						<?php if ($rows[$i]->kind == "CLM") echo JText::_('ACCESSGROUP_NAME_'.$rows[$i]->usertype); else echo $rows[$i]->funktion;?>
					</td>

					<td align="center">
						<?php echo $rows[$i]->verein;?>
					</td>
					<td align="center">
						<?php echo $rows[$i]->saison;?>
					</td>

					<td align="center">
						<?php if ($rows[$i]->date=='0000-00-00 00:00:00' OR !$rows[$i]->date) 
							{ ?><img width="16" height="16" src="components/com_clm/images/cancel_f2.png" /> <?php }
						else 	{ ?><img width="16" height="16" src="components/com_clm/images/apply_f2.png" /> <?php }?>
					</td>

					<td align="center">
						<?php if ($rows[$i]->aktive=='1') 
							{ ?><img width="16" height="16" src="components/com_clm/images/apply_f2.png" /> <?php }
						else 	{ ?><img width="16" height="16" src="components/com_clm/images/cancel_f2.png" /> <?php }?>
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

public static function setUserToolbar()
	{

		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		if (JRequest::getVar( 'task') == 'edit') { 
			$text = JText::_( 'Edit' );
		} else { 
			$text = JText::_( 'New' );
		}
		// Menubilder laden
		clm_core::$load->load_css("icons_images");
		JToolBarHelper::title(  JText::_( 'USER' ).': [ '. $text.' ]', 'clm_headmenu_benutzer.png' );
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
		JToolBarHelper::help( 'screen.clm.edit' );
	}

public static function user( &$row,$lists, $option )
	{
		CLMViewUsers::setUserToolbar();
		JRequest::setVar( 'hidemainmenu', 1 );
		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'extrainfo' );
		?>

	<script language="javascript" type="text/javascript">
		 Joomla.submitbutton = function (pressbutton) { 	
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			if (form.pid.value =="0") {
			// do field validation
			if (form.name.value == "") {
					alert( "<?php echo JText::_( 'USER_NAME_ANGEBEN', true ); ?>" );
				} else if (form.username.value == "") {
					alert( "<?php echo JText::_( 'USER_USER_ANGEBEN', true ); ?>" );
				} else if (form.email.value == "") {
					alert( "<?php echo JText::_( 'USER_MAIL_ANGEBEN', true ); ?>" );
				} else if ( getSelectedValue('adminForm','usertype') == "" ) {
					alert( "<?php echo JText::_( 'USER_FUNKTION_AUSWAEHLEN', true ); ?>" );  
				} else if ( getSelectedValue('adminForm','zps') == 0 ) {
					alert( "<?php echo JText::_( 'USER_VEREIN_AUSWAEHLEN', true ); ?>" );
				} else {
					submitform( pressbutton );
				}
			} else {
			// do field validation
				if ( getSelectedValue('adminForm','usertype') == "" ) {
					alert( "<?php echo JText::_( 'USER_FUNKTION_AUSWAEHLEN', true ); ?>" );
				} else if ( getSelectedValue('adminForm','zps') == 0 ) {
				alert( "<?php echo JText::_( 'USER_VEREIN_AUSWAEHLEN', true ); ?>" );
			} else if ( getSelectedValue('adminForm','sid') == 0 ) {
				alert( "<?php echo JText::_( 'USER_SAISON_AUSWAEHLEN', true ); ?>" );
			} else {
				submitform( pressbutton );
			}
				}
		}
		 
		</script>

		<form action="index.php" method="post" name="adminForm" id="adminForm">

		<div class="width-50 fltlft">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'USER_DETAILS' ); ?></legend>

		<table class="admintable">
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'USER_NAME' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="name" id="name" size="30" maxlength="60" value="<?php echo $row->name; ?>" /><?php echo JText::_( 'USER_EXAMPLE_NAME' );?>
			</td>
		</tr>

		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="username"><?php echo JText::_( 'USER' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="username" id="username" size="30" maxlength="60" value="<?php echo $row->username; ?>" /><?php echo JText::_( 'USER_EXAMPLE_USERNAME' );?>
			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'USER_MAIL' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="email" id="email" size="30" maxlength="60" value="<?php echo $row->email; ?>" /><?php echo JText::_( 'USER_EXAMPLE_MAIL' );?>
			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'USER_TELEFON' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="tel_fest" id="tel_fest" size="30" maxlength="60" value="<?php echo $row->tel_fest; ?>" /><?php echo JText::_( 'USER_EXAMPLE_PHONE' );?>
			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'USER_MOBILE' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="tel_mobil" id="tel_mobil" size="30" maxlength="60" value="<?php echo $row->tel_mobil; ?>" /><?php echo JText::_( 'USER_EXAMPLE_MOBILE' );?>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="usertype"><?php echo JText::_( 'USER_FUNCTION' ).' : '; ?></label>
			</td>
			<td>
			<?php echo $lists['usertype']; ?>
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="verein"><?php echo JText::_( 'VEREIN' ).' : '; ?></label>
			</td>
			<td>
			<?php echo $lists['verein']; ?>
			</td>
		</tr>

		<tr>
			<td class="key" width="20%" nowrap="nowrap">
 			<label for="name"><?php echo JText::_( 'USER_MGNR' ).' : '; ?></label>
 			</td>
 			<td>
 			<input class="inputbox" type="text" name="mglnr" id="mglnr" size="30" maxlength="6" value="<?php echo $row->mglnr; ?>" /><?php echo JText::_( 'USER_EXAMPLE_MGNR' );?>
 			</td>
 		</tr>
 
		<tr>
			<td class="key" nowrap="nowrap"><label for="sid"><?php echo JText::_( 'SAISON' ).' : '; ?></label>
			</td>
			<td>
			<?php echo $lists['saison']; ?>
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="published"><?php echo JText::_( 'JPUBLISHED' ).' : '; ?></label>
			</td>
			<td><fieldset class="radio">
			<?php echo $lists['published']; ?>
			</fieldset></td>
		</tr>
		<tr>
<!--			<td class="key" nowrap="nowrap"><label for="aktive"><?php echo JText::_( 'USER_MAIL' ).' : '; ?></label>
			</td>
			<td>
			<?php //echo $lists['aktive']; ?>
			</td>
		</tr>
-->

		</table>
		</fieldset>
		</div>

 <div class="width-50 fltrt">
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'REMARKS' ); ?></legend>
	<table class="adminlist">
	<legend><?php echo JText::_( 'REMARKS_PUBLIC' ); ?></legend>
	<br>
	<tr>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bemerkungen" id="bemerkungen" cols="40" rows="2" style="width:90%"><?php echo str_replace('&','&amp;',$row->bemerkungen);?></textarea>
	</td>
	</tr>
	</table>

	<table class="adminlist">
	<tr><legend><?php echo JText::_( 'REMARKS_INTERNAL' ); ?></legend>
	<br>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bem_int" id="bem_int" cols="40" rows="2" style="width:90%"><?php echo str_replace('&','&amp;',$row->bem_int);?></textarea>
	</td>
	</tr>
	</table>
  </fieldset>
<?php if( JRequest::getVar( 'task') =='add') { ?>
<br>
  <fieldset class="adminform">
	<table class="adminlist">
	<legend><?php echo JText::_( 'USER_LINE01' ); ?></legend>
	<?php echo JText::_( 'USER_LINE02' ); ?>
	<br><?php echo JText::_( 'USER_LINE03' ); ?>.
	<br><br>
	<tr>
	<td width="100%" valign="top">
		<?php echo $lists['jid']; ?>
	</td>
	</tr>
	</table>
   </fieldset>
<?php } else { ?>
<input type="hidden" name="pid" value="0" />
<?php } ?>
  </div>
		<div class="clr"></div>


		<input type="hidden" name="section" value="users" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="jid" id="jid" value="<?php echo $row->jid; ?>" />
		<input type="hidden" name="aktive" value="<?php echo $row->aktive; ?>" />
		<input type="hidden" name="script_task" value="<?php echo JRequest::getVar( 'task'); ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}
} ?>
