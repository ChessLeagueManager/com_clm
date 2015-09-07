<?php
function clm_view_spoiler($title,$content,$open=false) {
clm_core::$load->load_css("spoiler");
?>
<div class="<?php if (!$open) { echo "clm_view_spoiler_close"; } else { echo "clm_view_spoiler_open"; } ?>" onclick="this.className=this.className=='clm_view_spoiler_close'?'clm_view_spoiler_open':'clm_view_spoiler_close';">
<?php
echo $title;
?>
</div>
<div class="clm_view_spoiler_content">
<?php
echo $content;
?>
</div>
<?php
} ?>
