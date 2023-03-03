<?php defined( 'ABSPATH' ) || exit; ?>
<style>
#zipcode_table tr { cursor: pointer; }
</style>
<div class="wrap">
<h1><?php echo get_admin_page_title(); ?></h1>
<table id="zipcode_table" class="table table-striped table-bordered table-hover table-sm" style="width:100%">
	<thead>
		<tr class="table-info">
			<th>Zipcode</th>
			<th>State</th>
			<th>District</th>
			<th>City</th>
			<th>COD</th>
			<th>Delivery</th>
			<th>Return</th>
		</tr>
	</thead>
</table>
</div>	
	

<div id="myDlg" class="modal fade" role="dialog">
  <div class="modal-dialog modal-dialog-centered">
	<div class="modal-content">
	  <div class="modal-header">		
		<h4 class="modal-title">Zipcode : <span id="zipLbl"></span></h4>
		<button type="button" class="close" data-dismiss="modal">&times;</button>
	  </div>
	  <div class="modal-body">
		<span class="mr-3"><input type="checkbox" id="chkCOD" name="chkCOD" value="COD">COD</span>
		<span class="mr-3"><input type="checkbox" id="chkDelivery" name="chkDelivery" value="Delivery">Delivery</span>
		<span class="mr-3"><input type="checkbox" id="chkReturn" name="chkReturn" value="Return">Return</span>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-primary" onclick="saveZipcode();">Save</button>
      </div>
	</div>
  </div>
</div>
	
<script>
var table;
$(document).ready(function () {
    table = $('#zipcode_table').DataTable({
        processing: true, serverSide: true,
        ajax: { url: ajaxurl + '?action=wopl_getzipcode' },
		"columnDefs": [ {
			"targets": [4,5,6],
			"render": function ( data, type, row ) {
				return data.toLowerCase() == "yes" ? 
					'<i class="fa fa-check-circle-o fa-lg text-success"></i>' : 
					'<i class="fa fa-times-circle-o fa-lg text-danger"></i>' ;
			},
			"className": "text-center"
		} ]		
    });
	$('#zipcode_table tbody').on('click', 'tr', function () {
		var data = table.row( this ).data();
		//alert('You clicked on '+JSON.stringify(data));
		$('#zipLbl').html(data[0]);
		$('#chkCOD').prop("checked", data[4].toLowerCase() == "yes" );
		$('#chkDelivery').prop("checked", data[5].toLowerCase() == "yes" );
		$('#chkReturn').prop("checked", data[6].toLowerCase() == "yes" );
		$('#myDlg').modal('show');
	}); 
});

function saveZipcode() {
	$('#myDlg').modal('hide');
	jQuery.ajax({ type: "post", dataType: "json", url: ajaxurl,
        data: { 
			"action" : "wopl_savezipcode",
			"zipcode" : $('#zipLbl').html(),
			"COD" : $('#chkCOD')[0].checked ? "yes" : "no",
			"Delivery" : $('#chkDelivery')[0].checked ? "yes" : "no",
			"Return" : $('#chkReturn')[0].checked ? "yes" : "no",
		},
        success: function (data) { 
			if(!data["err"]) {
				alert(data["msg"]);
				table.ajax.reload();
			}
			else alert(data["msg"]);
		},
        error: function (st, er, et) { 
			alert("ERROR !! "+JSON.stringify([st, er, et]));
		}		
    });
}
</script>