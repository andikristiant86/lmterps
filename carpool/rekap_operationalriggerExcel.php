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
	
$sql="select prg_id,convert(varchar(10),req_date,103) as req_date,req_name,proj_code,proj_name,pm_name,
paid_by,payment,sewa_motor,allowance
from t_op_rigger_real a 
where $f_lkeid req_date between '$f_start_date' and '$f_end_date' order by req_name asc, req_date desc";
	
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
				
				<th width="100">REQ DATE</th>
				<th width="120">REQUEST NAME</th>
				<th width="120">PROJECT CODE</th>
				<th width="200">PROJECT NAME</th>
				<th width="120">PROJECT MANAGER</th>
				<th width="120">CASHIER</th>
				<th width="100" align="right">ALLOWANCE</th>
                <th width="100" align="right">SEWA MOTOR</th>
				<th width="150" align="right">TOTAL REQUEST</th>
				<th width="100" align="right">AMOUNT PAID</th>
				
			</tr>
        </thead>
<tbody>
<?php 
while($row=$sqlExc->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		
		$paymentRp=number_format($payment,0,".",",");
		$sewa_motorRp=number_format($sewa_motor,0,".",",");
		$allowanceRp=number_format($allowance,0,".",",");
		
		$totalx=$sewa_motor+$allowance;
		$totalxRp=number_format($totalx,0,".",",");
?>
			<style> .f_text{ mso-number-format:\@; } </style>
			<tr>
				<td width="100" align="center"><?=$prg_id;?></td>
				
				<td width="100" align="center"><?=$req_date;?></td>
				<td width="200" align="left"><?=$req_name;?></td>
				<td width="150" align="left"><?=$proj_code;?></td>
				<td width="300" align="left"><?=$proj_name;?></td>
				<td width="200" align="left"><?=$pm_name;?></td>
				<td width="200" align="left"><?=$paid_by;?></td>
				
				<td width="100" align="right"><?=$allowanceRp;?></td>
                <td width="100" align="right"><?=$sewa_motorRp;?></td>
				<td width="100" align="right"><?=$totalxRp;?></td>
                <td width="100" align="right"><?=$paymentRp;?></td>
				
			</tr>
<?
		//total
		$t_operational=$t_operational+$allowance;
		$t_rigger=$t_rigger+$sewa_motor;
		$t_tools=$t_tools+$totalx;
		$t_reimburse=$t_reimburse+$payment;
		
}
		$t_operationalRp=number_format($t_operational,0,".",",");
		$t_riggerRp=number_format($t_rigger,0,".",",");
		$t_toolsRP=number_format($t_tools,0,".",",");
		$t_reimburseRp=number_format($t_reimburse,0,".",",");
		
?>
			<tr>
				<td width="100"></td>
				<td width="100"></td>
				
				<td width="120"></td>
				<td width="200"></td>
				<td width="150"></td>
				<td width="150"></td>
				<td width="150"><b>GRAND TOTAL</b></td>
				<td width="100" align="right"><b><?=$t_operationalRp;?></b></td>
                <td width="100" align="right"><b><?=$t_riggerRp;?></b></td>
				<td width="100" align="right"><b><?=$t_toolsRP;?></b></td>
                <td width="100" align="right"><b><?=$t_reimburseRp;?></b></td>
			</tr>
		<tbody>
</table>