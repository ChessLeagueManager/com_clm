<?php
/**
 * @ CLM Component
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
function clm_view_mail($out) {
	clm_core::$load->load_css("mail");
	clm_core::$load->load_css("buttons");
	clm_core::$load->load_css("notification");
	$lang = clm_core::$lang->mail;
	clm_core::$cms->setTitle($lang->title);

	clm_core::$load->load_js("mail");
	$jid = clm_core::$access->getJid();

	$auser = $out["auser"];
	$str_mail_cc = $auser[0]->name." <".$auser[0]->email.">"; 

	$str_mail_to = '';
	// freie Mail an Benutzer
	if ($out["input"]["return_section"] == 'users') {
		$users = $out["users"];
		for ($x=0; $x < (count($users)); $x++) {
			if ($x > 0) $str_mail_to .= ', ';
			$str_mail_to .= $users[$x]->name." <".$users[$x]->email.">"."\r\n"; 
		}
	}
	
	// freie Mail an Mannschaftsleiter
	if ($out["input"]["return_section"] == 'mturniere' OR $out["input"]["return_section"] == 'ligen') {
		$teams = $out["teams"];
		for ($x=0; $x < (count($teams)); $x++) {
			if ($x > 0) $str_mail_to .= ', ';
			$str_mail_to .= $teams[$x]->mfname." <".$teams[$x]->mfmail.">";
			if (($x > 0) AND ($x % 2 == 1)) $str_mail_to .= "\r\n"; 
		}
		$liga = $out["liga"];
		if (($liga[0]->sl > 0) AND ($liga[0]->slmail != $auser[0]->email)) {
			$str_mail_to .= ', '.$liga[0]->slname." <".$liga[0]->slmail.">"; 				
		}
	}
	
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;
	$from = $config->email_from;
	$fromname = $config->email_fromname;

	$str_mail_from = $fromname." <".$from.">"; 

	echo "<h4>".$lang->title;
	echo '  '.$lang->date_on.' '.clm_core::$cms->showDate(time(), $lang->date_format_clm_f);
	echo "</h4>";
?>

<script>
function copytoClipboard(e) {
  // Get the text field
  var copyText = document.getElementById(e);

  // Select the text field
  copyText.select();
  copyText.setSelectionRange(0, 99999); // For mobile devices

  // Copy the text inside the text field
  navigator.clipboard.writeText(copyText.value);
  
  // Alert the copied text
  alert("Copied the text: " + copyText.value);
}
</script>
    
	<div class="outer_mail_subj">
			<div class="info">
				<?php echo $lang->mail_from; ?>
			</div>
			<div class="info_text">
				<?php echo $str_mail_from; ?>
			</div>
	</div>
	<?php if ($out["input"]["return_section"] == 'users') { ?>
		<div class="outer_mail_to">
			<div class="info">
				<?php echo $lang->mail_to; ?>
			</div>
			<div class="info_text">
				<?php echo $str_mail_to; ?>
			</div>
		</div>
	<?php } else { ?>
		<div class="outer_mail_to">
			<div class="info">
				<?php echo $lang->mail_to; ?>
			</div>
			<div class="text">
				<textarea class="mail_to" id="mail_to" oninput="clm_mail_fill_fields(this,false);"><?php echo $str_mail_to; ?></textarea>		
			</div>
			<button type="button" id="to_clipboard" onclick="copytoClipboard('mail_to')">Kopieren in Zwischenablage</button>
		</div>
	<?php } ?>
	<div class="outer_mail_subj">
			<div class="info">
				<?php echo $lang->mail_cc; ?>
			</div>
			<div class="info_text">
				<?php echo $str_mail_cc; ?>
			</div>
	</div>
	<div class="outer_mail_subj">
			<div class="info">
				<?php echo $lang->mail_subject; ?>
			</div>
			<div class="text">
				<textarea class="mail_subj" oninput="clm_mail_fill_fields(this,false);"><?php echo '';?></textarea>
			</div>
	</div>
	<div class="outer_mail_body">
			<div class="info">
				<?php echo $lang->mail_body; ?>
			</div>
			<div class="text">
				<textarea class="mail_body" oninput="clm_mail_fill_fields(this,false);"><?php echo '';?></textarea>
			</div>
	</div>

<?php
echo '<input type="hidden" class="return_section" value="'.$out["input"]["return_section"].'">';
echo '<input type="hidden" class="return_view" value="'.$out["input"]["return_view"].'">';
echo '<input type="hidden" class="cids" value="'.$out["input"]["cids"].'">';

echo '<div class="clm_view_notification"><div class="notice"><span>' . $lang->data_needed . '</div></div>';
echo '<div class="button_container">';
echo '<button type="button" onclick="javascript:history.back(1);" class="clm_button button_back">'.$lang->button_back.'</button>';
echo '<button type="button" onclick="clm_mail_save(this)" class="clm_button button_save" >'.$lang->button_save.'</button>';
echo '</div><div class="space"></div>';
 } ?>
