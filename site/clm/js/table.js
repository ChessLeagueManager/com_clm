clmQuery(document).ready(function($) {
    $.each($(".clm .special-table"), function(index, id) {
        $(id).find(".special-table-main").dataTable({
	    "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Alle"]],
	    "order": JSON.parse(id.getElementsByClassName("order")[0].innerHTML),
            "dom": '<"special-table-around"><"main-table"tr><"main-table-bottom"ilp>',
            "processing": true,
            "serverSide": true,
  		 "columnDefs": [
    			{ "orderable": false, "targets": 0 }
  				],
	    "pageLength": clm_table_pageLength,
            "ajax": {
                "url": clm_table_url,
                "type": "POST",
                "data": function(d) {
                    d.command = JSON.stringify(Array(1, Array($(".destination").html())));
                    d.names = Array();
                    d.values = Array();
                    $(id).find(".special-table-custom-input").each(function(index, id2) {
                        d.names[d.names.length] = $(id2).attr("name");
                        d.values[d.values.length] = $(id2).val();
                    });
                }
            },
            "language": {
                "sEmptyTable": clm_table_sEmptyTable,
                "sInfo": clm_table_sInfo,
                "sInfoEmpty": clm_table_sInfoEmpty,
                "sInfoFiltered": clm_table_sInfoFiltered,
                "sInfoPostFix": clm_table_sInfoPostFix,
                "sInfoThousands": clm_table_sInfoThousands,
                "sLengthMenu": clm_table_sLengthMenu,
                "sLoadingRecords": clm_table_sLoadingRecords,
                "sProcessing": clm_table_sProcessing,
                "sSearch": clm_table_sSearch,
                "sZeroRecords": clm_table_sZeroRecords,
                "oPaginate": {
                    "sFirst": clm_table_sFirst,
                    "sPrevious": clm_table_sPrevious,
                    "sNext": clm_table_sNext,
                    "sLast": clm_table_sLast
                },
                "oAria": {
                    "sSortAscending": clm_table_sSortAscending,
                    "sSortDescending": clm_table_sSortDescending
                }
            }
        });
    	// Ordering versetzen
      if (this.getElementsByClassName("clm_table_ordering").length > 0) {
      	$("div.clm_table_filter").append(this.getElementsByClassName("clm_table_ordering")[0]);
      }
		// Umbau der Struktur
      if (document.getElementsByClassName('clm_table_filter').length > 0) {
      	var newParent = document.getElementsByClassName('special-table-around')[0];
      	var oldParent = document.getElementsByClassName('clm_table_filter')[0];
      	newParent.appendChild(oldParent);
      }
		// Filter führen zum aktualisieren der Tabelle
      var table = $(this).find(".special-table-main").DataTable();
      $(this).find(".special-table-custom-input").on("change", function() {
      	table.draw();
      });
      $(this).find(".clm_table_filter_box").on("keyup", function() {
      	table.search($(this).val()).draw();
      });
		// Auswahl
      $(this).find('tbody').on('click', 'tr', function() {
      	$(this).toggleClass('selected');
         if (table.rows('.selected').data().length > 0) {
         	$(id).find(".clm_table_event").attr("disabled", false);
         	$(id).find(".clm_table_multi").attr("disabled", false);
         } else {
         	$(id).find(".clm_table_event").attr("disabled", true);
         	$(id).find(".clm_table_multi").attr("disabled", true);
         }
      });
      // Keine (doppelte) Änderung des Auswahlstatus
      $(this).find('tbody').on('click', '.clm_table_orderingBox', function() {
      	$(this).parent().parent().toggleClass('selected');
      });
      $(this).find('tbody').on('click', '.clm_table_image', function() {
         $(this).parent().parent().toggleClass('selected');
         if ($(this).hasClass("clm_table_confirm")) {
            if (!confirm(clm_table_really)) {
            	return;
         	}
         }
      	clm_table_button($(this).val(), new Array(1), this, true);
      });
      // URL unabhängig der Auswahl aufrufen
      $(this).find('.clm_table_url').click(function() {
      	window.location = $(this).val();
      });
      // Tab für jedes gewählte Element erzeugen
      $(this).find('.clm_table_multi').click(function() {
      	for (var i = 0; i < table.rows('.selected').data().length; i++) {
      	      		var child = window.open($(this).val()+table.rows('.selected').data()[i][table.rows('.selected').data()[i].length-1],"_blank");
      	      		child.blur();
      	      		window.focus();
      	}
      });
      // Form mit den gewählten IDs absenden.
      $(this).find('.clm_table_form').click(function() {
			clm_submit_post_via_hidden_form($(this).val(),table.rows('.selected').data());
      });
      // Ordering speichern
      $(this).find('.clm_table_ordering').click(function() {
      	clm_table_button($(this).val(), new Array(1), this, false);
      });
      // Bei jeder Aktualisierung der Tabelle müssen die Buttons deaktiviert werden
      $(this).find(".special-table-main").on('draw.dt', function() {
      	id.getElementsByTagName("th")[0].innerHTML=table.rows().data().length;
      	$(id).find(".clm_table_event").attr("disabled", true);
      	$(id).find(".clm_table_multi").attr("disabled", true);
      });
      // Events ausführen
      $(this).find('.clm_table_event, .clm_table_event_special').click(function() {
      if ($(this).hasClass("clm_table_event_special") || table.rows('.selected').data().length > 0) {
         	if ($(this).hasClass("clm_table_confirm")) {
            	if (!confirm(clm_table_really)) {
               	return;
               }
            }
      		clm_table_button($(this).val(), table.rows('.selected').data(), this, false);
      	}
      });
   	// Alles auswählen/abwählen
      $(this).find('.clm_table_check').on('click', function() {
      	if (table.rows('.selected').data().length < table.rows().data().length) {
         	var on = true;
         	$(id).find(".clm_table_event").attr("disabled", false);
         	$(id).find(".clm_table_multi").attr("disabled", false);
         } else {
         	var off = false;
         	$(id).find(".clm_table_event").attr("disabled", true);
         	$(id).find(".clm_table_multi").attr("disabled", true);
         }
         $(id).find("tr").toggleClass('selected', on);
      });
      // Reload wenn Login abgelaufen 
      $(this).find(".special-table-main").on('xhr.dt', function ( e, settings, json ) {
      	 if(typeof json == 'string' && json == 'e_noRights') {
		location.reload(true);
	 }
      });	
    });
});

function clm_table_button(val, result, buttonDiv, special) {
	
	 if(result.length==0) {
	 	result = new Array(1);
    	result[0] = new Array(1);
    	result[0][0] = null;
    }


    // Aus der Tabelle heraus oder über einen Button über dieser?
    if (special) {
        resultMessageDiv = buttonDiv.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("clm_view_notification")[0];
    } else {
        resultMessageDiv = buttonDiv.parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("clm_view_notification")[0];
    }

    var xmlhttp;
    if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else { // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                try {
                    var out = JSON.parse(xmlhttp.responseText);
                } catch (e) {
                    resultMessageDiv.innerHTML = "<div class='error'>" + clm_table_error1 + "</div>";
                    return;
                }
                var errorIds = "";
                for (var i = 0; i < out.length; i++) {
                    if (!out[i][0]) {
                        if (out.length == 1 && Object.keys(out[0]).length == 4) {
                            if (special || result[i][result[i].length - 1] == null) {
                                errorIds = out[i][3];
                            } else {
                            	  errorIds = out[i][3] + " (" + result[i][result[i].length - 1] + ")";
                            }
                            break;
                        }
                        if (errorIds != "") {
                            errorIds += ", ";
                        }
                        errorIds += result[i][result[i].length - 1];
                    }
                }

                if (errorIds != "" && out.length == 1 && Object.keys(out[0]).length == 4) {
                    resultMessageDiv.innerHTML = "<div class='warning'>" + errorIds + "</div>";
                } else if (errorIds != "") {
                    resultMessageDiv.innerHTML = "<div class='warning'>" + clm_table_error2 + " (" + errorIds + ")</div>";
                } else {
                    resultMessageDiv.innerHTML = "<div class='success'>" + clm_table_success + "</div>";
                }
                var table = clmQuery(resultMessageDiv.parentNode).find(".special-table-main").DataTable();
                table.draw();
            } else {
                resultMessageDiv.innerHTML = "<div class='error'>" + clm_table_error0 + "</div>";
            }
        }
    }
    xmlhttp.open("POST", clm_table_url, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    var command = new Array(result.length + 1);
    command[0] = 2;
    for (var i = 1; i <= result.length; i++) {
       command[i] = JSON.parse(val);
       if (Array.isArray(result[i - 1])) {
           command[i][1][0] = result[i - 1][result[i - 1].length - 1];
       } else if (command[i][0] == "db_ordering") {
       	ordering = resultMessageDiv.parentNode.getElementsByClassName("clm_table_orderingBox");
       	command[i][1][0] = new Array(ordering.length);
       	command[i][1][1] = new Array(ordering.length);
       	for (var p = 0; p < ordering.length; p++) {
         	command[i][1][1][p] = resultMessageDiv.parentNode.getElementsByClassName("clm_table_orderingBox")[p].value;
         	command[i][1][0][p] = resultMessageDiv.parentNode.getElementsByClassName("clm_table_orderingId")[p].value;
         }
       }
    }
    xmlhttp.send('command=' + JSON.stringify(command));
    resultMessageDiv.innerHTML = "<div class='notice'>" + clm_table_running + "</div>";
}
// http://stackoverflow.com/questions/7024040/jquery-open-page-in-a-tab-and-pass-some-post-values
function clm_submit_post_via_hidden_form(url, params) {
    var f = clmQuery("<form target='_blank' method='POST' style='display:none;'></form>").attr({
        action: url
    }).appendTo(document.body);

    for (var i = 0; i < params.length; i++) {
    	clmQuery('<input type="hidden" />').attr({
        	name: "ids["+i.toString()+"]",
                value: params[i][params[i].length-1]
        }).appendTo(f);
    }

    f.submit();

    f.remove();
}
