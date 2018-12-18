<?php
	include_once($DOCUMENT_ROOT."/s/config.php");
	$template->basicheader(2);
?>
<table id="dg" title=":: APPROVAL PULSA" class="easyui-datagrid" style="height:475px;width:100%"
data-options="
					rownumbers:true,
					singleSelect:true,
					collapsible: true,
					url:'monitoring_topup_appGet.php?act=view',
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
<th field="ocs_id" width="100">REQ ID</th>
<!--<th field="req_date" width="100">DATE</th>-->
<th field="req_name" width="250">TOPUP TO</th>
            </tr>
        </thead>
<thead>
<tr>
<th field="phone_number" width="150">PHONE NUMBER</th>
<th field="proj_name" width="250">DESCRIPTION</th>
<!--<th field="description" width="250">DESCRIPTION</th>-->
<th field="amount" width="150" align="right">VOUCHER</th>
<th field="amount_month" width="150" align="right">TOTAL <?=date('Ym');?></th>
<th field="create_by" width="150">CREATE BY</th>
</tr>
</thead>
</table>

<div id="toolbar">
<table width="100%" cellpadding=0 cellspacing=0><tr><td>
From date <?=$f->from_date("start_date","","","",false,1);?> Up to date <?=$f->up_to_date("end_date","","","",false,1);?>
<input class="easyui-searchbox" data-options="prompt:'Search...',menu:'#mm',searcher:doSearch" style="width:300px">
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" plain="true" onclick="approval()">Approved</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" plain="true" 
onclick="notapproval()">Reject</a>
</td>
</tr></table>
</div>
<div id="mm">
		<div data-options="name:'all',iconCls:'icon-ok'">All Category</div>
</div>


<script type="text/javascript">
var url;
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
		$.messager.confirm('Confirm','Approved ?',function(r){
			if (r){
				$.post('monitoring_topup_appGet.php?act=do_approval',{id:ids.join(',')},function(result){
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
	

}function notapproval(){
	var ids = [];
	var rows = $('#dg').datagrid('getSelections');
	for(var i=0; i<rows.length; i++){
		//if(rows[i].sts_app_pm=='OPEN'){
			ids.push(rows[i].id);
		//}
	}
	if(ids.join(',')==""){
		$.messager.alert('Error','Sorry, checklist this data!','error');
	}else{
		$.messager.prompt('Confirm','Are you sure reject this data pulsa?<b>Please input reason!</b>',function(r){
			if (r){
				$.post('monitoring_topup_appGet.php?act=do_notapproval&alasan='+r,{id:ids.join(',')},function(result){
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
	if (val == 'OPEN'){
		return '<span style="color:red;">'+val+'</span>';
	}else{
		return '<span style="color:green;">'+val+'</span>';
	}
}
</script>