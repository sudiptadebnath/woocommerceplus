<?php defined( 'ABSPATH' ) || exit; ?>
<div class="wrap">
<h1><?php echo get_admin_page_title(); ?></h1>
<table id="product_table" class="table table-striped table-bordered table-sm" style="width:100%">
	<thead>
		<tr class="table-info">
			<th>ID</th>
			<th>Img</th>
			<th>Description</th>
			<th>Category</th>
			<th>Action</th>
		</tr>
	</thead>
</table>
</div>	

	
<div class="wrap">
	<span class="h3">Addon Detail </span>
	<button type="button" class="btn btn-primary" onclick="saveAddons();">Save</button>
	<hr>
	<div class="row">
		<div class="col"><div class="row">
		  <div class="col">
			<img id="prodImg" class="img-fluid" style="max-height:300px;max-width:300px;" />
		  </div>
		  <div class="col">  
			<h3 id="prodId"></h3>
			<p id="prodDesc" class="display-5 text-info"></p>
		  </div>
		</div></div>
		<div class="col"><div id="prodAddon" style="height:300px;overflow-y:scroll;"></div>
		</div>	
	</div>
	
</div>	
	
	
<script>
var table;
$(document).ready(function () {
    table = $('#product_table').DataTable({
        processing: true, serverSide: true, order:[], 
        ajax: { url: ajaxurl + '?action=wopl_getproducts' },
		"columnDefs": [	{ 
			targets: [1,3,4],
			orderable: false, 
			searchable: false, 
		}, {
			"targets": [4],
			"render": function ( data, type, row ) {
				return '<button type="button" '
				+'class="btn btn-sm btn-primary" onclick="fetchDetail(this);" style="margin:1px; padding:1px;">'
				+'<i class="fa fa-sm fa-home"></i></button>'
				+'&nbsp;<button type="button" '
				+'class="btn btn-sm btn-primary" onclick="addDetail(this);" style="margin:1px; padding:1px;">'
				+'<i class="fa fa-sm fa-home"></i></button>';
			},
			"className": "text-center"
		} ]		
    });
});
function fetchDetail(me) {
    var data_row = table.row($(me).closest('tr')).data();
	$("#prodId").html(data_row[0]);
	$("#prodDesc").html(data_row[2]);
	$("#prodImg").attr("src",$(data_row[1]).attr("src"));

	jQuery.ajax({ type: "post", dataType: "json", url: ajaxurl,
        data: { "action" : "wopl_getproductaddon","pid" : data_row[0]},
        success: function (data) { 
			if(!data["err"]) {
				alert(data["msg"]);
				var html = "";
				for(var p in data["data"]) {
					var dtl = data["data"][p];
					html += '<div class="container border">'
						+'		'+dtl[1]
						+'		<h3 class="addonProdId">'+dtl[0]+'</h3>'
						+'		<p class="text-info">'+dtl[2]+'</p>'
						+'<button type="button" '
						+'class="btn btn-sm btn-primary" onclick="delAddon(this);">'
						+'<i class="fa fa-floppy"></i></button>'
						+'</div>';
				}
				$("#prodAddon").html(html);
			} else alert(data["msg"]);
		},
        error: function (st, er, et) { 
			alert("ERROR !! "+JSON.stringify([st, er, et]));
		}		
    });	
}

function addDetail(me) {
	var addons = $("#prodAddon").find(".addonProdId").map(function (idx, ele) {
		return $(ele).html();
	}).get()
    var data_row = table.row($(me).closest('tr')).data();
	if(addons.includes(data_row[0])) {
		alert("Already included");
		return;
	}
	var html = '<div class="container border">'
		+'		'+data_row[1]
		+'		<h3 class="addonProdId">'+data_row[0]+'</h3>'
		+'		<p class="text-info">'+data_row[2]+'</p>'
		+'<button type="button" '
		+'class="btn btn-sm btn-primary" onclick="delAddon(this);">'
		+'<i class="fa fa-floppy"></i></button>'
		+'</div>';
	$("#prodAddon").append(html);
}


function saveAddons() {
	var addons = $("#prodAddon").find(".addonProdId").map(function (idx, ele) {
		return $(ele).html();
	}).get()
	jQuery.ajax({ type: "post", dataType: "json", url: ajaxurl,
        data: { "action" : "wopl_saveproductaddon","pid" : $("#prodId").html(),"addons" : addons.join(",") },
        success: function (data) { 
			if(!data["err"]) {
				alert(data["msg"]);
			} else alert(data["msg"]);
		},
        error: function (st, er, et) { 
			alert("ERROR !! "+JSON.stringify([st, er, et]));
		}		
    });	
}


function delAddon(me) {
	var addons = $(me).closest(".container").remove();
}

</script>