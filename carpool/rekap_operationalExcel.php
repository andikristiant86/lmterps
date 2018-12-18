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

$f_lkeid=(empty($lke_id))?"":"x.lokasi='$lke_id' and";
	
$jumlah="(SELECT sum(nominal) FROM [dbo].[T_PEMBAYARAN_BIAYA_OP] where PENGAJUAN_ID=x.[no])";
	
$sql="select x.no,x.tgl,a.proj_code,a.proj_name,b.nm_peg as pm_name,c.lokasi_kerja as lokasi_kasir, d.nm_peg as pemohon, 
case when x.jenis in (5) then case when isnull(x.realisasi,0)=0 then $jumlah else x.realisasi end else 0 end AS operational,
case when x.jenis in (1) then case when isnull(x.realisasi,0)=0 then $jumlah else x.realisasi end else 0 end AS rigger,
case when x.jenis in (2) then case when isnull(x.realisasi,0)=0 then $jumlah else x.realisasi end else 0 end AS tools,
case when x.jenis in (3) then case when isnull(x.realisasi,0)=0 then $jumlah else x.realisasi end else 0 end AS reimburse,
case when x.jenis in (4) then case when isnull(x.realisasi,0)=0 then $jumlah else x.realisasi end else 0 end AS rental_mobil,
case when x.jenis in (17) then case when isnull(x.realisasi,0)=0 then $jumlah else x.realisasi end else 0 end AS tiket,
case when x.jenis in (19) then case when isnull(x.realisasi,0)=0 then $jumlah else x.realisasi end else 0 end AS sewa_tools,
case when x.jenis in (20) then case when isnull(x.realisasi,0)=0 then $jumlah else x.realisasi end else 0 end AS pengiriman_tools,
case when x.jenis in (21) then case when isnull(x.realisasi,0)=0 then $jumlah else x.realisasi end else 0 end AS entertain,
case when x.jenis in (22) then case when isnull(x.realisasi,0)=0 then $jumlah else x.realisasi end else 0 end AS hotel,
case when x.jenis in (23) then case when isnull(x.realisasi,0)=0 then $jumlah else x.realisasi end else 0 end AS atk,
case when x.jenis in (24) then case when isnull(x.realisasi,0)=0 then $jumlah else x.realisasi end else 0 end AS sembako,
case when x.jenis in (25) then case when isnull(x.realisasi,0)=0 then $jumlah else x.realisasi end else 0 end AS olahraga,
case when x.jenis in (26) then case when isnull(x.realisasi,0)=0 then $jumlah else x.realisasi end else 0 end AS driver
from t_pengajuan_biaya x 
left join m_project a on a.id=x.project_id
left join spg_data_current b on b.nip=a.pm_id
left join m_project_area c on c.lke_id=x.lokasi
left join spg_data_current d on d.nip=x.pemohon
where $f_lkeid x.[no] in (SELECT PENGAJUAN_ID FROM [dbo].[T_PEMBAYARAN_BIAYA_OP] GROUP BY PENGAJUAN_ID) and tgl between '$f_start_date' and '$f_end_date'";
	
$sqlExc=$dbproj->Execute($sql);

// $budget_plan=$dbproj->getOne("SELECT SUM(jumlah) FROM [dbo].[T_PENGAJUAN_BIAYA] WHERE x.[no] in (SELECT PENGAJUAN_ID FROM [dbo].[T_PEMBAYARAN_BIAYA_OP] GROUP BY PENGAJUAN_ID) and tgl between '$f_start_date' and '$f_end_date' and
// [jenis] in (SELECT JNS_ID FROM T_PENGAJUAN_BIAYA_PLAN WHERE LKE_ID='$lke_id') 
// and project_id in (SELECT proj_id FROM T_PENGAJUAN_BIAYA_PLAN WHERE LKE_ID='$lke_id')");
// $budget_planRp=number_format($budget_plan,0,".",",");

// $real_cost=$dbproj->getOne("SELECT SUM(case when isnull(x.realisasi,0)=0 then $jumlah else x.realisasi end) FROM [dbo].[T_PENGAJUAN_BIAYA] as x WHERE $f_lkeid 
// x.[no] in (SELECT PENGAJUAN_ID FROM [dbo].[T_PEMBAYARAN_BIAYA_OP] GROUP BY PENGAJUAN_ID) and 
// x.jenis in (5,1,2,3,4,17,19,20,21,22,23,24,25,26) and
// x.tgl between '$f_start_date' and '$f_end_date'");
// $real_costRp=number_format($real_cost,0,".",",");

// $saldo=$budget_plan-$real_cost;
// $saldoRp=number_format($saldo,0,".",",");

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
				
				<th width="100">DATE</th>
				<th width="120">REQUEST NAME</th>
				<th width="120">PROJECT CODE</th>
				<th width="200">PROJECT NAME</th>
				<th width="120">PROJECT MANAGER</th>
				<th width="120">CASHIER AREA</th>
				
				<th width="100" align="right">OPERATIONAL</th>
                <th width="100" align="right">RIGGER</th>
				<th width="100" align="right">TOOLS</th>
                <th width="100" align="right">REIMBURE</th>
				<th width="100" align="right">RENTAL CAR</th>
                <th width="100" align="right">TIKET</th>
				<th width="100" align="right">SEWA TOOLS</th>
				<th width="100" align="right">PENGIRIMAN TOOLS</th>
				<th width="100" align="right">ENTERTAIN</th>
				<th width="100" align="right">HOTEL</th>
                <th width="100" align="right">ATK</th>
                <th width="100" align="right">SEMBAKO</th>
                <th width="100" align="right">OLAHRAGA</th>
				<th width="100" align="right">DRIVER</th>
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
		$tgl=$f->convert_date($tgl,1);
		$operationalRp=number_format($operational,0,".",",");
		$riggerRp=number_format($rigger,0,".",",");
		$toolsRp=number_format($tools,0,".",",");
		$reimburseRp=number_format($reimburse,0,".",",");
		$rental_mobilRp=number_format($rental_mobil,0,".",",");
		$tiketRp=number_format($tiket,0,".",",");
		$sewa_toolsRp=number_format($sewa_tools,0,".",",");
		$pengiriman_toolsRp=number_format($pengiriman_tools,0,".",",");
		$entertainRp=number_format($entertain,0,".",",");
		$hotelRp=number_format($hotel,0,".",",");
		$atkRp=number_format($atk,0,".",",");
		$sembakoRp=number_format($sembako,0,".",",");
		$olahragaRp=number_format($olahraga,0,".",",");
		$driverRp=number_format($driver,0,".",",");
		
		$totalx=$operational+$rigger+$tools+$reimburse+$rental_mobil+$tiket+$sewa_tools+$pengiriman_tools+$entertain+$hotel+$atk+$sembako+$olahraga+$driver;
		$totalxRp=number_format($totalx,0,".",",");
?>
			<style> .f_text{ mso-number-format:\@; } </style>
			<tr>
				<td width="100" align="center"><?=$no;?></td>
				
				<td width="100" align="center"><?=$tgl;?></td>
				<td width="200" align="left"><?=$pemohon;?></td>
				<td width="150" align="left"><?=$proj_code;?></td>
				<td width="300" align="left"><?=$proj_name;?></td>
				<td width="200" align="left"><?=$pm_name;?></td>
				<td width="200" align="left"><?=$lokasi_kasir;?></td>
				
				<td width="100" align="right"><?=$operationalRp;?></td>
                <td width="100" align="right"><?=$riggerRp;?></td>
				<td width="100" align="right"><?=$toolsRp;?></td>
                <td width="100" align="right"><?=$reimburseRp;?></td>
				<td width="100" align="right"><?=$rental_mobilRp;?></td>
                <td width="100" align="right"><?=$tiketRp;?></td>
				<td width="100" align="right"><?=$sewa_toolsRp;?></td>
				<td width="100" align="right"><?=$pengiriman_toolsRp;?></td>
				<td width="100" align="right"><?=$entertainRp;?></td>
                <td width="100" align="right"><?=$hotelRp;?></td>
                <td width="100" align="right"><?=$atkRp;?></td>
                <td width="100" align="right"><?=$sembakoRp;?></td>
				<td width="100" align="right"><?=$olahragaRp;?></td>
				<td width="100" align="right"><?=$driverRp;?></td>
				<td width="100" align="right"><?=$totalxRp;?></td>
			</tr>
<?
		//total
		$t_operational=$t_operational+$operational;
		$t_rigger=$t_rigger+$rigger;
		$t_tools=$t_tools+$tools;
		$t_reimburse=$t_reimburse+$reimburse;
		$t_rental_mobil=$t_rental_mobil+$rental_mobil;
		$t_tiket=$t_tiket+$tiket;
		$t_sewa_tools=$t_sewa_tools+$sewa_tools;
		$t_pengiriman_tools=$t_pengiriman_tools+$pengiriman_tools;
		$t_entertain=$t_entertain+$entertain;
		$t_hotel=$t_hotel+$hotel;
		$t_atk=$t_atk+$atk;
		$t_sembako=$t_sembako+$sembako;
		$t_olahraga=$t_olahraga+$olahraga;
		$t_driver=$t_driver+$driver;
		$t_totalx=$t_totalx+$totalx;
}
		$t_operationalRp=number_format($t_operational,0,".",",");
		$t_riggerRp=number_format($t_rigger,0,".",",");
		$t_toolsRP=number_format($t_tools,0,".",",");
		$t_reimburseRp=number_format($t_reimburse,0,".",",");
		$t_rental_mobilRP=number_format($t_rental_mobil,0,".",",");
		$t_tiketRP=number_format($t_tiket,0,".",",");
		$t_sewa_toolsRp=number_format($t_sewa_tools,0,".",",");
		$t_pengiriman_toolsRp=number_format($t_pengiriman_tools,0,".",",");
		$t_entertainRP=number_format($t_entertain,0,".",",");
		$t_hotelRP=number_format($t_hotel,0,".",",");
		$t_atkRP=number_format($t_atk,0,".",",");
		$t_sembakoRP=number_format($t_sembako,0,".",",");
		$t_olahragaRp=number_format($t_olahraga,0,".",",");
		$t_driverRP=number_format($t_driver,0,".",",");
		$t_totalxRP=number_format($t_totalx,0,".",",");
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
				<td width="100" align="right"><b><?=$t_rental_mobilRP;?></b></td>
                <td width="100" align="right"><b><?=$t_tiketRP;?></b></td>
				<td width="100" align="right"><b><?=$t_sewa_toolsRp;?></b></td>
				<td width="100" align="right"><b><?=$t_pengiriman_toolsRp;?></b></td>
				<td width="100" align="right"><b><?=$t_entertainRP;?></b></th>
                <td width="100" align="right"><b><?=$t_hotelRP;?></b></td>
                <td width="100" align="right"><b><?=$t_atkRP;?></b></td>
                <td width="100" align="right"><b><?=$t_sembakoRP;?></b></td>
				<td width="100" align="right"><b><?=$t_olahragaRp;?></b></td>
				<td width="100" align="right"><b><?=$t_driverRP;?></b></td>
				<td width="100" align="right"><b><?=$t_totalxRP;?></b></td>
			</tr>
		<tbody>
</table>