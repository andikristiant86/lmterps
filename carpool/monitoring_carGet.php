<?php
include_once($DOCUMENT_ROOT."/s/config.php");
if($act=='view'){
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 20;
	$offset = ($page-1)*$rows;
	
	$sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'kode_kendaraan';
	$order = isset($_POST['order']) ? strval($_POST['order']) : 'asc';
	
	$q = $_POST['value'];
	$fname=isset($_POST['name']) ? strval($_POST['name']) : 'all';
	
	$find = ($fname=="all")?
	"where kode_kendaraan like '%$q%' or no_polisi like '%$q%' or status_out like '%$q%'":
	"where $fname like '%$q%'";
	
	$total_car=$db->getOne("select count(*) from inv_data_kendaraan");
	
	$sqlx="select * from (
	select a.kode_kendaraan,a.no_polisi, (
			select case when count(*)=0 then 'IN' else 'OUT' end as status_out from t_carpool b
			left join m_carpool c on b.ocs_id=c.ocs_id 
			where a.kode_kendaraan=b.car_number and c.status in ('1','4')
			) as status_out from inv_data_kendaraan a
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
	select a.kode_kendaraan,a.no_polisi, (
			select case when count(*)=0 then 'IN' else 'OUT' end as status_out from t_carpool b
			left join m_carpool c on b.ocs_id=c.ocs_id 
			where a.kode_kendaraan=b.car_number and c.status in ('1','4')
			) as status_out from inv_data_kendaraan a
	) as x	$find ");

	$sql="select * from (
	select a.kode_kendaraan,a.no_polisi, (
			select case when count(*)=0 then 'IN' else 'OUT' end as status_out from t_carpool b
			left join m_carpool c on b.ocs_id=c.ocs_id 
			where a.kode_kendaraan=b.car_number and c.status in ('1','4')
			) as status_out,
			(
			select NM_PEG from SPG_DATA_CURRENT b
			left join t_carpool c on b.NIP=c.NIP_DRIVER
			where a.kode_kendaraan=c.car_number
			) as name_driver,
			(select (select x.proj_name from m_project x where x.id=d.proj_id) from m_carpool d left join t_carpool c on d.ocs_id=c.ocs_id
			where a.kode_kendaraan=c.car_number and c.date_pulang=('1900-01-01 00:00:00.000')) as proj_nm
			from inv_data_kendaraan a 
	) as x $find order by $sort $order
	";

	$result_user=$dbproj->SelectLimit($sql,$rows,$offset);
	$items=array();
	while($row=$result_user->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$items[]=array("kdr_id"=>"$kode_kendaraan","car_number"=>"$no_polisi","status"=>"$status_out","name_driver"=>"$name_driver","proj_nm"=>"$proj_nm");
	}
	$jum_in=(empty($jum_in))?0:$jum_in;
	$jum_out=(empty($jum_out))?0:$jum_out;
	$result["footer"]=array(
							array("kdr_id"=>"","car_number"=>"<b>IN</b>","status"=>"<b><span style='color:green'>$jum_in</span></b>"),
							array("kdr_id"=>"","car_number"=>"<b>OUT</b>","status"=>"<b><span style='color:red'>$jum_out</span></b>"),
							array("kdr_id"=>"","car_number"=>"<b>TOTAL CAR</b>","status"=>"<b><span style='color:black'>$total_car</span></b>")
	);
	$result["rows"] = $items;
	echo json_encode($result);
}
?>