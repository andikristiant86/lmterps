<?php
	include_once($DOCUMENT_ROOT."/s/config.php");
	$template->basicheader(2);
?>
<script type="text/javascript">
var url;
function doSearch(value,name){
			$('#dg').datagrid('load',{
				value: value,
				name: name
			});
		}
function tambah(){
	$("#hideShow1").hide();
	$('#btnSave').linkbutton('enable');
	$('#dlg').dialog('open').dialog('setTitle','TOPUP');
	url = 'topup_pulsaGet.php?act=do_add';
}
function edit(){
	$('#btnSave').linkbutton('enable');
	var row = $('#dg').datagrid('getSelected');
		if (row){
			if(row.status=='CLOSED'){
				$.messager.alert('Error',"Sorry, you don't have permission to update this data!",'error');
			}else{

				if(row.status=='PULANG') $("#hideShow1").show();
				
				else $("#hideShow1").hide();
				$('#dlg').dialog('open').dialog('setTitle','FORM EDIT');
				$('#fm').form('load',row);
				$('#site_name').combogrid('setValue', row.site_name);
				url = 'topup_pulsaGet.php?act=do_update&ocs_id='+row.ocs_id+'&status='+row.status;
			}
	}
}
function Save(){
	$('#fm').form('submit',{
		url: url,
		onSubmit: function(){
			return $(this).form('validate');
			},
			success: function(result){
			$('#btnSave').linkbutton('disable');
			var result = eval('('+result+')');
			if (result.errorMsg){
			$.messager.show({
			title: 'Error',
			msg: result.errorMsg
			});
			} else {
			$('#dlg').dialog('close'); // close the dialog
			$('#dg').datagrid('reload'); // reload the user data
			}
		}
	});
}
function destroy(){
	var row = $('#dg').datagrid('getSelected');
	if (row){
		$.messager.confirm('Confirm','You want to delete this data?',function(r){
			if (r){
				$.post('topup_pulsaGet.php?act=do_destroy',{id:row.id},function(result){
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

function status(val,row){
	if (val == 'BELUM JALAN'){
				return '<span style="color:red;">'+val+'</span>';
	}else if(val=='PULANG'){
				return '<span style="color:blue;">'+val+'</span>';
	}else if(val=='CLOSED'){
				return '<span style="color:black;">'+val+'</span>';
	}else{
				return '<span style="color:green;">'+val+'</span>';
	}
}
function topup_pulsa(){
	var row = $('#dg').datagrid('getSelected');
	if (row){
			if(row.status=='CLOSED'){
				$.messager.alert('Error',"Sorry, you don't have permission to update this data!",'error');
			}else{
				window.location.assign("/carpool/topup_pulsa.php?ocs_id="+row.ocs_id);
			}
	}
}
function action(val,row){
	//var e = '<a href="#" onclick="editrow('+val+')">Edit</a> ';
    var d = '<a href="#" onclick="deleterow('+val+')">Delete</a>';
    return d;
}
function deleterow(target){
    $.messager.confirm('Confirm','You want to delete this data?',function(r){
		if (r){
			$.post('topup_pulsaGet.php?act=do_destroy',{id:target},function(result){
				if (result.success){
					$('#dg').datagrid('reload'); // reload the user data
				} else {
					$.messager.alert('Error',result.errorMsg,'error');
				}
			},'json');
		}
	});
}
</script>
<table id="dg" title="TOPUP PULSA" class="easyui-datagrid" style="height:475px;width:100%"
data-options="
					rownumbers:true,
					singleSelect:true,
					collapsible: true,
					url:'topup_pulsaGet.php?act=view&ocs_id=<?=$ocs_id?>',
					method:'post',
					pagination:true,
					toolbar:'#toolbar',
					striped: true,
					nowrap: false,
					pageSize:20,
					pageList: [10,20,30,40,50],
					showFooter:true,
					fitColumns:true
					"
>
<thead>
<tr>
<th field="ocs_id" width="100">ID</th>
<th field="req_date" width="100">DATE</th>
<th field="req_name" width="150">REQUEST NAME</th>
<th field="phone_number" width="150">PHONE NUMBER</th>
<th field="sts_app_pm" width="150" align="center">APPROVAL PM</th>
<!--<th field="sts_app_finance" width="150" align="center">APPROVAL FINANCE</th>-->
<th field="amount" width="150" align="right">AMOUNT</th>
<th field="status_topup" width="100" align="center">STATUS</th>
</tr>
</thead>
</table>

<div id="toolbar">
<table width="100%" cellpadding=0 cellspacing=0><tr><td>
<input class="easyui-searchbox" data-options="prompt:'Search...',menu:'#mm',searcher:doSearch" style="width:300px">
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="tambah()">Insert</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroy()">Delete</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" plain="true" onclick="window.close()">Close</a>
</td>
</tr></table>
</div>
<div id="mm">
		<div data-options="name:'all',iconCls:'icon-ok'">All Category</div>
</div>

<div id="dlg" class="easyui-dialog" style="width:50%;height:250px;padding:0px 0px"
closed="true" buttons="#dlg-buttons">

<form id="fm" method="post" enctype="multipart/form-data">
<input type="hidden" name="ocs_id" value="<?=$ocs_id;?>"> 
<input type="hidden" name="proj_id" value="<?=$proj_id;?>"> 
<div class="fitem">
	<label>Request Date</label>
	<?=$f->InputDate("request_date");?> 
	
</div>

<div class="fitem">
<label>Request Name</label>
<select class="easyui-combogrid" name="request_name" id="request_name" style="width:50%;" required="true"
					data-options="
							panelWidth:300,
							url: 'topup_pulsaGet.php?act=combo_dt',
							idField:'nip',
							textField:'nm_peg',
							mode:'remote',
							fitColumns:true,
							rownumbers:false,
					columns:[[
							{field:'nip',title:'NIP',width:'100'},
							{field:'nm_peg',title:'Name',width:'200'}
					]],
					onSelect: function (index,row){
						$('#fm').form('load',{req_nip:row.nip});
					}
					">
				</select>
<input name="req_nip" type="hidden">
</div>

<div class="fitem">
<label>Amount</label>
	<input name="amount" class="easyui-numberbox" data-options="precision:0,groupSeparator:','" style="width:30%;" required="true"></input>
</div>
<div class="fitem">
<label>Phone Number</label>
	<input name="phone_number" class="easyui-numberbox" data-options="precision:0,groupSeparator:''" style="width:30%;" required="true"></input>
</div>
<div class="fitem">
<label>Description</label>
<input class="easyui-textbox" name="description" data-options="multiline:true" style="width:50%;height:35px" required="true">
</div>

</form>
</div>

<div id="dlg-buttons">
<a href="javascript:void(0)" class="easyui-linkbutton" id="btnSave" iconCls="icon-ok" onclick="Save()" style="width:90px">Save</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Cancel</a>
</div>
