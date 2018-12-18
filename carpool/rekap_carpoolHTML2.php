<?php
	include_once($DOCUMENT_ROOT."/s/config.php");
	$template->basicheader(2);
	$f_start_date	=	$_REQUEST['start_date'];
	$f_start_date	= (empty($f_start_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_start_date,1,'/'));
	$f_end_date		=	$_REQUEST['end_date'];
	$f_end_date	= (empty($f_end_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_end_date,1,'/'));
?>
<table class="easyui-datagrid" title="Rekap Carpool" style="width:100%;height:500px"
data-options="
singleSelect: true,
collapsible: true,
rownumbers: true,
striped: true,
nowrap: false,
url: 'rekap_carpoolGet2.php?act=cetak&pm_id=<?=$pm_id;?>&proj_id=<?=$proj_code;?>&f_start_date=<?=$f_start_date?>&f_end_date=<?=$f_end_date?>&lke_id=<?=$lke_id?>',
method: 'get',
toolbar:'#toolbar',
showFooter:true
">
<thead data-options="frozen:true">
<tr>
<th field="proj_code" width="120">Project Code</th>
<th data-options="field:'proj_name',width:200">Project Name</th>
</tr>
</thead>
<thead>
<tr>			<th field="uang_pulsa" width="100" align="right">UANG PULSA</th>
                <th field="um" width="100" align="right">UANG MAKAN</th>
				<th field="uj" width="100" align="right">UANG JALAN</th>
                <th field="parking" width="100" align="right">PARKING</th>
				<th field="portal" width="100" align="right">PORTAL</th>
                <th field="three_in_one" width="100" align="right">THREE IN ONE</th>
				<th field="utb" width="100" align="right">UTB</th>
				<th field="bbm" width="100" align="right">BBM</th>
                <th field="etoll" width="100" align="right">ETOLL</th>
                <th field="mtoll" width="100" align="right">MTOLL</th>
                <th field="others" width="100" align="right">OTHERS</th>
				<th field="total" width="100" align="right">TOTAL</th>
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
<td align="CENTER">
	<b>REKAP CARPOOL FROM DATE <?=$f_start_date?> UP TO DATE <?=$f_end_date?></b>
</td>
<td align="right">
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-print" plain="true" onclick="alert('Maaf, Belum tersedia!')">Print</a>
</td>
</tr></table>
</div>