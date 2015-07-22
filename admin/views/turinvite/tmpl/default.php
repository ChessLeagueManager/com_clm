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

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">

<?php
$editor =JFactory::getEditor();
echo $editor->display('invitationText', $this->turnier->invitationText, '800', '600', '60', '20', false);

/*
The parameters of the display method are:

   * string $name: The control name
   * string $html: The contents of the text area
   * string $width: The width of the text area (px or %)
   * string $height: The height of the text area (px or %)
   * int $col: The number of columns for the textarea
   * int $row: The number of rows for the textarea
   * boolean $buttons: True and the editor buttons will be displayed
   * array $params: Associative array of editor parameters
  */


?>


		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="view" value="turinvite" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="turinvite" />
		<input type="hidden" name="id" value="<?php echo $this->param['id']; ?>" />
		<?php echo JHtml::_( 'form.token' ); ?>

</form>
