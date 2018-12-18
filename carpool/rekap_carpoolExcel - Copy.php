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

$f_lkeid=(empty($lke_id))?"":"a.lke_id='$lke_id' and";
	
$sql="select a.* from rekap_carpool a 
left join t_carpool_payment b on b.ocs_id=a.ocs_id
where b.status='PAID' and a.jenis='CARPOOL' and $f_lkeid a.str_date between '$f_start_date' and '$f_end_date'";
	
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
				<th width="100">DATE</th>
				
				<th width="120">PROJECT CODE</th>
				<th width="200">PROJECT NAME</th>
				<th width="120">PROJECT MANAGER</th>
				<th width="120">COORDINATOR</th>
				<th width="120">DT/SURVEYOR</th>
				<th width="100">KM START</th>
                <th width="100">KM END</th>
				<th width="120">DRIVER</th>
				<th width="100">NO_POLISI</th>
				<th width="100" align="right">UANG PULSA</th>
                <th width="100" align="right">UANG MAKAN</th>
				<th width="100" align="right">UANG JALAN</th>
                <th width="100" align="right">PARKING</th>
				<th width="100" align="right">PORTAL</th>
                <th width="100" align="right">THREE IN ONE</th>
				<th width="100" align="right">UTB</th>
				<th width="100" align="right">BBM</th>
				<th width="100" align="right">BBM LTR</th>
                <th width="100" align="right">ETOLL</th>
                <th width="100" align="right">MTOLL</th>
                <th width="100" align="right">OTHERS</th>
				<th width="100" align="right">TOTAL</th>
				<th width="150">AREA</th>
			</tr>
        </thead>
<tbody>
<?php 
while($row=$sqlExc->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$totalx=str_replace(",","",$uang_pulsa)+str_replace(",","",$um)+str_replace(",","",$uj)+str_replace(",","",$parking)+str_replace(",","",$portal)+str_replace(",","",$three_in_one)+
		str_replace(",","",$utb)+str_replace(",","",$bbm)+str_replace(",","",$etoll)+str_replace(",","",$mtoll)+str_replace(",","",$others);
		$total=number_format($totalx,0,".","");
		$area=$dbproj->getOne("select lokasi_kerja from m_project_area where lke_id='$lke_id'");
		
?>
			<style> .f_text{ mso-number-format:\@; } </style>
			<tr>
				<td width="100" align="center"><?=$ocs_id?></td>
				<td width="100" align="center"><?=substr($str_date,0,10);?></td>
				<td width="150" align="left"><?=$proj_code;?></td>
				<td width="300" align="left"><?=$proj_name;?></td>
				<td width="200" align="left"><?=$pm_name;?></td>
				<td width="200" align="left"><?=$dt_coord;?></td>
				<td width="200" align="left"><?=$dt_name;?></td>
				<td width="100"><?=$km_start;?></td>
                <td width="100"><?=$km_end;?></td>
				<td width="120"><?=$driver;?></td>
				<td width="100"><?=$no_polisi;?></td>
				<td width="100" align="right"><?=number_format($uang_pulsa,0,".","");?></td>
                <td width="100" align="right"><?=number_format($um,0,".","");?></td>
				<td width="100" align="right"><?=number_format($uj,0,".","");?></td>
                <td width="100" align="right"><?=number_format($parking,0,".","");?></td>
				<td width="100" align="right"><?=number_format($portal,0,".","");?></td>
                <td width="100" align="right"><?=number_format($three_in_one,0,".","");?></td>
				<td width="100" align="right"><?=number_format($utb,0,".","");?></td>
				<td width="100" align="right"><?=number_format($bbm,0,".","");?></td>
				<td width="100" align="right"><?=$bbm_ltr;?></td>
                <td width="100" align="right"><?=number_format($etoll,0,".","");?></td>
                <td width="100" align="right"><?=number_format($mtoll,0,".","");?></td>
                <td width="100" align="right"><?=number_format($others,0,".","");?></td>
				<td width="100" align="right"><?=$total;?></td>
				<td width="100" align="right"><?=$area;?></td>
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
		$grand_total=$grand_total+$totalx;
}
	$total_uang_pulsa=number_format($total_uang_pulsa,0,".","");
	$total_um=number_format($total_um,0,".","");
	$total_uj=number_format($total_uj,0,".","");
	$total_parking=number_format($total_parking,0,".","");
	$total_portal=number_format($total_portal,0,".","");
	$total_three_in_one=number_format($total_three_in_one,0,".","");
	$total_utb=number_format($total_utb,0,".","");
	$total_bbm=number_format($total_bbm,0,".","");
	$total_etoll=number_format($total_etoll,0,".","");
	$total_mtoll=number_format($total_mtoll,0,".","");
	$total_others=number_format($total_others,0,".","");
	$grand_total=number_format($grand_total,0,".","");
?>
			<tr>
				<td width="100"></td>
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
				<td width="100" align="right"><b><?=$total_uang_pulsa;?></b></td>
                <td width="100" align="right"><b><?=$total_um;?></b></td>
				<td width="100" align="right"><b><?=$total_uj;?></b></td>
                <td width="100" align="right"><b><?=$total_parking;?></b></td>
				<td width="100" align="right"><b><?=$total_portal;?></b></td>
                <td width="100" align="right"><b><?=$total_three_in_one;?></b></td>
				<td width="100" align="right"><b><?=$total_utb;?></b></td>
				<td width="100" align="right"><b><?=$total_bbm;?></b></td>
				<td width="100" align="right"></th>
                <td width="100" align="right"><b><?=$total_etoll;?></b></td>
                <td width="100" align="right"><b><?=$total_mtoll;?></b></td>
                <td width="100" align="right"><b><?=$total_others;?></b></td>
				<td width="100" align="right"><b><?=$grand_total;?></b></td>
				<td width="100"></td>
			</tr>
		<tbody>
</table>