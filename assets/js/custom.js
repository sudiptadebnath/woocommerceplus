jQuery(document).ready(function(){    

function reorder(oid) {
    jQuery.ajax({ type: "post", dataType: "json", url: ajax_object.ajax_url,
        data: { action: 'reorder', "orderId": oid },
        success: function (data) { 
			if(!data["err"]) {
				//REDIRECT
				window.location.href = data["data"];
			} else {
				alert(data["msg"]); 
			}
		},
        error: function (st, er) { alert("error >> "+JSON.stringify([st, er])); }		
    });
}

});  
