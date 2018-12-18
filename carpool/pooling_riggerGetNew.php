<?php
session_start();
include("$DOCUMENT_ROOT/s/config.php");
$nip_sipeg=$_SESSION['sipeg_nip_pegawai'];
$login_nip=(empty($nip_sipeg))? $login_nip:$nip_sipeg;
$lke_id=$f->lokasi_project($login_nip);
if($act=='view'){
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
	$sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'PRG_ID';
	$order = isset($_POST['order']) ? strval($_POST['order']) : 'desc';
	
	$offset = ($page-1)*$rows;
	
	$result = array();
	
	
	$sql="select a.id,a.prg_id,a.nip+' - '+b.nm_peg as [name],convert(varchar(10),a.req_date,105) as req_date,c.proj_name,
	case when a.status='APPROVED' then 'APPROVED' 
	when a.status='CANCEL' then '<font color=red> CANCEL BY</font><br>'+ isnull(e.nm_peg,'-') + '<br> <font color=red>'+ isnull(a.reason_reject,'-') + '</font>'
	when a.status='CLOSED' then '<font color=green> CLOSED</font>'
	when a.status='REJECT' then '<font color=red> REJECT BY</font><br>'+ isnull(e.nm_peg,'-') + '<br> <font color=red>'+ isnull(a.reason_reject,'-') + '</font>'
	else '<font color=#d1d1d1> WAITING</font><br>'+ d.nm_peg end as next_approval,
	(select  sum(amount) from m_pooling_rigger_cost where prg_id=a.prg_id) as sewa_motor,
	(select  count(*) from m_pooling_rigger_site where prg_id=a.prg_id) as total_site,
	a.area_site,a.transport
	from m_pooling_rigger a
	left join spg_data_current b on b.nip=a.nip 
	left join m_project c on c.id=a.proj_id
	left join spg_data_current d on d.nip=a.next_approval
	left join spg_data_current e on e.nip=a.reject_by
	where a.create_by='$login_nip' and (a.nip like '%$value%' or b.nm_peg like '%$value%')
	";
	
	$result["total"] = $dbproj->getOne("select count(*) from ($sql) as x");
	$resultx=$dbproj->SelectLimit("$sql order by a.id desc",$rows,$offset);
	$items=array();
	while($row=$resultx->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		
		$check_paid=$dbproj->getOne("select sum(payment) from M_POOLING_RIGGER_PAY where prg_id='$prg_id'");
		$date_paid=$dbproj->getOne("select top 1 convert(varchar(10),payment_date ,103) from M_POOLING_RIGGER_PAY where prg_id='$prg_id'");
		$next_approval=($check_paid>=1)?"<font color=blue>PAID <br> $date_paid</font>":$next_approval;
		
		$sewa_motor=number_format($sewa_motor,0,".",",");
		$items[]=array("prg_id"=>"$prg_id","name"=>"$name","req_date"=>"$req_date","next_approval"=>"$next_approval","proj_name"=>"$proj_name",
		"sewa_motor"=>"$sewa_motor","id"=>"$prg_id","area_site"=>"$area_site","transport"=>"$transport",
		"detail"=>"[<a href=javascript:void(0) OnClick=detailpool('$id')>DETAIL</a>] ($total_site)");
	}
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='view_app'){
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
	$sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'PRG_ID';
	$order = isset($_POST['order']) ? strval($_POST['order']) : 'desc';
	
	$offset = ($page-1)*$rows;
	
	$result = array();
	
	$sql="select a.id,a.prg_id,a.nip+' - '+b.nm_peg as [name],convert(varchar(10),a.req_date,105) as req_date,c.proj_name,
	'<font color=#d1d1d1> Waiting</font><br>'+ d.nm_peg as next_approval,
	(select  sum(amount) from m_pooling_rigger_cost where prg_id=a.prg_id) as sewa_motor,
	(select  count(*) from m_pooling_rigger_site where prg_id=a.prg_id) as total_site,a.transport,a.area_site
	from m_pooling_rigger a
	left join spg_data_current b on b.nip=a.nip 
	left join m_project c on c.id=a.proj_id
	left join spg_data_current d on d.nip=a.next_approval where a.next_approval='$login_nip' 
	and isnull(a.status,'OPEN') not in ('APPROVED','REJECT','CANCEL','CLOSED')
	and (select  count(*) from m_pooling_rigger_site x where x.prg_id=a.prg_id and x.STATUS IS NOT NULL) = 
	(select  count(*) from m_pooling_rigger_site where prg_id=a.prg_id)
	";
	
	$result["total"] = $dbproj->getOne("select count(*) ($sql)");
	$resultx=$dbproj->SelectLimit("$sql order by a.id desc",$rows,$offset);
	$items=array();
	while($row=$resultx->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$transport=($transport=='DT')?"WITH DT":"$transport";
		$sewa_motor=number_format($sewa_motor,0,".",",");
		$items[]=array("prg_id"=>"$prg_id","name"=>"$name","req_date"=>"$req_date","next_approval"=>"$next_approval","proj_name"=>"$proj_name",
		"sewa_motor"=>"$sewa_motor","id"=>"$prg_id","transport"=>"$transport","area_site"=>"$area_site",
		"detail"=>"[<a href=javascript:void(0) OnClick=detailpool('$id')>DETAIL</a>] ($total_site)");
	}
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='view_detail'){
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
	$sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'PRG_ID';
	$order = isset($_POST['order']) ? strval($_POST['order']) : 'desc';
	
	$offset = ($page-1)*$rows;
	
	$result = array();
	$prg_id=$dbproj->getOne("select prg_id from m_pooling_rigger where id='$id'");
	
	$sql="select site_id,isnull(status,'OFF') as status,isnull(allowance,0) as allowance,id,site_name,type_site,site_done_ke,reason 
	from m_pooling_rigger_site
	where prg_id='$prg_id'
	";
	
	$result["total"] = $dbproj->getOne("select count(*) ($sql)");
	$resultx=$dbproj->SelectLimit("$sql",$rows,$offset);
	$items=array();
	while($row=$resultx->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$statusx=($status=='DONE')?$status:"$status <br> <font color=red>$reason</font>";
		$reason=($status=='DONE')?"":"$reason";
		$allowance=number_format($allowance,0,".",",");
		$items[]=array("site_id"=>"$site_id","status"=>"$status","allowance"=>"$allowance","site_name"=>"$site_name","id"=>"$id","type_site"=>"$type_site",
		"site_ke"=>"$site_done_ke","statusx"=>"$statusx","reason"=>"$reason");
	}
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='combo_rigger'){
	$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	$sql="select top 10 x.nip,a.nm_peg from t_resource_assign x 
	left join spg_data_current a on a.nip=x.nip
	left join m_project b on b.id=x.proj_id
	where b.lke_id='$lke_id' and 
	isnull(x.flag_assign,0)=1 and x.role_id in ('8011030102010400000','6021030102010400000') and (x.nip like '%$q%' or a.nm_peg like '%$q%')";
	$resx=$dbproj->Execute($sql);
	$items=array();
	while($row=$resx->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$sewa_motorx	=$dbproj->getOne("SELECT isnull(sewa_motor,0) FROM [dbo].[m_pooling_rigger_set] WHERE [NIP] = '$nip'");
		$sewa_motorx	=$sewa_motorx<=0?0:$sewa_motorx;
		$gapok			=$db->getOne("SELECT cast(dbo.decodeBin64(GAJI_POKOK_FULL) as money) FROM [dbo].[PAY_GAPOK_PEGAWAI] WHERE [NIP] = '$nip'");
		$sewa_motor		=$gapok<=3000000?$sewa_motorx:0;	
		$type			=$dbproj->getOne("SELECT type FROM [dbo].[m_pooling_rigger_set] WHERE [NIP] = '$nip'");
		$sewa_motor		=$type=='ALL-IN'?0:$sewa_motor;
		$items[]=array("sewa_motor"=>"$sewa_motor","nip"=>"$nip","nm_peg"=>"$nm_peg");
	}
	$result["rows"] = $items;
	echo json_encode($result);
}
elseif($act=='combo_project'){
	$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	$sql="select top 100 a.proj_code,a.proj_name,x.sow_id,b.sow_name, a.proj_name+', '+b.sow_name as [text],x.proj_id  
	from m_project_sow x 
	left join m_project a on a.id=x.proj_id
	left join m_sow b on b.sow_id=x.sow_id
	where isnull(a.status,'RUNNING')='RUNNING' and a.lke_id='$lke_id' and 
	(a.proj_code like '%$q%' or a.proj_name like '%$q%' or b.sow_name like '%$q%')
	
	order by x.proj_id desc";
	$resx=$dbproj->Execute($sql);
	$items=array();
	while($row=$resx->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$items[]=array("proj_code"=>"$proj_code","proj_name"=>"$proj_name","sow_id"=>"$sow_id","sow_name"=>"$sow_name","text"=>"$text","proj_id"=>"$proj_id");
	}
	$result["rows"] = $items;
	echo json_encode($result);
}
elseif($act=='combo_site'){
	$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	//$r = explode(",",$q);
	//$q=$r[count($r)-1];
	$sql="select top 50 site_name, 
	upper(replace(site_name,' ',''))+cast(proj_id as varchar(25))+SOW_ID as site_id
	from m_site_work where proj_id='$proj_id' and sow_id='$sow_id' and 
	(site_name like '%$q%')";
	$resx=$dbproj->Execute($sql);
	$items=array();
	while($row=$resx->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$items[]=array("site_name"=>"$site_name","site_id"=>"$site_id");
	}
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='do_add'){
	$req_date=$f->convert_date($req_date,1,"/");
	$arrsql=array();
	$nip_atasan=$dbproj->getOne("select pm_id from m_project where id='$proj_id'");
	$rpm=$dbproj->getOne("select rpm_id from m_project where id='$proj_id'");
	$lke_id=$dbproj->getOne("select lke_id from m_project where id='$proj_id'");
	
	$group=$dbproj->getOne("select [group] from m_setting_allowance_rigger_group where proj_id='$proj_id'");
	if(empty($group)){
		echo json_encode(array('errorMsg'=>"Allowance rigger belum di aktifkan, silahkan bungungi IT segera!"));
		die();
	}
	
	$nip_atasan=empty($rpm)?$nip_atasan:$rpm;
	$prg_id=$f->generate_nomorkolom("lmt_project.dbo.m_pooling_rigger","PRG_ID","PRG");
	
	$sewa_motor=$dbproj->getOne("select isnull(allowance,0) from m_setting_allowance_rigger where [group]='$group' and area='$area_site' and transport='$transport'");
	
	$arrsql[]="insert into m_pooling_rigger 
	(prg_id,nip,proj_id,req_date,create_date,create_by,site_list,sow_id,next_approval,lke_id,area_site,transport) 
	values ('$prg_id','$nip','$proj_id','$req_date',getdate(),'$login_nip','$site_name[0]',
	'$sow_id','$nip_atasan','$lke_id','$area_site','$transport')";
	
	$arrsql[]="insert into m_pooling_rigger_cost (prg_id,type,amount,update_by,update_date) 
	values ('$prg_id','SEWA_MOTOR','$sewa_motor','$login_nip',getdate())";
	
	$ins_site=$_REQUEST['site_name'];
	$i=0;
	foreach($ins_site as $key=>$val){
		if(!empty($site[$i])){
			if(!empty($site_name[$i])){
				$arrsql[]="insert into m_pooling_rigger_site (prg_id,site_id,create_by,create_date,site_name) 
				values ('$prg_id','$site[$i]','$login_nip',getdate(),'$site_name[$i]')";
			}
		}
		$i++;
		
	}
	
	$dbproj->BeginTrans();
	for($i=0;$i<count($arrsql);$i++){
		$result=$dbproj->Execute($arrsql[$i]);
		if (!$result){
			$dbproj->RollbackTrans();
			echo json_encode(array('success'=>true));
			die();
		}
	}
	$dbproj->CommitTrans();
	$f->insert_log("INSERT m_pooling_rigger prg_id: ".($prg_id),json_encode($arrsql));
	
	echo json_encode(array('success'=>true));
		
}elseif($act=='do_addDetail'){
	
	$proj_id	=$dbproj->getOne("select proj_id from m_pooling_rigger where prg_id='$prg_id'");
	$group		=$dbproj->getOne("select [group] from m_setting_allowance_rigger_group where proj_id='$proj_id'");
	
	if(empty($group)){
		echo json_encode(array('errorMsg'=>"Allowance rigger belum di aktifkan, silahkan bungungi IT segera!"));
		die();
	}

	$check_status		=$dbproj->getOne("select status from m_pooling_rigger where prg_id='$prg_id'");
	if($check_status=='REJECT' || $check_status=='APPROVED'){
		echo json_encode(array('errorMsg'=>"ID: $prg_id status PAYMENT/REJECT, tidak boleh tambah site!"));
		die();
	}
	if(($status=='NOT DONE' OR $status=='FAILED') AND empty($reason)){
		echo json_encode(array('errorMsg'=>"Status not done atau failed wajib disertai alasan!"));
		die();
	}
	$arrsql=array();
	$gapok				=$db->getOne("SELECT cast(dbo.decodeBin64(GAJI_POKOK_FULL) as money) FROM [dbo].[PAY_GAPOK_PEGAWAI] 
							WHERE [NIP] = '$nip'");
	$allowance			=$dbproj->getOne("select allowance from m_pooling_rigger_set where nip='$nip'");
	$allowance			=$status=='DONE'?0:0;
	$allowance			=$gapok<=3000000?0:0;
	
	$arrsql[]="insert into m_pooling_rigger_site (prg_id,site_id,create_by,create_date,status,allowance,site_name,reason) 
	values ('$prg_id','$site_id','$login_nip',getdate(),'$status','$allowance','$site_name','$reason')";
	
	$dbproj->BeginTrans();
	for($i=0;$i<count($arrsql);$i++){
		$result=$dbproj->Execute($arrsql[$i]);
		if (!$result){
			$dbproj->RollbackTrans();
			echo json_encode(array('success'=>true));
			die();
		}
	}
	$dbproj->CommitTrans();
	$f->insert_log("INSERT m_pooling_rigger_site,($login_nip)");
	
	echo json_encode(array('success'=>true));
		
}
elseif($act=='do_destroy'){
	$check_status		=$dbproj->getOne("select status from m_pooling_rigger where prg_id='$prg_id'");
	if($check_status=='REJECT' || $check_status=='APPROVED' || $check_status=='CANCEL' || $check_status=='CLOSED'){
		echo json_encode(array('errorMsg'=>"ID: $prg_id status PAYMENT/REJECT/CANCEL, tidak boleh DIHAPUS!"));
		die();
	}
	
	$check_pay=$dbproj->getOne("select count(*) from M_POOLING_RIGGER_PAY where prg_id='$prg_id'");
	if($check_pay>=1){
		echo json_encode(array('errorMsg'=>"ID: $prg_id status PAYMENT, tidak boleh dihapus!"));
		die();
	}
	$arrsql=array();
	$prg_id=$_REQUEST['prg_id'];
	$arrsql[]="delete m_pooling_rigger where prg_id='$prg_id'";
	$arrsql[]="delete m_pooling_rigger_cost where prg_id='$prg_id'";
	$arrsql[]="delete m_pooling_rigger_site where prg_id='$prg_id'";

	$dbproj->BeginTrans();
	for($i=0;$i<count($arrsql);$i++){
		$result=$dbproj->Execute($arrsql[$i]);
		if (!$result){
			$dbproj->RollbackTrans();
			echo json_encode(array('success'=>true));
			die();
		}
	}
	$dbproj->CommitTrans();
	$f->insert_log("DELETE m_pooling_rigger prg_id: $prg_id");
	
	echo json_encode(array('success'=>true));
		
}elseif($act=='do_cancel'){
	
	$check_status		=$dbproj->getOne("select status from m_pooling_rigger where prg_id='$prg_id'");
	$payment			=$dbproj->getOne("select sum(payment) from m_pooling_rigger_pay where prg_id='$prg_id'");
	if($check_status=='CLOSED' || $payment > 0){
		echo json_encode(array('errorMsg'=>"ID: $prg_id status CLOSED/PAID, tidak boleh DICANCEL!"));
		die();
	}
	
	$arrsql=array();
	$prg_id=$_REQUEST['prg_id'];
	$arrsql[]="update m_pooling_rigger set status='CANCEL',reject_by='$login_nip',reason_reject='$reason' where prg_id='$prg_id'";
	
	$dbproj->BeginTrans();
	for($i=0;$i<count($arrsql);$i++){
		$result=$dbproj->Execute($arrsql[$i]);
		if (!$result){
			$dbproj->RollbackTrans();
			echo json_encode(array('success'=>true));
			die();
		}
	}
	$dbproj->CommitTrans();
	$f->insert_log("CANCEL m_pooling_rigger prg_id: $prg_id");
	
	echo json_encode(array('success'=>true));
		
}elseif($act=='do_closed'){
	
	$check_status		=$dbproj->getOne("select status from m_pooling_rigger where prg_id='$prg_id'");
	if($check_status!='APPROVED'){
		echo json_encode(array('errorMsg'=>"PASTIKAN STATUS SUDAH DISETUJUI/ APPROVED!"));
		die();
	}
	
	$arrsql=array();
	$prg_id=$_REQUEST['prg_id'];
	$arrsql[]="update m_pooling_rigger set status='CLOSED' where prg_id='$prg_id'";
	
	$dbproj->BeginTrans();
	for($i=0;$i<count($arrsql);$i++){
		$result=$dbproj->Execute($arrsql[$i]);
		if (!$result){
			$dbproj->RollbackTrans();
			echo json_encode(array('success'=>true));
			die();
		}
	}
	$dbproj->CommitTrans();
	$f->insert_log("CANCEL m_pooling_rigger prg_id: $prg_id");
	
	echo json_encode(array('success'=>true));
		
}elseif($act=='do_approved'){
	$arrsql=array();
	$prg_id=$_REQUEST['prg_id'];
	
	if($login_nip=='032015'){
		$arrsql[]="update m_pooling_rigger set status='APPROVED' where prg_id='$prg_id'";
	}else{
		$arrsql[]="update m_pooling_rigger set next_approval='032015' where prg_id='$prg_id'";
	}

	$dbproj->BeginTrans();
	for($i=0;$i<count($arrsql);$i++){
		$result=$dbproj->Execute($arrsql[$i]);
		if (!$result){
			$dbproj->RollbackTrans();
			echo json_encode(array('success'=>true));
			die();
		}
	}
	$dbproj->CommitTrans();
	$f->insert_log("APPROVED m_pooling_rigger prg_id: $prg_id");
	
	echo json_encode(array('success'=>true));
		
}
elseif($act=='do_approvedx'){
	$arrsql=array();
	$rows=explode(",",$prg_id);
	foreach ($rows as $key=>$val){
		if($login_nip=='032015'){
			$arrsql[]="update m_pooling_rigger set status='APPROVED' where prg_id='$val'";
		}else{
			$arrsql[]="update m_pooling_rigger set next_approval='032015' where prg_id='$val'";
		}
	}
	$dbproj->BeginTrans();
	for($i=0;$i<count($arrsql);$i++){
		$result=$dbproj->Execute($arrsql[$i]);
		if (!$result){
			$dbproj->RollbackTrans();
			echo json_encode(array('success'=>true));
			die();
		}
	}
	$dbproj->CommitTrans();
	$f->insert_log("APPROVED m_pooling_rigger prg_id: $prg_id");
	
	echo json_encode(array('success'=>true));
		
}
elseif($act=='do_unapproved'){
	$arrsql=array();
	$prg_id=$_REQUEST['prg_id'];

	$arrsql[]="update m_pooling_rigger set status='REJECT' where prg_id='$prg_id'";

	$dbproj->BeginTrans();
	for($i=0;$i<count($arrsql);$i++){
		$result=$dbproj->Execute($arrsql[$i]);
		if (!$result){
			$dbproj->RollbackTrans();
			echo json_encode(array('success'=>true));
			die();
		}
	}
	$dbproj->CommitTrans();
	$f->insert_log("APPROVED m_pooling_rigger prg_id: $prg_id");
	
	echo json_encode(array('success'=>true));
		
}
elseif($act=='do_update'){
	$arrsql=array();
	$gapok				=$db->getOne("SELECT cast(dbo.decodeBin64(GAJI_POKOK_FULL) as money) FROM [dbo].[PAY_GAPOK_PEGAWAI] WHERE [NIP] = '$nip'");
	$all_in				=$dbproj->getOne("select count(*) from m_pooling_rigger_set where nip='$nip' and type='ALL-IN'");
	$allowance			=$dbproj->getOne("select allowance from m_pooling_rigger_set where nip='$nip'");
	$allowance			=$status=='DONE'?$allowance:0;
	$allowance			=$gapok<=3000000?$allowance:0;
	
	$lke_id		=$dbproj->getOne("select lke_id from m_pooling_rigger where prg_id='$prg_id'");
	$transport	=$dbproj->getOne("select transport from m_pooling_rigger where prg_id='$prg_id'");
	$area_site	=$dbproj->getOne("select area_site from m_pooling_rigger where prg_id='$prg_id'");
	$proj_id	=$dbproj->getOne("select proj_id from m_pooling_rigger where prg_id='$prg_id'");
	$group		=$dbproj->getOne("select [group] from m_setting_allowance_rigger_group where proj_id='$proj_id'");
	
	if(empty($group)){
		echo json_encode(array('errorMsg'=>"Allowance rigger belum di aktifkan, silahkan bungungi IT segera!"));
		die();
	}
	
	if(($status=='NOT DONE' OR $status=='FAILED') AND empty($reason)){
		echo json_encode(array('errorMsg'=>"Status not done or failed wajib disertai alasan!"));
		die();
	}
	
	if($site_ke==1){
		$allowance=$dbproj->getOne("select isnull(incentive_site1,0) from m_setting_allowance_rigger where [group]='$group' and area='$area_site' and transport='$transport'");
	}else if($site_ke==2){
		$allowance=$dbproj->getOne("select isnull(incentive_site2,0) from m_setting_allowance_rigger where [group]='$group' and area='$area_site' and transport='$transport'");
	}else if($site_ke==3){
		$allowance=$dbproj->getOne("select isnull(incentive_site3,0) from m_setting_allowance_rigger where [group]='$group' and area='$area_site' and transport='$transport'");
	}else if($site_ke==4){
		$allowance=$dbproj->getOne("select isnull(incentive_site4,0) from m_setting_allowance_rigger where [group]='$group' and area='$area_site' and transport='$transport'");
	}else{
		$allowance=0;
	}
	
	// if($lke_id=='ARA-000001'){
		// if($proj_id=='267'){
	
			// $allowance=0;
			
		// }else{
			// if($site_ke >4){
				// $allowance=$dbproj->getOne("select amount from m_pooling_rigger_allowance where lke_id='$lke_id' and type='$area_site' and 
				// transport='$transport' and [no]='4'
				// ");
			// }else{
				// $allowance=$dbproj->getOne("select amount from m_pooling_rigger_allowance where lke_id='$lke_id' and type='$area_site' and 
				// transport='$transport' and [no]='$site_ke'
				// ");
			// }
		// }
	// }
	
		if($site_type=='ROPE_ACCESS'){
			$allowance=150000;
		}else if($site_type=='WALL_MOUNTING'){
			$allowance=300000;
		}//else if($site_type=='HCPT10'){
			// if($site_ke==1){
				// $allowance=10000;
			// }else if($site_ke==2){
				// $allowance=15000;
			// }else if($site_ke==3){
				// $allowance=20000;
			// }else if($site_ke==4){
				// $allowance=25000;
			// }else{
				// $allowance=0;
			// }
		// }else if($site_type=='HCPT15'){
			// if($site_ke==1){
				// $allowance=15000;
			// }else if($site_ke==2){
				// $allowance=30000;
			// }else if($site_ke==3){
				// $allowance=45000;
			// }else if($site_ke==4){
				// $allowance=60000;
			// }else{
				// $allowance=0;
			// }
		// }
	$allowance			=$status=='DONE' || $status=='ON SITE'?$allowance:0;
	
	if($all_in>=1){
		$allowance=0;
	}
	
	$arrsql[]="update m_pooling_rigger_site set status='$status', allowance='$allowance',site_id='$site_id',
	site_name='$site_name',type_site='$site_type',site_done_ke='$site_ke',reason='$reason'
	where id='$id'";

	$dbproj->BeginTrans();
	for($i=0;$i<count($arrsql);$i++){
		$result=$dbproj->Execute($arrsql[$i]);
		if (!$result){
			$dbproj->RollbackTrans();
			echo json_encode(array('success'=>true));
			die();
		}
	}
	$dbproj->CommitTrans();
	$f->insert_log("UPDATE m_pooling_rigger prg_id: $prg_id");
	
	echo json_encode(array('success'=>true));
		
}
elseif($act=='do_destroydetail'){
	
	$prg_id=$dbproj->getOne("select prg_id from m_pooling_rigger_site where id='$id'");
	
	$check_status		=$dbproj->getOne("select status from m_pooling_rigger where prg_id='$prg_id'");
	if($check_status=='REJECT' || $check_status=='APPROVED'){
		echo json_encode(array('errorMsg'=>"ID: $prg_id status PAYMENT/REJECT, tidak boleh DIHAPUS!"));
		die();
	}
	
	$arrsql=array();
	$id=$_REQUEST['id'];
	$arrsql[]="delete m_pooling_rigger_site where id='$id'";
	$dbproj->BeginTrans();
	for($i=0;$i<count($arrsql);$i++){
		$result=$dbproj->Execute($arrsql[$i]);
		if (!$result){
			$dbproj->RollbackTrans();
			echo json_encode(array('success'=>true));
			die();
		}
	}
	$dbproj->CommitTrans();
	$f->insert_log("DELETE m_pooling_rigger_site id: $id");
	
	echo json_encode(array('success'=>true));
		
}
?>