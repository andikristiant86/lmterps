<?php
	include_once($DOCUMENT_ROOT."/s/config.php");
	$template->basicheader(2);
	$lke_id=$db->getOne("select (select lokasi_kerja from spg_lokasi_kerja where lke_id=spg_data_current.lke_id) from spg_data_current where nip='$login_nip'");
?>
<script type="text/javascript">
var url;
function doSearch(value,name){
			var start_date 	= $('#str_date').val();
			var end_date 	= $('#upto_date').val();
			$('#dg').datagrid('load',{
				value: value,
				name: name,
				f_start_date 	: start_date,
				f_end_date 		: end_date
			});
		}
function tambah(){
	$("#hideShow1").hide();
	$('#btnSave').linkbutton('enable');
	$('#dlg').dialog('open').dialog('setTitle','FORM ADD');
		$('#fm').form('clear');
		$.post('makeproposalManagGet.php',{act:'generate_kode'},function(result){
			var txt=result.kode;
			list=txt.split("|");
			$('#fm').form('load',{
				ocs_id:list[0]
			});
		},'json');
	
	url = 'makeproposalManagGet.php?act=do_add';
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
				url = 'makeproposalManagGet.php?act=do_update&ocs_id='+row.ocs_id+'&status='+row.status;
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
			$.post('makeproposalManagGet.php?act=do_destroy',{id:row.ocs_id},function(result){
				if (result.success){
					$('#dg').datagrid('reload'); // reload the user data
				} else {
					$.messager.show({ // show error message
					title: 'Error',
					msg: result.errorMsg
					});
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
				window.location.assign("/carpool/topup_pulsa.php?ocs_id="+row.ocs_id+"&proj_id="+row.proj_id);
			}
	}
}
</script>
<table id="dg" title="PROPOSAL MANAGEMENT, AREA: <b><font color=red><?=$lke_id?></font></b>" class="easyui-datagrid" style="height:475px;width:100%"
data-options="
					rownumbers:true,
					singleSelect:true,
					collapsible: true,
					url:'makeproposalManagGet.php?act=view',
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
<thead data-options="frozen:true">
            <tr>
			<th data-options="field:'ck',checkbox:true"></th>
                <th field="ocs_id" width="100">ID</th>
				<th field="ocs_desc" width="200">DESCRIPTION</th>
            </tr>
        </thead>
<thead>
<thead>
<tr>

<th field="date" width="100">DATE</th>
<th field="time" width="75">TIME</th>
<th field="name_dt" width="150">REQ. NAME</th>
<th field="km_acuan" width="75" align="right">KM ACUAN</th>
<th field="uang_pulsa" width="80" align="right">PULSA</th>
<th field="phone_number" width="150" align="right">PHONE NUMBER</th>
<th field="status" width="100" formatter="status" align="center">STATUS</th>
</tr>
</thead>
</table>

<div id="toolbar">
<table width="100%" cellpadding=0 cellspacing=0><tr><td>
	From date <?=$f->from_date("start_date","","","",false,1);?> Up to date <?=$f->up_to_date("end_date","","","",false,1);?>
<input class="easyui-searchbox" data-options="prompt:'Search...',menu:'#mm',searcher:doSearch" style="width:250px">
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="<?php $f->CheckHakAccess($fua_add,"tambah"); ?>">Insert</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="<?php $f->CheckHakAccess($fua_edit,"edit"); ?>">Update</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="<?php $f->CheckHakAccess($fua_delete,"destroy"); ?>">Delete</a>
</td>
</tr></table>
</div>
<div id="mm">
		<div data-options="name:'all',iconCls:'icon-ok'">All Category</div>
		<div data-options="name:'time'">Time</div>
</div>

<div id="dlg" class="easyui-dialog" style="width:50%;height:300px;padding:0px 0px"
closed="true" buttons="#dlg-buttons">

<form id="fm" method="post" enctype="multipart/form-data">
<div class="fitem">
	<label>ID</label>
	<input name="ocs_id" id="ocs_id" class="easyui-textbox" style="width:29%;" readOnly required="true">
</div>
<div class="fitem">
	<label>Date/Time</label>
	<?=$f->InputDate("date");?> 
<select class="easyui-combobox" name="time" style="width:20%;" required="true"
data-options="panelHeight:'auto'">
	<option value="Pagi">Pagi</option>
	<option value="Siang">Siang</option>
	<option value="Malam">Malam</option>
</select>
</div>

<div class="fitem">
<label>Management</label>
<select class="easyui-combogrid" name="name_dt" id="name_dt" style="width:50%;"
					data-options="
							panelWidth:400,
							url: 'makeproposalManagGet.php?act=combo_dt',
							idField:'nip',
							textField:'nm_peg',
							mode:'remote',
							fitColumns:true,
							rownumbers:false,
					columns:[[
							{field:'nip',title:'NIP',width:'100'},
							{field:'nm_peg',title:'Employee Name',width:'300'}
					]],
					onSelect: function (index,row){
						$('#fm').form('load',{nip_dt:row.nip});
					}
					">
				</select>
<input name="nip_dt" type="hidden">
</div>

<div class="fitem">
<label>KM Acuan</label>
	<input name="km_acuan" class="easyui-numberbox" style="width:20%;" required="true"></input>
	PULSA: <input name="uang_pulsa" class="easyui-numberbox" data-options="precision:0,groupSeparator:','" style="width:20%;"></input>
</div>
<div class="fitem">
<label>Phone Number</label>
<input class="easyui-textbox" name="phone_number" style="width:50%;">
</div>

<div class="fitem">
<label>Description</label>
<input class="easyui-textbox" name="ocs_desc" data-options="multiline:true" style="width:50%;height:35px" required="true">
</div>

</form>
</div>

<div id="dlg-buttons">
<a href="javascript:void(0)" class="easyui-linkbutton" id="btnSave" iconCls="icon-ok" onclick="Save()" style="width:90px">Save</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Cancel</a>
</div>
