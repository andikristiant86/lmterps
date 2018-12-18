<?php 
$date=date("Ym");
$time=time();
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=MONITORING_CARPOOL_$time.xls");
header("Pragma: no-cache");
header("Expires: 0");

include("$DOCUMENT_ROOT/s/config.php");
$f_start_date	=	$_REQUEST['start_date'];
$f_start_date	= (empty($f_start_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_start_date,1,'/'));
$f_end_date		=	$_REQUEST['end_date'];
$f_end_date	= (empty($f_end_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_end_date,1,'/'));

$is_pm=$wia_accessname=="DefaultPM"?$dbproj->getOne("select count(*) from M_PROJECT_MANAGER where nip='$login_nip'"):0;

$sql="select 
	a.*,(select b.nm_peg from spg_data_current b where b.nip=a.dt_coord) as dtc_name,
	(select b.nm_peg from spg_data_current b where b.nip=a.nip_dt) as dt_name,
	(select b.nm_peg from spg_data_current b where b.nip=a.nip_rno) as rno_name,
	mp.proj_name as proj_name ,
	(select sum(isnull(b.bbm,0)+isnull(b.etoll,0)+isnull(b.mtoll,0)+isnull(b.others,0)+isnull(b.parking,0)+isnull(b.portal,0)+isnull(b.three_in_one,0)+
	isnull(b.uj,0)+isnull(b.um,0)+isnull(b.utb,0)) from t_carpool b where b.ocs_id=a.ocs_id) as total,
	case isnull(a.status,'') when '1' then 'JALAN' when '5' then 'JALAN*' when '2' then 'PULANG' when '3' then 'CLOSED' when '111' then 'REJECT' when 112 then 'CANCEL' else 'BELUM JALAN' end as sts_nm,
	(select lokasi_kerja from m_project_area where lke_id=a.lke_id) as area
	from m_carpool a
	left join m_project mp on mp.id=a.proj_id ".($is_pm>0?"and mp.lke_id=a.lke_id where mp.pm_id='$login_nip'":
	"where a.lke_id in (select area from m_carpool_admin where admin='$login_nip')");
	
$_sql="select * from ($sql) as x where [date] between '$f_start_date' and '$f_end_date' and pm_approve='1' and finance_approve='1' order by area asc";
	
$sqlExc=$dbproj->Execute($_sql);
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
				<th width="100">TIME</th>
				<th width="120">PROJECT CODE</th>
				<th width="200">PROJECT NAME</th>
				<th width="200">SITE ID</th>
				<th width="200">SITE NAME</th>
				<th width="120">PROJECT MANAGER</th>
				<th width="120">COORDINATOR</th>
				<th width="120">DT/SURVEYOR/RIGGER</th>
				<th width="120">RNO/PLO</th>
				<th width="120">DRIVER</th>
				<th width="120">NO_CAR</th>
				<th width="150">AREA</th>
				<th width="250">DESCRIPTION</th>
				<th width="120">STATUS</th>
				<th width="120">RESULT DT/SURVEYOR/RIGGER</th>
				<th width="120">RESULT RNO</th>
				<th width="200">REMARK</th>
			</tr>
        </thead>
<tbody>
<?php 
while($row=$sqlExc->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$proj_code=$dbproj->getOne("select proj_code from m_project where id='$proj_id'");
		$site_id=$dbproj->getOne("select site_id from m_milestone where proj_id='$proj_id' and site_name='$site_name'");
		//$proj_name=$dbproj->getOne("select proj_name from m_project where id='$proj_id'");
		$pm_name=$dbproj->getOne("select (select nm_peg from spg_data_current where nip=m_project.pm_id) from m_project where id='$proj_id'");
		$driver=$dbproj->getOne("select top 1 (select nm_peg from spg_data_current where nip=t_carpool.nip_driver) from t_carpool where ocs_id='$ocs_id'");
		$no_polisi=$dbproj->getOne("select top 1 (select no_polisi from inv_data_kendaraan where kode_kendaraan=t_carpool.car_number) from t_carpool where ocs_id='$ocs_id'");
		$remark=(empty($alasan_cancel))?$remark:$alasan_cancel;
?>
			<style> .f_text{ mso-number-format:\@; } </style>
			<tr>
				<td width="100" align="center"><?=$ocs_id?></td>
				<td width="100" align="center"><?=$f->convert_date($date,1);?></td>
				<td width="100" align="center"><?=$time;?></td>
				<td width="150" align="left"><?=$proj_code;?></td>
				<td width="300" align="left"><?=$proj_name;?></td>
				<td width="100" align="center"><?=$site_id?></td>
				<td width="300" align="left"><?=$site_name;?></td>
				<td width="200" align="left"><?=$pm_name;?></td>
				<td width="200" align="left"><?=$dtc_name;?></td>
				<td width="200" align="left"><?=$dt_name;?></td>
				<td width="200" align="left"><?=$rno_name;?></td>
				<td width="200" align="left"><?=$driver;?></td>
				<td width="200" align="left"><?=$no_polisi;?></td>
				<td width="150" align="left"><?=$area;?></td>
				<td width="250" align="left"><?=$ocs_desc;?></td>
				<td width="120" align="center"><?=$sts_nm;?></td>
				<td width="120" align="center"><?=$result_dt;?></td>
				<td width="120" align="center"><?=$result_rno;?></td>
				<td width="200" align="left"><?=$remark;?></td>
			</tr>
<?	
}
?>
			
		<tbody>
</table>