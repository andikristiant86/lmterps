<?php
	include_once($DOCUMENT_ROOT."/s/config.php");
	$template->basicheader(2);
	$lke_id=$db->getOne("select (select lokasi_kerja from spg_lokasi_kerja where lke_id=spg_data_current.lke_id) from spg_data_current where nip='$login_nip'");
?>
<table id="dg" title="CAR RENTAL, AREA:  <?=$lke_id;?>" class="easyui-datagrid" style="height:470px;width:100%" sortName="ClientID" sortOrder="desc"
data-options="
					rownumbers:true,
					singleSelect:true,
					collapsible: true,
					url:'car_rentalGet.php?act=view',
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
<th field="periode" width="100" sortable="true">Periode</th>
<th field="pm" width="125" sortable="true">PM Name</th>
<th field="proj_code" width="125" sortable="true">Project Code</th>
<th field="proj_name" width="250" sortable="true">Project Name</th>
<th field="total_car" width="100" sortable="true" align="right">Total Car</th>
<th field="total_cost" width="100" sortable="true" align="right">Total Cost</th>
<th field="status" width="100" sortable="true" align="center">Status</th>
</tr>
</thead>
</table>
<div id="toolbar">
<table width="100%" cellpadding=0 cellspacing=0><tr><td>
<input class="easyui-searchbox" data-options="prompt:'Search...',menu:'#mm',searcher:doSearch" style="width:300px">
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="add()">Insert</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="update()">Update</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroy()">Delete</a>
</td>
<td align="right">

</td>
</tr></table>
</div>
<div id="mm">
		<div data-options="name:'all',iconCls:'icon-ok'">All Category</div>
</div>
<div id="dlg" class="easyui-dialog" style="width:50%;height:250px;padding:0px 0px"
closed="true" buttons="#dlg-buttons">

<form id="fm" method="post" enctype="multipart/form-data">
<div class="fitem">
<label>Periode</label>
<input name="periode" id="periode" class="easyui-numberbox" required="true" style="width:25%;">
</div>

<div class="fitem">
<label>Project</label>
<select class="easyui-combogrid" name="proj_id" style="width:60%;" required="true"
					data-options="
							panelWidth:400,
							url: 'car_rentalGet.php?act=combo_project',
							idField:'id',
							textField:'proj_name',
							mode:'remote',
							fitColumns:true,
							rownumbers:false,nowrap: false,
					columns:[[
							{field:'proj_code',title:'Project Code',width:'30%'},
							{field:'proj_name',title:'Project Name',width:'60%'}
					]]
					">
				</select>
</div>

<div class="fitem">
<label>Total car</label>
<input name="total_car" id="total_car" class="easyui-numberbox" required="true" style="width:25%;">
</div>

<div class="fitem">
<label>Total cost</label>
<input name="total_cost" id="total_cost" class="easyui-numberbox" data-options="precision:0,groupSeparator:','" required="true" style="width:25%;">
</div>
<div class="fitem">
<label>Status</label>
<select class="easyui-combobox" name="status" style="width:25%;" data-options="panelHeight:'auto'" required="true">
        <option value="PLAN">PLAN</option>
        <option value="ACTUAL">ACTUAL</option>
</select>
</div>
</form>
</div>
<div id="dlg-buttons">
<a href="javascript:void(0)" class="easyui-linkbutton" id="btnSave" iconCls="icon-ok" onclick="Save()" style="width:90px">Save</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Cancel</a>
</div>
<script type="text/javascript">
var url;
function doSearch(value,name){
			$('#dg').datagrid('load',{
				value: value,
				name: name
			});
		}
function add(){
	$('#btnSave').linkbutton('enable');
	$('#dlg').dialog('open').dialog('setTitle','INSERT');
		$('#fm').form('clear');
		$('#fm').form('load',{
			periode:'<?=date("Ym");?>'
		});
	
	
	url = 'car_rentalGet.php?act=do_add';
}
function update(){
	$('#btnSave').linkbutton('enable');
	var row = $('#dg').datagrid('getSelected');
		if (row){
		$('#dlg').dialog('open').dialog('setTitle','UPDATE');
		$('#proj_id').combogrid('setValue', row.proj_id);
		$('#fm').form('load',row);
		url = 'car_rentalGet.php?act=do_update&id='+row.id;
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
	$.messager.confirm('Confirm','Delete this data?',function(r){
	if (r){
		$.post('car_rentalGet.php?act=do_destroy',{id:row.id},function(result){
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
</script>