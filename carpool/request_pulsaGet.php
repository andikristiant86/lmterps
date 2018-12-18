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
	if($sort=='OCS_ID'){
		$sort='OCS_ID';
	}elseif($sort=='OCS_Desc'){
		$sort='OCS_Desc';
	}
	
	$offset = ($page-1)*$rows;
	
	$f_start_date	=	$_REQUEST['f_start_date'];
	$f_start_date	= (empty($f_start_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_start_date,1,'/'));
	$f_end_date		=	$_REQUEST['f_end_date'];
	$f_end_date	= (empty($f_end_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_end_date,1,'/'));
	
	$q = $_POST['value'];
	$fname=isset($_POST['name']) ? strval($_POST['name']) : 'DESCRIPTION';
	$find = ($fname=="all")?"and (REQ_NAME  like '%$q%' OR OCS_ID like '%$q%' OR PROJ_CODE like '%$q%' or STS_APP_PM_NM like '%$q%'
	or STS_APP_FINANCE_NM like '%$q%' or STATUS_TOPUP like '%$q%'
	)":"and $fname like '%$q%'";
	
	$result = array();
	$result["total"] = $dbproj->getOne("SELECT count(*) FROM (
	select x.*, 
	(select nm_peg from SPG_DATA_CURRENT where nip=x.REQ_NIP) as REQ_NAME, 
	(SELECT PROJ_CODE from M_PROJECT where ID=case when x.PROJ_ID='MANAGEMENT' then 0 else x.PROJ_ID end) as PROJ_CODE,
	case STS_APP_PM WHEN 0 then 'NOT APPROVED' WHEN 1 THEN 'APPROVED' ELSE 'REJECT' END AS STS_APP_PM_NM,
	case STS_APP_FINANCE WHEN 0 then 'NOT APPROVED' WHEN 1 THEN 'APPROVED' ELSE 'REJECT' END AS STS_APP_FINANCE_NM
	from M_REQUEST_PULSA x) AS Y 
	where lke_id in (select area from m_topup_admin where admin='$login_nip') and 
	req_date between '$f_start_date' and '$f_end_date' $find");
		
	$sql="SELECT * FROM (
	select x.*, 
	(select nm_peg from SPG_DATA_CURRENT where nip=x.REQ_NIP) as REQ_NAME, 
	(SELECT PROJ_CODE from M_PROJECT where ID=case when x.PROJ_ID='MANAGEMENT' then 0 else x.PROJ_ID end) as PROJ_CODE,
	case STS_APP_PM WHEN 0 then 'NOT APPROVED' WHEN 1 THEN 'APPROVED' ELSE 'REJECT' END AS STS_APP_PM_NM,
	case STS_APP_FINANCE WHEN 0 then 'NOT APPROVED' WHEN 1 THEN 'APPROVED' ELSE 'REJECT' END AS STS_APP_FINANCE_NM
	from M_REQUEST_PULSA x) AS Y 
	where lke_id in (select area from m_topup_admin where admin='$login_nip') and 
	req_date between '$f_start_date' and '$f_end_date' $find
	ORDER BY CREATE_DATE ASC
	";
	
	$result_user=$dbproj->SelectLimit($sql,$rows,$offset);
	$items=array();
	while($row=$result_user->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$create_date=$f->convert_date(substr($create_date,0,10),1)." ".substr($create_date,11,8);
		$date_app_pm=$f->convert_date(substr($date_app_pm,0,10),1)." ".substr($date_app_pm,11,8);
		$received_date=$f->convert_date(substr($received_date,0,10),1);
		$req_date=$f->convert_date($req_date,1);
		$amount=number_format($amount,0,".",",");
		$km_acuan=number_format($km_acuan,0,".",",");
		$ocs_id=(empty($ocs_id))?"OTHERS":$ocs_id;
		$proj_code=(empty($proj_code))?"OTHERS":$proj_code;
		$date_app_pm=($ocs_id=='OTHERS')?$create_date:$date_app_pm;
		$coord_name=$db->getOne("select nm_peg from spg_data_current where nip='$create_by'");
		$type=$dbproj->getOne("select voucher_name from m_voucher_type where voucher_id='$voucher_id'");
		$nominal=$dbproj->getOne("select voucher_amount from m_voucher_nominal where id='$voucher_nominal'");
		
			$items[]=array("id"=>"$id","ocs_id"=>"$ocs_id","create_date"=>"$create_date","req_date"=>"$req_date","phone_number"=>"$phone_number","req_name"=>"$req_name",
			"sts_app_finance"=>"$sts_app_finance_nm","sts_app_pm"=>"$sts_app_pm_nm","description"=>"$description",
			"amount"=>"$amount","dt_name"=>"$dt_name","status_topup"=>"$status_topup","ocs_desc"=>"$ocs_desc","proj_code"=>"$proj_code",
			"coord_name"=>"$coord_name","received_date"=>"$received_date","date_app_pm"=>"$date_app_pm","voucher_paket"=>"$voucher_paket",
			"voucher_type"=>"$type","voucher_nominal"=>"$nominal"
			);
		
	}
	$total_topup=number_format($dbproj->getOne("select sum(amount) from 
	m_request_pulsa where lke_id in (select area from m_topup_admin where admin='$login_nip') 
	and req_date between '$f_start_date' and '$f_end_date' and status_topup='RECEIVED'"),0,'.',',');
	$result["footer"]=array(
							array("req_date"=>"<b>DEPOSIT</b>","req_name"=>"<b><span style='color:black'>600.000</span></b>")
							);
	$result["rows"] = $items;
	echo json_encode($result);
}
else if($act=='view1'){
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
	
	$f_start_date	=	$_REQUEST['f_start_date'];
	$f_start_date	= (empty($f_start_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_start_date,1,'/'));
	$f_end_date		=	$_REQUEST['f_end_date'];
	$f_end_date	= (empty($f_end_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_end_date,1,'/'));
	
	$q = $_POST['value'];
	$fname=isset($_POST['name']) ? strval($_POST['name']) : 'DESCRIPTION';
	$find = ($fname=="all")?"and (REQ_NAME  like '%$q%' OR OCS_ID like '%$q%' OR PROJ_CODE like '%$q%' or STS_APP_PM_NM like '%$q%'
	or STS_APP_FINANCE_NM like '%$q%' or STATUS_TOPUP like '%$q%'
	)":"and $fname like '%$q%'";
	
	$result = array();
	$result["total"] = $dbproj->getOne("SELECT count(*) FROM (
	select x.*, 
	(select nm_peg from SPG_DATA_CURRENT where nip=x.REQ_NIP) as REQ_NAME, 
	(SELECT PROJ_CODE from M_PROJECT where ID=x.PROJ_ID) as PROJ_CODE,
	case STS_APP_PM WHEN 0 then 'NOT APPROVED' WHEN 1 THEN 'APPROVED' ELSE 'REJECT' END AS STS_APP_PM_NM,
	case STS_APP_FINANCE WHEN 0 then 'NOT APPROVED' WHEN 1 THEN 'APPROVED' ELSE 'REJECT' END AS STS_APP_FINANCE_NM
	from M_REQUEST_PULSA x where x.proj_id in (select id from m_project where pm_id='$login_nip')) AS Y where req_date between '$f_start_date' and '$f_end_date' $find");
		
	$sql="SELECT * FROM (
	select x.*, 
	(select nm_peg from SPG_DATA_CURRENT where nip=x.REQ_NIP) as REQ_NAME, 
	(SELECT PROJ_CODE from M_PROJECT where ID=x.PROJ_ID) as PROJ_CODE,
	case STS_APP_PM WHEN 0 then 'NOT APPROVED' WHEN 1 THEN 'APPROVED' ELSE 'REJECT' END AS STS_APP_PM_NM,
	case STS_APP_FINANCE WHEN 0 then 'NOT APPROVED' WHEN 1 THEN 'APPROVED' ELSE 'REJECT' END AS STS_APP_FINANCE_NM
	from M_REQUEST_PULSA x where x.proj_id in (select id from m_project where pm_id='$login_nip')) AS Y where req_date between '$f_start_date' and '$f_end_date' $find
	ORDER BY CREATE_DATE ASC
	";
	
	$result_user=$dbproj->SelectLimit($sql,$rows,$offset);
	$items=array();
	while($row=$result_user->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$create_date=$f->convert_date(substr($create_date,0,10),1)." ".substr($create_date,11,8);
		$date_app_pm=$f->convert_date(substr($date_app_pm,0,10),1)." ".substr($date_app_pm,11,8);
		$received_date=$f->convert_date(substr($received_date,0,10),1);
		$req_date=$f->convert_date($req_date,1);
		$amount=number_format($amount,0,".",",");
		$km_acuan=number_format($km_acuan,0,".",",");
		$ocs_id=(empty($ocs_id))?"OTHERS":$ocs_id;
		$proj_code=(empty($proj_code))?"OTHERS":$proj_code;
		$date_app_pm=($ocs_id=='OTHERS')?$create_date:$date_app_pm;
		$coord_name=$db->getOne("select nm_peg from spg_data_current where nip='$create_by'");
			$items[]=array("id"=>"$id","ocs_id"=>"$ocs_id","create_date"=>"$create_date","req_date"=>"$req_date","phone_number"=>"$phone_number","req_name"=>"$req_name",
			"sts_app_finance"=>"$sts_app_finance_nm","sts_app_pm"=>"$sts_app_pm_nm","description"=>"$description",
			"amount"=>"$amount","dt_name"=>"$dt_name","status_topup"=>"$status_topup","ocs_desc"=>"$ocs_desc","proj_code"=>"$proj_code",
			"coord_name"=>"$coord_name","received_date"=>"$received_date","date_app_pm"=>"$date_app_pm"
			);
		
	}
	$total_topup=number_format($dbproj->getOne("select sum(amount) from m_request_pulsa where req_date between '$f_start_date' and '$f_end_date' and status_topup='RECEIVED'"),0,'.',',');
	$result["footer"]=array(
							array("req_date"=>"<b>Total Transfer</b>","amount"=>"<b><span style='color:black'>$total_topup</span></b>")
							);
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='do_approval'){
	$rows=explode(",",$id);
	foreach ($rows as $key=>$val){
		$dbproj->Execute("update m_request_pulsa set status_topup='RECEIVED',received_date=GETDATE() where id='$val'");
	}
	echo json_encode(array('success'=>true));	
}elseif($act=='do_notapproval'){
	$rows=explode(",",$id);
	foreach ($rows as $key=>$val){
		$dbproj->Execute("update m_request_pulsa set status_topup='REJECT',reason_reject='$reason_reject',received_date=GETDATE() where id='$val'");
	}
	echo json_encode(array('success'=>true));
}elseif($act=='combo_lokasi'){
	$cmp_id=$db->getOne("select cmp_id from spg_data_current where nip='$login_nip'");
	$sql="select lke_id,lokasi_kerja from m_project_area where cmp_id='$cmp_id'";
	$result=$dbproj->Execute($sql);
	$items=array();
	while($row=$result->Fetchrow()){
		$items[]=$row;
	}
	echo json_encode($items);
}elseif($act=='combo_kasir'){
	$cmp_id=$db->getOne("select cmp_id from spg_data_current where nip='$login_nip'");
	$sql="select x.kasir_id lke_id,x.kasir_area+' ('+y.nm_peg+')' as lokasi_kerja from lmt_payment.[dbo].[m_kasir] x left join spg_data_current y on y.nip=x.pic_id 
	where x.cmp_id='$cmp_id' and x.[type] in ('REGION') order by kasir_id asc";
	$result=$db->Execute($sql);
	$items=array();
	while($row=$result->Fetchrow()){
		$items[]=$row;
	}
	echo json_encode($items);
}elseif($act=='combo_kasir_car'){
	$cmp_id=$db->getOne("select cmp_id from spg_data_current where nip='$login_nip'");
	$sql="select x.kasir_id lke_id,x.kasir_area+' ('+y.nm_peg+')' as lokasi_kerja from lmt_payment.[dbo].[m_kasir] x left join spg_data_current y on y.nip=x.pic_id 
	where x.cmp_id='$cmp_id' and x.[type] in ('CARPOOL') order by sub_kasir asc";
	$result=$db->Execute($sql);
	$items=array();
	while($row=$result->Fetchrow()){
		$items[]=$row;
	}
	echo json_encode($items);
}
?>