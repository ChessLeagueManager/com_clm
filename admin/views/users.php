<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
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

        JToolBarHelper::title(JText::_('TITLE_USER'), 'clm_headmenu_benutzer.png');
        $clmAccess = clm_core::$access;

        if ($clmAccess->access('BE_user_general') === true) {
            JToolBarHelper::custom('copy_saison', 'copy.png', 'copy_f2.png', 'USER_VORSAISON', false);
            if ($clmAccess->access('BE_accessgroup_general') === true) {
                JToolBarHelper::custom('showaccessgroups', 'specialrankings.png', 'specialrankings_f2.png', JText::_('ACCESSGROUPS_BUTTON'), false);
            }
            JToolBarHelper::custom('send', 'mail.png', 'mail_f2.png', 'USER_ACCOUNT');
            JToolBarHelper::publishList();
            JToolBarHelper::unpublishList();
            JToolBarHelper::custom('copy', 'copy.png', 'copy_f2.png', JText::_('COPY'));
            JToolBarHelper::deleteList();
            JToolBarHelper::editList();
            JToolBarHelper::addNew();
            JToolBarHelper::custom('email', 'mail.png', 'mail_f2.png', JText::_('USER_MAIL'));
        }
        JToolBarHelper::help('screen.clm.user');
    }

    public static function users(&$rows, &$lists, &$pageNav, $option)
    {
        $mainframe	= JFactory::getApplication();
        CLMViewUsers::setUsersToolbar();
        $user = JFactory::getUser();
        //Ordering allowed ?
        $ordering = ($lists['order'] == 'a.ordering');
        //		JHtml::_('behavior.tooltip');
        require_once(JPATH_COMPONENT_SITE . DS . 'includes' . DS . 'tooltip.php');

        // Auswahlfelder durchsuchbar machen
        clm_core::$load->load_js("suche_liste");
        ?>
		<form action="index.php?option=com_clm&section=users" method="post" name="adminForm" id="adminForm">

		<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JText::_('FILTER'); ?>:
		<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_('GO'); ?></button>
		<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_catid').value='0';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_('RESET'); ?></button>
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
						<?php echo JHtml::_('grid.sort', 'USER', 'name', @$lists['order_Dir'], @$lists['order']); ?>
					</th>
					<th width="10%">
						<?php echo JHtml::_('grid.sort', 'USER_FUNCTION', 'd.name', @$lists['order_Dir'], @$lists['order']); ?>
					</th>
					<th width="22%">
						<?php echo JHtml::_('grid.sort', 'VEREIN', 'b.Vereinname', @$lists['order_Dir'], @$lists['order']); ?>
					</th>
					<th width="11%">
						<?php echo JHtml::_('grid.sort', 'SAISON', 'c.name', @$lists['order_Dir'], @$lists['order']); ?>
					</th>
					<th width="3%">
						<?php echo JHtml::_('grid.sort', 'USER_ACTIVE', 'u.lastvisitDate', @$lists['order_Dir'], @$lists['order']); ?>
					</th>

					<th width="3%">
						<?php echo JHtml::_('grid.sort', 'USER_MAIL', 'a.aktive', @$lists['order_Dir'], @$lists['order']); ?>
					</th>

					<th width="6%">
						<?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'a.published', @$lists['order_Dir'], @$lists['order']); ?>
					</th>
					<th width="8%" nowrap="nowrap">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'a.ordering', @$lists['order_Dir'], @$lists['order']); ?>
						<?php echo JHtml::_('grid.order', $rows); ?>
					</th>
					<th width="1%" nowrap="nowrap">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', @$lists['order_Dir'], @$lists['order']); ?>
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
        $row	= JTable::getInstance('users', 'TableCLM');
        for ($i = 0, $n = count($rows); $i < $n; $i++) {
            //$row = &$rows[$i];
            //$row = $value;
            $row->load($rows[$i]->id);
            $link 		= JRoute::_('index.php?option=com_clm&section=users&task=edit&id='. $row->id);
            $checked 	= JHtml::_('grid.checkedout', $row, $i);
            //				$published 	= JHtml::_('grid.published', $row, $i );
            $published 	= JHtml::_('jgrid.published', $row->published, $i);

            ?>
				<tr class="<?php echo 'row'. $k; ?>">

					<td align="center">
						<?php echo $pageNav->getRowOffset($i); ?>
					</td>

					<td>
						<?php echo $checked; ?>
					</td>

					<td>
								<span class="editlinktip hasTip" title="<?php echo JText::_('USER_EDIT');?>::<?php echo $row->name; ?>">
							<a href="<?php echo $link; ?>">
								<?php echo $row->name; ?></a></span>
					</td>

					<td align="center">
						<?php if ($rows[$i]->kind == "CLM") {
						    echo JText::_('ACCESSGROUP_NAME_'.$rows[$i]->usertype);
						} else {
						    echo $rows[$i]->funktion;
						}?>
					</td>

					<td align="center">
						<?php echo $rows[$i]->verein;?>
					</td>
					<td align="center">
						<?php echo $rows[$i]->saison;?>
					</td>

					<td align="center">
						<?php if ($rows[$i]->date == '0000-00-00 00:00:00' or $rows[$i]->date == '1970-01-01 00:00:00' or !$rows[$i]->date) { ?><img width="16" height="16" src="components/com_clm/images/cancel_f2.png" /> <?php } else { ?><img width="16" height="16" src="components/com_clm/images/apply_f2.png" /> <?php }?>
					</td>

					<td align="center">
						<?php if ($rows[$i]->aktive == '1') { ?><img width="16" height="16" src="components/com_clm/images/apply_f2.png" /> <?php } else { ?><img width="16" height="16" src="components/com_clm/images/cancel_f2.png" /> <?php }?>
					</td>

					<td align="center">
						<?php echo $published;?>
					</td>
 
					<td class="order">
						<?php $disabled = $ordering ? '' : 'disabled="disabled"'; ?>
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
		<?php echo JHtml::_('form.token'); ?>
		</form>
		<?php
    }

    public static function setUserToolbar()
    {

        if (clm_core::$load->request_string('task') == 'edit') {
            $text = JText::_('Edit');
        } else {
            $text = JText::_('New');
        }
        // Menubilder laden
        clm_core::$load->load_css("icons_images");
        JToolBarHelper::title(JText::_('USER').': [ '. $text.' ]', 'clm_headmenu_benutzer.png');
        JToolBarHelper::save();
        JToolBarHelper::apply();
        JToolBarHelper::cancel();
        JToolBarHelper::help('screen.clm.edit');
    }

    public static function user(&$row, $lists, $option)
    {
        CLMViewUsers::setUserToolbar();
        $_REQUEST['hidemainmenu'] = 1;
        JFilterOutput::objectHTMLSafe($row, ENT_QUOTES, 'extrainfo');

        // Konfigurationsparameter auslesen
        $config = clm_core::$db->config();
        $conf_user_member	= $config->user_member;
        $countryversion = $config->countryversion;
        $email_independent = $config->email_independent;

        $_REQUEST['clm_user_member'] = $conf_user_member;
        clm_core::$load->load_js("users");

        // Auswahlfelder durchsuchbar machen
        clm_core::$load->load_js("suche_liste");
        ?>


		<form action="index.php" method="post" name="adminForm" id="adminForm">

		<div class="width-50 fltlft">
		<fieldset class="adminform">
		<legend><?php echo JText::_('USER_DETAILS'); ?></legend>

		<table class="admintable">
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_('USER_NAME').' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="name" id="name" size="30" maxlength="60" value="<?php echo $row->name; ?>" /><?php echo JText::_('USER_EXAMPLE_NAME');?>
			</td>
		</tr>

		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="username"><?php echo JText::_('USER').' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="username" id="username" size="30" maxlength="60" value="<?php echo $row->username; ?>" /><?php echo JText::_('USER_EXAMPLE_USERNAME');?>
			</td>
		</tr>
		<?php if ($email_independent == 0) { ?>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="email"><?php echo JText::_('USER_MAIL').' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="email" id="email" size="30" maxlength="60" value="<?php echo $row->email; ?>" /><?php echo JText::_('USER_EXAMPLE_MAIL');?>
			</td>
		</tr>
		<?php }
		if ($email_independent == 1) { ?>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="email"><?php echo JText::_('USER_MAIL_CLM').' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="email" id="email" size="30" maxlength="60" value="<?php echo $row->email; ?>" /><?php echo JText::_('USER_EXAMPLE_MAIL');?>
			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="jmail"><?php echo JText::_('USER_MAIL_JOOMLA').' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="jmail" id="jmail" size="30" maxlength="60" value="<?php echo $row->jmail; ?>" /><?php echo JText::_('USER_EXAMPLE_MAIL');?>
			</td>
		</tr>
		<?php } ?>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="tel_fest"><?php echo JText::_('USER_TELEFON').' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="tel_fest" id="tel_fest" size="30" maxlength="60" value="<?php echo $row->tel_fest; ?>" /><?php echo JText::_('USER_EXAMPLE_PHONE');?>
			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="tel_mobil"><?php echo JText::_('USER_MOBILE').' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="tel_mobil" id="tel_mobil" size="30" maxlength="60" value="<?php echo $row->tel_mobil; ?>" /><?php echo JText::_('USER_EXAMPLE_MOBILE');?>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="usertype"><?php echo JText::_('USER_FUNCTION').' : '; ?></label>
			</td>
			<td>
			<?php echo $lists['usertype']; ?>
			</td>
		</tr>

		<tr>
			<td class="key" width="20%" nowrap="nowrap">
 			<label for="fideid"><?php echo JText::_('USER_FIDEID').' : '; ?></label>
 			</td>
 			<td>
 			<input class="inputbox" type="text" name="fideid" id="fideid" size="30" maxlength="9" value="<?php echo $row->fideid; ?>" title="<?php echo JText::_('USER_FIDEID_HINT');?>" /><?php echo JText::_('USER_EXAMPLE_FIDEID');?>
 			</td>
 		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="verein"><?php echo JText::_('VEREIN').' : '; ?></label>
			</td>
			<td>
			<?php echo $lists['verein']; ?>
			</td>
		</tr>

		<tr>
			<td class="key" width="20%" nowrap="nowrap">
 			<label for="name"><?php echo JText::_('USER_MGNR').' : '; ?></label>
 			</td>
 			<td>
 			<input class="inputbox" type="text" name="mglnr" id="mglnr" size="30" maxlength="6" value="<?php echo $row->mglnr; ?>" /><?php echo JText::_('USER_EXAMPLE_MGNR');?>
 			</td>
 		</tr>

		<tr>
			<td class="key" width="20%" nowrap="nowrap">
 			<label for="name"><?php if ($countryversion == "de") {
 			    echo JText::_('USER_PKZ').' : ';
 			} else {
 			    echo JText::_('USER_PKZ_EN').' : ';
 			} ?></label>
 			</td>
 			<td>
 			<input class="inputbox" type="text" name="PKZ" id="PKZ" size="30" maxlength="9" value="<?php echo $row->PKZ; ?>" /><?php echo JText::_('USER_EXAMPLE_PKZ');?>
 			</td>
 		</tr>
	<?php if ($conf_user_member == 1) { ?>
		<tr>
			<td class="key" nowrap="nowrap"><label for="org_exc"><?php echo JText::_('OPTION_ORG_EXC').' : '; ?></label>
			</td>
			<td><fieldset class="radio">
			<?php echo JHtml::_('select.booleanlist', 'org_exc', 'class="inputbox"', $row->org_exc); ?>
			</fieldset></td>
		</tr>
	<?php } ?>
		<tr>
			<td class="key" nowrap="nowrap"><label for="sid"><?php echo JText::_('SAISON').' : '; ?></label>
			</td>
			<td>
			<?php echo $lists['saison']; ?>
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="published"><?php echo JText::_('JPUBLISHED').' : '; ?></label>
			</td>
			<td><fieldset class="radio">
			<?php echo $lists['published']; ?>
			</fieldset></td>
		</tr>

		</table>
		</fieldset>
		</div>

 <div class="width-50 fltrt">
  <fieldset class="adminform">
   <legend><?php echo JText::_('REMARKS'); ?></legend>
	<table class="adminlist">
	<legend><?php echo JText::_('REMARKS_PUBLIC'); ?></legend>
	<br>
	<tr>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bemerkungen" id="bemerkungen" cols="40" rows="2" style="width:90%"><?php echo str_replace('&', '&amp;', $row->bemerkungen);?></textarea>
	</td>
	</tr>
	</table>

	<table class="adminlist">
	<tr><legend><?php echo JText::_('REMARKS_INTERNAL'); ?></legend>
	<br>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bem_int" id="bem_int" cols="40" rows="2" style="width:90%"><?php echo str_replace('&', '&amp;', $row->bem_int);?></textarea>
	</td>
	</tr>
	</table>
  </fieldset>
<?php if (clm_core::$load->request_string('task') == 'add') { ?>
<br>
  <fieldset class="adminform">
	<table class="adminlist">
	<legend><?php echo JText::_('USER_LINE01'); ?></legend>
	<?php echo JText::_('USER_LINE02'); ?>
	<br><?php echo JText::_('USER_LINE03'); ?>.
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
		<input type="hidden" name="script_task" value="<?php echo clm_core::$load->request_string('task'); ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
		</form>
		<?php
    }
} ?>
