<?php
session_start();
include("../s/config.php");
$nip_sipeg=$_SESSION['sipeg_nip_pegawai'];
$CreateBy=(empty($nip_sipeg))? $login_nip:$nip_sipeg;
$pm_nip=$db->getOne("select nip from(
						select nip,kd_pm from spg_data_current where kd_unit_org='1030102000000000'
						union
						select nip,kd_pm from spg_data_current2 where kd_unit_org='1030102000000000') as x
where kd_pm=(select kd_pm  from spg_data_current where nip='$CreateBy')");
$kd_pm=$db->getOne("select kd_pm from(
						select nip,kd_pm from spg_data_current where kd_unit_org='1030102000000000'
						union
						select nip,kd_pm from spg_data_current2 where kd_unit_org='1030102000000000') as x
where kd_pm=(select kd_pm  from spg_data_current where nip='$CreateBy')");
$CreateDate=date("Y/m/d");
$lke_id=$db->getOne("select lke_id from spg_data_current where nip='$login_nip'");
if($act=='view'){
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
	$sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'OCS_ID';
	$order = isset($_POST['order']) ? strval($_POST['order']) : 'desc';
	if($sort=='OCS_ID'){
		$sort='OCS_ID';
	}elseif($sort=='OCS_Desc'){
		$sort='OCS_Desc';
	}
	$offset = ($page-1)*$rows;

	$result = array();
	$f_start_date	=	$_REQUEST['f_start_date'];
	$f_start_date	= (empty($f_start_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_start_date,1,'/'));
	$f_end_date		=	$_REQUEST['f_end_date'];
	$f_end_date	= (empty($f_end_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_end_date,1,'/'));
	
	$find_val=$_REQUEST['value'];
	$find_name=$_REQUEST['name'];
	$find_str=($find_name=='all' or empty($find_name))?
	"where date between '$f_start_date' and '$f_end_date' and lke_id='$lke_id' and proposal_type='MANAGEMENT' and dt_coord='$CreateBy' and (ocs_id like '%$q%' or ocs_desc like '%$q%' or status_name like '%$q%')":
	"where  date between '$f_start_date' and '$f_end_date' and lke_id='$lke_id' and proposal_type='MANAGEMENT' and dt_coord='$CreateBy' and $find_name like '%$find_val%'";
	
	$result["total"] = $dbproj->getOne("select count(*) from (select *,
       case status 
               when 1 then 'JALAN'
               when 2 then 'PULANG'
               when 3 then 'CLOSED'
       else 'BELUM JALAN' end as status_name
       from m_carpool ) as x
       $find_str");
	
	$sql="select * from (select *,
       case status 
               when 1 then 'JALAN'
               when 2 then 'PULANG'
               when 3 then 'CLOSED'
       else 'BELUM JALAN' end as status_name
       from m_carpool ) as x
       $find_str order by $sort $order";
	
	$result_user=$dbproj->SelectLimit($sql,$rows,$offset);
	$items=array();
	while($row=$result_user->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$date=$f->convert_date($date,1);
		$proj_name=$dbproj->getOne("select proj_name from m_project where id='$proj_id'");
		$name_dt=$db->getOne("select nm_peg from spg_data_current where nip='$nip_dt'");
		$name_rno=$db->getOne("select nm_peg from spg_data_current where nip='$nip_rno'");
		$pm_approve=($pm_approve==1)?"Approved":"Not Approved";
		$finance_approve=($finance_approve==1)?"Approved":"Not Approved";
		$uang_pulsa=number_format($uang_pulsa,0,".",",");
		$km_acuan=number_format($km_acuan,0,".",",");
		$items[]=array("ocs_id"=>"$ocs_id","ocs_desc"=>"$ocs_desc","date"=>"$date","time"=>"$time",
		"proj_id"=>"$proj_id","proj_code"=>"$proj_code","proj_name"=>"$proj_name","site_name"=>"$site_name","sow_name"=>"$sow_name",
		"name_dt"=>"$name_dt","nip_dt"=>"$nip_dt","name_rno"=>"$name_rno","nip_rno"=>"$nip_rno","pm_approve"=>"$pm_approve","finance_approve"=>"$finance_approve",
		"status"=>"$status_name","km_acuan"=>"$km_acuan","uang_pulsa"=>"$uang_pulsa","result_dt"=>"$result_dt","result_rno"=>"$result_rno","remark"=>"$remark",
		"sow_id"=>"$sow_id","phone_number"=>"$phone_number"
		);
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
	$ocs_id=$f->generate_nomorkolom("lmt_project.dbo.M_Carpool","OCS_ID","CRP");
	foreach($HTTP_POST_VARS as $key=>$val){
	if(!preg_match("#^(name_dt|name_rno)#",$key)){
		if(preg_match("#^(date)#",$key)){
			$date=explode("/",$val);
				$hr=$date[0];
				$bl=$date[1];
				$th=$date[2];
				$date_en="$th/$bl/$hr";
				$date_en=($date_en=="//")?"":"$date_en";
			$columns .="$key,";
			$values .= "'$date_en',";
		}elseif(preg_match("#^(ocs_id)#",$key)){
			$columns .="$key,";
			$values .= "'$ocs_id',";
		}else{
			$columns .="$key,";
			$values .="'$val',";
		}
	}
	}
	$columns = preg_replace("/,$/","",$columns);
	$values	 = preg_replace("/,$/","",$values);
	$check_id=$dbproj->getOne("select count(*) from m_carpool where ocs_id='$ocs_id'");
	if ($check_id != 0){
		echo json_encode(array('errorMsg'=>"Error: Duplicate Data"));
		die();
	}	
	
	$result=$dbproj->Execute("insert into m_carpool ($columns,dt_coord,status,proposal_type,pm_approve,finance_approve,lke_id) 
	values ($values,'$CreateBy','4','MANAGEMENT','1','1','$lke_id')");
	
		if ($result){
			echo json_encode(array('success'=>true));
		} else {
			echo json_encode(array('errorMsg'=>"insert into m_carpool ($columns,dt_coord,status,proposal_type) values ($values,'$CreateBy','4','MANAGEMENT')"));
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
	$result=$dbproj->Execute("update m_carpool set $list,lke_id='$lke_id' where ocs_id='$ocs_id'");
	if($status=='PULANG')$dbproj->Execute("update m_carpool set status='3' where ocs_id='$ocs_id'");
		if ($result){
			echo json_encode(array('success'=>true));
		} else {
			echo json_encode(array('errorMsg'=>"Error: update m_carpool set $list where ocs_id='$ocs_id'"));
		}
}
elseif($act=='do_destroy'){
	$result=$dbproj->Execute("delete m_carpool where ocs_id='$id'");
		if ($result){
			$dbproj->Execute("delete t_approvel_carpool where ocs_id='$id'");
			echo json_encode(array('success'=>true));
		} else {
			echo json_encode(array('errorMsg'=>"Error: delete m_client where clientid='$id'"));
		}
}elseif($act=='combo_dt'){
	
	$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	$sql="select top 50 nip,nm_peg from spg_data_current
	where nip like '%$q%' or nm_peg like '%$q%'";
	$result_user=$dbproj->Execute($sql);
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