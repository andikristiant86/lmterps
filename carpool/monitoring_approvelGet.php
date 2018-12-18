<?php
ob_start();
session_start();
include("$DOCUMENT_ROOT/s/config.php");
$nip_sipeg=$_SESSION['sipeg_nip_pegawai'];
$login_nip=(empty($nip_sipeg))? $login_nip:$nip_sipeg;
$login_nip_c=($login_nip=='031629')?"021918":"$login_nip";
if($act=='view'){
	
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
	$sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'OCS_ID';
	$order = isset($_POST['order']) ? strval($_POST['order']) : 'asc';
	if($sort=='OCS_ID'){
		$sort='OCS_ID';
	}elseif($sort=='OCS_Desc'){
		$sort='OCS_Desc';
	}
	
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
	$sub_query="
			select a.*,b.ocs_desc,b.date,b.km_acuan,b.uang_pulsa,d.nm_peg as pm_name, b.time,b.pm_approve_date,
			(select c.proj_name from m_project c where c.id=b.proj_id) as proj_name,
			(select nm_peg from spg_data_current where nip=b.nip_dt) as dt_name,
			(select nm_peg from spg_data_current where nip=b.dt_coord) as dtc_name,
			(select lokasi_kerja from m_project_area where lke_id=b.lke_id) as area
			from t_approvel_carpool a 
			left join m_carpool b on a.ocs_id=b.ocs_id 
			left join m_project c on b.proj_id=c.id
			left join m_project_manager d on c.pm_id=d.nip
		";
	$result = array();
	$result["total"] = $dbproj->getOne("select count(*) from ($sub_query) as x
		where --date between '$f_start_date' and '$f_end_date' and 
		nip_app='$login_nip_c' and status_app='OPEN' --$find");
		
	$sql="select * from ($sub_query) as x
		where --date between '$f_start_date' and '$f_end_date' and 
		nip_app='$login_nip_c' and status_app='OPEN' --$find 
		order by status_app desc, ocs_id desc
	";
	$result_user=$dbproj->SelectLimit($sql,$rows,$offset);
	$items=array();
	while($row=$result_user->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$date=$f->convert_date($date,1);
		$uang_pulsa=number_format($uang_pulsa,0,".",",");
		$km_acuan=number_format($km_acuan,0,".",",");
		$pm_approve_date=$f->convert_date(substr($pm_approve_date,0,10),1)." ".substr($pm_approve_date,11,8);
		$items[]=array("id"=>"$id","ocs_id"=>"$ocs_id","ocs_desc"=>"$ocs_desc","status_app"=>"$status_app","proj_name"=>"$proj_name","date"=>"$date",
		"km_acuan"=>"$km_acuan","uang_pulsa"=>"$uang_pulsa","pm_name"=>"$pm_name","dt_name"=>"$dt_name","dtc_name"=>"$dtc_name","time"=>"$time",
		"pm_approve_date"=>"$pm_approve_date","area"=>"$area"
		);
	}
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='do_approval'){
	$date=date("Y-m-d");
	$rows=explode(",",$ocs_id);
	foreach ($rows as $key=>$val){
		$arr=explode("|",$val);
		$id=$arr[0];
		$ocs_id=$arr[1];
		$app_id=$dbproj->getOne("select app_id from t_approvel_carpool where id='$id'");
		$nip_app_next=$dbproj->getOne("select app_nip from m_approvel where app_no_urut='2' and app_jenis='REQ_CARPOOL'");
		$app_id_next=$dbproj->getOne("select app_id from m_approvel where app_no_urut='2' and app_jenis='REQ_CARPOOL'");
		if($app_id=='1'){
			$result=$dbproj->Execute("update t_approvel_carpool set status_app='WAITING...' where id='$id'");
			$dbproj->Execute("update m_carpool set pm_approve='1',pm_approve_date=GETDATE() where ocs_id='$ocs_id'");
			$dbproj->Execute("insert into t_approvel_carpool (nip_app,ocs_id,status_app,app_id) (select '$nip_app_next',ocs_id,'OPEN','$app_id_next' from t_approvel_carpool where id='$id')");
		}else{
			$result=$dbproj->Execute("update t_approvel_carpool set status_app='CLOSED' where ocs_id='$ocs_id'");
			$dbproj->Execute("update m_carpool set finance_approve='1',finance_approve_date=GETDATE() where ocs_id='$ocs_id'");
		}
	}
	echo json_encode(array('success'=>true));	
}elseif($act=='do_notapproval'){
	$rows=explode(",",$ocs_id);
	foreach ($rows as $key=>$val){
		$arr=explode("|",$val);
		$id=$arr[0];
		$ocs_id=$arr[1];
		$app_id=$dbproj->getOne("select app_id from t_approvel_carpool where id='$id'");
		$nip_app_next=$dbproj->getOne("select app_nip from m_approvel where app_no_urut='2' and app_jenis='REQ_CARPOOL'");
		$app_id_next=$dbproj->getOne("select app_id from m_approvel where app_no_urut='2' and app_jenis='REQ_CARPOOL'");
		if($app_id=='1'){
			$result=$dbproj->Execute("update t_approvel_carpool set status_app='NOT APPROVED' where id='$id'");
			$dbproj->Execute("update m_carpool set pm_approve='0' where ocs_id='$ocs_id'");
		}else{
			$result=$dbproj->Execute("update t_approvel_carpool set status_app='NOT APPROVED' where ocs_id='$ocs_id'");
			$dbproj->Execute("update m_carpool set finance_approve='0' where ocs_id='$ocs_id'");
		}
	}
	echo json_encode(array('success'=>true));	
}
?>