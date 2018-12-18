<?php
session_start();
include("../s/config.php");
$nip_sipeg=$_SESSION['sipeg_nip_pegawai'];
$CreateBy=(empty($nip_sipeg))? $login_nip:$nip_sipeg;
$pm_nip=$dbproj->getOne("select top 1 
(SELECT M_PROJECT.PM_ID FROM M_PROJECT WHERE M_PROJECT.id=T_RESOURCE_ASSIGN.PROJ_ID) from T_RESOURCE_ASSIGN where nip='$CreateBy' and FLAG_ASSIGN=1");

$res=$dbproj->Execute("select proj_id from m_carpool_coord where nip='CreateBy'");
$wInProj='';/*
while($row=$res->FetchRow()){
	foreach($row as $key=>$val){
		$wInProj=empty($wInProj)?"$val":",$val";
	}
}
$wInProj=empty($wInProj)?"":"in ($wInProj)";
*/
$kd_pm=$db->getOne("select kd_pm from(
						select nip,kd_pm from spg_data_current where kd_unit_org='1030102000000000'
						union
						select nip,kd_pm from spg_data_current2 where kd_unit_org='1030102000000000') as x
where kd_pm=(select kd_pm  from spg_data_current where nip='$CreateBy')");
$CreateDate=date("Y/m/d");
$lke_id=$db->getOne("select lke_id from spg_data_current where nip='$CreateBy'");
if($act=='view'){
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
	$sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'ocs_id';
	$order = isset($_POST['order']) ? strval($_POST['order']) : 'desc';
	
	$offset = ($page-1)*$rows;
	
	$result = array();
	$f_start_date	=	$_REQUEST['f_start_date'];
	//$f_start_date	= (empty($f_start_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_start_date,1,'/'));
	$f_end_date		=	$_REQUEST['f_end_date'];
	//$f_end_date	= (empty($f_end_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_end_date,1,'/'));
	$find_date=(empty($_REQUEST['f_start_date']) || empty($_REQUEST['f_end_date']))?"":
	"date between '".str_replace('/','-',$f->convert_date($f_start_date,1,'/'))."' and '".str_replace('/','-',$f->convert_date($f_end_date,1,'/'))."' and";
	$find_val=$_REQUEST['value'];
	$find_name=$_REQUEST['name'];
	$find_str=($find_name=='all' or empty($find_name))?
	"where $find_date dt_coord='$CreateBy' and (ocs_id like '%$find_val%' or ocs_desc like '%$find_val%' or status_name like '%$find_val%')":
	"where  $find_date $find_name like '%$find_val%'";
	
	
	$result["total"] = $dbproj->getOne("select count(*) from (select *,
       case status 
               when 1 then 'JALAN'
               when 2 then 'PULANG'
               when 3 then 'CLOSED'
			   when 112 then 'CANCEL'
			   when 111 then 'REJECT'
       else 'BELUM JALAN' end as status_name,
	   (SELECT SUM(AMOUNT) FROM M_REQUEST_PULSA WHERE OCS_ID=m_carpool.OCS_ID) AS AMOUNT_TOPUP
       from m_carpool ) as x
       $find_str");
	
	$sql="select * from (select *,
       case status 
               when 1 then 'JALAN'
               when 2 then 'PULANG'
               when 3 then 'CLOSED'
			   when 112 then 'CANCEL'
			   when 111 then 'REJECT'
       else 'BELUM JALAN' end as status_name,
	   (SELECT SUM(AMOUNT) FROM M_REQUEST_PULSA WHERE OCS_ID=m_carpool.OCS_ID) AS AMOUNT_TOPUP
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
		$pm_approve_date=$f->convert_date(substr($pm_approve_date,0,10),1)." ".substr($pm_approve_date,11,8);
		$finance_approve_date=$f->convert_date(substr($finance_approve_date,0,10),1)." ".substr($finance_approve_date,11,8);
		$pm_approve=($pm_approve==1)?"Approved <br> <font color=green>$pm_approve_date</font>":"Not Approved";
		$finance_approve=($finance_approve==1)?"Approved <br> <font color=green>$finance_approve_date</font>":"Not Approved";
		$uang_pulsa=number_format($amount_topup,0,".",",");
		$km_acuan=number_format($km_acuan,0,".",",");
		$operational=$um+$uj+$bbm+$parking;
		$operational=number_format($operational,0,".",",");
		$um=number_format($um,0,".",",");
		$uj=number_format($uj,0,".",",");
		$bbm=number_format($bbm,0,".",",");
		$parking=number_format($parking,0,".",",");
		
		$jum_site=(empty($site_name) || $site_name=="." || $site_name=="-")?0:$dbproj->getOne("select count(*) from m_carpool where proj_id='$proj_id' and sow_id='$sow_id' and site_name='$site_name'");
		
		$items[]=array("ocs_id"=>"$ocs_id","ocs_desc"=>"$ocs_desc","date"=>"$date","time"=>"$time",
		"proj_id"=>"$proj_id","proj_code"=>"$proj_code","proj_name"=>"$proj_name","site_name"=>"$site_name","sow_name"=>"$sow_name",
		"name_dt"=>"$name_dt","nip_dt"=>"$nip_dt","name_rno"=>"$name_rno","nip_rno"=>"$nip_rno","pm_approve"=>"$pm_approve","finance_approve"=>"$finance_approve",
		"status"=>"$status_name","km_acuan"=>"$km_acuan","uang_pulsa"=>"$uang_pulsa","result_dt"=>"$result_dt","result_rno"=>"$result_rno","remark"=>"$remark",
		"sow_id"=>"$sow_id","phone_number"=>"$phone_number","site_id"=>"$site_name","jum_site"=>"$jum_site","um"=>"$um","uj"=>"$uj","bbm"=>"$bbm","parking"=>"$parking",
		"operational"=>"$operational"
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
	$req_date=$_REQUEST['date'];
	$req_date=str_replace("/","-","$req_date");
	$batas_input=$db->getOne("SELECT case when '18:00:00' <= convert(varchar, getdate(), 108) and convert(varchar, getdate(), 105)='$req_date' then 'No' else 'Ok' end");
	if($batas_input=='No'){
		echo json_encode(array('errorMsg'=>"Maaf, batas waktu penginputan jam 18:00:00!"));
		die();
	}

$isAdminOrderCarpool=$db->getOne("select top 1 wia_inquiryaccess  from wf_inquiry_access where wia_accessname='$wia_accessname' and wia_inquiryname='AdminOrderCarpool'")=='Ya'?true:false;

if(!$isAdminOrderCarpool){ 
	$cek_req_sebelum=$dbproj->GetOne("SELECT COUNT(*) FROM [dbo].[M_CARPOOL] WHERE DT_COORD='$CreateBy' and [DATE] >= '2016-01-23' AND convert(varchar, [date], 105) <> '$req_date' AND
	case isnull(status,'') when '1' then 'JALAN' when '2' then 'PULANG' when '3' then 'CLOSED' when '111' then 'REJECT' 
	when 112 then 'CANCEL' else 'BELUM JALAN' end in ('BELUM JALAN','JALAN','PULANG')
	and isnull(TAMBAH_DURASI,0) < DateDiff (Day,[date],GETDATE())
	");
	$ara_id=$_REQUEST['lke_id'];
	if($cek_req_sebelum >= 1 and $ara_id=='ARA-000004'){
		echo json_encode(array('errorMsg'=>"Pastikan request sebelumnya sudah closed!"));
		die();
	}

	//$type=$dbproj->getOne("select type from m_sow where sow_id='$sow_id'");
	if(empty($proj_id)){
		echo json_encode(array('errorMsg'=>"Sorry, this field project is required!"));
		die();
	}
	if(empty($sow_id)){
		echo json_encode(array('errorMsg'=>"Sorry, this field sow is required!"));
		die();
	}
	if(empty($site_name)){
		echo json_encode(array('errorMsg'=>"Sorry, this field site is required!"));
		die();
	}
	// if(empty($site_id) and $type!='RESOURCE'){
		// echo json_encode(array('errorMsg'=>"Site name tidak ditemukan, silahkan konfirmasi ke project admin!"));
		// die();
	// }
	if(empty($nip_dt) and empty($nip_rno)){
		echo json_encode(array('errorMsg'=>"Sorry, this field DT or RNO is required!"));
		die();
	}
}
else
{
	if(empty($nip_dt)){
		echo json_encode(array('errorMsg'=>"Sorry, this field employee is required!"));
		die();
	}
}	
	$ocs_id=$f->generate_nomorkolom("lmt_project.dbo.M_Carpool","OCS_ID","CRP");
	foreach($HTTP_POST_VARS as $key=>$val){
	if(!preg_match("#^(name_dt|name_rno|site_id)#",$key)){
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
			
	$result=$dbproj->Execute("insert into m_carpool ($columns,dt_coord,status,create_date) values ($values,'$CreateBy','4',GETDATE())");
	
	
		if ($result){
			if(!$isAdminOrderCarpool){ 
				$nip_app=$dbproj->getOne("select pm_id from m_project where id='$proj_id'");
				$app_id=1;
			}
			else //bypass by luckman for carpool proposal
			{
				$detailuser=$f->detailPegawai($login_nip);
				$nip_app=$dbproj->getOne("select app_nip from m_approvel where app_no_urut='2' and app_jenis='REQ_CARPOOL' and cmp_id='".$detailuser['cmp_id']."'");
				$app_id=$dbproj->getOne("select app_id from m_approvel where app_no_urut='2' and app_jenis='REQ_CARPOOL' and cmp_id='".$detailuser['cmp_id']."'");
				$lke_id=$dbproj->getOne("select area from m_carpool_admin where admin='$login_nip'");;
				$dbproj->Execute("update m_carpool set pm_approve='1',proposal_type='MANAGEMENT',lke_id='$lke_id',pm_approve_date=GETDATE() where ocs_id='$ocs_id'");
			}
			$dbproj->Execute("insert into t_approvel_carpool (nip_app,ocs_id,status_app,app_id) values ('$nip_app','$ocs_id','OPEN','$app_id')");
			echo json_encode(array('success'=>true));
		} else {
			echo json_encode(array('errorMsg'=>"Error: insert into m_carpool ($columns,dt_coord,status,create_date) values ($values,'$CreateBy','4',GETDATE())"));
		}
}
elseif($act=='do_update'){
	$pm_approve=$dbproj->getOne("select isnull(pm_approve,0) from m_carpool where ocs_id='$ocs_id'");
	$finance_approve=$dbproj->getOne("select isnull(finance_approve,0) from m_carpool where ocs_id='$ocs_id'");
	// if($check>=1){
		// echo json_encode(array('errorMsg'=>"Jika status sudah disetujui tidak bisa diubah!"));
		// die();
	// }
	$type=$dbproj->getOne("select type from m_sow where sow_id='$sow_id'");
	if(empty($proj_id)){
		echo json_encode(array('errorMsg'=>"Sorry, field project can not be empty!"));
		die();
	}
	if(empty($sow_id)){
		echo json_encode(array('errorMsg'=>"Sorry, field sow can not be empty!"));
		die();
	}
	// if(empty($site_id) and $type!='RESOURCE'){
		// echo json_encode(array('errorMsg'=>"Site name tidak ditemukan, silahkan konfirmasi ke project admin!"));
		// die();
	// }
	foreach($HTTP_POST_VARS as $key=>$val){
		if(!preg_match("#^(ocs_id|name_dt|name_rno|site_id)#",$key)){
			if(preg_match("#^(date)#",$key)){
				$list .="";
			}elseif(preg_match("#^(um|uj|bbm|parking)#",$key) && ($pm_approve=='1' or $finance_approve=='1')){
				$list .="";
			}else{
				$list .="$key='$val',";
			}
		}
	}
	$columns = preg_replace("/,$/","",$columns);
	$values	 = preg_replace("/,$/","",$values);
	$list	 = preg_replace("/,$/","",$list);
	if($status=='PULANG' || ($pm_approve=='0' && $finance_approve=='0')){
		$result=$dbproj->Execute("update m_carpool set $list where ocs_id='$ocs_id'");
		if($pm_approve=='1' && $finance_approve=='1')$dbproj->Execute("update m_carpool set status='3' where ocs_id='$ocs_id'");
			if ($result){
				echo json_encode(array('success'=>true));
			} else {
				echo json_encode(array('errorMsg'=>"Error: update m_carpool set $list where ocs_id='$ocs_id'"));
			}
	}else{
		echo json_encode(array('errorMsg'=>"Maaf untuk merubah data setelah status PULANG, untuk merubah setatus jadi pulang silahkan hubungi carpool admin!"));
		die();
	}
}
elseif($act=='do_cancel'){
	if($status=='BELUM JALAN'){
		$dbproj->Execute("update m_carpool set status='112',alasan_cancel='$reason' where ocs_id='$ocs_id'");
	}else{
		echo json_encode(array('errorMsg'=>"Request bisa di cancel, jika status BELUM JALAN!"));
		die();
	}
	echo json_encode(array('success'=>true));
}
elseif($act=='do_destroy'){
	$result=$dbproj->Execute("delete m_carpool where ocs_id='$id'");
		if ($result){
			$dbproj->Execute("delete t_approvel_carpool where ocs_id='$id'");
			$dbproj->Execute("delete m_request_pulsa where ocs_id='$id'");
			echo json_encode(array('success'=>true));
		} else {
			echo json_encode(array('errorMsg'=>"Error: delete m_client where clientid='$id'"));
		}
}elseif($act=='combo_dt'){
	/**/if($x==1){ //plo/rno/enginer
		$find="'8011030102010900000','8011030102010000000','8011030102010200000','9321030102010900000','9271030102010000000','8011030102010300000',
	'9281030102010000000'";
	}
	else if($x==2){
		$find="";
	}
	else //DT/surveyor/Rigger
	{
		$find="'6021030102010700000','8011030102010700000','8011030102010600000','8011030102010102000','8011030102010400000','6021030102010400000',
		'6021030102010100000','8011030102010101000','6021030102010700000','6021030102010700000','6021030102010600000','9341030102010101000','8041030102010101000'";
	}
	/*$find=($x==1)?
	//plo/rno/enginer
	"'8011030102010900000','8011030102010000000','8011030102010200000','9321030102010900000','9271030102010000000','8011030102010300000',
	'9281030102010000000'":
	//DT/surveyor/Rigger
	"'6021030102010700000','8011030102010700000','8011030102010600000','8011030102010102000','8011030102010400000','6021030102010400000',
		'6021030102010100000','8011030102010101000','6021030102010700000','6021030102010700000','6021030102010600000','9341030102010101000','8041030102010101000'";
	
	*/
	$wInProj=empty($wInProj)?"":"or x.proj_id $wInProj";
	
	$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	$sql=empty($find)?"select top 50 nip, nm_peg from SPG_DATA_CURRENT where sts_pensiun=0 and (nip like '%$q%' or nm_peg like '%$q%')":
		"select top 50 x.nip,y.NM_PEG from T_RESOURCE_ASSIGN x
			LEFT JOIN SPG_DATA_CURRENT y on y.nip=x.nip
			where x.role_id in ($find) and x.FLAG_ASSIGN=1
		and ((SELECT M_PROJECT.PM_ID FROM M_PROJECT WHERE M_PROJECT.id=x.PROJ_ID)='$pm_nip' $wInProj)
		and (x.nip like '%$q%' or y.nm_peg like '%$q%')";
		
		
	
	$result_user=$dbproj->Execute($sql);
	$items=array();
	while($row=$result_user->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$items[]=array("nip"=>"$nip","nm_peg"=>"$nm_peg");
	}
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='combo_project'){
	$wInProj=empty($wInProj)?"":"or proj_id $wInProj";
	$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	$sql="select id, proj_code, proj_name,lke_id from project_running
	where (pm_id='$pm_nip' $wInProj) and (proj_code like '%$q%' or proj_name like '%$q%')";
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
}elseif($act=='combo_lke'){
	$sql="select lke_id,lokasi_kerja from spg_lokasi_kerja";
	$resultx=$db->Execute($sql);
	$items=array();
	while($row=$resultx->Fetchrow()){
		$items[]=$row;
	}
	echo json_encode($items);
}elseif($act=='combo_site'){
	$periode=date("Ym");
	$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	$sql="
	select top 50 * from (
	select a.proj_id, a.sow_id, a.resource_id,
	case when isnull(a.resource_id,'')='' then a.site_name else (select nm_peg from spg_data_current where nip=a.resource_id) end as site_name,a.id from t_milestone a
	where isnull(a.SITE_ID,'')!='' or a.periode='$periode'
	) as x
	where proj_id='$proj_id' and sow_id='$sow_id' and (site_name like '%$q%' or resource_id like '%$q%')";
	$result_user=$dbproj->Execute($sql);
	$items=array();
	while($row=$result_user->Fetchrow()){
		$items[]=$row;
	}
	$result["rows"] = $items;
	echo json_encode($result);
}
?>