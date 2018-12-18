<?php 
ob_start();
session_start();
include_once($DOCUMENT_ROOT."/s/config.php");
$project=$dbproj->getOne("select (select proj_name from m_project where id=m_carpool.proj_id) from m_carpool where ocs_id='$ocs_id'");
$driver=$dbproj->getOne("select (select top 1 nm_peg from spg_data_current where nip=t_carpool.nip_driver) from t_carpool where ocs_id='$ocs_id'");
$no_polisi=$dbproj->getOne("select (select top 1 no_polisi from inv_data_kendaraan where kode_kendaraan=t_carpool.car_number) from t_carpool where ocs_id='$ocs_id'");
$dt=$dbproj->getOne("select (select top 1 nm_peg from spg_data_current where nip=m_carpool.nip_dt) from m_carpool where ocs_id='$ocs_id'");
$dt_coord=$dbproj->getOne("select (select top 1 nm_peg from spg_data_current where nip=m_carpool.dt_coord) from m_carpool where ocs_id='$ocs_id'");
$km_start=$dbproj->getOne("select top 1 km_start from t_carpool where ocs_id='$ocs_id'");
$desc=$dbproj->getOne("select ocs_desc from m_carpool where ocs_id='$ocs_id'");

$status=$dbproj->getOne("select case isnull(status,'') when '1' then 'JALAN' when '2' then 'PULANG' when '3' then 'CLOSED' else 'BELUM JALAN' end as sts_nm from m_carpool where ocs_id='$ocs_id'");


$km_end=$dbproj->getOne("select top 1 km_end from t_carpool where ocs_id='$ocs_id'");
$km_total=$km_start+$km_end;
$um=$dbproj->getOne("select sum(um) from t_carpool where ocs_id='$ocs_id'");
$umRp=number_format($um,0,".",",");
$uj=$dbproj->getOne("select sum(uj) from t_carpool where ocs_id='$ocs_id'");
$ujRp=number_format($uj,0,".",",");
$bbm=$dbproj->getOne("select sum(bbm) from t_carpool where ocs_id='$ocs_id'");
$bbmRp=number_format($bbm,0,".",",");
$etol=$dbproj->getOne("select sum(etoll) from t_carpool where ocs_id='$ocs_id'");
$etolRp=number_format($etol,0,".",",");
$mtol=$dbproj->getOne("select sum(mtoll) from t_carpool where ocs_id='$ocs_id'");
$mtolRp=number_format($mtol,0,".",",");
$parking=$dbproj->getOne("select sum(parking) from t_carpool where ocs_id='$ocs_id'");
$parkingRp=number_format($parking,0,".",",");
$portal=$dbproj->getOne("select sum(portal) from t_carpool where ocs_id='$ocs_id'");
$portalRp=number_format($portal,0,".",",");
$three_in_one=$dbproj->getOne("select sum(three_in_one) from t_carpool where ocs_id='$ocs_id'");
$three_in_oneRp=number_format($three_in_one,0,".",",");
$utb=$dbproj->getOne("select sum(utb) from t_carpool where ocs_id='$ocs_id'");
$utbRp=number_format($utb,0,".",",");
$others=$dbproj->getOne("select sum(others) from t_carpool where ocs_id='$ocs_id'");
$othersRp=number_format($others,0,".",",");
$total=$um+$uj+$bbm+$etol+$mtol+$parking+$portal+$three_in_one+$utb+$others;
$total=number_format($total,0,".",",");
?>
<script type="text/javascript">
<!--
function printPartOfPage(elementId)
{
 var printContent = document.getElementById(elementId);
 var windowUrl = 'about:blank';
 var uniqueName = new Date();
 var windowName = 'Print' + uniqueName.getTime();

 var printWindow = window.open(windowUrl, windowName, 'left=50000,top=50000,width=0,height=0');

 printWindow.document.write(printContent.innerHTML);
 printWindow.document.close();
 printWindow.focus();
 printWindow.print();
 printWindow.close();
}
// -->
</script>

<div id="printDiv">
<style>
.boldtable, .boldtable TD
{
	font-family:sans-serif;
	font-size:9pt;
}
.boldtable TH{
	font-family:sans-serif;
	font-size:12pt;
	background-color:#dddddd;
}.tableTH TH{
	font-family:sans-serif;
	font-size:9pt;
	background-color:#dddddd;
}.tableTH, .tableTH TD
{
	font-family:sans-serif;
	font-size:9pt;
}
</style>
<img src="LMT.jpg" width="120" height="60">
<table width="100%" CLASS="boldtable" border=0 cellspacing=1 cellpadding=1>
<tr>
	<th align="center" colspan="6">
		TRANSPORTATION DAILY ALLOWANCE - CLAIM FORM BY ERPS
	</th>
</tr>

<tr>
		<td width="10%">Project</td> <td width="30%">: <?=$project?></td> <td width="10%">Date</td><td>: <?=date("d/m/Y");?></td> <td>Car No</td><td>: <?=$ocs_id?></td>
</tr>
<tr>
		<td>Driver</td> <td>: <?=$driver;?></td> <td>DT Enginer</td><td>: <?=$dt;?></td> <td>Status</td><td>: <b><?=$status?></b></td>
</tr>
<tr>
		<td>Description</td> <td colspan="4">: <?=$desc;?></td>
</tr>
</table>

<table width="100%" CLASS="boldtable">
<tr>
	<td colspan="6">
		<b>RECAPITULATION</b>
	</td>
</tr>
</table>
<table width="100%" CLASS="tableTH" border=0 cellspacing=1 cellpadding=1>
<tr>
	<th width="10%"></th><th>CAR</th><th>Type</th><th>COST</th>
</tr>
<tr>
	<td>NO POL</td><td><?=$no_polisi?></td><td>UANG MAKAN</td><td align="right"><?=$umRp;?></td>
</tr>
<tr>
	<td>KM START</td><td><?=$km_start?></td><td>UANG JALAN</td><td align="right"><?=$ujRp;?></td>
</tr>
<tr>
	<td>KM END</td><td><?=$km_end?></td><td>BBM</td><td align="right"><?=$bbmRp;?></td>
</tr>
<tr>
	<td>KM TOTAL</td><td><?=$km_total?></td><td>ETOLL</td><td align="right"><?=$etolRp;?></td>
</tr>
<tr>
	<td></td><td></td><td>MTOLL</td><td align="right"><?=$mtolRp;?></td>
</tr>
<tr>
	<td></td><td></td><td>PARKING</td><td align="right"><?=$parkingRp;?></td>
</tr>
<tr>
	<td></td><td></td><td>PORTAL</td><td align="right"><?=$portalRp;?></td>
</tr>
<tr>
	<td></td><td></td><td>THREE IN ONE</td><td align="right"><?=$three_in_oneRp;?></td>
</tr>
<tr>
	<td></td><td></td><td>UANG TAMBAL BAN</td><td align="right"><?=$utbRp;?></td>
</tr>
<tr>
	<td></td><td></td><td>OTHERS</td><td align="right"><?=$othersRp;?></td>
</tr>
<tr>
	<th><br></th><th></th><th></th><th></th>
</tr>
<tr>
	<td></td><td></td><td><B>TOTAL</B></td><td align="right"><b><?=$total;?></b></td>
</tr>
<tr>
	<td></td><td align="center"><b>Admin Carpool</b></td><td><B></B></td><td align="center"><b>Applicant</b></td>
</tr>
<tr>
	<td></td><td></td><td><B></B></td><td align="right"><b><br></b></td>
</tr>
<tr>
	<td></td><td></td><td><B></B></td><td align="right"><b><br></b></td>
</tr>
<tr>
	<td></td><td></td><td><B></B></td><td align="right"><b><br></b></td>
</tr>
<tr>
	<td></td><td align="center"><b><u><?=$db->getOne("select nm_peg from spg_data_current where nip='$login_nip'");?></u></b></td><td><B></B></td><td align="center">
	<b><u><?=$dt_coord;?></u></b>
	</td>
</tr>
</table>
</div>
<input type="button" value="Print" onclick="JavaScript:printPartOfPage('printDiv');" >