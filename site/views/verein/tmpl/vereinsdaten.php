<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access');

// Variablen holen
$sid = JRequest::getInt('saison','1');
$zps = JRequest::getVar('zps');

echo '<div ><div id="vereinsdaten">';

// Login Status prüfen
$clmuser= $this->clmuser;
$user	= JFactory::getUser();

	$mainframe	= JFactory::getApplication();

	$link = JURI::base() .'index.php?option=com_clm&view=verein&saison='. $sid .'&zps='. $zps;

// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	$conf_vereinsdaten=$config->conf_vereinsdaten;

if ($conf_vereinsdaten != 1) {
	$msg = JText::_( 'CLUB_DATA_DISABLED');
	$link = "index.php?option=com_clm&view=info";
	$mainframe->redirect( $link, $msg );
			}
if (!$user->get('id')) {
	$msg = JText::_( 'CLUB_DATA_LOGIN' );
	$mainframe->redirect( $link, $msg );
 			}
if ($clmuser[0]->published < 1) { 
	$msg = JText::_( 'CLUB_DATA_ACCOUNT' );
	$mainframe->redirect( $link, $msg );
				}
if ( $clmuser[0]->usertype == "spl" OR $clmuser[0]->zps <> $zps ) { 
		$msg = JText::_( 'CLUB_DATA_FALSE' );
		$mainframe->redirect( $link, $msg );
		}
if ($user->get('id') > 0 AND  $clmuser[0]->published > 0 AND $clmuser[0]->zps == $zps  OR $clmuser[0]->usertype == "admin"){

	$document = JFactory::getDocument();
	$cssDir = JURI::base().DS. 'components'.DS.'com_clm'.DS.'includes';
	$document->addStyleSheet( $cssDir.DS.'clm_content.css', 'text/css', null, array() );

$row 	= $this->row;

if (!isset($row[0]->name)) { ?>
<div class="componentheading"><?php echo JText::_('CLUB_DATA_NOT_EXIST') ?></div>
<?php } else {

	// Browsertitelzeile setzen
	$doc =JFactory::getDocument();
	$doc->setTitle(JText::_('CLUB_DATA_EDIT').' '.$row[0]->name);
 ?>
<div class="componentheading"><?php echo JText::_('CLUB_DATA_EDIT') . '&nbsp;:'; ?> <?php echo $row[0]->name; ?></div>
<br>
<div id="desc"><?php echo JText::_('CLUB_DATA_NOTE') ?></div>
<br>
<center>
<form action="index.php" method="post" name="adminForm">
		<div class="col width-95">

		<table class="admintable">

		<tr>
			<td class="key" nowrap="nowrap"><label for="lokal"><?php echo JText::_( 'CLUB_DATA_LOCATION' ); ?></label>
			</td>
			<td>
			<textarea class="inputbox"  rows="2" name="lokal" id="lokal"><?php echo $row[0]->lokal; ?></textarea>
			<br><?php  echo JText::_( 'CLM_ADDRESS' ) ; ?>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="adresse"><?php echo JText::_( 'CLUB_DATA_ADRESS' ); ?></label>
			</td>
			<td>
			<textarea class="inputbox"  rows="2" name="adresse" id="adresse"><?php echo $row[0]->adresse; ?></textarea>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="termine"><?php echo JText::_( 'CLUB_DATA_DATE' ); ?></label>
			</td>
			<td>
			<textarea class="inputbox" rows="2" name="termine" id="termine"><?php echo $row[0]->termine; ?></textarea>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="homepage"><?php echo JText::_( 'CLUB_DATA_HOMEPAGE' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="homepage" id="homepage" size="70" maxlength="100" value="<?php echo $row[0]->homepage; ?>" />
			</td>
		</tr>

<tr><td colspan="2"><hr></td></tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="vs"><?php echo JText::_( 'CLUB_DATA_CHIEF' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="vs" id="vs" size="70" maxlength="100" value="<?php echo $row[0]->vs; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="vs_mail"><?php echo JText::_( 'CLUB_DATA_MAIL' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="vs_mail" id="vs_mail" size="70" maxlength="100" value="<?php echo $row[0]->vs_mail; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="vs_tel"><?php echo JText::_( 'CLUB_DATA_PHONE' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="vs_tel" id="vs_tel" size="70" maxlength="100" value="<?php echo $row[0]->vs_tel; ?>" />
			</td>
		</tr>
<tr><td colspan="2"><hr></td></tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="tl"><?php echo JText::_( 'CLUB_DATA_TOURNAMENT' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="tl" id="tl" size="70" maxlength="100" value="<?php echo $row[0]->tl; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="tl_mail"><?php echo JText::_( 'CLUB_DATA_MAIL' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="tl_mail" id="tl_mail" size="70" maxlength="100" value="<?php echo $row[0]->tl_mail; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="tl_tel"><?php echo JText::_( 'CLUB_DATA_PHONE' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="tl_tel" id="tl_tel" size="70" maxlength="100" value="<?php echo $row[0]->tl_tel; ?>" />
			</td>
		</tr>
<tr><td colspan="2"><hr></td></tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="jw"><?php echo JText::_( 'CLUB_DATA_YOUTH' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="jw" id="jw" size="70" maxlength="100" value="<?php echo $row[0]->jw; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="jw_mail"><?php echo JText::_( 'CLUB_DATA_MAIL' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="jw_mail" id="jw_mail" size="70" maxlength="100" value="<?php echo $row[0]->jw_mail; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="jw_tel"><?php echo JText::_( 'CLUB_DATA_PHONE' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="jw_tel" id="jw_tel" size="70" maxlength="100" value="<?php echo $row[0]->jw_tel; ?>" />
			</td>
		</tr>
<tr><td colspan="2"><hr></td></tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="pw"><?php echo JText::_( 'CLUB_DATA_PRESS' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="pw" id="pw" size="70" maxlength="100" value="<?php echo $row[0]->pw; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="pw_mail"><?php echo JText::_( 'CLUB_DATA_MAIL' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="pw_mail" id="pw_mail" size="70" maxlength="100" value="<?php echo $row[0]->pw_mail; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="pw_tel"><?php echo JText::_( 'CLUB_DATA_PHONE' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="pw_tel" id="pw_tel" size="70" maxlength="100" value="<?php echo $row[0]->pw_tel; ?>" />
			</td>
		</tr>
<tr><td colspan="2"><hr></td></tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="kw"><?php echo JText::_( 'CLUB_DATA_MONEY' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="kw" id="kw" size="70" maxlength="100" value="<?php echo $row[0]->kw; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="kw_mail"><?php echo JText::_( 'CLUB_DATA_MAIL' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="kw_mail" id="kw_mail" size="70" maxlength="100" value="<?php echo $row[0]->kw_mail; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="kw_tel"><?php echo JText::_( 'CLUB_DATA_PHONE' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="kw_tel" id="kw_tel" size="70" maxlength="100" value="<?php echo $row[0]->kw_tel; ?>" />
			</td>
		</tr>
<tr><td colspan="2"><hr></td></tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="sw"><?php echo JText::_( 'CLUB_DATA_SENIOR' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="sw" id="sw" size="70" maxlength="100" value="<?php echo $row[0]->sw; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="sw_mail"><?php echo JText::_( 'CLUB_DATA_MAIL' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="sw_mail" id="sw_mail" size="70" maxlength="100" value="<?php echo $row[0]->sw_mail; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="sw_tel"><?php echo JText::_( 'CLUB_DATA_PHONE' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="sw_tel" id="sw_tel" size="70" maxlength="100" value="<?php echo $row[0]->sw_tel; ?>" />
			</td>
		</tr>
		</table>
</div>

<br>
<input type="submit" value=" <?php echo JText::_('CLUB_DATA_SEND_BUTTON') ?> ">
<input type="button" value=" <?php echo JText::_('CLUB_DATA_BACK_BUTTON') ?> " onClick="history.back()">
<?php 
// Keine Vereinsdaten eingegeben
if (!$row[0]->name) { 
$name = $this->name;
?>
<input type="hidden" name="name" value="<?php echo $name[0]->Vereinname; ?>" />
<input type="hidden" name="new" value="1" />
<?php } ?>
		<input type="hidden" name="layout" value="sent" />
		<input type="hidden" name="view" value="verein" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="saison" value="<?php echo $sid; ?>" />
		<input type="hidden" name="zps" value="<?php echo $zps; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
<?php } ?>
</center>
<?php } ?>

<br>

</div>
</div>
<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>
