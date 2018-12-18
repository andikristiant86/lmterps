<?php
	include_once($DOCUMENT_ROOT."/s/config.php");
	$template->basicheader(2);
?>
<table id="dg" title="MONITORING CAR" class="easyui-datagrid" style="height:475px;width:100%"
data-options="
					rownumbers:true,
					singleSelect:true,
					collapsible: true,
					url:'monitoring_carGet.php?act=view',
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
<!--
<th field="kdr_id" width="10" align="right">CAR NUMBER</th>-->
<th field="car_number" width="10">POLICE NUMBER</th>
<th field="name_driver" width="40">DRIVER</th>
<th field="proj_nm" width="40">PROJECT</th>
<th field="status" width="10" align="center" formatter="status">STATUS</th>
</tr>
</thead>
</table>

<div id="toolbar">
<table width="100%" cellpadding=0 cellspacing=0><tr><td>
<input class="easyui-searchbox" data-options="prompt:'Search...',menu:'#mm',searcher:doSearch" style="width:300px">
</td>
<td align="right">
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-print" plain="true" onclick="alert('Maaf, Belum tersedia!')">Print</a>
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
				value: value,
				name: name
			});
		}
function status (val,row){
	if (val == 'OUT'){
		return '<span style="color:red;">'+val+'</span>';
	}else {
		return '<span style="color:green;">'+val+'</span>';
	}
}
</script>