<?php
session_start();
include("../s/config.php");
$nip_sipeg=$_SESSION['sipeg_nip_pegawai'];
$CreateBy=(empty($nip_sipeg))? $login_nip:$nip_sipeg;
$pm_nip=$dbproj->getOne("select top 1 
(SELECT M_PROJECT.PM_ID FROM M_PROJECT WHERE M_PROJECT.id=T_RESOURCE_ASSIGN.PROJ_ID) from 
T_RESOURCE_ASSIGN where nip='$CreateBy' and FLAG_ASSIGN=1");

$area_pemohon=$dbproj->getOne("select top 1 
(select m_project.lke_id from m_project where m_project.id=t_resource_assign.proj_id) from 
t_resource_assign where nip='$CreateBy' and flag_assign=1");

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
	
	$sql="select *,
       case status 
               when 1 then 'JALAN'
               when 2 then 'PULANG'
               when 3 then 'CLOSED'
			   when 5 then 'VERIFIED'
			   when 112 then 'CANCEL'
			   when 111 then 'REJECT'
       else 'BELUM JALAN' end as status_name,
	   (SELECT SUM(AMOUNT) FROM M_REQUEST_PULSA WHERE OCS_ID=m_carpool.OCS_ID) AS AMOUNT_TOPUP
       from m_carpool";
	$result["total"] = $dbproj->getOne("select count(*) from ($sql) as x $find_str");
	
	$_sql="select * from ($sql) as x $find_str order by $sort $order";
	
	$result_user=$dbproj->SelectLimit($_sql,$rows,$offset);
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
		$site_id="";
		if(!empty($site_name)){
			$res=$dbproj->Execute("select id as site_id from t_milestone where site_name in ('".implode("','",explode(",",$site_name))."')");
			while($row=$res->Fetchrow()){
				$site_id.=empty($site_id)?$row['site_id']:",".$row['site_id'];
			}
		}
	/*	$res=$dbproj->Execute("select site_id from m_carpool_detail where ocs_id='$ocs_id'");
		$site_id="";
		while($row=$res->Fetchrow()){
			$site_id.=empty($site_id)?$row['site_id']:",".$row['site_id'];
		}*/
		//$jum_site=(empty($site_name) || $site_name=="." || $site_name=="-")?0:$dbproj->getOne("select count(*) from m_carpool where proj_id='$proj_id' and sow_id='$sow_id' and site_name='$site_name'");
		
		$items[]=array("ocs_id"=>"$ocs_id","ocs_desc"=>"$ocs_desc","date"=>"$date","time"=>"$time",
		"proj_id"=>"$proj_id","proj_code"=>"$proj_code","proj_name"=>"$proj_name","site_name"=>"$site_name","sow_name"=>"$sow_name",
		"name_dt"=>"$name_dt","nip_dt"=>"$nip_dt","name_rno"=>"$name_rno","nip_rno"=>"$nip_rno","pm_approve"=>"$pm_approve","finance_approve"=>"$finance_approve",
		"status"=>"$status_name","km_acuan"=>"$km_acuan","uang_pulsa"=>"$uang_pulsa","result_dt"=>"$result_dt","result_rno"=>"$result_rno","remark"=>"$remark",
		"sow_id"=>"$sow_id","phone_number"=>"$phone_number","site_id"=>"$site_id","jum_site"=>"$jum_site","um"=>"$um","uj"=>"$uj","bbm"=>"$bbm","parking"=>"$parking",
		"operational"=>"$operational"
		);
	}
	$result["rows"] = $items;
	echo json_encode($result);
}
else if ($act=='verify'){
	$arrsql=array();$arrSite=array();$arrIdx=array();
	if($_REQUEST['idx']==1){
		$ins=$_REQUEST['inserts'];
		//$ins[]=$_REQUEST['updates'];
		unset($IDXSIDE);
		for($i=count($ins)-1;$i>=0;$i--){
			$columns="";$values="";$updates="";
			unset($ID);unset($IDX);unset($OCS_SIDE_ID);unset($OCS_ID);
			unset($PROJ_ID);unset($SITE_ID);unset($RESULT_SITE);unset($REMARK_SITE);unset($SOW_ID);
			foreach($ins[$i] as $key=>$val){
				if(!preg_match("#^(productname|nm_peg)#",$key)){
					if(preg_match("#^(ocs_id)#",$key)||$key=="id"){
						$columns.="$key,";
						$values.="'$val',";
						$key=strtoupper($key);
						$$key=$val;
					}elseif(preg_match("#^(idx|idxsite|ocs_site_id|proj_id|sow_id|remark_site|result_site|site_id)#",$key)){
						$key=strtoupper($key);
						$$key=$val;
					}
					else{
						$columns.="$key,";
						$values.="'$val',";
						$updates.="$key='$val',";
					}
				}
			}
			$columns = preg_replace("/,$/","",$columns);
			$values	 = preg_replace("/,$/","",$values);
			$updates	 = preg_replace("/,$/","",$updates);
			if(empty($ID)){
				if(empty($OCS_SITE_ID)){
					if(count($arrSite)==0){
						$OCS_SITE_ID=$dbproj->getOne("select max(id) from m_carpool_site")+1;
						$arrsql[]="insert into m_carpool_site(ID,OCS_ID,PROJ_ID,SITE_ID,RESULT_SITE,REMARK,SOW_ID) values ($OCS_SITE_ID,'$OCS_ID',$PROJ_ID,$SITE_ID,'$RESULT_SITE','$REMARK_SITE',$SOW_ID)";
						$arrSite[]=array('val'=>$OCS_SITE_ID,'idx'=>$IDXSITE);
					}
					else
						$OCS_SITE_ID=$arrSite[val];
				}
				$_id=!empty($_id)?($_id+1):$dbproj->getOne("select max(id) from m_carpool_detail")+1;
				$arrsql[]="SET IDENTITY_INSERT m_carpool_detail ON";
				$arrsql[]="insert into m_carpool_detail (id,$columns,ocs_site_id) values ($_id,$values,$OCS_SITE_ID)";
				$arrsql[]="SET IDENTITY_INSERT m_carpool_detail OFF";
				$arrIdx[]=array('val'=>$_id,'idx'=>$IDX);
			}
			else {
				$arrsql[]="update m_carpool_detail set $updates where ocs_id='$OCS_ID' and id=$ID";
			}	
		}
		$ins=$_REQUEST['deletes'];
		for($i=count($ins)-1;$i>=0;$i--){
			unset($ID);unset($OCS_ID);
			foreach($ins[$i] as $key=>$val){
				if(preg_match("#^(ocs_id|id)#",$key)){
					$key=strtoupper($key);
					$$key=$val;
				}
			}
			$arrsql[]="delete m_carpool_detail where ocs_id='$OCS_ID' and id=$ID";			
		}
	}
	else{
		$ins=$_REQUEST['inserts'];
		for($i=count($ins)-1;$i>=0;$i--){
			$columns="";$values="";$updates="";
			unset($ID);unset($IDX);unset($OCS_ID);
			foreach($ins[$i] as $key=>$val){
				if(!preg_match("#^(productname|site_name)#",$key)){
					if(preg_match("#^(ocs_id)#",$key)||$key=="id"){
						$columns.="$key,";
						$values.="'$val',";
						$key=strtoupper($key);
						$$key=$val;
					}
					elseif(preg_match("#^(idx)#",$key)){
						$key=strtoupper($key);
						$$key=$val;
					}
					else{
						$columns.="$key,";
						$values.="'$val',";
						$updates.="$key='$val',";
					}
				}
			}
			$columns = preg_replace("/,$/","",$columns);
			$values	 = preg_replace("/,$/","",$values);
			$updates	 = preg_replace("/,$/","",$updates);
			if(empty($ID)){
				$ID=$dbproj->getOne("select max(id) from m_carpool_site")+1;
				$arrsql[]="insert into m_carpool_site ($columns,id) values ($values,$ID)";
				$arrIdx[]=array('val'=>$ID,'idx'=>$IDX);
			}
			else {
				$arrsql[]="update m_carpool_site set $updates where ocs_id='$OCS_ID' and id=$ID";
			}	
		}
		$ins=$_REQUEST['deletes'];
		for($i=count($ins)-1;$i>=0;$i--){
			unset($ID);unset($OCS_ID);
			foreach($ins[$i] as $key=>$val){
				if(preg_match("#^(ocs_id|id)#",$key)){
					$key=strtoupper($key);
					$$key=$val;
				}
			}
			$arrsql[]="delete m_carpool_detail where ocs_id='$OCS_ID' and id=$ID";			
		}
	}
	$res=array();
	$dbproj->BeginTrans();
	for($i=0;$i<count($arrsql);$i++){
	//	$f->pre($arrsql[$i]);
		$result=$dbproj->Execute($arrsql[$i]);
		if (!$result){
			print $dbproj->ErrorMsg();
			$dbproj->RollbackTrans();
			$res['msg']=array("Error: ".$arrsql[$i]);
			die(json_encode($res));
		}
	}
	$dbproj->CommitTrans();
	$f->insert_log("INSERT M_CARPOOL_SITE $title. ocs_id: ".($ocs_id),json_encode($arrsql));
	/*
	$res['msg']=$arrsql;*/
	$res['idsite']=$arrSite;
	$res['id']=$arrIdx;
	echo json_encode($res);
}
else if ($act=='view_siteverify'){
	$sql="select s.*, m.SITE_NAME 
from m_carpool_site s
left join T_MILESTONE m on m.ID=s.SITE_ID
where s.ocs_id like '$ocs_id'";
//die($sql);
	$res=$dbproj->Execute($sql);
	$items=array();
	while($row=$res->FetchRow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;
		}
		$items[]=array("id"=>"$id","proj_id"=>"$proj_id","sow_id"=>"$sow_id","site_id"=>"$site_id","site_name"=>"$site_name","ocs_id"=>"$ocs_id","result_site"=>"$result_site","remark"=>"$remark");
	}
	$result["rows"] = $items;
	echo json_encode($result);
}
else if ($act=='view_detailverify'){
	$sql="select d.*,c.nm_peg
from m_carpool_detail d
left join spg_data_current c on c.nip=d.nip
where d.ocs_id like '$ocs' and d.ocs_site_id=$site";
//die($sql);
	$res=$dbproj->Execute($sql);
	$items=array();
	while($row=$res->FetchRow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;
		}
		$items[]=array("id"=>"$id","nip"=>"$nip","nm_peg"=>"$nm_peg","result"=>"$result","remark"=>"$remark");
	}
	echo json_encode(array('rows'=>$items));
}
elseif($act=='view_resource'){
	if($x==1){ //plo/rno/enginer
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
	
		$sql.=empty($find)?"select top 3 nip, nm_peg from SPG_DATA_CURRENT where isnull(sts_pensiun,1)=0)
		":
		"select top 3 x.nip,y.nm_peg from t_resource_assign x
			left join spg_data_current y on y.nip=x.nip
			left join m_project a on a.id=x.proj_id
			where isnull(y.sts_pensiun,1)=0 and x.role_id in ($find) and x.flag_assign=1
		and a.lke_id = '$area_pemohon'";
		
	
	$resultx=$dbproj->Execute($sql);
	$items=array();
	while($row=$resultx->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$items[]=array("nip_dt"=>"$nip","nm_dt"=>"$nm_peg","result_dt"=>"","status_dt"=>"","remark_dt"=>"");
	}
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='list_result'){
	die(
	json_encode(array(
		array("result_dt"=>"CANCLE","result_name"=>"Cancel"),
		array("result_dt"=>"DONE","result_name"=>"Passed"),
		array("result_dt"=>"FAILED","result_name"=>"Failed"),
		array("result_dt"=>"NOT DONE","result_name"=>"Not Complate"),
		array("result_dt"=>"NOTE","result_name"=>"Complate-Note")
		)
	)
	);
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
		echo json_encode(array('errorMsg'=>"Maaf, batas waktu penginputan jam 00:01 s/d 18:00:00!"));
		die();
	}
	
$isAdminOrderCarpool=$db->getOne("select top 1 wia_inquiryaccess  from wf_inquiry_access where wia_accessname='$wia_accessname' and wia_inquiryname='AdminOrderCarpool'")=='Ya'?true:false;

if(!$isAdminOrderCarpool){ 

	$cek_req_sebelum=$dbproj->GetOne("SELECT COUNT(*) FROM [dbo].[M_CARPOOL] WHERE DT_COORD='$CreateBy' and [DATE] >= '2016-02-29' AND convert(varchar, [date], 105) <> '$req_date' AND
	case isnull(status,'') when '1' then 'JALAN' when '5' then 'VERIFIED' when '2' then 'PULANG' when '3' then 'CLOSED' when '111' then 'REJECT' 
	when 112 then 'CANCEL' else 'BELUM JALAN' end in ('BELUM JALAN','VERIFIED','JALAN','PULANG')
	and isnull(TAMBAH_DURASI,0) < DateDiff (Day,[date],GETDATE())
	");
	//$ara_id=$_REQUEST['lke_id'];
	if($cek_req_sebelum >= 1){
		echo json_encode(array('errorMsg'=>"Pastikan request sebelumnya sudah closed (yg sudah jalan)/cancel(yg belum jalan)!"));
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
	//if(empty($site_name)){
		//echo json_encode(array('errorMsg'=>"Sorry, this field site is required!"));
		//die();
	//}
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
	$_txt=array();
	foreach($HTTP_POST_VARS as $key=>$val){
		if(!preg_match("#^(nip_dt|site_id)#",$key)){
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
			}elseif(preg_match("#^(jum_site)#",$key)){
				$columns .="$key,";
				$values .= "$val,";
				$$key=$val;
			}else{					
				$columns .="$key,";
				$values .="'$val',";
				if(preg_match("#^(lke_id)#",$key))
					$$key=$val;
			}
		}
		else if(preg_match("#^(site_id|nip_dt)#",$key)){
			$$key=$val;
		}
	}
	$columns = preg_replace("/,$/","",$columns);
	$values	 = preg_replace("/,$/","",$values);
	$check_id=$dbproj->getOne("select count(*) from m_carpool where ocs_id='$ocs_id'");
	if ($check_id != 0){
		echo json_encode(array('errorMsg'=>"Error: Duplicate Data"));
		die();
	}	
	else{
		$next_id=$f->maxproj_id(array("table"=>"m_carpool"))+1;
	}
	$arrsql=array();
	if(count($site_id)>0){
		$res=$dbproj->Execute("select site_name from t_milestone where id in (".implode(",",$site_id).")");
		$site_name="";
	
		while($row=$res->Fetchrow()){
			$site_name.=empty($site_name)?$row['site_name']:",".$row['site_name'];
		}
	}
	if(count($nip_dt)>0){
		$res=$dbproj->Execute("select nm_peg as name_dt from spg_data_current where nip in ('".implode("','",$nip_dt).")");
		$name_dt="";
	
		while($row=$res->Fetchrow()){
			$name_dt.=empty($name_dt)?$row['name_dt']:",".$row['name_dt'];
		}
	}
	$arrsql[]="SET IDENTITY_INSERT m_carpool ON";
	$arrsql[]="insert into m_carpool (id,$columns,nip_dt,name_dt,site_name,dt_coord,status,create_date) 
		values ($next_id,$values,'".(implode(",",$nip_dt))."','$name_dt','$site_name','$CreateBy','4',GETDATE())";
	$arrsql[]="SET IDENTITY_INSERT m_carpool OFF";
	
	$ocs_site_id=$f->maxproj_id(array("table"=>"m_carpool_site"))+1;
	for($i=(count($site_id)==0?0:count($site_id)-1);$i>=0;$i--,$ocs_site_id++){
		
		$arrsql[]="insert into m_carpool_site (id,ocs_id, proj_id, site_id, sow_id, lke_id) 
			values ($ocs_site_id,'$ocs_id',$proj_id,'$site_id[$i]','$sow_id','$lke_id')";
			
		for($j=count($nip_dt)-1;$j>=0;$j--){
			$arrsql[]="insert into m_carpool_detail (OCS_ID,NIP,OCS_SITE_ID,UPDATE_DATE) 
				values ('$ocs_id','".$nip_dt[$j]."',$ocs_site_id,getdate())";
		}
	}
	
	if(!$isAdminOrderCarpool){
		$nip_app=$dbproj->getOne("select pm_id from m_project where id='$proj_id'");
		$app_id=1;
	}
	else //bypass by luckman for carpool proposal
	{
		$detailuser=$f->detailPegawai($login_nip);
		$nip_app=$dbproj->getOne("select app_nip from m_approvel where app_no_urut='2' and app_jenis='REQ_CARPOOL' and cmp_id='".$detailuser['cmp_id']."'");
		$app_id=$dbproj->getOne("select app_id from m_approvel where app_no_urut='2' and app_jenis='REQ_CARPOOL' and cmp_id='".$detailuser['cmp_id']."'");
		$arrsql[]="update m_carpool set pm_approve='1',proposal_type='MANAGEMENT',
		pm_approve_date=GETDATE() where ocs_id='$ocs_id'";	
	}
	$arrsql[]="insert into t_approvel_carpool (nip_app,ocs_id,status_app,app_id) values ('$nip_app','$ocs_id','OPEN','$app_id')";
	
	/*die(json_encode(array('errorMsg'=>"Error: ".json_encode($arrsql))));*/
			
	$dbproj->BeginTrans();
	for($i=0;$i<count($arrsql);$i++){
	//	$f->pre($arrsql[$i]);
		$result=$dbproj->Execute($arrsql[$i]);
		if (!$result){
			print $dbproj->ErrorMsg();
			$dbproj->RollbackTrans();
			die(json_encode(array('errorMsg'=>"Error: ".$arrsql[$i])));
			//die();
		}
	}
	$dbproj->CommitTrans();
	$f->insert_log("INSERT M_CARPOOL $title. ocs_id: ".($ocs_id),json_encode($arrsql));
	echo json_encode(array('success'=>true));
}
elseif($act=='do_update'){
	$pm_approve=$dbproj->getOne("select isnull(pm_approve,0) from m_carpool where ocs_id='$ocs_id'");
	$finance_approve=$dbproj->getOne("select isnull(finance_approve,0) from m_carpool where ocs_id='$ocs_id'");
	
	if($status=='PULANG'||$status=='JALAN'||($pm_approve=='0' && $finance_approve=='0')){
		// if($check>=1){
			// echo json_encode(array('errorMsg'=>"Jika status sudah disetujui tidak bisa diubah!"));
			// die();
		// }
		//$type=$dbproj->getOne("select type from m_sow where sow_id='$sow_id'");
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
			if(!preg_match("#^(ocs_id|nip_dt|proj_id|site_id|sow_name|lke_id)#",$key)){
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
		
		$result=$dbproj->Execute("update m_carpool set $list where ocs_id='$ocs_id'");	
		if($pm_approve=='1' && $finance_approve=='1'){
			$nip_notverify=$dbproj->getOne("select --s.ocs_id, s.site_id, 
d.nip -- , d.RESULT  
from m_carpool_site s
left join m_carpool_detail d on d.OCS_ID=s.OCS_ID and d.OCS_SITE_ID=s.ID
where s.ocs_id like '$ocs_id' and (d.result not in (1,2,3,4,5) or d.RESULT is null)");
			if(empty($nip_notverify)){
				$sts=$status=='JALAN'?"5":"3";
				$dbproj->Execute("update m_carpool set status='$sts' where ocs_id='$ocs_id'");
			}
		}
		if ($result){
				echo json_encode(array('success'=>true));
			} else {
				echo json_encode(array('errorMsg'=>"Error: update m_carpool set $list where ocs_id='$ocs_id'"));
			}
	}
	else{
		echo json_encode(array('errorMsg'=>"Maaf status harus dalam kondisi JALAN/PULANG, silahkan hubungi carpool admin!"));
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
	$pm_approve			=	$dbproj->getOne("select isnull(pm_approve,0) from m_carpool where ocs_id='$id'");
	$finance_approve	=	$dbproj->getOne("select isnull(finance_approve,0) from m_carpool where ocs_id='$id'");
	
	if ($pm_approve=='1' || $finance_approve=='1'){
		echo json_encode(array('errorMsg'=>"Request sudah di setujui, tidak boleh dihapus!"));
		die();
	}

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
	
	/*$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	$q=explode(",",$q);
	//echo json_encode($q);
	for($i=0;$i<count($q);$i++){
		if($i>0) $sql.="
		union
		";
		$_q=trim($q[$i]);*/
		
		
	$w_proj=empty($proj_id)?"":" and x.proj_id=$proj_id";
	
		
		$sql.=empty($find)?"select nip, nm_peg from SPG_DATA_CURRENT where isnull(sts_pensiun,1)=0 --and (nip like '%$_q%' or nm_peg like '%$_q%')
		":
		"select x.nip,y.nm_peg from t_resource_assign x
			left join spg_data_current y on y.nip=x.nip
			left join m_project a on a.id=x.proj_id
			where isnull(y.sts_pensiun,1)=0 and x.role_id in ($find) and x.flag_assign=1 and a.pm_id='$pm_nip' $w_proj
		and a.lke_id = '$area_pemohon' --and (x.nip like '%$_q%' or y.nm_peg like '%$_q%')";
		
	//}
	//echo htmlentities($sql);
	$resultx=$dbproj->Execute($sql);
	$items=array();
	while($row=$resultx->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$items[]=array("nip"=>"$nip","nm_peg"=>"$nm_peg","result_dt"=>"");
	}
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='combo_project'){
	$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	$sql="select id, proj_code, proj_name,lke_id from project_running
	where pm_id='$pm_nip' and (proj_code like '%$q%' or proj_name like '%$q%')";
	$result_user=$dbproj->Execute($sql);
	$items=array();
	while($row=$result_user->Fetchrow()){
		$items[]=$row;
	}
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='combo_project1'){
	$cmp_id=$db->getOne("select cmp_id from spg_data_current where nip='$login_nip'");
	$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	$sql="select id, proj_code, proj_name,lke_id from project_running
	where id in ('237','239','241','242','243','240') and cmp_id='$cmp_id' and (proj_code like '%$q%' or proj_name like '%$q%')";
	$resultx=$dbproj->Execute($sql);
	$items=array();
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
	//$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	$sql="
	select * from (
	select a.proj_id, a.sow_id, a.resource_id,
	case when isnull(a.resource_id,'')='' then a.site_name else (select nm_peg from spg_data_current where nip=a.resource_id) 
	end as site_name,a.id as site_id
	from t_milestone a
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