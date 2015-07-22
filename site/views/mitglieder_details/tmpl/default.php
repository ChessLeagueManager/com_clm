<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access');

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

// Variablen holen
$sid 		= JRequest::getInt( 'saison', '1' ); 
$zps 		= JRequest::getVar('zps');
$mgl		= JRequest::getInt('mglnr');

// Login Status prüfen
$clmuser 	= $this->clmuser;
$spieler	= $this->spieler;
$verein		= $this->verein;

$user		=& JFactory::getUser();

$mainframe = JFactory::getApplication();
$link = 'index.php';
	
// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');
// Konfigurationsparameter auslesen
$config = &JComponentHelper::getParams( 'com_clm' );

if (!$user->get('id')) {
	$msg = JText::_( 'CLUB_LIST_LOGIN' );
	$mainframe->redirect( $link, $msg );
 			}
if ($clmuser[0]->published < 1) {
	$msg = JText::_( 'CLUB_LIST_ACCOUNT' );
	$mainframe->redirect( $link, $msg );
				}
if ($clmuser[0]->zps <> $zps) {
	$msg = JText::_( 'CLUB_LIST_FALSE' );
	$mainframe->redirect( $link, $msg );
				}

if ($user->get('id') > 0 AND  $clmuser[0]->published > 0 AND $clmuser[0]->zps == $zps){

?>
<div id="clm">
<div id="mitglieder">

	<?php if (!$mgl ) { ?>
    <div class="componentheading"><?php echo JText::_('Spieler nachmelden') ?> ::: <?php echo $verein[0]->name; ?></div>
    <?php } else { ?>
    <div class="componentheading"><?php echo JText::_('Spieler bearbeiten') ?> ::: <?php echo $spieler[0]->Spielername .', '. $verein[0]->name; ?></div>
    <?php }  ?>

    <div class="clmbox">
    <a href="index.php?option=com_clm&amp;view=mitglieder&amp;saison=<?php echo $sid; ?>&amp;zps=<?php echo $zps; ?>">Mitgliederliste</a> | 
    <span>Mitgliederdetails</span> | 
    <a href="#">Mannschaftsf&uuml;hrer</a>  | 
    <a href="index.php?option=com_clm&view=verein&saison=<?php echo $sid; ?>&zps=<?php echo $zps; ?>&layout=vereinsdaten">Vereinsdaten &auml;ndern</a> 
    </div>
    <br />

    <form action="index.php" method="post" name="adminForm">
    
    <table class="mitglieder_details">
        <tr>
            <td>Spielername</td>
            <td><input class="inputbox" type="text" name="name" id="name" size="50" maxlength="100" value="<?php echo $spieler[0]->Spielername; ?>" /></td>
        </tr>
        <tr>
            <td>Mgl. Nr.</td>
            <td><input class="inputbox" type="text" name="mglnr" id="mglnr" size="4" maxlength="100" value="<?php echo $spieler[0]->Mgl_Nr; ?>" /></td>
        </tr>
        <tr>
            <td>DWZ</td>
            <td><input class="inputbox" type="text" name="dwz" id="dwz" size="4" maxlength="100" value="<?php echo $spieler[0]->DWZ; ?>" /></td>
        </tr>
        <tr>
            <td>DWZ Index</td>
            <td><input class="inputbox" type="text" name="dwz_index" id="dwz_index" size="4" maxlength="100" value="<?php echo $spieler[0]->DWZ_Index; ?>" /></td>
        </tr>
        <tr>
            <td>Geschlecht</td>
            <td>       
                    <select size="1" name="geschlecht" id="geschlecht">
                    <option value="M" <?php if ($spieler[0]->Geschlecht =="M"){ ?> selected="selected"<?php } ?>><?php echo JText::_( 'M' ); ?></option> 
                    <option value="W" <?php if ($spieler[0]->Geschlecht =="W"){ ?> selected="selected"<?php } ?>><?php echo JText::_( 'W' ); ?></option> 
                    </select>
            </td>
        </tr>
        <tr>
            <td>Geburtsjahr</td>
            <td><input class="inputbox" name="geburtsjahr" id="geburtsjahr" size="4" maxlength="100" value="<?php echo $spieler[0]->Geburtsjahr; ?>" /></td>
        </tr>
    </table>
    
	<input type="submit" value=" <?php echo JText::_('SUBMIT') ?> ">
	<?php  // Prüfen ob neuer Spieler
    if (!$mgl) { ?>
        <input type="hidden" name="new" value="1" />
    <?php } ?>
	<input type="hidden" name="layout" value="sent" />
	<input type="hidden" name="view" value="mitglieder_details" />
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="saison" value="<?php echo $sid; ?>" />
	<input type="hidden" name="zps" value="<?php echo $zps; ?>" />
    <input type="hidden" name="task" value="" />
    <?php echo JHTML::_( 'form.token' ); ?>
    </form>
<br>
<?php } ?>
<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>
</div>
</div>