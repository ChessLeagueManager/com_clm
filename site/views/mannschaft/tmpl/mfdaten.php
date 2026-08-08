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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

// Variablen holen
$sid = clm_core::$load->request_int('saison', 1);
$zps = clm_core::$load->request_string('zps');
$liga = clm_core::$load->request_int('liga', 1);
$tlnr = clm_core::$load->request_int('tlnr', 1);

echo '<div ><div id="vereinsdaten">';

// Login Status prüfen
$clmuser= $this->clmuser;

$user = Factory::getUser();

$mainframe	= Factory::getApplication();
$link = URI::base(true) . 'index.php/component/clm/?view=mannschaft&saison=' . $sid . '&liga=' . $liga . '&tlnr=' . $tlnr . '&Itemid=1';

// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();

if (!$user->get('id')) {
	$msg = Text::_( 'CLUB_DATA_LOGIN' );
	$mainframe->enqueueMessage( $msg );
	$mainframe->redirect( $link );
}

if ($clmuser[0]->published < 1) {
	$msg = Text::_( 'CLUB_DATA_ACCOUNT' );
	$mainframe->enqueueMessage( $msg . " - Zeile 43");
	$mainframe->redirect( $link );
}

if ( $clmuser[0]->usertype == "spl" ) { 
	$msg = Text::_( 'NO_PERMISSION' );
	$mainframe->enqueueMessage( $msg );
	$mainframe->redirect( $link );
}

if ( $clmuser[0]->zps <> $zps ) { 
	$msg = Text::_( 'CLUB_DATA_FALSE' );
	$mainframe->enqueueMessage( $msg . " - Zeile 55");
	$mainframe->redirect( $link );
}

if ($user->get('id') > 0 AND $clmuser[0]->published > 0 AND ($clmuser[0]->zps == $zps OR $clmuser[0]->usertype == "admin")) {

	// Stylesheet laden
	$document = Factory::getDocument();
	$cssDir = 'components'.DS.'com_clm'.DS.'includes';
	$document->addStyleSheet($cssDir.DS.'clm_content_0.css');
	$mannschaft = $this->mannschaft;

	if (!isset($mannschaft[0]->name)) {
		echo '<div class="componentheading">';
		echo Text::_('CLUB_DATA_NOT_EXIST');
		echo "</div>";
	} else {
		// Browsertitelzeile setzen
		$doc =Factory::getDocument();
		$doc->setTitle(Text::_('TEAM_DATA_EDIT').' '.$mannschaft[0]->name . ' (' . $mannschaft[0]->liga_name. ')');
	}
?>
<div class="componentheading"><?php echo Text::_('TEAM_DATA_EDIT') . '&nbsp;:'; ?> <?php echo $mannschaft[0]->name . ' (' . $mannschaft[0]->liga_name. ')'; ?></div>
<br>
<center>
<!-- <form action="index.php?option=com_clm&amp;view=verein&amp;layout=sent" method="post" name="adminForm" id="adminForm"> -->
<form>
	<div class="col width-95">

		<table class="admintable">

		<tr>
			<td class="key" nowrap="nowrap"><label for="lokal"><?php echo Text::_( 'CLUB_DATA_LOCATION' ); ?></label>
			</td>
			<td>
			<textarea class="inputbox"  rows="2" name="lokal" id="lokal"><?php echo $mannschaft[0]->lokal; ?></textarea>
			<br><?php  echo Text::_( 'CLM_ADDRESS' ) ; ?>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="adresse"><?php echo Text::_( 'CLUB_DATA_ADRESS' ); ?></label>
			</td>
			<td>
			<textarea class="inputbox"  rows="2" name="adresse" id="adresse"><?php echo $mannschaft[0]->adresse; ?></textarea>
			<br><?php  echo Text::_( 'CLM_ADDRESS' ) ; ?>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="adresse"><?php echo Text::_( 'TEAM_DATA_MF' ); ?></label> </td>
			<td>
				<select name="newteammf" id="newteammf">
  					<option value="0">--Please choose an option--</option>
<?php foreach ($this->teammf as $mf) {
				if ($mf->jid == $mannschaft[0]->mf) {
					echo '<option value="' . $mf->jid . '" selected>' . $mf->name . "</option>\n";
				} else {
					if ($mf->usertype == 'spl') {
						echo '<option value="' . $mf->jid . '" disabled>' . $mf->name . "</option>\n";
					} else {
						echo '<option value="' . $mf->jid . '">' . $mf->name . "</option>\n";
					}
				}
			}
?>
				</select>
			<br><?php  echo Text::_( 'CLM_SELEKTOR' ) ; ?>
			</td>
		</tr>

	</table>
</div>

<br>
<input type="submit" value=" <?php echo Text::_('TEAM_DATA_SEND_BUTTON') ?> ">
<input type="button" value=" <?php echo Text::_('TEAM_DATA_BACK_BUTTON') ?> " onClick="history.back()">

<input type="hidden" name="name" value="<?php echo $name[0]->Vereinname; ?>" />
<input type="hidden" name="new" value="1" />

		<input type="hidden" name="layout" value="sent" />
		<input type="hidden" name="view" value="mannschaft" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="saison" value="<?php echo $sid; ?>" />
		<input type="hidden" name="tlnr" value="<?php echo $tlnr; ?>" />
		<input type="hidden" name="liga" value="<?php echo $liga; ?>" />
		<input type="hidden" name="zps" value="<?php echo $zps; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo HTMLHelper::_( 'form.token' ); ?>
		</form>
</center>

<?php }
	  
require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); 
echo '</div>';
echo '</div>';
?>
