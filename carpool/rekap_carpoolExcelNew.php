<?php 
$date=date("Ym");
if($t!="y"){
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=Report_Carpool_$date.xls");
header("Pragma: no-cache");
header("Expires: 0");
}
include("$DOCUMENT_ROOT/s/config.php");
$f_start_date	=	$_REQUEST['start_date'];
$f_start_date	= (empty($f_start_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_start_date,1,'/'));
$f_end_date		=	$_REQUEST['end_date'];
$f_end_date	= (empty($f_end_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_end_date,1,'/'));

$f_lkeid=(empty($lke_id))?"":"lke_id='$lke_id' and";
	
$sql="select * from t_carpool_real_detail
where $f_lkeid paid_date between '$f_start_date' and '$f_end_date'";
	
if($t=="y")echo("$sql<br>");

$sqlExc=$dbproj->Execute($sql);

$area_name=$dbproj->getOne("select lokasi_kerja from m_project_area where lke_id='$lke_id'");
?>
<style>
.boldtable, .boldtable TD, .boldtable TH
{
	font-family:sans-serif;
	font-size:9pt;
}
</style>
<b>REPORT CAR POOL <?=$area_name;?> FROM DATE <?=$f_start_date;?> UP TO DATE <?=$f_end_date?></b><BR>
<table border="1" CLASS="boldtable">
        <thead>
			<tr>
				<th width="100">REQ ID</th>
				<th width="100">REQUEST DATE</th>
				<th width="100">PAID DATE</th>
				<th width="100">WEEK NUM</th>
				<th width="100">PERIODE</th>
				<th width="120">PROJECT CODE</th>
				<th width="200">PROJECT NAME</th>
				<th width="120">PROJECT MANAGER</th>
				<th width="120">COORDINATOR</th>
				<th width="120">DT/SURVEYOR</th>
				<th width="120">DRIVER</th>
				<th width="100">NO_POLISI</th>
				<th width="100">KM_START</th>
				<th width="100">KM_END</th>
				<th width="100">TOTAL_KM</th>
				<th width="150">AREA</th>
				<th width="100" align="right">OTHERS</th>
				<th width="100" align="right">BBM</th>
				<th width="100" align="right">ETOLL</th>
				<th width="100" align="right">TOTAL</th>
			</tr>
        </thead>
<tbody>
<?php 
while($row=$sqlExc->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$nominalRp=number_format($nominal,0,".",",");
		$bbmRp=number_format($bbm,0,".",",");
		$etolRp=number_format($etol,0,".",",");
		$total=$nominal+$bbm+$etol;
		$totalRp=number_format($total,0,".",",");
?>
			<style> .f_text{ mso-number-format:\@; } </style>
			<tr>
				<td width="100" align="center"><?=$ocs_id?></td>
				<td width="100" align="center"><?=$f->convert_date($req_date,1);?></td>
				<td width="100" align="center"><?=$f->convert_date($paid_date,1);?></td>
				<td width="100" align="center"><?=$weekn;?></td>
				<td width="100" align="center"><?=$periode;?></td>
				<td width="150" align="left"><?=$proj_code;?></td>
				<td width="300" align="left"><?=$proj_name;?></td>
				<td width="200" align="left"><?=$pm_nm;?></td>
				<td width="200" align="left"><?=$create_by;?></td>
				<td width="200" align="left"><?=$dt_nm;?></td>
				<td width="120"><?=$driver_nm;?></td>
				<td width="100"><?=$no_polisi;?></td>
				<td width="100"><?=$km_start;?></td>
				<td width="100"><?=$km_end;?></td>
				<td width="100"><?=$total_km;?></td>
				<td width="100" align="right"><?=$lokasi_kerja;?></td>
				<td width="100" align="right"><?=$nominalRp;?></td>
				<td width="100" align="right"><?=$bbmRp;?></td>
				<td width="100" align="right"><?=$etolRp;?></td>
				<td width="100" align="right"><?=$totalRp;?></td>
			</tr>
<?
$grand_total=$grand_total+$nominal;
$grand_total1=$grand_total1+$bbm;
$grand_total2=$grand_total2+$etol;
$grand_total3=$grand_total3+$total;
}
	$grand_total=number_format($grand_total,0,".",",");
	$grand_total1=number_format($grand_total1,0,".",",");
	$grand_total2=number_format($grand_total2,0,".",",");
	$grand_total3=number_format($grand_total3,0,".",",");
?>
			<tr>
				<td width="100"></td>
				<td width="100"></td>
				<td width="100"></td><td width="100"></td>				
				<td width="100"></td>
				<td width="120"></td>
				<td width="200"><b>TOTAL</b></td>
				<td width="150"></td>
				<td width="100"></td>
				<td width="100"></td>
				<td width="100"></td>
				<td width="100"></td>
				<td width="100"></td>
				<td width="100"></td>
				<td width="100"></td>
				<td width="100"></td>
				<td width="100" align="right"><b><?=$grand_total;?></b></td>
				<td width="100" align="right"><b><?=$grand_total1;?></b></td>
				<td width="100" align="right"><b><?=$grand_total2;?></b></td>
				<td width="100" align="right"><b><?=$grand_total3;?></b></td>
			</tr>
		<tbody>
</table>