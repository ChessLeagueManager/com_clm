function clm_modal_str_replace(string, search, replace) {
    return string.split(search).join(replace);
}

function clm_modal_display (text) {
  if(!document.getElementById("clm_modal_fade")) {
		document.body.innerHTML = '<div id="clm_modal_wrapper"><div id="clm_modal_fade" onclick="clm_modal_disable();"></div><div onclick="clm_modal_disable();" id="clm_modal_overlay"></div><a onclick="clm_modal_disable()" href="javascript:void(0)"></div>' + document.body.innerHTML;
  }
  document.getElementById('clm_modal_wrapper').style.display='block';
  document.getElementById('clm_modal_fade').style.display='block';
  document.getElementById('clm_modal_overlay').style.display='block';
  document.getElementById("clm_modal_overlay").innerHTML="";
  document.getElementById("clm_modal_overlay").appendChild(document.createTextNode(text));
  document.getElementById("clm_modal_overlay").innerHTML=clm_modal_str_replace(document.getElementById("clm_modal_overlay").innerHTML,'&lt;br/&gt;', '<br/>');
}

function clm_modal_disable(){
  document.getElementById('clm_modal_wrapper').style.display='none';
  document.getElementById('clm_modal_overlay').style.display='none';
  document.getElementById('clm_modal_fade').style.display='none';
  document.getElementById('clm_modal_overlay').innerHTML='';
}
