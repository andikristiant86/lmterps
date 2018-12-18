<?php
	include_once($DOCUMENT_ROOT."/s/config.php");
	$template->basicheader(2);
?>
 <table class="easyui-datagrid" title="Rekap Carpool" style="width:100%;height:475px"
data-options="
singleSelect: true,
collapsible: true,
rownumbers: true,
url: 'rekap_carpoolGet.php?act=cetak&pm_id=<?=$pm_id;?>&proj_id=<?=$proj_code;?>',
method: 'get',
toolbar:'#toolbar'
">
<thead data-options="frozen:true">
<tr>
<th field="proj_code" width="120">Project Code</th>
<th data-options="field:'proj_name',width:150">Project Name</th>
</tr>
</thead>
<thead>
<tr>
				<th field="namaSP" width="150">DRIVER</th>
				<th field="km_start" width="100">KM START</th>
                <th field="km_end" width="100">KM END</th>
                <th field="um" width="100" align="right">UM</th>
				<th field="uj" width="100" align="right">UJ</th>
                <th field="parking" width="100" align="right">PARKING</th>
				<th field="portal" width="100" align="right">PORTAL</th>
                <th field="three_in_one" width="100" align="right">THREE IN ONE</th>
				<th field="utb" width="100" align="right">UTB</th>
				<th field="bbm" width="100" align="right">BBM</th>
				<th field="bbm_ltr" width="100" align="right">BBM LTR</th>
                <th field="etoll" width="100" align="right">ETOLL</th>
                <th field="mtoll" width="100" align="right">MTOLL</th>
                <th field="others" width="100" align="right">OTHERS</th>
				<th field="total" width="100" align="right">Total</th>
				<!--
				<th field="description" width="200">DESCRIPTION</th>
                <th field="date_berangkat" width="150">DATE OF DEPARTURE</th>
				<th field="remaks" width="100">REMAKS</th>-->
</tr>
</thead>
</table>
<div id="toolbar">
<table width="100%" cellpadding=0 cellspacing=0><tr><td>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-back" plain="true" onclick="window.location.assign('rekap_carpool.php')">Back</a>
</td>
<td align="right">
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-print" plain="true" onclick="alert('Maaf, Belum tersedia!')">Print</a>
</td>
</tr></table>
</div>