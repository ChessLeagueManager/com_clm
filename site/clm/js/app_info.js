clmQuery(document).ready(function($) {

	$( ".clm_view_app_info .clm_qrcode .clm_qrcode_direct" ).each(function() {
		qrcode = new QRCode(this, {
			width : 200,
			height : 200
		});
		qrcode.makeCode("market://details?id=com.ChessLeagueManager.main&hl=de");
	});
	
	$( ".clm_view_app_info .clm_button" ).click(function() {
		inputs = this.parentNode.getElementsByTagName("input");
		
		this.parentNode.parentNode.getElementsByClassName("clm_qrcode_gen")[0].innerHTML="";
		this.parentNode.parentNode.getElementsByClassName("clm_qrcode_gen")[0].title="";
		
		if(inputs[0].value!="" && inputs[1].value!="") {
			if(clm_app_info_https==0) {
				url = "http://";		
			} else {
				url = "https://";		
			}
			url += clm_app_info_url+"/components/com_clm/clm/index.php?view=view_report&name="+inputs[0].value+"&password="+inputs[1].value;
			qrcode = new QRCode(this.parentNode.parentNode.getElementsByClassName("clm_qrcode_gen")[0], {
				width : 200,
				height : 200
			});
			qrcode.makeCode(url);
			this.parentNode.parentNode.getElementsByTagName("a")[0].href=url;
			this.parentNode.parentNode.getElementsByTagName("a")[0].className = "";
			this.parentNode.getElementsByClassName("clm_view_notification")[0].innerHTML=""
		} else {
			this.parentNode.parentNode.getElementsByTagName("a")[0].href="";
			this.parentNode.parentNode.getElementsByTagName("a")[0].className = "clm_disabled";
			this.parentNode.getElementsByClassName("clm_view_notification")[0].innerHTML="<div class='error'>"+clm_app_info_empty+"</div>"
		}
		
	});
	
	
});