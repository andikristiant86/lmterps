<?php
	include_once($DOCUMENT_ROOT."/s/config.php");
	$template->basicheader(2);
?>
<table id="dg" title="Monitoring Approval" class="easyui-datagrid" style="height:475px;width:100%"
data-options="
					rownumbers:true,
					singleSelect:false,
					collapsible: true,
					url:'monitoring_approvelGet1.php?act=view',
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
<!--
<th field="km_acuan" width="100" align="right">KM ACUAN</th>

-->
<th field="proj_name" width="200" >PROJECT NAME</th>
<th field="pm_approve_date" width="130" align="right">APPROVAL DATE (PM)</th>
<th field="status_app" width="100" align="center" formatter="status">STATUS</th>
</tr>
</thead>
</table>

<div id="toolbar">
<table width="100%" cellpadding=0 cellspacing=0><tr><td>
From date <?=$f->from_date("start_date","","","",false,1);?> Up to date <?=$f->up_to_date("end_date","","","",false,1);?>
<input class="easyui-searchbox" data-options="prompt:'Search...',menu:'#mm',searcher:doSearch" style="width:300px">
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" plain="true" onclick="approval()">Approved</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" plain="true" onclick="notapproval()">Not Approved</a>
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
				$.post('monitoring_approvelGet1.php?act=do_approval',{ocs_id:ocs.join(',')},function(result){
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
				$.post('monitoring_approvelGet1.php?act=do_notapproval&alasan='+r,{ocs_id:ocs.join(',')},function(result){
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