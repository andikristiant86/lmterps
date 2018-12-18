<?php
session_start();
include("../s/config.php");
$nip_sipeg=$_SESSION['sipeg_nip_pegawai'];
$CreateBy=(empty($nip_sipeg))? $login_nip:$nip_sipeg;
$pm_nip=$dbproj->getOne("select top 1 
(SELECT M_PROJECT.PM_ID FROM M_PROJECT WHERE M_PROJECT.id=T_RESOURCE_ASSIGN.PROJ_ID) from T_RESOURCE_ASSIGN where nip='$CreateBy' and FLAG_ASSIGN=1");

$kd_pm=$db->getOne("select kd_pm from(
						select nip,kd_pm from spg_data_current where kd_unit_org='1030102000000000'
						union
						select nip,kd_pm from spg_data_current2 where kd_unit_org='1030102000000000') as x
where kd_pm=(select kd_pm  from spg_data_current where nip='$CreateBy')");
$CreateDate=date("Y/m/d");
if($act=='view'){
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
	$offset = ($page-1)*$rows;
	
	$q = $_POST['value'];
	$fname=isset($_POST['name']) ? strval($_POST['name']) : 'ocs_desc';
	$find = ($fname=="all")?"where dt_coord='$CreateBy' and (ocs_id like '%$q%' or ocs_desc like '%$q%' or status_name like '%$q%')":
	"where  dt_coord='$CreateBy' and $fname like '%$q%'";
	
	$result = array();
	$result["total"] = $dbproj->getOne("select count(*) from (select*,
	case sts_app_pm 
	  when 0 then 'NOT APPROVED' 
	  when 1 then 'APPROVED' 
	  when 2 then 'REJECT' 
	  else 'NOT APPROVED' end as status_app_pm,
	case sts_app_finance
		when 0 then 'NOT APPROVED' 
		when 1 then 'APPROVED' 
		when 2 then 'REJECT' 
		else 'NOT APPROVED' end as status_app_finance
	from m_request_pulsa) as x where ocs_id='$ocs_id'");
	
	$sql="select * from (select*,
	case sts_app_pm 
	  when 0 then 'NOT APPROVED' 
	  when 1 then 'APPROVED' 
	  when 2 then 'REJECT' 
	  else 'NOT APPROVED' end as status_app_pm,
	case sts_app_finance
		when 0 then 'NOT APPROVED' 
		when 1 then 'APPROVED' 
		when 2 then 'REJECT' 
		else 'NOT APPROVED' end as status_app_finance
	from m_request_pulsa) as x where ocs_id='$ocs_id'" ;
	
	$result_user=$dbproj->SelectLimit($sql,$rows,$offset);
	$items=array();
	while($row=$result_user->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$req_date=$f->convert_date($req_date,1);
		$req_name=$db->getOne("select nm_peg from spg_data_current where nip='$req_nip'");
		
		$amount=number_format($amount,0,".",",");
		$items[]=array("id"=>"$id","ocs_id"=>"$ocs_id","req_date"=>"$req_date","req_name"=>"$req_name","phone_number"=>"$phone_number","amount"=>"$amount",
		"sts_app_pm"=>"$status_app_pm","sts_app_finance"=>"$status_app_finance","status_topup"=>"$status_topup");
	}
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='generate_kode'){
	$ocs_id=$f->generate_nomorkolom("lmt_project.dbo.M_Carpool","OCS_ID","CRP");
	$user_input=$db->getOne("select nm_peg from spg_pegawai where nip='$login_nip'");
	$tanggal_input=date("d/m/Y");
	echo json_encode(array('kode'=>"$ocs_id|$user_input|$tanggal_input"));
}
elseif($act=='do_add'){
	$request_date=str_replace("/","-",$f->convert_date($request_date,1,'/'));
	$lke_id=$dbproj->getOne("select lke_id from m_project where id='$proj_id'");
	$rpm_id=$dbproj->getOne("select rpm_id from m_project where id='$proj_id'");
	$pm_nip=empty($rpm_id)?$pm_nip:$rpm_id;
	$result=$dbproj->Execute("insert into m_request_pulsa (ocs_id,req_nip,req_date,proj_id,amount,nip_app_pm,sts_app_pm,description,sts_app_finance,
	phone_number,nip_approval,status_approval,status_topup,create_by,create_date,lke_id) 
	values ('$ocs_id','$req_nip','$request_date','$proj_id','$amount','$pm_nip','0','$description','0','$phone_number','$pm_nip','OPEN','NOTRECIEVED',
	'$CreateBy',GETDATE(),'$lke_id'
	)");
		if ($result){
			echo json_encode(array('success'=>true));
		} else {
			echo json_encode(array('errorMsg'=>"Sql error!"));
		}
}
elseif($act=='do_update'){
	foreach($HTTP_POST_VARS as $key=>$val){
		if(!preg_match("#^(ocs_id|name_dt|name_rno)#",$key)){
			if(preg_match("#^(date)#",$key)){
				$date=explode("/",$val);
				$hr=$date[0];
				$bl=$date[1];
				$th=$date[2];
				$date_en="$th/$bl/$hr";
				$date_en=($date_en=="//")?"":"$date_en";
				$list .="$key='$date_en',";
			}else{
				$list .="$key='$val',";
			}
		}
	}
	$columns = preg_replace("/,$/","",$columns);
	$values	 = preg_replace("/,$/","",$values);
	$list	 = preg_replace("/,$/","",$list);
	$result=$dbproj->Execute("update m_carpool set $list where ocs_id='$ocs_id'");
	if($status=='PULANG')$dbproj->Execute("update m_carpool set status='3' where ocs_id='$ocs_id'");
		if ($result){
			echo json_encode(array('success'=>true));
		} else {
			echo json_encode(array('errorMsg'=>"Error: update m_carpool set $list where ocs_id='$ocs_id'"));
		}
}
elseif($act=='do_destroy'){
	$check_status=$dbproj->getOne("select status_topup from m_request_pulsa where id='$id'");
	if($check_status=='RECEIVED'){
		echo json_encode(array('errorMsg'=>"Can not be removed, status <b>RECEIVED</b>!"));
		die();
	}
	$result=$dbproj->Execute("delete m_request_pulsa where id='$id'");
		if ($result){
			echo json_encode(array('success'=>true));
		} else {
			echo json_encode(array('errorMsg'=>"Error: delete m_client where clientid='$id'"));
		}
}elseif($act=='combo_dt'){
	$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	$sql="	select top 50 x.nip,y.nm_peg from T_RESOURCE_ASSIGN x
			LEFT JOIN SPG_DATA_CURRENT y on y.nip=x.nip
			where x.role_id in ('6021030102010700000','8011030102010700000','8011030102010600000','8011030102010102000',
		'6021030102010100000','8011030102010101000','6021030102010700000','6021030102010700000','6021030102010600000') and x.FLAG_ASSIGN=1
		and (SELECT M_PROJECT.PM_ID FROM M_PROJECT WHERE M_PROJECT.id=x.PROJ_ID)='$pm_nip'
		and (x.nip like '%$q%' or y.nm_peg like '%$q%')";
	$result_user=$db->Execute($sql);
	$items=array();
	while($row=$result_user->Fetchrow()){
		$items[]=$row;
	}
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='combo_project'){
	$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	$sql="select top 50 x.id, x.proj_code, x.proj_name, x.sow_id, (select y.sow_name from m_sow y where y.sow_id=x.sow_id) as sow_name from m_project x 
	where x.pm_id='$pm_nip' and (x.proj_code like '%$q%' or x.proj_name like '%$q%')";
	$result_user=$dbproj->Execute($sql);
	$items=array();
	while($row=$result_user->Fetchrow()){
		$items[]=$row;
	}
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='combo_sow'){
	$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	$sql="
	select top 50 * from (
	select a.proj_id, a.sow_id, (select b.sow_name from m_sow b where b.sow_id=a.sow_id) as sow_name from m_project_sow a
	) as x
	where proj_id in ('$proj_id') and (sow_id like '%$q%' or sow_name like '%$q%')";
	$result_user=$dbproj->Execute($sql);
	$items=array();
	while($row=$result_user->Fetchrow()){
		$items[]=$row;
	}
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='combo_site'){
	$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	$sql="
	select top 50 * from (
	select a.proj_id, a.sow_id, a.site_name from t_milestone a
	) as x
	where (site_name like '%$q%')";
	$result_user=$dbproj->Execute($sql);
	$items=array();
	while($row=$result_user->Fetchrow()){
		$items[]=$row;
	}
	$result["rows"] = $items;
	echo json_encode($result);
}
?>