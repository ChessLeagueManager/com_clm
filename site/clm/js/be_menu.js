clmQuery(window).load(function(){
	clmQuery.each(clmQuery(".clm .clm_view_be_menu"), function(index, id) {
		var wrapperWidth = clmQuery(id).innerWidth();
		if(wrapperWidth<650) {
			return;
		}
		var gridWidth = (wrapperWidth - 380);
		clmQuery(id).find(".clm-menu").css('width', gridWidth + 'px');
	});
});
clmQuery( window ).resize(function() {
	clmQuery.each(clmQuery(".clm .clm_view_be_menu"), function(index, id) {
		var wrapperWidth = clmQuery(id).innerWidth();
		if(wrapperWidth<650) {
			return;
		}
		var gridWidth = (wrapperWidth - 380);
		clmQuery(id).find(".clm-menu").css('width', gridWidth + 'px');
	});
});
