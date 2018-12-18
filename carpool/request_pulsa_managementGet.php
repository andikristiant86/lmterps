<?php
session_start();
include("../s/config.php");
$nip_sipeg=$_SESSION['sipeg_nip_pegawai'];
$login_nip=(empty($nip_sipeg))? $login_nip:$nip_sipeg;
$pm_nip=$dbproj->getOne("select top 1 
(SELECT M_PROJECT.PM_ID FROM M_PROJECT WHERE M_PROJECT.id=T_RESOURCE_ASSIGN.PROJ_ID) from T_RESOURCE_ASSIGN where nip='$login_nip' and FLAG_ASSIGN=1");

$kd_pm=$db->getOne("select kd_pm from(
						select nip,kd_pm from spg_data_current where kd_unit_org='1030102000000000'
						union
						select nip,kd_pm from spg_data_current2 where kd_unit_org='1030102000000000') as x
where kd_pm=(select kd_pm  from spg_data_current where nip='$login_nip')");
$kd_pm=(empty($kd_pm))?'0001':$kd_pm;
$CreateDate=date("Y/m/d");

$area_pemohon=$dbproj->getOne("select top 1 
(select m_project.lke_id from m_project where m_project.id=t_resource_assign.proj_id) from 
t_resource_assign where nip='$login_nip' and flag_assign=1");

if($act=='view'){
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
	$offset = ($page-1)*$rows;
	
	$q = $_POST['value'];
	$fname=isset($_POST['name']) ? strval($_POST['name']) : 'ocs_desc';
	$find = ($fname=="all")?"":"";
	
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
	from m_request_pulsa) as x where create_by='$login_nip'");
	
	$sql="select * from (select *,
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
	from m_request_pulsa) as x where create_by='$login_nip' order by id desc" ;
	
	$result_user=$dbproj->SelectLimit($sql,$rows,$offset);
	$items=array();
	while($row=$result_user->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$req_date=$f->convert_date($req_date,1);
		$req_name=$db->getOne("select nm_peg from spg_data_current where nip='$req_nip'");
		$proj_name=$dbproj->getOne("select proj_name from m_project where id='$proj_id'");
		$area=$dbproj->getOne("select lokasi_kerja from m_project_area where lke_id='$lke_id'");
		$amount=number_format($amount,0,".",",");
		$type=$dbproj->getOne("select voucher_name from m_voucher_type where voucher_id='$voucher_id'");
		$nominal=$dbproj->getOne("select voucher_amount from m_voucher_nominal where id='$voucher_nominal'");
		$proj_name=empty($proj_name)?"OTHERS":"$proj_name";
		$items[]=array("id"=>"$id","ocs_id"=>"$ocs_id","req_date"=>"$req_date","req_name"=>"$req_name","phone_number"=>"$phone_number","amount"=>"$amount",
		"sts_app_pm"=>"$status_app_pm","sts_app_finance"=>"$status_app_finance","status_topup"=>"$status_topup","ocs_id"=>"$ocs_id",
		"proj_name"=>"$proj_name","lke_id"=>"$lke_id","area"=>"$area","voucher_paket"=>"$voucher_paket","voucher_type"=>"$type","voucher_nominal"=>"$nominal");
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
	$ocs_id=$f->generate_nomorkolom("lmt_project.dbo.M_REQUEST_PULSA","OCS_ID","PLS");
	$request_date=str_replace("/","-",$f->convert_date($request_date,1,'/'));
	$nip_approval=$pm_nip;
	
	$nominalX=$dbproj->getOne("select price from m_voucher_price where area_id='$lke_id' and nominal_id='$nominal'");
	$amount=empty($nominalX)?$dbproj->getOne("select price from m_voucher_nominal where id='$nominal'"):$nominalX;

	$dbproj->Execute("insert into m_request_pulsa (ocs_id,req_nip,req_date,proj_id,amount,nip_app_pm,sts_app_pm,description,sts_app_finance,
	phone_number,nip_approval,status_approval,status_topup,create_by,create_date,lke_id,voucher_paket,voucher_id,voucher_nominal) 
	values ('$ocs_id','$req_nip','$request_date','$proj_id','$amount','$nip_approval','0','$description','1','$phone_number','$nip_approval','OPEN','NOTRECIEVED',
	'$login_nip',GETDATE(),'$lke_id','$paket','$voucher_id','$nominal')");
		
	echo json_encode(array('success'=>true));
		
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
	$result=$dbproj->Execute("delete m_request_pulsa where id='$id'");
		if ($result){
			echo json_encode(array('success'=>true));
		} else {
			echo json_encode(array('errorMsg'=>"Error: delete m_client where clientid='$id'"));
		}
}elseif($act=='combo_dt'){
	$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	$sql="select top 50 a.nip, b.nm_peg
	from t_resource_assign a
	left join spg_data_current b on b.nip=a.nip
	where a.flag_assign='1' and (a.nip like '%$q%' or b.nm_peg like '%$q%')";
	$result_user=$dbproj->Execute($sql);
	$items=array();
	while($row=$result_user->Fetchrow()){
		$items[]=$row;
	}
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='combo_employee'){
	$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	$sql="select top 50 nip, nm_peg
	from spg_data_current
	where --kd_jabatan_str+kd_unit_org in ('9341030102010101000','8011030102010101000','8041030102010101000','8011030102010102000') and 
	isnull(sts_pensiun,1)=0 and (nip like '%$q%' or nm_peg like '%$q%')";
	$result_user=$dbproj->Execute($sql);
	$items=array();
	while($row=$result_user->Fetchrow()){
		$items[]=$row;
	}
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='combo_project'){
	$filt_pm=($openFilter=='Ya')?"":"pm_id='$pm_nip' and";
	$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	$sql="select id, proj_code, proj_name from m_project 
	where isnull(status,'')='' and (proj_code like '%$q%' or proj_name like '%$q%') order by id desc";
	$resultx=$dbproj->Execute($sql);
	$items=array();
	$items[]=array("id"=>"0","proj_code"=>"0","proj_name"=>"MANAGEMENT");
	while($row=$resultx->Fetchrow()){
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
}elseif($act=='combo_voucher'){
	$sql="select * from m_voucher_type where paket='$paket'";
	$result=$dbproj->Execute($sql);
	$items=array();
	while($row=$result->Fetchrow()){
		$items[]=$row;
	}
	echo json_encode($items);
}elseif($act=='combo_voucheram'){
	$sql="select * from m_voucher_nominal where voucher_type='$voucher_id' order by price asc";
	$result=$dbproj->Execute($sql);
	$items=array();
	while($row=$result->Fetchrow()){
		$items[]=$row;
	}
	echo json_encode($items);
}elseif($act=='combo_paket'){
	$sql="select paket from m_voucher_type where status='1' group by paket order by paket desc";
	$result=$dbproj->Execute($sql);
	$items=array();
	while($row=$result->Fetchrow()){
		$items[]=$row;
	}
	echo json_encode($items);
}
?>