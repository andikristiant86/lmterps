<?php
	include_once($DOCUMENT_ROOT."/s/config.php");
	$template->basicheader(2);
	//include_once($DOCUMENT_ROOT."/project/report/check_saldo_transfer.php");
?>
<table id="dg" title="PAYMENT PULSA" class="easyui-datagrid" style="height:475px;width:100%"
data-options="
					rownumbers:true,
					singleSelect:false,
					collapsible: true,
					url:'request_pulsaGet.php?act=view',
					method:'post',
					pagination:true,
					toolbar:'#toolbar',
					striped: true,
					nowrap: false,
					pageSize:50,
					pageList: [10,20,30,40,50,100,200],
					showFooter:true,
					fitColumns:false
					"
>
<thead data-options="frozen:true">
            <tr>
	<th data-options="field:'ck',checkbox:true"></th>

<th field="ocs_id" width="100">CRP ID</th>
<th field="req_date" width="120">REQUEST DATE</th>
<th field="req_name" width="175">TOPUP TO</th>
            </tr>
        </thead>
<thead>
<thead>
<tr>
<th field="coord_name" width="150">COORDINATOR</th>
<th field="proj_code" width="150">PROJECT CODE</th>
<th field="sts_app_pm" width="100" align="center">STS APPROVE</th>
<th field="date_app_pm" width="170">APPROVE DATE</th>

<th field="phone_number" width="100">PHONE NUMBER</th>
<th field="voucher_paket" width="100">PAKET</th>
<th field="voucher_type" width="100">TYPE</th>
<th field="voucher_nominal" width="100">NOMINAL</th>
<th field="amount" width="100" align="right">PRICE</th>
<th field="description" width="200">DESKRIPSI</th>
<th field="status_topup" width="100" align="center" formatter="status">STS TRANSFER</th>
<th field="received_date" width="100">DATE</th>
</tr>
</thead>
</table>

<div id="toolbar">
<table width="100%" cellpadding=0 cellspacing=0><tr><td>
From date <?=$f->from_date("start_date","","","",false,1);?> Up to date <?=$f->up_to_date("end_date","","","",false,1);?>
<input class="easyui-searchbox" data-options="prompt:'Search...',menu:'#mm',searcher:doSearch" style="width:300px">
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" plain="true" onclick="<?php $f->CheckHakAccess($fua_add,"approval"); ?>">Process</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" plain="true" onclick="<?php $f->CheckHakAccess($fua_add,"notapproval"); ?>">Reject</a>
</td>
<td align="right">
	<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-print" plain="true" onclick="cetak()">Print</a>
</td>
</tr></table>
</div>
<div id="mm">
		<div data-options="name:'all',iconCls:'icon-ok'">All Category</div>
</div>


<script type="text/javascript">
var url;
function cetak(){
	var start_date 	= $('#str_date').val();
	var end_date 	= $('#upto_date').val();
	window.location.assign("/carpool/request_pulsaExcel.php?f_start_date="+start_date+"&f_end_date="+end_date);
}
function doSearch(value,name){
			var start_date 	= $('#str_date').val();
			var end_date 	= $('#upto_date').val();
			$('#dg').datagrid('load',{
				value		 	: value,
				name		 	: name,
				f_start_date 	: start_date,
				f_end_date 		: end_date
			});
		}
function approval(){
	var ids = [];
	var rows = $('#dg').datagrid('getSelections');
	for(var i=0; i<rows.length; i++){
		ids.push(rows[i].id);
	}
	if(ids.join(',')!=""){
		$.messager.confirm('Confirm','Are you sure process?',function(r){
			if (r){
				$.post('request_pulsaGet.php?act=do_approval',{id:ids.join(',')},function(result){
					if (result.success){
						$('#dg').datagrid('reload');
					} else {
						$.messager.show({
						title: 'error',
						msg: result.errorMsg
						});
					}
				},'json');
			}
		});
	}

}function notapproval(){
	var ids = [];
	var rows = $('#dg').datagrid('getSelections');
	for(var i=0; i<rows.length; i++){
		ids.push(rows[i].id);
	}
	if(ids.join(',')!=""){
		$.messager.prompt('Confirm','Are you sure <b>not approve?</b><br>Reason:',function(r){
			if (r){
				$.post('request_pulsaGet.php?act=do_notapproval&reason_reject='+r,{id:ids.join(',')},function(result){
					if (result.success){
						$('#dg').datagrid('reload');
					} else {
						$.messager.show({
						title: 'error',
						msg: result.errorMsg
						});
					}
				},'json');
			}
		});
	}
}
function status(val,row){
	if (val == 'RECEIVED'){
		return '<span style="color:green;">'+val+'</span>';
	}else if (val == 'NOTRECIEVED'){
		return '<span style="color:blue;">'+val+'</span>';
	}else if(val == 'REJECT'){
		return '<span style="color:red;">'+val+'</span>';
	}else{
		return '';
	}
}
</script>