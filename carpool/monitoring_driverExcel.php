<?php 
$date=date("Ymd");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=monitoring_driver_$date.xls");
header("Pragma: no-cache");
header("Expires: 0");

include("$DOCUMENT_ROOT/s/config.php");
$f_start_date	=	$_REQUEST['f_start_date'];
$f_start_date	= 	(empty($f_start_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_start_date,1,'/'));
	
$sql="select * from (
	select a.nm_peg,a.nip,
			(
			select case when count(*)=0 then 'BLM JALAN' else 'JALAN' end as status_out from t_carpool b
			left join m_carpool c on b.ocs_id=c.ocs_id 
			where a.nip=b.nip_driver and c.status in ('1','4') and convert(varchar(10),b.date_berangkat,111)='$CreateDate'
			) as status_out,
	(select case when count(*)=0 then 'ALFA' else 'HADIR' end as status_kehadiran from lmt_hcis.dbo.spg_absensi where nip=a.nip and tanggal_absen='$f_start_date') as status_kehadiran,
	(select masuk from lmt_hcis.dbo.spg_absensi where nip=a.nip and tanggal_absen='$f_start_date') as absent_in,
	(select keluar from lmt_hcis.dbo.spg_absensi where nip=a.nip and tanggal_absen='$f_start_date') as absent_out
	from spg_data_current a
	where a.kd_jabatan_str+a.kd_unit_org='8011040102010000000' and sts_pensiun=0
	) as x $find";
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
				<th width="100">NIP</th>
				<th width="250">NAME</th>
				<th width="150">STATUS ABSENST</th>
				<th width="200">ABSENT IN</th>
				<th width="200">ABSENT OUT</th>
				<th width="150">STATUS CAR</th>
			</tr>
        </thead>
<tbody>
<?php 
while($row=$sqlExc->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$absent_in=str_replace("Z","",str_replace("T"," ",$absent_in));
		$absent_out=str_replace("Z","",str_replace("T"," ",$absent_out));
?>
			<style> .f_text{ mso-number-format:\@; } </style>
			<tr>
				<td><?=$nip;?></td>
				<td><?=$nm_peg;?></td>
				<td><?=$status_kehadiran;?></td>
				<td><?=$absent_in;?></td>
				<td><?=$absent_out;?></td>
				<td><?=$status_out;?></td>
			</tr>
<?
}
?>
		<tbody>
</table>