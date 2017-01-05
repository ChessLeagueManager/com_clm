	function startPgnMatch(id, areaname) {
		var p = 0;
		Ende: while ( p < 500 ) {
			var old_name = 'pgnArea' + p;
			var element = document.getElementById(old_name);
			if (element != null) {
				document.getElementById(old_name).innerHTML = '';
				p++;
			} else {
				break Ende;
			}
		}
		document.getElementById(areaname).innerHTML = '';
		var tableStart = '<table><TR><TD align="right"><span class="editlinktip hasTip" title="' + text['pgnClose'] + '"><a onclick="closePgnMatch(\'' + areaname + '\');" class="pgn">x</a></span></TD></TR><tr><TD>';
		var tableEnd = '</TD></tr>';
		var div1 = '<div id="' + randomid + '_board" width="100%"></div>';
		var div2 = '<div id="' + randomid + '" style="visibility:hidden; display:none">';
		document.getElementById(areaname).innerHTML = tableStart + div1 + div2 + document.getElementById('pgnhidden' + id).value + '</div>' +  tableEnd;
	
		var brd = new Board(randomid, {'imagePrefix':imagepath + param['fe_pgn_style'] + '/',
											'showMovesPane':true,
											'commentFontSize':'10pt',
											'moveFontColor':param['fe_pgn_moveFont'],
											'commentFontColor':param['fe_pgn_commentFont'],
											'squareSize':'32px',
											'markLastMove':false,
											
											'blackSqColor':'url("'+ imagepath + 'zurich/board/darksquare.gif")',
											'lightSqColor':'url("'+ imagepath + 'zurich/board/lightsquare.gif")',
											
											'squareBorder':"0px solid #000000",
											'altRewind':text['altRewind'],
											'altBack':text['altBack'],
											'altFlip':text['altFlip'],
											'altShowMoves':text['altShowMoves'],
											'altComments':text['altComments'],
											'altPlayMove':text['altPlayMove'],
											'altFastForward':text['altFastForward'],
											'moveBorder':"1px solid #cccccc"
											});
		brd.init()
	
	}

	function closePgnMatch(areaname) {
		document.getElementById(areaname).innerHTML = '';
	}
