<?php
	include_once($DOCUMENT_ROOT."/s/config.php");
	$template->basicheader(2);
?>
<table id="dg" title="MONITORING DRIVER" class="easyui-datagrid" style="height:475px;width:100%"
data-options="
					rownumbers:true,
					singleSelect:true,
					collapsible: true,
					url:'monitoring_driverGet.php?act=view',
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
<th field="nip" width="10">NIP</th>
<th field="nm_peg" width="20">NAME</th>
<th field="status_kehadiran" width="20" formatter="status_kehadiran">PRESENCE STATUS</th>
<th field="absent_in" width="20">ABSENT IN</th>
<th field="absent_out" width="20">ABSENT OUT</th>
<th field="status" width="10" formatter="status" align="center">STATUS</th>
</tr>
</thead>
</table>
		<div id="toolbar">
<table width="100%" cellpadding=0 cellspacing=0><tr><td>
<?=$f->from_date("start_date","","","",false,1);?>
<input class="easyui-searchbox" data-options="prompt:'Search...',menu:'#mm',searcher:doSearch" style="width:300px">
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
function cetak(){
	var start_date 	= $('#str_date').val();
	window.location.assign("/carpool/monitoring_driverExcel.php?f_start_date="+start_date);
}
function doSearch(value,name){
	$('#dg').datagrid('load',{
		value: value,
		name: name
	});
}
function doSearch(value,name){
	var start_date 	= $('#str_date').val();
			$('#dg').datagrid('load',{
				value			: value,
				name			: name,
				f_start_date 	: start_date
			});
		}
function status (val,row){
	if (val == 'BLM JALAN'){
		return '<span style="color:red;">'+val+'</span>';
	}else {
		return '<span style="color:green;">'+val+'</span>';
	}
}function status_kehadiran (val,row){
	if (val == 'ALFA'){
		return '<span style="color:red;">'+val+'</span>';
	}else if(val == 'HADIR') {
		return '<span style="color:green;">'+val+'</span>';
	}else{
		return '';
	}
}
</script>