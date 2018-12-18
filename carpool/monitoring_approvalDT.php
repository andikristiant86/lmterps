<?php
	include_once($DOCUMENT_ROOT."/s/config.php");
	$template->basicheader(2);
?>
<table id="dg" title=":: APPROVAL CARPOOL" class="easyui-datagrid" style="height:475px;width:100%"
data-options="
					rownumbers:true,
					singleSelect:false,
					collapsible: true,
					url:'monitoring_approvalDTGet.php?act=view',
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
				<th field="date" width="100">OPEN DATE</th>
				<th field="time" width="75">TIME</th>
				
            </tr>
        </thead>
<thead>
<thead>
<tr>
<th field="area" width="165">AREA</th>
<th field="pm_name" width="165">PROJECT MANAGER</th>
<th field="dtc_name" width="165">DT COORDINATOR</th>
<th field="dt_name" width="150">DT NAME</th>
<th field="ocs_desc" width="175">DESKRIPSI</th>
<th field="proj_name" width="200" >PROJECT</th>
<th field="pm_app" width="175" align="right">CHECKED PM</th>
<th field="bc_app" width="175" align="right">CHECKED BC</th>
<th field="paid_date" width="175" align="right">PAID FINANCE</th>
</tr>
</thead>
</table>

<div id="toolbar">
<table width="100%" cellpadding=0 cellspacing=0><tr><td>
<input class="easyui-searchbox" data-options="prompt:'Search...',menu:'#mm',searcher:doSearch" style="width:300px">

</td>
<td>
	<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-xls" plain="true" onclick="alert()">Export</a>
</td>
</tr></table>
</div>
<div id="mm">
		<div data-options="name:'all',iconCls:'icon-ok'">All Category</div>
</div>


<script type="text/javascript">
var url;
function doSearch(value,name){
			$('#dg').datagrid('load',{
				value		 	: value,
				name		 	: name
			});
		}
function approval(){
	var ids = [];
	var ocs = [];
	var crp = [];
	var rows = $('#dg').datagrid('getSelections');
	for(var i=0; i<rows.length; i++){
		if(rows[i].status_app=='OPEN'){
			ocs.push(rows[i].id+'|'+rows[i].ocs_id);
			crp.push(rows[i].id);
		}
	}
	if(crp.join(',')==""){
		//$.messager.alert('Error','Sorry, status closed/waiting','error');
	}else{
		$.messager.confirm('Confirm','Are you sure approve?',function(r){
			if (r){
				$.post('monitoring_approvelGet.php?act=do_approval',{ocs_id:ocs.join(',')},function(result){
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
	var ocs = [];
	var crp = [];
	var rows = $('#dg').datagrid('getSelections');
	for(var i=0; i<rows.length; i++){
		if(rows[i].status_app=='OPEN'){
			ocs.push(rows[i].id+'|'+rows[i].ocs_id);
			crp.push(rows[i].id);
		}
	}
	if(crp.join(',')==""){
		//$.messager.alert('Error','Sorry, status closed/waiting','error');
	}else{
		$.messager.prompt('Confirm','Are you sure <b>not approve?</b>',function(r){
			if (r){
				$.post('monitoring_approvelGet.php?act=do_notapproval&alasan='+r,{ocs_id:ocs.join(',')},function(result){
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