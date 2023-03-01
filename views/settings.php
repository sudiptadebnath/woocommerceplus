<?php 
defined( 'ABSPATH' ) || exit; 

function getItm($arr, $pos, $defl="") {
	return !isset($arr[$pos]) ? $defl : $arr[$pos];
}
?>


<div class="wrap">

<h1>Woocommerceplus Plugin Settings</h1>

<form id="formWopl" method="post" onsubmit="return saveSettings();">

	<input type="hidden" name="action" value="save_settings" />
		
	<div id="errDiv" class="error notice hidden"><p id="errMsg"></p></div>

<table class="form-table" role="presentation">
<?php 
	global $wpdb;
	$table_name = $wpdb->prefix . 'wopl_settings';
    $sql = "SELECT * FROM $table_name";
    $results = $wpdb->get_results($sql) or die($wpdb->last_error);
    foreach($results as $ky => $result ) {
		$dtl = explode("||",$result->setting_typ);
		$ityp = getItm($dtl,0);
		switch($ityp) {
			case "TEXT":
?>
	<tr>
	<th scope="row"><label for="<?php echo $result->setting_key; ?>"><?php echo $result->setting_lbl; ?></label></th>
	<td><input type="<?php echo $ityp; ?>" class="form-control" placeholder="Enter <?php echo $result->setting_lbl; ?>" 
		name="<?php echo $result->setting_key; ?>" value="<?php echo $result->setting_value; ?>" 
		onblur="validateCtrl('<?php echo $result->setting_key; ?>', this.value,'<?php echo $result->setting_vld; ?>');"></td>
	</tr>

<?php
			break;
			
			case "COMBO":
?>
	<tr>
		<th scope="row"><label for="woplItem<?php echo $ky; ?>"><?php echo $result->setting_lbl; ?></label></th>
		<td><select class="form-control" name="<?php echo $result->setting_key; ?>">
<?php		foreach(explode(",",getItm($dtl,1)) as $i1 => $idtl) { 
			$sel = $result->setting_value == $i1 || $result->setting_value == $idtl ? "selected" : "";
?>
			<option value="<?php echo $i1; ?>"<?php echo $sel; ?>><?php echo $idtl; ?></option>
<?php 		} ?>
			
		</select></td>
	</tr>
<?php
			break;
			
		}
    }
?>

	<tr><th colspan="2"><button id="saveBtn" type="submit" 
	class="button button-primary">Save Changes</button></th></tr>

</form>
<script>
var vldErrs = {};
function validateCtrl(meid,vl,rul) {
	delete vldErrs[meid];
	if(rul) {
		rulPrt = rul.split(",");
		switch(rulPrt[0]) {
			case ">=<=":
				frmVl = parseFloat(rulPrt[1]);
				toVl = parseFloat(rulPrt[2]);
				meVl = parseFloat(vl);
				if (!(meVl >= frmVl && meVl <= toVl))
					vldErrs[meid] = rulPrt[3];
				break;
		}
	}
	errs = [];
	for(var ve in vldErrs) errs.push(vldErrs[ve]);
	err = errs.join("<BR>");
	if(err) showMsg(true,err);
}

function showMsg(err,msg) {
	jQuery("#errDiv").removeClass("error updated");
	jQuery("#errDiv").addClass(err ? "error" : "updated");
	jQuery("#errMsg").html(msg);
	jQuery("#errDiv").show();
	jQuery("#saveBtn").prop('disabled', false);
}
function saveSettings() {
	errs = [];
	for(var ve in vldErrs) errs.push(vldErrs[ve]);
	if(errs.length > 0) {
		alert("Can't Proceed !! contains errors."); 
		return false;
	}
	jQuery("#errDiv").hide();
	jQuery("#saveBtn").prop('disabled', true);
	jQuery.ajax({ type: "post", dataType: "json", url: ajaxurl,
        data: jQuery("#formWopl").serialize(),
        success: function (data) { 
			if(!data["err"]) showMsg(false,data["msg"]);
			else {
				showMsg(true,data["msg"]+JSON.stringify(data["data"]));
			}
		},
        error: function (st, er, et) { 
			showMsg(true,"ERROR !! "+JSON.stringify([st, er, et]));
		}		
    });
	return false;
}
</script>