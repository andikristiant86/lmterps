<?php
ob_start();
session_start();
include_once($DOCUMENT_ROOT."/s/config.php");
$nip_sipeg=$_SESSION['sipeg_nip_pegawai'];	
$login_nip=(empty($nip_sipeg))? $login_nip:$nip_sipeg;
//if($login_nip=="021450")$login_nip="080002";
$lke_id=$db->getOne("select lke_id from spg_data_current where nip='$login_nip'");
if($act=='view'){
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 20;
	$offset = ($page-1)*$rows;
	$result = array();
	
	$f_start_date	=	$_REQUEST['f_start_date'];
	$f_start_date	= (empty($f_start_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_start_date,1,'/'));
	$f_end_date		=	$_REQUEST['f_end_date'];
	$f_end_date	= (empty($f_end_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_end_date,1,'/'));
	
	$find_val=$_REQUEST['value'];
	$find_name=$_REQUEST['name'];
	
	//jika PM terfilter hanya projectnya saja.
	
	$is_pm=$wia_accessname=="DefaultPM"?$dbproj->getOne("select count(*) from M_PROJECT_MANAGER where nip='$login_nip'"):0;
	
	$find_str=($find_name=='all' or empty($find_name))?
	"where -- lke_id in (select area from m_carpool_admin where admin='$login_nip') and 	
	date between '$f_start_date' and '$f_end_date' and pm_approve='1' and finance_approve='1' and 
	(ocs_id like '%$find_val%' or time like '%$find_val%' or date like '%$find_val%' or site_name like '%$find_val%'
	or proj_name like '%$find_val%' or dtc_name like '%$find_val%' or sts_nm like '%$find_val%' or dt_name like '%$find_val%' or no_polisi like '%$find_val%' 
	or area like '%$find_val%'
	)":
	"where  -- lke_id in (select area from m_carpool_admin where admin='$login_nip') and 
	date between '$f_start_date' and '$f_end_date' and pm_approve='1' and finance_approve='1' 
	and $find_name like '%$find_val%'";
	
	$sql="select 
	a.*,(select b.nm_peg from spg_data_current b where b.nip=a.dt_coord) as dtc_name,
	(select b.nm_peg from spg_data_current b where b.nip=a.nip_dt) as dt_name,
	(select b.nm_peg from spg_data_current b where b.nip=a.nip_rno) as rno_name,
	mp.proj_name as proj_name ,
	(select sum(isnull(b.bbm,0)+isnull(b.etoll,0)+isnull(b.mtoll,0)+isnull(b.others,0)+isnull(b.parking,0)+isnull(b.portal,0)+isnull(b.three_in_one,0)+
	isnull(b.uj,0)+isnull(b.um,0)+isnull(b.utb,0)) from t_carpool b where b.ocs_id=a.ocs_id) as total,
	case isnull(a.status,'') when '1' then 'JALAN' when '2' then 'PULANG' when '3' then 'CLOSED' when '111' then 'REJECT' when 112 then 'CANCEL' else 'BELUM JALAN' end as sts_nm,
	(select lokasi_kerja from m_project_area where lke_id=a.lke_id) as area
	from m_carpool a
	left join m_project mp on mp.id=a.proj_id ".($is_pm>0?"and mp.lke_id=a.lke_id where mp.pm_id='$login_nip'":
	"where a.lke_id in (select area from m_carpool_admin where admin='$login_nip')");
	
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
		$totalx=number_format($total,0,'.',',');
		$operational=$um+$uj+$bbm+$parking;
		$operational=number_format($operational,0,'.',',');
		$uang_pulsa=number_format($uang_pulsa,0,'.',',');
		$proj_name=(empty($proj_name))?"OTHER":$proj_name;
		$durasi=1+$tambah_durasi;
		$sts_nm=($sts_nm=="REJECT")?"<font color=red>$sts_nm</font>":"$sts_nm";
		$create_date=$f->convert_date(substr($create_date,0,10),1)." ".substr($create_date,11,8);
		$nip_driver=$dbproj->getOne("select top 1  nip_driver from t_carpool where ocs_id='$ocs_id'");
		$car_number=$dbproj->getOne("select top 1  car_number from t_carpool where ocs_id='$ocs_id'");
		$km_start=$dbproj->getOne("select top 1  km_start from t_carpool where ocs_id='$ocs_id'");
		$items[]=array("ocs_id"=>"$ocs_id","date"=>"$date <br>$time <br>Durasi : $durasi","time"=>"$time","site_name"=>"$site_name","proj_name"=>"$proj_name <br> 
		<font color=orange>$area</font>","dtc_name"=>"$dtc_name <br> $create_date",
		"status"=>"$status","sts_nm"=>"$sts_nm","total"=>"$totalx","dt_name"=>"$dt_name","sow_name"=>"$sow_name","ocs_desc"=>"$ocs_desc","rno_name"=>"$rno_name",
		"uang_pulsa"=>"$uang_pulsa","no_polisi"=>"$no_polisi","operational"=>"$totalx","um"=>"$um","uj"=>"$uj","bbm"=>"$bbm","parking"=>"$parking",
		"nip_driver"=>"$nip_driver","kode_kendaraan1"=>"$car_number","km_start"=>"$km_start"
		);
	}
	$jum_jalan=$dbproj->getOne("select count(*) from ($sql) as x $find_str and sts_nm='JALAN' ");
	$jum_pulang=$dbproj->getOne("select count(*) from ($sql) as x $find_str and sts_nm='PULANG' ");
	$jum_blmjalan=$dbproj->getOne("select count(*) from ($sql) as x $find_str and sts_nm='BELUM JALAN' ");
	$jum_reject=$dbproj->getOne("select count(*) from ($sql) as x $find_str and sts_nm='REJECT' ");
	$jum_cancel=$dbproj->getOne("select count(*) from ($sql) as x $find_str and sts_nm='CANCEL' ");
	$jum_closed=$dbproj->getOne("select count(*) from ($sql) as x $find_str and sts_nm='CLOSED' ");
	
	$jum_rejectx=$jum_cancel+$jum_reject;
	$jum_pulangx=$jum_pulang+$jum_closed;
	$result["footer"]=array(
							array("ocs_desc"=>"<b>BELUM JALAN</b>","sts_nm"=>"<b><span style='color:black'>$jum_blmjalan</span></b>"),
							array("ocs_desc"=>"<b>JALAN</b>","sts_nm"=>"<b><span style='color:black'>$jum_jalan</span></b>"),
							array("ocs_desc"=>"<b>CLOSED</b>","sts_nm"=>"<b><span style='color:black'>$jum_pulangx</span></b>"),
							array("ocs_desc"=>"<b>REJECT</b>","sts_nm"=>"<b><span style='color:red'>$jum_rejectx</span></b>")
							);
	$result["rows"] = $items;
	echo json_encode($result);
}else if($act=='view_updateop'){
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 20;
	$offset = ($page-1)*$rows;
	$result = array();
	
	$f_start_date	=	$_REQUEST['f_start_date'];
	$f_start_date	= (empty($f_start_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_start_date,1,'/'));
	$f_end_date		=	$_REQUEST['f_end_date'];
	$f_end_date	= (empty($f_end_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_end_date,1,'/'));
	
	$find_val=$_REQUEST['value'];
	$find_name=$_REQUEST['name'];
	
	//jika PM terfilter hanya projectnya saja.
	
	$is_pm=$wia_accessname=="DefaultPM"?$dbproj->getOne("select count(*) from M_PROJECT_MANAGER where nip='$login_nip'"):0;
	
	$find_str=($find_name=='all' or empty($find_name))?
	"where -- lke_id in (select area from m_carpool_admin where admin='$login_nip') and
	sts_nm in ('JALAN','PULANG','CLOSED') and
	date between '$f_start_date' and '$f_end_date' and pm_approve='1' and finance_approve='1' and 
	(ocs_id like '%$find_val%' or time like '%$find_val%' or date like '%$find_val%' or site_name like '%$find_val%'
	or proj_name like '%$find_val%' or dtc_name like '%$find_val%' or sts_nm like '%$find_val%' or dt_name like '%$find_val%' or no_polisi like '%$find_val%' 
	or area like '%$find_val%' or nm_driver like '%$find_val%'
	)":
	"where  -- lke_id in (select area from m_carpool_admin where admin='$login_nip') and
	sts_nm in ('JALAN','PULANG','CLOSED') and
	date between '$f_start_date' and '$f_end_date' and pm_approve='1' and finance_approve='1' 
	and $find_name like '%$find_val%'";
	
	$sql="select 
	a.*,(select b.nm_peg from spg_data_current b where b.nip=a.dt_coord) as dtc_name,
	(select b.nm_peg from spg_data_current b where b.nip=a.nip_dt) as dt_name,
	(select b.nm_peg from spg_data_current b where b.nip=a.nip_rno) as rno_name,
	mp.proj_name as proj_name,
	case isnull(a.status,'') when '1' then 'JALAN' when '2' then 'PULANG' when '3' then 'CLOSED' when '111' then 'REJECT' when 112 then 'CANCEL' else 'BELUM JALAN' end as sts_nm,
	(select lokasi_kerja from m_project_area where lke_id=a.lke_id) as area,
	(select sum(isnull(bbm,0)) from t_carpool where ocs_id=a.ocs_id) as bbm_real,
	(select sum(isnull(etoll,0)) from t_carpool where ocs_id=a.ocs_id) as etoll_real,
	(select top 1  nip_driver from t_carpool where ocs_id=a.ocs_id) as nip_driver,
	(select top 1  update_by from t_carpool where ocs_id=a.ocs_id) as update_by,
	(select top 1  (select nm_peg from spg_data_current where nip=b.nip_driver) from t_carpool b where b.ocs_id=a.ocs_id) as nm_driver
	from m_carpool a
	left join m_project mp on mp.id=a.proj_id ".($is_pm>0?"and mp.lke_id=a.lke_id where mp.pm_id='$login_nip'":
	"where a.lke_id in (select area from m_carpool_admin where admin='$login_nip')");
	
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
		$etollRp=number_format($etoll_real,0,'.',',');
		$bbmRp=number_format($bbm_real,0,'.',',');
		$update_by=$db->getOne("select nm_peg from spg_data_current where nip='$update_by'");
		$sts_nm=($sts_nm=="PULANG" || $sts_nm=="CLOSED")?"<font color=green>$sts_nm</font>":"$sts_nm";
		$create_date=$f->convert_date(substr($create_date,0,10),1)." ".substr($create_date,11,8);
		
		$car_number=$dbproj->getOne("select top 1  car_number from t_carpool where ocs_id='$ocs_id'");
		$km_start=$dbproj->getOne("select top 1  km_start from t_carpool where ocs_id='$ocs_id'");
		$items[]=array("ocs_id"=>"$ocs_id <br> <font color=orange>$area</font>","date"=>"$date <br>$time","time"=>"$time","site_name"=>"$site_name","proj_name"=>"$proj_name <br> 
		<font color=orange>$area</font>","dtc_name"=>"$dtc_name <br> $create_date",
		"status"=>"$status","sts_nm"=>"$sts_nm","total"=>"$totalx","dt_name"=>"$dt_name","sow_name"=>"$sow_name","ocs_desc"=>"$ocs_desc","rno_name"=>"$rno_name",
		"uang_pulsa"=>"$uang_pulsa","no_polisi"=>"$no_polisi","operational"=>"$totalx","um"=>"$um","uj"=>"$uj","bbmRp"=>"$bbmRp","parking"=>"$parking",
		"nip_driver"=>"$nip_driver","nm_driver"=>"$nm_driver","kode_kendaraan1"=>"$car_number","km_start"=>"$km_start","etollRp"=>"$etollRp",
		"id"=>"$ocs_id","update_by"=>"$update_by"
		
		);
	}
	/*
	$jum_jalan=$dbproj->getOne("select count(*) from ($sql) as x $find_str and sts_nm='JALAN' ");
	$jum_pulang=$dbproj->getOne("select count(*) from ($sql) as x $find_str and sts_nm='PULANG' ");
	$jum_blmjalan=$dbproj->getOne("select count(*) from ($sql) as x $find_str and sts_nm='BELUM JALAN' ");
	$jum_reject=$dbproj->getOne("select count(*) from ($sql) as x $find_str and sts_nm='REJECT' ");
	$jum_cancel=$dbproj->getOne("select count(*) from ($sql) as x $find_str and sts_nm='CANCEL' ");
	$jum_closed=$dbproj->getOne("select count(*) from ($sql) as x $find_str and sts_nm='CLOSED' ");
	
	$jum_rejectx=$jum_cancel+$jum_reject;
	$jum_pulangx=$jum_pulang+$jum_closed;
	$result["footer"]=array(
							array("ocs_desc"=>"<b>BELUM JALAN</b>","sts_nm"=>"<b><span style='color:black'>$jum_blmjalan</span></b>"),
							array("ocs_desc"=>"<b>JALAN</b>","sts_nm"=>"<b><span style='color:black'>$jum_jalan</span></b>"),
							array("ocs_desc"=>"<b>CLOSED</b>","sts_nm"=>"<b><span style='color:black'>$jum_pulangx</span></b>"),
							array("ocs_desc"=>"<b>REJECT</b>","sts_nm"=>"<b><span style='color:red'>$jum_rejectx</span></b>")
							);*/
	$result["rows"] = $items;
	echo json_encode($result);
}
else if($act=='view_payment'){
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 20;
	$offset = ($page-1)*$rows;
	$result = array();
	
	$f_start_date	=	$_REQUEST['f_start_date'];
	$f_start_date	= (empty($f_start_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_start_date,1,'/'));
	$f_end_date		=	$_REQUEST['f_end_date'];
	$f_end_date	= (empty($f_end_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_end_date,1,'/'));
	
	$find_val=$_REQUEST['value'];
	$find_name=$_REQUEST['name'];
	$find_str=($find_name=='all' or empty($find_name))?
	"where sts_nm not in ('BELUM JALAN','CANCEL','REJECT') and lke_id in (select area from m_carpool_admin where admin='$login_nip') and date between '$f_start_date' and '$f_end_date' and pm_approve='1' and finance_approve='1' and 
	(ocs_id like '%$find_val%' or time like '%$find_val%' or date like '%$find_val%' or site_name like '%$find_val%'
	or proj_name like '%$find_val%' or dtc_name like '%$find_val%' or sts_nm like '%$find_val%' or dt_name like '%$find_val%' or no_polisi like '%$find_val%' 
	or area like '%$find_val%'
	)":
	"where  sts_nm not in ('BELUM JALAN') and lke_id in (select area from m_carpool_admin where admin='$login_nip') and date between '$f_start_date' and '$f_end_date' and pm_approve='1' and finance_approve='1' 
	and $find_name like '%$find_val%'";
	
	$sql="select 
	a.*,(select b.nm_peg from spg_data_current b where b.nip=a.dt_coord) as dtc_name,
	(select b.nm_peg from spg_data_current b where b.nip=a.nip_dt) as dt_name,
	(select b.nm_peg from spg_data_current b where b.nip=a.nip_rno) as rno_name,
	(select b.proj_name from m_project b where b.id=a.proj_id) as proj_name ,
	(select sum(isnull(b.bbm,0)+isnull(b.etoll,0)+isnull(b.mtoll,0)+isnull(b.others,0)+isnull(b.parking,0)+isnull(b.portal,0)+isnull(b.three_in_one,0)+
	isnull(b.uj,0)+isnull(b.um,0)+isnull(b.utb,0)) from t_carpool b where b.ocs_id=a.ocs_id) as total,
	case isnull(a.status,'') when '1' then 'JALAN' when '2' then 'PULANG' when '3' then 'CLOSED' when '111' then 'REJECT' when 112 then 'CANCEL' else 'BELUM JALAN' end as sts_nm,
	(select lokasi_kerja from m_project_area where lke_id=a.lke_id) as area,case when b.status is null then 'PAYMENT' else 'PAID' end as status_payment
	from m_carpool a
	left join t_carpool_payment b on b.ocs_id=a.ocs_id
	";
	
	$result["total"] = $dbproj->getOne("select count(*) from ($sql) as x $find_str");
	
	$sql="select * from ($sql) as x $find_str order by  ocs_id desc";
	if($t=="y") die(nl2br($sql));
	
	$resultx=$dbproj->SelectLimit($sql,$rows,$offset);
	$items=array();
	while($row=$resultx->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$date=$f->convert_date($date,1);
		$totalx=number_format($total,0,'.',',');
		$operational=$um+$uj+$bbm+$parking;
		$operational=number_format($operational,0,'.',',');
		$uang_pulsa=number_format($uang_pulsa,0,'.',',');
		$proj_name=(empty($proj_name))?"OTHER":$proj_name;
		$sts_nm=($sts_nm=="REJECT")?"<font color=red>$sts_nm</font>":"$sts_nm";
		$durasi=1+$tambah_durasi;
		$create_date=$f->convert_date(substr($create_date,0,10),1)." ".substr($create_date,11,8);
		$nip_driver=$dbproj->getOne("select top 1  nip_driver from t_carpool where ocs_id='$ocs_id'");
		$car_number=$dbproj->getOne("select top 1  car_number from t_carpool where ocs_id='$ocs_id'");
		$km_start=$dbproj->getOne("select top 1  km_start from t_carpool where ocs_id='$ocs_id'");
		$items[]=array("ocs_id"=>"$ocs_id","date"=>"$date <br>$time <br>Durasi : $durasi","time"=>"$time","site_name"=>"$site_name","proj_name"=>"$proj_name <br> 
		<font color=orange>$area</font>","dtc_name"=>"$dtc_name <br> $create_date",
		"status"=>"$status","sts_nm"=>"$status_payment","total"=>"$totalx","dt_name"=>"$dt_name","sow_name"=>"$sow_name","ocs_desc"=>"$ocs_desc","rno_name"=>"$rno_name",
		"uang_pulsa"=>"$uang_pulsa","no_polisi"=>"$no_polisi","operational"=>"$totalx","um"=>"$um","uj"=>"$uj","bbm"=>"$bbm","parking"=>"$parking",
		"nip_driver"=>"$nip_driver","kode_kendaraan1"=>"$car_number","km_start"=>"$km_start"
		);
	}
	$result["rows"] = $items;
	echo json_encode($result);
}
elseif($act=='detailview'){
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
	$offset = ($page-1)*$rows;
	$result = array();
	
	$ocs_id = $_REQUEST['ocs_id'];
		
	$rs = $dbproj->Execute("select a.*,
	(select top 1 b.nm_peg from spg_data_current b where b.nip=a.nip_driver) as driver_name,
	(select top 1 b.no_polisi from inv_data_kendaraan b where b.kode_kendaraan=a.car_number) as no_police
	from t_carpool a where a.ocs_id='$ocs_id'");
	$items = array();
	while($row = $rs->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$date_berangkat=$f->convert_date(substr($date_berangkat,0,10),1)." ".substr($date_berangkat,11,5);
		$date_pergi=substr($date_berangkat,0,10);
		$time_pergi=substr($date_berangkat,11,5);
		$date_pulang=$f->convert_date(substr($date_pulang,0,10),1)." ".substr($date_pulang,11,5);
		$total=number_format($um+$uj+$parking+$portal+$three_in_one+$bbm+$mtoll+$etoll+$others+$utb,0,'.',',');
		$um=number_format($um,0,'.',',');
		$uj=number_format($uj,0,'.',',');
		$parking=number_format($parking,0,'.',',');
		$portal=number_format($portal,0,'.',',');
		$three_in_one=number_format($three_in_one,0,'.',',');
		$bbm=number_format($bbm,0,'.',',');
		$mtoll=number_format($mtoll,0,'.',',');
		$etoll=number_format($etoll,0,'.',',');
		$others=number_format($others,0,'.',',');
		$utb=number_format($utb,0,'.',',');
		
		$items[]=array("id"=>"$id","date_berangkat"=>"$date_berangkat","date_pulang"=>"$date_pulang","ocs_id"=>"$ocs_id","driver_name"=>"$driver_name","no_police"=>"$no_police","km_start"=>"$km_start","km_end"=>"$km_end",
		"um"=>"$um","remaks"=>"$remaks","uj"=>"$uj","parking"=>"$parking","portal"=>"$portal","three_in_one"=>"$three_in_one","bbm"=>"$bbm","bbm_ltr"=>"$bbm_ltr","mtoll"=>"$mtoll",
		"etoll"=>"$etoll","others"=>"$others","description"=>"$description","date_pergi"=>"$date_pergi","time_pergi"=>"$time_pergi","nip_driver"=>"$nip_driver",
		"car_number"=>"$car_number","utb"=>"$utb","total"=>"$total"
		);
	}
	echo json_encode($items);
	
}elseif($act=='do_add'){
	
	if (empty($nip_driver)){
		echo json_encode(array('errorMsg'=>"Driver wajib diisi!"));
		die();
	}
	if(empty($car_number)){
		echo json_encode(array('errorMsg'=>"Car number wajib diisi!"));
		die();
	}

	$km_start=str_replace(",","",$km_start);
	$km_end=str_replace(",","",$km_end);
	$bbm_ltr=str_replace(",","",$bbm_ltr);
	
	$date_pergi=$f->convert_date($date_pergi,1,"/");
	$date_pulang=$f->convert_date($date_pulang,1,"/");
	
	$date_berangkat="$date_pergi $time_pergi";
	$date_pulang="$date_pulang $time_pulang";
	$check_tcar=$dbproj->getOne("select count(*) from t_carpool where ocs_id='$ocs_id'");
	
	if($check_tcar < 1){
		$result=$dbproj->Execute("insert into t_carpool 
		(ocs_id,nip_driver,car_number,km_start,km_end,remaks,um,uj,parking,portal,three_in_one,utb,bbm,bbm_ltr,etoll,mtoll,description,date_berangkat,date_pulang,others,update_by,
		update_date) 
		values ('$ocs_id','$nip_driver','$car_number','$km_start','$km_end','$remaks','$um','$uj','$parking','$portal','$three_in_one','$utb','$bbm','$bbm_ltr',
		'$etoll','$mtoll','$description','$date_berangkat','$date_pulang','$others','$login_nip',GETDATE())");
			
		if ($result){
			$no_polisi=$db->getOne("select no_polisi from inv_data_kendaraan where kode_kendaraan='$car_number'");
			$dbproj->Execute("update m_carpool set start_date='$date_berangkat',no_polisi='$no_polisi',driver='$nip_driver' where ocs_id='$ocs_id'");
			echo json_encode(array('success'=>true));
		} else {
			echo json_encode(array('errorMsg'=>"Error: insert into t_carpool (ocs_id,nip_driver,car_number,km_start,km_end,remaks,um,uj,parking,portal,three_in_one,utb,bbm,bbm_ltr,etoll,mtoll,description,date_berangkat,date_pulang) 
												values ('$ocs_id','$nip_driver','$car_number','$km_start','$km_end','$remaks','$um','$uj','$parking','$portal','$three_in_one','$utb','$bbm','$bbm_ltr',
												'$etoll','$mtoll','$description','$date_berangkat','$date_pulang')"));
		}
	}else{
		echo json_encode(array('errorMsg'=>"Tidak boleh lebih dari 1 mobil!"));
		die();
	}
}elseif($act=='combo_car'){
	$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	$sql="select top 50 kode_kendaraan, no_polisi from inv_data_kendaraan 
	where isnull(status,'SEWA') not in ('KEMBALI') and lke_id in (select area from lmt_project.dbo.m_carpool_admin where admin='$login_nip')  and (kode_kendaraan like '%$q%' or no_polisi like '%$q%')";
	// kode_kendaraan not in (select b.car_number from t_carpool b
			// left join m_carpool c on b.ocs_id=c.ocs_id 
			// where c.status in ('1','4')) and
	$resultx=$db->Execute($sql);
	$items=array();
	while($row=$resultx->Fetchrow()){
		$items[]=$row;
	}
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='combo_driver'){
	$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	$sql="select nm_peg,nip from spg_data_current 
	where kd_jabatan_str+kd_unit_org='8011040102010000000' and (nm_peg like '%$q%' or nip like '%$q%')";
	// and nip not in (
			// select nip_driver from ( select a.ocs_id,a.nip_driver, (select b.status from m_carpool b where b.ocs_id=a.ocs_id) as status from t_carpool a) as x where x.status not in ('1','4')
		// ) 
	$result_user=$dbproj->Execute($sql);
	$items=array();
	while($row=$result_user->Fetchrow()){
		$items[]=$row;
	}
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='do_update'){
	
	if (empty($nip_driver)){
		echo json_encode(array('errorMsg'=>"Driver wajib diisi!"));
		die();
	}
	if(empty($car_number)){
		echo json_encode(array('errorMsg'=>"Car number wajib diisi!"));
		die();
	}
	
	$km_start=str_replace(",","",$km_start);
	$km_end=str_replace(",","",$km_end);
	$bbm_ltr=str_replace(",","",$bbm_ltr);
	
	$date_pergi=$f->convert_date($date_pergi,1,"/");
	$date_pulang=$f->convert_date($date_pulang,1,"/");
	
	$date_berangkat="$date_pergi $time_pergi";
	$date_pulang="$date_pulang $time_pulang";
	
	$result=$dbproj->Execute("update t_carpool set 
	nip_driver='$nip_driver',car_number='$car_number',km_start='$km_start',km_end='$km_end',remaks='$remaks',um='$um',uj='$uj',parking='$parking',portal='$portal',
	three_in_one='$three_in_one',utb='$utb',bbm='$bbm',bbm_ltr='$bbm_ltr',etoll='$etoll',mtoll='$mtoll',description='$description',date_berangkat='$date_berangkat',
	date_pulang='$date_pulang',others='$others' where id='$id'");
		
	if ($result){
		$ocs_id=$dbproj->getOne("select ocs_id from t_carpool where id='$id'");
		$total_cost=$dbproj->getOne("select sum(um+uj+parking+portal+three_in_one+utb+bbm+etoll+mtoll+others) from t_carpool where ocs_id='$ocs_id'");
		$no_polisi=$db->getOne("select no_polisi from inv_data_kendaraan where kode_kendaraan='$car_number'");
		$dbproj->Execute("update m_carpool set start_date='$date_berangkat',no_polisi='$no_polisi',driver='$nip_driver' where ocs_id='$ocs_id'");
		echo json_encode(array('success'=>true));
	} else {
		echo json_encode(array('errorMsg'=>"update t_carpool set 
		nip_driver='$nip_driver',car_number='$car_number',km_start='$km_start',km_end='$km_end',remaks='$remaks',um='$um',uj='$uj',parking='$parking',portal='$portal',
		three_in_one='$three_in_one',utb='$utb',bbm='$bbm',bbm_ltr='$bbm_ltr',etoll='$etoll',mtoll='$mtoll',description='$description',date_berangkat='$date_berangkat',
		date_pulang='$date_pulang',others='$others' where id='$id'"));
	}
		
}elseif($act=='removeDetail'){
	$result=$dbproj->Execute("delete t_carpool where id='$id'");
	if ($result){
		echo json_encode(array('success'=>true));
	} else {
		echo json_encode(array('msg'=>'Some errors occured.'));
	}
}elseif($act=='generate_kode'){
	$ocs_id=$f->generate_nomorkolom("lmt_project.dbo.T_Carpool","OCS_ID","CRP");
	
	echo json_encode(array('kode'=>"$ocs_id"));
}elseif($act=='do_import'){
	$type=$_FILES["fileexcel"]["type"];
	if($type!='application/vnd.ms-excel'){
		echo json_encode(array('errorMsg'=>"Please, check format upload .xls"));
		die();
	}
	$data = new Spreadsheet_Excel_Reader($_FILES['fileexcel']['tmp_name']);
	$hasildata = $data->rowcount($sheet_index=0);
	$sukses = 0;
	$gagal = 0;
	for ($i=2; $i<=$hasildata; $i++)
	{
		$id 	= $data->val($i,1);
		$date_1 = $f->convert_date($data->val($i,4),1,"/");
		$date_2 = $f->convert_date($data->val($i,5),1,"/");
		$date_3 = $f->convert_date($data->val($i,6),1,"/");
		$date_4 = $f->convert_date($data->val($i,7),1,"/");
		$date_5 = $f->convert_date($data->val($i,8),1,"/");
		$sql	= "update t_milestone set date_1='$date_1',date_2='$date_2',date_3='$date_3',date_4='$date_4',date_5='$date_5' where id='$id'";
		$result = $dbproj->Execute("$sql");
		if ($result) $sukses++;
		else $gagal++;
	}
	echo json_encode(array('success'=>true));
}
elseif($act=='prosess_jalan'){

	$cek_driver=$db->getOne("select count(*) from spg_data_current where nip='$nip_driver1'");
	if($cek_driver==0){
		echo json_encode(array('errorMsg'=>'Driver wajib disi, silahkan pilih dengan benar!'));
		die();
	}
	
	$cek_nopol=$db->getOne("select count(*) from inv_data_kendaraan where kode_kendaraan='$kode_kendaraan1'");
	if($cek_nopol==0){
		echo json_encode(array('errorMsg'=>'No Polisi wajib disi, silahkan pilih dengan benar!'));
		die();
	}
	

	$result=$dbproj->Execute("update m_carpool set status='1' where ocs_id='$ocs_id'");
	if ($result){
		$check_ocs=$dbproj->getOne("select count(*) from t_carpool where ocs_id='$ocs_id'");
		if($check_ocs<=0){
			$dbproj->getOne("delete t_carpool where ocs_id='$ocs_id'");
			$dbproj->getOne("insert into t_carpool (ocs_id,uj,um,bbm,others,date_berangkat,update_by,update_date,description,km_start,nip_driver,car_number) 
			select ocs_id,uj,um,bbm,parking,GETDATE(),'$login_nip',GETDATE(),ocs_desc,'$km_start1','$nip_driver1','$kode_kendaraan1' from m_carpool where ocs_id='$ocs_id'");
			$no_polisi=$db->getOne("select no_polisi from inv_data_kendaraan where kode_kendaraan='$kode_kendaraan1'");
			$dbproj->getOne("update m_carpool set no_polisi='$no_polisi' where ocs_id='$ocs_id'");
		}
		echo json_encode(array('success'=>true));
	} else {
		echo json_encode(array('errorMsg'=>'Some errors occured.'));
	}
}elseif($act=='prosess_reject'){
	$result=$dbproj->Execute("update m_carpool set status='111',reject_nip='$login_nip',reject_date=GETDATE() where ocs_id='$ocs_id'");
	if ($result){
		$dbproj->getOne("delete t_carpool where ocs_id='$ocs_id'");
		echo json_encode(array('success'=>true));
	} else {
		echo json_encode(array('msg'=>'Some errors occured.'));
	}
}elseif($act=='do_tambahdurasi'){
	$durasi=$_REQUEST['durasi'];$ocs_id=$_REQUEST['ocs_id'];
	if($durasi <= 2 ){
		$dbproj->Execute("update m_carpool set tambah_durasi='$durasi' where ocs_id='$ocs_id'");
		echo json_encode(array('success'=>true));
	}else{
		echo json_encode(array('errorMsg'=>'Maksimal penambahan durasi hanya 2 hari.'));
		die();
	}
}
elseif($act=='prosess_paid'){
	$dbproj->Execute("delete t_carpool_payment where ocs_id='$ocs_id'");
	$dbproj->Execute("insert into t_carpool_payment (ocs_id,status) values ('$ocs_id','PAID')");
	
	echo json_encode(array('success'=>true));
	
}elseif($act=='prosess_pulang'){
	$date_pulang=$f->convert_date($date_pulang,1,"/");
	$date_pulang="$date_pulang $time_pulang";
	
	$result=$dbproj->Execute("update m_carpool set status='2', end_date='$date_pulang' where ocs_id='$ocs_id'");

	if ($result==1){
		$dbproj->Execute("update t_carpool set km_end='$km_end1',nip_driver='$nip_driver1',car_number='$kode_kendaraan1',km_start='$km_start1',
		date_pulang='$date_pulang'
		where ocs_id='$ocs_id'");
		echo json_encode(array('success'=>true));
	} else {
		echo json_encode(array('msg'=>'Some errors occured.'));
	}
}elseif($act=='prosess_opupdate'){
		$tgl_dibayar=$f->convert_date($tgl_dibayar,1,"/");
		$dbproj->Execute("update t_carpool set bbm='$bbmRp',etoll='$etollRp',
		update_by='$login_nip',update_date=GETDATE(),paid_date_bbm='$tgl_dibayar',paid_by_bbm='$login_nip'
		where ocs_id='$ocs_id'");
		echo json_encode(array('success'=>true));
}
elseif($act=='combo_driver1'){
	$sql="select nip,nm_peg from m_driver where area_id in (select area from lmt_project.dbo.m_carpool_admin where admin='$login_nip')";
	$resultx=$db->Execute($sql);
	while($row=$resultx->Fetchrow()){
		$items[]=$row;
	}
	echo json_encode($items);
}elseif($act=='combo_car1'){
	$sql="select no_polisi,kode_kendaraan from inv_data_kendaraan where status not in ('KEMBALI') and lke_id in (select area from lmt_project.dbo.m_carpool_admin where admin='$login_nip')";
	$resultx=$db->Execute($sql);
	while($row=$resultx->Fetchrow()){
		$items[]=$row;
	}
	echo json_encode($items);
}
?>