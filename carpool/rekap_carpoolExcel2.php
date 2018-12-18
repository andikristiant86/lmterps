<?php 
$date=date("Ym");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=Report_Carpool_$date.xls");
header("Pragma: no-cache");
header("Expires: 0");

include("$DOCUMENT_ROOT/s/config.php");
$f_start_date	=	$_REQUEST['start_date'];
$f_start_date	= (empty($f_start_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_start_date,1,'/'));
$f_end_date		=	$_REQUEST['end_date'];
$f_end_date	= (empty($f_end_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_end_date,1,'/'));
	
$f_lkeid=(empty($lke_id))?"":"lke_id='$lke_id' and";
	
$sql="select proj_id,proj_code, proj_name, sum(um) as um, sum(uj) as uj, sum(parking) as parking, sum(portal) as portal, sum(three_in_one) as three_in_one,
	sum(utb) as utb, sum(bbm) as bbm, sum(etoll) as etoll, sum(mtoll) as mtoll,sum(others) as others,sum(uang_pulsa) as uang_pulsa,
	sum(um+uj+parking+portal+three_in_one+utb+bbm+etoll+mtoll+others+uang_pulsa) as total
	from rekap_carpool where $f_lkeid str_date between '$f_start_date' and '$f_end_date' group by proj_code, proj_name,proj_id";
	
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
				
				<th width="120">PROJECT CODE</th>
				<th width="200">PROJECT NAME</th>
				<th width="100" align="right">UANG PULSA</th>
                <th width="100" align="right">UANG MAKAN</th>
				<th width="100" align="right">UANG JALAN</th>
                <th width="100" align="right">PARKING</th>
				<th width="100" align="right">PORTAL</th>
                <th width="100" align="right">THREE IN ONE</th>
				<th width="100" align="right">UTB</th>
				<th width="100" align="right">BBM</th>
                <th width="100" align="right">ETOLL</th>
                <th width="100" align="right">MTOLL</th>
                <th width="100" align="right">OTHERS</th>
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
?>
			<style> .f_text{ mso-number-format:\@; } </style>
			<tr>
				<td width="150" align="left"><?=$proj_code;?></td>
				<td width="300" align="left"><?=$proj_name;?></td>
				<td width="100" align="right"><?=number_format($uang_pulsa,0,".",",");?></td>
                <td width="100" align="right"><?=number_format($um,0,".",",");?></td>
				<td width="100" align="right"><?=number_format($uj,0,".",",");?></td>
                <td width="100" align="right"><?=number_format($parking,0,".",",");?></td>
				<td width="100" align="right"><?=number_format($portal,0,".",",");?></td>
                <td width="100" align="right"><?=number_format($three_in_one,0,".",",");?></td>
				<td width="100" align="right"><?=number_format($utb,0,".",",");?></td>
				<td width="100" align="right"><?=number_format($bbm,0,".",",");?></td>
                <td width="100" align="right"><?=number_format($etoll,0,".",",");?></td>
                <td width="100" align="right"><?=number_format($mtoll,0,".",",");?></td>
                <td width="100" align="right"><?=number_format($others,0,".",",");?></td>
				<td width="100" align="right"><?=$total;?></td>
			</tr>
<?
		$total_uang_pulsa=$total_uang_pulsa+str_replace(",","",$uang_pulsa);
		$total_um=$total_um+str_replace(",","",$um);
		$total_uj=$total_uj+str_replace(",","",$uj);
		$total_parking=$total_parking+str_replace(",","",$parking);
		$total_portal=$total_portal+str_replace(",","",$portal);
		$total_three_in_one=$total_three_in_one+str_replace(",","",$three_in_one);
		$total_utb=$total_utb+str_replace(",","",$utb);
		$total_bbm=$total_bbm+str_replace(",","",$bbm);
		$total_etoll=$total_etoll+str_replace(",","",$etoll);
		$total_mtoll=$total_mtoll+str_replace(",","",$mtoll);
		$total_others=$total_others+str_replace(",","",$others);
		$grand_total=$grand_total+str_replace(",","",$total);
}
	$total_uang_pulsa=number_format($total_uang_pulsa,0,".",",");
	$total_um=number_format($total_um,0,".",",");
	$total_uj=number_format($total_uj,0,".",",");
	$total_parking=number_format($total_parking,0,".",",");
	$total_portal=number_format($total_portal,0,".",",");
	$total_three_in_one=number_format($total_three_in_one,0,".",",");
	$total_utb=number_format($total_utb,0,".",",");
	$total_bbm=number_format($total_bbm,0,".",",");
	$total_etoll=number_format($total_etoll,0,".",",");
	$total_mtoll=number_format($total_mtoll,0,".",",");
	$total_others=number_format($total_others,0,".",",");
	$grand_total=number_format($grand_total,0,".",",");
?>
			<tr>
				
				<td width="120"></td>
				<td width="200"><b>TOTAL</b></td>
				<td width="100" align="right"><b><?=$total_uang_pulsa;?></b></td>
                <td width="100" align="right"><b><?=$total_um;?></b></td>
				<td width="100" align="right"><b><?=$total_uj;?></b></td>
                <td width="100" align="right"><b><?=$total_parking;?></b></td>
				<td width="100" align="right"><b><?=$total_portal;?></b></td>
                <td width="100" align="right"><b><?=$total_three_in_one;?></b></td>
				<td width="100" align="right"><b><?=$total_utb;?></b></td>
				<td width="100" align="right"><b><?=$total_bbm;?></b></td>
                <td width="100" align="right"><b><?=$total_etoll;?></b></td>
                <td width="100" align="right"><b><?=$total_mtoll;?></b></td>
                <td width="100" align="right"><b><?=$total_others;?></b></td>
				<td width="100" align="right"><b><?=$grand_total;?></b></td>
			</tr>
		<tbody>
</table>