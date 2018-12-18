<?php
ob_start();
session_start();
include("$DOCUMENT_ROOT/s/config.php");
$nip_sipeg=$_SESSION['sipeg_nip_pegawai'];	
$login_nip=(empty($nip_sipeg))? $login_nip:$nip_sipeg;
if($act=='view'){
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
	$sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'OCS_ID';
	$order = isset($_POST['order']) ? strval($_POST['order']) : 'asc';
	
	$offset = ($page-1)*$rows;
	$f_start_date	= $_REQUEST['f_start_date'];
	$f_start_date	= (empty($f_start_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_start_date,1,'/'));
	$f_end_date		=	$_REQUEST['f_end_date'];
	$f_end_date		= (empty($f_end_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_end_date,1,'/'));
	
	$q = $_POST['value'];
	$fname=isset($_POST['name']) ? strval($_POST['name']) : 'OCS_Desc';
	$find = ($fname=="all")?"and (ocs_id like '%$q%' or status_app like '%$q%' or pm_name like '%$q%' or time like '%$q%' 
	or dt_name like '%$q%' or dtc_name like '%$q%' or area like '%$q%')"
	:"and $fname like '%$q%'";
	$sub_query="select a.ocs_id,convert(varchar(14),a.[date],113) as req_date,a.[time],b.lokasi_kerja as area, c.proj_name, d.nm_peg as pm_name,e.nm_peg as dt_name,
	f.nm_peg as dtc_name,a.ocs_desc, convert(varchar(20),a.pm_approve_date,113) as app_pm, convert(varchar(20),a.finance_approve_date,113) as bc_app
	from m_carpool a 
	left join m_project_area b on b.lke_id=a.lke_id
	left join m_project c on c.id=a.proj_id
	left join spg_data_current d on d.nip=c.pm_id
	left join spg_data_current e on e.nip=a.nip_dt
	left join spg_data_current f on f.nip=a.dt_coord";
	$result = array();
	$result["total"] = $dbproj->getOne("select count(*) from (
		$sub_query	
		) as x
		--where --date between '$f_start_date' and '$f_end_date' and 
		--nip_app='$login_nip' and status_app='OPEN' $find");
		
	$sql="select * from ($sub_query) as x
		--where req_date='$f_start_date'
		--date between '$f_start_date' and '$f_end_date' and 
		--nip_app='$login_nip' and status_app='OPEN' $find 
		order by ocs_id desc, area desc
	";
	$result_user=$dbproj->SelectLimit($sql,$rows,$offset);
	$items=array();
	while($row=$result_user->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$paid_date=$dbproj->getOne("select convert(varchar(20),paid_date,113) from t_carpool where ocs_id='$ocs_id'");
		$items[]=array("id"=>"$id","ocs_id"=>"$ocs_id","ocs_desc"=>"$ocs_desc","status_app"=>"$status_app","proj_name"=>"$proj_name","date"=>"$req_date",
		"km_acuan"=>"$km_acuan","uang_pulsa"=>"$uang_pulsa","pm_name"=>"$pm_name","dt_name"=>"$dt_name","dtc_name"=>"$dtc_name","time"=>"$time",
		"pm_app"=>"$app_pm","area"=>"$area","bc_app"=>"$bc_app","paid_date"=>"$paid_date"
		);
	}
	$result["rows"] = $items;
	echo json_encode($result);
}
?>