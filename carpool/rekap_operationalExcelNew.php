<?php 
$date=date("Ym");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=REKAP_OPCOST_$date.xls");
header("Pragma: no-cache");
header("Expires: 0");

include("$DOCUMENT_ROOT/s/config.php");
$f_start_date	=	$_REQUEST['start_date'];
$f_start_date	= (empty($f_start_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_start_date,1,'/'));
$f_end_date		=	$_REQUEST['end_date'];
$f_end_date	= (empty($f_end_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_end_date,1,'/'));

$f_lkeid=(empty($lke_id))?"":"lke_id='$lke_id' and";

$sql="select * from t_operational_real where $f_lkeid 
tgl_transfer between '$f_start_date' and '$f_end_date'";
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
<b>REPORT OPERATIONAL <?=$area_name;?> FROM DATE <?=$f_start_date;?> UP TO DATE <?=$f_end_date?></b><BR>
<table border="1" CLASS="boldtable">
        <thead>
			<tr>
				<th width="100">REQ ID</th>
				<th width="120">TYPE</th>
				<th width="100">DATE</th>
				<th width="100">WEEK</th>
				<th width="100">PERIODE</th>
				<th width="120">REQUEST NAME</th>
				<th width="120">PROJECT CODE</th>
				<th width="200">PROJECT NAME</th>
				<th width="120">PROJECT MANAGER</th>
				<th width="120">AREA</th>
				<th width="100" align="right">AMOUNT</th>
			</tr>
        </thead>
<tbody>
<?php 
while($row=$sqlExc->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$tgl_transfer=$f->convert_date($tgl_transfer,1);
		$operationalRp=number_format($nominal,0,".",",");
?>
			<style> .f_text{ mso-number-format:\@; } </style>
			<tr>
				<td width="100" align="center"><?=$pengajuan_id;?></td>
				<td width="200" align="left"><?=$nm_jenis;?></td>
				<td width="100" align="center"><?=$tgl_transfer;?></td>
				<td width="100" align="center"><?=$weekn;?></td>
				<td width="100" align="center"><?=$periode;?></td>
				<td width="200" align="left"><?=$req_name;?></td>
				<td width="150" align="left"><?=$proj_code;?></td>
				<td width="300" align="left"><?=$proj_name;?></td>
				<td width="200" align="left"><?=$pm_name;?></td>
				<td width="200" align="left"><?=$lokasi_kasir;?></td>
				<td width="100" align="right"><?=$operationalRp;?></td>
			</tr>
<?
		//tota
		$t_totalx=$t_totalx+$nominal;
}
		$t_totalxRP=number_format($t_totalx,0,".",",");
?>
			<tr>
				<td width="100"></td><td width="100"></td>
				<td width="100"></td>
				<td width="100"></td>
				<td width="100"></td>
				<td width="120"></td>
				<td width="200"></td>
				<td width="150"></td>
				<td width="150"></td>
				<td width="150"><b>GRAND TOTAL</b></td>
				<td width="100" align="right"><b><?=$t_totalxRP;?></b></td>
                
			</tr>
		<tbody>
</table>