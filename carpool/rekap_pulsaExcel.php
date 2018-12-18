<?php 
$date=date("Ym");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=REPORT_PULSA_$date.xls");
header("Pragma: no-cache");
header("Expires: 0");

include("$DOCUMENT_ROOT/s/config.php");
$f_start_date	=	$_REQUEST['start_date'];
$f_start_date	= (empty($f_start_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_start_date,1,'/'));
$f_end_date		=	$_REQUEST['end_date'];
$f_end_date	= (empty($f_end_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_end_date,1,'/'));

$f_lkeid=(empty($lke_id))?"":"lke_id='$lke_id' and";
	
$sql="select * from t_pulsa_real where $f_lkeid convert(date, received_date) between '$f_start_date' and '$f_end_date'";
	
$sqlExc=$dbproj->Execute($sql);
?>
<style>
.boldtable, .boldtable TD, .boldtable TH
{
	font-family:sans-serif;
	font-size:9pt;
}
</style>
<table border="1" CLASS="boldtable">
        <thead>
			<tr>
				<th width="100">REQ ID</th>
				<th width="100" align="right">PHONE NUMBER</th>
				<th width="100">TYPE</th>
				<th width="100">DATE</th>
				<th width="100">WEEK</th>
				<th width="100">PERIODE</th>
				<th width="120">PROJECT CODE</th>
				<th width="200">PROJECT NAME</th>
				<th width="120">PROJECT MANAGER</th>
				<th width="120">RESOURCE NAME</th>
				<th width="150">AREA</th>
				<th width="100" align="right">AMOUNT</th>
				
				
			</tr>
        </thead>
<tbody>
<style> .f_text{ mso-number-format:\@; } </style>
<?php 
while($row=$sqlExc->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$pulsaRp=number_format($amount,0,".",",");
?>
			
			<tr>
				<td width="100" align="center"><?=$pls_id?></td>
				<td width="100" align="right" id="f_text" class="f_text"><?=$phone_number;?></td>
				<td width="100"><?=$transfer_type;?></td>
				<td width="100" align="left"><?=$received_date_conv;?></td>
				<td width="100" align="center"><?=$weekn;?></td>
				<td width="100" align="left"><?=$periode?></td>
				<td width="150" align="left"><?=$project_code;?></td>
				<td width="300" align="left"><?=$project_name;?></td>
				<td width="200" align="left"><?=$pm_nm;?></td>
				<td width="200" align="left"><?=$req_nm;?></td>
				<td width="100" align="right"><?=$lokasi_kerja;?></td>
				<td width="100" align="right"><?=$pulsaRp;?></td>
				
				
			</tr>
<?	
	$grand_total=$grand_total+$amount;
}
	$grand_totalRp=number_format($grand_total,0,".",",");
?>
			<tr>
				<td width="100"></td>  <td width="100"></td>
				<td width="100"></td>
				<td width="100"></td>
				<td width="100"></td>
				<td width="100"></td>
				<td width="120"></td>
				<td width="200"><b>TOTAL</b></td>
				<td width="150"></td>
				<td width="100"></td>
				<td width="100"></td>
				<td width="100" align="right"><b><?=$grand_totalRp;?></b></td>
             
				
			</tr>
		<tbody>
</table>