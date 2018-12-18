<?
include("../s/config.php");
$CreateBy="$login_nip";
$CreateDate=date("Y/m/d");
$today=date("Y-m-d");
if($act=='view'){
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
	$sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'nip';
	$order = isset($_POST['order']) ? strval($_POST['order']) : 'asc';

	$offset = ($page-1)*$rows;
	
	$f_start_date	=	$_REQUEST['f_start_date'];
	$f_start_date	= (empty($f_start_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_start_date,1,'/'));
	
	$q = $_POST['value'];
	$fname=isset($_POST['name']) ? strval($_POST['name']) : 'nm_peg';
	$find = ($fname=="all")?"where nm_peg like '%$q%' or nip like '%$q%' or status_out like '%$q%' or status_kehadiran like '%$q%'":
	"where $fname like '%$q%'";
	
	$total_driver=$db->getOne("select count(*) from spg_data_current a where a.kd_jabatan_str+a.kd_unit_org='8011040102010000000' and sts_pensiun=0");
	
	$sqlx="select * from (
	select a.nm_peg,a.nip,
			(
			select case when count(*)=0 then 'IN' else 'OUT' end as status_out from t_carpool b
			left join m_carpool c on b.ocs_id=c.ocs_id 
			where a.nip=b.nip_driver and c.status in ('1','4') and convert(varchar(10),b.date_berangkat,111)='$CreateDate'
			) as status_out,
			(select case when count(*)=0 then 'ALFA' else 'HADIR' end as status_kehadiran from lmt_hcis.dbo.spg_absensi where nip=a.nip and tanggal_absen='$f_start_date') 
			as status_kehadiran
	from spg_data_current a
	where a.kd_jabatan_str+a.kd_unit_org='8011040102010000000' and sts_pensiun=0
	) as x 		
	";

	$resultx=$dbproj->Execute($sqlx);
	$items=array();
	while($row=$resultx->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		if($status_out=='OUT')$jum_out += 1;
		else $jum_in += 1;
	}
	
	$result = array();
	$result["total"] = $dbproj->getOne("select count(*) from (
	select a.nm_peg,a.nip,
			(
			select case when count(*)=0 then 'IN' else 'OUT' end as status_out from t_carpool b
			left join m_carpool c on b.ocs_id=c.ocs_id 
			where a.nip=b.nip_driver and c.status in ('1','4') and convert(varchar(10),b.date_berangkat,111)='$CreateDate'
			) as status_out,
			(select case when count(*)=0 then 'ALFA' else 'HADIR' end as status_kehadiran from lmt_hcis.dbo.spg_absensi where nip=a.nip and tanggal_absen='$f_start_date') 
			as status_kehadiran
	from spg_data_current a
	where a.kd_jabatan_str+a.kd_unit_org='8011040102010000000' and sts_pensiun=0
	) as x $find");
	
	$sql="
	select * from (
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
	) as x $find order by $sort $order";
	
	$resultx=$dbproj->SelectLimit($sql,$rows,$offset);
	$items=array();
	while($row=$resultx->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$absent_in=str_replace("Z","",str_replace("T"," ",$absent_in));
		$absent_out=str_replace("Z","",str_replace("T"," ",$absent_out));
		$items[]=array("nm_peg"=>"$nm_peg","nip"=>"$nip","status"=>"$status_out","status_kehadiran"=>"$status_kehadiran","absent_in"=>"$absent_in",
		"absent_out"=>"$absent_out");
	}
	$jum_in=(empty($jum_in))?0:$jum_in;
	$jum_out=(empty($jum_out))?0:$jum_out;
	$result["footer"]=array(
							array("nm_peg"=>"<b>IN</b>","status"=>"<b><span style='color:green'>$jum_in</span></b>"),
							array("nm_peg"=>"<b>OUT</b>","status"=>"<b><span style='color:red'>$jum_out</span></b>"),
							array("nm_peg"=>"<b>TOTAL DRIVER</b>","status"=>"<b><span style='color:black'>$total_driver</span></b>")
	);
	$result["rows"] = $items;
	echo json_encode($result);
}
?>

