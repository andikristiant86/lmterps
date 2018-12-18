<?php
ob_start();
session_start();
include("$DOCUMENT_ROOT/s/config.php");
$nip_sipeg=$_SESSION['sipeg_nip_pegawai'];	
$login_nip=(empty($nip_sipeg))? $login_nip:$nip_sipeg;
$periode=$db->getOne("SELECT LEFT(CONVERT(varchar, GetDate(),112),6)");
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
	$find = ($fname=="all")?"and (ocs_id like '%$q%' or status_app like '%$q%' or pm_name like '%$q%' or time like '%$q%')":"and $fname like '%$q%'";
	
	$result = array();
	
	$query="select a.*,
	case a.sts_app_pm 
	  when 0 then 'OPEN' 
	  when 1 then 'APPROVED' 
	  when 2 then 'REJECT' 
	  else 'OPEN' end as status_app_pm,
	case a.sts_app_finance
		when 0 then 'OPEN' 
		when 1 then 'APPROVED' 
		when 2 then 'REJECT' 
		else 'OPEN' end as status_app_finance,b.nm_peg as topup_to, c.nm_peg as [create], d.proj_name, e.posisi, f.ppob_ket,f.transfer_type
	from m_request_pulsa a
	left join spg_data_current b on b.nip=a.req_nip
	left join spg_data_current c on c.nip=a.create_by
	left join m_project d on convert(varchar(15),d.id)=a.proj_id
	left join spg_08_jabatan_unit e on e.kd_jabatan+e.kd_unit_org=b.kd_jabatan_str+b.kd_unit_org
	left join lmt_payment.dbo.m_request_pulsa f on f.req_id=a.ocs_id";
	
	$filt="and (ocs_id like '%$q%' or topup_to like '%$q%' or [create] like '%$q%')";
	
	$result["total"] = $dbproj->getOne("select count(*) from ($query) as x where 
	((nip_approval='$login_nip') or (nip_approval=case when '$login_nip'='032015' then '021918' else '$login_nip' end)) and status_approval='OPEN' $filt");
		
	$sql="select * from ($query) as x where
	((nip_approval='$login_nip') or (nip_approval=case when '$login_nip'='032015' then '021918' else '$login_nip' end)) and status_approval='OPEN'  $filt order by req_date desc";
	
	$result_user=$dbproj->SelectLimit($sql,$rows,$offset);
	$items=array();
	while($row=$result_user->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$req_date=$f->convert_date($req_date,1);
		
		$amount_month=$dbproj->getOne("select sum(amount) from m_request_pulsa where req_nip='$req_nip' and status_topup='RECEIVED'
										and LEFT(CONVERT(varchar, received_date,112),6)='$periode'");
										
		$amount_month=number_format($amount_month,0,".",",");
		$amount=(!empty($ppob_ket))?strtoupper($ppob_ket):number_format($amount,0,".",",");
		$transfer_typee=($transfer_type=='UANG')?"<br><font color=red>TRANSFER UANG</font>":"";
		$proj_name=empty($proj_name)?"OTHERS <br><font color=green>$description</font> $transfer_typee":"$proj_name <br> <font color=green> $description</font> $transfer_typee";
		$items[]=array("id"=>"$id","ocs_id"=>"$ocs_id <BR> $req_date","req_date"=>"$req_date","req_name"=>"$topup_to <br> <font color=#d1d1d1>$posisi</font>",
		"phone_number"=>"$phone_number",
		"amount"=>"$amount",
		"sts_app_pm"=>"$status_app_pm","sts_app_finance"=>"$status_app_finance","status_received"=>"$status_received","create_by"=>"$create","proj_name"=>"$proj_name",
		"description"=>"$description","amount_month"=>"$amount_month");
	}

	// $result["footer"]=array(
							// array("description"=>"<b>TOTAL TOPUP PERIODE $periode</b>","amount"=>"<b><span style='color:black'>0</span></b>")
							// );
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='do_approval'){
	$rows=explode(",",$id);
	foreach ($rows as $key=>$val){
		//app ke 2 budget control a.n eko andika putro
		if($login_nip=='031629'){
			$req_id=$dbproj->getOne("select ocs_id from m_request_pulsa where id='$val'");
			$sql[]="update m_request_pulsa set sts_app_finance='1',date_app_finance=GETDATE(),
			sts_app_pm='1',date_app_pm=GETDATE(),nip_approval='$login_nip',status_approval='CLOSED' where id='$val'";
			$sql[]="update lmt_payment.dbo.m_request_pulsa set status='1' where req_id='$req_id'";
		}else{
			$req_id=$dbproj->getOne("select ocs_id from m_request_pulsa where id='$val'");
			$sql[]="update m_request_pulsa set nip_approval='031629' where id='$val'";
			$sql[]="update lmt_payment.dbo.m_request_pulsa set next_approval='031629' where req_id='$req_id'";
		}
	}
	$dbproj->BeginTrans();
	for($i=0;$i<count($sql);$i++){
				$result=$dbproj->Execute($sql[$i]);
				if (!$result){
						$dbproj->RollbackTrans();
						echo json_encode(array('errorMsg'=>"Sql error!!!"));
						die();
				}
			}
	$dbproj->CommitTrans();
	echo json_encode(array('success'=>true));
}elseif($act=='do_notapproval'){
	$rows=explode(",",$id);
	foreach ($rows as $key=>$val){
		$req_id=$dbproj->getOne("select ocs_id from m_request_pulsa where id='$val'");
		$status=$dbproj->getOne("select status from lmt_payment.dbo.m_request_pulsa where req_id='$req_id'");
		if($status==2) {echo json_encode(array('errorMsg'=>"Tidak boleh di reject, pulsa sudah ditopup oleh finance!!!")); die();}
		$sql[]="update m_request_pulsa set sts_app_finance='2',date_app_finance=GETDATE(),
		sts_app_pm='2',date_app_pm=GETDATE(),nip_approval='$login_nip',status_approval='CLOSED' where id='$val'";
		$sql[]="update lmt_payment.dbo.m_request_pulsa set status='4',reject_by='$login_nip',reject_date=GETDATE(),reason='$alasan' where req_id='$req_id'";
	}
	$dbproj->BeginTrans();
	for($i=0;$i<count($sql);$i++){
				$result=$dbproj->Execute($sql[$i]);
				if (!$result){
						$dbproj->RollbackTrans();
						echo json_encode(array('errorMsg'=>"Sql error!!!"));
						die();
				}
			}
	$dbproj->CommitTrans();
	echo json_encode(array('success'=>true));	
}
?>