<?php 
session_start();
include_once($DOCUMENT_ROOT."/s/config.php");
$nip_sipeg=$_SESSION['sipeg_nip_pegawai'];	
$login_nip=(empty($nip_sipeg))? $login_nip:$nip_sipeg;
//if($login_nip=="021450")$login_nip="032197";
$lke_id=$db->getOne("select lke_id from spg_data_current where nip='$login_nip'");
if($act=='view'){
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 20;
	$offset = ($page-1)*$rows;
	$result = array();
	
	/*$f_start_date	=	$_REQUEST['f_start_date'];
	$f_start_date	= (empty($f_start_date))?date("Y-m-d")
	:str_replace('/','-',$f->convert_date($f_start_date,1,'/'));
	$f_end_date		=	$_REQUEST['f_end_date'];
	$f_end_date	= (empty($f_end_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_end_date,1,'/'));
	*/
	$find_val=$_REQUEST['value'];
	$find_name=$_REQUEST['name'];
	if(empty($find_val)||empty($find_name)){
		echo json_encode(array('rows'=>array()));
		die();
	}
	//jika PM terfilter hanya projectnya saja.
	
	//$is_pm=$wia_accessname=="DefaultPM"?$dbproj->getOne("select count(*) from M_PROJECT_MANAGER where nip='$login_nip'"):0;
	
	$find_str=($find_name=='all' or empty($find_name))?
	"where 
	-- date between '$f_start_date' and '$f_end_date' and 
	(ocs_id like '%$find_val%' or date like '%$find_val%' 
	or dt_name like '%$find_val%' or no_polisi like '%$find_val%' 
	or nm_driver like '%$find_val%'
	)":
	 "where 
	-- date between '$f_start_date' and '$f_end_date' and 
	$find_name like '%$find_val%'";
	
	$sql="
	select 
	mp.OCS_ID,a.date,a.KM_ACUAN,mp.id,k.NO_POLISI,
	(select b.nm_peg from spg_data_current b where b.nip=a.nip_dt) as dt_name,
	mp.KM_START,mp.BBM,mp.ETOLL,mp.UPDATE_BY,
	(select nm_peg from spg_data_current where nip=mp.nip_driver) as nm_driver
	from t_carpool mp 
	left join m_carpool a on mp.ocs_id=a.ocs_id 
	left join lmt_hcis.dbo.INV_DATA_KENDARAAN k on k.KODE_KENDARAAN=mp.CAR_NUMBER
	where a.lke_id in (select area from m_carpool_admin where admin='$login_nip') and a.status=1";
	
	$_sql="select count(*) from ($sql) as x $find_str";
	$result["total"] = $dbproj->getOne($_sql);
	//if($t=="y") die(nl2br($sql));
	
	$_sql="select * from ($sql) as x $find_str order by  ocs_id desc";
	if($t=="y") die(nl2br($_sql));
	
	$resultx=$dbproj->SelectLimit($_sql,$rows,$offset);
	$items=array();
	while($row=$resultx->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$date=$f->convert_date($date,1);
		$etoll=number_format($etoll,0,',','');
		$bbm=number_format($bbm,0,',','');
		$items[]=array("ocs_id"=>$ocs_id,"crp_id"=>"$ocs_id<br><span style=font-size:8px;color:brown;>$date</span>","id"=>$id,
		"no_polisi"=>"$no_polisi<span style=font-size:8px;color:brown;><br>$nm_driver<br>$dt_name</span>","bbm"=>"$bbm",
		"km_start"=>"$km_start","etoll"=>"$etoll",
		"update_by"=>"$update_by"		
		);
	}
	
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='view1'){
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 1;
	$offset = ($page-1)*$rows;
	//$result = array();
	
	/*$f_start_date	=	$_REQUEST['f_start_date'];
	$f_start_date	= (empty($f_start_date))?date("Y-m-d")
	:str_replace('/','-',$f->convert_date($f_start_date,1,'/'));
	$f_end_date		=	$_REQUEST['f_end_date'];
	$f_end_date	= (empty($f_end_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_end_date,1,'/'));
	*/
	$find_val=$_REQUEST['value'];
	$find_name=$_REQUEST['name'];
	if(empty($find_val)||empty($find_name)){
		echo json_encode(array('msg'=>'filter kosong'));
		die();
	}
	//jika PM terfilter hanya projectnya saja.
	
	//$is_pm=$wia_accessname=="DefaultPM"?$dbproj->getOne("select count(*) from M_PROJECT_MANAGER where nip='$login_nip'"):0;
	
	$find_str=($find_name=='all' or empty($find_name))?
	"where ocs_id like '%$find_val'":
	 "where $find_name like '%$find_val%'";
	
	$sql="
	select 
	mp.OCS_ID,a.date,a.KM_ACUAN,mp.id,k.NO_POLISI,
	(select b.nm_peg from spg_data_current b where b.nip=a.nip_dt) as dt_name,
	mp.KM_START,mp.BBM,mp.ETOLL,mp.UPDATE_BY,
	(select nm_peg from spg_data_current where nip=mp.nip_driver) as nm_driver
	from t_carpool mp 
	left join m_carpool a on mp.ocs_id=a.ocs_id 
	left join lmt_hcis.dbo.INV_DATA_KENDARAAN k on k.KODE_KENDARAAN=mp.CAR_NUMBER
	where a.lke_id in (select area from m_carpool_admin where admin='$login_nip') and a.status=1";
	
	$_sql="select count(*) from ($sql) as x $find_str";
	//$result["total"] = $dbproj->getOne($_sql);
	//if($t=="y") die(nl2br($sql));
	
	$_sql="select * from ($sql) as x $find_str order by  ocs_id desc";
	if($t=="y") die(nl2br($_sql));
	
	$resultx=$dbproj->SelectLimit($_sql,$rows,$offset);
	$items=array();
	while($row=$resultx->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$date=$f->convert_date($date,1);
		$etoll=number_format($etoll,0,',','');
		$bbm=number_format($bbm,0,',','');
		$items=array("ocs_id"=>$ocs_id,"crp_id"=>"$ocs_id<br><span style=color:brown;><b>Tanggal</b> : $date</span>","id"=>$id,
		"no_polisi"=>"$no_polisi<br><span style=color:brown;><b>driver</b> : $nm_driver&nbsp;&nbsp;<b>DT</b> : $dt_name</span>","bbm"=>"$bbm",
		"km_start"=>"$km_start","etoll"=>"$etoll",
		"update_by"=>"$update_by"		
		);
	}
	
	//$result["rows"] = $items;
	echo json_encode($items);
}elseif($act=='update'){
	$arrsql=array();
	$ins=$_REQUEST['updates'];
	//$tgl_dibayar=$f->convert_date($tgl_dibayar,1,"/");
	for($i=count($ins)-1;$i>=0;$i--){
		$columns="";$values="";$updates="";
		unset($ID);unset($OCS_ID);
		foreach($ins[$i] as $key=>$val){
			if(!preg_match("#^(date|crp_id|dt_name|no_polisi|nm_driver|update_by)#",$key)){
				if(preg_match("#^(ocs_id)#",$key)||$key=="id"){
					$key=strtoupper($key);
					$$key=$val;
				}
				else{
					$updates.="$key='$val',";
				}
			}
		}
		$updates	 = preg_replace("/,$/","",$updates);
		
		$arrsql[]="
		update t_carpool set $updates,update_by='$login_nip',
		update_date=GETDATE(),paid_date_bbm=GETDATE(),paid_by_bbm='$login_nip' 
		where ocs_id='$OCS_ID' and id=$ID";
			
	}
	/*$ins=$_REQUEST['deletes'];
	for($i=count($ins)-1;$i>=0;$i--){
		unset($ID);unset($OCS_ID);
		foreach($ins[$i] as $key=>$val){
			if(preg_match("#^(ocs_id|id)#",$key)){
				$key=strtoupper($key);
				$$key=$val;
			}
		}
		$arrsql[]="delete t_carpool where ocs_id='$OCS_ID' and id=$ID";			
	}*/
	
	$res=array();
	$dbproj->BeginTrans();
	for($i=0;$i<count($arrsql);$i++){
	//	$f->pre($arrsql[$i]);
		$result=$dbproj->Execute($arrsql[$i]);
		if (!$result){
			$err=$dbproj->ErrorMsg();
			$dbproj->RollbackTrans();
			$res['msg']=array("Error: $err ".$arrsql[$i]);
			die(json_encode($res));
		}
	}
	$dbproj->CommitTrans();
	$f->insert_log("INSERT T_CARPOOL $title. ocs_id: ".($ocs_id),json_encode($arrsql));
	/*
	$res['msg']=$arrsql;*/
	echo json_encode($res);
}elseif($act=='update1'){
	$arrsql=array();
	$ins=$_REQUEST['updates'];
	//$tgl_dibayar=$f->convert_date($tgl_dibayar,1,"/");
	//for($i=count($ins)-1;$i>=0;$i--){
		$columns="";$values="";$updates="";
		unset($ID);unset($OCS_ID);
		foreach($ins as $key=>$val){
			if(!preg_match("#^(date|crp_id|dt_name|no_polisi|nm_driver|update_by)#",$key)){
				if(preg_match("#^(ocs_id)#",$key)||$key=="id"){
					$key=strtoupper($key);
					$$key=$val;
				}
				else{
					$updates.="$key='$val',";
				}
			}
		}
		$updates	 = preg_replace("/,$/","",$updates);
		
		$arrsql[]="
		update t_carpool set $updates,update_by='$login_nip',
		update_date=GETDATE(),paid_date_bbm=GETDATE(),paid_by_bbm='$login_nip' 
		where ocs_id='$OCS_ID' and id=$ID";
			
	//}
	$res=array();
	$dbproj->BeginTrans();
	for($i=0;$i<count($arrsql);$i++){
	//	$f->pre($arrsql[$i]);
		$result=$dbproj->Execute($arrsql[$i]);
		if (!$result){
			$err=$dbproj->ErrorMsg();
			$dbproj->RollbackTrans();
			$res['msg']=array("Error: $err ".$arrsql[$i]);
			die(json_encode($res));
		}
	}
	$dbproj->CommitTrans();
	$f->insert_log("INSERT T_CARPOOL $title. ocs_id: ".($ocs_id),json_encode($arrsql));
	
	echo json_encode($res);
}

?>