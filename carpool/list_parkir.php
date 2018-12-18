<?php
	include_once($DOCUMENT_ROOT."/s/config.php");
	//include("mysql/mysql.class.php");

$db		= ADONewConnection('mysql');
$db->PConnect("10.10.10.202:3307", "dms", "dms","dotproject");
$db->SetFetchMode(ADODB_FETCH_ASSOC);

	$template->basicheader(2);
?>
<table id="dg" title="List Parkir" class="easyui-datagrid" style="height:475px;width:100%"
data-options="
					rownumbers:true,
					singleSelect:false,
					collapsible: true,
					url:'list_parkirGet.php?act=view',
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
<thead>
            <tr>
	
<th field="id" width="100">ID</th> 
<th field="barcode" width="150">BARCODE</th>
<th field="date" width="150">TANGGAL</th>
<th field="status" width="150">STATUS</th>
<th field="file" width="100">FOTO</th>
</tr>
</thead>
</table>

<div id="toolbar">
<table width="100%" cellpadding=0 cellspacing=0><tr><td>
From date <?=$f->from_date("start_date","","","",false,1);?> Up to date <?=$f->up_to_date("end_date","","","",false,1);?>
<input class="easyui-searchbox" data-options="prompt:'Search...',menu:'#mm',searcher:doSearch" style="width:300px">
</td>
<td align="right">
	<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-print" plain="true" onclick="cetak()">Print</a>
</td>
</tr></table>
</div>
<div id="mm">
		<div data-options="name:'all',iconCls:'icon-ok'">All Category</div>		
		<div data-options="name:'barcode',iconCls:'icon-ok'">Barcode</div>
		<div data-options="name:'status',iconCls:'icon-ok'">Status</div>
</div>

<script type="text/javascript">
var url;
function cetak(){
	var start_date 	= $('#str_date').val();
	var end_date 	= $('#upto_date').val();
	window.location.assign("/carpool/list_carpoolExcel.php?f_start_date="+start_date+"&f_end_date="+end_date);
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
</script>