clmQuery(document).ready(function(){
	clmQuery('.pleaseWaitAfterClick, .btn-toolbar button').click(function(){
		clmQuery('pleaseWaitAfterClick, .btn-toolbar button').text(clm_submit_pleaseWait);
	});
	clmQuery('.disableAfterClick, .btn-toolbar button').click(function(){
		clmQuery('.disableAfterClick, .btn-toolbar button').attr("disabled", true);	
	});
});