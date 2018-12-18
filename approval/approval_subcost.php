<?php

ob_start();
session_start();	
include_once($DOCUMENT_ROOT."/s/config.php");
$template->basicheader(2);
$admin_area=$dbpay->getOne("select count(*) from m_kasir where pic_id='$login_nip' and verify='Y'");
?>
<table id="dg" title=":: APPROVAL SUBCOST" class="easyui-datagrid" style="height:475px;width:100%"
data-options="
					rownumbers:true,
					singleSelect:true,
					collapsible: true,
					url:'approval_subcostGet.php?act=view',
					method:'post',
					pagination:true,
					toolbar:'#toolbar',
					striped: true,
					nowrap: false,
					pageSize:50,
					pageList: [10,20,30,40,50,100,200],
					showFooter:true,
					fitColumns:true
					"
>
<thead data-options="frozen:true">
            <tr>
<th data-options="field:'ck',checkbox:true"></th>

<th field="nox" width="100">REQ. ID</th>

<th field="req_name" width="150">REQ. NAME</th>
            </tr>
        </thead>
<thead>
<thead>
<tr>
<th field="note" width="200" formatter="notedesc">DESCRIPTION</th>
<th field="project_name" width="150">PROJECT</th>
<th field="cara_bayar" width="100" align="center">PAYMENT TYPE</th>
<th field="amount" width="100" align="right">AMOUNT</th>
<th field="status_approval" width="130" align="right">STATUS</th>
<th field="bukti_transfer" width="70" align="center">OPTION</th>
</tr>
</thead>
</table>
<?php
$txt_app=$admin_area>=1?"Verify":"Approval";
?>
<div id="toolbar">
<table width="100%" cellpadding=0 cellspacing=0><tr><td>
<input class="easyui-searchbox" data-options="prompt:'Search...',menu:'#mm',searcher:doSearch" style="width:300px">
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" plain="true" onclick="approved()"><?=$txt_app;?></a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" plain="true" onclick="reject()">Reject</a>
</td>
<td align="right">
	<!--<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-print" plain="true" onclick="printx()">View</a>-->
</td>
</tr></table>
</div>
<div id="mm">
		<div data-options="name:'all',iconCls:'icon-ok'">All Category</div>
</div>

<script type="text/javascript">
var url;
function cetak(){
	window.location.assign("/finance/paid_costExport.php");
}
function doSearch(value,name){
			//var statusx = $('#status').combobox('getValue');
			$('#dg').datagrid('load',{
				value		 	: value,
				name		 	: name//,
				//status			: statusx
			});
}

function reject(){
	var ids = [];
	var rows = $('#dg').datagrid('getSelections');
	for(var i=0; i<rows.length; i++){
		ids.push(rows[i].id);
	}
	if(ids.join(',')!=""){
		$.messager.prompt('Confirm','Are you sure reject this data subcost?<br>Please input the reason!:',function(r){
			if (r){
				$.post('approval_subcostGet.php?act=do_reject&reason='+r,{id:ids.join(',')},function(result){
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

function approved(){
	var ids = [];
	var rows = $('#dg').datagrid('getSelections');
	for(var i=0; i<rows.length; i++){
		ids.push(rows[i].id);
	}
	if(ids.join(',')!=""){
		$.messager.confirm('Confirm','Are you sure approved this data subcost?',function(r){
			if (r){
				$.post('approval_subcostGet.php?act=do_approved',{id:ids.join(',')},function(result){
					if (result.success){
						$('#dg').datagrid('reload');
					} else {
						$.messager.alert('Error',result.errorMsg,'error');
					}
				},'json');
			}
		});
	}
}

function Save(){
	$('#btnSave').linkbutton('disable');
	$('#fm').form('submit',{
		url: url,
		onSubmit: function(){
			return $(this).form('validate');
			},
			success: function(result){
			var result = eval('('+result+')');
			if (result.errorMsg){
				$.messager.alert('Error',result.errorMsg,'error');
			} else {
				$('#dlg').dialog('close'); // close the dialog
				$('#dg').datagrid('reload'); // reload the user data
			}
		}
	});
}
function printx (no){
	// var row = $('#dg').datagrid('getSelected');
	// if (row){
		//if(row.status==6 || row.status==1001 || row.status==1002){
			window.open("/kepegawaian/absensi/uploads/?pengajuan_id="+no, "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=50, left=100, width=900, height=525");
		//}
	//}
	
}function openfile (filex){
	// var row = $('#dg').datagrid('getSelected');
	// if (row){
		//if(row.status==6 || row.status==1001 || row.status==1002){
			window.open("/finance/uploads/"+filex, "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=150, left=400, width=800, height=400");
		//}
	//}
	
}
function notedesc(val,row){
	return atob(val);
}
</script>